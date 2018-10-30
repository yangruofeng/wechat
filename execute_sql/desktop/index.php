<?php

header('P3P: CP=CAO PSA OUR');//允许跨域设置cookie，貌似存在安全隐患

define("InKHBuy", 1);
define("CURRENT_ROOT",realpath(dirname(__FILE__)));
define("PROJECT_ROOT",realpath(dirname(dirname(__FILE__))));
if (!@include(dirname(dirname(dirname(__FILE__))) . '/global.php')) exit('global.php isn\'t exists!');

RpcRouter::init();

define('CHARSET',$config['charset']);
require_once(PROJECT_ROOT.DS."inc.app.php");
adjust_timezone();

RpcRouter::handle(array(
    'defaultClass'=>'indexControl',
    'defaultMethod'=>'indexOp',
    'APP_NAME'=>'DEMO'
));

$st = microtime(true)-StartTime;//系统超时记录
if ($st>1)
    debug("_api_index_time", "$st - ".$_REQUEST['class'].".".$_REQUEST['method']." on ".$_SERVER['REMOTE_ADDR']."\n".file_get_contents("php://input")."\n");
