<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/10/31
 * Time: 09:30
 */
class apiLoginControl
{
    function __construct()
    {
        Language::read('entry_index,common');
    }

    /**
     * 登录api
     * @return result
     */
    function loginOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $user_code = trim($p['user_code']);
        $user_password = trim($p['user_password']);

        if (empty($user_code)) {
            return new result(false, 'The account cannot be empty!');
        }

        if (empty($user_password)) {
            return new result(false, 'The password cannot be empty!');
        }

        $m_um_user = M('um_user');
        $user = $m_um_user->getRow(array(
            "user_code" => $user_code,
        ));

        if (empty($user)) {
            return new result(false, 'Account error!');
        }

        if ($user->user_status == 0) {
            return new result(false, 'Deactivated account!');
        }

        if (empty($user) || $user->password != md5($user_password)) {
            return new result(false, 'Password error!');
        }

        $position_arr = array(
            userPositionEnum::CREDIT_OFFICER,
            userPositionEnum::TELLER,
            userPositionEnum::CHIEF_TELLER,
        );

        if (in_array($user['user_position'], $position_arr)) {
            return new result(false, 'No access to the system.');
        }

        $allow_by_browser=array(
            userPositionEnum::ROOT,
            userPositionEnum::DEVELOPER,
            userPositionEnum::BRANCH_MANAGER
        );
        if(!$this->checkBackOfficeSecurity()){
            if (!in_array($user['user_position'], $allow_by_browser)) {
                return new result(false, 'No Permission to Access By Browser');
            }
        }

        $is_system_close = userClass::chkSystemIsClose($user['user_position']);
        if ($is_system_close) {
            return new result(false, 'System Closed.');
        }

        $data_update = array(
            'uid' => $user->uid,
            'last_login_time' => Now(),
            'last_login_ip' => getIp()
        );
        $m_um_user->update($data_update);
        $user_arr = $user->toArray();

        $m_site_depart = M('site_depart');
        $depart = $m_site_depart->find(array('uid' => $user_arr['depart_id']));
        $user_arr['depart_name'] = $depart['depart_name'];
        $branch = M("site_branch")->find(array("uid" => $depart['branch_id']));
        if($branch && !$branch['status']){
            return new result(false,"This Branch ".$branch['branch_name']." Has Been Locked:",null,errorCodesEnum::USER_LOCKED);
        }
        $user_arr['branch_id'] = $branch['uid'];
        $user_arr['branch_name'] = $branch['branch_name'];


        setSessionVar("user_info", $user_arr);
        setSessionVar("is_login", "ok");

        $m_um_user_log = M('um_user_log');
        $m_um_user_log->recordLogin($user->uid, 'web');

        if ($p["remember_me"] == 1) {
            setcookie("user_code", $p["user_code"], time() + 3600 * 24 * 7);
        } else {
            setcookie("user_code", "", time() - 3600);
        }

        return new result(true, '', array('new_url' => ENTRY_DESKTOP_SITE_URL . DS . 'index.php'));
    }
    protected function checkBackOfficeSecurity()
    {
        if (global_settingClass::getCommonSetting()['backoffice_deny_without_client']) {
            return $_COOKIE['SITE_PRIVATE_KEY'] == md5(date("Ydm"));
        } else {
            return true;
        }
    }

    /**
     * counter登录
     * @return result
     */
    public function counterLoginOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $user_code = trim($p['user_code']);
        $user_password = trim($p['user_password']);
        if (empty($user_code)) {
            return new result(false, 'The account cannot be empty!');
        }

        if (empty($user_password)) {
            return new result(false, 'The password cannot be empty!');
        }

        $m_um_user = M('um_user');
        $user = $m_um_user->getRow(array(
            "user_code" => $user_code,
        ));

        if (empty($user)) {
            return new result(false, 'Account error!');
        }

        if ($user->is_credit_officer) {
            return new result(false, 'Not a teller!');
        }

        if ($user->user_status == 0) {
            return new result(false, 'Deactivated account!');
        }

        if (empty($user) || $user->password != md5($user_password)) {
            return new result(false, 'Password error!');
        }

        //position判断能否登陆
        $position_arr = array(
            userPositionEnum::TELLER,
            userPositionEnum::CHIEF_TELLER,
            userPositionEnum::CUSTOMER_SERVICE
        );

        if (!in_array($user['user_position'], $position_arr)) {
            return new result(false, 'No access to the system!');
        }

        $is_system_close = userClass::chkSystemIsClose($user['user_position']);
        if ($is_system_close) {
            return new result(false, 'System Closed.');
        }

        $data_update = array(
            'uid' => $user->uid,
            'last_login_time' => Now(),
            'last_login_ip' => getIp()
        );
        $m_um_user->update($data_update);
        $user_arr = $user->toArray();

        $m_site_depart = M('site_depart');
        $depart = $m_site_depart->find(array('uid' => $user_arr['depart_id']));
        $user_arr['depart_id'] = $depart['uid'];
        $user_arr['depart_name'] = $depart['depart_name'];
        $branch = M("site_branch")->find(array("uid" => $depart['branch_id']));

        if($branch && !$branch['status']){
            return new result(false,"This Branch ".$branch['branch_name']." Has Been Locked:",null,errorCodesEnum::USER_LOCKED);
        }

        $user_arr['branch_id'] = $branch['uid'];
        $user_arr['branch_name'] = $branch['branch_name'];

        setSessionVar("counter_info", $user_arr);
        setSessionVar("is_login", "ok");

        $m_um_user_log = M('um_user_log');
        $m_um_user_log->recordLogin($user->uid, 'counter');

//        if ($p["remember_me"] == 1) {
//            setcookie("user_code", $p["user_code"], time() + 3600 * 24 * 7);
//        } else {
//            setcookie("user_code", "", time() - 3600);
//        }

        return new result(true, '', array('new_url' => ENTRY_COUNTER_SITE_URL . DS . 'index.php'));
    }

}
