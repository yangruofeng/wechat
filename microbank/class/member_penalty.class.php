<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/16
 * Time: 15:53
 */
class member_penaltyClass
{
    public function addReduceApply($member_id, $penalty_info, $penalty_ids, $creator_id)
    {
        $member_id = intval($member_id);
        $receivable = round($penalty_info['receivable'], 2);
        $deducting = round($penalty_info['deducting'], 2);
        $paid = $receivable - $deducting;
        $currency = $penalty_info['currency'];
        $remark = $penalty_info['remark'];
        $creator_id = intval($creator_id);

        $userObj = new objectUserClass($creator_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $r = new ormReader();
        $sql = "SELECT la.* FROM client_member cm LEFT JOIN loan_account la ON cm.obj_guid = la.obj_guid WHERE cm.uid = $member_id";
        $loan_account = $r->getRow($sql);
        if (!$loan_account) {
            return new result(false, 'Invalid member id.');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $loan_penalty_receipt = M('loan_penalty_receipt');
            $row = $loan_penalty_receipt->newRow();
            $row->account_id = $loan_account['uid'];
            $row->receivable = $receivable;
            $row->deducting = $deducting;
            $row->paid = $paid;
            $row->currency = $currency;
            $row->creator_id = $creator_id;
            $row->creator_name = $userObj->user_name;

            $m_dict = new core_dictionaryModel();
            $global_settings = $m_dict->getDictValue(dictionaryKeyEnum::GLOBAL_SETTINGS);
            $teller_reduce_penalty_maximum = round($global_settings['teller_reduce_penalty_maximum']);
            if ($deducting >= $teller_reduce_penalty_maximum) {
                $row->state = loanPenaltyReceiptStateEnum::CREATE;
            } else {
                $row->state = loanPenaltyReceiptStateEnum::APPROVED;
                if($paid<=0){
                    $row->state=loanPenaltyReceiptStateEnum::COMPLETE;
                }
            }
            $row->remark = $remark;
            $row->create_time = Now();
            $rt_1 = $row->insert();
            if (!$rt_1->STS) {
                $conn->rollback();
                return $rt_1;
            }

            $m_loan_penalty = M('loan_penalty');
            $m_loan_penalty_receipt_detail = M('loan_penalty_receipt_detail');
            foreach ($penalty_ids as $penalty_id) {
                $penalty_row = $m_loan_penalty->getRow($penalty_id);
                if ($penalty_row->state != loanPenaltyHandlerStateEnum::CREATE) {
                    $conn->rollback();
                    return new result('Invalid Penalty.');
                }
                $penalty_row->state = loanPenaltyHandlerStateEnum::APPLY_REDUCE;
                $rt_2 = $penalty_row->update();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return $rt_2;
                }

                $detail_row = $m_loan_penalty_receipt_detail->newRow();
                $detail_row->receipt_id = $row->uid;
                $detail_row->penalty_id = $penalty_id;
                $rt_3 = $detail_row->insert();
                if (!$rt_3->STS) {
                    $conn->rollback();
                    return $rt_3;
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Add Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 获取罚金申请
     * @param $pageNumber
     * @param $pageSize
     * @param $filter
     * @return array
     */
    public function getPenaltyApplyList($pageNumber, $pageSize, $filter)
    {
        $r = new ormReader();
        $sql = "SELECT lpr.*, cm.login_code FROM loan_penalty_receipt lpr"
            . " INNER JOIN loan_account la ON lpr.account_id = la.uid"
            . " INNER JOIN client_member cm ON la.obj_guid = cm.obj_guid WHERE 1 = 1";
        if (intval($filter['state']) == loanPenaltyReceiptStateEnum::APPROVED) {
            $sql .= " AND lpr.state >= " . loanPenaltyReceiptStateEnum::APPROVED;
        } else {
            $sql .= " AND lpr.state = " . intval($filter['state']);
        }
        if (trim($filter['search_text'])) {
            $sql .= " AND cm.login_code LIKE '%" . qstr2(trim($filter['search_text'])) . "%'";
        }
        $sql .= " ORDER BY lpr.uid DESC";
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
     * 接任务
     * @param $uid
     * @param $auditor_id
     * @return result
     */
    public function getPenaltyRequestTask($uid, $auditor_id)
    {
        $uid = intval($uid);
        $auditor_id = intval($auditor_id);
        $m_loan_penalty_receipt = M('loan_penalty_receipt');
        $row = $m_loan_penalty_receipt->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id.');
        }
        if ($row->state > loanPenaltyReceiptStateEnum::AUDITING) {
            return new result(false, 'Audited.');
        } else if ($row->state == loanPenaltyReceiptStateEnum::AUDITING) {
            if ($row->auditor_id == $auditor_id) {
                return new result(true);
            } else {
                return new result(false, 'Others are already reviewing it.');
            }
        } else {
            $auditor = new objectUserClass($auditor_id);
            $chk = $auditor->checkValid();
            if (!$chk->STS) {
                return $chk;
            }

            $row->state = loanPenaltyReceiptStateEnum::AUDITING;
            $row->auditor_id = $auditor_id;
            $row->auditor_name = $auditor->user_name;
            $row->update_time = Now();
            $rt = $row->update();
            return $rt;
        }
    }


    /**
     * 获取罚金申请详情
     * @param $uid
     * @return ormDataRow
     */
    public function getPenaltyRequestDetail($uid)
    {
        $uid = intval($uid);
        $r = new ormReader();
        $sql_1 = "SELECT lpr.*,cm.login_code,mg.grade_code FROM loan_penalty_receipt lpr"
            . " INNER JOIN loan_account la ON lpr.account_id = la.uid"
            . " INNER JOIN client_member cm ON la.obj_guid = cm.obj_guid"
            . " LEFT JOIN member_grade mg ON mg.uid = cm.member_grade WHERE lpr.uid = $uid";
        $request_detail = $r->getRow($sql_1);

        $sql_2 = "SELECT lc.contract_sn FROM loan_penalty_receipt lpr"
            . " INNER JOIN loan_penalty_receipt_detail lprd ON lprd.receipt_id = lpr.uid"
            . " INNER JOIN loan_penalty lp ON lp.uid = lprd.penalty_id"
            . " INNER JOIN loan_contract lc ON lp.contract_id = lc.uid WHERE lpr.uid = $uid";
        $request_penalty_list = $r->getRows($sql_2);
        $request_detail['penalty_list'] = $request_penalty_list;
        return $request_detail;
    }


    /**处理罚金申请
     * @param $uid
     * @param $state
     * @param $audit_remark
     * @param $auditor_id
     * @return result
     */
    public function auditPenaltyRequest($uid, $state, $audit_remark, $auditor_id)
    {
        $uid = intval($uid);
        $state = intval($state);
        $audit_remark = trim($audit_remark);
        $auditor_id = intval($auditor_id);
        $m_loan_penalty_receipt = M('loan_penalty_receipt');
        $row = $m_loan_penalty_receipt->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id.');
        }

        if ($row->state != loanPenaltyReceiptStateEnum::AUDITING) {
            return new result(false, 'It\'s not being reviewed.');
        }

        if ($row->auditor_id != $auditor_id) {
            return new result(false, 'Others are already reviewing it.');
        }

        $row->state = $state == loanPenaltyReceiptStateEnum::APPROVED ? $state : loanPenaltyReceiptStateEnum::REJECTED;
        $row->audit_remark = $audit_remark;
        $row->audit_time = Now();
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Audit Successful.');
        } else {
            return new result(true, 'Audit Failed.');
        }

    }


    /**
     * 取消任务
     * @param $uid
     * @param $auditor_id
     * @return result
     */
    public function abandonPenaltyRequestTask($uid, $auditor_id)
    {
        $uid = intval($uid);
        $auditor_id = intval($auditor_id);
        $m_loan_penalty_receipt = M('loan_penalty_receipt');
        $row = $m_loan_penalty_receipt->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id.');
        }

        if ($row->state != loanPenaltyReceiptStateEnum::AUDITING) {
            return new result(false, 'It\'s not being reviewed.');
        }

        if ($row->auditor_id != $auditor_id) {
            return new result(false, 'Others are already reviewing it.');
        }

        $row->state = loanPenaltyReceiptStateEnum::CREATE;
        $row->auditor_id = '';
        $row->auditor_name = '';
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Abandon Successful.');
        } else {
            return new result(true, 'Abandon Failed.');
        }
    }

}