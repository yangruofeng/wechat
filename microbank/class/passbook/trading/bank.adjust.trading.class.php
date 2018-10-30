<?php

class bankAdjustTradingClass extends adjustTradingClass {
    public function __construct($bankAccountId, $amount, $currency)
    {
        parent::__construct(passbookClass::getBankAccountPassbook($bankAccountId), $amount, $currency);
    }
}