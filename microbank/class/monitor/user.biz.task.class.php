<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/7/12
 * Time: 22:58
 */
class userBizTaskClass extends userTaskBaseClass
{
    public function handleNewTask($task_id, $task_type, $receiver_id, $receiver_type = objGuidTypeEnum::UM_USER, $sender_id = 0, $sender_type = objGuidTypeEnum::SYSTEM, $msg = "")
    {
        $md = new task_user_bizModel();
        //先判断自己是否有任务
        $chk = $md->find(array(
            "receiver_id" => $receiver_id,
            "receiver_type" => $receiver_type,
            "task_state" => userTaskStateTypeEnum::RUNNING
        ));
        if ($chk) {
            return new result(false, "You can\'t deal with new task before finish the suspended task.");
        }
        //再判断这个任务是否在别人手中
        $chk = $md->find(array(
            "task_id" => $task_id,
            "task_type" => $task_type,
            "task_state" => array("neq", userTaskStateTypeEnum::CANCEL)
        ));
        if ($chk) {
            if ($chk['receiver_id'] != $receiver_id) {
                return new result(false, "This task has been locked by others!");
            }
            if ($chk['task_state'] != userTaskStateTypeEnum::RUNNING) {
                return new result(false, "This task has been already finished");
            }
            return new result(true);
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $row = $md->newRow(array(
            "task_id" => $task_id,
            "task_type" => $task_type,
            "sender_id" => $sender_id,
            "sender_type" => $sender_type,
            "receiver_id" => $receiver_id,
            "receiver_type" => $receiver_type,
            "task_state" => userTaskStateTypeEnum::RUNNING,
            "create_time" => Now(),
            "update_time" => Now()
        ));
        $ret = $row->insert();
        if (!$ret->STS) {
            $conn->rollback();
            return $ret;
        }
        $ret = $this->afterHandle($task_id, $receiver_id);
        if (!$ret->STS) {
            $conn->rollback();
            return $conn;
        }
        $conn->submitTransaction();
        return $ret;
    }

    public function afterHandle($task_id, $receiver_id)
    {
        return new result(true);
    }

    public function cancelTask($task_id, $task_type, $handler_id, $handler_type = objGuidTypeEnum::UM_USER)
    {
        $md = new task_user_bizModel();
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
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $row->task_state = userTaskStateTypeEnum::CANCEL;
        $row->update_time = Now();
        $ret = $row->update();
        if (!$ret->STS) {
            $conn->rollback();
            return $ret;
        }
        $ret = $this->afterCancel($task_id, $handler_id);
        if (!$ret->STS) {
            $conn->rollback();
            return $conn;
        }
        $conn->submitTransaction();
        return $ret;

    }

    public function afterCancel($task_id, $receiver_id)
    {
        return new result(true);
    }

    public function finishTask($task_id, $task_type, $handler_id, $handler_type = objGuidTypeEnum::UM_USER, $args)
    {
        $md = new task_user_bizModel();
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
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $row->task_state = userTaskStateTypeEnum::DONE;
        $row->update_time = Now();
        $ret = $row->update();
        if (!$ret->STS) {
            $conn->rollback();
            return new result(false, "Update task failed!");
        }
        $ret = $this->afterFinish($task_id, $handler_id, $args);
        if (!$ret->STS) {
            $conn->rollback();
            return $ret;
        }
        $conn->submitTransaction();
        return $ret;
    }

    public function afterFinish($task_id, $receiver_id, $args)
    {
        return new result(true);
    }

    public function getProcessingTask($task_id)
    {
        showMessage("Not Implement");
    }

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