<?php

class report_general_ledgerControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report");
        Tpl::setDir("report_general_ledger");
    }

    public function generalLedgerOp()
    {
        Tpl::showPage('full.trial.balance');
    }

    public function getFullTrialBalanceListOp()
    {

    }

    public function balanceByAccountHeaderOp(){
        Tpl::showPage('full.trial.balance');
    }

    public function balanceByMonthOp(){
        Tpl::showPage('full.trial.balance');
    }

    public function balanceByAccountHeaderAndMonthOp(){
        Tpl::showPage('full.trial.balance');
    }

    public function balanceForAMonthOp(){
        Tpl::showPage('balance.for.month');
    }

    public function getBalanceForMonthListOp(){

    }

    public function balanceByAccountHeaderForAMonthOp(){
        Tpl::showPage('balance.for.month');
    }

}
