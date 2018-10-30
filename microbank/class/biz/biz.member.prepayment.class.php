<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/22
 * Time: 14:52
 */
class bizMemberPrepaymentClass extends bizBaseClass
{
    public function __construct($scene_code)
    {

        // 只是counter处理的
        $is_open = $this->checkBizOpen();
        if (!$is_open->STS) {
            throw new Exception('Function close!', errorCodesEnum::FUNCTION_CLOSED);
        }

        $this->scene_code = bizSceneEnum::COUNTER;
        $this->biz_code = bizCodeEnum::MEMBER_PREPAYMENT;
        $this->bizModel = new biz_member_prepaymentModel();
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

        return new result(true, 'success', array(
            'biz_id' => $biz_id
        ));
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
        $sql = "select * from biz_member_prepayment_detail where biz_id=" . qstr($biz_id) . " and
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


    /**
     * 第一步，确认进入提前还款流程
     * @param $user_id
     * @param $member_id
     * @param $apply_id
     * @return result
     */
    public function bizStart($user_id, $member_id, $apply_id)
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


        $m_loan_prepayment_apply = M('loan_prepayment_apply');
        $apply_info = $m_loan_prepayment_apply->find(array('uid' => $apply_id));
        if (!$apply_info) {
            return new result(false, 'No apply info:' . $apply_info, null, errorCodesEnum::INVALID_PARAM);
        }
        if ($apply_info['state'] < prepaymentApplyStateEnum::APPROVED) {
            return new result(false, 'Apply did not approved.', null, errorCodesEnum::UN_MATCH_OPERATION);
        }
        if ($apply_info['state'] == prepaymentApplyStateEnum::SUCCESS) {
            return new result(false, 'Already handled.', null, errorCodesEnum::UN_MATCH_OPERATION);
        }

        $biz->scene_code = $this->scene_code;
        $biz->biz_code = $this->biz_code;
        $biz->apply_id = $apply_id;
        $biz->prepayment_type = $apply_info['prepayment_type'];
        $biz->member_id = $member_id;
        $biz->operator_id = $user_id;
        $biz->operator_name = $userObj->user_name;
        $biz->cashier_id = $user_id;
        $biz->branch_id = $userObj->branch_id;
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $insert = $biz->insert();
        if (!$insert->STS) {
            return new result(false, 'Insert biz fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }
        $biz->biz_id = $biz->uid;

        return new result(true, 'success', array(
            'biz_id' => $biz->uid
        ));
    }

    /** 确认金额币种及还款方式
     * @param $biz_id
     * @param $repayment_way
     * @param $currency_amount
     * @return result
     */
    public function confirmPrepayment($biz_id, $repayment_way, $currency_amount)
    {

        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if (!$biz) {
            return new result(false, 'No biz info:' . $biz_id, null, errorCodesEnum::NO_DATA);
        }

        if (empty($currency_amount)) {
            return new result(false, 'Invalid currency amount.', null, errorCodesEnum::INVALID_PARAM);
        }

        // 判断还款方式是否支持
        $support_way = array(
            repaymentWayEnum::CASH,
            repaymentWayEnum::PASSBOOK
        );

        if (!in_array($repayment_way, $support_way)) {
            return new result(false, 'Not support repayment way.', null, errorCodesEnum::NOT_SUPPORTED);
        }


        $apply_id = $biz['apply_id'];
        $m_loan_prepayment_apply = M('loan_prepayment_apply');
        $prepayment_apply = $m_loan_prepayment_apply->find(array('uid' => $apply_id));
        if (!$prepayment_apply) {
            return new result(false, 'Invalid Apply.', null, errorCodesEnum::NO_DATA);
        }

        if ($prepayment_apply['state'] < prepaymentApplyStateEnum::APPROVED) {
            return new result(false, 'Apply not approved.', null, errorCodesEnum::UN_MATCH_OPERATION);
        }

        //重新计算提前还款额
        $rt = loan_contractClass::prepaymentPreview(array(
                'contract_id' => $prepayment_apply['contract_id'],
                'prepayment_type' => $prepayment_apply['prepayment_type'],
                'amount' => $prepayment_apply['apply_principal_amount'],
                'repay_period' => $prepayment_apply['repay_period'],
                'deadline_date' => $prepayment_apply['deadline_date'],
            )
        );
        if (!$rt->STS) {
            return $rt;
        }

        $prepayment_data = $rt->DATA;

        $apply_amount = $prepayment_data['total_prepayment_amount'];
        $apply_currency = $prepayment_apply['currency'];

        // 验证金额是否足够
        $paid_amount = 0;
        foreach ($currency_amount as $c => $a) {
            // 买入还款合同货币
            $exchange_rate = global_settingClass::getCurrencyRateBetween($c, $apply_currency);
            $exchange_amount = round($a * $exchange_rate, 2);
            $paid_amount += $exchange_amount;
        }
        if ($paid_amount < $apply_amount) {
            return new result(false, 'Amount not enough.', null, errorCodesEnum::INVALID_AMOUNT);
        }

        $biz->repayment_way = $repayment_way;
        $biz->update_time = Now();
        $up = $biz->update();
        if (!$up->STS) {
            return new result(false, 'Update biz fail.', null, errorCodesEnum::DB_ERROR);
        }

        // 删除原来的，防止重复
        $sql = "delete from biz_member_prepayment_detail where amount_type='0' and biz_id='$biz_id' ";
        $del = $m_biz->conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete old data fail.', null, errorCodesEnum::DB_ERROR);
        }

        // todo 需要整理下和MEMBER_APP一致
        //插入应还的金额币种
        $sql = "insert into biz_member_prepayment_detail(biz_id,currency,amount,amount_type) values
              ('$biz_id','$apply_currency','$apply_amount','0') ";
        $insert = $this->bizModel->conn->execute($sql);
        if (!$insert->STS) {
            return new result(false, 'Insert payable currency amount fail.', null, errorCodesEnum::DB_ERROR);
        }


        // 删除原来的，防止重复
        $sql = "delete from biz_member_prepayment_detail where amount_type='1' and biz_id='$biz_id' ";
        $del = $m_biz->conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete old data fail.', null, errorCodesEnum::DB_ERROR);
        }

        //插入实际还款的金额币种
        $sql = "insert into biz_member_prepayment_detail(biz_id,currency,amount,amount_type) values ";
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
            'biz_id' => $biz_id,
            'prepayment_type' => $repayment_way,
            'currency_amount' => $currency_amount
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


    /**
     * 提交完成
     */
    public function bizSubmit($biz_id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if (!$biz) {
            return new result(false, 'No biz info:' . $biz_id, null, errorCodesEnum::NO_DATA);
        }
        if ($biz->state == bizStateEnum::DONE) {
            return new result(true, 'success', array(
                'biz_id' => $biz_id
            ));
        }
        if ($biz->biz_code != $this->biz_code) {
            return new result(false, 'Un-match operation.', null, errorCodesEnum::UN_MATCH_OPERATION);
        }


        $prepayment_apply = (new loan_prepayment_applyModel())->getRow($biz->apply_id);
        if (!$prepayment_apply) {
            return new result(false, 'No apply info.', null, errorCodesEnum::NO_DATA);
        }

        if ($prepayment_apply->state == prepaymentApplyStateEnum::SUCCESS) {
            return new result(true);
        }


        $repayment_way = $biz['repayment_way'];
        $apply_id = $biz['apply_id'];
        $cashier_id = $biz['cashier_id'];


        // 应还的金额
        $sql = "select * from biz_member_prepayment_detail where amount_type='0' and biz_id=" . qstr($biz_id);
        $rows = $m_biz->reader->getRows($sql);
        $exchange_currency_amount = array();
        foreach ($rows as $v) {
            $exchange_currency_amount[$v['currency']] = $v['amount'];
        }

        // 实际还款的金额
        $sql = "select * from biz_member_prepayment_detail where amount_type='1' and biz_id=" . qstr($biz_id);
        $rows = $m_biz->reader->getRows($sql);
        $currency_amount = array();
        foreach ($rows as $v) {
            $currency_amount[$v['currency']] = $v['amount'];
        }


        switch ($repayment_way) {
            case repaymentWayEnum::CASH:
                $rt = loanRepaymentWorkerClass::prepaymentByCash($apply_id, $cashier_id, null, null, $currency_amount, $exchange_currency_amount);
                if (!$rt->STS) {
                    $biz->state = bizStateEnum::FAIL;
                    $biz->update_time = Now();
                    $biz->update();
                    return $rt;
                }
                break;
            case repaymentWayEnum::PASSBOOK:
                $rt = loanRepaymentWorkerClass::prepaymentByBalance($apply_id, $cashier_id, null, null, $currency_amount);
                if (!$rt->STS) {
                    $biz->state = bizStateEnum::FAIL;
                    $biz->update_time = Now();
                    $biz->update();
                    return $rt;
                }
                break;
            default:
                return new result(false, 'Invalid repayment way:' . $repayment_way, null, errorCodesEnum::NOT_SUPPORTED);

        }

        // 更新那条申请的状态
        $prepayment_apply->state = prepaymentApplyStateEnum::SUCCESS;
        $prepayment_apply->update_time = Now();
        $up = $prepayment_apply->update();
        if (!$up->STS) {
            return new result(false, 'Update prepayment apply fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }


        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $up = $biz->update();
        if (!$up->STS) {
            return new result(false, 'Update biz fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $biz);

    }

}