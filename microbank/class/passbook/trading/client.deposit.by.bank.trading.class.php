<?php

class clientDepositByBankTradingClass extends tradingClass {
    private $client_savings_passbook;
    private $bank_account_id;
    private $amount;
    private $currency;

    public function __construct($clientSavingsPassbook, $bankAccountId, $amount, $currency)
    {
        parent::__construct();

        $this->client_savings_passbook = $clientSavingsPassbook;
        $this->bank_account_id = $bankAccountId;
        $this->amount = $amount;
        $this->currency = $currency;

        $this->subject = "Client Deposit";

        $bankObj = new objectSysBankClass($bankAccountId);
        $this->sys_memo = $clientSavingsPassbook->getName().' deposit to savings through '.
        $bankObj->bank_name.'('.$bankObj->bank_account_no.'): '.$amount.$currency;

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

        // 构建detail
        // 客人储蓄账户 - 贷
        $detail[]=$this->createTradingDetailItem($this->client_savings_passbook,$this->amount,$this->currency,accountingDirectionEnum::CREDIT,'Deposit by bank transfer');
        // 银行账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_bank,$this->amount,$this->currency,accountingDirectionEnum::DEBIT,'Client deposit');

        return $detail;
    }
}