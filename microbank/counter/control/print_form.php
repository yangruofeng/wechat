<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/25
 * Time: 9:18
 */
class print_formControl extends counter_baseControl
{

    public function __construct()
    {
        parent::__construct();
        $_GET['lang'] = 'kh';
        Language::read('certification,common_lang,define');
        Language::read('print_form');
        $this->outputSubMenu('print_form');
        Tpl::setLayout('print_layout');
        Tpl::setDir("print_form");
        Tpl::output('no_include_bottom', true);
    }

    /**
     * 存款
     */
    public function printDepositOp()
    {
        $biz_id = intval($_GET['biz_id']);
        $r = new ormReader();
        $sql = "SELECT bmd.*,cm.login_code,cm.display_name,cm.obj_guid,site_branch.branch_name FROM biz_member_deposit bmd "
            . " INNER JOIN client_member cm ON bmd.member_id = cm.uid "
            . " inner join site_branch on bmd.branch_id=site_branch.uid"
            . " WHERE bmd.uid =" . $biz_id;
        $deposit_info = $r->getRow($sql);
        Tpl::output('deposit_info', $deposit_info);
        Tpl::showPage("deposit.receipt");
    }

    /**
     * 取款
     */
    public function printWithdrawOp()
    {
        $biz_id = intval($_GET['biz_id']);
        $r = new ormReader();
        //$sql = "SELECT bmw.*,cm.login_code,cm.obj_guid FROM biz_member_withdraw bmw INNER JOIN client_member cm ON bmw.member_id = cm.uid WHERE bmw.uid =" . $biz_id;
        $sql = "SELECT bmd.*,cm.login_code,cm.display_name,cm.obj_guid,site_branch.branch_name FROM biz_member_withdraw bmd "
            . " INNER JOIN client_member cm ON bmd.member_id = cm.uid "
            . " inner join site_branch on bmd.branch_id=site_branch.uid"
            . " WHERE bmd.uid =" . $biz_id;
        $withdraw_info = $r->getRow($sql);
        Tpl::output('withdraw_info', $withdraw_info);
        Tpl::showPage("withdraw.receipt");
    }

    /**
     * 合同计划
     */
    public function printInstallmentSchemeOp()
    {
        $contract_id = intval($_GET['contract_id']);
        $lang = trim($_GET['lang']);
        $cashier_name = $this->user_name;
        $cashier = $this->user_info;
        Tpl::output('cashier', $cashier);
        Tpl::output('cashier_name', $cashier_name);
        $rt = loan_contractClass::getLoanContractDetailInfo($contract_id);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }

        $contract_info = $rt->DATA;
        $begin_principal = $contract_info['loan_amount'];
        $first_pay_date = '';
        $final_pay_date = '';
        $days_of_use_total = 0;
        $payment_total = 0;
        $i = 0;
        foreach ($contract_info['loan_installment_scheme'] as $k => $v) {
            ++$i;
            if ($i == 1) $first_pay_date = $v['receivable_date'];
            $final_pay_date = $v['receivable_date'];

            $start_time = strtotime($v['interest_date']);
            $end_time = strtotime($v['receivable_date']);
            $days_of_use = round(($end_time - $start_time) / (3600 * 24));
            $v['days_of_use'] = $days_of_use;
            $days_of_use_total += $days_of_use;
            $payment_total += $v['amount'];

            $v['begin_principal'] = $begin_principal;
            $principal_owed = $begin_principal - $v['receivable_principal'];
            $v['principal_owed'] = $principal_owed < 0 ? 0 : $principal_owed;
            $begin_principal = $principal_owed;
            $contract_info['loan_installment_scheme'][$k] = $v;
        }

        $contract_info['disburse_date'] = reset($contract_info['loan_disbursement_scheme'])['disbursable_date'];
        $contract_info['first_pay_date'] = $first_pay_date;
        $contract_info['final_pay_date'] = $final_pay_date;
        $contract_info['days_of_use_total'] = $days_of_use_total;
        $contract_info['payment_total'] = $payment_total;

        // 罚金换算成年利率
        $contract_info['penalty_rate_yearly'] = round($contract_info['contract_info']['penalty_rate'] / $contract_info['contract_info']['penalty_divisor_days'] * 365, 2);

        $r = new ormReader();
        $sql = "SELECT COUNT(uid) loan_times FROM loan_contract WHERE account_id = " . intval($contract_info['contract_info']['account_id']) . " AND state >=" . qstr(loanContractStateEnum::PENDING_DISBURSE);
        $loan_times = $r->getRow($sql);
        $contract_info['loan_times'] = intval($loan_times['loan_times']);
        if ($_GET['_show_scheme']) {
            $contract_info['loan_times'] += 1;
            $contract_info['contract_sn'] = $contract_info['virtual_contract_sn'];
        }
        Tpl::output('contract_info', $contract_info);


        $co_list =(new member_follow_officerModel())->getCoByMemberId($contract_info['member_info']['uid']);

        Tpl::output('client_info', $contract_info['member_info']);

        $member_full_address = $this->getMemberFullAddress($contract_info['member_info']['obj_guid'], 'kh');
        Tpl::output("member_full_address", $member_full_address);

        Tpl::output('co', reset($co_list));
        Tpl::output('no_include_bottom', true);
        if ($lang == 'kh') {
            Tpl::showPage("loan.installment.scheme.kh");
        } else {
            Tpl::showPage("loan.installment.scheme");
        }
    }

    /**
     * 抵押物
     */
    public function printCollateralOp()
    {
        // todo 测试
        //$_GET['lang']='en';
        //Language::read('common_lang,define');
        //Language::read('print_form');
        $contract_id = intval($_GET['contract_id']);
        $lang = trim($_GET['lang']);
        $r = new ormReader();
        $sql_1 = "SELECT mac.contract_no,cm.*,sb.branch_name FROM member_authorized_contract mac LEFT JOIN client_member cm ON mac.member_id = cm.uid"
            . " LEFT JOIN site_branch sb ON mac.branch_id=sb.uid WHERE mac.uid=" . $contract_id;
        $basic_info = $r->getRow($sql_1);
        if (!$basic_info) {
            showMessage('No contract info');
        }

        // 性别
        if ($basic_info['gender'] == memberGenderEnum::FEMALE) {
            $basic_info['gender'] = L('enum_gender_female');
        } else {
            $basic_info['gender'] = L('enum_gender_male');
        }


        $member_address = (new common_addressModel())->getMemberResidencePlaceByGuid($basic_info['obj_guid']);
        $id1 = $member_address['id1'];
        $id2 = $member_address['id2'];
        $id3 = $member_address['id3'];
        $id4 = $member_address['id4'];

        if ($id1) {
            $basic_info['id_1'] = $this->getTreeTextById($id1, 'kh');
        }
        if ($id2) {
            $basic_info['id_2'] = $this->getTreeTextById($id2, 'kh');
        }
        if ($id3) {
            $basic_info['id_3'] = $this->getTreeTextById($id3, 'kh');
        }
        if ($id4) {
            $basic_info['id_4'] = $this->getTreeTextById($id4, 'kh');
        }

        $basic_info['street'] = $member_address['street'];
        $basic_info['house_number'] = $member_address['house_number'];
        $basic_info['address_detail'] = $member_address['address_detail'];
        $basic_info['address_group'] = $member_address['address_group'];
        Tpl::output('basic_info', $basic_info);
        Tpl::output('member_address', $member_address);

        $asset_owner_list = array();
        $other_owner_list = array();  // 其他联系人，除自己外的
        if ($_GET['mortgage_id']) {
            $mortgage_id = explode('_', $_GET['mortgage_id']);
            $mortgage_id_str = '(' . implode(',', $mortgage_id) . ')';
            $sql_2 = "SELECT ma.*,mvc.cert_issue_time FROM member_asset_mortgage mam INNER JOIN member_assets ma ON mam.member_asset_id=ma.uid"
                . " INNER JOIN member_verify_cert mvc ON mvc.uid=ma.cert_id WHERE mam.uid IN " . $mortgage_id_str;
            $assets_info = $r->getRows($sql_2);
            Tpl::output('assets_info', $assets_info);

            $assets_info = reset($assets_info);
            $member_asset_id = $assets_info['uid'];
            $sql = "SELECT mao.relative_id,mao.relative_name,mcrr.* FROM member_assets_owner mao LEFT JOIN member_credit_request_relative mcrr ON mcrr.uid = mao.relative_id
            WHERE mao.member_asset_id = " . $member_asset_id;
            $asset_owner_list = $r->getRows($sql);

            $m_define = new core_definitionModel();

            foreach ($asset_owner_list as $key => $owner) {
                // 获取语言的关系名称
                $name = $m_define->getItemNameByLang(userDefineEnum::GUARANTEE_RELATIONSHIP, $owner['relation_name_code'], $lang);
                if ($name) {
                    $owner['relation_name'] = $name;
                    $asset_owner_list[$key] = $owner;
                }
                if ($owner['relative_id'] == 0) {
                    $owner['id_sn'] = $basic_info['id_sn'];
                    $asset_owner_list[$key] = $owner;
                } else {
                    $other_owner_list[] = $owner;
                }
            }

        }

        Tpl::output('owner_list', $asset_owner_list);
        Tpl::output('other_owner_list', $other_owner_list);

        Tpl::output('no_include_bottom', true);


        if ($lang == 'kh') {
            Tpl::showPage("collateral.gurantee.receipt.kh");
        } else {
            Tpl::showPage("collateral.gurantee.receipt");
        }
    }

    /**
     * 还款
     * @throws Exception
     */
    public function printRepaymentOp()
    {
        $biz_id = intval($_GET['biz_id']);
        $lang = trim($_GET['lang']);

        $biz_info = (new biz_member_loan_repaymentModel())->getRepaymentDetail($biz_id);
        if ($biz_info) {
            $client_info = memberClass::getMemberBaseInfo($biz_info['member_id']);
            Tpl::output('biz_info', $biz_info);
            Tpl::output('client_info', $client_info);
        } else {
            showMessage('Invalid biz id.');
        }


        $exchange_rate = global_settingClass::getCurrencyRateBetween(currencyEnum::USD, currencyEnum::KHR);
        Tpl::output('exchange_rate', $exchange_rate);
        Tpl::output('no_include_bottom', true);
        if ($lang == 'kh') {
            Tpl::showPage("repayment.receipt.kh");
        } else {
            Tpl::showPage("repayment.receipt");
        }

    }

    /**
     * 会员流水
     */
    public function printMemberFlowOp()
    {
        $member_id = intval($_GET['member_id']);
        $lang = trim($_GET['lang']);
        Tpl::output('member_id', $member_id);
        $member_info = memberClass::getMemberBaseInfo($member_id);
        Tpl::output('member_info', $member_info);

        $currency = trim($_GET['currency']);
        Tpl::output('currency', $currency);

        $date_start = trim($_GET['date_start']);
        $date_end = trim($_GET['date_end']);

        Tpl::output("date_start", $date_start);
        Tpl::output("date_end", $date_end);
        $param = array(
            'member_id' => $member_id,
            'currency' => $currency,
            'start_date' => $date_start,
            'end_date' => $date_end,
        );
        $member_flow = member_savingsClass::getMemberBillTransaction($param);
        if (!$member_flow->STS) {
            showMessage('Invalid Param');
        }

        $data = $member_flow->DATA;
        Tpl::output('data', $data['data']['list']);
        Tpl::output('no_include_bottom', true);
        if ($lang == 'kh') {
            Tpl::showPage('member.balance.flow.kh');
        } else {
            Tpl::showPage('member.balance.flow');
        }

    }

    /**
     * 抵押合同
     */
    public function printCreditAgreementOp()
    {
        //$_GET['lang'] = 'en';
        //Language::read('common_lang,define');
        //Language::read('print_form');
        $lan = $_GET['lang'];
        $branch_id = $this->branch_id;
        $m_branch = M('site_branch');
        $branch_info = $m_branch->find(array('uid' => $branch_id));
        $contract_id = intval($_GET['contract_id']);
        if (!$contract_id) {
            showMessage("Invalid Contract ID");
        }

        $r = new ormReader();

        //获取contract的数据
        $m_contract = new member_authorized_contractModel();
        $contract_info = $m_contract->find(array("uid" => $contract_id));
        if (!$contract_info) {
            showMessage("Invalid Contract ID");
        }
        //获取客人信息
        $client_info = memberClass::getMemberBaseInfo($contract_info['member_id']);
        //获取授信信息
        $m_grant = new member_credit_grantModel();
        $grant_info = $m_grant->find(array("uid" => $contract_info['grant_credit_id']));
        //获取资产信息

        // 获取授信信息下的资产列表
        $sql = "select a.*,vc.cert_issue_time from member_credit_grant_assets g left join member_assets a
        on a.uid=g.member_asset_id left join member_verify_cert vc on vc.uid=a.cert_id
        where g.grant_id=" . qstr($grant_info['uid']);
        $asset_list = $r->getRows($sql);
        //print_r($asset_list);die;
        Tpl::output('grant_asset_list', $asset_list);


        //获取申请信息
        $client_request_id = $grant_info['credit_request_id'];
        $member_credit_request = (new member_credit_requestModel())->find(array(
            'uid' => $client_request_id
        ));
        $m_request = new member_credit_request_relativeModel();
        $relative_list = $m_request->select(array("request_id" => $client_request_id));

        Tpl::output('member_credit_request', $member_credit_request);
        //获取抵押信息
        //$m_mortgage = new member_asset_mortgageModel();
        //$mortgage_list = $m_mortgage->select(array("contract_no" => $contract_info['contract_sn'], "contract_type" => 0));

        $product_info = memberClass::getMemberCreditLoanProduct($contract_info['member_id']);


        $sql = "SELECT * FROM member_credit WHERE member_id=" . $contract_info['member_id'];
        $sql .= " ORDER BY uid desc";
        $credit_info = $r->getRow($sql);

        $branch_address = $this->getFullAddressById($branch_info['address_id'], 'kh');
        $branch_address = $branch_address . $branch_info['address_detail'];
        Tpl::output("branch_address", $branch_address);

        // member 的地址信息
        $member_address = (new common_addressModel())->getMemberResidencePlaceByGuid($client_info['obj_guid']);
        if ($member_address) {
            $member_address['id1'] = $this->getTreeTextById($member_address['id1'], $lan);
            $member_address['id2'] = $this->getTreeTextById($member_address['id2'], $lan);
            $member_address['id3'] = $this->getTreeTextById($member_address['id3'], $lan);
            $member_address['id4'] = $this->getTreeTextById($member_address['id4'], $lan);
        }

        Tpl::output('member_address', $member_address);

        $member_full_address = $this->getMemberFullAddress($client_info['obj_guid'], 'kh');
        Tpl::output("member_full_address", $member_full_address);

        Tpl::output("branch_info", $branch_info);
        Tpl::output("client_info", $client_info);
        Tpl::output("contract_info", $contract_info);
        Tpl::output("grant_info", $grant_info);
        //Tpl::output("mortgage_list", $mortgage_list);
        Tpl::output("product_info", $product_info);
        Tpl::output("credit_info", $credit_info);
        Tpl::output("relative_list", $relative_list);
        Tpl::showPage("agreement.credit");

    }

    protected function getMemberFullAddress($obj_guid, $lang = 'en')
    {
        $member_address = (new common_addressModel())->getMemberResidencePlaceByGuid($obj_guid);
        $id_arr = array(
            $member_address['id4'],
            $member_address['id3'],
            $member_address['id2'],
            $member_address['id1'],
        );
        $full_address_arr = array();
        if ($member_address['address_detail']) {
            $full_address_arr[] = $member_address['address_detail'];
        }

        foreach ($id_arr as $id) {
            if (!$id) continue;
            $full_address = $this->getTreeTextById($id, $lang);
            if ($full_address) {
                $full_address_arr[] = $full_address;
            }
        }
        return implode(', ', $full_address_arr);

    }

    protected function getTreeTextById($uid, $lang = 'en')
    {
        $m_core_tree = M('core_tree');
        $node_info = $m_core_tree->getRow($uid);
        if ($node_info) {
            $node_text_alias = my_json_decode($node_info['node_text_alias']);
            return $node_text_alias[$lang];
        } else {
            return '';
        }

    }

    protected function getFullAddressById($uid, $lang = 'en', $full_address = '')
    {
        $m_core_tree = M('core_tree');
        $node_info = $m_core_tree->getRow($uid);
        if (!$node_info) {
            return $full_address;
        } elseif ($node_info['node_level'] == 1) {
            $node_text_alias = my_json_decode($node_info['node_text_alias']);
            return $node_text_alias[$lang] . ', ' . $full_address;
        } else {
            $node_text_alias = my_json_decode($node_info['node_text_alias']);
            $full_address = $node_text_alias[$lang] . ', ' . $full_address;
            return $this->getFullAddressById($node_info['pid'], $lang, $full_address);
        }
    }

    /**
     * COD flow
     */
    public function printCODFlowOp()
    {
        $user_id = $this->user_id;
        $user_name = $this->user_name;
        Tpl::output("user_name", $user_name);
        $currency = trim($_GET['currency']);
        $lang = trim($_GET['lang']);
        $userObj = new objectUserClass($user_id);
        $passbook = $userObj->getUserPassbook();

        $filters = array();
        $filters['start_date'] = trim($_GET['date_start']);
        $filters['end_date'] = trim($_GET['date_end']);
        $pageNumber = 1;
        $pageSize = 100000;
        Tpl::output("user_name", $userObj->user_name);
        Tpl::output("currency", $currency);
        Tpl::output("date_start", trim($_GET['date_start']));
        Tpl::output("date_end", trim($_GET['date_end']));
        $m_passbook_account_flow = new passbook_account_flowModel();
        $data = $m_passbook_account_flow->searchFlowListByBookAndCurrency($passbook, $currency, $pageNumber, $pageSize, $filters);
        $rows = $data->rows;
        Tpl::output('data', $rows);
        Tpl::showPage('COD.balance.flow');
    }

    /**
     * COD flow
     */
    public function printCIVFlowOp()
    {
        $branch_id = $this->branch_id;
        $branch_Obj = new objectBranchClass($branch_id);
        $passbook = $branch_Obj->getPassbook();
        $filters = array();
        $filters['start_date'] = $_GET['date_start'];
        $filters['end_date'] = $_GET['date_end'];
        $currency = trim($_GET['currency']);
        $pageNumber = 1;
        $pageSize = 100000;

        $m_passbook_account_flow = new passbook_account_flowModel();
        $data = $m_passbook_account_flow->searchFlowListByBookAndCurrency($passbook, $currency, $pageNumber, $pageSize, $filters);
        $rows = $data->rows;
        Tpl::output('data', $rows);
        Tpl::output("branch_name", $branch_Obj->branch_name);
        Tpl::output("currency", $currency);
        Tpl::output("date_start", trim($_GET['date_start']));
        Tpl::output("date_end", trim($_GET['date_end']));
        Tpl::showPage('CIV.balance.flow');
    }


    public function printTellerDailyReportOp()
    {
        $param = array_merge(array(), $_GET, $_POST);

        $user_id = $param['user_id'];

        $userObj = new objectUserClass($user_id);
        $branch_id = $userObj->branch_id;

        $param['pageNumber'] = 1;
        $param['pageSize'] = 1000000;

        $data = counter_codClass::getUserDailyVoucherData($param);

        $m_user = new um_userModel();
        // ct的信息
        $chief_teller_info = $m_user->getBranchChiefTellerInfo($branch_id);
        // branch manager info
        $branch_manager_info = $m_user->getBranchManagerInfo($branch_id);
        Tpl::output('chief_teller', $chief_teller_info);
        Tpl::output('branch_manager', $branch_manager_info);


        Tpl::output('user_id', $user_id);
        Tpl::output('param', $param);
        Tpl::output('data', $data);
        Tpl::showpage('teller.daily.report');
    }

    public function printAssetMortgageOp()
    {
        $uid = intval($_GET['uid']);
        $m_member_asset_mortgage = new member_asset_mortgageModel();
        $info = $m_member_asset_mortgage->getAssetMortgage($uid);
        Tpl::output('info', $info);

        $wap_url = getUrl('wap_asset', 'showAssetDetail', array('uid' => $info['uid']), false, WAP_SITE_URL);
        Tpl::output('wap_url', $wap_url);


        Tpl::showpage('asset.mortgage');
    }

    /**
     * 投票二维码url
     */
    public function getQrCodeOp()
    {
        $url = $_GET['url'];
        qrcodeClass::generateQrCodeImage($url);
    }

    public function printCTDailyReportOp(){
        $branch_name = $this->branch_name;
        Tpl::output('branch_name',$branch_name);
        Tpl::output('user_code', $this->user_info['user_code']);
        Tpl::output('user_name',$this->user_info['user_name']);
        $user_id = $this->user_id;
        $userObj = new objectUserClass($user_id);
        $branch_id = $userObj->branch_id;
        $m_user = new um_userModel();
        $branch_manager_info = $m_user->getBranchManagerInfo($branch_id);
        Tpl::output('branch_manager', $branch_manager_info);
        $end_date = $_GET['year'] . '-' . str_pad($_GET['month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($_GET['day'], 2, '0', STR_PAD_LEFT);
        Tpl::output('end_date',$end_date);
        $end_date = date('Y-m-d 23:59:59', strtotime($end_date));
        $ret = array();

        $br_book = passbookClass::getBranchPassbook($this->branch_id);
        $br_book_info = $br_book->getPassbookInfo();
        $ret[] = array(
            'uid' => $br_book_info['uid'],
            'book_code' => $br_book_info['book_code'],
            'book_name' => $br_book_info['book_name'],
            'balance' => $br_book->getAccountBalanceOfEndDay($end_date),
            'remark' => 'Cash In Vault'
        );

        //获取teller的acct
        $r = new ormReader();
        $sql = "SELECT uu.uid,uu.user_name FROM um_user uu"
            . " LEFT JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " LEFT JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE uu.user_position = " . qstr(userPositionEnum::TELLER) . " AND sb.uid = " . $this->branch_id;
        $cashier_list = $r->getRows($sql);
        $teller_acct = array();
        foreach ($cashier_list as $item) {
            $teller_book = passbookClass::getUserPassbook($item['uid']);
            $teller_book_info = $teller_book->getPassbookInfo();
            $ret[] = array(
                'uid' => $teller_book_info['uid'],
                'book_code' => $teller_book_info['book_code'],
                'book_name' => $teller_book_info['book_name'],
                'balance' => $teller_book->getAccountBalanceOfEndDay($end_date),
                'remark' => 'TELLER : ' . $item['user_name']
            );
        }

        //获取bank的acct
        $mb = new site_bankModel();
        $bank_acct = $mb->select(array("branch_id" => $this->branch_id));
        foreach ($bank_acct as $item) {
            $bank_book = passbookClass::getBankAccountPassbook($item['uid']);
            $bank_book_info = $bank_book->getPassbookInfo();
            $ret[] = array(
                'uid' => $bank_book_info['uid'],
                'book_code' => $bank_book_info['book_code'],
                'book_name' => $bank_book_info['book_name'],
                'balance' => $bank_book->getAccountBalanceOfEndDay($end_date),
                'remark' => 'BANK : ' . $item['bank_account_name']
            );
        }
        Tpl::output('data',$ret);
        Tpl::showPage('ct.daily.report');
    }
    /**
     * 预览合同计划
     */
    public function printPreviewInstallmentSchemeByGrantProductUidOp()
    {
        $cgp_id=intval($_GET['grant_product_uid']);
        $currency = $_GET['currency'];
        $rt = creditFlowClass::getTempContractByCreditGrantProductID($cgp_id,$currency);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }

        $contract_info = $rt->DATA;
        $begin_principal = $contract_info['loan_amount'];
        $first_pay_date = '';
        $final_pay_date = '';
        $days_of_use_total = 0;
        $payment_total = 0;
        $i = 0;
        foreach ($contract_info['loan_installment_scheme'] as $k => $v) {
            ++$i;
            if ($i == 1) $first_pay_date = $v['receivable_date'];
            $final_pay_date = $v['receivable_date'];

            $start_time = strtotime($v['interest_date']);
            $end_time = strtotime($v['receivable_date']);
            $days_of_use = round(($end_time - $start_time) / (3600 * 24));
            $v['days_of_use'] = $days_of_use;
            $days_of_use_total += $days_of_use;
            $payment_total += $v['amount'];

            $v['begin_principal'] = $begin_principal;
            $principal_owed = $begin_principal - $v['receivable_principal'];
            $v['principal_owed'] = $principal_owed < 0 ? 0 : $principal_owed;
            $begin_principal = $principal_owed;
            $contract_info['loan_installment_scheme'][$k] = $v;
        }

        $contract_info['disburse_date'] = reset($contract_info['loan_disbursement_scheme'])['disbursable_date'];
        $contract_info['first_pay_date'] = $first_pay_date;
        $contract_info['final_pay_date'] = $final_pay_date;
        $contract_info['days_of_use_total'] = $days_of_use_total;
        $contract_info['payment_total'] = $payment_total;

        // 罚金换算成年利率
        $contract_info['penalty_rate_yearly'] = round($contract_info['contract_info']['penalty_rate'] / $contract_info['contract_info']['penalty_divisor_days'] * 365, 2);

        $r = new ormReader();
        $sql = "SELECT COUNT(uid) loan_times FROM loan_contract WHERE account_id = " . intval($contract_info['contract_info']['account_id']) . " AND state >=" . qstr(loanContractStateEnum::PENDING_DISBURSE);
        $loan_times = $r->getRow($sql);
        $contract_info['loan_times'] = intval($loan_times['loan_times'])+1;
        if ($_GET['_show_scheme']) {
            $contract_info['loan_times'] += 1;
            $contract_info['contract_sn'] = $contract_info['virtual_contract_sn'];
        }
        Tpl::output('contract_info', $contract_info);

        $co_list = M('member_follow_officer')->getCoByMemberId($contract_info['member_info']['uid']);

        Tpl::output('client_info', $contract_info['member_info']);

        $member_full_address = $this->getMemberFullAddress($contract_info['member_info']['obj_guid'], 'kh');
        Tpl::output("member_full_address", $member_full_address);

        Tpl::output('co', reset($co_list));
        Tpl::output('no_include_bottom', true);

        $cashier_name = $this->user_name;
        $cashier = $this->user_info;
        Tpl::output('cashier', $cashier);
        Tpl::output('cashier_name', $cashier_name);

        $lang = Language::currentCode();
        if ($lang == 'kh') {
            Tpl::showPage("loan.installment.scheme.kh");
        } else {
            Tpl::showPage("loan.installment.scheme");
        }
    }



}
