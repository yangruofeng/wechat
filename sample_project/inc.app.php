<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/1/2015
 * Time: 8:26 PM
 */
//初始化yo
require_once(BASE_CORE_PATH."/ormYo.php");
require_once(BASE_CORE_PATH."/Yo.php");
$dsn=$GLOBALS['config']['db_conf'];//getConf('db_conf');
ormYo::setup($dsn);
ormYo::$default_db_key="db_point";
ormYo::$lang_current="en";
ormYo::$freez=!$GLOBALS['config']['debug'];//==true;//是否冻结
ormYo::$log_path=_LOG_;//日志路径
//ormYo::$IDField="uid";//表的自增列
ormYo::$schema_path=_DATA_SCHEMA_."/";//datasource保存路径
ormYo::$lang_a=getLangTypeList();

require_once(_APP_COMMON_."/define_enum.php");

global $config;
define('GLOBAL_RESOURCE_SITE_URL',$config['global_resource_site_url']);
define('PROJECT_RESOURCE_SITE_URL',$config['project_resource_site_url']);
define('CURRENT_RESOURCE_SITE_URL',"resource"); // 直接使用resource目录

define('UPLOAD_SITE_URL',$config['upload_site_url']);
define('ATTACH_AVATAR','shop/avatar');
define('ATTACH_COMMON','shop/common');

define('DESKTOP_SITE_URL', $config['desktop_site_url']);
define('SHR_SITE_URL', $config['shr_site_url']);