<?php

class loanDisburseTradingClass extends tradingClass {
    private $scheme_info;
    private $contract_info;
    private $product_info;
    private $loan_account;

    public function __construct($schemeInfo)
    {
        parent::__construct();

        $contract_model = new loan_contractModel();
        $product_model = new loan_productModel();
        $account_model = new loan_accountModel();

        $contract_info = $contract_model->getRow($schemeInfo->contract_id);
        $product_info = $product_model->getRow($contract_info->product_id);
        $account_info = $account_model->getRow($contract_info->account_id);

        $this->scheme_info=$schemeInfo;
        $this->contract_info=$contract_info;
        $this->product_info=$product_info;
        $this->loan_account=new loan_accountClass($account_info);

        $currency = $contract_info['currency'];
        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_info['uid']);

        $this->subject = 'Loan Disbursement ';
        $this->remark = $this->product_info->product_name . ": " . $this->contract_info->contract_sn;

        $this->sys_memo = "Loan disburse to client balance,contract sn:".$this->contract_info['contract_sn'].
            ',loan amount:'.$this->contract_info['apply_amount'].$currency.
            ',client:'.($member_info['display_name']?($member_info['display_name']
                .'('.$member_info['kh_display_name'].')'):$member_info['login_code']).' cid '.$member_info['obj_guid'].',disburse amount:'.$this->scheme_info['amount'].
            '';
    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array
     * @throws Exception
     */
    protected function getTradingDetail()
    {
        $detail = array();

        // 准备所需的passbook
        $passbook_receivable = ($this->contract_info->loan_term_day <= 365) ?
            passbookClass::getShortLoanPassbookOfLoanAccount($this->loan_account) :
            passbookClass::getLongLoanPassbookOfLoanAccount($this->loan_account); // 一年（含）以下是短期贷款，一年以上是长期贷款
        $passbook_client = passbookClass::getSavingsPassbookOfLoanAccount($this->loan_account);   // loan account对应的储蓄账户

        // 构建detail
        $currency = $this->contract_info->currency;

        // 应收贷款科目 - 借
        $detail[] = $this->createTradingDetailItem($passbook_receivable, $this->scheme_info->principal, $currency, accountingDirectionEnum::DEBIT,
            $this->subject);
        // 活期存款 - 贷
        $detail[] = $this->createTradingDetailItem($passbook_client, $this->scheme_info->principal, $currency, accountingDirectionEnum::CREDIT,
            $this->subject.': '.$this->contract_info->contract_sn);

        return $detail;
    }
}