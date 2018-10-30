<?php

class passbookWorkerClass {


    // 放款是直接放到余额
    public static function disburseLoan($schemeId) {


        $scheme_model = new loan_disbursement_schemeModel();
        $scheme_info = $scheme_model->getRow($schemeId);
        if (!$scheme_info) {
            return new result(false, "Disbursement scheme $schemeId not found", null, errorCodesEnum::UNEXPECTED_DATA);
        }

        $contract_info = (new loan_contractModel())->getRow($scheme_info->contract_id);
        if( !$contract_info ){
            return new result(false,'No contract info: '.$scheme_info->contract_id,null,errorCodesEnum::NO_CONTRACT);
        }


        $conn = ormYo::Conn();
        try{

            $conn->startTransaction();
            // 第一个是转到储蓄账户
            $ret = (new loanDisburseTradingClass($scheme_info))->execute();
            if (!$ret->STS) {
                $conn->rollback();
                return $ret;
            }

            // 需要扣钱才调用
            if( ( $scheme_info['deduct_annual_fee'] +
                $scheme_info['deduct_interest'] +
                $scheme_info['deduct_operation_fee'] +
                $scheme_info['deduct_admin_fee'] +
                $scheme_info['deduct_loan_fee'] +
                $scheme_info['deduct_insurance_fee']
                +$scheme_info['deduct_service_fee']) > 0 )
            {
                // 第二个是从储蓄账户扣除相关费用
                $ret = (new loanDeductTradingClass($scheme_info))->execute();
                if (!$ret->STS) {
                    $conn->rollback();
                    return $ret;
                }
            }


            $conn->submitTransaction();



        }catch(Exception $e){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNKNOWN_ERROR);
        }


        // todo 自动转到partner(withdraw_to_partner)
        return $ret;

    }

    public static function writeOffLoan($contractId, $remark) {
        // 创建和执行交易
        $trading = new loanWrittenOffTradingClass($contractId);
        $trading->remark = $remark;
        $ret = $trading->execute();
        return $ret;
    }

    public static function memberDepositByCash($memberId, $cashierUserId, $amount, $currency,$remark=null,$multi_currency = array(), $exchange_to_currency = array()) {
        // 创建和执行交易
        $trading = new memberDepositByCashTradingClass($memberId, $cashierUserId, $amount, $currency,$multi_currency,$exchange_to_currency);
        $trading->remark = $remark;
        $ret = $trading->execute();
        return $ret;
    }

    public static function memberDepositByPartner($memberId, $accountHandlerId, $amount, $currency,$biz_id=0,$remark=null) {

        $handler_model = new member_account_handlerModel();
        $handler_info = $handler_model->getRow($accountHandlerId);
        if( !$handler_info ){
            return new result(false,'Not found handler',null,errorCodesEnum::NO_ACCOUNT_HANDLER);
        }

        switch ($handler_info->handler_type) {
            case memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY:
                $partner_id = partnerClass::getAsiaweiluyPartnerID();
                break;
            default:
                return new result(false, 'Partner is not supported now', null, errorCodesEnum::NOT_SUPPORTED);
        }

        // 存钱不需要锁定，操作成功了就加钱
        //  自动扣款
        $handler_class = loan_handlerClass::getHandler($accountHandlerId);
        if( !$handler_class ){
            return new result(false,'Not found handler',null,errorCodesEnum::NO_ACCOUNT_HANDLER);
        }
        $refBiz = array(
            'type' => refBizTypeEnum::SAVINGS,
            'sub_type' => bizCodeEnum::MEMBER_DEPOSIT_BY_PARTNER,
            'account_id' => $accountHandlerId,
            'biz_id' => $biz_id
        );
        $description = 'Member deposit by partner';
        $rt = $handler_class->automaticDeduction($refBiz,$amount,$currency,$description);

        $api_trx_id = $rt->DATA['uid'];
        if( !$rt->STS ){
            return $rt;
        }

        try{

            // 创建和执行交易
            $trading = new memberDepositByPartnerTradingClass($memberId, $partner_id, $amount, $currency);
            $trading->remark = $remark;
            $ret = $trading->execute();
            if (!$ret->STS) {
                $ret->DATA['api_trx_id'] = $api_trx_id;
                $ret->DATA['trade_id'] = null;
                return $ret;
            }

            $trade_id = $ret->DATA;

            return new result(true,'success',array(
                'trade_id' => $trade_id,
                'api_trx_id' => $api_trx_id
            ));


        }catch( Exception $e ){

            return new result(false,$e->getMessage(),array(
                'trade_id' => null,
                'api_trx_id' => $api_trx_id
            ),errorCodesEnum::UNKNOWN_ERROR);

        }


    }

    public static function memberDepositByBank($memberId, $bankAccountId, $amount, $currency) {

        // 创建和执行交易
        $ret = (new memberDepositByBankTradingClass($memberId, $bankAccountId, $amount, $currency))->execute();
        return $ret;
    }

    public static function memberWithdrawToCash($memberId, $tradingPassword, $cashierUserId, $amount, $currency,$remark) {

        // 创建和执行交易
        $trading = new memberWithdrawByCashTradingClass($memberId, $tradingPassword, $cashierUserId, $amount, $currency);
        $trading->remark = $remark;
        $ret = $trading->execute();
        return $ret;
    }

    public static function memberWithdrawToBank($memberId,$tradingPassword,$amount,$currency,$bankAccountId,$trading_fee=0,$client_fee=0)
    {
        // 创建和执行交易
        $ret = (new memberWithdrawByBankTradingClass($memberId,$tradingPassword,$bankAccountId,$amount,$currency,$trading_fee,$client_fee))->execute();
        return $ret;
    }

    public static function memberWithdrawToPartner($memberId,$tradingPassword,$accountHandlerId, $amount, $currency,$biz_id=0,$remark=null) {

        // 中间状态代替事务
        $handler_model = new member_account_handlerModel();
        $handler_info = $handler_model->getRow($accountHandlerId);

        switch ($handler_info->handler_type) {
            case memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY:
                $partner_id = partnerClass::getAsiaweiluyPartnerID();
                break;
            default:
                return new result(false, 'Partner is not supported now', null, errorCodesEnum::NOT_SUPPORTED);
        }


        // 创建和执行交易
        $trading = new memberWithdrawByPartnerTradingClass($memberId,$tradingPassword,$partner_id, $amount, $currency);
        $trading->remark = $remark;
        $trading->is_outstanding = 1;  // 先lock
        $ret = $trading->execute();
        if ( !$ret->STS ) {
            return $ret;
        }

        $trade_id = $ret->DATA;

        try{

            // 交易成功自动转账到member账户
            $refBiz = array(
                'type' => refBizTypeEnum::SAVINGS,
                'sub_type' => bizCodeEnum::MEMBER_WITHDRAW_TO_PARTNER,
                'account_id' => $accountHandlerId,
                'biz_id' => $biz_id
            );

            $handler_class = loan_handlerClass::getHandler($accountHandlerId);
            if( !$handler_class ){
                return new result(false,'Invalid handler',null,errorCodesEnum::NO_ACCOUNT_HANDLER);
            }

            $description = 'Member savings withdraw to partner';

            $rt = $handler_class->deposit($refBiz,$amount,$currency,$description);

            $api_trx_id = $rt->DATA['uid'];

            $return_data = $rt->DATA;
            $return_data['trade_id'] = $trade_id;

            if( !$rt->STS ){

                // 失败，释放锁住的金额
                if( $rt->CODE != errorCodesEnum::UNKNOWN_ERROR ){
                    $re = tradingClass::reject($trade_id);
                    // 取消失败
                    if( !$re->STS && $re->CODE != errorCodesEnum::TRADING_CANCELLED ){
                        $re->DATA = $return_data;
                        return $re;
                    }
                }
                // 未知错误，挂起
                $rt->DATA = $return_data;
                return $rt;
            }

            // 成功，确认交易
            $rt = tradingClass::confirm($trade_id);
            if( !$rt->STS && $rt->CODE != errorCodesEnum::TRADING_FINISHED ){
                $rt->DATA = $return_data;
                return $rt;
            }

            return new result(true,'success',array(
                'trade_id' => $trade_id,
                'api_trx_id' => $api_trx_id
            ));

        }catch( Exception $e ){

            return new result(false,$e->getMessage(),array(
                'trade_id' => $trade_id,
                'api_trx_id' => null
            ));

        }



    }

    public static function memberTransferToMember($fromMemberId,$tradingPassword, $toMemberId, $amount, $currency, $remark = null) {
        // 创建交易
        $trading = new memberToMemberTradingClass($fromMemberId, $tradingPassword, $toMemberId, $amount, $currency);
        $trading->remark = $remark;

        // 执行交易
        $ret = $trading->execute();
        return $ret;
    }

    public static function memberPaymentToMember($fromMemberId,$tradingPassword, $toMemberId, $amount, $currency,$remark=null) {
        // 创建和执行交易
        $trading = new memberPaymentToMemberTradingClass($fromMemberId, $tradingPassword, $toMemberId, $amount, $currency);
        $trading->remark = $remark;
        $ret = $trading->execute();
        return $ret;
    }

    public static function memberLoanRepaymentOfSchema($schemeInfo,$paid_currency,$multi_currency = array())
    {
        $ret = (new loanRepaymentTradingClass($schemeInfo,$paid_currency,$multi_currency))->execute();
        return $ret;
    }

    public static function adjustBranch($branchId, $amount, $currency, $remark = null)
    {
        // 创建交易
        $trading = new branchAdjustTradingClass($branchId,$amount,$currency);
        $trading->remark = $remark;
        // 执行交易
        $ret = $trading->execute();
        return $ret;
    }
    public static function adjustUser($userId, $amount, $currency, $remark = null)
    {
        // 创建交易
        $trading = new userAdjustTradingClass($userId,$amount,$currency);
        $trading->remark = $remark;
        // 执行交易
        $ret = $trading->execute();
        return $ret;
    }
    public static function adjustBank($bankAccountId, $amount, $currency, $remark = null) {
        // 创建交易
        $trading = new bankAdjustTradingClass($bankAccountId,$amount,$currency);
        $trading->remark = $remark;
        // 执行交易
        $ret = $trading->execute();
        return $ret;
    }

    public static function adjustMember($memberId, $amount, $currency, $remark = null) {
        // 创建交易
        $trading = new memberAdjustTradingClass($memberId,$amount,$currency);
        $trading->remark = $remark;
        // 执行交易
        $ret = $trading->execute();
        return $ret;
    }
    public static function adjustSystem($book_id, $amount, $currency, $remark = null) {
        // 创建交易
        $trading = new glAdjustTradingClass($book_id,$amount,$currency);
        $trading->remark = $remark;
        // 执行交易
        $ret = $trading->execute();
        return $ret;
    }


    public static function adjustAnyPassbook($guid, $amount, $currency, $remark = null)
    {
        try {
            // 创建交易
            $trading = new adjustTradingClass(passbookClass::getOrCreatePassbookByObjGuid($guid), $amount, $currency);
            $trading->remark = $remark;

            // 执行交易
            $ret = $trading->execute();
            return $ret;
        } catch (Exception $ex) {
            return new result(false, $ex->getMessage(), null, $ex->getCode());
        }
    }


    public static function IncomeOperationFeeByBalance($member_id,$amount, $currency,$remark,$businessType=businessTypeEnum::OTHER)
    {
        try{
            $memberObj = new objectMemberClass($member_id);
            $client_passbook = $memberObj->getSavingsPassbook();
            $trading = new incomeOperationFeeBalanceTradingClass($client_passbook,$amount,$currency,$businessType);
            $trading->remark = $remark;
            $rt = $trading->execute();
            return $rt;
        }catch( Exception $e ){
            return new result(false, $e->getMessage(), null, errorCodesEnum::UNEXPECTED_DATA);
        }
    }


    public static function IncomeOperationFeeByCash($cashierUserId, $amount, $currency,$remark, $businessType = businessTypeEnum::OTHER)
    {
        try{
            $trading = new incomeOperationFeeCashTradingClass($cashierUserId,$amount,$currency,$businessType);
            $trading->remark = $remark;
            $rt = $trading->execute();
            return $rt;
        }catch( Exception $e ){
            return new result(false, $e->getMessage(), null, errorCodesEnum::UNEXPECTED_DATA);
        }
    }


    public static function memberPaymentLoanPenaltyByBalance($member_id,$amount,$currency,$remark,$business_type=businessTypeEnum::CREDIT_LOAN)
    {
        try{
            $memberObj = new objectMemberClass($member_id);
            $client_passbook = $memberObj->getSavingsPassbook();
            $trading = new incomeFromBalanceTradingClass($client_passbook,$amount,$currency,incomingTypeEnum::OVERDUE_PENALTY,$business_type);
            $trading->subject = 'Loan penalty';
            $trading->remark = $remark;
            $rt = $trading->execute();
            return $rt;
        }catch( Exception $e ){
            return new result(false, $e->getMessage(), null, errorCodesEnum::UNEXPECTED_DATA);
        }

    }

    public static function memberPaymentLoanPenaltyByCash($member_id, $cashier_id,$amount,$currency,$remark,$business_type=businessTypeEnum::CREDIT_LOAN)
    {
        try{
            $trading_deposit = new memberDepositByCashTradingClass($member_id, $cashier_id, $amount, $currency);
            $trading_deposit->remark = $remark;
            $rt = $trading_deposit->execute();
            if (!$rt->STS) return $rt;

            $trading_income = new incomeFromBalanceTradingClass(
                passbookClass::getSavingsPassbookOfMemberId($member_id),
                $amount,$currency,incomingTypeEnum::OVERDUE_PENALTY,$business_type);
            $trading_income->subject = 'Loan penalty';
            $trading_income->remark = $remark;
            $rt = $trading_income->execute();
            return $rt;
        }catch( Exception $e ){
            return new result(false, $e->getMessage(), null, errorCodesEnum::UNEXPECTED_DATA);
        }
    }



    public static function receiveCreditAuthContractFeeByCash($cashier_id,$amount,$currency,$remark)
    {
        $userObj = new objectUserClass($cashier_id);
        $trading = new incomeFromCashTradingClass($cashier_id,$amount,$currency,incomingTypeEnum::LOAN_FEE,businessTypeEnum::CREDIT_LOAN);
        $trading->subject = 'Credit Loan Fee';
        $trading->remark = $remark;
        $trading->sys_memo = "Credit Loan Fee income($remark) by cash:cashier ".
            $userObj->user_name.'('.$userObj->user_code.'):'.$amount.$currency;
        $rt = $trading->execute();
        return $rt;
    }

    public static function receiveCreditAuthContractFeeByBalance($member_id, $loan_fee_amount, $admin_fee_amount,$annual_fee_amount, $currency, $remark)
    {

        $memberObj = new objectMemberClass($member_id);
        if ($loan_fee_amount > 0) {
            $trading = new incomeFromBalanceTradingClass(
                passbookClass::getSavingsPassbookOfMemberId($member_id),
                $loan_fee_amount,
                $currency,
                incomingTypeEnum::LOAN_FEE,
                businessTypeEnum::CREDIT_LOAN
            );
            $trading->subject = 'Credit Contract Loan Fee';
            $trading->remark = $remark;
            $trading->sys_memo = "Credit Loan Fee income($remark) by balance: ".
                $memberObj->display_name.'('.$memberObj->object_id.'):'.$loan_fee_amount.$currency;
            $rt = $trading->execute();
            if (!$rt->STS) return $rt;
        }

        if ($admin_fee_amount > 0) {
            $trading = new incomeFromBalanceTradingClass(
                passbookClass::getSavingsPassbookOfMemberId($member_id),
                $admin_fee_amount,
                $currency,
                incomingTypeEnum::ADMIN_FEE,
                businessTypeEnum::CREDIT_LOAN
            );
            $trading->subject = 'Credit Contract Admin Fee';
            $trading->remark = $remark;
            $trading->sys_memo = "Credit Admin Fee income($remark) by balance: ".
                $memberObj->display_name.'('.$memberObj->object_id.'):'.$admin_fee_amount.$currency;
            $rt = $trading->execute();
            if( !$rt->STS ){
                return new result(false, 'Loan Fee succeed and admin fee failed', null, errorCodesEnum::PART_FAILED, $rt);
            }
        }
        if ($annual_fee_amount> 0) {
            $trading = new incomeFromBalanceTradingClass(
                passbookClass::getSavingsPassbookOfMemberId($member_id),
                $annual_fee_amount,
                $currency,
                incomingTypeEnum::ANNUAL_FEE,
                businessTypeEnum::CREDIT_LOAN
            );
            $trading->subject = 'Credit Contract Annual Fee';
            $trading->remark = $remark;
            $trading->sys_memo = "Credit Annual Fee income($remark) by balance: ".
                $memberObj->display_name.'('.$memberObj->object_id.'):'.$annual_fee_amount.$currency;
            $rt = $trading->execute();
            if( !$rt->STS ){
                return new result(false, 'Loan Fee & Admin Fee succeed,But Annual Fee Failed', null, errorCodesEnum::PART_FAILED, $rt);
            }
        }

        return new result(true);
    }




    public static function otherIncomeByCash($cashier_id,$amount,$currency,$remark=null)
    {
        $userObj = new objectUserClass($cashier_id);

        $trading = new incomeFromCashTradingClass($cashier_id,$amount,$currency,incomingTypeEnum::OTHER_INCOMING,businessTypeEnum::OTHER);
        $trading->subject = 'Other Incoming';
        $trading->remark = $remark;
        $trading->sys_memo = "Other income ($remark) by cash:cashier ".
        $userObj->user_name.'('.$userObj->user_code.'):'.$amount.$currency;
        $rt = $trading->execute();
        return $rt;
    }

    public static function otherIncomeByClientBalance($member_id,$amount,$currency,$remark=null)
    {
        $memberObj = new objectMemberClass($member_id);

        $passbook = $memberObj->getSavingsPassbook();
        $trading = new incomeFromBalanceTradingClass($passbook,$amount,$currency,incomingTypeEnum::OTHER_INCOMING,businessTypeEnum::OTHER);
        $trading->subject = 'Other Incoming';
        $trading->remark = $remark;
        $trading->sys_memo = "Other income ($remark) by client savings:client ".
            ($memberObj->display_name).'('.$memberObj->object_id.'):'.$amount.$currency;
        $rt = $trading->execute();
        return $rt;

    }

    public static function memberPurchaseSavingsProduct($member_id, $product_id, $amount, $purchase_fee, $currency) {
        $trading = new memberPurchaseSavingsProductByBalanceTradingClass($member_id,$product_id,$amount,$currency,$purchase_fee);
        $rt = $trading->execute();
        return $rt;
    }

    public static function memberRedeemSavingsProduct($member_id, $product_id, $amount, $redeem_fee, $currency) {
        $trading = new memberRedeemSavingsProductByBalanceTradingClass($member_id,$product_id,$amount,$currency,$redeem_fee);
        $rt = $trading->execute();
        return $rt;
    }
}