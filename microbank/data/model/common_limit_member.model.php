<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class common_limit_memberModel extends tableModelBase
{
    public function  __construct()
    {
        parent::__construct('common_limit_member');
    }

    public function setMemberLimit($p)
    {
        $m_common_limit_member = M('common_limit_member');
        $member_grade = intval($p['member_grade']);
        $creator_id = intval($p['creator_id']);
        $creator_name = intval($p['creator_name']);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $m_common_limit_member->delete(array('member_grade' => $member_grade));
        if (!$rt->STS) {
            $conn->rollback();
            return new result(false, 'Set Member Limit Failure');
        }

        $limit_arr = array(
            bizCodeEnum::MEMBER_WITHDRAW_TO_PARTNER => array(
                'per_time' => $p[bizCodeEnum::MEMBER_WITHDRAW_TO_PARTNER]['per_time'],
                'per_day' => $p[bizCodeEnum::MEMBER_WITHDRAW_TO_PARTNER]['per_day']
            ),
            bizCodeEnum::MEMBER_WITHDRAW_TO_BANK => array(
                'per_time' => $p[bizCodeEnum::MEMBER_WITHDRAW_TO_BANK]['per_time'],
                'per_day' => $p[bizCodeEnum::MEMBER_WITHDRAW_TO_BANK]['per_day']
            ),
            bizCodeEnum::MEMBER_WITHDRAW_TO_CASH => array(
                'per_time' => $p[bizCodeEnum::MEMBER_WITHDRAW_TO_CASH]['per_time'],
                'per_day' => $p[bizCodeEnum::MEMBER_WITHDRAW_TO_CASH]['per_day']
            ),
            bizCodeEnum::MEMBER_TRANSFER_TO_MEMBER => array(
                'per_time' => $p[bizCodeEnum::MEMBER_TRANSFER_TO_MEMBER]['per_time'],
                'per_day' => $p[bizCodeEnum::MEMBER_TRANSFER_TO_MEMBER]['per_day']
            ),
            bizCodeEnum::MEMBER_TRANSFER_TO_BANK => array(
                'per_time' => $p[bizCodeEnum::MEMBER_TRANSFER_TO_BANK]['per_time'],
                'per_day' => $p[bizCodeEnum::MEMBER_TRANSFER_TO_BANK]['per_day']
            ),
            bizCodeEnum::MEMBER_DEPOSIT_BY_PARTNER => array(
                'per_time' => $p[bizCodeEnum::MEMBER_DEPOSIT_BY_PARTNER]['per_time'],
                'per_day' => $p[bizCodeEnum::MEMBER_DEPOSIT_BY_PARTNER]['per_day']
            ),
            bizCodeEnum::MEMBER_DEPOSIT_BY_BANK => array(
                'per_time' => $p[bizCodeEnum::MEMBER_DEPOSIT_BY_BANK]['per_time'],
                'per_day' => $p[bizCodeEnum::MEMBER_DEPOSIT_BY_BANK]['per_day']
            ),
            bizCodeEnum::MEMBER_DEPOSIT_BY_CASH => array(
                'per_time' => $p[bizCodeEnum::MEMBER_DEPOSIT_BY_CASH]['per_time'],
                'per_day' => $p[bizCodeEnum::MEMBER_DEPOSIT_BY_CASH]['per_day']
            )
        );

        foreach ($limit_arr as $key => $limit) {
            if (!is_numeric($limit['per_time']) && !is_numeric($limit['per_time'])) {
                continue;
            }
            $row = $m_common_limit_member->newRow();
            $row->member_grade = $member_grade;
            $row->limit_key = $key;
            if (is_numeric($limit['per_time'])) {
                $row->per_time = intval($limit['per_time']);
            } else {
                $row->per_time = -1;
            }
            if (is_numeric($limit['per_day'])) {
                $row->per_day = intval($limit['per_day']);
            } else {
                $row->per_day = -1;
            }
            $row->creator_id = $creator_id;
            $row->creator_name = $creator_name;
            $row->create_time = Now();
            $rt_2 = $row->insert();
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Set Member Limit Failure');
            }
        }
        $conn->submitTransaction();
        return new result(true, 'Set Member Limit Success');
    }

    public function selectLimit($grade_id){
        $r = new ormReader();
        $sql = "select * from common_limit_member WHERE member_grade = " . $grade_id;
        $limit_list = $r->getRows($sql);
        if ($limit_list) {
            $limit_list = resetArrayKey($limit_list, 'limit_key');
        }
        return $limit_list;
    }

}
