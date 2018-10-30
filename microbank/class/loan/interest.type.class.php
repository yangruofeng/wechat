<?php

abstract class interestTypeClass {

    /**
     * @param $interestType string              计息方式
     * @param null $repaymentPeriodType string  还款期间类型
     * @return interestTypeClass
     * @throws Exception
     */
    public static function getInstance($interestType, $repaymentPeriodType = null,$principal_paid_month=null)
    {
        switch ($interestType) {
            case interestPaymentEnum::SINGLE_REPAYMENT :
                return new singleRepaymentClass();
            case interestPaymentEnum::ANYTIME_SINGLE_REPAYMENT:
                return new anytimeSingleRepaymentClass();
            case interestPaymentEnum::ADVANCE_SINGLE_REPAYMENT:
                return new advanceSingleRepaymentClass();
            case interestPaymentEnum::FIXED_PRINCIPAL :
                return new fixedPrincipleClass($repaymentPeriodType);
            case interestPaymentEnum::ANNUITY_SCHEME :
                return new annuitySchemaClass($repaymentPeriodType);
            case interestPaymentEnum::ANYTIME_ANNUITY:
                return new anytimeAnnuityClass($repaymentPeriodType);
            case interestPaymentEnum::FLAT_INTEREST :
                return new flatInterestClass($repaymentPeriodType);
            case interestPaymentEnum::BALLOON_INTEREST :
                return new balloonInterestClass($repaymentPeriodType);
            case interestPaymentEnum::SEMI_BALLOON_INTEREST :
                return new semiBalloonInterestClass($repaymentPeriodType,$principal_paid_month);
            case interestPaymentEnum::ADVANCE_FIX_REPAYMENT_DATE:
                return new advanceFixRepaymentDateClass();
            default:
                throw new Exception('Not supported payment type - ' . $interestType, errorCodesEnum::NOT_SUPPORTED);
        }
    }

    public static function isPeriodicRepayment($interestType) {
        return self::getInstance($interestType, null) instanceof periodicRepaymentClass;
    }

    public static function isOnetimeRepayment($interestType) {
        return self::getInstance($interestType, null) instanceof onetimeRepaymentClass;
    }

    public static function isNeedRecalculateInterestForPayment($interestType)
    {
        if( $interestType == interestPaymentEnum::ANYTIME_SINGLE_REPAYMENT ){
            return true;
        }
        return false;
    }

    public static function getPeriodicFirstRepaymentDate($repayment_period,$interest_start_date,$adjust_start_date=null)
    {
        $interest_start_timestamp = strtotime($interest_start_date);
        if( !$adjust_start_date ){
            $re = loan_baseClass::getInstalmentPaymentTimeInterval($repayment_period);
            if( !$re->STS ){
               throw new Exception($re->MSG,$re->CODE);
            }
            $installment_time_interval_arr = $re->DATA;  // 'value' => 1,'unit' => 'year'
            $time_interval_value = $installment_time_interval_arr['value'];
            $time_interval_unit = $installment_time_interval_arr['unit'];
            $first_repayment_date_timestamp = strtotime('+'.$time_interval_value.' '.$time_interval_unit,$interest_start_timestamp);
            return date('Y-m-d',$first_repayment_date_timestamp);

        }else{

            $adjust_date_time = strtotime($adjust_start_date);
            switch ($repayment_period){
                case interestRatePeriodEnum::YEARLY :
                    $first_repayment_date_timestamp = strtotime('+1 year',$adjust_date_time);
                    break;
                case interestRatePeriodEnum::SEMI_YEARLY:
                    $first_repayment_date_timestamp = strtotime('+6 month',$adjust_date_time);
                    break;
                case interestRatePeriodEnum::QUARTER :
                    $first_repayment_date_timestamp = strtotime('+3 month',$adjust_date_time);
                    break;
                case interestRatePeriodEnum::MONTHLY :

                    $first_repayment_date_timestamp = $adjust_date_time;
                    // 第一次还款日期要在15-45天内
                    $diff_days = ($first_repayment_date_timestamp-$interest_start_timestamp)/(24*3600);
                    if( $diff_days < 15 ){
                        // 加一个月
                        $first_repayment_date_timestamp = strtotime('+1 month',$first_repayment_date_timestamp);
                    }

                    // 加一个月还不够
                    $diff_days = ($first_repayment_date_timestamp-$interest_start_timestamp)/(24*3600);
                    if( $diff_days < 15 ){
                        // 再加一个月
                        $first_repayment_date_timestamp = strtotime('+1 month',$first_repayment_date_timestamp);
                    }
                    /* echo $diff_days;die;
                    $less_days = ceil(15-$diff_days);
                    if( $less_days > 0 ){
                        $less_month = ceil($less_days/30);
                        $first_repayment_date_timestamp = strtotime('+'.$less_month.' month',$first_repayment_date_timestamp);
                    }*/

                    break;
                case interestRatePeriodEnum::WEEKLY:
                    $first_repayment_date_timestamp = strtotime('+1 week',$interest_start_timestamp);
                    break;
                case interestRatePeriodEnum::DAILY:
                    $first_repayment_date_timestamp = strtotime('+1 day',$interest_start_timestamp);
                    break;
                default:
                    throw new Exception('Not support repayment period:'.$repayment_period,errorCodesEnum::NOT_SUPPORTED);

            }

            return date('Y-m-d',$first_repayment_date_timestamp);

        }
    }



    /**
     * 获得指定贷款时间分成多少期还款
     * @param $loanTime int         贷款时间
     * @param $loanTimeUnit string  贷款时间单位
     * @return int  分成的期数
     * @throws Exception
     */
    public abstract function getRepaymentPeriodCount($loanTime, $loanTimeUnit);

    /**
     * 获得指定贷款时间分成多少期还款
     * @param $loanTime int          贷款时间
     * @param $loanTimeUnit string   贷款时间单位
     * @param $disburseDate string   发放贷款时间（计算期间的开始时间）
     * @param $firstRepayDate string 第一次还款时间 （与发放贷款时间一起计算第一期的时间）
     * @return array                 各期还款中间的时间
     * @throws Exception
     */
    public abstract function getRepaymentPeriods($loanTime, $loanTimeUnit, $disburseDate=null, $firstRepayDate=null);

    /**
     * @param $timeUnit
     * @return string
     * @throws Exception
     */
    public static function getDirectPeriodTypeForTimeUnit($timeUnit) {
        switch ($timeUnit) {
            case loanPeriodUnitEnum::YEAR :
                return interestRatePeriodEnum::YEARLY;
            case loanPeriodUnitEnum::MONTH:
                return interestRatePeriodEnum::MONTHLY;
            case loanPeriodUnitEnum::DAY:
                return interestRatePeriodEnum::DAILY;
            default:
                throw new Exception("Unknown time unit - " . $timeUnit, errorCodesEnum::UNEXPECTED_DATA);
        }
    }

    /**
     * 计算正规化利率表示
     * @param $interestInfo array (   利率及其他费用信息
     *  interest_rate                          利率值
     *  interest_rate_unit                     利率单位
     *  operation_fee                          运营费率值
     *  operation_fee_unit                     运营费率单位
     * )
     * @param $loanTime int           贷款时间
     * @param $loanTimeUnit string    贷款时间单位
     * @return array (
     *  interest_rate                          每期利率/利息金额值
     *  interest_rate_type                     interest_rate的意义：0:利率/1:利息金额
     *  operation_fee                          每期运营费率/运营费金额
     *  operation_fee_type                     operation_fee的意义：0:费率/1:费额
     * )
     * @throws Exception
     */
    public function normalizeInterestInfo($interestInfo, $loanTime, $loanTimeUnit,$true_days=false) {
        $keys = array("interest_rate", "operation_fee");
        $ret = array();

        $matchedPeriodUnit = self::getDirectPeriodTypeForTimeUnit($loanTimeUnit);
        foreach ($keys as $key) {
            if ($interestInfo[$key] > 0) {
                if ($interestInfo["{$key}_unit"] != $matchedPeriodUnit) {
                    $rt = loan_baseClass::interestRateConversion($interestInfo[$key], $interestInfo["{$key}_unit"], $matchedPeriodUnit,$true_days);
                    if (!$rt->STS) throw new Exception($rt->MSG, $rt->CODE);
                    $ret[$key] = $rt->DATA * $loanTime;          // 单利直接乘
                } else {
                    $ret[$key] = $interestInfo[$key] * $loanTime;
                }


            }
        }

        return array_merge( (array) $interestInfo,$ret);
    }


    public function getPeriodicPeriodRate($interest_info,$repayment_period,$true_days=false)
    {
        $new_interest = $interest_info;
        $rt = loan_baseClass::interestRateConversion($interest_info['interest_rate'], $interest_info['interest_rate_unit'], $repayment_period,$true_days);
        if (!$rt->STS) throw new Exception($rt->MSG, $rt->CODE);
        $new_interest['interest_rate'] = $rt->DATA;
        $new_interest['interest_rate_unit'] = $repayment_period;

        $rt = loan_baseClass::interestRateConversion($interest_info['operation_fee'], $interest_info['operation_fee_unit'], $repayment_period,$true_days);
        if (!$rt->STS) throw new Exception($rt->MSG, $rt->CODE);
        $new_interest['operation_fee'] = $rt->DATA;
        $new_interest['operation_fee_unit'] = $repayment_period;
        return $new_interest;
    }

    /**
     * @param $totalAmount  float          贷款金额
     * @param $repaymentPeriods array      各个还款周期时间长度
     * @param $interestInfo array (        利率及其他费用信息
     *  interest_rate                          利率值
     *  interest_rate_unit                     利率单位
     *  operation_fee                          运营费率值
     *  operation_fee_unit                     运营费率单位
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
    public abstract function getInstallmentSchema($totalAmount, $repaymentPeriods, $interestInfo);

    /**
     * 计算当前时间点上还指定计划需要付的各项金额
     * @param $schemaInfo ormDataRow        计划信息
     * @param $interestInfo array (    利率信息
     *  interest_rate                          利率值
     *  interest_rate_unit                     利率单位
     *  operation_fee                          运营费率值
     *  operation_fee_unit                     运营费率单位
     * )
     * @return ormDataRow
     */
    public function calculateRepaymentInterestOfSchema($schemaInfo, $interestInfo) {
        return $schemaInfo;
    }

    /**
     * 计算提前还款信息
     * @param $contract_info array  合同信息
     * @param $currentOutstandingSchemas array    当前未还清还款计划： 在截止计息日期前起息，并未还清的计划列表
     * @param $contractRemainingPrincipal float   当前合同剩余本金
     * @param $firstOutstandingSchemaInterestDate string        第一个未还清计划的起息日期： 合同开始日期或最后一期已经还清的计划的还款日
     * @param $interestInfo array (       利率信息
     *  interest_rate                          利率值
     *  interest_rate_unit                     利率单位
     *  operation_fee                          运营费率值
     *  operation_fee_unit                     运营费率单位
     * )
     * @param $cutOffDate string          截止计息日期
     * @param $prepaymentAmount float     可以用于还款的总金额，减去当前未还清还款计划需要还的本息，剩余的就是提前还本金的金额。不含提取还款违约金。
     * @param $remainingPrincipal float   提前还款完成之后的剩余本金
     * @param $remainingPeriodCount int   提前还款完成之后的剩余期数。如果不是全部还清，该值必须大于0，如果指定了prepaymentAmount，并且已经还清，该参数无意义
     * @return array(
     *  cut_off_date                截止计息日期
     *  prepayment_principal        提前还款本金
     *  total_principal             总付本金 = 提前还款本金 + 未还清计划本金合计
     *  total_interest              总付利息
     *  total_operation_fee         总付运营费
     *  total_amount                总付金额  = 总付本金 + 总付利息 + 总付运营费
     *  remaining_schemas array     剩余还款计划
     * )
     */
    public abstract function calculatePrepaymentInfo($contract_info,$currentOutstandingSchemas, $contractRemainingPrincipal, $firstOutstandingSchemaInterestDate, $interestInfo, $cutOffDate, $prepaymentAmount, $remainingPrincipal, $remainingPeriodCount,$contract_remain_schemas=null);


}