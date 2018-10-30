<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class site_departModel extends tableModelBase
{
    public function  __construct()
    {
        parent::__construct('site_depart');
    }

    /**
     * 添加branch
     * @param $p
     * @return result
     */
    public function addDepart($p)
    {
        $depart_code = trim($p['depart_code']);
        $depart_name = trim($p['depart_name']);
        $branch_id = intval($p['branch_id']);
        $leader = intval($p['leader']);
        $assistant = intval($p['assistant']);
        $creator_id = intval($p['creator_id']);
        $creator_name = trim($p['creator_name']);
        if (empty($depart_code) || empty($depart_name)) {
            return new result(false, 'Code and name cannot be empty!');
        }
        if (!$branch_id) {
            return new result(false, 'Branch cannot be empty!');
        }

        $chk_code = $this->getRow(array('depart_code' => $depart_code, 'branch_id' => $branch_id));
        if ($chk_code) {
            return new result(false, 'Code already existed!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $this->newRow();
            $row->depart_code = $depart_code;
            $row->depart_name = $depart_name;
            $row->branch_id = $branch_id;
            $row->leader = $leader;
            $row->assistant = $assistant;
            $row->creator_id = $creator_id;
            $row->creator_name = $creator_name;
            $row->create_time = Now();
            $rt = $row->insert();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Add failed1--' . $rt->MSG);
            }

            $class_user = new userClass();
            $rt_1 = $class_user->addPointDepartPeriodByDepartId($rt->AUTO_ID);
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Add failed2--' . $rt_1->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Add successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 编辑部门
     * @param $p
     * @return result
     * @throws Exception
     */
    public function editDepart($p)
    {
        $uid = intval($p['uid']);
        $depart_code = trim($p['depart_code']);
        $depart_name = trim($p['depart_name']);
        $leader = intval($p['leader']);
        $assistant = intval($p['assistant']);
        if (empty($depart_code) || empty($depart_name)) {
            return new result(false, 'Code and name cannot be empty!');
        }
        $row = $this->getRow(array('uid' => $uid));
        $branch_id = $row->branch_id;
        $chk_code = $this->getRow(array('depart_code' => $depart_code, 'branch_id' => $branch_id, 'uid' => array('neq', $uid)));
        if ($chk_code) {
            return new result(false, 'Code already existed!');
        }

        $row->depart_code = $depart_code;
        $row->depart_name = $depart_name;
//        $row->branch_id = $branch_id;
        $row->leader = $leader;
        $row->assistant = $assistant;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Edit successful!');
        } else {
            return new result(false, 'Edit failed--' . $rt->MSG);
        }

    }

    /**
     * 删除部门
     * @param $uid
     * @return result
     */
    public function deleteDepart($uid)
    {
        $chk_user = M('um_user')->find(array('depart_id' => $uid));
        if ($chk_user) {
            return new result(false,'The department has users.');
        }

        $rt = $this->delete(array('uid' => $uid));
        if (!$rt->STS) {
            return new result(false, 'Delete failed--' . $rt->MSG);
        } else {
            return new result(true, 'Delete successful!');
        }
    }

}
