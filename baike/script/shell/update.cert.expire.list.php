<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/26
 * Time: 16:26
 */

require_once(dirname(__FILE__).'/../include_common.php');


$cert_url = SCRIPT_SITE_URL.'/index.php?act=script_cert&op=updateExpireCertList';
while( true ){
    $re = @file_get_contents($cert_url);
    echo date('Y-m-d H:i:s').' :Update cert expire list'."\n";
    print_r($re);
    echo "\n";
    sleep(12*3600);
}