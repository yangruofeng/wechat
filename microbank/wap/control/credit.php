<?php

class creditControl {
  public function __construct(){
    Language::read('act,label,tip');
    Tpl::setLayout('empty_layout');
    Tpl::setDir('credit');
  }

  public function indexOp(){
    $member_id = cookie('member_id');
    if(!$member_id){
      @header("Location: ".getUrl('login', 'index', array(), false, WAP_SITE_URL)."");
    }
    $data['member_id'] =  $member_id;
    $data['token'] =  cookie('token');
    $url = ENTRY_API_SITE_URL.'/member.message.unread.count.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    Tpl::output('msgcount', $rt['DATA']);
    $url = ENTRY_API_SITE_URL.'/credit.loan.index.page.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    Tpl::output('data', $rt['DATA']);
    Tpl::output('html_title', L('label_loan'));
    Tpl::output('header_title', L('label_loan'));
    Tpl::output('nav_footer', 'credit');
    Tpl::showPage('index');
  }

  public function getRatetDataOp(){
    $data = $_POST;
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $url = ENTRY_API_SITE_URL.'/credit.loan.index.page.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true, L('tip_success'), $rt['DATA']);
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function certListOp(){
    $url = ENTRY_API_SITE_URL.'/member.credit.cert.list.php';
    $data = array();
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    $credit = array();
    if($rt['STS']){
      $credit = $rt['DATA'];
    }
    Tpl::output('credit', $credit);
    Tpl::output('html_title', 'Get Credit');
    Tpl::output('header_title', 'Get Credit');
    Tpl::showPage('cerification_list');
  }

  public function getCertedResultOp(){
    $url = ENTRY_API_SITE_URL.'/member.certed.result.php';
    $data = array();
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $data['type'] = $_POST['type'];
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true, L('tip_success'), array('cert_id' => $rt['DATA']['cert_result']['uid'], 'state' => $rt['DATA']['cert_result']['verify_state']));
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function certTypeListOp(){
    $type = $_GET['type'];
    $url = ENTRY_API_SITE_URL.'/member.certed.result.php';
    $data = array();
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $data['type'] = $type;
    $page = 'certtype.list';
    switch ($type) {
      case certificationTypeEnum::CAR :
        Tpl::output('html_title', L('label_vehicle_property'));
        Tpl::output('header_title', L('label_vehicle_property'));
        break;
      case certificationTypeEnum::LAND :
        Tpl::output('html_title', L('label_landg_property'));
        Tpl::output('header_title', L('label_landg_property'));
        break;
      case certificationTypeEnum::HOUSE :
        Tpl::output('html_title', L('label_housing_property'));
        Tpl::output('header_title', L('label_housing_property'));
        break;
      case certificationTypeEnum::MOTORBIKE :
        Tpl::output('html_title', L('label_motorcycle_asset_certificate'));
        Tpl::output('header_title', L('label_motorcycle_asset_certificate'));
        break;
      case certificationTypeEnum::GUARANTEE_RELATIONSHIP :
        $url = ENTRY_API_SITE_URL.'/member.guarantee.list.php';
        Tpl::output('html_title', 'Guarantee Relation');
        Tpl::output('header_title', 'Guarantee Relation');
        $page = 'certtype.list.relationship';
        break;
      default:
      Tpl::output('html_title', L('label_vehicle_property'));
      Tpl::output('header_title', L('label_vehicle_property'));
        break;
    }
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    Tpl::output('list', $rt['DATA']);
    Tpl::showPage($page);
  }

  public function cerificationOp(){
    $type = $_GET['type'];
    $cert_id = $_GET['cert_id'];
    Tpl::output('type', $type);
    Tpl::output('token', cookie('token'));
    Tpl::output('cert_id', $cert_id?:0);
    Tpl::output('member_id', cookie('member_id'));
    switch ($type) {
      case certificationTypeEnum::ID :
        Tpl::output('html_title', L('label_id_card'));
        Tpl::output('header_title', L('label_id_card'));
        Tpl::showPage('cerification.id');
        break;
      case certificationTypeEnum::FAIMILYBOOK :
        Tpl::output('html_title', L('label_family_book'));
        Tpl::output('header_title', L('label_family_book'));
        Tpl::showPage('cerification.familybook');
        break;
      case certificationTypeEnum::PASSPORT : //1111
        Tpl::output('html_title', L('label_family_book'));
        Tpl::output('header_title', L('label_family_book'));
        Tpl::showPage('cerification.familybook');
        break;
      case certificationTypeEnum::HOUSE :
        Tpl::output('html_title', L('label_housing_property'));
        Tpl::output('header_title', L('label_housing_property'));
        Tpl::showPage('cerification.house');
        break;
      case certificationTypeEnum::CAR :
        Tpl::output('html_title', L('label_vehicle_property'));
        Tpl::output('header_title', L('label_vehicle_property'));
        Tpl::showPage('cerification.car');
        break;
      case certificationTypeEnum::WORK_CERTIFICATION :
        Tpl::output('html_title', L('label_working_certificate'));
        Tpl::output('header_title', L('label_working_certificate'));
        Tpl::showPage('cerification.work');
        break;
      case certificationTypeEnum::CIVIL_SERVANT : //1111
        Tpl::output('html_title', L('label_landg_property'));
        Tpl::output('header_title', L('label_landg_property'));
        Tpl::showPage('cerification.land');
        break;
      case certificationTypeEnum::FAMILY_RELATIONSHIP :
        $url = ENTRY_API_SITE_URL.'/system.config.init.php';
        $rt = curl_post($url, array());
        $rt = json_decode($rt, true);
        Tpl::output('guarantee_relationship', $rt['DATA']['user_define']['guarantee_relationship']);
        Tpl::output('html_title', 'Add Member');
        Tpl::output('header_title', 'Add Member');
        Tpl::showPage('cerification.relationshop');
        break;
      case certificationTypeEnum::LAND :
        Tpl::output('html_title', L('label_landg_property'));
        Tpl::output('header_title', L('label_landg_property'));
        Tpl::showPage('cerification.land');
        break;
      case certificationTypeEnum::RESIDENT_BOOK :
        $url = ENTRY_API_SITE_URL.'/member.certed.result.php';
        $data = array();
        $data['token'] = cookie('token');
        $data['member_id'] = cookie('member_id');
        $data['type'] = $type;
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        Tpl::output('data', $rt);
        Tpl::output('html_title', L('label_resident_book'));
        Tpl::output('header_title', L('label_resident_book'));
        Tpl::showPage('cerification.residentbook');
        break;
      case certificationTypeEnum::MOTORBIKE : //11111
        Tpl::output('html_title', L('label_motorcycle_asset_certificate'));
        Tpl::output('header_title', L('label_motorcycle_asset_certificate'));
        Tpl::showPage('cerification.motorcycle');
        break;

      default:
        Tpl::showPage('index');
        break;
    }
  }

  public function showCertCheckInfoOp(){
    $type = $_GET['type'];
    $state = $_GET['state'];
    $cert_id = $_GET['cert_id'];
    Tpl::output('type', $type);
    Tpl::output('state', $state);
    Tpl::output('cert_id', $cert_id);
    Tpl::output('token', cookie('token'));
    Tpl::output('member_id', cookie('member_id'));
    switch ($type) {
      case certificationTypeEnum::ID :
        Tpl::output('html_title', L('label_id_card'));
        Tpl::output('header_title', L('label_id_card'));
        Tpl::showPage('cerification.id.check');
        break;
      case certificationTypeEnum::FAIMILYBOOK :
        Tpl::output('html_title', L('label_family_book'));
        Tpl::output('header_title', L('label_family_book'));
        Tpl::showPage('cerification.familybook.check');
        break;
      case certificationTypeEnum::WORK_CERTIFICATION :
        Tpl::output('html_title', L('label_working_certificate'));
        Tpl::output('header_title', L('label_working_certificate'));
        Tpl::showPage('cerification.work.check');
        break;
      case certificationTypeEnum::CAR :
        Tpl::output('html_title', L('label_vehicle_property'));
        Tpl::output('header_title', L('label_vehicle_property'));
        Tpl::showPage('cerification.car.check');
        break;
      case certificationTypeEnum::LAND :
        Tpl::output('html_title', L('label_landg_property'));
        Tpl::output('header_title', L('label_landg_property'));
        Tpl::showPage('cerification.land.check');
        break;
      case certificationTypeEnum::HOUSE :
        Tpl::output('html_title', L('label_housing_property'));
        Tpl::output('header_title', L('label_housing_property'));
        Tpl::showPage('cerification.house.check');
        break;
      default:
        Tpl::showPage('index');
        break;
    }
  }

  public function historyOp(){
    Tpl::output('html_title', L('label_credit_history'));
    Tpl::output('header_title', L('label_credit_history'));
    Tpl::showPage('history');
  }

  public function getCreditHistoryDataOp(){
    $data = $_POST;
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $url = ENTRY_API_SITE_URL.'/member.credit.release.list.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true, L('tip_success'), $rt['DATA']);
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function helpOp(){
    Tpl::output('html_title', L('label_help'));
    Tpl::output('header_title', L('label_help'));
    Tpl::showPage('help');
  }

  public function creditLoanOp(){
    $data = array();
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $url = ENTRY_API_SITE_URL.'/member.credit.cert.list.php';
    $rt = curl_post($url, $data);
    $credit = json_decode($rt, true);
    $credit_info = $credit['DATA']['credit_info'];
    Tpl::output('credit_info', $credit_info);
    $params = array();
    $params['product_id'] = $_GET['product_id'];
    $url = ENTRY_API_SITE_URL.'/credit_loan.loan.max.month.php';
    $rt = curl_post($url, $params);
    $rt = json_decode($rt, true);
    $months = $rt['DATA'];
    Tpl::output('months', $months);
    Tpl::output('html_title', L('label_loan'));
    Tpl::output('header_title', L('label_loan'));
    Tpl::showPage('credit_loan');
  }

  public function submitWithdrawOp(){
    $data = $_POST;
    $data['token'] = cookie('token');
    $data['member_id'] = cookie('member_id');
    $url = ENTRY_API_SITE_URL.'/credit_loan.withdraw.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true, L('tip_success'), array('contract_id' => $rt['DATA']['contract_id']));
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function withdrawConfirmOp(){
    $contract_id = $_GET['contract_id'];
    $data = array();
    $data['token'] = cookie('token');
    $data['contract_id'] = $contract_id;
    $url = ENTRY_API_SITE_URL.'/loan.contract.detail.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    Tpl::output('detail', $rt['DATA']);
    Tpl::output('html_title', L('label_withdraw'));
    Tpl::output('header_title', L('label_withdraw'));
    Tpl::showPage('credit_loan.confirm');
  }

  public function ajaxSubmitConfirmWithdrawOp(){
    $data = $_POST;
    $data['token'] = cookie('token');
    $url = ENTRY_API_SITE_URL.'/credit_loan.contract.confirm.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true, L('tip_success'));
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function repaymentOp(){
    $params = array();
    $params['token'] = cookie('token');
    $params['contract_id'] = $_GET['contract_id'];
    $url = ENTRY_API_SITE_URL.'/loan.contract.detail.php';
    $detail = curl_post($url, $params);
    $detail = json_decode($detail, true);
    Tpl::output('detail', $detail['DATA']);
    $url = ENTRY_API_SITE_URL.'/loan.contract.payable.info.php';
    $info = curl_post($url, $params);
    $info = json_decode($info, true);
    Tpl::output('info', $info['DATA']);
    Tpl::output('html_title', 'Repayment');
    Tpl::output('header_title', 'Repayment');
    Tpl::showPage('credit_loan.repayment');
  }

  public function repaymentPendingOp(){
    $params = array();
    $params['token'] = cookie('token');
    $params['member_id'] = cookie('member_id');
    $params['page_size'] = 10;
    $params['page_num'] = 1;
    $url = ENTRY_API_SITE_URL.'/member.loan.pending.repayment.list.php';
    $detail = curl_post($url, $params);
    $detail = json_decode($detail, true);
    Tpl::output('data', $detail['DATA']);
    Tpl::output('html_title', 'Pending Repayment');
    Tpl::output('header_title', 'Pending Repayment');
    Tpl::showPage('credit_loan.pending.repayment');
  }

  public function ajaxGetPendingRepaymentDataOp(){
    $params = array();
    $params['token'] = cookie('token');
    $params['member_id'] = cookie('member_id');
    $params['page_size'] = $_GET['page_size'];
    $params['page_num'] = $_GET['page_num'];
    $url = ENTRY_API_SITE_URL.'/member.loan.pending.repayment.list.php';
    $detail = curl_post($url, $params);
    $detail = json_decode($detail, true);
    if($rt['STS']){
      return new result(true, L('tip_success'), $rt['DATA']);
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function repaymentBoundAccountOp(){
    $params = array();
    $params['token'] = cookie('token');
    $params['member_id'] = cookie('member_id');
    $url = ENTRY_API_SITE_URL.'/member.loan.auto.deduction.account.php';
    $list = curl_post($url, $params);
    $list = json_decode($list, true);
    Tpl::output('list', $list['DATA']);
    Tpl::output('html_title', 'Bound Account');
    Tpl::output('header_title', 'Bound Account');
    Tpl::showPage('credit_loan.account.repayment');
  }

  public function repaymentCompanyReceiveAccountOp(){
    $params = array();
    $params['currency'] = $_GET['currency'];
    $url = ENTRY_API_SITE_URL.'/system.company.receive.account.php';
    $data = curl_post($url, $params);
    $data = json_decode($data, true);
    Tpl::output('list', $data['DATA']);
    Tpl::output('html_title', 'Bank Transfer');
    Tpl::output('header_title', 'Bank Transfer');
    Tpl::showPage('credit_loan.company.repayment');
  }

  public function withdrawSuccessOp(){
    Tpl::output('html_title', L('label_wap_name'));
    Tpl::output('header_title', L('label_wap_name'));
    Tpl::showPage('credit_loan.success');
  }

  public function ajaxAddRelationshipOp(){
    $data = $_POST;
    $data['member_id'] = cookie('member_id');
    $data['token'] = cookie('token');
    $url = ENTRY_API_SITE_URL.'/member.add.guarantee.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true, L('tip_success'));
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function ajaxGuaranteeConfirmOp(){
    $data = $_POST;
    $data['member_id'] = cookie('member_id');
    $data['token'] = cookie('token');
    $url = ENTRY_API_SITE_URL.'/member.guarantee.confirm.php';
    $rt = curl_post($url, $data);
    $rt = json_decode($rt, true);
    if($rt['STS']){
      return new result(true, L('tip_success'));
    }else{
      return new result(false, L('tip_code_'.$rt['CODE']));
    }
  }

  public function helpCreditOp(){
    Language::set($_GET['lang']);
    Tpl::output('html_title', L('label_what_credit'));
    Tpl::output('header_title', L('label_what_credit'));
    Tpl::showPage('help_credit');
  }

  public function helpGetCreditOp(){
    Language::set($_GET['lang']);
    Tpl::output('html_title', L('label_how_to_get_credit'));
    Tpl::output('header_title', L('label_how_to_get_credit'));
    Tpl::showPage('help_credit.get');
  }

}
