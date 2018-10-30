<?php

class manualVoucherTradingClass extends tradingClass {
    private $dr_list;
    private $cr_list;
    private $amount;
    private $currency;

    public function __construct($_dr_list, $_cr_list,$_ccy,$_remark)
    {
        parent::__construct();
        $this->dr_list=$_dr_list;
        $this->cr_list=$_cr_list;
        $this->currency=$_ccy;

        $this->subject = "Manual Voucher";
        $this->remark = $_remark;
        $this->sys_memo = $_remark;
    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     */
    protected function getTradingDetail()
    {
        $detail = array();
        foreach($this->dr_list as $i=>$item){
            $detail[]=$this->createTradingDetailItem(passbookClass::getPassbookOfManualGLCode($item['gl_code'],$this->currency),$item['gl_amount'],$this->currency,accountingDirectionEnum::DEBIT,$item['gl_subject']);
        }
        foreach($this->cr_list as $i=>$item){
            $detail[]=$this->createTradingDetailItem(passbookClass::getPassbookOfManualGLCode($item['gl_code'],$this->currency),$item['gl_amount'],$this->currency,accountingDirectionEnum::CREDIT,$item['gl_subject']);
        }
        return $detail;
    }
}