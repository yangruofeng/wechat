<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/9/2
 * Time: 7:55
 */
class member_loanV2Control extends member_loanControl{
    public function ajaxGetLoanCategoryOptionOp($p)
    {
        $sub_product_id = intval($p['sub_product_id']);
        $member_category_id = intval($p['m_uid']);  // member_credit_category.id
        $member_id = intval($p['member_id']);
        $rt = credit_loanClass::getMemberLoanOptionByCategoryNew($member_id,$member_category_id);
        if(!$rt->STS){
            return new result(false,$rt->MSG);
        }
        $product=$rt->DATA;
        // 展示利率信息
        $list = counter_memberClass::getMemberLoanProductInterestList($member_category_id, $sub_product_id, $member_id);
        $ret['product']=array_merge($product,$p);
        $ret['member_id']=$p['member_id'];
        $ret['page_tip']=$product['page_tip'];
        $ret['rate_list']=$list;
        return new result(true,"",$ret);
    }

    public function loanContractViewDetailOp()
    {
        $params = array_merge($_GET,$_POST);
        $contract_id = $params['contract_id'];
        $rt = loan_contractClass::getLoanContractDetailInfo($contract_id);
        if( !$rt->STS ){
            showMessage($rt->MSG);
        }else{
            Tpl::output('contract_detail_data',$rt->DATA);
            Tpl::showPage('loan.contract.view.detail');
        }
    }

}