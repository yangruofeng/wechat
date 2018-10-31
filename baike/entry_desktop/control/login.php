<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/10/30
 * Time: 16:00 PM
 */
class loginControl
{
    function __construct()
    {
        Tpl::setDir("login");
    }

    /**
     * 登录页面
     */
    function loginOp()
    {
        //if (!$this->checkSecurity()) die("Access Denied");
        $m = new common_app_versionModel();

        $download_url = getConf('app_download_url');

        $member_app = $m->orderBy('uid desc')->find(array(
            'app_name' => appTypeEnum::MEMBER_APP
        ));
        if ($member_app) {
            $member_app['download_url'] = $download_url . '/' . $member_app['download_url'];
        }

        $operator_app = $m->orderBy('uid desc')->find(array(
            'app_name' => appTypeEnum::OPERATOR_APP
        ));
        if ($operator_app) {
            $operator_app['download_url'] = $download_url . '/' . $operator_app['download_url'];

        }

        Tpl::output('member_app', $member_app);
        Tpl::output('operator_app', $operator_app);
        Tpl::output('login_sign', true);
        Tpl::showPage("login", "login_layout");
    }

    protected function checkSecurity()
    {
        if (global_settingClass::getCommonSetting()['backoffice_deny_without_client']) {
            return $_COOKIE['SITE_PRIVATE_KEY'] == md5(date("Ydm"));
        } else {
            return true;
        }
    }

    /**
     * 退出
     */
    function loginOutOp()
    {
        session_start();
        $user_id = $_SESSION['user_info']['uid'];
        unset($_SESSION['user_info']);
        unset($_SESSION['is_login']);
        session_write_close();
        // 退出记录日志
        $m_um_user_log = M('um_user_log');
        $m_um_user_log->recordLogout($user_id);
        $login_url = getUrl("login", "login", array(), false, ENTRY_DESKTOP_SITE_URL);
        @header('Location:' . $login_url);
    }

}