<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/29
 * Time: 17:36
 */
class schemaRepaymentByBankClass extends schemaNormalRepaymentClass
{

    protected $cashier_id;
    protected $schema_id;
    protected $system_bank_info;
    protected $payment_amount;
    protected $payment_currency;

    public function __construct($cashier_id,$system_bank_info,$schema_id,$penalty,$payment_amount,$payment_currency,$multi_currency=null)
    {
        $scheme_info = (new loan_installment_schemeModel())->getRow($schema_id);
        if( !$scheme_info ){
            throw new Exception('Unknown schema id: '.$this->schema_id);
        }

        $contract_info = (new loan_contractModel())->getRow($scheme_info->contract_id);
        if( !$contract_info ){
            throw new Exception('No contract info of id:'.$scheme_info->contract_id);
        }

        $member_info = loan_contractClass::getLoanContractMemberInfo($scheme_info->contract_id);
        if( !$member_info ){
            throw new Exception('No loan member info.');
        }

        $this->cashier_id = $cashier_id;
        $this->schema_id = $schema_id;
        $this->penalty = $penalty;
        $this->system_bank_info = $system_bank_info;
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
        // todo member的账户信息
        return array(
            'uid' => 0,
            'handler_type' => memberAccountHandlerTypeEnum::BANK,
            'handler_name' => $this->system_bank_info['bank_name'],
            'handler_account' => $this->system_bank_info['bank_account_no'],
            'handler_phone' => $this->system_bank_info['bank_account_phone'],
            'handler_property' => null,
        );
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

        // 外部先将钱存入balance(因为有合并还款的，存一次就好)

        if( $this->multi_currency ){

            return $this->execute();

        }else{

            if( $this->payment_amount >0 && $this->payment_currency ){

                $this->multi_currency = null;
                $rt = $this->execute($this->payment_amount,$this->payment_currency);
                return $rt;

            }else{

                $scheme_info = $this->schema_info;
                $member_info = $this->member_info;
                $member_id = $member_info['uid'];

                $schema_amount = $scheme_info['amount'] - $scheme_info['actual_payment_amount'];
                $currency = $this->contract_info['currency'];

                $memberObj = new objectMemberClass($member_id);
                $member_balance = $memberObj->getSavingsAccountBalance();
                $paid_currency_amount = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($schema_amount,$currency,$member_balance);
                $this->multi_currency = $paid_currency_amount->DATA['multi_currency'];
                $rt = $this->execute();
                return $rt;
            }

        }

    }


}