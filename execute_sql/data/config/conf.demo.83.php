<?php
defined('InKHBuy') or exit('Access Invalid!');

$config['db_conf']=array(
    "db_point"=>array(
        "db_type"=>"mysql",
        "db_host"=>"127.0.0.1",
        "db_user"=>"root",
        "db_pwd"=>"Ace-2015",
        "db_name"=>"game_demo",
        "db_port"=>3306
    )
);
$config['debug']=true;
$config['session'] = array(
    'save_handler' => 'redis',
    'save_path' => 'tcp://127.0.0.1:6379?weight=1&persistent=1&prefix=PHPREDIS_SESSION_POINT_DEMO&database=11'
);

$config['global_resource_site_url'] = "http://point.mekong24.com/resource";
$config['project_resource_site_url'] = "http://point.mekong24.com/entry/resource";
$config['desktop_site_url'] = "http://point.mekong24.com/game_dig/desktop";
$config['api_site_url'] = "http://point.mekong24.com/game_dig/api";

$config['game_api'] = array(
    'entry_url' => 'http://point.mekong24.com/entry/api/v1/',
    'app_id' => 6,
    'app_key' => '620e854f-8949-11e7-90c4-52540074bbbd'
);