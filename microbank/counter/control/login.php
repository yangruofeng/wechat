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

    public function indexOp()
    {
        $this->loginOp();
    }

    /**
     * 登录页面
     */
    function loginOp()
    {
        if ($_COOKIE['SITE_PRIVATE_KEY']) {
            $version = $_COOKIE['CLIENT_VERSION'];
            $app_name = 'samrithisak-client';
            $rt = versionClass::checkUpdate($app_name,$version);
            if($rt->DATA['is_required'] == 1){
                Tpl::output("version", $version);
                Tpl::setDir("home");
                Tpl::setLayout("empty_layout");
                Tpl::output('download_url',$rt->DATA['download_url_1']);
                Tpl::showPage("update.page");
            } else {
                Tpl::output('login_sign', true);
                Tpl::showPage("login", "login_layout");
            }
        } else {
            Tpl::output('login_sign', true);
            Tpl::showPage("login", "login_layout");
        }
    }

    /**
     * 退出
     */
    function loginOutOp()
    {
        session_start();
        $user_id = $_SESSION['counter_info']['uid'];
        unset($_SESSION['counter_info']);
        unset($_SESSION['is_login']);
        session_write_close();
        // 退出记录日志
        $m_um_user_log = M('um_user_log');
        $m_um_user_log->recordLogout($user_id);
        if($_REQUEST['backstage']){
            return new result(true,"login-out success");
        }else{
            $login_url = getUrl("login", "login", array(), false, ENTRY_COUNTER_SITE_URL);
            @header('Location:' . $login_url);
        }
    }

}