<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/12
 * Time: 13:58
 */
class cash_on_handControl extends counter_baseControl
{

    public function __construct()
    {
        parent::__construct();
        Language::read('cash');
        $this->outputSubMenu('cash_on_hand');
        Tpl::setLayout('home_layout');
        Tpl::setDir("cash_on_hand");
    }

    public function transactionsOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage("transactions");
    }

    /**
     * 获取user 交易记录
     */
    public function getTransactionsListOp($p)
    {
        $user_id = $this->user_id;
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $currency = trim($p['currency']);

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

    /**
     * user 交易明细
     */
    public function tradeDetailOp()
    {
        $user_guid = $this->user_info['obj_guid'];
        $uid = $_GET['uid'];
        $r = new ormReader();
        $sql = "select cm.display_name,uu.user_name,pt.update_time,pt.trading_type,paf.credit,paf.debit 
                from passbook_trading pt left join passbook_account_flow paf  on pt.uid=paf.trade_id  
                left join passbook_account pa on pa.uid=paf.account_id LEFT JOIN passbook p ON p.uid=pa.book_id 
                LEFT JOIN client_member cm ON cm.obj_guid = p.obj_guid LEFT JOIN um_user uu ON uu.obj_guid=p.obj_guid WHERE p.obj_guid !=" . $user_guid . " AND pt.uid=" . $uid;
        $detail = $r->getRow($sql);
        Tpl::output('detail', $detail);
        Tpl::output('show_menu', 'transactions');
        Tpl::showPage('trading.detail');
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

    public function getReceiveListOp($p)
    {
        $user_obj_guid = $this->user_info['obj_guid'];
        $state = trim($p['state']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);

        $r = new ormReader();
        $sql = "SELECT * FROM biz_obj_transfer
            WHERE (create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')  AND receiver_obj_guid = " . $user_obj_guid;
        if ($state == 'pending') {
            $sql .= " AND is_outstanding = 1 AND state = 0";
        }
        if ($state == 'received') {
            $sql .= " AND is_outstanding = 0 AND state = 100";
        }
        if ($state == 'rejected') {
            $sql .= " AND is_outstanding = 0 AND state = 10";
        }

        $sql .= " ORDER BY update_time DESC";
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

    public function transferToVaultOp()
    {
        $branch_id = $this->user_info['branch_id'];
        $r = new ormReader();
        $sql = "SELECT uu.uid,uu.user_name FROM um_user uu"
            . " LEFT JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " LEFT JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE uu.user_position = 'chief_teller' AND sb.uid = " . $branch_id;
        $cashier = $r->getRows($sql);
        Tpl::output('cashier', $cashier);
        Tpl::showPage("transfer.vault");
    }

    /**
     * cashier to ct
     */
    public function transferToCtOp($p)
    {

        $user_id = $this->user_id;
        $branch_id = $this->user_info['branch_id'];
        $chief_id = intval($p['chief_id']);
        $currency = trim($p['currency']);
        $amount = trim($p['amount']);
        $trading_password = trim($p['trading_password']);
        $remark = trim($p['remark']);
        $tellerToBranch = new bizTellerToBranchClass(bizSceneEnum::COUNTER);

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $tellerToBranch->execute($user_id, $trading_password, $branch_id, $chief_id, $amount, $currency, $remark);
        if ($rt->STS) {
            $conn->submitTransaction();
            return new result(true, "Transfer Successful");
        } else {
            $conn->rollback();
            return new result(false, 'Transfer Failure' . $rt->MSG);
        }
    }


    /**
     * 接受转入
     */
    public function receiveTransferOp($p)
    {
        $biz_id = intval($p['biz_id']);
        $branchToTeller = new bizBranchToTellerClass(bizSceneEnum::COUNTER);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $branchToTeller->confirm($biz_id);
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
        $branchToTeller = new bizBranchToTellerClass(bizSceneEnum::COUNTER);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $branchToTeller->cancel($biz_id);
        if ($rt->STS) {
            $conn->submitTransaction();
            return new result(true, "Reject Successful");
        } else {
            $conn->rollback();
            return new result(false, 'Reject Failure' . $rt->MSG);
        }
    }

    public function getTransferVaultListOp($p)
    {
        $obj_guid = $this->user_info['obj_guid'];
        $state = trim($p['state']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $r = new ormReader();
        $sql = "SELECT * FROM biz_obj_transfer WHERE biz_code = '" . bizCodeEnum::TELLER_TO_BRANCH . "' AND sender_handler_obj_guid = " . $obj_guid;
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

        $extend=array();
        $extend['extend_cid']=trim($p['extend_cid']);
        $extend['extend_client_name']=trim($p['extend_client_name']);
        $extend['extend_contract_sn']=trim($p['extend_contract_sn']);


        $flag_type = flagTypeEnum::INCOME;
        $bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        $rt = $bizOutSystem->bizStart($cashier_id, $flag_type, $amount, $currency, $remark,$extend);
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
        $cashier_card_no = trim($p['cashier_card_no']);
        $cashier_key = trim($p['cashier_key']);
//        $chief_teller_card_no = trim($p['chief_teller_card_no']);
//        $chief_teller_key = trim($p['chief_teller_key']);
        $bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        //启动事务
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt_1 = $bizOutSystem->checkTellerPassword($biz_id, $cashier_card_no, $cashier_key);
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, $rt_1->MSG);
            }

//            $rt_2 = $bizOutSystem->checkChiefTellerPassword($biz_id, $chief_teller_card_no, $chief_teller_key);
//            if (!$rt_2->STS) {
//                $conn->rollback();
//                return new result(false, $rt_2->MSG);
//            }

            $rt_3 = $bizOutSystem->bizSubmit($biz_id);
            if (!$rt_3->STS) {
                $conn->rollback();
                return new result(false, $rt_3->MSG);
            }
            $conn->submitTransaction();
            return new result(true, 'Submit Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }


    /**
     * 其他业务收入历史记录
     */
    public function getCashInListOp($p)
    {
        $cashier_id = $this->user_id;
        $flag_type = flagTypeEnum::INCOME;
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
        $bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        $extend=array(
            "search_by"=>$p['search_by'],
            "search_value"=>$p['search_value']
        );
        $data = $bizOutSystem->getPageFlowList($cashier_id, $pageNumber, $pageSize, $flag_type,$extend);
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
        $bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        $systemAccountBalance = $bizOutSystem::getSystemAccountBalance();

        Tpl::output('systemAccountBalance', $systemAccountBalance);
        Tpl::showPage('cash.out');
    }


    public function submitCashOutOp($p)
    {
        $cashier_id = $this->user_id;
        $amount = round($p['amount'], 2);
        $currency = trim($p['currency']);
        $remark = trim($p['remark']);

        $extend=array();
        $extend['extend_cid']=trim($p['extend_cid']);
        $extend['extend_client_name']=trim($p['extend_client_name']);
        $extend['extend_contract_sn']=trim($p['extend_contract_sn']);

        $flag_type = flagTypeEnum::PAYOUT;
        $bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        $rt = $bizOutSystem->bizStart($cashier_id, $flag_type, $amount, $currency, $remark,$extend);
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
        $cashier_card_no = trim($p['cashier_card_no']);
        $cashier_key = trim($p['cashier_key']);
//        $chief_teller_card_no = trim($p['chief_teller_card_no']);
//        $chief_teller_key = trim($p['chief_teller_key']);
        $bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        //启动事务
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt_1 = $bizOutSystem->checkTellerPassword($biz_id, $cashier_card_no, $cashier_key);
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, $rt_1->MSG);
            }

//            $rt_2 = $bizOutSystem->checkChiefTellerPassword($biz_id, $chief_teller_card_no, $chief_teller_key);
//            if (!$rt_2->STS) {
//                $conn->rollback();
//                return new result(false, $rt_2->MSG);
//            }

            $rt_3 = $bizOutSystem->bizSubmit($biz_id);
            if (!$rt_3->STS) {
                $conn->rollback();
                return new result(false, $rt_3->MSG);
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
        $bizOutSystem = new bizOutSystemCashFlowClass(bizSceneEnum::COUNTER);
        $extend=array(
            "search_by"=>$p['search_by'],
            "search_value"=>$p['search_value']
        );
        $data = $bizOutSystem->getPageFlowList($cashier_id, $pageNumber, $pageSize, $flag_type,$extend);
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

    public function dayVoucherOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $day = $params['day']?:date('Y-m-d');
        $condition = array(
            'currency' => $params['currency']?:currencyEnum::USD,
            'day' => $day
        );
        Tpl::output('user_id',$this->user_id);
        Tpl::output('condition',$condition);
        Tpl::showpage('teller.daily.voucher');
    }

    public function getDayVoucherListOp($p)
    {
        return counter_codClass::getUserDailyVoucherData($p);
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
        $member_id=$_GET['member_id'];
        $client_info=counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info",$client_info);
        Tpl::output("member_id",$member_id);
        Tpl::showPage("teller.journal.voucher");
    }

    public function getJournalVoucherListOp($p)
    {
        $trade_id = $p['trade_id'];
        $tellerObj = new objectUserClass($this->user_id);
        $passbook = $tellerObj->getUserPassbook();
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

}
