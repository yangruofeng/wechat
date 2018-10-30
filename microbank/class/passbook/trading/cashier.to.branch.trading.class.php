<?php

class cashierToBranchTradingClass extends outstandingTradingClass  {
    private $branch_id;
    private $cashier_user_id;
    private $amount;
    private $currency;

    private $cashier_info;

    public function __construct($cashierUserId, $branchId, $amount, $currency,$remark=null)
    {
        parent::__construct();

        $this->branch_id = $branchId;
        $this->cashier_user_id = $cashierUserId;
        $this->amount = $amount;
        $this->currency = $currency;

        $this->subject = 'Cashier To Branch';
        $this->remark = $remark;

        $userObj = new objectUserClass($cashierUserId);
        $this->cashier_info = $userObj->object_info;
        $branchObj = new objectBranchClass($branchId);
        $this->sys_memo = $userObj->user_name.'('.$userObj->user_code.')'.'
         to branch '.$branchObj->branch_name.'('.$branchObj->branch_code.'): '.$amount.
        $currency;
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
        $passbook_cashier = passbookClass::getUserPassbook($this->cashier_user_id);

        // 构建detail
        // 分行账户 - 借
        $detail[]=$this->createTradingDetailItem($passbook_branch,$this->amount,$this->currency,accountingDirectionEnum::DEBIT,'Receive from cashier: '.$this->cashier_info['user_name']);
        // 出纳账户 - 贷
        $detail[]=$this->createTradingDetailItem($passbook_cashier,$this->amount,$this->currency,accountingDirectionEnum::CREDIT,'Transfer to branch');

        return $detail;
    }

    public static function filterFlowsForConfirmVerify($flows) {
        $ret = array();
        foreach ($flows as $flow) {
            if ($flow->obj_type == "user" && $flow->credit > 0) {
                $ret[]=$flow;
            }
        }
        return $ret;
    }
}