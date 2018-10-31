<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 8/30/2018
 * Time: 2:10 PM
 */
class loanV2Control extends back_office_baseControl{

    /**
     * 保存category允许的利息包
     * @param $p
     * @return result
     */
    public function ajaxSaveLoanCategoryAllowedInterestPackageOp($p){
        $list=$p['chk_interest_package'];
        $category_id=$p['category_id'];
        $m=new loan_category_interestModel();
        $sql="delete from loan_category_interest where category_id=".qstr($category_id);
        $ret=$m->conn->execute($sql);
        if($ret->STS){
            foreach($list as $package_id){
                $row=$m->newRow();
                $row->category_id=$category_id;
                $row->interest_package_id=$package_id;
                $ret=$row->insert();
                if(!$ret->STS){
                    return new result(false,"Failed to Insert:".$ret->MSG);
                }
            }
            return new result(true,"OK");
        }else{
            return new result(false,"Failed to Delete Old Setting:".$ret->MSG);
        }

    }
    /**
     * 保存category允许的支付方式
     * @param $p
     * @return result
     */
    public function ajaxSaveLoanCategoryAllowedSubProductOp($p){
        $list=$p['chk_sub_product'];
        $category_id=$p['category_id'];
        $m=new loan_category_productModel();
        $sql="delete from loan_category_product where category_id=".qstr($category_id);
        $ret=$m->conn->execute($sql);
        if($ret->STS){
            foreach($list as $sub_product_id){
                $row=$m->newRow();
                $row->category_id=$category_id;
                $row->sub_product_id=$sub_product_id;
                $ret=$row->insert();
                if(!$ret->STS){
                    return new result(false,"Failed to Insert:".$ret->MSG);
                }
            }
            return new result(true,"OK");
        }else{
            return new result(false,"Failed to Delete Old Setting:".$ret->MSG);
        }

    }
}