<?php

class helpControl
{
    public function __construct()
    {
        Language::read('act,label,tip');
        Tpl::setLayout('empty_layout');
        Tpl::setDir('help');
    }

    public function helpListOp()
    {
        Tpl::output('html_title', L('label_help'));
        Tpl::output('header_title', L('label_help'));
        Tpl::showPage('help');
    }

    public function getHelpDataOp()
    {
        $data = $_POST;
        $data['member_id'] = cookie('member_id');
        $url = ENTRY_API_SITE_URL . '/help.list.php';
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, L('tip_success'), $rt['DATA']);
        } else {
            return new result(false, L('tip_code_' . $rt['CODE']));
        }
    }

    public function helpDetailOp()
    {
        $id = intval($_GET['id']);
        $url = ENTRY_API_SITE_URL . '/help.detail.php';
        $data = array();
        $data['uid'] = $id;
        $rt = curl_post($url, $data);
        $rt = my_json_decode($rt);
        Tpl::output('detail', $rt['DATA']['detail']);
        Tpl::output('html_title', L('label_help_detail'));
        Tpl::output('header_title', L('label_help_detail'));
        Tpl::showPage('help.detail');
    }

}
