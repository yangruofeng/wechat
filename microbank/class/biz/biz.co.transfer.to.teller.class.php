<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/25
 * Time: 16:08
 */
class bizCoTransferToTellerClass extends bizBaseClass
{
    public function __construct()
    {

        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!');
        }
        $this->scene_code = bizSceneEnum::APP_CO;
        $this->biz_code = bizCodeEnum::CO_TRANSFER_TO_TELLER;
        $this->bizModel = new biz_obj_transferModel();
    }

    public function getBizDetailById($id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->find(array(
            'uid' => $id
        ));
        return $biz;
    }

    public function checkBizOpen()
    {
        return new result(true);
    }


    public function execute($co_id,$co_trading_password,$teller_id,$amount,$currency,$remark=null)
    {
        $coObj = new objectUserClass($co_id);
        $chk = $coObj->checkTradingPassword($co_trading_password);
        if( !$chk->STS ){
            return $chk;
        }

        $tellerObj = new objectUserClass($teller_id);
        $chk = $tellerObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        $amount = round($amount,2);
        if( $amount <= 0 ){
            return new result(false,'');
        }

        // 检查余额
        $co_balance = $coObj->getPassbookBalance();
        if( $co_balance[$currency] < $amount ){
            return new result(false,'Balance not enough.',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
        }

        // 转账
        $mark = 'Co transfer to teller:co '.$coObj->user_name;
        $rt = (new userToUserTradingClass($co_id,$teller_id,$amount,$currency,$mark))->execute();
        if( !$rt->STS ){
            return $rt;
        }
        $trade_id = intval($rt->DATA);

        $m_biz = $this->bizModel;
        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->sender_obj_type = $coObj->object_type;
        $biz->sender_obj_guid = $coObj->object_id;
        $biz->sender_handler_obj_type = $coObj->object_type;
        $biz->sender_handler_obj_guid = $coObj->object_id;
        $biz->sender_handler_name = $coObj->user_name;
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
        $biz->branch_id = $coObj->branch_id;

        $insert = $biz->insert();
        if( !$insert->STS ){
            return new result(false,'Insert biz fail.'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        $biz->biz_id = $biz->uid;

        $url = getUrl('co_app','tellerReceiveConfirm',array('uid'=>$biz->uid),false,WAP_OPERATOR_SITE_URL);

        $biz->qrcode_content = $url;
        return new result(true,'success',$biz);

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


        $m_user = new um_userModel();


        // 构建签名
        $sign = array();

        $co = $m_user->getRow(array(
            'obj_guid' => $biz->sender_obj_guid
        ));
        if( !$co ){
            return new result(false,'No user.',null,errorCodesEnum::USER_NOT_EXISTS);
        }

        $sign[$co->obj_guid] = md5($biz->passbook_trading_id.$biz->currency.round($biz->amount,2).$co->trading_password);


        $teller = $m_user->getRow(array(
            'obj_guid' => $biz->receiver_obj_guid
        ));
        if( !$teller ){
            return new result(false,'No user.',null,errorCodesEnum::USER_NOT_EXISTS);
        }

        $sign[$teller->obj_guid] = md5($biz->passbook_trading_id.$biz->currency.round($biz->amount,2).$teller->trading_password);



        // 交易确认
        $rt = userToUserTradingClass::confirm($biz->passbook_trading_id,$sign);
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

    public function cancel($biz_id)
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
        $m_user = new um_userModel();

        // 构建签名
        $sign = array();

        $co = $m_user->getRow(array(
            'obj_guid' => $biz->sender_obj_guid
        ));
        if( !$co ){
            return new result(false,'No user.',null,errorCodesEnum::USER_NOT_EXISTS);
        }

        $sign[$co->obj_guid] = md5($biz->passbook_trading_id.$biz->currency.round($biz->amount,2).$co->trading_password);


        $teller = $m_user->getRow(array(
            'obj_guid' => $biz->receiver_obj_guid
        ));
        if( !$teller ){
            return new result(false,'No user.',null,errorCodesEnum::USER_NOT_EXISTS);
        }

        $sign[$teller->obj_guid] = md5($biz->passbook_trading_id.$biz->currency.round($biz->amount,2).$teller->trading_password);


        $rt = userToUserTradingClass::reject($biz->passbook_trading_id,$sign);
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