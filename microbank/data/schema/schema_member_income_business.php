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
    'Field' => 'branch_code',
    'Type' => 'varchar(200)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => 'Default',
    'Extra' => '',
    'Comment' => '分支code',
  ),
  2 => 
  array (
    'Field' => 'member_id',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  3 => 
  array (
    'Field' => 'relative_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '关系人ID',
  ),
  4 => 
  array (
    'Field' => 'relative_name',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '关系人名字',
  ),
  5 => 
  array (
    'Field' => 'industry_id',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'common_industry.uid',
  ),
  6 => 
  array (
    'Field' => 'industry_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  7 => 
  array (
    'Field' => 'industry_place',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'common_industry_place.uid',
  ),
  8 => 
  array (
    'Field' => 'industry_place_text',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'common_industry_place.place',
  ),
  9 => 
  array (
    'Field' => 'research_text',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '调查数据的json',
  ),
  10 => 
  array (
    'Field' => 'research_text_src',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '客户提供的调查数据json',
  ),
  11 => 
  array (
    'Field' => 'employees',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '员工人数',
  ),
  12 => 
  array (
    'Field' => 'income',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '收入合计',
  ),
  13 => 
  array (
    'Field' => 'expense',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '费用合计',
  ),
  14 => 
  array (
    'Field' => 'profit',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '利润',
  ),
  15 => 
  array (
    'Field' => 'operator_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  16 => 
  array (
    'Field' => 'operator_id',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  17 => 
  array (
    'Field' => 'operator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  18 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  19 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  20 => 
  array (
    'Field' => 'coord_x',
    'Type' => 'decimal(10,6)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.000000',
    'Extra' => '',
    'Comment' => '经度',
  ),
  21 => 
  array (
    'Field' => 'coord_y',
    'Type' => 'decimal(10,6)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.000000',
    'Extra' => '',
    'Comment' => '纬度',
  ),
  22 => 
  array (
    'Field' => 'address_detail',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '位置',
  ),
  23 => 
  array (
    'Field' => 'update_operator_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  24 => 
  array (
    'Field' => 'update_operator_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  25 => 
  array (
    'Field' => 'update_operator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
);