<?php

class bankToHeadquarterTradingClass extends tradingClass {
    private $bank_account_id;
    private $amount;
    private $currency;

    public function __construct($bankAccountId, $amount, $currency,$remark=null)
    {
        parent::__construct();

        $this->bank_account_id = $bankAccountId;
        $this->amount = $amount;
        $this->currency = $currency;

        $this->subject = 'Headquarter Withdraw From Bank';
        $this->remark = $remark;

        $bankObj = new objectSysBankClass($bankAccountId);
        $this->sys_memo = 'Headquarter withdraw from bank '.$bankObj->bank_name.'('.
            $bankObj->bank_account_no.'):'.$amount.$currency;

    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     */
    protected function getTradingDetail()
    {
        $detail = array();

        // 准备所需的passbook
        $passbook_bank = passbookClass::getBankAccountPassbook($this->bank_account_id);
        $passbook_hiv = passbookClass::getSystemPassbook(systemAccountCodeEnum::HQ_CIV);

        // 构建detail
        // 银行账户 - 贷
        $detail[]=$this->createTradingDetailItem($passbook_bank,$this->amount,$this->currency,accountingDirectionEnum::CREDIT,$this->subject);
        // HIV账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_hiv,$this->amount,$this->currency,accountingDirectionEnum::DEBIT,$this->subject);

        return $detail;
    }
}