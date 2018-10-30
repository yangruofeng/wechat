<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/7/12
 * Time: 22:59
 */
class operatorNewCertTaskClass extends userBizTaskClass
{
    public function afterHandle($task_id, $receiver_id)
    {
        $md = new member_verify_certModel();
        $row = $md->getRow($task_id);
        if (!$row) {
            return new result(false, 'Invalid consult.');
        }
        $operator_id = $receiver_id;

        $obj_user = new objectUserClass($operator_id);
        $row->auditor_id = $operator_id;
        $row->auditor_name = $obj_user->user_name;
        $row->verify_state = certStateEnum::LOCK;
        $row->update_time = Now();
        return $row->update();
    }

    public function afterCancel($task_id, $receiver_id)
    {
        $md = new member_verify_certModel();
        $row = $md->getRow($task_id);
        if (!$row) {
            return new result(false, 'Invalid consult.');
        }
        $row->auditor_id = 0;
        $row->auditor_name = '';
        $row->verify_state = certStateEnum::CREATE;
        $row->update_time = Now();
        return $row->update();
    }

    public function afterFinish($task_id, $receiver_id, $param)
    {
        $m_member_verify_cert = new member_verify_certModel();
        $row = $m_member_verify_cert->getRow($task_id);
        if (!$row) {
            return new result(false, 'Invalid Cert!');
        }

        //存认证log
        $m_member_cert_log = new member_cert_logModel();
        $rt_1 = $m_member_cert_log->insertCertLog($task_id, 1);
        if (!$rt_1->STS) {
            return new result(false, 'Insert cert log failed!' . $rt_1->MSG);
        }

        $cert_type = $row->cert_type;
        $ret = $m_member_verify_cert->updateState($param);
        if (!$ret->STS) {
            return new result(false, $ret->MSG);
        }

        $operator = array(
            'operator_id' => $param['auditor_id'],
            'operator_name' => $param['auditor_name'],
        );

        switch ($cert_type) {
            case certificationTypeEnum::HOUSE :
            case certificationTypeEnum::CAR :
            case certificationTypeEnum::LAND :
            case certificationTypeEnum::STORE:
            case certificationTypeEnum::MOTORBIKE:
                // 更新资产认证状态
                if ($param['verify_state'] == certStateEnum::PASS) {
                    $asset_state = assetStateEnum::CERTIFIED;
                } else {
                    $asset_state = assetStateEnum::INVALID;
                }
                $m_asset = new member_assetsModel();
                $rt_2 = $m_asset->updateAssetState($task_id, $asset_state);
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
                $rt_5 = $m_member_verify_cert->rejectCertBySystem($row->member_id, $row->cert_type, $row->create_time, $operator);
                if (!$rt_5->STS) {
                    return $rt_5;
                }
                break;
            case certificationTypeEnum::RESIDENT_BOOK :
            case certificationTypeEnum::PASSPORT :
            case certificationTypeEnum::BIRTH_CERTIFICATE:
            case certificationTypeEnum::FAIMILYBOOK :
                $rt_5 = $m_member_verify_cert->rejectCertBySystem($row->member_id, $row->cert_type, $row->create_time, $operator);
                if (!$rt_5->STS) {
                    return $rt_5;
                }
                break;
            case certificationTypeEnum::WORK_CERTIFICATION :
                $rt_4 = $this->checkMemberWork($task_id, $param['verify_state']);
                if (!$rt_4->STS) {
                    return new result(false, $rt_4->MSG);
                }
                $rt_5 = $m_member_verify_cert->rejectCertBySystem($row->member_id, $row->cert_type, $row->create_time, $operator);
                if (!$rt_5->STS) {
                    return $rt_5;
                }
                break;
            default:
                return new result(false, 'Not supported type');
        }

        return new result(true, 'Handle Successful.', array('cert_type' => $cert_type));
    }

    public function getProcessingTask($task_id)
    {
        $processing_task = array(
            'title' => "<New Certification>",
            'url' => getUrl('operator', 'showCertificationDetail', array('uid' => $task_id, 'show_menu_a' => "certificationFile"), false, BACK_OFFICE_SITE_URL),
        );
        return $processing_task;
    }

    public function getTaskPendingCount($receiver_id, $last_time,$receiver_type)
    {
        $r = new ormReader();
        $sql = "SELECT a.*,b.`uid` FROM member_verify_cert a "
            . "INNER JOIN member_follow_officer mfo ON a.member_id = mfo.member_id "
            . "LEFT JOIN task_user_biz b ON a.uid=b.task_id AND b.task_type='" . $this->biz_code . "' and b.task_state='" . userTaskStateTypeEnum::RUNNING . "'"
            . "WHERE mfo.officer_type = 1 and mfo.officer_id = " . intval($receiver_id) . " AND b.uid IS NULL AND a.verify_state=" . qstr(certStateEnum::CREATE) ;
        $list = $r->getRows($sql);
        $count_pending = count($list);
        $count_new = 0;

        //这里处理一个cert-type的分类
        $cert_type = (new certificationTypeEnum())->Dictionary();
        $arr_group_by = array();
        foreach ($cert_type as $key => $val) {
            $arr_group_by[$key] = array(
                'count_pending' => 0,
                'count_new' => 0,
            );
        }

        foreach ($list as $item) {
            if ($arr_group_by[$item['cert_type']]) {
                $arr_group_by[$item['cert_type']]['count_pending'] += 1;
                if ($item['create_time'] > $last_time) {
                    $count_new += 1;
                    $arr_group_by[$item['cert_type']]['count_new'] += 1;
                }
            }
        }
        return array(
            $this->biz_code => array(
                "count_pending" => $count_pending,
                "count_new" => $count_new,
                "group_by" => $arr_group_by
            )
        );
    }
}