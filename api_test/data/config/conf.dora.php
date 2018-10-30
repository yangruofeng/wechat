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
        "db_pwd"=>"",
        "db_name"=>"loan",
        "db_port"=>3306
    )
);

$config['debug']=true;
$config['site_root'] = 'http://localhost/microbank';
$config['global_resource_site_url'] = "http://localhost/microbank/resource";
$config['project_resource_site_url'] = "http://localhost/microbank/microbank/resource";

$config['entry_api_url'] = "http://localhost/microbank/entry/api/v1";
$config['bank_api_url'] = "http://localhost/microbank/microbank/api/v1";
