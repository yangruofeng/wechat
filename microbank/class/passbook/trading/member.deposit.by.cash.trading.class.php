<?php

class memberDepositByCashTradingClass extends clientDepositByCashTradingClass {
    public function __construct($memberId, $cashierUserId, $amount = null, $currency = null, $multi_currency = array(), $exchange_to_currency = array())
    {
        $member_passbook=passbookClass::getSavingsPassbookOfMemberId($memberId);
        parent::__construct($member_passbook, $cashierUserId, $amount, $currency, $multi_currency, $exchange_to_currency);
    }

    public static function filterFlowsForConfirmVerify($flows) {
        $ret = array();
        foreach ($flows as $flow) {
            if ($flow->obj_type == "user") {
                $ret[]=$flow;
            }
        }
        return $ret;
    }
}