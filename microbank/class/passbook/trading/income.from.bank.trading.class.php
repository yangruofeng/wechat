<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/4/27
 * Time: 14:49
 */

class incomeFromBankTradingClass extends tradingClass {
    private $bank_account_id;
    private $amount;
    private $currency;
    private $incoming_type;
    private $business_type;

    /**
     * incomeFromBankTradingClass constructor.
     * @param $bankAccountId
     * @param $amount
     * @param $currency
     * @param string $incomingType
     * @param string $businessType
     */
    public function __construct($bankAccountId, $amount, $currency, $incomingType, $businessType = businessTypeEnum::OTHER)
    {
        parent::__construct();

        $this->subject = 'Income From Bank';

        $this->bank_account_id = $bankAccountId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->incoming_type = $incomingType;
        $this->business_type = $businessType;

        $bankObj = new objectSysBankClass($bankAccountId);
        $this->sys_memo = "Other income ($businessType->$incomingType) through bank ".
            $bankObj->bank_name.'('.$bankObj->bank_account_no.'):'.$amount.$currency;
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
        $passbook_bank = passbookClass::getBankAccountPassbook($this->bank_account_id);
        $passbook_incoming = passbookClass::getIncomingPassbook($this->incoming_type, $this->business_type);

        // 构建detail
        // 银行账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_bank,$this->amount,$this->currency,accountingDirectionEnum::DEBIT);
        // 收入账户 - 贷
        $detail[]=$this->createTradingDetailItem($passbook_incoming,$this->amount,$this->currency,accountingDirectionEnum::CREDIT);

        return $detail;
    }
}