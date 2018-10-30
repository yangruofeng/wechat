<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/16
 * Time: 17:13
 */
class loan_disburse_scheme_script_taskModel extends tableModelBase
{

    public function __construct()
    {
        parent::__construct('loan_disburse_scheme_script_task');
    }


    public function scriptExecutingPrepare($schemeId) {

        $script_info = $this->getRow(array('scheme_id' => $schemeId));
        if (!$script_info) {
            $script_info = $this->newRow();
            $script_info->scheme_id = $schemeId;
            $script_info->is_suspended = 0;
            $script_info->last_execute_time = time();
            $script_info->last_error_code = 0;
            $script_info->finish_time = null;
            $rt = $script_info->insert();
            if (!$rt->STS) {
                return new result(false, $rt->MSG, null, errorCodesEnum::DB_ERROR);
            }
        } else {
            if ($script_info->is_suspended)
                return new result(false, 'Task is suspended', errorCodesEnum::INVALID_STATE);
            if ($script_info->next_execute_time > time())
                return new result(false, 'Cannot retry now', errorCodesEnum::INVALID_STATE);

            $script_info->is_suspended = 1;
            $script_info->last_execute_time = time();
            $script_info->last_error_code = 0;
            $script_info->finish_time = null;
            $rt = $script_info->update();
            if (!$rt->STS) {
                return new result(false, $rt->MSG, null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true);
    }

    public function scriptExecutingFinish($schemeId, $errorCode,$error_msg=null, $isSuspended=0, $nextExecuteTime=0) {

        $m = $this;
        $script_info = $m->getRow(array('scheme_id' => $schemeId));

        if (!$script_info) {
            return new result(false, "Task not found", null, errorCodesEnum::UNEXPECTED_DATA);
        }
        if ($nextExecuteTime<0)
            $nextExecuteTime = 0;

        $script_info->is_suspended = $isSuspended;
        $script_info->last_error_code = $errorCode;
        $script_info->last_error_msg = $error_msg;
        $script_info->next_execute_time = $nextExecuteTime ?: $script_info->last_execute_time + C("retry_interval_task_script_failed");
        $script_info->finish_time = date("Y-m-d H:i:s");
        $rt = $script_info->update();
        if (!$rt->STS) {
            return new result(false, $rt->MSG, null, errorCodesEnum::DB_ERROR);
        } else {
            return new result(true);
        }
    }
}