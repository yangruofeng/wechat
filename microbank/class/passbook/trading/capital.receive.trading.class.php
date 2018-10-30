<?php

class capitalReceiveTradingClass extends tradingClass {
    private $amount;
    private $currency;

    public function __construct($amount, $currency,$remark=null)
    {
        parent::__construct();

        $this->amount = $amount;
        $this->currency = $currency;
        $this->remark = $remark;

        $this->subject = "Receive Capital";
        $this->sys_memo = 'Receive capital('.$remark.'):'.$amount.$currency;
    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     */
    protected function getTradingDetail()
    {
        $detail = array();

        // 准备所需的passbook
        $passbook_hiv = passbookClass::getSystemPassbook(systemAccountCodeEnum::HQ_CIV);
        $passbook_capital = passbookClass::getSystemPassbook(systemAccountCodeEnum::HQ_CAPITAL);

        // 构建detail

        // HIV账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_hiv,$this->amount,$this->currency,accountingDirectionEnum::DEBIT,'Transfer out capital');
        // capital - 贷
        $detail[]=$this->createTradingDetailItem($passbook_capital,$this->amount,$this->currency,accountingDirectionEnum::CREDIT,$this->subject);

        return $detail;
    }
}