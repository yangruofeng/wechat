<?php

class report_cross_applicationControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report");
        Tpl::setDir("report_cross_application");
    }


    public function crossApplicationOp(){
        Tpl::showPage('cross.application');
    }

    public function authorizedSystemUsersOp(){
        Tpl::showPage('authorized.system.users');
    }

    public function getAuthorizedSystemUsersListOp(){

    }

    public function transactionListingOp(){
        Tpl::showPage('transaction.listing');
    }

    public function getTransactionListOp(){

    }

    public function todayOpenAccountsOp(){
        Tpl::showPage('today.open.accounts');
    }

    public function getTodayOpenAccountsListOp(){

    }

    public function dailyTransactionListingOp(){
        Tpl::showPage('daily.transaction.listing');
    }

    public function getDailyTransactionListOp(){

    }

    public function summaryAccountByGLOp(){
        Tpl::showPage('summary.account.by.gl');
    }

    public function getSummaryAccountByGLListOp(){

    }

    public function  GLAccountAndCustomerBalanceOp(){
        Tpl::showPage('gl.account.and.customer.balance');
    }

    public function getGLAccountAndCustomerBalanceListOp(){

    }

    public function GLAccountMovementOp(){
        Tpl::showPage('gl.account.movement');
    }

    public function getGLAccountMovementListOp(){

    }

    public function EndOfMonthAccountListingOp(){
        Tpl::showPage('end.of.month.account.listing');
    }

    public function getEndOfMonthAccountListingListOp(){

    }


}
