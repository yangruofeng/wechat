<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/24
 * Time: 18:16
 */
class loan_billpay_checkModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('loan_billpay_check');
    }

    public function addBillPayCheck($bill_code, $amount, $pay_time, $remark, $state, $api_result, $operator_id)
    {
        $amount = round($amount, 2);
        $m_loan_contract_billpay_code = M('loan_contract_billpay_code');
        $bill_info = $m_loan_contract_billpay_code->find(array('bill_code' => $bill_code));
        if (!$bill_info) {
            return new result(false, 'Invalid bill code.');
        }
        $member_info = loan_contractClass::getLoanContractMemberInfo($bill_info['contract_id']);

        $user_obj = new objectUserClass($operator_id);
        $row = $this->newRow();
        $row->bill_code = $bill_code;
        $row->contract_id = $bill_info['contract_id'];
        $row->member_id = $member_info['uid'];
        $row->bank_id = $bill_info['bank_id'];
        $row->bank_name = $bill_info['bank_name'];
        $row->bank_account_no = $bill_info['bank_account_no'];
        $row->currency = $bill_info['currency'];
        $row->amount = $amount;
        $row->remark = $remark;
        $row->pay_time = $pay_time;
        $row->state = $state;
        $row->api_result = $api_result;
        $row->operator_id = $operator_id;
        $row->operator_name = $user_obj->user_name;
        $row->create_time = Now();
        $row->update_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Add successful.');
        } else {
            return new result(true, 'Add failed.');
        }
    }

    public function getBillPayCheckList($pageNumber, $pageSize, $filter = array())
    {
        $sql = 'SELECT lbc.*,cm.display_name FROM loan_billpay_check lbc INNER JOIN client_member cm ON lbc.member_id = cm.uid WHERE 1 = 1';
        if (intval($filter['operator_id'])) {
            $sql .= ' AND lbc.operator_id = ' . intval($filter['operator_id']);
        }
        $data = $this->reader->getPage($sql, $pageNumber, $pageSize);
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