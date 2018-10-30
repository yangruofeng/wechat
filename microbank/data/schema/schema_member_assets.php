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
    'Field' => 'cert_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '认证id',
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
    'Field' => 'asset_name',
    'Type' => 'varchar(200)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '资产名称',
  ),
  6 => 
  array (
    'Field' => 'asset_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '资产类型->cert_type，房屋，汽车，土地...',
  ),
  7 => 
  array (
    'Field' => 'asset_sn',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '资产编号',
  ),
  8 => 
  array (
    'Field' => 'asset_cert_type',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '资产证件类型',
  ),
  9 => 
  array (
    'Field' => 'valuation',
    'Type' => 'decimal(12,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '估值',
  ),
  10 => 
  array (
    'Field' => 'valuate_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '估价时间',
  ),
  11 => 
  array (
    'Field' => 'valuate_user_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'um_user.uid',
  ),
  12 => 
  array (
    'Field' => 'valuate_user_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '评估人',
  ),
  13 => 
  array (
    'Field' => 'mortgage_state',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '抵押状态，0 未抵押 1 已抵押',
  ),
  14 => 
  array (
    'Field' => 'mortgage_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '抵押时间',
  ),
  15 => 
  array (
    'Field' => 'asset_state',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '-1 删除,0 创建，100 认证通过 ',
  ),
  16 => 
  array (
    'Field' => 'hold_state',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '持有状态,0 未在库，1 在库',
  ),
  17 => 
  array (
    'Field' => 'remark',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'remark',
  ),
  18 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建时间',
  ),
  19 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '更新时间',
  ),
  20 => 
  array (
    'Field' => 'credit',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '可抵押信用',
  ),
  21 => 
  array (
    'Field' => 'monthly_rent',
    'Type' => 'decimal(12,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '月租金收入',
  ),
  22 => 
  array (
    'Field' => 'research_text',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '调查内容json',
  ),
  23 => 
  array (
    'Field' => 'coord_x',
    'Type' => 'decimal(10,6)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.000000',
    'Extra' => '',
    'Comment' => '经度',
  ),
  24 => 
  array (
    'Field' => 'coord_y',
    'Type' => 'decimal(10,6)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.000000',
    'Extra' => '',
    'Comment' => '纬度',
  ),
  25 => 
  array (
    'Field' => 'address_detail',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '位置',
  ),
);