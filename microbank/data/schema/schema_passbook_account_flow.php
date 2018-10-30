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
    'Comment' => '流水ID',
  ),
  1 => 
  array (
    'Field' => 'account_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '余额ID',
  ),
  2 => 
  array (
    'Field' => 'begin_balance',
    'Type' => 'decimal(20,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '开始余额',
  ),
  3 => 
  array (
    'Field' => 'credit',
    'Type' => 'decimal(20,2)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '贷方记账金额',
  ),
  4 => 
  array (
    'Field' => 'debit',
    'Type' => 'decimal(20,2)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '借方记账金额',
  ),
  5 => 
  array (
    'Field' => 'end_balance',
    'Type' => 'decimal(20,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '结束余额',
  ),
  6 => 
  array (
    'Field' => 'subject',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  7 => 
  array (
    'Field' => 'remark',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '备注',
  ),
  8 => 
  array (
    'Field' => 'trade_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '交易记录ID book_trading.uid',
  ),
  9 => 
  array (
    'Field' => 'ref_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '交易关联记录ID book_trading.uid',
  ),
  10 => 
  array (
    'Field' => 'exchange_rate',
    'Type' => 'decimal(18,10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  11 => 
  array (
    'Field' => 'base_currency',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  12 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建时间',
  ),
  13 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '更新时间',
  ),
  14 => 
  array (
    'Field' => 'state',
    'Type' => 'tinyint(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '状态',
  ),
);