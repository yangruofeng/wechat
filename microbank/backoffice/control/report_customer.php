<?php

class report_customerControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report");
        Tpl::setDir("report_customer");
    }

    public function customerOp(){
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage('customer');
    }

    public function getCustomerListOp($p){

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = trim($p['search_text']);
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];

        $filters = array(
            'search_text' => $search_text,
            'date_start' => $date_start,
            'date_end' => $date_end,
        );
        $data = customerReportClass::getCustomerList($pageNumber, $pageSize, $filters);
        return $data;

    }

    public function CIFTransactionOp(){
        Tpl::showPage('cif.transaction');
    }

    public function getCIFTransactionListOp(){

    }
}
