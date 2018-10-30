<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/17
 * Time: 15:51
 */
class loanPrepaymentByBalanceClass extends prepaymentPayBaseClass
{

    protected $member_id;
    protected $cashier_id;

    public function __construct($apply_id,$cashier_id=0,$amount=null,$currency=null,$multi_currency=array())
    {

        $apply_info = (new loan_prepayment_applyModel())->getRow($apply_id);
        if( !$apply_info ){
            throw new Exception('No apply info:'.$apply_id,errorCodesEnum::INVALID_PARAM);
        }

        $contract_info = (new loan_contractModel())->getRow($apply_info->contract_id);
        if( !$contract_info ){
            throw new Exception('No contract info:'.$apply_info->contract_id,errorCodesEnum::NO_CONTRACT);
        }

        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_info['uid']);
        if( !$member_info ){
            throw new Exception('Unknown loan client.',errorCodesEnum::MEMBER_NOT_EXIST);
        }


        $this->apply_info = $apply_info;
        $this->contract_info = $contract_info;
        $this->member_id = $member_info['uid'];
        $this->member_info = $member_info;
        $this->cashier_id = $cashier_id;

        $this->paid_amount = $amount;
        $this->paid_currency = $currency;

        if( $multi_currency ){
            $this->multi_currency = $multi_currency;
        }else{
            $this->multi_currency = array(
                $currency => $amount
            );
        }


    }


    public function getPayerInfo()
    {
        return member_handlerClass::getMemberDefaultPassbookHandlerInfo($this->member_info['uid']);
    }

    public function getHandlerInfo()
    {
        $handler_info = array();
        if( $this->cashier_id ){
            $cashier = new objectUserClass($this->cashier_id);
            $handler_info['handler_id'] = $cashier->user_id;
            $handler_info['handler_name'] = $cashier->user_name;
        }else{
            $handler_info['handler_id'] = 0;
            $handler_info['handler_name'] = 'System';
        }
        return $handler_info;
    }

    public function execute()
    {
        $rt = $this->recalculateAmount();
        if( !$rt->STS ){
            return $rt;
        }

        $currency = $this->contract_info['currency'];
        $paid_total = $this->apply_info['total_payable_amount'];

        $memberObj = new objectMemberClass($this->member_info['uid']);
        $balance = $memberObj->getSavingsAccountBalance();

        $rt = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($paid_total,$currency,$balance);

        if( !$rt->STS ){

            return new result(false,'Balance not enough.',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
        }

        $multi_currency = $rt->DATA['multi_currency'];
        $this->multi_currency = $multi_currency;


        return $this->handle();
    }

}