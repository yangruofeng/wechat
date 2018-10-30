<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 9:56
 */

$config['db_conf']=array(
    "db_loan"=>array(
        "db_type"=>"mysql",
        "db_host"=>"localhost",
        "db_user"=>"root",
        "db_pwd"=>"root",
        "db_name"=>"micbank",  // micbank  bank_live
        "db_port"=>3306
    )
);

$config['session'] = array(
    'save_handler' => 'files',
    'save_path' => BASE_DATA_PATH.'/session'
);

$config['debug']=true;
$config['site_name'] = 'Microbank';

$config['site_root'] = 'http://localhost/microbank';
$config['global_resource_site_url'] = "http://localhost/microbank/resource";
$config['project_site_url'] = 'http://localhost/microbank/microbank';

$config['entry_api_url'] = "http://localhost/microbank/microbank/api/v1";

$config['upload_site_url'] = 'http://localhost/microbank/microbank/data/upload';

$config['entry_root_url'] = 'http://localhost/microbank/entry';

$config['app_download_url'] = 'http://localhost/microbank/microbank/data/downloads';

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

$config['member_set_trading_password_way'] = 1;  // 0 登陆密码+身份证尾号  1 登陆密码+短信验证码

$config['api_google_map']='AIzaSyDCPjrMiBD6X2qMRS6EfqBzq_ZY37GGLUA';



