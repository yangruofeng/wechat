<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/14
 * Time: 17:04
 */
class bizMemberDepositByPartnerClass extends bizMemberDepositClass
{
    public function __construct($scene_code)
    {
        parent::__construct($scene_code);
        $this->biz_code = bizCodeEnum::MEMBER_DEPOSIT_BY_PARTNER;
        $this->scene_code = $scene_code;
    }



    /**
     * @return result
     */
    protected function checkLimit($member_id,$amount,$currency)
    {

        switch( $this->scene_code )
        {
            case bizSceneEnum::APP_MEMBER:

                // 检查member限制
                $chk = $this->checkMemberLimit($member_id,$amount,$currency);
                if( !$chk->STS ){
                    return $chk;
                }
                return new result(true);

            case bizSceneEnum::APP_CO :
                return new result(false, 'Member deposit by partner is not supported by APP_CO', null, errorCodesEnum::NOT_SUPPORTED);
            case bizSceneEnum::COUNTER :
                return new result(false, 'Member deposit by partner is not supported by counter.', null, errorCodesEnum::NOT_SUPPORTED);

            case bizSceneEnum::BACK_OFFICE :
                return new result(false, 'Member deposit by partner is not supported by BACKOFFICE', null, errorCodesEnum::NOT_SUPPORTED);
            default:
                return new result(false, 'Unknown scene', null, errorCodesEnum::NOT_SUPPORTED);
        }
    }



    public function bizStart($member_id,$amount,$currency,$account_handler_id,$remark)
    {
        // 功能开关检查
        if( member_handlerClass::checkHandlerFunctionIsClosedById($account_handler_id) ){
            return new result(false,'Function closed.',null,errorCodesEnum::FUNCTION_CLOSED);
        }

        // 检查场景限制
        $chk = $this->checkLimit($member_id,$amount,$currency);
        if( !$chk->STS ){
            return $chk;
        }

        $memberObj = new objectMemberClass($member_id);
        $chk = $memberObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        // 是否在黑名单
        $black_list = $memberObj->getBlackList();
        if( in_array(blackTypeEnum::DEPOSIT,$black_list) ){
            return new result(false,'Member is in black list for deposit.',null,errorCodesEnum::MEMBER_IS_IN_BLACK_LIST);
        }

        $m_biz = $this->bizModel;
        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->member_id = $member_id;
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->fee = 0;
        $biz->member_handler_id = $account_handler_id;
        $biz->remark = $remark;
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $insert = $biz->insert();
        if( !$insert->STS  ){
            return new result(false,'Fail',null,errorCodesEnum::DB_ERROR);
        }


        return new result(true,'success',array(
            'biz_id' => $biz->uid
        ));

    }

    public function bizSubmit($biz_id)
    {
        $m = $this->bizModel;
        $biz = $m->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info.',null,errorCodesEnum::NO_DATA);
        }
        if( $biz->state == bizStateEnum::DONE ){
            return new result(true,'success',$biz);
        }

        if( $biz->state != bizStateEnum::CREATE ){
            return new result(false,'Invalid state.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $member_id = $biz->member_id;
        $account_handler_id = $biz->member_handler_id;
        $amount = $biz->amount;
        $currency = $biz->currency;
        $handler_info = member_handlerClass::getHandlerInfoById($account_handler_id);

        // 功能开关检查
        if( member_handlerClass::checkHandlerFunctionIsClosedById($account_handler_id) ){
            return new result(false,'Function closed.',null,errorCodesEnum::FUNCTION_CLOSED);
        }


        $type_name = member_handlerClass::getHandlerTypeName($handler_info['handler_type']);
        $short_account = substr($handler_info['handler_account'],-4);

        $remark = "Account: ".$type_name.
            "(".$short_account.")";

        // 更新状态
        $biz->state = bizStateEnum::PENDING_CHECK;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Update biz fail',null,errorCodesEnum::DB_ERROR);
        }

        // 账本执行
        $rt = passbookWorkerClass::memberDepositByPartner($member_id,$account_handler_id,$amount,$currency,$biz_id,$remark);
        if( !$rt->STS ){

            $biz->passbook_trading_id = $rt->DATA['trade_id'];
            $biz->api_trx_id = $rt->DATA['api_trx_id'];
            if( $rt->CODE != errorCodesEnum::UNKNOWN_ERROR ){
                $biz->state = bizStateEnum::FAIL;
            }

            $biz->update_time = Now();
            $biz->update();

            return $rt;
        }

        $trade_id = intval($rt->DATA['trade_id']);
        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $biz->passbook_trading_id = $trade_id;
        $biz->api_trx_id = $rt->DATA['api_trx_id'];
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Update biz fail',null,errorCodesEnum::DB_ERROR);
        }


        $title = "Deposit by $type_name";
        $subject = "Deposit in $amount".$currency." by $type_name(".$short_account.").";
        member_messageClass::sendSystemMessage($member_id,$title,$subject);

        $biz->biz_id = $biz_id;

        return new result(true,'success',$biz);
    }

    public function bizConfirm($biz_id) {
        $m = $this->bizModel;
        $biz = $m->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info.',null,errorCodesEnum::NO_DATA);
        }
        if( $biz->state == bizStateEnum::DONE ){
            return new result(true,'success',$biz);
        }

        if( $biz->state != bizStateEnum::PENDING_CHECK ){
            return new result(false,'Invalid state.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        if ($biz->passbook_trading_id) {
            $rt = memberDepositByPartnerTradingClass::confirm($biz->passbook_trading_id);
            if ($rt->STS || $rt->CODE == errorCodesEnum::TRADING_FINISHED) {
                $biz->state = bizStateEnum::DONE;
                $biz->update_time = Now();
                $up = $biz->update();
                if( !$up->STS ){
                    return new result(false,'Update biz fail',null,errorCodesEnum::DB_ERROR);
                } else {
                    return new result(true);
                }
            } else {
                return $rt;
            }
        } else {
            return new result(false, 'No ref trading id', null, errorCodesEnum::UNEXPECTED_DATA);
        }
    }

    public function bizCancel($biz_id) {
        $m = $this->bizModel;
        $biz = $m->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info.',null,errorCodesEnum::NO_DATA);
        }
        if( $biz->state == bizStateEnum::CANCEL ){
            return new result(true,'success',$biz);
        }

        if( $biz->state != bizStateEnum::PENDING_CHECK ){
            return new result(false,'Invalid state.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        if ($biz->passbook_trading_id) {
            $rt = memberDepositByPartnerTradingClass::reject($biz->passbook_trading_id);
            if ($rt->STS || $rt->CODE == errorCodesEnum::TRADING_CANCELLED) {
                $biz->state = bizStateEnum::CANCEL;
                $biz->update_time = Now();
                $up = $biz->update();
                if( !$up->STS ){
                    return new result(false,'Update biz fail',null,errorCodesEnum::DB_ERROR);
                } else {
                    return new result(true);
                }
            } else {
                return $rt;
            }
        } else {
            return new result(false, 'No ref trading id', null, errorCodesEnum::UNEXPECTED_DATA);
        }
    }
}