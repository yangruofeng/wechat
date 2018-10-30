<?php

class repaymentReportClass
{

    public static function getRepaymentList($pageNumber, $pageSize, $filters)
    {

    }

    public static function agingOfLoanArrear($filters)
    {
        $ret = loanReportClass::provisionData($filters);
        $due_ret = $ret['due_ret'];
        $reg_ret = $ret['reg_ret'];

        $term_bracket = array(
            'bracket_1' => array('max' => 0),
            'bracket_2' => array('min' => 1, 'max' => 1),
            'bracket_3' => array('min' => 2, 'max' => 5),
            'bracket_4' => array('min' => 6, 'max' => 10),
            'bracket_5' => array('min' => 11, 'max' => 30),
            'bracket_6' => array('min' => 31, 'max' => 60),
            'bracket_7' => array('min' => 61, 'max' => 90),
            'bracket_8' => array('min' => 91, 'max' => 181),
            'bracket_9' => array('min' => 181, 'max' => 360),
            'bracket_10' => array('min' => 361),
        );

        foreach ($term_bracket as $key => $term) {
            $term['statistics'] = array(
                'loan_count' => 0,
                'due_principal' => 0,
                'outstanding_balance' => 0,
            );
            $term_bracket[$key] = $term;
        }

        $amount_total = array(
            'loan_count' => 0,
            'due_principal' => 0,
            'outstanding_balance' => 0,
        );

        foreach ($reg_ret as $val) {
            ++$term_bracket['bracket_1']['statistics']['loan_count'];
            $term_bracket['bracket_1']['statistics']['outstanding_balance'] += $val['loan_balance'] + $val['interest'];

            ++$amount_total['loan_count'];
            $amount_total['outstanding_balance'] += $val['loan_balance'] + $val['interest'];
        }

        foreach ($due_ret as $val) {
            foreach ($term_bracket as $key => $term) {
                if ($term['max'] == 0) {
                    continue;
                }

                if (($val['days'] >= $term['min'] && $val['days'] <= $term['max']) || (!isset($term['max']) && $val['days'] >= $term['min'])) {
                    ++$term['statistics']['loan_count'];
                    $term['statistics']['due_principal'] += $val['principal'];
                    $term['statistics']['outstanding_balance'] += $val['loan_balance'] + $val['interest'];
                    $term_bracket[$key] = $term;

                    ++$amount_total['loan_count'];
                    $amount_total['due_principal'] += $val['principal'];
                    $amount_total['outstanding_balance'] += $val['loan_balance'] + $val['interest'];
                    break;
                }
            }
        }

        return array(
            'data' => $term_bracket,
            'amount_total' => $amount_total,
        );

    }

    public function getLoanInFallingDueList($pageNumber, $pageSize, $filters)
    {
        $where = "lis.state != " . qstr(schemaStateTypeEnum::CANCEL) .
            " AND lis.state != " . qstr(schemaStateTypeEnum::COMPLETE) .
            " AND lc.state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE) .
            " AND lc.state <= " . qstr(loanContractStateEnum::COMPLETE);
        if ($filters['currency']) {
            $where .= " AND lc.currency = " . qstr($filters['currency']);
        }
        if ($filters['date_start']) {
            $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
            $where .= " AND lis.receivable_date >= " . qstr($date_start);
        }

        if ($filters['date_end']) {
            $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
            $where .= " AND lis.receivable_date <= " . qstr($date_end);
        }

        $sql = <<<SQL
select lis.*,lc.virtual_contract_sn,lc.sub_product_code,lc.sub_product_name,cm.obj_guid,cm.display_name from loan_installment_scheme lis
inner join loan_contract lc on lis.contract_id = lc.uid
inner join loan_account la on lc.account_id = la.uid
inner join client_member cm on cm.obj_guid = la.obj_guid
where $where
order by lis.receivable_date asc
SQL;
        $r = new ormReader();
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        $amount_total = array(
            'count' => 0,
            'principal_amount' => 0,
            'scheduled_interest' => 0,
            'penalty' => 0,
        );
        foreach ($rows as $key => $row) {
            $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($row['uid']);
            $row['penalty'] = $penalty;
            $row['principal_amount'] = $row['receivable_principal'] - $row['paid_principal'];
            $row['scheduled_interest'] = $row['receivable_interest'] - $row['paid_interest'];
            $rows[$key] = $row;

            ++$amount_total['count'];
            $amount_total['principal_amount'] += $row['principal_amount'];
            $amount_total['scheduled_interest'] += $row['scheduled_interest'];
            $amount_total['penalty'] += $penalty;
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize,
            "amount_total" => $amount_total,
        );
    }

}