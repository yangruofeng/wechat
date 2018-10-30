<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/6
 * Time: 17:24
 */

class loan_productControl extends bank_apiControl
{


    /**
     * 取得贷款产品列表
     * @return result
     */
    public function getProductListOp()
    {

        // 产品少，不分页了
        $param = array_merge(array(),$_GET,$_POST);

        $list = loan_productClass::getAllActiveSubProductList();
        $return = array();
        foreach( $list as $v){
            // 计算产品最低月利率
            $v['sub_product_icon'] = global_settingClass::getLoanProductIconByInterestType($v['interest_type']);
            $min = loan_productClass::getMinMonthlyRate($v['uid']);
            $v['min_rate'] = $min;
            $v['min_rate_desc'] = $min.'%';
            $return[] = $v;
        }

        return new result(true,'success',$return);

    }


    public function getSubProductDetailInfoOp()
    {
        $param = array_merge(array(),$_GET,$_POST);
        $product_id = $param['product_id'];
        $product_detail = loan_productClass::getSubProductDetailInfo($product_id);
        if( !$product_detail ){
            return new result(false,'No product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }
        $product_detail['sub_product_icon'] = global_settingClass::getLoanProductIconByInterestType($product_detail['interest_type']);

        return new result(true,'success',$product_detail);
    }

    public function getMainProductDetailInfoOp()
    {
        $param = array_merge(array(),$_GET,$_POST);
        $product_id = $param['product_id'];
        $product_detail = loan_productClass::getMainProductDetailInfo($product_id);
        if( !$product_detail ){
            return new result(false,'No product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }
        return new result(true,'success',$product_detail);
    }

    public function getProductDesRateListOp()
    {
        $param = array_merge(array(),$_GET,$_POST);
        $product_id = $param['product_id'];
        $currency = $param['currency'];
        $page_num = $param['page_num'];
        $page_size = $param['page_size'];
        $re = loan_productClass::getProductDescribeRateList($product_id,$page_num,$page_size,$currency);
        return new result(true,'success',$re);
    }



    public function subProductLoanIndexOp()
    {
        $param = array_merge(array(),$_GET,$_POST);
        //$product_id = intval($param['product_id']);
        $member_id = intval($param['member_id']);

        $member_credit_category_id = intval($param['member_credit_category_id']);
        $m_member_category = new member_credit_categoryModel();
        $credit_category = $m_member_category->getRow(array(
            'uid' => $member_credit_category_id
        ));
        if( !$credit_category ){
            return new result(false,'No credit category:'.$member_credit_category_id,null,errorCodesEnum::INVALID_PARAM);
        }

        $loan_category = (new loan_categoryModel())->find(array(
            'uid' => $credit_category['category_id']
        ));

        $credit_category['is_special'] = $loan_category['is_special']?1:0;
        $credit_category['special_key'] = $loan_category['special_key'];




        $product_id = $credit_category['sub_product_id'];

        $m_sub_product = new loan_sub_productModel();

        $sql = "select sp.*,p.category,p.product_code,p.product_name from loan_sub_product sp left join loan_product p on p.uid=sp.product_id 
        where sp.uid='$product_id'";

        $sub_product_info = $m_sub_product->reader->getRow($sql);
        if( !$sub_product_info ){
            return new result(false,'No sub product.',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }

        $member_credit = memberClass::getCreditBalance($member_id);
        // 修改为产品的信用值
        $member_credit['credit'] = $credit_category['credit'];
        $member_credit['balance'] = $credit_category['credit_balance'];

        //$monthly_min_rate = loan_productClass::getMinMonthlyRate($product_id);
        // 最低日利率
        $monthly_min_rate = loan_productClass::getProductMinOrMaxRateOfPeriodType($product_id,interestRatePeriodEnum::DAILY);
        $sub_product_info['monthly_mint_rate'] = $monthly_min_rate.'%';
        $sub_product_info['is_single_repayment'] = interestTypeClass::isOnetimeRepayment($sub_product_info['interest_type'])?1:0;



        // 最新五个
        $list = $m_sub_product->getMemberLatestLoanHistory($product_id,$member_id,10);

        // 最近的贷款还款记录
        $m_contract = new loan_contractModel();
        $page_data = $m_contract->getMemberLoanOrRepayListOfProduct($member_id,$member_credit_category_id,1,20);
        $mix_list = $page_data->rows;


        return new result(true,'success',array(
            'product_info' => $sub_product_info,
            'member_credit_category' => $credit_category,
            'member_credit' => $member_credit,
            'loan_list' => $list,
            'mix_list' => $mix_list
        ));

    }


    public function getLoanCategoryListOp()
    {
        $param = array_merge(array(),$_GET,$_POST);
        $lang = $param['lang'];
        $list = loan_categoryClass::getAllCategoryList();
        $return = array();
        foreach( $list as $v){

            $v['category_lang'] = my_json_decode($v['category_lang']);
            if( $v['category_lang'][$lang] ){
                $v['category_name'] = $v['category_lang'][$lang];
            }
            unset($v['product_qualification']);
            unset($v['product_feature']);
            unset($v['product_required']);
            unset($v['product_notice']);

            // 计算产品最低月利率
            $min = loan_productClass::getMinMonthlyRate($v['default_product_id']);
            $v['min_rate'] = $min;
            $v['min_rate_desc'] = $min.'%';
            $return[] = $v;
        }

        return new result(true,'success',array(
            'list' => $return
        ));

    }


    public function getCategoryDetailOp()
    {
        $param = array_merge(array(),$_GET,$_POST);
        $cate_id = $param['cate_id'];

        $data = loan_categoryClass::getCategoryDetailInfoById($cate_id);
        if( !$data['product_info'] ){
            return new result(false,'No info:'.$cate_id,null,errorCodesEnum::INVALID_PARAM);
        }
        return new result(true,'success',$data);

    }



}