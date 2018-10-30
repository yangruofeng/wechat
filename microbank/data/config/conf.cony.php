<?php
$config['db_conf']=array(
    "db_loan"=>array(
        "db_type"=>"mysql",
        "db_host"=>"localhost",
        "db_user"=>"root",
        "db_pwd"=>"",
        "db_name"=>"bank_demo",
        "db_port"=>3306
    )
);

$config['debug']=true;
$config['site_root'] = 'http://localhost/microbank';
$config['global_resource_site_url'] = "http://localhost/microbank/resource";
$config['project_site_url'] = "http://localhost/microbank/microbank";
$config['upload_site_url'] = 'http://localhost/microbank/microbank/data/upload';
$config['entry_api_url'] = "http://localhost/microbank/entry/api/v1";

$config['asiaweiluy_api'] = array(
    'entry_url' => 'https://alpha-api.asiaweiluy.com/gateway.php',
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
