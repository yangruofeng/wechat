<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/16
 * Time: 10:53
 */
class loan_consultModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('loan_consult');
    }

    public function addConsult($p)
    {
        $member_id = intval($p['member_id']);
        $product_id = intval($p['product_id']);
        $product_name = trim($p['product_name']);
        $applicant_name = trim($p['applicant_name']);
        $apply_amount = round($p['apply_amount'], 2);
        $currency = $p['currency'] ?: currencyEnum::USD;
        $loan_time = intval($p['loan_time']);
        $loan_time_unit = trim($p['loan_time_unit']);
        $loan_purpose = trim($p['loan_purpose']);
        $mortgage = implode(',', $p['mortgage']);
        $address = trim($p['address']);
        $branch_id = intval($p['branch_id']);
        $state = intval($p['state']) ?: loanConsultStateEnum::CREATE;

        $memo = $p['memo'];
        $request_source = trim($p['request_source']) ?: loanApplySourceEnum::MEMBER_APP;
        $creator_id = intval($p['creator_id']);
        $creator_name = trim($p['creator_name']);

        $country_code = trim($p['country_code']);
        $phone_number = trim($p['phone_number']);
        $phone_arr = tools::getFormatPhone($country_code, $phone_number);
        $contact_phone = $phone_arr['contact_phone'];

        if (empty($applicant_name) || empty($contact_phone)) {
            return new result(false, 'The applicant\'s information is incomplete!');
        }
        if ($apply_amount < 0) {
            return new result(false, 'The application amount cannot be less than 0!');
        }
        if ($loan_time <= 0) {
            return new result(false, 'The loan time cannot be less than 0!');
        }

        $row = $this->newRow();
        $row->member_id = $member_id;
        $row->product_id = $product_id;
        $row->product_name = $product_name;
        $row->applicant_name = $applicant_name;
        $row->address = $address;
        $row->apply_amount = $apply_amount;
        $row->operator_id = intval($p['operator_id']) ?: 0;
        $row->operator_name = $p['operator_name'];
        $row->operator_remark = trim($p['operator_remark']) ?: '';

        $row->currency = $currency;
        $row->loan_time = $loan_time;
        $row->loan_time_unit = $loan_time_unit;
        $row->contact_phone = $contact_phone;
        $row->loan_purpose = $loan_purpose;
        $row->mortgage = $mortgage;
        $row->memo = $memo;
        $row->request_source = $request_source;
        $row->state = $state;
        $row->creator_id = $creator_id;
        $row->creator_name = $creator_name;
        $row->branch_id = $branch_id;
        $row->create_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Add Successful!');
        } else {
            return new result(false, 'Add Failed!');
        }
    }

    public function rejectById($uid, $remark, $operator_id, $operator_name)
    {
        $uid = intval($uid);
        $row = $this->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }

        $row->state = loanConsultStateEnum::BRANCH_REJECT;
        $row->bm_remark = $remark;
        $row->bm_id = $operator_id;
        $row->bm_name = $operator_name;
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Handle Successful!');
        } else {
            return new result(false, 'Handle Failed!');
        }
    }

    public function getConsultById($uid)
    {
        $info = $this->find(array('uid' => $uid));
        return $info;
    }
}