<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/21
 * Time: 16:49
 */
class enum_langClass{

    public function __construct()
    {
        Language::read('define');
    }
    public static function getLangType(){
        $a = $GLOBALS['config']["lang_type_list"];
        return $a;
    }
    public static function getWorkTypeEnumLang(){
        return array(
            //workTypeEnum::FREE=>L("work_type_free"),
            workTypeEnum::EXTERNAL_STAFF=>L("work_type_external_staff"),
            workTypeEnum::STAFF=>L("work_type_internal_staff"),
            workTypeEnum::GOVERNMENT=>L("work_type_government"),
            workTypeEnum::BUSINESS => L('work_type_business'),
            workTypeEnum::HOUSE_WIFE=>L('work_type_house_wife')
        );
    }

    public static function getCertificationTypeEnumLang()
    {
        return array(
            certificationTypeEnum::ID => L('certification_id'),
            certificationTypeEnum::PASSPORT => L('certification_passport'),
            certificationTypeEnum::FAIMILYBOOK => L('certification_family_book'),
            certificationTypeEnum::RESIDENT_BOOK => L('certification_resident_book'),
            certificationTypeEnum::BIRTH_CERTIFICATE => L('certification_birthday'),
            certificationTypeEnum::GUARANTEE_RELATIONSHIP => 'Guarantee Relationship',
//            certificationTypeEnum::WORK_CERTIFICATION => L('certification_work'),
            certificationTypeEnum::CAR => L('certification_car_asset'),
            certificationTypeEnum::HOUSE => L('certification_house_asset'),
            certificationTypeEnum::STORE => L('certification_store_asset'),
            certificationTypeEnum::LAND => L('certification_land_asset'),
            certificationTypeEnum::MOTORBIKE => L('certification_motorbike'),
            certificationTypeEnum::DEGREE=>L('certification_degree')
        );
    }



    public static function getPaymentTypeLang()
    {
        return array(
            memberAccountHandlerTypeEnum::CASH => 'Cash',
            memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY => 'Asiaweiluy',
            memberAccountHandlerTypeEnum::BANK => 'Bank transfer',
            memberAccountHandlerTypeEnum::PARTNER_LOAN => 'Loan',
            memberAccountHandlerTypeEnum::PASSBOOK => 'Balance'
        );
    }


    public static function getLoanApplySourceLang()
    {
        return array(
            loanApplySourceEnum::MEMBER_APP => 'Member App',
            loanApplySourceEnum::OPERATOR_APP => 'Operator App',
            loanApplySourceEnum::PHONE => 'Phone',
            loanApplySourceEnum::FACEBOOK => 'Facebook',
            loanApplySourceEnum::CLIENT => 'Client'
         );
    }

    public static function getLoanApplyStateLang()
    {
        return array(
            loanApplyStateEnum::LOCKED => 'Handling,locked',
            loanApplyStateEnum::CREATE => 'New Apply',
            loanApplyStateEnum::OPERATOR_REJECT => 'Operator Reject',
            loanApplyStateEnum::ALLOT_CO => 'Allot to CO',
            loanApplyStateEnum::CO_HANDING => 'CO handling',
            loanApplyStateEnum::CO_CANCEL => 'CO canceled',
            loanApplyStateEnum::CO_APPROVED => 'CO approved',
            loanApplyStateEnum::BM_APPROVED => 'BM approved',
            loanApplyStateEnum::BM_CANCEL => 'BM canceled',
            loanApplyStateEnum::HQ_APPROVED => 'HQ approved',
            loanApplyStateEnum::HQ_CANCEL => 'HQ canceled',
            loanApplyStateEnum::ALL_APPROVED_CANCEL => 'Client cancel',
            loanApplyStateEnum::DONE => 'Completely done'
        );
    }


    public static function getLoanInstallmentTypeLang()
    {
        return array(
            interestPaymentEnum::SINGLE_REPAYMENT => 'Single Repayment',
            interestPaymentEnum::ADVANCE_SINGLE_REPAYMENT => 'Advance Single Repayment',
            interestPaymentEnum::ANYTIME_SINGLE_REPAYMENT => 'Anytime Single Repayment',
            interestPaymentEnum::ANNUITY_SCHEME => 'Annuity Schema',
            interestPaymentEnum::BALLOON_INTEREST => 'Balloon Interest',
            interestPaymentEnum::FIXED_PRINCIPAL => 'Fixed Principal',
            interestPaymentEnum::FLAT_INTEREST => 'Flat Interest',
            interestPaymentEnum::SEMI_BALLOON_INTEREST => 'Semi Balloon',
            interestPaymentEnum::ADVANCE_FIX_REPAYMENT_DATE => 'Fix Repayment Date',
            interestPaymentEnum::ANYTIME_ANNUITY => 'Anytime Annuity',
        );
    }

    public static function getLoanRepaymentPeriodLang()
    {
        return array(
            interestRatePeriodEnum::WEEKLY => L('enum_interest_rate_period_weekly'),
            interestRatePeriodEnum::YEARLY => L('enum_interest_rate_period_yearly'),
            interestRatePeriodEnum::MONTHLY => L('enum_interest_rate_period_monthly'),
            interestRatePeriodEnum::DAILY => L('enum_interest_rate_period_daily'),
            interestRatePeriodEnum::QUARTER => L('enum_interest_rate_period_quarterly'),
            interestRatePeriodEnum::SEMI_YEARLY => L('enum_interest_rate_period_semi_yearly'),
        );
    }



    public static function getLoanTimeUnitLang()
    {
        return array(
            loanPeriodUnitEnum::YEAR => L('enum_loan_unit_year'),
            loanPeriodUnitEnum::MONTH => L('enum_loan_unit_month'),
            loanPeriodUnitEnum::DAY => L('enum_loan_unit_day'),
        );
    }

    public static function getMemberStateLang(){

        return array(
            memberStateEnum::CANCEL => 'Cancel',
            memberStateEnum::CREATE => 'Create',
            memberStateEnum::CHECKED => 'Checked',
            memberStateEnum::TEMP_LOCKING => 'Temp Locking',
            memberStateEnum::SYSTEM_LOCKING => 'System Locking',
            memberStateEnum::VERIFIED => 'Verified',
        );
    }


    public static function getPassbookTradingTypeLang()
    {
        return global_settingClass::getAllTradingType();
    }

    public static function getMemberTradingTypeLang()
    {
        return global_settingClass::getMemberTradingType();
    }


    public static function getComplaintAdviceStateLang(){

        return array(
            complaintAdviceEnum::CREATE => 'Create',
            complaintAdviceEnum::HANDLE => 'Handle',
            complaintAdviceEnum::CHECKED => 'Checked',
        );
    }

    public static function getPassbookStateLang(){

        return array(
            passbookStateEnum::CANCEL => 'cancel',
            passbookStateEnum::ACTIVE => 'active',
            passbookStateEnum::FREEZE => 'freeze',
        );
    }

    public static function getPassbookAccountFlowStateLang(){

        return array(
            passbookAccountFlowStateEnum::CANCELLED => 'Cancelled',
            passbookAccountFlowStateEnum::CREATE => 'Create',
            passbookAccountFlowStateEnum::OUTSTANDING => 'Outstanding',
            passbookAccountFlowStateEnum::DONE => 'Done',
        );
    }


    public static function getLoanProductCategoryLang()
    {
        return array(
            loanProductCategoryEnum::CREDIT_LOAN => 'Credit Loan',
        );
    }

    public static function getLoanPenaltyReceiptStateLang(){
        return array(
            loanPenaltyReceiptStateEnum::CREATE => 'create',
            loanPenaltyReceiptStateEnum::APPROVED => 'approved',
            loanPenaltyReceiptStateEnum::REJECTED => 'rejected',
            loanPenaltyReceiptStateEnum::COMPLETE => 'complete',
        );
    }

    public static function getLoanPenaltyHandlerStateLang(){
        return array(
            loanPenaltyHandlerStateEnum::CREATE => 'create',
            loanPenaltyHandlerStateEnum::APPLY_REDUCE => 'apply reduce',
            loanPenaltyHandlerStateEnum::DONE => 'done',
        );
    }


    public static function getCounterBizLang()
    {
        return array(
            bizCodeEnum::MEMBER_WITHDRAW_TO_CASH => 'Member Withdraw To Cash',
            bizCodeEnum::MEMBER_DEPOSIT_BY_CASH => 'Member Deposit By Cash',
            bizCodeEnum::MEMBER_LOAN_REPAYMENT_BY_CASH => 'Member Loan Repayment',
            bizCodeEnum::MEMBER_PREPAYMENT => 'Member Loan Prepayment',
            bizCodeEnum::MEMBER_CREATE_LOAN_CONTRACT => 'Member Loan',
            bizCodeEnum::RECEIVE_LOAN_PENALTY_BY_COUNTER => 'Member Loan Penalty Repayment',
        );
    }


    public static function getBizStateLang()
    {
        return array(
            bizStateEnum::CREATE => 'New',
            bizStateEnum::CANCEL => 'Cancel',
            bizStateEnum::FAIL => 'Fail',
            bizStateEnum::DONE => 'Done',
            bizStateEnum::REJECT => 'Reject',
            bizStateEnum::PENDING_APPROVE => 'Pending Approve',
        );
    }


    public static function getCommonApproveStateLang()
    {
        return array(
            commonApproveStateEnum::CANCEL => 'Cancel',
            commonApproveStateEnum::CREATE => 'New',
            commonApproveStateEnum::APPROVING => 'Approving',
            commonApproveStateEnum::REJECT => 'Reject',
            commonApproveStateEnum::PASS => 'Pass',
        );
    }

    public static function getGenderLang()
    {
        return array(
            memberGenderEnum::MALE => L('enum_gender_male'),
            memberGenderEnum::FEMALE => L('enum_gender_female')
         );
    }

    public static function getAssetsType($type = 0)
    {
        if ($type == 1) {
            return array(
                certificationTypeEnum::LAND => "land",
                certificationTypeEnum::HOUSE => 'house',
                certificationTypeEnum::STORE => 'store',
                certificationTypeEnum::CAR => "car",
                certificationTypeEnum::MOTORBIKE => 'motorbike',
                certificationTypeEnum::DEGREE=>"degree"
            );
        } else {
            return array(
                certificationTypeEnum::CAR => L('certification_car_asset'),
                certificationTypeEnum::HOUSE => L('certification_house_asset'),
                certificationTypeEnum::STORE => L('certification_store_asset'),
                certificationTypeEnum::LAND => L('certification_land_asset'),
                certificationTypeEnum::MOTORBIKE => L('certification_motorbike'),
                certificationTypeEnum::DEGREE=>L('certification_degree')
            );
        }
    }

    public static function getModuleBusinessLang()
    {
        return array(
            moduleBusinessEnum::MODULE_DEPOSIT => 'Deposit',
            moduleBusinessEnum::MODULE_WITHDRAW => 'Withdraw',
            moduleBusinessEnum::MODULE_EXCHANGE => 'Exchange',
            moduleBusinessEnum::MODULE_CREDIT => 'Credit',
            moduleBusinessEnum::MODULE_CERTIFICATION => 'Certification',
            moduleBusinessEnum::MODULE_BRANCH => 'Branch',
            moduleBusinessEnum::MODULE_SERVICE=>'Service',
            moduleBusinessEnum::MODULE_SAVINGS=>'Savings',
            moduleBusinessEnum::MODULE_HOME=>'Home',
            moduleBusinessEnum::MODULE_LOAN=>'Loan(Any Time)',
            moduleBusinessEnum::MODULE_LOAN_ONE_TIME=>'Loan(One Time)',
            moduleBusinessEnum::MODULE_LOAN_CONTRACT=>'Loan Contract',
            moduleBusinessEnum::MODULE_LOAN_REPAY=>'Loan Repay',
            moduleBusinessEnum::MODULE_APPROVE_CREDIT=>'Approve Credit',
            moduleBusinessEnum::MODULE_TOP_UP=>'Top-up',



        );
    }


    public static function getLoanConsultStateLang()
    {
        return array(
            loanConsultStateEnum::CREATE => 'Create',
            loanConsultStateEnum::LOCKED => 'Locked',
            loanConsultStateEnum::OPERATOR_REJECT => 'Operator Reject',
            loanConsultStateEnum::OPERATOR_APPROVED => 'Operator Approved',
            loanConsultStateEnum::ALLOT_BRANCH => 'Allot Branch',
            loanConsultStateEnum::BRANCH_REJECT => 'Branch Reject',
            loanConsultStateEnum::ALLOT_CO=>'Allot Co',
            loanConsultStateEnum::CO_HANDING=>'Co Handing',
            loanConsultStateEnum::CO_CANCEL=>'Co Cancel',
            loanConsultStateEnum::CO_APPROVED=>'Co Approved',
        );
    }

    public static function getSpecialLoanCateLang()
    {
        return array(
            specialLoanCateKeyEnum::FIX_REPAYMENT_DATE => 'Fix Repayment Date',
            specialLoanCateKeyEnum::QUICK_LOAN => 'Quick Loan',
        );
    }

    public static function getPartnerBizTypeLang()
    {
        return array(
            partnerBizTypeEnum::TRANSFER => 'Transfer',
        );
    }


    public static function getAssetStateLang()
    {
        return array(
            assetStateEnum::CREATE => 'New',
            assetStateEnum::CANCEL => 'Cancel',
            assetStateEnum::INVALID => 'Invalid',
            assetStateEnum::CERTIFIED => 'Certified',
            assetStateEnum::GRANTED => 'Granted'
        );
    }

}