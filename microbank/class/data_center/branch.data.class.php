<?php

class branchDataClass
{
    public static function getBranchStaffData($branch_id, $pageNumber, $pageSize, $filters = array()){
        $r = new ormReader();
        $where = "d.branch_id = " . qstr($branch_id);
        if($filters['staff']){
            $where .= " and (u.uid like '%".$filters['staff']."%' or u.user_code like '%".$filters['staff']."%' or u.user_name like '%".$filters['staff']."%')";
        }
        if($filters['phone_number']){
            $phone = tools::getFormatPhone($filters['country_code'], $filters['phone_number']);
            $where .= " and u.mobile_phone like '%".$phone['contact_phone']."%'";
        }
        if($filters['user_status'] > -1){
            $where .= " and u.user_status = " .qstr($filters['user_status']);
        }
        $sql = "select d.branch_id,u.* from um_user u left join site_depart d on u.depart_id = d.uid where $where";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $list = $data->rows;
        $num = ($pageNumber - 1) * $pageSize;
        foreach($list as $k => $v){
            $list[$k]['no'] = ++$num;
        }
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "data" => $list,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public static function getBranchBankData($branch_id, $pageNumber, $pageSize, $filters = array()){
        $r = new ormReader();
        $where = "b.branch_id = " . qstr($branch_id);
        if($filters['bank_id']){
            $where .= " and b.uid = " . qstr($filters['bank_id']);
        }
        if($filters['account_state'] > -1){
            $where .= " and b.account_state = " .qstr($filters['account_state']);
        }
        $sql = "select b.uid,b.obj_guid,b.bank_name,b.bank_account_no,b.bank_account_name,b.currency,b.account_state,br.branch_name,p.uid book_id from site_bank b
left join site_branch br on b.branch_id = br.uid
inner join passbook p on b.obj_guid = p.obj_guid
 where $where";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        if(count($rows) > 0){
            $book_ids = resetArrayKey($rows,"book_id");
            $ids = array_keys($book_ids);
            $str_ids = implode("','",$ids);
            $str_ids = "IN('$str_ids')";

            $where1 = "a.book_id $str_ids";
            if ($filters['start_date']) {
                $start_date = system_toolClass::getFormatStartDate($filters['start_date']);
                $where1 .= " AND f.update_time >= '$start_date' ";
            }

            if ($filters['end_date']) {
                $end_date = system_toolClass::getFormatEndDate($filters['end_date']);
                $where1 .= " AND f.update_time <= '$end_date' ";
            }
            $sql1 = "select a.book_id,a.uid,a.currency,a.balance,sum(f.credit) credit,sum(f.debit) debit from passbook_account_flow f inner join passbook_account a on f.account_id = a.uid where $where1 group by a.uid";

            $ret = $r->getRows($sql1);
            $flow = array();
            foreach($ret as $k => $v){
                $flow[$v['book_id']] = $v;
            }
            $num = ($pageNumber - 1) * $pageSize;
            foreach($rows as $k => $v){
                $rows[$k]['no'] = ++$num;
                $rows[$k]['balance'] = $flow[$v['book_id']]['balance'];
                $rows[$k]['credit'] = $flow[$v['book_id']]['credit'];
                $rows[$k]['debit'] = $flow[$v['book_id']]['debit'];
                $rows[$k]['account_id'] = $flow[$v['book_id']]['uid'];
            }
        }
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public static function getBranchBankFlowData($account_id, $pageNumber, $pageSize, $filters = array()){
        $r = new ormReader();
        $where = "f.account_id = " . qstr($account_id) ." and f.state = " . qstr(passbookAccountFlowStateEnum::DONE);
        if ($filters['start_date']) {
            $start_date = system_toolClass::getFormatStartDate($filters['start_date']);
            $where .= " AND f.update_time >= '$start_date' ";
        }

        if ($filters['end_date']) {
            $end_date = system_toolClass::getFormatEndDate($filters['end_date']);
            $where .= " AND f.update_time <= '$end_date' ";
        }
        $sql = "select a.uid,a.currency,f.uid flow_id,f.begin_balance,f.credit,f.debit,f.end_balance,f.update_time,t.sys_memo,t.subject from passbook_account_flow f left join passbook_account a on a.uid = f.account_id inner join passbook_trading t on f.trade_id = t.uid where $where";

        $data = $r->getPage($sql,$pageNumber,$pageSize);
        $rows = $data->rows;
        $num = ($pageNumber - 1) * $pageSize;
        $list = array();
        foreach($rows as $k => $v){
            $rows[$k]['no'] = ++$num;
        }
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }
}