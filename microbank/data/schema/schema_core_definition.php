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
    'Field' => 'category',
    'Type' => 'varchar(50)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '分类',
  ),
  2 => 
  array (
    'Field' => 'category_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '分类名',
  ),
  3 => 
  array (
    'Field' => 'category_name_json',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '分类名多语言',
  ),
  4 => 
  array (
    'Field' => 'item_name',
    'Type' => 'varchar(50)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '项名称',
  ),
  5 => 
  array (
    'Field' => 'item_name_json',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '项名称多语言',
  ),
  6 => 
  array (
    'Field' => 'item_code',
    'Type' => 'varchar(20)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'Short Code',
  ),
  7 => 
  array (
    'Field' => 'item_desc',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '描述',
  ),
  8 => 
  array (
    'Field' => 'item_value',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '值',
  ),
  9 => 
  array (
    'Field' => 'is_system',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
);