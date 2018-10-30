<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/16
 * Time: 10:53
 */
class loan_applyModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('loan_apply');
    }

    public function addApply($p)
    {
        $member_id = intval($p['member_id']);
        $product_id = intval($p['product_id']);
        $product_name = trim($p['product_name']);
        $applicant_name = trim($p['applicant_name']);
        $apply_amount = round($p['apply_amount'], 2);
        $currency = $p['currency']?:currencyEnum::USD;
        $loan_time = intval($p['loan_time']);
        $loan_time_unit = trim($p['loan_time_unit']);
        $loan_purpose = trim($p['loan_purpose']);
        $mortgage = implode(',',$p['mortgage']);
        $address_region = trim($p['address_region']);
        $address_detail = trim($p['address_detail']);
        $content = trim($p['content']);
        if( $address_region || $address_detail ){
            $address = trim($address_region,',').' '.$address_detail;
        }else{
            $address = null;
        }

        $request_source = trim($p['request_source']) ?: loanApplySourceEnum::MEMBER_APP;
        $apply_time = Now();
        $creator_id = intval($p['creator_id']);
        $creator_name = trim($p['creator_name']);

        $country_code = trim($p['country_code']);
        $phone_number = trim($p['phone_number']);
        $phone_arr = tools::getFormatPhone($country_code,$phone_number);
        $contact_phone = $phone_arr['contact_phone'];

        if (empty($applicant_name) || empty($contact_phone)) {
            return new result(false, 'The applicant\'s information is incomplete!');
        }
        if ($apply_amount < 0) {
            return new result(false, 'The application amount cannot be less than 0!');
        }
        if( $loan_time <= 0 ){
            return new result(false, 'The loan time cannot be less than 0!');
        }

        $row = $this->newRow();
        $row->member_id = $member_id;
        $row->product_id = $product_id;
        $row->product_name = $product_name;
        $row->applicant_name = $applicant_name;
        $row->applicant_address = $address;
        $row->apply_amount = $apply_amount;
        $row->currency = $currency;
        $row->loan_time = $loan_time;
        $row->loan_time_unit = $loan_time_unit;
        $row->contact_phone = $contact_phone;
        $row->loan_purpose = $loan_purpose;
        $row->mortgage = $mortgage;
        $row->apply_time = $apply_time;
        $row->request_source = $request_source;
        $row->state = loanApplyStateEnum::CREATE;
        $row->creator_id = $creator_id;
        $row->creator_name = $creator_name;
        $row->content = $content;
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Add Successful!');
        } else {
            return new result(true, 'Add Failed!');
        }
    }
}