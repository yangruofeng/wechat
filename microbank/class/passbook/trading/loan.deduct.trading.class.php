<?php

class loanDeductTradingClass extends tradingClass {
    private $scheme_info;
    private $contract_info;
    private $product_info;
    private $loan_account;
    private $client_passbook;

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

        $passbook_client = passbookClass::getSavingsPassbookOfLoanAccount($this->loan_account);   // loan account对应的储蓄账户
        $this->client_passbook = $passbook_client;

        $currency = $this->contract_info['currency'];

        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_info['uid']);

        $this->subject = "Loan Deducting from balance";
        $fee_array = array();
        if( $this->scheme_info->deduct_annual_fee > 0 ){
            $fee_array[] = "Annual fee: ".$this->scheme_info->deduct_annual_fee;
        }
        if( $this->scheme_info->deduct_admin_fee > 0 ){
            $fee_array[] = "Admin fee: ".$this->scheme_info->deduct_admin_fee;
        }
        if( $this->scheme_info->deduct_loan_fee > 0 ){
            $fee_array[] = "Loan fee: ".$this->scheme_info->deduct_loan_fee;
        }
        if( $this->scheme_info->deduct_insurance_fee > 0 ){
            $fee_array[] = "Insurance fee: ".$this->scheme_info->deduct_insurance_fee;
        }
        if( $this->scheme_info['deduct_service_fee'] > 0 ){
            $fee_array[] = "Service charges: ".$this->scheme_info['deduct_service_fee'];
        }
        $this->remark = implode(',',$fee_array);

        $this->sys_memo = "Loan deduct fee,contract sn:".$this->contract_info['contract_sn'].
            ',loan amount:'.$this->contract_info['apply_amount'].$currency.
        ',client:'.($member_info['display_name']?($member_info['display_name']
            .'('.$member_info['kh_display_name'].')'):$member_info['login_code']).' cid '.$member_info['obj_guid'].',disburse amount:'.$this->scheme_info['amount'].
        ',other fee:'.implode(',',$fee_array);
    }


    protected function getInterestItemType()
    {
        $year_days = 365;
        $loan_days = $this->contract_info['loan_term_day'];
        if( $loan_days >= $year_days ){
            return incomingTypeEnum::INTEREST_STDL_LONG;
        }else{
            return incomingTypeEnum::INTEREST_STDL_SHORT;
        }
    }

    /**
     * 获取交易的passbook、货币、借方金额、贷方金额列表明细
     * @return array()
     */
    protected function getTradingDetail()
    {
        $detail = array();

        // 准备所需的passbook
        $passbook_client = $this->client_passbook;

        // 构建detail
        $currency = $this->contract_info->currency;

        // 准备业务类型
        if ( $this->product_info->category == loanProductCategoryEnum::CREDIT_LOAN ) {
            $business_type = businessTypeEnum::CREDIT_LOAN;
        } else {
            throw new Exception("Product category is not supported now - [" . $this->product_info->category . "]");
        }
        // 具体收入账户的passbook，用到才创建

        // 总费用
        $total_fee = 0;
        // 年费收入 - 贷
        if ($this->scheme_info->deduct_annual_fee > 0) {
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getIncomingPassbook(incomingTypeEnum::ANNUAL_FEE, $business_type),
                $this->scheme_info->deduct_annual_fee,
                $currency,
                accountingDirectionEnum::CREDIT,
                'Loan annual fee');
            $total_fee += $this->scheme_info->deduct_annual_fee;
        }

        // 利息收入 - 贷
        if ($this->scheme_info->deduct_interest > 0) {
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getIncomingPassbook($this->getInterestItemType(), $business_type),
                $this->scheme_info->deduct_interest,
                $currency,
                accountingDirectionEnum::CREDIT,
                'Loan interest');
            $total_fee += $this->scheme_info->deduct_interest;
        }

        // 营运费收入 - 贷
        if ($this->scheme_info->deduct_operation_fee > 0) {
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getIncomingPassbook(incomingTypeEnum::OPERATION_FEE, $business_type),
                $this->scheme_info->deduct_operation_fee,
                $currency,
                accountingDirectionEnum::CREDIT,
                'Loan interest');
            $total_fee += $this->scheme_info->deduct_operation_fee;
        }

        // 管理费收入 - 贷
        if ($this->scheme_info->deduct_admin_fee > 0) {
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getIncomingPassbook(incomingTypeEnum::ADMIN_FEE, $business_type),
                $this->scheme_info->deduct_admin_fee,
                $currency,
                accountingDirectionEnum::CREDIT,
                'Loan admin fee');
            $total_fee += $this->scheme_info->deduct_admin_fee;
        }

        // 手续费收入 - 贷
        if ($this->scheme_info->deduct_loan_fee > 0) {
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getIncomingPassbook(incomingTypeEnum::LOAN_FEE, $business_type),
                $this->scheme_info->deduct_loan_fee,
                $currency,
                accountingDirectionEnum::CREDIT,
                'Loan fee');
            $total_fee += $this->scheme_info->deduct_loan_fee;
        }

        // 保险费收入 - 贷
        if ($this->scheme_info->deduct_insurance_fee > 0) {
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getIncomingPassbook(incomingTypeEnum::INSURANCE_FEE, $business_type),
                $this->scheme_info->deduct_insurance_fee,
                $currency,
                accountingDirectionEnum::CREDIT,
                'Insurance fee'
            );
            $total_fee += $this->scheme_info->deduct_insurance_fee;
        }

        // 服务费收入 - 贷
        if( $this->scheme_info->deduct_service_fee > 0 ){
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getIncomingPassbook(incomingTypeEnum::SERVICE_FEE, $business_type),
                $this->scheme_info->deduct_service_fee,
                $currency,
                accountingDirectionEnum::CREDIT,
                'Service charges'
            );
            $total_fee += $this->scheme_info->deduct_service_fee;
        }

        // 活期存款 - 借
        if ($total_fee > 0) {
            $detail[] = $this->createTradingDetailItem($passbook_client, $total_fee, $currency, accountingDirectionEnum::DEBIT,
                'Loan fee');
        }

        return $detail;
    }
}