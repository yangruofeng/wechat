<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/23
 * Time: 15:44
 */
class loan_sub_productModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('loan_sub_product');
    }


    public function getSubProductDetail()
    {

    }


    public function getMemberLatestLoanHistory($product_id,$member_id,$limit=5)
    {
        $limit = intval($limit);

        $product_info = $this->find(array(
            'uid' => $product_id
        ));
        if( !$product_info ){
            return null;
        }
        $loanAccount = memberClass::getLoanAccountInfoByMemberId($member_id);
        if( !$loanAccount ){
            return null;
        }

        $sql = "select c.* from loan_contract c left join loan_sub_product sp on sp.uid=c.sub_product_id
        where c.account_id='".$loanAccount['uid']."' and sp.sub_product_code='".$product_info['sub_product_code']."' 
        and c.state>='".loanContractStateEnum::PENDING_DISBURSE."' order by c.uid desc limit 0,$limit ";


        $list = $this->reader->getRows($sql);

        return $list;
    }
}