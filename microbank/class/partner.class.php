<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/11/7
 * Time: 9:35
 */
class partnerClass
{

    /**
     * 添加partner
     * @param $param
     * @return result
     */
    public function addPartner($param)
    {
        $partner_code = trim($param['partner_code']);
        $partner_name = trim($param['partner_name']);
        $creator_id = intval($param['creator_id']);
        $creator_name = trim($param['creator_name']);
        $is_active = intval($param['is_active']);

        if (!$partner_code || !$partner_name) {
            return new result(false, 'Param Error!');
        }

        $m_partner = M('partner');
        $chk_code = $m_partner->find(array('partner_code' => $partner_code));
        if ($chk_code) {
            return new result(false, 'Code repeat!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $m_partner->newRow();
            $row->partner_code = $partner_code;
            $row->partner_name = $partner_name;
            $row->is_active = $is_active;
            $row->creator_id = $creator_id;
            $row->creator_name = $creator_name;
            $row->create_time = Now();
            $rt_1 = $row->insert();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Add Failed!' . $rt_1->MSG);
            }

            $obj_guid = generateGuid($rt_1->AUTO_ID, objGuidTypeEnum::PARTNER);
            $row->obj_guid = $obj_guid;
            $rt_2 = $row->update();
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Add Failed!' . $rt_2->MSG);
            }

            $m_partner_trx_api = M('partner_trx_api');
            $currency_list = (new currencyEnum())->Dictionary();
            foreach ($currency_list as $key => $currency) {
                $trx_amount = round($param[$key], 2);
                $row = $m_partner_trx_api->newRow();
                $row->partner_id = $rt_1->AUTO_ID;
                $row->obj_guid = $obj_guid;
                $row->trx_time = Now();
                $row->remark = 'init';
                $row->currency = $key;
                $row->trx_amount = $trx_amount;
                $row->trx_type = 'init';
                $row->trx_flag = 1;
                $row->is_manual = 1;
                $row->api_state = 100;
                $row->operator_id = 0;
                $row->operator_name = $creator_id;
                $row->creator_id = $creator_id;
                $row->creator_name = $creator_name;
                $row->create_time = Now();
                $rt_3 = $row->insert();
                if (!$rt_3->STS) {
                    $conn->rollback();
                    return new result(false, 'Add Failed!' . $rt_3->MSG);
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Add Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    public function editPartner($param)
    {
        $uid = intval($param['uid']);
        $partner_code = trim($param['partner_code']);
        $partner_name = trim($param['partner_name']);
        $is_active = intval($param['is_active']);

        if (!$partner_code || !$partner_name) {
            return new result(false, 'Param Error!');
        }

        $m_partner = M('partner');
        $chk_code = $m_partner->find(array('partner_code' => $partner_code, 'uid' => array('neq', $uid)));
        if ($chk_code) {
            return new result(false, 'Code repeat!');
        }

        $row = $m_partner->getRow(array('uid' => $uid));
        $row->partner_code = $partner_code;
        $row->partner_name = $partner_name;
        $row->is_active = $is_active;
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Edit Successful!');
        } else {
            return new result(false, 'Edit Failed!');
        }
    }

    public function deletePartner($uid)
    {
        $m_partner = M('partner');
        $row = $m_partner->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }

        $m_partner_trx_api = M('partner_trx_api');
        $chk_trx = $m_partner_trx_api->find(array('partner_id' => $uid, 'trx_type' => array('neq', 'init')));
        if ($chk_trx) {
            return new result(false, 'There are transaction records that cannot be deleted!');
        }
        $rt = $row->delete();
        if ($rt->STS) {
            return new result(true, 'Delete Successful!');
        } else {
            return new result(false, 'Delete Failed!');
        }
    }

    /**
     * 获取partner信息
     * @param $uid
     * @return array|bool|mixed|null
     */
    public function getPartnerInfo($uid)
    {
        $uid = intval($uid);
        $m_partner = M('partner');
        $partner_info = $m_partner->find(array('uid' => $uid));
        if (!$partner_info) {
            return array();
        }
        $m_partner_trx_api = M('partner_trx_api');
        $init_balance = $m_partner_trx_api->field('currency,trx_amount')->select(array('partner_id' => $uid, 'trx_type' => 'init'));
        foreach ($init_balance as $val) {
            $partner_info['init_balance'][$val['currency']] = $val['trx_amount'];
        }

        return $partner_info;
    }

    /**
     * 获取partner list
     * @return result
     */
    public function getPartnerList($condition = array())
    {
        $m_partner = M('partner');
        $r = new ormReader();
        $condition = array_merge(array('is_active' => 1), $condition);
        $partner_list = $m_partner->select($condition);
        $currency_list = (new currencyEnum())->Dictionary();
        foreach ($partner_list as $k_1 => $partner) {
            $book=passbookClass::getPartnerPassbook($partner['uid']);
            $arr_account=$book->getAccountAllCurrencyDetail();
            foreach ($currency_list as $k_2 => $currency) {
                $partner_list[$k_1]['book_account'][$k_2] = $arr_account[$currency];
            }
        }
        return $partner_list;
    }

    /**
     * 添加手工帐
     * @param $p
     * @return result
     */
    public function addManual($p)
    {
        $partner_id = intval($p['partner_id']);
        $currency = trim($p['currency']);
        $amount = round($p['amount'], 2);
        $trx_type = $p['trx_type'];
//        $operator_id = intval($p['operator_id']);
        $operator_name = trim($p['operator_name']);
        $trx_time = $p['trx_time'] ?: Now();
        $remark = trim($p['remark']);
        $api_state = $p['api_state'] ?: 100;
        $creator_id = $p['creator_id'];
        $creator_name = $p['creator_name'];

        if ($trx_type == 'deposit' || $trx_type == 'plus') {
            $trx_flag = 1;
        } else {
            $trx_flag = -1;
        }
        if (empty($trx_time)) {
            $trx_time = date('Y-m-d');
        }

        $m_partner = M('partner');
        $partner = $m_partner->getRow($partner_id);
        if (!$partner) {
            return new result(false, 'Invalid Id!');
        }

        $m_partner_trx_api = M('partner_trx_api');
        $row = $m_partner_trx_api->newRow();
        $row->partner_id = $partner_id;
        $row->obj_guid = $partner->obj_guid;
        $row->trx_time = $trx_time;
        $row->remark = $remark;
        $row->currency = $currency;
        $row->trx_amount = $amount;
        $row->trx_type = $trx_type;
        $row->trx_flag = $trx_flag;
        $row->is_manual = 1;
        $row->api_state = $api_state;
        $row->operator_id = 0;
        $row->operator_name = $operator_name;
        $row->creator_id = $creator_id;
        $row->creator_name = $creator_name;
        $row->create_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Add Successful!');
        } else {
            return new result(false, 'Add Failure!');
        }
    }

    /**
     * 编辑手工帐
     * @param $p
     * @return result
     */
    public function editManual($p)
    {
        $uid = intval($p['trace_id']);
        $partner_id = intval($p['partner_id']);
        $currency = trim($p['currency']);
        $amount = round($p['amount'], 2);
        $trx_type = $p['trx_type'];
        $operator_id = intval($p['operator_id']);
        $operator_name = $p['operator_name'];
        $trx_time = $p['trx_time'];
        $remark = trim($p['remark']);
        $api_state = $p['api_state'] ?: 100;

        if ($trx_type == 'deposit' || $trx_type == 'plus') {
            $trx_flag = 1;
        } else {
            $trx_flag = -1;
        }
        if (empty($trx_time)) {
            $trx_time = date('Y-m-d');
        }
        $m_partner_trx_api = M('partner_trx_api');
        $row = $m_partner_trx_api->getRow(array('uid' => $uid));
        $row->partner_id = $partner_id;
        $row->trx_time = $trx_time;
        $row->remark = $remark;
        $row->currency = $currency;
        $row->trx_amount = $amount;
        $row->trx_type = $trx_type;
        $row->trx_flag = $trx_flag;
        $row->api_state = $api_state;
        $row->operator_id = $operator_id;
        $row->operator_name = $operator_name;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Update Successful!');
        } else {
            return new result(false, 'Update Failure!');
        }
    }

    /**
     * 改变账目状态
     * @param $p
     * @return result
     */
    public function changeTraceState($p)
    {
        $uid = intval($p['uid']);
        $state = $p['state'];
        $m_partner_trx_api = M('partner_trx_api');
        $row = $m_partner_trx_api->getRow(array('uid' => $uid));
        $row->api_state = $state;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Change Successful!');
        } else {
            return new result(false, 'Change Failure!');
        }
    }

    /**
     * 增加查账记录
     * @param $p
     * @return result
     */
    public function addCheckTrace($p)
    {
        $uid = intval($p['uid']);
        $currency = $p['currency'];
        $operator_id = $p['operator_id'];
        $operator_name = $p['operator_name'];

        $r = new ormReader();
        $m_partner = M('partner');
        $m_partner_trace_check = M('partner_trace_check');

        $partner_info = $m_partner->getRow(array('uid' => $uid));

        $time = Now();
        $sql = "SELECT SUM(trx_flag*trx_amount) system_balance FROM partner_trx_api WHERE partner_id = $uid AND currency = '" . $currency . "' AND api_state > 10 AND trx_time <= '" . $time . "'";
        $system_balance = $r->getOne($sql);
        $check_balance = $system_balance;//接口查询

        if ($system_balance != $check_balance) {
            return new result(false, 'The accounts are not correct, please check it!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $m_partner_trace_check->newRow();
            $row->partner_id = $uid;
            $row->currency = $currency;
            $row->system_balance = $system_balance;
            $row->api_balance = $check_balance;
            $row->check_result = 'success';
            $row->check_remark = '';
            $row->check_time = $time;
            $row->operator_id = $operator_id;
            $row->operator_name = $operator_name;
            $row->create_time = $time;
            $rt = $row->insert();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Check Failure!');
            }

            $sql = "UPDATE partner_trx_api SET is_check = 1 WHERE partner_id = $uid AND currency = '" . $currency . "' AND api_state > 10 AND trx_time <= '" . $time . "'";
            $rt_1 = $r->conn->execute($sql);
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Compare Failure!');
            }
            $conn->submitTransaction();
            return new result(false, 'Compare Success!');

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    public static function getAsiaweiluyPartnerID()
    {
        return (new partnerModel())->getRow(array('partner_code' => 'ace'))->uid;
    }

    public static function getGUID($partnerId, $return_account = false)
    {
        $partner_model = new partnerModel();
        $partner_info = $partner_model->getRow($partnerId);
        if (!$partner_info) throw new Exception("Partner $partnerId not found");

        if (!$partner_info->obj_guid) {
            $partner_info->obj_guid = generateGuid($partner_info->uid, objGuidTypeEnum::PARTNER);
            $ret = $partner_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for partner failed - " . $ret->MSG);
            }
        }
        if ($return_account) {
            return $partner_info->toArray();
        } else {
            return $partner_info->obj_guid;
        }
    }


    /** 生成billpaycode
     * @param $partner_code
     * @return result
     */
    public static function generateBillPayCode($partner_code)
    {
        // todo 按规则重新生成
        switch ($partner_code) {
            case partnerEnum::ACE:
                $bill_code = '1' . date('Ymd') . mt_rand(10, 99);
                break;
            default :
                return new result(false, 'Unknown partner', null, errorCodesEnum::INVALID_PARTNER);
        }
        return new result(true, 'success', $bill_code);
    }

    public static function getPartnerApiLogData($partner_id, $pageNumber, $pageSize, $filters = array()){
        $partner_id = intval($partner_id);
        $where = "partner_id = $partner_id";

        if ($filters['start_date']) {
            $start_date = system_toolClass::getFormatStartDate($filters['start_date']);
            $where .= " AND trx_time >= '$start_date' ";
        }
        if ($filters['end_date']) {
            $end_date = system_toolClass::getFormatEndDate($filters['end_date']);
            $where .= " AND trx_time <= '$end_date' ";
        }
        $r = new ormReader();
        $sql = "select api_trx_id,api_parameter,trx_amount,trx_time from partner_trx_api where $where";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "data" => $data->rows,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }


    public static function getPartnerLimitGroupByPartner()
    {
        $r = new ormReader();
        $sql = "select * from partner_limit_setting order by partner_code,biz_type";
        $list = $r->getRows($sql);
        $format_list = array();
        foreach( $list as $v ){
           if( $format_list[$v['partner_code']] ){
               $format_list[$v['partner_code']]['limit'][] = $v;
           }else{
               $format_list[$v['partner_code']] = array(
                   'partner_code' => $v['partner_code'],
                   'partner_name' => $v['partner_name'],
                   'limit' => array($v)
               );
           }
        }
        return $format_list;
    }

    public static function getPartnerBizLimit($partner_code,$biz_type)
    {
        $m = new partner_limit_settingModel();
        $row = $m->find(array(
            'partner_code' => $partner_code,
            'biz_type' => $biz_type
        ));
        return $row;
    }
}
