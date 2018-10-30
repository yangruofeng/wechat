<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/9
 * Time: 19:55
 */
class bizMemberScanPayMemberClass extends bizBaseClass
{
    public function __construct($scene_code)
    {
        $this->scene_code = $scene_code;
        $this->biz_code = bizCodeEnum::MEMBER_SCAN_PAY_TO_MEMBER;
        $chk = $this->checkBizOpen();
        if( !$chk->STS ){
            throw new Exception('Function closed.',errorCodesEnum::FUNCTION_CLOSED);
        }
        $this->bizModel = new biz_member_scan_pay_to_memberModel();
    }

    public function checkBizOpen()
    {

        if( global_settingClass::isForbiddenPay() || global_settingClass::isForbiddenCollect() ){
            return new result(false,'Function closed.',null,errorCodesEnum::FUNCTION_CLOSED);
        }

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

    /** 检查客户限制
     * @param $member_id
     * @param $amount
     * @param $currency
     * @return result
     */
    protected function checkMemberLimit($member_id,$amount,$currency)
    {
        $memberObj = new objectMemberClass($member_id);
        if( $memberObj->grade_info ){
            $grade_id = $memberObj->grade_info['uid'];
            $limit = global_settingClass::getMemberBizLimitByGrade($this->biz_code,$grade_id);
        }else{
            $limit = global_settingClass::getMemberBizLimitByGrade($this->biz_code);
        }

        $ex_rate = global_settingClass::getCurrencyRateBetween($currency,currencyEnum::USD);

        if( $limit ){

            $amount = round($amount*$ex_rate,2);
            // 检查单次
            if( $limit['per_time'] > 0 && $amount > $limit['per_time'] ){
                return new result(false,'Exceed member per time limit amount:'.$limit['per_time'],null,errorCodesEnum::EXCEEDED_PER_TIMES_LIMIT);
            }


            $reader = new ormReader();
            // 当日已经进行的金额
            $done_amount = 0;
            $sql = "select * from biz_member_scan_pay_to_member where biz_code='".$this->biz_code."' and member_id='$member_id' 
            and DATE_FORMAT(update_time,'%Y-%m-%d')='".date('Y-m-d')."' and state='".bizStateEnum::DONE."' ";
            $rows = $reader->getRows($sql);
            // 换算成设定的美元
            foreach( $rows as $v ){
                $e_rate = global_settingClass::getCurrencyRateBetween($v['currency'],currencyEnum::USD);
                $done_amount += round($e_rate*$v['amount']);
            }

            if( $limit['per_day'] > 0 && ($done_amount+$amount) > $limit['per_day'] ){
                return new result(false,'Exceed member per day limit amount: '.$limit['per_day'],null,errorCodesEnum::EXCEEDED_PER_DAY_LIMIT);
            }

        }
        return new result(true);

    }


    public function checkLimit($member_id,$amount,$currency)
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
                return new result(false, 'Member pay to member is not supported by APP_CO.', null, errorCodesEnum::NOT_SUPPORTED);
            case bizSceneEnum::COUNTER :

                return new result(false, 'Member pay to member is not supported by counter.', null, errorCodesEnum::NOT_SUPPORTED);

            case bizSceneEnum::BACK_OFFICE :
                return new result(false, 'Member pay to member is not supported by BACKOFFICE.', null, errorCodesEnum::NOT_SUPPORTED);
            default:
                return new result(false, 'Unknown scene', null, errorCodesEnum::NOT_SUPPORTED);
        }
    }

    public function getFee($amount)
    {
        return 0;
    }

    /** 密文检查客户的交易密码
     * @param $biz_id
     * @param $member_id
     * @param $time
     * @param $sign
     * @param $remark
     * @return result
     */
    public function checkMemberTradingPasswordSign($biz_id,$member_id,$time,$sign,$remark='')
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $memberObj = new objectMemberClass($member_id);
        $m_log = new member_verify_trading_password_logModel();
        $times = $m_log->getDayErrorTimes($member_id);
        if( $times >= 5 ){
            return new result(false,'Password wrong too many times.',null,errorCodesEnum::PASSWORD_ERROR_MORE_TIMES);
        }

        // 检查交易密码签名
        $self_sign = md5($biz_id.$member_id.$time.$memberObj->trading_password);
        if( $sign != $self_sign ){

            $m_log->addLog($member_id,'******',1,$remark);
            return new result(false,'Password error.',null,errorCodesEnum::PASSWORD_ERROR);
        }

        $biz->member_trading_password = $memberObj->trading_password;
        $biz->update_time = Now();
        $up = $biz->update();


        return new result(true);

    }


    public function checkMemberTradingPassword($biz_id,$password)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $member_id = $biz->member_id;

        $objectMember = new objectMemberClass($member_id);
        $chk = $objectMember->checkTradingPassword($password,'Member Scan Pay To Member');
        if( !$chk->STS ){
            return $chk;
        }

        $biz->member_trading_password = $objectMember->trading_password;
        $biz->update_time = Now();
        $up = $biz->update();


        return new result(true);

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
        $biz->member_name = $from_memberObj->member_account;
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

        if( $biz->biz_code != $this->biz_code ){
            return new result(false,'Invalid biz.',null,errorCodesEnum::INVALID_PARAM);
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


        $re = passbookWorkerClass::memberPaymentToMember($from_member_id,$trading_password,$to_member_id,$amount,$currency,$remark);
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
                return new result(false,'Payment success,update biz fail!',null,errorCodesEnum::DB_ERROR);
            }

        }

        $title = "Payment";
        $from_subject = "Payment: pay $amount-$currency to ".$toMemberObj->member_account;
        $to_subject = "Receive: receive $amount-$currency  from ".$fromMemberObj->member_account;
        member_messageClass::sendSystemMessage($from_member_id,$title,$from_subject,array(
            'notice_type' => jpushNoticeTypeEnum::SCAN_PAY_PAYMENT_OK
        ));
        member_messageClass::sendSystemMessage($to_member_id,$title,$to_subject,array(
            'notice_type' => jpushNoticeTypeEnum::SCAN_PAY_RECEIVE_OK
        ));
        return new result(true,'success',$biz);

    }


}