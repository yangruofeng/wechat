<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/11/7
 * Time: 9:35
 */
class branchClass
{
    public function __construct()
    {
    }

    /**
     * 获取分部信息
     * @param $uid
     * @return array|bool|mixed|null
     */
    public function getBranchInfo($uid)
    {
        $m_site_branch = M('site_branch');
        $branch_info = $m_site_branch->find(array('uid' => $uid));
        if (!$branch_info) {
            return array();
        }

        $m_site_branch_limit = M('site_branch_limit');
        $limit_list = $m_site_branch_limit->select(array('branch_id' => $uid));
        $limit_arr = array();
        foreach ($limit_list as $val) {
            $limit_arr[$val['limit_key']] = array(
                'max_per_day' => $val['max_per_day'],
                'max_per_time' => $val['max_per_time']
            );
        }

        $m_site_branch_images = M('site_branch_images');
        $image_list = $m_site_branch_images->select(array("branch_id" => $uid));
        $arr_img = array();
        foreach ($image_list as $item) {
            $arr_img[] = getImageUrl($item['image_url']);
        }
        $branch_info['image_list'] = $arr_img;
        $branch_info['limit_arr'] = $limit_arr;
        return $branch_info;
    }

    /**
     * 添加branch
     * @param $p
     * @return result
     */
    public function addBranch($p)
    {
        $branch_code = trim($p['branch_code']);
        $branch_name = trim($p['branch_name']);
        $address_id = intval($p['address_id']);
        $address_region = trim($p['address_region']);
        $address_detail = trim($p['address_detail']);
        $coord_x = $p['coord_x'];
        $coord_y = $p['coord_y'];
        $contact_phone = trim($p['contact_phone']);
        $manager = intval($p['manager']);
        $status = intval($p['status']);
        $is_hq = $p['is_hq'] ? 1 : 0;
        if (empty($branch_code) || empty($branch_name)) {
            return new result(false, 'Code and name cannot be empty!');
        }

        $m_site_branch = M('site_branch');
        $chk_code = $m_site_branch->find(array('branch_code' => $branch_code));
        if ($chk_code) {
            return new result(false, 'Code exists!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $m_site_branch->newRow();
            $row->branch_code = $branch_code;
            $row->branch_name = $branch_name;
            $row->address_id = $address_id;
            $row->address_region = $address_region;
            $row->address_detail = $address_detail;
            $row->coord_x = $coord_x;
            $row->coord_y = $coord_y;
            $row->contact_phone = $contact_phone;
            $row->manager = $manager;
            $row->status = $status;
            $row->creator_id = intval($p['creator_id']);
            $row->creator_name = trim($p['creator_name']);
            $row->create_time = Now();
            $row->is_hq = $is_hq;
            $rt = $row->insert();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Add failed--' . $rt->MSG);
            }

            $row->obj_guid = generateGuid($rt->AUTO_ID, objGuidTypeEnum::SITE_BRANCH);
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
     * 添加Branch 限制条件
     */
    public function addBranchLimit($p)
    {
        $branch_id = intval($p['branch_id']);
        $m_site_branch_limit = M('site_branch_limit');

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $m_site_branch_limit->delete(array('branch_id' => $branch_id));
        if (!$rt->STS) {
            $conn->rollback();
            return new result(false, $rt->MSG);
        }
        $limit_arr = array(
//            bizCodeEnum::MEMBER_WITHDRAW_TO_PARTNER => array(
//                'max_per_day' => $p[bizCodeEnum::MEMBER_WITHDRAW_TO_PARTNER]['max_per_day']
//            ),
//            bizCodeEnum::MEMBER_WITHDRAW_TO_BANK => array(
//                'max_per_day' => $p[bizCodeEnum::MEMBER_WITHDRAW_TO_BANK]['max_per_day']
//            ),
            bizCodeEnum::MEMBER_WITHDRAW_TO_CASH => array(
                'max_per_day' => $p[bizCodeEnum::MEMBER_WITHDRAW_TO_CASH]['max_per_day']
            ),
//            bizCodeEnum::MEMBER_TRANSFER_TO_MEMBER => array(
//                'max_per_day' => $p[bizCodeEnum::MEMBER_TRANSFER_TO_MEMBER]['max_per_day']
//            ),
//            bizCodeEnum::MEMBER_TRANSFER_TO_BANK => array(
//                'max_per_day' => $p[bizCodeEnum::MEMBER_TRANSFER_TO_BANK]['max_per_day']
//            ),
//            bizCodeEnum::MEMBER_DEPOSIT_BY_PARTNER => array(
//                'max_per_day' => $p[bizCodeEnum::MEMBER_DEPOSIT_BY_PARTNER]['max_per_day']
//            ),
//            bizCodeEnum::MEMBER_DEPOSIT_BY_BANK => array(
//                'max_per_day' => $p[bizCodeEnum::MEMBER_DEPOSIT_BY_BANK]['max_per_day']
//            ),
            bizCodeEnum::MEMBER_DEPOSIT_BY_CASH => array(
                'max_per_day' => $p[bizCodeEnum::MEMBER_DEPOSIT_BY_CASH]['max_per_day']
            ),
//            bizCodeEnum::TELLER_TO_BRANCH => array(
//                'max_per_day' => $p[bizCodeEnum::TELLER_TO_BRANCH]['max_per_day']
//            ),
//            bizCodeEnum::BRANCH_TO_TELLER => array(
//                'max_per_day' => $p[bizCodeEnum::BRANCH_TO_TELLER]['max_per_day']
//            ),
//            bizCodeEnum::BRANCH_TO_BANK => array(
//                'max_per_day' => $p[bizCodeEnum::BRANCH_TO_BANK]['max_per_day']
//            ),
//            bizCodeEnum::BANK_TO_BRANCH => array(
//                'max_per_day' => $p[bizCodeEnum::BANK_TO_BRANCH]['max_per_day']
//            )
        );

        foreach ($limit_arr as $key => $limit) {
            if (!is_numeric($limit['max_per_day'])) {
                continue;
            }
            $row = $m_site_branch_limit->newRow();
            $row->branch_id = $branch_id;
            $row->limit_key = $key;
            if (is_numeric($limit['max_per_day'])) {
                $row->max_per_day = intval($limit['max_per_day']);
            } else {
                $row->max_per_day = -1;
            }
            $row->creator_id = intval($p['creator_id']);
            $row->creator_name = trim($p['creator_name']);
            $row->create_time = Now();
            $rt_2 = $row->insert();
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, $rt_2->MSG);
            }
        }

        //特殊限制
        $row = $m_site_branch_limit->newRow();
        $row->branch_id = $branch_id;
        $row->limit_key = 'approve_credit_limit';
        $row->limit_value = intval($p['approve_credit_limit']);
        $row->creator_id = intval($p['creator_id']);
        $row->creator_name = trim($p['creator_name']);
        $row->create_time = Now();
        $rt_3 = $row->insert();
        if (!$rt_3->STS) {
            $conn->rollback();
            return new result(false, $rt_3->MSG);
        }

        $conn->submitTransaction();
        return new result(true, "Set Branch Limit Successful");
    }

    /**
     * 编辑branch
     * @param $p
     * @return result
     * @throws Exception
     */
    public function editBranch($p)
    {
        $uid = intval($p['uid']);
        $branch_code = trim($p['branch_code']);
        $branch_name = trim($p['branch_name']);
        $address_id = intval($p['address_id']);
        $address_region = trim($p['address_region']);
        $address_detail = trim($p['address_detail']);
        $coord_x = $p['coord_x'];
        $coord_y = $p['coord_y'];
        $contact_phone = trim($p['contact_phone']);
        $manager = intval($p['manager']);
        $status = intval($p['status']);
        $is_hq = $p['is_hq'] ? 1 : 0;
        if (empty($branch_code) || empty($branch_name)) {
            return new result(false, 'Code and name cannot be empty!');
        }
        $m_site_branch = M('site_branch');
        $row = $m_site_branch->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Id');
        }
        $chk_code = $m_site_branch->find(array('branch_code' => $branch_code, 'uid' => array('neq', $uid)));
        if ($chk_code) {
            return new result(false, 'Branch Code Exist!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row->branch_code = $branch_code;
            $row->branch_name = $branch_name;
            $row->address_id = $address_id;
            $row->address_region = $address_region;
            $row->address_detail = $address_detail;
            $row->coord_x = $coord_x;
            $row->coord_y = $coord_y;
            $row->contact_phone = $contact_phone;
            $row->manager = $manager;
            $row->status = $status;
            $row->update_time = Now();
            $row->is_hq = $is_hq;

            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Edit Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 删除branch
     * 判断是否关联
     * @param $uid
     * @return result
     */
    public function deleteBranch($uid)
    {
        $m_site_branch = M('site_branch');
        $chk_depart = M('site_depart')->find(array('branch_id' => $uid));
        if ($chk_depart) {
            return new result(false, 'The branch has departments');
        }

        $chk_bank = M('site_bank')->find(array('branch_id' => $uid));
        if ($chk_bank) {
            return new result(false, 'The branch has bean bind bank account');
        }

        $chk_user = M('client_member')->find(array('branch_id' => $uid));
        if ($chk_user) {
            return new result(false, 'The branch has client');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt = $m_site_branch->delete(array('uid' => $uid));
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed--' . $rt->MSG);
            }

            $m_site_branch_limit = M('site_branch_limit');
            $rt_1 = $m_site_branch_limit->delete(array('branch_id' => $uid));
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed--' . $rt_1->MSG);
            }
            $conn->submitTransaction();
            return new result(true, 'Delete successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    public function saveBranchImages($params)
    {
        $branch_id = intval($params['uid']);
        $save_path = fileDirsEnum::BRANCH;
        $image_arr = array();
        foreach ($_FILES as $key => $u_file) {
            if (startWith($key, 'branch_image_old')) {
                $k1 = str_replace('branch_image_old', 'branch_image_id', $key);
                $image_arr[] = $params[$k1];
            } elseif (startWith($key, 'branch_image')) {
                if (!empty($u_file)) {
                    $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                    $upload->set('save_path', null);
                    $upload->set('default_dir', $save_path);
                    $re = $upload->server2upun($key);
                    if ($re == false) {
                        return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                    }

                    $image_arr[] = $upload->img_url;
                    unset($upload);
                }
            }
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $m_image = M('site_branch_images');
        $sql = "delete from site_branch_images where branch_id = " . $branch_id;
        $del = $m_image->conn->execute($sql);
        if (!$del->STS) {
            $conn->rollback();
            return new result(false, 'Delete old image fail.', null, errorCodesEnum::DB_ERROR);
        }

        foreach ($image_arr as $key => $value) {
            if ($value['image_url']) {
                $row = $m_image->newRow();
                $row->branch_id = $branch_id;
                $row->image_url = $value;
                $row->creator_id = $params['creator_id'];
                $row->creator_name = $params['creator_name'];
                $row->create_time = Now();
                $insert = $row->insert();
                if (!$insert->STS) {
                    $conn->rollback();
                    return new result(false, 'Save image fail' . $insert->MSG);
                }
            }
        }

        $conn->submitTransaction();
        return new result(true, 'Save image successful.');
    }

    public static function getGUID($branchId, $return_account = false)
    {
        $branch_model = new site_branchModel();
        $branch_info = $branch_model->getRow($branchId);
        if (!$branch_info) throw new Exception("Branch $branchId not found");

        if (!$branch_info->obj_guid) {
            $branch_info->obj_guid = generateGuid($branch_info->uid, objGuidTypeEnum::SITE_BRANCH);
            $ret = $branch_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for branch failed - " . $ret->MSG);
            }
        }
        if ($return_account) {
            return $branch_info->toArray();
        } else {
            return $branch_info->obj_guid;
        }
    }

    /**
     * 获取分行的Cash In Vault
     * @param $branch_id
     * @return array
     */

    public static function getBranchBalance($branch_id, $require_recent = false)
    {
        $branch = new objectBranchClass($branch_id);
        $rt1 = $branch->getPassbookCurrencyBalance();
        $rt2 = $branch->getPassbookCurrencyAccountDetail();
        $arr = array_merge(array(), $rt1, $rt2);

        $currency_list = (new currencyEnum())->Dictionary();
        $data = array();
        foreach ($currency_list as $key => $currency) {
            $data['balance_' . $key] = passbookAccountClass::getBalance($arr[$key]['balance'], $arr[$key]['outstanding']);
            $data['outstanding_' . $key] = passbookAccountClass::getOutstanding($arr[$key]['balance'], $arr[$key]['outstanding']);
        }
        if ($require_recent) {
            $m_book_flow = new passbook_account_flowModel();
            $recent = array();
            foreach ($rt2 as $acct) {
                $recent[$acct['currency']] = $m_book_flow->limit(1, 10)->orderBy("uid desc")->select(array("account_id" => $acct['uid']));
            }
            $data['recent'] = $recent;
        }
        return $data;
    }


    /**
     * 获取一个分行的所有银行
     * @param $branch_id
     * @return mixed
     */
    public static function getBankList($branch_id, $require_balance = false, $require_last_transaction)
    {
        $m_bank = M('site_bank');
        $bank_list = $m_bank->select(array('branch_id' => $branch_id));
        $bank_list = resetArrayKey($bank_list, "uid");
        $m_book_flow = new passbook_account_flowModel();
        if ($require_balance) {
            foreach ($bank_list as $k => $item) {
                $bank_currency = $item['currency'];
                $bankPassbook = new objectSysBankClass($k);
                $account_list = $bankPassbook->getPassbookCurrencyAccountDetail();
                $balance = array();
                foreach ($account_list as $acct) {
                    $balance[$acct['currency']] = $acct['balance'];
                    if ($acct['currency'] == $bank_currency) {
                        if ($require_last_transaction) {
                            $last = $m_book_flow->orderBy("uid desc")->find(array("account_id" => $acct['uid']));
                            $bank_list[$k]['last'] = $last;
                        }
                    }
                }
                $bank_list[$k]['balance'] = $balance;
            }
        }
        return $bank_list;
    }

    /**
     * 更改分行状态
     * @param $branch_id
     * @param $status
     * @return mixed
     */
    public static function editBranchStatus($branch_id, $status)
    {
        $m_site_branch = M('site_branch');
        $row = $m_site_branch->getRow(array('uid' => $branch_id));
        if (!$row) {
            return new result(false, 'Invalid Id');
        }

        $row->status = $status;
        $row->update_time = Now();
        $rt = $row->update();
        if (!$rt->STS) {
            return new result(false, 'Edit failed--' . $rt->MSG);
        }
        return new result(true, 'Edit Successful!');
    }

    public function editBranchCredit($param)
    {
        $branch_id = intval($param['branch_id']);
        $credit = round($param['credit'], 2);
        $remark = trim($param['remark']);
        $operator_id = intval($param['operator_id']);
        $operator_name = trim($param['operator_name']);
        $m_site_branch = M('site_branch');
        $row = $m_site_branch->getRow($branch_id);
        if (!$row) {
            return new result(false, 'Invalid Id.');
        }
        $before_credit = $row->credit;

        if ($before_credit == $credit) {
            return new result(false, 'Value no change.');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row->credit = $credit;
            $row->update_time = Now();
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Edit Failed.');
            }

            $m_site_credit_flow = M('site_credit_flow');
            $row_flow = $m_site_credit_flow->newRow();
            $row_flow->receiver_id = $branch_id;
            $row_flow->receiver_type = objGuidTypeEnum::SITE_BRANCH;
            $row_flow->before_credit = $before_credit;
            $row_flow->credit = $credit;
            $row_flow->remark = $remark;
            $row_flow->operator_id = $operator_id;
            $row_flow->operator_name = $operator_name;
            $row_flow->create_time = Now();
            $rt = $row_flow->insert();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Edit Failed.');
            }
            $conn->submitTransaction();
            return new result(true, 'Edit Successful.');

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }

    }

    public function editBranchLimitApprove($branch_id, $limit_arr)
    {
        $m_site_branch = M('site_branch');
        $row = $m_site_branch->getRow($branch_id);
        $profile = my_json_decode($row['profile']);
        if (!is_array($profile)) {
            $profile = array();
        }
        $profile['limit_chief_teller_approve'] = $limit_arr;
        $row->profile = my_json_encode($profile);
        $row->update_time = Now();
        $rt = $row->update();
        return $rt;
    }

}