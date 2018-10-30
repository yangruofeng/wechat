<?php

class memberRedeemSavingsProductByBalanceTradingClass extends clientRedeemSavingsProductByBalanceTradingClass {
    public function __construct($memberId, $productId, $amount, $currency, $redeemFee)
    {
        parent::__construct(passbookClass::getSavingsPassbookOfMemberId($memberId), $productId, $amount, $currency, $redeemFee);
    }
}