<?php

class loanDisbursementClass
{
    /**
     * co list下member情况
     * @param $pageNumber
     * @param $pageSize
     * @param $filters
     * @return array
     */
    public static function getDisbursementList($pageNumber, $pageSize, $filters = array())
    {
        $r = new ormReader();
        $sql = "SELECT uu.*,sb.branch_name FROM um_user uu"
            . " INNER JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " INNER JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE uu.user_status = 1 AND uu.user_position = " . qstr(userPositionEnum::CREDIT_OFFICER);
        if (intval($filters['branch_id'])) {
            $sql .= " AND sb.uid = " . intval($filters['branch_id']);
        }
        if (trim($filters['search_text'])) {
            $search_text = qstr2(trim($filters['search_text']));
            $sql .= " AND (uu.user_code like '%" . $search_text . "%' OR uu.user_name like '%" . $search_text . "%')";
        }

        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        $currency_list = (new currencyEnum())->Dictionary();
        if ($rows) {
            $rows = resetArrayKey($rows, 'uid');
            $co_ids = array_column($rows, 'uid');
            $co_id_str = '(' . implode(',', $co_ids) . ')';

            $sql = "SELECT * FROM member_follow_officer WHERE officer_id IN $co_id_str AND is_active = 1";
            $member_list = $r->getRows($sql);
            if ($member_list) {
                $member_ids = array_column($member_list, 'member_id');
                $member_ids = array_unique($member_ids);
                $member_id_str = '(' . implode(',', $member_ids) . ')';

                $sum_sql_arr = array();
                foreach ($currency_list as $key => $currency) {
                    $sum_sql_arr[] = "SUM(CASE lc.currency WHEN '$key' THEN lds.principal ELSE 0 END) loan_amount_$key";
                }
                $sum_sql = implode(',', $sum_sql_arr);

                $where = " WHERE cm.uid IN $member_id_str AND lc.state > " . qstr(loanContractStateEnum::PENDING_APPROVAL);
                $where .= " AND lds.state = " . qstr(disbursementStateEnum::DONE);
                if ($filters['date_start']) {
                    $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
                    $where .= " AND lds.execute_time >= " . qstr($date_start);
                }
                if ($filters['date_end']) {
                    $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
                    $where .= " AND lds.execute_time <= " . qstr($date_end);
                }
                $sql_1 = "SELECT cm.uid,$sum_sql"
                    . " FROM loan_disbursement_scheme lds"
                    . " INNER JOIN loan_contract lc ON lds.contract_id = lc.uid"
                    . " INNER JOIN loan_account la ON lc.account_id = la.uid"
                    . " INNER JOIN client_member cm ON cm.obj_guid = la.obj_guid"
                    . " $where GROUP BY cm.uid";
                $loan_amount_list = $r->getRows($sql_1);
                $loan_amount_list = resetArrayKey($loan_amount_list, 'uid');

                $where = " WHERE cm.uid IN $member_id_str AND lc.state > " . qstr(loanContractStateEnum::PENDING_APPROVAL);
                if ($filters['date_start']) {
                    $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
                    $where .= " AND lc.start_date >= " . qstr($date_start);
                }
                if ($filters['date_end']) {
                    $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
                    $where .= " AND lc.start_date <= " . qstr($date_end);
                }
                $sql_2 = "SELECT cm.uid,count(lc.uid) loan_count"
                    . " FROM loan_contract lc"
                    . " INNER JOIN loan_account la ON lc.account_id = la.uid"
                    . " INNER JOIN client_member cm ON cm.obj_guid = la.obj_guid"
                    . " $where GROUP BY cm.uid";
                $loan_count_list = $r->getRows($sql_2);
                $loan_count_list = resetArrayKey($loan_count_list, 'uid');

                $sql_3 = "SELECT cm.uid,MAX(loan_actual_cycle) loan_actual_cycle"
                    . " FROM loan_contract lc"
                    . " INNER JOIN loan_account la ON lc.account_id = la.uid"
                    . " INNER JOIN client_member cm ON cm.obj_guid = la.obj_guid"
                    . " WHERE cm.uid IN $member_id_str AND lc.state > " . qstr(loanContractStateEnum::PENDING_APPROVAL)
                    . " GROUP BY cm.uid";

                $loan_actual_cycle = $r->getRows($sql_3);
                $loan_actual_cycle = resetArrayKey($loan_actual_cycle, 'uid');

                $total_amount = array();
                foreach ($member_list as $member) {
                    $co_id = $member['officer_id'];
                    $member_id = $member['member_id'];
                    $loan_count = intval($loan_count_list[$member_id]['loan_count']);
                    if (intval($loan_actual_cycle[$member_id]['loan_actual_cycle']) > 1) {
                        $rows[$co_id]['repeat_member']['loan_count'] = intval($rows[$co_id]['repeat_member']['loan_count']) + $loan_count;
                        $total_amount['repeat_member']['loan_count'] = intval($total_amount['repeat_member']['loan_count']) + $loan_count;
                        foreach ($currency_list as $c_k => $c_v) {
                            $rows[$co_id]['repeat_member']['loan_amount_' . $c_k] = round($rows[$co_id]['repeat_member']['loan_amount_' . $c_k], 2) + round($loan_amount_list[$member_id]['loan_amount_' . $c_k]);
                            $total_amount['repeat_member']['loan_amount_' . $c_k] = round($total_amount['repeat_member']['loan_amount_' . $c_k], 2) + round($loan_amount_list[$member_id]['loan_amount_' . $c_k]);
                        }
                    } else {
                        $rows[$co_id]['new_member']['loan_count'] = intval($rows[$co_id]['new_member']['loan_count']) + $loan_count;
                        $total_amount['new_member']['loan_count'] = intval($total_amount['new_member']['loan_count']) + $loan_count;
                        foreach ($currency_list as $c_k => $c_v) {
                            $rows[$co_id]['new_member']['loan_amount_' . $c_k] = round($rows[$co_id]['new_member']['loan_amount_' . $c_k], 2) + round($loan_amount_list[$member_id]['loan_amount_' . $c_k]);
                            $total_amount['new_member']['loan_amount_' . $c_k] = round($total_amount['new_member']['loan_amount_' . $c_k], 2) + round($loan_amount_list[$member_id]['loan_amount_' . $c_k]);
                        }
                    }
                    $rows[$co_id]['total_amount']['loan_count'] = intval($rows[$co_id]['total_amount']['loan_count']) + $loan_count;
                    $total_amount['total_amount']['loan_count'] = intval($total_amount['total_amount']['loan_count']) + $loan_count;
                    foreach ($currency_list as $c_k => $c_v) {
                        $rows[$co_id]['total_amount']['loan_amount_' . $c_k] = round($rows[$co_id]['total_amount']['loan_amount_' . $c_k], 2) + round($loan_amount_list[$member_id]['loan_amount_' . $c_k]);
                        $total_amount['total_amount']['loan_amount_' . $c_k] = round($total_amount['total_amount']['loan_amount_' . $c_k], 2) + round($loan_amount_list[$member_id]['loan_amount_' . $c_k]);
                    }
                }
            }
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize,
            "total_amount" => $total_amount,
            "currency_list" => $currency_list
        );
    }

    /**
     * 单个co下member情况
     * @param $co_id
     * @param $filters
     * @return array
     */
    public static function getDisbursementClientLoanListByCoId($co_id, $filters = array())
    {
        $co_id = intval($co_id);
        $m_um_user = M('um_user');
        $co_info = $m_um_user->find(array('uid' => $co_id, 'user_position' => userPositionEnum::CREDIT_OFFICER));
        if (!$co_info) {
            return array();
        }

        $r = new ormReader();
        $sql_1 = "SELECT cm.uid,MAX(loan_actual_cycle) loan_actual_cycle FROM loan_contract lc"
            . " INNER JOIN loan_account la ON lc.account_id = la.uid"
            . " INNER JOIN client_member cm ON cm.obj_guid = la.obj_guid"
            . " INNER JOIN member_follow_officer mfo ON mfo.member_id = cm.uid"
            . " WHERE mfo.officer_id = $co_id AND mfo.is_active = 1 AND lc.state > " . qstr(loanContractStateEnum::PENDING_APPROVAL)
            . " GROUP BY cm.uid";
        $loan_actual_cycle = $r->getRows($sql_1);
        if (!$loan_actual_cycle) {//无贷款记录
            return array(
                'co_info' => $co_info
            );
        }

        $client_ids = array_column($loan_actual_cycle, 'uid');

        $new_client = array();
        $repeat_client = array();
        foreach ($loan_actual_cycle as $val) {
            if ($val['loan_actual_cycle'] > 1) {
                $repeat_client[] = $val['uid'];
            } else {
                $new_client[] = $val['uid'];
            }
        }

        $currency_list = (new currencyEnum())->Dictionary();
        $client_address = self::getMemberAddress($client_ids);
        $new_client_loan = self::getLoanListByMemberIds($new_client, $filters, $client_address);
        $repeat_client_loan = self::getLoanListByMemberIds($repeat_client, $filters, $client_address);
        $total_amount = array();
        $total_amount['loan_count'] = intval($new_client_loan['loan_total']['loan_count']) + intval($repeat_client_loan['loan_total']['loan_count']);
        $total_amount['gender_m'] = intval($new_client_loan['loan_total']['gender_m']) + intval($repeat_client_loan['loan_total']['gender_m']);
        $total_amount['gender_f'] = intval($new_client_loan['loan_total']['gender_f']) + intval($repeat_client_loan['loan_total']['gender_f']);
        foreach ($currency_list as $c_k => $c_v) {
            $total_amount['amount_' . $c_k] = round($new_client_loan['loan_total']['amount_' . $c_k], 2) + round($repeat_client_loan['loan_total']['amount_' . $c_k], 2);
        }
        return array(
            'co_info' => $co_info,
            'new_client_loan' => $new_client_loan,
            'repeat_client_loan' => $repeat_client_loan,
            'total_amount' => $total_amount,
            'currency_list' => $currency_list
        );
    }

    private static function getLoanListByMemberIds($member_ids, $filters, $client_address)
    {
        if (empty($member_ids)) {
            return array();
        }
        $client_id_str = "(" . implode(',', $member_ids) . ")";
        $where = "WHERE cm.uid IN $client_id_str AND lc.state > " . qstr(loanContractStateEnum::PENDING_APPROVAL);
        if ($filters['date_start']) {
            $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
            $where .= " AND lc.start_date >= " . qstr($date_start);
        }
        if ($filters['date_end']) {
            $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
            $where .= " AND lc.start_date <= " . qstr($date_end);
        }
        $sql_1 = "SELECT lc.*,cm.uid member_id,cm.display_name,cm.login_code,cm.gender FROM loan_contract lc"
            . " INNER JOIN loan_account la ON lc.account_id = la.uid"
            . " INNER JOIN client_member cm ON cm.obj_guid = la.obj_guid"
            . " $where ORDER BY lc.uid DESC";
        $r = new ormReader();
        $loan_list = $r->getRows($sql_1);

        $loan_total = array();
        if ($loan_list) {
            $contract_ids = array_column($loan_list, 'uid');
            $contract_id_str = "(" . implode(',', $contract_ids) . ")";
            $sql_2 = "SELECT * FROM loan_disbursement_scheme WHERE uid IN"
                . " (SELECT MAX(uid) FROM loan_disbursement_scheme WHERE contract_id IN $contract_id_str AND state = " . disbursementStateEnum::DONE . " GROUP BY contract_id)";
            $disbursement_scheme = $r->getRows($sql_2);
            $disbursement_scheme = resetArrayKey($disbursement_scheme, 'contract_id');
            $loan_total['loan_count'] = count($loan_list);
            foreach ($loan_list as $key => $loan) {
                $loan['disburse_date'] = $disbursement_scheme[$key]['execute_time'];
                $loan['address'] = $client_address[$loan['member_id']];
                $loan_total['amount_' . $loan['currency']] = round($loan_total['amount_' . $loan['currency']], 2) + $loan['apply_amount'];
                if ($loan['gender'] == memberGenderEnum::MALE) {
                    $loan_total['gender_m'] = intval($loan_total['gender_m']) + 1;
                }
                if ($loan['gender'] == memberGenderEnum::FEMALE) {
                    $loan_total['gender_f'] = intval($loan_total['gender_f']) + 1;
                }
                $loan_list[$key] = $loan;
            }
        }
        return array(
            'loan_total' => $loan_total,
            'loan_list' => $loan_list
        );
    }

    private static function getMemberAddress($member_ids)
    {
        if (!$member_ids) {
            return array();
        }

        $member_id_str = "(" . implode(',', $member_ids) . ")";
        $where = "WHERE cm.uid IN $member_id_str AND ca.state = 1 AND ca.address_category = " . qstr(addressCategoryEnum::MEMBER_RESIDENCE_PLACE);

        $r = new ormReader();
        $sql_2 = "SELECT cm.uid,ct.node_text FROM common_address ca"
            . " INNER JOIN client_member cm ON cm.obj_guid = ca.obj_guid"
            . " LEFT JOIN core_tree ct ON ca.id2 = ct.uid $where";
        $id2_list = $r->getRows($sql_2);
        $id2_list = resetArrayKey($id2_list, 'uid');

        $sql_3 = "SELECT cm.uid,ct.node_text FROM common_address ca"
            . " INNER JOIN client_member cm ON cm.obj_guid = ca.obj_guid"
            . " LEFT JOIN core_tree ct ON ca.id3 = ct.uid $where";
        $id3_list = $r->getRows($sql_3);
        $id3_list = resetArrayKey($id3_list, 'uid');

        $sql_4 = "SELECT cm.uid,ct.node_text FROM common_address ca"
            . " INNER JOIN client_member cm ON cm.obj_guid = ca.obj_guid"
            . " LEFT JOIN core_tree ct ON ca.id4 = ct.uid $where";
        $id4_list = $r->getRows($sql_4);
        $id4_list = resetArrayKey($id4_list, 'uid');

        $address_arr = array();
        foreach ($member_ids as $member_id) {
            $address_arr[$member_id] = array(
                'addr2' => $id2_list[$member_id]['node_text'],
                'addr3' => $id3_list[$member_id]['node_text'],
                'addr4' => $id4_list[$member_id]['node_text'],
            );
        }

        return $address_arr;
    }


}