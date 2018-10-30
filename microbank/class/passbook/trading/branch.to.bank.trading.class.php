<?php

class branchToBankTradingClass extends tradingClass {
    private $branch_id;
    private $bank_account_id;
    private $amount;
    private $currency;

    public function __construct($branchId, $bankAccountId, $amount, $currency,$remark=null)
    {
        parent::__construct();

        $this->branch_id = $branchId;
        $this->bank_account_id = $bankAccountId;
        $this->amount = $amount;
        $this->currency = $currency;

        $this->subject = "Branch Deposit To Bank";
        $this->remark = $remark;

        $branchObj = new objectBranchClass($branchId);
        $bankObj = new objectSysBankClass($bankAccountId);
        $this->sys_memo = $branchObj->branch_name.'('.$branchObj->branch_code.')'.
            ' deposit to '.$bankObj->bank_name.'('.$bankObj->bank_account_no.')'.
        ': '.$amount.$currency;
    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     */
    protected function getTradingDetail()
    {
        $detail = array();

        // 准备所需的passbook
        $passbook_branch = passbookClass::getBranchPassbook($this->branch_id);
        $passbook_bank = passbookClass::getBankAccountPassbook($this->bank_account_id);

        // 构建detail
        // 银行账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_bank,$this->amount,$this->currency,accountingDirectionEnum::DEBIT,$this->subject);
        // 分行账户 - 贷
        $detail[]=$this->createTradingDetailItem($passbook_branch,$this->amount,$this->currency,accountingDirectionEnum::CREDIT,$this->subject);

        return $detail;
    }
}