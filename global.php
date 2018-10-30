<?php
/**
 * 入口文件
 *
 * 统一入口，进行初始化信息
 *
 *
 * @copyright  Copyright (c) 2007-2013 KHBuy Inc. (http://www.KHBuy.com)
 * @license    http://www.KHBuy.com/
 * @link       http://www.KHBuy.com/
 * @since      File available since Release v1.1
 */

//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ERROR | E_COMPILE_ERROR | E_PARSE | E_CORE_ERROR | E_USER_ERROR);
define('GLOBAL_ROOT', str_replace('\\', '/', dirname(__FILE__)));

define('DS', '/');
define('InKHBuy', true);
define("SITE_CODE", "XXDEV");
define("PLUTOFLAG", "HAHAHA");
define('StartTime', microtime(true));
define('TIMESTAMP', time());

define('BASE_CORE_PATH', GLOBAL_ROOT . DS . 'core');
define('BASE_COMMON_PATH', GLOBAL_ROOT . DS . 'common');
define('BASE_DATA_PATH', PROJECT_ROOT . DS . 'data');
define('BASE_MODEL_PATH', GLOBAL_ROOT . DS . 'model');
define("_APP_CLASS_", PROJECT_ROOT . DS . "class");
define("_APP_COMMON_", PROJECT_ROOT . DS . "common");
define('_UPLOAD_', BASE_DATA_PATH . DS . 'upload');
define("_LOG_", BASE_DATA_PATH . DS . "log");
define("_CACHE_", BASE_DATA_PATH . DS . "cache");
define("_DATA_SCHEMA_", BASE_DATA_PATH . DS . "schema");//定义数据模型路径
define("_CONFIG_", BASE_DATA_PATH . DS . "config");
define("_DATA_MODEL_", BASE_DATA_PATH . DS . "model");

if (!is_dir(BASE_DATA_PATH)) {
    mkdir(BASE_DATA_PATH, 0755);
}
if (!is_dir(_CACHE_)) {
    mkdir(_CACHE_, 0755);
}
if (!is_dir(BASE_DATA_PATH . DS . 'session')) {
    mkdir(BASE_DATA_PATH . DS . 'session', 0755);
}
if (!is_dir(_DATA_SCHEMA_)) {
    mkdir(_DATA_SCHEMA_, 0755);
}
if (!is_dir(_UPLOAD_)) {
    mkdir(_UPLOAD_, 0755);
}
if (!is_dir(_LOG_)) {
    mkdir(_LOG_, 0755);
}

if (!defined("_DATA_PATH_")) define("_DATA_PATH_", BASE_DATA_PATH);

if (!@include(_CONFIG_ . DS . 'config.common.php')) exit('config.common.php isn\'t exists!');
global $config;

ini_set("session.gc_maxlifetime", 60 * 60 * 24);
ini_set("session.cache_expire", 60 * 24);
ini_set("session.cookie_lifetime", 60 * 60 * 24);
ini_set("max_execution_time", 60 * 3);//can be overrided.

require_once BASE_CORE_PATH . DS . "core.php";//通用函数、__autoload,__shutDown处理
require_once(BASE_CORE_PATH . DS . "RpcRouter.php");
require_once(BASE_CORE_PATH . DS . "inc.DzTemplate.php");
require_once(BASE_CORE_PATH . DS . "logger.php");
require_once(BASE_CORE_PATH . DS . "language.php");
require_once(BASE_CORE_PATH . DS . "tpl.php");
require_once(BASE_CORE_PATH . DS . "thumb.php");

define("_SITE_ROOT_", $config["site_root"]);
define("_IMAGE_ROOT_", $config["image_root"]);

define('COOKIE_PRE', $config['cookie_pre']);
define('SUBDOMAIN_SUFFIX', $config['subdomain_suffix']);
define('ENABLE_PSEUDO_STATIC_URL', $config['enable_pseudo_static_url']);