<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/28
 * Time: 14:36
 */
class loan_written_offClass
{
    public function __construct()
    {
    }


    /** 计算合同核销损失
     * @param $contract_id
     * @return result
     */
    public static function calculateContractWriteOffLoss($contract_id)
    {
        $contract_id = intval($contract_id);
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        // 计算损失
        $sql = "select sum(receivable_principal) loss_principal,sum(receivable_interest) loss_interest,sum(receivable_operation_fee) loss_operation_fee,
        sum(receivable_admin_fee) loss_admin_fee,sum(actual_payment_amount) total_repayment from loan_installment_scheme
        where contract_id='$contract_id' and state!='" . schemaStateTypeEnum::COMPLETE . "' and state!='" . schemaStateTypeEnum::CANCEL . "' ";
        $loss_arr = $m_contract->reader->getRow($sql);

        // 损失本金
        $loss_principal = $loss_arr['loss_principal'] - $loss_arr['total_repayment'];
        if ($loss_principal < 0) {
            $loss_principal = 0;
        }

        return new result(true, 'success', array(
            'loss_principal' => round($loss_principal, 2),
            'loss_interest' => round($loss_arr['loss_interest'], 2),
            'loss_operation_fee' => round($loss_arr['loss_operation_fee'], 2),
            'loss_amount' => round($loss_principal, 2) + round($loss_arr['loss_interest'], 2) + round($loss_arr['loss_operation_fee'], 2)
        ));
    }

    /**
     * 添加申请
     * @param $uid
     * @param $remark
     * @param $operator_id
     * @param $close_type
     * @return result
     */
    public static function addWrittenOffRequest($uid, $remark, $operator_id, $close_type = '')
    {
        $uid = intval($uid);
        $loan_contract = M('loan_contract')->find(array('uid' => $uid));
        if (!$loan_contract) {
            return new result(false, 'Invalid Id.');
        }

        if (!$remark) {
            return new result(false, 'Empty Remark.');
        }


        if ( !loan_contractClass::loanContractIsUnderExecuting($loan_contract) ) {
            return new result(false, 'Invalid State.');
        }

        $request_record = self::getLastWriteOffRequest($uid);
        if ($request_record && $request_record['state'] !== writeOffStateEnum::APPROVING) {
            return new result(false, 'The last application was under review.');
        }

        $userObj = new objectUserClass($operator_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $rt = self::calculateContractWriteOffLoss($uid);
        if (!$rt->STS) {
            return new result(false, $rt->MSG);
        }

        $loss_arr = $rt->DATA;
        $m_loan_writtenoff = new loan_writtenoffModel();
        if ($request_record && $request_record['state'] !== writeOffStateEnum::CREATE) {
            $row = $m_loan_writtenoff->getRow($request_record['uid']);
            $row->update_time = Now();
        } else {
            $row = $m_loan_writtenoff->newRow();
            $row->contract_id = $uid;
            $row->create_time = Now();
        }

        $row->close_type = $close_type ?: contractWriteOffTypeEnum::SYSTEM;
        $row->close_remark = trim($remark);
        $row->currency = $loan_contract['currency'];
        $row->loss_amount = round($loss_arr['loss_amount'], 2);
        $row->loss_principal = round($loss_arr['loss_principal'], 2);
        $row->loss_interest = round($loss_arr['loss_interest'], 2);
        $row->loss_operation_fee = round($loss_arr['loss_operation_fee'], 2);
        $row->creator_id = $operator_id;
        $row->creator_name = $userObj->user_name;
        $row->state = writeOffStateEnum::CREATE;
        if ($request_record && $request_record['state'] !== writeOffStateEnum::CREATE) {
            $rt = $row->update();
        } else {
            $rt = $row->insert();
        }

        if ($rt->STS) {
            return new result(true, 'Add Successful.');
        } else {
            return new result(false, 'Add Failed.');
        }
    }

    /**
     * 获取最后一条申请记录
     * @param $contract_id
     * @return mixed
     */
    public static function getLastWriteOffRequest($contract_id)
    {
        $m_loan_writtenoff = new loan_writtenoffModel();
        $last_request = $m_loan_writtenoff->orderBy('uid DESC')->find(array('contract_id' => $contract_id));
        return $last_request;
    }

    /**
     * 申请记录
     */
    public static function getWriteOffRequest($pageNumber, $pageSize, $creator_id)
    {
        $r = new ormReader();
        $sql = "SELECT lwo.*,lc.contract_sn,cm.display_name,cm.login_code FROM loan_writtenoff lwo LEFT JOIN loan_contract lc ON lwo.contract_id=lc.uid"
            . " INNER JOIN loan_account la ON lc.account_id=la.uid"
            . " INNER JOIN client_member cm ON cm.obj_guid=la.obj_guid WHERE lwo.creator_id=" . $creator_id;
        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * @param $param
     * @return result
     */
    public function commitWrittenOff($param)
    {
        $off_id = intval($param['off_id']);
        $operator_id = $param['operator_id'];
        $operator_name = $param['operator_name'];
        $remark = trim($param['remark']);
        $committee_member = $param['committee_member'];

        $m_loan_writtenoff = M('loan_writtenoff');
        $written_off = $m_loan_writtenoff->getRow($off_id);
        if ($written_off['state'] != writeOffStateEnum::CREATE) {
            return new result(false, 'Invalid State.');
        }
        $loss_amount = $written_off['loss_amount'];

        $limit_voter = global_settingClass::getVoterOfWrittenOffByLossAmount($loss_amount);
        if ($limit_voter > 1) {//必须超过1人投票
            if (count($committee_member) < $limit_voter) {
                return new result(false, "Asking for at least " . $limit_voter . " voters");
            }
        }

        if (count($committee_member) == 1 && $committee_member[0] == $operator_id) {
            $is_auto_vote = 1;//说明允许只有一个投票者，并且是自己
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $written_off->operator_id = $operator_id;
            $written_off->operator_name = $operator_name;
            $written_off->operator_remark = $remark;
            $written_off->state = writeOffStateEnum::APPROVING;
            $written_off->update_time = Now();
            $rt_1 = $written_off->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Commit Failure!' . $rt_1->MSG);
            }

            $m_loan_writtenoff_attender = M('loan_writtenoff_attender');
            foreach ($committee_member as $val) {
                $row_2 = $m_loan_writtenoff_attender->newRow();
                $row_2->attender_id = $val;
                $row_2->off_id = $off_id;
                $row_2->vote_result = commonApproveStateEnum::CREATE;
                if ($is_auto_vote) {
                    $row_2->vote_result = commonApproveStateEnum::PASS;
                    $row_2->update_time = Now();
                }
                $rt_2 = $row_2->insert();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Commit Failure!');
                }
            }

            if ($is_auto_vote) {//核销账目
                //核销账目
                $rt_2 = $this->writeOffContract($off_id);
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return $rt_2;
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Commit Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 参与投票人员
     * @param $off_id
     * @return ormCollection
     */
    public function getVoteCommitteeMember($off_id)
    {
        $off_id = intval($off_id);
        $r = new ormReader();
        $sql = "SELECT lwa.*,uu.user_name FROM loan_writtenoff_attender lwa LEFT JOIN um_user uu ON lwa.attender_id = uu.uid WHERE lwa.off_id = " . $off_id;
        return $r->getRows($sql);
    }

    /**
     * 重新编辑核销
     * @param $uid
     * @return result
     */
    public function cancelWrittenOff($uid)
    {
        $m_loan_writtenoff = M('loan_writtenoff');
        $row = $m_loan_writtenoff->getRow(array('uid' => $uid, 'state' => writeOffStateEnum::APPROVING));
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $row->state = writeOffStateEnum::CREATE;
        $row->update_time = Now();
        $rt_1 = $row->update();
        if (!$rt_1->STS) {
            $conn->rollback();
            return new result(false, 'Cancel Failure!');
        }

        $m_loan_writtenoff_attender = M('loan_writtenoff_attender');
        $rt_2 = $m_loan_writtenoff_attender->delete(array('off_id' => $uid));
        if (!$rt_2->STS) {
            $conn->rollback();
            return new result(false, 'Cancel Failure!');
        }

        $conn->submitTransaction();
        return new result(true);
    }

    /**
     * 重置投票时间
     * @param $uid
     * @return result
     */
    public function resetVoteTimer($uid)
    {
        $m_loan_writtenoff = M('loan_writtenoff');
        $row = $m_loan_writtenoff->getRow(array('uid' => $uid, 'state' => writeOffStateEnum::APPROVING));
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row->update_time = Now();
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Reset Failure!');
            }

            $sql = "update loan_writtenoff_attender set vote_result = " . commonApproveStateEnum::CREATE . ",update_time = null WHERE off_id = " . $uid;
            $rt_2 = $m_loan_writtenoff->conn->execute($sql);
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Reset Failure!' . $rt_2->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Reset Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 确认投票
     * @param $uid
     * @return ormResult|result
     */
    public function completeVote($uid)
    {
        $uid = intval($uid);
        $m_loan_writtenoff = M('loan_writtenoff');
        $row = $m_loan_writtenoff->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid id.');
        }

        $r = new ormReader();
        $sql = "select count(*) vote_count from loan_writtenoff_attender WHERE off_id = " . $uid;
        $vote_count = $r->getOne($sql);

        $sql = "select count(*) approval_count from loan_writtenoff_attender WHERE vote_result = " . commonApproveStateEnum::PASS . " AND off_id = " . $uid;
        $approval_count = $r->getOne($sql);

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            if ($approval_count >= $vote_count) {
                //核销账目
                $rt_1 = $this->writeOffContract($uid);
                if (!$rt_1->STS) {
                    $conn->rollback();
                    return $rt_1;
                }

                $conn->submitTransaction();
                return new result(true, 'Pass!');
            } else {
                $row->state = writeOffStateEnum::REJECT;
                $row->vote_result = commonApproveStateEnum::REJECT;;
                $row->update_time = Now();
                $rt_1 = $row->update();
                if (!$rt_1->STS) {
                    $conn->rollback();
                    return $rt_1;
                }

                $conn->submitTransaction();
                return new result(true, 'Not Pass!');
            }
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 投票
     * @param $param
     * @return result
     */
    public function submitVoteWrittenOff($param)
    {
        $off_id = intval($param['off_id']);
        $vote_state = intval($param['vote_state']);
        $account = trim($param['account']);
        $password = trim($param['password']);

        $m_um_user = M('um_user');
        $um_user = $m_um_user->find(array('user_code' => $account));
//        if (!$um_user || $um_user['user_position'] != userPositionEnum::COMMITTEE_MEMBER || $um_user['user_position'] == userPositionEnum::BACK_OFFICER) {
        if (!$um_user) {
            return new result(false, 'Invalid Account!');
        }

        if ($um_user['password'] != md5($password)) {
            return new result(false, 'Password Error!');
        }
        $user_id = $um_user['uid'];

        $m_loan_writtenoff_attender = M('loan_writtenoff_attender');
        $row = $m_loan_writtenoff_attender->getRow(array('off_id' => $off_id, 'attender_id' => $user_id));
        if (!$row) {
            return new result(false, 'Invalid Account!');
        }

        if ($row->vote_result != commonApproveStateEnum::CREATE) {
            return new result(false, 'The account has been voted on!');
        }

        $m_loan_writtenoff = M('loan_writtenoff');
        $written_off = $m_loan_writtenoff->find($off_id);
        if (!$written_off) {
            return new result(false, 'Invalid written off.');
        }
        if ((strtotime($written_off['update_time']) + 300) < time()) {
            return new result(false, 'The vote has been timed out!');
        }

        $row->vote_result = $vote_state;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Vote Successful!');
        } else {
            return new result(true, 'Vote Failed!');
        }
    }

    /**
     * 合同核销（意外核销)
     * @param $off_id
     * @return result
     */
    private function writeOffContract($off_id)
    {
        $m_off = new loan_writtenoffModel();
        $off_info = $m_off->getRow($off_id);
        if (!$off_info) {
            return new result(false, 'Invalid param');
        }
        if ($off_info['state'] != writeOffStateEnum::APPROVING) {
            return new result(false, 'Written off state error.');
        }
        $contract_id = $off_info->contract_id;

        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract');
        }

        if ($contract['state'] < loanContractStateEnum::PENDING_DISBURSE || $contract['state'] >= loanContractStateEnum::COMPLETE) {
            return new result(false, 'Contract state error.');
        }

        $rt = self::calculateContractWriteOffLoss($contract_id);
        $loss_arr = $rt->DATA;

        // 更新合同
        $contract->loss_principal = $loss_arr['loss_principal'];
        $contract->loss_interest = $loss_arr['loss_interest'];
        $contract->loss_operation_fee = $loss_arr['loss_operation_fee'];
        $contract->update_time = Now();
        $up = $contract->update();
        if (!$up->STS) {
            return new result(false, 'Write off fail');
        }

        $trading = new loanWrittenOffTradingClass($contract_id);
        $trading->remark = $off_info['operator_remark'];
        $ret = $trading->execute();
        if (!$ret->STS) {
            return new result(false, $ret->MSG);
        }

        $contract->state = loanContractStateEnum::WRITE_OFF;
        $up = $contract->update();
        if (!$up->STS) {
            return new result(false, 'Write off fail');
        }

        $off_info->loss_amount = $loss_arr['loss_amount'];
        $off_info->loss_principal = $loss_arr['loss_principal'];
        $off_info->loss_interest = $loss_arr['loss_interest'];
        $off_info->loss_operation_fee = $loss_arr['loss_operation_fee'];
        $off_info->vote_result = commonApproveStateEnum::PASS;
        $off_info->state = writeOffStateEnum::COMPLETE;
        $off_info->close_date = Now();
        $off_info->update_time = Now();
        $up = $off_info->update();
        if (!$up->STS) {
            return new result(false, 'Write off fail');
        }

        //更新相关保险的合同
        $sql = "update insurance_contract set state='" . insuranceContractStateEnum::COMPLETE . "' where loan_contract_id='$contract_id' ";
        $up = $m_contract->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Update fail');
        }

        // 发送消息通知 member
        $sql = "select m.uid member_id from loan_account a inner join client_member m on a.obj_guid=m.obj_guid where a.uid='" . $contract->account_id . "' ";
        $member_id = $m_contract->reader->getOne($sql);
        $title = 'Loan Contract Written Off';
        $body = "Your loan contract(contract sn: " . $contract->contract_sn . ") has been written off!";
        member_messageClass::sendSystemMessage($member_id, $title, $body);

        return new result(true);
    }
}