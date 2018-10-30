<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/20
 * Time: 17:59
 */
class bizBankAdjustClass extends bizBaseClass
{
    public function __construct($scene_code)
    {
        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!');
        }

        $this->scene_code = $scene_code;
        $this->biz_code = bizCodeEnum::BANK_ADJUST_FEE_INTEREST;
        $this->bizModel = new biz_bank_adjustModel();
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

    public function execute($user_id,$password,$bank_id,$type,$amount,$remark)
    {
        $m_biz = $this->bizModel;
        $userObj = new objectUserClass($user_id);
        $chk = $userObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        $rt = $userObj->checkTradingPassword($password);
        if( !$rt->STS ){
            return $rt;
        }

        $bankObj = new objectSysBankClass($bank_id);
        $amount = round($amount,2);
        if( $amount <= 0 ){
            return new result(false,'Invalid amount:'.$amount,null,errorCodesEnum::INVALID_AMOUNT);
        }

        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->bank_id = $bank_id;
        $biz->flag = $type;
        $biz->amount = $amount;
        $biz->remark = $remark;
        $biz->state = bizStateEnum::CREATE;
        $biz->operator_id = $user_id;
        $biz->operator_name = $userObj->user_name;
        $biz->branch_id = $userObj->branch_id;
        $biz->create_time = Now();
        $biz->update_time = Now();
        $insert = $biz->insert();
        if( !$insert->STS ){
           return new result(false,'Insert biz fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        if( $type == 1 ){
            $tradingClass = new incomeFromBankTradingClass($bank_id,$amount,$bankObj->currency,incomingTypeEnum::BANK_ADJUST_INTEREST);
            $tradingClass->subject = 'Bank Interest Income';
            $tradingClass->remark = $remark;
            $rt = $tradingClass->execute();

        }else{
            $tradingClass = new payoutFromBankTradingClass($bank_id,$amount,$bankObj->currency,outgoingTypeEnum::BANK_FEE);
            $tradingClass->subject = 'Bank Fee';
            $tradingClass->remark = $remark;
            $rt = $tradingClass->execute();
        }
        if( !$rt->STS ){
            return $rt;
        }

        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Update biz fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'biz_id' => $biz->uid
        ));

    }

}