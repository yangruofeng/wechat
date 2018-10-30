<?php
$config['db_conf']=array(
    /*"db_loan"=>array(
        "db_type"=>"mysql",
        "db_host"=>"47.88.189.36",
        "db_user"=>"demo",
        "db_pwd"=>"Ace-2015",
        "db_name"=>"bank_demo",
        "db_port"=>3306
    )*/
  "db_loan"=>array(
        "db_type"=>"mysql",
        "db_host"=>"localhost",
        "db_user"=>"root",
        "db_pwd"=>"",
        "db_name"=>"bank_dev",
        "db_port"=>3306
    )
);

$config['debug'] = true;
$config['session'] = array(
    'save_handler' => 'files',
    'save_path' => BASE_DATA_PATH.'/session'
);

$config['debug']=true;
$config['site_name'] = 'Microbank';

$config['site_root'] = 'http://localhost/bank';
$config['global_resource_site_url'] = "http://localhost/bank/resource";
$config['project_site_url'] = 'http://localhost/bank/microbank';

$config['entry_api_url'] = "http://localhost/bank/microbank/api/v1";

$config['upload_site_url'] = 'http://localhost/bank/microbank/data/upload';

$config['entry_root_url'] = 'http://localhost/bank/entry';

$config['app_download_url'] = 'http://localhost/bank/microbank/data/downloads';

$config['asiaweiluy_api'] = array(
    'entry_url' => 'http://203.90.246.212:8791/gateway.php',
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

// 是否开启重置系统开关
$config['is_open_reset_system'] = 0;
