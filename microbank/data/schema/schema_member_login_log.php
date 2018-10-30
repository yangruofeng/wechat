<?php
$static_schema=array (
  0 => 
  array (
    'Field' => 'uid',
    'Type' => 'int(11) unsigned',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
    'Comment' => '日志ID',
  ),
  1 => 
  array (
    'Field' => 'member_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '用户id',
  ),
  2 => 
  array (
    'Field' => 'client_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '终端id',
  ),
  3 => 
  array (
    'Field' => 'client_type',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '终端类型',
  ),
  4 => 
  array (
    'Field' => 'device_id',
    'Type' => 'varchar(200)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '设备号',
  ),
  5 => 
  array (
    'Field' => 'device_name',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '设备名称',
  ),
  6 => 
  array (
    'Field' => 'login_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '登录时间',
  ),
  7 => 
  array (
    'Field' => 'login_ip',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '登录ip',
  ),
  8 => 
  array (
    'Field' => 'login_area',
    'Type' => 'varchar(200)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '登录区域',
  ),
  9 => 
  array (
    'Field' => 'logout_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '退出时间',
  ),
  10 => 
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