<?php

class reportControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('report');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Report");
        Tpl::setDir("report");
    }

    public function balanceSheetOp()
    {

        $ret = balanceSheetClass::getReportData();
        Tpl::output("data", $ret->DATA);
        Tpl::showpage('balance_sheet');
    }

    //Page: Cash On Hand Of Credit Officer
    public function showCashOnHandCoListPageOp()
    {
        Tpl::output('title', 'Credit Officer');
        Tpl::output('method', 'getCashOnHandCoList');//获取相关数据方法
        Tpl::showpage('cash.on.hand');
    }

    /**
     * Data: Cash On Hand Of Credit Officer
     * @param $p
     * @return array
     */
    public function getCashOnHandCoListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = $p['search_text'];
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getAssetCashOnHandListOfCreditOfficer($search_text, $pageNumber, $pageSize);
        $ret['flow'] = 'showCashOnHandCoFlowPage';
        return $ret;
    }

    //Page: Cash On Hand Of Teller
    public function showCashOnHandTellerListPageOp()
    {
        Tpl::output('title', 'Teller');
        Tpl::output('method', 'getCashOnHandTellerList');//获取相关数据方法
        Tpl::showpage('cash.on.hand');
    }

    /**
     * Data: Cash On Hand Of Teller
     * @param $p
     * @return array
     */
    public function getCashOnHandTellerListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = $p['search_text'];
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getAssetCashOnHandListOfTeller($search_text, $pageNumber, $pageSize);
        $ret['flow'] = 'showCashOnHandTellerFlowPage';
        return $ret;
    }

    //Page: Cash On Hand Of Other
    public function showCashOnHandOtherListPageOp()
    {
        Tpl::output('title', 'Other');
        Tpl::output('method', 'getCashOnHandOtherList');//获取相关数据方法
        Tpl::showpage('cash.on.hand');
    }

    /**
     * Data: Cash On Hand Of Other
     * @param $p
     * @return array
     */
    public function getCashOnHandOtherListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = $p['search_text'];
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getAssetCashOnHandListOfOtherUser($search_text, $pageNumber, $pageSize);
        $ret['flow'] = 'showCashOnHandOtherFlowPage';
        return $ret;
    }

    //Page: Cash On Hand Flow Of Teller
    public function showCashOnHandTellerFlowPageOp()
    {
        $uid = $_GET['uid'];
        $m_user = new um_userModel();
        $ret = $m_user->getUserInfoById($uid);
        Tpl::output('info', $ret);
        Tpl::output('title', 'Teller');
        Tpl::output('back', 'showCashOnHandTellerListPage');
        Tpl::output('method', 'getCashOnHandTellerFlow');//获取相关数据方法
        Tpl::showpage('cash.on.hand.flow');
    }

    /**
     * Data: Cash On Hand Flow Of Teller
     * @param $p
     * @return array
     */
    public function getCashOnHandTellerFlowOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getAssetPassbookFlow($p['pid'], $pageNumber, $pageSize);
        return $ret;
    }

    //Page: Cash On Hand Flow Of Credit Officer
    public function showCashOnHandCoFlowPageOp()
    {
        $uid = $_GET['uid'];
        $m_user = new um_userModel();
        $ret = $m_user->getUserInfoById($uid);
        Tpl::output('info', $ret);
        Tpl::output('title', 'Teller');
        Tpl::output('back', 'showCashOnHandCoListPage');
        Tpl::output('method', 'getCashOnHandCoFlow');//获取相关数据方法
        Tpl::showpage('cash.on.hand.flow');
    }

    /**
     * Data: Cash On Hand Flow Of Credit Officer
     * @param $p
     * @return array
     */
    public function getCashOnHandCoFlowOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getAssetPassbookFlow($p['pid'], $pageNumber, $pageSize);
        return $ret;
    }

    //Page: Cash On Hand Flow Of Other
    public function showCashOnHandOtherFlowPageOp()
    {
        $uid = $_GET['uid'];
        $m_user = new um_userModel();
        $ret = $m_user->getUserInfoById($uid);
        Tpl::output('info', $ret);
        Tpl::output('title', 'Teller');
        Tpl::output('back', 'showCashOnHandOtherListPage');
        Tpl::output('method', 'getCashOnHandOtherFlow');//获取相关数据方法
        Tpl::showpage('cash.on.hand.flow');
    }

    /**
     * Data: Cash On Hand Flow Of Other
     * @param $p
     * @return array
     */
    public function getCashOnHandOtherFlowOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getAssetPassbookFlow($p['pid'], $pageNumber, $pageSize);
        return $ret;
    }

    //Page: Cash In Valut Of Headquarter
    public function showCashInVaultHeadquarterListPageOp()
    {
        Tpl::output('title', 'Headquarters');
        Tpl::output('method', 'getCashInVaultHeadquarterList');
        Tpl::showpage('cash.in.vault');
    }

    /**
     * Data: Cash In Valut Of Headquarter
     * @param $p
     * @return array
     */
    public function getCashInVaultHeadquarterListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = $p['search_text'];
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getCashInVaultListOfHq($search_text, $pageNumber, $pageSize);
        $ret['page'] = 'headquarter'; //用于显示不同的字段
        $ret['flow'] = 'showCashInVaultHeadquarterFlowPage'; //handle flow
        return $ret;
    }

    //Page: Cash In Valut Flow Of Headquarter
    public function showCashInVaultHeadquarterFlowPageOp()
    {
        $uid = $_GET['uid'];
        $m_gl_account = new gl_accountModel();
        $ret = $m_gl_account->getAccountInfoById($uid);
        Tpl::output('info', $ret);
        Tpl::output('title', 'Headquarters');
        Tpl::output('back', 'showCashInVaultHeadquarterListPage');
        Tpl::output('method', 'getCashInVaultHeadquarterFlow');//获取相关数据方法
        Tpl::showpage('cash.in.valut.flow');
    }

    /**
     * Data: Cash In Valut Flow Of Headquarter
     * @param $p
     * @return array
     */
    public function getCashInVaultHeadquarterFlowOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getAssetPassbookFlow($p['pid'], $pageNumber, $pageSize);
        return $ret;
    }

    //Page: Cash In Valut Of Branch
    public function showCashInVaultBranchListPageOp()
    {
        Tpl::output('title', 'Branches');
        Tpl::output('method', 'getCashInVaultBranchList');
        Tpl::showpage('cash.in.vault');
    }

    /**
     * Data: Cash In Valut Of Branch
     * @param $p
     * @return array
     */
    public function getCashInVaultBranchListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = $p['search_text'];
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getCashInVaultListOfBranch($search_text, $pageNumber, $pageSize);
        $ret['page'] = 'branch';//用于显示不同的字段
        $ret['flow'] = 'showCashInVaultBranchFlowPage'; //handle flow
        return $ret;
    }

    //Page: Cash In Valut Flow Of Branch
    public function showCashInVaultBranchFlowPageOp()
    {
        $uid = $_GET['uid'];
        $m_site_branch = new site_branchModel();
        $ret = $m_site_branch->getBranchInfoById($uid);
        Tpl::output('info', $ret);
        Tpl::output('title', 'Branch');
        Tpl::output('back', 'showCashInVaultBranchListPage');
        Tpl::output('method', 'getCashInVaultBranchFlow');//获取相关数据方法
        Tpl::showpage('cash.in.valut.flow');
    }

    /**
     * Data: Cash In Valut Flow Of Branch
     * @param $p
     * @return array
     */
    public function getCashInVaultBranchFlowOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getAssetPassbookFlow($p['pid'], $pageNumber, $pageSize);
        return $ret;
    }

    //Page: Receivable Of Short Principal
    public function showReceivableShortPrincipalListPageOp()
    {
        Tpl::output('title', 'Short Term');
        Tpl::output('method', 'getReceivableShortPrincipalList');
        Tpl::showpage('receivable.principal');
    }

    /**
     * Data: Receivable Of Short Principal
     * @param $p
     * @return array
     */
    public function getReceivableShortPrincipalListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = $p['search_text'];
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getReceivablePrincipalListOfShortTerm($search_text, $pageNumber, $pageSize);
        $ret['flow'] = 'showReceivableShortPrincipalFlowPage'; //handle flow
        return $ret;
    }

    //Page: Receivable Flow Of Short Principal
    public function showReceivableShortPrincipalFlowPageOp()
    {
        $uid = $_GET['uid'];
        $m_member = new memberModel();
        $ret = $m_member->getMemberInfoById($uid);
        Tpl::output('info', $ret);
        Tpl::output('title', 'Short Terms');
        Tpl::output('back', 'showReceivableShortPrincipalListPage');
        Tpl::output('method', 'getReceivableShortPrincipalFlow');//获取相关数据方法
        Tpl::showpage('receivable.principal.flow');
    }

    /**
     * Data: Receivable Flow Of Short Principal
     * @param $p
     * @return array
     */
    public function getReceivableShortPrincipalFlowOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getAssetPassbookFlow($p['pid'], $pageNumber, $pageSize);
        return $ret;
    }

    //Page: Receivable Of Long Principal
    public function showReceivableLongPrincipalListPageOp()
    {
        Tpl::output('title', 'Long Term');
        Tpl::output('method', 'getReceivableLongPrincipalList');
        Tpl::showpage('receivable.principal');
    }

    /**
     * Data: Receivable Of Long Principal
     * @param $p
     * @return array
     */
    public function getReceivableLongPrincipalListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = $p['search_text'];
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getReceivablePrincipalListOfLongTerm($search_text, $pageNumber, $pageSize);
        $ret['flow'] = 'showReceivableLongPrincipalFlowPage'; //handle flow
        return $ret;
    }

    //Page: Receivable Flow Of Short Principal
    public function showReceivableLongPrincipalFlowPageOp()
    {
        $uid = $_GET['uid'];
        $m_member = new memberModel();
        $ret = $m_member->getMemberInfoById($uid);
        Tpl::output('info', $ret);
        Tpl::output('title', 'Long Terms');
        Tpl::output('back', 'showReceivableLongPrincipalListPage');
        Tpl::output('method', 'getReceivableLongPrincipalFlow');//获取相关数据方法
        Tpl::showpage('receivable.principal.flow');
    }

    /**
     * Data: Receivable Flow Of Short Principal
     * @param $p
     * @return array
     */
    public function getReceivableLongPrincipalFlowOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getAssetPassbookFlow($p['pid'], $pageNumber, $pageSize);
        return $ret;
    }

    //Page: Liability Of Savings
    public function showLiabilitySavingsListPageOp()
    {
        Tpl::output('title', 'Deposit');
        Tpl::output('method', 'getLiabilitySavingsList');
        Tpl::showpage('liabilities.deposit');
    }

    /**
     * Data: Receivable Of Long Principal
     * @param $p
     * @return array
     */
    public function getLiabilitySavingsListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = $p['search_text'];
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getLiabilityListOfSavings($search_text, $pageNumber, $pageSize);
        $ret['flow'] = 'showLiabilitySavingsFlowPage'; //handle flow
        return $ret;
    }

    //Page: Receivable Flow Of Short Principal
    public function showLiabilitySavingsFlowPageOp()
    {
        $uid = $_GET['uid'];
        $m_member = new memberModel();
        $ret = $m_member->getMemberInfoById($uid);
        Tpl::output('info', $ret);
        Tpl::output('title', 'Savings');
        Tpl::output('back', 'showLiabilitySavingsListPage');
        Tpl::output('method', 'getLiabilitySavingsFlow');//获取相关数据方法
        Tpl::showpage('liabilities.deposit.flow');
    }

    /**
     * Data: Receivable Flow Of Short Principal
     * @param $p
     * @return array
     */
    public function getLiabilitySavingsFlowOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getAssetPassbookFlow($p['pid'], $pageNumber, $pageSize);
        return $ret;
    }

    //Page: Bank
    public function showBankFlowPageOp()
    {
        $uid = $_GET['uid'];
        $m_site_bank = new site_bankModel();
        $ret = $m_site_bank->getBankInfoById($uid);
        Tpl::output('info', $ret);
        Tpl::showpage('bank.flow');
    }

    /**
     * Data: Bank Flow
     * @param $p
     * @return array
     */
    public function getBankFlowListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getAssetPassbookFlow($p['pid'], $pageNumber, $pageSize);
        return $ret;
    }

    public function incomeStatementOp()
    {
        //$ret = incomeStatementClass::getReportData();
        //Tpl::output('data', $ret->DATA);
        Tpl::showpage('income.statement');
    }

    public function getIncomeStatementListOp($p)
    {
        $model_passbook = new passbookModel();
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search['account_name'] = $p['account_name'];
        $search['d1'] = $p['date_start'];
        $search['d2'] = dateAdd($p['date_end'], 1);
        $ret = $model_passbook->getIncomeStatementList($search, $pageNumber, $pageSize);
        return $ret;
    }

    public function showIncomeStatementFlowPageOp()
    {
        $time = $_GET['time'];
        $model_passbook = new passbookModel();
        $ret = $model_passbook->getIncomeStatementTotal($time);
        Tpl::output('info', $ret);
        Tpl::showpage('income.statement.flow');
    }

    public function getIncomeStatementFlowListOp($p)
    {
        $model_passbook = new passbookModel();
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $currency = $p['currency'];
        $ret = $model_passbook->getIncomeStatementFlowList($p['time'], $currency, $pageNumber, $pageSize);
        return $ret;
    }

    /**
     * Loan list
     */
    public function loanOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        Tpl::showPage('loan');
    }

    /**
     * 获取贷款列表
     * @param $p
     * @return array
     */
    public function getLoanListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = trim($p['search_text']);
        $currency = trim($p['currency']);
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];

        $filters = array(
            'search_text' => $search_text,
            'currency' => $currency,
            'date_start' => $date_start,
            'date_end' => $date_end,
        );
        $data = loanReportClass::getLoanList($pageNumber, $pageSize, $filters);
        return $data;
    }

    /**
     * 贷款情况统计
     */
    public function loanStatusOp()
    {
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output('currency_list', $currency_list);

        $loan_contract_state = (new loanContractStateEnum())->Dictionary();
        Tpl::output('loan_contract_state', $loan_contract_state);

        $data = loanReportClass::getLoanSummary();
        Tpl::output('data', $data);
        Tpl::showPage('loan.status');
    }

    public function getLoanStatusListOp()
    {

    }

    public function interestRateOp()
    {
        Tpl::showPage('loan.interest.rate');
    }

    public function getLoanInterestRateListOp()
    {

    }

    public function loanSizeOp()
    {
        Tpl::showPage('loan.size');
    }

    public function getLoanSizeListOp()
    {

    }

    public function investmentRatioOp()
    {
        Tpl::showPage('loan.investment.ratio');
    }

    public function getLoanInvestmentRatioListOp()
    {

    }

    public function repaymentOp()
    {
        Tpl::showPage('repayment');
    }

    public function getRepaymentListOp()
    {

    }

    public function agingOfLoanArrearOp()
    {
        Tpl::showPage('repayment.aging.of.loan.arrear');
    }

    public function getAgingOfLoanArrearListOp()
    {

    }

    public function loanInFallingDueOp()
    {
        Tpl::showPage('repayment.loan.in.falling.due');
    }

    public function getLoanInFallingDueListOp()
    {

    }

    public function disbursementOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        Tpl::showPage('disbursement');
    }

    public function getDisbursementListOp($p){
        return array();
    }

    public function getLoanProvisionListOp()
    {

    }


}
