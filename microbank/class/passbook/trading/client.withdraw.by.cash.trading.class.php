<?php

class clientWithdrawByCashTradingClass extends tradingClass {
    private $client_savings_passbook;
    private $cashier_user_id;
    private $amount;
    private $multi_currency;

    public function __construct($clientSavingsPassbook, $cashierUserId, $amount = null, $currency = null, $multi_currency = array())
    {
        parent::__construct();

        $this->client_savings_passbook = $clientSavingsPassbook;
        $this->cashier_user_id = $cashierUserId;

        if (!$multi_currency || empty($multi_currency)) {
            $this->multi_currency = array();
            $this->multi_currency[$currency] = $amount;
        } else {
            $this->multi_currency = $multi_currency;
        }

        $this->subject = "Client Withdraw";

        $userObj = new objectUserClass($cashierUserId);

        $amount_arr = array();
        foreach( $this->multi_currency as $c=>$a ){
            $amount_arr[] = $c.':'.$a;
        }

        $this->sys_memo = $clientSavingsPassbook->getName().' withdraw by cash:'.
            'cashier '.$userObj->user_name.'('.$userObj->user_code.'): '
            .implode(',',$amount_arr);

    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     * @throws Exception
     */
    protected function getTradingDetail()
    {
        $detail = array();

        // 准备所需的passbook
        $passbook_cashier = passbookClass::getUserPassbook($this->cashier_user_id);

        foreach ($this->multi_currency as $c => $a) {
            // 构建detail
            // 客人储蓄账户 - 借
            $detail[] = $this->createTradingDetailItem(
                $this->client_savings_passbook,
                $a,
                $c,
                accountingDirectionEnum::DEBIT,
                'Withdraw by cash(Counter)');
            // cashier账户 - 贷
            $detail[] = $this->createTradingDetailItem(
                $passbook_cashier,
                $a,
                $c,
                accountingDirectionEnum::CREDIT,
                'Client withdraw');
        }

        return $detail;
    }
}