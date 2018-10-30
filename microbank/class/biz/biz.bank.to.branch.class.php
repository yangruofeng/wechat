<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/8
 * Time: 16:59
 */
class bizBankToBranchClass extends bizBaseClass
{

    public function __construct($scene_code)
    {
        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!');
        }

        $this->scene_code = $scene_code;
        $this->biz_code = bizCodeEnum::BANK_TO_BRANCH;
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

    public function execute($branch_id,$bank_id,$chief_teller_id,$trading_password,$amount,$currency,$remark)
    {
        $m_biz = $this->bizModel;

        $branchObj = new objectBranchClass($branch_id);
        $chk = $branchObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        // 检查分行的信用限制
        $credit = $branchObj->getCredit();
        if( $credit > 0 ){
            $branch_balance = $branchObj->getPassbookCurrencyBalance();
            $total_amount = system_toolClass::convertMultiCurrencyAmount($branch_balance,
                currencyEnum::USD);
            if( $total_amount >= $credit ){
                return new result(false,'Branch cash in vault out of credit limit:'.$credit,null,
                    errorCodesEnum::OUT_OF_ACCOUNT_CREDIT);
            }
        }

        $bankObj = new objectSysBankClass($bank_id);
        $chk = $bankObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        $chiefTellerObj = new objectUserClass($chief_teller_id);
        $chk = $chiefTellerObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        /* 因为分行的银行帐号被总行的treasure托管
        if( $chiefTellerObj->position != userPositionEnum::CHIEF_TELLER ){
            return new result(false,'No access.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }
        */

        $chk = $chiefTellerObj->checkTradingPassword($trading_password);
        if( !$chk->STS ){
            return $chk;
        }

        $amount = round($amount,2);
        // 验证金额
        if( $amount <= 0 ){
            return new result(false,'Invalid amount.',null,errorCodesEnum::INVALID_AMOUNT);
        }

        $bankBalance = $bankObj->getPassbookCurrencyBalance();
        if( $bankBalance[$currency] < $amount ){
            return new result(false,'Balance not enough.',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
        }

        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->sender_obj_type = $bankObj->object_type;
        $biz->sender_obj_guid = $bankObj->object_id;
        $biz->sender_handler_obj_type = objGuidTypeEnum::SYSTEM;
        $biz->sender_handler_obj_guid = 0;
        $biz->sender_handler_name = 'System';
        $biz->receiver_obj_type = $branchObj->object_type;
        $biz->receiver_obj_guid = $branchObj->object_id;
        $biz->receiver_handler_obj_type = $chiefTellerObj->object_type;
        $biz->receiver_handler_obj_guid = $chiefTellerObj->object_id;
        $biz->receiver_handler_name = $chiefTellerObj->user_name;
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->fee = 0;
        $biz->state = bizStateEnum::CREATE;
        $biz->remark = $remark;
        $biz->create_time = Now();
        $biz->is_outstanding = 0;
        $biz->branch_id = $branch_id;
        $insert = $biz->insert();
        if( !$insert->STS ){
            return new result(false,'Insert biz fail.'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        $mark = $remark?:"Operator: ".$chiefTellerObj->user_name;
        // 更新账本
        $rt = (new bankToBranchTradingClass($bank_id,$branch_id,$amount,$currency,$mark))->execute();
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