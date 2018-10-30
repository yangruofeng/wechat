<?php
/**
 * Created by PhpStorm.
 * User: muruo
 * Date: 2018/6/25
 * Time: 20:36
 */
class semiBalloonInterestClass extends periodicRepaymentClass
{

    public function __construct($repaymentPeriodType,$principal_paid_month)
    {
        parent::__construct($repaymentPeriodType);
        $this->principal_paid_month = $principal_paid_month;
    }


    public function getPayablePrincipalPeriods($interval_month,$periodCount)
    {
        $principal_period = array();
        $cal_amount = 0;
        switch( $this->repayment_period_type ){
            case interestRatePeriodEnum::YEARLY:
                for( $i=1;$i<=$periodCount;$i++){
                    $cal_amount += 12;
                    if( $cal_amount>=$interval_month ){
                        $principal_period[] = $i;
                        $cal_amount = 0;
                    }
                }
                break;
            case interestRatePeriodEnum::SEMI_YEARLY:
                for( $i=1;$i<=$periodCount;$i++){
                    $cal_amount += 6;
                    if( $cal_amount>=$interval_month ){
                        $principal_period[] = $i;
                        $cal_amount = 0;
                    }
                }
                break;
            case interestRatePeriodEnum::QUARTER:
                for( $i=1;$i<=$periodCount;$i++){
                    $cal_amount += 3;
                    if( $cal_amount>=$interval_month ){
                        $principal_period[] = $i;
                        $cal_amount = 0;
                    }
                }
                break;
            case interestRatePeriodEnum::MONTHLY:
                for( $i=1;$i<=$periodCount;$i++){
                    $cal_amount += 1;
                    if( $cal_amount>=$interval_month ){
                        $principal_period[] = $i;
                        $cal_amount = 0;
                    }
                }
                break;
            case interestRatePeriodEnum::WEEKLY:
                $interval_days = $interval_month*30;
                for( $i=1;$i<=$periodCount;$i++){
                    $cal_amount += 7;
                    if( $cal_amount>=$interval_days ){
                        $principal_period[] = $i;
                        $cal_amount = 0;
                    }
                }
                break;
            case interestRatePeriodEnum::DAILY:
                $interval_days = $interval_month*30;
                for( $i=1;$i<=$periodCount;$i++){
                    $cal_amount += 1;
                    if( $cal_amount>=$interval_days ){
                        $principal_period[] = $i;
                        $cal_amount = 0;
                    }
                }
                break;
            default:

        }
        if( empty($principal_period) ){
            $principal_period[] = $periodCount;
        }
        return $principal_period;

    }


    public function getInstallmentSchema($totalAmount, $repaymentPeriods, $interestInfo)
    {
        $per_operation_fee = $this->getPerPeriodOperationFeeAmount($totalAmount,$interestInfo);

        $repaymentPeriodCount = count($repaymentPeriods);
        $interestPeriods = array_map(function($item)use($interestInfo){
            return $this->normalizeInterestInfo($interestInfo, $item[0], $item[1]);
        }, $repaymentPeriods);

        $baseInterestInfo = end($interestPeriods);
        reset($interestPeriods);

        if ($baseInterestInfo['interest_rate_type'] == 1)
            return new result(false, 'Fixed interest amount per period is not supported by annuity schema', null, errorCodesEnum::UNKNOWN_ERROR);



        // 计算哪些期数应还本金
       /* $principal_paid_days = $this->principal_paid_month?$this->principal_paid_month*30:6*30;
        $cal_sum_days = 0;
        $principal_period = array();
        for( $s=1;$s<=$repaymentPeriodCount;$s++){
            $c_period = $repaymentPeriods[$s-1];
            $rt = loan_baseClass::calLoanDays($c_period[0],$c_period[1]);
            if( !$rt->STS ){
                return $rt;
            }
            $days = $rt->DATA;
            if( $s==1 && $days<30 ){
                $days = 30;  // 矫正一期
            }
            if( $days>=28 && $days < 30 ){
                $days = 30;  // 矫正2月份
            }
            $cal_sum_days += $days;
            if( $cal_sum_days >= $principal_paid_days ){
                $principal_period[] = $s;
                $cal_sum_days = 0;  // 重新累计
            }
        }
        if( empty($principal_period) ){
            $principal_period[] = $repaymentPeriodCount;
        }*/

        $principal_paid_month = $this->principal_paid_month?$this->principal_paid_month:6;
        $principal_period = $this->getPayablePrincipalPeriods($principal_paid_month,$repaymentPeriodCount);

        // 平均每期应还本金
        $period_paid_principal = round($totalAmount/count($principal_period),2);


        $schema = array();

        $min_interest = round($baseInterestInfo['interest_min_value'], 2);
        $min_operator_fee = round($baseInterestInfo['operation_min_value'], 2);

        // 剩余本金
        $remaining_principal = $totalAmount;
        $total_pay = $total_interest = $total_o_fee = 0;
        $pay_principal = 0;


        for ($i = 1; $i <= $repaymentPeriodCount; $i++) {
            $interestCurrent = $interestPeriods[$i-1];
            $period_current = $repaymentPeriods[$i-1];

            // 本期应还利息
            $current_interest = round($remaining_principal * $interestCurrent['interest_rate'] / 100,2);
            // 本期应还ope_fee
            $current_operation_fee = $per_operation_fee;

            $temp = array();
            $temp['scheme_index'] = $i;

            // 本期应还本金
            if ($i < $repaymentPeriodCount) {

                // 本期应还本金
                if( in_array($i,$principal_period) ){
                    $current_principal = $period_paid_principal;
                }else{
                    $current_principal = 0;
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

            $temp['initial_principal'] = round($remaining_principal,2);
            $temp['receivable_principal'] = round($current_principal, 2);
            $temp['receivable_interest'] = round($current_interest, 2);
            $temp['receivable_operation_fee'] = round($current_operation_fee, 2);
            // 剩余本金

            // 剩余本金
            if($interestInfo['currency']==currencyEnum::KHR){
                //decline的要把本金格式化为1000，total不管
                if($i<$repaymentPeriodCount && $temp['receivable_principal']>0){
                    $remainder= $temp['receivable_principal']%1000;
                    if($remainder>0){
                        $ext_amt=1000-intval($remainder);
                        $temp['receivable_principal']=intval($temp['receivable_principal']+$ext_amt);
                    }
                }
                $temp['receivable_interest']=floor($temp['receivable_interest']/100)*100+($temp['receivable_interest']%100>=50?100:0);
                $temp['receivable_operation_fee']=floor($temp['receivable_operation_fee']/100)*100+($temp['receivable_operation_fee']%100>=50?100:0);
                $temp['amount'] = intval($temp['receivable_principal']) + intval($temp['receivable_interest']) + intval($temp['receivable_operation_fee']);

                $pay_principal += $temp['receivable_principal'];
                $remaining_principal = intval($remaining_principal) - intval($temp['receivable_principal']);
                if( $remaining_principal <= 0 ){
                    $remaining_principal = 0;
                }
                $temp['remaining_principal'] = $remaining_principal;

            }else{
                $pay_principal += $temp['receivable_principal'];
                $remaining_principal = $remaining_principal - $temp['receivable_principal'];
                $temp['remaining_principal'] = $remaining_principal;
                $temp['amount'] = $temp['receivable_principal'] + $temp['receivable_interest'] + $temp['receivable_operation_fee'];
            }




            $temp['receive_date'] = $period_current[2];
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