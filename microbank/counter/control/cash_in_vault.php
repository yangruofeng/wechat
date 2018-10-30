<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/12
 * Time: 13:58
 */
class cash_in_vaultControl extends counter_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('cash');
        $this->outputSubMenu('cash_in_vault');
        Tpl::setLayout('home_layout');
        Tpl::setDir("cash_in_vault");
    }


    public function bankOp()
    {
        $branch_id = $this->user_info['branch_id'];
        $m_branch = M('site_branch');
        $branch = $m_branch->find(array('uid' => $branch_id));
        $branch_guid = $branch['obj_guid'];
        $m_bank = M('site_bank');
        $bank = $m_bank->select(array('branch_id' => $branch_id));
        foreach ($bank as $key => $value) {
            $uid = $value['uid'];
            $bank_guid = $value['obj_guid'];
            $r = new ormReader();
            $sql = "SELECT * FROM biz_obj_transfer WHERE (sender_obj_guid =" . $branch_guid . " and receiver_obj_guid=" . $bank_guid . ") or (sender_obj_guid=" . $bank_guid . " and receiver_obj_guid=" . $branch_guid . ")";
            $sql .= " ORDER BY update_time DESC";
            $transaction = $r->getRow($sql);
            $bankPassbook = new objectSysBankClass($uid);
            $balance = $bankPassbook->getPassbookCurrencyBalance();
            $bank[$key]['balance'] = $balance;
            $bank[$key]['transaction'] = $transaction;
        }
        Tpl::output('bank', $bank);
        Tpl::showPage("bank");
    }


    /**
     * chief teller 存款
     */
    public function bankDepositOp($p)
    {
        $bank_id = intval($p['bank_id']);
        $user_id = $this->user_id;
        $branch_id = $this->user_info['branch_id'];
        $amount = round($p['amount'], 2);
        $password = trim($p['password']);
        $currency = trim($p['currency']);
        $remark = trim($p['remark']);

        $branchToBank = new bizBranchToBankClass(bizSceneEnum::COUNTER);
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
    public function bankWithdrawOp($p)
    {
        $bank_id = intval($p['bank_id']);
        $user_id = $this->user_id;
        $branch_id = $this->user_info['branch_id'];
        $amount = round($p['amount'], 2);
        $password = trim($p['password']);
        $currency = trim($p['currency']);
        $remark = trim($p['remark']);

        $branchToBank = new bizBankToBranchClass(bizSceneEnum::COUNTER);
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

        $bank_adjust = new bizBankAdjustClass(bizSceneEnum::COUNTER);
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

    public function pendingReceiveOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage("pending.receive");
    }


    /**
     * 待收款处理
     */
    public function getReceiveListOp($p)
    {
        $branch_id = $this->user_info['branch_id'];
        $m_branch = M('site_branch');
        $branch = $m_branch->find(array('uid' => $branch_id));
        $branch_guid = $branch['obj_guid'];
        $state = trim($p['state']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);

        $r = new ormReader();
        $sql = "SELECT * FROM biz_obj_transfer
            WHERE (create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "') AND biz_code = '" . bizCodeEnum::TELLER_TO_BRANCH . "' AND receiver_obj_guid = " . $branch_guid;
        if ($state == 'pending') {
            $sql .= " AND is_outstanding = 1 AND state = 0";
            $sql .= " ORDER BY create_time DESC";
        }
        if ($state == 'received') {
            $sql .= " AND is_outstanding = 0 AND state = 100";
            $sql .= " ORDER BY update_time DESC";
        }
        if ($state == 'rejected') {
            $sql .= " AND is_outstanding = 0 AND state = 10";
            $sql .= " ORDER BY update_time DESC";
        }

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
            "state" => $state
        );
    }

    public function transferToCashierOp()
    {
        $branch_id = $this->user_info['branch_id'];
        $r = new ormReader();
        $sql = "SELECT uu.uid,uu.user_name FROM um_user uu"
            . " LEFT JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " LEFT JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE uu.user_position = 'teller' AND sb.uid = " . $branch_id;
        $cashier = $r->getRows($sql);
        Tpl::output('cashier', $cashier);

        Tpl::showPage("transfer.cashier");
    }


    /**
     * 接受转入
     */
    public function receiveTransferOp($p)
    {
        $biz_id = intval($p['biz_id']);
        $tellerToBranch = new bizTellerToBranchClass(bizSceneEnum::COUNTER);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $tellerToBranch->confirm($biz_id);
        if ($rt->STS) {
            $conn->submitTransaction();
            return new result(true, "Receive Successful");
        } else {
            $conn->rollback();
            return new result(false, 'Receive Failure' . $rt->MSG);
        }
    }

    /**
     * 拒绝转入
     */
    public function rejectTransferOp($p)
    {
        $biz_id = intval($p['biz_id']);
        $tellerToBranch = new bizTellerToBranchClass(bizSceneEnum::COUNTER);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $tellerToBranch->cancel($biz_id);
        if ($rt->STS) {
            $conn->submitTransaction();
            return new result(true, "Reject Successful");
        } else {
            $conn->rollback();
            return new result(false, 'Reject Failure' . $rt->MSG);
        }
    }

    /**
     * Ct to cashier
     */
    public function transferToTellerOp($p)
    {
        $user_id = $this->user_id;
        $branch_id = $this->user_info['branch_id'];
        $cashier_id = intval($p['cashier_id']);
        $currency = trim($p['currency']);
        $amount = trim($p['amount']);
        $password = trim($p['password']);
        $remark = trim($p['remark']);
        $branchToTeller = new bizBranchToTellerClass(bizSceneEnum::COUNTER);

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $branchToTeller->execute($branch_id, $user_id, $password, $cashier_id, $amount, $currency, $remark);
        if ($rt->STS) {
            $conn->submitTransaction();
            return new result(true, "Transfer Successful");
        } else {
            $conn->rollback();
            return new result(false, 'Transfer Failure' . $rt->MSG);
        }

    }

    public function showTransactionOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::output('show_menu', 'bank');
        $bank_id = $_GET['bank_id'];
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

    public function bankTradeDetailOp()
    {
        $bank_id = intval($_GET['bank_id']);
        $m_bank = M('site_bank');
        $bank = $m_bank->find(array('uid' => $bank_id));
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


    /**
     * branch transfer to cashier 列表
     */
    public function getTransferCashierListOp($p)
    {
        $obj_guid = $this->user_info['obj_guid'];
        $state = trim($p['state']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $r = new ormReader();
        $sql = "SELECT * FROM biz_obj_transfer
            WHERE biz_code = '" . bizCodeEnum::BRANCH_TO_TELLER . "' AND sender_handler_obj_guid = " . $obj_guid;
        if ($state == 'pending') {
            $sql .= " AND is_outstanding = 1 AND state = 0";
            $sql .= " ORDER BY create_time DESC";
        }
        if ($state == 'received') {
            $sql .= " AND is_outstanding = 0 AND state = 100";
            $sql .= " ORDER BY update_time DESC";
        }
        if ($state == 'rejected') {
            $sql .= " AND is_outstanding = 0 AND state = 10";
            $sql .= " ORDER BY update_time DESC";
        }

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

    public function getBranchBalanceOp()
    {
        $branch_id = $this->user_info['branch_id'];
        $m_branch = M('site_branch');
        $branch_info = $m_branch->find(array('uid' => $branch_id));
        $branch_name = $branch_info['branch_name'];
        $branch = new objectBranchClass($branch_id);
        $rt1 = $branch->getPassbookCurrencyBalance();
        $rt2 = $branch->getPassbookCurrencyAccountDetail();
        $arr = array_merge(array(), $rt1, $rt2);

        $currency_list = (new currencyEnum())->Dictionary();
        $data = array();
        foreach ($currency_list as $key => $currency) {
            $data['cash_' . $key] = ncPriceFormat(passbookAccountClass::getBalance($arr[$key]['balance'], $arr[$key]['outstanding']));
            $data['out_' . $key] = ncPriceFormat(passbookAccountClass::getOutstanding($arr[$key]['balance'], $arr[$key]['outstanding']));
        }
        $data['branch_name'] = $branch_name;
        return new result(true, '', $data);

    }

    public function cashierOp()
    {
        $branch_id = $this->user_info['branch_id'];
        $r = new ormReader();
        $sql = "SELECT uu.uid,uu.user_name FROM um_user uu"
            . " LEFT JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " LEFT JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE uu.user_position = " . qstr(userPositionEnum::TELLER) . " AND sb.uid = " . $branch_id;
        $cashier_list = $r->getRows($sql);
        $currency_list = (new currencyEnum())->Dictionary();
        foreach ($cashier_list as $key => $cashier) {
            $rt1 = userClass::getPassbookBalanceOfUser($cashier['uid']);
            $rt2 = userClass::getPassbookAccountAllCurrencyDetailOfUser($cashier['uid']);
            $arr = array_merge(array(), $rt1, $rt2);
            foreach ($currency_list as $k => $currency) {
                $cashier['balance'][$k] = ncPriceFormat(passbookAccountClass::getBalance($arr[$k]['balance'], $arr[$k]['outstanding']));
            }
            $cashier_list[$key] = $cashier;
        }
        Tpl::output('cashier_list', $cashier_list);
        Tpl::showPage('cashier');
    }

    public function cashierTransactionOp()
    {
        $cashier_id = intval($_GET['cashier_id']);
        Tpl::output('cashier_id', $cashier_id);
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::output('show_menu', 'cashier');
        Tpl::showPage("cashier.transactions");
    }

    public function getCashierTransactionsListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $currency = trim($p['currency']);
        $user_id = trim($p['cashier_id']);
        $m_user = M('um_user');
        $user = $m_user->find(array('uid' => $user_id));
        Tpl::output('user', $user);
        $userObj = new objectUserClass($user_id);
        $passbook = $userObj->getUserPassbook();

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
        );
    }

    public function transactionsOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::output('show_menu', 'transactions');
        Tpl::showPage('branch.transactions');
    }


    public function getTransactionsListOp($p)
    {
        $branch_id = $this->branch_id;
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $currency = trim($p['currency']);
        $branch_Obj = new objectBranchClass($branch_id);
        $passbook = $branch_Obj->getPassbook();
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
        );
    }

    public function tradeDetailOp()
    {
        $branch_id = $this->branch_id;
        $m_branch = M('site_branch');
        $branch = $m_branch->find(array('uid' => $branch_id));
        $branch_guid = $branch['obj_guid'];

        $uid = intval($_GET['uid']);

        $r = new ormReader();
        $m_passbook_trading = M('passbook_trading');
        $trading_info = $m_passbook_trading->find(array('uid' => $uid));
        if (!$trading_info) {
            showMessage('Invalid Id.');
        }

        $sql = "SELECT paf.credit,paf.debit,p.obj_type,p.obj_guid,p.book_name FROM passbook_account_flow paf"
            . " INNER JOIN passbook_account pa ON pa.uid = paf.account_id"
            . " INNER JOIN passbook p ON p.uid = pa.book_id"
            . " WHERE p.obj_guid !=" . $branch_guid . " AND paf.trade_id=" . $uid;
        $trading_flow = $r->getRows($sql);

//        $m_client_member = M('client_member');
//        $m_um_user = M('um_user');
//        $m_site_bank = M('site_bank');
//        foreach ($trading_flow as $key => $flow) {
//            switch ($flow['obj_type']) {
//                case passbookObjTypeEnum::CLIENT_MEMBER:
//                    $member_info = $m_client_member->find(array('obj_guid' => $flow['obj_guid']));
//                    $partner_name = $member_info['display_name'] ?: $member_info['login_code'];
//                    $partner_type = 'Member';
//                    break;
//                case passbookObjTypeEnum::UM_USER:
//                    $user_info = $m_um_user->find(array('obj_guid' => $flow['obj_guid']));
//                    $partner_name = $user_info['user_name'];
//                    $partner_type = 'User';
//                    break;
//                case passbookObjTypeEnum::BANK:
//                    $bank_info = $m_site_bank->find(array('obj_guid' => $flow['obj_guid']));
//                    $partner_name = $bank_info['bank_name'];
//                    $partner_type = 'Bank';
//                    break;
//                default:
//                    showMessage('Invalid passbook type.' . $flow['obj_type']);
//            }
//            $flow['partner_name'] = $partner_name;
//            $flow['partner_type'] = $partner_type;
//            $trading_flow[$key] = $flow;
//        }
        Tpl::output('trading_info', $trading_info);
        Tpl::output('trading_flow', $trading_flow);
        Tpl::output('show_menu', 'transactions');
        Tpl::showPage('trading.detail');
    }

    /**
     * 其他收入
     */
    public function cashInOp()
    {
        Tpl::showPage('cash.in');
    }

    public function submitCashInOp($p)
    {
        $cashier_id = $this->user_id;
        $amount = round($p['amount'], 2);
        $currency = trim($p['currency']);
        $remark = trim($p['remark']);
        $flag_type = flagTypeEnum::INCOME;
        $bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        $rt = $bizOutSystem->bizStart($cashier_id, $flag_type, $amount, $currency, $remark);
        if ($rt->STS) {
            return new result(true, $rt->MSG, $rt->DATA);
        } else {
            return new result(false, $rt->MSG);
        }

    }

    public function authorizeCashInOp()
    {
        $biz_id = $_GET['biz_id'];
        if (!$biz_id) {
            show_exception("Invalid Parameter:Biz-ID is incorrect!");
        }
        $bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        $arr_biz = $bizOutSystem->getBizDetailById($biz_id);
        if (!$arr_biz || !count($arr_biz)) {
            show_exception("invalid business information");
        }
        //cashier必须是当前用户
        if ($arr_biz['cashier_id'] != $this->user_id) {
            show_exception("invalid business information: are you a hacker?");
        }
        //超时5分钟不认可
        $time_diff = time() - strtotime($arr_biz['create_time']);
        if ($time_diff >= 5 * 60) {
            show_exception("The operation timed out!");
        }
        Tpl::output("biz", $arr_biz);
        Tpl::output('show_menu', 'cashIn');
        Tpl::showPage('confirm.cash.in');
    }

    public function confirmCashInOp($p)
    {

        $biz_id = intval($p['biz_id']);
        $chief_teller_card_no = trim($p['chief_teller_card_no']);
        $chief_teller_key = trim($p['chief_teller_key']);
        $bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        //启动事务
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt_1 = $bizOutSystem->checkChiefTellerPassword($biz_id, $chief_teller_card_no, $chief_teller_key);
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, $rt_1->MSG);
            }

            $rt_2 = $bizOutSystem->bizSubmit($biz_id);
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, $rt_2->MSG);
            }
            $conn->submitTransaction();
            return new result(true, 'Submit Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    public function getCashInListOp($p)
    {
        $cashier_id = $this->user_id;
        $flag_type = flagTypeEnum::INCOME;
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
        $bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        $data = $bizOutSystem->getPageFlowList($cashier_id, $pageNumber, $pageSize, $flag_type);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize
        );
    }

    /**
     * 其他支出
     */
    public function cashOutOp()
    {
        /*
        $bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        $systemAccountBalance = $bizOutSystem::getSystemAccountBalance();
        Tpl::output('systemAccountBalance', $systemAccountBalance);
        */
        //获取扩展交易类型
        $ext_trade_type = common_civ_ext_typeModel::getExtOutType();
        Tpl::output("extra_type", $ext_trade_type);
        Tpl::showPage('cash.out');
    }


    public function submitCashOutOp($p)
    {
        $cashier_id = $this->user_id;
        $amount = round($p['amount'], 2);
        $currency = trim($p['currency']);
        $extra_type = $p['extra_type'];
        $remark = trim($p['remark']);
        $bizOutSystem = new bizCivExtOutClass(bizSceneEnum::COUNTER);
        $rt = $bizOutSystem->execute($this->branch_id, $cashier_id, $amount, $currency, $remark, $extra_type);
        if ($rt->STS) {
            return new result(true, $rt->MSG, $rt->DATA);
        } else {
            return new result(false, $rt->MSG);
        }

    }

    public function authorizeCashOutOp()
    {
        $biz_id = $_GET['biz_id'];
        if (!$biz_id) {
            show_exception("Invalid Parameter:Biz-ID is incorrect!");
        }
        $bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        $arr_biz = $bizOutSystem->getBizDetailById($biz_id);
        if (!$arr_biz || !count($arr_biz)) {
            show_exception("invalid business information");
        }
        //cashier必须是当前用户
        if ($arr_biz['cashier_id'] != $this->user_id) {
            show_exception("invalid business information: are you a hacker?");
        }
        //超时5分钟不认可
        $time_diff = time() - strtotime($arr_biz['create_time']);
        if ($time_diff >= 5 * 60) {
            show_exception("The operation timed out!");
        }
        Tpl::output("biz", $arr_biz);
        Tpl::output('show_menu', 'cashOut');
        Tpl::showPage('confirm.cash.out');
    }

    public function confirmCashOutOp($p)
    {

        $biz_id = intval($p['biz_id']);
        $chief_teller_card_no = trim($p['chief_teller_card_no']);
        $chief_teller_key = trim($p['chief_teller_key']);
        $bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        //启动事务
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt_1 = $bizOutSystem->checkChiefTellerPassword($biz_id, $chief_teller_card_no, $chief_teller_key);
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, $rt_1->MSG);
            }

            $rt_2 = $bizOutSystem->bizSubmit($biz_id);
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, $rt_2->MSG);
            }
            $conn->submitTransaction();
            return new result(true, 'Submit Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }


    /**
     * 其他业务支出历史记录
     */
    public function getCashOutListOp($p)
    {
        $cashier_id = $this->user_id;
        $flag_type = flagTypeEnum::PAYOUT;
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
        $md = new biz_civ_adjustModel();
        $data = $md->getHistoryList(flagTypeEnum::PAYOUT, $pageNumber, $pageSize);

        //$bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        //$data = $bizOutSystem->getPageFlowList($cashier_id, $pageNumber, $pageSize, $flag_type);

        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize
        );
    }

    public function journalVoucherOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $trade_type = global_settingClass::getAllTradingType();
        Tpl::output("trade_type", $trade_type);
        $member_id = $_GET['member_id'];
        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info", $client_info);
        Tpl::output("member_id", $member_id);
        Tpl::showPage("teller.journal.voucher");
    }

    public function getJournalVoucherListOp($p)
    {
        $trade_id = $p['trade_id'];
        $obj = new objectBranchClass($this->user_info['branch_id']);
        $passbook = $obj->getPassbook();
        $trade_type = $p['trade_type'];
        $remark = $p['remark'];
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $pageNumber = $p['pageNumber'];
        $pageSize = $p['pageSize'];
        $filters = array(
            'trade_id' => $trade_id,
            'trade_type' => $trade_type,
            'remark' => $remark,
            'start_date' => $date_start,
            'end_date' => $date_end
        );
        return counter_codClass::getCounterVoucherData($passbook, $pageNumber, $pageSize, $filters);
    }

    public function dailyReportOp()
    {
        Tpl::showPage("report.daily");
    }

    public function getDailyReportByDayOp($p)
    {
        $end_date = $p['year'] . '-' . str_pad($p['month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($p['day'], 2, '0', STR_PAD_LEFT);
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
        return $ret;
    }

    public function cashierCreditOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['cashier_id']);
        if ($p['form_submit'] == 'ok') {
            $class_user = new userClass();
            $p['operator_id'] = $this->user_id;
            $p['operator_name'] = $this->user_name;
            $rt = $class_user->editUserCredit($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('cash_in_vault', 'cashier', array(), false, ENTRY_COUNTER_SITE_URL));
            } else {
                showMessage($rt->MSG);
            }
        } else {
            $m_um_user = M('um_user');
            $user = $m_um_user->find(array('uid' => $uid));
            Tpl::output('user', $user);
            Tpl::output('show_menu', 'cashier');
            Tpl::showPage('cashier.credit');
        }
    }


    public function exchangeOp()
    {
        $usd_exchange_khr = global_settingClass::getCurrencyRateBetween(currencyEnum::USD, currencyEnum::KHR);
        $khr_exchange_usd = global_settingClass::getCurrencyRateBetween(currencyEnum::KHR, currencyEnum::USD);
        Tpl::output('usd_exchange_khr',$usd_exchange_khr);
        Tpl::output('khr_exchange_usd',$khr_exchange_usd);
        Tpl::showPage('branch.exchange.index');
    }

    public function submitExchangeOp($p)
    {
        $branch_id = $this->branch_id;
        $user_id = $this->user_id;
        $amount = round($p['amount'],2);
        $from_currency = trim($p['from_currency']);
        $to_currency = trim($p['to_currency']);
        $exchange_rate = global_settingClass::getCurrencyRateBetween($from_currency, $to_currency);
        $remark = trim($p['remark']);
       $biz_branch_exchange = new bizBranchExchangeClass(bizSceneEnum::COUNTER);
       $rt= $biz_branch_exchange-> bizStart($branch_id,$user_id,$amount,$from_currency,$to_currency,$exchange_rate,$remark);
       if(!$rt->STS){
           return new result(false,$rt->MSG);
       }
        return new result(true,'',$rt->DATA);
    }

    public function exchangeCheckOp(){
        $biz_id=$_GET['biz_id'];
        $biz_branch_exchange = new bizBranchExchangeClass(bizSceneEnum::COUNTER);
        $data = $biz_branch_exchange->getBizDetailById($biz_id);
        Tpl::output('data',$data);
        Tpl::output('show_menu', 'exchange');
        Tpl::showPage('exchange.check');
    }

    public function confirmExchangeOp($p){
        $biz_id = intval($p['biz_id']);
        $password = trim($p['password']);
        $biz_branch_exchange = new bizBranchExchangeClass(bizSceneEnum::COUNTER);
        $rt_1 = $biz_branch_exchange->checkUserTradingPassword($biz_id,$password);
        if(!$rt_1->STS){
            return new result(false,$rt_1->MSG);
        }
        $rt_2 = $biz_branch_exchange->bizConfirm($biz_id);
        if(!$rt_2->STS){
            return new result(false,$rt_1->MSG);
        }

        return new result(true,$rt_2->MSG);
    }

    public function getExchangeHistoryOp($p)
    {
        $branch_id = $this->branch_id;
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
        $biz_branch_exchange = new bizBranchExchangeClass(bizSceneEnum::COUNTER);
        $data = $biz_branch_exchange->getExchangeHistory($branch_id,$pageNumber,$pageSize);
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
