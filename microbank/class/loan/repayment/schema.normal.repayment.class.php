<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/2
 * Time: 11:40
 */
abstract class schemaNormalRepaymentClass
{

    protected $schema_info;
    protected $contract_info;
    protected $member_info;
    protected $penalty;
    protected $paid_currency;
    protected $multi_currency=null;
    protected $is_schema_amount_repayment=0;
    public $ref_trade_id = null;

    abstract function getPayerInfo();
    abstract function getCreatorInfo();

    abstract function repaymentExecute();

    /**
     * 交易确认
     */
    public static function tradingConfirm($tradingId,$signs=array())
    {
        return loanRepaymentTradingClass::confirm($tradingId,$signs);
    }

    /**
     * 交易取消
     */
    public static function tradingCancel($tradingId,$signs=array())
    {
        return loanRepaymentTradingClass::reject($tradingId,$signs);
    }

    public function allotAmountForAllType($paid_amount,$schema_info)
    {
        $receivable_principal = $schema_info['receivable_principal'];
        $receivable_interest = $schema_info['receivable_interest'];
        $receivable_operation = $schema_info['receivable_operation_fee'];
        $paid_principal = $schema_info['paid_principal'];
        $paid_interest = $schema_info['paid_interest'];
        $paid_operation_fee = $schema_info['paid_operation_fee'];

        $left_interest = $receivable_interest - $paid_interest;
        $left_operation_fee = $receivable_operation - $paid_operation_fee;
        $left_principal = $receivable_principal-$paid_principal;

        // 矫正
        if( $left_interest < 0 ){
            $left_interest = 0;
        }
        if( $left_operation_fee < 0 ){
            $left_operation_fee = 0;
        }
        if( $left_principal < 0 ){
            $left_principal = 0;
        }



        $current_principal = 0;
        $current_interest = 0;
        $current_operation_fee = 0;

        // 优先利息
        if( $paid_amount > 0 ){

            if( $paid_amount <= $left_interest  ){
                $current_interest += $paid_amount;
                $paid_amount = 0;
            }else{
                $current_interest += $left_interest;
                $paid_amount -= $current_interest;
            }
        }

        // operation fee
        if( $paid_amount > 0 ){

            if( $paid_amount <= $left_operation_fee ){
                $current_operation_fee += $paid_amount;
                $paid_amount = 0;
            }else{
                $current_operation_fee = $left_operation_fee;
                $paid_amount -= $current_operation_fee;
            }
        }

        // principal
        if( $paid_amount > 0 ){

            if( $paid_amount <= $left_principal ){
                $current_principal += $paid_amount;
                $paid_amount = 0;
            }else{
                $current_principal += $left_principal;
                $paid_amount -= $current_principal;
            }
        }


        $schema_info['paid_principal'] += $current_principal;
        $schema_info['paid_interest'] += $current_interest;
        $schema_info['paid_operation_fee'] += $current_operation_fee;
        $schema_info['actual_payment_amount'] += $current_operation_fee+$current_interest+$current_principal;

        return array(
            'principal' => $current_principal,
            'interest' => $current_interest,
            'operation_fee' => $current_operation_fee,
            'left_amount' => $paid_amount,
            'new_schema_info' => $schema_info
        );

    }

    protected function execute($paid_amount=null, $paid_currency=null)
    {
        $scheme_info = $this->schema_info;
        if ($scheme_info->state == schemaStateTypeEnum::COMPLETE || $scheme_info->state == schemaStateTypeEnum::CANCEL) {
            return new result(true, 'success');
        }

        //$original_schema_array = $scheme_info->toArray();  // 必须用数组的方式,并且在schema更新前保存

        $contract_info = $this->contract_info;
        $contract_interest_info = loan_contractClass::getContractInterestInfoByContractInfo($contract_info);

        if(  !loan_contractClass::loanContractIsUnderExecuting($contract_info) ){
            return new result(false, 'Invalid contract state:' . $contract_info->contract_sn, null, errorCodesEnum::UNEXPECTED_DATA);
        }

        $penalty = $this->penalty;

        $payerInfo = $this->getPayerInfo();
        if (empty($payerInfo)) {
            return new result(false, 'No handler info. ', null, errorCodesEnum::NO_ACCOUNT_HANDLER);
        }

        $creatorInfo = $this->getCreatorInfo();

        $interest_class = interestTypeClass::getInstance($contract_info['repayment_type'], $contract_info['repayment_period']);


        $new_schema_info = $interest_class->calculateRepaymentInterestOfSchema($scheme_info->toArray(), $contract_interest_info);
        $schema_remaining_amount = $new_schema_info['amount'] - $new_schema_info['actual_payment_amount'];

        // member_id在创建换汇交易、查询余额时用到
        $member_id = $this->member_info['uid'];
        $memberObj = new objectMemberClass($member_id);
        $member_balance = $memberObj->getSavingsAccountBalance();   // 先查询好，下面换汇会改变这个余额的

        // 先执行换汇
        $after_exchange_balance_amount = 0;
        $exchange_trading_remark = "Repayment exchange currency:".$this->contract_info['contract_sn'];
        $exchanged = array();  // 用于记录日志
        if ($paid_currency == null) {
            // 多货币还款
            $padding_currency = null;
            foreach ($this->multi_currency as $k=>$v) {
                if ($v > 0) {
                    if ($k != $contract_info['currency']) {
                        $trading = new memberExchangeTradingClass($member_id, $v, $k, $contract_info['currency']);
                        $trading->subject = "Repayment exchange";
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
                } else {
                    $padding_currency = $k;
                }
            }

            if ($after_exchange_balance_amount >= $schema_remaining_amount) {
                $repayment_amount = $schema_remaining_amount;
            } else if ($padding_currency == null) {
                $repayment_amount = $after_exchange_balance_amount;
            } else {
                $expected_amount = $schema_remaining_amount - $after_exchange_balance_amount;

                if ($padding_currency != $contract_info['currency']) {
                    // 计算需要换汇的金额
                    $exchange_rate = global_settingClass::getCurrencyRateBetween($padding_currency, $contract_info['currency']);
                    $need_amount = round($expected_amount / $exchange_rate, 2);
                    $exchange_to_amount = round($need_amount * $exchange_rate, 2);
                    if ($exchange_to_amount < $expected_amount)
                        $need_amount += 0.01;

                    // 最多换汇$need_amount
                    if ($member_balance[$padding_currency] >= $need_amount) {
                        $exchange_from_amount = $need_amount;
                    } else {
                        $exchange_from_amount = $member_balance[$padding_currency];
                    }

                    // 换汇交易
                    $trading = new memberExchangeTradingClass($member_id, $exchange_from_amount, $padding_currency, $contract_info['currency']);
                    $trading->subject = "Repayment exchange";
                    $trading->remark = $exchange_trading_remark;                    $rt = $trading->execute();
                    if (!$rt->STS) return $rt;
                    $repayment_amount = $after_exchange_balance_amount + $trading->exchange_to_amount;
                    $exchanged[$padding_currency] = array(
                        'amount'=>$exchange_from_amount,
                        'to_amount'=>$trading->exchange_to_amount,
                        'exchange_rate' => $trading->exchange_rate
                    );
                } else {
                    if ($member_balance[$padding_currency] >= $expected_amount) {
                        $repayment_amount = $schema_remaining_amount;
                        $exchanged[$padding_currency] = array(
                            'amount'=>$expected_amount,
                            'to_amount'=>$expected_amount,
                            'exchange_rate' => 1
                        );
                    } else {
                        $repayment_amount = $after_exchange_balance_amount + $member_balance[$padding_currency];
                        $exchanged[$padding_currency] = array(
                            'amount'=>$member_balance[$padding_currency],
                            'to_amount'=>$member_balance[$padding_currency],
                            'exchange_rate' => 1
                        );
                    }
                }
            }
        } else {
            // 单货币还款
            if ($paid_currency != $contract_info['currency']) {
                // 计算需要换汇的金额
                $exchange_rate = global_settingClass::getCurrencyRateBetween($paid_currency, $contract_info['currency']);
                $need_amount = round($schema_remaining_amount / $exchange_rate, 2);
                $exchange_to_amount = round($need_amount * $exchange_rate, 2);
                if ($exchange_to_amount < $schema_remaining_amount)
                    $need_amount += 0.01;

                // 最多换汇$need_amount
                if ($paid_amount >= $need_amount) {
                    $exchange_from_amount = $need_amount;
                } else {
                    $exchange_from_amount = $paid_amount;
                }

                // 换汇交易
                $trading = new memberExchangeTradingClass($member_id, $exchange_from_amount, $paid_currency, $contract_info['currency']);
                $trading->subject = "Repayment exchange";
                $trading->remark = $exchange_trading_remark;
                $rt = $trading->execute();
                if (!$rt->STS) return $rt;
                $repayment_amount = $trading->exchange_to_amount;

                $exchanged[$paid_currency] = array(
                    'amount'=>$exchange_from_amount,
                    'to_amount'=>$trading->exchange_to_amount,
                    'exchange_rate' => $trading->exchange_rate
                );
            } else {
                if ($paid_amount >= $schema_remaining_amount) {
                    $repayment_amount = $schema_remaining_amount;
                    $exchanged[$paid_currency] = array(
                        'amount'=>$schema_remaining_amount,
                        'to_amount'=>$schema_remaining_amount,
                        'exchange_rate' => 1
                    );
                } else {
                    $repayment_amount = $paid_amount;
                    $exchanged[$paid_currency] = array(
                        'amount'=>$paid_amount,
                        'to_amount'=>$paid_amount,
                        'exchange_rate' => 1
                    );
                }
            }
        }

        if ($repayment_amount > 0) {
            // 执行还款
            $tradingClass =  new loanRepaymentTradingClass($new_schema_info, $repayment_amount);
            $tradingClass->ref_trade_id = $this->ref_trade_id;
            $rt = $tradingClass->execute();
            if (!$rt->STS) {
                return $rt;
            }

            // 更新计划状态
            $now = Now();

            // 分配本次金额
            $re_data = $this->allotAmountForAllType($repayment_amount,$new_schema_info);
            $scheme_info->paid_interest += $re_data['interest'];
            $scheme_info->paid_operation_fee += $re_data['operation_fee'];
            $scheme_info->paid_principal += $re_data['principal'];

            $scheme_info->actual_payment_amount += $repayment_amount;
            $scheme_info->settle_penalty += $penalty;
            $scheme_info->execute_time = $now;
            $scheme_info->last_repayment_time = $now;
            // 判断还清的方式有变
            if( $repayment_amount >= $schema_remaining_amount ){
                $scheme_info->state = schemaStateTypeEnum::COMPLETE;
                $scheme_info->done_time = $now;
            }else{
                $scheme_info->state = schemaStateTypeEnum::GOING;
            }

            $up = $scheme_info->update();
            if (!$up->STS) {
                return new result(false, 'Update schema info fail.', null, errorCodesEnum::DB_ERROR);
            }

            // 处理anytime single的问题
            if( $contract_info['repayment_type'] == interestPaymentEnum::ANYTIME_SINGLE_REPAYMENT ){

                // 此次偿还了本金，没有偿还本金就继续按照原计划进行还款
                if( $re_data['principal'] > 0 &&  $scheme_info->state != schemaStateTypeEnum::COMPLETE ){

                    // 偿还了本金，需要重新计算利息
                    $new_principal = $scheme_info['receivable_principal']-$scheme_info['paid_principal'];

                    if( $new_principal > 0 ){
                        $left_days = (int)((strtotime($scheme_info['receivable_date']) - strtotime(date('Y-m-d'))) / (24 * 3600));

                        $period_info = $interest_class->getRepaymentPeriods($left_days,loanPeriodUnitEnum::DAY,
                            date('Y-m-d'),$scheme_info['receivable_date']);
                        $rt = $interest_class->getInstallmentSchema($new_principal,$period_info,$contract_interest_info);
                        if( !$rt->STS ){
                            return $rt;
                        }
                        $new_repayment_schema = $rt->DATA['payment_schema'];
                        $new_installment_schema = current($new_repayment_schema);
                        // 原计划作废
                        $scheme_info->state = schemaStateTypeEnum::CANCEL;
                        $scheme_info->execute_time = Now();
                        $up = $scheme_info->update();
                        if( !$up->STS ){
                            return new result(false,'Cancel schema fail.',null,errorCodesEnum::DB_ERROR);
                        }

                        // 重新插入新计划
                        $new_schema_row = (new loan_installment_schemeModel())->newRow();
                        $new_schema_row->contract_id = $scheme_info['contract_id'];
                        $new_schema_row->scheme_idx = $scheme_info['scheme_idx']+1;
                        $new_schema_row->scheme_name = 'Period '.$new_schema_row->scheme_idx;
                        $new_schema_row->initial_principal = $new_installment_schema['initial_principal'];
                        $new_schema_row->interest_date = date('Y-m-d');
                        $new_schema_row->receivable_date = $scheme_info['receivable_date'];
                        $new_schema_row->penalty_start_date = $scheme_info['penalty_start_date'];
                        $new_schema_row->receivable_principal = $new_installment_schema['receivable_principal'];
                        $new_schema_row->receivable_interest = $new_installment_schema['receivable_interest'];
                        $new_schema_row->receivable_operation_fee = $new_installment_schema['receivable_operation_fee'];
                        $new_schema_row->receivable_admin_fee = 0;
                        $new_schema_row->ref_amount = $new_installment_schema['amount'];
                        $new_schema_row->amount = $new_installment_schema['amount'];
                        $new_schema_row->account_handler_id = $scheme_info['account_handler_id'];
                        $new_schema_row->state = schemaStateTypeEnum::CREATE;
                        $new_schema_row->create_time = Now();
                        $insert = $new_schema_row->insert();
                        if( !$insert->STS ){
                            return new result(false,'Insert new schema fail:'.$insert->MSG,null,
                                errorCodesEnum::DB_ERROR);
                        }

                    }

                }

            }



            // 添加罚金记录
            if ($penalty > 0) {
                $penalty_model = new loan_penaltyModel();
                $penalty_info = $penalty_model->newRow();
                $penalty_info->account_id = $contract_info->account_id;
                $penalty_info->contract_id = $contract_info->uid;
                $penalty_info->scheme_id = $scheme_info->uid;
                $penalty_info->currency = $contract_info->currency;
                $penalty_info->penalty_amount = $penalty;
                $penalty_info->state = loanPenaltyHandlerStateEnum::CREATE;
                $penalty_info->create_time = date("Y-m-d H:i:s");
                $insert = $penalty_info->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add penalty failed', null, errorCodesEnum::DB_ERROR);
                }
            }

            $m_log = new loan_repaymentModel();

            // 计算逾期天数
            $overdue_days = ceil( (strtotime(date('Y-m-d'))-strtotime($scheme_info['receivable_date']) )/86400 );
            if( $overdue_days < 0 ){
                $overdue_days = 0;
            }

            // 记录真实的扣款行为
            if( !empty($exchanged) ){

                $temp_schema_info = $new_schema_info;  // 每次计算后重置
                foreach( $exchanged as $c=>$item ){

                    $allot_data = $this->allotAmountForAllType($item['to_amount'],$temp_schema_info);
                    $temp_schema_info = $allot_data['new_schema_info'];

                    $log = $m_log->newRow();
                    $log->scheme_id = $scheme_info['uid'];
                    $log->contract_id = $contract_info->uid;
                    $log->currency = $contract_info['currency'];
                    $log->receivable_amount = $item['to_amount'];
                    $log->penalty_amount = 0;  // 罚金提走
                    $log->amount = $item['to_amount'];
                    $log->interest_amount = $allot_data['interest'];
                    $log->operation_fee_amount = $allot_data['operation_fee'];
                    $log->principal_amount = $allot_data['principal'];
                    $log->overdue_days = $overdue_days;
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
                    $log->creator_id = $creatorInfo['creator_id'];
                    $log->creator_name = $creatorInfo['creator_name'];
                    $log->branch_id = $creatorInfo['branch_id'];
                    $log->teller_id = $creatorInfo['teller_id'];
                    $log->teller_name = $creatorInfo['teller_name'];
                    $log->state = loanRepaymentStateEnum::SUCCESS;
                    $insert = $log->insert();
                    if (!$insert->STS) {
                        return new result(false, 'Repayment fail', null, errorCodesEnum::DB_ERROR);
                    }
                }

            }else{

                $paid_currency = $paid_currency?:$contract_info['currency'];
                $allot_data = $this->allotAmountForAllType($repayment_amount,$new_schema_info);

                $log = $m_log->newRow();
                $log->scheme_id = $scheme_info['uid'];
                $log->contract_id = $contract_info->uid;
                $log->currency = $contract_info['currency'];
                $log->receivable_amount = $repayment_amount;
                $log->penalty_amount = 0;  // 罚金提走
                $log->amount = $repayment_amount;
                $log->interest_amount += $allot_data['interest'];
                $log->operation_fee_amount += $allot_data['operation_fee'];
                $log->principal_amount += $allot_data['principal'];
                $log->overdue_days = $overdue_days;
                $log->payer_id = $payerInfo['uid'];
                $log->payer_type = $payerInfo['handler_type'];
                $log->payer_name = $payerInfo['handler_name'];
                $log->payer_phone = $payerInfo['handler_phone'];
                $log->payer_account = $payerInfo['handler_account'];
                $log->payer_property = $payerInfo['handler_property'];
                $log->payer_amount = $exchanged[$paid_currency]['amount'];
                $log->payer_currency = $paid_currency;
                $log->payer_exchange_rate = $exchanged[$paid_currency]['exchange_rate'];
                $log->create_time = Now();
                $log->creator_id = $creatorInfo['creator_id'];
                $log->creator_name = $creatorInfo['creator_name'];
                $log->branch_id = $creatorInfo['branch_id'];
                $log->teller_id = $creatorInfo['teller_id'];
                $log->teller_name = $creatorInfo['teller_name'];
                $log->state = loanRepaymentStateEnum::SUCCESS;
                $insert = $log->insert();
                if (!$insert->STS) {
                    return new result(false, 'Repayment fail', null, errorCodesEnum::DB_ERROR);
                }
            }




            // 合同已还清
            $re = loan_contractClass::updateContractStateAfterRepayment($contract_info->uid);
            if (!$re->STS) {
                return $re;
            }

        }

        return new result(true, 'success');
    }

}