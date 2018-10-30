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
    'Field' => 'obj_guid',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '对象的全局跨表id',
  ),
  2 => 
  array (
    'Field' => 'allow_multi_contract',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
    'Comment' => '允许多合同',
  ),
  3 => 
  array (
    'Field' => 'account_type',
    'Type' => 'tinyint(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '0:member,10:partner,20:dealer,30:法人',
  ),
  4 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '更新时间',
  ),
  5 => 
  array (
    'Field' => 'repayment_ability',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '还款能力值',
  ),
  6 => 
  array (
    'Field' => 'due_date',
    'Type' => 'varchar(5)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '客户的还款日',
  ),
  7 => 
  array (
    'Field' => 'principal_periods',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '6',
    'Extra' => '',
    'Comment' => 'semi balloon 偿还本金间隔，单位月',
  ),
);