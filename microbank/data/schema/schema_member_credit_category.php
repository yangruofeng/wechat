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
    'Comment' => 'client_member.uid',
  ),
  2 => 
  array (
    'Field' => 'category_id',
    'Type' => 'int(10)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'loan_category.uid',
  ),
  3 => 
  array (
    'Field' => 'sub_product_id',
    'Type' => 'int(10)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'loan_sub_product.uid',
  ),
  4 => 
  array (
    'Field' => 'alias',
    'Type' => 'varchar(100)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '别名',
  ),
  5 => 
  array (
    'Field' => 'alias_lang',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '别名的语言包',
  ),
  6 => 
  array (
    'Field' => 'credit',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '信用',
  ),
  7 => 
  array (
    'Field' => 'credit_balance',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '信用余额',
  ),
  8 => 
  array (
    'Field' => 'creator_id',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  9 => 
  array (
    'Field' => 'creator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  10 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建时间',
  ),
  11 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '更新时间',
  ),
  12 => 
  array (
    'Field' => 'update_operator_id',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '更新人',
  ),
  13 => 
  array (
    'Field' => 'is_close',
    'Type' => 'tinyint(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '是否关闭',
  ),
  14 => 
  array (
    'Field' => 'is_one_time',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '是否一次性借完',
  ),
  15 => 
  array (
    'Field' => 'interest_package_id',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '利息包，0为默认设置',
  ),
  16 => 
  array (
    'Field' => 'credit_usd',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  17 => 
  array (
    'Field' => 'credit_usd_balance',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  18 => 
  array (
    'Field' => 'credit_khr',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  19 => 
  array (
    'Field' => 'credit_khr_balance',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  20 => 
  array (
    'Field' => 'interest_rate_usd',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '客人特殊利率',
  ),
  21 => 
  array (
    'Field' => 'interest_rate_khr',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '客人特殊利率',
  ),
  22 => 
  array (
    'Field' => 'operation_fee_usd',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  23 => 
  array (
    'Field' => 'operation_fee_khr',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  24 => 
  array (
    'Field' => 'loan_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  25 => 
  array (
    'Field' => 'loan_fee_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  26 => 
  array (
    'Field' => 'admin_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  27 => 
  array (
    'Field' => 'admin_fee_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  28 => 
  array (
    'Field' => 'annual_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '年费',
  ),
  29 => 
  array (
    'Field' => 'annual_fee_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '年费类型',
  ),
);