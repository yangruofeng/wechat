<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/8
 * Time: 16:06
 */
class bizBranchToTellerClass extends bizBaseClass
{
    public function __construct($scene_code)
    {
        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!');
        }

        $this->scene_code = $scene_code;
        $this->biz_code = bizCodeEnum::BRANCH_TO_TELLER;
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

    public function execute($branch_id,$chief_teller_id,$chief_teller_trading_password,$teller_id,$amount,$currency,$remark)
    {
        $m_biz = $this->bizModel;

        $branchObj = new objectBranchClass($branch_id);
        $chk = $branchObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        $chiefTellerObj = new objectUserClass($chief_teller_id);
        $chk = $chiefTellerObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        $tellerObj = new objectUserClass($teller_id);
        $chk = $tellerObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        // 需要检查信用限制
        $credit = $tellerObj->getCredit();
        if( $credit > 0 ){
            $cashier_balance = $tellerObj->getPassbookBalance();
            // 换算信用
            $total_amount = system_toolClass::convertMultiCurrencyAmount($cashier_balance,currencyEnum::USD);
            if( $total_amount >= $credit ){
                return new result(false,'Cashier balance out of credit:'.$credit,null,errorCodesEnum::OUT_OF_ACCOUNT_CREDIT);
            }
        }


        $amount = round($amount,2);
        // 验证chief teller 交易密码
        $chk = $chiefTellerObj->checkTradingPassword($chief_teller_trading_password);
        if( !$chk->STS ){
            return $chk;
        }

        // 验证金额
        if( $amount <= 0 ){
            return new result(false,'Invalid amount.',null,errorCodesEnum::INVALID_AMOUNT);
        }

        $branchBalance = $branchObj->getPassbookCurrencyBalance();
        if( $branchBalance[$currency] < $amount ){
            return new result(false,'Balance not enough.',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
        }

        $mark = $remark?:"Operator: ".$chiefTellerObj->user_name;
        // 执行账本
        $rt = (new branchToCashierTradingClass($branch_id,$teller_id,$amount,$currency,$mark))->execute();
        if( !$rt->STS ){
            return $rt;
        }

        $trade_id = $rt->DATA;

        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->sender_obj_type = $branchObj->object_type;
        $biz->sender_obj_guid = $branchObj->object_id;
        $biz->sender_handler_obj_type = $chiefTellerObj->object_type;
        $biz->sender_handler_obj_guid = $chiefTellerObj->object_id;
        $biz->sender_handler_name = $chiefTellerObj->user_name;
        $biz->receiver_obj_type = $tellerObj->object_type;
        $biz->receiver_obj_guid = $tellerObj->object_id;
        $biz->receiver_handler_obj_type = $tellerObj->object_type;
        $biz->receiver_handler_obj_guid = $tellerObj->object_id;
        $biz->receiver_handler_name = $tellerObj->user_name;
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->fee = 0;
        $biz->state = bizStateEnum::CREATE;
        $biz->remark = $remark;
        $biz->create_time = Now();
        $biz->is_outstanding = 1;  // 需要确认的
        $biz->passbook_trading_id = $trade_id;
        $biz->branch_id = $branch_id;
        $insert = $biz->insert();
        if( !$insert->STS ){
            return new result(false,'Insert biz fail.',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'biz_id' => $biz->uid
        ));


    }

    public function confirm($biz_id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid biz id : '.$biz_id,null,errorCodesEnum::NO_DATA);
        }

        if( $biz->biz_code != $this->biz_code ){
            return new result(false,'Invalid biz type.',null,errorCodesEnum::INVALID_PARAM);
        }

        if( $biz->state == bizStateEnum::DONE ){
            return new result(true,'success');
        }

        // 构建签名
        $teller = (new um_userModel())->getRow(array(
            'obj_guid' => $biz->receiver_obj_guid
        ));
        if( !$teller ){
            return new result(false,'No user.',null,errorCodesEnum::USER_NOT_EXISTS);
        }

        $sign = array(
            $teller->obj_guid => md5($biz->passbook_trading_id.$biz->currency.round($biz->amount,2).$teller->trading_password)
        );

        // 交易确认
        $rt = branchToCashierTradingClass::confirm($biz->passbook_trading_id,$sign);

        if( !$rt->STS ){
            $biz->state = bizStateEnum::FAIL;
            $biz->update_time = Now();
            $biz->update();
            return $rt;
        }

        $biz->is_outstanding = 0;
        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Update biz fail.',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');
    }


    public function cancel( $biz_id )
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid biz id : '.$biz_id,null,errorCodesEnum::NO_DATA);
        }

        if( $biz->biz_code != $this->biz_code ){
            return new result(false,'Invalid biz type.',null,errorCodesEnum::INVALID_PARAM);
        }

        if( $biz->state == bizStateEnum::DONE ){
            return new result(false,'Biz done.',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        if( $biz->state == bizStateEnum::REJECT ){
            return new result(true,'success');
        }

        // 取消账本的交易
        // 构建签名
        $teller = (new um_userModel())->getRow(array(
            'obj_guid' => $biz->receiver_obj_guid
        ));
        if( !$teller ){
            return new result(false,'No user.',null,errorCodesEnum::USER_NOT_EXISTS);
        }

        $sign = array(
            $teller->obj_guid => md5($biz->passbook_trading_id.$biz->currency.round($biz->amount,2).$teller->trading_password)
        );
        $rt = branchToCashierTradingClass::reject($biz->passbook_trading_id,$sign);
        if( !$rt->STS ){
            return $rt;
        }

        $biz->is_outstanding = 0;
        $biz->state = bizStateEnum::REJECT;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Update biz fail.',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');

    }



}