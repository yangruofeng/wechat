<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/2
 * Time: 15:46
 */
class member_credit_grantModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('member_credit_grant');
    }

    /**
     * 最新授信记录
     */
    public function getCreditGrantBaseInfo($contact_phone)
    {
        $sql = "select g.uid,g.member_id,g.credit,g.max_credit,g.grant_time from member_credit_grant g left join client_member m on g.member_id = m.uid where m.phone_id = '$contact_phone' and m.is_verify_phone = '1' and g.state = 100 order by g.grant_time desc limit 1;";
        $info = $this->reader->getRow($sql);
        return $info;
    }

    public function getCreditGrantList($pageNumber, $pageSize, $filter = array())
    {
        $sql = "SELECT mcg.*,cm.display_name,cm.login_code FROM member_credit_grant mcg LEFT JOIN client_member cm ON mcg.member_id = cm.uid WHERE 1 = 1";
        if (intval($filter['member_id'])) {
            $sql .= " AND mcg.member_id = " . intval($filter['member_id']);
        }
        if (isset($filter['state'])) {
            $sql .= " AND mcg.state = " . intval($filter['state']);
        }
        $sql .= " ORDER BY mcg.uid DESC";

        $data = $this->reader->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        foreach ($rows as $key => $val) {
            $rows[$key]['grant_detail_list'] = $this->getGrantDetailByGrantId($val['uid']);
            $rows[$key]['grant_rate'] = $this->getGrantRateByGrantId($val['uid']);
        }

        return new result(true, null, array(
            'rows' => $rows,
            'total' => $total,
            'page_total' => $pageTotal
        ));
    }
    public function getCreditGrantNotSignList($pageNumber, $pageSize, $filter = array())
    {
        $sql = "SELECT mcg.*,cm.obj_guid,cm.display_name,cm.login_code FROM member_credit_grant mcg LEFT JOIN client_member cm ON mcg.member_id = cm.uid ";
        $sql.=" LEFT JOIN member_authorized_contract mac ON mcg.uid=mac.grant_credit_id ";
        $sql.=" WHERE mcg.state='".commonApproveStateEnum::PASS."' AND mac.uid IS NULL";
        if($filter['search_text']){
            $sql.=" and (cm.obj_guid = ".qstr($filter['search_text'])." or cm.display_name like '%".qstr2($filter['search_text'])."%' or cm.login_code like '%".qstr2($filter['search_text'])."%') ";
        }
        if (intval($filter['member_id'])) {
            $sql .= " AND mcg.member_id = " . intval($filter['member_id']);
        }

        $sql .= " ORDER BY mcg.uid DESC";

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
    public function getCreditGrantSignList($pageNumber, $pageSize, $filter = array())
    {
        $sql = "SELECT mac.*,cm.obj_guid,cm.display_name,cm.login_code FROM member_credit_grant mcg LEFT JOIN client_member cm ON mcg.member_id = cm.uid ";
        $sql.=" INNER JOIN member_authorized_contract mac ON mcg.uid=mac.grant_credit_id ";
        $sql.=" WHERE mcg.state=".qstr(commonApproveStateEnum::PASS);
        if($filter['search_text']){
            $sql.=" and (cm.obj_guid = ".qstr($filter['search_text'])." or cm.display_name like '%".qstr2($filter['search_text'])."%' or cm.login_code like '%".qstr2($filter['search_text'])."%') ";
        }
        if (intval($filter['member_id'])) {
            $sql .= " AND mcg.member_id = " . intval($filter['member_id']);
        }

        $sql .= " ORDER BY mcg.uid DESC";

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
    public function getGrantDetailByGrantId($uid)
    {
        $sql = "select e.*,a.asset_type,a.asset_name,a.mortgage_state,a.valuation from member_credit_grant_assets e left join member_assets a on a.uid=e.member_asset_id
            where e.grant_id=" . $uid;
        $list = $this->reader->getRows($sql);
        return resetArrayKey($list, 'member_asset_id');
    }

    public function getGrantRateByGrantId($uid)
    {
        $sql = "select * from member_credit_grant_rate WHERE credit_grant_id = " . $uid;
        $list = $this->reader->getRows($sql);
        return resetArrayKey($list, 'product_id');
    }
}