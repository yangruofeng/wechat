<?php

class loanWrittenOffTradingClass extends tradingClass {
    private $contract_info;
    private $product_info;
    private $loan_account;

    public function __construct($contractId)
    {
        parent::__construct();

        $contract_model = new loan_contractModel();
        $product_model = new loan_productModel();
        $account_model = new loan_accountModel();

        $contract_info = $contract_model->getRow($contractId);
        $product_info = $product_model->getRow($contract_info->product_id);
        $account_info = $account_model->getRow($contract_info->account_id);

        $this->contract_info = $contract_info;
        $this->product_info = $product_info;
        $this->loan_account = new loan_accountClass($account_info);

        $currency = $contract_info['currency'];
        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_info['uid']);


        $this->subject = "Loan Written Off";
        $this->remark = "Contract SN : " . $this->contract_info->contract_sn;

        $this->sys_memo = "Loan contract written off,contract sn:".$this->contract_info['contract_sn'].
            ',loan amount:'.$this->contract_info['apply_amount'].$currency.
            ',client:'.($member_info['display_name']?($member_info['display_name']
                .'('.$member_info['kh_display_name'].')'):$member_info['login_code']).' cid '.$member_info['obj_guid'].
            ',loss principal:'.$this->contract_info->loss_principal;

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
        $passbook_receivable = ($this->contract_info->loan_term_day <= 365) ?
            passbookClass::getShortLoanPassbookOfLoanAccount($this->loan_account) :
            passbookClass::getLongLoanPassbookOfLoanAccount($this->loan_account); // 一年（含）以下是短期贷款，一年以上是长期贷款
        $passbook_principal_loss = passbookClass::getSystemPassbook(systemAccountCodeEnum::LOSS_LOAN_PRINCIPAL);

        // 应收贷款科目 - 贷
        $detail[] = $this->createTradingDetailItem(
            $passbook_receivable,
            $this->contract_info->loss_principal,
            $this->contract_info->currency,
            accountingDirectionEnum::CREDIT);

        // 本金损失科目 - 借
        $detail[] = $this->createTradingDetailItem(
            $passbook_principal_loss,
            $this->contract_info->loss_principal,
            $this->contract_info->currency,
            accountingDirectionEnum::DEBIT
        );

        // 不计应收，所以没有收入损失

        return $detail;
    }
}