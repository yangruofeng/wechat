<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/26
 * Time: 15:15
 */
class loanSettingClass{
    /**
     * 获取loan fee & admin fee,如果没有传category-id，就取default的
     * @param int $amount 只考虑美元金额
     * @param int $category_id loan_category.uid
     */
    public static function matchLoanFeeOfSetting($amount,$category_id=0){
        $amount=intval($amount);
        //global里已经是default-setting去merge special-setting了
        $sql="select * from loan_fee_setting where category_id='" . $category_id . "' and currency='".currencyEnum::USD."' and min_amount<=".$amount ." and max_amount>=".$amount;
        $r=new ormReader();
        $item=$r->getRow($sql);
        if(!$item && $category_id>0){
            //取default的
            $sql="select * from loan_fee_setting where category_id='0' and currency='".currencyEnum::USD."'  and min_amount<=".$amount ." and max_amount>=".$amount;
            $item=$r->getRow($sql);
        }
        if($item){
            return array(
                "loan_fee"=>$item['loan_fee'],
                "loan_fee_type"=>$item['loan_fee_type'],
                "admin_fee"=>$item['admin_fee'],
                "admin_fee_type"=>$item['admin_fee_type'],
                "annual_fee"=>$item['annual_fee'],
                "annual_fee_type"=>$item['annual_fee_type']
            );
        }else{
            return array(
                "loan_fee"=>0,
                "loan_fee_type"=>0,
                "admin_fee"=>0,
                "admin_fee_type"=>1,
                "annual_fee"=>0,
                "annual_fee_type"=>1
            );
        }
    }
    public static function getLoanFeeByGrantId($grant_id){
        $m_grant=new member_credit_grantModel();
        $grant_info=$m_grant->find(array("uid"=>$grant_id));
        if(!$grant_info){
            throw new Exception("Invalid Credit-Agreement, Grant-ID:".$grant_id);
        }
        if(($grant_info['loan_fee']+$grant_info['admin_fee'])>0){
            return array(
                "loan_fee"=>$grant_info['loan_fee'],
                "loan_fee_type"=>$grant_info['loan_fee_type'],
                "admin_fee"=>$grant_info['admin_fee'],
                "admin_fee_type"=>$grant_info['admin_fee_type']
            );
        }
        return self::matchLoanFeeOfSetting($grant_info['max_credit'],$grant_info['default_credit_category_id']);
    }

}