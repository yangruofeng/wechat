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
    'Comment' => '',
  ),
  1 => 
  array (
    'Field' => 'module_code',
    'Type' => 'varchar(100)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '模块code',
  ),
  2 => 
  array (
    'Field' => 'platform',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '平台,如counter,app',
  ),
  3 => 
  array (
    'Field' => 'is_close',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '是否关闭',
  ),
  4 => 
  array (
    'Field' => 'is_new',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '是否新增',
  ),
  5 => 
  array (
    'Field' => 'is_show',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
    'Comment' => '是否展示',
  ),
  6 => 
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