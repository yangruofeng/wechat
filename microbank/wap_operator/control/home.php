<?php

class homeControl extends wap_operator_baseControl
{
    public function __construct()
    {
        parent::__construct();
        //Language::read('act,label,tip');
        Tpl::setLayout('empty_layout');
        Tpl::setDir('home');
    }

    public function indexOp()
    {
        Tpl::output('html_title', L('label_index'));
        Tpl::output('header_title', L('label_home'));
        Tpl::output('nav_footer', 'home');
        Tpl::showPage('index');
    }

    public function regFirstOp()
    {
        if (!cookie('member_id')) {
            //@header("Location: ".getUrl('login', 'index', array(), false, WAP_OPERATOR_SITE_URL)."");
        }
        Tpl::output('html_title', 'Register Customer');
        Tpl::output('header_title', 'Register Customer');
        Tpl::showPage('register.first');
    }

    public function ajaxRegFirstOp()
    {
        $data = $_POST;
        $url = ENTRY_API_SITE_URL . '/phone.is.registered.php';
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        if ($rt['DATA']['is_registered']) {
            return new result(false, '手机号已被注册');
        }
        $url = ENTRY_API_SITE_URL . '/phone.code.send.php';
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        return new result(true, $rt['DATA']);
    }

    public function regSecondOp()
    {
        Tpl::output('officer_id', cookie('member_id'));
        Tpl::output('officer_name', cookie('member_name'));
        Tpl::output('html_title', 'Information');
        Tpl::output('header_title', 'Information');
        Tpl::showPage('register.second');
    }

    public function phoneRegisterOp()
    {
        $url = ENTRY_API_SITE_URL . '/phone.code.verify.php';
        $rt = curl_post($url, $_POST);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, L('tip_register_success'), array('member_id' => $rt['DATA']['uid']));
        } else {
            return new result(false, L('tip_code_' . $rt['CODE']));
        }
    }

    public function regThirdOp()
    {
        Tpl::output('html_title', 'Register Success');
        Tpl::output('header_title', 'Register Success');
        Tpl::showPage('register.third');
    }

    public function searchOp()
    {
        $this->pageCheckToken();
        $type = $_GET['type'];
        $params['type'] = $type;
        $params['guid'] = $_GET['guid'];
        $params['country_code'] = $_GET['country_code'];
        $params['phone_number'] = $_GET['phone_number'];
        $member = memberClass::searchMember($params);
        Tpl::output('data', $member);
        Tpl::output('html_title', 'Search');
        Tpl::output('header_title', 'Search');
        Tpl::showPage('search.index');
    }

    public function searchClientOp()
    {
        $re = $this->ajaxCheckToken();
        if (!$re->STS) {
            return $re;
        }
        $member = memberClass::searchMember($_POST);
        if ($member['uid']) {
            return new result(true, 'success', $member);
        } else {
            return new result(false, 'no member.');
        }
    }

    public function verifyOp()
    {
        $url = ENTRY_API_SITE_URL . '/officer.get.member.cert.result.php';
        $data = array();
        $data['token'] = cookie('token');
        $data['member_id'] = $_GET['id'];
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        $credit = array();
        if ($rt['STS']) {
            $credit = $rt['DATA'];
        }
        Tpl::output('credit', $credit);
        Tpl::output('html_title', 'Verify');
        Tpl::output('header_title', 'Verify');
        Tpl::showPage('verify.index');
    }

    public function getCertedResultOp()
    {
        $url = ENTRY_API_SITE_URL . '/officer.get.member.cert.detail.php';
        $data = array();
        $data['token'] = cookie('token');
        $data['member_id'] = $_GET['id'];
        $data['type'] = $_GET['type'];
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        if ($rt['CODE'] == errorCodesEnum::INVALID_TOKEN || $rt['CODE'] == errorCodesEnum::NO_LOGIN) {
            setNcCookie('token', '');
            setNcCookie('member_id', '');
            setNcCookie('user_code', '');
            setNcCookie('user_name', '');
            return new result(false, L('tip_code_' . $rt['CODE']), array(), $rt['CODE']);
        }
        if ($rt['STS']) {
            return new result(true, L('tip_success'), array('cert_id' => $rt['DATA']['cert_result']['uid'], 'state' => $rt['DATA']['cert_result']['verify_state']));
        } else {
            return new result(false, L('tip_code_' . $rt['CODE']));
        }
    }

    public function cerificationOp()
    {
        $type = $_GET['type'];
        $cert_id = $_GET['cert_id'];
        Tpl::output('type', $type);
        Tpl::output('token', cookie('token'));
        Tpl::output('cert_id', $cert_id ?: 0);
        Tpl::output('member_id', $_GET['id']);
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
            case certificationTypeEnum::GUARANTEE_RELATIONSHIP :
                $url = ENTRY_API_SITE_URL . '/system.config.init.php';
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
                $url = ENTRY_API_SITE_URL . '/member.certed.result.php';
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

    public function showCertCheckInfoOp()
    {
        $type = $_GET['type'];
        $state = $_GET['state'];
        $cert_id = $_GET['cert_id'];
        Tpl::output('type', $type);
        Tpl::output('state', $state);
        Tpl::output('cert_id', $cert_id);
        Tpl::output('token', cookie('token'));
        Tpl::output('member_id', $_GET['member_id']);
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
            case certificationTypeEnum::PASSPORT : //1111
                Tpl::output('html_title', L('label_family_book'));
                Tpl::output('header_title', L('label_family_book'));
                Tpl::showPage('cerification.familybook.check');
                break;
            case certificationTypeEnum::HOUSE :
                Tpl::output('html_title', L('label_housing_property'));
                Tpl::output('header_title', L('label_housing_property'));
                Tpl::showPage('cerification.house.check');
                break;
            case certificationTypeEnum::CAR :
                Tpl::output('html_title', L('label_vehicle_property'));
                Tpl::output('header_title', L('label_vehicle_property'));
                Tpl::showPage('cerification.car.check');
                break;
            case certificationTypeEnum::WORK_CERTIFICATION :
                Tpl::output('html_title', L('label_working_certificate'));
                Tpl::output('header_title', L('label_working_certificate'));
                Tpl::showPage('cerification.work.check');
                break;
            case certificationTypeEnum::GUARANTEE_RELATIONSHIP :
                $url = ENTRY_API_SITE_URL . '/system.config.init.php';
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
                Tpl::output('html_title', L('label_resident_book'));
                Tpl::output('header_title', L('label_resident_book'));
                Tpl::showPage('cerification.residentbook.check');
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

    public function certTypeListOp()
    {
        $type = $_GET['type'];
        $url = ENTRY_API_SITE_URL . '/officer.get.member.cert.detail.php';
        $data = array();
        $data['token'] = cookie('token');
        $data['member_id'] = $_GET['id'];
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
                $url = ENTRY_API_SITE_URL . '/co.get.member.guarantee.list.php';
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

    public function ajaxAddRelationshipOp()
    {
        $data = $_POST;
        $data['member_id'] = $_POST['id'];
        $data['token'] = cookie('token');
        $url = ENTRY_API_SITE_URL . '/officer.submit.guarantee.request.php';
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, L('tip_success'));
        } else {
            return new result(false, L('tip_code_' . $rt['CODE']));
        }
    }

    public function ajaxGuaranteeConfirmOp()
    {
        $data = $_POST;
        $data['member_id'] = cookie('member_id');
        $data['token'] = cookie('token');
        $url = ENTRY_API_SITE_URL . '/member.guarantee.confirm.php';
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, L('tip_success'));
        } else {
            return new result(false, L('tip_code_' . $rt['CODE']));
        }
    }

    public function addLoanRequestOp()
    {
        Tpl::output('cid', $_GET['cid']);
        Tpl::output('client_id', $_GET['id']);
        Tpl::output('client_name', $_GET['name']);
        Tpl::output('html_title', 'Request For Loan');
        Tpl::output('header_title', 'Request For Loan');
        Tpl::showPage('loan.add');
    }

    public function ajaxAddLoanRequestOp()
    {
        $params = $_POST;
        $params['officer_id'] = cookie('member_id');
        $params['token'] = cookie('token');
        $url = ENTRY_API_SITE_URL . '/co.add.loan.request.php';
        $rt = curl_post($url, $params);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, L('tip_success'));
        } else {
            //return new result(false, L('tip_code_'.$rt['CODE']));
            return new result(false, $rt['MSG']);
        }
    }

    public function ajaxAddCreditRequestOp()
    {
        $data = $_POST;
        $data['officer_id'] = cookie('member_id');
        $data['token'] = cookie('token');
        $url = ENTRY_API_SITE_URL . '/officer.submit.suggest.member.credit.php';
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, L('tip_success'));
        } else {
            //return new result(false, L('tip_code_'.$rt['CODE']));
            return new result(false, $rt['MSG']);
        }
    }

    public function clientRequestOp()
    {
        $id = $_GET['id'];
        Tpl::output('html_title', 'Client Request List');
        Tpl::output('header_title', 'Client Request List');
        Tpl::showPage('request.list');
    }

    public function ajaxClientRequestOp()
    {
        $id = $_POST['id'];
        $data = array();
        $data['member_id'] = $id;
        $url = ENTRY_API_SITE_URL . '/co.member.loan.request.list.php';
        $rt = curl_post($url, $data);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, L('tip_success'), $rt['DATA']['list']);
        } else {
            return new result(false, L('tip_code_' . $rt['CODE']));
        }
    }

    public function informationOp()
    {
        (new client_profileControl())->profileIndexPageOp();
        /*
        $this->pageCheckToken();
        $params['type'] = 1;
        $params['guid'] = $_GET['cid'];
        $member = memberClass::searchMember($params);
        Tpl::output('data', $member);
        $member_id = intval($_GET['id']);
        Tpl::output('work_info', L("work_type_" . $member['work_type']));
        //product list
        $class_loan_product = new loan_productClass();
        $product_list = $class_loan_product->getCoMemberValidSubProductList($member_id);
        $semi_balloon = false;
        foreach ($product_list as $v) {
            if ($v['interest_type'] == interestPaymentEnum::SEMI_BALLOON_INTEREST && !$v['limit_uid']) {
                $semi_balloon = true;
            }
        }
        Tpl::output('semi_balloon', $semi_balloon);
        Tpl::output('product_list', $product_list);
        $residence = memberClass::getMemberResidencePlace($member_id);
        Tpl::output('residence', $residence->DATA['address_info']);
        $account_model = new loan_accountModel();
        $account_info = $account_model->getRow(array('obj_guid' => $member['obj_guid']));
        Tpl::output('due_date', $account_info->due_date);
        Tpl::output('period', $account_info->principal_periods);
*/
        /*
        $id = $_GET['id'];
        $list = M('loan_category')->getCategoryList();//所有loan category
        $member_category = loan_categoryClass::getMemberCreditCategoryList($id, 1); //member category
        $member_category_new = array();
        foreach ($member_category as $k => $v) {
            $member_category_new[$v['category_id']] = $v;
        }
        foreach ($list as $k => $v) {
            if ($member_category_new[$v['uid']]) {
                $list[$k]['category_id'] = $v['uid'];
                $list[$k]['is_close'] = $member_category_new[$v['uid']]['is_close'];
                $list[$k]['member_id'] = $member_category_new[$v['uid']]['member_id'];
                $list[$k]['member_category_id'] = $member_category_new[$v['uid']]['uid'];
                $list[$k]['sub_product_id'] = $member_category_new[$v['uid']]['sub_product_id'];
            } else {
                $list[$k]['category_id'] = $v['uid'];
                $list[$k]['is_close'] = 1;
                $list[$k]['member_id'] = 0;
                $list[$k]['member_category_id'] = 0;
                $list[$k]['sub_product_id'] = $v['sub_product_id'];
            }
        }
        Tpl::output('category', $list);
        Tpl::output('cate_ids', json_encode(array_column($list, 'uid')));
        //输出可选的sub_product
        $sub_list = loan_productClass::getAllActiveSubProductList();
        $arr_sub = array();
        foreach ($sub_list as $item) {
            $arr_sub[] = array("sub_product_id" => $item['uid'], "sub_product_name" => $item['sub_product_name']);
        }
        $arr_sub_new = array();
        foreach ($arr_sub as $v) {
            $arr_sub_new[$v['sub_product_name']] = $v['sub_product_id'];
        }
        Tpl::output('sub_ids', json_encode(array_column($arr_sub, 'sub_product_id')));
        Tpl::output('sub_names', json_encode(array_column($arr_sub, 'sub_product_name')));
        Tpl::output('sub_arr', json_encode($arr_sub_new));
        */
/*
        $list=loan_categoryClass::getMemberCreditCategorySetting($member_id);
        Tpl::output("category",$list);

        //输出可选的sub_product
        $sub_list = loan_productClass::getAllActiveSubProductList();
        $arr_sub_product_list=array();
        foreach($sub_list as $item){
            $arr_sub_product_list[]=array("id"=>$item['uid'],"value"=>$item['sub_product_name']);
        }
        Tpl::output("sub_product_list",$arr_sub_product_list);
        //输出可选的package
        $package_list=loan_productClass::getProductPackageList();
        $arr_package=array();
        foreach($package_list as $item){
            $arr_package[]=array("id"=>$item['uid'],"value"=>$item['package']);
        }
        Tpl::output("sub_package_list",$arr_package);



        Tpl::output('html_title', 'Profile');
        Tpl::output('header_title', 'Profile');
        Tpl::showPage('information');
*/
    }

    public function limitProductOp()
    {
        $uid = intval($_GET['uid']);
        $member_id = intval($_GET['member_id']);
        $class_loan_product = new loan_productClass();
        $info = $class_loan_product->getActiveSubProductListByUid($uid);
        Tpl::output('info', $info);
        //limit product
        $limit_product = M('member_limit_loan_product')->select(array('member_id' => $member_id, 'product_code' => $info['sub_product_code']));
        Tpl::output('limit_product', $limit_product);
        Tpl::output('html_title', 'Product');
        Tpl::output('header_title', 'Product');
        Tpl::showPage('information.product');
    }

    public function submitLimitProductOp($p)
    {
        $class_loan_product = new loan_productClass();
        $member_id = $p['member_id'];
        $product_code = $p['product_code'];
        $state = $p['state'];
        $new_state = $state ? 0 : 1;
        if ($state) {//解禁
            $rt = $class_loan_product->deleteMemberLimitProduct($member_id, $product_code, cookie('member_id'));
        } else {
            $rt = $class_loan_product->setMemberLimitProduct($member_id, $product_code, cookie('member_id'));
        }
        if (!$rt->STS) {
            return new result($rt->STS, 'Handle Fail!', array('state' => $new_state));
        }
        return new result($rt->STS, 'Handle Successful!', array('state' => $new_state));
    }

    public function idCardInfomationOp()
    {
        $this->pageCheckToken();
        $params['type'] = 1;
        $params['guid'] = $_GET['cid'];
        $member = memberClass::searchMember($params);;
        Tpl::output('data', $member);
        Tpl::output('html_title', 'ID Card Info');
        Tpl::output('header_title', 'ID Card Info');
        Tpl::showPage('information.card');
    }

    public function occupationInfomationOp()
    {
        $this->pageCheckToken();
        $member_id = intval($_GET['id']);

        $member = memberClass::getMemberBaseInfo($member_id);;
        $member_industry = memberClass::getMemberIndustryInfo($member_id);

        Tpl::output("member_info", $member);
        Tpl::output("member_industry", $member_industry);
        $m_common_industry = M('common_industry');
        //industry
        $industry_list = M('common_industry')->orderBy('industry_name asc')->select(array('state' => 1));
        $industry_list_new = array();
        foreach ($industry_list as $k => $v) {
            $first = strtoupper(substr($v['industry_name'], 0, 1));
            if (in_array($first, $industry_list_new)) {
                array_push($industry_list_new[$first], $v);
            } else {
                $industry_list_new[$first][] = $v;
            }
        }
        Tpl::output('industry_list', $industry_list_new);

        $work_type = (new workTypeEnum)->Dictionary();
        $work_type_lang = enum_langClass::getWorkTypeEnumLang();
        foreach ($work_type as $key => $value) {
            $work_type_new[$key] = $work_type_lang[$key];
        }
        Tpl::output('work_type', $work_type_new);


        $m_member_work = new member_workModel();
        $work = $m_member_work->getMemberWork($member_id);
        Tpl::output('work_info', $work);
        Tpl::output('html_title', 'Occupation Info');
        Tpl::output('header_title', 'Occupation Info');

        Tpl::showPage('information.occupation');
    }

    public function submitClientWorkTypeOp($p)
    {
        $member_id = intval($p['uid']);
        $is_with_business = intval($p['is_with_business']);
        $work_type = trim($p['work_type']);

        //处理industry_list
        $m_common_industry = M('common_industry');
        $industry_list = $m_common_industry->select(array('state' => 1));
        $arr_member_industry = array();
        if ($is_with_business) {
            foreach ($industry_list as $ik => $iv) {
                if (intval($p['industry_item_' . $iv['uid']]) > 0) {
                    $arr_member_industry[] = $iv['uid'];
                }
            }
        }

        $m_client_member = M('client_member');
        $row = $m_client_member->getRow(array('uid' => $member_id));
        $row->work_type = $work_type;
        $row->is_with_business = $is_with_business;
        $row->operate_time = Now();
        $ret = $row->update();
        if ($ret->STS) {
            memberClass::setMemberIndustry($member_id, $arr_member_industry);
        }
        return new result($ret->STS, $ret->STS ? 'Handle Successful!' : $ret->MSG);
    }

    public function assetsEvaluateOp()
    {
        $this->pageCheckToken();
        $member_id = $_GET['id'];
        $officer_id = cookie('member_id');
        if ($member_id <= 0) {
            $this->pageErrorMsg(L('tip_code_' . errorCodesEnum::INVALID_PARAM));
        }
        $data = credit_officerClass::getLatestMemberAssetEvaluation($officer_id, $member_id);
        Tpl::output('data', $data);
        Tpl::output('html_title', 'Assets Evaluate');
        Tpl::output('header_title', 'Assets Evaluate');
        Tpl::showPage('assets.evaluate');
    }

    public function editAssetsEvaluateOp()
    {
        $this->pageCheckToken();
        $asset_id = $_GET['uid'];
        $m_member_assets = new member_assetsModel();
        $asset = $m_member_assets->getAssets($asset_id);
        if (!$asset) {
            $this->pageErrorMsg(L('tip_code_' . errorCodesEnum::INVALID_PARAM));
        }
        Tpl::output('data', $asset);
        $officer_id = cookie('member_id');
        $list = userClass::getOneAssetEvaluateHistoryForMember($officer_id, $asset_id);
        Tpl::output('list', $list);
        Tpl::output('html_title', 'Edit Assets Evaluate');
        Tpl::output('header_title', 'Edit Assets Evaluate');
        Tpl::showPage('assets.evaluate.edit');
    }

    public function ajaxEditEvaluteOp()
    {
        $re = $this->ajaxCheckToken();
        if (!$re->STS) {
            return $re;
        }
        $params['id'] = $_GET['id'];
        $params['valuation'] = $_GET['valuation'];
        $params['remark'] = $_GET['remark'];
        $params['officer_id'] = cookie('member_id');
        $rt = credit_officerClass::submitMemberAssetsEvaluate($params);
        if ($rt->STS) {
            return new result(true, L('tip_success'), $rt->DATA);
        } else {
            return new result(false, L('tip_code_' . $rt->CODE));
        }
    }

    public function businessEvaluateOp()
    {
        $this->pageCheckToken();
        $officer_id = cookie('member_id');
        $member_id = $_GET['id'];
        $member_industry_info = memberClass::getMemberIndustryInfo($member_id);
        $last_research_info = credit_officerClass::getLastSubmitMemberIncomeResearch($officer_id, $member_id);

        $research_lst = $last_research_info['member_industry_research'] ?: array();

        $research_lst = resetArrayKey($research_lst, "industry_id");
        foreach ($member_industry_info as $ids_k => $ids_item) {
            if ($research_lst[$ids_k]) {
                $member_industry_info[$ids_k]['research_json'] = $research_lst[$ids_k]['research_text'];
            }
        }
        Tpl::output('last_research_info', $last_research_info);
        Tpl::output('member_industry_info', $member_industry_info);
        $ret = userClass::getMemberCreditReferenceInfo($member_id, $officer_id);

        $m_industry_place = M("common_industry_place");
        $place_lst = $m_industry_place->getAll();
        Tpl::output("business_place", $place_lst ? $place_lst->toArray() : array());

        Tpl::output('total', $ret->DATA['total_income']);
        Tpl::output('html_title', 'Income Research');
        Tpl::output('header_title', 'Income Research');
        Tpl::showPage('income_research');
    }

    public function contractListOp()
    {
        Tpl::output('html_title', 'Contract List');
        Tpl::output('header_title', 'Contract List');
        Tpl::showPage('contract.list');
    }

    public function mortgageListOp()
    {
        Tpl::output('html_title', 'Mortgage List');
        Tpl::output('header_title', 'Mortgage List');
        Tpl::showPage('mortgage.list');
    }

    public function addCreditRequestOp()
    {
        (new suggest_creditControl())->editCreditPageOp();
    }

    public function creditOfficerOp()
    {
        $this->pageCheckToken();
        $member_id = $_GET['id'];
        $list = memberClass::getMemberCreditOfficerList($member_id);
        Tpl::output('list', $list);
        Tpl::output('html_title', 'Credit Officer');
        Tpl::output('header_title', 'Credit Officer');
        Tpl::showPage('credit_officer');
    }

    public function suggestHistoryOp()
    {
        $this->pageCheckToken();
        $member_id = intval($_GET['id']);
        $officer_id = intval(cookie('member_id'));
        $list = userClass::getMemberCreditSuggestHistoryByUser($member_id, $officer_id);
        Tpl::output('list', $list);
        Tpl::output('html_title', 'Suggest History');
        Tpl::output('header_title', 'Suggest History');
        Tpl::showPage('credit_suggest.history');
    }

    public function incomeResearchHistoryOp()
    {
        $this->pageCheckToken();
        $officer_id = cookie('member_id');
        $member_id = $_GET['id'];
        $list = userClass::getMemberIncomeResearchHistoryOfUser($member_id, $officer_id);
        Tpl::output('list', $list);
        Tpl::output('html_title', 'Research History');
        Tpl::output('header_title', 'Research History');
        Tpl::showPage('income_research.history');
    }

    /**
     * 保存申请
     */
    public function saveRequestCreditOp()
    {
        $params = $_POST;
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $asset_credit = @my_json_decode(urldecode($params['asset_credit']));
        $chk_increase = @my_json_decode(urldecode($params['chk_increase']));
        $credit_category = @my_json_decode(urldecode($params['credit_category']));
        $asset_credit_new = array();
        $all_category_id=array();
        if (!$params['default_credit_category_id']) {
            return new result(false,"required to choose credit category");
        }
        $all_category_id[]=$params['default_credit_category_id'];

        foreach ($asset_credit as $key => $val) {
            if ($chk_increase[$key]) {
                if (!$credit_category[$key]) {
                    showMessage("required to choose credit category");
                }
                if(!in_array($credit_category[$key],$all_category_id)){
                    $all_category_id[]=$credit_category[$key];
                }
                $asset_credit_new[] = array(
                    'asset_id' => $val['asset_id'],
                    'credit' => $val['credit'],
                    'member_credit_category_id' => $credit_category[$key]
                );
            }
        }
        $params['asset_credit'] = my_json_encode($asset_credit_new);

        $currency_credit=array();
        $credit_ccy_id=my_json_decode(urldecode($params['credit_ccy_id']));
        $credit_ccy_chk=my_json_decode(urldecode($params['credit_ccy_chk']));
        $credit_ccy_usd=my_json_decode(urldecode($params['credit_ccy_usd']));
        $credit_ccy_khr=my_json_decode(urldecode($params['credit_ccy_khr']));
        $credit_ccy_total=my_json_decode(urldecode($params['credit_ccy_total']));


        foreach($credit_ccy_id as $i=>$ccy_id){
            if(in_array($ccy_id,$all_category_id)){
                $currency_credit[$ccy_id]=array(
                    "member_credit_category_id"=>$ccy_id,
                    "credit"=>$credit_ccy_total[$i],
                    "credit_usd"=>($credit_ccy_chk[$i]?$credit_ccy_usd[$i]:$credit_ccy_total[$i]),
                    "credit_khr"=>($credit_ccy_chk[$i]?$credit_ccy_khr[$i]:0)
                );
            }
        }
        $params['credit_currency']=$currency_credit;
        $rt = credit_officerClass::submitMemberSuggestCredit($params);
        if ($rt->STS) {
            $conn->submitTransaction();
            return new result(true, $rt->MSG, null);
        } else {
            $conn->rollback();
            return new result(false, $rt->MSG, null);
        }
    }

    public function clientCbcOp()
    {
        $m_cbc = M('client_cbc');
        $data = $m_cbc->getClientLatestCbcDetail($_GET['id']);
        Tpl::output('data', $data);
        Tpl::output('html_title', 'Client CBC');
        Tpl::output('header_title', 'Client CBC');
        Tpl::showPage('client.cbc');
    }

    public function clientReportOp()
    {
        Tpl::setDir("report");
        Tpl::setLayout("weui_layout");
        Tpl::output('html_title', 'Report');
        Tpl::output('header_title', 'Report');
        Tpl::showPage('report.index');
    }

    public function mortgagedAssetOp()
    {
        $m_member_assets = M('member_assets');
        $list = $m_member_assets->getMemberMortgaged($_GET['member_id']);
        Tpl::output('data', $list);
        Tpl::output('html_title', 'Mortgaged Asset');
        Tpl::output('header_title', 'Mortgaged Asset');
        Tpl::showPage('client.mortgaged.asset');
    }

    public function inputTestOp()
    {
        Tpl::output('html_title', 'input test');
        Tpl::output('header_title', 'input test');
        Tpl::showPage('input.test');
    }


    public function submitClientRepaymentDayOp($p)
    {
        $cid = intval($p['cid']);
        $day = intval($p['day']);
        $m_loan_account = M('loan_account');
        $row = $m_loan_account->getRow(array('obj_guid' => $cid));
        if (!$row->uid) {
            return new result(false, L('tip_code_' . errorCodesEnum::NO_LOAN_ACCOUNT));
        }
        $ret = loan_accountClass::editLoanAccountDueDate($cid, $day);
        return $ret;
    }

    public function submitClientPrincipalPeriodOp($p)
    {
        $cid = intval($p['cid']);
        $period = intval($p['period']);
        $m_loan_account = M('loan_account');
        $row = $m_loan_account->getRow(array('obj_guid' => $cid));
        if (!$row->uid) {
            return new result(false, L('tip_code_' . errorCodesEnum::NO_LOAN_ACCOUNT));
        }
        $ret = loan_accountClass::editLoanAccountPrincipalPeriod($cid, $period);
        return $ret;
    }

    public function creditCategoryOp()
    {
        $id = $_GET['id'];
        /*
        $list = M('loan_category')->getCategoryList();//所有loan category
        $member_category = loan_categoryClass::getMemberCreditCategoryList($id, 1); //member category
        $member_category_new = array();
        foreach ($member_category as $k => $v) {
            $member_category_new[$v['category_id']] = $v;
        }
        foreach ($list as $k => $v) {
            if ($member_category_new[$v['uid']]) {
                $list[$k]['category_id'] = $v['uid'];
                $list[$k]['is_close'] = $member_category_new[$v['uid']]['is_close'];
                $list[$k]['member_id'] = $member_category_new[$v['uid']]['member_id'];
                $list[$k]['member_category_id'] = $member_category_new[$v['uid']]['uid'];
                $list[$k]['sub_product_id'] = $member_category_new[$v['uid']]['sub_product_id'];
            } else {
                $list[$k]['category_id'] = $v['uid'];
                $list[$k]['is_close'] = 1;
                $list[$k]['member_id'] = 0;
                $list[$k]['member_category_id'] = 0;
                $list[$k]['sub_product_id'] = $v['sub_product_id'];
            }
        }
        Tpl::output('category', $list);
        Tpl::output('cate_ids', json_encode(array_column($list, 'uid')));
        */
        $list=loan_categoryClass::getMemberCreditCategorySetting($id);
        Tpl::output("category",$list);

        //输出可选的sub_product
        $sub_list = loan_productClass::getAllActiveSubProductList();
        $arr_sub_product_list=array();
        foreach($sub_list as $item){
            $arr_sub_product_list[]=array("id"=>$item['uid'],"value"=>$item['sub_product_name']);
        }
        Tpl::output("arr_sub_product_list");

        /*
        $arr_sub = array();
        foreach ($sub_list as $item) {
            $arr_sub[] = array("sub_product_id" => $item['uid'], "sub_product_name" => $item['sub_product_name']);
        }
        $arr_sub_new = array();
        foreach ($arr_sub as $v) {
            $arr_sub_new[$v['sub_product_name']] = $v['sub_product_id'];
        }
        Tpl::output('sub_ids', json_encode(array_column($arr_sub, 'sub_product_id')));
        Tpl::output('sub_names', json_encode(array_column($arr_sub, 'sub_product_name')));
        Tpl::output('sub_arr', json_encode($arr_sub_new));
        */

        Tpl::output('html_title', 'Credit Category');
        Tpl::output('header_title', 'Credit Category');
        Tpl::showPage('credit.category');
    }

    public function submitLoanCategoryOp($p)
    {
        $officer_id = cookie('member_id');
        $member_id = intval($p['member_id']);
        $category_id = intval($p['category_id']);
        $is_close = intval($p['state']);
        $params['officer_id'] = $officer_id;
        $params['member_id'] = $member_id;
        $params['category_id'] = $category_id;
        $params['is_close'] = $is_close;
        //查询member_credit_category 是否存在
        $m_member_category = M('member_credit_category');
        $info = $m_member_category->find(array('member_id' => $member_id, 'category_id' => $category_id));
        if ($info) { //存在则更改is_close
            $ret = $m_member_category->updateMemberState($params);
        } else {//否则添加
            $ret = $m_member_category->addMemberCategory($params);
        }
        return $ret;
    }

    public function submitLoanCategoryProductOp($p)
    {
        $m=new member_credit_categoryModel();
        $member_id=$p['member_id'];
        $category_id=$p['category_id'];
        $product_id=$p['product_id'];
        $row=$m->getRow(array("member_id"=>$member_id,"category_id"=>$category_id));
        if(!$row){
            return new result(false,"this category has not been set up yet");
        }
        $row->sub_product_id=intval($product_id);
        $row->update_time=Now();
        $row->update_operator_id=cookie('member_id');
        return $row->update();
    }
    public function submitLoanCategoryInterestOp($p){
        $m=new member_credit_categoryModel();
        $member_id=$p['member_id'];
        $category_id=$p['category_id'];
        $package_id=$p['package_id'];
        $row=$m->getRow(array("member_id"=>$member_id,"category_id"=>$category_id));
        if(!$row){
            return new result(false,"this category has not been set up yet");
        }
        $row->interest_package_id=intval($package_id);
        $row->update_time=Now();
        $row->update_operator_id=cookie('member_id');
        return $row->update();
    }




}
