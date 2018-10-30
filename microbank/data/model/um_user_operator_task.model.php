<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 11:20
 */
class um_user_operator_taskModel extends tableModelBase
{
    public $user_id;

    public function __construct($user_id)
    {
        parent::__construct('um_user_operator_task');
        $this->user_id = $user_id;
    }

    public function insertTask($task_id, $task_type)
    {
        $row = $this->newRow();
        $row->user_id = $this->user_id;
        $row->task_type = $task_type;
        $row->task_id = $task_id;
        $row->task_state = 10;//正在进行任务 0取消任务  100完成任务
        $row->insert_time = Now();
        $rt = $row->insert();
        return $rt;
    }

    public function isHandleTask($task_id, $task_type)
    {
        $row = $this->find(array('user_id' => $this->user_id, 'task_state' => 10));
        if ($row && !($row['task_type'] == $task_type && $row['task_id'] == $task_id)) {
            return false;
        } else {
            return true;
        }
    }

    public function checkCurrentTask($task_id, $task_type)
    {
        $chk = $this->find(array('user_id' => $this->user_id, 'task_id' => $task_id, 'task_type' => $task_type, 'task_state' => 10));
        return $chk ? true : false;
    }

    public function updateTaskState($task_id, $task_type, $task_state = 0)
    {
        $row = $this->getRow(array('user_id' => $this->user_id, 'task_id' => $task_id, 'task_type' => $task_type, 'task_state' => 10));
        if (!$row) {
            return new result(false, 'Param Error!');
        }
        $row->task_state = $task_state;
        $row->update_time = Now();
        return $row->update();
    }
}