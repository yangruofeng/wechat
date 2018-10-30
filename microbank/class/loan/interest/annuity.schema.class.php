<?php

class annuitySchemaClass extends periodicRepaymentClass {
    public function __construct($repaymentPeriodType)
    {
        parent::__construct($repaymentPeriodType);
    }

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

        $repaymentPeriodCount = count($repaymentPeriods);
        $interestPeriods = array_map(function($item)use($interestInfo){
            return $this->normalizeInterestInfo($interestInfo, $item[0], $item[1],true);
        }, $repaymentPeriods);


        //$baseInterestInfo = end($interestPeriods);  // 不是真实的利率信息
        // 取得利率实际分期利率值
        $baseInterestInfo = $this->getPeriodicPeriodRate($interestInfo,$this->repayment_period_type,true);
        if ($baseInterestInfo['operation_fee_type'] == 1) {
            $per_operation_fee = round($baseInterestInfo['operation_fee'], 2);
        } else {
            $per_operation_fee = round($totalAmount * $baseInterestInfo['operation_fee'] / 100,2);
        }

        reset($interestPeriods);
        if ($baseInterestInfo['interest_rate_type'] == 1)
            return new result(false, 'Fixed interest amount per period is not supported by annuity schema', null, errorCodesEnum::UNKNOWN_ERROR);

        $schema = array();

        $min_interest = round($baseInterestInfo['interest_min_value'], 2);
        $min_operator_fee = round($baseInterestInfo['operation_min_value'], 2);

        // 剩余本金
        $remaining_principal = $totalAmount;
        $total_pay = $total_interest = $total_o_fee = 0;
        $pay_principal = 0;

        // 按公式计算每期还款金额
        $interestRate = $baseInterestInfo['interest_rate'] / 100;
        /*if ($baseInterestInfo['operation_fee_type'] == 1) {
            $operationRate = 0;
        } else {
            $operationRate = $baseInterestInfo['operation_fee'] / 100;
        }*/
        $operationRate = 0;
        // 运营费和利息一起计算
        $calc_rate = $interestRate + $operationRate;
        $pay_amount = ($totalAmount * $calc_rate * pow(1 + $calc_rate, $repaymentPeriodCount)) / (pow(1 + $calc_rate, $repaymentPeriodCount) - 1);

        // 没有利息的情况
        if( $pay_amount <= 0 ){
            $pay_amount = $totalAmount/$repaymentPeriodCount;
        }

        // 调整小数问题
        $pay_amount = ceil($pay_amount+$per_operation_fee);



        for ($i = 1; $i <= $repaymentPeriodCount; $i++) {
            $interestCurrent = $interestPeriods[$i-1];
            $period_current = $repaymentPeriods[$i-1];

            if( $remaining_principal <= 0 ){
                break;
            }

            // 本期应还利息
            $current_interest = round($remaining_principal * $interestCurrent['interest_rate'] / 100,2);
            // 本期应还ope_fee
            $current_operation_fee = $per_operation_fee;

            $temp = array();
            $temp['scheme_index'] = $i;

            // 本期应还本金
            if ($i < $repaymentPeriodCount) {
                // 本期应还总利息

                $interest_operator  = round($current_interest+$current_operation_fee,2);

                $current_principal = $pay_amount - $interest_operator;
                // 因为有向上取整，需要处理小额的问题
                if( $current_principal >= $remaining_principal ){
                    $current_principal = $remaining_principal;
                    $current_interest = round($remaining_principal * $interestCurrent['interest_rate'] / 100,2);
                    $pay_amount = $current_principal+$current_interest+$per_operation_fee;
                }
            } else {
                // 修正本金
                $current_principal = $remaining_principal;

                // 修正最低值
                if ($total_interest + $current_interest < $min_interest) {
                    $current_interest = round($min_interest - $total_interest,2);
                }
                if ($total_o_fee + $current_operation_fee < $min_operator_fee) {
                    $current_operation_fee = round($min_operator_fee - $total_o_fee,2);
                }
            }

            // 规避利息超出还款总额的BUG
            if( $current_principal < 0 ){
                $current_principal = 0;
            }



            $temp['initial_principal'] = round($remaining_principal,2);
            $temp['receivable_principal'] = round($current_principal, 2);
            $temp['receivable_interest'] = round($current_interest, 2);
            $temp['receivable_operation_fee'] = round($current_operation_fee, 2);

            $temp['receive_date'] = $period_current[2];
            // 剩余本金
            if($interestInfo['currency']==currencyEnum::KHR){
                $temp['amount'] = $temp['receivable_principal'] + $temp['receivable_interest'] + $temp['receivable_operation_fee'];

                $temp['receivable_interest']=floor($temp['receivable_interest']/100)*100+($temp['receivable_interest']%100>=50?100:0);
                $temp['receivable_operation_fee']=floor($temp['receivable_operation_fee']/100)*100+($temp['receivable_operation_fee']%100>=50?100:0);
                $temp['amount']=floor($temp['amount']/100)*100+($temp['amount']%100>=50?100:0);
                if ($i < $repaymentPeriodCount){
                    $remainder= $temp['amount']%1000;
                    if($remainder>0){
                        $ext_amt=1000-$remainder;
                        $temp['amount']+=$ext_amt;
                    }
                }
                $temp['receivable_principal']=$temp['amount']-$temp['receivable_interest']-$temp['receivable_operation_fee'];
                $pay_principal += $temp['receivable_principal'];
                $remaining_principal = $remaining_principal - $temp['receivable_principal'];
                if( $remaining_principal <= 0 ){
                    $remaining_principal = 0;
                }
                $temp['remaining_principal'] = $remaining_principal;

            }else{
                $pay_principal += $temp['receivable_principal'];
                $remaining_principal = $remaining_principal - $temp['receivable_principal'];
                if( $remaining_principal <= 0 ){
                    $remaining_principal = 0;
                }
                $temp['remaining_principal'] = $remaining_principal;
                $temp['amount'] = $temp['receivable_principal'] + $temp['receivable_interest'] + $temp['receivable_operation_fee'];

            }


            $schema[] = $temp;
            $total_interest += $temp['receivable_interest'];
            $total_o_fee += $temp['receivable_operation_fee'];
            $total_pay += $temp['amount'];
        }

        $service_fee = 0;
        if( $interestInfo['service_fee'] > 0 ){
            if( $interestInfo['service_fee_type'] == 1 ){
                $service_fee = $interestInfo['service_fee'];
            }else{
                $service_fee = round($totalAmount*($interestInfo['service_fee']/100),2);
            }
        }


        return new result(true, 'success', array(
            'payment_total' => array(
                'total_payment' => $total_pay,
                'total_principal' => $totalAmount,
                'total_interest' => $total_interest,
                'total_operator_fee' => $total_o_fee,
                'total_period_pay' => round($total_pay / $repaymentPeriodCount, 2),
                'deduct_service_fee' => $service_fee
            ),
            'payment_schema' => $schema,
        ));
    }

    /**
     * 根据计划还款期数与每期还款金额计算期初贷款金额
     * @param $periodCount int      计划还款期数
     * @param $periodAmount float   每期还款金额
     * @param $interestInfo array   利率信息
     * @return float  期初贷款金额（本金）
     */
    public function calculateInitialPrincipalByPeriodAmount($periodCount, $periodAmount, $interestInfo) {
        $normalized_interest_info = $this->normalizeInterestInfo($interestInfo, null, null);

        // 根据剩余计划倒推应该剩余的本金
        $interest_rate = 0;
        $interest_amount = 0;
        if ($normalized_interest_info['interest_rate_type'] == 0) {
            $interest_rate += $normalized_interest_info['interest_rate']/100;
        } else {
            $interest_amount += $normalized_interest_info['interest_rate'];
        }
        if ($normalized_interest_info['operation_fee'] && $normalized_interest_info['operation_fee_type'] == 0) {
            $interest_rate += $normalized_interest_info['operation_fee']/100;
        } else {
            $interest_amount += $normalized_interest_info['operation_fee'];
        }

        $initialPrincipal = 0;
        for ($i=$periodCount;$i>0;$i--) {
            // 利息 = 期初本金 * 利率 + 利额
            // 还款金额 = 期初本金 - 期末本金 + 利息 = 期初本金 - 期末本金 + 期初本金 * 利率 + 利额
            // 期初本金 = (还款金额 + 期末本金 - 利额) / (1+利率)
            $initialPrincipal = ($periodAmount + $initialPrincipal - $interest_amount) / (1 + $interest_rate);
        }

        return $initialPrincipal;
    }


    public function calculatePrepaymentInfo($contract_info,$currentOutstandingSchemas, $contractRemainingPrincipal, $firstOutstandingSchemaInterestDate, $interestInfo, $cutOffDate, $prepaymentAmount, $remainingPrincipal, $remainingPeriodCount,$contract_remain_schemas=null)
    {
        if( !$cutOffDate ){
            $cutOffDate = date("Y-m-d");
        }
        if (!empty($currentOutstandingSchemas)) {
            $cutOffDate = max($cutOffDate,end($currentOutstandingSchemas)['receivable_date']);
        }

        return parent::calculatePrepaymentInfo($contract_info,$currentOutstandingSchemas, $contractRemainingPrincipal, $firstOutstandingSchemaInterestDate, $interestInfo, $cutOffDate, $prepaymentAmount, $remainingPrincipal, $remainingPeriodCount,$contract_remain_schemas);
    }
}