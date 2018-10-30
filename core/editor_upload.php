<?php
error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);
define("InKHBuy", 1);
define("SITE_CODE","XXDEV");
define("DS","/");
define("_UPLOAD_","../../app_ext");
ini_set('date.timezone','Asia/Shanghai');//时区

include_once("../_config/config.common.php");
define("_IMAGE_ROOT_",$config['image_root']);
include_once("imageHandler.php");
include_once("UploadFile.php");

$upload = new UploadFile();
$inputName='filedata';
$result = $upload->upload($inputName);
$err=$upload->error;
$msg=$upload->full_path;

echo "{'err':'".$upload->jsonString($err)."','msg':'".$upload->jsonString($msg)."'}";
?>
