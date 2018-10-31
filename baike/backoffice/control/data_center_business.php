<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/17/2018
 * Time: 11:26 AM
 */
class data_center_businessControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('operator');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Dev");
        Tpl::setDir("data_center_business");
    }

    public function indexOp()
    {
        Tpl::showPage("index");
    }

    public function creditOverviewOp()
    {
        $credit_group = businessDataClass::getCreditGroupCategory();
        $pending_credit = businessDataClass::getPendingCredit();
        Tpl::output("credit_group", $credit_group);
        Tpl::output("pending_credit", $pending_credit);
        Tpl::showPage("credit/overview");
    }

    public function creditTop10Op()
    {
        $credit_top = businessDataClass::creditTop10();
        Tpl::output("credit_top", $credit_top);
        Tpl::showPage("credit/top.list");
    }

    public function creditAgreementOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage("credit.agreement.index");
    }

    public function getCreditAgreementListOp($p)
    {
        $pageNumber = $p['pageNumber'] ?: 1;
        $pageSize = $p['pageSize'] ?: 50;
        $filter = array(
            'obj_guid' => $p['obj_guid'],
            'member_name' => $p['member_name']
        );
        return businessDataClass::creditAgreement($pageNumber, $pageSize, $filter);
    }

    public function creditAgreementDetailOp()
    {
        $uid = $_GET['uid'];
        $detail = businessDataClass::creditAgreementDetail($uid);
        Tpl::output("detail", $detail);
        Tpl::showPage("credit.agreement.detail");
    }

    public function creditLogOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage("credit.log.index");
    }

    public function getCreditLogListOp($p)
    {
        $pageNumber = $p['pageNumber'] ?: 1;
        $pageSize = $p['pageSize'] ?: 50;
        $filter = array(
            'obj_guid' => $p['obj_guid'],
            'member_name' => $p['member_name']
        );
        return businessDataClass::creditLog($pageNumber, $pageSize, $filter);
    }

    /**
     * 贷款统计
     */
    public function loanOverviewOp()
    {
        $data = businessDataClass::getLoanOverview();
        Tpl::output("data", $data);
        Tpl::showPage("loan.overview");
    }

    public function loanTop10Op()
    {
        $loan_top = businessDataClass::loanTop10();
        Tpl::output("loan_top", $loan_top);
        Tpl::showPage("loan.top.list");
    }

    public function loanContractOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage("loan.contract.index");
    }

    public function getLoanContractListOp($p)
    {
        $pageNumber = $p['pageNumber'] ?: 1;
        $pageSize = $p['pageSize'] ?: 50;
        $filter = array(
            'obj_guid' => $p['obj_guid'],
            'member_name' => $p['member_name']
        );
        return businessDataClass::getLoanContract($pageNumber, $pageSize, $filter);
    }

    public function loanOverdueOp()
    {
        Tpl::showPage('loan.overdue');
    }

    /**
     * @param $p
     * @return array
     */
    public function getLoanOverdueListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        return businessDataClass::getLoanOverdueList($pageNumber, $pageSize);
    }

    public function loanPenaltyOp()
    {
        Tpl::showPage("loan.penalty");
    }

    public function getLoanPenaltyOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 50;
        return businessDataClass::getLoanPenalty($pageNumber, $pageSize);
    }

    public function loanRepayOp()
    {
        Tpl::showPage("loan.repay");
    }

    public function getLoanRepayOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 50;
        return businessDataClass::getLoanRepay($pageNumber, $pageSize);
    }

    public function deposit_overviewOp()
    {
        $data = businessDataClass::getDepositOverview();
        Tpl::output("data", $data);
        Tpl::showPage("deposit.overview");
    }

    public function deposit_logOp()
    {
        $bizCode = (new bizCodeEnum())->toArray();
        Tpl::output("bizCode", $bizCode);
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage("deposit.log.index");
    }

    public function getDepositLogListOp($p)
    {
        $pageNumber = $p['pageNumber'] ?: 1;
        $pageSize = $p['pageSize'] ?: 50;
        $filter = array(
            'code' => $p['code'],
            'currency' => $p['currency']
        );
        $data = businessDataClass::getDepositLog($pageNumber, $pageSize, $filter);
        return $data;
    }

    public function withdraw_overviewOp()
    {
        $data = businessDataClass::getWithdrawOverview();
        Tpl::output("data", $data);
        Tpl::showPage("withdraw.overview");
    }

    public function withdraw_logOp()
    {
        $bizCode = (new bizCodeEnum())->toArray();
        Tpl::output("bizCode", $bizCode);
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage("withdraw.log.index");
    }

    public function getWithdrawLogListOp($p)
    {
        $pageNumber = $p['pageNumber'] ?: 1;
        $pageSize = $p['pageSize'] ?: 50;
        $filter = array(
            'code' => $p['code'],
            'currency' => $p['currency']
        );
        $data = businessDataClass::getWithdrawLog($pageNumber, $pageSize, $filter);
        return $data;
    }

    public function transfer_overviewOp()
    {
        $member_data = businessDataClass::getMemberTransferOverview();
        Tpl::output("member_data", $member_data);
        $obj_data = businessDataClass::getObjTransferOverview();
        Tpl::output("obj_data", $obj_data);
        Tpl::showPage("transfer.overview");
    }

    public function transfer_logOp()
    {
        $bizCode = (new bizCodeEnum())->toArray();
        Tpl::output("bizCode", $bizCode);
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage("transfer.log.index");
    }

    public function getTransferLogListOp($p)
    {
        $pageNumber = $p['pageNumber'] ?: 1;
        $pageSize = $p['pageSize'] ?: 50;
        $type = $p['type'];
        $filter = array(
            'code' => $p['code'],
            'currency' => $p['currency']
        );
        $data = businessDataClass::getTransferLog($type, $pageNumber, $pageSize, $filter);
        return $data;
    }

    public function exchange_overviewOp()
    {
        $data = businessDataClass::getExchangeOverview();
        Tpl::output("data", $data);
        Tpl::showPage("exchange.overview");
    }

    public function exchange_logOp()
    {
        $type = (new objGuidTypeEnum())->toArray();
        Tpl::output("type", $type);
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage("exchange.log.index");
    }

    public function getExchangeLogListOp($p)
    {
        $pageNumber = $p['pageNumber'] ?: 1;
        $pageSize = $p['pageSize'] ?: 50;
        $filter = array(
            'obj_type' => $p['obj_type']
        );
        $data = businessDataClass::getExchangeLog($pageNumber, $pageSize, $filter);
        return $data;
    }

    public function savingsTop100Op()
    {
        $currency = $_GET['currency'] ?: currencyEnum::USD;
        $list = businessDataClass::getSavingsUsdTop100($currency);
        Tpl::output("list", $list);
        Tpl::output("currency", $currency);
        Tpl::showPage("savings.top.list");
    }

}