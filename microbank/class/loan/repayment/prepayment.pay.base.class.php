<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/24
 * Time: 11:34
 */
abstract class prepaymentPayBaseClass
{
    protected $contract_info;
    protected $apply_info;
    protected $member_info;
    protected $cutoff_date;
    protected $remaining_schema;

    protected $actual_duct_currency = null;  // 实际扣除金额的账户
    protected $actual_duct_multi_currency = null;

    protected $paid_amount;  // 客户实际支付的金额和币种
    protected $paid_currency;
    protected $exchange_rate=1;
    protected $multi_currency=null;

    public $ref_trade_id = null;

    protected function penaltyHandlerBeforeExecute()
    {
        //处理罚金
        return loan_contractClass::insertPendingHandlePenaltyOfContract($this->contract_info);
    }

    abstract function getPayerInfo();

    abstract function getHandlerInfo();

    abstract function execute();

    protected function recalculateAmount()
    {
        // 重算金额
        $rt = loan_contractClass::prepaymentPreview(array(
            'contract_id' => $this->contract_info['uid'],
            'prepayment_type' => $this->apply_info['prepayment_type'],
            'amount' => $this->apply_info['apply_principal_amount'],
            'repay_period' => $this->apply_info['repay_period'],
            'deadline_date' => $this->apply_info['deadline_date'],
        ));
        if( !$rt->STS ){
            return $rt;
        }

        $prepaymentInfo = $rt->DATA;

        $cutoff_date = $prepaymentInfo['cut_off_date'];
        $paid_total = $prepaymentInfo['total_prepayment_amount'];
        $paid_principal = $prepaymentInfo['total_paid_principal'];
        $paid_interest = $prepaymentInfo['total_paid_interest'];
        $paid_operation_fee = $prepaymentInfo['total_paid_operation_fee'];
        $loss_interest = $prepaymentInfo['loss_interest']?:0;
        $loss_operation_fee = $prepaymentInfo['loss_operation_fee']?:0;
        $remain_schema = $prepaymentInfo['left_schema'];

        $this->remaining_schema = $remain_schema;

        //更新下实际的金额
        $this->apply_info->deadline_date = $cutoff_date;
        $this->apply_info->total_payable_amount = $paid_total;
        $this->apply_info->payable_principal = $paid_principal;
        $this->apply_info->payable_interest = $paid_interest;
        $this->apply_info->payable_operation_fee = $paid_operation_fee;
        $this->apply_info->loss_interest = $loss_interest;
        $this->apply_info->loss_operation_fee = $loss_operation_fee;
        $this->apply_info->update_time = Now();
        $up = $this->apply_info->update();
        if( !$up->STS ){
            return new result(false,'Update apply info fail.',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true);
    }

    protected function allotTypeAmount($des_amount,$amount)
    {

        $principal_amount = 0;
        $interest_amount = 0;
        $operation_fee_amount = 0;

        if( $des_amount['interest'] > 0 ){

            if( $amount <= $des_amount['interest'] ){
                $interest_amount += $amount;
                $des_amount['interest'] -= $amount;
                $amount = 0;
            }else{
                $interest_amount += $des_amount['interest'];
                $des_amount['interest'] = 0;
                $amount -= $interest_amount;
            }

        }

        if( $amount > 0 ){
            if( $des_amount['operation_fee'] > 0 ){

                if( $amount <= $des_amount['operation_fee'] ){
                    $operation_fee_amount += $amount;
                    $des_amount['operation_fee'] -= $amount;
                    $amount = 0;
                }else{
                    $operation_fee_amount += $des_amount['operation_fee'];
                    $des_amount['operation_fee'] = 0;
                    $amount -= $operation_fee_amount;
                }
            }
        }

        $principal_amount += $amount;

        return array(
            'des_amount' => $des_amount,
            'allot_amount' => array(
                'principal' => $principal_amount,
                'interest' => $interest_amount,
                'operation_fee' => $operation_fee_amount
            )
        );

    }

    protected function handle()
    {
        // 先处理罚金
        $rt = $this->penaltyHandlerBeforeExecute();
        if( !$rt->STS ){
            return $rt;
        }

        $contract_info = $this->contract_info;

        // 处理还款
        $contract_id = $this->contract_info['uid'];

        /*** 是否超过期限 ***/
        // todo 先屏蔽日期的判断处理，现在都是及时的
        /*$deadline_date = date('Y-m-d',strtotime($this->apply_info['deadline_date']));
        if( $deadline_date < date('Y-m-d') ){
            return new result(false,'Exceed the deadline:'.$deadline_date,null,errorCodesEnum::UN_MATCH_OPERATION);
        }*/

        $paid_total = round($this->apply_info['total_payable_amount'],2);


        // 还款先换汇，

        $member_id = $this->member_info['uid'];
        $memberObj = new objectMemberClass($member_id);
        $member_balance = $memberObj->getSavingsAccountBalance();   // 先查询好，下面换汇会改变这个余额的

        // 多货币还款
        $padding_currency = null;
        $after_exchange_balance_amount = 0;
        $exchange_trading_remark = "Prepayment exchange currency:".$this->contract_info['contract_sn'];
        $exchanged = array();  // 用于记录日志

        foreach ($this->multi_currency as $k=>$v) {

            if ($v > 0) {

                if( $member_balance[$k] < $v ){
                    return new result(false,'Balance not enough:'.$v.$k,null,errorCodesEnum::BALANCE_NOT_ENOUGH);
                }

                if ($k != $contract_info['currency']) {
                    $trading = new memberExchangeTradingClass($member_id, $v, $k, $contract_info['currency']);
                    $trading->subject = "Prepayment exchange";
                    $trading->remark = $exchange_trading_remark;
                    $rt = $trading->execute();
                    if (!$rt->STS) return $rt;
                    $after_exchange_balance_amount += $trading->exchange_to_amount;
                    $exchanged[$k] = array(
                        'amount'=>$v,
                        'to_amount'=>$trading->exchange_to_amount,
                        'exchange_rate' => $trading->exchange_rate
                    );

                } else {
                    $after_exchange_balance_amount += $v;
                    $exchanged[$k] = array(
                        'amount'=>$v,
                        'to_amount'=>$v,
                        'exchange_rate' => 1
                    );
                }

                $member_balance[$k] -= $v;
            } else {
                $padding_currency = $k;
            }
        }


        if ($after_exchange_balance_amount >= $paid_total) {

            $repayment_amount = $paid_total;

        } else if ($padding_currency == null) {

            $repayment_amount = $after_exchange_balance_amount;

        } else {

            $expected_amount = $paid_total - $after_exchange_balance_amount;

            if ($padding_currency != $contract_info['currency']) {
                // 计算需要换汇的金额
                $exchange_rate = global_settingClass::getCurrencyRateBetween($padding_currency, $contract_info['currency']);
                $need_amount = round($expected_amount / $exchange_rate, 2);
                $exchange_to_amount = round($need_amount * $exchange_rate, 2);
                if ($exchange_to_amount < $expected_amount)
                    $need_amount += 0.01;

                $exchange_from_amount = $need_amount;
                // 换汇金额不足
                if( $member_balance[$padding_currency] < $exchange_from_amount ){
                    return new result(false,'Balance not enough-2:'.$exchange_from_amount.$padding_currency,null,errorCodesEnum::BALANCE_NOT_ENOUGH);
                }
                $member_balance[$padding_currency] -= $exchange_from_amount;

                // 换汇交易
                $trading = new memberExchangeTradingClass($member_id, $need_amount, $padding_currency, $contract_info['currency']);
                $trading->subject = "Prepayment exchange";
                $trading->remark = $exchange_trading_remark;
                $rt = $trading->execute();
                if (!$rt->STS) return $rt;
                $repayment_amount = $after_exchange_balance_amount + $trading->exchange_to_amount;
                $exchanged[$padding_currency] = array(
                    'amount'=>$exchange_from_amount,
                    'to_amount'=>$trading->exchange_to_amount,
                    'exchange_rate' => $trading->exchange_rate
                );
            } else {

                if( $member_balance[$padding_currency] < $expected_amount ){
                    return new result(false,'Balance not enough-3:'.$expected_amount.$padding_currency,null,errorCodesEnum::BALANCE_NOT_ENOUGH);
                }

                $repayment_amount = $paid_total;
                $exchanged[$padding_currency] = array(
                    'amount'=>$expected_amount,
                    'to_amount'=>$expected_amount,
                    'exchange_rate' => 1
                );

            }
        }

        if( $repayment_amount < $paid_total ){
            return new result(false,'Balance not enough-42:'.$repayment_amount.'-'.$paid_total,array(
                'repayment_amount' => $repayment_amount,
                'paid_total' => $paid_total,
                'is_true' => ($repayment_amount < $paid_total)?true:false
            ),errorCodesEnum::BALANCE_NOT_ENOUGH);
        }


        // 还款,合同币种还款
        $tradingClass = (new loanPrepaymentTradingClass(
            $this->contract_info['uid'],
            $this->apply_info['total_payable_amount'],
            $this->apply_info['payable_principal'],
            $this->apply_info['payable_interest'],
            $this->apply_info['payable_operation_fee'],
            $this->apply_info['loss_interest'],
            $this->apply_info['loss_operation_fee'],
            $contract_info['currency'],
            null
        ));
        $tradingClass->remark = "Loan prepayment: ".$this->contract_info['contract_sn'];
        $tradingClass->ref_trade_id = $this->ref_trade_id;
        $rt = $tradingClass->execute();
        if( !$rt->STS ){
            return $rt;
        }


        // 添加还款日志
        $payerInfo = $this->getPayerInfo();
        $handler_info = $this->getHandlerInfo();

        $m_log = new loan_repaymentModel();

        $des_type_amount = array(
            'principal' => $this->apply_info['payable_principal'],
            'interest' => $this->apply_info['payable_interest'],
            'operation_fee' => $this->apply_info['loss_operation_fee']
        );


        foreach( $exchanged as $c=>$item ){

            // 分配分类金额
            $data = $this->allotTypeAmount($des_type_amount,$item['to_amount']);
            $des_type_amount = $data['des_amount'];
            $allot_amount = $data['allot_amount'];

            $log = $m_log->newRow();
            $log->scheme_id = 0;
            $log->contract_id = $this->contract_info['uid'];
            $log->currency = $contract_info['currency'];
            $log->receivable_amount = $item['to_amount'];
            $log->penalty_amount = 0;
            $log->amount = $item['to_amount'];
            $log->interest_amount = $allot_amount['interest'];
            $log->operation_fee_amount = $allot_amount['operation_fee'];
            $log->principal_amount = $allot_amount['principal'];
            $log->overdue_days = 0;
            $log->payer_id = $payerInfo['uid'];
            $log->payer_type = $payerInfo['handler_type'];
            $log->payer_name = $payerInfo['handler_name'];
            $log->payer_phone = $payerInfo['handler_phone'];
            $log->payer_account = $payerInfo['handler_account'];
            $log->payer_property = $payerInfo['handler_property'];
            $log->payer_amount = $item['amount'];
            $log->payer_currency = $c;
            $log->payer_exchange_rate = $item['exchange_rate'];
            $log->create_time = Now();
            $log->creator_id = $handler_info['handler_id'];
            $log->creator_name = $handler_info['handler_name'];
            $log->state = loanRepaymentStateEnum::SUCCESS;
            $insert = $log->insert();
            if (!$insert->STS) {
                return new result(false, 'Repayment fail', null, errorCodesEnum::DB_ERROR);
            }
        }


        // 更新原来的未还清计划为作废计划
        $sql = "update loan_installment_scheme set state='".schemaStateTypeEnum::CANCEL."' where 
         contract_id='".$this->contract_info['uid']."' and state!='".schemaStateTypeEnum::COMPLETE."' ";
        $up = $m_log->conn->execute($sql);
        if( !$up->STS ){
            return new result(false,'Cancel schema fail.',null,errorCodesEnum::DB_ERROR);
        }


        // 插入新计划
        $rt = loan_contractClass::insertNewInstallmentSchemaAfterPrepayment($this->contract_info,$this->remaining_schema);
        if( !$rt->STS ){
            return $rt;
        }


        //更新合同
        $rt = loan_contractClass::updateContractStateAfterRepayment($contract_id);
        if( !$rt->STS ){
            return $rt;
        }


        // 更新申请状态
        $this->apply_info->state = loanPenaltyHandlerStateEnum::DONE;
        $this->apply_info->handler_id =  $handler_info['handler_id'];
        $this->apply_info->handler_name = $handler_info['handler_name'];
        $this->apply_info->handle_remark = "Loan prepayment";
        $this->apply_info->handle_time = Now();
        $this->apply_info->update_time = Now();
        $up = $this->apply_info->update();
        if( !$up->STS ){
            return new result(false,'Update apply fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$this->apply_info);
    }

}