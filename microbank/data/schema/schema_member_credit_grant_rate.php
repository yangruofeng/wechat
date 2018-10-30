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
    'Field' => 'credit_grant_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  2 => 
  array (
    'Field' => 'product_id',
    'Type' => 'int(10)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '二级产品表的uid',
  ),
  3 => 
  array (
    'Field' => 'product_name',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '二级产品的名称',
  ),
  4 => 
  array (
    'Field' => 'rate_no_mortgage',
    'Type' => 'decimal(10,2)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '无抵押利率',
  ),
  5 => 
  array (
    'Field' => 'rate_mortgage1',
    'Type' => 'decimal(10,2)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '抵押1型利率',
  ),
  6 => 
  array (
    'Field' => 'rate_mortgage2',
    'Type' => 'decimal(10,2)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '抵押2型利率',
  ),
);