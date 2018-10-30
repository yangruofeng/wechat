<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/12
 * Time: 16:08
 */
class common_counter_biz_settingModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('common_counter_biz_setting');
    }


    public function getAllSetting()
    {
        $sql = "select * from common_counter_biz_setting";
        $list = $this->reader->getRows($sql);
        return $list;
    }


    public function getCounterBizSetting(){
        $common_counter_biz = $this->getAllSetting();
        $common_counter_biz = resetArrayKey($common_counter_biz, 'biz_code');
        $arr_biz = array();
        foreach ($common_counter_biz as $key => $value){
            $arr_biz[$key] = array(
                'is_require_ct_approve' => $value['is_require_ct_approve'],
                'min_approve_amount' => $value['min_approve_amount'],
            );
        }return $arr_biz;

    }



    public function insertSingleBizSettingData($biz_code,$data,$user_info)
    {
        // 是否已经设置了
        $row = $this->getRow(array(
            'biz_code' => $biz_code
        ));
        if( $row ){

            $row->is_require_ct_approve = $data['is_require_ct_approve'];
            $row->min_approve_amount = $data['is_require_ct_approve']?$data['min_approve_amount']:0;
            $row->operator_id = $user_info['user_id'];
            $row->operator_name = $user_info['user_name'];
            $row->update_time = Now();
            $up = $row->update();
            return $up;

        }else{
            $row = $this->newRow();
            $row->biz_code = $biz_code;
            $row->is_require_ct_approve = $data['is_require_ct_approve'];
            $row->min_approve_amount = $data['is_require_ct_approve']?$data['min_approve_amount']:0;
            $row->operator_id = $user_info['user_id'];
            $row->operator_name = $user_info['user_name'];
            $row->create_time = Now();
            $row->update_time = Now();
            $insert = $row->insert();
            return $insert;
        }

    }

    public function insertSetting($data,$user_info)
    {
        foreach( $data as $biz_code=>$v ){
            $rt = $this->insertSingleBizSettingData($biz_code,$v,$user_info);
            if( !$rt->STS ){
                return $rt;
            }
        }
        return new result(true);
    }
    public function getFormattedSetting(){
        $rows=$this->select("1=1");
        $arr=resetArrayKey($rows,"biz_code");

        $arr[bizCodeEnum::MEMBER_DEPOSIT_BY_CASH]=array_merge(array(
            "biz_table"=>"biz_member_deposit",
            "biz_caption"=>"Deposit"
        ),$arr[bizCodeEnum::MEMBER_DEPOSIT_BY_CASH]);

        $arr[bizCodeEnum::MEMBER_WITHDRAW_TO_CASH]=array_merge(array(
            "biz_table"=>"biz_member_withdraw",
            "biz_caption"=>"Withdraw"
        ),$arr[bizCodeEnum::MEMBER_WITHDRAW_TO_CASH]);

        $arr[bizCodeEnum::MEMBER_CREATE_LOAN_CONTRACT]=array_merge(array(
            "biz_table"=>"biz_member_create_loan_contract",
            "biz_caption"=>"New Loan"
        ),$arr[bizCodeEnum::MEMBER_CREATE_LOAN_CONTRACT]);

        $arr[bizCodeEnum::MEMBER_LOAN_REPAYMENT_BY_CASH]=array_merge(array(
            "biz_table"=>"biz_member_loan_repayment_by_cash",
            "biz_caption"=>"Repayment By Cash",
            "biz_table_detail"=>'biz_member_loan_repayment_by_cash_detail'
        ),$arr[bizCodeEnum::MEMBER_LOAN_REPAYMENT_BY_CASH]);

        $arr[bizCodeEnum::MEMBER_PREPAYMENT]=array_merge(array(
            "biz_table"=>"biz_member_prepayment",
            "biz_caption"=>"Prepayment",
            "biz_table_detail"=>'biz_member_prepayment_detail'
        ),$arr[bizCodeEnum::MEMBER_PREPAYMENT]);

        $arr[bizCodeEnum::RECEIVE_LOAN_PENALTY_BY_COUNTER]=array_merge(array(
            "biz_table"=>"biz_receive_member_penalty",
            "biz_caption"=>"Receive Penalty",
            "biz_table_detail"=>'biz_receive_member_penalty_detail'

        ),$arr[bizCodeEnum::RECEIVE_LOAN_PENALTY_BY_COUNTER]);

        return $arr;
    }
}