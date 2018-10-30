<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/17
 * Time: 下午4:11
 */
class userTaskClass
{

    protected $user_id = 0;

    function __construct($_user_id)
    {
        $this->user_id = $_user_id;
    }

    /**
     * 处理任务
     * @param $task_id
     * @param $task_type
     * @return ormResult|result
     */
    public function handleTask($task_id, $task_type)
    {
        $m = M('um_user_operator_task');
        $old_task = $m->find(array(
            "user_id" => $this->user_id,
            "task_state" => userTaskStateTypeEnum::RUNNING
        ));
        if ($old_task) {
            if ($old_task['task_id'] != $task_id || $old_task['task_type'] != $task_type) {
                return new result(false, "You can\'t deal with new task before finish the suspended task.");
            } else {
                return new result(true);
            }
        }
        $row = $m->getRow(array(
            "task_id" => $task_id,
            "task_type" => $task_type,
            "task_state" => array("neq", userTaskStateTypeEnum::CANCEL)
        ));
        if ($row) {
            if ($row->user_id != $this->user_id) {
                return new result(false, "This task has been locked by others!");
            }
            if ($row->task_state != userTaskStateTypeEnum::RUNNING) {
                return new result(false, "Invalid Task State");
            }
            return new result(true, "", $row);
        } else {
            $conn = ormYo::Conn();
            $conn->startTransaction();
            try {
                $row = $m->newRow(
                    array(
                        "user_id" => $this->user_id,
                        "task_id" => $task_id,
                        "task_type" => $task_type,
                        "task_state" => userTaskStateTypeEnum::RUNNING,
                        "insert_time" => Now(),
                        "update_time" => Now()
                    )
                );
                $rt_1 = $row->insert();
                if (!$rt_1->STS) {
                    $conn->rollback();
                    return new result(false, 'Add Task Failed.');
                }
                $rt_2 = $this->afterHandleTask($task_id, $task_type);
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Add Task Failed.');
                }

                $conn->submitTransaction();
                return new result(true, 'Add Task Successful.');
            } catch (Exception $ex) {
                $conn->rollback();
                showMessage($ex->getMessage());
            }
        }
    }

    /**
     * 完成任务关联操作
     * @param $task_id
     * @param $task_type
     * @param $handle_type
     * @return result
     */
    private function afterHandleTask($task_id, $task_type, $handle_type = 'handle')
    {
        $task_table = "";
        $fld_operator_id = "operator_id";
        $fld_operator_name = "operator_name";
        $fld_state = "operate_state";
        $state_value = "";
        $fld_time = "operate_time";
        switch ($task_type) {
            case operateTypeEnum::NEW_CLIENT:
                $task_table = "client_member";
                $state_value = $handle_type == 'cancel' ? newMemberCheckStateEnum::CREATE : newMemberCheckStateEnum::LOCKED;
                break;
            case operateTypeEnum::CERTIFICATION_FILE:
                $task_table = "member_verify_cert";
                $fld_operator_id = "auditor_id";
                $fld_operator_name = "auditor_name";
                $fld_state = "verify_state";
                $fld_time = "auditor_time";
                $state_value = $handle_type == 'cancel' ? certStateEnum::CREATE : certStateEnum::LOCK;
                break;
            case operateTypeEnum::LOAN_CONSULT:
                $task_table = "loan_consult";
                $fld_state = "state";
                $fld_time = "update_time";
                $state_value = $handle_type == 'cancel' ? loanConsultStateEnum::CREATE : loanConsultStateEnum::LOCKED;
                break;
            default:
                return new result(false, "not support yet");
        }

        $m = M($task_table);

        if ($handle_type == 'cancel') {
            $update_arr = array(
                'uid' => $task_id,
                $fld_operator_id => 0,
                $fld_operator_name => ' ',
                $fld_state => $state_value,
                $fld_time => Now(),
            );
        } else {
            $userObj = new objectUserClass($this->user_id);
            $update_arr = array(
                'uid' => $task_id,
                $fld_operator_id => $this->user_id,
                $fld_operator_name => $userObj->user_name,
                $fld_state => $state_value,
                $fld_time => Now(),
            );
        }
        $rt = $m->update($update_arr);
        if ($rt->STS) {
            return new result(true, 'After Handle Successful.');
        } else {
            return new result(true, 'After Handle Failed.');
        }
    }

    /**
     * 取消任务
     */
    public function cancelTask()
    {
        $m = M('um_user_operator_task');
        $running_task = $m->getRow(array(
            "user_id" => $this->user_id,
            "task_state" => userTaskStateTypeEnum::RUNNING
        ));

        if (!$running_task) {
            return new result(true);
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $running_task->task_state = userTaskStateTypeEnum::CANCEL;
            $running_task->update_time = Now();
            $rt_1 = $running_task->update();
            if (!$rt_1->STS) {
                return new result(false, 'Update Task Failed.');
            }

            $rt_2 = $this->afterHandleTask($running_task->task_id, $running_task->task_type, 'cancel');
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Cancel Task Failed.');
            }

            $conn->submitTransaction();
            return new result(true, 'Cancel Task Successful.');
        } catch (Exception $ex) {
            $conn->rollback();
            showMessage($ex->getMessage());
        }
    }

    /**
     * 完成任务
     * @param $task_id
     * @param $task_type
     * @param $task_arr
     * @return result
     */
    public function finishedTask($task_id, $task_type, $task_arr)
    {
        $m = M('um_user_operator_task');
        $row = $m->getRow(array(
            "task_id" => $task_id,
            "task_type" => $task_type,
            "task_state" => userTaskStateTypeEnum::RUNNING,
            "user_id" => $this->user_id
        ));

        if (!$row) {
            return new result(false, 'Invalid Task.');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();

        try {
            $row->task_state = userTaskStateTypeEnum::DONE;
            $row->update_time = Now();
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Handle Failed.' . $rt->MSG);
            }

            switch ($task_type) {
                case operateTypeEnum::NEW_CLIENT:
                    $rt_2 = $this->operatorCheckNewMember($task_arr);
                    break;
                case operateTypeEnum::CERTIFICATION_FILE:
                    $rt_2 = $this->operatorCheckCertification($task_arr);
                    break;
                case operateTypeEnum::LOAN_CONSULT:
                    $rt_2 = $this->operatorCheckLoanConsult($task_arr);
                    break;
                default:
                    $conn->rollback();
                    return new result(false, "not support yet");
            }

            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, $rt_2->MSG, $rt_2->DATA);
            }

            $conn->submitTransaction();
            return new result(true, 'Handle Successful.', $rt_2->DATA);
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * operator检查新会员
     * @param $param
     * @return result
     */
    private function operatorCheckNewMember($param)
    {
        $member_id = intval($param['member_id']);
        $operate_state = intval($param['operate_state']);
        $operate_remark = trim($param['operate_remark']);
        $work_type = trim($param['work_type']);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            return new result(false, 'Invalid member.');
        }

        $row->operate_state = $operate_state;
        $row->operate_remark = $operate_remark;
        $row->operate_time = Now();
        $row->update_time = Now();
        $row->work_type = $work_type;

        $member_property = my_json_decode($row->member_property);
        if ($operate_remark == newMemberCheckStateEnum::CLOSE) {
            $member_property['original_member_state'] = $row->member_state;
            $row->member_property = my_json_encode($member_property);
            $row->member_state = memberStateEnum::CREATE;
        } else {
            // 恢复到原来状态
            if ($member_property['original_member_state']) {
                $row->member_state = $member_property['original_member_state'];
            } else {
                $row->member_state = memberStateEnum::CHECKED;
            }

        }
        $rt_1 = $row->update();
        if (!$rt_1->STS) {
            return new result(false, 'Handle Failed.');
        }

        $rt_2 = memberClass::memberBindOfficer($member_id, $this->user_id);
        if (!$rt_2->STS) {
            return new result(false, 'Handle Failed.');
        }

        return new result(true, 'Handle Successful.');
    }

    /**
     * operator 审核贷款申请
     * @param $param
     * @return result
     */
    public function operatorCheckLoanConsult($param)
    {
        $uid = intval($param['uid']);
        $operate_state = intval($param['operate_state']);
        $operate_remark = trim($param['operate_remark']);

        $m_loan_consult = M('loan_consult');
        $row = $m_loan_consult->getRow($uid);
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

    /**
     * operator审核资料
     * @param $param
     * @return ormResult|result
     */
    public function operatorCheckCertification($param)
    {

        $uid = intval($param['uid']);

        $m_member_verify_cert = new member_verify_certModel();
        $row = $m_member_verify_cert->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }

        //存认证log
        $m_member_cert_log = new member_cert_logModel();
        $rt_1 = $m_member_cert_log->insertCertLog($uid, 1);
        if (!$rt_1->STS) {
            return $rt_1;
        }

        $cert_type = $row->cert_type;

        $ret = $m_member_verify_cert->updateState($param);
        if (!$ret->STS) {
            return new result(false, $ret->MSG);
        }
        switch ($cert_type) {
            case certificationTypeEnum::HOUSE :
            case certificationTypeEnum::CAR :
            case certificationTypeEnum::LAND :
            case certificationTypeEnum::MOTORBIKE:
                // 更新资产认证状态
                if ($param['verify_state'] == certStateEnum::PASS) {
                    $asset_state = assetStateEnum::CERTIFIED;
                } else {
                    $asset_state = assetStateEnum::INVALID;
                }
                $m_asset = new member_assetsModel();
                $rt_2 = $m_asset->updateAssetState($uid, $asset_state);
                if (!$rt_2->STS) {
                    return new result(false, $rt_2->MSG);
                }
                break;
            case certificationTypeEnum::ID :
                if (intval($param['verify_state']) == certStateEnum::PASS) {// 修改会员表身份证信息
                    $m_member = new memberModel();
                    $param['member_id'] = $row['member_id'];
                    $param['cert_sn'] = $row['cert_sn'];
                    $rt_3 = $m_member->updateMemberId($param);
                    if (!$rt_3->STS) {
                        return new result(false, $rt_3->MSG);
                    }
                }
                break;
            case certificationTypeEnum::RESIDENT_BOOK :
            case certificationTypeEnum::PASSPORT :
            case certificationTypeEnum::BIRTH_CERTIFICATE :
            case certificationTypeEnum::FAIMILYBOOK :
                break;
            case certificationTypeEnum::WORK_CERTIFICATION :
                $rt_4 = $this->checkMemberWork($uid, $param['verify_state']);
                if (!$rt_4->STS) {
                    return new result(false, $rt_4->MSG);
                }
                break;
            default:
                return new result(false, 'Not supported type');
        }

        return new result(true, '', array('cert_type' => $cert_type));
    }

    /**
     * 验证工作
     * @param $cert_id
     * @param $state
     * @return result
     * @throws Exception
     */
    private function checkMemberWork($cert_id, $state)
    {
        $m_work = new member_workModel();
        $extend_info = $m_work->getRow(array(
            'cert_id' => $cert_id
        ));

        if (!$extend_info) {
            return new result(false, 'Invalid Id.');
        }

        if ($state == certStateEnum::PASS) {
            $work_state = workStateStateEnum::VALID;
            $sql = "UPDATE member_work SET state = " . workStateStateEnum::HISTORY . " WHERE member_id = " . $extend_info['member_id'] . " AND state = " . workStateStateEnum::VALID;
            $rt_1 = $m_work->reader->conn->execute($sql);
            if (!$rt_1->STS) {
                return new result(false, $rt_1->MSG);
            }
        } else {
            $work_state = workStateStateEnum::INVALID;
        }

        $extend_info->state = $work_state;
        $up = $extend_info->update();
        if (!$up->STS) {
            return new result(false, $up->MSG);
        }


        // 如果通过
        if ($state == certStateEnum::PASS && $extend_info->is_government) {
            // 如果是政府员工，更新member表
            $m_member = new memberModel();
            $member = $m_member->getRow($extend_info->member_id);
            if ($member) {
                $member->is_government = 1;
                $up = $member->update();
                if (!$up->STS) {
                    return new result(false, $up->MSG);
                }
            }
        }

        return new result(true);
    }

    public function resumeClient($member_id)
    {
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            return new result(false, 'Invalid member.');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row->operator_id = 0;
            $row->operator_name = '';
            $row->operate_state = newMemberCheckStateEnum::CREATE;
            $row->operate_remark = '';
            $row->operate_time = '';
            $row->update_time = Now();

            // 恢复到原来状态
            $member_property = my_json_decode($row->member_property);
            if ($member_property['original_member_state']) {
                $row->member_state = $member_property['original_member_state'];
            } else {
                $row->member_state = memberStateEnum::CREATE;
            }

            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Resume Failed.');
            }

            $m_um_user_operator_task = M('um_user_operator_task');
            $task = $m_um_user_operator_task->getRow(array('task_id' => $member_id, 'task_type' => operateTypeEnum::NEW_CLIENT, 'task_state' => userTaskStateTypeEnum::DONE));
            if ($task) {
                $task->task_state = userTaskStateTypeEnum::CANCEL;
                $task->update_time = Now();
                $rt_2 = $task->update();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Resume Failed.');
                }
            }
            $conn->submitTransaction();
            return new result(true, 'Resume Successful.');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }

    }

}