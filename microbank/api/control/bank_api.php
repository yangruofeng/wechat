<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 10:18
 */

abstract class bank_apiControl{

    protected $appId;
    protected $appKey;

    public function __construct()
    {
        Language::read('define,certification,common_lang');
    }


    /**
     * app使用api检查token
     * @return result
     */
    protected function checkToken()
    {
        // 首先检查是否被关闭了
        $app_state = global_settingClass::getMemberAppClosedState();
        if( $app_state['is_closed'] ){
            return new result(false,'App closed!',$app_state['closed_reason'],errorCodesEnum::APP_CLOSED);
        }

        $params = array_merge(array(),$_GET,$_POST);
        $token = $params['token'];
        $member_id = $params['member_id'];
        $m_member_token = new member_tokenModel();
        return $m_member_token->checkToken($token,$member_id);
    }


    /** CO APP 检查token
     * @return result
     */
    protected function checkOperator()
    {
        // 首先检查是否被关闭了
        $app_state = global_settingClass::getCreditOfficerAppClosedState();
        if( $app_state['is_closed'] ){
            return new result(false,'App closed!',$app_state['closed_reason'],errorCodesEnum::APP_CLOSED);
        }

        $params = array_merge(array(),$_GET,$_POST);
        $token = $params['token'];
        $m_token = new um_user_tokenModel();
        return $m_token->checkToken($token);
    }

    protected function checkAppSign()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $key = getConf('app_secret_key');

        if ( $this->sign($params,$key) != $params['sign'] ) {
            return new result(false, "Sign error", null, errorCodesEnum::SIGN_ERROR);
        }

        return new result(true);
    }



    /**
     * 强验证api检查签名
     * @return result
     */
    protected function checkSign()
    {

        $api_config = getConf('api_config');
        if( !$api_config ){
            return new result(false,'Api config not exist!',null,errorCodesEnum::CONFIG_ERROR);
        }
        $this->appId = $api_config['appId'];
        $this->appKey = $api_config['appKey'];

        $params = array_merge(array(),$_GET,$_POST);

        if ( $this->sign($params,$this->appKey) != $params['sign'] ) {
            return new result(false, "Sign error", null, errorCodesEnum::SIGN_ERROR);
        }

        return new result(true);


    }

    protected function sign($parameters,$key)
    {

        $parameters = array_ksort($parameters);
        $segments = array();
        foreach ($parameters as $k=>$v) {
            if ($k == "sign_type") continue;
            if ($k == "sign") continue;
            if ($k == "act") continue;
            if ($k == "op") continue;
            if ($k == "yoajax") continue;
            if ($k == "_s") continue;
            if ($v === null || $v === "") continue;
            $segments[]="$k=$v";
        }

        return md5(join("&", $segments).$key);
    }
}




