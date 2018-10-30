<?php

class clientControl {
  public function __construct(){
    Language::read('act,label,tip');
    Tpl::setLayout('empty_layout');
    Tpl::setDir('client');
  }

  public function indexOp(){
    $user_info = array('state' => 10);
    $token = $_GET['token'];
    $officer_id = $_GET['id'];
    if($token && $officer_id){
      $rts = userClass::getUserBaseInfo($officer_id);
      if ($rts->STS) {
        $user_info = $rts->DATA['user_info'];
        $user_info['token'] = $token;
        setNcCookie('token', $token);
        setNcCookie('member_id', $user_info['uid']);
        setNcCookie('user_code', $user_info['user_code']);
        setNcCookie('user_name', $user_info['user_name']);
          setNcCookie('user_position', $user_info['user_position']);
          setNcCookie('branch_id', $user_info['branch_id']);
      }
    }
      $position=cookie('user_position');
      if($position==userPositionEnum::CHIEF_CREDIT_OFFICER){
          $rt = chief_credit_officerClass::getFollowedMemberList(cookie('branch_id'));
      }else{
          $rt = credit_officerClass::getFollowedMemberList(cookie('member_id'));
      }

    Tpl::output('token', cookie('token'));
    Tpl::output('list', $rt);
    Tpl::output('html_title', 'Operator');
    Tpl::output('header_title', 'Operator');
    Tpl::output('nav_footer', 'client');
    Tpl::output('user_info', json_encode($user_info)); 
    Tpl::output('user_code', $user_info['user_code'] ? : cookie('user_code')); 
    Tpl::output('user_name', $user_info['user_name'] ? : cookie('user_name')); 
    Tpl::showPage('index');
  }
}
