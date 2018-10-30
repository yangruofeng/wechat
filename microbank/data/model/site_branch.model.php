<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class site_branchModel extends tableModelBase
{
    public function  __construct()
    {
        parent::__construct('site_branch');
    }

    public function searchBranchListByFreeText($searchText, $pageNumber, $pageSize) {
        $sql = "SELECT sb.*,uu.user_code FROM site_branch sb LEFT JOIN um_user uu ON sb.manager = uu.uid ";
        if ($searchText) {
            $sql .= " WHERE sb.branch_code LIKE '%" . qstr2($searchText) . "%' OR sb.branch_name LIKE '%" . qstr2($searchText) . "%'";
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

    public function getBranchInfoById($uid){
        $uid = intval($uid);
        $info = $this->find(array('uid' => $uid));
        return $info;
    }

    public function getBranchInfoByGUID($guid)
    {
        $info = $this->find(array('obj_guid' => $guid));
        return $info;
    }

    public function getBranchSettingList(){
        //limit
        $sql1 = "select l.branch_id,l.limit_value from site_branch sb left join site_branch_limit l on sb.uid = l.branch_id where l.limit_key = 'approve_credit_limit'";
        //credit
//        $sql2 = "select receiver_id,credit from site_credit_flow order by uid desc limit 1";
        $sql = "select b.*,l.limit_value from site_branch b left join ($sql1) l on b.uid = l.branch_id";
       return $this->reader->getRows($sql);
    }

}
