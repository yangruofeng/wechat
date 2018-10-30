<?php
$static_schema=array (
  0 => 
  array (
    'Field' => 'uid',
    'Type' => 'int(10)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
    'Comment' => '',
  ),
  1 => 
  array (
    'Field' => 'member_id',
    'Type' => 'int(10)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  2 => 
  array (
    'Field' => 'title',
    'Type' => 'varchar(40)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '标题',
  ),
  3 => 
  array (
    'Field' => 'ext_type',
    'Type' => 'int(2)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '扩展类型：0->证件，1=>收入，2=>支出',
  ),
  4 => 
  array (
    'Field' => 'ext_amount',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '增减金额',
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
    'Type' => 'int(10)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  7 => 
  array (
    'Field' => 'operator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  8 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  9 => 
  array (
    'Field' => 'update_operator_id',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '记录最后更新人',
  ),
  10 => 
  array (
    'Field' => 'update_operator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  11 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  12 => 
  array (
    'Field' => 'relative_id',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '关系人id',
  ),
  13 => 
  array (
    'Field' => 'relative_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => 'Own',
    'Extra' => '',
    'Comment' => '关系人姓名',
  ),
  14 => 
  array (
    'Field' => 'state',
    'Type' => 'int(2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '100=>granted',
  ),
  15 => 
  array (
    'Field' => 'request_id',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  16 => 
  array (
    'Field' => 'grant_id',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
);