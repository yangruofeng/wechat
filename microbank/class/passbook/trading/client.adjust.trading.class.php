<?php

class clientAdjustTradingClass extends adjustTradingClass {
    public function __construct($clientSavingsPassbook, $amount, $currency)
    {
        parent::__construct($clientSavingsPassbook, $amount, $currency);
    }
}