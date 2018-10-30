<?php

class certificationDataClass
{
    /**
     * 获取cert数据
     * @param $pageNumber
     * @param $pageSize
     * @param $filters
     * @return array
     */
    public static function getCertificationList($pageNumber, $pageSize, $filters)
    {
        $sql = "SELECT mvc.*,cm.display_name,cm.obj_guid,cm.login_code,ma.asset_name,ma.asset_sn FROM member_verify_cert mvc"
            . " INNER JOIN client_member cm ON mvc.member_id = cm.uid"
            . " LEFT JOIN member_assets ma ON mvc.uid = ma.cert_id"
            . " WHERE 1 = 1";
        if ($filters['search_text']) {
            $search_text = trim($filters['search_text']);
            $sql .= " AND (cm.obj_guid = " . qstr($search_text);
            $sql .= " OR cm.display_name LIKE '%" . qstr2($search_text) . "%'";
            $sql .= " OR cm.login_code LIKE '%" . qstr2($search_text) . "%'";
            $sql .= " OR (ma.asset_name LIKE '%" . qstr2($search_text) . "%' OR ma.asset_name is null)";
            $sql .= " OR (ma.asset_sn LIKE '%" . qstr2($search_text) . "%' OR ma.asset_sn is null)";
            $sql .= " OR mvc.cert_sn LIKE '%" . qstr2($search_text) . "%'";
            $sql .= " OR mvc.cert_name LIKE '%" . qstr2($search_text) . "%')";
        }

        if (intval($filters['cert_type'])) {
            $sql .= " AND mvc.cert_type = " . intval($filters['cert_type']);
        }

        if (intval($filters['verify_state']) == 1) {
            $sql .= " AND (mvc.verify_state = 0 OR mvc.verify_state = -1)";
        }
        if (intval($filters['verify_state']) >= certStateEnum::PASS) {
            $sql .= " AND mvc.verify_state = " . intval($filters['verify_state']);
        }

        $sql .= " ORDER BY mvc.create_time DESC";
        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 20;
        $r = new ormReader();
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        // 取图片
        foreach ($rows as $key => $row) {
            $sql = "select * from member_verify_cert_image where cert_id = " . $row['uid'];
            $images = $r->getRows($sql);
            $row['cert_images'] = $images;
            $rows[$key] = $row;
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }


    public static function getCertificationDetail($uid)
    {
        $r = new ormReader();
        $m_member_verify_cert = M('member_verify_cert');
        $data = $m_member_verify_cert->find(array('uid' => $uid));
        // image
        $sql = "select * from member_verify_cert_image where cert_id='" . $data['uid'] . "'";
        $images = $r->getRows($sql);
        $data['cert_images'] = $images;
        return $data;
    }

    public static function getCertificationAssetDetail($uid)
    {
        $r = new ormReader();
        $sql = "select c.*,a.uid asset_id from member_verify_cert c left join member_assets a on c.uid = a.cert_id where c.uid = $uid";
        $data = $r->getRow($sql);
        // image
        $sql = "select * from member_verify_cert_image where cert_id='" . $data['uid'] . "'";
        $images = $r->getRows($sql);
        $data['cert_images'] = $images;
        return $data;
    }

}