<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/4/27
 * Time: 14:49
 */

class incomeFromBalanceTradingClass extends tradingClass {
    private $client_savings_passbook;
    private $amount;
    private $currency;
    private $incoming_type;
    private $business_type;
    private $multi_currency;

    /**
     * incomeOperationFeeBalanceTradingClass constructor.
     * @param $clientSavingsPassbook passbookClass
     * @param $amount
     * @param $currency
     * @param string $incomingType
     * @param string $businessType
     */
    public function __construct($clientSavingsPassbook, $amount, $currency, $incomingType, $businessType = businessTypeEnum::OTHER,$multi_currency=array())
    {
        parent::__construct();

        $this->subject = 'Income From Balance';

        $this->client_savings_passbook = $clientSavingsPassbook;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->incoming_type = $incomingType;
        $this->business_type = $businessType;

        if( $multi_currency && !empty($multi_currency) ){
            $this->multi_currency = $multi_currency;
        }else{
            $this->multi_currency = array();
            $this->multi_currency[$currency] = $amount;
        }

        $amount_arr = array();
        foreach( $this->multi_currency as $c=>$a ){
            $amount_arr[] = $c.':'.$a;
        }
        $this->sys_memo = "Other income ($businessType->$incomingType) from savings of ".
        $clientSavingsPassbook->getName().':'.implode(',',$amount_arr);
    }

    /**
     * 是否允许余额为负
     * ***注意***，需要特别小心使用，如非必要，不要重写此方法
     * @return bool
     */
    protected function allowNegativeBalance()
    {
        switch ($this->incoming_type) {
            case incomingTypeEnum::LOAN_FEE:
            case incomingTypeEnum::ADMIN_FEE:
            case incomingTypeEnum::ANNUAL_FEE:
                return true;
            default:
                return false;
        }
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
        $passbook_incoming = passbookClass::getIncomingPassbook($this->incoming_type, $this->business_type);

        // 构建detail
        foreach( $this->multi_currency as $c=>$a ){

            if( $a > 0 ){

                // client账户 - 借
                $detail[]=$this->createTradingDetailItem($this->client_savings_passbook,$a,$c,accountingDirectionEnum::DEBIT);
                // 收入账户 - 贷
                $detail[]=$this->createTradingDetailItem($passbook_incoming,$a,$c,accountingDirectionEnum::CREDIT);

            }
        }


        return $detail;
    }
}