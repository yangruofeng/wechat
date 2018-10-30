<?php
$config['db_conf'] = array(
    "db_local" => array(
        "db_type" => "mysql",
        "db_host" => "localhost",
        "db_user" => "root",
        "db_pwd" => "",
        "db_name" => "bank_demo",
        "db_port" => 3306
    ),
    "db_remote" => array(
        "db_type" => "mysql",
        "db_host" => "47.88.189.36",
        "db_user" => "demo",
        "db_pwd" => "Ace-2015",
        "db_name" => "bank_dev",
        "db_port" => 3306
    )
);
$config['author'] = "cony";
$config['debug'] = true;
$config['session'] = array(
    'save_handler' => 'files',
    'save_path' => BASE_DATA_PATH . DS . "session"
);

$config['global_resource_site_url'] = "http://localhost/microbank/resource";
