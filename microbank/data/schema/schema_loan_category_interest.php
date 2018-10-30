<?php
$static_schema=array (
  0 => 
  array (
    'Field' => 'uid',
    'Type' => 'int(10)',
    'Null' => 'NO',
    'Key' => 'PRI',
    'Default' => NULL,
    'Extra' => 'auto_increment',
    'Comment' => '',
  ),
  1 => 
  array (
    'Field' => 'category_id',
    'Type' => 'int(10)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => 'loan_category.uid',
  ),
  2 => 
  array (
    'Field' => 'interest_package_id',
    'Type' => 'int(10)',
    'Null' => 'NO',
    'Key' => '',
    'Default' => '0',
    'Extra' => '',
    'Comment' => 'loan_sub_product.uid',
  ),
);