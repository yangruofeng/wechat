<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/26
 * Time: 16:44
 */

define("InKHBuy", 1);
define("CURRENT_ROOT",realpath(dirname(__FILE__)));
define("PROJECT_ROOT",realpath(dirname(dirname(__FILE__))));
if (!@include(dirname(dirname(dirname(__FILE__))) . '/global.php')) exit('global.php isn\'t exists!');

RpcRouter::init();
define('CHARSET',$config['charset']);
adjust_timezone();
require_once(PROJECT_ROOT.DS."inc.app.php");



