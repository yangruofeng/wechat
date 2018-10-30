<?php
//Notes: common, can be overrided
$config = array(
    "class_path_a" => array(),//需要搜索class的路径
    "register_shutdown_function" => "_shutdown_function",//default func for shutdown to check any error
    "mc" => array(
        'host' => 'localhost',
        'port' => 11211,
    ),
    "SERVER_TIMEZONE" => "Asia/Phnom_Penh",//default timezone jinbian Time
    "_S" => '_s',
    'fileExt' => array(
        'image' => array('JPG', 'BMP', 'GIF', 'PNG'),
        'video' => array('AVI', 'FLV', 'F4V', 'WMV', '3GP')
    ),
    "default_lang" => "en",//默认语言
    "lang_pack" => "_pack/lang_pack.xls",//语言包
    "orm_pack" => "_pack/orm_pack.xls",//orm-schema包
    "lang_type_list" => array(
        "en" => "English",
        "zh_cn" => "简体中文",
//		"zh_tw"=>"繁体中文",
        "kh" => "ខ្មែរ",
        'vn' => 'Tiếng Việt',
        'id' => 'Indonesia',
        'thai' => 'ภาษาไทย'
    ),
    'content_cache_key' => array(),
    'charset' => 'UTF-8',
    "tablepre" => "dig_",
    "default_currency_sign" => "$",
    "expire_timeout" => 3600 * 24,   // 24小时
    "cache.expire" => 24*3600,
    "cache.type" => "file"
);

if (!@include(GLOBAL_ROOT . '/config.switch.override.tmp')) {
    $_switch_conf = "conf.local";
}

$config_file = $_switch_conf . ".php";
if (!@include($config_file)) exit($config_file . ' isn\'t exists!');

if (isset($_SERVER['HTTP_X_AUTO_CERT_PROXY']) && $_SERVER['HTTP_X_AUTO_CERT_PROXY'] == "1") {
    if ($config['site_root']) {
        $config['site_root'] = str_replace('http://', 'https://', $config['site_root']);
    }
    if ($config['image_root']) {
        $config['image_root'] = str_replace('http://', 'https://', $config['image_root']);
    }
    if ($config['data_share_site']) {
        $config['data_share_site'] = str_replace('http://', 'https://', $config['data_share_site']);
    }
    if ($config['shop_url']) {
        $config['shop_url'] = str_replace('http://', 'https://', $config['shop_url']);
    }
    if ($config['pos_service']) {
        $config['pos_service'] = str_replace('http://', 'https://', $config['pos_service']);
    }
    if ($config['money_url']) {
        $config['money_url'] = str_replace('http://', 'https://', $config['money_url']);
    }
    if ($config['money_admin']) {
        $config['money_admin'] = str_replace('http://', 'https://', $config['money_admin']);
    }
}
