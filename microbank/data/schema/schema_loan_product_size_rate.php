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
    'Field' => 'main_product_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'loan_product.uid',
  ),
  2 => 
  array (
    'Field' => 'product_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'loan_sub_product.uid',
  ),
  3 => 
  array (
    'Field' => 'currency',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => 'USD',
    'Extra' => '',
    'Comment' => '',
  ),
  4 => 
  array (
    'Field' => 'loan_size_min',
    'Type' => 'decimal(20,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '最小值',
  ),
  5 => 
  array (
    'Field' => 'loan_size_max',
    'Type' => 'decimal(20,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '最大值',
  ),
  6 => 
  array (
    'Field' => 'min_term_days',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  7 => 
  array (
    'Field' => 'max_term_days',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  8 => 
  array (
    'Field' => 'guarantee_type',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '担保方式*空',
  ),
  9 => 
  array (
    'Field' => 'mortgage_type',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '抵押方式*空',
  ),
  10 => 
  array (
    'Field' => 'interest_payment',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  11 => 
  array (
    'Field' => 'interest_rate',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '利息',
  ),
  12 => 
  array (
    'Field' => 'interest_rate_mortgage1',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '抵押1型利率',
  ),
  13 => 
  array (
    'Field' => 'interest_rate_mortgage2',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '抵押2型利率',
  ),
  14 => 
  array (
    'Field' => 'interest_rate_unit',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => 'yearly',
    'Extra' => '',
    'Comment' => '利率基准单位 yearly 年 monthly 月 daily 日',
  ),
  15 => 
  array (
    'Field' => 'interest_rate_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '百分比还是金额,0 百分比，1数字',
  ),
  16 => 
  array (
    'Field' => 'interest_min_value',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  17 => 
  array (
    'Field' => 'interest_rate_period',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '年／月／日',
  ),
  18 => 
  array (
    'Field' => 'is_prime_rate',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '采用基准利率',
  ),
  19 => 
  array (
    'Field' => 'prime_rate_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '基准利率id',
  ),
  20 => 
  array (
    'Field' => 'admin_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  21 => 
  array (
    'Field' => 'admin_fee_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '0 百分比，1数字',
  ),
  22 => 
  array (
    'Field' => 'loan_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  23 => 
  array (
    'Field' => 'loan_fee_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  24 => 
  array (
    'Field' => 'operation_fee',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  25 => 
  array (
    'Field' => 'operation_fee_mortgage1',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '抵押1型利率(soft)',
  ),
  26 => 
  array (
    'Field' => 'operation_fee_mortgage2',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '抵押2型利率(hard)',
  ),
  27 => 
  array (
    'Field' => 'operation_fee_unit',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => 'yearly',
    'Extra' => '',
    'Comment' => '利率基准单位 yearly 年 monthly 月 daily 日',
  ),
  28 => 
  array (
    'Field' => 'operation_fee_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '0 百分比，1数字',
  ),
  29 => 
  array (
    'Field' => 'operation_min_value',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  30 => 
  array (
    'Field' => 'grace_days',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '宽限日期',
  ),
  31 => 
  array (
    'Field' => 'is_full_interest',
    'Type' => 'tinyint(1)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
    'Comment' => '提前还款是否全额利息',
  ),
  32 => 
  array (
    'Field' => 'prepayment_interest',
    'Type' => 'decimal(10,2)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '提前返款利息',
  ),
  33 => 
  array (
    'Field' => 'prepayment_interest_type',
    'Type' => 'tinyint(1)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '提前返款利息单位',
  ),
  34 => 
  array (
    'Field' => 'is_show_for_client',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
    'Comment' => '是否member app显示',
  ),
  35 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  36 => 
  array (
    'Field' => 'service_fee',
    'Type' => 'decimal(20,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '',
  ),
  37 => 
  array (
    'Field' => 'service_fee_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
);