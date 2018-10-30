<?php

class customerReportClass
{

    public static function getCustomerList($pageNumber, $pageSize, $filters)
    {
        $r = new ormReader();
        $sql = "SELECT cm.* FROM client_member cm WHERE 1 = 1";
        if ($filters['search_text']) {
            $sql .= " AND (cm.display_name LIKE '%" . qstr2($filters['search_text']) . "%' or cm.obj_guid like '%".qstr2($filters['search_text'])."%' or cm.login_code like '%".qstr2($filters['search_text'])."%')";
        }
        if ($filters['date_start']) {
            $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
            $sql .= " AND cm.update_time >= " . qstr($date_start);
        }

        if ($filters['date_end']) {
            $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
            $sql .= " AND cm.update_time <= " . qstr($date_end);
        }

        $sql .= " ORDER BY cm.uid DESC";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $member_ids = array_column($rows, 'uid');
            $member_address = self::getMemberAddress($member_ids);
            $income_expense_arr = self::getMemberIncomeAndExpense($member_ids);
            foreach ($rows as $key => $row) {
                $income_expense = $income_expense_arr[$row['uid']];
                $address = $member_address[$row['uid']]['full_text'];
                $row['full_text'] = $address;
                $rows[$key] = array_merge(array(), $row, $income_expense);
            }
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

    public static function getMemberIncomeAndExpense($member_id)
    {
        $r = new ormReader();
        if (!is_array($member_id)) {
            $member_id = array($member_id);
        }
        $member_id_str = "(" . implode(',', $member_id) . ")";
        $where = " member_id IN $member_id_str";

        $sql1 = "SELECT member_id,SUM(profit) income_business FROM member_income_business WHERE $where AND operator_type = " . intval(operatorTypeEnum::BM) . " GROUP BY member_id";
        $income_business = $r->getRows($sql1);
        $income_business = resetArrayKey($income_business, 'member_id');

        $sql2 = "SELECT member_id,SUM(monthly_rent) assets_rental FROM member_assets_rental WHERE $where GROUP BY member_id";
        $assets_rental = $r->getRows($sql2);
        $assets_rental = resetArrayKey($assets_rental, 'member_id');

        $sql3 = "SELECT SUM(salary) income_salary FROM member_income_salary WHERE $where GROUP BY member_id";
        $income_salary = $r->getRows($sql3);
        $income_salary = resetArrayKey($income_salary, 'member_id');

        $sql4 = "SELECT SUM(CASE WHEN ext_type = 1 THEN ext_amount ELSE 0 END) income_attachment,SUM(CASE WHEN ext_type = 2 THEN ext_amount ELSE 0 END) expense_attachment FROM member_attachment WHERE ext_type > 0 AND $where GROUP BY member_id";
        $member_attachment = $r->getRows($sql4);
        $member_attachment = resetArrayKey($member_attachment, 'member_id');

        $arr = array();
        foreach ($member_id as $id) {
            $total_income = round($income_business[$id]['income_business'], 2) + round($assets_rental[$id]['assets_rental'], 2) + round($income_salary[$id]['member_income_salary'], 2) + round($member_attachment[$id]['income_attachment']);
            $total_expense = round($member_attachment[$id]['expense_attachment'], 2);
            $net_income = $total_income - $total_expense;
            $arr[$id] = array(
                'total_income' => $total_income,
                'total_expense' => $total_expense,
                'net_income' => $net_income
            );
        }
        return $arr;
    }

    public static function getMemberAddress($member_id)
    {
        $r = new ormReader();
        if (!is_array($member_id)) {
            $member_id = array($member_id);
        }
        $member_id_str = "(" . implode(',', $member_id) . ")";
        $sql = "SELECT cm.uid member_id,ca.* FROM common_address ca INNER JOIN client_member cm ON cm.obj_guid = ca.obj_guid WHERE cm.uid IN $member_id_str AND ca.state = 1 GROUP BY cm.uid";
        $address_list = $r->getRows($sql);
        $address_list = resetArrayKey($address_list, 'member_id');
        return $address_list;
    }
}