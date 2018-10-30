<?php

class headquarterToPartnerTradingClass extends tradingClass {
    private $partner_account_id;
    private $amount;
    private $currency;

    public function __construct($partnerAccountId, $amount, $currency,$remark=null,$handler_name="")
    {
        parent::__construct();

        $this->partner_account_id = $partnerAccountId;
        $this->amount = $amount;
        $this->currency = $currency;

        $this->subject = 'Headquarter Deposit To Partner';
        $this->remark = $remark;

        $partnerObj = new objectPartnerClass($partnerAccountId);
        $this->sys_memo = 'Headquarter deposit to partner '.$partnerObj->partner_name.'('.
            $partnerObj->partner_code.'):'.$amount.$currency.", Handler:".$handler_name;
    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     */
    protected function getTradingDetail()
    {
        $detail = array();

        // 准备所需的passbook
        $passbook_partner = passbookClass::getPartnerPassbook($this->partner_account_id);
        $passbook_hiv = passbookClass::getSystemPassbook(systemAccountCodeEnum::HQ_CIV);

        // 构建detail
        // 银行账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_partner,$this->amount,$this->currency,accountingDirectionEnum::DEBIT,'Receive from headquarter');
        // HIV账户 - 贷
        $detail[]=$this->createTradingDetailItem($passbook_hiv,$this->amount,$this->currency,accountingDirectionEnum::CREDIT,'Transfer to partner');

        return $detail;
    }
}