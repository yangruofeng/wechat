<?php
defined('InKHBuy') or exit('Access Invalid!');

$config['db_conf']=array(
    "db_local"=>array(
        "db_type"=>"mysql",
        "db_host"=>"rm-j6clnr4nb13a9hd94.mysql.rds.aliyuncs.com",
        "db_user"=>"webserver",
        "db_pwd"=>"Ace-2015",
        "db_name"=>"samrithisak",
        "db_port"=>3306
    ),
    "db_remote" => array(
        "db_type"=>"mysql",
        "db_host"=>"47.88.189.36",
        "db_user"=>"demo",
        "db_pwd"=>"Ace-2015",
        "db_name"=>"bank_dev",
        "db_port"=>3306
    )
);
$config['author'] = "Live";
$config['debug']=false;
$config['session'] = array(
    'save_handler' => 'redis',
    'save_path' => 'tcp://127.0.0.1:6379?weight=1&persistent=1&prefix=PHPREDIS_SESSION_BANK_LIVE&database=11'
);

$config['site_root'] = 'http://common.samrithisak.com';
$config['global_resource_site_url'] = "http://common.samrithisak.com/resource";
$config['project_site_url'] = "http://bank.samrithisak.com";
$config['app_download_url'] = 'http://bank.samrithisak.com/data/downloads';
$config['entry_api_url'] = "http://bank.samrithisak.com/api/v1";