<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/1
 * Time: 14:25
 */
class member_follow_officerModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('member_follow_officer');
    }

    public function getCoByMemberId($member_id)
    {
        $sql = 'SELECT mfo.*,uu.mobile_phone FROM member_follow_officer mfo INNER JOIN um_user uu ON mfo.officer_id = uu.uid WHERE mfo.member_id = ' . intval($member_id) . ' and is_active = 1 and uu.user_position = ' . qstr(userPositionEnum::CREDIT_OFFICER);
        $co_list = $this->reader->getRows($sql);
        return $co_list;
    }

    public function getOperatorInfoByMemberId($member_id){
        $sql = "SELECT mfo.*,uu.user_code,uu.user_name,uu.mobile_phone FROM member_follow_officer mfo INNER JOIN um_user uu ON mfo.officer_id = uu.uid WHERE mfo.member_id = '$member_id' and is_active = 1 and mfo.officer_type = 1";
        $info = $this->reader->getRow($sql);
        return $info;
    }
}