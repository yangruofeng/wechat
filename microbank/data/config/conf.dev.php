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
        "db_host"=>"127.0.0.1",
        "db_user"=>"root",
        "db_pwd"=>"Ace-2015",
        "db_name"=>"bank_dev",
        "db_port"=>3306
    )
);

// 暂时使用
$config['session'] = array(
    'save_handler' => 'files',
    'save_path' => BASE_DATA_PATH.'/session'
);

$config['debug']=true;
$config['site_root'] = 'http://dev.samrithisak.com';
$config['global_resource_site_url'] = "http://dev.samrithisak.com/resource";
$config['project_site_url'] = "http://dev.samrithisak.com/microbank";
$config['app_download_url'] = 'http://dev.samrithisak.com/microbank/data/downloads';
$config['entry_api_url'] = "http://dev.samrithisak.com/microbank/api/v1";


$config['asiaweiluy_api'] = array(
    'entry_url' => 'https://uat-api.asiaweiluy.com/gateway.php',
    'partner_id' => '8888',
    'partner_key' => 'key_ace_uat_8888'
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

$config['sms_api']='tencent';

$config['cache.type'] = 'redis';
$config['redis'] = array(
    'master' => array(
        'host' => '127.0.0.1',
        'port' => '6379'
    )
);
