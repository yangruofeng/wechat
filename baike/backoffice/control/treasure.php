<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/7/8
 * Time: 16:12
 */
class treasureControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('financial');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "");
        Tpl::setDir("treasure");
        Language::read('financial');
    }

    public function branchListOp()
    {
        $ccy_list = (new currencyEnum())->Dictionary();
        $m_br = new site_branchModel();
        $br_list = $m_br->getBranchSettingList();

        foreach ($br_list as $k => $v) {
            $balance = branchClass::getBranchBalance($v['uid'], true);
            $total_to_usd = 0;
            foreach ($ccy_list as $ccy_k => $ccy_v) {
                $br_list[$k]['balance'][$ccy_k] = $balance['balance_' . $ccy_k];
                if ($ccy_k != currencyEnum::USD) {
                    $rate = global_settingClass::getCurrencyRateBetween($ccy_k, currencyEnum::USD);
                    $amount = round($balance['balance_' . $ccy_k] * $rate, 2);
                } else {
                    $amount = $balance['balance_' . $ccy_k];
                }
                $total_to_usd += $amount;
            }
            $br_list[$k]['balance']['Est.USD'] = $total_to_usd;
        }
        Tpl::output("branch_list", $br_list);
        Tpl::showPage("branch.list");
    }

    public function branchIndexOp()
    {
        $branch_id = $_GET['branch_id'];
        if (!$branch_id) {
            showMessage("Invalid Parameter:No Branch ID");
        }
        $m_branch = new site_branchModel();
        $branch_info = $m_branch->find(array("uid" => $branch_id));
        if (!$branch_info) {
            showMessage("Invalid Parameter:No Branch Found");
        }
        Tpl::output("branch_info", $branch_info);
        $balance = branchClass::getBranchBalance($branch_id, true);
        Tpl::output("balance", $balance);
        Tpl::output("civ_recent", $balance['recent']);
        $bank_list = branchClass::getBankList($branch_id, true, true);
        Tpl::output("bank_list", $bank_list);
        Tpl::showPage("branch.index");
    }

    /**
     * chief teller 存款到银行
     */
    public function bankDepositByBranchOp($p)
    {
        $bank_id = intval($p['bank_id']);
        $user_id = $this->user_id;
        $branch_id = intval($p['branch_id']);
        $amount = round($p['amount'], 2);
        $password = trim($p['password']);
        $currency = trim($p['currency']);
        $remark = trim($p['remark']);
        if (!$amount || !$password || !$currency || !$branch_id || !$bank_id) {
            return new result(false, "Invalid Parameter");
        }


        $branchToBank = new bizBranchToBankClass(bizSceneEnum::BACK_OFFICE);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $branchToBank->execute($branch_id, $bank_id, $user_id, $password, $amount, $currency, $remark);
        if ($rt->STS) {
            $conn->submitTransaction();
            return new result(true, "Deposit Successful");
        } else {
            $conn->rollback();
            return new result(false, 'Deposit Failure' . $rt->MSG);
        }

    }

    /**
     * chief teller 取款
     */
    public function bankWithdrawByBranchOp($p)
    {
        $bank_id = intval($p['bank_id']);
        $user_id = $this->user_id;
        $branch_id = $p['branch_id'];
        $amount = round($p['amount'], 2);
        $password = trim($p['password']);
        $currency = trim($p['currency']);
        $remark = trim($p['remark']);
        if (!$amount || !$password || !$currency || !$branch_id || !$bank_id) {
            return new result(false, "Invalid Parameter");
        }

        $branchToBank = new bizBankToBranchClass(bizSceneEnum::BACK_OFFICE);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $branchToBank->execute($branch_id, $bank_id, $user_id, $password, $amount, $currency, $remark);
        if ($rt->STS) {
            $conn->submitTransaction();
            return new result(true, "Withdraw Successful");
        } else {
            $conn->rollback();
            return new result(false, 'Withdraw Failure' . $rt->MSG);
        }
    }

    /**
     * 存款
     * @throws Exception
     */
    public function bankDepositByHQOp($p)
    {
        $bank_id = intval($p['bank_id']);
        $user_id = $this->user_id;
        $branch_id = $p['branch_id'];
        $amount = round($p['amount'], 2);
        $password = trim($p['password']);
        $currency = trim($p['currency']);
        $remark = trim($p['remark']);
        if (!$amount || !$password || !$bank_id) {
            return new result(false, "Invalid Parameter");
        }
        $m_bank = M('site_bank');
        $bank = $m_bank->getRow($bank_id);
        if (!$bank) {
            return new result(false, 'Invalid Bank Id!');
        }

        $class_biz = new bizHeadquarterToBankClass(bizSceneEnum::BACK_OFFICE);// bizFactoryClass::getInstance(bizSceneEnum::BACK_OFFICE, bizCodeEnum::HEADQUARTER_TO_BANK);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $class_biz->execute($bank_id, $user_id, $password, $amount, $bank['currency'], $remark);
        if ($rt->STS) {
            $conn->submitTransaction();
            return new result(true, "Deposit Successful");
        } else {
            $conn->rollback();
            return new result(false, 'Deposit Failure--' . $rt->MSG);
        }
    }

    /**
     * 取款
     * @throws Exception
     */
    public function bankWithdrawByHQOp($p)
    {
        $bank_id = intval($p['bank_id']);
        $user_id = $this->user_id;
        $branch_id = $p['branch_id'];
        $amount = round($p['amount'], 2);
        $password = trim($p['password']);
        $currency = trim($p['currency']);
        $remark = trim($p['remark']);
        if (!$amount || !$password || !$bank_id) {
            return new result(false, "Invalid Parameter");
        }
        $m_bank = M('site_bank');
        $bank = $m_bank->getRow($bank_id);
        if (!$bank) {
            return new result(false, 'Invalid Bank Id!');
        }

        $class_biz = new bizBankToHeadquarterClass(bizSceneEnum::BACK_OFFICE);// bizFactoryClass::getInstance(bizSceneEnum::BACK_OFFICE, bizCodeEnum::HEADQUARTER_TO_BANK);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $class_biz->execute($bank_id, $user_id, $password, $amount, $bank['currency'], $remark);
        if ($rt->STS) {
            $conn->submitTransaction();
            return new result(true, "Withdraw Successful");
        } else {
            $conn->rollback();
            return new result(false, 'Withdraw Failure--' . $rt->MSG);
        }
    }

    /**
     * 调息
     */
    public function bankAdjustOp($p)
    {
        $bank_id = intval($p['bank_id']);
        $user_id = $this->user_id;
        $trade_type = trim($p['trade_type']);
        $amount = round($p['amount'], 2);
        $password = trim($p['password']);
        $remark = trim($p['remark']);

        $bank_adjust = new bizBankAdjustClass(bizSceneEnum::BACK_OFFICE);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $bank_adjust->execute($user_id, $password, $bank_id, $trade_type, $amount, $remark);
        if ($rt->STS) {
            $conn->submitTransaction();
            return new result(true, "Adjust Successful");
        } else {
            $conn->rollback();
            return new result(false, 'Adjust Failure' . $rt->MSG);
        }
    }

    public function showBankTransactionOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $bank_id = $_GET['bank_id'];

        $m_bank = M('site_bank');
        $bank = $m_bank->find(array('uid' => $bank_id));
        Tpl::output("bank", $bank);
        Tpl::output('bank_id', $bank_id);
        Tpl::showPage('bank.transaction');
    }

    /**
     * branch==bank交易记录
     */
    public function getBankTransactionListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $bank_id = trim($p['bank_id']);
        Tpl::output('bank_id', $bank_id);
        $m_bank = M('site_bank');
        $bank = $m_bank->find(array('uid' => $bank_id));
        $bank_name = $bank['bank_name'];
        $currency = $bank['currency'];
        $bank_Obj = new objectSysBankClass($bank_id);
        $passbook = $bank_Obj->getPassbook();
        $filters = array();
        $filters['start_date'] = $p['date_start'];
        $filters['end_date'] = $p['date_end'];
        $m_passbook_account_flow = new passbook_account_flowModel();
        $data = $m_passbook_account_flow->searchFlowListByBookAndCurrency($passbook, $currency, $pageNumber, $pageSize, $filters);
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
            "bank_name" => $bank_name,
            "currency" => $currency
        );
    }

    public function bankTransactionItemDetailOp()
    {
        $bank_id = intval($_GET['bank_id']);
        $m_bank = M('site_bank');
        $bank = $m_bank->find(array('uid' => $bank_id));
        Tpl::output("bank", $bank);
        $bank_guid = $bank['obj_guid'];

        $uid = intval($_GET['uid']);
        $r = new ormReader();
        $m_passbook_trading = M('passbook_trading');
        $trading_info = $m_passbook_trading->find(array('uid' => $uid));
        if (!$trading_info) {
            showMessage('Invalid Id.');
        }

        $sql = "SELECT paf.credit,paf.debit,p.obj_type,p.obj_guid FROM passbook_account_flow paf"
            . " INNER JOIN passbook_account pa ON pa.uid = paf.account_id"
            . " INNER JOIN passbook p ON p.uid = pa.book_id"
            . " WHERE p.obj_guid !=" . $bank_guid . " AND paf.trade_id=" . $uid;
        $trading_flow = $r->getRows($sql);

        $m_client_member = M('client_member');
        $m_um_user = M('um_user');
        $m_site_bank = M('site_bank');
        $m_gl_account = M('gl_account');
        foreach ($trading_flow as $key => $flow) {
            switch ($flow['obj_type']) {
                case passbookObjTypeEnum::CLIENT_MEMBER:
                    $member_info = $m_client_member->find(array('obj_guid' => $flow['obj_guid']));
                    $partner_name = $member_info['display_name'] ?: $member_info['login_code'];
                    $partner_type = 'Member';
                    break;
                case passbookObjTypeEnum::UM_USER:
                    $user_info = $m_um_user->find(array('obj_guid' => $flow['obj_guid']));
                    $partner_name = $user_info['user_name'];
                    $partner_type = 'User';
                    break;
                case passbookObjTypeEnum::BANK:
                    $bank_info = $m_site_bank->find(array('obj_guid' => $flow['obj_guid']));
                    $partner_name = $bank_info['bank_name'];
                    $partner_type = 'Bank';
                    break;
                case passbookObjTypeEnum::BRANCH:
                    $partner_name = $this->branch_name;
                    $partner_type = 'Branch';
                    break;
                case passbookObjTypeEnum::GL_ACCOUNT;
                    $account_info = $m_gl_account->find(array('obj_guid' => $flow['obj_guid']));
                    $partner_name = $account_info['account_name'];
                    $partner_type = 'Gl_account';
                    break;
                default:
                    showMessage('Invalid passbook type.' . $flow['obj_type']);
            }
            $flow['partner_name'] = $partner_name;
            $flow['partner_type'] = $partner_type;
            $trading_flow[$key] = $flow;
        }
        Tpl::output('trading_info', $trading_info);
        Tpl::output('trading_flow', $trading_flow);
        Tpl::output('show_menu', 'bank');
        Tpl::showPage('bank.trading.detail');
    }

    public function settingCIVExtraTypeOp()
    {
        $m_common_civ_ext_type = M('common_civ_ext_type');
        $list = $m_common_civ_ext_type->select(array('uid' => array('neq', 0)));
        Tpl::output('list', $list);
        Tpl::showPage("setting.civ.extra.type");
    }

    public function addCIVExtraTypeOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $m_common_civ_ext_type = M('common_civ_ext_type');
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $rt = $m_common_civ_ext_type->addExtType($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('treasure', 'settingCIVExtraType', array('uid' => $p['branch_id']), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('treasure', 'addCIVExtraType', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            Tpl::showPage("add.civ.extra.type");
        }
    }

    public function editCIVExtraTypeOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_common_civ_ext_type = M('common_civ_ext_type');
        $uid = intval($p['uid']);
        if ($p['form_submit'] == 'ok') {
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $rt = $m_common_civ_ext_type->editExtType($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('treasure', 'settingCIVExtraType', array('uid' => $p['branch_id']), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('treasure', 'editCIVExtraType', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $info = $m_common_civ_ext_type->getRow($uid);
            Tpl::output('info', $info);
            Tpl::showPage("edit.civ.extra.type");
        }
    }

    public function delCIVExtraTypeOp($p)
    {
        $uid = intval($p['uid']);
        $m_common_civ_ext_type = M('common_civ_ext_type');
        $row = $m_common_civ_ext_type->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid id!');
        }
        $rt = $m_common_civ_ext_type->delete(array('uid' => $uid));

        if ($rt->STS) {
            return new result(true, 'Delete successful!');
        } else {
            return new result(false, 'Delete failed!');
        }
    }

    public function branchBalanceFlowPageOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("branch_id", $_GET['branch_id']);
        Tpl::output("branch_code", $_GET['branch_code']);
        Tpl::output("currency", $_GET['currency']);
        Tpl::output("condition", $condition);
        Tpl::showPage("branch.flow.index");

    }

    public function getBranchBalanceFlowListOp($p)
    {
        $branch_id = $p['branch_id'];
        $currency = $p['currency'];
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $page_num = $p['pageNumber'];
        $page_size = $p['pageSize'];
        $filters = array(
            'start_date' => $date_start,
            'end_date' => $date_end
        );
        $obj = new objectBranchClass($branch_id);
        $passbook = $obj->getPassbook();
        $m = new passbook_account_flowModel();
        $page_data = $m->searchFlowListByBookAndCurrency($passbook, $currency, $page_num, $page_size, $filters);

        return array(
            'sts' => true,
            'data' => $page_data->rows,
            'pageNumber' => $page_data->pageIndex,
            'pageSize' => $page_data->pageSize,
            'total' => $page_data->count,
            'pageTotal' => $page_data->pageCount,
            'pageType' => $p['type']
        );

    }

    public function dayVoucherOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $day = $params['day'] ?: date('Y-m-d');
        $condition = array(
            'currency' => $params['currency'] ?: currencyEnum::USD,
            'day' => $day
        );
        Tpl::output('branch_id', $_GET['branch_id']);
        Tpl::output('condition', $condition);
        Tpl::showpage('branch.daily.voucher');
    }

    public function getDayVoucherListOp($p)
    {
        return counter_codClass::getUserDailyVoucherData($p);
    }

    /**
     * 添加branch 限制条件
     */
    public function branchLimitOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        if ($p['form_submit'] == 'ok') {
            $m_site_branch_limit = new branchClass();
            $rt = $m_site_branch_limit->addBranchLimit($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('treasure', 'branchList', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage($rt->MSG);
            }
        } else {
            $biz_code_limit = array(
//                bizCodeEnum::MEMBER_WITHDRAW_TO_PARTNER,
//                bizCodeEnum::MEMBER_WITHDRAW_TO_BANK,
                bizCodeEnum::MEMBER_WITHDRAW_TO_CASH,
//                bizCodeEnum::MEMBER_TRANSFER_TO_MEMBER,
//                bizCodeEnum::MEMBER_TRANSFER_TO_BANK,
//                bizCodeEnum::MEMBER_DEPOSIT_BY_PARTNER,
//                bizCodeEnum::MEMBER_DEPOSIT_BY_BANK,
                bizCodeEnum::MEMBER_DEPOSIT_BY_CASH,
//                bizCodeEnum::TELLER_TO_BRANCH,
//                bizCodeEnum::BRANCH_TO_TELLER,
//                bizCodeEnum::BRANCH_TO_BANK,
//                bizCodeEnum::BANK_TO_BRANCH,
            );
            $biz_code_limit_new = array();
            foreach ($biz_code_limit as $code) {
                $biz_code_limit_new[$code] = ucwords(str_replace('_', ' ', $code));
            }
            Tpl::output('biz_limit_name', $biz_code_limit_new);
            $m_site_branch = M('site_branch');
            $branch = $m_site_branch->find(array('uid' => $uid));
            $m_site_branch_limit = M('site_branch_limit');
            $branch_limit = $m_site_branch_limit->select(array('branch_id' => $uid));
            $branch_limit = resetArrayKey($branch_limit, 'limit_key');
            Tpl::output('branch_limit', $branch_limit);
            Tpl::output('branch', $branch);


//            $profile = my_json_decode($branch['profile']);
//            $set_value = $profile['limit_chief_teller_approve'];
//            Tpl::output('setting_value', $set_value);

            $set_value = branchSettingClass::getCounterBizSetting($uid);
            Tpl::output('setting_value', $set_value);


            $set_list = enum_langClass::getCounterBizLang();
            Tpl::output('list', $set_list);

            Tpl::showPage('branch.limit');
        }
    }

    public function saveLimitApproveOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $data = array();

        $branch_id = intval($p['branch_id']);
        $biz_code = $p['biz_code'];
        $is_require_ct_approve = $p['is_require_ct_check'];
        $min_approve_amount = $p['min_check_amount'];
        foreach ($biz_code as $key => $v) {
            $current_code = $v;
            $is_require = intval($is_require_ct_approve[$current_code]) ? 1 : 0;
            $min_amount = intval($min_approve_amount[$current_code]);
            $data[$current_code] = array(
                'is_require_ct_approve' => $is_require,
                'min_approve_amount' => $min_amount
            );
        }

        $m_site_branch_limit = new branchClass();
        $rt = $m_site_branch_limit->editBranchLimitApprove($branch_id, $data);
        if ($rt->STS) {
            showMessage('Setting successful.', getUrl('treasure', 'branchList', array(), false, BACK_OFFICE_SITE_URL));
        } else {
            showMessage($rt->MSG);
        }

    }

    /**
     * 添加branch 限制条件
     */
    public function branchCreditOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        if ($p['form_submit'] == 'ok') {
            $p['operator_id'] = $this->user_id;
            $p['operator_name'] = $this->user_name;
            $m_site_branch = new branchClass();
            $rt = $m_site_branch->editBranchCredit($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('treasure', 'branchList', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage($rt->MSG);
            }
        } else {
            $m_site_branch = M('site_branch');
            $branch = $m_site_branch->find(array('uid' => $uid));
            Tpl::output('branch', $branch);
            Tpl::showPage('branch.credit');
        }
    }


}