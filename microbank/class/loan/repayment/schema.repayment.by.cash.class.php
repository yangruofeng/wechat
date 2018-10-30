<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/2
 * Time: 11:25
 */
class schemaRepaymentByCashClass extends schemaNormalRepaymentClass
{
    protected $cashier_id;
    protected $schema_id;
    protected $payment_amount;
    protected $payment_currency;


    public function __construct($cashier_id,$schema_id,$penalty,$payment_amount,$payment_currency,$multi_currency=null)
    {
        $scheme_info = (new loan_installment_schemeModel())->getRow($schema_id);
        if( !$scheme_info ){
            throw new Exception('Unknown schema id: '.$this->schema_id,errorCodesEnum::NO_DATA);
        }

        $contract_info = (new loan_contractModel())->getRow($scheme_info->contract_id);
        if( !$contract_info ){
            throw new Exception('No contract info of id:'.$scheme_info->contract_id,errorCodesEnum::NO_CONTRACT);
        }

        $member_info = loan_contractClass::getLoanContractMemberInfo($scheme_info->contract_id);
        if( !$member_info ){
            throw new Exception('No loan member info.',errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $this->cashier_id = $cashier_id;
        $this->schema_id = $schema_id;
        $this->penalty = $penalty;
        if( !empty($multi_currency) ){
            $this->multi_currency = $multi_currency;
        }else{
            $this->multi_currency = array();
            $this->multi_currency[$payment_currency] = $payment_amount;
        }

        $this->schema_info = $scheme_info;
        $this->contract_info = $contract_info;
        $this->member_info = $member_info;
    }

    public function getPayerInfo()
    {
        $handler_info = member_handlerClass::getMemberDefaultCashHandlerInfo($this->member_info['uid']);
        return $handler_info;
    }

    public function getCreatorInfo()
    {
        $userObj = new objectUserClass($this->cashier_id);
        return array(
            'creator_id' => $userObj->user_id,
            'creator_name' => $userObj->user_name,
            'teller_id' => $userObj->user_id,
            'teller_name' => $userObj->user_name,
            'branch_id' => $userObj->branch_id
        );
    }

    public function repaymentExecute()
    {

        // 外面先存钱（存钱不换汇），然后统一从余额里面扣钱
        /*$scheme_info = $this->schema_info;
        $member_info = $this->member_info;
        $member_id = $member_info['uid'];

        $schema_amount = $scheme_info['amount'] - $scheme_info['actual_payment_amount'];
        $currency = $this->contract_info['currency'];

        $memberObj = new objectMemberClass($member_id);
        $member_balance = $memberObj->getSavingsAccountBalance();
        $paid_currency_amount = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($schema_amount,$currency,$member_balance);
        $this->multi_currency = $paid_currency_amount->DATA['multi_currency'];*/

        $rt = $this->execute($this->payment_amount,$this->payment_currency);
        return $rt;

    }

}