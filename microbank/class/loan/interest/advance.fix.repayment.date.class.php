<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/25
 * Time: 22:48
 */
class advanceFixRepaymentDateClass extends singleRepaymentClass
{

    public function getRepaymentPeriods($loanTime, $loanTimeUnit, $disburseDate = null, $firstRepayDate = null)
    {
        if( !$disburseDate ){
            $disburseDate = date('Y-m-d');
        }
        if( !$firstRepayDate ){
            $firstRepayDate = date('Y-m-d',
                strtotime('+'.$loanTime.' '.$loanTimeUnit,strtotime($disburseDate)));
        }

        // 不管贷款日期，计算利息都是计算一个月
        $ret[0] = array(1,loanPeriodUnitEnum::MONTH,$firstRepayDate);
        return $ret;

    }


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