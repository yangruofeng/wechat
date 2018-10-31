<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2016/8/30
 * Time: 17:09
 */
class authEnum extends Enum
{
    /* back office权限设置开始*/
    const AUTH_HOME_MONITOR = "monitor_monitor";

    const AUTH_USER_BRANCH = "user_branch";
    const AUTH_USER_ROLE = "user_role";
    const AUTH_USER_USER = "user_user";
    const AUTH_USER_STAFF = "user_staff";
    const AUTH_USER_COMMITTEE = "user_committee";
    const AUTH_USER_BIND_CARD = "user_bindCard";
    const AUTH_SETTING_COMPANY_INFO = "setting_companyInfo";
    const AUTH_REGION_LIST = "region_list";
    const AUTH_USER_LOG = "user_log";

    const AUTH_CLIENT_CLIENT = "client_client";
    const AUTH_CLIENT_CERIFICATION = "client_cerification";
    const AUTH_CLIENT_BLACK_LIST = "client_blackList";
    const AUTH_CLIENT_GRADE = "client_grade";

    const AUTH_LOAN_COMMITTEE_APPROVE_CREDIT_APPLICATION = "loan_committee_approveCreditApplication";
    const AUTH_LOAN_COMMITTEE_USER_VOTE = "loan_committee_userVote";
//    const AUTH_LOAN_COMMITTEE_FAST_GRANT_CREDIT = "loan_committee_fastGrantCredit";
//    const AUTH_LOAN_COMMITTEE_CUT_CREDIT = "loan_committee_cutCredit";
    const AUTH_LOAN_COMMITTEE_GRANT_CREDIT_HISTORY = "loan_committee_grantCreditHistory";
    const AUTH_LOAN_COMMITTEE_APPROVE_PREPAYMENT_REQUEST = "loan_committee_approvePrepaymentRequest";
    const AUTH_LOAN_COMMITTEE_APPROVE_PENALTY_REQUEST = "loan_committee_approvePenaltyRequest";
    const AUTH_LOAN_COMMITTEE_APPROVE_WRITTEN_OFF_REQUEST = "loan_committee_approveWrittenOffRequest";
    const AUTH_LOAN_COMMITTEE_APPROVE_WITHDRAW_MORTGAGE_REQUEST = "loan_committee_approveWithdrawMortgageRequest";

    const AUTH_LOAN_PRODUCT = "loan_product";
    const AUTH_LOAN_INTEREST_PACKAGE="loan_productPackagePage";
    const AUTH_LOAN_SETTING_CREDIT_CATEGORY="loan_category";
    const AUTH_LOAN_SETTING_FEE="loan_loanFeeSetting";
    const AUTH_LOAN_PREPAYMENT_LIMIT = "loan_prepaymentLimit";
    const AUTH_SETTING_CREDIT_LEVEL = 'setting_creditLevel';
    const AUTH_SETTING_INDUSTRY = "setting_industry";
    const AUTH_SETTING_INDUSTRY_PLACE = "setting_industry_place";
    const AUTH_SETTING_ASSET_SURVEY = "setting_assetSurvey";

    const AUTH_SAVINGS_CATEGORY = "savings_category";
    const AUTH_SAVINGS_PRODUCT = "savings_product";

    const AUTH_FINANCIAL_HQ_VAULT = "financial_hqVault";
    const AUTH_PARTNER_BANK = "partner_bank";
    const AUTH_FINANCIAL_HQ_BANK = "financial_hqBank";
    const AUTH_FINANCIAL_BRANCH_BANK = "financial_branchBank";
    const AUTH_FINANCIAL_EXCHANGE_RATE = "financial_exchangeRate";
    const AUTH_FINANCIAL_CHECK_BILLPAY = "financial_checkBillPay";
    const AUTH_TREASURE_BRANCH_LIST = "treasure_branchList";
    const AUTH_TREASURE_SETTING_CIV_EXTRA_TYPE = "treasure_settingCIVExtraType";

    const AUTH_DATA_CENTER_BRANCH_INDEX = "data_center_branch_index";
    const AUTH_DATA_CENTER_STAFF_INDEX = "data_center_staff_index";
    const AUTH_DATA_CENTER_PARTNER_INDEX = "data_center_partner_index";
    const AUTH_DATA_CENTER_BANK_INDEX = "data_center_bank_index";
    const AUTH_DATA_CENTER_MEMBER_INDEX = "data_center_member_index";
    const AUTH_DATA_CENTER_CERTIFICATION_INDEX = "data_center_certification_index";
    const AUTH_DATA_CENTER_BUSINESS_INDEX = "data_center_business_index";
    const AUTH_DATA_CENTER_DAILY_INDEX="data_center_daily_index";

    const AUTH_REPORT_LOAN = "report_loan_loan";
    const AUTH_REPORT_LOAN_ANALYSIS="report_loan_analysis";
    const AUTH_REPORT_LOAN_SUPER="report_loan_super";

    const AUTH_REPORT_REPAYMENT = "report_repayment_repayment";
    const AUTH_REPORT_DISBURSEMENT = "report_disbursement_disbursement";
    const AUTH_REPORT_OUTSTANDING = "report_outstanding";
    const AUTH_REPORT_CUSTOMER = "report_customer_customer";
//    const AUTH_REPORT_CROSS_APPLICATION = "report_cross_application_crossApplication";
    const AUTH_REPORT_BALANCE_SHEET = "report_balanceSheet";
    const AUTH_REPORT_INCOME_STATEMENT = "report_incomeStatement";
    const AUTH_REPORT_JOURNAL_VOUCHER = "report_journal_voucher";
    const AUTH_REPORT_RECEIVABLE_INTEREST = "report_receivable_interest";

    const AUTH_POINT_EVENT = "point_event";
    const AUTH_POINT_POINT_RECORD = "point_pointRecord";
    const AUTH_POINT_USER_POINT = "point_userPoint";

    const AUTH_TOOLS_CALCULATOR = "tools_calculator";

    /* back office权限设置结束*/

    /*counter权限设置开始*/
    const AUTH_MEMBER_REGISTER = "member_register";
    const AUTH_MEMBER_MY_CLIENT = "member_my_client";
    const AUTH_MEMBER_DOCUMENT_COLLECTION = "member_documentCollection";
    const AUTH_MEMBER_FINGERPRINT_COLLECTION = "member_fingerprintCollection";
    const AUTH_MEMBER_AUTHORIZED_CONTRACT = "member_authorizedContract";
    const AUTH_MEMBER_LOAN = "member_loan";
    const AUTH_MEMBER_REPAYMENT = "member_repayment";
    const AUTH_MEMBER_DEPOSIT = "member_deposit";
    const AUTH_MEMBER_WITHDRAWAL = "member_withdrawal";
    const AUTH_MEMBER_PENALTY = "member_penalty";
    const AUTH_MEMBER_PROFILE = "member_profile";

    const AUTH_COMPANY_INDEX = "company_index";

    const AUTH_SERVICE_LOAN_CONSULT = "service_loanConsult";
    const AUTH_SERVICE_MY_CONSULTATION = "service_my_consultation";
    const AUTH_SERVICE_CURRENCY_EXCHANGE = "service_currencyExchange";

    const AUTH_MORTGAGE_INDEX = "mortgage_index";
    const AUTH_COUNTER_MORTGAGE_MY_STORAGE = "mortgage_myStoragePage";
    const AUTH_COUNTER_MORTGAGE_RECEIVE_TRANSFER = "mortgage_pendingReceiveFromTransfer";
    const AUTH_COUNTER_MORTGAGE_RECEIVE_CLIENT = "mortgage_pendingReceiveFromClient";
    const AUTH_COUNTER_MORTGAGE_REQUEST_WITHDRAW = "mortgage_pendingWithdrawByRequest";


    const AUTH_CASH_ON_HAND_TRANSACTIONS = "cash_on_hand_transactions";
    const AUTH_CASH_ON_HAND_PENDING_RECEIVE = "cash_on_hand_pendingReceive";
    const AUTH_CASH_ON_HAND_TRANSFER_TO_VAULT = "cash_on_hand_transferToVault";
    const AUTH_CASH_ON_HAND_CASH_IN = "cash_on_hand_cashIn";
    const AUTH_CASH_ON_HAND_CASH_OUT = "cash_on_hand_cashOut";

    const AUTH_CASH_IN_VAULT_TRANSACTIONS = "cash_in_vault_transactions";
    const AUTH_CASH_IN_VAULT_BANK = "cash_in_vault_bank";
    const AUTH_CASH_IN_VAULT_PENDINGRECEIVE = "cash_in_vault_pendingReceive";
    const AUTH_CASH_IN_VAULT_TRANSFERTOCASHIER = "cash_in_vault_transferToCashier";
    const AUTH_CASH_IN_VAULT_CASHIER = "cash_in_vault_cashier";
    const AUTH_CASH_IN_VAULT_CASH_IN = "cash_in_vault_cashIn";
    const AUTH_CASH_IN_VAULT_CASH_OUT = "cash_in_vault_cashOut";
    /*counter权限设置结束*/
}

interface IauthGroup
{
    function getGroupKey();

    function getGroupName();

    function getAuthList();
}

class authGroup_home implements IauthGroup
{
    function getGroupKey()
    {
        return "home";//menu的key值
    }

    function getGroupName()
    {
        return "home";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_HOME_MONITOR
        );
    }
}

class authGroup_user implements IauthGroup
{
    function getGroupKey()
    {
        return "user";//menu的key值
    }

    function getGroupName()
    {
        return "user";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_USER_BRANCH,
            authEnum::AUTH_USER_ROLE,
            authEnum::AUTH_USER_USER,
            authEnum::AUTH_USER_STAFF,
            authEnum::AUTH_USER_COMMITTEE,
            authEnum::AUTH_USER_BIND_CARD,
            authEnum::AUTH_SETTING_COMPANY_INFO,
            authEnum::AUTH_REGION_LIST,
            authEnum::AUTH_USER_LOG,
        );
    }
}

class authGroup_client implements IauthGroup
{
    function getGroupKey()
    {
        return "client";//menu的key值
    }

    function getGroupName()
    {
        return "client";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_CLIENT_CLIENT,
            authEnum::AUTH_CLIENT_CERIFICATION,
            authEnum::AUTH_CLIENT_BLACK_LIST,
            authEnum::AUTH_CLIENT_GRADE,
        );
    }
}

class authGroup_loan_committee implements IauthGroup
{
    function getGroupKey()
    {
        return "loan_committee";//menu的key值
    }

    function getGroupName()
    {
        return "loan_committee";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_LOAN_COMMITTEE_APPROVE_CREDIT_APPLICATION,
            authEnum::AUTH_LOAN_COMMITTEE_USER_VOTE,
//            authEnum::AUTH_LOAN_COMMITTEE_FAST_GRANT_CREDIT,
//            authEnum::AUTH_LOAN_COMMITTEE_CUT_CREDIT,
            authEnum::AUTH_LOAN_COMMITTEE_GRANT_CREDIT_HISTORY,
            authEnum::AUTH_LOAN_COMMITTEE_APPROVE_PREPAYMENT_REQUEST,
            authEnum::AUTH_LOAN_COMMITTEE_APPROVE_PENALTY_REQUEST,
            authEnum::AUTH_LOAN_COMMITTEE_APPROVE_WRITTEN_OFF_REQUEST,
            authEnum::AUTH_LOAN_COMMITTEE_APPROVE_WITHDRAW_MORTGAGE_REQUEST
        );
    }
}

class authGroup_loan implements IauthGroup
{
    function getGroupKey()
    {
        return "loan";//menu的key值
    }

    function getGroupName()
    {
        return "loan";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_LOAN_PRODUCT,
            authEnum::AUTH_LOAN_INTEREST_PACKAGE,
            authEnum::AUTH_LOAN_SETTING_CREDIT_CATEGORY,
            authEnum::AUTH_LOAN_SETTING_FEE,
            authEnum::AUTH_LOAN_PREPAYMENT_LIMIT,
            authEnum::AUTH_SETTING_CREDIT_LEVEL,
            authEnum::AUTH_SETTING_INDUSTRY,
            authEnum::AUTH_SETTING_INDUSTRY_PLACE,
            authEnum::AUTH_SETTING_ASSET_SURVEY,
        );
    }
}

class authGroup_savings implements IauthGroup
{
    function getGroupKey()
    {
        return "savings";//menu的key值
    }

    function getGroupName()
    {
        return "savings";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_SAVINGS_CATEGORY,
            authEnum::AUTH_SAVINGS_PRODUCT,
        );
    }
}

class authGroup_financial implements IauthGroup
{
    function getGroupKey()
    {
        return "financial";//menu的key值
    }

    function getGroupName()
    {
        return "financial";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_FINANCIAL_HQ_VAULT,
            authEnum::AUTH_PARTNER_BANK,
            authEnum::AUTH_FINANCIAL_HQ_BANK,
            authEnum::AUTH_FINANCIAL_BRANCH_BANK,
            authEnum::AUTH_FINANCIAL_EXCHANGE_RATE,
            authEnum::AUTH_FINANCIAL_CHECK_BILLPAY,
            authEnum::AUTH_TREASURE_BRANCH_LIST,
            authEnum::AUTH_TREASURE_SETTING_CIV_EXTRA_TYPE,
        );
    }
}

class authGroup_data_center implements IauthGroup
{
    function getGroupKey()
    {
        return "data_center";//menu的key值
    }

    function getGroupName()
    {
        return "data_center";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_DATA_CENTER_BRANCH_INDEX,
            authEnum::AUTH_DATA_CENTER_STAFF_INDEX,
            authEnum::AUTH_DATA_CENTER_PARTNER_INDEX,
            authEnum::AUTH_DATA_CENTER_BANK_INDEX,
            authEnum::AUTH_DATA_CENTER_MEMBER_INDEX,
            authEnum::AUTH_DATA_CENTER_CERTIFICATION_INDEX,
            authEnum::AUTH_DATA_CENTER_BUSINESS_INDEX,
            authEnum::AUTH_DATA_CENTER_DAILY_INDEX
        );
    }
}


class authGroup_report implements IauthGroup
{
    function getGroupKey()
    {
        return "report";//menu的key值
    }

    function getGroupName()
    {
        return "report";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_REPORT_LOAN,
            authEnum::AUTH_REPORT_LOAN_ANALYSIS,
            authEnum::AUTH_REPORT_LOAN_SUPER,
            authEnum::AUTH_REPORT_REPAYMENT,
            authEnum::AUTH_REPORT_DISBURSEMENT,
            authEnum::AUTH_REPORT_OUTSTANDING,
            authEnum::AUTH_REPORT_CUSTOMER,
//            authEnum::AUTH_REPORT_CROSS_APPLICATION,
            authEnum::AUTH_REPORT_BALANCE_SHEET,
            authEnum::AUTH_REPORT_INCOME_STATEMENT,
            authEnum::AUTH_REPORT_JOURNAL_VOUCHER,
            authEnum::AUTH_REPORT_RECEIVABLE_INTEREST,

        );
    }
}

class authGroup_tools implements IauthGroup
{
    function getGroupKey()
    {
        return "tools";//menu的key值
    }

    function getGroupName()
    {
        return "tools";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_TOOLS_CALCULATOR,
        );
    }
}

/*counter 开始*/

class authGroup_counter_member implements IauthGroup
{
    function getGroupKey()
    {
        return "member";//menu的key值
    }

    function getGroupName()
    {
        return "member";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_MEMBER_REGISTER,
            authEnum::AUTH_MEMBER_MY_CLIENT,
            authEnum::AUTH_MEMBER_DOCUMENT_COLLECTION,
            authEnum::AUTH_MEMBER_FINGERPRINT_COLLECTION,
            authEnum::AUTH_MEMBER_AUTHORIZED_CONTRACT,
            authEnum::AUTH_MEMBER_LOAN,
            authEnum::AUTH_MEMBER_REPAYMENT,
            authEnum::AUTH_MEMBER_DEPOSIT,
            authEnum::AUTH_MEMBER_WITHDRAWAL,
            authEnum::AUTH_MEMBER_PENALTY,
            authEnum::AUTH_MEMBER_PROFILE,
        );
    }
}

class authGroup_counter_company implements IauthGroup
{
    function getGroupKey()
    {
        return "company";//menu的key值
    }

    function getGroupName()
    {
        return "company";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_COMPANY_INDEX,
        );
    }
}

class authGroup_counter_service implements IauthGroup
{
    function getGroupKey()
    {
        return "service";//menu的key值
    }

    function getGroupName()
    {
        return "service";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_SERVICE_LOAN_CONSULT,
            authEnum::AUTH_SERVICE_MY_CONSULTATION,
            authEnum::AUTH_SERVICE_CURRENCY_EXCHANGE,
        );
    }
}

class authGroup_counter_mortgage implements IauthGroup
{
    function getGroupKey()
    {
        return "mortgage";//menu的key值
    }

    function getGroupName()
    {
        return "mortgage";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_COUNTER_MORTGAGE_MY_STORAGE,
            authEnum::AUTH_COUNTER_MORTGAGE_RECEIVE_TRANSFER,
            authEnum::AUTH_COUNTER_MORTGAGE_RECEIVE_CLIENT,
            authEnum::AUTH_COUNTER_MORTGAGE_REQUEST_WITHDRAW
        );
    }
}

class authGroup_counter_cash_on_hand implements IauthGroup
{
    function getGroupKey()
    {
        return "cash_on_hand";//menu的key值
    }

    function getGroupName()
    {
        return "cash_on_hand";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_CASH_ON_HAND_TRANSACTIONS,
            authEnum::AUTH_CASH_ON_HAND_PENDING_RECEIVE,
            authEnum::AUTH_CASH_ON_HAND_TRANSFER_TO_VAULT,
            authEnum::AUTH_CASH_ON_HAND_CASH_IN,
            authEnum::AUTH_CASH_ON_HAND_CASH_OUT,
        );
    }
}

class authGroup_counter_cash_in_vault implements IauthGroup
{
    function getGroupKey()
    {
        return "cash_in_vault";//menu的key值
    }

    function getGroupName()
    {
        return "cash_in_vault";//取语言包
    }

    function getAuthList()
    {
        return array(
            authEnum::AUTH_CASH_IN_VAULT_TRANSACTIONS,
            authEnum::AUTH_CASH_IN_VAULT_BANK,
            authEnum::AUTH_CASH_IN_VAULT_PENDINGRECEIVE,
            authEnum::AUTH_CASH_IN_VAULT_TRANSFERTOCASHIER,
            authEnum::AUTH_CASH_IN_VAULT_CASHIER,
            authEnum::AUTH_CASH_IN_VAULT_CASH_IN,
            authEnum::AUTH_CASH_IN_VAULT_CASH_OUT
        );
    }
}

/*counter 结束*/
