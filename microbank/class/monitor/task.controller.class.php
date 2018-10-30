<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/12/2018
 * Time: 9:43 AM
 * 任务的控制器
 */
class taskControllerClass
{
    public static function getOperationMonitorStructure()
    {
        return array(
            userTaskTypeEnum::OPERATOR_NEW_CLIENT => array("is_msg" => 0),
            userTaskTypeEnum::OPERATOR_NEW_CONSULT => array("is_msg" => 0),
            userTaskTypeEnum::OPERATOR_NEW_CERT => array("is_msg" => 0),
            userTaskTypeEnum::OPERATOR_RELATIVE_NEW_CERT => array('is_msg'=>0),
            userTaskTypeEnum::CHANGE_CLIENT_ICON => array("is_msg" => 0),
            userTaskTypeEnum::CHANGE_CLIENT_DEVICE => array("is_msg" => 0),
            userTaskTypeEnum::CLIENT_CHANGE_TRADING_PASSWORD => array('is_msg'=>0),
            userTaskTypeEnum::CO_SUBMIT_BM => array("is_msg" => 1),
            userTaskTypeEnum::BM_REJECT_CO => array("is_msg" => 1),
            userTaskTypeEnum::BM_NEW_CLIENT => array("is_msg" => 1),
            userTaskTypeEnum::BM_NEW_CONSULT => array("is_msg" => 1),
            userTaskTypeEnum::BM_REQUEST_FOR_CREDIT => array("is_msg" => 1),
            userTaskTypeEnum::OPERATOR_MY_CONSULT => array("is_msg" => 1),
            userTaskTypeEnum::MONITOR_OVERDUE_LOAN => array('is_msg' => 0)
        );
    }

    //获取一个用户的待处理任务数目
    public static function getPendingTaskCount($receiver_id, $last_time, $receiver_type = objGuidTypeEnum::UM_USER)
    {
        $arr = self::getOperationMonitorStructure();
        $ret = array();
        foreach ($arr as $code => $item) {
            $st = microtime(true);
            $cls = self::getTaskInstanceByType($code);
            $t1 = microtime(true) - $st;
            $ret = array_merge($ret, $cls->getTaskPendingCount($receiver_id, $last_time, $receiver_type));
            $t2 = microtime(true) - $st;
            if ($t2 > 1) {
                debug("t1=$t1, t2=" . ($t2-$t1) . ", params=" . json_encode(array($code, $receiver_id, $last_time, $receiver_type)));
            }
        }
        return $ret;
    }

    //获得一个msg类型任务的列表
    public static function getPendingTaskMsgList($receiver_id, $task_type, $receiver_type = objGuidTypeEnum::UM_USER)
    {
        $cls = self::getTaskInstanceByType($task_type);
        return $cls->getTaskPendingList($receiver_id, $receiver_type);
    }

    /**
     * @param $_type
     * @return userTaskBaseClass
     * @throws Exception
     */
    public static function getTaskInstanceByType($_type)
    {
        $arr = self::getOperationMonitorStructure();
        $item = $arr[$_type];
        if (!$item) {
            throw new Exception("Invalid Type:No Declare");
        }
        $str = ucwords(str_replace("_", " ", $_type));
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);
        $str = $str . "TaskClass";
        if (class_exists($str)) {
            $cls = new $str;
            if ($cls instanceof userTaskBaseClass) {
                $cls->biz_code = $_type;
                $cls->biz_setting = $item;
                return $cls;
            } else {
                throw new Exception("Invalid Type:Invalid Class Type");
            }
        } else {

            if ($item['is_msg']) {
                $cls = new userMsgTaskClass();
                $cls->biz_code = $_type;
                $cls->biz_setting = $item;
            } else {
                $cls = new userBizTaskClass();
                $cls->biz_code = $_type;
                $cls->biz_setting = $item;
            }
            return $cls;
        }
    }

    public static function handleNewTask($task_id, $task_type, $receiver_id, $receiver_type = objGuidTypeEnum::UM_USER, $sender_id = 0, $sender_type = objGuidTypeEnum::SYSTEM, $msg = "")
    {
        $cls = self::getTaskInstanceByType($task_type);
        $ret = $cls->handleNewTask($task_id, $task_type, $receiver_id, $receiver_type, $sender_id, $sender_type, $msg);
        return $ret;
    }

    //完成任务
    public static function finishTask($task_id, $task_type, $handler_id, $handler_type = objGuidTypeEnum::UM_USER, $args)
    {
        $cls = self::getTaskInstanceByType($task_type);
        $ret = $cls->finishTask($task_id, $task_type, $handler_id, $handler_type, $args);
        return $ret;
    }

    //

    public static function cancelBizTask($receiver_id)
    {
        $md = new task_user_bizModel();
        $item = $md->find(array(
            "receiver_id" => $receiver_id,
            "task_state" => userTaskStateTypeEnum::RUNNING
        ));
        if (!$item) {
            return new result(true);
        }
        return self::cancelTaskById($item['task_id'], $item['task_type'], $receiver_id);
    }

    //取消任务
    public static function cancelTaskById($task_id, $task_type, $handler_id, $handler_type = objGuidTypeEnum::UM_USER)
    {
        $cls = self::getTaskInstanceByType($task_type);
        $ret = $cls->cancelTask($task_id, $task_type, $handler_id, $handler_type);
        return $ret;
    }

    public static function getProcessingTask($receiver_id)
    {
        $md = new task_user_bizModel();
        $item = $md->find(array(
            "receiver_id" => $receiver_id,
            "task_state" => userTaskStateTypeEnum::RUNNING
        ));
        if (!$item) {
            return array();
        }

        $cls = self::getTaskInstanceByType($item['task_type']);
        $task = $cls->getProcessingTask($item['task_id']);
        return $task;
    }

    public static function startBizTask($receiver_id)
    {
        $task = self::getProcessingTask($receiver_id);
        if (!$task) {
            showMessage("No task is running");
        } else {
            $url = $task['url'];
            redirect($url);
        }
    }
}
