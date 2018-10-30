<?php
//Notes: common, can be overrided
$config = array(
    "class_path_a" => array(),//需要搜索class的路径
    "register_shutdown_function" => "_shutdown_function",//default func for shutdown to check any error
    "SERVER_TIMEZONE" => "Asia/Phnom_Penh",//default timezone jinbian Time
//    "session" => array(
//        'save_handler' => 'files',
//        'save_path' => BASE_DATA_PATH . DS . "session"
//    ),
    "lang_type_list" => array(
        "en" => "English",
        "zh_cn" => "简体中文",
        "kh" => "Khmer"
    ),
    "currency" => array(
        'USD' => 'USD',
        'KHR' => 'KHR',
        'CNY' => 'CNY',
    ),
    "cache.expire" => 24*3600,
    "cache.type" => "file"
);

$config['session']['save_handler'] = 'redis';
$config['session']['save_path'] = 'tcp://127.0.0.1:6379?weight=1&persistent=1&prefix=PHPREDIS_SESSION_BANK_DEMO_&database=11';

$config['oss_target'] = 'upyun';

//upyun配置
$config['upyun_param'] = array(
    'api' => 'http://v0.api.upyun.com/',
    'bucket' => 'bank-demo',
    'form_api' => '8mFFpMueTzJfESGJuHIuPdIGhuw=',
    'user_name' =>  'khbank',
    'pwd' =>  'Xx20171201',
    'oss_url_prefix' =>  '',
    'upyun_url' =>  'http://bankimage.khbuy.com',
    'target_url' =>  'http://v0.api.upyun.com/bank-demo',
);

//短信开关
$config['sms_api']='yunpian';
$config['app_secret_key'] = '6bc944bd-8886-11e7-81e6-ccb0daf5504e';

// 邮件配置
$config['site_name'] = 'Microbank';
$config['email_host'] = 'smtp.exmail.qq.com';
$config['email_port'] = '465';
$config['email_id'] = 'service@khbuy.com';
$config['email_pass'] = 'Kh-20170614';
$config['email_addr'] = 'service@khbuy.com';


if (!@include(GLOBAL_ROOT . '/config.switch.override.tmp')) {
    $_switch_conf = "conf.local";
}

//google-map的api
$config['api_google_map']='AIzaSyDpiK4nCWqTGpv2sVKxodYkDv8mUaXak7g';

// 自动任务失败重试的时间间隔/秒
// 只用作一般性的错误
$config['retry_interval_task_script_failed'] = 3600;
$config['sp_version']=0;
$config_file = $_switch_conf . ".php";
if (!@include($config_file)) exit($config_file . ' isn\'t exists!');
$special_config_file = $_switch_conf . "." . $_SERVER['HTTP_HOST'] . ".php";
@include($special_config_file);

