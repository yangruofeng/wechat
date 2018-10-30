<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/27
 * Time: 11:48
 */
class bizFactoryClass
{
    public static function getInstance($scene_code,$biz_code)
    {
        switch ($biz_code) {
            case bizCodeEnum::MEMBER_WITHDRAW_TO_CASH :
                return new bizMemberWithdrawToCashClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_WITHDRAW_TO_BANK :
                return new bizMemberWithdrawToBankClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_WITHDRAW_TO_PARTNER :
                return new bizMemberWithdrawToPartnerClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_DEPOSIT_BY_CASH :
                return new bizMemberDepositByCashClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_DEPOSIT_BY_BANK :
                return new bizMemberDepositByBankClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_DEPOSIT_BY_PARTNER :
                return new bizMemberDepositByPartnerClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_TRANSFER_TO_MEMBER :
                return new bizMemberToMemberClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_TRANSFER_TO_BANK :
                return new bizMemberTransferToBankClass($scene_code);
                break;
            case bizCodeEnum::TELLER_TO_BRANCH :
                return new bizTellerToBranchClass($scene_code);
                break;
            case bizCodeEnum::BRANCH_TO_TELLER :
                return new bizBranchToTellerClass($scene_code);
                break;
            case bizCodeEnum::BRANCH_TO_BANK :
                return new bizBranchToBankClass($scene_code);
                break;
            case bizCodeEnum::BANK_TO_BRANCH :
                return new bizBankToBranchClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_SCAN_PAY_TO_MEMBER :
                return new bizMemberScanPayMemberClass($scene_code);
                break;
            case bizCodeEnum::BANK_TO_HEADQUARTER:
                return new bizBankToHeadquarterClass($scene_code);
                break;
            case bizCodeEnum::HEADQUARTER_TO_BANK:
                return new bizHeadquarterToBankClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_LOAN_REPAYMENT_BY_CASH:
                return new bizMemberLoanRepaymentByCashClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_LOAN_REPAYMENT_BY_MEMBER_APP:
                return new bizMemberLoanRepaymentByMemberAppClass();
                break;
            case bizCodeEnum::MEMBER_CHANGE_TRADING_PASSWORD_BY_COUNTER:
                return new bizMemberChangeTradingPasswordByCounterClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_CHANGE_PHONE_BY_COUNTER:
                return new bizMemberChangePhoneByCounterClass($scene_code);
                break;
            case bizCodeEnum::CO_RECEIVE_LOAN_FROM_MEMBER:
                return new bizCoReceiveLoanFromClientClass();
                break;
            case bizCodeEnum::CO_TRANSFER_TO_TELLER:
                return new bizCoTransferToTellerClass();
                break;
            case bizCodeEnum::CAPITAL_TO_CIV:
                return new bizCapitalToCivClass($scene_code);
                break;
            case bizCodeEnum::CIV_TO_COD:
                return new bizCivToCodClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_CREATE_LOAN_CONTRACT:
                return new bizMemberCreateLoanContractClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_LOAN_BY_MEMBER_APP:
                return new bizMemberLoanByMemberAppClass();
                break;
            case bizCodeEnum::MEMBER_UNLOCK_BY_COUNTER :
                return new bizMemberLockHandleClass();
                break;
            case bizCodeEnum::RECEIVE_LOAN_PENALTY_BY_COUNTER :
                return new bizReceiveLoanPenaltyClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_PREPAYMENT:
                return new bizMemberPrepaymentClass($scene_code);
                break;
            case bizCodeEnum::MEMBER_PREPAYMENT_BY_MEMBER_APP:
                return new bizMemberPrepaymentByMemberAppClass();
                break;
            case bizCodeEnum::OUT_SYSTEM_CASH_FLOW:
                return new bizOutSystemCashFlowClass($scene_code);
                break;
            case bizCodeEnum::BANK_ADJUST_FEE_INTEREST:
                return new bizBankAdjustClass($scene_code);
                break;
            case bizCodeEnum::HEADQUARTER_TO_BRANCH:
                return new bizHeadquarterToBranchClass($scene_code);
            case bizCodeEnum::BRANCH_TO_HEADQUARTER:
                return new bizBranchToHeadquarterClass($scene_code);
            case bizCodeEnum::ONE_TIME_CREDIT_LOAN:
                return new bizOneTimeCreditLoanClass($scene_code);
                break;
            case bizCodeEnum::GL_BATCH:
                return new bizGlBatchClass($scene_code);
                break;
            case bizCodeEnum::BRANCH_EXCHANGE:
                return new bizBranchExchangeClass($scene_code);
                break;
            case bizCodeEnum::CANCEL_CREDIT_CONTRACT:
                return new bizCreditContractCancelClass();
                break;
            case bizCodeEnum::CHECK_LOAN_BILL_PAY_BY_CONSOLE:
                return new bizCheckLoanBillPayByConsoleClass();
                break;
            default:
                throw new Exception('Not supported biz type.', errorCodesEnum::NOT_SUPPORTED);
        }
    }


}