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
    'Field' => 'address_type',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '地址类别',
  ),
  2 => 
  array (
    'Field' => 'member_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '客户ID',
  ),
  3 => 
  array (
    'Field' => 'coord_x',
    'Type' => 'decimal(12,7)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.0000000',
    'Extra' => '',
    'Comment' => '经度',
  ),
  4 => 
  array (
    'Field' => 'coord_y',
    'Type' => 'decimal(12,7)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.0000000',
    'Extra' => '',
    'Comment' => '纬度',
  ),
  5 => 
  array (
    'Field' => 'location',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '详细位置',
  ),
  6 => 
  array (
    'Field' => 'user_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '用户ID',
  ),
  7 => 
  array (
    'Field' => 'user_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '用户名字',
  ),
  8 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '时间',
  ),
);