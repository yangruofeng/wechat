<?php

require_once(_APP_COMMON_."/api_doc.php");

global $config;
define('GLOBAL_RESOURCE_SITE_URL',$config['global_resource_site_url']);
define('PROJECT_RESOURCE_SITE_URL',$config['project_resource_site_url']);
define('CURRENT_RESOURCE_SITE_URL',"resource"); // 直接使用resource目录