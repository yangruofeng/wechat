<?php

class advanceSingleRepaymentClass extends singleRepaymentClass {
    /**
     * @param $totalAmount  float          贷款金额
     * @param $repaymentPeriods array      各个还款周期时间长度
     * @param $interestInfo array (        利率及其他费用信息
     *  interest_rate                          每期利率/利息金额值
     *  interest_rate_type                     interest_rate的意义：0:利率/1:利息金额
     *  interest_min_value                     最小总利息额
     *  operation_fee                          每期运营费率/运营费金额
     *  operation_fee_type                     operation_fee的意义：0:费率/1:费额
     *  operation_min_value                    最小总运营费额
     * )
     * @return result 其中DATA格式： array(
     *  payment_total=> array(
     *   total_payment  总应还金额
     *   total_principal  本金
     *   total_interest   总利息
     *   total_operator_fee  总运营费
     *   total_period_pay
     *   deduct_interest  预扣利息
     *   deduct_operation_fee  预扣营运费
     *  ),
     *  payment_schema=> array(   还款计划
     *    array(
     *      scheme_index   还款期序号
     *      receivable_principal   当期应还本金
     *      receivable_interest    当期应还利息
     *      receivable_operation_fee  当期营运费
     *      amount                    当期应还总额
     *      remaining_principal       合同剩余本金
     *    ), ...
     *  )
     * )
     */
    public function getInstallmentSchema($totalAmount, $repaymentPeriods, $interestInfo)
    {
        $ret = parent::getInstallmentSchema($totalAmount, $repaymentPeriods, $interestInfo);
        if (!$ret->STS) return $ret;

        $data = $ret->DATA;
        // 将总利息/总运营费放到预扣利息/预扣运营费中，本还款计划中只保留应还本金
        $schema = array();
        foreach ($data['payment_schema'] as $item) {
            $item['receivable_interest'] = 0;
            $item['receivable_operation_fee'] = 0;
            $item['amount'] = $item['receivable_principal'];
            $schema[]=$item;
        }

        $service_fee = 0;
        if( $interestInfo['service_fee'] > 0 ){
            if( $interestInfo['service_fee_type'] == 1 ){
                $service_fee = $interestInfo['service_fee'];
            }else{
                $service_fee = round($totalAmount*($interestInfo['service_fee']/100),2);
            }
        }

        return new result(true, null, array(
            'payment_total' => array_merge($data['payment_total'], array(
                'deduct_interest' => $data['payment_total']['total_interest'],
                'deduct_operation_fee' => $data['payment_total']['total_operator_fee'],
                'deduct_service_fee' => $service_fee

            )),
            'payment_schema' => $schema,
        ));
    }

    public function calculatePrepaymentInfo($contract_info,$currentOutstandingSchemas, $contractRemainingPrincipal, $firstOutstandingSchemaInterestDate, $interestInfo, $cutOffDate, $prepaymentAmount, $remainingPrincipal, $remainingPeriodCount,$contract_remain_schemas=null)
    {
        // 已经提前还了利息了,剩余的全是本金
        $cutOffDate = date('Y-m-d');


        $outstanding_principal = 0;
        $outstanding_interest = 0;
        $outstanding_operation_fee = 0;

        foreach ($currentOutstandingSchemas as $item) {
            $outstanding_principal += $item['receivable_principal']-$item['paid_principal'];
            $outstanding_interest += $item['receivable_interest']-$item['paid_interest'];
            $outstanding_operation_fee += $item['receivable_operation_fee']-$item['paid_operation_fee'];
        }

        $outstanding_amount = $outstanding_principal+$outstanding_interest+$outstanding_operation_fee;


        // 必须还清剩余本金
        $prepaymentPrincipal = $contractRemainingPrincipal;
        $prepaymentAmount = $prepaymentPrincipal + $outstanding_amount;

        $remaining_schemas = null;
        $remaining_total_amount = array();

        return array(
            'cut_off_date' => $cutOffDate,
            'prepayment_principal' => $prepaymentPrincipal,
            'total_principal' => $prepaymentPrincipal+$outstanding_principal,
            'total_interest' => $outstanding_interest,
            'total_operation_fee' => $outstanding_operation_fee,
            'total_amount' => $prepaymentAmount,
            'remaining_schemas' => $remaining_schemas,
            'remaining_total_amount' => $remaining_total_amount
        );
    }

}