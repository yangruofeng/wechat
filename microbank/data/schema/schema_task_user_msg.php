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
    'Comment' => '',
  ),
  1 => 
  array (
    'Field' => 'task_id',
    'Type' => 'int(10)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '业务id',
  ),
  2 => 
  array (
    'Field' => 'task_type',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '业务类型',
  ),
  3 => 
  array (
    'Field' => 'sender_id',
    'Type' => 'int(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '发送人',
  ),
  4 => 
  array (
    'Field' => 'sender_type',
    'Type' => 'int(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '发送人类型',
  ),
  5 => 
  array (
    'Field' => 'receiver_id',
    'Type' => 'int(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '接受人',
  ),
  6 => 
  array (
    'Field' => 'receiver_type',
    'Type' => 'int(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '接受人类型',
  ),
  7 => 
  array (
    'Field' => 'task_state',
    'Type' => 'int(2)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '任务状态',
  ),
  8 => 
  array (
    'Field' => 'msg',
    'Type' => 'varchar(200)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '消息内容',
  ),
  9 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  10 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
);