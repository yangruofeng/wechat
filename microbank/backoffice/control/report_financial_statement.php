<?php

class report_financial_statementControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report");
        Tpl::setDir("report_financial_statement");
    }

    public function financialStatementOp(){
        Tpl::showPage('financial.statement');
    }

    public function getBalanceSheetListOp(){

    }

    public function profitAndLossOp(){
        Tpl::showPage('profit.and.loss');
    }

    public function getProfitAndLossListOp(){

    }

    public function CGAPIndicatorOp(){
        Tpl::showPage('cgap.indicator');
    }

    public function getCGAPIndicatorListOp(){

    }

    public function weeklyStatementOfConditionOp(){
        Tpl::showPage('weekly.statement.of.condition');
    }

    public function getWeeklyStatementOfConditionListOp(){

    }
}
