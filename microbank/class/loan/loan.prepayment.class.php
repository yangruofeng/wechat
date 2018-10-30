<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/17
 * Time: 10:42
 */
abstract class loanPrepaymentClass
{
    protected $contract_info;
    protected $member_info;
    protected $apply_info;

    public function __construct($contract_info)
    {
        $this->contract_info = $contract_info;
    }

    public static function getInstance($contract_info)
    {
        if( interestTypeClass::isPeriodicRepayment($contract_info['repayment_type']) ){
            return new installmentPrepaymentClass($contract_info);
        }else{
            return new singlePrepaymentClass($contract_info);
        }
    }

    /**
     * @param $deadline_date
     * @return mixed
     * 'cut_off_date' => 截止日期,
     * 'total_prepayment_amount' => 合计应还总额,
     * 'currency' => $contract['currency'],
     * 'total_paid_principal' => 合计还本金,
     * 'total_paid_interest' => 合计还利息,
     * 'total_paid_operation_fee' => 合计还运营费,
     * 'total_paid_penalty' => 合计还罚金,
     * 'prepayment_principal' => 提前还本金金额,
     * 'prepayment_fee' => 0,
     * 'left_schema' => 剩余新计划
     */
    abstract function getPrepaymentDetailByAllPaid($deadline_date);

    abstract function getPrepaymentDetailByPartialPrincipalPayment($principal_amount,$deadline_date);

    abstract function getPrepaymentDetailByPaidPeriod($paid_period,$deadline_date);




}