<?php
/* vim: set tabstop=2 shiftwidth=2 softtabstop=2: */
error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);
//error_reporting(E_ALL);


if (!defined("_ROOT_")){ throw new Exception("//_ROOT_ is not defined."); }
if (!defined("_CORE_PHP_")){ throw new Exception("//_ROOT_PHP_ is not defined."); }
if (!defined("_CORE_WEB_")){ throw new Exception("//_ROOT_WEB_ is not defined."); }
if (!defined("_APP_CLASS_")){ throw new Exception("//_ROOT_APP_ is not defined."); }


define("_LOG_",realpath(_ROOT_."/../../logs/"));
if(!is_dir(_ROOT_."/../../logs/")){
	mkdir(_ROOT_."/../../logs/",0755);
}


//file handler
/*
if(!defined("_TMP_")) define("_TMP_",realpath(_ROOT_."/../../tmp/"));

if(!is_dir(_TMP_ .'/session/')){
	mkdir(_TMP_ .'/session/');
}
*/

if(!is_dir(_ROOT_.'/../../app_ext/')){
	mkdir(_ROOT_.'/../../app_ext/',0755);
}
if(!defined("_UPLOAD_")) define("_UPLOAD_","../../app_ext");

if(!is_dir(_ROOT_.'/../../data/')){
    mkdir(_ROOT_.'/../../data/',0755);
}
if(!is_dir(_ROOT_.'/../../data/cache/')){
    mkdir(_ROOT_.'/../../data/cache/',0755);
}
if(!is_dir(_ROOT_.'/../../data/session/')){
    mkdir(_ROOT_.'/../../data/session/',0755);
}
if(!is_dir(_ROOT_.'/../../data/schema/')){
    mkdir(_ROOT_.'/../../data/schema/',0755);
}
if(!is_dir(_ROOT_.'/../../data/file/')){
    mkdir(_ROOT_.'/../../data/file/',0755);
}
define("_DATA_SCHEMA_",realpath(_ROOT_."/../../data/schema"));//定义数据模型路径

define("_DATA_FILE_",realpath(_ROOT_."/../../data/file"));





if(!defined("_DATA_PATH_")) define("_DATA_PATH_",realpath(_ROOT_."/../../data"));
//session_save_path(_DATA_PATH_ ."/session/");
//more other php setting for session for this app
//ini_set("session.use_cookies",0);//no cookie for session
//session_set_cookie_params(60*20);
//ini_set('session.cookie_domain', 'mega.v5');
//ini_set("session.name","PHPSESSID");//tmp.solution for phprpc
//ini_set("session.save_handler", "memcache");
//ini_set("session.save_path", "tcp://127.0.0.1:11211");
//ini_set("session.save_path","tcp://server:port?persistent=1&amp;weight=1&amp;timeout=1&amp;retry_interval=15");

ini_set("session.gc_maxlifetime",60*60*24);
ini_set("session.cache_expire",60*24);
ini_set("session.cookie_lifetime",60*60*24);
ini_set("max_execution_time", 60*3);//can be overrided.


require_once _CORE_PHP_."/core.php";//通用函数、__autoload,__shutDown处理
require_once(_CORE_PHP_."/RpcRouter.php");
require_once(_CORE_PHP_."/inc.DzTemplate.php");
require_once(_CORE_PHP_."/logger.php");
require_once(_CORE_PHP_."/language.php");
require_once(_CORE_PHP_."/tpl.php");
require_once(_CORE_PHP_."/thumb.php");

define("_SITE_ROOT_",$config["site_root"]);
define("_IMAGE_ROOT_",$config["image_root"]);




   

