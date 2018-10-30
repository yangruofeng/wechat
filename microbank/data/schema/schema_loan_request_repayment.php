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
    'Field' => 'member_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'client_member.uid',
  ),
  2 => 
  array (
    'Field' => 'prepayment_apply_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '提前还款申请ID',
  ),
  3 => 
  array (
    'Field' => 'currency',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '货币',
  ),
  4 => 
  array (
    'Field' => 'type',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => 'schema',
    'Extra' => '',
    'Comment' => '还款类型,schema 计划还款  balance 提前还款（合同剩余余额）',
  ),
  5 => 
  array (
    'Field' => 'repayment_way',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '还款方式 0 线下（现金+bank） 1 绑定的账户扣款',
  ),
  6 => 
  array (
    'Field' => 'amount',
    'Type' => 'decimal(20,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.00',
    'Extra' => '',
    'Comment' => '收款金额',
  ),
  7 => 
  array (
    'Field' => 'payer_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => 'loan_account.handler_id',
  ),
  8 => 
  array (
    'Field' => 'payer_type',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '收款人类型:handler_type',
  ),
  9 => 
  array (
    'Field' => 'payer_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '收款人名字，防止handler表的改变',
  ),
  10 => 
  array (
    'Field' => 'payer_phone',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '收款人电话',
  ),
  11 => 
  array (
    'Field' => 'payer_account',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '收款人帐号',
  ),
  12 => 
  array (
    'Field' => 'payer_property',
    'Type' => 'varchar(2000)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '付款人特性',
  ),
  13 => 
  array (
    'Field' => 'bank_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'bank_account.uid',
  ),
  14 => 
  array (
    'Field' => 'bank_code',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '银行code',
  ),
  15 => 
  array (
    'Field' => 'bank_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '银行名称',
  ),
  16 => 
  array (
    'Field' => 'bank_account_no',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '银行帐号',
  ),
  17 => 
  array (
    'Field' => 'bank_account_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '银行账户名',
  ),
  18 => 
  array (
    'Field' => 'request_remark',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '备注',
  ),
  19 => 
  array (
    'Field' => 'auditor_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  20 => 
  array (
    'Field' => 'auditor_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  21 => 
  array (
    'Field' => 'audit_remark',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  22 => 
  array (
    'Field' => 'audit_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  23 => 
  array (
    'Field' => 'handler_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '处理者ID',
  ),
  24 => 
  array (
    'Field' => 'handler_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '处理者name',
  ),
  25 => 
  array (
    'Field' => 'handle_remark',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '备注',
  ),
  26 => 
  array (
    'Field' => 'handle_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '处理时间',
  ),
  27 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建时间',
  ),
  28 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '更新时间',
  ),
  29 => 
  array (
    'Field' => 'state',
    'Type' => 'tinyint(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '0新建  10 处理中 20 失败  100成功',
  ),
  30 => 
  array (
    'Field' => 'request_img',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '转账记录',
  ),
  31 => 
  array (
    'Field' => 'request_img1',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '转账记录',
  ),
  32 => 
  array (
    'Field' => 'request_img2',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '转账记录',
  ),
);