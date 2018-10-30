<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2017/9/15
 * Time: 14:24
 */

define("InKHBuy", 1);
define("PROJECT_ROOT",realpath( dirname(dirname(dirname(__FILE__)))) );
define('CURRENT_ROOT',realpath( dirname(dirname(__FILE__))) );
if (!@include( dirname(dirname(dirname(dirname(__FILE__)))).'/global.php')) exit('global.php isn\'t exists!');
if (!@include(PROJECT_ROOT.'/inc.app.php')) exit('inc.app.php isn\'t exists!');

ini_set('max_execution_time',0);

$url = SCRIPT_SITE_URL."/index.php?act=settle&op=expired";
while(true) {
    $re = @file_get_contents($url,false,$f_context);
    print_r($re);
    sleep(1);
}