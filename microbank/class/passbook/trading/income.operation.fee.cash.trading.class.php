<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/4/27
 * Time: 14:44
 */

class incomeOperationFeeCashTradingClass extends incomeFromCashTradingClass  {
    public function __construct($cashierUserId, $amount, $currency, $businessType = businessTypeEnum::OTHER)
    {
        parent::__construct($cashierUserId, $amount, $currency, incomingTypeEnum::OPERATION_FEE, $businessType);

        $this->subject = "Operation Fee";

        $userObj = new objectUserClass($cashierUserId);
        $this->sys_memo = "Operation Fee ($businessType) by cash:cashier ".
            $userObj->user_name.'('.$userObj->user_code.'):'.$amount.$currency;

    }
}