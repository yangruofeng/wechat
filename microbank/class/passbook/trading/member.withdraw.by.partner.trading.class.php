<?php

class memberWithdrawByPartnerTradingClass extends clientWithdrawByPartnerTradingClass {
    /**
     * memberWithdrawByPartnerTradingClass constructor.
     * @param int $memberId 会员ID
     * @param string $tradingPassword MD5加密后的交易密码
     * @param int $partnerId 合作银行或机构ID
     * @param float $amount 金额
     * @param string $currency 货币
     * @param float $trading_fee 交易费用，公司付出的
     * @param float $client_fee 取现手续费，客人付出的
     * @throws Exception
     */
    public function __construct($memberId, $tradingPassword, $partnerId, $amount, $currency, $trading_fee = 0.0, $client_fee = 0.0)
    {
        if (!memberClass::checkTradingPassword($memberId, $tradingPassword)) {
            throw new Exception("Trading password is error", errorCodesEnum::NOT_PERMITTED);
        }

        $member_passbook = passbookClass::getSavingsPassbookOfMemberId($memberId);
        parent::__construct($member_passbook, $partnerId, $amount, $currency, $trading_fee, $client_fee);
    }
}