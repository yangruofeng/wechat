<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/6
 * Time: 17:53
 */
class member_verify_certModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_verify_cert');
    }

    /**
     * 获取role信息
     * @param $param
     * @return result
     */
    public function updateState($param)
    {
        $uid = intval($param['uid']);
        $remark = $param['remark'];

        $row = $this->getRow(array('uid' => $uid));
        if (empty($row)) {
            return new result(false, 'Invalid Id!');
        }

        if (intval($param['auditor_id'])) {
            $row->auditor_id = intval($param['auditor_id']);
        }

        if (trim($param['auditor_name'])) {
            $row->auditor_name = trim($param['auditor_name']);
        }

        if (trim($param['cert_addr_detail'])) {
            $row->cert_addr = trim($param['cert_addr_detail']);

        }
        if ($param['cert_expire_time']) {
            $row->cert_expire_time = $param['cert_expire_time'];
        }

        if (isset($param['verify_state'])) {
            $row->verify_state = intval($param['verify_state']);
        }

        $row->auditor_time = Now();
        $row->verify_remark = $remark;
        $ret = $row->update();
        if (!$ret->STS) {
            return new result(false, 'Edit failed--' . $ret->MSG);
        }

        // 给用户发送消息
        $member_id = $row['member_id'];
        switch ($param['verify_state']) {
            case certStateEnum::PASS:
                $title = 'Certification Pass';
                $body = 'Your submitted certificate has been passed!';
                member_messageClass::sendSystemMessage($member_id, $title, $body);
                break;
            case certStateEnum::NOT_PASS :
                $title = 'Certification Un-pass';
                $body = 'Your submitted certificate authentication did not pass, please resubmit the information!';
                member_messageClass::sendSystemMessage($member_id, $title, $body);
                break;
            default:
                break;
        }
        return new result(true, 'Edit Successful');
    }

    public function rejectCertBySystem($member_id, $cert_type, $before_time, $operator)
    {
        $member_id = intval($member_id);
        $cert_type = intval($cert_type);
        $sql = "UPDATE member_verify_cert SET verify_state = " . intval(certStateEnum::NOT_PASS)
            . ", verify_remark = 'Reject By System--Checked new certificate'"
            . ", auditor_id = " . intval($operator['operator_id'])
            . ", auditor_name = " . qstr(trim($operator['operator_name']))
            . ", auditor_time = " . qstr(Now())
            . "WHERE member_id = $member_id AND cert_type = $cert_type AND verify_state = " . qstr(certStateEnum::CREATE) . " AND create_time < " . qstr($before_time);
        $rt = $this->conn->execute($sql);
        if (!$rt->STS) {
            return new result(false, 'Reject certificate by system failed.1' . $rt->MSG);
        }

        $lock_row = $this->getRow(array(
            'member_id' => $member_id,
            'cert_type' => $cert_type,
            'verify_state' => certStateEnum::LOCK,
            'create_time' => array('<', $before_time)
        ));
        if ($lock_row) {
            $lock_row->verify_state = certStateEnum::NOT_PASS;
            $lock_row->verify_remark = 'Reject By System--Checked new certificate';
            $lock_row->auditor_id = intval($operator['operator_id']);
            $lock_row->auditor_name = trim($operator['operator_name']);
            $lock_row->auditor_time = Now();
            $rt = $lock_row->update();
            if (!$rt->STS) {
                return new result(false, 'Reject certificate by system failed.2' . $rt->MSG);
            }
            $md = new task_user_bizModel();
            $task_row = $md->getRow(array(
                "task_id" => $lock_row->uid,
                "task_type" => userTaskTypeEnum::OPERATOR_NEW_CERT,
                "task_state" => userTaskStateTypeEnum::RUNNING
            ));
            if ($task_row) {
                $task_row->task_state = userTaskStateTypeEnum::CANCEL;
                $task_row->update_time = Now();
                $rt = $task_row->update();
                if (!$rt->STS) {
                    return new result(false, 'Reject certificate by system failed.3' . $rt->MSG);
                }
            }
        }
        return new result(true);
    }

    /**
     * 获取会员资产信息
     */
    public function getVerifyCertList($member_id, $cert_type)
    {
        $sql = "select uid from member_verify_cert where member_id = '$member_id' and verify_state = 10 and cert_type IN ($cert_type) ORDER BY uid desc";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    //获取最新未审核资产信息
    public function getVerifyCertListLast($member_id, $cert_type)
    {
        $sql = "select max(uid) uid from member_verify_cert where member_id = '$member_id' and (verify_state = 0 or verify_state = -1) and cert_type IN ($cert_type) GROUP BY cert_type ORDER BY uid desc";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    public function getVerifyCertByIds($ids)
    {
        $sql = "select * from member_verify_cert where uid IN ($ids) ORDER BY uid desc;";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    public function getVerifyCertDetailById($uid)
    {
        $sql = "SELECT verify.*,member.display_name,member.login_code,member.id_en_name_json,member.id_kh_name_json,member.gender,member.birthday,member.nationality,member.phone_id,member.email,member.operator_id FROM member_verify_cert AS verify LEFT JOIN client_member AS member ON verify.member_id = member.uid WHERE verify.uid = " . intval($uid);
        $cert_info = $this->reader->getRow($sql);
        if ($cert_info) {
            $m_member_verify_cert_image = M('member_verify_cert_image');
            $cert_images = $m_member_verify_cert_image->select(array('cert_id' => $uid));
            $cert_info['cert_images'] = $cert_images;
        }
        return $cert_info;
    }


    public function getVerifyCertHistoryByType($member_id, $cert_type)
    {
        $r = new ormReader();
        $sql = "SELECT mvc.*,ma.asset_name,ma.asset_sn FROM member_verify_cert mvc LEFT JOIN member_assets ma ON mvc.uid = ma.cert_id WHERE mvc.member_id = " . qstr($member_id) . " AND mvc.cert_type = " . qstr($cert_type) . " ORDER BY mvc.uid DESC";
        $history = $r->getRows($sql);
        foreach ($history as $k => $v) {
            $sql = "SELECT * FROM member_verify_cert_image WHERE cert_id=" . $v['uid'];
            $images = $r->getRows($sql);
            $v['cert_images'] = $images;
            $history[$k] = $v;
        }
        return $history;
    }

    public function getPageList($pageNumber, $pageSize, $filter)
    {
        $sql1 = "select verify.*,member.login_code,member.display_name,member.phone_id,member.email from member_verify_cert as verify"
            . " inner join client_member as member on verify.member_id = member.uid";

        if (intval($filter['operator_id'])) {
            $sql1 .= " inner join member_follow_officer as mfo on verify.member_id = mfo.member_id";
            $sql1 .= " where mfo.officer_type = 1 and mfo.officer_id = " . intval($filter['operator_id']);
        } else {
            $sql1 .= " where 1 = 1";
        }
        if (isset($filter['cert_type'])) {
            $sql1 .= " and verify.cert_type = " . intval($filter['cert_type']);
        }
        if (isset($filter['verify_state'])) {
            if (is_array($filter['verify_state'])) {
                $verify_state = "(" . implode(',', $filter['verify_state']) . ")";
                $sql1 .= " and verify.verify_state in $verify_state";
            } else {
                $sql1 .= " and verify.verify_state = " . intval($filter['verify_state']);
            }
        }

        if (trim($filter['member_name'])) {
            $sql1 .= " and (member.login_code like '%" . qstr2($filter['member_name']) . "%')";
        }
        $sql1 .= " ORDER BY verify.uid desc";
        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 20;
        $data = $this->reader->getPage($sql1, $pageNumber, $pageSize);
        $rows = $data->rows;


        $list = array();
        foreach ($rows as $row) {
            $sql = "select * from member_verify_cert_image where cert_id='" . $row['uid'] . "'";
            $images = $this->reader->getRows($sql);
            $row['cert_images'] = $images;
            $list[] = $row;
        }
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "data" => $list,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );

    }
}
