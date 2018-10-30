<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/27
 * Time: 16:16
 */
class bizMemberWithdrawClass extends bizBaseClass
{
    public function __construct($scene_code)
    {

        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!',errorCodesEnum::FUNCTION_CLOSED);
        }
        $this->bizModel = new biz_member_withdrawModel();
    }

    public function checkBizOpen()
    {

        if( global_settingClass::isForbiddenWithdraw() ){
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

    /** 检查分行限制
     * @param $branch_id
     * @param $amount
     * @param $currency
     * @return result
     */
    protected function checkBranchDayLimit($branch_id,$amount,$currency)
    {
        $reader = new ormReader();

        // 当日已经进行的金额
        $done_amount = 0;
        $sql = "select * from biz_member_withdraw where biz_code='".$this->biz_code."' and branch_id='$branch_id' 
        and DATE_FORMAT(update_time,'%Y-%m-%d')='".date('Y-m-d')."' and state='".bizStateEnum::DONE."' ";
        $rows = $reader->getRows($sql);
        // 换算成设定的美元
        foreach( $rows as $v ){
            $e_rate = global_settingClass::getCurrencyRateBetween($v['currency'],currencyEnum::USD);
            $done_amount += round($e_rate*$v['amount'],2);
        }

        $ex_rate = global_settingClass::getCurrencyRateBetween($currency,currencyEnum::USD);
        $request_amount = round($amount*$ex_rate,2);

        // 获取分行限制
        $limit = global_settingClass::getBranchBizLimitSetting($branch_id,$this->biz_code);
        if( $limit && $limit['max_per_day'] && ($done_amount+$request_amount) > $limit['max_per_day'] ){
            return new result(false,'Exceed branch day limit:'.$limit['max_per_day'],null,errorCodesEnum::EXCEEDED_PER_DAY_LIMIT);
        }

        return new result(true);
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
            $sql = "select * from biz_member_withdraw where biz_code='".$this->biz_code."' and member_id='$member_id' 
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
        $chk = $objectMember->checkTradingPassword($password,'Member Withdraw');
        if( !$chk->STS ){
            return $chk;
        }
        $biz->member_trading_password = $objectMember->trading_password;
        $biz->update_time = Now();
        $up = $biz->update();


        return new result(true);

    }

    public function checkTellerPassword($biz_id,$card_no,$key)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $teller_id = $biz->cashier_id;
        $teller = new objectUserClass($teller_id);

        $branch_id = $teller->branch_id;
        $chk = $this->checkTellerAuth($teller_id,$branch_id,$card_no,$key);
        if( !$chk->STS ){
            return $chk;
        }
        $biz->cashier_trading_password = $teller->trading_password;
        $biz->cashier_name = $teller->user_name;
        $biz->update_time = Now();
        $up = $biz->update();

        return new result(true,'success',array(
            'biz_id' => $biz_id
        ));
    }


    public function checkChiefTellerPassword($biz_id,$card_no,$key)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $cashier_id = $biz->cashier_id;
        $cashierObj = new objectUserClass($cashier_id);

        $branch_id = $cashierObj->branch_id;
        $rt = $this->checkChiefTellerAuth($branch_id,$card_no,$key);
        if( !$rt->STS ){
            return $rt;
        }
        $ct_id = $rt->DATA;
        $ctObj = new objectUserClass($ct_id);
        $biz->bm_id = $ct_id;
        $biz->bm_name = $ctObj->user_name;
        $biz->bm_trading_password = $ctObj->trading_password;
        $biz->update_time = Now();
        $up = $biz->update();

        return new result(true,'success',array(
            'biz_id' => $biz_id
        ));
    }


    /** 录入客户信息
     * @param $biz_id
     * @param $member_image
     * @return result
     */
    public function insertMemberInfo($biz_id,$member_image)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info:'.$biz_id,null,errorCodesEnum::NO_DATA);
        }
        $biz->member_image = $member_image;
        $biz->update_time = Now();
        $up = $biz->update();

        $biz->biz_id = $biz->uid;

        $m_image = new biz_scene_imageModel();
        $insert = $m_image->insertSceneImage($biz->member_id,$member_image,$this->biz_code,$this->scene_code);
        if( !$insert->STS  ){
            return $insert;
        }

        return new result(true,'success',array(
            'biz_id' => $biz->uid
        ));
    }

    public function isNeedCTApprove($biz_id)
    {
        $biz_info = $this->getBizDetailById($biz_id);
        if( !$biz_info ){
            return true;
        }
        $common_setting = $this->counterBizIsNeedCTApprove(array(
            $biz_info['currency'] => $biz_info['amount']
        ));
        return $common_setting;

    }


}