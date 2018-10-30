<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/10/10
 * Time: 15:49
 */
class member_relative_verify_certModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_relative_verify_cert');
    }

    public function getLastCertResultByType($type,$relative_id)
    {
        $row = $this->orderBy('uid desc')->find(array(
            'relative_id' => $relative_id,
            'cert_type' => $type
        ));
        if( $row ){
            // 获取图片
            $sql = "select * from member_relative_verify_cert_image where cert_id=".$row['uid'];
            $image_list = $this->reader->getRows($sql);
            $row['image_list'] = $image_list;
        }
        return $row;
    }

    public function getVerifyCertDetailById($uid)
    {
        $sql = "SELECT verify.*,member.display_name,member.login_code,member.id_en_name_json,member.id_kh_name_json,
        member.gender,member.birthday,member.nationality,member.phone_id,member.email,member.operator_id,
         mcrr.name relative_name,mcrr.relation_type,mcrr.relation_name
        FROM member_relative_verify_cert AS verify 
        LEFT JOIN client_member AS member ON verify.member_id = member.uid 
        left join member_credit_request_relative mcrr on mcrr.uid=verify.relative_id
        WHERE verify.uid = " . intval($uid);
        $cert_info = $this->reader->getRow($sql);
        if ($cert_info) {
            $m_member_verify_cert_image = new member_relative_verify_cert_imageModel();
            $cert_images = $m_member_verify_cert_image->select(array('cert_id' => $uid));
            $cert_info['cert_images'] = $cert_images;
            $relative_info = (new member_credit_request_relativeModel())->find(array(
                'uid' => $cert_info['relative_id']
            ));
            $cert_info['relative_detail_info'] = $relative_info;
        }
        return $cert_info;
    }

    public function getVerifyCertHistoryByType($relative_id, $cert_type)
    {
        $r = new ormReader();
        $sql = "SELECT mvc.* FROM member_relative_verify_cert mvc  WHERE mvc.relative_id = " . qstr($relative_id) . " AND mvc.cert_type = " . qstr($cert_type) . " ORDER BY mvc.uid DESC";
        $history = $r->getRows($sql);
        foreach ($history as $k => $v) {
            $sql = "SELECT * FROM member_relative_verify_cert_image WHERE cert_id=" . $v['uid'];
            $images = $r->getRows($sql);
            $v['cert_images'] = $images;
            $history[$k] = $v;
        }
        return $history;
    }



    public function getPageList($pageNumber, $pageSize, $filter)
    {

        $where = " 1=1 ";
        if (intval($filter['operator_id'])) {
            $where .= " and mfo.officer_id = " . intval($filter['operator_id']);
        }

        if (isset($filter['cert_type'])) {
            $where .= " and cv.cert_type = " . qstr($filter['cert_type']);
        }

        if (isset($filter['verify_state'])) {
            if ( is_array($filter['verify_state']) && !empty($filter['verify_state']) ) {
                $verify_state = "(" . implode(',', $filter['verify_state']) . ")";
                $where .= " and cv.verify_state in $verify_state";
            } else {
                $where .= " and cv.verify_state = " . qstr($filter['verify_state']);
            }
        }

        if ( $filter['search_text'] ) {
            $where .= " and (cm.login_code like '%" . qstr2($filter['search_text']) . "%' or cm.obj_guid=".qstr($filter['search_text']).")";
        }

        $sql = "select cv.*,cm.login_code,cm.display_name,cm.phone_id,cm.email,cm.obj_guid member_guid,crr.name relative_name,
          crr.relation_name,crr.relation_type
          from member_relative_verify_cert cv left join client_member cm on cm.uid=cv.member_id 
          left join member_credit_request_relative crr on crr.uid=cv.relative_id 
          left join member_follow_officer mfo on mfo.member_id=cv.member_id 
          where $where group by cv.uid order by cv.uid desc ";


        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 20;
        $data = $this->reader->getPage($sql, $pageNumber, $pageSize);
        $cert_rows = $data->rows;
        $cert_rows = resetArrayKey($cert_rows,'uid');

        // 图片合成一个sql查询
        $cert_ids = array(0);
        foreach( $cert_rows as $v ){
            $cert_ids[] = $v['uid'];
        }

        $sql = " select * from member_relative_verify_cert_image where cert_id in(".join(',',$cert_ids).")";
        $image_list = $this->reader->getRows($sql);
        foreach($image_list as $v ){
            $cert_id = $v['cert_id'];
            $cert_rows[$cert_id]['cert_images'][] = $v;
        }



        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "data" => $cert_rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );

    }


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

        return new result(true, 'Edit Successful');

    }

    public function rejectCertBySystem($relative_id, $cert_type, $before_time, $operator)
    {
        $relative_id = intval($relative_id);
        $cert_type = intval($cert_type);
        $sql = "UPDATE member_relative_verify_cert SET verify_state = " . intval(certStateEnum::NOT_PASS)
            . ", verify_remark = 'Reject By System--Checked new certificate'"
            . ", auditor_id = " . intval($operator['operator_id'])
            . ", auditor_name = " . qstr(trim($operator['operator_name']))
            . ", auditor_time = " . qstr(Now())
            . "WHERE relative_id = '$relative_id' AND cert_type = $cert_type AND verify_state = " . qstr(certStateEnum::CREATE) . " AND create_time < " . qstr($before_time);
        $rt = $this->conn->execute($sql);
        if (!$rt->STS) {
            return new result(false, 'Reject certificate by system failed.1' . $rt->MSG);
        }

        $lock_row = $this->getRow(array(
            'relative_id' => $relative_id,
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
                "task_type" => userTaskTypeEnum::OPERATOR_RELATIVE_NEW_CERT,
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


}