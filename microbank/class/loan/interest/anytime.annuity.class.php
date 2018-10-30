<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/9/18
 * Time: 16:59
 */
class anytimeAnnuityClass extends annuitySchemaClass
{

    public function calculatePrepaymentInfo($contract_info,$currentOutstandingSchemas, $contractRemainingPrincipal, $firstOutstandingSchemaInterestDate, $interestInfo, $cutOffDate, $prepaymentAmount, $remainingPrincipal, $remainingPeriodCount,$contract_remain_schemas=null)
    {
        // 截止日固定到当天,不管任何设置
        $cutOffDate = date('Y-m-d');
        return parent::calculatePrepaymentInfo($contract_info,$currentOutstandingSchemas, $contractRemainingPrincipal, $firstOutstandingSchemaInterestDate, $interestInfo, $cutOffDate, $prepaymentAmount, $remainingPrincipal, $remainingPeriodCount,$contract_remain_schemas);
    }

}