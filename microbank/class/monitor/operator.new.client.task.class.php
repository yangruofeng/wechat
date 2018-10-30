<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/7/12
 * Time: 22:59
 */
class operatorNewClientTaskClass extends userBizTaskClass
{
    public function afterHandle($task_id, $receiver_id)
    {
        $member_id = $task_id;
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            return new result(false, 'Invalid member.');
        }
        $operator_id = $receiver_id;

        $obj_user = new objectUserClass($operator_id);
        $row->operator_id = $operator_id;
        $row->operator_name = $obj_user->user_name;
        $row->operate_state = newMemberCheckStateEnum::LOCKED;
        $row->operate_time = Now();
        $row->update_time = Now();
        return $row->update();
    }

    public function afterCancel($task_id, $receiver_id)
    {
        $member_id = $task_id;
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            return new result(false, 'Invalid member.');
        }
        $row->operator_id = 0;
        $row->operator_name = '';
        $row->operate_state = newMemberCheckStateEnum::CREATE;
        $row->operate_time = Now();
        $row->update_time = Now();
        return $row->update();
    }

    public function afterFinish($task_id, $receiver_id, $param)
    {
        $param['handler_id'] = $receiver_id;

        $member_id = intval($param['member_id']);
        $operate_state = intval($param['operate_state']);
        $operate_remark = trim($param['operate_remark']);
        $work_type = trim($param['work_type']);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            return new result(false, 'Invalid member.');
        }
        $operator_id = $param['handler_id'];
        $obj_user = new objectUserClass($operator_id);
        $row->operator_id = $operator_id;
        $row->operator_name = $obj_user->user_name;
        $row->operate_state = $operate_state;
        $row->operate_remark = $operate_remark;
        $row->operate_time = Now();
        $row->update_time = Now();
        $row->work_type = $work_type;

        $member_property = my_json_decode($row->member_property);
        if ($operate_remark == newMemberCheckStateEnum::CLOSE) {
            $member_property[memberPropertyKeyEnum::ORIGINAL_STATE] = $row->member_state;
            $row->member_property = my_json_encode($member_property);
            $row->member_state = memberStateEnum::CREATE;
        } else {
            // 恢复到原来状态
            if ($member_property['original_member_state']) {
                $row->member_state = $member_property[memberPropertyKeyEnum::ORIGINAL_STATE];
            } else {
                $row->member_state = memberStateEnum::CHECKED;
            }

        }
        $rt_1 = $row->update();
        if (!$rt_1->STS) {
            return new result(false, 'Handle Failed.');
        }

        $rt_2 = memberClass::memberBindOfficer($member_id, $param['handler_id']);
        if (!$rt_2->STS) {
            return new result(false, 'Handle Failed.');
        }

        return new result(true, 'Handle Successful.');
    }

    public function getProcessingTask($task_id)
    {
        $processing_task = array(
            'title' => "<New Register>",
            'url' => getUrl('operator', 'checkNewClient', array('uid' => $task_id, 'show_menu_a' => "new_client"), false, BACK_OFFICE_SITE_URL),
        );
        return $processing_task;
    }

    public function getTaskPendingCount($receiver_id, $last_time,$receiver_type)
    {
        $r = new ormReader();
        $sql = "SELECT a.*,b.`uid` FROM client_member a "
            . " LEFT JOIN task_user_biz b ON a.uid=b.task_id AND b.task_type='" . $this->biz_code . "' and b.task_state='" . userTaskStateTypeEnum::RUNNING . "'"
            . " left join member_follow_officer mf on a.uid=mf.member_id and mf.officer_type=1"
            . " WHERE mf.uid is null and b.uid IS NULL";
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