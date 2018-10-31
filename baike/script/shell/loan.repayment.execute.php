<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/30
 * Time: 10:12
 */
require_once(dirname(__FILE__).'/../include_common.php');

$url = SCRIPT_SITE_URL.'/index.php?act=loan&op=schemaRepaymentExecute';

while( true ){
    $re = @file_get_contents($url);
    $arr = @json_decode($re,true);
    echo date('Y-m-d H:i:s').' :Loan repayment execute'."\n";
    print_r($arr['DATA']);
    echo "\n";
    sleep(10);
}