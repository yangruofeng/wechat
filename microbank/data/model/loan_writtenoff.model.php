<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/28
 * Time: 14:57
 */
class loan_writtenoffModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('loan_writtenoff');
    }

    public function getWrittenOffDetail($uid)
    {
        $uid = intval($uid);
        $r = new ormReader();
        $sql = "SELECT lw.*,lc.contract_sn,lc.sub_product_name,lc.apply_amount,lc.start_date,lc.state contract_state,cm.display_name,cm.login_code,cm.uid member_id,cm.phone_id,cm.member_icon FROM loan_writtenoff lw"
            . " INNER JOIN loan_contract lc ON lw.contract_id = lc.uid"
            . " INNER JOIN loan_account la ON lc.account_id = la.uid"
            . " INNER JOIN client_member cm ON la.obj_guid = cm.obj_guid WHERE lw.uid = $uid";
        $detail = $r->getRow($sql);
        return $detail;
    }

    public function getWrittenOffList($pageNumber, $pageSize, $filter)
    {
        $r = new ormReader();
        $sql = "SELECT lw.*,lc.contract_sn,lc.sub_product_name,cm.login_code FROM loan_writtenoff lw"
            . " INNER JOIN loan_contract lc ON lw.contract_id = lc.uid"
            . " INNER JOIN loan_account la ON lc.account_id = la.uid"
            . " INNER JOIN client_member cm ON la.obj_guid = cm.obj_guid WHERE 1 = 1";

        if (isset($filter['state'])) {
            $sql .= " AND lw.state = " . intval($filter['state']);
        }

        if (trim($filter['search_text'])) {
            $sql .= " AND (lc.contract_sn = " . qstr(trim($filter['search_text'])) . " OR cm.login_code LIKE '%" . qstr2(trim($filter['search_text'])) . "%')";
        }
        $sql .= " ORDER BY lw.uid DESC";
        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "data" => $rows,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize,
        );
    }


}