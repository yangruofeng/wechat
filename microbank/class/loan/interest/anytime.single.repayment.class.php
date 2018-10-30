<?php

class anytimeSingleRepaymentClass extends singleRepaymentClass {

    public function calculateRepaymentInterestOfSchema($schemaInfo, $interestInfo)
    {
        $contract_info = (new loan_contractModel())->find(array(
            'uid' => $schemaInfo['contract_id']
        ));

        $remaining_principal = $schemaInfo['receivable_principal'] - $schemaInfo['paid_principal'];

        $prepayment_info = $this->calculatePrepaymentInfo(
            $contract_info,
            array(),
            $remaining_principal,
            $schemaInfo['interest_date'],
            $interestInfo,
            date("Y-m-d"), 0, 0, 0,array($schemaInfo));

        $schemaInfo['receivable_interest'] = $prepayment_info['total_interest'];
        $schemaInfo['receivable_operation_fee'] = $prepayment_info['total_operation_fee'];
        $schemaInfo['amount'] =
            $schemaInfo['receivable_principal'] +
            $schemaInfo['receivable_interest'] +
            $schemaInfo['receivable_operation_fee'];

        return $schemaInfo;
    }
}