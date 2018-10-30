<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/7/12
 * Time: 22:59
 */
class userMonitorTaskClass extends userTaskBaseClass
{
    public function handleNewTask($task_id, $task_type, $receiver_id, $receiver_type = objGuidTypeEnum::UM_USER, $sender_id = 0, $sender_type = objGuidTypeEnum::SYSTEM,$msg="")
    {
        return new result(true);
    }

    public function cancelTask($task_id, $task_type, $handler_id, $handler_type = objGuidTypeEnum::UM_USER)
    {
      return new result(true);
    }

    public function finishTask($task_id, $task_type, $handler_id, $handler_type = objGuidTypeEnum::UM_USER, $args)
    {
      return new result(true);
    }

    public function getProcessingTask($task_id)
    {
        showMessage("Not Support Processing By MSG-Type");
    }
    /**
     * 获取任务的条数
     * @param $receiver_id
     * @param $last_time
     */
    public function getTaskPendingCount($receiver_id, $last_time,$receiver_type)
    {
        return array(
            $this->biz_code => array(
                "count_pending" => 0,
                "count_new" => 0,
            )
        );
        //showMessage("Not Implement");
    }

    public function getTaskPendingList($receiver_id,$receiver_type)
    {
        showMessage("Not Support");
    }
}
