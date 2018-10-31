<?php

class report_accountingControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report");
        Tpl::setDir("report_accounting");
    }
    public function balanceSheetOp(){
        Tpl::showPage("balance_sheet");

    }
    public function incomeStatementOp(){
        Tpl::showPage("profit.index");

    }

}
