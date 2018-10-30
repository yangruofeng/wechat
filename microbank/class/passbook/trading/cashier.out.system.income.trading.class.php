<?php

class cashierOutSystemIncomeTradingClass extends tradingClass {
    private $cashier_user_id;
    private $amount;
    private $currency;

    private $cashier_info;

    public function __construct($cashierUserId, $amount, $currency,$remark=null)
    {
        parent::__construct();

        $this->cashier_user_id = $cashierUserId;
        $this->amount = $amount;
        $this->currency = $currency;

        $this->subject = 'Cashier Out System Income';
        $this->remark = $remark;

        $userObj = new objectUserClass($cashierUserId);
        $this->cashier_info = $userObj->object_info;
        $this->sys_memo = 'Out system income ('.$remark.'):cashier '.$userObj->user_name.
        '('.$userObj->user_code.'):'.$amount.$currency;
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
        $passbook_system = passbookClass::getSystemPassbook(systemAccountCodeEnum::OUT_SYSTEM_INCOME_AND_EXPENSES);
        $passbook_cashier = passbookClass::getUserPassbook($this->cashier_user_id);

        // 构建detail
        // 系统账户 - 贷
        $detail[]=$this->createTradingDetailItem(
            $passbook_system,
            $this->amount,
            $this->currency,
            accountingDirectionEnum::CREDIT,
            $this->remark);
        // 出纳账户 - 借
        $detail[]=$this->createTradingDetailItem(
            $passbook_cashier,
            $this->amount,
            $this->currency,
            accountingDirectionEnum::DEBIT,
            'Out system income: ' . $this->remark);

        return $detail;
    }
}