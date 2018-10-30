<?php

class memberExchangeTradingClass extends exchangeTradingClass {
    public function __construct($memberId, $amount, $fromCurrency, $toCurrency) {
        $member_passbook =passbookClass::getSavingsPassbookOfMemberId($memberId);
        parent::__construct($member_passbook, $amount, $fromCurrency, $toCurrency);
    }
}