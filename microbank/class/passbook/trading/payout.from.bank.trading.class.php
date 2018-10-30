<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/4/27
 * Time: 14:49
 */

class payoutFromBankTradingClass extends tradingClass {
    private $bank_account_id;
    private $amount;
    private $currency;
    private $outgoing_type;
    private $business_type;

    /**
     * payoutFromBankTradingClass constructor.
     * @param $bankAccountId
     * @param $amount
     * @param $currency
     * @param string $outgoingType
     * @param string $businessType
     */
    public function __construct($bankAccountId, $amount, $currency, $outgoingType, $businessType = businessTypeEnum::OTHER)
    {
        parent::__construct();

        $this->bank_account_id = $bankAccountId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->outgoing_type = $outgoingType;
        $this->business_type = $businessType;

        $this->subject = 'Bank Payout';

        $bankObj = new objectSysBankClass($bankAccountId);
        $this->sys_memo = "Bank payment($businessType->$outgoingType): ".$bankObj->bank_name.'('.
            $bankObj->bank_account_no.'):'.$amount.$currency;
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
        $passbook_outgoing = passbookClass::getOutgoingPassbook($this->outgoing_type, $this->business_type);

        // 构建detail
        // 银行账户 - 贷
        $detail[]=$this->createTradingDetailItem($passbook_bank,$this->amount,$this->currency,accountingDirectionEnum::CREDIT);
        // 支出账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_outgoing,$this->amount,$this->currency,accountingDirectionEnum::DEBIT);

        return $detail;
    }
}