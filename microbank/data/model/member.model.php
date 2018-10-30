<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 11:20
 */
class memberModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('client_member');
    }

    public function searchMemberListByFreeText($searchText, $pageNumber, $pageSize, $filter = array())
    {
        $sql = "SELECT cm.*,mg.grade_code FROM client_member cm LEFT JOIN member_grade mg ON cm.member_grade = mg.uid WHERE 1 =1 ";
        if ($searchText) {
            $sql .= " AND (cm.obj_guid = '" . qstr2($searchText) . "' OR cm.display_name like '%" . qstr2($searchText) . "%' OR cm.phone_id like '%" . qstr2($searchText) . "%' OR  cm.login_code like '%" . qstr2($searchText) . "')";
        }
        if (trim($filter['work_type'])) {
            $sql .= " AND cm.work_type = " . qstr(trim($filter['work_type']));
        }
        if (is_numeric($filter['grade_id'])) {
            $sql .= " AND cm.member_grade = " . intval($filter['grade_id']);
        }
        $sql .= " ORDER BY cm.create_time DESC";
        $data = $this->reader->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return new result(true, null, array(
            'rows' => $rows,
            'total' => $total,
            'page_total' => $pageTotal
        ));
    }

    public function getClientCbcNewList($ids, $search_text, $pageNumber, $pageSize)
    {
        $sql = "SELECT * FROM client_member where uid not in ($ids) and id_sn != '' ";
        if ($search_text) {
            $sql .= " and obj_guid = '" . qstr2($search_text) . "' OR display_name like '%" . qstr2($search_text) . "%' OR login_code like'%" . qstr2($search_text) . "%' OR phone_id like '" . qstr2($search_text) . "' OR  login_code like '" . qstr2($search_text) . "'";
        }
        $sql .= " order by uid desc";
        $data = $this->reader->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count ?: 0;
        $pageTotal = $data->pageCount ?: 0;
        return new result(true, null, array(
            'list' => $rows,
            'total' => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize
        ));
    }

    public function getMemberInfoById($mid)
    {
        $info = $this->find(array('uid' => $mid));
        return $info;
    }

    public function getMemberInfoByAccountId($accountId)
    {
        $sql = "select m.* from loan_account a left join  client_member m on m.obj_guid=a.obj_guid where a.uid=" . qstr($accountId);
        return $this->reader->getRow($sql);
    }

    public function getMemberSettingInfo($mid)
    {
        $sql = "select m.uid,m.obj_guid,m.login_code,m.display_name,m.phone_id,m.member_state,c.credit,c.credit_balance from client_member m left join member_credit c on m.uid = c.member_id where m.uid = '$mid'";
        $info = $this->reader->getRow($sql);
        return $info;
    }

    public function resetMemberPhoneState($mid)
    {
        $member_info = $this->getRow($mid);
        if (!$member_info) {
            return new result(false, 'Member not found', null, errorCodesEnum::UNEXPECTED_DATA);
        }

        $ret = $member_info->delete();
        if ($ret->STS) {
            return new result(true);
        } else {
            return new result(false, 'Delete failed - ' . $ret->MSG, null, errorCodesEnum::DB_ERROR);
        }
    }

    public function updateMemberId($param)
    {
        // 修改会员表身份证信息
        $m_member = new memberModel();
        $member = $m_member->getRow(intval($param['member_id']));
        if (!$member) {
            return new result(false, 'Error member');
        }

        // 检查是否被他人认证过
        $sql = "SELECT * FROM member_verify_cert WHERE member_id != " . qstr($param['member_id']) . " AND cert_type = " . qstr(certificationTypeEnum::ID) . "
        AND cert_sn = " . qstr($param['cert_sn']) . " AND verify_state = " . qstr(certStateEnum::PASS) . " ORDER BY uid DESC";
        $other = $m_member->reader->getRow($sql);

        if ($other) {
            return new result(false, 'ID has been certificated', null, errorCodesEnum::ID_SN_HAS_CERTIFICATED);
        }

        $id_en_name_json = json_encode(array('en_family_name' => $param['en_family_name'], 'en_given_name' => $param['en_given_name']));
        $id_kh_name_json = json_encode(array('kh_family_name' => $param['kh_family_name'], 'kh_given_name' => $param['kh_given_name']));
        $member->initials = strtoupper(substr($param['en_family_name'], 0, 1));
        $member->display_name = $param['en_family_name'] . ' ' . $param['en_given_name'];
        $member->kh_display_name = $param['kh_family_name'] . ' ' . $param['kh_given_name'];
        $member->id_sn = $param['cert_sn'];
        $member->id_type = $param['id_type'];
        $member->nationality = $param['nationality'];
        $member->id_en_name_json = $id_en_name_json;
        $member->id_kh_name_json = $id_kh_name_json;
        $member->id_address1 = intval($param['id_address1']);
        $member->id_address2 = intval($param['id_address2']);
        $member->id_address3 = intval($param['id_address3']);
        $member->id_address4 = intval($param['id_address4']);
        $member->id_expire_time = $param['cert_expire_time'];
        $member->address = trim($param['cert_addr']);
        $member->address_detail = trim($param['cert_addr_detail']);
        if (trim($param['gender'])) {
            $member->gender = trim($param['gender']);
        }
        if (trim($param['birthday'])) {
            $member->birthday = trim($param['birthday']);
        }

        $up = $member->update();

        if (!$up->STS) {
            return new result(false, 'Update Id Fail.' . $up->MSG);
        }

        if ($member->member_state != memberStateEnum::VERIFIED) {
            $rt = memberClass::changeMemberState($param['member_id'], memberStateEnum::VERIFIED, $param['remark'], intval($param['auditor_id']), memberVerifyTypeEnum::ID_CARD, false);
            if (!$rt->STS) {
                return new result(false, 'Update Failed.' . $rt->MSG);
            }
        }

        return new result(true, 'Update Id Successful.');

    }
}