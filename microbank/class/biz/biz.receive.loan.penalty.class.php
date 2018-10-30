<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/11
 * Time: 10:56
 */
class bizReceiveLoanPenaltyClass extends bizBaseClass
{
    public function __construct($scene_code)
    {

        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!',errorCodesEnum::FUNCTION_CLOSED);
        }

        $this->scene_code = bizSceneEnum::COUNTER;
        $this->biz_code = bizCodeEnum::RECEIVE_LOAN_PENALTY_BY_COUNTER;
        $this->bizModel = new biz_receive_member_penaltyModel();
    }

    public function checkBizOpen()
    {
        return new result(true);
    }


    public function getBizDetailById($id)
    {
        return $this->bizModel->find(array(
            'uid' => $id
        ));
    }

    public function checkMemberTradingPassword($biz_id, $password)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if (!$biz) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $member_id = $biz->member_id;
        $objectMember = new objectMemberClass($member_id);
        if ($password != md5($objectMember->trading_password)) {
            return new result(false, 'Password error', null, errorCodesEnum::PASSWORD_ERROR);
        }

        $biz->member_trading_password = $objectMember->trading_password;
        $biz->update_time = Now();
        $up = $biz->update();

        return new result(true, 'success');

    }


    public function checkTellerPassword($biz_id, $card_no, $key)
    {
        $m = $this->bizModel;
        $biz = $m->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info.',null,errorCodesEnum::NO_DATA);
        }

        $userObj = new objectUserClass($biz->cashier_id);
        $branch_id = $userObj->branch_id;
        $chk = $this->checkTellerAuth($biz->cashier_id,$branch_id,$card_no,$key);
        if( !$chk->STS ){
            return $chk;
        }
        $biz->cashier_trading_password = $userObj->trading_password;
        $biz->cashier_name = $userObj->user_name;
        $biz->update_time = Now();
        $biz->update();

        return new result(true);
    }

    public function checkChiefTellerPassword($biz_id,$card_no,$key)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $cashier_id = $biz->cashier_id;
        $cashierObj = new objectUserClass($cashier_id);

        $branch_id = $cashierObj->branch_id;
        $rt = $this->checkChiefTellerAuth($branch_id,$card_no,$key);
        if( !$rt->STS ){
            return $rt;
        }

        $user_id = $rt->DATA;
        $chiefTeller = new objectUserClass($user_id);
        $biz->bm_id = $user_id;
        $biz->bm_trading_password = $chiefTeller->trading_password;
        $biz->bm_name = $chiefTeller->user_name;
        $biz->update_time = Now();
        $up = $biz->update();


        return new result(true,'success',array(
            'biz_id' => $biz_id
        ));

    }

    public function isNeedCTApprove($biz_id)
    {
        $r = new ormReader();
        $sql = "select * from biz_receive_member_penalty_detail where biz_id=".qstr($biz_id);
        $rows = $r->getRows($sql);
        $multi_currency = array();
        foreach( $rows as $v ){
            $multi_currency[$v['currency']] = $v['amount'];
        }
        $biz_info = $this->getBizDetailById($biz_id);
        $branch_id = $biz_info['branch_id'];
        return $this->counterBizIsNeedCTApprove($multi_currency,$branch_id);
    }


    /** 第一步，确认进入还罚金流程
     * @param $user_id
     * @param $member_id
     * @param $receipt_id
     * @return result
     */
    public function bizStart($user_id,$member_id,$receipt_id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->newRow();

        $userObj = new objectUserClass($user_id);
        $chk = $userObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        // 需要检查信用限制
        $credit = $userObj->getCredit();
        if( $credit > 0 ){
            $cashier_balance = $userObj->getPassbookBalance();
            // 换算信用
            $total_amount = system_toolClass::convertMultiCurrencyAmount($cashier_balance,currencyEnum::USD);
            if( $total_amount >= $credit ){
                return new result(false,'Cashier balance out of credit:'.$credit,null,errorCodesEnum::OUT_OF_ACCOUNT_CREDIT);
            }
        }

        $receipt_info = (new loan_penalty_receiptModel())->getRow($receipt_id);
        if( !$receipt_info ){
            return new result(false,'No receipt info:'.$receipt_id,null,errorCodesEnum::INVALID_PARAM);
        }
        if( $receipt_info->state != loanPenaltyReceiptStateEnum::APPROVED ){
            return new result(false,'Apply did not approved.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }
        if( $receipt_info->state == loanPenaltyReceiptStateEnum::COMPLETE ){
            return new result(false,'Already handled.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $biz->scene_code = $this->scene_code;
        $biz->biz_code = $this->biz_code;
        $biz->receipt_id = $receipt_id;
        $biz->member_id = $member_id;
        $biz->operator_id = $user_id;
        $biz->operator_name = $userObj->user_name;
        $biz->cashier_id = $user_id;
        $biz->branch_id = $userObj->branch_id;
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $insert = $biz->insert();
        if( !$insert->STS ){
            return new result(false,'Insert biz fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }
        $biz->biz_id = $biz->uid;

        return new result(true,'success',array(
            'biz_id' => $biz->uid
        ));
    }


    /** 第二步，从客户处收到钱
     * @param $biz_id
     * @param $payment_way
     * @param  $currency_amount
     *  array(
     *    USD 100
     *    KHR 100
     * )
     * @param $remark
     * @return result
     */
    public function receiveMoney($biz_id,$payment_way,$currency_amount,$remark)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info:'.$biz_id,null,errorCodesEnum::NO_DATA);
        }

        if( empty($currency_amount) ){
            return new result(false,'No currency amount.',null,errorCodesEnum::INVALID_PARAM);
        }

        // 不支持的还款方式
        if( !in_array($payment_way,array(
            repaymentWayEnum::CASH,repaymentWayEnum::PASSBOOK
        )) ){
            return new result(false,'Not support payment way:'.$payment_way,null,errorCodesEnum::NOT_SUPPORTED);
        }


        // 先计算金额是否足够
        $receipt_info = (new loan_penalty_receiptModel())->getRow($biz->receipt_id);
        if( !$receipt_info ){
            return new result(false,'No receipt info:'.$biz->receipt_id,null,errorCodesEnum::INVALID_PARAM);
        }

        $receivable_amount = $receipt_info['paid'];
        $receivable_currency = $receipt_info['currency'];


        // 计算的币种金额
        if( $payment_way == repaymentWayEnum::PASSBOOK ){

            $memberObj = new objectMemberClass($biz->member_id);
            $cal_currency = $memberObj->getSavingsAccountBalance();
            $rt = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($receivable_amount,$receivable_currency,$cal_currency);
            if( !$rt->STS ){
                return new result(false,'Amount not enough for:'.$receipt_info->paid.$receipt_info->currency,null,errorCodesEnum::AMOUNT_TOO_LITTLE);
            }
            $currency_amount = $rt->DATA['multi_currency'];
        }else{
            $cal_currency = $currency_amount;
            $rt = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($receivable_amount,$receivable_currency,$cal_currency);
            if( !$rt->STS ){
                return new result(false,'Amount not enough for:'.$receipt_info->paid.$receipt_info->currency,null,errorCodesEnum::AMOUNT_TOO_LITTLE);

            }
        }


        $biz->payment_way = $payment_way;
        $biz->remark = $remark;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Db error:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        // 删掉存在的
        $sql = "delete from biz_receive_member_penalty_detail where biz_id='$biz_id' ";
        $del = $m_biz->conn->execute($sql);
        if( !$del->STS ){
            return new result(false,'Delete old data fail.',null,errorCodesEnum::DB_ERROR);
        }

        // 插入实际收钱详细
        $sql = "insert into biz_receive_member_penalty_detail(biz_id,currency,amount) values ";
        $sql_arr = array();
        foreach( $currency_amount as $c=>$a ){
            $sql_arr[] = "('$biz_id','$c','$a')";
        }
        $sql .= implode(',',$sql_arr);
        $insert = $m_biz->conn->execute($sql);
        if( !$insert->STS ){
            return new result(false,'Insert currency amount fail.',null,errorCodesEnum::DB_ERROR);
        }


        return new result(true,'success',array(
            'biz_id' => $biz_id
        ));
    }


    /** 外部不开放调用
     * @param $receipt_id
     * @return result
     */
    protected function handlerAfterReceivePenalty($receipt_id)
    {
        $m_penalty = new loan_penaltyModel();

        // 更新receipt状态
        $sql = "update loan_penalty_receipt set state='".loanPenaltyReceiptStateEnum::COMPLETE."' 
        where uid='$receipt_id' ";
        $up = $m_penalty->conn->execute($sql);
        if( !$up->STS ){
            return new result(false,'DB error:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        $r = new ormReader();
        $sql = "select * from loan_penalty_receipt_detail where receipt_id='$receipt_id' ";
        $rows = $r->getRows($sql);
        if( count($rows) < 1 ){
            return new result(true);
        }
        $ids = array();
        foreach( $rows as $v ){
            $ids[] = $v['penalty_id'];

        }

        $ids_str = implode(',',$ids);

        // 更新罚金记录状态
        $sql = "update loan_penalty set state='".loanPenaltyHandlerStateEnum::DONE."' where uid in ( $ids_str ) ";
        $up = $m_penalty->conn->execute($sql);
        if( !$up->STS ){
            return new result(false,'Update loan penalty state fail.',null,errorCodesEnum::DB_ERROR);
        }

        // 处理合同
        $sql = "select DISTINCT contract_id from loan_penalty where uid in ($ids_str) ";
        $rows = $r->getRows($sql);
        if( count($rows) > 0 ){
            foreach( $rows as $value ){
                $rt = loan_contractClass::updateContractStateAfterRepayment($value['contract_id']);
                if( !$rt->STS ){
                    return $rt;
                }
            }
        }

        return new result(true);


    }


    /** 录入客户信息
     * @param $biz_id
     * @param $member_image
     * @return result
     */
    public function insertMemberInfo($biz_id,$member_image)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info:'.$biz_id,null,errorCodesEnum::NO_DATA);
        }
        $biz->member_image = $member_image;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Insert client info fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        $m_image = new biz_scene_imageModel();
        $insert = $m_image->insertSceneImage($biz->member_id,$member_image,$this->biz_code,$this->scene_code);
        if( !$insert->STS  ){
            return $insert;
        }

        $biz->biz_id = $biz->uid;
        return new result(true,'success',array(
            'biz_id' => $biz->uid
        ));
    }


    /** 最后一步，提交业务
     * @param $biz_id
     * @param $member_image
     * @return result
     */
    public function bizSubmit($biz_id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info:'.$biz_id,null,errorCodesEnum::NO_DATA);
        }
        if( $biz->state == bizStateEnum::DONE ){
            return new result(true,'success',array(
                'biz_id' => $biz_id
            ));
        }

        if( $biz->biz_code != $this->biz_code ){
            return new result(false,'Un-match operation.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $receipt_model = new loan_penalty_receiptModel();
        $penalty_info = $receipt_model->getRow($biz->receipt_id);

        // 收钱
        $payment_way = $biz->payment_way;
        // 查询实际还款的详细
        $sql = "select * from biz_receive_member_penalty_detail where biz_id=".qstr($biz_id);
        $currency_list = $m_biz->reader->getRows($sql);
        $multi_currency = array();
        foreach ($currency_list as $row) {
            $multi_currency[$row['currency']] = $row['amount'];
        }
        if( !empty($currency_list) ){
            $remark = null;

            switch( $payment_way ){
                case repaymentWayEnum::CASH:
                    // 存钱
                    $rt = passbookWorkerClass::memberDepositByCash(
                        $biz->member_id,
                        $biz->cashier_id,
                        null,
                        null,
                        null,
                        $multi_currency);
                    if (!$rt->STS) return $rt;

                    $remark = 'Repayment loan penalty by cash: cashier '.$biz->cashier_name;
                break;
                case repaymentWayEnum::PASSBOOK:
                    $remark = 'Repayment loan penalty by balance';
                    break;
                default:
                    return new result(false,'Un support repayment way:'.$payment_way,null,errorCodesEnum::NOT_SUPPORTED);
            }

            // 换汇
            foreach( $currency_list as $v ){
                $exchange_trading = new memberExchangeTradingClass(
                    $biz->member_id,
                    $v['amount'],
                    $v['currency'],
                    $penalty_info->currency);
                $rt = $exchange_trading->execute();
                if( !$rt->STS ) return $rt;
            }

            // 罚金收入
            $rt = passbookWorkerClass::memberPaymentLoanPenaltyByBalance(
                $biz->member_id,
                $penalty_info->paid,
                $penalty_info->currency,
                $remark);
            if( !$rt->STS ) return $rt;
        }

        // 处理业务状态
        $rt = $this->handlerAfterReceivePenalty($biz->receipt_id);
        if( !$rt->STS ){
            $biz->update_time = Now();
            $biz->state = bizStateEnum::FAIL;
            $biz->update();
            return $rt;
        }

        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Db error:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'biz_id' => $biz_id
        ));

    }


}