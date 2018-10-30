<?php

class report_gl_transactionControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report");
        Tpl::setDir("report_gl_transaction");
    }

    public function glTransactionOp(){
        Tpl::showPage('transaction.by.batch');
    }

    public function getTransactionByBatchListOp(){

    }

    public function transactionByAccountOp(){
        Tpl::showPage('transaction.by.account');
    }

    public function getTransactionByAccountListOp(){

    }

    public function transactionForAccountByValueDateOp(){
        Tpl::showPage('transaction.for.account.by.value.date');
    }

    public function getTransactionForAccountByValueDateListOp(){

    }

    public function transactionByValueDateOp(){
        Tpl::showPage('transaction.for.account.by.value.date');
    }


    public function transactionByPostDateOp(){
        Tpl::showPage('transaction.by.account');
    }

    public function reprintOldBatchOp(){
        Tpl::showPage('reprint.old.batch');
    }

    public function getReprintOldBatchListOp(){

    }


}
