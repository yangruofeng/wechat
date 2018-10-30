<?php

class loanRepaymentTradingClass extends tradingClass
{
    private $scheme_info;
    private $contract_info;
    private $product_info;
    private $loan_account;
    private $multi_currency;
    private $repayment_amount;
    private $actual_payment_amount;
    private $payment_principal;
    private $payment_interest;
    private $payment_operation_fee;

    // 币种参数已无用，外部已先换汇成合同币种
    public function __construct($schemeInfo, $repaymentAmount, $currency = null, $multi_currency = array())
    {
        parent::__construct();

        $contract_model = new loan_contractModel();
        $product_model = new loan_productModel();
        $account_model = new loan_accountModel();

        // 外部传进来的统一用兼容的数组取值方式
        $contract_info = $contract_model->getRow($schemeInfo['contract_id']);
        $product_info = $product_model->getRow($contract_info['product_id']);
        $account_info = $account_model->getRow($contract_info['account_id']);

        $this->scheme_info = $schemeInfo;;
        $this->contract_info = $contract_info;
        $this->product_info = $product_info;
        $this->loan_account = new loan_accountClass($account_info);

        $this->repayment_amount = $repaymentAmount;

        // 分配金额
        $payment_data = $this->getPaymentDetail($schemeInfo,$repaymentAmount);
        $this->actual_payment_amount = $payment_data['total_amount'];
        $this->payment_principal = $payment_data['principal'];
        $this->payment_interest = $payment_data['interest'];
        $this->payment_operation_fee = $payment_data['operation_fee'];

        $contract_currency = $contract_info['currency'];
        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_info['uid']);


        $this->subject = "Loan Repayment";
        $this->remark = $this->product_info->product_name . " : " . $this->contract_info->contract_sn .
            ",period: " . $schemeInfo['scheme_idx'] . "," . "due date:" . $contract_info->due_date;

        $this->sys_memo = "Loan repayment,contract sn:".$this->contract_info['contract_sn'].
            ',loan amount:'.$this->contract_info['apply_amount'].$contract_currency.
            ',client:'.($member_info['display_name']?($member_info['display_name']
                .'('.$member_info['kh_display_name'].')'):$member_info['login_code']).' cid '.$member_info['obj_guid'].
            ',schema id:'.$schemeInfo['uid'].',period:'.$schemeInfo['scheme_idx'].
            ',receivable date:'.$schemeInfo['receivable_date'].', total repayment amount:'.$this->actual_payment_amount.
            ',principal:'.$this->payment_principal.',interest:'.$this->payment_interest.
            ',operation fee:'.$this->payment_operation_fee;

    }


    public function getPaymentDetail($schema_info,$repayment_amount)
    {
        $need_amount = $schema_info['amount']-$schema_info['actual_payment_amount'];
        $left_interest = $schema_info['receivable_interest'] - $schema_info['paid_interest'];
        $left_operation_fee = $schema_info['receivable_operation_fee'] - $schema_info['paid_operation_fee'];
        $actual_payment_amount = $repayment_amount>=$need_amount?$need_amount:$repayment_amount;

        $cal_amount = $actual_payment_amount;
        $paid_interest = $paid_operation_fee = $paid_principal = 0;

        if( $cal_amount >= $left_interest ){
            $paid_interest += $left_interest;
            $cal_amount -= $paid_interest;
        }else{
            $paid_interest += $cal_amount;
            $cal_amount = 0;
        }

        if( $cal_amount > 0 ){
            if( $cal_amount >= $left_operation_fee ){
                $paid_operation_fee += $left_operation_fee;
                $cal_amount -= $paid_operation_fee;
            }else{
                $paid_operation_fee += $cal_amount;
                $cal_amount = 0;
            }
        }

        $paid_principal += $cal_amount;
        return array(
            'total_amount' => $actual_payment_amount,
            'principal' => $paid_principal,
            'interest' => $paid_interest,
            'operation_fee' => $paid_operation_fee
        );

    }


    public function getPaidInterestItemType()
    {
        $year_days = 365;
        $schema_info = $this->scheme_info;
        // 是否逾期
        if( date('Y-m-d') <= date('Y-m-d',strtotime($schema_info['receivable_date'])) ){
            // 贷款天数
            if( $this->contract_info['loan_term_day'] >=$year_days ){
                return incomingTypeEnum::INTEREST_STDL_LONG;
            }else{
                return incomingTypeEnum::INTEREST_STDL_SHORT;
            }
        }

        // 逾期了，逾期天数
        $e_time = strtotime(date('Y-m-d'));
        $s_time = strtotime($schema_info['receivable_date']);
        $days = ceil( ($e_time-$s_time)/86400 );
        if( $this->contract_info['loan_term_day'] >=$year_days ){

            if( $days >=30 && $days<=60 ){
                return incomingTypeEnum::INTEREST_SUB_STDL_LONG;
            }elseif( $days > 60 && $days <= 90 ){
                return incomingTypeEnum::INTEREST_DFL_LONG;
            }elseif( $days > 90 ){
                return incomingTypeEnum::INTEREST_LL_LONG;
            }else{
                return incomingTypeEnum::INTEREST_STDL_LONG;
            }

        }else{

            if( $days >=30 && $days<=60 ){
                return incomingTypeEnum::INTEREST_SUB_STDL_SHORT;
            }elseif( $days > 60 && $days <= 90 ){
                return incomingTypeEnum::INTEREST_DFL_SHORT;
            }elseif( $days > 90 ){
                return incomingTypeEnum::INTEREST_LL_SHORT;
            }else{
                return incomingTypeEnum::INTEREST_STDL_SHORT;
            }

        }
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
        if ($this->product_info->category == loanProductCategoryEnum::CREDIT_LOAN) {
            $business_type = businessTypeEnum::CREDIT_LOAN;
        } else {
            throw new Exception("Product category is not supported now - [" . $this->product_info->category . "]");
        }

        // 构建detail
        $currency = $this->contract_info['currency'];


        // 活期存款 - 借
        $detail[] = $this->createTradingDetailItem(
            $passbook_client,
            round($this->actual_payment_amount, 2),
            $currency,
            accountingDirectionEnum::DEBIT,
            'Loan repayment: ' . $this->contract_info->contract_sn);



        // 利息收入 - 贷
        if( $this->payment_interest > 0 ){
            $incoming_type = $this->getPaidInterestItemType();
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getIncomingPassbook($incoming_type, $business_type),
                round($this->payment_interest, 2),
                $currency,
                accountingDirectionEnum::CREDIT, 'Loan interest');
        }

        // 营运费收入 - 贷
        if ( $this->payment_operation_fee > 0 ) {
            $detail[] = $this->createTradingDetailItem(
                passbookClass::getIncomingPassbook(incomingTypeEnum::OPERATION_FEE, $business_type),
                round($this->payment_operation_fee, 2),
                $currency,
                accountingDirectionEnum::CREDIT, 'Loan operation fee');
        }

        // 应收贷款科目 - 贷
        if( $this->payment_principal ){
            $detail[] = $this->createTradingDetailItem(
                $passbook_receivable,
                round($this->payment_principal, 2),
                $currency,
                accountingDirectionEnum::CREDIT, 'Loan principal');
        }


        // 已经去掉了计划的抹零
        /*if ($finished) {
            // 抹零调整科目，根据差值正负号决定借贷方向
            if ($need_amount < $total_amount) {
                // 实际还款小于应该还款的抹零，调整科目方向为借
                $detail[] = $this->createTradingDetailItem(
                    passbookClass::getSystemPassbook(systemAccountCodeEnum::ROUND_ADJUST),
                    round($total_amount - $need_amount, 2),
                    $currency,
                    accountingDirectionEnum::DEBIT, $this->subject);
            } else if ($need_amount > $total_amount) {
                // 实际还款大于应该还款的抹零，调整科目方向为贷
                $detail[] = $this->createTradingDetailItem(
                    passbookClass::getSystemPassbook(systemAccountCodeEnum::ROUND_ADJUST),
                    round($need_amount - $total_amount, 2),
                    $currency,
                    accountingDirectionEnum::CREDIT, $this->subject);
            }
        }*/

        return $detail;
    }
}
