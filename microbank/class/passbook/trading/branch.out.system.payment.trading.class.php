<?php

class branchOutSystemPaymentTradingClass extends tradingClass {
    private $branch_id;
    private $amount;
    private $currency;

    public function __construct($branchId, $amount, $currency,$remark=null)
    {
        parent::__construct();

        $this->branch_id = $branchId;
        $this->amount = $amount;
        $this->currency = $currency;

        $this->subject = 'Branch Out System Payment';
        $this->remark = $remark;
        $branchObj = new objectBranchClass($branchId);
        $this->sys_memo = $branchObj->branch_name.'('.$branchObj->branch_code.')'.
            ' out system payment ('.$this->remark.'): '.$amount.$currency;
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
        $passbook_branch = passbookClass::getBranchPassbook($this->branch_id);

        // 构建detail
        // 系统账户 - 借
        $detail[]=$this->createTradingDetailItem(
            $passbook_system,
            $this->amount,
            $this->currency,
            accountingDirectionEnum::DEBIT,
            $this->remark);
        // 分行账户 - 贷
        $detail[]=$this->createTradingDetailItem(
            $passbook_branch,
            $this->amount,
            $this->currency,
            accountingDirectionEnum::CREDIT,
            'Out system income: ' . $this->remark);

        return $detail;
    }
}