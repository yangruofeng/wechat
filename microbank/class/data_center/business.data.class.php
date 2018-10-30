<?php

class businessDataClass
{
    public static function getCreditGroupCategory()
    {
        $r = new ormReader();
        //按产品分类计算credit和balance
        $sql = "select category_id,sum(credit) credit,sum(credit_balance) credit_balance from member_credit_category where is_close = 0 group by category_id";
        $arr_credit = $r->getRows($sql);
        $arr_credit = resetArrayKey($arr_credit, "category_id");
        $sql = "select * from loan_category where is_close = 0";
        $category = $r->getRows($sql);

        $arr_detail = array();
        $total_credit = 0;
        $total_credit_balance = 0;
        foreach ($category as $item) {
            $credit = $arr_credit[$item['uid']] ?: array();
            $arr_detail[] = array_merge($item, $credit);
            $total_credit += $credit['credit'] ?: 0;
            $total_credit_balance += $credit['credit_balance'] ?: 0;
        }

        $credit_group = array(
            'credit_arr' => $arr_detail,
            'total_credit' => $total_credit,
            'total_credit_balance' => $total_credit_balance,
        );

        return $credit_group;
    }

    public static function getPendingCredit()
    {
        $r = new ormReader();
        $sql = "SELECT mcc.category_id,SUM(a.`max_credit`) credit FROM member_credit_grant a"
            . " INNER JOIN member_credit_category mcc ON a.default_credit_category_id = mcc.uid"
            . " LEFT JOIN member_authorized_contract b ON a.uid = b.grant_credit_id"
            . " WHERE b.uid IS NULL AND a.state = " . qstr(commonApproveStateEnum::PASS)
            . " GROUP BY mcc.category_id";
        $pending_credit = $r->getRows($sql);

        $arr_credit = resetArrayKey($pending_credit, "category_id");
        $sql = "select * from loan_category where is_close = 0";
        $category = $r->getRows($sql);
        $arr_detail = array();
        $total_credit = 0;
        foreach ($category as $item) {
            $credit = $arr_credit[$item['uid']] ?: array();
            $arr_detail[] = array_merge($item, $credit);
            $total_credit += $credit['credit'] ?: 0;
        }

        $data = array(
            'pending_credit' => $arr_detail,
            'total_credit' => $total_credit,
        );
        return $data;
    }

    public static function creditTop10()
    {
        $sql = "SELECT mcc.*,cm.display_name,cm.login_code FROM member_credit_category mcc"
            . " LEFT JOIN client_member cm ON mcc.member_id = cm.uid"
            . " WHERE is_close = 0 ORDER BY credit DESC limit 10";
        $r = new ormReader();
        $top_list = $r->getRows($sql);
        return $top_list;
    }

    public static function creditAgreement($pageNumber, $pageSize, $filter = array())
    {
        $r = new ormReader();
        $where = ' where c.state = ' . qstr(authorizedContractStateEnum::COMPLETE);

        if ($filter['obj_guid']) {
            $where .= ' and m.obj_guid = ' . qstr($filter['obj_guid']);
        }
        if ($filter['member_name']) {
            $where .= ' and (m.login_code like "%' . qstr2($filter['member_name']) . '%" or m.display_name like "%' . qstr2($filter['member_name']) . '%")';
        }
        $sql = "select m.login_code,m.obj_guid,m.display_name,c.* from member_authorized_contract c left join client_member m on c.member_id = m.uid $where";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $list = $data->rows;
        if ($list) {
            $uids = implode(',', array_column($list, 'uid'));
            $sql1 = "select * from member_authorized_contract_image where authorized_contract_id in($uids)";
            $imgs = $r->getRows($sql1);
            $images = array();
            foreach ($imgs as $v) {
                $images[$v['authorized_contract_id']][] = $v['image_path'];
            }
            foreach ($list as $k => $v) {
                $list[$k]['images'] = $images[$v['uid']];
            }
        }
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "list" => $list,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public static function creditLog($pageNumber, $pageSize, $filter = array())
    {
        $r = new ormReader();
        $where = ' where 1 = 1 ';

        if ($filter['obj_guid']) {
            $where .= ' and m.obj_guid = ' . qstr($filter['obj_guid']);
        }
        if ($filter['member_name']) {
            $where .= ' and (m.login_code like "%' . qstr2($filter['member_name']) . '%" or m.display_name like "%' . qstr2($filter['member_name']) . '%")';
        }
        $sql = "select m.login_code,m.obj_guid,m.display_name,l.* from member_credit_log l left join client_member m on l.member_id = m.uid $where";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $list = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "list" => $list,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public function getLoanOverview()
    {
        $r = new ormReader();

        $sql = "SELECT mcc.category_id,lc.currency,SUM(lc.apply_amount) apply_amount FROM loan_contract lc"
            . " INNER JOIN member_credit_category mcc ON lc.member_credit_category_id = mcc.uid"
            . " WHERE mcc.is_close = 0 AND lc.state >= " . loanContractStateEnum::PENDING_DISBURSE
            . " GROUP BY mcc.category_id,lc.currency";
        $arr_loan = $r->getRows($sql);
        $arr_loan_new = array();
        foreach ($arr_loan as $loan) {
            $arr_loan_new[$loan['category_id']][$loan['currency']] = $loan['apply_amount'];
        }

        $sql = "select * from loan_category where is_close = 0";
        $category = $r->getRows($sql);

        $currency_list = (new currencyEnum())->Dictionary();
        $arr_detail = array();
        $total_loan = array();
        foreach ($currency_list as $key => $currency) {
            $total_loan[$key] = 0;
        }
        $total_loan['usd_total'] = 0;
        foreach ($category as $item) {
            $loan = $arr_loan_new[$item['uid']] ?: array();
            $apply_amount_usd = 0;
            foreach ($loan as $key => $apply_amount) {
                $total_loan[$key] += round($apply_amount, 2);
                if ($key != currencyEnum::USD) {
                    $rate = global_settingClass::getCurrencyRateBetween($key, currencyEnum::USD);
                    $apply_amount_usd += round($apply_amount * $rate, 2);
                } else {
                    $apply_amount_usd += $apply_amount;
                }
            }
            $loan['usd_total'] = $apply_amount_usd;
            $arr_detail[] = array_merge($item, $loan);
            $total_loan['usd_total'] += $apply_amount_usd;
        }

        $credit_group = array(
            'loan_arr' => $arr_detail,
            'total_loan' => $total_loan,
            'currency_list' => $currency_list,
        );

        return $credit_group;
    }

    public function loanTop10()
    {
        $currency_list = (new currencyEnum())->Dictionary();
        $sql = 'case lc.currency';
        foreach ($currency_list as $key => $currency) {
            $rate = global_settingClass::getCurrencyRateBetween($key, currencyEnum::USD);
            $sql .= " when '" . $key . "' then lc.apply_amount * $rate";
        }
        $sql .= " else 0 end";

        $r = new ormReader();
        $sql = "SELECT cm.login_code,cm.display_name,mcc.alias,lc.apply_amount,lc.currency,lc.create_time,($sql) as apply_amount_usd FROM loan_contract lc"
            . " INNER JOIN client_member cm on cm.obj_guid = lc.client_obj_guid"
            . " INNER JOIN member_credit_category mcc on mcc.uid = lc.member_credit_category_id"
            . " where lc.state >= " . loanContractStateEnum::PENDING_DISBURSE . " ORDER BY apply_amount_usd DESC LIMIT 10";
        $list = $r->getRows($sql);
        return $list;
    }

    public function getLoanContract($pageNumber, $pageSize, $filter = array())
    {
        $r = new ormReader();
        $where = ' where c.state = ' . qstr(loanContractStateEnum::COMPLETE);
        if ($filter['obj_guid']) {
            $where .= ' and c.client_obj_goid = ' . qstr($filter['obj_guid']);
        }
        if ($filter['member_name']) {
            $where .= ' and (m.login_code like "' . $filter['member_name'] . '" or m.display_name like "' . $filter['member_name'] . '")';
        }
        $sql = "select m.login_code,m.display_name,c.*,mcc.alias from loan_contract c"
            . " left join client_member m on m.obj_guid = c.client_obj_guid"
            . " left join member_credit_category mcc on mcc.uid = c.member_credit_category_id"
            . " $where order by c.uid desc";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $list = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "list" => $list,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public static function getLoanOverdueList($pageNumber, $pageSize, $filter = array())
    {
        $r = new ormReader();
        $sql = "SELECT lis.contract_id,SUM(lis.amount) amount,COUNT(lis.uid) num ,MIN(lis.receivable_date) receivable_date"
            . " FROM loan_installment_scheme lis WHERE lis.state = 0 AND lis.receivable_date < '" . date('Y-m-d') . "' GROUP BY lis.contract_id ORDER BY lis.receivable_date DESC";
        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $contract_ids = array_column($rows, 'contract_id');
            $contract_id_str = '(' . implode(',', $contract_ids) . ')';
            $sql = "SELECT lc.uid,lc.virtual_contract_sn contract_sn,cm.display_name,cm.phone_id"
                . " FROM loan_contract lc LEFT JOIN loan_account la ON lc.account_id = la.uid"
                . " LEFT JOIN client_member cm ON la.obj_guid = cm.obj_guid"
                . " WHERE lc.uid IN $contract_id_str";
            $arr = $r->getRows($sql);
            $arr = resetArrayKey($arr, 'uid');
            foreach ($rows as $key => $row) {
                $contract_id = $row['contract_id'];
                $row = array_merge($row, $arr[$contract_id]);
                $rows[$key] = $row;
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

    public static function getLoanPenalty($pageNumber, $pageSize)
    {
        $r = new ormReader();
        $sql = "select p.*,c.virtual_contract_sn contract_sn,lis.receivable_date,m.login_code,m.display_name,m.phone_id from loan_penalty p"
            . " left join loan_contract c on p.contract_id = c.uid"
            . " left join loan_installment_scheme lis on p.scheme_id = lis.uid"
            . " left join loan_account a on c.account_id = a.uid"
            . " left join client_member m on m.obj_guid = a.obj_guid"
            . " where p.state = 0";

        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 50;
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

    public static function getLoanRepay($pageNumber, $pageSize, $filter = array())
    {
        $r = new ormReader();
        $sql = "select lr.*,c.virtual_contract_sn,lis.scheme_name,m.display_name from loan_repayment lr "
            . " left join loan_contract c on lr.contract_id = c.uid"
            . " left join loan_installment_scheme lis on lr.scheme_id = lis.uid"
            . " left join loan_account a on c.account_id = a.uid"
            . " left join client_member m on m.obj_guid = a.obj_guid"
            . " where lr.state = 100";
        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 50;
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

    public static function getDepositOverview()
    {
        $r = new ormReader();
        $sql = "select biz_code,currency,sum(amount) amount from biz_member_deposit where state = " . qstr(assetRequestWithdrawStateEnum::DONE) . " group by currency,biz_code";
        $list = $r->getRows($sql);
        $data = array();
        $total = array();
        foreach ($list as $k => $v) {
            $data[$v['biz_code']]['amount'][$v['currency']] = $v['amount'];
            $total[$v['currency']] += $v['amount'];
            if ($v['currency'] != currencyEnum::USD) {
                $rate = global_settingClass::getCurrencyRateBetween($v['currency'], currencyEnum::USD);
                $amount = round($v['amount'] * $rate, 2);
            } else {
                $amount = $v['amount'];
            }
            $data[$v['biz_code']]['amount']['total_to_usd'] += $amount;
            $total['total_to_usd'] += $amount;
        }
        return array('list' => $data, 'total' => $total);
    }

    public static function getDepositLog($pageNumber, $pageSize, $filter = array())
    {
        $r = new ormReader();
        $sql = "select b.*,m.login_code,m.obj_guid from biz_member_deposit b left join client_member m on m.uid = b.member_id where b.state = " . qstr(assetRequestWithdrawStateEnum::DONE);
        if ($filter['code']) {
            $sql .= " and biz_code = " . qstr($filter['code']);
        }
        if ($filter['currency']) {
            $sql .= " and currency = " . qstr($filter['currency']);
        }
        $sql .= " order by b.uid desc";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $list = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "list" => $list,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public static function getWithdrawOverview()
    {
        $r = new ormReader();
        $sql = "select biz_code,currency,sum(amount) amount from biz_member_withdraw where state = " . qstr(assetRequestWithdrawStateEnum::DONE) . " group by currency,biz_code";
        $list = $r->getRows($sql);
        $data = array();
        $total = array();
        foreach ($list as $k => $v) {
            $data[$v['biz_code']]['amount'][$v['currency']] = $v['amount'];
            $total[$v['currency']] += $v['amount'];
            if ($v['currency'] != currencyEnum::USD) {
                $rate = global_settingClass::getCurrencyRateBetween($v['currency'], currencyEnum::USD);
                $amount = round($v['amount'] * $rate, 2);
            } else {
                $amount = $v['amount'];
            }
            $data[$v['biz_code']]['amount']['total_to_usd'] += $amount;
            $total['total_to_usd'] += $amount;
        }
        return array('list' => $data, 'total' => $total);
    }

    public static function getWithdrawLog($pageNumber, $pageSize, $filter = array())
    {
        $r = new ormReader();
        $sql = "select b.*,m.login_code,m.obj_guid from biz_member_withdraw b left join client_member m on m.uid = b.member_id where b.state = " . qstr(assetRequestWithdrawStateEnum::DONE);
        if ($filter['code']) {
            $sql .= " and biz_code = " . qstr($filter['code']);
        }
        if ($filter['currency']) {
            $sql .= " and currency = " . qstr($filter['currency']);
        }
        $sql .= " order by b.uid desc";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $list = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "list" => $list,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public static function getMemberTransferOverview()
    {
        $r = new ormReader();
        $sql = "select currency,biz_code,sum(amount) amount from biz_member_transfer where state = 100 group by currency,biz_code";
        $list = $r->getRows($sql);
        $data = array();
        $total = array();
        foreach ($list as $k => $v) {
            $data[$v['biz_code']]['amount'][$v['currency']] = $v['amount'];
            $total[$v['currency']] += $v['amount'];
            if ($v['currency'] != currencyEnum::USD) {
                $rate = global_settingClass::getCurrencyRateBetween($v['currency'], currencyEnum::USD);
                $amount = round($v['amount'] * $rate, 2);
            } else {
                $amount = $v['amount'];
            }
            $data[$v['biz_code']]['amount']['total_to_usd'] += $amount;
            $total['total_to_usd'] += $amount;
        }
        return array('list' => $data, 'total' => $total);
    }

    public static function getObjTransferOverview()
    {
        $r = new ormReader();
        $sql = "select currency,biz_code,sum(amount) amount from biz_obj_transfer where state = 100 group by currency,biz_code";
        $list = $r->getRows($sql);
        $data = array();
        $total = array();
        foreach ($list as $k => $v) {
            $data[$v['biz_code']]['amount'][$v['currency']] = $v['amount'];
            $total[$v['currency']] += $v['amount'];
            if ($v['currency'] != currencyEnum::USD) {
                $rate = global_settingClass::getCurrencyRateBetween($v['currency'], currencyEnum::USD);
                $amount = round($v['amount'] * $rate, 2);
            } else {
                $amount = $v['amount'];
            }
            $data[$v['biz_code']]['amount']['total_to_usd'] += $amount;
            $total['total_to_usd'] += $amount;
        }
        return array('list' => $data, 'total' => $total);
    }

    public static function getTransferLog($type, $pageNumber, $pageSize, $filter)
    {
        $r = new ormReader();
        if ($type == 'member') {
            $sql = "select t.*,m.login_code,m.obj_guid from biz_member_transfer t left join client_member m on m.uid = t.member_id where t.state = " . qstr(assetRequestWithdrawStateEnum::DONE);
        } else {
            $sql = "select t.*,u.user_name from biz_obj_transfer t left join um_user u on u.obj_guid = t.receiver_obj_guid  where t.state = " . qstr(assetRequestWithdrawStateEnum::DONE);
        }

        if ($filter['code']) {
            $sql .= " and t.biz_code = " . qstr($filter['code']);
        }
        if ($filter['currency']) {
            $sql .= " and t.currency = " . qstr($filter['currency']);
        }
        $sql .= " order by t.uid desc";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $list = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "list" => $list,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize,
            "type" => $type
        );
    }

    public static function getExchangeOverview()
    {
        $r = new ormReader();
        $sql = "select obj_type,sum(amount) amount,sum(exchange_amount) exchange_amount, from_currency,to_currency from biz_obj_exchange where state = 100 group by obj_type,from_currency,to_currency";
        $list = $r->getRows($sql);
        $type = (new objGuidTypeEnum())->toArray();
        $type_arr = array();
        foreach ($type as $k => $v) {
            $type_arr[$v] = $k;
        }
        foreach ($list as $k => $v) {
            /*$type = '';
            switch($v['obj_type']){
                case objGuidTypeEnum::SYSTEM:
                    $type = 'System';
                    break;
                case objGuidTypeEnum::CLIENT_MEMBER:
                    $type = 'Member';
                    break;
                case objGuidTypeEnum::UM_USER:
                    $type = 'User';
                    break;
                case objGuidTypeEnum::SITE_BRANCH:
                    $type = 'Branch';
                    break;
                case objGuidTypeEnum::PARTNER:
                    $type = 'Partner';
                    break;
                case objGuidTypeEnum::BANK_ACCOUNT:
                    $type = 'Bank';
                    break;
                case objGuidTypeEnum::SHORT_LOAN:
                    $type = 'Short Loan';
                    break;
                case objGuidTypeEnum::LONG_LOAN:
                    $type = 'Long Loan';
                    break;
                case objGuidTypeEnum::SHORT_DEPOSIT:
                    $type = 'Short Deposit';
                    break;
                case objGuidTypeEnum::LONG_DEPOSIT:
                    $type = 'Long Deposit';
                    break;
                case objGuidTypeEnum::GL_ACCOUNT:
                    $type = 'gl';
                    break;
                default:
            }*/
            $list[$k]['item'] = $type_arr[$v['obj_type']];
        }
        return $list;
    }

    public static function getExchangeLog($pageNumber, $pageSize, $filter)
    {
        $r = new ormReader();
        $sql = "select * from biz_obj_exchange where state = 100";
        if ($filter['obj_type'] > -1) {
            $sql .= " and obj_type = " . qstr($filter['obj_type']);
        }
        $sql .= " order by uid desc";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $list = $data->rows;
        $type = (new objGuidTypeEnum())->toArray();
        $type_arr = array();
        foreach ($type as $k => $v) {
            $type_arr[$v] = $k;
        }
        foreach ($list as $k => $v) {
            $list[$k]['item'] = $type_arr[$v['obj_type']];
        }
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "list" => $list,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public static function getSavingsUsdTop100($currency)
    {
        $r = new ormReader();
        $sql = "select s.* from (select m.obj_guid,m.login_code,m.display_name,m.phone_id,b.branch_code,b.branch_name,a.currency,sum(a.balance - a.outstanding) balance from client_member m left join passbook p on m.obj_guid = p.obj_guid left join passbook_account a on p.uid = a.book_id left join site_branch b on m.branch_id = b.uid where currency = " . qstr($currency) . " group by m.uid) s order by s.balance desc limit 100";
        $list = $r->getRows($sql);
        return $list;
    }

}