<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/4/27
 * Time: 14:49
 */

class incomeOperationFeeBalanceTradingClass extends incomeFromBalanceTradingClass {
    /**
     * incomeOperationFeeBalanceTradingClass constructor.
     * @param $clientSavingsPassbook passbookClass
     * @param $amount
     * @param $currency
     * @param string $businessType
     */
    public function __construct($clientSavingsPassbook, $amount, $currency, $businessType = businessTypeEnum::OTHER)
    {
        parent::__construct($clientSavingsPassbook, $amount, $currency, incomingTypeEnum::OPERATION_FEE, $businessType);
        $this->subject = "Operation Fee";

        $this->sys_memo = "Operation Fee ($businessType) from savings of ".
            $clientSavingsPassbook->getName().':'.$amount.$currency;

    }
}