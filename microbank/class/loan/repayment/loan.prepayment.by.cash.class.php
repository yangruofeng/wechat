<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/17
 * Time: 15:50
 */
class loanPrepaymentByCashClass extends prepaymentPayBaseClass
{

    protected $member_id;
    protected $cashier_id;
    protected $cashierObj;
    protected $amount;
    protected $currency;
    protected $exchange_currency_amount=array();


    public function __construct($apply_id,$cashier_id,$amount,$currency,$multi_currency=null,$exchange_currency_amount=array())
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

        $this->cashierObj = new objectUserClass($cashier_id);

        $amount = round($amount,2);
        $this->apply_info = $apply_info;
        $this->contract_info = $contract_info;
        $this->member_id = $member_info['uid'];
        $this->member_info = $member_info;
        $this->cashier_id = $cashier_id;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->multi_currency = $multi_currency;
        $this->exchange_currency_amount = $exchange_currency_amount;

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
        return member_handlerClass::getMemberDefaultCashHandlerInfo($this->member_info['uid']);
    }

    public function getHandlerInfo()
    {
        $handler_info = array();
        $handler_info['handler_id'] = $this->cashierObj->user_id;
        $handler_info['handler_name'] = $this->cashierObj->user_name;
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
        $rt = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($paid_total,$currency,$this->multi_currency);
        if( !$rt->STS ){
            return new result(false,'Amount not enough.',null,errorCodesEnum::AMOUNT_TOO_LITTLE);
        }

        // 构建详细的memo
        $userObj = $this->cashierObj;
        $member_info = $this->member_info;

        $currency_str_arr = array();
        foreach( $this->multi_currency as $c=>$a ){
            $currency_str_arr[] = $a.$c;
        }

        $sys_memo = " Loan prepayment by cash,contract sn:".$this->contract_info['contract_sn']
            .',client '.($member_info['display_name']?:$member_info['login_code']).'('.$member_info['obj_guid'].')'
            .',repayment amount:'.implode(',',$currency_str_arr).'.cashier:'.$userObj->user_name.
            '('.$userObj->user_code.')'.',branch '.$userObj->branch_name;

        // 存钱不换汇
        $remark = "Loan prepayment(".$this->contract_info['contract_sn']."):cashier ".$this->cashierObj->user_name;
        $depositClass = new memberDepositByCashTradingClass(
            $this->member_id,
            $this->cashier_id,
            $this->amount,
            $this->currency,
            $this->multi_currency);
        $depositClass->remark = $remark;
        $depositClass->sys_memo = $sys_memo;
        $rt = $depositClass->execute();
        if( !$rt->STS ){
            return $rt;
        }

        $this->ref_trade_id = $rt->DATA;

        return $this->handle();

    }

}