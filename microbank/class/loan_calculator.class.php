<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/28
 * Time: 13:01
 */

// 贷款计算器
class loan_calculatorClass
{

    public function __construct()
    {
    }



    /** 不合理方式
     * 一次偿还，每一个周期计算一次利息
     * 一次偿还-每期固定利息金额的还款计划
     * @param $total_amount *贷款总金额
     * @param $per_rate  *每期利率
     * @param $payment_period  *还款期数
     * @param $operator_fee  *每期运营费
     * @return result  *还款计划
     */
    public static function single_repayment_getPaymentSchemaByFixInterestAmount($total_amount,$per_rate,$payment_period,$interest_info=null,$min_interest=0)
    {

        $schema = array();
        $temp = array();
        $payment_period = intval($payment_period);
        $total_amount = round($total_amount,2);

        $current_principal = $total_amount;
        // 基本利息
        $current_interest = round($per_rate,2);

        // 最低运营费
        $min_operator_fee = 0;
        if( $interest_info['operation_min_value'] ){
            $min_operator_fee = round($interest_info['operation_min_value'],2);
        }


        if( $interest_info && $interest_info['operation_fee'] >0 && $interest_info['operation_fee_type'] == 0 ){
            $o_rate = $interest_info['operation_fee']/100;
            $current_operator_fee = round($total_amount*$o_rate,2);

        }else{
            $current_operator_fee = $interest_info['operation_fee']?round($interest_info['operation_fee'],2):0;
        }

        $min_value = $min_interest+$min_operator_fee;
        if( ($current_interest+$current_operator_fee) < $min_value ){
            $total_interest = round($min_value,2);
            $current_interest = $total_interest-$current_operator_fee;
        }


        $temp['scheme_index'] = 1;
        $temp['receivable_principal'] = $current_principal;
        $temp['receivable_interest'] = $current_interest;
        $temp['receivable_operation_fee'] = $current_operator_fee;
        $temp['remaining_principal'] = 0.00;
        $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+$temp['receivable_operation_fee'];
        $total_pay_principal = $temp['receivable_principal'];
        $total_pay_interest = $temp['receivable_interest'];
        $total_pay_operator_fee = $temp['receivable_operation_fee'];
        $total_pay_amount = $temp['amount'];
        $period_pay = $total_pay_amount;
        $schema[] = $temp;
        return new result(true,'success',array(
            'payment_total' => array(
                'total_payment' => $total_pay_amount,
                'total_principal' => $total_pay_principal,
                'total_interest' => $total_pay_interest,
                'total_operator_fee' => $total_pay_operator_fee,
                'total_period_pay' => $period_pay
            ),
            'payment_schema' => $schema,
        ));
    }

    /** 一次偿还，每一个周期计算一次利息
     * 一次偿还-每期固定利息的还款计划
     * @param $total_amount *贷款总金额
     * @param $per_rate  *每期利率
     * @param $payment_period  *还款期数
     * @param $operator_fee  *每期运营费
     * @return result  *还款计划
     */
    public static function single_repayment_getPaymentSchemaByFixInterest($total_amount,$per_rate,$payment_period,$interest_info=null,$min_interest=0)
    {
        $schema = array();
        $temp = array();
        $payment_period = intval($payment_period);
        $total_amount = round($total_amount,2);

        $current_principal = $total_amount;
        // 基本利息
        $current_interest = round($total_amount*$per_rate,2);

        // 最低运营费
        $min_operator_fee = 0;
        if( $interest_info['operation_min_value'] ){
            $min_operator_fee = round($interest_info['operation_min_value'],2);
        }

        if( $interest_info && $interest_info['operation_fee'] >0 && $interest_info['operation_fee_type'] == 0 ){
            $o_rate = $interest_info['operation_fee']/100;
            $new_rate = $per_rate+$o_rate;
            $total_interest = round($total_amount*$new_rate,2);
            $current_operator_fee = $total_interest-$current_interest;

        }else{
            $current_operator_fee = $interest_info['operation_fee']?round($interest_info['operation_fee'],2):0;
        }

        $min_value = $min_interest+$min_operator_fee;
        if( ($current_interest+$current_operator_fee) < $min_value ){
            $total_interest = round($min_value,2);
            $current_interest = $total_interest-$current_operator_fee;
        }


        $temp['scheme_index'] = 1;
        $temp['receivable_principal'] = $current_principal;
        $temp['receivable_interest'] = $current_interest;
        $temp['receivable_operation_fee'] = $current_operator_fee;
        $temp['remaining_principal'] = 0.00;
        $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+ $temp['receivable_operation_fee'];

        $schema[] = $temp;
        $total_pay_principal = $temp['receivable_principal'];
        $total_pay_interest = $temp['receivable_interest'];
        $total_pay_operator_fee = $temp['receivable_operation_fee'];
        $total_pay_amount = $temp['amount'];
        $period_pay = $total_pay_amount;
        return new result(true,'success',array(
            'payment_total' => array(
                'total_payment' => $total_pay_amount,
                'total_principal' => $total_pay_principal,
                'total_interest' => $total_pay_interest,
                'total_operator_fee' => $total_pay_operator_fee,
                'total_period_pay' => $period_pay
            ),
            'payment_schema' => $schema,
        ));
    }

    /** 一次偿还，预收利息，到期还本金
     * @param $total_amount *贷款总金额
     * @param $per_rate  *每期利率
     * @param $payment_period  *还款期数
     * @param $operator_fee  *每期运营费
     * @return result  *还款计划
     */
    public static function advance_single_repayment_getPaymentSchemaByFixInterest($total_amount,$per_rate,$payment_period,$interest_info=null,$min_interest=0)
    {
        $schema = array();
        $temp = array();
        $payment_period = intval($payment_period);
        $total_amount = round($total_amount,2);

        $current_principal = $total_amount;
        // 基本利息
        $current_interest = round($total_amount*$per_rate,2);

        // 最低运营费
        $min_operator_fee = 0;
        if( $interest_info['operation_min_value'] ){
            $min_operator_fee = round($interest_info['operation_min_value'],2);
        }

        if( $interest_info && $interest_info['operation_fee'] >0 && $interest_info['operation_fee_type'] == 0 ){
            $o_rate = $interest_info['operation_fee']/100;
            $new_rate = $per_rate+$o_rate;
            $total_interest = round($total_amount*$new_rate,2);
            $current_operator_fee = $total_interest-$current_interest;

        }else{
            $current_operator_fee = $interest_info['operation_fee']?round($interest_info['operation_fee'],2):0;
        }

        $min_value = $min_interest+$min_operator_fee;
        if( ($current_interest+$current_operator_fee) < $min_value ){
            $total_interest = round($min_value,2);
            $current_interest = $total_interest-$current_operator_fee;
        }

        $temp['scheme_index'] = 1;
        $temp['receivable_principal'] = $current_principal;
        $temp['receivable_interest'] = 0.00;
        $temp['receivable_operation_fee'] = 0.00;
        $temp['remaining_principal'] = 0.00;
        $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+ $temp['receivable_operation_fee'];

        $schema[] = $temp;
        $total_pay_principal = $temp['receivable_principal'];
        $total_pay_interest = $current_interest;
        $total_pay_operator_fee = $current_operator_fee;
        $total_pay_amount = $temp['amount'] + $current_interest + $current_operator_fee;
        $period_pay = $total_pay_amount;
        return new result(true,'success',array(
            'payment_total' => array(
                'total_payment' => $total_pay_amount,
                'total_principal' => $total_pay_principal,
                'total_interest' => $total_pay_interest,
                'total_operator_fee' => $total_pay_operator_fee,
                'total_period_pay' => $period_pay,
                'deduct_interest' => $current_interest,
                'deduct_operation_fee' => $current_operator_fee
            ),
            'payment_schema' => $schema,
        ));
    }


    /** 等额本息 -- 不合理方式
     * 等额本息-每期固定利息金额的还款计划
     * @param $total_amount *贷款总金额
     * @param $per_rate  *每期利率
     * @param $payment_period  *还款期数
     * @param $operator_fee  *每期运营费
     * @return result  *还款计划
     */
    public static function annuity_schema_getPaymentSchemaByFixInterestAmount($total_amount,$per_rate,$payment_period,$interest_info=null,$min_interest=0)
    {
        $schema = array();
        $payment_period = intval($payment_period);
        $total_amount = round($total_amount,2);
        $per_principal = round($total_amount/$payment_period,2);
        $fix_rate = round($per_rate,2);
        $remaining_principal = $total_amount;
        $total_pay = $total_interest = $total_o_fee = 0;
        $pay_principal = 0;  // 已还本金


        // 最低运营费
        $min_operator_fee = 0;
        if( $interest_info['operation_min_value'] ){
            $min_operator_fee = round($interest_info['operation_min_value'],2);
        }

        $current_interest = $fix_rate;


        if( $interest_info && $interest_info['operation_fee'] >0 && $interest_info['operation_fee_type'] == 0  ){

            $o_rate = $interest_info['operation_fee']/100;
            $pay_amount = ( $total_amount*$o_rate*pow(1+$o_rate,$payment_period) )/( pow(1+$o_rate,$payment_period) -1 );
            $current_pay_amount = round($pay_amount,2)+$current_interest;

            for($i=1;$i<=$payment_period;$i++){


                $temp = array();
                $temp['scheme_index'] = $i;

                $current_operator_fee = round($remaining_principal*$o_rate,2);

                $current_principal = $current_pay_amount-$current_interest-$current_operator_fee;

                // 修正一下总还款本金
                if( $i == $payment_period ){
                    $current_principal = round($remaining_principal,2);
                    // 修正最低值
                    $min_value = $min_operator_fee+$min_interest;
                    if( ($total_interest+$total_o_fee+$current_operator_fee+$current_interest) < $min_value ){
                        $current_total_interest = round($min_value-$total_o_fee-$total_interest,2);
                        $current_operator_fee = $current_total_interest-$current_interest;
                    }
                }

                $temp['receivable_principal'] = $current_principal;
                $temp['receivable_interest'] = $current_interest;
                $temp['receivable_operation_fee'] = $current_operator_fee;
                $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+$temp['receivable_operation_fee'];

                $pay_principal +=  $temp['receivable_principal'];
                $remaining_principal = $remaining_principal-$temp['receivable_principal'];
                $temp['remaining_principal'] = $remaining_principal;
                $schema[] = $temp;
                $total_interest += $temp['receivable_interest'];
                $total_o_fee += $temp['receivable_operation_fee'];
                $total_pay += $temp['amount'];
            }


        }else{

            $current_principal = $per_principal;

            $current_operator_fee = $interest_info['operation_fee']?round($interest_info['operation_fee'],2):0;

            $min_value = $min_interest+$min_operator_fee;

            for($i=1;$i<=$payment_period;$i++){


                $temp = array();
                $temp['scheme_index'] = $i;
                // 修正一下总还款本金
                if( $i == $payment_period ){
                    $current_principal = round($remaining_principal,2);
                    //修正最低值
                    if( ($total_interest+$total_o_fee+$current_interest+$current_operator_fee) < $min_value ){
                        $current_total_interest = round($min_value-$total_interest-$total_o_fee,2);
                        $current_interest = $current_total_interest-$current_operator_fee;
                    }

                }

                $temp['receivable_principal'] = $current_principal;
                $temp['receivable_interest'] = $current_interest;
                $temp['receivable_operation_fee'] = $current_operator_fee;
                $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+$temp['receivable_operation_fee'];

                $pay_principal +=  $temp['receivable_principal'];
                $remaining_principal = $remaining_principal-$temp['receivable_principal'];
                $temp['remaining_principal'] = $remaining_principal;
                $schema[] = $temp;
                $total_interest += $temp['receivable_interest'];
                $total_o_fee += $temp['receivable_operation_fee'];
                $total_pay += $temp['amount'];
            }

        }



        return new result(true,'success',array(
            'payment_total' => array(
                'total_payment' => $total_pay,
                'total_principal' => $total_amount,
                'total_interest' => $total_interest,
                'total_operator_fee' => $total_o_fee,
                'total_period_pay' => round($total_pay/$payment_period,2),
            ),
            'payment_schema' => $schema,
        ));

    }

    /** 等额本息
     * 等额本息-每期固定利息金额的还款计划
     * @param $total_amount *贷款总金额
     * @param $per_rate  *每期利率
     * @param $payment_period  *还款期数
     * @param $operator_fee  *每期运营费
     * @return result  *还款计划
     */
    public static function annuity_schema_getPaymentSchemaByFixInterest($total_amount,$per_rate,$payment_period,$interest_info=null,$min_interest=0)
    {
        $schema = array();
        $payment_period = intval($payment_period);
        $total_amount = round($total_amount,2);

        // 剩余本金
        $remaining_principal = $total_amount;
        $total_pay = $total_interest = $total_o_fee = 0;
        $pay_principal = 0;

        if( $interest_info && $interest_info['operation_fee'] >0 && $interest_info['operation_fee_type'] == 0  ){

            // 运营费和利息一起计算
            $min_operator_fee = round($interest_info['operation_min_value'],2);
            $operation_rate = $interest_info['operation_fee']/100;
            $new_per_rate = $per_rate+$operation_rate;
            $pay_amount = ( $total_amount*$new_per_rate*pow(1+$new_per_rate,$payment_period) )/( pow(1+$new_per_rate,$payment_period) -1 );
            $pay_amount = round($pay_amount,2);

            for( $i=1;$i<=$payment_period;$i++){

                $temp = array();
                $temp['scheme_index'] = $i;

                // 本期应还利息
                $current_interest = round($remaining_principal*$per_rate,2);
                // 本期应还ope_fee
                $current_operation_fee = round($remaining_principal*$operation_rate,2);

                // 本期应还总利息
                $interest_operator = $current_interest+$current_operation_fee;

                // 本期应还本金
                if( $i == $payment_period ){
                    // 修正本金
                    $current_principal = round($remaining_principal,2);
                    $interest_operator = round($pay_amount-$current_principal,2);
                    // 修正最低值
                    if( ($total_interest+$total_o_fee+$interest_operator) < ($min_interest+$min_operator_fee) ){
                        $interest_operator = round($min_interest+$min_operator_fee-$total_interest-$total_o_fee,2);
                    }
                    $current_interest = round($remaining_principal*$per_rate,2);
                    $current_operation_fee = $interest_operator-$current_interest;
                }else{
                    $current_principal = round($pay_amount-$interest_operator,2);
                }

                $temp['receivable_principal'] = round($current_principal,2);
                $temp['receivable_interest'] = $current_interest;
                $temp['receivable_operation_fee'] = $current_operation_fee;
                // 剩余本金
                $pay_principal += $temp['receivable_principal'];
                $remaining_principal = $remaining_principal-$temp['receivable_principal'];
                $temp['remaining_principal'] = $remaining_principal;
                $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+$temp['receivable_operation_fee'];
                $schema[] = $temp;
                $total_interest += $temp['receivable_interest'];
                $total_o_fee += $temp['receivable_operation_fee'];
                $total_pay += $temp['amount'];
            }


        }else{


            $pay_amount = ( $total_amount*$per_rate*pow(1+$per_rate,$payment_period) )/( pow(1+$per_rate,$payment_period) -1 );
            $pay_amount = round($pay_amount,2)+round($interest_info['operation_fee'],2);

            $min_operator_fee = round($interest_info['operation_min_value'],2)?:0;

            for( $i=1;$i<=$payment_period;$i++){


                $temp = array();
                $temp['scheme_index'] = $i;

                // 本期应还运营费
                $current_operation_fee = round($interest_info['operation_fee'],2);
                // 本期应还利息
                $current_interest = round($remaining_principal*$per_rate,2);

                // 本期应还总利息
                $interest_operator = $current_interest+$current_operation_fee;

                // 本期应还本金
                if( $i == $payment_period ){
                    $current_principal = round($remaining_principal,2);
                    $interest_operator = $pay_amount-$current_principal;
                    $current_interest = $interest_operator-$current_operation_fee;
                    // 修正最低值
                    if( ($total_interest+$total_o_fee+$interest_operator) < ($min_interest+$min_operator_fee) ){
                        $interest_operator = round($min_interest+$min_operator_fee-$total_interest-$total_o_fee,2);
                    }
                    $current_interest = round($remaining_principal*$per_rate,2);
                    $current_operation_fee = $interest_operator-$current_interest;

                }else{
                    $current_principal = round($pay_amount-$interest_operator,2);
                }

                $temp['receivable_principal'] = round($current_principal,2);
                $temp['receivable_interest'] = $current_interest;
                $temp['receivable_operation_fee'] = $current_operation_fee;
                // 剩余本金
                $pay_principal += $temp['receivable_principal'];
                $remaining_principal = $remaining_principal-$temp['receivable_principal'];
                $temp['remaining_principal'] = $remaining_principal;
                $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+$temp['receivable_operation_fee'];
                $schema[] = $temp;

                $total_interest += $temp['receivable_interest'];
                $total_o_fee += $temp['receivable_operation_fee'];
                $total_pay += $temp['amount'];
            }

        }


        return new result(true,'success',array(
            'payment_total' => array(
                'total_payment' => $total_pay,
                'total_principal' => $total_amount,
                'total_interest' => $total_interest,
                'total_operator_fee' => $total_o_fee,
                'total_period_pay' => round($total_pay/$payment_period,2),
            ),
            'payment_schema' => $schema,
        ));

    }

    /** 等额本金  不合理方式
     * 等额本金-每期固定利息金额的还款计划
     * @param $total_amount *贷款总金额
     * @param $per_rate  *每期利率
     * @param $payment_period  *还款期数
     * @param $operator_fee  *每期运营费
     * @return result  *还款计划
     */
    public static function fixed_principle_getPaymentSchemaByFixInterestAmount($total_amount,$per_rate,$payment_period,$interest_info=null,$min_interest=0)
    {
        $schema = array();
        $payment_period = intval($payment_period);
        $total_amount = round($total_amount,2);
        $per_principle = round($total_amount/$payment_period,2);
        $fix_rate = round($per_rate,2);
        // 剩余本金
        $remaining_principal = $total_amount;
        $total_pay = $total_interest = $total_o_fee = 0;
        $pay_principal = 0;

        $current_principal = $per_principle;
        $current_interest = $fix_rate;

        $min_operator_fee = 0;
        if( $interest_info['operation_min_value'] ){
            $min_operator_fee = round($interest_info['operation_min_value'],2);
        }

        $min_value = $min_operator_fee+$min_interest;


        if( $interest_info && $interest_info['operation_fee'] >0 && $interest_info['operation_fee_type'] == 0  ){

            $o_rate = $interest_info['operation_fee']/100;
            $current_operator_fee = round($remaining_principal*$o_rate,2);

            for($i=1;$i<=$payment_period;$i++){

                $temp = array();
                $temp['scheme_index'] = $i;
                if( $i == $payment_period ){
                    $current_principal = round($remaining_principal,2);
                    // 修正最低值
                    if( ($total_o_fee+$total_interest+$current_interest+$current_operator_fee) < $min_value ){
                        $current_total_interest = round($min_value-$total_interest-$total_o_fee,2);
                        $current_interest = $current_total_interest-$current_operator_fee;
                    }
                }

                $temp['receivable_principal'] = $current_principal;
                $temp['receivable_interest'] = $current_interest;
                $temp['receivable_operation_fee'] = $current_operator_fee;
                $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+$temp['receivable_operation_fee'];

                $pay_principal += $temp['receivable_principal'];
                $remaining_principal = $remaining_principal-$temp['receivable_principal'];
                $temp['remaining_principal'] = $remaining_principal;
                $schema[] = $temp;

                $total_interest += $temp['receivable_interest'];
                $total_o_fee += $temp['receivable_operation_fee'];
                $total_pay += $temp['amount'];
            }

        }else{

            $current_operator_fee = $interest_info['operation_fee']?round($interest_info['operation_fee'],2):0;

            for($i=1;$i<=$payment_period;$i++){



                $temp = array();
                $temp['scheme_index'] = $i;
                if( $i == $payment_period ){
                    $current_principal = round($remaining_principal,2);
                    // 修正最低值
                    if( ($total_o_fee+$total_interest+$current_interest+$current_operator_fee) < $min_value ){
                        $current_total_interest = round($min_value-$total_interest-$total_o_fee,2);
                        $current_interest = $current_total_interest-$current_operator_fee;
                    }
                }


                $temp['receivable_principal'] = $current_principal;
                $temp['receivable_interest'] = $current_interest;
                $temp['receivable_operation_fee'] = $current_operator_fee;
                $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+$temp['receivable_operation_fee'];

                $pay_principal += $temp['receivable_principal'];
                $remaining_principal = $remaining_principal-$temp['receivable_principal'];
                $temp['remaining_principal'] = $remaining_principal;
                $schema[] = $temp;

                $total_interest += $temp['receivable_interest'];
                $total_o_fee += $temp['receivable_operation_fee'];
                $total_pay += $temp['amount'];
            }

        }


        return new result(true,'success',array(
            'payment_total' => array(
                'total_payment' => $total_pay,
                'total_principal' => $total_amount,
                'total_interest' => $total_interest,
                'total_operator_fee' => $total_o_fee,
                'total_period_pay' => round($total_pay/$payment_period,2),
            ),
            'payment_schema' => $schema,
        ));
    }

    /** 等额本金
     * 等额本金-每期固定利息的还款计划
     * @param $total_amount *贷款总金额
     * @param $per_rate  *每期利率
     * @param $payment_period  *还款期数
     * @param $operator_fee  *每期运营费
     * @return result  *还款计划
     */
    public static function fixed_principle_getPaymentSchemaByFixInterest($total_amount,$per_rate,$payment_period,$interest_info=null,$min_interest=0)
    {
        $schema = array();
        $payment_period = intval($payment_period);
        $total_amount = round($total_amount,2);
        $per_principle = round($total_amount/$payment_period,2);
        // 剩余本金
        $remaining_principal = round($total_amount,2);
        $total_pay = $total_interest = $total_o_fee = 0;
        $pay_principal = 0;

        $min_operator_fee = 0;
        if( $interest_info['operation_min_value'] ){
            $min_operator_fee = round($interest_info['operation_min_value'],2);
        }

        $min_value = $min_operator_fee+$min_interest;

        if( $interest_info && $interest_info['operation_fee'] >0 && $interest_info['operation_fee_type'] == 0 ){

            $o_rate = $interest_info['operation_fee']/100;
            $new_rate = $per_rate+$o_rate;
            for( $i=1;$i<=$payment_period;$i++){


                $temp = array();

                // 本期应还本金
                $current_principle = $per_principle;

                // 本期应还总利息
                $interest_operator = round($remaining_principal*$new_rate,2);

                // 本期应还利息
                $current_interest = round($remaining_principal*$per_rate,2);

                // 本期应还运营费
                $current_operation_fee = $interest_operator-$current_interest;

                $temp['scheme_index'] = $i;
                if( $i == $payment_period ){
                    // 修正本金
                    $current_principle = round($remaining_principal,2);
                    // 修正最低利息
                    if( ($total_interest+$total_o_fee+$interest_operator) < $min_value ){
                        $interest_operator = round($min_value-$total_o_fee-$total_interest,2);
                        $current_interest = $interest_operator-$current_operation_fee;
                    }
                }

                $temp['receivable_principal'] = $current_principle;
                $temp['receivable_interest'] = $current_interest;
                $temp['receivable_operation_fee'] = $current_operation_fee;
                $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+$temp['receivable_operation_fee'];

                $pay_principal += $temp['receivable_principal'];
                $remaining_principal = $remaining_principal-$temp['receivable_principal'];
                $temp['remaining_principal'] = $remaining_principal;
                $schema[] = $temp;

                $total_interest += $temp['receivable_interest'];
                $total_o_fee += $temp['receivable_operation_fee'];
                $total_pay += $temp['amount'];
            }

        }else{

            $current_operation_fee = $interest_info['operation_fee']?round($interest_info['operation_fee'],2):0;

            for( $i=1;$i<=$payment_period;$i++){

                $temp = array();

                // 本期应还本金
                $current_principle = $per_principle;
                // 本期应还利息
                $current_interest = round($remaining_principal*$per_rate,2);

                // 本期应还总利息
                $interest_operator = $current_interest+$current_operation_fee;

                $temp['scheme_index'] = $i;

                if( $i == $payment_period ){
                    // 修正本金
                    $current_principle = round($remaining_principal,2);
                    //修正最低值
                    if( ($total_interest+$total_o_fee+$interest_operator) < $min_value ){
                        $interest_operator = round($min_value-$total_o_fee-$total_interest,2);
                        $current_interest = $interest_operator-$current_operation_fee;
                    }
                }

                $temp['receivable_principal'] = $current_principle;
                $temp['receivable_interest'] = $current_interest;
                $temp['receivable_operation_fee'] = $current_operation_fee;
                $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+$temp['receivable_operation_fee'];
                $pay_principal += $temp['receivable_principal'];
                $remaining_principal = $remaining_principal-$temp['receivable_principal'];
                $temp['remaining_principal'] = $remaining_principal;
                $schema[] = $temp;

                $total_interest += $temp['receivable_interest'];
                $total_o_fee += $temp['receivable_operation_fee'];
                $total_pay += $temp['amount'];
            }

        }




        return new result(true,'success',array(
            'payment_total' => array(
                'total_payment' => $total_pay,
                'total_principal' => $total_amount,
                'total_interest' => $total_interest,
                'total_operator_fee' => $total_o_fee,
                'total_period_pay' => round($total_pay/$payment_period,2),
            ),
            'payment_schema' => $schema,
        ));
    }


    /** 每期固定利息 不合理方式
     * 每期固定利息-每期固定利息金额的还款计划
     * @param $total_amount *贷款总金额
     * @param $per_rate  *每期利率
     * @param $payment_period  *还款期数
     * @param $operator_fee  *每期运营费
     * @return result  *还款计划
     */
    public static function flat_interest_getPaymentSchemaByFixInterestAmount($total_amount,$per_rate,$payment_period,$interest_info=null,$min_interest=0)
    {
        $schema = array();
        $payment_period = intval($payment_period);
        $total_amount = round($total_amount,2);
        $per_principle = round($total_amount/$payment_period,2);
        // 剩余本金
        $remaining_principal = $total_amount;
        $total_pay = $total_interest = $total_o_fee = 0;
        $pay_principal = 0;

        $min_operator_fee = 0;
        if( $interest_info['operation_min_value'] ){
            $min_operator_fee = round($interest_info['operation_min_value'],2);
        }

        $min_value = $min_operator_fee+$min_interest;

        $current_principal = $per_principle;

        // 本期应还利息
        $current_interest = round($per_rate,2);

        if( $interest_info && $interest_info['operation_fee'] >0 && $interest_info['operation_fee_type'] == 0 ){
            $o_rate = $interest_info['operation_fee']/100;
            $current_operator_fee = round($total_amount*$o_rate,2);
        }else{
            $current_operator_fee = $interest_info['operation_fee']?round($interest_info['operation_fee'],2):0;
        }

        for( $i=1;$i<=$payment_period;$i++){


            $temp = array();

            $temp['scheme_index'] = $i;
            if( $i == $payment_period ){
                $current_principal = round($remaining_principal,2);
                // 修正最低值
                if( ($total_o_fee+$total_interest+$current_interest+$current_operator_fee) < $min_value ){
                    $current_total_interest = round($min_value-$total_interest-$total_o_fee,2);
                    $current_interest = $current_total_interest-$current_operator_fee;
                }
            }

            $temp['receivable_principal'] = $current_principal;
            $temp['receivable_interest'] = $current_interest;
            $temp['receivable_operation_fee'] = $current_operator_fee;
            $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+$temp['receivable_operation_fee'];
            $pay_principal += $temp['receivable_principal'];
            $remaining_principal = $remaining_principal-$per_principle;
            $temp['remaining_principal'] = $remaining_principal;
            $schema[] = $temp;

            $total_interest += $temp['receivable_interest'];
            $total_o_fee += $temp['receivable_operation_fee'];
            $total_pay += $temp['amount'];
        }


        return new result(true,'success',array(
            'payment_total' => array(
                'total_payment' => $total_pay,
                'total_principal' => $total_amount,
                'total_interest' => $total_interest,
                'total_operator_fee' => $total_o_fee,
                'total_period_pay' => round($total_pay/$payment_period,2),
            ),
            'payment_schema' => $schema,
        ));

    }


    /** 每期固定利息
     * 每期固定利息-每期固定利息的还款计划
     * @param $total_amount *贷款总金额
     * @param $per_rate  *每期利率
     * @param $payment_period  *还款期数
     * @param $operator_fee  *每期运营费
     * @return result  *还款计划
     */
    public static function flat_interest_getPaymentSchemaByFixInterest($total_amount,$per_rate,$payment_period,$interest_info=null,$min_interest=0)
    {
        $schema = array();
        $payment_period = intval($payment_period);
        $total_amount = round($total_amount,2);
        $per_principle = round($total_amount/$payment_period,2);
        // 剩余本金
        $remaining_principal = $total_amount;
        $total_pay = $total_interest = $total_o_fee = 0;
        $pay_principal = 0;

        $min_operator_fee = 0;
        if( $interest_info['operation_min_value'] ){
            $min_operator_fee = round($interest_info['operation_min_value'],2);
        }

        $min_value = $min_operator_fee+$min_interest;

        $current_principal = $per_principle;

        // 本期应还利息
        $current_interest = round($total_amount*$per_rate,2);

        if( $interest_info && $interest_info['operation_fee'] >0 && $interest_info['operation_fee_type'] == 0  ){
            $o_rate = $interest_info['operation_fee']/100;
            $current_operator_fee = round($total_amount*$o_rate,2);
        }else{
            $current_operator_fee = $interest_info['operation_fee']?round($interest_info['operation_fee'],2):0;
        }

        for( $i=1;$i<=$payment_period;$i++){


            $temp = array();

            $temp['scheme_index'] = $i;

            if( $i == $payment_period ){
                // 修正本金
                $current_principal = round($remaining_principal,2);
                // 修正最低值

                if( ($total_interest+$total_o_fee+$current_interest+$current_operator_fee) < $min_value ){
                    $current_total_interest = round($min_value-$total_o_fee-$total_interest,2);
                    $current_interest = $current_total_interest-$current_operator_fee;
                }
            }

            $temp['receivable_principal'] = $current_principal;
            $temp['receivable_interest'] = $current_interest;
            $temp['receivable_operation_fee'] = $current_operator_fee;
            $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+$temp['receivable_operation_fee'];

            $pay_principal += $temp['receivable_principal'];
            $remaining_principal = $remaining_principal-$per_principle;
            $temp['remaining_principal'] = $remaining_principal;
            $schema[] = $temp;

            $total_interest += $temp['receivable_interest'];
            $total_o_fee += $temp['receivable_operation_fee'];
            $total_pay += $temp['amount'];
        }

        return new result(true,'success',array(
            'payment_total' => array(
                'total_payment' => $total_pay,
                'total_principal' => $total_amount,
                'total_interest' => $total_interest,
                'total_operator_fee' => $total_o_fee,
                'total_period_pay' => round($total_pay/$payment_period,2),
            ),
            'payment_schema' => $schema,
        ));

    }

    /** 先利息后本金
     * 先利息后本金-每期固定利息金额的还款计划
     * @param $total_amount *贷款总金额
     * @param $per_rate  *每期利率
     * @param $payment_period  *还款期数
     * @param $operator_fee  *每期运营费
     * @return result  *还款计划
     */
    public static function balloon_interest_getPaymentSchemaByFixInterestAmount($total_amount,$per_rate,$payment_period,$interest_info=null,$min_interest=0)
    {
        $schema = array();
        $payment_period = intval($payment_period);
        $total_amount = round($total_amount,2);
        $per_principle = 0;
        // 剩余本金
        $remaining_principal = $total_amount;
        $total_pay = $total_interest = $total_o_fee = 0;

        $min_operator_fee = 0;
        if( $interest_info['operation_min_value'] ){
            $min_operator_fee = round($interest_info['operation_min_value'],2);
        }
        $min_value = $min_operator_fee+$min_interest;


        // 本期应还利息
        $current_interest = round($per_rate,2);

        // 因最后还本金，实际每期利息一样
        if( $interest_info && $interest_info['operation_fee'] >0 && $interest_info['operation_fee_type'] == 0  ){
            $o_rate = $interest_info['operation_fee']/100;
            $current_operator_fee = round($total_amount*$o_rate,2);
        }else{
            $current_operator_fee = $interest_info['operation_fee']?round($interest_info['operation_fee'],2):0;
        }

        for( $i=1;$i<=$payment_period;$i++){


            $temp = array();

            // 本期应还本金
            if( $i == $payment_period ){  // 最后一期
                $current_principal = $total_amount;
                // 修正最小值
                if( ($total_o_fee+$total_interest+$current_interest+$current_operator_fee) < $min_value ){
                    $current_total_interest = round($min_value-$total_o_fee-$total_interest,2);
                    $current_interest = $current_total_interest-$current_operator_fee;
                }
            }else{
                $current_principal = 0;
            }
            $temp['scheme_index'] = $i;
            $temp['receivable_principal'] = $current_principal;
            $temp['receivable_interest'] = $current_interest;
            $temp['receivable_operation_fee'] = $current_operator_fee;
            $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+$temp['receivable_operation_fee'];
            $remaining_principal = $remaining_principal-$per_principle;
            $temp['remaining_principal'] = $remaining_principal;
            $schema[] = $temp;

            $total_interest += $temp['receivable_interest'];
            $total_o_fee += $temp['receivable_operation_fee'];
            $total_pay += $temp['amount'];
        }
        return new result(true,'success',array(
            'payment_total' => array(
                'total_payment' => $total_pay,
                'total_principal' => $total_amount,
                'total_interest' => $total_interest,
                'total_operator_fee' => $total_o_fee,
                'total_period_pay' => round($total_pay/$payment_period,2),
            ),
            'payment_schema' => $schema,
        ));

    }

    /** 先利息后本金
     * 先利息后本金-每期固定利息的还款计划
     * @param $total_amount *贷款总金额
     * @param $per_rate  *每期利率
     * @param $payment_period  *还款期数
     * @param $operator_fee  *每期运营费
     * @return result  *还款计划
     */
    public static function balloon_interest_getPaymentSchemaByFixInterest($total_amount,$per_rate,$payment_period,$interest_info=null,$min_interest=0)
    {
        $schema = array();
        $payment_period = intval($payment_period);
        $total_amount = round($total_amount,2);
        $per_principle = 0;
        // 剩余本金
        $remaining_principal = $total_amount;
        $total_pay = $total_interest = $total_o_fee = 0;

        $min_operator_fee = 0;
        if( $interest_info['operation_min_value'] ){
            $min_operator_fee = round($interest_info['operation_min_value'],2);
        }
        $min_value = $min_operator_fee+$min_interest;

        $current_interest = round($total_amount*$per_rate,2);

        // 因最后还本金，实际每期利息一样
        if( $interest_info && $interest_info['operation_fee'] >0 && $interest_info['operation_fee_type'] == 0  ){
            $o_rate = $interest_info['operation_fee']/100;
            $current_operator_fee = round($total_amount*$o_rate,2);
        }else{
            $current_operator_fee = $interest_info['operation_fee']?round($interest_info['operation_fee'],2):0;
        }

        for( $i=1;$i<=$payment_period;$i++){


            $temp = array();

            // 本期应还本金
            if( $i == $payment_period ){  // 最后一期

                $current_principal = $total_amount;
                // 修正最低值
                if( ($total_interest+$total_o_fee+$current_interest+$current_operator_fee) < $min_value ){
                    $current_total_interest = round($min_value-$total_o_fee-$total_interest,2);
                    $current_interest = $current_total_interest-$current_operator_fee;
                }

            }else{
                $current_principal = 0;
            }

            $temp['scheme_index'] = $i;
            $temp['receivable_principal'] = $current_principal;
            $temp['receivable_interest'] = $current_interest;
            $temp['receivable_operation_fee'] = $current_operator_fee;
            $temp['amount'] = $temp['receivable_principal']+$temp['receivable_interest']+$temp['receivable_operation_fee'];
            $remaining_principal = $remaining_principal-$per_principle;
            $temp['remaining_principal'] = $remaining_principal;
            $schema[] = $temp;

            $total_interest += $temp['receivable_interest'];
            $total_o_fee += $temp['receivable_operation_fee'];
            $total_pay += $temp['amount'];
        }


        return new result(true,'success',array(
            'payment_total' => array(
                'total_payment' => $total_pay,
                'total_principal' => $total_amount,
                'total_interest' => $total_interest,
                'total_operator_fee' => $total_o_fee,
                'total_period_pay' => round($total_pay/$payment_period,2),
            ),
            'payment_schema' => $schema,
        ));
    }

}