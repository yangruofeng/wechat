<?php

class exchangeTradingClass extends tradingClass {
    private $exchange_passbook;
    private $amount;
    private $from_currency;
    private $to_currency;
    private $increase_direction;
    private $decrease_direction;

    public $exchange_rate;
    public $exchange_to_amount;

    /**
     * exchangeTradingClass constructor.
     * @param $passbook passbookClass
     * @param $amount float  The amount of from currency
     * @param $fromCurrency
     * @param $toCurrency
     * @param $exchangeRate float 1 fromCurrency = ? toCurrency
     * @throws Exception
     */
    public function __construct($passbook, $amount, $fromCurrency, $toCurrency, $exchangeRate=null)
    {
        parent::__construct();

        $this->exchange_passbook = $passbook;
        $this->amount = round($amount,2);
        $this->from_currency = $fromCurrency;
        $this->to_currency = $toCurrency;
        $this->exchange_rate = $exchangeRate;

        $this->subject = "Exchange Currency";

        $book_info = $this->exchange_passbook->getPassbookInfo();
        $book_type = $book_info['book_type'];
        switch ($book_type) {
            case passbookTypeEnum::ASSET:
                $increase_direction = accountingDirectionEnum::DEBIT;
                $decrease_direction = accountingDirectionEnum::CREDIT;
                if (!$this->exchange_rate) {
                    throw new Exception("Must specify the exchange rate for ASSET passbook exchange", errorCodesEnum::INVALID_PARAM);
                }
                break;
            case passbookTypeEnum::DEBT:
                $increase_direction = accountingDirectionEnum::CREDIT;
                $decrease_direction = accountingDirectionEnum::DEBIT;
                if (!$this->exchange_rate) {
                    $this->exchange_rate = global_settingClass::getCurrencyRateBetween($this->from_currency, $this->to_currency);
                }
                break;
            default:
                throw new Exception("Exchange trading is supported for ASSET/DEBT passbook only", errorCodesEnum::NOT_SUPPORTED);
                break;
        }

        $this->increase_direction = $increase_direction;
        $this->decrease_direction = $decrease_direction;
        $this->exchange_to_amount = round($this->amount * $this->exchange_rate, 2);

        $this->sys_memo = $passbook->getName().' currency exchange('.$this->remark.') :'.$amount.$fromCurrency.
            '->'.$this->exchange_to_amount.$toCurrency.',exchange rate:'.$this->exchange_rate;


    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     * @throws Exception
     */
    protected function getTradingDetail()
    {
        $detail = array();

        // 构建detail

        // from货币passbook - passbook余额减少方向
        $detail[] = $this->createTradingDetailItem(
            $this->exchange_passbook,
            $this->amount,
            $this->from_currency,
            $this->decrease_direction,
            $this->subject,
            $this->exchange_rate,
            $this->to_currency);

        // from货币的换汇结算户 - passbook余额增加方向
        $detail[] = $this->createTradingDetailItem(
            passbookClass::getSystemPassbook(systemAccountCodeEnum::EXCHANGE_SETTLEMENT),
            $this->amount,
            $this->from_currency,
            $this->increase_direction,
            $this->subject,
            $this->exchange_rate,
            $this->to_currency);


        // to货币passbook - passbook余额增加方向
        $detail[] = $this->createTradingDetailItem(
            $this->exchange_passbook,
            $this->exchange_to_amount,
            $this->to_currency,
            $this->increase_direction,
            $this->subject,
            $this->exchange_rate,
            $this->from_currency);

        // to货币的换汇结算户 - passbook余额减少方向
        $detail[] = $this->createTradingDetailItem(
            passbookClass::getSystemPassbook(systemAccountCodeEnum::EXCHANGE_SETTLEMENT),
            $this->exchange_to_amount,
            $this->to_currency,
            $this->decrease_direction,
            $this->subject,
            $this->exchange_rate,
            $this->from_currency);

        return $detail;
    }
}