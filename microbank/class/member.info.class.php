<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/9
 * Time: 16:00
 */
class memberInfoClass
{
    public function __construct()
    {
    }

    public static function getMemberDetail($member_id)
    {
        $member_id = intval($member_id);
        if (!$member_id) {
            return new result(false, 'Client Error');
        }

        $r = new ormReader();
        $contract_info = array();
        $data = memberClass::getMemberBaseInfo($member_id);
        if (!$data) {
            return new result(false, 'Client Error');
        }

        $branch = M('site_branch')->find(array('uid' => $data['branch_id']));
        $data['branch_name'] = $branch['branch_name'];

        $m_member_follow_officer = M('member_follow_officer');
        $operator = $m_member_follow_officer->getOperatorInfoByMemberId($member_id);
        $data['operator'] = $operator;

        //member co list
        $class_member = new memberClass();
        $member_co_list = $class_member->getMemberCoList($member_id);
        $data['member_co_list'] = $member_co_list;

        $data['allow_product'] = memberClass::getMemberCreditLoanProduct($member_id);

        //Residence
        $m_common_address = M('common_address');
        $residence = $m_common_address->getMemberResidencePlaceByGuid($data['obj_guid']);
        $data['residence'] = $residence;

        $loan_account = M('loan_account')->find(array('obj_guid' => $data['obj_guid']));
        $data['loan_uid'] = $loan_account['uid'];

        $expire_time = M('member_credit')->orderBy('uid DESC')->find(array('member_id' => $member_id));
        $data['expire_time'] = $expire_time['expire_time'];

        $loan_principal = member_statisticsClass::getLoanTotalGroupByCurrency($member_id);
        if (!$loan_principal->STS) {
            return new result(false, $loan_principal->MSG);
        }
        $data['loan_principal'] = $loan_principal->DATA;

        $outstanding_principal = member_statisticsClass::getMemberTotalPayableLoanPrincipalGroupByCurrency($member_id);
        $data['outstanding_principal'] = $outstanding_principal;

        $loan_uid = intval($data['loan_uid']);
        $sql2 = "SELECT contract.*,product.product_code,product.product_name,product.product_description FROM loan_contract as contract left join loan_product as product on contract.product_id = product.uid where contract.account_id = " . $loan_uid . " and contract.state >= " . loanContractStateEnum::PENDING_DISBURSE . " order by contract.uid desc";
        $contracts = $r->getRows($sql2);

        $sql2 = "SELECT contract.* FROM insurance_contract as contract left join insurance_account as account on contract.account_id = account.uid where account.obj_guid = " . qstr($data['obj_guid']) . " order by contract.uid desc";
        $insurance_contracts = $r->getRows($sql2);

        //*这是request loan的次数
        $sql = "SELECT count(uid) as count from loan_apply where member_id = " . $member_id;
        $loan_count = $r->getOne($sql);
        $contract_info['all_enquiries'] = $loan_count;

        //*这是第一次发放贷款的日期
        $sql = "select create_time from loan_contract where account_id = " . $loan_uid . " AND state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE) . " ORDER BY uid ASC";
        $create_time = $r->getOne($sql);
        $contract_info['earliest_loan_issue_date'] = $create_time;
        $loan_summary = memberClass::getMemberLoanSummary($member_id, 1, 7);

        $return_data = array();
        $return_data['contract_info'] = $contract_info;
        $return_data['loan_summary'] = $loan_summary->DATA;

        $credit_info = memberClass::getCreditBalance($member_id);

        $return_data['detail'] = $data;
        $return_data['credit_info'] = $credit_info;
        $return_data['contracts'] = $contracts;
        $return_data['insurance_contracts'] = $insurance_contracts;

        // 客户的黑名单项目
        $sql = "select * from client_black where member_id='$member_id' ";
        $rows = $r->getRows($sql);
        $black_list = array();
        foreach ($rows as $v) {
            $black_list[$v['type']] = true;
        }

        $black_type = (new blackTypeEnum())->toArray();
        $client_black = array();
        foreach ($black_type as $type) {
            $client_black[$type]['type'] = $type;
            $client_black[$type]['check'] = $black_list[$type] ?: false;
        }

        $return_data['black'] = $client_black;

        // 储蓄账户余额
        $memberObject = new objectMemberClass($member_id);
        $savings_balance = $memberObject->getSavingsAccountBalance();
        $return_data['savings_balance'] = $savings_balance;
        return new result(true, '', $return_data);
    }

    public static function getClientPage($pageNumber, $pageSize, $filters = array())
    {
        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 20;

        $r = new ormReader();
        $sql = "SELECT cm.*,mc.credit,mc.credit_balance,mc.expire_time,SUM(CASE WHEN mfo.is_active = 1 AND officer_type = 0 THEN 1 ELSE 0 END) co_count"
            . " FROM client_member cm"
            . " LEFT JOIN member_credit mc ON cm.uid = mc.member_id"
            . " LEFT JOIN member_credit_suggest mcs ON cm.uid = mcs.member_id"
            . " LEFT JOIN member_follow_officer mfo ON cm.uid = mfo.member_id"
            . " LEFT JOIN task_co_bm tcb ON cm.uid = tcb.member_id"
            . " WHERE 1 = 1";
        if (intval($filters['branch_id'])) {
            $sql .= " AND cm.branch_id = " . intval($filters['branch_id']);
        }
        if (trim($filters['search_text'])) {
            $sql .= " AND (cm.obj_guid = " . qstr(trim($filters['search_text'])) . " OR cm.display_name LIKE '%" . qstr2(trim($filters['search_text'])) . "%' OR cm.login_code LIKE '%" . trim($filters['search_text']) . "%' OR cm.phone_id LIKE '%" . qstr2(trim($filters['search_text'])) . "%')";
        }
        if (intval($filters['is_credit']) == 1) {
            $sql .= " AND mc.credit > 0 AND expire_time >= " . qstr(Now());
        } else if (intval($filters['is_credit']) == 2) {
            $sql .= " AND (mc.credit IS NULL OR expire_time < " . qstr(Now()) . ")";
        }
        if (intval($filters['pending_committee_approve'])) {
            $sql .= " AND mcs.state = " . memberCreditSuggestEnum::PENDING_APPROVE;
        }

        // new client 是指刚注册或者还没有提交过信用申请的
        if (intval($filters['member_state_new'])) {
            $sql .= " AND ( cm.member_state='".memberStateEnum::CREATE."' or tcb.uid is null )";
        }

//        if (intval($filters['member_state_suspended'])) {
//            //$sql .= " AND cm.member_state = " . memberStateEnum::SUSPENDED;
//        } elseif (isset($filters['member_state_suspended'])) {
//            $sql .= " AND cm.member_state != " . memberStateEnum::TEMP_LOCKING;
//        }
        if (intval($filters['member_state_cancel'])) {
            $sql .= " AND cm.member_state = " . memberStateEnum::CANCEL;
        } elseif (isset($filters['member_state_cancel'])) {
            $sql .= " AND cm.member_state != " . memberStateEnum::CANCEL;
        }
        $sql .= " GROUP BY cm.uid ORDER BY cm.uid DESC";

        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if (intval($filters['member_state_cancel']) && $rows) {
            $member_ids = array_column($rows, 'uid');
            $member_id_str = '(' . implode(',', $member_ids) . ')';
            $sql = "SELECT * FROM member_state_log WHERE uid IN (SELECT max(uid) FROM member_state_log WHERE member_id IN $member_id_str AND current_state = " . memberStateEnum::CANCEL . " GROUP BY member_id)";
            $log_list = $r->getRows($sql);
            $log_list = resetArrayKey($log_list, 'member_id');
            foreach ($rows as $key => $row) {
                $row['change_state_remark'] = $log_list[$row['uid']]['remark'];
                $rows[$key] = $row;
            }
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "member_state_cancel" => intval($filters['member_state_cancel'])
        );
    }

    public static function lockClientForCo($member_id, $type = 1)
    {
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        $member_property = json_decode($row['member_property']);
        if (is_array($member_property)) {
            $member_property[memberPropertyKeyEnum::LOCK_FOR_CO] = $type;
        } else {
            $member_property = array(
                memberPropertyKeyEnum::LOCK_FOR_CO => $type
            );
        }
        $row->member_property = my_json_encode($member_property);
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Handle successful.');
        } else {
            return new result(true, 'Handle failed.');
        }
    }
}