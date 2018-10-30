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
    'Comment' => '地址ID',
  ),
  1 => 
  array (
    'Field' => 'obj_type',
    'Type' => 'varchar(20)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '全局类型',
  ),
  2 => 
  array (
    'Field' => 'obj_guid',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '全局对象ID',
  ),
  3 => 
  array (
    'Field' => 'address_category',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '地址类型,如home,company',
  ),
  4 => 
  array (
    'Field' => 'id1',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '地址id(1级)',
  ),
  5 => 
  array (
    'Field' => 'id1_text',
    'Type' => 'varchar(200)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '名称',
  ),
  6 => 
  array (
    'Field' => 'id1_text_json',
    'Type' => 'varchar(2000)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '多语言名称',
  ),
  7 => 
  array (
    'Field' => 'id2',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '地址id(2级)',
  ),
  8 => 
  array (
    'Field' => 'id2_text',
    'Type' => 'varchar(200)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '名称',
  ),
  9 => 
  array (
    'Field' => 'id2_text_json',
    'Type' => 'varchar(2000)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '多语言名称',
  ),
  10 => 
  array (
    'Field' => 'id3',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '地址id(3级)',
  ),
  11 => 
  array (
    'Field' => 'id3_text',
    'Type' => 'varchar(200)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '名称',
  ),
  12 => 
  array (
    'Field' => 'id3_text_json',
    'Type' => 'varchar(2000)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '多语言名称',
  ),
  13 => 
  array (
    'Field' => 'id4',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '地址id(4级)',
  ),
  14 => 
  array (
    'Field' => 'id4_text',
    'Type' => 'varchar(200)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '名称',
  ),
  15 => 
  array (
    'Field' => 'id4_text_json',
    'Type' => 'varchar(2000)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '多语言名称',
  ),
  16 => 
  array (
    'Field' => 'coord_x',
    'Type' => 'decimal(12,6)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.000000',
    'Extra' => '',
    'Comment' => '经度',
  ),
  17 => 
  array (
    'Field' => 'coord_y',
    'Type' => 'decimal(12,6)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0.000000',
    'Extra' => '',
    'Comment' => '纬度',
  ),
  18 => 
  array (
    'Field' => 'address_detail',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  19 => 
  array (
    'Field' => 'street',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '街道',
  ),
  20 => 
  array (
    'Field' => 'house_number',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '门牌号',
  ),
  21 => 
  array (
    'Field' => 'address_group',
    'Type' => 'varchar(200)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '组',
  ),
  22 => 
  array (
    'Field' => 'full_text',
    'Type' => 'varchar(2000)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '地址全路径',
  ),
  23 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '时间',
  ),
  24 => 
  array (
    'Field' => 'state',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '1',
    'Extra' => '',
    'Comment' => '状态 1 可用',
  ),
);