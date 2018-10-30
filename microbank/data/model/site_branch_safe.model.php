<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class site_branch_safeModel extends tableModelBase
{
    public function  __construct()
    {
        parent::__construct('site_branch_safe');
    }

    public function addSafe($param)
    {
        $branch_id = intval($param['branch_id']);
        $safe_code = trim($param['safe_code']);
        $remark = trim($param['remark']);
        $operator_id = intval($param['operator_id']);
        $operator_name = trim($param['operator_name']);
        if (!$safe_code) {
            return new result(false, 'Safe code required.');
        }

        $chk_code = $this->find(array('branch_id' => $branch_id, 'safe_code' => $safe_code));
        if ($chk_code) {
            return new result(false, 'Safe code already exist.');
        }

        $row = $this->newRow();
        $row->branch_id = $branch_id;
        $row->safe_code = $safe_code;
        $row->remark = $remark;
        $row->operator_id = $operator_id;
        $row->operator_name = $operator_name;
        $row->create_time = Now();
        $row->update_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Add successful.');
        } else {
            return new result(true, 'Add failed.');
        }
    }

    public function editSafe($param)
    {
        $uid = intval($param['uid']);
        $safe_code = trim($param['safe_code']);
        $remark = trim($param['remark']);
        $operator_id = intval($param['operator_id']);
        $operator_name = trim($param['operator_name']);
        if (!$safe_code) {
            return new result(false, 'Safe code required.');
        }

        $row = $this->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id.');
        }

        $chk_code = $this->find(array('branch_id' => $row['branch_id'], 'safe_code' => $safe_code, 'uid' => array('neq', $uid)));
        if ($chk_code) {
            return new result(false, 'Safe code already exist.');
        }

        $row->safe_code = $safe_code;
        $row->remark = $remark;
        $row->operator_id = $operator_id;
        $row->operator_name = $operator_name;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Edit successful.');
        } else {
            return new result(true, 'Edit failed.');
        }
    }

    public function getSafeInfoById($id)
    {
        return $this->find(array(
            'uid' => $id
        ));
    }
}
