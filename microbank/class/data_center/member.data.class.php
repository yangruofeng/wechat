<?php

class memberDataClass
{
    public static function getMemberSummary($filter=array())
    {
        $r = new ormReader();

        $where = '';
        if( $filter['branch_id'] ){
            $where .= " and branch_id=".qstr($filter['branch_id']);
        }


        $sql = "select count(uid) count from client_member where member_state != " . qstr(memberStateEnum::CANCEL) . " and is_verify_phone = 1 $where ";
        $count_client = $r->getOne($sql);

        $sql = "select count(uid) count from client_member where member_state = " . qstr(memberStateEnum::CREATE) . " and is_verify_phone = 1 $where ";
        $count_pending_checked_client = $r->getOne($sql);

        $sql = "select count(uid) count from client_member where member_state in('" . memberStateEnum::TEMP_LOCKING . "','" . memberStateEnum::SYSTEM_LOCKING . "')  and is_verify_phone = 1 $where ";
        $count_lock_client = $r->getOne($sql);

        $sql = "select count(uid) count from client_member where member_state = " . qstr(memberStateEnum::VERIFIED) . " and is_verify_phone = 1 $where ";
        $count_verify_client = $r->getOne($sql);

        $sql = "select count(uid) count from client_member where to_days(create_time) = to_days(now()) $where ";
        $count_today_client = $r->getOne($sql);

        $sql = "select count(uid) count from client_member where member_state = " . qstr(memberStateEnum::CHECKED) . " and is_verify_phone = 1 $where ";
        $count_pending_verify_client = $r->getOne($sql);

        $client['count_client'] = $count_client;
        $client['count_today_client'] = $count_today_client;
        $client['count_pending_checked_client'] = $count_pending_checked_client;
        $client['count_pending_verify_client'] = $count_pending_verify_client;
        $client['count_lock_client'] = $count_lock_client;
        $client['count_verify_client'] = $count_verify_client;
        return $client;
    }

    /**
     * 获取会员列表
     * @param $pageNumber
     * @param $pageSize
     * @param $filters
     * @return array
     */
    public static function getMemberList($pageNumber, $pageSize, $filters)
    {
        $sql = "SELECT loan.*,client.uid AS member_id,client.obj_guid AS o_guid,client.login_code,client.display_name,client.alias_name,client.phone_country,client.phone_number,client.phone_id,client.email,client.create_time,sb.branch_name
            FROM client_member AS client"
            . " LEFT JOIN loan_account AS loan ON loan.obj_guid = client.obj_guid
            left join site_branch sb on sb.uid=client.branch_id
            WHERE 1 = 1 ";
        if ($filters['search_text']) {
            $search_text = trim($filters['search_text']);
            $sql .= " AND (loan.obj_guid = " . qstr($search_text);
            $sql .= " OR client.login_code LIKE '%" . qstr2($search_text) . "%'";
            $sql .= " OR client.display_name LIKE '%" . qstr2($search_text) . "%'";
            $sql .= " OR client.phone_id LIKE '%" . qstr2($search_text) . "%')";
        }

        if( $filters['branch_id'] ){
            $sql .= " and client.branch_id=".qstr($filters['branch_id']).' ';
        }

        if (intval($filters['ck'])) {
            $sql .= ' AND client.create_time >= "' . date("Y-m-d") . '"';
        }
        if($filters['type']){
            switch($filters['type']){
                case 'all':
                    $sql .= " and client.member_state != " . qstr(memberStateEnum::CANCEL) . " and client.is_verify_phone = 1";
                    break;
                case 'register_today':
                    $sql .= " and to_days(client.create_time) = to_days(now())";
                    break;
                case 'pending_checked':
                    $sql .= " and client.member_state = " . qstr(memberStateEnum::CREATE) . " and client.is_verify_phone = 1";
                    break;
                case 'pending_verify':
                    $sql .= " and client.member_state = " . qstr(memberStateEnum::CHECKED) . " and client.is_verify_phone = 1";
                    break;
                case 'locked':
                    $sql .= " and client.member_state in('" . memberStateEnum::TEMP_LOCKING . "','" . memberStateEnum::SYSTEM_LOCKING . "')  and is_verify_phone = 1";
                    break;
                case 'verified':
                    $sql .= " and client.member_state = " . qstr(memberStateEnum::VERIFIED) . " and client.is_verify_phone = 1";
                    break;
                default:
            }
        }

        $sql .= " ORDER BY client.create_time DESC";
        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 20;
        $r = new ormReader();
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
     * 贷款合同列表
     * @param $pageNumber
     * @param $pageSize
     * @param $filters
     * @return array
     */
    public static function getLoanList($pageNumber, $pageSize, $filters)
    {
        $where = "lc.state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE);

        if (intval($filters['member_id'])) {
            $where .= " AND cm.uid = " . intval($filters['member_id']);
        }

        if (intval($filters['state'])) {
            $where .= " AND lc.state = " . intval($filters['state']);
        }

        if ($filters['currency']) {
            $where .= " AND lc.currency = " . qstr($filters['currency']);
        }

        if ($filters['date_start']) {
            $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
            $where .= " AND lc.start_date >= " . qstr($date_start);
        }

        if ($filters['date_end']) {
            $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
            $where .= " AND lc.start_date <= " . qstr($date_end);
        }

        $sql = <<<SQL
select lc.*,cm.obj_guid,cm.display_name from loan_contract lc
inner join loan_account la on lc.account_id = la.uid
inner join client_member cm on cm.obj_guid = la.obj_guid
where $where
order by lc.uid desc
SQL;

        $r = new ormReader();
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $contract_id_arr = array_column($rows, 'uid');
            $contract_id_str = "(" . implode(',', $contract_id_arr) . ")";
            $sum_field = array(
                'receivable_principal',
                'paid_principal',
                'receivable_interest',
                'paid_interest',
                'receivable_operation_fee',
                'paid_operation_fee',
//                'receivable_admin_fee',
//                'paid_admin_fee',
            );
            $sum_field_arr = array();
            foreach ($sum_field as $field) {
                $sum_field_arr[] = "SUM($field) $field";
            }
            $sum_field_str = implode(', ', $sum_field_arr);
            $sql = "SELECT contract_id, $sum_field_str FROM loan_installment_scheme WHERE contract_id IN $contract_id_str GROUP BY contract_id";
            $list = $r->getRows($sql);
            $list = resetArrayKey($list, 'contract_id');
            foreach ($rows as $key => $row) {
                $contract_id = $row['uid'];
                $row = array_merge($row, $list[$contract_id]);
                $rows[$key] = $row;
            }
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 获取member详情
     * @param $p
     * @return array
     */
    public static function getMemberDetail($p)
    {
        $search_by = trim($p['search_by']);
        if ($search_by == "1") {
            $ret = memberClass::searchMember(array(
                "type" => '2',
                "country_code" => $p['country_code'],
                "phone_number" => $p['phone_number']
            ));
        } elseif ($search_by == "2") {
            $ret = memberClass::searchMember(array(
                "type" => '1',
                "guid" => $p['phone_number']
            ));
        } elseif ($search_by == "3") {
            $ret = memberClass::searchMember(array(
                "type" => '3',
                "login_code" => $p['phone_number']
            ));
        } else {
            $ret = memberClass::searchMember(array(
                "type" => '4',
                "display_name" => $p['phone_number']
            ));
        }

        $member_id = intval($ret['uid']);
        if (!$member_id) {
            return array();
        }

        $r = new ormReader();
        $data = memberClass::getMemberBaseInfo($member_id);
        if (!$data) {
            return array();
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

        //Residence
        $m_common_address = M('common_address');
        $residence = $m_common_address->getMemberResidencePlaceByGuid($data['obj_guid']);
        $data['residence'] = $residence;

        $expire_time = M('member_credit')->orderBy('uid DESC')->find(array('member_id' => $member_id));
        $data['expire_time'] = $expire_time['expire_time'];

        $loan_principal = member_statisticsClass::getLoanTotalGroupByCurrency($member_id);
        if (!$loan_principal->STS) {
            return new result(false, $loan_principal->MSG);
        }
        $data['loan_principal'] = $loan_principal->DATA;

        $outstanding_principal = member_statisticsClass::getMemberTotalPayableLoanPrincipalGroupByCurrency($member_id);
        $data['outstanding_principal'] = $outstanding_principal;

        $loan_account = M('loan_account')->find(array('obj_guid' => $data['obj_guid']));
        $loan_uid = intval($loan_account['uid']);

        //*这是request loan的次数
        $sql = "SELECT count(uid) as count from loan_apply where member_id = " . $member_id;
        $loan_count = $r->getOne($sql);
        $contract_info = array();
        $contract_info['all_enquiries'] = $loan_count;

        //*这是第一次发放贷款的日期
        $sql = "select create_time from loan_contract where account_id = " . $loan_uid . " and state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE) . " order by uid asc";
        $create_time = $r->getOne($sql);
        $contract_info['earliest_loan_issue_date'] = $create_time;
        $loan_summary = memberClass::getMemberLoanSummary($member_id, 1, 7);

        $return_data = array();
        $return_data['contract_info'] = $contract_info;
        $return_data['loan_summary'] = $loan_summary->DATA;

        $credit_info = memberClass::getCreditBalance($member_id);

        $return_data['detail'] = $data;
        $return_data['credit_info'] = $credit_info;

        // 储蓄账户余额
        $memberObject = new objectMemberClass($member_id);
        $savings_balance = $memberObject->getSavingsAccountBalance();
        $return_data['savings_balance'] = $savings_balance;
        return $return_data;
    }

    /**
     * @param $member_id
     * @return array
     */
    public static function getMemberRegisterBy($member_id)
    {
        $member_id = intval($member_id);
        if (!$member_id) {
            return array();
        }

        $data = memberClass::getMemberBaseInfo($member_id);
        return $data;
    }

    /**
     * 获取会员商业收入调查
     * @param $member_id
     * @return array
     */
    public static function getMemberBusiness($member_id)
    {
        $member_id = intval($member_id);
        $co_list = memberClass::getMemberCreditOfficerList($member_id);
        $co_list = resetArrayKey($co_list, "officer_id");

        $member_industry_info = memberClass::getMemberIndustryInfo($member_id);
        $m_common_industry = new common_industryModel();
        foreach ($member_industry_info as $key => $val) {
            $industry_info = $m_common_industry->getIndustryInfo($val['uid']);
            $member_industry_info[$key] = $industry_info;
        }
        $business = array();
        $business['member_industry_info'] = $member_industry_info;

        $co_research = array();
        foreach ($co_list as $co) {
            $business_research = credit_researchClass::getMemberBusinessResearch($member_id, $co['officer_id']);
            $co_research[$co['officer_id']] = resetArrayKey($business_research, 'industry_id');
        }

        $r = new ormReader();
        $sql = "SELECT * FROM member_income_business WHERE uid IN (SELECT MAX(uid) FROM member_income_business WHERE member_id = $member_id AND operator_type = 1 GROUP BY industry_id)";
        $bm_research = $r->getRows($sql);
        $business_research_operator = $co_list;
        if ($bm_research) {
            $first_bm_research = reset($bm_research);
            array_unshift($business_research_operator, array('officer_id' => $first_bm_research['operator_id'], 'officer_name' => $first_bm_research['operator_name']));
            $co_research[$first_bm_research['operator_id']] = resetArrayKey($bm_research, 'industry_id');
        }

        $business['business_research_operator'] = $business_research_operator;
        $business['co_research'] = $co_research;

        $m_member_income_business_image = M('member_income_business_image');
        $business['business_image'] = $m_member_income_business_image->getImagesGroupIndustryByMemberId($member_id);

        return $business;
    }

    /**
     * 获取会员工作收入调查
     * @param $member_id
     * @return array
     */
    public static function getMemberSalary($member_id)
    {
        $member_id = intval($member_id);
        $salary_income = credit_researchClass::getOfficerLastSubmitMemberSalaryResearch($member_id);
        return $salary_income;
    }

    /**
     * 获取会员额外收入
     * @param $member_id
     * @return array|null
     */
    public static function getMemberAttachment($member_id)
    {
        $member_id = intval($member_id);
        $attachment = credit_researchClass::getMemberAttachmentList($member_id);
        return $attachment;
    }

    public static function getMemberIdentity($member_id)
    {
        $member_id = intval($member_id);
        $identity_type = memberIdentityClass::getIdentityType();
        $at_list = array_keys($identity_type);
        $cert_type = '(' . implode(',', $at_list) . ')';
        $sql = "SELECT * FROM member_verify_cert WHERE uid IN (SELECT MAX(uid) FROM member_verify_cert WHERE member_id = $member_id AND cert_type IN $cert_type AND verify_state = " . certStateEnum::PASS . " GROUP BY cert_type)";
        $r = new ormReader();
        $check_list = $r->getRows($sql);
        $check_list = resetArrayKey($check_list, 'cert_type');
        $m_member_verify_cert_image = M('member_verify_cert_image');
        foreach ($check_list as $key => $val) {
            $val['images'] = $m_member_verify_cert_image->select(array('cert_id' => $val['uid']));
            $check_list[$key] = $val;
        }
        return $check_list;
    }

    /**
     * 获取会员资产
     * @param $member_id
     * @return array
     */
    public static function getMemberAssets($member_id)
    {
        $member_id = intval($member_id);
        $r = new ormReader();
        $m_member_assets = M('member_assets');
        $m_member_verify_cert_image = M('member_verify_cert_image');
        $assets_arr = array();
        $assets = $m_member_assets->orderBy('asset_type ASC')->select(array('member_id' => $member_id, 'asset_state' => array('>=', assetStateEnum::CERTIFIED)));
        $assets_arr['assets'] = $assets;

        $assets_group = array();
        foreach ($assets as $asset) {
            $asset['images'] = $m_member_verify_cert_image->select(array('cert_id' => $asset['cert_id']));
            $assets_group[$asset['asset_type']][] = $asset;
        }
        $assets_arr['assets_group'] = $assets_group;

        //assets evaluate
        $sql = "SELECT * FROM member_assets_evaluate WHERE uid IN (SELECT MAX(uid) FROM member_assets_evaluate WHERE member_id = $member_id AND evaluator_type = 1 GROUP BY member_assets_id)";
        $assets_evaluate_list = $r->getRows($sql);
        $assets_arr['assets_evaluate_list'] = resetArrayKey($assets_evaluate_list, 'member_assets_id');

        $rental_research = credit_researchClass::getMemberRentalResearch($member_id);
        $assets_arr['rental_research'] = $rental_research;
        return $assets_arr;
    }

    /**
     * 会员信用变化日志
     * @param $pageNumber
     * @param $pageSize
     * @param $filters
     * @return array
     */
    public static function getMemberCreditLogList($pageNumber, $pageSize, $filters)
    {
        $where = " WHERE 1 = 1";
        if (intval($filters['member_id'])) {
            $where .= " AND member_id = " . intval($filters['member_id']);
        }

        if ($filters['date_start']) {
            $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
            $where .= " AND create_time >= " . qstr($date_start);
        }

        if ($filters['date_end']) {
            $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
            $where .= " AND create_time <= " . qstr($date_end);
        }

        $r = new ormreader();
        $sql = "SELECT * FROM member_credit_log $where";
        $sql .= " ORDER BY uid DESC";
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
     * 获取授权记录
     * @param $pageNumber
     * @param $pageSize
     * @param $filters
     * @return array
     */
    public static function getMemberCreditAgreementList($pageNumber, $pageSize, $filters)
    {
        $where = " WHERE 1 = 1";
        if (intval($filters['member_id'])) {
            $where .= " AND mac.member_id = " . intval($filters['member_id']);
        }

        if ($filters['date_start']) {
            $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
            $where .= " AND mac.create_time >= " . qstr($date_start);
        }

        if ($filters['date_end']) {
            $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
            $where .= " AND mac.create_time <= " . qstr($date_end);
        }

        $r = new ormreader();
        $sql = "SELECT mac.*,sb.branch_name FROM member_authorized_contract mac LEFT JOIN site_branch sb ON mac.branch_id = sb.uid $where";
        $sql .= " ORDER BY uid DESC";
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
     * 抵押物列表
     * @param $pageNumber
     * @param $pageSize
     * @param $filters
     * @return array
     */
    public static function getClientCreditMortgageList($pageNumber, $pageSize, $filters)
    {
        $member_id = intval($filters['member_id']);
        $r = new ormReader();
        $sql = "SELECT a.*,b.asset_type,b.asset_name,b.asset_sn FROM member_assets_storage a "
            . " INNER JOIN member_assets b ON a.member_asset_id = b.uid"
            . " WHERE a.is_history = 0 and a.is_pending = 0 AND a.flow_type < 20 AND b.member_id = " . $member_id;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        if ($rows) {
            $asset_ids = array_column($rows, 'member_asset_id');
            $asset_id_str = "(" . implode(',', $asset_ids) . ")";
            $sql = "SELECT * FROM member_assets_evaluate WHERE uid IN (SELECT MAX(uid) FROM member_assets_evaluate WHERE member_assets_id IN $asset_id_str AND evaluator_type = 1 GROUP BY member_assets_id)";
            $assets_evaluate = $r->getRows($sql);
            $assets_evaluate = resetArrayKey($assets_evaluate, 'member_assets_id');
            foreach ($rows as $key => $row) {
                $assets_id = $row['member_asset_id'];
                $row['evaluation'] = $assets_evaluate[$assets_id]['evaluation'];
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
        );
    }

    /**
     * 获取cbc数据
     * @param $member_id
     * @return mixed
     */
    public static function getClientCbc($member_id)
    {
        $m_client_cbc = M('client_cbc');
        $client_cbc = $m_client_cbc->orderBy('uid DESC')->find(array('client_id' => $member_id, "client_type" => 0, 'state' => 1));
        return $client_cbc;
    }

    /**
     * 会员信用变化日志
     * @param $pageNumber
     * @param $pageSize
     * @param $filters
     * @return array
     */
    public static function getClientChangeStateLogList($pageNumber, $pageSize, $filters)
    {
        $where = " WHERE 1 = 1";
        if (intval($filters['member_id'])) {
            $where .= " AND member_id = " . intval($filters['member_id']);
        }

        if ($filters['date_start']) {
            $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
            $where .= " AND create_time >= " . qstr($date_start);
        }

        if ($filters['date_end']) {
            $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
            $where .= " AND create_time <= " . qstr($date_end);
        }

        $r = new ormreader();
        $sql = "SELECT * FROM member_state_log $where";
        $sql .= " ORDER BY uid DESC";
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