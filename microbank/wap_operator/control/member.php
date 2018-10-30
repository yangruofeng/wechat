<?php

class memberControl {
  public function __construct(){
    Language::read('act,label,tip');
    Tpl::setLayout('empty_layout');
    Tpl::setDir('member');
  }

  public function indexOp(){
    $member_info = array();
    $member_info['member_id'] = cookie('member_id');
    $member_info['obj_guid'] = cookie('obj_guid');
    $member_info['member_name'] = cookie('member_name');
    $member_info['member_icon'] = cookie('member_icon');
    if($rt['STS']){
      $member_info['credit'] = $rt['DATA'];
    }
    Tpl::output('member_info', $member_info);
    Tpl::output('html_title', L('label_user_center'));
    Tpl::output('header_title', L('label_account'));
    Tpl::output('nav_footer', 'account');
    Tpl::showPage('index');
  }

  public function cashOp(){
    Tpl::output('html_title', 'Cash On Hand');
    Tpl::output('header_title', 'Cash On Hand');
    Tpl::showPage('cash');
  }

  public function settingOp(){
    Tpl::output('html_title', L('label_setting'));
    Tpl::output('header_title', L('label_setting'));
    Tpl::showPage('setting');
  }

  public function logoutOp(){
    $member_id = cookie('member_id');
    if(!$member_id){
      setNcCookie('token', '');
      setNcCookie('member_id', '');
      setNcCookie('obj_guid', '');
      setNcCookie('member_name', '');
      return new result(true, L('tip_logout_success'));
    }
    $url = ENTRY_API_SITE_URL.'/member.logout.php';
    $data = array();
    $data['client_type'] = 'wap';
    $data['member_id'] = cookie('member_id');
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      setNcCookie('token', '');
      setNcCookie('member_id', '');
      setNcCookie('obj_guid', '');
      setNcCookie('member_name', '');
      return new result(true, L('tip_logout_success'));
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function loanContractOp(){
    $member_id = cookie('member_id');
    if(!$member_id){
      @header("Location: ".getUrl('login', 'index', array(), false, WAP_OPERATOR_SITE_URL)."");
    }
    Tpl::output('html_title', L('label_loan_contract'));
    Tpl::output('header_title', L('label_loan_contract'));
    Tpl::showPage('loan_contract');
  }

  public function getLoanContractDataOp(){
    $data = $_POST;
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $url = ENTRY_API_SITE_URL.'/member.loan.contract.list.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true,  L('tip_success'), $rt['DATA']);
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function loanContractDetailOp(){
    $id = $_GET['id'];
    $url = ENTRY_API_SITE_URL.'/loan.contract.detail.php';
    $data = array();
    $data['token'] = cookie('token');
    $data['contract_id'] = $id;
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    Tpl::output('detail', $rt['DATA']);
    Tpl::output('html_title', L('label_loan_contract_detail'));
    Tpl::output('header_title', L('label_loan_contract_detail'));
    Tpl::showPage('loan_contract.detail');
  }

  public function installmentSchemeOp(){
    $id = $_GET['contract_id'];
    $key = $_GET['key'];
    $url = ENTRY_API_SITE_URL.'/loan.contract.detail.php';
    $data = array();
    $data['token'] = cookie('token');
    $data['contract_id'] = $id;
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    $loan_installment_scheme = $rt['DATA']['loan_installment_scheme'];
    Tpl::output('detail', $loan_installment_scheme[$key]);
    Tpl::output('html_title', 'Installment Scheme');
    Tpl::output('header_title', 'Installment Scheme');
    Tpl::showPage('loan_contract.installmentScheme');
  }

  public function disbursementSchemeOp(){
    $id = $_GET['contract_id'];
    $key = $_GET['key'];
    $url = ENTRY_API_SITE_URL.'/loan.contract.detail.php';
    $data = array();
    $data['token'] = cookie('token');
    $data['contract_id'] = $id;
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    $loan_disbursement_scheme = $rt['DATA']['loan_disbursement_scheme'];
    Tpl::output('detail', $loan_disbursement_scheme[$key]);
    Tpl::output('html_title', 'Disbursement Scheme');
    Tpl::output('header_title', 'Disbursement Scheme');
    Tpl::showPage('loan_contract.disbursementScheme');
  }

  public function insuranceContractOp(){
    $member_id = cookie('member_id');
    if(!$member_id){
      @header("Location: ".getUrl('login', 'index', array(), false, WAP_OPERATOR_SITE_URL)."");
    }
    Tpl::output('html_title', L('label_insurance_contract'));
    Tpl::output('header_title', L('label_insurance_contract'));
    Tpl::showPage('insurance_contract');
  }

  public function getInsuranceContractDataOp(){
    $data = $_POST;
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $url = ENTRY_API_SITE_URL.'/member.insurance.contract.list.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true, L('tip_success'), $rt['DATA']);
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function insuranceContractDetailOp(){
    $id = $_GET['id'];
    $url = ENTRY_API_SITE_URL.'/insurance.contract.detail.php';
    $data = array();
    $data['token'] = cookie('token');
    $data['contract_id'] = $id;
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    Tpl::output('detail', $rt['DATA']);
    Tpl::output('html_title', L('label_insurance_contract_detail'));
    Tpl::output('header_title', L('label_insurance_contract_detail'));
    Tpl::showPage('insurance_contract.detail');
  }

  public function asiaweiluyAccountOp(){
    $member_id = cookie('member_id');
    if(!$member_id){
      @header("Location: ".getUrl('login', 'index', array(), false, WAP_OPERATOR_SITE_URL)."");
    }
    $url = ENTRY_API_SITE_URL.'/member.ace.account.info.php';
    $data = array();
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    Tpl::output('detail', $rt['DATA']);
    Tpl::output('html_title', L('label_asiaweiluy_account'));
    Tpl::output('header_title', L('label_asiaweiluy_account'));
    Tpl::showPage('asiaweiluy_account');
  }

  public function bankHandleOp(){
    $url = ENTRY_API_SITE_URL.'/member.bind.ace.php';
    $account_handler_id = $_POST['account_handler_id'];
    if($account_handler_id){
      $url = ENTRY_API_SITE_URL.'/member.edit.loan.ace.account.php';
    }
    $data = $_POST;
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true, L('tip_success'), $rt['DATA']);
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function getVetifyCodeOp(){
    $url = ENTRY_API_SITE_URL.'/phone.code.send.php';
    $rt = curl_post($url, $_POST);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true, L('tip_login_success'), $rt['DATA']);
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function changeLangOp(){
    Tpl::output('html_title', L('label_language'));
    Tpl::output('header_title', L('label_language'));
    Tpl::showPage('language');
  }

  public function aboutUsOp(){
    $url = ENTRY_API_SITE_URL.'/system.company.info.php';
    $rt = curl_post($url, array());
    $rt = json_decode($rt, true);
    Tpl::output('detail', $rt['DATA']);
    Tpl::output('html_title', L('label_about_us'));
    Tpl::output('header_title', L('label_about_us'));
    Tpl::showPage('about_us');
  }

  public function changePasswordOp(){
    Tpl::output('html_title', L('label_modify_password'));
    Tpl::output('header_title', L('label_modify_password'));
    Tpl::showPage('change_password');
  }

  public function ajaxChangePasswordOp(){
    $data = $_POST;
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $url = ENTRY_API_SITE_URL.'/member.change.pwd.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true, L('tip_modify_success'));
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function editProfileOp(){
    Tpl::output('token', cookie('token'));
    Tpl::output('member_id', cookie('member_id'));
    Tpl::output('member_icon', cookie('member_icon'));
    Tpl::output('html_title', '个人信息');
    Tpl::output('header_title', '个人信息');
    Tpl::showPage('member_profile');
  }

}
