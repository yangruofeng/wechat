<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/17
 * Time: 10:37
 */
class installmentPrepaymentClass extends loanPrepaymentClass
{

    public function getPrepaymentDetailByAllPaid($deadline_date)
    {
        $contract = $this->contract_info;

        $un_complete_schema = (new loan_contractModel())->getContractUncompletedSchemas($contract['uid']);
        if( count($un_complete_schema) < 1 ){
            return new result(false,'Contract paid off.',null,errorCodesEnum::CONTRACT_BEEN_PAID_OFF);
        }

        $payable_penalty = 0;
        $today = date('Y-m-d');
        $overdue_schema = array();
        $normal_left_schema = array();

        // 可提前还的本金部分
        $remainingPrincipal = 0;
        foreach( $un_complete_schema as $v ){

            if ($v['receivable_date'] < $today) {
                $overdue_schema[] = $v;
                $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
                $payable_penalty += $penalty;
            } else {
                $remainingPrincipal += $v['receivable_principal']-$v['paid_principal'];
                $normal_left_schema[] = $v;
            }
        }

        // 没有可提前还款的金额
        /*if( empty($normal_left_schema) ){
            return new result(false,'No prepayment amount.',null,errorCodesEnum::NOT_SUPPORT_PREPAYMENT);

        }*/

        $fistInterestDate = reset($normal_left_schema)['interest_date']?:$today;


        $currentOutstandingSchema = $overdue_schema;
        // 计算计息日前的金额
        $outstanding_amount = $outstanding_principal = $outstanding_interest = $outstanding_operation_fee = 0;
        foreach ($currentOutstandingSchema as $item) {
            $outstanding_principal += $item['receivable_principal'];
            $outstanding_interest += $item['receivable_interest'];
            $outstanding_operation_fee += $item['receivable_operation_fee'];
            $outstanding_amount += ($outstanding_principal+$outstanding_interest+$outstanding_operation_fee);
        }


        // 提前还款全部本金
        //$remainingPrincipal = reset($normal_left_schema)['initial_principal'];

        // 合同剩余本金,逾期的不计入内
        $contractLeftPrincipal = $remainingPrincipal;

        $prepaymentAmount = $outstanding_amount + $remainingPrincipal;
        $remainingPeriod = 0;
        $leftPrincipal = 0;


        $contractInterestInfo = loan_contractClass::getContractInterestInfoByContractInfo($contract);
        $interestClass = interestTypeClass::getInstance($contract['repayment_type'],$contract['repayment_period']);

        $prepaymentInfo = $interestClass->calculatePrepaymentInfo($contract,$currentOutstandingSchema,$contractLeftPrincipal,
            $fistInterestDate,$contractInterestInfo,$deadline_date,$prepaymentAmount,$leftPrincipal,$remainingPeriod,$normal_left_schema);

        // 计算损失的利息

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
            'left_schema' => $prepaymentInfo['remaining_schemas'],
            'remaining_total_amount' => $prepaymentInfo['remaining_total_amount']
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
        // TODO: Implement getPrepaymentDetailByPaidPeriod() method.
        return new result(false,'Not support yet.',null,errorCodesEnum::NOT_SUPPORTED);
    }

}