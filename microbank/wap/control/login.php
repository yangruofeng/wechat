<?php

class loginControl
{
    public function __construct()
    {
        Language::read('act,label,tip');
        Tpl::setLayout('empty_layout');
        Tpl::setDir('login');
    }

    public function indexOp()
    {
        Tpl::output('html_title', L('act_login'));
        Tpl::showPage('login');
    }

    public function loginOp()
    {
        $url = ENTRY_API_SITE_URL . '/member.login.php';
        $rt = curl_post($url, $_POST);
        $rt = json_decode($rt);
        if ($rt->STS) {
          setNcCookie('token', $rt->DATA->token);
          setNcCookie('member_id', $rt->DATA->member_info->uid);
          setNcCookie('obj_guid', $rt->DATA->member_info->obj_guid);
          setNcCookie('member_name', $rt->DATA->member_info->login_code);
          setNcCookie('member_icon', $rt->DATA->member_info->member_icon);
          return new result(true, L('tip_login_success'));
        } else {
          return new result(false, L('tip_code_' . $rt->CODE));
        }
    }

    public function verifyOp()
    {
        Tpl::output('html_title', L('act_register'));
        Tpl::output('header_title', L('label_sign_up'));
        Tpl::showPage('verify');
    }

    public function getVetifyCodeOp()
    {
        $url = ENTRY_API_SITE_URL . '/phone.code.send.php';
        $rt = curl_post($url, $_POST);
        $rt = json_decode($rt, true);
        if ($rt->STS) {
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
