<?php

class bizHeadquarterToBankClass extends bizBaseClass
{
    public function __construct($scene_code)
    {
        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!');
        }

        $this->scene_code = $scene_code;
        $this->biz_code = bizCodeEnum::HEADQUARTER_TO_BANK;
        $this->bizModel = new biz_obj_transferModel();
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

    public function execute($bank_id,$user_id,$trading_password,$amount,$currency,$remark)
    {

        $m_biz = $this->bizModel;

        $accountObj = new objectGlAccountClass(systemAccountCodeEnum::HQ_CIV);
        $chk = $accountObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        $bankObj = new objectSysBankClass($bank_id);
        $chk = $bankObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        $userObj = new objectUserClass($user_id);
        $chk = $userObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }


        $chk = $userObj->checkTradingPassword($trading_password);
        if( !$chk->STS ){
            return $chk;
        }

        $amount = round($amount,2);
        // 验证金额
        if( $amount <= 0 ){
            return new result(false,'Invalid amount.',null,errorCodesEnum::INVALID_AMOUNT);
        }

        $branchBalance = $accountObj->getPassbookCurrencyBalance();
        if( $branchBalance[$currency] < $amount ){
            return new result(false,'Balance not enough.',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
        }

        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->sender_obj_type = $accountObj->object_type;
        $biz->sender_obj_guid = $accountObj->object_id;
        $biz->sender_handler_obj_type = $userObj->object_type;
        $biz->sender_handler_obj_guid = $userObj->object_id;
        $biz->sender_handler_name = $userObj->user_name;
        $biz->receiver_obj_type = $bankObj->object_type;
        $biz->receiver_obj_guid = $bankObj->object_id;
        $biz->receiver_handler_obj_type = objGuidTypeEnum::SYSTEM;
        $biz->receiver_handler_obj_guid = 0;
        $biz->receiver_handler_name = 'System';
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->fee = 0;
        $biz->state = bizStateEnum::CREATE;
        $biz->remark = $remark;
        $biz->create_time = Now();
        $biz->is_outstanding = 0;
        $biz->branch_id = $userObj->branch_id;
        $insert = $biz->insert();
        if( !$insert->STS ){
            return new result(false,'Insert biz fail.'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        $remark =$remark?:("Operator: ".$userObj->user_name);

        // 更新账本
        $rt = (new headquarterToBankTradingClass($bank_id,$amount,$currency,$remark))->execute();
        if( !$rt->STS ){
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
        if( !$up->STS ){
            return new result(false,'Update biz fail',null,errorCodesEnum::DB_ERROR);
        }

        $biz->biz_id = $biz->uid;

        return new result(true,'success',$biz);

    }
}