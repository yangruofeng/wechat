<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/6
 * Time: 17:53
 */
class loan_productModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('loan_product');
    }


    public function getMainProductInfoById($id)
    {
        return $this->find(array(
            'uid' => $id
        ));
    }

    public function getAllProductListOfSimpleData()
    {
        $sql = "select uid product_id,product_code,product_name from loan_product where 
          state='".loanProductStateEnum::ACTIVE."' ";
        $list = $this->reader->getRows($sql);
        return $list;
    }


    public function getAllSubProductOfMainProductId($id)
    {
        // 需要去重,展示最新一条的
        $sql = "select x.* from  (select * from loan_sub_product order by uid desc) x 
        where x.product_id='$id'  group by x.product_key order by sub_product_code asc ";
        return $this->reader->getRows($sql);
    }
}