<?php

class memberPaymentToMemberTradingClass extends clientPaymentToClientTradingClass {
    public function __construct($fromMemberId,$tradingPassword, $toMemberId, $amount, $currency)
    {
        if (!memberClass::checkTradingPassword($fromMemberId, $tradingPassword)) {
            throw new Exception("Trading password is error", errorCodesEnum::NOT_PERMITTED);
        }
        $from_member_passbook = passbookClass::getSavingsPassbookOfMemberId($fromMemberId);
        $to_member_passbook =passbookClass::getSavingsPassbookOfMemberId($toMemberId);
        parent::__construct($from_member_passbook, $to_member_passbook, $amount, $currency);
    }
}