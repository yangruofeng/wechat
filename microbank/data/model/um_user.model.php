<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class um_userModel extends tableModelBase
{

    public function  __construct()
    {
        parent::__construct('um_user');
    }


    public function getUserFollowedMemberList($user_id)
    {
        // 用内链，剔除不存在的member
        $sql = "select m.*,c.credit,c.credit_balance,c.expire_time,c.grant_time,c.credit_terms,tcb.state co_to_bm_credit_state from member_follow_officer f 
        inner join client_member m on m.uid=f.member_id 
        left join member_credit c on c.member_id=m.uid 
        left join ( select * from ( select * from task_co_bm where co_id='$user_id' order by uid desc  ) x group by x.member_id ) tcb on tcb.member_id=m.uid
        where f.officer_id='$user_id' 
        and f.is_active='1' group by f.officer_id,f.member_id order by f.update_time desc  ";

        return $this->reader->getRows($sql);
    }

    public function getOperatorList(){
        $sql = "SELECT uu.* FROM um_user uu LEFT JOIN site_depart sd on uu.depart_id = sd.uid WHERE  uu.user_position = ".qstr(userPositionEnum::OPERATOR);
        return $this->reader->getRows($sql);
    }

    public function getUserInfoById($uid){
        $info = $this->find(array('uid' => $uid));
        return $info;
    }

    public function getUserInfoByGuid($guid)
    {
        $info = $this->find(array('obj_guid' => $guid));
        return $info;
    }




    public function searchUserListByFreeText($searchText, $pageNumber, $pageSize) {
        $sql = "SELECT uu.*,sb.branch_name FROM um_user uu left JOIN site_depart sd on uu.depart_id=sd.uid LEFT JOIN site_branch sb ON sd.branch_id = sb.uid ";
        if ($searchText) {
            $sql .= " WHERE uu.user_code LIKE '%" . qstr2($searchText) . "%' OR uu.user_name LIKE '%" . qstr2($searchText) . "%'";
        }
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



    public function getBranchManagerInfo($branch_id)
    {
        $sql = "SELECT uu.*,sb.branch_name FROM um_user uu left JOIN site_depart sd on uu.depart_id=sd.uid LEFT JOIN 
        site_branch sb ON sd.branch_id = sb.uid where sd.branch_id=".qstr($branch_id)." and uu.user_position=".qstr(userPositionEnum::BRANCH_MANAGER);
        return $this->reader->getRow($sql);
    }

    public function getBranchChiefTellerInfo($branch_id)
    {
        $sql = "SELECT uu.*,sb.branch_name FROM um_user uu left JOIN site_depart sd on uu.depart_id=sd.uid LEFT JOIN 
        site_branch sb ON sd.branch_id = sb.uid where sd.branch_id=".qstr($branch_id)." and uu.user_position=".qstr(userPositionEnum::CHIEF_TELLER);
        return $this->reader->getRow($sql);
    }

}
