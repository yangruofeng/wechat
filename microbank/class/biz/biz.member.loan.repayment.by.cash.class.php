<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/18
 * Time: 17:06
 */
class bizMemberLoanRepaymentByCashClass extends bizBaseClass
{
    public function __construct($scene_code = bizSceneEnum::COUNTER)
    {

        $is_open = $this->checkBizOpen();
        if (!$is_open->STS) {
            throw new Exception('Function close!');
        }

        // todo 是否只能在counter操作
        $this->scene_code = bizSceneEnum::COUNTER;
        $this->biz_code = bizCodeEnum::MEMBER_LOAN_REPAYMENT_BY_CASH;
        $this->bizModel = new biz_member_loan_repaymentModel();
    }

    public function checkBizOpen()
    {
        if( global_settingClass::isForbiddenReturnLoan() ){
            return new result(false,'Close loan repayment.',null,errorCodesEnum::FUNCTION_CLOSED);
        }
        return new result(true);
    }

    public function getBizDetailById($id)
    {
        return $this->bizModel->find(array(
            'uid' => $id
        ));
    }

    public function checkTellerPassword($biz_id, $card_no, $key)
    {
        $m = $this->bizModel;
        $biz = $m->getRow($biz_id);
        if (!$biz) {
            return new result(false, 'No biz info.', null, errorCodesEnum::NO_DATA);
        }

        $userObj = new objectUserClass($biz->cashier_id);
        $branch_id = $userObj->branch_id;
        $chk = $this->checkTellerAuth($biz->cashier_id, $branch_id, $card_no, $key);
        if (!$chk->STS) {
            return $chk;
        }
        $biz->cashier_trading_password = $userObj->trading_password;
        $biz->cashier_name = $userObj->user_name;
        $biz->update_time = Now();
        $up = $biz->update();

        return new result(true);
    }


    public function checkChiefTellerPassword($biz_id, $card_no, $key)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if (!$biz) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $cashier_id = $biz->cashier_id;
        $cashierObj = new objectUserClass($cashier_id);

        $branch_id = $cashierObj->branch_id;
        $rt = $this->checkChiefTellerAuth($branch_id, $card_no, $key);

        if (!$rt->STS) {
            return $rt;
        }

        $user_id = $rt->DATA;
        $chiefTeller = new objectUserClass($user_id);
        $biz->bm_id = $user_id;
        $biz->bm_trading_password = $chiefTeller->trading_password;
        $biz->bm_name = $chiefTeller->user_name;
        $biz->update_time = Now();
        $up = $biz->update();


        return new result(true, 'success', array(
            'biz_id' => $biz_id
        ));

    }

    public function isNeedCTApprove($biz_id)
    {
        $r = new ormReader();
        $sql = "select * from biz_member_loan_repayment_detail where biz_id=" . qstr($biz_id) . " and
        amount_type = '1' ";
        $rows = $r->getRows($sql);
        $multi_currency = array();
        foreach ($rows as $v) {
            $multi_currency[$v['currency']] = $v['amount'];
        }
        $biz_info = $this->getBizDetailById($biz_id);
        $branch_id = $biz_info['branch_id'];
        return $this->counterBizIsNeedCTApprove($multi_currency,$branch_id);
    }


    /** 第一步，选择计划
     * @param $cashier_id
     * @param $member_id
     * @param array $schema_list
     * @return result
     */
    public function stepSelectSchemas($cashier_id, $member_id, $schema_list = array())
    {
        if (empty($schema_list)) {
            return new result(false, 'Empty schema list.', null, errorCodesEnum::INVALID_PARAM);
        }

        $cashierObj = new objectUserClass($cashier_id);
        $chk = $cashierObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        // 需要检查信用限制
        $credit = $cashierObj->getCredit();
        if( $credit > 0 ){
            $cashier_balance = $cashierObj->getPassbookBalance();
            // 换算信用
            $total_amount = system_toolClass::convertMultiCurrencyAmount($cashier_balance,currencyEnum::USD);
            if( $total_amount >= $credit ){
                return new result(false,'Cashier balance out of credit:'.$credit,null,errorCodesEnum::OUT_OF_ACCOUNT_CREDIT);
            }
        }

        $memberObj = new objectMemberClass($member_id);
        $chk = $memberObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }


        $currency_total = array();
        $m_schema = new loan_installment_schemeModel();
        $schema_list_info = $m_schema->getSchemaDetailByIds($schema_list);
        foreach ($schema_list_info as $schema_info) {
            $rt = loan_contractClass::getContractInterestInfo($schema_info['contract_id']);
            if (!$rt->STS) {
                continue;
            }
            $interest_info = $rt->DATA;
            $interestClass = interestTypeClass::getInstance($schema_info['repayment_type'], $schema_info['repayment_period']);
            $schema_info = $interestClass->calculateRepaymentInterestOfSchema($schema_info, $interest_info);
            $total_amount = $schema_info['amount'] - $schema_info['actual_payment_amount'];

            if ($currency_total[$schema_info['currency']]) {
                $currency_total[$schema_info['currency']] += $total_amount;
            } else {
                $currency_total[$schema_info['currency']] = $total_amount;
            }

        }

        $repayment_way = repaymentWayEnum::CASH;
        // 插入申请，与APP一致
        $rt = loan_contractClass::insertSchemaRepaymentApplyInfo($schema_list, $repayment_way, $member_id, array());
        if (!$rt->STS) {
            return $rt;
        }
        $repayment_request = $rt->DATA;


        // 插入业务表
        $biz = $this->bizModel->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->repayment_type = 0;
        $biz->repayment_way = repaymentWayEnum::CASH;
        $biz->member_id = $member_id;
        $biz->request_id = $repayment_request['uid'];
        $biz->cashier_id = $cashier_id;
        $biz->cashier_name = $cashierObj->user_name;
        $biz->create_time = Now();
        $biz->branch_id = $cashierObj->branch_id;
        $insert = $biz->insert();
        if (!$insert->STS) {
            return new result(false, 'Insert biz fail.', null, errorCodesEnum::DB_ERROR);
        }

        $biz_id = $biz->uid;

        // 插入应还的金额统计
        $sql = "insert into biz_member_loan_repayment_detail(biz_id,currency,amount,amount_type) values ";
        $sql_arr = array();
        foreach ($currency_total as $c => $a) {
            $sql_arr[] = "('$biz_id','$c','$a','0')";
        }
        $sql .= implode(',', $sql_arr);
        $insert = $this->bizModel->conn->execute($sql);
        if (!$insert->STS) {
            return new result(false, 'Insert currency amount fail.', null, errorCodesEnum::DB_ERROR);
        }


        return new result(true, 'success', array(
            'biz_id' => $biz_id,
            'total_amount' => $currency_total,
        ));


    }


    /** 第二步，输入实际还款的金额和币种
     * @param $biz_id
     * @param $currency_amount
     * array(
     *    'USD' => 100
     *    'KHR' => 100
     * )
     * @return result
     */
    public function stepInputAmount($biz_id, $currency_amount)
    {
        $biz = $this->bizModel->getRow($biz_id);
        if (!$biz) {
            return new result(false, 'No biz info:' . $biz_id, null, errorCodesEnum::NO_DATA);
        }

        if (empty($currency_amount)) {
            return new result(false, 'Invalid currency amount', null, errorCodesEnum::INVALID_AMOUNT);
        }

        $conn = $this->bizModel->conn;

        // 先删除原来的，防止重复
        $sql = "delete from biz_member_loan_repayment_detail where amount_type='1' and biz_id='$biz_id' ";
        $del = $conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete old data fail.', null, errorCodesEnum::DB_ERROR);
        }

        // 插入新的实际还款金额明细
        $sql = "insert into biz_member_loan_repayment_detail(biz_id,currency,amount,amount_type) values ";
        $sql_arr = array();
        foreach ($currency_amount as $c => $a) {
            if ($a > 0) {
                $sql_arr[] = "('$biz_id','$c','$a','1')";
            }
        }

        if (empty($sql_arr)) {
            return new result(false, 'Invalid currency amount.', null, errorCodesEnum::INVALID_PARAM);
        }

        $sql .= implode(',', $sql_arr);
        $insert = $this->bizModel->conn->execute($sql);
        if (!$insert->STS) {
            return new result(false, 'Insert currency amount fail.', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', array(
            'biz_id' => $biz_id
        ));
    }


    /** 录入客户信息
     * @param $biz_id
     * @param $member_image
     * @return result
     */
    public function insertMemberInfo($biz_id, $member_image)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if (!$biz) {
            return new result(false, 'No biz info:' . $biz_id, null, errorCodesEnum::NO_DATA);
        }
        $biz->member_image = $member_image;
        $biz->update_time = Now();
        $up = $biz->update();
        if (!$up->STS) {
            return new result(false, 'Insert client info fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        $m_image = new biz_scene_imageModel();
        $insert = $m_image->insertSceneImage($biz->member_id,$member_image,$this->biz_code,$this->scene_code);
        if( !$insert->STS  ){
            return $insert;
        }

        $biz->biz_id = $biz->uid;
        return new result(true, 'success', array(
            'biz_id' => $biz->uid
        ));
    }


    /** 完成密码验证后（第三步）,第四步，提交业务（外部使用事务）
     * @param $biz_id
     * @return result
     */
    public function bizSubmit($biz_id)
    {
        $biz = $this->bizModel->getRow($biz_id);
        if (!$biz) {
            return new result(false, 'No biz info:' . $biz_id, null, errorCodesEnum::NO_DATA);
        }

        if ($biz->biz_code != $this->biz_code) {
            return new result(false, 'Invalid biz:' . $biz_id, null, errorCodesEnum::INVALID_PARAM);
        }

        if ($biz->state == bizStateEnum::DONE) {
            return new result(true, 'success', $biz);
        }

        $m_apply = new loan_request_repaymentModel();
        $apply = $m_apply->getRow($biz->request_id);
        if (!$apply) {
            return new result(false, 'Invalid biz:' . $biz_id, null, errorCodesEnum::INVALID_PARAM);
        }

        // 获得所有计划
        $m_detail = new loan_request_repayment_detailModel();
        $rows = $m_detail->getRows(array(
            'request_id' => $biz->request_id
        ));
        if (count($rows) < 1) {
            return new result(false, 'No select schemas.', null, errorCodesEnum::NO_DATA);
        }

        $schema_ids = array();
        foreach ($rows as $v) {
            $schema_ids[] = $v['scheme_id'];
        }


        // 获取实际还款的多货币
        $sql = "select * from biz_member_loan_repayment_detail where amount_type='1' and biz_id=" . qstr($biz_id);
        $rows = $this->bizModel->reader->getRows($sql);
        $multi_currency = array();
        foreach ($rows as $v) {
            $multi_currency[$v['currency']] = $v['amount'];
        }

        // 理论的目标货币及金额
        $sql = "select * from biz_member_loan_repayment_detail where amount_type='0' and biz_id=" . qstr($biz_id);
        $rows = $this->bizModel->reader->getRows($sql);
        $exchange_currency_amount = array();
        foreach ($rows as $v) {
            $exchange_currency_amount[$v['currency']] = $v['amount'];
        }

        // 执行还款
        $rt = loanRepaymentWorkerClass::schemasRepaymentByCash($biz->member_id, $biz->cashier_id, $schema_ids, null, null, $multi_currency, $exchange_currency_amount);
        if (!$rt->STS) {
            $biz->state = bizStateEnum::FAIL;
            $biz->update_time = Now();
            $biz->update();
            return $rt;
        }


        $trade_id = intval($rt->DATA);
        $biz->passbook_trading_id = $trade_id;  // 已经无意义了
        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $up = $biz->update();
        if (!$up->STS) {
            return new result(false, 'Update biz info fail.', null, errorCodesEnum::DB_ERROR);
        }

        // 更新request状态
        $apply->state = requestRepaymentStateEnum::SUCCESS;
        $apply->update_time = Now();
        $apply->handler_id = 0;
        $apply->handler_name = 'System';
        $apply->handle_remark = 'System auto handle';
        $apply->handle_time = Now();
        $apply->update();

        // 完成
        return new result(true, 'success', $biz);

    }


}