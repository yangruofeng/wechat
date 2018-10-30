<?php
$static_schema=array (
  0 => 
  array (
    'Field' => 'uid',
    'Type' => 'int(10) unsigned',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
    'Comment' => '',
  ),
  1 => 
  array (
    'Field' => 'card_no',
    'Type' => 'varchar(20)',
    'Null' => 'NO',
    'Key' => 'UNI',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '卡的序列号',
  ),
  2 => 
  array (
    'Field' => 'card_key',
    'Type' => 'varchar(500)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => 'FFFFFFFFFFFF',
    'Extra' => '',
    'Comment' => '卡的密钥',
  ),
  3 => 
  array (
    'Field' => 'expire_time',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '过期时间',
  ),
  4 => 
  array (
    'Field' => 'last_block_sign',
    'Type' => 'char(32)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '最后一个区块(扇区)的签名',
  ),
  5 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建卡的时间',
  ),
  6 => 
  array (
    'Field' => 'create_user_id',
    'Type' => 'int(10) unsigned',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建卡的用户ID',
  ),
  7 => 
  array (
    'Field' => 'create_user_name',
    'Type' => 'varchar(50)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建卡的用户名',
  ),
  8 => 
  array (
    'Field' => 'lmt',
    'Type' => 'timestamp',
    'Null' => 'NO',
    'Key' => '',
    'Default' => 'CURRENT_TIMESTAMP',
    'Extra' => '',
    'Comment' => '',
  ),
);