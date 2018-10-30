<?php

class memberWithdrawByCashTradingClass extends clientWithdrawByCashTradingClass {
    public function __construct($memberId, $tradingPassword, $cashierUserId, $amount = null, $currency = null, $multi_currency = array())
    {
        if (!memberClass::checkTradingPassword($memberId, $tradingPassword)) {
            throw new Exception("Trading password is error", errorCodesEnum::NOT_PERMITTED);
        }

        $member_passbook = passbookClass::getSavingsPassbookOfMemberId($memberId);
        parent::__construct($member_passbook, $cashierUserId, $amount, $currency, $multi_currency);
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