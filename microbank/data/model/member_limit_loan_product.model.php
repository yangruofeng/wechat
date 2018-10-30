<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/19
 * Time: 14:49
 */
class member_limit_loan_productModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_limit_loan_product');
    }

    public function isLimitMemberLoanByProductInfo($member_id,$sub_product_info)
    {
        $row = $this->getRow(array(
            'member_id' => $member_id,
            'product_code' => $sub_product_info['sub_product_code']
        ));
        return $row?true:false;
    }
}