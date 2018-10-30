<?php

class messageControl {
  public function __construct(){
    Language::read('act,label,tip');
    Tpl::setLayout('empty_layout');
    Tpl::setDir('message');
  }

  public function indexOp(){
    Tpl::output('html_title', L('label_message'));
    Tpl::output('header_title', L('label_message'));
    Tpl::output('nav_footer', 'message');
    Tpl::showPage('index');
  }

  public function getmessageDataOp(){
    $data = $_POST;
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $url = ENTRY_API_SITE_URL.'/member.message.list.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true, L('tip_success'), $rt['DATA']);
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function readOp(){
    $data = array();
    $data['message_id'] = $_GET['msg'];
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $url = ENTRY_API_SITE_URL.'/member.message.read.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    Tpl::output('detail', $rt['DATA']);
    Tpl::output('html_title', 'Message Detail');
    Tpl::output('header_title', 'Message Detail');
    Tpl::showPage('msg.detail');
  }
}
