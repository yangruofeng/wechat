<?php

class memberDepositByBankTradingClass extends clientDepositByBankTradingClass {
    public function __construct($memberId, $bankAccountId, $amount, $currency)
    {
        $member_passbook=passbookClass::getSavingsPassbookOfMemberId($memberId);
        parent::__construct($member_passbook, $bankAccountId, $amount, $currency);
    }
}