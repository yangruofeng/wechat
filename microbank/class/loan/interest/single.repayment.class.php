<?php

class singleRepaymentClass extends onetimeRepaymentClass {

    /**
     * 计算还款计划
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
        $repaymentPeriodCount = count($repaymentPeriods);
        $interestPeriods = array_map(function($item)use($interestInfo){
            return $this->normalizeInterestInfo($interestInfo, $item[0], $item[1]);
        }, $repaymentPeriods);
        if ($repaymentPeriodCount != 1)
            return new result(false, 'repayment period count must be 1', null, errorCodesEnum::UNKNOWN_ERROR);
        $interestInfo = $interestPeriods[0];

        $schema = array();
        $temp = array();

        // 基本利息
        if ($interestInfo['interest_rate_type'] == 1) {
            $current_interest = $interestInfo['interest_rate'];
        } else {
            $current_interest = $totalAmount*$interestInfo['interest_rate']/100;
        }
        // 运营费
        if( $interestInfo && $interestInfo['operation_fee'] >0){
            if ($interestInfo['operation_fee_type'] == 1) {
                $current_operator_fee = $interestInfo['operation_fee']?round($interestInfo['operation_fee'],2):0;
            }else{
                $current_operator_fee = $totalAmount*$interestInfo['operation_fee']/100;
            }
        } else {
            $current_operator_fee = 0;
        }

        // 最低限制
        ////////////////////////////////////////////////////////////////////
        // 利息费
        $min_interest = 0;
        if( $interestInfo['interest_min_value'] ) {
            $min_interest = round($interestInfo['interest_min_value'], 2);
        }

        // 运营费
        $min_operator_fee = 0;
        if( $interestInfo['operation_min_value'] ) {
            $min_operator_fee = round($interestInfo['operation_min_value'], 2);
        }

        // 调整，利息和operation fee 分开
        if( $current_interest < $min_interest ){
            $current_interest = $min_interest;
        }
        if( $current_operator_fee < $min_operator_fee ){
            $current_operator_fee = $min_operator_fee;
        }

        /////////////////////////////////////////////////////////////////////

        $temp['scheme_index'] = 1;
        $temp['initial_principal'] = round($totalAmount,2);
        $temp['receivable_principal'] = round($totalAmount,2);
        $temp['receivable_interest'] = round($current_interest,2);
        $temp['receivable_operation_fee'] = round($current_operator_fee,2);
        $temp['remaining_principal'] = 0.00;
        $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+ $temp['receivable_operation_fee'];
        $temp['receive_date'] = $repaymentPeriods[0][2];

        $schema[] = $temp;
        $total_pay_principal = $temp['receivable_principal'];
        $total_pay_interest = $temp['receivable_interest'];
        $total_pay_operator_fee = $temp['receivable_operation_fee'];
        $total_pay_amount = $temp['amount'];
        $period_pay = $total_pay_amount;


        $service_fee = 0;
        if( $interestInfo['service_fee'] > 0 ){
            if( $interestInfo['service_fee_type'] == 1 ){
                $service_fee = $interestInfo['service_fee'];
            }else{
                $service_fee = round($totalAmount*($interestInfo['service_fee']/100),2);
            }
        }

        return new result(true,'success',array(
            'payment_total' => array(
                'total_payment' => $total_pay_amount,
                'total_principal' => $total_pay_principal,
                'total_interest' => $total_pay_interest,
                'total_operator_fee' => $total_pay_operator_fee,
                'total_period_pay' => $period_pay,
                'deduct_service_fee' => $service_fee
            ),
            'payment_schema' => $schema,
        ));
    }

}