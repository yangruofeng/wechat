<?php

class loginControl
{
    public function __construct()
    {
        Language::read('act,label,tip');
        Tpl::setLayout('empty_layout');
        Tpl::setDir('login');
    }

    public function indexOp(){
        Tpl::output('html_title', L('act_login'));
        Tpl::showPage('login');
    }

    public function loginOp(){
        $url = ENTRY_API_SITE_URL . '/officer.login.php';
        $rt = curl_post($url, $_POST);
        $rt = json_decode($rt,true);
        // 数组取用
        if ($rt['STS']) {
            $user_info = $rt['DATA']['user_info'];
            $expire_time = 7*24*3600;
            setNcCookie('token', $rt['DATA']['token'],$expire_time);
            setNcCookie('member_id', $user_info['uid'],$expire_time);
            setNcCookie('obj_guid', $user_info['obj_guid'],$expire_time);
            setNcCookie('member_name', $user_info['user_name'],$expire_time);
            setNcCookie('user_position', $user_info['user_position'],$expire_time);
            setNcCookie('branch_id', $user_info['branch_id'],$expire_time);
            setNcCookie('user_code', $user_info['user_code'],$expire_time);
            setNcCookie('user_name', $user_info['user_name'],$expire_time);

            return new result(true, L('tip_login_success'));
        } else {
            return new result(false, L('tip_code_' . $rt->CODE));
        }
    }

    public function verifyOp(){
        Tpl::output('html_title', L('act_register'));
        Tpl::output('header_title', L('label_sign_up'));
        Tpl::showPage('verify');
    }

    public function getVetifyCodeOp(){
        $url = ENTRY_API_SITE_URL . '/phone.code.send.php';
        $rt = curl_post($url, $_POST);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, L('tip_login_success'), $rt['DATA']);
        } else {
            return new result(false, L('tip_code_' . $rt['CODE']));
        }
    }

    public function phoneRegisterOp()
    {
        $url = ENTRY_API_SITE_URL . '/member.phone.register.php';
        $rt = curl_post($url, $_POST);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, L('tip_register_success'), array('member_id' => $rt['DATA']['uid']));
        } else {
            return new result(false, L('tip_code_' . $rt['CODE']));
        }
    }

    public function registerOp()
    {
        Tpl::output('html_title', L('act_register'));
        Tpl::output('header_title', L('label_sign_up'));
        Tpl::showPage('register');
    }

    public function registerDetailOp()
    {
        $url = ENTRY_API_SITE_URL . '/member.edit.register.info.php';
        $rt = curl_post($url, $_POST);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, L('tip_register_success'), array('member_id' => $rt['DATA']['uid']));
        } else {
            return new result(false, L('tip_code_' . $rt['CODE']));
        }
    }

    public function forgotPasswordOp()
    {
        Tpl::output('html_title', L('act_forgot_password'));
        Tpl::output('header_title', L('act_forgot_password'));
        Tpl::showPage('forgot_pwd');
    }

}
