<?php

class report_outstandingControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report");
        Tpl::setDir("report_outstanding");
    }

    public function outstandingOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);
        Tpl::showPage('outstanding.province');
    }

    public function getLoanOutstandingProvinceListOp($p)
    {
        $branch_id = intval($p['branch_id']);
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $filters = array(
            'date_start' => $date_start,
            'date_end' => $date_end,
        );
        $data = loanOutstandingClass::getloanOutstandingProvinceData($branch_id, $filters);
        return $data;
    }

    public function loanOutstandingGenderOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);
        Tpl::showPage('outstanding.gender');
    }

    public function getLoanOutstandingGenderListOp($p)
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

        $data = loanOutstandingClass::getloanOutstandingGenderData($pageNumber, $pageSize, $filters);
        return $data;
    }

    public function loanOutstandingProductOp()
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
        Tpl::showPage('outstanding.product');
    }

    public function getLoanOutstandingProductListOp($p)
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
        $title = loanOutstandingClass::getLoanOutstandingProductList();
        $data = loanOutstandingClass::getLoanOutstandingProductData($pageNumber, $pageSize, $filters);
        $data['title'] = $title;
        return $data;
    }

    public function loanOutstandingPurposeOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        Tpl::showPage('outstanding.purpose');
    }

}
