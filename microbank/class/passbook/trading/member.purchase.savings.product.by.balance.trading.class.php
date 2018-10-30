<?php

class memberPurchaseSavingsProductByBalanceTradingClass extends clientPurchaseSavingsProductByBalanceTradingClass {
    public function __construct($memberId, $productId, $amount, $currency, $purchaseFee)
    {
        parent::__construct(passbookClass::getSavingsPassbookOfMemberId($memberId), $productId, $amount, $currency, $purchaseFee);
    }
}