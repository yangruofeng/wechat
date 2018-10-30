<?php

class clientDepositByPartnerTradingClass extends tradingClass {
    private $client_savings_passbook;
    private $partner_info;
    private $amount;
    private $currency;

    public function __construct($clientSavingsPassbook, $partnerId, $amount, $currency)
    {
        parent::__construct();

        $partner_model = new partnerModel();
        $partner_info = $partner_model->getRow($partnerId);

        $this->client_savings_passbook = $clientSavingsPassbook;
        $this->partner_info = $partner_info;
        $this->amount = $amount;
        $this->currency = $currency;

        $this->subject = "Client Deposit";

        $partnerObj = new objectPartnerClass($partnerId);
        $this->sys_memo = $clientSavingsPassbook->getName().' deposit to savings by partner(api):'.
            'partner '.$partnerObj->partner_name.': '.$amount.$currency;

    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     */
    protected function getTradingDetail()
    {
        $detail = array();

        // 准备所需的passbook
        $passbook_partner = passbookClass::getPartnerPassbook($this->partner_info->uid);

        // 构建detail
        // 客人储蓄账户 - 贷
        $detail[]=$this->createTradingDetailItem($this->client_savings_passbook,$this->amount,$this->currency,accountingDirectionEnum::CREDIT,'Deposit by '.$this->partner_info['partner_name']);
        // partner结算账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_partner,$this->amount,$this->currency,accountingDirectionEnum::DEBIT,'Client deposit');

        return $detail;
    }
}