<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/18
 * Time: 17:33
 */
class biz_member_loan_repaymentModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('biz_member_loan_repayment');
    }

    public function getRepaymentList($pageNumber, $pageSize, $filter)
    {
        $sql = "SELECT a.*,cm.login_code FROM biz_member_loan_repayment a LEFT JOIN client_member cm ON a.member_id = cm.uid WHERE a.state = ".qstr(bizStateEnum::DONE);
        if (intval($filter['cashier_id'])) {
            $sql .= " AND a.cashier_id = " . intval($filter['cashier_id']);
        }
        $sql .= " ORDER BY a.uid DESC";
        $data = $this->reader->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        if ($rows) {
            $r = new ormReader();
            foreach ($rows as $key => $row) {
                $sql_2 = "SELECT bmlrbcd.currency, bmlrbcd.amount FROM biz_member_loan_repayment a LEFT JOIN biz_member_loan_repayment_detail bmlrbcd ON a.uid = bmlrbcd.biz_id WHERE bmlrbcd.amount_type=1 AND  a.uid = " . $row['uid'];
                $currency_amount = $r->getRows($sql_2);
                $currency_amount = resetArrayKey($currency_amount, 'currency');
                $row['USD'] = $currency_amount['USD'];
                $row['KHR'] = $currency_amount['KHR'];
                $rows[$key] = $row;
            }
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize
        );
    }

    public function getRepaymentDetail($biz_id)
    {
        $biz_id = intval($biz_id);
        $biz_info = $this->find(array('uid' => $biz_id, 'state' => repaymentStateEnum::DONE));
        if ($biz_info) {
            $sql = "select * from loan_contract WHERE uid IN (SELECT contract_id from loan_request_repayment_detail WHERE request_id = " . $biz_info['request_id'] . " GROUP BY contract_id)";
            $contract_list = $this->reader->getRows($sql);
            $contract_sn_list = array_column($contract_list, 'contract_sn');
            $biz_info['contract_sn_list'] = $contract_sn_list;

            $sql = "SELECT lis.* FROM loan_request_repayment_detail d INNER JOIN loan_installment_scheme lis ON d.scheme_id = lis.uid WHERE d.request_id = " . $biz_info['request_id'];
            $schema_list = $this->reader->getRows($sql);
            $total_principal = 0;
            $total_interest = 0;
            $total_operation_fee = 0;
            $total_admin_fee = 0;
            $total_penalty = 0;

            foreach ($schema_list as $schema) {
                $total_principal += $schema['receivable_principal'];
                $total_interest += $schema['receivable_interest'];
                $total_operation_fee += $schema['receivable_operation_fee'];
                $total_admin_fee += $schema['receivable_admin_fee'];
            }
            $biz_info['total_principal'] = $total_principal;
            $biz_info['total_interest'] = $total_interest;
            $biz_info['total_operation_fee'] = $total_operation_fee;
            $biz_info['total_admin_fee'] = $total_admin_fee;
            $biz_info['total_penalty'] = $total_penalty;
            $biz_info['schema_list'] = $schema_list;
            $biz_info['total_amount'] = round($total_principal,2) + round($total_interest,2) + round($total_operation_fee,2) + round($total_admin_fee,2) + round($total_penalty,2);
        }
        return $biz_info;
    }
}