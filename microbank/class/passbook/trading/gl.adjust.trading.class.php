<?php

class glAdjustTradingClass extends adjustTradingClass {
    public function __construct($book_id, $amount, $currency)
    {
        parent::__construct(passbookClass::getPassbookInstanceById($book_id), $amount, $currency);
    }


}