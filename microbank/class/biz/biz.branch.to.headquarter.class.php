<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/8
 * Time: 11:33
 */
class bizBranchToHeadquarterClass extends bizBaseClass
{
    public function __construct($scene_code)
    {
        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!');
        }

        $this->scene_code = $scene_code;
        $this->biz_code = bizCodeEnum::BRANCH_TO_HEADQUARTER;
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


    public function execute($branch_id,$amount,$currency,$remark,$operator_id)
    {
        $m_biz = $this->bizModel;

        $operatorObj = new objectUserClass($operator_id);
        $chk = $operatorObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        $accountObj = new objectGlAccountClass(systemAccountCodeEnum::HQ_CIV);
        $chk = $accountObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        $branchObj = new objectBranchClass($branch_id);
        $chk = $branchObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }


        // 验证金额
        $amount = round($amount,2);
        if( $amount <= 0 ){
            return new result(false,'Invalid amount.',null,errorCodesEnum::INVALID_AMOUNT);
        }
        $branch_balance = $branchObj->getPassbookCurrencyBalance();
        if( $branch_balance[$currency] < $amount ){
            return new result(false,'Balance not enough.',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
        }

        $mark = $remark?:"Operator: ".$operatorObj->user_name;


        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->sender_obj_type = $branchObj->object_type;
        $biz->sender_obj_guid = $branchObj->object_id;
        $biz->sender_handler_obj_type = $operatorObj->object_type;
        $biz->sender_handler_obj_guid = $operatorObj->object_id;
        $biz->sender_handler_name = $operatorObj->user_name;
        $biz->receiver_obj_type = $accountObj->object_type;
        $biz->receiver_obj_guid = $accountObj->object_id;
        $biz->receiver_handler_obj_type = objGuidTypeEnum::SYSTEM;
        $biz->receiver_handler_obj_guid = 0;
        $biz->receiver_handler_name = '';
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->fee = 0;
        $biz->state = bizStateEnum::CREATE;
        $biz->remark = $remark;
        $biz->create_time = Now();
        $biz->is_outstanding = 0;  // 需要确认的
       // $biz->passbook_trading_id = $trade_id;
        $biz->branch_id = $branch_id;
        $insert = $biz->insert();
        if( !$insert->STS ){
            return new result(false,'Insert biz fail.'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        // 更新账本
        $rt = (new branchToHeadquarterTradingClass($branch_id,$amount,$currency,$mark))->execute();
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