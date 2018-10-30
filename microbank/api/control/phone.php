<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 14:07
 */
class phoneControl extends bank_apiControl
{

    /**
     * 获取发送短信验证码的冷却时间
     * @return result  时间是s
     */
    public function verifyCoolTimeOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        $country_code = $params['country_code'];
        $phone_number = $params['phone'];
        $cool_time = smsClass::getSendCoolTimeOfPhone($country_code,$phone_number);
        return new result(true,'success',$cool_time);

    }

    /**
     * 发送短信验证码,不处理验证问题，正常号码都可以发送，是否验证过在具体的业务处理
     * @return result
     */
    public function sendCodeOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        $country_code = $params['country_code'];
        $phone_number = $params['phone'];
        return smsClass::sendVerifyCode($country_code,$phone_number);

    }


    /**
     * 验证短信验证码
     * @return result
     */
    public function verifyCodeOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        return smsClass::apiCheckVerifyCode($params);

    }


    /** 检查电话是否已经注册会员了
     * @return result
     */
    public function phoneIsRegisteredOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $country_code = $params['country_code'];
        $phone_number = $params['phone'];
        $is_registered = memberClass::checkPhoneNumberIsRegistered($country_code,$phone_number);
        return new result(true,'success',array(
            'is_registered' => $is_registered
        ));
    }

}