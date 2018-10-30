<?php
$static_schema=array (
  0 => 
  array (
    'Field' => 'message_id',
    'Type' => 'int(10) unsigned',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
    'Comment' => '消息ID',
  ),
  1 => 
  array (
    'Field' => 'message_type',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '消息类型，1: 系统消息',
  ),
  2 => 
  array (
    'Field' => 'sender_type',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '发送放类型，1：系统，0：会员',
  ),
  3 => 
  array (
    'Field' => 'sender_id',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '发送方ID',
  ),
  4 => 
  array (
    'Field' => 'sender_name',
    'Type' => 'varchar(100)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '发送方名称',
  ),
  5 => 
  array (
    'Field' => 'message_title',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '标题',
  ),
  6 => 
  array (
    'Field' => 'message_body',
    'Type' => 'varchar(255)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '内容',
  ),
  7 => 
  array (
    'Field' => 'message_time',
    'Type' => 'datetime',
    'Null' => 'NO',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '发送时间',
  ),
  8 => 
  array (
    'Field' => 'message_state',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '状态，0：正常，1，删除',
  ),
);