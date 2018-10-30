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
    'Comment' => 'id',
  ),
  1 => 
  array (
    'Field' => 'member_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'client_member.uid',
  ),
  2 => 
  array (
    'Field' => 'asset_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '资产ID，member_assets.uid',
  ),
  3 => 
  array (
    'Field' => 'renter',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '承租人',
  ),
  4 => 
  array (
    'Field' => 'monthly_rent',
    'Type' => 'decimal(12,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '月租',
  ),
  5 => 
  array (
    'Field' => 'remark',
    'Type' => 'varchar(200)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '备注',
  ),
  6 => 
  array (
    'Field' => 'operator_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '操作人',
  ),
  7 => 
  array (
    'Field' => 'operator_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '操作人类型',
  ),
  8 => 
  array (
    'Field' => 'operator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '操作人名字',
  ),
  9 => 
  array (
    'Field' => 'update_operator_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '操作人',
  ),
  10 => 
  array (
    'Field' => 'update_operator_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '操作人类型',
  ),
  11 => 
  array (
    'Field' => 'update_operator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '操作人名字',
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
);