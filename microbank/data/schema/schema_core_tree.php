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
    'Field' => 'root_key',
    'Type' => 'varchar(50)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => 'Tree Key',
  ),
  2 => 
  array (
    'Field' => 'node_text',
    'Type' => 'varchar(100)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '节点描述',
  ),
  3 => 
  array (
    'Field' => 'node_text_alias',
    'Type' => 'varchar(500)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '节点多语言json',
  ),
  4 => 
  array (
    'Field' => 'node_path',
    'Type' => 'varchar(100)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '节点id完整路径',
  ),
  5 => 
  array (
    'Field' => 'node_level',
    'Type' => 'tinyint(4)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '层级',
  ),
  6 => 
  array (
    'Field' => 'node_sort',
    'Type' => 'tinyint(4)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  7 => 
  array (
    'Field' => 'pid',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '父级ID',
  ),
  8 => 
  array (
    'Field' => 'is_leaf',
    'Type' => 'tinyint(1)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '是否叶子',
  ),
);