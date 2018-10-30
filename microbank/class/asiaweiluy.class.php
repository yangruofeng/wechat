<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/30
 * Time: 17:50
 */
class asiaweiluyClass
{
    function __construct()
    {
    }


    /** 电话号码组装ACE账号
     * @param $country_code
     * @param $phone_number
     * @return string
     */
    public static function formatAceAccountByPhone($country_code,$phone_number)
    {
        return '+'.trim($country_code).'-'.trim($phone_number);
    }


    /** 验证是否ACE会员,如 +86-18902461905
     * @param $accountAce
     * @return result
     */
    public static function verifyAceAccount($accountAce)
    {
        $ace_api = asiaweiluyApi::Instance();
        $re = $ace_api->verifyClientAccount($accountAce);
        if( !$re->STS ){
            return new result(false,'Api error',null,$re->CODE);
        }
        $data = $re->DATA;
        $is_member = 0;
        if( $data['flag_ace_member'] == 1 ){
            $is_member = 1;
        }
        return new result(true,'success',$is_member);
    }


    /** 绑定账号发送验证码  如 +86-18902461905
     * @param $accountAce
     * @return result
     */
    public static function bindAccountSendVerifyCode($accountAce)
    {
        $ace_api = asiaweiluyApi::Instance();
        $re = $ace_api->bindStart($accountAce);
        if( !$re->STS ){
            return new result(false,'Api error',null,$re->CODE);
        }
        $data = $re->DATA;
        return new result(true,'success',array(
            'verify_id' => $data['application_id'],
            'phone_id' => $accountAce
        ));
    }


    /** 绑定账号验证验证码
     * @param $verify_id
     * @param $code
     * @return result
     */
    public static function bindAccountCheckVerifyCode($verify_id,$code)
    {
        $ace_api = asiaweiluyApi::Instance();
        $re = $ace_api->bindFinish($verify_id,$code);
        if( !$re->STS ){
            return new result(false,'Api error',null,$re->CODE);
        }
        $ok = 0;
        $data = $re->DATA;
        if( $data['flag_success'] == 1 ){
            $ok = 1;
        }
        return new result(true,'success',$ok);
    }


    /** 解除绑定开始
     * @param $accountAce
     * @return result
     */
    public static function aceUnbindStart($accountAce)
    {
        $ace_api = asiaweiluyApi::Instance();
        $rt = $ace_api->unbindStart($accountAce);
        if( !$rt->STS ){
            return $rt;
        }
        $data = $rt->DATA;
        return new result(true,'success',array(
            'verify_id' => $data['application_id'],
            'phone_id' => $accountAce
        ));
    }

    /** 解除绑定结束
     * @param $verify_id
     * @param $code
     * @return result
     */
    public static function aceUnbindFinish($verify_id,$code)
    {
        $ace_api = asiaweiluyApi::Instance();
        $rt = $ace_api->unbindFinish($verify_id,$code);
        if( !$rt->STS ){
            return $rt;
        }
        $data = $rt->DATA;
        $is_success = 0;
        if( $data['flag_success'] == 1 ){
            $is_success = 1;
        }
        return new result(true,'success',array(
            'is_success' => $is_success
        ));
    }

    /** 查询客户余额
     * @param $accountAce
     * @return result
     */
    public static function queryClientCurrencyBalance($accountAce)
    {
        $ace_api = asiaweiluyApi::Instance();
        $rt = $ace_api->queryClientBalance($accountAce);
        if( !$rt->STS ){
            return $rt;
        }
        $list = $rt->DATA;
        $currency_amount = array();
        foreach( $list as $v ){
            $currency_amount[$v['currency']] = $v['amount'];
        }
        return new result(true,'success',$currency_amount);
    }


}