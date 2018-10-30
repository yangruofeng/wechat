<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/10
 * Time: 9:38
 */
class loan_categoryModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('loan_category');
    }

    /**
     * 获取列表，不包含description那些text项
     * @param $showClosed bool
     */
    public function getCategoryList($showClosed=false)
    {
        $sql="select a.*,b.sub_product_name default_product_name,c.package interest_package_name from loan_category a ";
        $sql.=" inner join loan_sub_product b on a.default_product_id=b.uid";
        $sql.=" left join loan_product_package c on a.interest_package_id=c.uid";
        if (!$showClosed) {
            $sql.=" where a.is_close=0";
        }
        $sql.=" order by a.is_close,category_name ";

        $rows=$this->reader->getRows($sql);
        return $rows;

    }
    public function getCategoryItem($uid){
        $row=$this->find(array("uid"=>$uid));
        //取允许的interest_package
        $m_interest=new loan_category_interestModel();
        $list_interest=$m_interest->select(array("category_id"=>$uid));
        $list_interest=resetArrayKey($list_interest,"interest_package_id");
        $list_interest=array_keys($list_interest);
        $row['allowed_interest_package_id']=$list_interest;

        //取允许的sub-product
        $m_product=new loan_category_productModel();
        $list_product=$m_product->select(array("category_id"=>$uid));
        $list_product=resetArrayKey($list_product,"sub_product_id");
        $list_product=array_keys($list_product);
        $row['allowed_sub_product_id']=$list_product;

        return $row;
    }



}