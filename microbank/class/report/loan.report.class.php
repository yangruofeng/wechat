<?php

class loanReportClass
{
    private static $rate = array(
        'standard' => 0,
        'substandard' => 10,
        'doubtful' => 30,
        'loss' => 100,
        'Regular/Current' => 0
    );

    /**
     * 贷款合同列表
     * @param $pageNumber
     * @param $pageSize
     * @param $filters
     * @return array
     */
    public static function getLoanList($pageNumber, $pageSize, $filters)
    {
        $where = "lc.state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE) . " AND lc.state <= " . qstr(loanContractStateEnum::COMPLETE);
        if ($filters['currency']) {
            $where .= " AND lc.currency = " . qstr($filters['currency']);
        }
        if ($filters['search_text']) {
            $where .= " AND (lc.virtual_contract_sn = " . qstr($filters['search_text']);
            $where .= " OR cm.obj_guid = " . qstr($filters['search_text']);
            $where .= " OR cm.display_name LIKE '%" . qstr2($filters['search_text']) . "%')";
        }
        if ($filters['date_start']) {
            $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
            $where .= " AND lc.start_date >= " . qstr($date_start);
        }

        if ($filters['date_end']) {
            $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
            $where .= " AND lc.start_date <= " . qstr($date_end);
        }
        if ($filters['branch_id']) {
            $where .= " and lc.branch_id=" . qstr($filters['branch_id']);
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
            $member_obj_guid_arr = array_unique(array_column($rows, 'obj_guid'));
            $member_obj_guid_str = '(' . implode(',', $member_obj_guid_arr) . ')';
            $sql_address = "SELECT * FROM common_address WHERE obj_guid IN $member_obj_guid_str AND state = 1 AND address_category = " . qstr(addressCategoryEnum::MEMBER_RESIDENCE_PLACE);
            $member_address = $r->getRows($sql_address);
            $member_address = resetArrayKey($member_address, 'obj_guid');

            $m_loan_installment_scheme = M('loan_installment_scheme');
            $loan_count = 0;
            $principal_disbursement_total = 0;
            $principal_current_total = 0;
            $overdue_balance_total = 0;
            $overdue_interest_total = 0;
            $paid_principal_total = 0;
            $paid_interest_total = 0;

            $m_core_tree = M('core_tree');
            $address_arr = $m_core_tree->select(array('root_key' => 'region'));
            $address_arr = resetArrayKey($address_arr, 'uid');
            foreach ($rows as $key => $row) {
                if ($member_address[$row['obj_guid']]) {
                    $row['member_full_address'] = $member_address[$row['obj_guid']]['full_text'];
                    $row['id1'] = $address_arr[$member_address[$row['obj_guid']]['id1']]['node_text'];
                    $row['id2'] = $address_arr[$member_address[$row['obj_guid']]['id2']]['node_text'];
                    $row['id3'] = $address_arr[$member_address[$row['obj_guid']]['id3']]['node_text'];
                    $row['id4'] = $address_arr[$member_address[$row['obj_guid']]['id4']]['node_text'];
                    $row['group'] = $member_address[$row['obj_guid']]['address_group'];
                    $row['street'] = $member_address[$row['obj_guid']]['street'];
                    $row['house_number'] = $member_address[$row['obj_guid']]['house_number'];
                }

                $installment_scheme = $m_loan_installment_scheme->select(array('contract_id' => $row['uid']));
                $overdue_balance = 0;
                $overdue_interest = 0;
                $last_transaction = '';
                $last_transaction_date = '';
                $paid_principal = 0;
                $paid_interest = 0;
                foreach ($installment_scheme as $scheme) {
                    if (time() >= strtotime($scheme['receivable_date']) && $scheme['state'] >= schemaStateTypeEnum::CREATE && $scheme['state'] < schemaStateTypeEnum::COMPLETE) {
                        $overdue_balance += $scheme['receivable_principal'];
                        $overdue_interest += $scheme['receivable_interest'] + $scheme['receivable_operation_fee'] + $scheme['receivable_admin_fee'];
                    }
                    if ($scheme['state'] == schemaStateTypeEnum::COMPLETE) {
                        $last_transaction = $scheme['actual_payment_amount'];
                        $last_transaction_date = $scheme['execute_time'];
                        $paid_principal += $scheme['receivable_principal'];
                        $paid_interest += $scheme['receivable_interest'] + $scheme['receivable_operation_fee'] + $scheme['receivable_admin_fee'];
                    }
                }
                $row['overdue_balance'] = $overdue_balance;
                $row['overdue_interest'] = $overdue_interest;
                $row['last_transaction'] = $last_transaction;
                $row['last_transaction_date'] = $last_transaction_date;
                $row['paid_principal'] = $paid_principal;
                $row['paid_interest'] = $paid_interest;
                $row['current_balance'] = $row['apply_amount'] - $row['paid_principal'];

                ++$loan_count;
                $principal_disbursement_total += $row['apply_amount'];
                $principal_current_total += $row['current_balance'];
                $overdue_balance_total += $overdue_balance;
                $overdue_interest_total += $overdue_interest;
                $paid_principal_total += $paid_principal;
                $paid_interest_total += $paid_interest;

                $rows[$key] = $row;
            }
        }
        $loan_total = array(
            'loan_count' => $loan_count,
            'principal_disbursement_total' => $principal_disbursement_total,
            'principal_current_total' => $principal_current_total,
            'overdue_balance_total' => $overdue_balance_total,
            'overdue_interest_total' => $overdue_interest_total,
            'paid_principal_total' => $paid_principal_total,
            'paid_interest_total' => $paid_interest_total,
        );

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize,
            "loan_total" => $loan_total
        );
    }

    public static function getLoanSummary($filters)
    {
        $sql_filter = "";
        if ($filters['branch_id']) {
            $sql_filter .= " and lc.branch_id=" . qstr($filters['branch_id']);
        }
        $r = new ormReader();
        $sql = "SELECT state,COUNT(uid) loan_count FROM loan_contract lc"
            . " WHERE state > " . qstr(loanContractStateEnum::PENDING_APPROVAL) . $sql_filter
            . " GROUP BY state ORDER BY state ASC";
        $loan_summary = $r->getRows($sql);
        $loan_summary = resetArrayKey($loan_summary, 'state');

        $loan_count_total = 0;
        $principal_disbursed_total = array();
        $current_balance_total = array();
        $overdue_balance_total = array();
        foreach ($loan_summary as $key => $val) {
            $state = $val['state'];
            $loan_count_total += $val['loan_count'];
            if ($state >= loanContractStateEnum::PENDING_DISBURSE) {
                $sql_1 = "SELECT lc.currency,SUM(principal) principal_disbursed FROM loan_disbursement_scheme lds INNER JOIN loan_contract lc ON lds.contract_id = lc.uid"
                    . " WHERE lc.state = " . qstr($state) . " AND lds.state = " . qstr(disbursementStateEnum::DONE) . $sql_filter
                    . " GROUP BY lc.currency";
                $principal_disbursed = $r->getRows($sql_1);
                foreach ($principal_disbursed as $row) {
                    $val['principal_disbursed'][$row['currency']] = $row['principal_disbursed'];
                    $principal_disbursed_total[$row['currency']] = round($principal_disbursed_total[$row['currency']], 2) + $row['principal_disbursed'];
                }


                $sql_2 = "SELECT lc.currency,SUM(amount) current_balance FROM loan_installment_scheme lis INNER JOIN loan_contract lc ON lis.contract_id = lc.uid"
                    . " WHERE lc.state = " . qstr($state) . " AND lis.state < " . qstr(disbursementStateEnum::DONE) . $sql_filter
                    . " GROUP BY lc.currency";
                $current_balance = $r->getRows($sql_2);
                foreach ($current_balance as $row) {
                    $val['current_balance'][$row['currency']] = $row['current_balance'];
                    $current_balance_total[$row['currency']] = round($current_balance_total[$row['currency']], 2) + $row['current_balance'];
                }

                $sql_2 = "SELECT lc.currency,SUM(amount) overdue_balance FROM loan_installment_scheme lis INNER JOIN loan_contract lc ON lis.contract_id = lc.uid"
                    . " WHERE lc.state = " . qstr($state) . " AND lis.state < " . qstr(disbursementStateEnum::DONE) . " AND lis.receivable_date < " . qstr(date('Y-m-d 00:00:00')) . $sql_filter
                    . " GROUP BY lc.currency";
                $overdue_balance = $r->getRows($sql_2);
                foreach ($overdue_balance as $row) {
                    $val['overdue_balance'][$row['currency']] = $row['overdue_balance'];
                    $overdue_balance_total[$row['currency']] = round($overdue_balance_total[$row['currency']], 2) + $row['overdue_balance'];
                }

                $loan_summary[$key] = $val;
            }
        }

        $data = array(
            'loan_summary' => $loan_summary,
            'loan_count_total' => $loan_count_total,
            'principal_disbursed_total' => $principal_disbursed_total,
            'current_balance_total' => $current_balance_total,
            'overdue_balance_total' => $overdue_balance_total,
        );
        return $data;
    }

    public static function getLoanInterestRateList($filters)
    {
        $r = new ormReader();
        $where = "WHERE interest_rate_type = 0 AND state > " . qstr(loanContractStateEnum::PENDING_APPROVAL);
        if ($filters['date_start']) {
            $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
            $where .= " AND start_date >= " . qstr($date_start);
        }

        if ($filters['date_end']) {
            $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
            $where .= " AND start_date <= " . qstr($date_end);
        }
        if ($filters['branch_id']) {
            $where .= " AND branch_id = " . qstr($filters['branch_id']);
        }

        $interest_bracket = array(
            'bracket_1' => array('max' => 0.00),
            'bracket_2' => array('min' => 0.00, 'max' => 5.00),
            'bracket_3' => array('min' => 5.00, 'max' => 10.00),
            'bracket_4' => array('min' => 10.00, 'max' => 12.00),
            'bracket_5' => array('min' => 15.00, 'max' => 18.00),
            'bracket_6' => array('min' => 18.00, 'max' => 20.00),
            'bracket_7' => array('min' => 20.00),
        );

        $currency_list = (new currencyEnum())->Dictionary();
        $sum_sql_arr = array();
        foreach ($currency_list as $key => $currency) {
            $sum_sql_arr[] = "SUM(CASE currency WHEN '$key' THEN apply_amount ELSE 0 END) loan_amount_$key";
        }
        $sum_sql = implode(',', $sum_sql_arr);


        $sql = "SELECT COUNT(uid) loan_count,$sum_sql FROM loan_contract $where";

        $amount_total = array();
        foreach ($interest_bracket as $k => $bracket) {
            $min = $bracket['min'];
            $max = $bracket['max'];
            $sql1 = $sql;
            if (isset($min)) {
                $sql1 .= " AND interest_rate > $min";
            }
            if (isset($max)) {
                $sql1 .= " AND interest_rate <= $max";
            }
            $row = $r->getRow($sql1);
            $interest_bracket[$k]['report'] = $row;
            $amount_total['loan_count'] = intval($amount_total['loan_count']) + intval($row['loan_count']);
            foreach ($currency_list as $key => $currency) {
                $amount_total['loan_amount_' . $key] = round($amount_total['loan_amount_' . $key], 2) + round($row['loan_amount_' . $key], 2);
            }

        }
        return array(
            'data' => $interest_bracket,
            'amount_total' => $amount_total,
            'currency_list' => $currency_list,
        );
    }

    public static function getLoanSizeList($filters)
    {
        $r = new ormReader();
        $currency = $filters['currency'] ?: currencyEnum::USD;
        $where = "WHERE state > " . qstr(loanContractStateEnum::PENDING_APPROVAL) . " AND currency = " . qstr($currency);
        if ($filters['date_start']) {
            $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
            $where .= " AND start_date >= " . qstr($date_start);
        }

        if ($filters['date_end']) {
            $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
            $where .= " AND start_date <= " . qstr($date_end);
        }
        if ($filters['branch_id']) {
            $where .= " AND branch_id=" . qstr($filters['branch_id']);
        }

        if ($currency == currencyEnum::USD) {
            $interest_bracket = array(
                'bracket_1' => array('min' => 0.00, 'max' => 100.00),
                'bracket_2' => array('min' => 100.00, 'max' => 500.00),
                'bracket_3' => array('min' => 500.00, 'max' => 1000.00),
                'bracket_4' => array('min' => 1000.00, 'max' => 10000.00),
                'bracket_5' => array('min' => 10000.00, 'max' => 20000.00),
                'bracket_6' => array('min' => 20000.00),
            );
        } elseif ($currency == currencyEnum::KHR) {
            $interest_bracket = array(
                'bracket_1' => array('min' => 0.00, 'max' => 400000.00),
                'bracket_2' => array('min' => 400000.00, 'max' => 2000000.00),
                'bracket_3' => array('min' => 2000000.00, 'max' => 4000000.00),
                'bracket_4' => array('min' => 4000000.00, 'max' => 40000000.00),
                'bracket_5' => array('min' => 40000000.00, 'max' => 80000000.00),
                'bracket_6' => array('min' => 40000000.00),
            );
        }


        $sql = "SELECT COUNT(uid) loan_count,SUM(apply_amount) loan_amount FROM loan_contract $where";

        $amount_total = array();
        foreach ($interest_bracket as $k => $bracket) {
            $min = $bracket['min'];
            $max = $bracket['max'];
            $sql1 = $sql;
            if (isset($min)) {
                $sql1 .= " AND apply_amount > $min";
            }
            if (isset($max)) {
                $sql1 .= " AND apply_amount <= $max";
            }
            $row = $r->getRow($sql1);
            $interest_bracket[$k]['report'] = $row;
            $amount_total['loan_count'] = intval($amount_total['loan_count']) + intval($row['loan_count']);
            $amount_total['loan_amount'] = round($amount_total['loan_amount'], 2) + round($row['loan_amount'], 2);


        }
        return array(
            'data' => $interest_bracket,
            'amount_total' => $amount_total,
        );
    }

    public static function getLoanInvestmentRatioList($filters)
    {
        $m_loan_sub_product = M('loan_sub_product');
        $sub_product_list = $m_loan_sub_product->select(array('state' => 20));
        $sub_product_id = array_column($sub_product_list, 'uid');
        $sub_product_str = "('" . implode("','", $sub_product_id) . "')";

        $r = new ormReader();
        $currency = $filters['currency'] ?: currencyEnum::USD;
        $where = "WHERE sub_product_id IN $sub_product_str AND state > " . qstr(loanContractStateEnum::PENDING_APPROVAL) . " AND currency = " . qstr($currency);
        if ($filters['date_start']) {
            $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
            $where .= " AND start_date >= " . qstr($date_start);
        }

        if ($filters['date_end']) {
            $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
            $where .= " AND start_date <= " . qstr($date_end);
        }
        if ($filters['branch_id']) {
            $where .= " AND branch_id = " . qstr($filters['branch_id']);
        }

        $sql = "SELECT sub_product_id,COUNT(uid) loan_count,SUM(apply_amount) loan_amount FROM loan_contract $where GROUP BY sub_product_id";
        $list = $r->getRows($sql);
        $list = resetArrayKey($list, 'sub_product_id');
        $amount_total = array(
            'loan_count' => 0,
            'loan_amount' => 0,
        );
        foreach ($list as $key => $row) {
            $amount_total['loan_count'] += $row['loan_count'];
            $amount_total['loan_amount'] += round($row['loan_amount'], 2);
        }
        foreach ($list as $key => $row) {
            $investment_ratio = round(round($row['loan_amount'], 2) / $amount_total['loan_amount'], 4) * 100;
            $list[$key]['investment_ratio'] = $investment_ratio;
        }
        return array(
            'sub_product_list' => $sub_product_list,
            'data' => $list,
            'amount_total' => $amount_total,
        );
    }

    public static function getLoanProvisionData($filters)
    {
        $ret = self::provisionData($filters);
        $due_ret = $ret['due_ret'];
        $reg_ret = $ret['reg_ret'];
        $data['less'] = array();
        $data['less']['standard'] = array();
        $data['less']['substandard'] = array();
        $data['less']['doubtful'] = array();
        $data['less']['loss'] = array();
        $data['greater'] = array();
        $data['greater']['standard'] = array();
        $data['greater']['substandard'] = array();
        $data['greater']['doubtful'] = array();
        $data['greater']['loss'] = array();
        //逾期合同
        foreach ($due_ret as $k => $v) {
            switch ($v['days']) {
                case 0 < $v['days'] && $v['days'] <= 30:
                    if ($v['loan_term_day'] <= 365) {
                        $data['less']['standard']['contract_id'] += 1;
                        $data['less']['standard']['loan_balance'] += $v['loan_balance'];
                        $data['less']['standard']['principal'] += $v['principal'];
                        $data['less']['standard']['interest'] += $v['interest'];
                    } else {
                        $data['greater']['standard']['contract_id'] += 1;
                        $data['greater']['standard']['loan_balance'] += $v['loan_balance'];
                        $data['greater']['standard']['principal'] += $v['principal'];
                        $data['greater']['standard']['interest'] += $v['interest'];
                    }
                    break;
                case 30 < $v['days'] && $v['days'] <= 60:
                    if ($v['loan_term_day'] <= 365) {
                        $data['less']['substandard']['contract_id'] += 1;
                        $data['less']['substandard']['loan_balance'] += $v['loan_balance'];
                        $data['less']['substandard']['principal'] += $v['principal'];
                        $data['less']['substandard']['interest'] += $v['interest'];
                    } else {
                        $data['greater']['substandard']['contract_id'] += 1;
                        $data['greater']['substandard']['loan_balance'] += $v['loan_balance'];
                        $data['greater']['substandard']['principal'] += $v['principal'];
                        $data['greater']['substandard']['interest'] += $v['interest'];
                    }
                    break;
                case 60 < $v['days'] && $v['days'] <= 90:
                    if ($v['loan_term_day'] <= 365) {
                        $data['less']['doubtful']['contract_id'] += 1;
                        $data['less']['doubtful']['loan_balance'] += $v['loan_balance'];
                        $data['less']['doubtful']['principal'] += $v['principal'];
                        $data['less']['doubtful']['interest'] += $v['interest'];
                    } else {
                        $data['greater']['doubtful']['contract_id'] += 1;
                        $data['greater']['doubtful']['loan_balance'] += $v['loan_balance'];
                        $data['greater']['doubtful']['principal'] += $v['principal'];
                        $data['greater']['doubtful']['interest'] += $v['interest'];
                    }
                    break;
                case 90 < $v['days']:
                    if ($v['loan_term_day'] <= 365) {
                        $data['less']['loss']['contract_id'] += 1;
                        $data['less']['loss']['loan_balance'] += $v['loan_balance'];
                        $data['less']['loss']['principal'] += $v['principal'];
                        $data['less']['loss']['interest'] += $v['interest'];
                    } else {
                        $data['greater']['loss']['contract_id'] += 1;
                        $data['greater']['loss']['loan_balance'] += $v['loan_balance'];
                        $data['greater']['loss']['principal'] += $v['principal'];
                        $data['greater']['loss']['interest'] += $v['interest'];
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }
        //非逾期合同
        foreach ($reg_ret as $k => $v) {
            if ($v['loan_term_day'] <= 365) {
                $data['less']['Regular/Current']['contract_id'] += 1;
                $data['less']['Regular/Current']['loan_balance'] += $v['loan_balance'];
                $data['less']['Regular/Current']['principal'] += $v['principal'];
                $data['less']['Regular/Current']['interest'] += $v['interest'];
            } else {
                $data['greater']['Regular/Current']['contract_id'] += 1;
                $data['greater']['Regular/Current']['loan_balance'] += $v['loan_balance'];
                $data['greater']['Regular/Current']['principal'] += $v['principal'];
                $data['greater']['Regular/Current']['interest'] += $v['interest'];
            }
        }
        foreach ($data as $k => $v) {
            foreach ($v as $key => $value) {
                if ($data[$k][$key]) {
                    $data[$k][$key]['rate'] = self::$rate[$key];
                    $data[$k][$key]['amount'] = $value['loan_balance'] * (self::$rate[$key] / 100);
                }
                $data[$k]['total']['contract_id'] += $value['contract_id'];
                $data[$k]['total']['loan_balance'] += $value['loan_balance'];
                $data[$k]['total']['principal'] += $value['principal'];
                $data[$k]['total']['interest'] += $value['interest'];
                $data[$k]['total']['amount'] += $data[$k][$key]['amount'];
            }
            $data['total']['contract_id'] += $data[$k]['total']['contract_id'];
            $data['total']['loan_balance'] += $data[$k]['total']['loan_balance'];
            $data['total']['principal'] += $data[$k]['total']['principal'];
            $data['total']['interest'] += $data[$k]['total']['interest'];
            $data['total']['amount'] += $data[$k]['total']['amount'];
        }
        return $data;
    }

    public static function getLoanProvisionContractData($filters)
    {
        $ret = self::provisionData($filters);
        $due_ret = $ret['due_ret'];
        $reg_ret = $ret['reg_ret'];
        $data['less'] = array();
        $data['less']['standard'] = array();
        $data['less']['substandard'] = array();
        $data['less']['doubtful'] = array();
        $data['less']['loss'] = array();
        $data['greater'] = array();
        $data['greater']['standard'] = array();
        $data['greater']['substandard'] = array();
        $data['greater']['doubtful'] = array();
        $data['greater']['loss'] = array();
        //逾期合同
        foreach ($due_ret as $k => $v) {
            $temp = array();
            $temp['contract_sn'] = $v['contract_sn'];
            $temp['login_code'] = $v['login_code'];
            $temp['days'] = $v['days'];
            $temp['loan_balance'] = $v['loan_balance'];
            $temp['principal'] = $v['principal'];
            $temp['interest'] = $v['interest'];
            switch ($v['days']) {
                case 0 < $v['days'] && $v['days'] <= 30:
                    $temp['rate'] = self::$rate['standard'];
                    $temp['amount'] = $v['loan_balance'] * (self::$rate['standard'] / 100);
                    if ($v['loan_term_day'] <= 365) {
                        $data['less']['standard']['contract'][] = $temp;
                        $data['less']['standard']['total']['contract_id'] += 1;
                        $data['less']['standard']['total']['loan_balance'] += $v['loan_balance'];
                        $data['less']['standard']['total']['principal'] += $v['principal'];
                        $data['less']['standard']['total']['interest'] += $v['interest'];
                        $data['less']['standard']['total']['rate'] = self::$rate['standard'];
                        $data['less']['standard']['total']['amount'] += $temp['amount'];
                    } else {
                        $data['greater']['standard']['contract'][] = $temp;
                        $data['greater']['standard']['total']['contract_id'] += 1;
                        $data['greater']['standard']['total']['loan_balance'] += $v['loan_balance'];
                        $data['greater']['standard']['total']['principal'] += $v['principal'];
                        $data['greater']['standard']['total']['interest'] += $v['interest'];
                        $data['greater']['standard']['total']['rate'] = self::$rate['standard'];
                        $data['greater']['standard']['total']['amount'] += $temp['amount'];
                    }
                    break;
                case 30 < $v['days'] && $v['days'] <= 60:
                    $temp['rate'] = self::$rate['substandard'];
                    $temp['amount'] = $v['loan_balance'] * (self::$rate['substandard'] / 100);
                    if ($v['loan_term_day'] <= 365) {
                        $data['less']['substandard']['contract'][] = $temp;
                        $data['less']['substandard']['total']['contract_id'] += 1;
                        $data['less']['substandard']['total']['loan_balance'] += $v['loan_balance'];
                        $data['less']['substandard']['total']['principal'] += $v['principal'];
                        $data['less']['substandard']['total']['interest'] += $v['interest'];
                        $data['less']['substandard']['total']['rate'] = self::$rate['substandard'];
                        $data['less']['substandard']['total']['amount'] += $temp['amount'];
                    } else {
                        $data['greater']['substandard']['contract'][] = $temp;
                        $data['greater']['substandard']['total']['contract_id'] += 1;
                        $data['greater']['substandard']['total']['loan_balance'] += $v['loan_balance'];
                        $data['greater']['substandard']['total']['principal'] += $v['principal'];
                        $data['greater']['substandard']['total']['interest'] += $v['interest'];
                        $data['greater']['substandard']['total']['rate'] = self::$rate['substandard'];
                        $data['greater']['substandard']['total']['amount'] += $temp['amount'];
                    }
                    break;
                case 60 < $v['days'] && $v['days'] <= 90:
                    $temp['rate'] = self::$rate['doubtful'];
                    $temp['amount'] = $v['loan_balance'] * (self::$rate['doubtful'] / 100);
                    if ($v['loan_term_day'] <= 365) {
                        $data['less']['doubtful']['contract'][] = $temp;
                        $data['less']['doubtful']['total']['contract_id'] += 1;
                        $data['less']['doubtful']['total']['loan_balance'] += $v['loan_balance'];
                        $data['less']['doubtful']['total']['principal'] += $v['principal'];
                        $data['less']['doubtful']['total']['interest'] += $v['interest'];
                        $data['less']['doubtful']['total']['rate'] = self::$rate['doubtful'];
                        $data['less']['doubtful']['total']['amount'] += $temp['amount'];
                    } else {
                        $data['greater']['doubtful']['contract'][] = $temp;
                        $data['greater']['doubtful']['total']['contract_id'] += 1;
                        $data['greater']['doubtful']['total']['loan_balance'] += $v['loan_balance'];
                        $data['greater']['doubtful']['total']['principal'] += $v['principal'];
                        $data['greater']['doubtful']['total']['interest'] += $v['interest'];
                        $data['greater']['doubtful']['total']['rate'] = self::$rate['doubtful'];
                        $data['greater']['doubtful']['total']['amount'] += $temp['amount'];
                    }
                    break;
                case 90 < $v['days']:
                    $temp['rate'] = self::$rate['loss'];
                    $temp['amount'] = $v['loan_balance'] * (self::$rate['loss'] / 100);
                    if ($v['loan_term_day'] <= 365) {
                        $data['less']['loss']['contract'][] = $temp;
                        $data['less']['loss']['total']['contract_id'] += 1;
                        $data['less']['loss']['total']['loan_balance'] += $v['loan_balance'];
                        $data['less']['loss']['total']['principal'] += $v['principal'];
                        $data['less']['loss']['total']['interest'] += $v['interest'];
                        $data['less']['loss']['total']['rate'] = self::$rate['loss'];
                        $data['less']['loss']['total']['amount'] += $temp['amount'];
                    } else {
                        $data['greater']['loss']['contract'][] = $temp;
                        $data['greater']['loss']['total']['contract_id'] += 1;
                        $data['greater']['loss']['total']['loan_balance'] += $v['loan_balance'];
                        $data['greater']['loss']['total']['principal'] += $v['principal'];
                        $data['greater']['loss']['total']['interest'] += $v['interest'];
                        $data['greater']['loss']['total']['rate'] = self::$rate['loss'];
                        $data['greater']['loss']['total']['amount'] += $temp['amount'];
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }

        //非逾期合同
        foreach ($reg_ret as $k => $v) {
            $temp = array();
            $temp['contract_sn'] = $v['contract_sn'];
            $temp['login_code'] = $v['login_code'];
            $temp['days'] = $v['days'];
            $temp['loan_balance'] = $v['loan_balance'];
            $temp['principal'] = $v['principal'];
            $temp['interest'] = $v['interest'];
            $temp['rate'] = self::$rate['Regular/Current'];
            $temp['amount'] = $v['loan_balance'] * (self::$rate['Regular/Current'] / 100);
            if ($v['loan_term_day'] <= 365) {
                $data['less']['Regular/Current']['contract'][] = $temp;
                $data['less']['Regular/Current']['total']['contract_id'] += 1;
                $data['less']['Regular/Current']['total']['loan_balance'] += $v['loan_balance'];
                $data['less']['Regular/Current']['total']['principal'] += $v['principal'];
                $data['less']['Regular/Current']['total']['interest'] += $v['interest'];
                $data['less']['Regular/Current']['total']['rate'] = self::$rate['Regular/Current'];
                $data['less']['Regular/Current']['total']['amount'] += $temp['amount'];
            } else {
                $data['greater']['Regular/Current']['contract'][] = $temp;
                $data['greater']['Regular/Current']['total']['contract_id'] += 1;
                $data['greater']['Regular/Current']['total']['loan_balance'] += $v['loan_balance'];
                $data['greater']['Regular/Current']['total']['principal'] += $v['principal'];
                $data['greater']['Regular/Current']['total']['interest'] += $v['interest'];
                $data['greater']['Regular/Current']['total']['rate'] = self::$rate['Regular/Current'];
                $data['greater']['Regular/Current']['total']['amount'] += $temp['amount'];
            }
        }

        return $data;

    }

    public static function provisionData($filters)
    {
        $r = new ormReader();
        $where = "c.state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE) . " AND c.state < " . qstr(loanContractStateEnum::COMPLETE) . " AND s.state != " . qstr(schemaStateTypeEnum::COMPLETE) . " AND s.state != " . qstr(schemaStateTypeEnum::CANCEL) . " and c.currency = " . qstr($filters['currency'] ?: 'USD');

        if (intval($filters['branch_id'])) {
            $where .= " AND m.branch_id = " . qstr($filters['branch_id']);
        }

        $p_where = "state != " . qstr(schemaStateTypeEnum::CANCEL) . " AND state != " . qstr(schemaStateTypeEnum::COMPLETE);

        //查询有逾期的合同 最早的逾期天数
        $where1 = $p_where . " and datediff(now(),receivable_date) > 0";
        $sql1 = <<<SQL
select contract_id,receivable_date,datediff(now(),receivable_date) days, sum(receivable_principal) principal,sum(receivable_interest) interest 
from loan_installment_scheme 
where $where1
group by contract_id
order by contract_id
SQL;

        $sql2 = <<<SQL
select c.uid,c.contract_sn,m.login_code,c.currency,c.loan_term_day,due.days,due.principal,due.interest,sum(s.receivable_principal) loan_balance from loan_contract c
left join loan_account a on c.account_id = a.uid
left join client_member m on m.obj_guid = a.obj_guid 
inner join loan_installment_scheme s on c.uid = s.contract_id
inner join ($sql1) due on c.uid = due.contract_id
where $where
group by c.uid
SQL;


        //查询未逾期过得合同
        $where2 = "s.days <= 0";
        $sql3 = <<<SQL
select s.* from (
select contract_id,receivable_date,datediff(now(),receivable_date) days, sum(receivable_principal) principal,sum(receivable_interest) interest 
from loan_installment_scheme 
where $p_where
group by contract_id
order by contract_id) s where $where2
SQL;

        $sql4 = <<<SQL
select c.uid,c.contract_sn,m.login_code,c.currency,c.loan_term_day,reg.days,reg.principal,reg.interest,sum(s.receivable_principal) loan_balance from loan_contract c 
left join loan_account a on c.account_id = a.uid
left join client_member m on m.obj_guid = a.obj_guid
inner join loan_installment_scheme s on c.uid = s.contract_id
inner join ($sql3) reg on c.uid = reg.contract_id
where $where
group by c.uid
SQL;

        $due_ret = $r->getRows($sql2); //逾期合同
        $reg_ret = $r->getRows($sql4); //未逾期合同 
        $data['due_ret'] = $due_ret;
        $data['reg_ret'] = $reg_ret;
        return $data;
    }

    public static function getOperatorClientOverdueLoan($operator_id)
    {
        $r = new ormReader();
        $sql = "SELECT cm.login_code,cm.display_name,cm.phone_id,lc.client_obj_guid,mfo.officer_id,lc.currency,lc.contract_sn,lis.contract_id,SUM(lis.amount) amount,COUNT(lis.uid) num ,MIN(lis.receivable_date) receivable_date FROM loan_installment_scheme lis left join loan_contract lc on lis.contract_id = lc.uid left join client_member cm on lc.client_obj_guid = cm.obj_guid left join member_follow_officer mfo on mfo.member_id = cm.uid WHERE lis.state = 0 AND lis.receivable_date < " . qstr(Now()) . " and mfo.officer_id = " . qstr($operator_id) . " GROUP BY lis.contract_id ORDER BY lis.receivable_date DESC";
        return $r->getRows($sql);
    }

    public static function getMasterClientList($page_number,$page_size,$filters)
    {
        $r = new ormReader();
        $where = '';
        $search_text = trim($filters['search_text']);
        if( $search_text ){
            $where .= " and (cm.obj_guid=".qstr($search_text)." or cm.display_name like '%".qstr2($search_text)."%' or cm.phone_number like '%".qstr2($search_text)."%'  )";
        }
        if( $filters['branch_id'] ){
            $where .= " and lc.branch_id=".qstr($filters['branch_id']);
        }
        if( $filters['currency'] ){
            $where .= " and lc.currency=".qstr($filters['currency']);
        }
        // 时间筛选是筛选合同发生的时间区间,用合同的开始时间来筛选
        if( $filters['date_start'] ){
            $where.= " and lc.start_date>=".qstr(system_toolClass::getFormatStartDate($filters['date_start']));
        }
        if( $filters['date_end'] ){
            $where .= " and lc.start_date<=".qstr(system_toolClass::getFormatEndDate($filters['date_end']));
        }
        // 首先是获取合同数据
        $sql = "select lc.*,cm.display_name,cm.kh_display_name,cm.gender,cm.phone_country,cm.phone_number,cm.phone_id,
        address.id1_text,address.id2_text,address.id3_text,address.id4_text,sb.branch_name,sb.obj_guid branch_guid,mcc.alias product_type,
        lis.loan_balance,lis.min_receivable_date,lr.repayment_num,lr.repayment_principal,lr.repayment_interest,lr.repayment_operation_fee,
        lr.repayment_penalty,mfo.officer_name 
        from loan_contract lc INNER  JOIN client_member cm on cm.obj_guid=lc.client_obj_guid 
        left join site_branch sb on sb.uid=lc.branch_id 
        left join member_credit_category mcc on mcc.uid=lc.member_credit_category_id 
        left join (select * from ( SELECT * from common_address where state='1' order by uid desc ) x group by obj_guid) address on address.obj_guid=lc.client_obj_guid
        left join (select contract_id,count(uid) repayment_num,sum(principal_amount) repayment_principal,
          sum(interest_amount) repayment_interest,sum(operation_fee_amount) repayment_operation_fee,sum(penalty_amount) repayment_penalty
          from loan_repayment where state='".loanRepaymentStateEnum::SUCCESS."' group by contract_id ) lr  on lr.contract_id=lc.uid
        left join ( select contract_id,sum(receivable_principal) loan_balance,min(receivable_date) min_receivable_date from loan_installment_scheme where state>='".schemaStateTypeEnum::CREATE."' and state<'".schemaStateTypeEnum::COMPLETE."' group by contract_id )  lis on lis.contract_id=lc.uid
        left join ( select y.* from (select * from member_follow_officer where officer_type='0' order by is_primary desc) y group by y.member_id ) mfo on mfo.member_id=cm.uid
        where lc.state>=".qstr(loanContractStateEnum::PENDING_DISBURSE)."  $where ".
        " group by lc.uid ";
        $page_data = $r->getPage($sql,$page_number,$page_size);
        $report_rows = $page_data->rows;

        $credit_grant_ids = array();
        foreach( $report_rows as $key=>$v ){
            $v['day_late'] = null;
            if( $v['min_receivable_date'] ){
                $diff_date = system_toolClass::diffBetweenTwoDays(Now(),$v['min_receivable_date']);
                if( $diff_date > 0 ){
                    $v['day_late'] = $diff_date;
                }
            }

            $rt = loan_baseClass::interestRateConversion($v['interest_rate'],$v['interest_rate_unit'],interestRatePeriodEnum::MONTHLY);
            if( $rt->STS ){
                $v['monthly_interest_rate'] = $rt->DATA;
            }else{
                $v['monthly_interest_rate'] = $v['interest_rate'];
            }

            $report_rows[$key] = $v;
            $credit_grant_ids[] = $v['credit_grant_id'];
        }

        $credit_grant_ids = array_unique($credit_grant_ids);
        // 处理共同借款人
        $coborrowers_name = array();
        if( !empty($credit_grant_ids) ){
            $sql = "select mcrr.*,mcg.uid grant_id from member_credit_grant mcg INNER JOIN  member_credit_request_relative mcrr on mcg.credit_request_id=mcrr.request_id 
            where mcg.uid in (".join(',',$credit_grant_ids).") ";
            $list = $r->getRows($sql);
            foreach( $list as $v ){
                $coborrowers_name[$v['grant_id']][] = $v['name'];
            }
            foreach( $report_rows as $k=>$value ){
                $report_rows[$k]['coborrower_name'] = join(',',(array)$coborrowers_name[$value['credit_grant_id']]);
            }
        }

        return array(
            "sts" => true,
            "data" => $report_rows,
            "total" => $page_data->count,
            "pageTotal" => $page_data->pageCount,
            "pageNumber" => $page_number,
            "pageSize" => $page_size,
        );


    }
}