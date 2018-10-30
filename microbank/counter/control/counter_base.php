<?php

class counter_baseControl extends control
{
    public $user_id;
    public $user_name;
    public $user_info;
    public $auth_list;
    public $user_position;
    public $branch_id;//记录当前counter的分行id， add by tim
    public $branch_name;

    function __construct()
    {
        if (!$this->checkSecurity()) die("Access Denied");

        Language::read('auth,define,common_lang');
        $this->checkLogin();
        $user = userBase::Current('counter_info');
        $user_info = $user->property->toArray();
        $this->user_info = $user_info;
        $this->user_id = $user_info['uid'];
        $this->user_name = $user_info['user_code'];
        $this->user_position = $user_info['user_position'];
        //$auth_arr = $user->getAuthList();
        //$this->auth_list = $auth_arr['counter'];
        $counter_info = getSessionVar("counter_info");
        $this->branch_id = $counter_info['branch_id'];
        $this->branch_name = $counter_info['branch_name'];
        Tpl::output("token_uid", $this->user_id);
        Tpl::output("token_passport", md5($user_info['password']));

        $is_system_close = userClass::chkSystemIsClose($this->user_position);
        if ($is_system_close) {
            $this->alertExit("System Closed.");
        }
    }

    protected function checkSecurity()
    {
        if (global_settingClass::getCommonSetting()['counter_deny_without_client']) {
            return $_COOKIE['SITE_PRIVATE_KEY'] == md5(date("Ydm"));
        } else {
            return true;
        }
    }

    protected function checkLogin()
    {
        if (!getSessionVar("is_login") || !getSessionVar("counter_info")) {
            $ref_url = request_uri();
            $login_url = getUrl("login", "login", array("ref_url" => urlencode($ref_url)), false, ENTRY_COUNTER_SITE_URL);
            @header('Location:' . $login_url);
            die();
        } else {
            return operator::getUserInfo();
        }
    }

    protected function outputSubMenu($key)
    {
        $reset_menu = $this->getResetMenu();
        $sub_menu = $reset_menu[$key]['child'];
        Tpl::output('sub_menu', $sub_menu);
    }

    /**
     * 根据权限获取menu
     * @return array
     */
    protected function getResetMenu()
    {
        $index_menu = $this->getIndexMenu();
        /*
        foreach ($index_menu as $key => $menu) {
            foreach ($menu['child'] as $k => $child) {
                $argc = explode(',', $child['args']);//分割args字符串
                $auth = $child['auth'] ?: ($argc[1] . '_' . $argc[2]);//取control和function连接
                if (!in_array($auth, $this->auth_list)) { //判断是否存在权限
                    unset($index_menu[$key]['child'][$k]);//没有权限就删除
                }
            }
            if (empty($index_menu[$key]['child'])) {//如果整个child都为空，及二级菜单都为空，不展示一级菜单
                unset($index_menu[$key]);
            }
            if ($key == 'cash_in_vault' && $this->user_position != userPositionEnum::CHIEF_TELLER) {
                unset($index_menu[$key]);
            }
        }*/
        switch ($this->user_position) {
            case userPositionEnum::TELLER:
                unset($index_menu['service']);
                unset($index_menu['cash_in_vault']);
                unset($index_menu['gl_account']);
                unset($index_menu['manual_voucher']);

                break;
            case userPositionEnum::CHIEF_TELLER:
                unset($index_menu['service']);
                unset($index_menu['cash_on_hand']);
                unset($index_menu['member']);
                unset($index_menu['staff']);
                break;
            case userPositionEnum::CUSTOMER_SERVICE:
                unset($index_menu['member']);
                unset($index_menu['mortgage']);
                unset($index_menu['cash_in_vault']);
                unset($index_menu['cash_on_hand']);
                unset($index_menu['gl_account']);
                unset($index_menu['gl_voucher']);
                unset($index_menu['staff']);
                break;
            default:
                return array();
        }

        //

        return $index_menu;

    }

    /**
     * 定义menu
     * @return array
     */
    protected function getIndexMenu()
    {
        $indexMenu = array(
            'member' => array(
                "title" => 'Member',
                'args' => 'microbank/counter,member_index,start'
            ),
            'staff' => array(
                "title" => 'Staff',
                'child' => array(
                    array('args' => 'microbank/counter,staff,registerFingerprint', 'title' => 'Register Fingerprint'),
                )
            ),
            /*
            'pending_approve' => array(
                "title" => 'Pending Approve',
                'args'=>'microbank/counter,member_index,bizPendingCtApprove'
            ),
            */
            /*
            'company' => array(
                "title" => 'Company',
                'child' => array(
                    array('args' => 'microbank/counter,company,index', 'title' => 'Index'),
                )
            ),
            */
            'service' => array(
                "title" => 'Service',
                'child' => array(
                    array('args' => 'microbank/counter,service,loanConsult', 'title' => 'Loan Consult'),
                    array('args' => 'microbank/counter,member,register', 'title' => 'Register'),
                    array('args' => 'microbank/counter,member,documentCollection', 'title' => 'Document collection'),
                    array('args' => 'microbank/counter,member,fingerprintCollection', 'title' => 'Fingerprint Collection'),
                    array('args' => 'microbank/counter,service,currencyExchange', 'title' => 'Currency Exchange'),
                )
            ),
            /*
            'my_client'=>array(
                array('args' => 'microbank/backoffice,web_credit,client', 'auth' => 'member_my_client', 'cross_domain' => 1, 'title' => 'My Client'),
            ),
            'my_consult'=>array(
                array('args' => 'microbank/backoffice,operator,consultation', 'auth' => 'service_my_consultation', 'cross_domain' => 1, 'title' => 'My Consultation'),
            ),
            */
            'mortgage' => array(
                "title" => 'Mortgage',
                'child' => array(
                    array('args' => 'microbank/counter,mortgage,myStoragePage', 'title' => 'My Storage'),
                    array('args' => 'microbank/counter,mortgage,pendingReceiveFromTransfer', 'title' => 'Pending Receive From Transfer'),
                    array('args' => 'microbank/counter,mortgage,pendingReceiveFromClient', 'title' => 'Pending Receive From Client'),
                    array('args' => 'microbank/counter,mortgage,pendingWithdrawByRequest', 'title' => 'Request Withdraw Client'),
                    array('args' => 'microbank/counter,mortgage,branchSafe', 'title' => 'Safe'),
                )
            ),
            'cash_on_hand' => array(
                "title" => 'Cash On Hand',
                'child' => array(
                    array('args' => 'microbank/counter,cash_on_hand,transactions', 'title' => 'Transactions'),
                    array('args' => 'microbank/counter,cash_on_hand,pendingReceive', 'title' => 'Pending Receive'),
                    array('args' => 'microbank/counter,cash_on_hand,transferToVault', 'title' => 'Transfer To Vault'),
                    array('args' => 'microbank/counter,cash_on_hand,cashIn', 'title' => 'Cash In'),
                    array('args' => 'microbank/counter,cash_on_hand,cashOut', 'title' => 'Cash Out'),
                    array('args' => 'microbank/counter,cash_on_hand,journalVoucher', 'title' => 'Journal Voucher'),
                    array('args' => 'microbank/counter,cash_on_hand,dayVoucher', 'title' => 'Daily Report'),

                )
            ),
            'cash_in_vault' => array(
                "title" => 'Cash In Vault',
                'child' => array(
                    array('args' => 'microbank/counter,cash_in_vault,transactions', 'title' => 'Transactions'),
                    array('args' => 'microbank/counter,cash_in_vault,transferToCashier', 'title' => 'Transfer To Cashier'),
                    array('args' => 'microbank/counter,cash_in_vault,pendingReceive', 'title' => 'Pending Receive'),
                    array('args' => 'microbank/counter,cash_in_vault,bank', 'title' => 'Bank'),
                    array('args' => 'microbank/counter,cash_in_vault,cashier', 'title' => 'Cashier'),
                    array('args' => 'microbank/counter,cash_in_vault,cashIn', 'title' => 'Extra Cash In'),
                    array('args' => 'microbank/counter,cash_in_vault,cashOut', 'title' => 'Extra Cash Out'),
                    array('args' => 'microbank/counter,cash_in_vault,exchange', 'title' => 'Exchange Currency'),
                    array('args' => 'microbank/counter,cash_in_vault,journalVoucher', 'title' => 'Journal Voucher'),
                    array('args' => 'microbank/counter,cash_in_vault,dailyReport', 'title' => 'Daily Report'),
                )
            ),
            /*
            'gl_account' => array(
                "title" => 'GL Accounts',
                'child' => array(
                    array('args' => 'microbank/counter,gl_tree,index', 'title' => 'Tree Style'),
                    array('args' => 'microbank/counter,gl_tree,showTableStyle', 'title' => 'Table Style'),
                    array('args' => 'microbank/counter,gl_tree,showUserDefined', 'title' => 'User Defined')
                )
            ),
            'gl_voucher' => array(
                "title" => 'Manual Voucher',
                'child' => array(
                    array('args' => 'microbank/counter,gl_voucher,voucherIndex', 'title' => 'New Voucher'),
                    array('args' => 'microbank/counter,gl_voucher,showVoucherListPage', 'title' => 'Voucher List'),
                )
            ),
            */
            'rule' => array(
                "title" => 'Rule',
                'child' => array(
                    array('args' => 'microbank/counter,rule,counterBiz', 'title' => 'Biz Rule'),
                )
            ),
            'report_loan_summary'=>array(
                "title" => 'Loan Summary',
                'icon'=>'rule',
                'child' => array(
                    array('args' => 'microbank/backoffice,report_loan,loan', 'auth' => 'member_my_client', 'cross_domain' => 1, 'title' => 'Loan Summary')
                )
            ),
            'report_loan_analysis'=>array(
                "title" => 'Loan Analysis',
                'icon'=>'rule',
                'child' => array(
                    array('args' => 'microbank/backoffice,report_loan_analysis,index', 'cross_domain' => 1, 'title' => 'Loan Analysis')
                )
            )
        );
        return $indexMenu;
    }

    /**
     * 获取member的业务菜单
     *
     */
    public function getMemberBusinessMenu()
    {
        $menu = array(
            'client_panel' => array('args' => 'microbank/counter,member_index,index', 'title' => 'Client Panel'),
            moduleBusinessEnum::MODULE_CREDIT => array('args' => 'microbank/counter,member_credit,showClientCreditMain', 'title' => 'Credit Agreement'),
            moduleBusinessEnum::MODULE_LOAN => array('args' => 'microbank/counter,member_loan,loanIndex', 'title' => 'Loan(Any Time)'),
            moduleBusinessEnum::MODULE_LOAN_ONE_TIME => array('args' => 'microbank/counter,member_loan,loanOneTimeIndex', 'title' => 'Loan(One Time)'),
            moduleBusinessEnum::MODULE_LOAN_REPAY => array('args' => 'microbank/counter,member_loan,repaymentIndex', 'title' => 'Repayment'),
            moduleBusinessEnum::MODULE_DEPOSIT => array('args' => 'microbank/counter,member_cash,depositIndex', 'title' => 'Deposit'),
            moduleBusinessEnum::MODULE_WITHDRAW => array('args' => 'microbank/counter,member_cash,withdrawIndex', 'title' => 'Withdrawal'),
            'penalty' => array('args' => 'microbank/counter,member_loan,penaltyIndex', 'title' => 'Penalty'),
            'voucher' => array('args' => 'microbank/counter,member_voucher,clientVoucherIndex', 'title' => 'Voucher'),
            'mortgage' => array('args' => 'microbank/counter,member_mortgage,clientMortgageIndex', 'title' => 'Mortgage'),
        );

        // 开关控制
        $business_model = global_settingClass::getModuleBusinessSetting(bizSceneEnum::COUNTER);
        $modules = array_keys($menu);
        foreach ($modules as $code) {
            if ($business_model[$code]) {
                $data_value = $business_model[$code];
                if (!$data_value['is_show'] || $data_value['is_close']) {
                    unset($menu[$code]);
                }
            }
        }

        return $menu;
    }


    /**
     * 获取client信息  move from memberControl by tim
     * @param $p
     * @return result
     */
    public function getClientInfoOp($p)
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

        $m_member_grade = M('member_grade');
        $member_grade = $m_member_grade->find(array('uid' => $client_info['member_grade']));
        $client_info['grade_code'] = $member_grade['grade_code'];
        $client_info['member_state_text'] = L('client_member_state_' . $client_info['member_state']);

        $identity_type = memberIdentityClass::getIdentityType();
        $identity_type = array_keys($identity_type);
        $identity_type_str = '(' . implode(',', $identity_type) . ')';
        $sql = "select * from member_verify_cert WHERE member_id = " . qstr($client_info['uid']) . " and cert_type in $identity_type_str and verify_state = " . qstr(certStateEnum::PASS);
        $r = new ormReader();
        $identity_list = $r->getRows($sql);
        $identity_list = resetArrayKey($identity_list, 'cert_type');
        foreach ($identity_type as $key) {
            $client_info['identity_list'][$key] = $identity_list[$key] ? 1 : 0;
        }

//        $m_member_verify_cert = M('member_verify_cert');
//        $member_verify_cert = $m_member_verify_cert->select(array('member_id' => $client_info['uid'], 'verify_state' => array('<=', certStateEnum::PASS)));
//        foreach ($member_verify_cert as $val) {
//            switch ($val['cert_type']) {
//                case certificationTypeEnum::ID:
//                    $client_info['identity_authentication'] = $val['verify_state'] == 10 ? 1 : 0;
//                    break;
//                case certificationTypeEnum::FAIMILYBOOK:
//                    $client_info['family_book'] = $val['verify_state'] == 10 ? 1 : 0;
//                    break;
//                case certificationTypeEnum::WORK_CERTIFICATION:
//                    $client_info['working_certificate'] = $val['verify_state'] == 10 ? 1 : 0;
//                    break;
//                case certificationTypeEnum::RESIDENT_BOOK:
//                    $client_info['resident_book'] = $val['verify_state'] == 10 ? 1 : 0;
//                    break;
//                default:
//            }
//        }
//
//        if (!isset($client_info['identity_authentication'])) {
//            $client_info['identity_authentication'] = 0;
//        }
//
//        if (!isset($client_info['family_book'])) {
//            $client_info['family_book'] = 0;
//        }
//
//        if (!isset($client_info['working_certificate'])) {
//            $client_info['working_certificate'] = 0;
//        }
//
//        if (!isset($client_info['resident_book'])) {
//            $client_info['resident_book'] = 0;
//        }

//        $r = new ormReader();
//        $sql = "SELECT cert_type,COUNT(uid) cert_num FROM member_verify_cert WHERE member_id = " . $client_info['uid'] . " AND verify_state = " . certStateEnum::PASS . " GROUP BY cert_type";
//        $member_assets = $r->getRows($sql);
//        foreach ($member_assets as $val) {
//            switch ($val['cert_type']) {
//                case certificationTypeEnum::CAR:
//                    $client_info['vehicle_property'] = $val['cert_num'];
//                    break;
//                case certificationTypeEnum::LAND:
//                    $client_info['land_property'] = $val['cert_num'];
//                    break;
//                case certificationTypeEnum::HOUSE:
//                    $client_info['housing_property'] = $val['cert_num'];;
//                    break;
//                case certificationTypeEnum::MOTORBIKE:
//                    $client_info['motorcycle_asset_certificate'] = $val['cert_num'];;
//                    break;
//                default:
//            }
//        }
//
//        if (!isset($client_info['vehicle_property'])) {
//            $client_info['vehicle_property'] = 0;
//        }
//
//        if (!isset($client_info['land_property'])) {
//            $client_info['land_property'] = 0;
//        }
//
//        if (!isset($client_info['housing_property'])) {
//            $client_info['housing_property'] = 0;
//        }
//
//        if (!isset($client_info['motorcycle_asset_certificate'])) {
//            $client_info['motorcycle_asset_certificate'] = 0;
//        }

        $sql = "SELECT COUNT(uid) guarantee_num FROM member_guarantee WHERE member_id = " . $client_info['uid'] . " AND relation_state = 100";
        $guarantee_num = $r->getOne($sql);
        $client_info['guarantee_num'] = intval($guarantee_num);
        $client_info['member_icon'] = getImageUrl($client_info['member_icon'], imageThumbVersion::AVATAR);

        if ($p['limit_key']) {
            $member_limit = memberClass::getMemberLimit($client_info['member_grade'], $p['limit_key']);
            $client_info['member_limit'] = $member_limit;
        }
        return new result(true, '', $client_info);
    }

}
