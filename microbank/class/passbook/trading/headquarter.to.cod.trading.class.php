<?php

class headquarterToCodTradingClass extends tradingClass {
    private $amount;
    private $currency;

    public function __construct($amount, $currency)
    {
        parent::__construct();

        $this->amount = $amount;
        $this->currency = $currency;

        $this->subject = 'Transfer To CashOnHand(For Expense)';

        $this->sys_memo = $this->subject.':'.$amount.$currency;
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
        $passbook_cod = passbookClass::getSystemPassbook(systemAccountCodeEnum::HQ_COD);
        $passbook_civ = passbookClass::getSystemPassbook(systemAccountCodeEnum::HQ_CIV);

        // 构建detail
        // COD账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_cod,$this->amount,$this->currency,accountingDirectionEnum::DEBIT,'Receive from headquarter');
        // CIV账户 - 贷
        $detail[]=$this->createTradingDetailItem($passbook_civ,$this->amount,$this->currency,accountingDirectionEnum::CREDIT,'Transfer to branch');

        return $detail;
    }
}