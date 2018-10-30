<?php

class adjustTradingClass extends tradingClass {
    private $adjust_passbook;
    private $amount;
    private $currency;


    /**
     * adjustTradingClass constructor.
     * @param $passbook passbookClass
     * @param $amount
     * @param $currency
     */
    public function __construct($passbook, $amount, $currency,$remark=null)
    {
        parent::__construct();

        $this->adjust_passbook = $passbook;
        $this->amount = $amount;
        $this->currency = $currency;

        $this->subject = "System Adjust";
        $this->remark = $remark;
        $this->sys_memo = $passbook->getName()." adjust amount: $amount".$currency;
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
        $book_type = $this->adjust_passbook->getPassbookInfo()->book_type;
        switch ($book_type) {
            case passbookTypeEnum::ASSET:
            case passbookTypeEnum::COST:
            case passbookTypeEnum::PROFIT_EXPENSE:
                $passbook_system = passbookClass::getSystemPassbook(systemAccountCodeEnum::HQ_INIT);
                $adjust_book_plus_direction = accountingDirectionEnum::DEBIT;
                break;
            case passbookTypeEnum::DEBT:
            case passbookTypeEnum::EQUITY:
            case passbookTypeEnum::PROFIT:
            case passbookTypeEnum::PROFIT_INCOME:
            case passbookTypeEnum::COMMON:
                $passbook_system = passbookClass::getSystemPassbook(systemAccountCodeEnum::HQ_DEBT_INIT);
                $adjust_book_plus_direction = accountingDirectionEnum::CREDIT;
                break;
            default:
                throw new Exception('Unknown passbook type - ' . $book_type);
        }

        // 构建detail
        if ($this->amount > 0)
        {
            if ($adjust_book_plus_direction === accountingDirectionEnum::DEBIT) {
                // 调整账户 - 借
                $detail[]=$this->createTradingDetailItem($this->adjust_passbook,$this->amount,$this->currency,accountingDirectionEnum::DEBIT,$this->subject);
                // 系统账户 - 贷
                $detail[]=$this->createTradingDetailItem($passbook_system,$this->amount,$this->currency,accountingDirectionEnum::CREDIT,$this->subject);
            } else {
                // 调整账户 - 贷
                $detail[]=$this->createTradingDetailItem($this->adjust_passbook,$this->amount,$this->currency,accountingDirectionEnum::CREDIT,$this->subject);
                // 系统账户 - 借
                $detail[]=$this->createTradingDetailItem($passbook_system,$this->amount,$this->currency,accountingDirectionEnum::DEBIT,$this->subject);
            }
        }
        else if ($this->amount < 0)
        {
            if ($adjust_book_plus_direction === accountingDirectionEnum::CREDIT) {
                // 调整账户 - 借
                $detail[]=$this->createTradingDetailItem($this->adjust_passbook,-$this->amount,$this->currency,accountingDirectionEnum::DEBIT,$this->subject);
                // 系统账户 - 贷
                $detail[]=$this->createTradingDetailItem($passbook_system,-$this->amount,$this->currency,accountingDirectionEnum::CREDIT,$this->subject);
            } else {
                // 调整账户 - 贷
                $detail[]=$this->createTradingDetailItem($this->adjust_passbook,-$this->amount,$this->currency,accountingDirectionEnum::CREDIT,$this->subject);
                // 系统账户 - 借
                $detail[]=$this->createTradingDetailItem($passbook_system,-$this->amount,$this->currency,accountingDirectionEnum::DEBIT,$this->subject);
            }
        }

        return $detail;
    }
}