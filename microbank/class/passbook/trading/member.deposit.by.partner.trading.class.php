<?php

class memberDepositByPartnerTradingClass extends clientDepositByPartnerTradingClass {
    public function __construct($memberId, $partnerId, $amount, $currency)
    {
        $member_passbook=passbookClass::getSavingsPassbookOfMemberId($memberId);
        parent::__construct($member_passbook, $partnerId, $amount, $currency);
    }
}