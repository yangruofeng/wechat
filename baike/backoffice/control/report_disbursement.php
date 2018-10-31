<?php

class report_disbursementControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report");
        Tpl::setDir("report_disbursement");
    }

    /**
     * co负责member情况
     */
    public function disbursementOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);
        Tpl::showPage('disbursement');
    }

    public function getDisbursementListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = trim($p['search_text']);
        $branch_id = intval($p['branch_id']);
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];

        $filters = array(
            'search_text' => $search_text,
            'branch_id' => $branch_id,
            'date_start' => $date_start,
            'date_end' => $date_end,
        );
        $data = loanDisbursementClass::getDisbursementList($pageNumber, $pageSize, $filters);
        return $data;
    }

    public function disbursementClientLoanOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);

        Tpl::showPage('disbursement.client');
    }

    public function getCoListOp($p)
    {
        $branch_id = intval($p['branch_id']);
        $co_list = credit_officerClass::getCoListByBranchId($branch_id);
        return array(
            'data' => $co_list
        );
    }

    public function getDisbursementClientLoanListOp($p)
    {
        $co_id = intval($p['co_id']);
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $filters = array(
            'date_start' => $date_start,
            'date_end' => $date_end,
        );

        $data = loanDisbursementClass::getDisbursementClientLoanListByCoId($co_id, $filters);
        return $data;
    }

    public function paymentInArrearsOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output('condition', $condition);
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output('currency_list', $currency_list);
        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);
        Tpl::showPage('payment.arrear');
    }

    public function getPaymentInArrearsListOp($p)
    {
        $search_text = trim($p['search_text']);
        $branch_id = intval($p['branch_id']);
        $currency = trim($p['currency']);
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];

        $filters = array(
            'search_text' => $search_text,
            'branch_id' => $branch_id,
            'currency' => $currency,
            'date_start' => $date_start,
            'date_end' => $date_end,
        );
        $data = loanOutstandingClass::getPaymentInArrearData($filters);
        return $data;
    }

    public function loanCollectionCategoryOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);
        Tpl::showPage('collection.category');
    }

    public function getLoanCollectionCategoryListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = trim($p['search_text']);
        $branch_id = intval($p['branch_id']);
        $currency = trim($p['currency']);
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];

        $filters = array(
            'search_text' => $search_text,
            'branch_id' => $branch_id,
            'currency' => $currency,
            'date_start' => $date_start,
            'date_end' => $date_end,
        );
        $data = loanOutstandingClass::getLoanCollectionByCategoryData($pageNumber, $pageSize, $filters);
        return $data;
    }

}
