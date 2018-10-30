<?php

class memberAdjustTradingClass extends clientAdjustTradingClass {
    public function __construct($memberId, $amount, $currency)
    {

        $member_passbook=passbookClass::getSavingsPassbookOfMemberId($memberId);
        parent::__construct($member_passbook, $amount, $currency);
    }
}