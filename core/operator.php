<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/14/2016
 * Time: 12:04 AM
 */
class operator
{
    private static $instance = null;
    private static $member_info = array();
    private static $user_pool = array();


    public static function getInstance()
    {
        if (self::$instance === null || !(self::$instance instanceof operator)) {
            self::$instance = new operator();
        }
        return self::$instance;
    }

    public static function getUserInfo_old($key = 'user_info')
    {
        $_s = $_GET['token_key'] ?: ($_POST['token_key'] ?: $_COOKIE['PHPSESSID']);
        if ($_s) {
            if (self::$user_pool[$_s]) return self::$user_pool[$_s];
            session_id($_s);
            session_start();
            $key = $key ?: 'user_info';
            $arr = $_SESSION[$key];
            self::$user_pool[$_s] = $arr;
            session_write_close();
        }
        return $arr ?: array();
    }

    public static function getUserInfo($key = null)
    {

        self::getInstance();
        if (count(self::$member_info)) return self::$member_info;
        $user_uid = null;
        $member = null;
        if (!count(self::$member_info)) {

            $user_uid = $_SESSION['game_member_id'];
            if ($user_uid) {

            }
        }

        if ($member) {
            self::$member_info = $member;
        }

        if ($key && !$_COOKIE['key']) {
            // setcookie("key",$key);
        }

        return self::$member_info;
    }

    public static function getFullName($admin = '')
    {
        $user = self::getUserInfo($admin);
        return $user['member_name'];
    }

    public static function getUserCode($admin = '')
    {
        $user = self::getUserInfo($admin);
        return $user['member_name'];
    }

    public static function getUserId($admin = '')
    {
        $user = self::getUserInfo($admin);
        if (!$user) return null;
        return $user['member_id'];
    }

    public static function getMemberId($admin = '')
    {
        $user = self::getUserInfo($admin);
        return $user['member_id'];
    }

    public static function getUserType()
    {
        $user = self::getUserInfo();
        return $user['member_type'];
    }


    public static function getUserIcon($admin = '')
    {
        $user = self::getUserInfo($admin);
        return $user['avatar'];
    }

    public static function getUserCurrency($admin = '')
    {
        $user = self::getUserInfo($admin);
        return $user['currency'];
    }


    //登录获取令牌
    static function getToken($user_id, $client)
    {
        $m = M('um_user_token');
        if ($user_id) {
            $um_users_model = M('um_users');
            $member = $um_users_model->getRow(array("uid" => $user_id));
            if (!$member) {
                return null;
            } else {
                $user_code = $member->user_code;
            }
            $m->delete(array('user_id' => $user_id, 'client_type' => $client));
        } else {
            $user_code = 'root';
        }

        $row = $m->newRow();
        $token = md5($member->user_code . strval(TIMESTAMP) . strval(rand(0, 999999)));
        $row->token = $token;
        $row->user_id = $user_id;
        $row->user_code = $user_code;
        $row->create_time = TIMESTAMP;
        $row->login_time = TIMESTAMP;
        $row->client_type = $client;
        $result = $row->insert();
        if ($result->STS) {
            return $token;
        } else {
            return null;
        }
    }

    //注销令牌
    static function cancelToken($user_id, $token)
    {
        session_unset();
        session_destroy();
        $m = M('um_user_token');
        $row = $m->getRow(array("token" => $token, 'user_id' => $user_id));
        if ($row) {
            $update = $row->delete();
            $_COOKIE['key'] = '';
            setcookie("key");
            return new result($update->STS, $update->MSG);
        } else {
            return new result(false, "Invalid Token");
        }
    }

    public static function log($p)
    {
        if (!$p || !is_array($p)) {
            return false;
        }
        $p['operator_id'] = self::getUserId();
        $p['operator_name'] = self::getUserCode() . "【" . self::getFullName() . "】";
        $p["operator_ip"] = getIp();
        $p["operate_time"] = date("Y-m-d H:i:s");
        $trace = debug_backtrace();
        if (count($trace) > 1) {
            $trace_call = $trace[1];
            $p['action'] = $trace_call['class'] . "->" . $trace_call['function'];
        }
        $m = M("operate_log");
        $row = $m->newRow($p);
        $rt = $row->insert();
        return $rt;
    }
}