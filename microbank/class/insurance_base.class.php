<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/16
 * Time: 12:04
 */
class insurance_baseClass
{
    public function __construct()
    {
    }


    public static function getContractDetail($contract_id)
    {
        $contract_id = intval($contract_id);
        $m_contract = new insurance_contractModel();
        $insurance_contract = $m_contract->getRow($contract_id);
        if( !$insurance_contract ){
            return  new result(false,'No contract',null,errorCodesEnum::NO_CONTRACT);
        }

        // 产品主项
        $m_product = new insurance_productModel();
        $insurance_product = $m_product->getRow($insurance_contract->product_id);
        $insurance_product = $insurance_product?:null;

        // 产品细目
        $m_item = new insurance_product_itemModel();
        $item = $m_item->getRow($insurance_contract->product_item_id);
        $item = $item?:null;

        // 缴费计划
        $m_schema = new insurance_payment_schemeModel();
        $rows = $m_schema->select(array(
            'contract_id' => $contract_id
        ));
        $schemas = array();
        foreach( $rows as $v ){
            $item = $v;
            $item['payable_date'] = date('Y-m-d',strtotime($v['payable_date']));
            $item['expire_date'] = $v['expire_date']?date('Y-m-d',strtotime($v['expire_date'])):null;
            $schemas[] = $item;
        }


        // 受益人
        $m_benefit = new insurance_contract_beneficiaryModel();
        $benefit = $m_benefit->select(array(
            'contract_id' => $insurance_contract->uid
        ));


        // 相关贷款合同
        $loan_contract = null;
        if( $insurance_contract->loan_contract_id ){
            $m_loan = new loan_contractModel();
            $loan_contract = $m_loan->getRow($insurance_contract->loan_contract_id);
            $loan_contract = $loan_contract?:null;
        }

        return new result(true,'success',array(

            'insurance_contract' => $insurance_contract,
            'insurance_product' => $insurance_product,
            'insurance_product_item' => $item,
            'beneficiary' => $benefit,
            'insurance_payment_schema' => $schemas,
            'loan_contract' => $loan_contract
        ));
    }

}