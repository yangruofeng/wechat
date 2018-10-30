<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class client_cbcModel extends tableModelBase
{
    public function  __construct()
    {
        parent::__construct('client_cbc');
    }

    public function editClientCbc($param)
    {
        $client_id = intval($param['client_id']);
        $client_type=intval($param['client_type']);
        $all_previous_enquiries = intval($param['all_previous_enquiries']);
        $enquiries_for_previous_30_days = intval($param['enquiries_for_previous_30_days']);
        $earliest_loan_issue_date = trim($param['earliest_loan_issue_date']);
        $normal_accounts = intval($param['normal_accounts']);
        $delinquent_accounts = intval($param['delinquent_accounts']);
        $closed_accounts = intval($param['closed_accounts']);
        $reject_accounts = intval($param['reject_accounts']);
        $write_off_accounts = intval($param['write_off_accounts']);
        $total_limits = round($param['total_limits'], 2);
        $total_liabilities = round($param['total_liabilities'], 2);
        $total_limits_khr = round($param['total_limits_khr'], 2);
        $total_liabilities_khr = round($param['total_liabilities_khr'], 2);
        $total_limits_thb = round($param['total_limits_thb'], 2);
        $total_liabilities_thb = round($param['total_liabilities_thb'], 2);
        $guaranteed_normal_accounts = intval($param['guaranteed_normal_accounts']);
        $guaranteed_delinquent_accounts = intval($param['guaranteed_delinquent_accounts']);
        $guaranteed_closed_accounts = intval($param['guaranteed_closed_accounts']);
        $guaranteed_reject_accounts = intval($param['guaranteed_reject_accounts']);
        $guaranteed_write_off_accounts = intval($param['guaranteed_write_off_accounts']);
        $guaranteed_total_limits = round($param['guaranteed_total_limits'], 2);
        $guaranteed_total_liabilities = round($param['guaranteed_total_liabilities'], 2);
        $guaranteed_total_limits_khr = round($param['guaranteed_total_limits_khr'], 2);
        $guaranteed_total_liabilities_khr = round($param['guaranteed_total_liabilities_khr'], 2);
        $guaranteed_total_limits_thb = round($param['guaranteed_total_limits_thb'], 2);
        $guaranteed_total_liabilities_thb = round($param['guaranteed_total_liabilities_thb'], 2);
        $creator_id = intval($param['creator_id']);
        $creator_name = trim($param['creator_name']);
        $pay_to_cbc=intval($param['pay_to_cbc']);
        $cbc_file = $param['cbc_file'];

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $this->getRow(array('client_id' => $client_id,"client_type"=>$client_type, 'state' => 1));
            if ($row) {

                // 如果没有上传就取上次的
                if( !$cbc_file ){
                    $cbc_file =  $row['cbc_file'];
                }

                $row->state = 10;
                $row->update_time = Now();
                $rt_1 = $row->update();
                if (!$rt_1->STS) {
                    $conn->rollback();
                    return new result(false, $rt_1->MSG);
                }
            }



            $row = $this->newRow();
            $row->client_id = $client_id;
            $row->client_type=$client_type;
            $row->all_previous_enquiries = $all_previous_enquiries;
            $row->enquiries_for_previous_30_days = $enquiries_for_previous_30_days;
            if($earliest_loan_issue_date){
                $row->earliest_loan_issue_date = $earliest_loan_issue_date;
            }
            $row->normal_accounts = $normal_accounts;
            $row->delinquent_accounts = $delinquent_accounts;
            $row->closed_accounts = $closed_accounts;
            $row->reject_accounts = $reject_accounts;
            $row->write_off_accounts = $write_off_accounts;
            $row->total_limits = $total_limits;
            $row->total_liabilities = $total_liabilities;
            $row->total_limits_khr = $total_limits_khr;
            $row->total_liabilities_khr = $total_liabilities_khr;
            $row->total_limits_thb = $total_limits_thb;
            $row->total_liabilities_thb = $total_liabilities_thb;
            $row->guaranteed_normal_accounts = $guaranteed_normal_accounts;
            $row->guaranteed_delinquent_accounts = $guaranteed_delinquent_accounts;
            $row->guaranteed_closed_accounts = $guaranteed_closed_accounts;
            $row->guaranteed_reject_accounts = $guaranteed_reject_accounts;
            $row->guaranteed_write_off_accounts = $guaranteed_write_off_accounts;
            $row->guaranteed_total_limits = $guaranteed_total_limits;
            $row->guaranteed_total_liabilities = $guaranteed_total_liabilities;
            $row->guaranteed_total_limits_khr = $guaranteed_total_limits_khr;
            $row->guaranteed_total_liabilities_khr = $guaranteed_total_liabilities_khr;
            $row->guaranteed_total_limits_thb = $guaranteed_total_limits_thb;
            $row->guaranteed_total_liabilities_thb = $guaranteed_total_liabilities_thb;
            $row->state = 1;
            $row->creator_id = $creator_id;
            $row->creator_name = $creator_name;
            $row->create_time = Now();
            $row->pay_to_cbc=$pay_to_cbc;
            $row->pay_to_srs=round($param['pay_to_srs'],2);
            $row->cbc_file = $cbc_file;
            $rt_2 = $row->insert();
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, $rt_2->MSG);
            }

            $conn->submitTransaction();
            return new result(true, "Edit Successful!");
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }


    public function getClientCbcIds()
    {
        $sql = "select uid,member_id from client_cbc group by member_id";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    public function getClientCbcChecked($search_text, $pageNumber, $pageSize)
    {
        $sql = "SELECT c.*,m.obj_guid,m.login_code,m.display_name,m.phone_country,m.phone_number,m.phone_id,m.work_type FROM (SELECT * FROM client_cbc ORDER BY uid DESC) c left join client_member m on c.member_id = m.uid ";
        if ($search_text) {
            $sql .= " where m.obj_guid = '" . qstr2($search_text) . "' OR m.display_name like '%" . qstr2($search_text) . "%' OR login_code like '%". qstr2($search_text) ."%' OR m.phone_id like '" . qstr2($search_text) . "' OR  m.login_code like '" . qstr2($search_text) . "'";
        }
        $sql .= " GROUP BY c.member_id ORDER BY c.uid DESC";
        $data = $this->reader->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count ?: 0;
        $pageTotal = $data->pageCount ?: 0;
        return new result(true, null, array(
            'list' => $rows,
            'total' => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize
        ));
    }

    public function getCbcByMemberId($mid)
    {
        $info = $this->find(array('member_id' => $mid, 'state' => 1));
        return $info;
    }

    public function getCbcListByMemberId($mid, $pageNumber, $pageSize)
    {
        $sql = "select c.*,m.obj_guid,m.login_code,m.phone_id,m.work_type from client_cbc c left join client_member m on c.member_id = m.uid where c.member_id = '$mid' order by c.uid desc";
        $data = $this->reader->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count ?: 0;
        $pageTotal = $data->pageCount ?: 0;
        return new result(true, null, array(
            'list' => $rows,
            'total' => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize
        ));
    }

    public function getCbcDetailById($uid)
    {
        $info = $this->find(array('uid' => $uid));
        return $info;
    }

    public function getClientLatestCbcDetail($client_id,$client_type=0){
        $sql = "select c.*,m.login_code,m.phone_id from client_cbc c left join client_member m on c.client_id = m.uid where c.client_id = '$client_id' and c.client_type='$client_type' order by uid desc limit 1";
        $info = $this->reader->getRow($sql);
        return $info;
    }

}
