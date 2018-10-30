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
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '会员ID',
  ),
  2 => 
  array (
    'Field' => 'relative_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'member_credit_request_relative.uid',
  ),
  3 => 
  array (
    'Field' => 'mug_shot',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '头部照片',
  ),
  4 => 
  array (
    'Field' => 'mug_shot_sha',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '文件sha1码，快捷校验重复性',
  ),
  5 => 
  array (
    'Field' => 'cert_type',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '证件类型(1身份证3护照2户口本)',
  ),
  6 => 
  array (
    'Field' => 'cert_name',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '证件上的名字',
  ),
  7 => 
  array (
    'Field' => 'cert_name_json',
    'Type' => 'varchar(2000)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '名字多语言',
  ),
  8 => 
  array (
    'Field' => 'cert_sn',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '证件上的编号',
  ),
  9 => 
  array (
    'Field' => 'cert_addr',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '证件上的地址',
  ),
  10 => 
  array (
    'Field' => 'cert_expire_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '证件过期时间',
  ),
  11 => 
  array (
    'Field' => 'cert_issue_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '证件发放时间',
  ),
  12 => 
  array (
    'Field' => 'location',
    'Type' => 'varchar(200)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '认证所在位置',
  ),
  13 => 
  array (
    'Field' => 'x_coordinate',
    'Type' => 'decimal(11,6)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'x坐标(经度)',
  ),
  14 => 
  array (
    'Field' => 'y_coordinate',
    'Type' => 'decimal(11,6)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'y坐标(纬度)',
  ),
  15 => 
  array (
    'Field' => 'source_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '资料来源 0 自提交 1 柜台提交',
  ),
  16 => 
  array (
    'Field' => 'verify_state',
    'Type' => 'varchar(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '验证状态:-1审核中锁住状态,0未审核,10审核通过，100审核未通过',
  ),
  17 => 
  array (
    'Field' => 'verify_remark',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '验证备注',
  ),
  18 => 
  array (
    'Field' => 'auditor_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => 'um_user.uid',
  ),
  19 => 
  array (
    'Field' => 'auditor_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'um.user_name',
  ),
  20 => 
  array (
    'Field' => 'auditor_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '审核时间',
  ),
  21 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建时间',
  ),
  22 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '更新时间',
  ),
  23 => 
  array (
    'Field' => 'creator_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '创建人，um_user.uid',
  ),
  24 => 
  array (
    'Field' => 'creator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'um.user_name',
  ),
);