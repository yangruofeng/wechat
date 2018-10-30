<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/17
 * Time: 21:46
 */
class loan_fee_settingModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('loan_fee_setting');
    }

    public function getInfoById($id)
    {
        return $this->find(array(
            'uid' => $id
        ));
    }

    public function getDefaultSettingList()
    {
        $sql = "select * from loan_fee_setting where category_id=0 order by currency,min_amount,max_amount";
        return $this->reader->getRows($sql);
    }

    public function getSettingListOfCategoryId($cate_id)
    {
        $sql = "select * from loan_fee_setting where category_id='" . $cate_id . "' order by currency,min_amount,max_amount";
        $special_list = $this->reader->getRows($sql);
        return $special_list;
    }

    /** API计算需要使用这个方法
     * @param $cate_id
     * @return ormCollection
     */
    public function getSpecialSettingListOfCategoryId($cate_id)
    {
        // 还是一定要合并default的，防止因为cate没有特殊设置出现不计算的问题
        $sql = "select * from loan_fee_setting where category_id='" . $cate_id . "' order by currency,min_amount,max_amount";
        $special_list = $this->reader->getRows($sql);
        $default_list = $this->getDefaultSettingList();
        $ret = array();
        foreach ($default_list as $item) {
            $special_item = array();
            if ($special_list) {
                foreach ($special_list as $v) {
                    if ($v['currency'] == $item['currency'] && $v['min_amount'] == $item['min_amount'] && $v['max_amount'] == $item['max_amount']) {
                        $special_item = $v;
                        break;
                    }
                }
            }
            $new_item = array_merge($item, array("uid" => 0, 'default_uid' => $item['uid'], 'category_id' => $cate_id));
            if ($special_item) {
                $new_item = array_merge($new_item, $special_item);
            }
            $ret[] = $new_item;
        }
        return $ret;
    }

    public function updateInfo($params)
    {
        $uid = intval($params['uid']);
        $category_id = intval($params['category_id']);
        $currency = $params['currency'];
        $min_amount = round($params['min_amount'], 2);
        $max_amount = round($params['max_amount'], 2);
        $admin_fee = round($params['admin_fee'], 2);
        $admin_fee_type = intval($params['admin_fee_type']);
        $loan_fee = round($params['loan_fee'], 2);
        $loan_fee_type = intval($params['loan_fee_type']);
        $annual_fee = round($params['annual_fee'], 2);
        $annual_fee_type = intval($params['annual_fee_type']);
        if ($min_amount > $max_amount) {
            return new result(false, 'Max amount less than min amount.');
        }

        if ($uid) {
            $row = $this->getRow($uid);
            if (!$row) {
                return new result(false, 'Invalid id:' . $uid);
            }

            $row->currency = $currency;
            $row->min_amount = $min_amount;
            $row->max_amount = $max_amount;
            $row->admin_fee = $admin_fee;
            $row->admin_fee_type = $admin_fee_type;
            $row->loan_fee = $loan_fee;
            $row->loan_fee_type = $loan_fee_type;
            $row->annual_fee = $annual_fee;
            $row->annual_fee_type = $annual_fee_type;

            $row->update_time = Now();
            $up = $row->update();
            return $up;
        } else {
            $row = $this->newRow();
            $row->category_id = $category_id;
            $row->currency = $currency;
            $row->min_amount = $min_amount;
            $row->max_amount = $max_amount;
            $row->admin_fee = $admin_fee;
            $row->admin_fee_type = $admin_fee_type;
            $row->loan_fee = $loan_fee;
            $row->loan_fee_type = $loan_fee_type;
            $row->annual_fee = $annual_fee;
            $row->annual_fee_type = $annual_fee_type;

            $row->create_time = Now();
            $insert = $row->insert();
            return $insert;
        }
    }

    public function deleteInfoById($id)
    {
        $row = $this->getRow($id);
        if (!$row) {
            return new result(false, 'Invalid id:' . $id);
        }
        $del = $row->delete();
        return $del;

    }

    public function copyDefaultSetting($category_id)
    {
        $default_setting = $this->getDefaultSettingList();
        if ($default_setting) {
            $conn = ormYo::Conn();
            $conn->startTransaction();
            foreach ($default_setting as $setting) {
                $row = $this->newRow($setting);
                $row->category_id = $category_id;
                $row->create_time = Now();
                $row->create_time = Now();
                $rt = $row->insert();
                if (!$rt->STS) {
                    $conn->rollback();
                    return new result(true, 'Copy Successful.');
                }
            }
            $conn->submitTransaction();
        }
        return new result(true);
    }

}