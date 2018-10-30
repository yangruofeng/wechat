<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/2
 * Time: 14:22
 */


$config['db_conf']=array(
    "db_loan"=>array(
        "db_type"=>"mysql",
        "db_host"=>"localhost",
        "db_user"=>"root",
        "db_pwd"=>"root",
        "db_name"=>"bank_test",
        "db_port"=>3306
    )
);

$config['debug']=true;
$config['site_root'] = 'http://localhost/microbank_tim';
$config['global_resource_site_url'] = "http://localhost/microbank_tim/resource";
$config['project_resource_site_url'] = "http://localhost/microbank_tim/microbank/resource";

$config['entry_api_url'] = "http://localhost/microbank_tim/entry/api/v1";
$config['bank_api_url'] = "http://localhost/microbank_tim/microbank/api/v2";