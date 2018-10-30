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
    'Comment' => '产品ID',
  ),
  1 => 
  array (
    'Field' => 'category',
    'Type' => 'varchar(50)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '产品分类，如信用贷、房贷',
  ),
  2 => 
  array (
    'Field' => 'product_code',
    'Type' => 'varchar(50)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '产品Code',
  ),
  3 => 
  array (
    'Field' => 'product_name',
    'Type' => 'varchar(200)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '产品名',
  ),
  4 => 
  array (
    'Field' => 'product_description',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '产品描述',
  ),
  5 => 
  array (
    'Field' => 'product_qualification',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '贷款条件',
  ),
  6 => 
  array (
    'Field' => 'product_feature',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '产品特色',
  ),
  7 => 
  array (
    'Field' => 'product_required',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '贷款必须资料',
  ),
  8 => 
  array (
    'Field' => 'product_notice',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '产品公告',
  ),
  9 => 
  array (
    'Field' => 'state',
    'Type' => 'tinyint(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '状态 10：temp 20：active 30:inactive 40:history',
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
    'Field' => 'creator_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建者Id',
  ),
  12 => 
  array (
    'Field' => 'creator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建者Name',
  ),
  13 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '更新时间',
  ),
  14 => 
  array (
    'Field' => 'is_multi_contract',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
    'Comment' => '允许多合同标志',
  ),
  15 => 
  array (
    'Field' => 'is_advance_interest',
    'Type' => 'tinyint(1) unsigned zerofill',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
    'Comment' => '预扣利息',
  ),
  16 => 
  array (
    'Field' => 'is_editable_penalty',
    'Type' => 'tinyint(1) unsigned zerofill',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
    'Comment' => '是否允许合同修改罚款利率',
  ),
  17 => 
  array (
    'Field' => 'is_editable_interest',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
    'Comment' => '是否允许修改利息',
  ),
  18 => 
  array (
    'Field' => 'is_editable_grace_days',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
    'Comment' => '是否允许修改宽限天数',
  ),
  19 => 
  array (
    'Field' => 'product_key',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '产品系列key',
  ),
);