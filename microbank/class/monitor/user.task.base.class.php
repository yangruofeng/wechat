<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/7/12
 * Time: 22:58
 */
abstract class userTaskBaseClass
{
    public $biz_code = "";
    public $biz_setting = array();
    public $task_title = "New Task";

    abstract function handleNewTask($task_id, $task_type, $receiver_id, $receiver_type = objGuidTypeEnum::UM_USER, $sender_id = 0, $sender_type = objGuidTypeEnum::SYSTEM,$msg="");

    abstract function cancelTask($task_id, $task_type, $handler_id, $handler_type = objGuidTypeEnum::UM_USER);

    abstract function finishTask($task_id, $task_type, $handler_id, $handler_type = objGuidTypeEnum::UM_USER, $args);

    abstract function getProcessingTask($task_id);
    //获取待处理任务的条数
    abstract function getTaskPendingCount($receiver_id,$last_time,$receiver_type);
    //获取待处理任务的列表
    abstract function getTaskPendingList($receiver_id,$receiver_type);
}