<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 11:20
 */
class site_bankModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('site_bank');
    }

    /**
     * 添加收款账号
     * @param $p
     * @return result
     */
    public function addBank($p)
    {
        $bank_uid = intval($p['bank_uid']);
        $currency = $p['currency'];
        $bank_account_no = trim($p['bank_account_no']);
        $bank_account_name = trim($p['bank_account_name']);
        $bank_account_phone = trim($p['bank_account_phone']);
        $account_state = intval($p['account_state']);
        $allow_client_deposit = intval($p['allow_client_deposit']);
        $branch_id = intval($p['branch_id']);
        $creator_id = intval($p['creator_id']);
        $creator_name = trim($p['creator_name']);
        $is_allow_billpay = intval($p['is_allow_billpay'])?1:0;

        $m_common_bank_lists = M('common_bank_lists');
        $bank_info = $m_common_bank_lists->find(array('uid' => $bank_uid));
        if( !$bank_info ){
            return new result(false,'Not found bank info:'.$bank_uid,null,errorCodesEnum::NO_DATA);
        }
        $bank_code = $bank_info['bank_code'];
        $bank_currency = $bank_info['currency'];
        $bank_name = $bank_info['bank_name'];

        $chk_account = $this->find(array(
            'bank_code' => $bank_code,
            'bank_account_no' => $bank_account_no
        ));
        if ($chk_account) {
            // todo 先暂时取消，用于live处理balance
            //return new result(false, 'The bank already exists!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $this->newRow();
            $row->bank_code = $bank_code;
            $row->bank_name = $bank_name;
            $row->currency = $currency;
            $row->bank_account_no = $bank_account_no;
            $row->bank_account_name = $bank_account_name;
            $row->bank_account_phone = $bank_account_phone;
            $row->account_state = $account_state;
            $row->allow_client_deposit = $allow_client_deposit;
            $row->branch_id = $branch_id;
            $row->creator_id = $creator_id;
            $row->creator_name = $creator_name;
            $row->create_time = Now();
            $row->is_allow_billpay = $is_allow_billpay;
            $rt = $row->insert();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Add failed!' . $rt->MSG);
            }

            $row->obj_guid = generateGuid($rt->AUTO_ID, objGuidTypeEnum::BANK_ACCOUNT);
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Add failed--' . $rt_1->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Add successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 编辑收款账号
     * @param $p
     * @return result
     */
    public function editBank($p)
    {
        $uid = intval($p['uid']);
        $bank_account_no = trim($p['bank_account_no']);
        $bank_account_name = trim($p['bank_account_name']);
        $bank_account_phone = trim($p['bank_account_phone']);
        $account_state = intval($p['account_state']);
        $allow_client_deposit = intval($p['allow_client_deposit']);
        $is_allow_billpay = intval($p['is_allow_billpay'])?1:0;

        $row = $this->getRow($uid);
        $chk_account = $this->find(array('bank_code' => $row['bank_code'], 'bank_account_no' => $bank_account_no, 'uid' => array('neq', $uid)));
        if ($chk_account) {
            // todo 暂时取消验证，现在模式有重复
            //return new result(false, 'The bank already exists!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $this->getRow($uid);
            $row->bank_account_no = $bank_account_no;
            $row->bank_account_name = $bank_account_name;
            $row->bank_account_phone = $bank_account_phone;
            $row->account_state = $account_state;
            $row->allow_client_deposit = $allow_client_deposit;
            $row->is_allow_billpay = $is_allow_billpay;
            $row->update_time = Now();
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Update failed!' . $rt->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Update successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * @param $uid
     * @return result
     * @throws Exception
     */
    public function deleteBank($uid)
    {
        $row = $this->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }
        $row->account_state = -1;
        $row->update_time = Now();
        $rt = $row->update();
        if (!$rt->STS) {
            return new result(false, 'Remove failed!');
        } else {
            return new result(true, 'Remove successful!');
        }
    }

    public function searchBankListByFreeText($searchText, $pageNumber, $pageSize, $filter = array())
    {
        $sql = "SELECT sbk.*, sb.branch_name FROM site_bank sbk LEFT JOIN site_branch sb ON sbk.branch_id = sb.uid WHERE sbk.account_state >= 0 ";
        if ($searchText) {
            $sql .= " AND (sbk.bank_account_name LIKE '%" . qstr2($searchText) . "%' OR sbk.bank_account_no = " . qstr($searchText) . ")";
        }

        if (intval($filter['branch_id'])) {
            $sql .= " AND sbk.branch_id = " . intval($filter['branch_id']);
        }

        if ($filter['bank_code']) {
            $sql .= " AND sbk.bank_code = " . qstr($filter['bank_code']);
        }

        if ($filter['type'] == 'branch') {
            $sql .= " AND sbk.branch_id != 0";
        }

        if ($filter['type'] == 'hq') {
            $sql .= " AND sbk.branch_id = 0";
        }

        $sql .= " ORDER BY sbk.uid DESC";
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

    public function searchBankListGroupByBranch($searchText, $pageNumber, $pageSize, $filter = array())
    {
        $sql = "SELECT sbk.branch_id, sb.branch_name FROM site_bank sbk INNER JOIN site_branch sb ON sbk.branch_id = sb.uid WHERE sbk.account_state >= 0 ";
        if ($searchText) {
            $sql .= " AND (sbk.bank_account_name LIKE '%" . qstr2($searchText) . "%' OR sbk.bank_account_no = " . qstr($searchText) . ")";
        }

        if ($filter['bank_code']) {
            $sql .= " AND sbk.bank_code = " . qstr($filter['bank_code']);
        }

        if (intval($filter['branch_id'])) {
            $sql .= " AND sbk.branch_id = " . intval($filter['branch_id']);
        }

        if ($filter['type'] == 'branch') {
            $sql .= " AND sbk.branch_id != 0";
        }

        if ($filter['type'] == 'hq') {
            $sql .= " AND sbk.branch_id = 0";
        }

        $sql .= " GROUP BY sbk.branch_id ORDER BY sbk.branch_id ASC";
        $data = $this->reader->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $branch_ids = array_column($rows, 'branch_id');
            $branch_id_str = '(' . implode(',', $branch_ids) . ')';
            $sql = "SELECT * FROM site_bank WHERE account_state >= 0 AND branch_id IN $branch_id_str";

            if ($searchText) {
                $sql .= " AND (bank_account_name LIKE '%" . qstr2($searchText) . "%' OR bank_account_no = " . qstr($searchText) . ")";
            }

            if ($filter['bank_code']) {
                $sql .= " AND bank_code = " . qstr($filter['bank_code']);
            }
            $bank_list = $this->reader->getRows($sql);
            foreach ($bank_list as $key => $bank) {
                $object_sys_bank_class = new objectSysBankClass($bank['uid']);
                $balance = $object_sys_bank_class->getPassbookCurrencyBalance();
                $bank['balance'] = $balance[$bank['currency']];
                $bank_list[$key] = $bank;
            }

            $rows_new = array();
            foreach ($rows as $row) {
                $rows_new[$row['branch_id']] = $row;
            }
            foreach ($bank_list as $bank) {
                $rows_new[$bank['branch_id']]['bank_list'][] = $bank;
            }
        }

        return new result(true, null, array(
            'rows' => $rows_new,
            'total' => $total,
            'page_total' => $pageTotal
        ));
    }

    public function getBankInfoById($uid){
        $info = $this->find(array('uid' => $uid));
        return $info;
    }
}