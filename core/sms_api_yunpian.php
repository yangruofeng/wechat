<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 6/16/2016
 * Time: 11:33 PM
 */
class sms_api_yunpian{
    /*
     * 发送验证码，成功返回格式
   array (size=3)
  'code' => int 0
  'msg' => string 'OK' (length=2)
  'result' =>
    array (size=3)
      'count' => int 1
      'fee' => float 0.101
      'sid' => float 7408222532

     * */
    public function sendVerifyCode($phone,$code){
        if(!$phone || !$code){
            return new result(false,"Empty Parameter");
        }
        $text="【KHBUY】You verification code is ".$code;
        return $this->sendSmsToYP($phone,$text);
    }
    /*
     * 发送pincode
     * */
    public function sendPinCode($phone,$pin,$amount,$telecom){
        if(!$phone || !$pin || !$amount || !$telecom){
            return new result(false,"Empty Parameter");
        }
        $text="【KHBUY】PIN:".$pin."\nAMOUNT:".$amount."\nTELECOM:".$telecom;
        return $this->sendSmsToYP($phone,$text);
    }
    public  function sendWalletChange($phone,$text){
        if(!$phone || !$text){
            return new result(false,"Empty Parameter");
        }
        $text="【KHBUY】wallet changed ".$text;
        return $this->sendSmsToYP($phone,$text);
    }
    public function  sendSmsToYP($phone,$text){
        header("Content-Type:text/html;charset=utf-8");
        $apikey = "511ac0c39d2ae310fe91fb71b5dd10a7"; //修改为您的apikey(https://www.yunpian.com)登陆官网后获取
        $ch = curl_init();
        /* 设置验证方式 */
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));

        /* 设置返回结果为流 */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        /* 设置超时时间*/
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        /* 设置通信方式 */
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 取得用户信息
        /*
        $json_data = $this->get_user($ch,$apikey);
        $array = json_decode($json_data,true);
        echo '<pre>';print_r($array);
        */

// 发送短信
        $data=array('text'=>$text,'apikey'=>$apikey,'mobile'=>$phone);
        $json_data = $this->send($ch,$data);
        $array = json_decode($json_data,true);
        curl_close($ch);
        return $array;
    }


    /***************************************************************************************/
//获得账户
    private function get_user($ch,$apikey){
        curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v1/user/get.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('apikey' => $apikey)));
        return curl_exec($ch);
    }
    private function send($ch,$data){
        curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v1/sms/send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        return curl_exec($ch);
    }
    private function tpl_send($ch,$data){
        curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v1/sms/tpl_send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        return curl_exec($ch);
    }
    private function voice_send($ch,$data){
        curl_setopt ($ch, CURLOPT_URL, 'http://voice.yunpian.com/v1/voice/send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        return curl_exec($ch);
    }

}