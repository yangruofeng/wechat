<?php
$static_schema=array (
  0 => 
  array (
    'Field' => 'uid',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
    'Comment' => 'ID',
  ),
  1 => 
  array (
    'Field' => 'contract_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'loan_contract.uid',
  ),
  2 => 
  array (
    'Field' => 'scheme_idx',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '收款计划序号',
  ),
  3 => 
  array (
    'Field' => 'scheme_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '项名',
  ),
  4 => 
  array (
    'Field' => 'initial_principal',
    'Type' => 'decimal(14,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '期初本金',
  ),
  5 => 
  array (
    'Field' => 'interest_date',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '本期起息日期',
  ),
  6 => 
  array (
    'Field' => 'receivable_date',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '应收日期',
  ),
  7 => 
  array (
    'Field' => 'penalty_start_date',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '罚期',
  ),
  8 => 
  array (
    'Field' => 'receivable_principal',
    'Type' => 'decimal(14,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '应收本金',
  ),
  9 => 
  array (
    'Field' => 'receivable_interest',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '应收利息',
  ),
  10 => 
  array (
    'Field' => 'receivable_operation_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '应收营运费用',
  ),
  11 => 
  array (
    'Field' => 'receivable_admin_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '应收管理费用',
  ),
  12 => 
  array (
    'Field' => 'recovery_amount',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => ' ',
  ),
  13 => 
  array (
    'Field' => 'ref_amount',
    'Type' => 'decimal(14,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '理论应还金额',
  ),
  14 => 
  array (
    'Field' => 'amount',
    'Type' => 'decimal(14,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '应收金额',
  ),
  15 => 
  array (
    'Field' => 'settle_penalty',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '结算罚金，本息还清后结算的罚金',
  ),
  16 => 
  array (
    'Field' => 'deduction_penalty',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '减免罚金',
  ),
  17 => 
  array (
    'Field' => 'actual_payment_amount',
    'Type' => 'decimal(14,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '实际还款金额',
  ),
  18 => 
  array (
    'Field' => 'paid_operation_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '已还operation fee',
  ),
  19 => 
  array (
    'Field' => 'paid_interest',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '已还利息',
  ),
  20 => 
  array (
    'Field' => 'paid_principal',
    'Type' => 'decimal(14,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '已还本金',
  ),
  21 => 
  array (
    'Field' => 'paid_penalty',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '已还罚金',
  ),
  22 => 
  array (
    'Field' => 'account_handler_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '收款处理方式',
  ),
  23 => 
  array (
    'Field' => 'state',
    'Type' => 'tinyint(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '状态',
  ),
  24 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建时间',
  ),
  25 => 
  array (
    'Field' => 'execute_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '执行时间',
  ),
  26 => 
  array (
    'Field' => 'last_repayment_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '上次还款时间',
  ),
  27 => 
  array (
    'Field' => 'done_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '完成时间',
  ),
  28 => 
  array (
    'Field' => 'passbook_trading_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '交易ID',
  ),
  29 => 
  array (
    'Field' => 'lock_currency',
    'Type' => 'varchar(1000)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '锁定的金额',
  ),
);