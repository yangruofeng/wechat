<?php

class homeControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('enum');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Home");
        Tpl::setDir("home");
    }

    /**
     * 搜索 client contract
     * @param $p
     * @return result
     */
    public function searchTextOp($p)
    {
        $search_text = trim($p['search_text']);
        $m_client_member = M('client_member');
        $chk_client = $m_client_member->find(array('obj_guid' => $search_text));
        if ($chk_client) {
            return new result(true, '', array('type' => 'client', 'url' => getUrl('client', 'clientDetail', array('uid' => $chk_client['uid'], 'show_menu' => 'client-client'), false, BACK_OFFICE_SITE_URL)));
        }
        $chk_client = $m_client_member->find(array('login_code' => $search_text));
        if ($chk_client) {
            return new result(true, '', array('type' => 'client', 'url' => getUrl('client', 'clientDetail', array('uid' => $chk_client['uid'], 'show_menu' => 'client-client'), false, BACK_OFFICE_SITE_URL)));
        }
        $m_loan_contract = M('loan_contract');
        $chk_contract = $m_loan_contract->find(array('contract_sn' => $search_text));
        if ($chk_contract) {
            return new result(true, '', array('type' => 'contract', 'url' => getUrl('loan', 'contractDetail', array('uid' => $chk_contract['uid'], 'show_menu' => 'loan-contract'), false, BACK_OFFICE_SITE_URL)));
        } else {
            return new result(false, 'The search results are empty!');
        }
    }

    /**
     * 计算器
     */
    public function calculatorOp()
    {

        $class_product = new loan_productClass();
        $valid_products = $class_product->getValidProductList();
        Tpl::output("valid_products", $valid_products);

        $m_core_definition = M('core_definition');
        $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type', 'guarantee_type'));
        Tpl::output("mortgage_type", $define_arr['mortgage_type']);
        Tpl::output("guarantee_type", $define_arr['guarantee_type']);

        $interest_payment = (new interestPaymentEnum())->Dictionary();
        $interest_rate_period = (new interestRatePeriodEnum())->Dictionary();
        Tpl::output("interest_payment", $interest_payment);
        Tpl::output("interest_rate_period", $interest_rate_period);

        Tpl::showPage("calculator");
    }

    /**
     * 贷款计算
     * @param $p
     * @return result
     */
    public function loanPreviewOp($p)
    {
        $creditLoan = new credit_loanClass();
        $re = $creditLoan->loanPreview($p);
        if (!$re->STS) {
            return $re;
        }
        $data = $re->DATA;
        $data_new = array();
        $data_new['loan_amount'] = ncAmountFormat($data['total_repayment']['total_principal']);
        $data_new['repayment_amount'] = ncAmountFormat($data['total_repayment']['total_payment']);
        $data_new['arrival_amount'] = ncAmountFormat($data['arrival_amount']);
        $data_new['service_charge'] = ncAmountFormat($data['loan_fee']);
        $data_new['total_interest'] = ncAmountFormat($data['total_repayment']['total_interest']);
        $data_new['period_repayment_amount'] = ncAmountFormat($data['period_repayment_amount']);
        $data_new['interest_rate'] = $data['interest_rate_type'] == 0 ? ($data['interest_rate'] . '%') : ncAmountFormat($data['interest_rate']);
        $data_new['interest_rate_unit'] = $data['interest_rate_unit'];
        $data_new['repayment_number'] = count($data['repayment_schema']);
        if ($data_new['repayment_number'] > 1) {
            $first_repayment = array_shift($data['repayment_schema']);
            $second_repayment = array_shift($data['repayment_schema']);
            if ($first_repayment['amount'] == $second_repayment['amount']) {
                $data_new['each_repayment'] = ncAmountFormat($first_repayment['amount']);
                $data_new['single_repayment'] = 0;
                $data_new['first_repayment'] = 0;
            } else {
                $data_new['first_repayment'] = ncAmountFormat($first_repayment['amount']);
                $data_new['single_repayment'] = 0;
                $data_new['each_repayment'] = 0;
            }
        } else {
            $first_repayment = array_shift($data['repayment_schema']);
            $data_new['single_repayment'] = ncAmountFormat($first_repayment['amount']);
            $data_new['first_repayment'] = 0;
            $data_new['each_repayment'] = 0;
        }
        $data_new['operation_fee'] = ncAmountFormat($first_repayment['receivable_operation_fee']);
        $re->DATA = $data_new;
        return $re;
    }
}
