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
    'Comment' => '会员ID',
  ),
  1 => 
  array (
    'Field' => 'obj_guid',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '对象的全局跨表编号',
  ),
  2 => 
  array (
    'Field' => 'short_loan_guid',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '短期贷款账户全局编号',
  ),
  3 => 
  array (
    'Field' => 'long_loan_guid',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '长期贷款账户全局编号',
  ),
  4 => 
  array (
    'Field' => 'short_deposit_guid',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '短期存款账户全局编号',
  ),
  5 => 
  array (
    'Field' => 'long_deposit_guid',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '长期存款账户全局编号',
  ),
  6 => 
  array (
    'Field' => 'login_code',
    'Type' => 'varchar(50)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '登录ID',
  ),
  7 => 
  array (
    'Field' => 'login_password',
    'Type' => 'varchar(50)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '登录密码(hash+salt)',
  ),
  8 => 
  array (
    'Field' => 'trading_password',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '交易密码',
  ),
  9 => 
  array (
    'Field' => 'trading_verify_amount',
    'Type' => 'decimal(10,2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '交易密码启用金额',
  ),
  10 => 
  array (
    'Field' => 'trading_verify_currency',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '启用金额币种',
  ),
  11 => 
  array (
    'Field' => 'gesture_password',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '手势密码',
  ),
  12 => 
  array (
    'Field' => 'fingerprint',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '指纹',
  ),
  13 => 
  array (
    'Field' => 'family_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '姓氏',
  ),
  14 => 
  array (
    'Field' => 'given_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '名',
  ),
  15 => 
  array (
    'Field' => 'initials',
    'Type' => 'varchar(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '姓名中的大写首字母',
  ),
  16 => 
  array (
    'Field' => 'display_name',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '名称（family+given_name）',
  ),
  17 => 
  array (
    'Field' => 'kh_display_name',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '名称（family+given_name）',
  ),
  18 => 
  array (
    'Field' => 'alias_name',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'json保存多语言姓名',
  ),
  19 => 
  array (
    'Field' => 'work_type',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  20 => 
  array (
    'Field' => 'gender',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '性别',
  ),
  21 => 
  array (
    'Field' => 'civil_status',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '婚姻状况',
  ),
  22 => 
  array (
    'Field' => 'birthday',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '生日',
  ),
  23 => 
  array (
    'Field' => 'phone_country',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '电话区号',
  ),
  24 => 
  array (
    'Field' => 'phone_number',
    'Type' => 'varchar(30)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '电话号码',
  ),
  25 => 
  array (
    'Field' => 'phone_id',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '格式化后的电话，passport之一',
  ),
  26 => 
  array (
    'Field' => 'is_verify_phone',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '是否验证电话号码',
  ),
  27 => 
  array (
    'Field' => 'verify_phone_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '电话验证时间',
  ),
  28 => 
  array (
    'Field' => 'email',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '电子邮件1',
  ),
  29 => 
  array (
    'Field' => 'is_verify_email',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '是否验证邮箱',
  ),
  30 => 
  array (
    'Field' => 'verify_email_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '邮箱验证时间',
  ),
  31 => 
  array (
    'Field' => 'member_property',
    'Type' => 'text',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'json保存扩展属性，如职业收入等',
  ),
  32 => 
  array (
    'Field' => 'member_profile',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'json保存配置信息，规则项，如limit',
  ),
  33 => 
  array (
    'Field' => 'member_verification',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'json字符串，计算项',
  ),
  34 => 
  array (
    'Field' => 'member_grade',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => 'member_grade.grade_code',
  ),
  35 => 
  array (
    'Field' => 'member_image',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '头像原图',
  ),
  36 => 
  array (
    'Field' => 'member_icon',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '头像剪切图',
  ),
  37 => 
  array (
    'Field' => 'open_source',
    'Type' => 'tinyint(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '开户来源(0=>网络，1=>柜台,10=>第三方)',
  ),
  38 => 
  array (
    'Field' => 'open_org',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '开户机构,open_source=1',
  ),
  39 => 
  array (
    'Field' => 'open_addr',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '开户地址',
  ),
  40 => 
  array (
    'Field' => 'branch_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  41 => 
  array (
    'Field' => 'member_state',
    'Type' => 'tinyint(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
    'Comment' => '用户状态',
  ),
  42 => 
  array (
    'Field' => 'verify_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  43 => 
  array (
    'Field' => 'verify_remark',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  44 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建时间',
  ),
  45 => 
  array (
    'Field' => 'creator_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '创建者Id',
  ),
  46 => 
  array (
    'Field' => 'creator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建者Name',
  ),
  47 => 
  array (
    'Field' => 'operator_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  48 => 
  array (
    'Field' => 'operator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  49 => 
  array (
    'Field' => 'operate_state',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '',
  ),
  50 => 
  array (
    'Field' => 'operate_remark',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  51 => 
  array (
    'Field' => 'operate_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  52 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '更新时间',
  ),
  53 => 
  array (
    'Field' => 'last_login_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '最后登录时间',
  ),
  54 => 
  array (
    'Field' => 'last_login_ip',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '最后登录ip',
  ),
  55 => 
  array (
    'Field' => 'id_sn',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '身份证号码',
  ),
  56 => 
  array (
    'Field' => 'id_type',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '身份证类型0国内1国外',
  ),
  57 => 
  array (
    'Field' => 'nationality',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '国籍',
  ),
  58 => 
  array (
    'Field' => 'id_en_name_json',
    'Type' => 'varchar(2000)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '英文名',
  ),
  59 => 
  array (
    'Field' => 'id_kh_name_json',
    'Type' => 'varchar(2000)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '柬文名',
  ),
  60 => 
  array (
    'Field' => 'id_address1',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '省',
  ),
  61 => 
  array (
    'Field' => 'id_address2',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '市',
  ),
  62 => 
  array (
    'Field' => 'id_address3',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '县',
  ),
  63 => 
  array (
    'Field' => 'id_address4',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '镇',
  ),
  64 => 
  array (
    'Field' => 'id_expire_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '证件过期时间',
  ),
  65 => 
  array (
    'Field' => 'address',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  66 => 
  array (
    'Field' => 'address_detail',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  67 => 
  array (
    'Field' => 'register_location',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '注册时位置',
  ),
  68 => 
  array (
    'Field' => 'is_with_business',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '是否有商业',
  ),
);