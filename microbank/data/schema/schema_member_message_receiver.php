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
    'Comment' => '记录ID',
  ),
  1 => 
  array (
    'Field' => 'message_id',
    'Type' => 'int(10) unsigned',
    'Null' => 'NO',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '消息ID',
  ),
  2 => 
  array (
    'Field' => 'receiver_type',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'MUL',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '接收者类型',
  ),
  3 => 
  array (
    'Field' => 'receiver_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '接收者ID',
  ),
  4 => 
  array (
    'Field' => 'receiver_name',
    'Type' => 'varchar(50)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '接收者名称',
  ),
  5 => 
  array (
    'Field' => 'is_read',
    'Type' => 'tinyint(1)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '是否已读',
  ),
  6 => 
  array (
    'Field' => 'read_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '阅读时间',
  ),
  7 => 
  array (
    'Field' => 'is_deleted',
    'Type' => 'tinyint(1)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '是否删除',
  ),
  8 => 
  array (
    'Field' => 'delete_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '删除时间',
  ),
);