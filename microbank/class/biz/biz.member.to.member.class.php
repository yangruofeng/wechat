<?php

class bizMemberToMemberClass extends bizMemberTransferClass
{


    public function __construct($scene_code)
    {
        parent::__construct($scene_code);
        $this->scene_code = $scene_code;
        $this->biz_code = bizCodeEnum::MEMBER_TRANSFER_TO_MEMBER;

    }



    public function getFee($amount)
    {
        return $amount*0;
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
                return new result(false, 'Member to member is not supported by APP_CO', null, errorCodesEnum::NOT_SUPPORTED);
            case bizSceneEnum::COUNTER :

                // 检查member限制
                $chk = $this->checkMemberLimit($member_id,$amount,$currency);
                if( !$chk->STS ){
                    return $chk;
                }
                return new result(true);
            case bizSceneEnum::BACK_OFFICE :
                return new result(false, 'Member to member is not supported by BACKOFFICE', null, errorCodesEnum::NOT_SUPPORTED);
            default:
                return new result(false, 'Unknown scene', null, errorCodesEnum::NOT_SUPPORTED);
        }
    }


    public function bizStart($from_member_id,$to_member_id,$amount,$currency,$remark)
    {

        if( $from_member_id == $to_member_id ){
            return new result(false,'Can not pay to self.',null,errorCodesEnum::CAN_NOT_TRANSFER_TO_SELF);
        }

        // 场景限制
        $chk = $this->checkLimit($from_member_id,$amount,$currency);
        if( !$chk->STS ){
            return $chk;
        }

        // 检查余额
        $from_memberObj = new objectMemberClass($from_member_id);
        $check_valid = $from_memberObj->checkValid();
        if( !$check_valid->STS ){
            return $check_valid;
        }

        // 是否在黑名单
        $black_list = $from_memberObj->getBlackList();
        if( in_array(blackTypeEnum::TRANSFER,$black_list) ){
            return new result(false,'Member is in black list for transfer.',null,errorCodesEnum::MEMBER_IS_IN_BLACK_LIST);
        }


        $member_balance = $from_memberObj->getSavingsAccountBalance();
        if( $member_balance[$currency] < $amount ){
            return new result(false,'Balance not enough',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
        }

        $to_memberObj = new objectMemberClass($to_member_id);
        $check_valid = $to_memberObj->checkValid();
        if( !$check_valid->STS ){
            return $check_valid;
        }

        // 插入业务表
        $m_biz = $this->bizModel;
        $biz = $m_biz->newRow();
        $biz->member_id = $from_member_id;
        $biz->transfer_type = memberTransferToTypeEnum::MEMBER;
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->to_member_id = $to_member_id;
        $biz->to_member_name = $to_memberObj->member_account;
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->fee = $this->getFee($amount);
        $biz->remark = $remark;
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $insert = $biz->insert();
        if( !$insert->STS ){
            return new result(false,'Handler fail',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success',array(
            'biz_id' => $biz->uid
        ));

    }





    /** 业务提交
     * @param $biz_id
     * @return result
     */
    public function bizSubmit($biz_id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        if( $biz->state == bizStateEnum::DONE ){
            return new result(true,'success',$biz);
        }

        $from_member_id = $biz->member_id;
        $to_member_id = $biz->to_member_id;
        $fromMemberObj = new objectMemberClass($from_member_id);
        $toMemberObj = new objectMemberClass($to_member_id);
        $trading_password = $fromMemberObj->trading_password;
        $amount = $biz->amount;
        $currency = $biz->currency;

        $remark = $biz->remark;

        $re = passbookWorkerClass::memberTransferToMember($from_member_id,$trading_password,$to_member_id,$amount,$currency,$remark);
        if( !$re->STS ){
            $biz->state = bizStateEnum::FAIL;
            $biz->update_time = Now();
            $biz->update();
            return $re;
        }else{
            $trade_id = intval($re->DATA);
            $biz->state = bizStateEnum::DONE;
            $biz->update_time = Now();
            $biz->passbook_trading_id = $trade_id;
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Transfer success,update biz fail!',null,errorCodesEnum::DB_ERROR);
            }
        }

        $title = "Member transfer";
        $from_subject = "Transfer to ".$toMemberObj->member_account.": $amount-$currency.";
        $to_subject = "Receive $amount-$currency : transfer from ".$fromMemberObj->member_account;
        member_messageClass::sendSystemMessage($from_member_id,$title,$from_subject);
        member_messageClass::sendSystemMessage($to_member_id,$title,$to_subject);

        return new result(true,'success',$biz);
    }


}