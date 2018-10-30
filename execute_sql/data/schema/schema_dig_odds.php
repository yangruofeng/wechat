<?php
$static_schema=array (
  0 => 
  array (
    'Field' => 'id',
    'Type' => 'int(10) unsigned',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
    'Comment' => '',
  ),
  1 => 
  array (
    'Field' => 'game_type',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => 'MUL',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  2 => 
  array (
    'Field' => 'red_packet_count',
    'Type' => 'int(11)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  3 => 
  array (
    'Field' => 'odds',
    'Type' => 'decimal(18,2)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => NULL,
    'Extra' => '',
    'Comment' => '',
  ),
  4 => 
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