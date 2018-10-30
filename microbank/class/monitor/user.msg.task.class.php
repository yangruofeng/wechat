<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/7/12
 * Time: 22:59
 */
class userMsgTaskClass extends userTaskBaseClass
{
    public function handleNewTask($task_id, $task_type, $receiver_id, $receiver_type = objGuidTypeEnum::UM_USER, $sender_id = 0, $sender_type = objGuidTypeEnum::SYSTEM, $msg = "")
    {
        $md = new task_user_msgModel();
        //再判断这个任务是否在别人手中
        $chk = $md->find(array(
            "task_id" => $task_id,
            "task_type" => $task_type,
            "receiver_id" => $receiver_id,
            "task_state" => userTaskStateTypeEnum::RUNNING
        ));
        if ($chk) {
            return new result(true);
        }
        if (!$msg) {
            if ($sender_type == objGuidTypeEnum::UM_USER) {
                $obj_user = new objectUserClass($sender_id);
                $sender_name = $obj_user->user_name;
            } else {
                $sender_name = "SYSTEM";
            }
            $msg = $sender_name . " Transfer 【" . $this->task_title . "】 To You At " . Now();
        }

        $row = $md->newRow(array(
            "task_id" => $task_id,
            "task_type" => $task_type,
            "sender_id" => $sender_id,
            "sender_type" => $sender_type,
            "receiver_id" => $receiver_id,
            "receiver_type" => $receiver_type,
            "task_state" => userTaskStateTypeEnum::RUNNING,
            "msg" => $msg,
            "create_time" => Now(),
            "update_time" => Now()
        ));
        return $row->insert();
    }

    public function cancelTask($task_id, $task_type, $handler_id, $handler_type = objGuidTypeEnum::UM_USER)
    {
        $md = new task_user_msgModel();
        $row = $md->getRow(array(
            "receiver_id" => $handler_id,
            "receiver_type" => $handler_type,
            "task_id" => $task_id,
            "task_type" => $task_type,
            "task_state" => userTaskStateTypeEnum::RUNNING
        ));
        if (!$row) {
            return new result(false, "No Task Found!");
        }
        $row->task_state = userTaskStateTypeEnum::CANCEL;
        $row->update_time = Now();
        return $row->update();
    }

    public function finishTask($task_id, $task_type, $handler_id, $handler_type = objGuidTypeEnum::UM_USER, $args)
    {
        $md = new task_user_msgModel();
        $row = $md->getRow(array(
            "receiver_id" => $handler_id,
            "receiver_type" => $handler_type,
            "task_id" => $task_id,
            "task_type" => $task_type,
            "task_state" => userTaskStateTypeEnum::RUNNING
        ));
        if (!$row) {
            return new result(false, "No Task Found!");
        }
        $row->task_state = userTaskStateTypeEnum::DONE;
        $row->update_time = Now();
        return $row->update();
    }

    public function getProcessingTask($task_id)
    {
        showMessage("Not Support Processing By MSG-Type");
    }

    /**
     * 获取任务的条数
     * @param $receiver_id
     * @param $last_time
     * @param $receiver_type
     * @return array
     */
    public function getTaskPendingCount($receiver_id, $last_time, $receiver_type)
    {
        $md = new task_user_msgModel();
        $list = $md->select(array(
            "receiver_id" => $receiver_id,
            "task_type" => $this->biz_code,
            "task_state" => userTaskStateTypeEnum::RUNNING
        ));
        $count_pending = count($list);
        $count_new = 0;
        foreach ($list as $item) {
            if ($item['update_time'] > $last_time) {
                $count_new += 1;
            }
        }
        return array(
            $this->biz_code => array(
                "count_pending" => $count_pending,
                "count_new" => $count_new,
                "is_msg" => 1
            )
        );
        //showMessage("Not Implement");
    }

    public function getTaskPendingList($receiver_id, $receiver_type)
    {
        $md = new task_user_msgModel();
        $list = $md->select(array(
            "receiver_id" => $receiver_id,
            "task_type" => $this->biz_code,
            "task_state" => userTaskStateTypeEnum::RUNNING
        ));
        return $list;
    }
}
