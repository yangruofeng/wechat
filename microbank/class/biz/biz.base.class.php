<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/14
 * Time: 15:58
 */
abstract class bizBaseClass
{
    public $biz_code;  // 统一业务code格式
    public $scene_code;  // 统一场景code格式


    /**
     * @var tableModelBase
     */
    protected $bizModel;

    abstract function checkBizOpen();  // 统一格式检查业务功能是否开启

    abstract function getBizDetailById($id);

    /** 切换业务场景
     * @param $scene_code
     */
    public function changeBizScene($scene_code)
    {
        $this->scene_code = $scene_code;
    }


    /** 密文检查客户的交易密码（应该弃用，放在具体的类实现这个）
     * @param $biz_id
     * @param $member_id
     * @param $time
     * @param $sign
     * @param $remark
     * @return result
     */
    public function checkMemberTradingPasswordSign($biz_id,$member_id,$time,$sign,$remark='')
    {
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

        return new result(true);

    }


    /** 作为底层验证方法
     * @param $member_id
     * @param $sign
     * @param $self_sign
     * @param string $remark
     * @return result
     */
    public function verifyMemberTradingPasswordBySign($member_id,$sign,$self_sign,$remark='')
    {

        $m_log = new member_verify_trading_password_logModel();
        $times = $m_log->getDayErrorTimes($member_id);
        if( $times >= 5 ){
            return new result(false,'Password wrong too many times.',null,errorCodesEnum::PASSWORD_ERROR_MORE_TIMES);
        }
        if( $sign != $self_sign ){
            $m_log->addLog($member_id,'******',1,$remark);
            return new result(false,'Password error.',null,errorCodesEnum::PASSWORD_ERROR);
        }

        return new result(true);

    }


    /** 验证teller卡
     * @param $user_id
     * @param $branch_id
     * @param $card_no
     * @param $auth_key
     * @return result
     */
    public function checkTellerAuth($user_id,$branch_id,$card_no,$auth_key)
    {
        // 先检查卡是否合法
        $rt = icCardClass::confirm($card_no,$auth_key,null);
        if( !$rt->STS ){
            return new result(false,'Invalid card. ' . $rt->MSG,null,errorCodesEnum::INVALID_AUTH_CARD, $rt);
        }

        // 检查卡是否授权给用户
        $card = (new um_user_cardModel())->getRow(array(
            'card_no' => $card_no,
            'state' => 1,
        ));
        if( !$card ){
            return new result(false,'This card is not authorized.',null,errorCodesEnum::INVALID_AUTH_CARD);
        }

        // 检查卡与用户是否匹配
        if( $card->user_id != $user_id ){
            return new result(false,'Invalid user of this card!'.$card_no.'->'.$user_id,null,errorCodesEnum::INVALID_AUTH_CARD);
        }

        $userObj = new objectUserClass($card->user_id);

        // 是否当前分行的
        if( $branch_id != $userObj->branch_id ){
            return new result(false,'Invalid branch.',null,errorCodesEnum::INVALID_AUTH_CARD);
        }

        // 检查职位信息
        if( $userObj->position != userPositionEnum::TELLER ){
            return new result(false,'Position not match: not teller.',null,errorCodesEnum::NOT_CHIEF_TELLER);
        }

        return new result(true,'success');
    }


    /** 验证chief teller卡
     * @param $branch_id
     * @param $card_no
     * @param $auth_key
     * @return result
     */
    public function checkChiefTellerAuth($branch_id,$card_no,$auth_key)
    {
        // 先检查卡是否合法
        $rt = icCardClass::confirm($card_no,$auth_key,null);
        if( !$rt->STS ){
            return new result(false,'Invalid card. ' . $rt->MSG,null,errorCodesEnum::INVALID_AUTH_CARD, $rt);
        }

        $card = (new um_user_cardModel())->getRow(array(
            'card_no' => $card_no,
            'state' => 1,
        ));
        if( !$card ){
            return new result(false,'This card is not authorized.',null,errorCodesEnum::INVALID_AUTH_CARD);
        }

        // 不检查具体user

        $chiefTellerObj = new objectUserClass($card->user_id);

        // 是否当前分行的
        if( $branch_id != $chiefTellerObj->branch_id ){
            return new result(false,'Invalid branch.'.$card_no.'-> branch id:'.$branch_id,null,errorCodesEnum::INVALID_AUTH_CARD);
        }

        // 检查职位信息
        if( $chiefTellerObj->position != userPositionEnum::CHIEF_TELLER ){
            return new result(false,'Position not match: not chief teller.'.$card_no,null,errorCodesEnum::NOT_CHIEF_TELLER);
        }

        return new result(true,'success',$card->user_id);
    }


    public function checkCounterBizIsNeedCTApproveByCode($biz_code,$multi_currency,$branch_id=0)
    {

        $info = global_settingClass::getCounterBizCTApproveDetail($biz_code,$branch_id);
        if( !$info['is_require']){
            return false;
        }

        // 检查限额
        if( $info['min_amount'] > 0){
            // 换算金额
            $total_amount = 0;
            foreach( $multi_currency as $c=>$a ){
                $rate = global_settingClass::getCurrencyRateBetween($c,currencyEnum::USD);
                $ex_amount = round($a*$rate,2);
                $total_amount += $ex_amount;
            }
            if( $total_amount >= $info['min_amount'] ){
                return true;
            }

            return false;
        }else{
            return true;
        }
    }


    public function counterBizIsNeedCTApprove($multi_currency,$branch_id=0)
    {
        $biz_code = $this->biz_code;
        return $this->checkCounterBizIsNeedCTApproveByCode($biz_code,$multi_currency,$branch_id);
    }

}