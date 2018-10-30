<?php

class loanControl {
  public function __construct(){
    Language::read('act,label,tip');
    Tpl::setLayout('empty_layout');
    Tpl::setDir('loan');
  }

  public function indexOp(){
    $data = array();
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $url = ENTRY_API_SITE_URL.'/member.message.unread.count.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    Tpl::output('msgcount', $rt['DATA']);
    Tpl::output('html_title', 'Loan');
    Tpl::output('header_title', 'Loan');
    Tpl::output('nav_footer', 'loan');
    Tpl::showPage('index');
  }

  public function calculatorOp(){
    Tpl::output('html_title', L('label_calculation'));
    Tpl::output('header_title', L('label_calculation'));
    Tpl::showPage('calculator');
  }

  public function productInfoOp(){
    $url = ENTRY_API_SITE_URL.'/credit_loan.preview.php';
    unset($_GET['act']);
    unset($_GET['op']);
    $rt = curl_post($url, $_GET);
    $rt = json_decode($rt, true);
    $item = $rt['DATA'];
    Tpl::output('item', $item);
    Tpl::output('html_title', $item['product_info']['product_name']);
    Tpl::output('header_title', $item['product_info']['product_name']);
    Tpl::showPage('calculator.view');
  }

  public function applyOp(){
    $url = ENTRY_API_SITE_URL.'/system.config.init.php';
    $rt = curl_post($url, array());
    $rt = json_decode($rt, true);
    Tpl::output('mortgage_type', $rt['DATA']['user_define']['mortgage_type']);
    $url = ENTRY_API_SITE_URL.'/loan.propose.get.php';
    $rt = curl_post($url, array());
    $rt = json_decode($rt, true);
    Tpl::output('purpose', $rt['DATA']);
    Tpl::output('html_title', L('act_apply_online'));
    Tpl::output('header_title', L('act_apply_online'));
    Tpl::showPage('apply');
  }

  public function applyConfirmOp(){
    $url = ENTRY_API_SITE_URL.'/loan.apply.app.php';
    $data = $_POST;
    $member_id = cookie('member_id');
    if(!$member_id){
      return new result(false, L('tip_login_expires'), 10);
    }
    $data['member_id'] = $member_id;
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true, L('tip_apply_success'));
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']), 20);
    }
  }

  public function loanLevelDetailOp(){
    if($_GET['type'] == 'credit'){
      $url = ENTRY_API_SITE_URL.'/credit_loan.rate.credit.level.php';
      $rt = curl_post($url, array('rate_id'=>$_GET['id']));
      $rt = json_decode($rt, true);
      Tpl::output('item', $rt['DATA']);
    }else{
      $index = $_GET['index'] - 1;
      $url = ENTRY_API_SITE_URL.'/credit_loan.loan.level.php';
      $rt = curl_post($url, array());
      $rt = json_decode($rt, true);
      Tpl::output('item', $rt['DATA'][$index]);
    }

    Tpl::output('html_title', L('label_loan_level'));
    Tpl::output('header_title', L('label_detail'));
    Tpl::showPage('level_detail');
  }

  public function creditLevelListOp(){
    $url = ENTRY_API_SITE_URL.'/credit_loan.loan.level.php';
    $rt = curl_post($url, array());
    $rt = json_decode($rt, true);
    Tpl::output('loan_level', $rt['DATA']);
    Tpl::output('html_title', L('label_credit_loan'));
    Tpl::output('header_title', L('label_credit_loan'));
    Tpl::showPage('level_list');
  }

  public function hotLineOp(){
    $url = ENTRY_API_SITE_URL.'/system.company.hotline.php';
    $rt = curl_post($url, array());
    $rt = json_decode($rt, true);
    Tpl::output('list', $rt['DATA']);
    Tpl::output('html_title', 'Hotline');
    Tpl::output('header_title', 'Hotline');
    Tpl::showPage('hotline');
  }

  public function ajaxLoanApplyPreviewOp(){
    $params['amount'] = $_GET['amount'];
    $params['loan_time'] = $_GET['loan_time'];
    $url = ENTRY_API_SITE_URL.'/loan.apply.preview.php';
    $rt = curl_post($url, $params);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true, L('tip_apply_success'), $rt['DATA']);
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function productListOp(){
    $url = ENTRY_API_SITE_URL.'/loan.product.list.php';
    $rt = curl_post($url, array());
    $rt = json_decode($rt, true);
    $list = $rt['DATA'];
    Tpl::output('list', $list);
    Tpl::output('html_title', 'Product List');
    Tpl::output('header_title', 'Product List');
    Tpl::showPage('product_list');
  }

  public function productDetailOp(){
    $url = ENTRY_API_SITE_URL.'/loan.product.detail.php';
    $rt = curl_post($url, array('product_id'=>$_GET['id']));
    $rt = json_decode($rt, true);
    $info = $rt['DATA']['product_info'];
    Tpl::output('info', $info);
    Tpl::output('rate_list', $rt['DATA']['rate_list']);
    Tpl::output('html_title', $info['product_name']);
    Tpl::output('header_title', $info['product_name']);
    Tpl::showPage('product_detail');
  }

  public function collectOp(){
    Tpl::output('html_title', 'Collect');
    Tpl::output('header_title', 'Collect');
    Tpl::showPage('collect');
  }

  public function transferOp(){
    Tpl::output('html_title', 'Transfer');
    Tpl::output('header_title', 'Transfer');
    Tpl::showPage('transfer');
  }

  public function payOp(){
    Tpl::output('html_title', 'Pay');
    Tpl::output('header_title', 'Pay');
    Tpl::showPage('pay');
  }
}
