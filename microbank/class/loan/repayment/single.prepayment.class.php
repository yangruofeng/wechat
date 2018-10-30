<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/17
 * Time: 10:39
 */
class singlePrepaymentClass extends loanPrepaymentClass
{

    public function getPrepaymentDetailByAllPaid($deadline_date)
    {
        $contract = $this->contract_info;

        $m_contract = new loan_contractModel();

        $left_schema = $m_contract->getContractUncompletedSchemas($contract['uid']);
        if( count($left_schema) > 1 ){
            return new result(false,'Not single repayment:'.$contract['uid'],null,errorCodesEnum::UNEXPECTED_DATA);
        }

        $today = date('Y-m-d');
        $remainingSchema = current($left_schema);
        if( $remainingSchema['receivable_date'] < $today ){
            return new result(false,'No prepayment amount.',null,errorCodesEnum::NOT_SUPPORT_PREPAYMENT);
        }

        $payable_penalty = 0;

        // 提前还款全部本金
        $remainingPrincipal = $remainingSchema['receivable_principal']-$remainingSchema['paid_principal'];

        // 合同合计剩余本金
        $contractLeftPrincipal = $remainingPrincipal;

        $prepaymentAmount = $remainingPrincipal;
        $remainingPeriod = 0;
        $leftPrincipal = 0;

        $fistInterestDate = $remainingSchema['interest_date'];

        $contract_interest_info = loan_contractClass::getContractInterestInfoByContractInfo($contract);
        $interestClass = interestTypeClass::getInstance($contract['repayment_type'],$contract['repayment_period']);

        $prepaymentInfo = $interestClass->calculatePrepaymentInfo($contract,array(),$contractLeftPrincipal,
            $fistInterestDate,$contract_interest_info,$deadline_date,$prepaymentAmount,$leftPrincipal,$remainingPeriod,array($remainingSchema));

        $info = array(
            'total_prepayment_amount' => $prepaymentInfo['total_amount'],
            'currency' => $contract['currency'],
            'cut_off_date' => $prepaymentInfo['cut_off_date'],
            'total_paid_principal' => $prepaymentInfo['total_principal'],
            'total_paid_interest' => $prepaymentInfo['total_interest'],
            'total_paid_operation_fee' => $prepaymentInfo['total_operation_fee'],
            'total_paid_penalty' => $payable_penalty,
            'prepayment_principal' => $prepaymentInfo['prepayment_principal'],
            'prepayment_fee' => 0,
            'left_schema' => $prepaymentInfo['remaining_schemas']
        );

        return new result(true,'success',$info);

    }

    public function getPrepaymentDetailByPartialPrincipalPayment($principal_amount, $deadline_date)
    {
        // TODO: Implement getPrepaymentDetailByPartialPrincipalPayment() method.
        return new result(false,'Not support yet.',null,errorCodesEnum::NOT_SUPPORTED);
    }

    public function getPrepaymentDetailByPaidPeriod($paid_period, $deadline_date)
    {
        return $this->getPrepaymentDetailByAllPaid($deadline_date);
    }
}