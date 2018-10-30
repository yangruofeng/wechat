<?php
$static_schema=array (
  0 => 
  array (
    'Field' => 'uid',
    'Type' => 'int(10) unsigned',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
    'Comment' => '',
  ),
  1 => 
  array (
    'Field' => 'account_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'member_id',
  ),
  2 => 
  array (
    'Field' => 'receivable',
    'Type' => 'decimal(10,2)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '应收',
  ),
  3 => 
  array (
    'Field' => 'deducting',
    'Type' => 'decimal(10,2)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '减扣',
  ),
  4 => 
  array (
    'Field' => 'paid',
    'Type' => 'decimal(20,2)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '实收',
  ),
  5 => 
  array (
    'Field' => 'currency',
    'Type' => 'varchar(20)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'currency',
  ),
  6 => 
  array (
    'Field' => 'remark',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '备注',
  ),
  7 => 
  array (
    'Field' => 'state',
    'Type' => 'tinyint(4)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '状态',
  ),
  8 => 
  array (
    'Field' => 'creator_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建者ID',
  ),
  9 => 
  array (
    'Field' => 'creator_name',
    'Type' => 'varchar(50)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建者名称',
  ),
  10 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建时间',
  ),
  11 => 
  array (
    'Field' => 'auditor_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '审核人ID',
  ),
  12 => 
  array (
    'Field' => 'auditor_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '审核人名称',
  ),
  13 => 
  array (
    'Field' => 'audit_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '审核时间',
  ),
  14 => 
  array (
    'Field' => 'audit_remark',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '审核备注',
  ),
);