<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/23
 * Time: 13:18
 */
class bizMemberWithdrawToBankClass extends bizMemberWithdrawClass
{
    public function __construct($scene_code)
    {

        throw new Exception('Not support yet.',errorCodesEnum::NOT_SUPPORTED);
        parent::__construct($scene_code);
        $this->biz_code = bizCodeEnum::MEMBER_WITHDRAW_TO_BANK;
        $this->scene_code = $scene_code;

    }


    public function getFee($amount)
    {
        return 0;
    }

    // 检查交易场景限制
    public  function checkLimit($member_id,$amount,$currency)
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
                return new result(false,'Withdraw to bank is not supported by CO_APP.',null,errorCodesEnum::NOT_SUPPORTED);
                break;
            case bizSceneEnum::COUNTER :
                return new result(false,'Withdraw to bank is not supported by counter.',null,errorCodesEnum::NOT_SUPPORTED);

                break;
            case bizSceneEnum::BACK_OFFICE :
                return new result(false,'Withdraw to bank is not supported by back-office.',null,errorCodesEnum::NOT_SUPPORTED);
                break;
            default:
                return new result(false,'Unknown biz scene.',null,errorCodesEnum::UNEXPECTED_DATA);
                break;

        }

    }

    // 业务开始
    public  function bizStart($from_member_id,$amount,$currency,$member_handler_id,$remark)
    {

        // 检查场景限制
        $scene_limit = $this->getLimit();
        if( $scene_limit && $amount > $scene_limit ){
            return new result(false,'Invalid amount',null,errorCodesEnum::INVALID_AMOUNT);
        }

        // 检查业务限制
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

        // 是否在黑名单
        $black_list = $objectMember->getBlackList();
        if( in_array(blackTypeEnum::WITHDRAW,$black_list) ){
            return new result(false,'Member is in black list for withdraw.',null,errorCodesEnum::MEMBER_IS_IN_BLACK_LIST);
        }

        $cny_balance = $objectMember->getSavingsAccountBalance();
        if( $cny_balance[$currency] < $amount ){
            return new result(false,'Balance not enough',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
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
            return new result(false,'Fail',null,errorCodesEnum::DB_ERROR);
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
        $member_id = $biz->member_id;
        $memberObj = new objectMemberClass($member_id);
        $amount = $biz->amount;
        $currency = $biz->currency;
        $bank_id = $biz->bank_id;
        $member_trading_password =$memberObj->trading_password;
        $bank_info = member_handlerClass::getHandlerInfoById($bank_id);

        $re = passbookWorkerClass::memberWithdrawToBank($member_id,$member_trading_password,$amount,$currency,$bank_id);
        if( !$re->STS ){
            $biz->update_time = Now();
            $biz->state = bizStateEnum::FAIL;
            $biz->update();
            return $re;
        }else{
            $trade_id = intval($re->DATA);
            $biz->state = bizStateEnum::DONE;
            $biz->update_time = Now();
            $biz->passbook_trading_id = $trade_id;
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Update biz fail',null,errorCodesEnum::DB_ERROR);
            }
        }

        $title = "Withdraw to bank";
        $subject = "Withdraw to bank(".substr($bank_info['handler_account'],-4)."): $amount".$currency;
        member_messageClass::sendSystemMessage($member_id,$title,$subject);

        return new result(true,'success',$biz);


    }


}