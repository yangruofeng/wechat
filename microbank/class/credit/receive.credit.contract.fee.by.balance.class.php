<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/20
 * Time: 13:11
 */

class receiveCreditContractFeeByBalanceClass
{
    public function __construct()
    {
    }


    public function execute($auth_contract_id)
    {
        $auth_contract_id = intval($auth_contract_id);
        $m = new member_authorized_contractModel();
        $auth_contract = $m->getRow($auth_contract_id);
        if( !$auth_contract ){
            return new result(false,'No auth contract info:'.$auth_contract_id,null,errorCodesEnum::NO_DATA);
        }

        if( $auth_contract->is_paid == 1 ){
            return new result(true);
        }

        $member_id = $auth_contract['member_id'];

        $fee_usd = $auth_contract['fee'];
        $loan_fee_amount = $auth_contract['loan_fee_amount'];
        $admin_fee_amount = $auth_contract['admin_fee_amount'];
        $annual_fee_amount=$auth_contract['annual_fee_amount'];

        $remark = "Pay credit loan fee and admin fee by balance: contract sn ".$auth_contract['contract_no'];

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{


            $pay_currency_amount = array();

            // 分开扣钱
            if( $auth_contract['fee'] > 0 ){
                $currency = currencyEnum::USD;
                $rt = passbookWorkerClass::receiveCreditAuthContractFeeByBalance(
                    $member_id,
                    $loan_fee_amount,
                    $admin_fee_amount,
                    $annual_fee_amount,
                    $currency,
                    $remark);
                if( !$rt->STS ){
                    $conn->rollback();
                    return $rt;
                }
                $pay_currency_amount[$currency] = $auth_contract['fee'];
            }

            if( $auth_contract['fee_khr'] > 0 ){

                // 从KHR账户扣钱
                $currency = currencyEnum::KHR;
                $rt = passbookWorkerClass::receiveCreditAuthContractFeeByBalance(
                    $member_id,
                    $auth_contract['loan_fee_khr_amount'],
                    $auth_contract['admin_fee_khr_amount'],
                    $auth_contract['annual_fee_khr_amount'],
                    $currency,
                    $remark);
                if( !$rt->STS ){
                    $conn->rollback();
                    return $rt;
                }

                $pay_currency_amount[$currency] = $auth_contract['fee_khr'];


            }

            if( !empty($pay_currency_amount) ){
                $m_payment_detail = new member_authorized_contract_payment_detailModel();
                $rt = $m_payment_detail->insertPaymentDetail($auth_contract_id,$pay_currency_amount);
                if( !$rt->STS ){
                    $conn->rollback();
                    return $rt;
                }
            }

            $auth_contract->is_paid = 1;
            $auth_contract->pay_time = Now();
            $auth_contract->update_time = Now();
            $up = $auth_contract->update();
            if( !$up->STS ){
                $conn->rollback();
                return new result(false,'Update receive info fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
            }

            $conn->submitTransaction();
            return new result(true);

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage());
        }

    }


    /*public static function start($auth_contract_id)
    {
        $conn = ormYo::Conn();
        $conn->startTransaction();

        try{

            $auth_contract_id = intval($auth_contract_id);
            $m = new member_authorized_contractModel();
            $auth_contract = $m->getRow($auth_contract_id);
            if( !$auth_contract ){
                $conn->rollback();
                return new result(false,'No auth contract info:'.$auth_contract_id,null,errorCodesEnum::NO_DATA);
            }

            if( $auth_contract->fee <=0 || $auth_contract->is_paid == 1){
                $conn->submitTransaction();
                return new result(true);
            }

            // 已经锁过了
            if( $auth_contract->passbook_trading_id > 0 ){
                $conn->submitTransaction();
                return new result(true);
            }

            $member_obj = new objectMemberClass($auth_contract->member_id);
            $fee = $auth_contract->fee;
            $currency = currencyEnum::USD;
            $member_balance = $member_obj->getSavingsAccountBalance();
            // 计算余额是否足够
            $rt = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($fee,$currency,$member_balance);
            if( $rt->STS ){
                $multi_currency = $rt->DATA['multi_currency'];
            }else{
                $multi_currency = array(
                    $currency => $fee
                );
            }
            $member_passbook = $member_obj->getSavingsPassbook();

            $remark = "Credit contract fee:".$auth_contract->contract_no;
            $tradingClass = new incomeFromBalanceTradingClass($member_passbook,
                null,
                null,
                incomingTypeEnum::LOAN_FEE,
                businessTypeEnum::CREDIT_LOAN,
                $multi_currency
            );
            $tradingClass->subject = 'Credit Contract Fee';
            $tradingClass->is_lock = 1;
            $tradingClass->is_outstanding = 1;
            $tradingClass->remark = $remark;
            $rt = $tradingClass->execute();
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }

            $trading_id = intval($rt->DATA);
            $auth_contract->passbook_trading_id = $trading_id;
            $auth_contract->update_time = Now();
            $up = $auth_contract->update();
            if( !$up->STS ){
                $conn->rollback();
                return new result(false,'Update trading fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
            }

            $conn->submitTransaction();
            return new result(true);



        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,$e->getCode()?:errorCodesEnum::UNEXPECTED_DATA);
        }

    }*/

    /*public static function confirm($auth_contract_id)
    {


        $conn = ormYo::Conn();
        $conn->startTransaction();

        try{

            $auth_contract_id = intval($auth_contract_id);
            $m = new member_authorized_contractModel();
            $auth_contract = $m->getRow($auth_contract_id);
            if( !$auth_contract ){
                $conn->rollback();
                return new result(false,'No auth contract info:'.$auth_contract_id,null,errorCodesEnum::NO_DATA);
            }

            if( $auth_contract->fee <=0 || $auth_contract->is_paid == 1){
                $conn->submitTransaction();
                return new result(true);
            }

            if( !$auth_contract->passbook_trading_id ){
                $conn->rollback();
                return new result(false,'Did not start yet.',null,errorCodesEnum::UN_MATCH_OPERATION);
            }

            $trading_id = $auth_contract->passbook_trading_id;
            $rt = incomeFromBalanceTradingClass::confirm($trading_id);
            if( !$rt->STS  && $rt->CODE != errorCodesEnum::TRADING_FINISHED ){
                $conn->rollback();
                return $rt;
            }
            $auth_contract->is_paid = 1;
            $auth_contract->pay_time = Now();
            $auth_contract->update_time = Now();
            $up = $auth_contract->update();
            if( !$up->STS ){
                $conn->rollback();
                return new result(false,'Update receive info fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
            }

            $conn->submitTransaction();
            return new result(true);


        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,$e->getCode()?:errorCodesEnum::UNEXPECTED_DATA);
        }


    }*/
}