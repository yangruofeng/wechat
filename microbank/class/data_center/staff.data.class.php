<?php

class staffDataClass
{
    public static function getClientLogType()
    {
        $sql = "SELECT client_type FROM um_user_log GROUP BY client_type";
        $r = new ormReader();
        $client_type = $r->getRows($sql);
        return $client_type;
    }

    public static function getStaffLogList($pageNumber, $pageSize, $filters)
    {
        $where = " WHERE 1 = 1";
        if (intval($filters['uid'])) {
            $where .= " AND user_id = " . intval($filters['uid']);
        }

        if ($filters['date_start']) {
            $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
            $where .= " AND login_time >= " . qstr($date_start);
        }

        if ($filters['date_end']) {
            $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
            $where .= " AND login_time <= " . qstr($date_end);
        }

        if ($filters['client_type']){
            $where .= " AND client_type = " . qstr($filters['client_type']);
        }

        $r = new ormreader();
        $sql = "SELECT * FROM um_user_log $where";
        $sql .= " ORDER BY uid DESC";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

}