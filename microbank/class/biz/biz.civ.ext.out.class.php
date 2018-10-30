<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/8
 * Time: 16:59
 */
class bizCivExtOutClass extends bizBaseClass
{

    public function __construct($scene_code)
    {
        $is_open = $this->checkBizOpen();
        if (!$is_open->STS) {
            throw new Exception('Function close!');
        }

        $this->scene_code = $scene_code;
        $this->biz_code = bizCodeEnum::CIV_EXT_OUT;
        $this->bizModel = new biz_civ_adjustModel();
    }

    public function checkBizOpen()
    {
        return new result(true);
    }

    public function getBizDetailById($id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->find(array(
            'uid' => $id
        ));
        return $biz;
    }

    public function execute($branch_id,$user_id, $amount, $currency, $remark,$ext_trade_type=0)
    {
        $m_biz = $this->bizModel;
        if($branch_id==0){
            $civObj = new objectGlAccountClass(systemAccountCodeEnum::HQ_CIV);
        }else{
            $civObj = new objectBranchClass($branch_id);
        }


        $chk = $civObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $codObj = new objectGlAccountClass(systemAccountCodeEnum::BRANCH_CIV_EXT_OUT);
        $chk = $codObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $userObj = new objectUserClass($user_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }
        // TODO: 权限验证

        $amount = round($amount, 2);
        // 验证金额
        if ($amount <= 0) {
            return new result(false, 'Invalid amount.', null, errorCodesEnum::INVALID_AMOUNT);
        }

        $civBalance = $civObj->getPassbookCurrencyBalance();
        if ($civBalance[$currency] < $amount) {
            return new result(false, 'Balance not enough.', null, errorCodesEnum::BALANCE_NOT_ENOUGH);
        }

        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->branch_id = $branch_id;
        $biz->flag=flagTypeEnum::PAYOUT;
        $biz->operator_id = $userObj->user_id;
        $biz->operator_name = $userObj->user_name;
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->state = bizStateEnum::CREATE;
        $biz->ext_trade_type=intval($ext_trade_type);
        $biz->remark = $remark;
        $biz->create_time = Now();

        $insert = $biz->insert();
        if (!$insert->STS) {
            return new result(false, 'Insert biz fail.' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        // 更新账本
        $rt = (new civExtOutTradingClass($branch_id,$amount, $currency,$biz->remark))->execute();
        if (!$rt->STS) {
            $biz->state = bizStateEnum::FAIL;
            $biz->update_time = Now();
            $biz->update();
            return $rt;
        }

        $trade_id = intval($rt->DATA);
        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $biz->passbook_trading_id = $trade_id;
        $up = $biz->update();
        if (!$up->STS) {
            return new result(false, 'Update biz fail', null, errorCodesEnum::DB_ERROR);
        }

        $biz->biz_id = $biz->uid;

        return new result(true, 'success', $biz);
    }
}