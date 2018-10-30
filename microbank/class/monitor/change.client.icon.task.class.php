<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/7/12
 * Time: 22:59
 */
class changeClientIconTaskClass extends userBizTaskClass
{
    public function afterHandle($task_id, $receiver_id)
    {
        $md = new member_change_photo_requestModel();
        $row = $md->getRow($task_id);
        if (!$row) {
            return new result(false, 'Invalid consult.');
        }
        $operator_id = $receiver_id;

        $obj_user = new objectUserClass($operator_id);
        $row->operator_id = $operator_id;
        $row->operator_name = $obj_user->user_name;
        $row->state = commonApproveStateEnum::APPROVING;
        $row->update_time = Now();
        return $row->update();
    }

    public function afterCancel($task_id, $receiver_id)
    {
        $md = new member_change_photo_requestModel();
        $row = $md->getRow($task_id);
        if (!$row) {
            return new result(false, 'Invalid consult.');
        }
        $row->operator_id = 0;
        $row->operator_name = '';
        //$row->state = commonApproveStateEnum::CANCEL;  // 这是一个BUG，领取任务没有处理取消后，导致别人也不能再处理
        $row->state = commonApproveStateEnum::CREATE;
        $row->update_time = Now();
        return $row->update();
    }

    public function afterFinish($task_id, $receiver_id, $param)
    {
        $rt = credit_officerClass::checkMemberChangePhoto($param);
        return $rt;
    }

    public function getProcessingTask($task_id)
    {
        $processing_task = array(
            'title' => "<New Change Member Icon>",
            'url' => getUrl('operator', 'clientChangePhotoDetail', array('uid' => $task_id, 'show_menu_a' => "client_change_photo"), false, BACK_OFFICE_SITE_URL),
        );
        return $processing_task;
    }

    public function getTaskPendingCount($receiver_id, $last_time,$receiver_type)
    {
        //目前让所有operator都可以check
        $r = new ormReader();
        $sql = "SELECT a.*,b.`uid` FROM member_change_photo_request a "
            . "LEFT JOIN task_user_biz b ON a.uid=b.task_id AND b.task_type='" . $this->biz_code . "' and b.task_state='" . userTaskStateTypeEnum::RUNNING . "'"
            . "WHERE b.uid IS NULL AND a.state='" . commonApproveStateEnum::CREATE . "'";
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