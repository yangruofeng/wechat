<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/7/12
 * Time: 22:59
 */
class operatorNewConsultTaskClass extends userBizTaskClass
{
    public function afterHandle($task_id, $receiver_id)
    {
        $md = new loan_consultModel();
        $row = $md->getRow($task_id);
        if (!$row) {
            return new result(false, 'Invalid consult.');
        }
        $operator_id = $receiver_id;

        $obj_user = new objectUserClass($operator_id);
        $row->operator_id = $operator_id;
        $row->operator_name = $obj_user->user_name;
        $row->state = loanConsultStateEnum::LOCKED;
        $row->update_time = Now();
        return $row->update();
    }

    public function afterCancel($task_id, $receiver_id)
    {
        $md = new loan_consultModel();
        $row = $md->getRow($task_id);
        if (!$row) {
            return new result(false, 'Invalid consult.');
        }
        $row->operator_id = 0;
        $row->operator_name = '';
        $row->state = loanConsultStateEnum::CREATE;
        $row->update_time = Now();
        return $row->update();
    }

    public function afterFinish($task_id, $receiver_id, $param)
    {
        $operate_state = intval($param['operate_state']);
        $operate_remark = trim($param['operate_remark']);

        $m_loan_consult = new loan_consultModel();
        $row = $m_loan_consult->getRow($task_id);
        if (!$row) {
            return new result(false, 'Invalid consult.');
        }

        $row->operator_remark = $operate_remark;
        $row->update_time = Now();
        $row->state = $operate_state;
        $rt_1 = $row->update();
        if (!$rt_1->STS) {
            return new result(false, 'Handle Failed1.');
        } else {
            return new result(true, 'Handle Successful.');
        }
    }

    public function getProcessingTask($task_id)
    {
        $processing_task = array(
            'title' => "<New Consult>",
            'url' => getUrl('operator', 'operateLoanConsult', array('uid' => $task_id, 'show_menu_a' => "new_consult"), false, BACK_OFFICE_SITE_URL),
        );
        return $processing_task;
    }

    public function getTaskPendingCount($receiver_id, $last_time,$receiver_type)
    {
        $r=new ormReader();
        $sql = "SELECT a.*,b.`uid` FROM loan_consult a "
            . " LEFT JOIN task_user_biz b ON a.uid=b.task_id AND b.task_type='" . $this->biz_code . "' and b.task_state='" . userTaskStateTypeEnum::RUNNING . "'"
            ." LEFT JOIN client_member cm ON a.`member_id`=cm.uid"
            . " WHERE b.uid IS NULL AND a.state='" . loanConsultStateEnum::CREATE . "' AND (a.member_id=0 OR (a.`member_id`>0 AND cm.`operator_id` IS NOT NULL))";
        $list = $r->getRows($sql);
        $count_pending = count($list);
        $count_new = 0;
        foreach ($list as $item) {
            if ($item['create_time'] > $last_time) {
                $count_new += 1;
            }
        }
        return array(
            $this->biz_code => array(
                "count_pending" => $count_pending,
                "count_new" => $count_new
            )
        );
    }
}