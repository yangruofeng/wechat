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
    'Field' => 'biz_code',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '业务类型',
  ),
  2 => 
  array (
    'Field' => 'scene_code',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '业务场景',
  ),
  3 => 
  array (
    'Field' => 'bill_code',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '单号',
  ),
  4 => 
  array (
    'Field' => 'member_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'member id',
  ),
  5 => 
  array (
    'Field' => 'bank_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'site_bank.uid',
  ),
  6 => 
  array (
    'Field' => 'bank_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '银行名字',
  ),
  7 => 
  array (
    'Field' => 'bank_account_no',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'bank_account_no',
  ),
  8 => 
  array (
    'Field' => 'currency',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '货币',
  ),
  9 => 
  array (
    'Field' => 'amount',
    'Type' => 'decimal(20,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '总额',
  ),
  10 => 
  array (
    'Field' => 'remark',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '备注',
  ),
  11 => 
  array (
    'Field' => 'pay_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '还款时间',
  ),
  12 => 
  array (
    'Field' => 'state',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '状态 0新建    100完成',
  ),
  13 => 
  array (
    'Field' => 'operator_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '处理人ID',
  ),
  14 => 
  array (
    'Field' => 'operator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '处理人名称',
  ),
  15 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建时间',
  ),
  16 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '更新时间',
  ),
  17 => 
  array (
    'Field' => 'branch_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '分行ID',
  ),
);