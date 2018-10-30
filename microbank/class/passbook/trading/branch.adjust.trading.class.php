<?php

class branchAdjustTradingClass extends adjustTradingClass {
    public function __construct($branchId, $amount, $currency)
    {
        parent::__construct(passbookClass::getBranchPassbook($branchId), $amount, $currency);
    }


}