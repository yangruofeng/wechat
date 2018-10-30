<?php

class report_repaymentControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report");
        Tpl::setDir("report_repayment");
    }

    public function repaymentOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -7))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage('repayment');
    }

    public function getRepaymentListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $currency = trim($p['currency']);
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];

        $filters = array(
            'currency' => $currency,
            'date_start' => $date_start,
            'date_end' => $date_end,
        );
        $data = repaymentReportClass::getRepaymentList($pageNumber, $pageSize, $filters);
        return $data;
    }

    public function agingOfLoanArrearOp()
    {
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output('currency_list', $currency_list);
        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);
        Tpl::showPage('repayment.aging.of.loan.arrear');
    }

    public function getAgingOfLoanArrearListOp($p)
    {
        $currency = trim($p['currency']);
        $branch_id = intval($p['branch_id']);
        $filters = array(
            'currency' => $currency,
            'branch_id' => $branch_id,
        );

        $data = repaymentReportClass::agingOfLoanArrear($filters);
        return $data;
    }

    public function loanInFallingDueOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -7))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage('repayment.loan.in.falling.due');
    }

    public function getLoanInFallingDueListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $currency = trim($p['currency']);
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];

        $filters = array(
            'currency' => $currency,
            'date_start' => $date_start,
            'date_end' => $date_end,
        );
        $data = repaymentReportClass::getLoanInFallingDueList($pageNumber, $pageSize, $filters);
        return $data;
    }


}
