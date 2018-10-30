<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/25
 * Time: 11:27
 */
class memberControl extends counter_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('member');
        Tpl::setDir('member');
        Tpl::setLayout('home_layout');
        $this->outputSubMenu('service');
    }

    public function homeIndexOp()
    {
        Tpl::showPage('home.index');
    }

    public function registerOp()
    {
        $m_industry = M('common_industry');
        $industry = $m_industry->select(array('uid' => array('neq', 0)));
        Tpl::output('industry', $industry);

        $work_type = (new workTypeEnum())->Dictionary();
        Tpl::output('work_type', $work_type);
        Tpl::showPage('register');
    }

    /**
     * 发送验证码
     * @param $p
     * @return result
     */
    public function sendVerifyCodeForRegisterOp($p)
    {
        $country_code = trim($p['country_code']);
        $phone_number = trim($p['phone']);
        $format_phone = tools::getFormatPhone($country_code, $phone_number);
        $contact_phone = $format_phone['contact_phone'];

        // 检查合理性
        if (!isPhoneNumber($contact_phone)) {
            return new result(false, 'Invalid phone', null, errorCodesEnum::INVALID_PARAM);
        }

        // 判断是否被其他member注册过
        $m_member = new memberModel();
        $row = $m_member->getRow(array(
            'phone_id' => $contact_phone,
        ));
        if ($row) {
            return new result(false, 'The phone number has been registered.');
        }

        $rt = $this->sendVerifyCodeOp($p);
        if ($rt->STS) {
            return new result(true, L('tip_success'), $rt->DATA);
        } else {
            return new result(false, L('tip_code_' . $rt->CODE), array('code' => $rt->CODE, 'msg' => $rt->MSG));
        }
    }

    /**
     * 发送验证码
     * @param $p
     * @return result
     */
    public function sendVerifyCodeOp($p)
    {
        $data = $p;
        $url = ENTRY_API_SITE_URL . '/phone.code.send.php';
        $rt = curl_post($url, $data);
        debug($rt);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, L('tip_success'), $rt['DATA']);
        } else {
            return new result(false, L('tip_code_' . $rt['CODE']));
        }
    }

    /**
     * 注册账号
     * @param $p
     * @return result
     */
    public function registerClientOp($p)
    {
        $p['branch_id'] = $this->user_info['branch_id'];
        $p['open_source'] = memberSourceEnum::COUNTER;
        $p['password'] = $p['login_password'];
        $p['login_code'] = $p['login_account'];
        $p['sms_id'] = $p['verify_id'];
        $p['sms_code'] = $p['verify_code'];
        $p['creator_id'] = $this->user_id;
        $p['creator_name'] = $this->user_name;
        $p['officer_id'] = $this->user_id;
        $p['cord_x'] = 0;
        $p['cord_y'] = 0;

        $full_arr = array();
        if (trim($p['address_detail'])) {
            $full_arr[] = trim($p['address_detail']);
        }

        $m_core_tree = new core_treeModel();
        if (intval($p['id4'])) {
            $id4_info = $m_core_tree->getRow(intval($p['id4']));
            $full_arr[] = $id4_info['node_text'];
        }

        if (intval($p['id3'])) {
            $id3_info = $m_core_tree->getRow(intval($p['id3']));
            $full_arr[] = $id3_info['node_text'];
        }

        if (intval($p['id2'])) {
            $id2_info = $m_core_tree->getRow(intval($p['id2']));
            $full_arr[] = $id2_info['node_text'];
        }

        if (intval($p['id1'])) {
            $id1_info = $m_core_tree->getRow(intval($p['id1']));
            $full_arr[] = $id1_info['node_text'];
        }

        $full_text = implode(', ', $full_arr);

        $p['full_text'] = $full_text;

        $rt = memberClass::phoneRegisterNew($p);
        return $rt;
    }

    /**
     * 证件采集
     */
    public function documentCollectionOp()
    {
        $client_id = intval($_GET['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $client_id));
        if ($client_info) {
            $format_phone = tools::separatePhone($client_info['phone_id']);
            Tpl::output('phone_arr', $format_phone);
        }
        Tpl::showPage('document.collection');
    }

    /**
     * 证件采集页面
     */
    public function uploadMemberCertificationPageOp()
    {
        $client_id = intval($_GET['client_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $client_id));
        if (!$client_info) {
            showMessage('No eligible clients!');
        }
        Tpl::output('client_info', $client_info);

        $cert_type = intval($_GET['type']);
        $image_structure = global_settingClass::getCertImageStructure();
        $stt = $image_structure[$cert_type];
        Tpl::output("image_structure", $stt);

        $property_list = memberIdentityClass::getIdentityType();
        Tpl::output("cert_type", $cert_type);
        Tpl::output("title", $property_list[$cert_type]);

        if($cert_type == certificationTypeEnum::ID){
            $country_code = (new nationalityEnum)->Dictionary();
            Tpl::output('country_code', $country_code);
        }

        Tpl::output('show_menu', 'documentCollection');
        Tpl::showPage('document.upload');
    }

    /**
     * 证件保存
     * @return result
     */
    public function saveMemberDocumentOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $params['auditor_id'] = $this->user_id;
        $params['auditor_name'] = $this->user_name;
        $class_member_identity = new memberIdentityClass();
        $rt = $class_member_identity->saveClientNewIdentity($params);
        return $rt;
    }

    /**
     * 获取地址选项
     * @param $p
     * @return array
     */
    public function getAreaListOp($p)
    {
        $pid = intval($p['uid']);
        $m_core_tree = M('core_tree');
        $list = $m_core_tree->getChildByPid($pid, 'region');
        return array('list' => $list);
    }

    /**
     * 获取历史记录
     * @param $p
     * @return array
     */
    public function getCertificationListOp($p)
    {
        $uid = intval($p['uid']);
        $cert_type = intval($p['cert_type']);
        $r = new ormReader();

        $sql1 = "select verify.*,member.login_code,member.display_name,member.phone_id,member.email from member_verify_cert as verify left join client_member as member on verify.member_id = member.uid where 1=1  ";

        if ($uid) {
            $sql1 .= " and verify.member_id = $uid";
        }
        if ($cert_type) {
            $sql1 .= " and verify.cert_type = $cert_type";
        }

        $sql1 .= " ORDER BY verify.uid desc";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql1, $pageNumber, $pageSize);
        $rows = $data->rows;
        $list = array();
        // 取图片
        foreach ($rows as $row) {
            $sql = "select * from member_verify_cert_image where cert_id='" . $row['uid'] . "'";
            $images = $r->getRows($sql);
            $row['cert_images'] = $images;
            $list[] = $row;
        }
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $list,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 指纹录入
     */
    public function fingerprintCollectionOp()
    {
        Tpl::showPage('fingerprint.collection');
    }

    /**
     * 获取指纹信息
     * @param $p
     * @return result
     */
    public function getClientFingermarkOp($p)
    {
        $country_code = trim($p['country_code']);
        $phone = trim($p['phone']);

        $format_phone = tools::getFormatPhone($country_code, $phone);
        $contact_phone = $format_phone['contact_phone'];

        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('phone_id' => $contact_phone, 'is_verify_phone' => 1));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }
        $client_info['member_icon'] = getImageUrl($client_info['member_icon'], imageThumbVersion::AVATAR);
        $m_member_grade = M('member_grade');
        $member_grade = $m_member_grade->find(array('uid' => $client_info['member_grade']));
        $client_info['grade_code'] = $member_grade['grade_code'];

        $m_common_fingerprint_library = M('common_fingerprint_library');
        $fingerprint_info = $m_common_fingerprint_library->orderBy('uid DESC')->find(array('obj_uid' => $client_info['obj_guid'], 'obj_type' => objGuidTypeEnum::CLIENT_MEMBER));
        if ($fingerprint_info) {
            $client_info['feature_img'] = $fingerprint_info['feature_img'];
            $client_info['certification_status'] = 'Registered';
            $client_info['certification_time'] = timeFormat($fingerprint_info['create_time']);
        } else {
            $client_info['feature_img'] = 'resource/img/member/photo.png';
            $client_info['certification_status'] = 'Unregistered';
            $client_info['certification_time'] = '';
        }

        return new result(true, '', $client_info);
    }

    /**
     * 保存指纹
     * @param $p
     * @return result
     */
    public function saveFeatureAuthenticationOp($p)
    {
        $uid = intval($p['client_id']);
        $feature_img = $p['feature_img'];

        $m_client_member = M('client_member');
        $client_info = $m_client_member->getRow(array('uid' => $uid));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

        $m_common_fingerprint_library = M('common_fingerprint_library');


        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $fingerprint = $m_common_fingerprint_library->getRow(array('obj_uid' => $client_info['obj_guid'], 'obj_type' => objGuidTypeEnum::CLIENT_MEMBER));
            if ($fingerprint) {
                $rt_1 = $fingerprint->delete();
                if (!$rt_1->STS) {
                    $conn->rollback();
                    return new result(false, 'Add Failed!1');
                }
            }

            $fingerprint_row = $m_common_fingerprint_library->newRow();
            $fingerprint_row->obj_type = objGuidTypeEnum::CLIENT_MEMBER;
            $fingerprint_row->obj_uid = $client_info['obj_guid'];
            $fingerprint_row->finger_index = 1;
            $fingerprint_row->feature_img = $feature_img;
            $fingerprint_row->feature_img = $feature_img;
            $fingerprint_row->create_time = Now();
            $rt_2 = $fingerprint_row->insert();
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Add Failed!2');
            }

            $client_info->fingerprint = $feature_img;
            $client_info->update_time = Now();
            $rt_3 = $client_info->update();
            if (!$rt_3->STS) {
                $conn->rollback();
                return new result(false, 'Add Failed!3');
            }

            $conn->submitTransaction();
            return new result(false, 'Add Successful!');

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }

    }





    /**
     * 获取保险资料
     * @param int $uid
     * @return array
     */
    public function getInsurancePrice($uid = 0)
    {
        $r = new ormReader();
        $sql1 = "select loan_contract_id,sum(price) as price from insurance_contract GROUP BY loan_contract_id";
        if ($uid) {
            $sql1 = "select loan_contract_id,sum(price) as price from insurance_contract where loan_contract_id = " . $uid . " GROUP BY loan_contract_id";
        }
        $insurance = $r->getRows($sql1);
        $insurance_arr = array();
        foreach ($insurance as $key => $value) {
            $insurance_arr[$value['loan_contract_id']] = $value;
        }
        return $insurance_arr;
    }


    /**
     * 根据申请创建合同
     * @param $p
     * @return result
     */
    public function createContractOp($p)
    {
        $uid = intval($p['uid']);

        $obj_user = new objectUserClass($this->user_id);
        $rt = $obj_user->createContractByApply($uid);
        if ($rt->STS) {
            $data = $rt->DATA;
            $contract_id = $data['Contract'];
            return new result(true, '', array('url' => getUrl('member', 'showCreateContract', array('uid' => $contract_id), false, ENTRY_COUNTER_SITE_URL)));
        } else {
            return new result(false, $rt->MSG);
        }
    }

    public function showMortgageOp()
    {

        Tpl::output('show_menu', 'loan');
        $uid = intval($_GET['uid']);
        $info = $this->getContractInfoOp(array(), $uid);
        $contract_info = $info['data'];

        Tpl::output('contract_info', $contract_info);

        //担保人
        $r = new ormReader();
        $sql = 'SELECT mg.*,cm.display_name,cm.login_code FROM member_guarantee mg LEFT JOIN client_member cm ON mg.relation_member_id = cm.uid WHERE mg.relation_state = 100 AND mg.member_id = ' . $contract_info['member_id'];
        $guarantor_list = $r->getRows($sql);
        Tpl::output('guarantor_list', $guarantor_list);

        //抵押物
        $sql = "SELECT cert.*,cert_image.image_url FROM member_verify_cert AS cert LEFT JOIN member_verify_cert_image AS cert_image ON cert_image.cert_id = cert.uid WHERE cert.verify_state = 10";
        $sql .= " AND cert.member_id = " . $contract_info['member_id'] . " AND cert.cert_type IN(" . certificationTypeEnum::HOUSE . ',' . certificationTypeEnum::LAND . ',' . certificationTypeEnum::CAR . ',' . certificationTypeEnum::MOTORBIKE . ")";
        $sql .= " ORDER BY cert.cert_type ASC";
        $rows = $r->getRows($sql);
        $list = array();
        // 取图片
        foreach ($rows as $row) {
            if (isset($list[$row['uid']])) {
                $list[$row['uid']]['img_list'][] = $row['image_url'];
            } else {
                $list[$row['uid']] = $row;
                $list[$row['uid']]['img_list'][] = $row['image_url'];
            }
        }
        Tpl::output('mortgage_list', $list);


        Tpl::showPage("loan.mortgage");
    }


    /**
     * 展示新创建合同，进行修改
     */
    public function showCreateContractOp()
    {
        Tpl::output('show_menu', 'loan');
        $uid = intval($_GET['uid']);

        $info = $this->getContractInfoOp(array(), $uid);
        $contract_info = $info['data'];

        Tpl::output('contract_info', $contract_info);

        //担保人
        $r = new ormReader();
        $sql = 'SELECT mg.*,cm.display_name,cm.login_code FROM member_guarantee mg LEFT JOIN client_member cm ON mg.relation_member_id = cm.uid WHERE mg.relation_state = 100 AND mg.member_id = ' . $contract_info['member_id'];
        $guarantor_list = $r->getRows($sql);
        Tpl::output('guarantor_list', $guarantor_list);

        //抵押物
        $sql = "SELECT cert.*,cert_image.image_url FROM member_verify_cert AS cert LEFT JOIN member_verify_cert_image AS cert_image ON cert_image.cert_id = cert.uid WHERE cert.verify_state = 10";
        $sql .= " AND cert.member_id = " . $contract_info['member_id'] . " AND cert.cert_type IN(" . certificationTypeEnum::HOUSE . ',' . certificationTypeEnum::LAND . ',' . certificationTypeEnum::CAR . ',' . certificationTypeEnum::MOTORBIKE . ")";
        $sql .= " ORDER BY cert.cert_type ASC";
        $rows = $r->getRows($sql);
        $list = array();
        // 取图片
        foreach ($rows as $row) {
            if (isset($list[$row['uid']])) {
                $list[$row['uid']]['img_list'][] = $row['image_url'];
            } else {
                $list[$row['uid']] = $row;
                $list[$row['uid']]['img_list'][] = $row['image_url'];
            }
        }
        Tpl::output('mortgage_list', $list);

        Tpl::showPage('contract.add.two');

    }

    /**
     * 确认合同
     */
    public function submitContractOp()
    {
        $param = array_merge(array(), $_GET, $_POST);
        $uid = intval($_GET['uid']);
        $guarantor_id = $param['guarantor_id'];
        $mortgage_id = $param['mortgage_id'];
        $scan_img = $param['scan_img'];

        $obj_user = new objectUserClass($this->user_id);
        $rt = $obj_user->editContractAndConfirmToExecute($uid, $guarantor_id, $mortgage_id, $scan_img);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }

        $url = getUrl('member', 'loan', array(), false, ENTRY_COUNTER_SITE_URL);
        showMessage('Submit successfully!', $url);
    }

    /**
     * member 修改交易密码
     */
    public function memberChangeTradePwdOp()
    {
        $cashier_id = $this->user_id;
        $member_id = intval($_POST['member_id']);
        $member_image = trim($_POST['member_image']);
        $verify_id = intval($_POST['verify_id']);
        $verify_code = trim($_POST['verify_code']);
        $get_fee = global_settingClass::getChangeProfileFee();
        $fee = $get_fee['change_trade_password'];
        $feeMethod = trim($_POST['feeMethod']);
        $currency = currencyEnum::USD;
        $new_trade_pwd = trim($_POST['new_trade_pwd']);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            showMessage('Invalid Id!');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $bizMember = new bizMemberChangeTradingPasswordByCounterClass();
        $rt = $bizMember->execute($cashier_id, $member_id, $member_image, $verify_id, $verify_code, $new_trade_pwd, $fee, $currency, $feeMethod);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage('Change Trade Password Success!', getUrl('member', 'profile', array('client_id' => $member_id), false, ENTRY_COUNTER_SITE_URL));
        } else {
            $conn->rollback();
            showMessage('Change Trade Password Failed!');
        }
    }


    public function sendVerifyCodeForChangePhoneOp($p)
    {
        $country_code = $p['country_code'];
        $phone_number = $p['phone'];
        $send_code = smsClass::sendVerifyCode($country_code, $phone_number);
        if ($send_code->STS) {
            return new result(true, 'Send Success', $send_code->DATA);
        } else {
            return new result(false, 'Send Failure');
        }
    }


    /**
     * member 修改电话号码
     */
    public function memberChangePhoneNumOp()
    {
        $cashier_id = $this->user_id;
        $member_id = intval($_POST['member_id']);
        $member_image = trim($_POST['member_image']);
        $verify_id = intval($_POST['verify_id']);
        $verify_code = trim($_POST['verify_code']);
        $get_fee = global_settingClass::getChangeProfileFee();
        $fee = $get_fee['change_phone_number'];
        $currency = currencyEnum::USD;
        $feeMethod = trim($_POST['feeMethod']);
        $country_code = trim($_POST['country_code']);
        $phone_number = trim($_POST['phone']);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            showMessage('Invalid Id!');
        }
        $member_phone = memberClass::checkPhoneNumberIsRegistered($country_code, $phone_number);
        if ($member_phone) {
            return new result(false, 'The Phone Has Been Used');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $bizMember = new bizMemberChangePhoneByCounterClass();
        $rt = $bizMember->execute($cashier_id, $member_id, $member_image, $verify_id, $verify_code, $country_code, $phone_number, $fee, $currency, $feeMethod);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage('Change Phone Number Success!', getUrl('member', 'profile', array('client_id' => $member_id), false, ENTRY_COUNTER_SITE_URL));
        } else {
            $conn->rollback();
            showMessage($rt->MSG);
        }
    }



    /**
     * 获取会员信息
     * @param $p
     * @return array
     */
    public function getMemberInfoOp($p)
    {
        $search_text = trim($p['search_text']);
        $r = new ormReader();
        $sql = "SELECT * FROM client_member  WHERE 1 = 1 ";
        if ($search_text) {
            $sql .= " AND (obj_guid = '" . qstr2($search_text) . "' OR display_name like '%" . qstr2($search_text) . "%' OR phone_id like '%" . qstr2($search_text) . "%' OR login_code like '%" . qstr2($search_text) . "')";
        }
        $member_info = $r->getRow($sql);

        if ($member_info) {
            $credit_info = memberClass::getCreditBalance($member_info['uid']);
            $credit_balance = array();
            $all_currency = (new currencyEnum())->toArray();
            foreach ($all_currency as $key => $currency) {
                $exchange_rate = global_settingClass::getCurrencyRateBetween(currencyEnum::USD, $key);
                if ($exchange_rate <= 0) {
                    return new result(false, 'Not set currency exchange rate:' . $currency . '-' . $currency);
                }
                $credit_balance[$key] = round($exchange_rate * $credit_info['balance'], 2);
            }
            return array(
                "sts" => true,
                "data" => $member_info,
                "credit_info" => $credit_info,
                "credit_balance" => $credit_balance,
            );
        } else {
            return array(
                "sts" => true,
                "data" => array(),
            );
        }
    }

    /**
     * 获取贷款产品
     * @param $p
     * @return array
     */
    public function getMemberProductListOp($p)
    {
        $member_id = intval($p['uid']);
        $sub_product_list = loan_productClass::getMemberCanLoanSubProductList($member_id, 1);
        foreach ($sub_product_list as $k => $v) {
            $min_monthly_rate = loan_productClass::getMinMonthlyRate($v['uid']);
            $v['monthly_min_rate'] = $min_monthly_rate . '%';
            $sub_product_list[$k] = $v;
        }
        if ($sub_product_list) {
            return array(
                "sts" => true,
                "data" => $sub_product_list,
            );
        } else {
            return array(
                "sts" => true,
                "data" => array(),
            );
        }
    }

    /**
     * 获取添加历史
     * @param $p
     * @return array
     */
    public function getAddContractListOp($p)
    {
        $cashier_id = $this->user_id;
        $r = new ormReader();
        $sql = "SELECT bmclc.*,lc.contract_sn,lsp.sub_product_name FROM biz_member_create_loan_contract bmclc INNER JOIN loan_sub_product lsp ON bmclc.sub_product_id=lsp.uid INNER JOIN loan_contract lc ON bmclc.contract_id = lc.uid WHERE bmclc.state = 100 AND bmclc.cashier_id=" . $cashier_id;
        $sql .= " ORDER BY bmclc.update_time DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }





}