<?php

class jpushApiInner {
    private $_api_entry_url;
    private $_app_key;
    private $_master_secret;
    private $_retry_times;

    public function __construct($conf){
        $this->_api_entry_url = $conf['entry_url'];
        $this->_app_key = $conf['app_key'];
        $this->_master_secret = $conf['master_secret'];
        $this->_retry_times = 1;
    }

    private function getAuthStr() { return $this->_app_key . ":" . $this->_master_secret; }

    public function callApi($method = "POST", $body = null, $times=1) {
        logger::record("jpush-api",$this->_api_entry_url);
        logger::record("jpush-api", json_encode($body));

        if (!defined('CURL_HTTP_VERSION_2_0')) {
            define('CURL_HTTP_VERSION_2_0', 3);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_api_entry_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "JPush-API-PHP-Client");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);  // 连接建立最长耗时
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);  // 请求最长耗时
        // 设置SSL版本 1=CURL_SSLVERSION_TLSv1, 不指定使用默认值,curl会自动获取需要使用的CURL版本
        // curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 如果报证书相关失败,可以考虑取消注释掉该行,强制指定证书版本
        //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
        // 设置Basic认证
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->getAuthStr());
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);

        // 设置Post参数
        if ($method === "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
        } else if ($method === "DELETE" || $method === "PUT") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        if (!is_null($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Connection: Keep-Alive'
        ));

        $output = curl_exec($ch);
        logger::record("jpush-api",$output);
        $response = array();
        $errorCode = curl_errno($ch);

        $msg = '';
        if (isset($body['options']['sendno'])) {
            $sendno = $body['options']['sendno'];
            $msg = 'sendno: ' . $sendno;
        }

        if ($errorCode) {
            $retries = $this->_retry_times;
            if ($times < $retries) {
                return $this->callApi($method, $body, ++$times);
            } else {
                if ($errorCode === 28) {
                    return new result(false, $msg . "Response timeout. Your request has probably be received by JPush Server,please check that whether need to be pushed again.", null, errorCodesEnum::UNKNOWN_ERROR);
                } elseif ($errorCode === 56) {
                    // resolve error[56 Problem (2) in the Chunked-Encoded data]
                    return new result(false, $msg . "Response timeout, maybe cause by old CURL version. Your request has probably be received by JPush Server, please check that whether need to be pushed again.", null, errorCodesEnum::UNKNOWN_ERROR);
                } else {
                    return new result(false, "$msg . Connect timeout. Please retry later. Error:" . $errorCode . " " . curl_error($ch), null, errorCodesEnum::UNKNOWN_ERROR);
                }
            }
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header_text = substr($output, 0, $header_size);
            $body = substr($output, $header_size);
            $headers = array();
            foreach (explode("\r\n", $header_text) as $i => $line) {
                if (!empty($line)) {
                    if ($i === 0) {
                        $headers[0] = $line;
                    } else if (strpos($line, ": ")) {
                        list ($key, $value) = explode(': ', $line);
                        $headers[$key] = $value;
                    }
                }
            }
            $response['headers'] = $headers;
            $response['body'] = $body;
            $response['http_code'] = $httpCode;
        }
        curl_close($ch);
        return new result(true, null, $response);
    }
}

class jpushApi {
    private static $__instance;

    public static function Instance() {
        if (!self::$__instance) {
            self::$__instance = new jpushApi();
        }
        return self::$__instance;
    }

    private $api_entry_list = array();

    private function __construct() {
        foreach (C("jpush_api") as $c) {
            $this->api_entry_list[] = new jpushApiInner($c);
        }
    }

    private function callApi($method = "POST", $body = null) {
        foreach ($this->api_entry_list as $api_item) {
            $rt = $api_item->callApi($method, $body);
            if (!$rt->STS) return $rt;
        }

        return new result(true);
    }

    /*public function sendUserMessage($userId, $messageId, $messageText) {
        $opts = array();
        $opts['platform'] = "all";
        $opts['audience'] = array(
            'alias' => [$userId]
        );

        $opts['notification'] = array(
            'alert' => $messageText,
            'android' => array(
                'alert' => $messageText,
                'extras' => array(
                    'type' => 1,
                    'message_id' => $messageId
                )
            ),
            'ios' => array(
                'alert' => $messageText,
                'extras' => array(
                    'type' => 1,
                    'message_id' => $messageId
                )
            )
        );

        return $this->callApi($this->_api_entry_url, "POST", $opts);
    }*/

    public function sendUserMessage($userId,$messageId,$messageContent,$extras=array())
    {
        $opts = array();
        $opts['platform'] = "all";

        if ($userId) {
            $opts['audience'] = array(
                'alias' => [$userId]
            );
        } else {
            $opts['audience'] = "all";
        }

        if ($messageId) {
            $extra = array_merge(array(
                'type' => 1,
                'message_id' => $messageId
            ),(array) $extras);
        }else{
            $extra = array_merge(array(
                'type' => -1,
                'message_id' => 0
            ),(array) $extras);
        }

        $opts['notification'] = array(
            'alert' => $messageContent,
            'android' => array(
                'alert' => $messageContent,
                'extras' => $extra
            ),
            'ios' => array(
                'alert' => $messageContent,
                'extras' => $extra
            )
        );

        $opts['options'] = array(
            'apns_production' => !C("debug")
        );

        return $this->callApi("POST", $opts);
    }

    public function sendPushByDeviceRegistration($alias,$registration_id,$messageContent,$extras=array())
    {
        $opts = array();
        $opts['platform'] = "all";
        $opts['audience'] = array(
            //'alias' => [$alias],    // 取消别名的条件，是 & 的关系
            'registration_id' => [$registration_id]
        );

        $opts['notification'] = array(
            'alert' => $messageContent,
            'android' => array(
                'alert' => $messageContent,
                'extras' => $extras
            ),
            'ios' => array(
                'alert' => $messageContent,
                'extras' => $extras
            )
        );

        $opts['options'] = array(
            'apns_production' => !C("debug")
        );

        return $this->callApi("POST", $opts);
    }
}