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
    'Field' => 'product_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '主产品ID',
  ),
  2 => 
  array (
    'Field' => 'sub_product_code',
    'Type' => 'varchar(50)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '产品Code',
  ),
  3 => 
  array (
    'Field' => 'sub_product_name',
    'Type' => 'varchar(200)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '产品名',
  ),
  4 => 
  array (
    'Field' => 'sub_product_icon',
    'Type' => 'varchar(200)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '产品图标',
  ),
  5 => 
  array (
    'Field' => 'repayment_type',
    'Type' => 'varchar(100)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '还款方式,如月付',
  ),
  6 => 
  array (
    'Field' => 'interest_type',
    'Type' => 'varchar(100)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '利息计算方式，如等额本金',
  ),
  7 => 
  array (
    'Field' => 'penalty_divisor_days',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '罚款利率除以多少天',
  ),
  8 => 
  array (
    'Field' => 'penalty_on',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'total',
  ),
  9 => 
  array (
    'Field' => 'penalty_rate',
    'Type' => 'decimal(5,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '罚款利率',
  ),
  10 => 
  array (
    'Field' => 'penalty_is_compound_interest',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '罚金是否复利计算',
  ),
  11 => 
  array (
    'Field' => 'grace_days',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '宽限天数',
  ),
  12 => 
  array (
    'Field' => 'is_editable_penalty',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '罚金是否可改',
  ),
  13 => 
  array (
    'Field' => 'is_full_interest_prepayment',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
    'Comment' => '提前还款是否全息',
  ),
  14 => 
  array (
    'Field' => 'is_approved_prepayment_request',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
    'Comment' => '提前还款申请是否审核',
  ),
  15 => 
  array (
    'Field' => 'is_advance_interest',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '预扣利息',
  ),
  16 => 
  array (
    'Field' => 'product_key',
    'Type' => 'varchar(100)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '产品key',
  ),
  17 => 
  array (
    'Field' => 'product_description',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  18 => 
  array (
    'Field' => 'product_qualification',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  19 => 
  array (
    'Field' => 'product_feature',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  20 => 
  array (
    'Field' => 'product_required',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  21 => 
  array (
    'Field' => 'product_notice',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  22 => 
  array (
    'Field' => 'state',
    'Type' => 'tinyint(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '状态 10：temp 20：active 30:inactive 40:history',
  ),
  23 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建时间',
  ),
  24 => 
  array (
    'Field' => 'creator_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建者Id',
  ),
  25 => 
  array (
    'Field' => 'creator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建者Name',
  ),
  26 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '更新时间',
  ),
  27 => 
  array (
    'Field' => 'sub_summary',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '二级产品简介',
  ),
  28 => 
  array (
    'Field' => 'is_only_for_counter',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '是否只可在柜台贷款',
  ),
  29 => 
  array (
    'Field' => 'max_contracts_per_client',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '单客户最多同时贷款数',
  ),
  30 => 
  array (
    'Field' => 'number_code',
    'Type' => 'varchar(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '数字化编号',
  ),
);