<?php

class clientRedeemSavingsProductByBalanceTradingClass extends tradingClass {
    protected $client_savings_passbook;
    protected $product_id;
    protected $amount;
    protected $currency;
    protected $redeem_fee;
    protected $product_info;

    public function __construct($clientSavingsPassbook, $productId, $amount, $currency, $redeemFee)
    {
        parent::__construct();

        $this->client_savings_passbook = $clientSavingsPassbook;
        $this->product_id = $productId;
        $this->amount = round($amount, 2);
        $this->currency = $currency;
        $this->redeem_fee = round($redeemFee, 2);
        $this->product_info = savingsProductClass::getProductInfoById($productId);

        $this->subject = "Purchase Savings Product";
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
        // 储蓄账户加钱 - 贷
        $detail[]=$this->createTradingDetailItem(
            $this->client_savings_passbook,
            $this->amount - $this->redeem_fee,
            $this->currency,
            accountingDirectionEnum::CREDIT,
            'Purchase '.$this->product_info['product_name']);

        // 产品账户扣钱 - 借
        $detail[]=$this->createTradingDetailItem(
            passbookClass::getSavingsProductPassbook($this->product_id),
            $this->amount,
            $this->currency,
            accountingDirectionEnum::DEBIT,
            'Purchase');

        if ($this->redeem_fee > 0) {
            // 手续收入账户 - 贷
            $detail[]=$this->createTradingDetailItem(
                passbookClass::getIncomingPassbook(incomingTypeEnum::OPERATION_FEE, businessTypeEnum::SAVINGS),
                $this->redeem_fee,
                $this->currency,
                accountingDirectionEnum::CREDIT,
                'Redeem Savings Product');
        }

        return $detail;
    }
}