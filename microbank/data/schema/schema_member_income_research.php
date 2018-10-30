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
    'Field' => 'branch_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '分行ID',
  ),
  2 => 
  array (
    'Field' => 'member_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '会员id',
  ),
  3 => 
  array (
    'Field' => 'researcher_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '调查人类型，0 CO 1 BM ',
  ),
  4 => 
  array (
    'Field' => 'operator_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'um_user.uid',
  ),
  5 => 
  array (
    'Field' => 'operator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '调查人',
  ),
  6 => 
  array (
    'Field' => 'income_rental_land',
    'Type' => 'decimal(12,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '月土地租金',
  ),
  7 => 
  array (
    'Field' => 'income_rental_housing',
    'Type' => 'decimal(12,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '月房屋租金',
  ),
  8 => 
  array (
    'Field' => 'income_business',
    'Type' => 'decimal(12,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '商业收入',
  ),
  9 => 
  array (
    'Field' => 'business_employees',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  10 => 
  array (
    'Field' => 'income_salary',
    'Type' => 'decimal(12,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '薪水收入',
  ),
  11 => 
  array (
    'Field' => 'company_name',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '公司名称',
  ),
  12 => 
  array (
    'Field' => 'company_industry',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '公司所属行业',
  ),
  13 => 
  array (
    'Field' => 'work_position',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '工作职位',
  ),
  14 => 
  array (
    'Field' => 'income_others',
    'Type' => 'decimal(12,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '其他收入',
  ),
  15 => 
  array (
    'Field' => 'others_remark',
    'Type' => 'varchar(2000)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '其他收入简述',
  ),
  16 => 
  array (
    'Field' => 'research_remark',
    'Type' => 'varchar(5000)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '调查备注',
  ),
  17 => 
  array (
    'Field' => 'research_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '调查时间',
  ),
);