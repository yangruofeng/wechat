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
    'Field' => 'mug_shot',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '头部照片',
  ),
  3 => 
  array (
    'Field' => 'mug_shot_sha',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '文件sha1码，快捷校验重复性',
  ),
  4 => 
  array (
    'Field' => 'cert_type',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '证件类型(1身份证3护照2户口本)',
  ),
  5 => 
  array (
    'Field' => 'cert_name',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '证件上的名字',
  ),
  6 => 
  array (
    'Field' => 'cert_name_json',
    'Type' => 'varchar(2000)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '名字多语言',
  ),
  7 => 
  array (
    'Field' => 'cert_sn',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '证件上的编号',
  ),
  8 => 
  array (
    'Field' => 'cert_addr',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '证件上的地址',
  ),
  9 => 
  array (
    'Field' => 'cert_expire_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '证件过期时间',
  ),
  10 => 
  array (
    'Field' => 'cert_issue_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '证件发放时间',
  ),
  11 => 
  array (
    'Field' => 'location',
    'Type' => 'varchar(200)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '认证所在位置',
  ),
  12 => 
  array (
    'Field' => 'x_coordinate',
    'Type' => 'decimal(11,6)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'x坐标(经度)',
  ),
  13 => 
  array (
    'Field' => 'y_coordinate',
    'Type' => 'decimal(11,6)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'y坐标(纬度)',
  ),
  14 => 
  array (
    'Field' => 'source_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '资料来源 0 自提交 1 柜台提交',
  ),
  15 => 
  array (
    'Field' => 'verify_state',
    'Type' => 'varchar(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '验证状态:-1审核中锁住状态,0未审核,10审核通过，100审核未通过',
  ),
  16 => 
  array (
    'Field' => 'verify_remark',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '验证备注',
  ),
  17 => 
  array (
    'Field' => 'auditor_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => 'um_user.uid',
  ),
  18 => 
  array (
    'Field' => 'auditor_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'um.user_name',
  ),
  19 => 
  array (
    'Field' => 'auditor_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '审核时间',
  ),
  20 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建时间',
  ),
  21 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '更新时间',
  ),
  22 => 
  array (
    'Field' => 'creator_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '创建人，um_user.uid',
  ),
  23 => 
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