<?php

header('P3P: CP=CAO PSA OUR');//允许跨域设置cookie，貌似存在安全隐患

define("InKHBuy", 1);
define("CURRENT_ROOT",realpath(dirname(__FILE__)));
define("PROJECT_ROOT",realpath(dirname(dirname(__FILE__))));
if (!@include(dirname(dirname(dirname(__FILE__))) . '/global.php')) exit('global.php isn\'t exists!');

define('CHARSET',$config['charset']);
require_once(PROJECT_ROOT.DS."inc.app.php");
adjust_timezone();

RpcRouter::init();
RpcRouter::handle(array(
    'defaultClass'=>'indexControl',
    'defaultMethod'=>'indexOp',
    'APP_NAME'=>'DEMO'
));
