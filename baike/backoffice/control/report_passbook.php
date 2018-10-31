<?php

class report_passbookControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report");
        Tpl::setDir("report_passbook");
    }


    public function balanceSheetOp()
    {
        $condition = array(
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);

        Tpl::showPage("balance_sheet");
    }

    public function getBalanceSheetDataOp($p)
    {
        $date_end = $p['date_end'];
        if (preg_match('/^\d{4}\-\d{2}-\d{2}$/', $date_end)) {
            $date_end .= " 23:59:59";
        }
        $data = balanceSheetClass::getDevBalanceSheetData($date_end);
        return $data;
    }

    public function balanceSheetDetailOp()
    {
        $param = array_merge(array(),$_GET,$_POST);
        $book_code = $param['book_code'];
        $currency = $param['currency'];
        $type = $param['type'];
        if($type == 'income'){
            Tpl::output('main_url','incomeStatement');
        }
        Tpl::output('report_type',$type);
        $m_gl_account = new gl_accountModel();
        $gl_account = $m_gl_account->getRow(array(
            'book_code' => $book_code
        ));
        if( !$gl_account ){
            showMessage('Not found account:'.$book_code);
        }

        // 是否叶子账户
        if( $gl_account['is_leaf'] ){
            // 获得passbook
            $gl_passbook = (new passbookModel())->getRow(array(
                'book_code' => $gl_account['book_code'],
            ));
            if( !$gl_passbook ){
                showMessage('Error gl account type!');
            }
            $_GET['book_id'] = $gl_passbook['uid'];
            $this->reportAccountFlowOp();
            die;

        }

        $data = balanceSheetClass::getGlAccountBalanceDetailData($gl_account,$currency);
        Tpl::output('gl_account',$gl_account);
        Tpl::output('currency',$currency);
        Tpl::output('data',$data);
        Tpl::showpage('gl_account.detail');
    }

    public function incomeStatementOp(){
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage("profit.index");
    }

    public function getIncomeStatementDataOp($p){
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $filters = array(
            'start_date' => $date_start,
            'end_date' => $date_end
        );
        $data = balanceSheetClass::getDevIncomeStatementData($date_start, $date_end);
        return $data;
    }


    public function reportAccountFlowOp()
    {
        $param = array_merge(array(),$_GET,$_POST);
        $book_id = $param['book_id'];
        $currency = $param['currency'];
        $type = $param['type'];
        if($type == 'income'){
            Tpl::output('main_url','incomeStatement');
        }
        Tpl::output('report_type',$type);
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        // 获得passbook
        $gl_passbook = (new passbookModel())->getRow(array(
            'uid' => $book_id,
        ));
        Tpl::output('gl_passbook',$gl_passbook);
        Tpl::output('book_id',$book_id);
        Tpl::output('currency',$currency);
        Tpl::showpage('gl_account.flow');

    }


    public function getPassbookFlowOp($p)
    {
        $book_id = $p['book_id'];
        $currency = $p['currency'];
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $page_num = $p['pageNumber'];
        $page_size = $p['pageSize'];
        $filters = array(
            'start_date' => $date_start,
            'end_date' => $date_end
        );
        $passbook = passbookClass::getPassbookInstanceById($book_id);
        $m = new passbook_account_flowModel();
        $page_data = $m->searchFlowListByBookAndCurrency($passbook,$currency,$page_num,$page_size,$filters);

        return array(
            'sts' => true,
            'data' => $page_data->rows,
            'pageNumber' => $page_data->pageIndex,
            'pageSize' => $page_data->pageSize,
            'total' => $page_data->count,
            'pageTotal' => $page_data->pageCount,
            'pageType' => $p['type']
        );
    }

    public function voucherFlowOp(){
        $type = $_GET['type'];
        if($type == 'income'){
            Tpl::output('main_url','incomeStatement');
        }
        $trade_id = $_GET['trade_id'];
        $m = new passbook_tradingModel();
        $info = $m->find(array("uid" => $trade_id));
        $data = $m->getTradingFlows($trade_id);
        Tpl::output("list", $data);
        Tpl::output("info", $info);
        Tpl::showpage('voucher.flow');
    }

    public function journalVoucherOp(){
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $trade_type = global_settingClass::getAllTradingType();
        Tpl::output("trade_type", $trade_type);
        Tpl::showpage('journal.voucher');
    }

    public function getJournalVoucherDataOp($p){
        $trade_id = $p['trade_id'];
        $trade_type = $p['trade_type'];
        $remark = $p['remark'];
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $pageNumber = $p['pageNumber'];
        $pageSize = $p['pageSize'];
        $filters = array(
            'trade_id' => $trade_id,
            'trade_type' => $trade_type,
            'remark' => $remark,
            'start_date' => $date_start,
            'end_date' => $date_end
        );
        $data = balanceSheetClass::getJournalVoucherData($pageNumber, $pageSize, $filters);
        return $data;
    }

    public function receivableInterestOp(){
        $condition = array(
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage("receivable.interest");
    }

    public function getReceivableInterestDataOp($p)
    {
        $date_end = $p['date_end'];
        $data = balanceSheetClass::getReceivableInterestData($date_end);
        return $data;
    }

    public function accountBalanceOp(){
        Tpl::showPage("account.balance");
    }

    public function getAccountBalanceListOp($p){
        $book_code = $p['book_code'];
        $book_name = $p['book_name'];
        $pageNumber = $p['pageNumber'];
        $pageSize = $p['pageSize'];
        $filters = array(
            'book_code' => $book_code,
            'book_name' => $book_name
        );
        $data = passbookClass::getPassbookAccount($pageNumber, $pageSize, $filters);
        return $data;
    }

    public function accountBalanceFlowOp(){
        $account_id = $_REQUEST['account_id'];
        Tpl::output("account_id", $account_id);
        Tpl::showPage("account.balance.flow");
    }
}
