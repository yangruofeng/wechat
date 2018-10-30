<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/8
 * Time: 16:59
 */
class bizCapitalToCivClass extends bizBaseClass
{

    public function __construct($scene_code)
    {
        $is_open = $this->checkBizOpen();
        if (!$is_open->STS) {
            throw new Exception('Function close!');
        }

        $this->scene_code = $scene_code;
        $this->biz_code = bizCodeEnum::CAPITAL_TO_CIV;
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

    public function execute($user_id, $amount, $currency, $remark)
    {
        $m_biz = $this->bizModel;

        $capitalObj = new objectGlAccountClass(systemAccountCodeEnum::HQ_CAPITAL);
        $chk = $capitalObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $civObj = new objectGlAccountClass(systemAccountCodeEnum::HQ_CIV);
        $chk = $civObj->checkValid();
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

        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->sender_obj_type = $capitalObj->object_type;
        $biz->sender_obj_guid = $capitalObj->object_id;
        $biz->sender_handler_obj_type = $userObj->object_type;
        $biz->sender_handler_obj_guid = $userObj->object_id;
        $biz->sender_handler_name = $userObj->user_name;
        $biz->receiver_obj_type = $civObj->object_type;
        $biz->receiver_obj_guid = $civObj->object_id;
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
        if (!$insert->STS) {
            return new result(false, 'Insert biz fail.' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        // 更新账本
        $rt = (new capitalReceiveTradingClass($amount, $currency))->execute();
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