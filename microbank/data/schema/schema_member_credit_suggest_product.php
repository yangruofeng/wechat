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
    'Field' => 'credit_suggest_id',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => 'member_credit_suggest.uid',
  ),
  2 => 
  array (
    'Field' => 'member_credit_category_id',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => 'member_credit_category.uid',
  ),
  3 => 
  array (
    'Field' => 'credit',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  4 => 
  array (
    'Field' => 'credit_usd',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  5 => 
  array (
    'Field' => 'credit_khr',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  6 => 
  array (
    'Field' => 'exchange_rate',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '4000',
    'Extra' => '',
    'Comment' => '',
  ),
  7 => 
  array (
    'Field' => 'interest_rate',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '特殊利率',
  ),
  8 => 
  array (
    'Field' => 'interest_rate_khr',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '特殊利率(KHR)',
  ),
  9 => 
  array (
    'Field' => 'operation_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  10 => 
  array (
    'Field' => 'operation_fee_khr',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  11 => 
  array (
    'Field' => 'loan_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  12 => 
  array (
    'Field' => 'loan_fee_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  13 => 
  array (
    'Field' => 'admin_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  14 => 
  array (
    'Field' => 'admin_fee_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  15 => 
  array (
    'Field' => 'annual_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '年费',
  ),
  16 => 
  array (
    'Field' => 'annual_fee_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '年费类型',
  ),
  17 => 
  array (
    'Field' => 'loan_fee_khr',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  18 => 
  array (
    'Field' => 'admin_fee_khr',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  19 => 
  array (
    'Field' => 'annual_fee_khr',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
);