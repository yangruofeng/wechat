<?php

class userAdjustTradingClass extends adjustTradingClass {
    public function __construct($userId, $amount, $currency)
    {
        parent::__construct(passbookClass::getUserPassbook($userId), $amount, $currency);
    }


}