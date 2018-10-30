<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/13
 * Time: 17:34
 */

// 提现
class bizMemberWithdrawToPartnerClass extends bizMemberWithdrawClass
{

    public function __construct($scene_code)
    {
        parent::__construct($scene_code);
        $this->biz_code = bizCodeEnum::MEMBER_WITHDRAW_TO_PARTNER;
        $this->scene_code = $scene_code;
    }


    // 检查交易限制
    public function checkLimit($member_id,$amount,$currency)
    {

        switch($this->scene_code )
        {
            case bizSceneEnum::APP_MEMBER:

                // 检查member限制
                $chk = $this->checkMemberLimit($member_id,$amount,$currency);
                if( !$chk->STS ){
                    return $chk;
                }

                return new result(true);
                break;
            case bizSceneEnum::APP_CO :
                return new result(false,'CO app not support withdraw.',null,errorCodesEnum::NOT_SUPPORTED);
                break;
            case bizSceneEnum::COUNTER :

                return new result(false,'Counter not support withdraw to partner.',null,errorCodesEnum::NOT_SUPPORTED);

                break;
            case bizSceneEnum::BACK_OFFICE :
                return new result(false,'Back office not support withdraw.',null,errorCodesEnum::NOT_SUPPORTED);
                break;
            default:
                return new result(false,'Unknown scene',null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }



    public function getFee($amount)
    {
        return $amount*0;
    }

    // 业务开始
    public  function bizStart($from_member_id,$amount,$currency,$member_handler_id,$remark)
    {

        // 功能开关检查
        if( member_handlerClass::checkHandlerFunctionIsClosedById($member_handler_id) ){
            return new result(false,'Function closed.',null,errorCodesEnum::FUNCTION_CLOSED);
        }

        // 检查场景限制
        $chk = $this->checkLimit($from_member_id,$amount,$currency);
        if( !$chk->STS ){
            return $chk;
        }

        // 检查余额
        $objectMember = new objectMemberClass($from_member_id);
        $chk = $objectMember->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        $cny_balance = $objectMember->getSavingsAccountBalance();
        if( $cny_balance[$currency] < $amount ){
            return new result(false,'Balance not enough',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
        }

        // 是否在黑名单
        $black_list = $objectMember->getBlackList();
        if( in_array(blackTypeEnum::WITHDRAW,$black_list) ){
            return new result(false,'Member is in black list for withdraw.',null,errorCodesEnum::MEMBER_IS_IN_BLACK_LIST);
        }

        $handler_info = member_handlerClass::getHandlerInfoById($member_handler_id);
        if( !$handler_info ){
            return new result(false,'No handler:'.$member_handler_id,null,errorCodesEnum::NO_ACCOUNT_HANDLER);
        }


        // 检查partner的业务限制
        $partner_code = member_handlerClass::getPartnerCodeByHandlerInfo($handler_info);
        // 获取限定设置
        $limit = partnerClass::getPartnerBizLimit($partner_code,partnerBizTypeEnum::TRANSFER);
        if( $limit ){

            $usd_rate = global_settingClass::getCurrencyRateBetween($currency,currencyEnum::USD);
            $usd_amount = round($amount*$usd_rate,2);

            $r = new ormReader();


            // 检查单次限额
            if( $limit['per_time'] > 0 ){
                if( $usd_amount > $limit['per_time'] ){
                    return new result(false,'Exceed partner per time limit:'.$limit['per_time'],null,errorCodesEnum::EXCEEDED_PER_TIMES_LIMIT);
                }
            }

            // 检查单日限额
            if( $limit['per_day'] > 0 ){
                // 当日成交额
                $sql = "select amount,currency from biz_member_withdraw where biz_code='".$this->biz_code."' and member_handler_id='$member_handler_id' 
                and state='".bizStateEnum::DONE."' and DATE_FORMAT(update_time,'%Y-%m-%d')='".date('Y-m-d')."' ";
                $list = $r->getRows($sql);
                $day_amount = 0;
                foreach( $list as $v ){
                    $rate = global_settingClass::getCurrencyRateBetween($v['currency'],currencyEnum::USD);
                    $day_amount += round($v['amount']*$rate,2);
                }
                // 还要加上本次的
                $day_amount += $usd_amount;
                if( $day_amount > $limit['per_day'] ){
                    return new result(false,'Exceed partner per day limit:'.$limit['per_day'],null,errorCodesEnum::EXCEEDED_PER_DAY_LIMIT);
                }

            }
        }


        $m_biz = $this->bizModel;
        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->member_id = $from_member_id;
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->fee = $this->getFee($amount);
        $biz->member_handler_id = $member_handler_id;
        $biz->remark = $remark;
        $biz->scene_code = $this->scene_code;
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $insert = $biz->insert();
        if( !$insert->STS  ){
            return new result(false,'Fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'biz_id' => $biz->uid
        ));

    }




    // 业务提交
    public  function bizSubmit($biz_id)
    {

        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        if( $biz->state == bizStateEnum::DONE ){
            return new result(true,'success',$biz);
        }

        if( $biz->state != bizStateEnum::CREATE ){
            return new result(false,'Invalid state.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $member_id = $biz->member_id;
        $memberObj = new objectMemberClass($member_id);
        $amount = $biz->amount;
        $currency = $biz->currency;
        $trading_password = $memberObj->trading_password;
        $account_handler_id = $biz->member_handler_id;
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

        $re = passbookWorkerClass::memberWithdrawToPartner($member_id,$trading_password,$account_handler_id,$amount,$currency,$biz_id,$remark);
        $trade_id = intval($re->DATA['trade_id']);
        if( !$re->STS ){

            $biz->passbook_trading_id  = $trade_id;
            $biz->api_trx_id = $re->DATA['api_trx_id'];
            if( $re->CODE != errorCodesEnum::UNKNOWN_ERROR ){
                $biz->state = bizStateEnum::FAIL;
            }
            $biz->update_time = Now();
            $biz->update();
            return $re;
        }else{

            $biz->state = bizStateEnum::DONE;
            $biz->update_time = Now();
            $biz->passbook_trading_id  = $trade_id;
            $biz->api_trx_id = $re->DATA['api_trx_id'];
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Update biz fail',null,errorCodesEnum::DB_ERROR);
            }
        }


        $title = "Withdraw to $type_name";
        $subject = "Withdraw to $type_name(".$short_account."): $amount".$currency;
        member_messageClass::sendSystemMessage($member_id,$title,$subject);

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
            $rt = memberWithdrawByPartnerTradingClass::confirm($biz->passbook_trading_id);
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
            $rt = memberWithdrawByPartnerTradingClass::reject($biz->passbook_trading_id);
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