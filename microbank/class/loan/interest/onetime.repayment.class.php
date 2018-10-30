<?php

abstract class onetimeRepaymentClass extends interestTypeClass {

    /**
     * 获得指定贷款时间分成多少期还款
     * @param $loanTime int         贷款时间
     * @param $loanTimeUnit string  贷款时间单位
     * @return int  分成的期数
     * @throws Exception
     */
    public function getRepaymentPeriodCount($loanTime, $loanTimeUnit) {
        return 1;
    }

    public function getRepaymentPeriods($loanTime, $loanTimeUnit, $disburseDate = null, $firstRepayDate = null)
    {
        if( !$disburseDate ){
            $disburseDate = date('Y-m-d');
        }
        if( !$firstRepayDate ){
            $firstRepayDate = date('Y-m-d',
                strtotime('+'.$loanTime.' '.$loanTimeUnit,strtotime($disburseDate)));
        }
        //全部换算成天
        $period_interest_start_day = $disburseDate;
        $period_interest_end_day = $firstRepayDate;
        $days = (int)((strtotime($period_interest_end_day) - strtotime($period_interest_start_day)) / (24 * 3600));
        $ret[0] = array($days, loanPeriodUnitEnum::DAY,$firstRepayDate);
        return $ret;
        //return array(array($loanTime, $loanTimeUnit));
    }

    public function calculatePrepaymentInfo($contract_info,$currentOutstandingSchemas, $contractRemainingPrincipal, $firstOutstandingSchemaInterestDate, $interestInfo, $cutOffDate, $prepaymentAmount, $remainingPrincipal, $remainingPeriodCount,$contract_remain_schemas=null)
    {
        // 放的是逾期的，原来的计算方式不对
        /*if (empty($currentOutstandingSchemas)){
            return new result(false,'Can\'t calculate for repaid contract',null,errorCodesEnum::INVALID_PARAM);
        }*/

      /*  if (count($currentOutstandingSchemas) != 1) throw new Exception('Illegal data - Multiple schemas for onetime repayment', errorCodesEnum::UNEXPECTED_DATA);*/


        $t_interest_date = strtotime(date('Y-m-d',strtotime($firstOutstandingSchemaInterestDate)));
        // 计算贷款日至截止日的天数
        $t_cut_off_date = strtotime(date('Y-m-d',strtotime($cutOffDate)));
        $diff_days = ceil(($t_cut_off_date - $t_interest_date )/86400);
        if( $diff_days <= 0){
            $diff_days = 0;
        }

        $interest_info = $this->normalizeInterestInfo($interestInfo, $diff_days, loanPeriodUnitEnum::DAY);

        // 基本利息
        if ($interest_info['interest_rate_type'] == 1) {
            $current_interest = $interest_info['interest_rate'];
        } else {
            $current_interest = round($contractRemainingPrincipal*$interest_info['interest_rate']/100,2);
        }
        // 运营费
        if( $interest_info && $interest_info['operation_fee'] >0){
            if ($interest_info['operation_fee_type'] == 1) {
                $current_operator_fee = $interest_info['operation_fee']?round($interest_info['operation_fee'],2):0;
            }else{
                $current_operator_fee = round($contractRemainingPrincipal*$interest_info['operation_fee']/100,2);
            }
        } else {
            $current_operator_fee = 0;
        }

        // 最低限制
        ////////////////////////////////////////////////////////////////////
        // 利息费
        $min_interest = 0;
        if( $interest_info['interest_min_value'] ) {
            $min_interest = round($interest_info['interest_min_value'], 2);
        }

        // 运营费
        $min_operator_fee = 0;
        if( $interest_info['operation_min_value'] ) {
            $min_operator_fee = round($interest_info['operation_min_value'], 2);
        }

        // 调整
        if( $current_interest < $min_interest ){
            $current_interest = $min_interest;
        }
        if( $current_operator_fee < $min_operator_fee ){
            $current_operator_fee = $min_operator_fee;
        }

        // 特殊产品处理
        $loan_category = (new member_credit_categoryModel())->getLoanCategoryInfoByMemberCategoryId($contract_info['member_credit_category_id']);
        if( $loan_category['special_key'] == specialLoanCateKeyEnum::QUICK_LOAN ){
            if( $contract_info['currency'] == currencyEnum::USD ){
                if( $current_interest < 1 ){
                    $current_interest = 1;
                }
            }elseif( $contract_info['currency'] == currencyEnum::KHR ){
                if( $current_interest < 4000 ){
                    $current_interest = 4000;
                }
            }
        }

        /////////////////////////////////////////////////////////////////////

        $outstanding_amount = $current_interest + $current_operator_fee;   // 这段时间产生的利息与运营费必须要交

        // 必须还清剩余本金
        $prepaymentPrincipal = $contractRemainingPrincipal;
        $prepaymentAmount = $prepaymentPrincipal + $outstanding_amount;

        $remaining_schemas = null;
        $remaining_total_amount = array();



        return array(
            'cut_off_date' => $cutOffDate,
            'prepayment_principal' => $prepaymentPrincipal,
            'total_principal' => $prepaymentPrincipal,
            'total_interest' => $current_interest,
            'total_operation_fee' => $current_operator_fee,
            'total_amount' => $prepaymentAmount,
            'remaining_schemas' => $remaining_schemas,
            'remaining_total_amount' => $remaining_total_amount
        );
    }
}