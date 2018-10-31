<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/22
 * Time: 17:34
 */

require_once(dirname(__FILE__).'/../include_common.php');
ini_set('max_execution_time',0);

$url_prefix = rtrim(SCRIPT_SITE_URL,'/').'/';

while( true ){

    $task_list = include(dirname(__FILE__).'/../script.task.list.php');

    foreach( $task_list as $task ){
        try {
            echo date('Y-m-d H:i:s').' :'.$task['name']."\n";
            $re = @file_get_contents($url_prefix.$task['url']);
            print_r($re);
        } catch(Exception $ex) {
            echo "Task executing error: " . $ex->getMessage();
            print_r($ex);
        }
        echo "\n";
    }

    sleep(5);
}