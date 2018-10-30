<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/2
 * Time: 14:12
 */
class schemaRepaymentByAutoDeductClass extends schemaNormalRepaymentClass
{

    protected $schema_id;
    protected $exchange_rate;


    public function __construct($schema_id,$penalty)
    {
        throw new Exception('Give up using.',errorCodesEnum::FUNCTION_CLOSED);

        $scheme_info = (new loan_installment_schemeModel())->getRow($schema_id);
        if( !$scheme_info ){
            throw new Exception('Unknown schema id: '.$this->schema_id,errorCodesEnum::NO_DATA);
        }

        $contract_info = (new loan_contractModel())->getRow($scheme_info->contract_id);
        if( !$contract_info ){
            throw new Exception('No contract info of id:'.$scheme_info->contract_id,errorCodesEnum::NO_CONTRACT);
        }

        $member_info = loan_contractClass::getLoanContractMemberInfo($scheme_info->contract_id);
        if( !$member_info ){
            throw new Exception('No loan member info.',errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $this->schema_id = $schema_id;
        $this->penalty = $penalty;
        $this->exchange_rate = 1;

        $this->is_schema_amount_repayment = false;  // 多样扣款

        $this->schema_info = $scheme_info;
        $this->contract_info = $contract_info;
        $this->member_info = $member_info;

    }


    public function getPayerInfo()
    {
        return member_handlerClass::getMemberDefaultPassbookHandlerInfo($this->member_info['uid']);
    }

    public function getCreatorInfo()
    {
        return array(
            'creator_id' => 0,
            'creator_name' => 'System',
            'teller_id' => 0,
            'teller_name' => null,
            'branch_id' =>0
        );
    }

    public function repaymentExecute()
    {
        // 不适用此方法
    }


    public function scriptAutoRepaymentExecuteStart()
    {

        $scheme_info = $this->schema_info;
        $contract_info = $this->contract_info;
        $schema_amount = $scheme_info['amount'] - $scheme_info['actual_payment_amount'];
        $contract_currency = $this->contract_info['currency'];

        // 自动分配有账户的钱
        $member_id = $this->member_info['uid'];
        $memberObj = new objectMemberClass($member_id);
        $member_balance = $memberObj->getSavingsAccountBalance();

        // 剔除掉没钱的
        foreach( $member_balance as $key=>$v ){
            if( $v <= 0 ){
                unset($member_balance[$key]);
            }
        }

        $rt = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($schema_amount,$contract_currency,$member_balance);

        if( !$rt->STS ){

            // 系统余额不足

            // 从绑定的ACE里扣钱
            if( global_settingClass::isAllowAutoDeductFromACE() ){

                $left_amount = $rt->DATA['left_amount'];
                // 获取列表
                $ace_handler = member_handlerClass::getMemberDefaultAceHandlerInfo($member_id);

                if( empty($ace_handler) ){
                    // 没有账户
                    $member_balance[$contract_currency] = -1;
                    $this->multi_currency = $member_balance;

                }else{

                    $handlerClass = loan_handlerClass::getHandler($ace_handler['uid']);
                    if( !$handlerClass ){
                        $member_balance[$contract_currency] = -1;
                        $this->multi_currency = $member_balance;

                    }

                    // 先查询余额
                    $rt = $handlerClass->getHandlerMultiCurrencyBalance();
                    if( !$rt->STS ){
                        return $rt;
                    }
                    $handler_balance = $rt->DATA;
                    $cal_handler_balance = array();
                    // 过滤掉系统不支持的币种和没钱的币种
                    foreach( (new currencyEnum())->toArray() as $currency ){
                        if( $handler_balance[$currency] > 0 ){
                            $cal_handler_balance[$currency] = $handler_balance[$currency];
                        }
                    }

                    $remark = 'Loan repayment:'.$this->contract_info['contract_sn'];

                    // 计算是否足额
                    $rt = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($left_amount,$contract_currency,$cal_handler_balance);
                    if( !$rt->STS ){

                        // 金额不足
                        // 先将钱全部存过来
                        foreach( $cal_handler_balance as $c=>$a ){
                            $rt = passbookWorkerClass::memberDepositByPartner($member_id,$ace_handler['uid'],$a,$c,0,$remark);
                            if( !$rt->STS ){
                                return $rt;
                            }
                        }

                        $lock_currency = $memberObj->getSavingsAccountBalance();
                        $lock_currency[$contract_currency] = -1;
                        // 然后锁定余额账户的钱
                        $this->multi_currency = $lock_currency;


                    }else{

                        // 够钱
                        // 存需要的钱过来
                        $deposit_amount = $rt->DATA['multi_currency'];
                        foreach( $deposit_amount as $c=>$a ){
                            $rt = passbookWorkerClass::memberDepositByPartner($member_id,$ace_handler['uid'],$a,$c,0,$remark);
                            if( !$rt->STS ){
                                return $rt;
                            }
                        }

                        // 存完后重新计算扣款
                        $new_member_balance = $memberObj->getSavingsAccountBalance();
                        $rt = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($schema_amount,$contract_currency,$new_member_balance);
                        if( !$rt->STS ){
                            // 计算误差，锁定余额账户的钱

                            $lock_currency = $new_member_balance;
                            $lock_currency[$contract_currency] = -1;
                            // 然后锁定余额账户的钱
                            $this->multi_currency = $lock_currency;

                        }

                        // 得到扣款的币种金额
                        $multi_currency_amount = $rt->DATA['multi_currency'];
                        $this->multi_currency = $multi_currency_amount;

                    }

                }
            }



        }else{

            $paid_currency_amount = $rt->DATA['multi_currency'];
            // 余额是足够的
            $this->multi_currency = $paid_currency_amount;
        }



        $interest_class = interestTypeClass::getInstance($contract_info['repayment_type'], $contract_info['repayment_period']);
        // 重新传数据进去，不然内部变动影响外部的更新
        $new_schema_info = $interest_class->calculateRepaymentInterestOfSchema($scheme_info->toArray(), $contract_info);

        $tradingClass =  new loanRepaymentTradingClass($new_schema_info,null,$this->multi_currency);
        $tradingClass->is_outstanding = 1;
        $tradingClass->is_lock = 1;
        $rt = $tradingClass->execute();
        if (!$rt->STS) {
            return $rt;
        }

        $trading_id = intval($rt->DATA);
        $scheme_info->passbook_trading_id = $trading_id;
        $scheme_info->execute_time = Now();
        $up = $scheme_info->update();
        if (!$up->STS) {
            return new result(false, 'Update schema info fail.', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true);

    }


    public function scriptAutoRepaymentExecuteConfirm()
    {
        $schema_id = $this->schema_info['uid'];
        // 重新获取信息
        $schema_info = (new loan_installment_schemeModel())->getRow($schema_id);

        if( !$schema_info ){
            return new result(false,'No schema info:'.$schema_id,null,errorCodesEnum::NO_DATA);
        }
        if( $schema_info->state == schemaStateTypeEnum::COMPLETE || $schema_info->state == schemaStateTypeEnum::CANCEL ){
            return new result(true);
        }

        $schema_amount = $schema_info['amount'] - $schema_info['actual_payment_amount'];

        if( !$schema_info->passbook_trading_id ){
            return new result(false,'Start first.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $contract_currency = $this->contract_info['currency'];

        return $this->execute($schema_amount,$contract_currency);

    }






}