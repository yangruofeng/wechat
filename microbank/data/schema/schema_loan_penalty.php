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
    'Field' => 'contract_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '合同ID',
  ),
  3 => 
  array (
    'Field' => 'scheme_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '产生这条罚金的还款计划ID',
  ),
  4 => 
  array (
    'Field' => 'currency',
    'Type' => 'varchar(20)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'currency',
  ),
  5 => 
  array (
    'Field' => 'penalty_amount',
    'Type' => 'decimal(20,2)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '罚金金额',
  ),
  6 => 
  array (
    'Field' => 'state',
    'Type' => 'tinyint(4)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '罚金状态：0: 未处理/100: 已处理',
  ),
  7 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建时间',
  ),
  8 => 
  array (
    'Field' => 'lmt',
    'Type' => 'timestamp',
    'Null' => 'NO',
    'Key' => '',
    'Default' => 'CURRENT_TIMESTAMP',
    'Extra' => 'on update CURRENT_TIMESTAMP',
    'Comment' => '修改时间',
  ),
);