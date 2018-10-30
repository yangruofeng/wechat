<?php

class civExtOutTradingClass extends tradingClass {
    private $amount;
    private $currency;
    private $branch_id;

    public function __construct($branch_id,$amount, $currency,$remark)
    {
        parent::__construct();
        $this->branch_id=$branch_id;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->subject = 'Extra Cash Out';
        $this->remark=$remark?:"Branch-ID:".$branch_id;

        $branchObj = new objectBranchClass($branch_id);
        $this->sys_memo = $branchObj->branch_name.'('.$branchObj->branch_code.')'.
            ' extra cash out ('.$this->remark.') : '.$amount.$currency;
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
        $passbook_ext = passbookClass::getSystemPassbook(systemAccountCodeEnum::BRANCH_CIV_EXT_OUT);
        $passbook_branch = passbookClass::getBranchPassbook($this->branch_id);

        // 构建detail
        // COD账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_ext,$this->amount,$this->currency,accountingDirectionEnum::DEBIT,$this->subject);
        // CIV账户 - 贷
        $detail[]=$this->createTradingDetailItem($passbook_branch,$this->amount,$this->currency,accountingDirectionEnum::CREDIT,$this->subject);

        return $detail;
    }
}