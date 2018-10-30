<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/4/27
 * Time: 14:44
 */

class incomeFromCashTradingClass extends tradingClass {
    private $cashier_user_id;
    private $amount;
    private $currency;
    private $incoming_type;
    private $business_type;

    public function __construct($cashierUserId, $amount, $currency, $incomingType, $businessType = businessTypeEnum::OTHER)
    {
        parent::__construct();

        $this->subject = 'Income From Cash';

        $this->cashier_user_id = $cashierUserId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->incoming_type = $incomingType;
        $this->business_type = $businessType;

        $userObj = new objectUserClass($cashierUserId);
        $this->sys_memo = "Other income ($businessType->$incomingType) by cash:cashier ".
            $userObj->user_name.'('.$userObj->user_code.'):'.$amount.$currency;

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
        $passbook_cashier = passbookClass::getUserPassbook($this->cashier_user_id);
        $passbook_incoming = passbookClass::getIncomingPassbook($this->incoming_type, $this->business_type);

        // 构建detail
        // cashier账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_cashier,$this->amount,$this->currency,accountingDirectionEnum::DEBIT);
        // 收入账户 - 贷
        $detail[]=$this->createTradingDetailItem($passbook_incoming,$this->amount,$this->currency,accountingDirectionEnum::CREDIT);

        return $detail;
    }
}