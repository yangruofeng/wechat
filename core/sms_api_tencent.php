<?php

class sms_api_tencent{
    public function sendVerifyCode($phone,$code){
        if(!$phone || !$code){
            return new result(false, "Empty Parameter", null, errorCodesEnum::INVALID_PARAM);
        }
        if(!$phone['nationcode'] || !$phone['mobile']) {
            return new result(false, "", null, errorCodesEnum::INVALID_PARAM);
        }
        return $this->sendSms($phone,171084,array($code));
    }

    public function sendSms($phone,$tplId,$params){
        header("Content-Type:text/html;charset=utf-8");
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

        $appkey = "8431a0182fb0423075b3dc1c75234c35";
        $r = rand();
        $time = time();
        $mobile = $phone['mobile'];
        $keystr = "appkey=$appkey&random=$r&time=$time&mobile=$mobile";

        // 发送短信
        $data=array(
            'params'=>$params,
            'sig' => hash('sha256', $keystr),
            "sign" => "Samrithisak",
            'tel' => $phone,
            'time' => $time,
            'tpl_id' => $tplId);

        $json_data = $this->send($ch,$r,$data);
        $array = json_decode($json_data,true);
        curl_close($ch);
        return $array;
    }

    private function send($ch,$r,$data){
        $url = 'https://yun.tim.qq.com/v5/tlssmssvr/sendsms?sdkappid=1400123743&random='.$r;
        curl_setopt ($ch, CURLOPT_URL, $url);
        logger::record("tencent-sms-api", $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        logger::record("tencent-sms-api", json_encode($data));
        $ret = curl_exec($ch);
        logger::record("tencent-sms-api", $ret);
        return $ret;
    }


}