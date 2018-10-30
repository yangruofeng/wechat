<?php

class monitorClass
{
    static function getMonitorConfig()
    {
        $rt = array(
            array("auth" => authEnum::AUTH_CLIENT_CERIFICATION, "fn" => "getMonitorSQL_verification", "title" => "Verification",
                "url" => getUrl("client", "cerification", array(), "operation", BACK_OFFICE_SITE_URL), "group" => "client"),
            array("auth" => authEnum::AUTH_LOAN_CONTRACT, "fn" => "getMonitorSQL_contract", "title" => "New Contracts",
                "url" => getUrl("loan", "contract", array(), "operation", BACK_OFFICE_SITE_URL), "group" => "loan"),
        );

        return $rt;
    }

    public static function getMonitorItems()
    {
        $user = userBase::Current();
        $items = self::getMonitorConfig();
        $req = array();
        foreach ($items as $k => $item) {
            if ($user->checkAuth($item['auth'])) {
                $req[$k] = array("key" => $k, "title" => $item['title'], "url" => $item['url'], "group" => $item['group']);
            }
        }
        return $req;
    }

    public function getMonitor($p)
    {
        $last_time = $p['last_time'];

        $user = userBase::Current();
        $items = self::getMonitorConfig();
        $req = array();
        foreach ($items as $k => $item) {
            if ($user->checkAuth($item['auth'])) {
                $req[$k] = array_merge(array("key" => $k), $item);
            }
        }

        if (!count($req)) return array('STS' => false);
        $data = array();
        foreach ($req as $item) {
            $fn = $item['fn'];
            $values = $this->$fn();
            if ($values && is_string($values)) {
                $tmp_rd = new ormReader(ormYo::Conn("db_loan"));
                $values = $tmp_rd->getRows($values);
            }

            if ($values && is_array($values)) {
                $data[$item['key']]['count'] = count($values);
                $data[$item['key']]['new'] = 0;
                $data[$item['key']]['title'] = $item['title'];
                if ($last_time) {
                    $new_cnt = 0;
                    foreach ($values as $v) {
                        if ($v['operate_time'] > $last_time) {
                            $new_cnt += 1;
                        }
                    }
                    $data[$item['key']]['new'] = $new_cnt;
                }
                //$data[$item['key']]['new']=1;
                if (count($values) > 0) {
                    $last_item = array_pop($values);
                    $data[$item['key']]['content'] = '<span style="font-weight: bold;">' . $last_item['task_item'] . '</span><br/><span>' . $last_item['operate_time'] . '</span>';
                } else {
                    $data[$item['key']]['content'] = 'null<br/>&nbsp;';
                }
            }
        }

        return array('STS' => true, "data" => $data, "last_time" => Now());

    }

    public function getMonitorSQL_verification()
    {
        $sql = "select member.display_name task_item, verify.auditor_time operate_time from member_verify_cert as verify left join client_member as member on verify.member_id = member.uid where verify.verify_state = 0";
        return $sql;
    }

    public function getMonitorSQL_grant_credit()
    {
        $sql = "SELECT client.display_name task_item, loan.update_time operate_time FROM loan_account as loan left join client_member as client on loan.obj_guid = client.obj_guid where loan.credit is null or loan.credit = 0";
        return $sql;
    }

    public function getMonitorSQL_approve_credit()
    {
        $r = new ormReader();
        $sql1 = "select uid from (select * from loan_approval order by uid desc) loan_approval group by obj_guid";
        $ids = $r->getRows($sql1);
        $ids = array_column($ids, 'uid');
        $ids = implode(',', $ids);
        $sql = "SELECT client.display_name task_item, approval.create_time operate_time "
            . " FROM loan_account as loan "
            . " left join client_member as client on loan.obj_guid = client.obj_guid"
            . " inner join loan_approval as approval on client.obj_guid = approval.obj_guid where approval.uid in (" . $ids . ")"
            . " and approval.state = 0 ";
        return $sql;
    }

    public function getMonitorSQL_contract()
    {
        $sql = "SELECT contract.contract_sn task_item,contract.create_time operate_time FROM loan_contract as contract"
            . " inner join loan_account as account on contract.account_id = account.uid"
            . " left join client_member as member on account.obj_guid = member.obj_guid where contract.state = 0 and contract.create_time >= date_add(NOW(), INTERVAL -1 DAY)";
        return $sql;
    }

    public function getDashboardInfo()
    {
        $r = new ormReader();

        $sql = "select count(uid) count from client_member where member_state != '" . memberStateEnum::CANCEL . "' and is_verify_phone = 1";
        $count_client = $r->getOne($sql);

        $sql = "select count(uid) count from client_member where member_state != '" . memberStateEnum::CANCEL . "' and is_verify_phone = 1 and create_time >= '" . date('Y-m-d', time()) . "'";
        $count_register_client = $r->getOne($sql);

        $sql = "select count(uid) count from client_member where member_state = " . qstr(memberStateEnum::CHECKED) . " and is_verify_phone = 1";
        $count_checked_client = $r->getOne($sql);

        $sql = "select count(uid) count from client_member where member_state in('" . memberStateEnum::TEMP_LOCKING . "','" . memberStateEnum::SYSTEM_LOCKING . "')  and is_verify_phone = 1";
        $count_lock_client = $r->getOne($sql);

        $sql = "select count(uid) count from client_member where member_state = " . qstr(memberStateEnum::VERIFIED) . " and is_verify_phone = 1";
        $count_verify_client = $r->getOne($sql);

        $client['count_client'] = $count_client;
        $client['count_register_client'] = $count_register_client;
        $client['count_checked_client'] = $count_checked_client;
        $client['count_lock_client'] = $count_lock_client;
        $client['count_verify_client'] = $count_verify_client;

        $sql = "SELECT COUNT(*) FROM loan_consult";
        $count_consult = $r->getOne($sql);

        $sql = "SELECT COUNT(*) FROM member_assets";
        $count_assets = $r->getOne($sql);

        $sql = "SELECT COUNT(*) FROM member_credit_request";
        $count_credit_request = $r->getOne($sql);

        $sql = "SELECT COUNT(*) FROM member_credit_grant";
        $count_credit_grant = $r->getOne($sql);

        $sql = "SELECT COUNT(*) FROM member_income_business";
        $count_business_research = $r->getOne($sql);

        $credit_arr = array();
        $credit_arr['count_consult'] = $count_consult;
        $credit_arr['count_assets'] = $count_assets;
        $credit_arr['count_credit_request'] = $count_credit_request;
        $credit_arr['count_credit_grant'] = $count_credit_grant;
        $credit_arr['count_business_research'] = $count_business_research;

        return array(
            'STS' => true,
            'client' => $client,
            'credit_arr' => $credit_arr,
        );

    }

    public static function getDashboardLoan()
    {
        $count_contract = statisticsClass::getLoanContractNumSummary(0);
        $loan_total = 0;
        $loan_ret = statisticsClass::getLoanTotal();
        if ($loan_ret->STS) {
            $loan_total = $loan_ret->DATA;
        }
        $payable_total = 0;
        $payable_ret = statisticsClass::getLoanTotalRepayable();
        if ($payable_ret->STS) {
            $payable_total = $payable_ret->DATA;
        }
        $receivable_interest_total = 0;
        $interest_ret = statisticsClass::getLoanInterestTotal();
        if ($interest_ret->STS) {
            $receivable_interest_total = $interest_ret->DATA;
        }
        $outstanding_interest_total = 0;
        $outstanding_ret = statisticsClass::getOutstandingLoanInterestTotal();
        if ($outstanding_ret->STS) {
            $outstanding_interest_total = $outstanding_ret->DATA;
        }
        $loan['count_contract'] = $count_contract;
        $loan['total_principal'] = $loan_total;
        $loan['total_outstanding_principal'] = $payable_total;
        $loan['total_receivable_interest'] = $receivable_interest_total;
        $loan['total_outstanding_interest'] = $outstanding_interest_total;

        return array(
            'data' => $loan
        );
    }

    public static function getDashboardSavings()
    {
        $savings = statisticsClass::getMemberSavings();
        $savings_new = array();
        foreach ($savings as $k => $v) {
            $savings_new[$v['currency']] = $v['balance'];
        }
        return array(
            'data' => $savings_new
        );
    }

    public static function getDashboardBusinessActivity()
    {
        $r = new ormReader();
        $business_activity = array();
        $sql = "select count(*) from biz_bank_adjust WHERE state = 100";
        $count_bank_adjust = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Bank Adjust',
            'count' => $count_bank_adjust,
        );

        $sql = "select count(*) from biz_obj_transfer WHERE state = 100";
        $count_obj_transfer = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Account Transfer',
            'count' => $count_obj_transfer,
        );

        $sql = "select count(*) from biz_out_system_flow WHERE state = 100";
        $count_out_system_flow = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Out System Flow',
            'count' => $count_out_system_flow,
        );

        $sql = "select count(*) from biz_member_deposit WHERE state = 100";
        $count_member_deposit = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Member Deposit',
            'count' => $count_member_deposit,
        );

        $sql = "select count(*) from biz_member_withdraw WHERE state = 100";
        $count_member_withdraw = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Member Withdraw',
            'count' => $count_member_withdraw,
        );

        $sql = "select count(*) from biz_member_transfer WHERE state = 100";
        $count_member_transfer = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Member Transfer',
            'count' => $count_member_transfer,
        );

        $sql = "select count(*) from biz_member_create_loan_contract WHERE state = 100";
        $count_member_create_loan_contract = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Create Loan Contract',
            'count' => $count_member_create_loan_contract,
        );

        $sql = "select count(*) from biz_member_loan_repayment WHERE state = 100";
        $count_member_loan_repayment_by_cash = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Repayment By Cash',
            'count' => $count_member_loan_repayment_by_cash,
        );

        $sql = "select count(*) from biz_member_prepayment WHERE state = 100";
        $count_member_prepayment = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Member Prepayment',
            'count' => $count_member_prepayment,
        );

        $sql = "select count(*) from biz_receive_member_penalty WHERE state = 100";
        $count_receive_member_penalty = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Receive Penalty',
            'count' => $count_receive_member_penalty,
        );

        $sql = "select count(*) from biz_co_receive_loan_from_member WHERE state = 100";
        $count_co_receive_loan_from_member = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Co Receive Loan',
            'count' => $count_co_receive_loan_from_member,
        );

        $sql = "select count(*) from biz_member_scan_pay_to_member WHERE state = 100";
        $count_member_scan_pay_to_member = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Member Scan Pay To Member',
            'count' => $count_member_scan_pay_to_member,
        );

        $sql = "select count(*) from biz_member_change_password WHERE state = 100";
        $count_member_change_password = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Member Change Password',
            'count' => $count_member_change_password,
        );

        $sql = "select count(*) from biz_member_change_phone WHERE state = 100";
        $count_member_change_phone = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Member Change Phone',
            'count' => $count_member_change_phone,
        );

        $sql = "select count(*) from biz_member_lock_handle WHERE state = 100";
        $count_member_lock_handle = $r->getOne($sql);
        $business_activity[] = array(
            'title' => 'Member Lock Handle',
            'count' => $count_member_lock_handle,
        );
        return array(
            'data' => $business_activity
        );
    }

    public function getLoanContract($filter = array()){
        $r = new ormReader();
        if($filter['uid']){
            $where = ' where c.uid in( ' . $filter['uid'] . ')';
        }else{
            $where = ' where c.state != ' . qstr(loanContractStateEnum::CANCEL);
            if($filter['client_text']){
                $where .= ' and (m.obj_guid = '.qstr($filter['client_text']).' or m.login_code like "%'.$filter['client_text'].'%" or m.display_name like "%'.$filter['client_text'].'%" or m.phone_id like "%'.$filter['client_text'].'%")';
            }
            if($filter['contract_sn']){
                $where .= ' and c.contract_sn = '. qstr($filter['contract_sn']);
            }
            if($filter['state']){
                $where .= ' and c.state = '. qstr($filter['state']);
            }
            if($filter['date_start'] == $filter['date_end']){
                $where .= ' and c.start_date = ' .qstr(system_toolClass::getFormatEndDate($filter['date_end']));
            }else{
                $where .= ' and c.start_date between '. qstr(system_toolClass::getFormatStartDate($filter['date_start'])) .' and ' . qstr(system_toolClass::getFormatEndDate($filter['date_end']));
            }
        }
        $sql = "select m.obj_guid,m.login_code,m.display_name,m.phone_id,mcc.alias,c.* from loan_contract c left join client_member m on m.obj_guid = c.client_obj_guid left join member_credit_category mcc on c.member_credit_category_id = mcc.uid $where order by c.uid desc";
        $list = $r->getRows($sql);
        return $list;
    }
}