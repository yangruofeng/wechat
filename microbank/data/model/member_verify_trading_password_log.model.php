<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/9
 * Time: 13:36
 */
class member_verify_trading_password_logModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_verify_trading_password_log');
    }


    public function addLog($member_id, $input_password, $is_error = 0, $remark = '')
    {
        $log = $this->newRow();
        $log->member_id = $member_id;
        $log->input_password = $input_password;
        $log->is_error = intval($is_error);
        $log->remark = $remark;
        $log->client_ip = getIp();
        $log->request_uri = get_current_url();
        $log->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $log->create_time = Now();
        $insert = $log->insert();
        return $insert;
    }


    public function getDayErrorTimes($member_id)
    {
        $day = date('Y-m-d');
        $sql = "select count(*) from member_verify_trading_password_log 
        where member_id='$member_id' and is_error='1' AND is_clear = 0 and date_format(create_time,'%Y-%m-%d')='$day' ";
        $num = $this->reader->getOne($sql);
        return $num;
    }

    /**
     * @param $member_id
     * @return result
     */
    public function clearErrorTimes($member_id)
    {
        $sql = "UPDATE member_verify_trading_password_log SET is_clear = 1 WHERE is_error = 1 AND member_id = " . intval($member_id);
        $rt = $this->conn->execute($sql);
        if ($rt->STS) {
            return new result(true, 'Clear successful.');
        } else {
            return new result(false, 'Clear failed.');
        }
    }

}