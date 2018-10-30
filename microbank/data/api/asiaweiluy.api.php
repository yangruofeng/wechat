<?php

class asiaweiluyApi {
    private static $__instance;

    public static function Instance() {
        if (!self::$__instance) {
            self::$__instance = new asiaweiluyApi();
        }
        return self::$__instance;
    }
    private $_api_entry_url;
    private $_partner_id;
    private $_partner_key;

    public function __construct(){
        $conf = C("asiaweiluy_api");
        $this->_api_entry_url = $conf['entry_url'];
        $this->_partner_id = $conf['partner_id'];
        $this->_partner_key = $conf['partner_key'];
    }

    /**
     * @param $method
     * @param $params
     * @return result
     */
    private function callApi($method, $params) {
        $url = $this->_api_entry_url . "?method=" . $method;
        logger::record("asiaweiluy-api",$url);
        logger::record("asiaweiluy-api", http_build_query($params));
        $ret = curl_https_post($url, $params);
        logger::record("asiaweiluy-api",$ret);
        if (!$ret) return new result(false, "api server error", null, errorCodesEnum::UNKNOWN_ERROR);
        $ret = my_json_decode($ret);
        if (is_string($ret)) $ret = my_json_decode($ret);
        if (!isset($ret['return_code']))
            return new result(false, 'unknown api response', $ret, errorCodesEnum::UNKNOWN_ERROR);
        else if ($ret['return_code'] == "100")
            return new result(true, '', $ret['data']);
        else
            return new result(false, $ret['error_message'], $ret['data'], errorCodesEnum::API_ERROR_ACE_BASE + intval($ret['return_code']));
    }

    private function makeSecurity($text) {
        $sign = md5($text);
        $rand = md5(rand());
        return substr($rand, 0, 4) . $sign . substr($rand, -4);
    }

    public function test() {
        $params = array();
        $params['partner'] = $this->_partner_id;
        $params['call_time'] = date("Y-m-d H:i:s");
        $params['security'] = $this->makeSecurity($this->_partner_id.$this->_partner_key.$params['call_time']);

        return $this->callApi("ace.hello", $params);
    }

    /**
     * 发放贷款 - 开始事务
     * @param $accountAce
     * @param $amount
     * @param $currency
     * @param $description
     * @return result (
     *   STS:   true/false
     *   CODE:  错误CODE，STS=true时无此项，减API_ERROR_ACE_BASE之后的值：
     *    900 Unknown Partner
     *    905 Invalid Member Phone Number
     *    922 Inactive Member
     *    923 Partner Not Enough Balance
     *    924 Over Member Balance Limit
     *    932 Not Signed Member
     *    997 API Maintenance
     *    998 Unauthorized IP
     *    999 Illegal API Call
     *   MSG:   错误消息，STS=true时无此项
     *   DATA:  相关数据
     *    transfer_id    交易编号，通过finish确认交易
     * )
     */
    public function disburseStart($accountAce, $amount, $currency, $description) {
        $params = array();
        $params['partner'] = $this->_partner_id;
        $params['call_time'] = date("Y-m-d H:i:s");
        $params['member_phone'] = $accountAce;
        $params['currency'] = $currency;
        $params['amount'] = $amount;
        $params['description'] = $description;
        $params['security'] = $this->makeSecurity($this->_partner_id.$this->_partner_key.$accountAce.$params['call_time']);

        return $this->callApi("ace.transfer.partner2member.start", $params);
    }

    /**
     * 发放贷款 - 完成事务
     * @param $transferId
     * @return result (
     *   STS:   true/false
     *   CODE:  错误CODE，STS=true时无此项，减API_ERROR_ACE_BASE之后的值：
     *    900 Unknown Partner
     *    905 Invalid Member Phone Number
     *    940 Invalid Transfer ID
     *    941 Transfer Wrong Status
     *    997 API Maintenance
     *    998 Unauthorized IP
     *    999 Illegal API Call
     *   MSG:   错误消息，STS=true时无此项
     * )
     */
    public function disburseFinish($transferId) {
        $params = array();
        $params['partner'] = $this->_partner_id;
        $params['call_time'] = date("Y-m-d H:i:s");
        $params['transfer_id'] = $transferId;
        $params['security'] = $this->makeSecurity($this->_partner_id.$this->_partner_key.$transferId.$params['call_time']);

        return $this->callApi("ace.transfer.partner2member.finish", $params);
    }

    /**
     * 查询ACE partner账户余额
     * @return result (
     *   STS:   true/false
     *   CODE:  错误CODE，STS=true时无此项，减API_ERROR_ACE_BASE之后的值：
     *    900 Unknown Partner
     *    997 API Maintenance
     *    998 Unauthorized IP
     *    999 Illegal API Call
     *   MSG:   错误消息，STS=true时无此项
     *   DATA:  array (
     *     array(
     *      currency
     *      amount
     *     )
     *   )  --End Data--
     * )
     */
    public function queryMyBalance() {
        $params = array();
        $params['partner'] = $this->_partner_id;
        $params['call_time'] = date("Y-m-d H:i:s");
        $params['security'] = $this->makeSecurity($this->_partner_id.$this->_partner_key.$params['call_time']);

        return $this->callApi("ace.partner.check", $params);
    }

    /**
     * 查询ACE会员账户余额
     * @param $accountAce string ACE账号
     * @return result (
     *   STS:   true/false
     *   CODE:  错误CODE，STS=true时无此项，减API_ERROR_ACE_BASE之后的值：
     *    900 Unknown Partner
     *    905 Invalid Member Phone Number
     *    997 API Maintenance
     *    998 Unauthorized IP
     *    999 Illegal API Call
     *   MSG:   错误消息，STS=true时无此项
     *   DATA:  array (
     *     array(
     *      currency
     *      amount
     *     )
     *   )  --End Data--
     * )
     */
    public function queryClientBalance($accountAce) {
        $params = array();
        $params['partner'] = $this->_partner_id;
        $params['call_time'] = date("Y-m-d H:i:s");
        $params['member_phone'] = $accountAce;
        $params['security'] = $this->makeSecurity($this->_partner_id.$this->_partner_key.$accountAce.$params['call_time']);

        return $this->callApi("ace.member.check", $params);
    }

    /**
     * 自动扣收还款 - 开始事务
     * @param $accountAce
     * @param $amount
     * @param $currency
     * @param $description
     * @return result (
     *   STS:   true/false
     *   CODE:  错误CODE，STS=true时无此项，减API_ERROR_ACE_BASE之后的值：
     *    900 Unknown Partner
     *    905 Invalid Member Phone Number
     *    920 Member Not Enough Balance
     *    921 Over Partner Balance Limit
     *    932 Not Signed Member
     *    997 API Maintenance
     *    998 Unauthorized IP
     *    999 Illegal API Call
     *   MSG:   错误消息，STS=true时无此项
     *   DATA:  相关数据
     *    transfer_id    交易编号，通过finish确认交易
     * )
     */
    public function collectStart($accountAce, $amount, $currency, $description) {
        $params = array();
        $params['partner'] = $this->_partner_id;
        $params['call_time'] = date("Y-m-d H:i:s");
        $params['member_phone'] = $accountAce;
        $params['currency'] = $currency;
        $params['amount'] = $amount;
        $params['description'] = $description;
        $params['security'] = $this->makeSecurity($this->_partner_id.$this->_partner_key.$accountAce.$params['call_time']);

        return $this->callApi("ace.transfer.member2partner.start", $params);
    }

    /**
     * 自动扣收还款 - 完成事务
     * @param $transferId
     * @return result (
     *   STS:   true/false
     *   CODE:  错误CODE，STS=true时无此项，减API_ERROR_ACE_BASE之后的值：
     *    900 Unknown Partner
     *    940 Invalid Transfer ID
     *    941 Transfer Wrong Status
     *    997 API Maintenance
     *    998 Unauthorized IP
     *    999 Illegal API Call
     *   MSG:   错误消息，STS=true时无此项
     * )
     */
    public function collectFinish($transferId) {
        $params = array();
        $params['partner'] = $this->_partner_id;
        $params['call_time'] = date("Y-m-d H:i:s");
        $params['transfer_id'] = $transferId;
        $params['security'] = $this->makeSecurity($this->_partner_id.$this->_partner_key.$transferId.$params['call_time']);

        return $this->callApi("ace.transfer.member2partner.finish", $params);
    }

    /**
     * 验证是否是ACE会员
     * @param $accountAce string ACE的账号，是一个电话号码，例：+855-888123435
     * @return result (
     *   STS:   true/false
     *   CODE:  错误CODE，STS=true时无此项，减API_ERROR_ACE_BASE之后的值：
     *    900 Unknown Partner
     *    905 Invalid Member Phone Number
     *    997 API Maintenance
     *    998 Unauthorized IP
     *    999 Illegal API Call
     *   MSG:   错误消息，STS=true时无此项
     *   DATA:  相关数据
     *    flag_ace_member   1: 是ACE会员， 2: 不是ACE会员
     * )
     */
    public function verifyClientAccount($accountAce) {
        $params = array();
        $params['partner'] = $this->_partner_id;
        $params['call_time'] = date("Y-m-d H:i:s");
        $params['member_phone'] = $accountAce;
        $params['security'] = $this->makeSecurity($this->_partner_id.$this->_partner_key.$accountAce.$params['call_time']);

        return $this->callApi("ace.member.verify", $params);
    }

    /**
     * 绑定账号 - 开始事务
     * 通知ACE发送绑定通知及验证码
     * @param $accountAce string ACE的账号，是一个电话号码，例：+855-888123435
     * @return result (
     *   STS:   true/false
     *   CODE:  错误CODE，STS=true时无此项，减API_ERROR_ACE_BASE之后的值：
     *    900 Unknown Partner
     *    930 Invalid Phone Number
     *    931 Signed Member
     *    997 API Maintenance
     *    998 Unauthorized IP
     *    999 Illegal API Call
     *   MSG:   错误消息，STS=true时无此项
     *   DATA:  相关数据
     *    application_id     下一步绑定验证使用的编号
     * )
     */
    public function bindStart($accountAce) {
        $params = array();
        $params['partner'] = $this->_partner_id;
        $params['call_time'] = date("Y-m-d H:i:s");
        $params['member_phone'] = $accountAce;
        $params['security'] = $this->makeSecurity($this->_partner_id.$this->_partner_key.$accountAce.$params['call_time']);

        return $this->callApi("ace.member.sign.start", $params);
    }

    /**
     * 绑定账号 - 完成事务
     * 将用户提交的验证码以及上一步绑定开始获得的编号交由ACE验证，确认绑定
     * @param $startId string     绑定账号开始获得的编号
     * @param $verifyCode string  用户提交的验证码
     * @return result (
     *   STS:   true/false
     *   CODE:  错误CODE，STS=true时无此项，减API_ERROR_ACE_BASE之后的值：
     *    900 Unknown Partner
     *    907 Invalid Sign ID
     *    908 Invalid Sign ID Status
     *    909 Invalid Verify Code
     *    997 API Maintenance
     *    998 Unauthorized IP
     *    999 Illegal API Call
     *   MSG:   错误消息，STS=true时无此项
     *   DATA:  相关数据
     *    flag_success     1：绑定成功，2：绑定失败
     */
    public function bindFinish($startId, $verifyCode) {
        $params = array();
        $params['partner'] = $this->_partner_id;
        $params['call_time'] = date("Y-m-d H:i:s");
        $params['application_id'] = $startId;
        $params['verify_code'] = $verifyCode;
        $params['security'] = $this->makeSecurity($this->_partner_id.$this->_partner_key.$startId.$params['call_time']);

        return $this->callApi("ace.member.sign.finish", $params);
    }

    /**
     * 解除绑定账号 - 开始事务
     * 通知ACE发送解除绑定通知及验证码
     * @param $accountAce string ACE的账号，是一个电话号码，例：+855-888123435
     * @return result (
     *   STS:   true/false
     *   CODE:  错误CODE，STS=true时无此项，减API_ERROR_ACE_BASE之后的值：
     *    900 Unknown Partner
     *    930 Not ACE Member
     *    932 Not Signed Member
     *    997 API Maintenance
     *    998 Unauthorized IP
     *    999 Illegal API Call
     *   MSG:   错误消息，STS=true时无此项
     *   DATA:  相关数据
     *    application_id     下一步绑定验证使用的编号
     * )
     */
    public function unbindStart($accountAce) {
        $params = array();
        $params['partner'] = $this->_partner_id;
        $params['call_time'] = date("Y-m-d H:i:s");
        $params['member_phone'] = $accountAce;
        $params['security'] = $this->makeSecurity($this->_partner_id.$this->_partner_key.$accountAce.$params['call_time']);

        return $this->callApi("ace.member.unbind.start", $params);
    }

    /**
     * 解除绑定账号 - 完成事务
     * 将用户提交的验证码以及上一步绑定开始获得的编号交由ACE验证，确认解除绑定
     * @param $startId string     解除绑定账号开始获得的编号
     * @param $verifyCode string  用户提交的验证码
     * @return result (
     *   STS:   true/false
     *   CODE:  错误CODE，STS=true时无此项，减API_ERROR_ACE_BASE之后的值：
     *    900 Unknown Partner
     *    907 Invalid Sign ID
     *    908 Invalid Sign ID Status
     *    909 Invalid Verify Code
     *    997 API Maintenance
     *    998 Unauthorized IP
     *    999 Illegal API Call
     *   MSG:   错误消息，STS=true时无此项
     *   DATA:  相关数据
     *    flag_success     1：解除绑定成功，2：解除绑定失败
     */
    public function unbindFinish($startId, $verifyCode) {
        $params = array();
        $params['partner'] = $this->_partner_id;
        $params['call_time'] = date("Y-m-d H:i:s");
        $params['application_id'] = $startId;
        $params['verify_code'] = $verifyCode;
        $params['security'] = $this->makeSecurity($this->_partner_id.$this->_partner_key.$startId.$params['call_time']);

        return $this->callApi("ace.member.unbind.finish", $params);
    }
}