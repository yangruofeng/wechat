<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/28
 * Time: 16:58
 */

class single_repaymentClass
{


    /** 计算计划到还款日时产生的利息
     * @param $schema_id
     * @return result
     */
    public static function reCalculatePaymentInterestOfSchema($schema_id)
    {
        $m_schema = new loan_installment_schemeModel();
        $schema_info = $m_schema->getRow($schema_id);
        if( !$schema_info ){
            return new result(false,'No schema info:'.$schema_id,null,errorCodesEnum::NO_DATA);
        }
        $contract_info = (new loan_contractModel())->getRow($schema_info->contract_id);
        if( !$contract_info ){
            return new result(false,'No contract info:'.$schema_info->contract_id,null,errorCodesEnum::NO_DATA);
        }
        if(  !interestTypeClass::isNeedRecalculateInterestForPayment($contract_info['repayment_type']) ){
            return new result(false,'Not single repayment contract.'.$schema_info->contract_id,null,errorCodesEnum::UNEXPECTED_DATA);
        }

        $loan_days = $contract_info['loan_term_day'];
        // 获取最低要计算利息的天数
        $min_cal_days = global_settingClass::getInterestMindaysByLoanDays($loan_days);

        // 计算贷款日至今的天数
        $loan_time = date('Y-m-d',strtotime($contract_info['start_date']));
        $today = date('Y-m-d');
        $diff_days = ceil( ( strtotime($today) - strtotime($loan_time) )/86400 );
        if( $diff_days <= 0){
            $diff_days = 0;
        }
        if( $diff_days < $min_cal_days ){
            $diff_days = $min_cal_days;
        }


        // 计算利息，换算到日利率
        $interest_rate = $contract_info['interest_rate'];
        $int_re = loan_baseClass::interestRateConversion($interest_rate,$contract_info['interest_rate_unit'],interestRatePeriodEnum::DAILY);
        if( !$int_re->STS ){
            return $int_re;
        }
        $day_interest_rate = $int_re->DATA;

        $operate_rate = $contract_info['operation_fee'];
        $op_re = loan_baseClass::interestRateConversion($operate_rate,$contract_info['operation_fee_unit'],interestRatePeriodEnum::DAILY);
        if( !$op_re->STS ){
            return $op_re;
        }
        $day_operate_rate = $op_re->DATA;

        $loan_amount = $contract_info['apply_amount'];

        // 结算实际利息
        $total_interest = round($day_interest_rate/100*$diff_days*$loan_amount,2);
        if( $total_interest < $contract_info['interest_min_value'] ){
            $total_interest = $contract_info['interest_min_value'];
        }
        $total_operation_fee = round($day_operate_rate/100*$diff_days*$loan_amount,2);
        if( $total_operation_fee < $contract_info['operation_min_value'] ){
            $total_operation_fee = $contract_info['operation_min_value'];
        }

        return new result(true,'success',array(
            'principal' => $loan_amount,
            'total_interest' => $total_interest,
            'total_operation_fee' => $total_operation_fee
        ));

    }


}