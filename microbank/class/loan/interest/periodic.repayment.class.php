<?php

abstract class periodicRepaymentClass extends interestTypeClass {
    protected $repayment_period_type;
    protected $principal_paid_month;

    public function __construct($repaymentPeriodType)
    {
        $this->repayment_period_type = $repaymentPeriodType;
    }

    /**
     * 根据还款周期类型获取一个周期的默认时间长度
     * @param $repaymentPeriodType string 还款周期类型
     * @return array  第一个元素是值，第二个元素是单位
     * @throws Exception
     */
    public static function getDefaultPeriodTime($repaymentPeriodType) {
        switch ($repaymentPeriodType)
        {
            case interestRatePeriodEnum::DAILY:
                return array(1, loanPeriodUnitEnum::DAY);
            case interestRatePeriodEnum::WEEKLY:
                return array(7, loanPeriodUnitEnum::DAY);
            case interestRatePeriodEnum::MONTHLY:
                return array(1, loanPeriodUnitEnum::MONTH);
            case interestRatePeriodEnum::QUARTER:
                return array(3, loanPeriodUnitEnum::MONTH);
            case interestRatePeriodEnum::SEMI_YEARLY:
                return array(6, loanPeriodUnitEnum::MONTH);
            case interestRatePeriodEnum::YEARLY:
                return array(1, loanPeriodUnitEnum::YEAR);
            default:
                throw new Exception('Unknown repayment period type - ' . $repaymentPeriodType);
        }
    }

    /**
     * 获得指定贷款时间分成多少期还款
     * @param $loanTime int         贷款时间
     * @param $loanTimeUnit string  贷款时间单位
     * @return int  分成的期数
     * @throws Exception
     */
    public function getRepaymentPeriodCount($loanTime, $loanTimeUnit) {
        $not_match_error = "Loan time ($loanTime $loanTimeUnit) is not matched with repayment period type ({$this->repayment_period_type})";

        switch ($loanTimeUnit) {
            case loanPeriodUnitEnum::YEAR :
                switch ($this->repayment_period_type)
                {
                    case interestRatePeriodEnum::YEARLY:
                        return $loanTime;
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        return $loanTime * 2;
                    case interestRatePeriodEnum::QUARTER:
                        return $loanTime * 4;
                    case interestRatePeriodEnum::MONTHLY:
                        return $loanTime * 12;
                    default:
                        throw new Exception($not_match_error, errorCodesEnum::INVALID_PARAM);
                }
                break;
            case loanPeriodUnitEnum::MONTH:
                switch ($this->repayment_period_type)
                {
                    case interestRatePeriodEnum::YEARLY:
                        if ($loanTime % 12 == 0) {
                            return $loanTime / 12;
                        } else {
                            throw new Exception($not_match_error, errorCodesEnum::INVALID_PARAM);
                        }
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        if ($loanTime % 6 == 0) {
                            return $loanTime / 6;
                        } else {
                            throw new Exception($not_match_error, errorCodesEnum::INVALID_PARAM);
                        }
                    case interestRatePeriodEnum::QUARTER:
                        if ($loanTime % 3 == 0) {
                            return $loanTime / 3;
                        } else {
                            throw new Exception($not_match_error, errorCodesEnum::INVALID_PARAM);
                        }
                    case interestRatePeriodEnum::MONTHLY:
                        return $loanTime;
                    default:
                        throw new Exception($not_match_error, errorCodesEnum::INVALID_PARAM);
                }
                break;
            case loanPeriodUnitEnum::DAY:
                switch ($this->repayment_period_type)
                {
                    case interestRatePeriodEnum::WEEKLY:
                        if ($loanTime % 7 == 0) {
                            return $loanTime / 7;
                        } else {
                            throw new Exception($not_match_error, errorCodesEnum::INVALID_PARAM);
                        }
                    case interestRatePeriodEnum::DAILY:
                        return $loanTime;
                    default:
                        throw new Exception($not_match_error, errorCodesEnum::INVALID_PARAM);
                }
                break;
            default:
                throw new Exception("Unknown time unit - " . $loanTimeUnit, errorCodesEnum::UNEXPECTED_DATA);
        }
    }

    private function getRepaymentPeriodsInner($periodCount, $disburseDate = null, $firstRepayDate = null) {
        $ret = array_fill(0, $periodCount, self::getDefaultPeriodTime($this->repayment_period_type));

        if( !$disburseDate ){
            $disburseDate = date('Y-m-d');
        }
        if( !$firstRepayDate ){
            $first_ret = reset($ret);
            $firstRepayDate = date('Y-m-d',
                strtotime('+'.$first_ret[0].' '.$first_ret[1],strtotime($disburseDate)));
        }
        //全部换算成天

        $period_interest_start_day = $disburseDate;
        $period_interest_end_day = $firstRepayDate;
        $counter = 1;
        foreach( $ret as $key=>$v ){
            $days = (int)((strtotime($period_interest_end_day) - strtotime($period_interest_start_day)) / (24 * 3600));
            $ret[$key] = array($days, loanPeriodUnitEnum::DAY,$period_interest_end_day);

            $period_interest_start_day = $period_interest_end_day;
           /* $period_interest_end_day = date('Y-m-d',
                strtotime('+'.$v[0].' '.$v[1],strtotime($period_interest_start_day)));*/

            // 需要使用原始日期叠加，处理2分月的日期偏差的问题
            $period_interest_end_day = date('Y-m-d',
                strtotime('+'.$v[0]*$counter.' '.$v[1],strtotime($firstRepayDate)));
            $counter++;


        }

        /*if ($firstRepayDate) {
            if (!$disburseDate) $disburseDate = date("Y-m-d");
            $days = (int)((strtotime($firstRepayDate) - strtotime($disburseDate)) / (24 * 3600));
            $ret[0] = array($days, loanPeriodUnitEnum::DAY);
        }*/

        return $ret;
    }

    public function getRepaymentPeriods($loanTime, $loanTimeUnit, $disburseDate = null, $firstRepayDate = null)
    {
        $periodCount = $this->getRepaymentPeriodCount($loanTime, $loanTimeUnit);
        return $this->getRepaymentPeriodsInner($periodCount, $disburseDate, $firstRepayDate);
    }

    public function calculatePrepaymentInfo($contract_info,$currentOutstandingSchemas, $contractRemainingPrincipal, $firstOutstandingSchemaInterestDate, $interestInfo, $cutOffDate, $prepaymentAmount, $remainingPrincipal, $remainingPeriodCount,$contract_remain_schemas=null)
    {
        $outstanding_principal = 0;
        $outstanding_interest = 0;
        $outstanding_operation_fee = 0;

        foreach ($currentOutstandingSchemas as $item) {
            $outstanding_principal += $item['receivable_principal']-$item['paid_principal'];
            $outstanding_interest += $item['receivable_interest']-$item['paid_interest'];
            $outstanding_operation_fee += $item['receivable_operation_fee']-$item['paid_operation_fee'];
        }


        $t_interest_date = strtotime($firstOutstandingSchemaInterestDate);
        // 计算贷款日至截止日的天数
        $t_cut_off_date = strtotime(date('Y-m-d',strtotime($cutOffDate)));
        $diff_days = ceil(($t_cut_off_date - $t_interest_date )/86400);
        if( $diff_days <= 0){
            $diff_days = 0;
        }

        if( $diff_days > 0 ){
            $interest_info = $this->normalizeInterestInfo($interestInfo, $diff_days, loanPeriodUnitEnum::DAY);
            // 基本利息
            if ($interest_info['interest_rate_type'] == 1) {
                $current_interest = $interest_info['interest_rate'];
            } else {
                $current_interest = round($contractRemainingPrincipal*$interest_info['interest_rate']/100,2);
            }
            // 运营费  todo 提前还款是否要重算operation fee
            if ($interest_info['operation_fee_type'] == 1) {
                $current_operator_fee = $interest_info['operation_fee']?round($interest_info['operation_fee'],2):0;
            }else{
                $current_operator_fee = round($contractRemainingPrincipal*$interest_info['operation_fee']/100,2);
            }

            $outstanding_interest += $current_interest;
            $outstanding_operation_fee += $current_operator_fee;
        }

        // 特殊产品处理
        $loan_category = (new member_credit_categoryModel())->getLoanCategoryInfoByMemberCategoryId($contract_info['member_credit_category_id']);
        if( $loan_category['special_key'] == specialLoanCateKeyEnum::QUICK_LOAN ){
            if( $contract_info['currency'] == currencyEnum::USD ){
                if( $outstanding_interest < 1 ){
                    $outstanding_interest = 1;
                }
            }elseif( $contract_info['currency'] == currencyEnum::KHR ){
                if( $outstanding_interest < 4000 ){
                    $outstanding_interest = 4000;
                }
            }
        }

        $outstanding_amount = $outstanding_principal + $outstanding_interest + $outstanding_operation_fee;

        if ($remainingPeriodCount > 0) {
            if ($prepaymentAmount) {
                $prepaymentPrincipal = $prepaymentAmount - $outstanding_amount;
                $remainingPrincipal = $contractRemainingPrincipal - $outstanding_principal - $prepaymentPrincipal;
            } else if ($remainingPrincipal) {
                // 没有指定还款金额，根据剩余本金计算要还的金额
                $prepaymentPrincipal = $contractRemainingPrincipal - $outstanding_principal - $remainingPrincipal;
                $prepaymentAmount = $prepaymentPrincipal + $outstanding_amount;
            } else {
                throw new Exception("Prepayment amount and remaining principal can't be 0 both while remaining period count greater than 0", errorCodesEnum::INVALID_PARAM);
            }
        } else {
            // 还清剩余本金
            $prepaymentPrincipal = $contractRemainingPrincipal - $outstanding_principal;
            $remainingPrincipal = 0;
            $prepaymentAmount = $prepaymentPrincipal + $outstanding_amount;
        }

        // 重新计算剩余计划
        if ($remainingPrincipal > 0) {
            $interest_date = $cutOffDate;
            $fist_repayment_date = reset($contract_remain_schemas)['receivable_date'];
            $periods = $this->getRepaymentPeriodsInner($remainingPeriodCount,$interest_date,$fist_repayment_date);
            $calc_ret = $this->getInstallmentSchema($remainingPrincipal, $periods, $interestInfo);
            if (!$calc_ret->STS) {
                throw new Exception($calc_ret->MSG, $calc_ret->CODE);
            } else {
                $remaining_schemas = $calc_ret->DATA['payment_schema'];
                $remaining_total_amount = $calc_ret->DATA['payment_total'];
            }
        } else {
            $remaining_schemas = null;
            $remaining_total_amount = array();
        }



        return array(
            'cut_off_date' => $cutOffDate,
            'prepayment_principal' => $prepaymentPrincipal,
            'total_principal' => $prepaymentPrincipal + $outstanding_principal,
            'total_interest' => $outstanding_interest,
            'total_operation_fee' => $outstanding_operation_fee,
            'total_amount' => $prepaymentAmount,
            'remaining_schemas' => $remaining_schemas,
            'remaining_total_amount' => $remaining_total_amount
        );
    }

    /** 获取分期方式的每期应还operation fee
     * @param $total_amount
     * @param $interest_info
     * @return float|result
     */
    public function getPerPeriodOperationFeeAmount($total_amount,$interest_info)
    {
        // 计算原始的operation fee 利率
        $rt = loan_baseClass::interestRateConversion($interest_info['operation_fee'],
            $interest_info['operation_fee_unit'],$this->repayment_period_type);
        if( !$rt->STS ){
            return $rt;
        }
        $periodic_operation_fee = $rt->DATA;  // 每期一样，不用换算到天
        // 每期应还operation_fee
        if ($interest_info['operation_fee_type'] == 1) {
            $per_operation_fee = round($periodic_operation_fee, 2);
        } else {
            $per_operation_fee = round($total_amount * $periodic_operation_fee / 100,2);
        }
        return $per_operation_fee;

    }
}