<?php

class loanPrepaymentTradingClass extends tradingClass {

    private $total_amount;
    private $principal;
    private $interest;
    private $operation_fee;
    private $penalty;
    private $prepayment_fee;
    private $contract_info;
    private $product_info;
    private $loan_account;
    private $currency;
    private $multi_currency;
    private $loss_interest;
    private $loss_operation_fee;

    public function getPaidInterestItemType()
    {

        $year_days = 365;
        if( $this->contract_info['loan_term_day'] >=$year_days ){

            return incomingTypeEnum::INTEREST_STDL_LONG;

        }else{

            return incomingTypeEnum::INTEREST_STDL_SHORT;
        }

    }

    public function __construct($contract_id,$total_amount, $paid_principal,$paid_interest,$paid_operation_fee,$loss_interest,$loss_operation_fee,$currency = null, $multi_currency = array())
    {
        parent::__construct();


        $contract_model = new loan_contractModel();
        $product_model = new loan_productModel();
        $account_model = new loan_accountModel();


        $contract_info = $contract_model->getRow($contract_id);
        $product_info = $product_model->getRow($contract_info->product_id);
        $account_info = $account_model->getRow($contract_info->account_id);


        $this->total_amount = $total_amount;
        $this->principal = $paid_principal;
        $this->interest = $paid_interest;
        $this->operation_fee = $paid_operation_fee;
        $this->penalty = 0;
        $this->prepayment_fee = 0;
        $this->contract_info = $contract_info;
        $this->product_info = $product_info;
        $this->loan_account = new loan_accountClass($account_info);
        $this->loss_interest = $loss_interest;
        $this->loss_operation_fee = $loss_operation_fee;

        $this->currency = $currency;

        if (!$multi_currency || empty($multi_currency)) {
            $this->multi_currency = array();
            if( $currency ){
                $this->multi_currency[$currency] = -1;
            }else{
                $this->multi_currency[$contract_info['currency']] = -1;
            }

        } else {
            $this->multi_currency = $multi_currency;
        }

        $currency = $contract_info['currency'];
        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_info['uid']);


        $this->subject = "Loan Prepayment";
        $this->remark = $this->product_info->product_name . ',' . $this->contract_info->contract_sn;

        $this->sys_memo = "Loan prepayment,contract sn:".$this->contract_info['contract_sn'].
            ',loan amount:'.$this->contract_info['apply_amount'].$currency.
            ',client:'.($member_info['display_name']?($member_info['display_name']
                .'('.$member_info['kh_display_name'].')'):$member_info['login_code']).' cid '.$member_info['obj_guid'].
            ',total prepayment amount:'.$this->total_amount.',principal:'.$this->principal.
            ',interest:'.$this->interest.',operation fee:'.$this->operation_fee;

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
        $passbook_client = passbookClass::getSavingsPassbookOfLoanAccount($this->loan_account);   // loan account对应的储蓄账户

        // 准备业务类型
        if ($this->product_info->category == loanProductCategoryEnum::CREDIT_LOAN ) {
            $business_type = businessTypeEnum::CREDIT_LOAN;
        } else {
            throw new Exception("Product category is not supported now - [" . $this->product_info->category . "]");
        }

        // 构建detail
        $currency = $this->contract_info->currency;
        $total_amount = $this->total_amount;

        $need_amount = $total_amount;
        foreach ($this->multi_currency as $c => $a) {
            if ($need_amount <= 0) break;

            if ($a > 0) {
                $currency_amount = $a;
            } else {
                $currency_amount = $need_amount;
            }

            // 如果还款货币与贷款合同货币不同，需要换汇结算户参与
            if ($c != $currency) {
                // 获取还款货币买入合同货币的当前设置汇率
                $exchange_rate = global_settingClass::getCurrencyRateBetween($c, $currency);
                // 计算还款货币的金额
                $exchanged_amount = round($currency_amount * $exchange_rate, 2);
                if ($need_amount < $exchanged_amount) {
                    $exchanged_amount = $need_amount;
                    $currency_amount = round($need_amount / $exchange_rate, 2);
                }
                $need_amount -= $exchanged_amount;

                // 活期存款 - 借
                $detail[] = $this->createTradingDetailItem(
                    $passbook_client,
                    $currency_amount,
                    $c,
                    accountingDirectionEnum::DEBIT,
                    'Loan prepayment: '.$this->contract_info->contract_sn);

                // 还款货币的换汇结算户 - 贷
                $detail[] = $this->createTradingDetailItem(
                    passbookClass::getSystemPassbook(systemAccountCodeEnum::EXCHANGE_SETTLEMENT),
                    $currency_amount,
                    $c,
                    accountingDirectionEnum::CREDIT,$this->subject,
                    $exchange_rate,
                    $currency);

                // 合同货币的换汇结算户 - 借
                $detail[] = $this->createTradingDetailItem(
                    passbookClass::getSystemPassbook(systemAccountCodeEnum::EXCHANGE_SETTLEMENT),
                    $exchanged_amount,
                    $currency,
                    accountingDirectionEnum::DEBIT,$this->subject,
                    $exchange_rate,
                    $c);
            } else {
                if ($need_amount < $currency_amount) {
                    $currency_amount = $need_amount;
                }
                $need_amount -= $currency_amount;

                // 活期存款 - 借
                $detail[] = $this->createTradingDetailItem(
                    $passbook_client,
                    $currency_amount,
                    $c,
                    accountingDirectionEnum::DEBIT,'Loan prepayment: '.$this->contract_info->contract_sn);
            }
        }

        if ($need_amount > 0) {
            throw new Exception('The specified amounts for multi currency are not enough to repay', errorCodesEnum::INVALID_PARAM);
        }

        // 应收贷款科目 - 贷
        $detail[] = $this->createTradingDetailItem(
            $passbook_receivable,
            $this->principal,
            $currency,
            accountingDirectionEnum::CREDIT,$this->subject);

        // todo 修改利息计入的科目
        // 利息收入 - 贷

        $detail[] = $this->createTradingDetailItem(
            passbookClass::getIncomingPassbook($this->getPaidInterestItemType(), $business_type),
            $this->interest,
            $currency,
            accountingDirectionEnum::CREDIT,
            $this->subject
         );

        // 运营费收入 - 贷
        $detail[] = $this->createTradingDetailItem(
            passbookClass::getIncomingPassbook(incomingTypeEnum::OPERATION_FEE, $business_type),
            $this->operation_fee,
            $currency,
            accountingDirectionEnum::CREDIT,
            $this->subject
        );

        // 收入是发生才产生，所以不用计算损失部分

        return $detail;
    }
}