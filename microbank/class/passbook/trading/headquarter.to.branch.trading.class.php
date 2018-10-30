<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/7/9
 * Time: 7:46
 */
class headquarterToBranchTradingClass extends tradingClass {
    private $branch_id;
    private $amount;
    private $currency;

    public function __construct($branchId, $amount, $currency,$remark)
    {
        parent::__construct();

        $this->branch_id = $branchId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->remark=$remark;

        $this->subject = 'Headquarter To Branch';

        $branchObj = new objectBranchClass($branchId);
        $this->sys_memo = 'Headquarter to branch '.$branchObj->branch_name.
            '('.$branchObj->branch_code.'):'.$amount.$currency;
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
        $passbook_hiv = passbookClass::getSystemPassbook(systemAccountCodeEnum::HQ_CIV);

        // 构建detail
        // 分行账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_branch,$this->amount,$this->currency,accountingDirectionEnum::DEBIT,'Receive from headquarter');
        // HIV账户 - 贷
        $detail[]=$this->createTradingDetailItem($passbook_hiv,$this->amount,$this->currency,accountingDirectionEnum::CREDIT,'Transfer to branch');

        return $detail;
    }
}