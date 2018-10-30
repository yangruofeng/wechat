<?php


$config['db_conf']=array(
    "db_point"=>array(
        "db_type"=>"mysql",
        "db_host"=>"localhost",
        "db_user"=>"root",
        "db_pwd"=>"root",
        "db_name"=>"microbank",
        "db_port"=>3306
    )
);

$config['session'] = array(
    'save_handler' => 'files',
    'save_path' => BASE_DATA_PATH.'/session'
);

$config['debug']= true;

$config['tablepre'] = '';

$config['global_resource_site_url'] = "http://localhost/microbank/resource";
$config['project_resource_site_url'] = "http://localhost/microbank/test/resource";
$config['current_resource_site_url'] = 'http://localhost/microbank/test/desktop/resource';

$config['root_url'] = 'http://localhost/microbank/test';
$config['desktop_site_url'] = 'http://localhost/microbank/test/desktop';
$config['api_site_url'] = 'http://localhost/microbank/test/api';




