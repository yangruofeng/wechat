<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/2
 * Time: 14:30
 */
class loanRepaymentWorkerClass
{


    /**
     * @param $member_id
     * @param $cashier_id
     * @param $schema_list
     *  array(1,2,3)
     * @param $amount
     * @param $currency
     * @param $multi_currency
     * @param $exchange_currency_amount .要买入的目标币种金额
     * @return result
     */
    public static function schemasRepaymentByCash($member_id,$cashier_id,$schema_list,$amount,$currency,$multi_currency=null,$exchange_currency_amount=null)
    {
        if( global_settingClass::isForbiddenReturnLoan() ){
            return new result(false,'Forbidden return loan.',null,errorCodesEnum::FUNCTION_CLOSED);
        }

        if( !is_array($schema_list) || empty($schema_list) ){
            return new result(false,'Invalid schema list.',null,errorCodesEnum::INVALID_PARAM);
        }

        if (!$multi_currency) {
            $multi_currency = array();
            $multi_currency[$currency] = $amount;
        }

        $contract_sn = array();
        $schema_dict = array();


        // 获得所有的合同号与计划
        $m_contract = new loan_contractModel();
        $m_schema = new loan_installment_schemeModel();
        foreach( $schema_list as $id ){
            $schema_info = $m_schema->find(array(
                'uid' => $id
            ));
            if( !$schema_info ){
                return new result(false,'No schema info:'.$id,null,errorCodesEnum::NO_DATA);
            }
            $contract_info = $m_contract->getRow(array(
                'uid' => $schema_info['contract_id']
            ));
            if( !$contract_info ){
                return new result(false,'No contract info:'.$schema_info['contract_id'],null,errorCodesEnum::NO_DATA);
            }
            $interest_info = loan_contractClass::getContractInterestInfoByContractInfo($contract_info);
            if (!in_array($contract_info['contract_sn'], $contract_sn)) {
                $contract_sn[] = $contract_info['contract_sn'];
            }

            $interest_class = interestTypeClass::getInstance($contract_info['repayment_type'], $contract_info['repayment_period']);
            $new_schema_info = $interest_class->calculateRepaymentInterestOfSchema($schema_info, $interest_info);
            $schema_remaining_amount = $new_schema_info['amount'] - $new_schema_info['actual_payment_amount'];

            $schema_dict[$id] = array(
                'currency' => $contract_info['currency'],
                'amount' => $schema_remaining_amount
            );

        }

        $schema_str = 'schema ids:'.implode(',',$schema_list);
        $currency_str_arr = array();
        foreach( $multi_currency as $c=>$a ){
            $currency_str_arr[] = $a.$c;
        }


        $member_info = (new memberModel())->getMemberInfoById($member_id);

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            // 将钱存入balance
            $mark = "Loan repayment for loan account number ".implode(',',$contract_sn);

            // 构建详细的memo
            $userObj = new objectUserClass($cashier_id);
            $sys_memo = " Loan repayment deposit,contract list:".implode(',',$contract_sn).
            ','.$schema_str.',client '.($member_info['display_name']?:$member_info['login_code']).'('.$member_info['obj_guid'].')'
            .',repayment amount:'.implode(',',$currency_str_arr).'.cashier:'.$userObj->user_name.
            '('.$userObj->user_code.')'.',branch '.$userObj->branch_name;

            // 存钱的时候不换汇了
            $exchange_currency_amount = null;
            $depositTrading = new memberDepositByCashTradingClass(
                $member_id,
                $cashier_id,
                $amount,
                $currency,
                $multi_currency,
                null);
            $depositTrading->remark = $mark;
            $depositTrading->sys_memo = $sys_memo;
            $rt = $depositTrading->execute();
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $trade_id = intval($rt->DATA);

            $alloc_ret = system_toolClass::calMultiCurrencyDeductForMultiCurrencyAmount($schema_dict, $multi_currency);
            if (!$alloc_ret->STS) {
                $conn->rollback();
                return new result(false, 'Alloc amount for each schema failed', null, errorCodesEnum::UNKNOWN_ERROR, $alloc_ret);

            }
            $schema_dict = $alloc_ret->DATA;

            foreach( $schema_list as $schema_id ) {
                $schema_alloc_info = $schema_dict[$schema_id];
                if ($schema_alloc_info['left_amount'] < $schema_alloc_info['amount']) {
                    $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($schema_id);

                    // Notes: 正常还款是否使用提前还款逻辑不再在这里判断，而是在具体的interestType类中的calculateRepaymentInterestOfSchema方法实现
                    // 正常还款不使用提前还款逻辑的计息方式，calculateRepaymentInterestOfSchema不做处理直接返回传入的计划
                    // 使用提前还款逻辑的，提前还款逻辑计算得到的各项数据更新传入的计划并返回，外部execute中更新计划表，并按更新后的计划执行其他逻辑

                    $repaymentClass = new schemaRepaymentByCashClass(
                        $cashier_id,
                        $schema_id,
                        $penalty,
                        null,
                        null,
                        $schema_alloc_info['multi_currency']);
                    $repaymentClass->ref_trade_id = $trade_id;
                    $rt = $repaymentClass->repaymentExecute();
                    if (!$rt->STS) {
                        $conn->rollback();
                        return $rt;
                    }
                }
            }

            $conn->submitTransaction();

            return new result(true,'success',$trade_id);

        }catch( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNKNOWN_ERROR);
        }



    }


    /**
     * @param $schema_list
     *  array(1,2,3)
     * @param $member_handler_id
     * @return result
     */
    public static function schemasRepaymentByPartner($schema_list,$member_handler_id)
    {

        // 内部有事务

        if( global_settingClass::isForbiddenReturnLoan() ){
            return new result(false,'Forbidden return loan.',null,errorCodesEnum::FUNCTION_CLOSED);
        }

        if( !is_array($schema_list) || empty($schema_list) ){
            return new result(false,'Invalid schema list.',null,errorCodesEnum::INVALID_PARAM);
        }
        foreach( $schema_list as $schema_id ) {


            $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($schema_id);

            // Notes: 正常还款是否使用提前还款逻辑不再在这里判断，而是在具体的interestType类中的calculateRepaymentInterestOfSchema方法实现
            // 正常还款不使用提前还款逻辑的计息方式，calculateRepaymentInterestOfSchema不做处理直接返回传入的计划
            // 使用提前还款逻辑的，提前还款逻辑计算得到的各项数据更新传入的计划并返回，外部execute中更新计划表，并按更新后的计划执行其他逻辑
            $rt = (new schemaRepaymentByPartnerClass($schema_id, $penalty, $member_handler_id))->repaymentExecute();

            if (!$rt->STS) {
                return $rt;
            }

        }

        return new result(true,'success');

    }


    /**
     * @param $schema_list
     *  array(1,2,3)
     * @return result
     */
    public static function schemasRepaymentByBalance($schema_list)
    {
        // 内部有事务
        if( global_settingClass::isForbiddenReturnLoan() ){
            return new result(false,'Forbidden return loan.',null,errorCodesEnum::FUNCTION_CLOSED);
        }

        if( !is_array($schema_list) || empty($schema_list) ){
            return new result(false,'Invalid schema list.',null,errorCodesEnum::INVALID_PARAM);
        }
        foreach( $schema_list as $schema_id ) {

            $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($schema_id);

            // Notes: 正常还款是否使用提前还款逻辑不再在这里判断，而是在具体的interestType类中的calculateRepaymentInterestOfSchema方法实现
            // 正常还款不使用提前还款逻辑的计息方式，calculateRepaymentInterestOfSchema不做处理直接返回传入的计划
            // 使用提前还款逻辑的，提前还款逻辑计算得到的各项数据更新传入的计划并返回，外部execute中更新计划表，并按更新后的计划执行其他逻辑
            $rt = (new schemaRepaymentByBalanceClass($schema_id, $penalty))->repaymentExecute();

            if (!$rt->STS) {
                return $rt;
            }

        }

        return new result(true,'success');
    }

    /**
     * @param $user_id
     * @param $member_id
     * @param $schema_list
     *  array(1,2,3)
     * @param $system_bank_id
     * @param $amount
     * @param $currency
     * @return result
     */
    public static function schemasRepaymentByBank($user_id,$member_id,$schema_list,$system_bank_id,$amount,$currency){

        if( global_settingClass::isForbiddenReturnLoan() ){
            return new result(false,'Forbidden return loan.',null,errorCodesEnum::FUNCTION_CLOSED);
        }

        if( !is_array($schema_list) || empty($schema_list) ){
            return new result(false,'Invalid schema list.',null,errorCodesEnum::INVALID_PARAM);
        }

        $systemBankObj = new objectSysBankClass($system_bank_id);

        $contract_sn = array();
        $schema_dict = array();

        // 临时计算变量
        $cal_multi_currency = array(
            $currency => $amount
        );
        $m_contract = new loan_contractModel();
        $m_schema = new loan_installment_schemeModel();
        foreach( $schema_list as $id ){
            $schema_info = $m_schema->find(array(
                'uid' => $id
            ));
            if( !$schema_info ){
                return new result(false,'No schema info:'.$id,null,errorCodesEnum::NO_DATA);
            }
            $contract_info = $m_contract->getRow(array(
                'uid' => $schema_info['contract_id']
            ));
            if( !$contract_info ){
                return new result(false,'No contract info:'.$schema_info['contract_id'],null,errorCodesEnum::NO_DATA);
            }
            $interest_info = loan_contractClass::getContractInterestInfoByContractInfo($contract_info);
            if (!in_array($contract_info['contract_sn'], $contract_sn)) {
                $contract_sn[] = $contract_info['contract_sn'];
            }

            $interest_class = interestTypeClass::getInstance($contract_info['repayment_type'], $contract_info['repayment_period']);
            $new_schema_info = $interest_class->calculateRepaymentInterestOfSchema($schema_info, $interest_info);
            $schema_remaining_amount = $new_schema_info['amount'] - $new_schema_info['actual_payment_amount'];
            // 计算分配额
            $rt = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($schema_remaining_amount,
                $contract_info['currency'],$cal_multi_currency);
            $paid_currency = $rt->DATA['multi_currency'];

            $schema_dict[$id] = $paid_currency;

            // 减扣总额
            foreach( $paid_currency as $c=>$a){
                $cal_multi_currency[$c] -= $a;
            }

        }

        $member_info = (new memberModel())->getMemberInfoById($member_id);

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            // 构建详细的memo
            $schema_str = 'schema ids:'.implode(',',$schema_list);
            $bankObj = new objectSysBankClass($system_bank_id);
            $sys_memo = "Client loan repayment through bank transfer,contract list:".implode(',',$contract_sn).
                ','.$schema_str.',client '.($member_info['display_name']?:$member_info['login_code']).'('.$member_info['obj_guid'].')'
                .',repayment amount:'.$amount.$currency.'.Transfer to bank:'.$bankObj->bank_name.
                '('.$bankObj->bank_account_no.')';

            // 将钱存入balance
            $mark = "Loan repayment for loan account number ".implode(',',$contract_sn);
            $tClass = new memberDepositByBankTradingClass($member_id,$system_bank_id,$amount,$currency);
            $tClass->remark = $mark;
            $tClass->sys_memo = $sys_memo;
            $rt = $tClass->execute();
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }

            $trade_id = $rt->DATA;

            foreach( $schema_list as $schema_id ) {

                $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($schema_id);

                // Notes: 正常还款是否使用提前还款逻辑不再在这里判断，而是在具体的interestType类中的calculateRepaymentInterestOfSchema方法实现
                // 正常还款不使用提前还款逻辑的计息方式，calculateRepaymentInterestOfSchema不做处理直接返回传入的计划
                // 使用提前还款逻辑的，提前还款逻辑计算得到的各项数据更新传入的计划并返回，外部execute中更新计划表，并按更新后的计划执行其他逻辑

                // 金额币种都传null，直接从余额扣钱就好了
                $repaymentClass = new schemaRepaymentByBankClass($user_id,
                    $systemBankObj->object_info,
                    $schema_id,$penalty,
                    null,
                    null,
                    $schema_dict[$schema_id]);
                $repaymentClass->ref_trade_id = $trade_id;
                $rt = $repaymentClass->repaymentExecute();
                if (!$rt->STS) {
                    $conn->rollback();
                    return $rt;
                }

            }

            $conn->submitTransaction();
            return new result(true);


        }catch( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNKNOWN_ERROR);
        }


    }

    public static function prepaymentByCash($apply_id,$cashier_id,$amount,$currency,$multi_currency=array(),$exchange_currency_amount=array())
    {
        if( global_settingClass::isForbiddenReturnLoan() ){
            return new result(false,'Forbidden return loan.',null,errorCodesEnum::FUNCTION_CLOSED);
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{
            $rt = (new loanPrepaymentByCashClass($apply_id,$cashier_id,$amount,$currency,$multi_currency,$exchange_currency_amount))->execute();
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNKNOWN_ERROR);
        }
    }

    public static function prepaymentByBalance($apply_id,$cashier_id=0,$amount=null,$currency=null,$multi_currency=array())
    {
        if( global_settingClass::isForbiddenReturnLoan() ){
            return new result(false,'Forbidden return loan.',null,errorCodesEnum::FUNCTION_CLOSED);
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{
            $rt = (new loanPrepaymentByBalanceClass($apply_id,$cashier_id,$amount,$currency,$multi_currency))->execute();
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNKNOWN_ERROR);
        }
    }

    public static function prepaymentByPartner($apply_id,$member_handler_id)
    {
        // 内部有事务
        if( global_settingClass::isForbiddenReturnLoan() ){
            return new result(false,'Forbidden return loan.',null,errorCodesEnum::FUNCTION_CLOSED);
        }
        return (new loanPrepaymentByPartnerClass($apply_id,$member_handler_id))->execute();
    }

    public static function prepaymentByBank($user_id,$apply_id,$system_bank_id,$amount,$currency,$remark=null)
    {
        if( global_settingClass::isForbiddenReturnLoan() ){
            return new result(false,'Forbidden return loan.',null,errorCodesEnum::FUNCTION_CLOSED);
        }

        $systemBankObj = new objectSysBankClass($system_bank_id);

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{
            $class = new loanPrepaymentByBankClass($apply_id,$user_id,$amount,$currency,$systemBankObj->object_info);
            $rt = $class->execute();
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNKNOWN_ERROR);
        }
    }


    public static function repaymentByBillPayCode($user_id,$billCode,$amount,$currency,$remark=null)
    {
        if( global_settingClass::isForbiddenReturnLoan() ){
            return new result(false,'Forbidden return loan.',null,errorCodesEnum::FUNCTION_CLOSED);
        }
        // 先查询合同
        $m_contract = new loan_contractModel();
        $reader = $m_contract->reader;
        $sql = "select * from loan_contract_billpay_code where bill_code=".qstr($billCode);
        $contract_bill = $m_contract->reader->getRow($sql);
        if( !$contract_bill ){
            return new result(false,'Invalid bill pay code:'.$billCode,null,errorCodesEnum::INVALID_PARAM);
        }

        $contract_id = $contract_bill['contract_id'];
        $contract_info = $m_contract->getRow($contract_bill['contract_id']);
        if( !$contract_info ){
            return new result(false,'No contract info:'.$contract_bill['contract_id'],null,errorCodesEnum::NO_CONTRACT);
        }

        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_id);
        if( !$member_info ){
            return new result(false,'Not find loan member info:'.$contract_id,null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 自动识别是提前还款还是计划还款
        // 查询是否有待还款的提前还款申请
        $sql = "select * from loan_prepayment_apply where contract_id='$contract_id' order by uid desc";
        $apply = $reader->getRow($sql);
        if( $apply ){
            if( $apply['state'] == prepaymentApplyStateEnum::APPROVED ){
                // 还款
                if( $apply['deadline_date'] >= date('Y-m-d') ){
                    return self::prepaymentByBank($user_id,$apply['uid'],$contract_bill['bank_id'],$amount,$currency);

                    // todo 如果提前还款后有剩余应该还要进行计划还款
                }
            }
        }

        // 计划还款
        // 获取可以偿还的计划
        $rt = loan_contractClass::getRepaymentSchemaByAmount($contract_id,$amount,$currency);
        if( !$rt->STS ){
            return $rt;
        }
        $schema_list = $rt->DATA['repayment_schema'];
        if( empty($schema_list) ){
            return new result(true);
        }

        $schema_ids = array();
        foreach( $schema_list as $v ){
            $schema_ids[] = $v['uid'];
        }

        return self::schemasRepaymentByBank($user_id,$member_info['uid'],$schema_ids,$contract_bill['bank_id'],$amount,$currency);

    }


}