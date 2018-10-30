<?php

class loanOutstandingClass {

    public static function getloanOutstandingProvinceData($branch_id, $filters = array()){
        $m_loan_contract = new loan_contractModel();

        $where = "c.state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE) . " AND c.state < " . qstr(loanContractStateEnum::COMPLETE) . " AND s.state != " . qstr(schemaStateTypeEnum::CANCEL) . " AND s.state != " . qstr(schemaStateTypeEnum::COMPLETE);

        if(intval($branch_id)){
            $where .= " AND m.branch_id = '$branch_id'";
        }

        if ($filters['date_start']) {
            $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
            $where .= " AND s.create_time >= " . qstr($date_start);
        }
        if ($filters['date_end']) {
            $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
            $where .= " AND s.create_time <= " . qstr($date_end);
        }

        $sql = <<<SQL
select a.obj_guid,m.gender,m.login_code,m.id_address1,t.node_text_alias id_address1_name,m.id_address2,r.node_text_alias id_address2_name,m.id_address3,m.id_address4,c.apply_amount apply_amount,sum(s.receivable_principal) principal_balance,c.currency 
from loan_installment_scheme s 
left join loan_contract c on s.contract_id = c.uid 
left join loan_account a on c.account_id = a.uid 
left join client_member m on m.obj_guid = a.obj_guid 
left join core_tree t on t.uid = m.id_address1
left join core_tree r on r.uid = m.id_address2  
where $where 
group by a.obj_guid,m.id_address1,m.id_address2,m.id_address3,m.id_address4,c.currency 
SQL;

        $rows = $m_loan_contract->reader->getRows($sql);
        $loan = array();
        foreach ($rows as $k => $v) {
            $gender =  $v['gender'] == 'male' || !$v['gender'] ? 1 : 2;
            if($v['id_address1'] && $v['id_address2'] && $v['id_address3'] && $v['id_address4']){ //地址存在
                $loan[$v['id_address1']]['text_name'] = $v['id_address1_name']; 
                if(!in_array($v['id_address2'], $loan[$v['id_address1']]['children'][$v['id_address2']]['district'])){
                    $loan[$v['id_address1']]['children'][$v['id_address2']]['district'][] =  $v['id_address2'];
                }
                if(!in_array($v['id_address3'], $loan[$v['id_address1']]['children'][$v['id_address2']]['commune'])){
                    $loan[$v['id_address1']]['children'][$v['id_address2']]['commune'][] =  $v['id_address3'];
                }
                if(!in_array($v['id_address4'], $loan[$v['id_address1']]['children'][$v['id_address2']]['village'])){
                    $loan[$v['id_address1']]['children'][$v['id_address2']]['village'][] =  $v['id_address4'];
                }
                $loan[$v['id_address1']]['children'][$v['id_address2']]['text_name']  = $v['id_address2_name']; 
                $loan[$v['id_address1']]['children'][$v['id_address2']]['male']  += $gender == 1 ? 1 : 0; 
                $loan[$v['id_address1']]['children'][$v['id_address2']]['female'] += $gender == 2 ? 1 : 0;
                $loan[$v['id_address1']]['children'][$v['id_address2']]['total'] += 1;
                $loan[$v['id_address1']]['children'][$v['id_address2']]['apply_amount'][$v['currency']] += $v['apply_amount']; 
                $loan[$v['id_address1']]['children'][$v['id_address2']]['principal_balance'][$v['currency']] += $v['principal_balance'];
            }else{ //地址不存在
                $children['text_name'] = ''; 
                $children['district'] = 0; 
                $children['commune'] = 0; 
                $children['village'] = 0; 
                $children['male'] += $gender == 1 ? 1 : 0; 
                $children['female'] += $gender == 2 ? 1 : 0; 
                $children['total'] += 1; 
                $children['apply_amount'][$v['currency']] += $v['apply_amount']; 
                $children['principal_balance'][$v['currency']] += $v['principal_balance']; 
                $loan['empty']['children']['child'] = $children;
                $loan['empty']['text_name'] = '';
            }
        }

        foreach ($loan as $k => $v) {
            foreach ($v['children'] as $ck => $cv) {
                $temp = array();
                $temp['text_name'] = $cv['text_name'];
                $temp['district'] = count($cv['district']);
                $temp['commune'] = count($cv['commune']);
                $temp['village'] = count($cv['village']);
                $temp['male'] = $cv['male'];
                $temp['female'] = $cv['female'];
                $temp['total'] = $cv['total'];
                $temp['apply_amount'] = $cv['apply_amount'];
                $temp['principal_balance'] = $cv['principal_balance'];
                $loan[$k]['children'][$ck] = $temp;
                $loan[$k]['total']['district'] += $temp['district'];
                $loan[$k]['total']['commune'] += $temp['commune'];
                $loan[$k]['total']['village'] += $temp['village'];
                $loan[$k]['total']['male'] += $temp['male'];
                $loan[$k]['total']['female'] += $temp['female'];
                $loan[$k]['total']['total'] += $temp['total'];
                foreach ($cv['apply_amount']as $key => $value) {
                    $loan[$k]['total']['apply_amount'][$key] += $cv['apply_amount'][$key];
                }
                foreach ($cv['principal_balance']as $key => $value) {
                    $loan[$k]['total']['principal_balance'][$key] += $cv['principal_balance'][$key];
                }
            }
        }
        $news = array();
        if($loan){
            $news['empty'] = $loan['empty'];
            unset($loan['empty']);
        }
        return array_merge($news, $loan);
    }

    public static function getloanOutstandingGenderData($pageNumber, $pageSize, $filters = array()){
        $r = new ormReader();
        $sql = "SELECT uu.*,sb.branch_name FROM um_user uu"
            . " INNER JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " INNER JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE uu.user_status = 1 AND uu.user_position = " . qstr(userPositionEnum::CREDIT_OFFICER);
        if (intval($filters['branch_id'])) {
            $sql .= " AND sb.uid = " . intval($filters['branch_id']);
        }
        if (trim($filters['search_text'])) {
            $search_text = qstr2(trim($filters['search_text']));
            $sql .= " AND (uu.user_code like '%" . $search_text . "%' OR uu.user_name like '%" . $search_text . "%')";
        }
        //1、分页查询co
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $rows = resetArrayKey($rows, 'uid');
            $co_ids = array_column($rows, 'uid');
            $co_id_str = '(' . implode(',', $co_ids) . ')';
            $where = " o.officer_id IN $co_id_str AND o.officer_type = '0' AND o.is_active = '1'";

            $where1 = "c.state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE) . " AND c.state != " . qstr(loanContractStateEnum::WRITE_OFF) . " AND s.state != " . qstr(schemaStateTypeEnum::CANCEL) . " AND s.state != " . qstr(schemaStateTypeEnum::COMPLETE);

            if ($filters['date_start']) {
                $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
                $where1 .= " AND s.create_time >= " . qstr($date_start);
            }
            if ($filters['date_end']) {
                $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
                $where1 .= " AND s.create_time <= " . qstr($date_end);
            }
            
            $sql = <<<SQL
select o.officer_id,m.uid,m.gender,s.apply_amount,s.currency from member_follow_officer  o
left join client_member m on o.member_id = m.uid
left join loan_account a on m.obj_guid = a.obj_guid 
inner join (
select c.account_id,c.currency, sum(s.receivable_principal) apply_amount from loan_contract c 
left join loan_installment_scheme s on s.contract_id = c.uid
where $where1
group by c.account_id,c.currency
) s on s.account_id = a.uid
where $where
group by o.officer_id,m.uid 
order by o.officer_id asc
SQL;

            $ret = $r->getRows($sql);
            $list = array();
            //统计单个co的各个client的male,female的总和
            foreach ($ret as $k => $v) {
                if($v['gender'] == 'male' || !$v['gender']){
                    //male 为null则算为male
                    if(!in_array($v['uid'], $list[$v['officer_id']]['male']['count'])){
                        $list[$v['officer_id']]['male']['count'][] =  $v['uid']; //Array: client uid
                    }
                    if($v['currency']){
                        $list[$v['officer_id']]['male']['amount'][$v['currency']] += $v['apply_amount'];
                    }
                }else{
                    //female
                    if(!in_array($v['uid'], $list[$v['officer_id']]['male']['count'])){
                        $list[$v['officer_id']]['female']['count'][] =  $v['uid']; //Array: client uid
                    }
                    if($v['currency']){
                        $list[$v['officer_id']]['female']['amount'][$v['currency']] += $v['apply_amount'];
                    }
                }
            }
            //合并officer，loan两个数组，没有loan过得也显示
            foreach ($rows as $k => $v) {
                if(!$list[$k]){
                    $list[$k] = array();
                }
            }
            ksort($list);
            $total_amount = array();
            //1、统计单个co的所有client的总和 2、统计当前页的所有co的total
            foreach ($list as $k => $v) {
                $list[$k]['officer_name'] = $rows[$k]['user_name'];
                //单个co的total
                foreach ($v as $ck => $cv) {
                    $list[$k][$ck]['count'] = count($cv['count']);
                    $list[$k][$ck]['amount'] = $cv['amount'];
                    $list[$k]['total']['count'] += count($cv['count']);
                    foreach ($cv['amount'] as $key => $value) {
                        $list[$k]['total']['amount'][$key] += $cv['amount'][$key];
                    }
                }

                //所有列的total
                $total_amount['male']['count'] += $list[$k]['male']['count'];
                foreach ($list[$k]['male']['amount'] as $key => $value) {
                    $total_amount['male']['amount'][$key] += $value;
                }
                $total_amount['female']['count'] += $list[$k]['female']['count'];
                foreach ($list[$k]['female']['amount'] as $key => $value) {
                    $total_amount['female']['amount'][$key] += $value;
                }
                $total_amount['total']['count'] += $list[$k]['total']['count'];
                foreach ($list[$k]['total']['amount'] as $key => $value) {
                    $total_amount['total']['amount'][$key] += $value;
                }
            }
        }

        return array(
            "sts" => true,
            "data" => $list,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize,
            "total_amount" => $total_amount,
            "currency_list" => $currency_list
        );
    }

    /**
     * 获取product report 表头
     */
    public static function getLoanOutstandingProductList(){
        $r = new ormReader();
        $sql = "select uid,repayment_type,sub_product_name from loan_sub_product where state = " .qstr(loanProductStateEnum::ACTIVE);
        $list = $r->getRows($sql);
        return $list;
    }

    /**
     * 获取product report 数据
     */
    public static function getLoanOutstandingProductData($pageNumber, $pageSize, $filters = array()){
        $r = new ormReader();
        $sql = "SELECT uu.*,sb.branch_name FROM um_user uu"
            . " INNER JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " INNER JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE uu.user_status = 1 AND uu.user_position = " . qstr(userPositionEnum::CREDIT_OFFICER);
        if (intval($filters['branch_id'])) {
            $sql .= " AND sb.uid = " . intval($filters['branch_id']);
        }
        if (trim($filters['search_text'])) {
            $search_text = qstr2(trim($filters['search_text']));
            $sql .= " AND (uu.user_code like '%" . $search_text . "%' OR uu.user_name like '%" . $search_text . "%')";
        }
        
        //1、分页查询co
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $rows = resetArrayKey($rows, 'uid');
            $co_ids = array_column($rows, 'uid');
            $co_id_str = '(' . implode(',', $co_ids) . ')';
            $where = " o.officer_id IN $co_id_str AND o.officer_type = '0' AND o.is_active = '1'";

            $where1 = "c.state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE) . " AND c.state < " . qstr(loanContractStateEnum::COMPLETE) . " AND s.state != " . qstr(schemaStateTypeEnum::CANCEL) . " AND s.state != " . qstr(schemaStateTypeEnum::COMPLETE) . " AND c.currency = " . qstr($filters['currency']) . " AND p.state = " . qstr(loanProductStateEnum::ACTIVE);

            if ($filters['date_start']) {
                $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
                $where1 .= " AND s.create_time >= " . qstr($date_start);
            }
            if ($filters['date_end']) {
                $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
                $where1 .= " AND s.create_time <= " . qstr($date_end);
            }
            $sql = <<<SQL
select o.officer_id,m.uid,s.sub_product_id,s.sub_product_name,s.currency, s.amount from member_follow_officer o 
left join client_member m on o.member_id = m.uid 
left join loan_account a on m.obj_guid = a.obj_guid 
inner join ( 
select c.account_id,c.sub_product_id,c.sub_product_name,c.currency, sum(s.receivable_principal) amount from loan_sub_product p 
left join loan_contract c on p.uid = c.sub_product_id left join loan_installment_scheme s on c.uid = s.contract_id 
where $where1  
group by c.sub_product_id,c.account_id
order by c.sub_product_id 
) s on a.uid = s.account_id 
where $where
group by o.officer_id,m.uid,s.sub_product_id order by o.officer_id
SQL;

            $ret = $r->getRows($sql);
            $list = array();
            foreach ($ret as $k => $v) {
                if(!in_array($v['uid'], $list[$v['officer_id']]['product'][$v['sub_product_id']]['count'])){
                    $list[$v['officer_id']]['product'][$v['sub_product_id']]['uids'][] = $v['uid']; //Array: client uid
                }
                $list[$v['officer_id']]['product'][$v['sub_product_id']]['count'] = count($list[$v['officer_id']]['product'][$v['sub_product_id']]['uids']);
                $list[$v['officer_id']]['product'][$v['sub_product_id']]['amount'] += $v['amount'];
                $list[$v['officer_id']]['officer_name'] = $rows[$v['officer_id']]['user_name'];
            }

            //合并officer，loan两个数组，没有loan过得也显示
            foreach ($rows as $k => $v) {
                if(!$list[$k]){
                    $list[$k]['officer_name'] = $v['user_name'];
                }
            }
            ksort($list);
        }

        return array(
            "sts" => true,
            "data" => $list,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize,
            "currency_list" => $currency_list
        );
    }

    /**
     * 获取co逾期合同
     */
    public static function getPaymentInArrearData($filters = array()){
        $where1 = "c.state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE) . " AND c.state < " . qstr(loanContractStateEnum::COMPLETE) . " AND s.state != " . qstr(schemaStateTypeEnum::CANCEL) . " AND s.state != " . qstr(schemaStateTypeEnum::COMPLETE). " and c.currency = " . qstr($filters['currency']?:'USD');
        
        //查询剩余未还
        $sql1 = <<<SQL
select c.uid, sum(s.amount) amount 
from loan_installment_scheme s 
left join loan_contract c ON c.uid = s.contract_id
where $where1 
group by c.uid
SQL;

        $where2 = $where1." and date_format(s.receivable_date,'%Y%m%d') < '" . date('Ymd') . "'";
        //查询合同逾期总额
        $sql2 = <<<SQL
select a.obj_guid,c.uid,s.receivable_date,datediff(now(),s.receivable_date) days, sum(s.receivable_principal) principal,sum(s.receivable_interest) interest, sum(s.receivable_principal + s.receivable_interest) total
from loan_installment_scheme s 
left join loan_contract c ON c.uid = s.contract_id
left join  loan_account a on c.account_id = a.uid 
where $where2 
group by c.uid order by c.uid desc
SQL;

        //查询合同表信息
        $where3 = " f.officer_type = '0' AND f.is_active = '1'";
        if (intval($filters['branch_id'])) {
            $where3 .= " and m.branch_id = " . intval($filters['branch_id']);
        }

        $sql3 = <<<SQL
select m.uid,m.login_code,m.phone_id,f.officer_id,f.officer_name,c.uid contract_id,c.contract_sn,c.loan_period_value periods,c.start_date,c.end_date,c.loan_actual_cycle,c.apply_amount,due.receivable_date,due.days,sum(d.amount) amount,due.principal,due.interest,due.total,al.amount late_amount from loan_disbursement_scheme d 
left join loan_contract c on c.uid = d.contract_id
inner join ($sql1) al on al.uid = c.uid
inner join ($sql2) due on c.uid = due.uid
inner join client_member m on due.obj_guid = m.obj_guid
inner join member_follow_officer f on m.uid = f.member_id 
where $where3
group by c.uid
SQL;

        $r = new ormReader();  
        $ret = $r->getRows($sql3); 
        $list = array();
        foreach ($ret as $k => $v) {
            $list[$v['officer_id']]['officer_name'] = $v['officer_name'];
            $list[$v['officer_id']]['contract'][$v['contract_id']]['contract_sn'] = $v['contract_sn'];
            $list[$v['officer_id']]['contract'][$v['contract_id']]['client_name'] = $v['login_code'];
            $list[$v['officer_id']]['contract'][$v['contract_id']]['bussiness'] = '';//未查字段
            $list[$v['officer_id']]['contract'][$v['contract_id']]['phone'] = $v['phone_id'];
            $list[$v['officer_id']]['contract'][$v['contract_id']]['occupation'] = '';//未查字段
            $list[$v['officer_id']]['contract'][$v['contract_id']]['disburse'] = $v['start_date'];
            $list[$v['officer_id']]['contract'][$v['contract_id']]['maturity'] = $v['end_date'];
            $list[$v['officer_id']]['contract'][$v['contract_id']]['circle'] = $v['loan_actual_cycle'];
            $list[$v['officer_id']]['contract'][$v['contract_id']]['disburse_amount'] = $v['amount'];
            $list[$v['officer_id']]['contract'][$v['contract_id']]['period'] = $v['periods'];
            $list[$v['officer_id']]['contract'][$v['contract_id']]['month_period'] = '';//未查字段
            $list[$v['officer_id']]['contract'][$v['contract_id']]['principal'] = $v['principal'];
            $list[$v['officer_id']]['contract'][$v['contract_id']]['interest'] = $v['interest'];
            $list[$v['officer_id']]['contract'][$v['contract_id']]['total'] = $v['total'];
            $list[$v['officer_id']]['contract'][$v['contract_id']]['late_amount'] = $v['late_amount'];
            $list[$v['officer_id']]['contract'][$v['contract_id']]['days'] = $v['days'];
            $list[$v['officer_id']]['total']['contract_number'] += 1;
            $list[$v['officer_id']]['total']['disburse_amount'] += $v['amount'];
            $list[$v['officer_id']]['total']['principal'] += $v['principal'];
            $list[$v['officer_id']]['total']['interest'] += $v['interest'];
            $list[$v['officer_id']]['total']['total'] += $v['total'];
            $list[$v['officer_id']]['total']['late_amount'] += $v['late_amount'];
        }
        return $list;
    }

    public static function getLoanCollectionByCategoryData($pageNumber, $pageSize, $filters = array()){
        $r = new ormReader();
        $sql = "SELECT uu.*,sb.branch_name FROM um_user uu"
            . " INNER JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " INNER JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE uu.user_status = 1 AND uu.user_position = " . qstr(userPositionEnum::CREDIT_OFFICER);
        if (intval($filters['branch_id'])) {
            $sql .= " AND sb.uid = " . intval($filters['branch_id']);
        }
        if (trim($filters['search_text'])) {
            $search_text = qstr2(trim($filters['search_text']));
            $sql .= " AND (uu.user_code like '%" . $search_text . "%' OR uu.user_name like '%" . $search_text . "%')";
        }
        
        //1、分页查询co
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        if ($rows) {
            $where1 = " c.currency = " . qstr($filters['currency']);
            $rows = resetArrayKey($rows, 'uid');
            $co_ids = array_column($rows, 'uid');
            $co_id_str = '(' . implode(',', $co_ids) . ')';
            $where2 = " o.officer_id IN $co_id_str AND o.officer_type = '0' AND o.is_active = '1'";
            
            $sql1 = <<<SQL
select c.account_id,c.currency,sum(s.receivable_principal) principal,sum(s.receivable_interest) interest,sum(c.receivable_penalty) penalty,sum(c.receivable_loan_fee) loan_fee,sum(c.receivable_admin_fee) admin_fee,sum(c.receivable_operation_fee) operation_fee 
from loan_installment_scheme s 
inner join loan_contract c on s.contract_id = c.uid
where $where1
group by c.account_id
SQL;

            $sql2 = <<<SQL
select o.officer_id,o.officer_name,s.currency,s.principal,s.interest,s.penalty,s.loan_fee,s.admin_fee,s.operation_fee from member_follow_officer o inner join client_member m on o.member_id = m.uid 
inner join loan_account a on m.obj_guid = a.obj_guid
inner join ($sql1) s on a.uid = s.account_id
where $where2 
SQL;

            //echo $sql2;die;
            $ret = $r->getRows($sql2); 
            $list = array();
            foreach ($ret as $k => $v) {
                $list[$v['officer_id']]['officer_name'] = $v['officer_name'];
                $list[$v['officer_id']]['principal'] += $v['principal'];
                $list[$v['officer_id']]['interest'] += $v['interest'];
                $list[$v['officer_id']]['penalty'] += $v['penalty'];
                $list[$v['officer_id']]['loan_fee'] += $v['loan_fee'];
                $list[$v['officer_id']]['admin_fee'] += $v['admin_fee'];
                $list[$v['officer_id']]['operation_fee'] += $v['operation_fee'];
                $sum = $v['principal'] + $v['interest'] + $v['penalty'] + $v['loan_fee'] + $v['admin_fee'] + $v['operation_fee'];
                $list[$v['officer_id']]['total'] += $sum;

            }
            //合并officer，loan两个数组，没有loan过得也显示
            foreach ($rows as $k => $v) {
                if(!$list[$k]){
                    $list[$k]['officer_name'] = $v['user_name'];
                }
            }
            ksort($list);
        }

        return array(
            "sts" => true,
            "data" => $list,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize,
            "currency_list" => $currency_list
        );
    }
}