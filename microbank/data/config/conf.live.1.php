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
        "db_host"=>"rm-j6clnr4nb13a9hd94.mysql.rds.aliyuncs.com",
        "db_user"=>"webserver",
        "db_pwd"=>"Ace-2015",
        "db_name"=>"samrithisak",
        "db_port"=>3306
    )
);

// 暂时使用
$config['session'] = array(
    'save_handler' => 'files',
    'save_path' => BASE_DATA_PATH.'/session'
);

$config['debug']=true;
$config['site_root'] = 'http://private.samrithisak.com';
$config['global_resource_site_url'] = "http://private.samrithisak.com/resource";
$config['project_site_url'] = "http://private.samrithisak.com/microbank";
$config['app_download_url'] = 'http://bank.samrithisak.com/data/downloads';
$config['entry_api_url'] = "http://private.samrithisak.com/microbank/api/v1";
$config['cookie_domain'] = "samrithisak.com";

$config['asiaweiluy_api'] = array(
    'entry_url' => 'https://api.asiaweiluy.com/gateway.php',
    'partner_id' => '8888',
    'partner_key' => 'ACE_API_KEY_SAMRITHISAK_TEBS'
);

$config['jpush_api'] = array(
    array(
        'entry_url' => 'https://api.jpush.cn/v3/push',
        'app_key' => '6c88c864d83ad3a16198d21d',
        'master_secret' => 'b75feef3c6c4d2ea84957545'
    ),
    array(
        'entry_url' => 'https://api.jpush.cn/v3/push',
        'app_key' => 'de9c49bc327c0ca630d2d855',
        'master_secret' => 'f4f48144f9890f85203692a3'
    )
);

//
//$config['upyun_param'] = array(
//    'api' => 'http://v0.api.upyun.com/',
//    'bucket' => 'bank-live',
//    'form_api' => 'fLMl2BeAViOiLO65VpH++T7x3ig=',
//    'user_name' =>  'khbank',
//    'pwd' =>  'Xx20171201',
//    'oss_url_prefix' =>  '',
//    'upyun_url' =>  'http://bank-live.test.upcdn.net',
//    'target_url' =>  'http://v0.api.upyun.com/bank-live',
//);

// 是否开启重置系统开关
$config['is_open_reset_system'] = 0;

$config['member_set_trading_password_way'] = 1;  // 0 登陆密码+身份证尾号  1 登陆密码+短信验证码

$config['api_google_map']='AIzaSyAGsv7VKM_GvEEOvgTlxrCFm7_hDtXWMHw';

$config['sms_api']='tencent';

$config['cache.type'] = 'file';
$config['redis'] = array(
    'master' => array(
        'host' => '127.0.0.1',
        'port' => '6379'
    )
);
