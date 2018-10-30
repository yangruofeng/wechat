<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class um_user_logModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('um_user_log');
    }

    /**
     * 登录日志
     * @param $user_id
     * @param $client_type
     * @return ormResult
     */
    public function recordLogin($user_id, $client_type)
    {
        $new_log = $this->newRow();
        $new_log->user_id = $user_id;
        $new_log->client_id = 0;
        $new_log->client_type = $client_type;
        $new_log->login_time = Now();
        $new_log->login_ip = getIp();
        $new_log->update_time = Now();
        $insert = $new_log->insert();
        return $insert;
    }

    /**
     * 退出日志
     * @param $user_id
     * @return result
     */
    public function recordLogout($user_id)
    {
        $user_log = $this->orderBy('uid desc')->getRow(array('user_id' => $user_id));
        if ($user_log) {
            $user_log->logout_time = Now();
            $user_log->update_time = Now();
            $update = $user_log->update();
            return $update;
        } else {
            return new result(false, 'No login log!');
        }
    }
}
