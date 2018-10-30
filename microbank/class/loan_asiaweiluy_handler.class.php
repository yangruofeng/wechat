<?php

class loan_asiaweiluy_handlerClass extends loan_handlerClass {
    protected function __construct($handler_info)
    {
        parent::__construct($handler_info);
    }


    public function getHandlerMultiCurrencyBalance()
    {
        // 查询余额
        return asiaweiluyClass::queryClientCurrencyBalance($this->handler_info['handler_account']);
    }


    public function getDisbursementSpecialInfo()
    {
        return array(
            'creator_id' => 0,
            'creator_name' => 'System'
        );
    }

    public function getInstallmentSpecialInfo()
    {
        return array(
            'creator_id' => 0,
            'creator_name' => 'System'
        );
    }

    public function apiDisbursementExecute($refBiz, $amount, $currency, $description)
    {

        $trx_model = new partner_trx_apiModel();
        $current_log = $trx_model->getRow(array(
            'ref_biz_type' => $refBiz['type'],
            'ref_biz_sub_type' => $refBiz['sub_type'],
            'ref_biz_account_id' => $refBiz['account_id'],
            'ref_biz_id' => $refBiz['biz_id'],
            'is_manual' => 0,
            'api_state' => array('<>',apiStateEnum::CANCELLED)
        ));
        if ($current_log) {
            if ($current_log->api_state == apiStateEnum::FINISHED) {
                return new result(true, null, $current_log);
            } else {
                return new result(false, 'Uncertain intermediate state of api log', null, errorCodesEnum::UNKNOWN_ERROR);
            }
        }


        $member_info = (new memberModel())->getRow($this->handler_info->member_id);

        $trx_model = new partner_trx_apiModel();
        $trx_info = $trx_model->newRow();
        $trx_info->partner_id = partnerClass::getAsiaweiluyPartnerID();
        $trx_info->obj_guid = $member_info['obj_guid'];
        $trx_info->trx_time = Now();
        $trx_info->remark = $description;
        $trx_info->currency = $currency;
        $trx_info->trx_amount = $amount;
        $trx_info->trx_type = trxTypeEnum::DEC;
        $trx_info->is_manual = 0;
        $trx_info->api_state = apiStateEnum::CREATED;
        $trx_info->api_start_time = Now();
        $trx_info->api_parameter = my_json_encode(array(
            'ace_account' => $this->handler_info->handler_account,
            'amount' => $trx_info->trx_amount,
            'currency' => $trx_info->currency,
            'description' => $trx_info->remark
        ));
        $trx_info->ref_biz_type = $refBiz['type'];
        $trx_info->ref_biz_sub_type = $refBiz['sub_type'];
        $trx_info->ref_biz_account_id = $refBiz['account_id'];
        $trx_info->ref_biz_id = $refBiz['biz_id'];
        $trx_info->creator_id = 0;
        $trx_info->creator_name = 'System';
        $trx_info->create_time = Now();
        $db_ret = $trx_info->insert();
        if (!$db_ret->STS) {
            return new result(false, 'Create Trx Failed - ' . $db_ret->MSG, null, errorCodesEnum::DB_ERROR);
        }

        $api = asiaweiluyApi::Instance();
        $api_ret = $api->disburseStart(
            $this->handler_info->handler_account,
            $trx_info->trx_amount,
            $trx_info->currency,
            $trx_info->remark);

        if ($api_ret->DATA) {
            $trx_info->api_trx_id = $api_ret->DATA['transfer_id'];
        } else {
            $trx_info->api_trx_id = null;
        }

        if (!$api_ret->STS) {
            $trx_info->api_state = apiStateEnum::CANCELLED;
            $trx_info->api_error = $api_ret->MSG;
            $trx_info->update();

            $api_ret->DATA = $trx_info;
            return $api_ret;
        } else {
            $trx_info->api_state = apiStateEnum::STARTED;

            $db_ret = $trx_info->update();
            if (!$db_ret->STS) {
                return new result(false, "Update Trx Id Failed - " . $db_ret->MSG, $trx_info, errorCodesEnum::DB_ERROR);
            }
        }

        try{
            // 测试
            $api_ret = $api->disburseFinish($trx_info->api_trx_id);
            if (!$api_ret->STS) {
                if ($api_ret->CODE == errorCodesEnum::UNKNOWN_ERROR) {
                    $trx_info->api_state = apiStateEnum::PENDING_CHECK;
                } else {
                    $trx_info->api_state = apiStateEnum::CANCELLED;
                }

                $trx_info->api_error = $api_ret->MSG;
                $trx_info->update();

                $api_ret->DATA = $trx_info;
                return $api_ret;
            } else {
                $trx_info->api_state = apiStateEnum::FINISHED;
                $trx_info->update();
                return new result(true,'success',$trx_info);
            }

        }catch( Exception $e ){
            $trx_info->api_state = apiStateEnum::PENDING_CHECK;
            $trx_info->api_error = $e->getMessage();
            $trx_info->update_time = Now();
            $trx_info->update();
            return new result(false,$e->getMessage(),$trx_info,errorCodesEnum::UNKNOWN_ERROR);
        }


    }

    public function apiInstallmentExecute($refBiz, $amount, $currency, $description, $maximizationDeduction=true)
    {
        $trx_model = new partner_trx_apiModel();
        $current_log = $trx_model->getRow(array(
            'ref_biz_type' => $refBiz['type'],
            'ref_biz_sub_type' => $refBiz['sub_type'],
            'ref_biz_account_id' => $refBiz['account_id'],
            'ref_biz_id' => $refBiz['biz_id'],
            'is_manual' => 0,
            'api_state' => array('<>',apiStateEnum::CANCELLED)
        ));
        if ($current_log) {
            if ($current_log->api_state == apiStateEnum::FINISHED) {
                return new result(true, null, $current_log);
            } else {
                return new result(false, 'Uncertain intermediate state of api log', null, errorCodesEnum::UNKNOWN_ERROR);
            }
        }


        $member_info = (new memberModel())->getRow($this->handler_info->member_id);


        $api = asiaweiluyApi::Instance();
        if ($maximizationDeduction) {
            $api_ret = $api->queryClientBalance($this->handler_info->handler_account);
            if (!$api_ret->STS) return $api_ret;
            $client_balance = array();
            foreach ($api_ret->DATA as $item) {
                $client_balance[$item['currency']] = $item['amount'];
            }

            if ($client_balance[$currency] < $amount) {
                $amount = $client_balance[$currency];
            }
        }

        if ($amount <= 0) return new result(false, 'Repayment is finished', null, errorCodesEnum::UNEXPECTED_DATA);

        $trx_info = $trx_model->newRow();
        $trx_info->partner_id = partnerClass::getAsiaweiluyPartnerID();
        $trx_info->obj_guid = $member_info['obj_guid'];
        $trx_info->trx_time = Now();
        $trx_info->remark = $description;
        $trx_info->currency = $currency;
        $trx_info->trx_amount = $amount;
        $trx_info->trx_type = trxTypeEnum::INC;
        $trx_info->is_manual = 0;
        $trx_info->api_state = apiStateEnum::CREATED;
        $trx_info->api_start_time = Now();
        $trx_info->api_parameter = my_json_encode(array(
            'ace_account' => $this->handler_info->handler_account,
            'amount' => $trx_info->trx_amount,
            'currency' => $trx_info->currency,
            'description' => $trx_info->remark
        ));
        $trx_info->ref_biz_type = $refBiz['type'];
        $trx_info->ref_biz_sub_type = $refBiz['sub_type'];
        $trx_info->ref_biz_account_id = $refBiz['account_id'];
        $trx_info->ref_biz_id = $refBiz['biz_id'];
        $trx_info->creator_id = 0;
        $trx_info->creator_name = 'System';
        $trx_info->create_time = Now();
        $db_ret = $trx_info->insert();
        if (!$db_ret->STS) {
            return new result(false, 'Create Trx Failed - ' . $db_ret->MSG, null, errorCodesEnum::DB_ERROR);
        }

        $api_ret = $api->collectStart(
            $this->handler_info->handler_account,
            $trx_info->trx_amount,
            $trx_info->currency,
            $trx_info->remark);

        if ($api_ret->DATA) {
            $trx_info->api_trx_id = $api_ret->DATA['transfer_id'];
        } else {
            $trx_info->api_trx_id = null;
        }

        if (!$api_ret->STS) {
            $trx_info->api_state = apiStateEnum::CANCELLED;
            $trx_info->api_error = $api_ret->MSG;
            $trx_info->update();

            $api_ret->DATA = $trx_info;
            return $api_ret;
        } else {
            $trx_info->api_state = apiStateEnum::STARTED;

            $db_ret = $trx_info->update();
            if (!$db_ret->STS) {
                return new result(false, "Update Trx Id Failed - " . $db_ret->MSG, $trx_info, errorCodesEnum::DB_ERROR);
            }
        }

        try{

            $api_ret = $api->collectFinish($trx_info->api_trx_id);
            if (!$api_ret->STS) {
                if ($api_ret->CODE == errorCodesEnum::UNKNOWN_ERROR) {
                    $trx_info->api_state = apiStateEnum::PENDING_CHECK;
                } else {
                    $trx_info->api_state = apiStateEnum::CANCELLED;
                }

                $trx_info->api_error = $api_ret->MSG;
                $trx_info->update();

                $api_ret->DATA = $trx_info;
                return $api_ret;
            } else {
                $trx_info->api_state = apiStateEnum::FINISHED;
                $trx_info->update();
                return new result(true,'success',$trx_info);
            }

        }catch( Exception $e ){
            $trx_info->api_state = apiStateEnum::PENDING_CHECK;
            $trx_info->api_error = $e->getMessage();
            $trx_info->update_time = Now();
            $trx_info->update();
            return new result(false,$e->getMessage(),$trx_info,errorCodesEnum::UNKNOWN_ERROR);

        }


    }
}