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
    'Comment' => '等级ID',
  ),
  1 => 
  array (
    'Field' => 'grade_code',
    'Type' => 'varchar(50)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '等级名称',
  ),
  2 => 
  array (
    'Field' => 'grade_caption',
    'Type' => 'varchar(255)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '等级说明',
  ),
  3 => 
  array (
    'Field' => 'min_score',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '最小分',
  ),
  4 => 
  array (
    'Field' => 'max_score',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '最大分',
  ),
  5 => 
  array (
    'Field' => 'creator_id',
    'Type' => 'int(11)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => '创建者Id',
  ),
  6 => 
  array (
    'Field' => 'creator_name',
    'Type' => 'varchar(50)',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建者Name',
  ),
  7 => 
  array (
    'Field' => 'create_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '创建时间',
  ),
  8 => 
  array (
    'Field' => 'update_time',
    'Type' => 'datetime',
    'Null' => 'YES',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '更新时间',
  ),
);