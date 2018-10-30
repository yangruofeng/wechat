<?php
$config['db_conf']=array(
    "db_loan"=>array(
        "db_type"=>"mysql",
        "db_host"=>"localhost",
        "db_user"=>"root",
        "db_pwd"=>"",
        "db_name"=>"microbank_dev_180914",
        "db_port"=>3306
    )
);

$config['debug']=true;
$config['site_root'] = 'http://local.khbuy.com/microbank_dev';
$config['global_resource_site_url'] = "http://local.khbuy.com/microbank_dev/resource";
$config['project_site_url'] = "http://local.khbuy.com/microbank_dev/microbank";

$config['entry_api_url'] = "http://local.khbuy.com/microbank_dev/microbank/api/v1";

$config['upload_site_url'] = 'http://local.khbuy.com/microbank_dev/microbank/data/upload';

$config['entry_root_url'] = 'http://local.khbuy.com/microbank_dev/entry';

$config['app_download_url'] = 'http://local.khbuy.com/microbank_dev/microbank/data/downloads';


$config['api_config'] = array(
    'appId' => 1,
    'appKey' => '6bc944bd-8886-11e7-81e6-ccb0daf5504e',
);

$config['asiaweiluy_api'] = array(
    'entry_url' => 'https://uat-api.asiaweiluy.com/gateway.php',
    'partner_id' => '8888',
    'partner_key' => 'MuXi7Yr3wKpiVxu4QpiY'
);

$config['jpush_api'] = array(
    array(
        'entry_url' => 'https://api.jpush.cn/v3/push',
        'app_key' => '8578562ebb25934b28fe5403',
        'master_secret' => '0706273884e8ed7605db37d4'
    )
);

$config['session']['save_handler'] = 'files';
$config['session']['save_path'] = BASE_DATA_PATH . DS . "session";

$config['cache.type'] = 'redis';
$config['redis'] = array(
    'master' => array(
        'host' => '127.0.0.1',
        'port' => '6379'
    )
);



