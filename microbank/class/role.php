<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/11/7
 * Time: 9:35
 */
class role
{
    /**
     * role列表
     * @param $p
     * @return array
     */
    public function getRolePage($p)
    {
        $r = new ormReader();
        $sql = "SELECT * FROM um_role";
        $search_text = trim($p['search_text']);
        if ($search_text) {
            $sql .= " WHERE role_name LIKE '%" . $search_text . "%'";
        }
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $role_ids = implode(',', array_column($rows, 'uid'));
            $sql = "SELECT role_id,auth_group_id,auth_type FROM um_role_group WHERE role_id IN ($role_ids)";
            $auth_group = $r->getRows($sql);
            $auth_group_back_office = array();
            $auth_group_counter = array();
            foreach ($auth_group as $val) {
                if ($val['auth_type'] == authTypeEnum::BACK_OFFICE) {
                    $auth_group_back_office[$val['role_id']][] = $val['auth_group_id'];
                }
                if ($val['auth_type'] == authTypeEnum::COUNTER) {
                    $auth_group_counter[$val['role_id']][] = $val['auth_group_id'];
                }
            }
            foreach ($rows as $key => $row) {
                $row['auth_group_back_office'] = $auth_group_back_office[$row['uid']];
                $row['auth_group_counter'] = $auth_group_counter[$row['uid']];
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

    /**
     * role下user列表
     * @param $p
     * @return array
     */
    public function getUserListByRole($p)
    {
        $r = new ormReader();
        $sql = "SELECT uu.*,sb.branch_name,sd.depart_name FROM um_user uu"
            . " INNER JOIN um_user_role uur ON uu.uid = uur.user_id"
            . " LEFT JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " LEFT JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE 1 = 1";
        $role_id = intval($p['role_id']);
        if ($role_id) {
            $sql .= " AND uur.role_id =  $role_id";
        }

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
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

    /**
     * 获取role信息
     * @param $uid
     * @return result
     */
    public function getRoleInfo($uid)
    {
        $m_um_role = M('um_role');
        $role_info = $m_um_role->find(array('uid' => $uid));
        if (empty($role_info)) {
            return new result(false, 'Invalid Id');
        }

        $allow_back_office = $this->getRoleAuthListById($uid, authTypeEnum::BACK_OFFICE);
        $allow_counter = $this->getRoleAuthListById($uid, authTypeEnum::COUNTER);
        $role_info['allow_back_office'] = $allow_back_office;
        $role_info['allow_counter'] = $allow_counter;
        return new result(true, '', $role_info);
    }

    /**
     * 获取role权限
     * @param $uid
     * @param $type
     * @return array
     */
    private function getRoleAuthListById($uid, $type)
    {
        $m_um_role_group = M('um_role_group');
        $m_special_auth = M('um_special_auth');
        $role_group = $m_um_role_group->select(array('role_id' => $uid, 'auth_type' => $type));
        $special_auth = $m_special_auth->select(array('role_id' => $uid, 'auth_type' => $type));
        $role_group = array_column($role_group, 'auth_group_id');
        $allow_auth = array();
        $limit_auth = array();
        foreach ($special_auth as $auth) {
            if ($auth['special_type'] == 1) {
                $allow_auth[] = $auth['auth_code'];
            }
            if ($auth['special_type'] == 2) {
                $limit_auth[] = $auth['auth_code'];
            }
        }

        $auth_select = array();
        foreach ($role_group as $key => $r) {
            $role = authBase::getAuthGroup($r, $type);
            if (!$role) continue;
            $auth_group_list = $role->getAuthList();
            if (in_array($r, $role_group)) {
                $auth_select = array_merge($auth_select, $auth_group_list);
            }
        }
        $auth_select = array_merge($auth_select, $allow_auth);
        $auth_select = array_unique($auth_select);
        $auth_select = array_diff($auth_select, $limit_auth);
        return array('role_group' => $role_group, 'allow_auth' => $auth_select, 'limit_auth' => $limit_auth);
    }

    /**
     * 获取role列表
     * @return result
     */
    public function getRoleList()
    {
        $m_um_role = M('um_role');
        $role_list = $m_um_role->select(array('role_status' => 1));
        foreach ($role_list as $key => $role) {
            $rt = $this->getRoleInfo($role['uid']);
            $role_list[$key] = $rt->DATA;
        }
        return new result(true, '', $role_list);
    }

    /**
     * 添加role
     * @param $param
     * @return result
     */
    public function addRole($param)
    {
        $role_name = trim($param['role_name']);
        $auth_group = $param['auth_group'];
        $auth_select = $param['auth_select'];
        $auth_group_counter = $param['auth_group_counter'];
        $auth_select_counter = $param['auth_select_counter'];
        $remark = $param['remark'];
        $role_status = intval($param['role_status']);
        $creator_id = intval($param['creator_id']);
        $creator_name = $param['creator_name'];
        if (!$role_name) {
            return new result(false, 'Role name cannot be empty!');
        }
        $m_um_role = M('um_role');

        $chk_name = $m_um_role->getRow(array('role_name' => $role_name));
        if ($chk_name) {
            return new result(false, 'Name exists!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $m_um_role->newRow();
            $row->role_name = $role_name;
            $row->role_status = $role_status;
            $row->role_status = $role_status;
            $row->creator_id = $creator_id;
            $row->creator_name = $creator_name;
            $row->create_time = Now();
            $row->remark = $remark;
            $rt_1 = $row->insert();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Add failed--' . $rt_1->MSG);
            }

            $rt_2 = $this->addRoleAuth($rt_1->AUTO_ID, $auth_group, $auth_select, authTypeEnum::BACK_OFFICE);
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, $rt_2->MSG);
            }

            $rt_3 = $this->addRoleAuth($rt_1->AUTO_ID, $auth_group_counter, $auth_select_counter, authTypeEnum::COUNTER);
            if (!$rt_3->STS) {
                $conn->rollback();
                return new result(false, $rt_3->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Add Successful');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 保存role权限
     * @param $role_id
     * @param $auth_group
     * @param $auth_select
     * @param $type
     * @return result
     */
    private function addRoleAuth($role_id, $auth_group, $auth_select, $type)
    {
        $m_um_role_group = M('um_role_group');
        $m_special_auth = M('um_special_auth');
        $auth_list = array();
        foreach ($auth_group as $group) {
            $row_group = $m_um_role_group->newRow();
            $row_group->role_id = $role_id;
            $row_group->auth_group_id = $group;
            $row_group->auth_type = $type;
            $rt_1 = $row_group->insert();
            if (!$rt_1->STS) {
                return new result(false, 'Add failed--' . $rt_1->MSG);
            }

            $role = authBase::getAuthGroup($group, $type);
            if (!$role) continue;
            $auth_group_list = $role->getAuthList();
            $auth_list = array_merge($auth_list, $auth_group_list);
        }
        $auth_list = array_unique($auth_list);
        $allow_auth = array_diff($auth_select, $auth_list);
        $limit_auth = array_diff($auth_list, $auth_select);

        foreach ($allow_auth as $auth) {
            $row_special_auth = $m_special_auth->newRow();
            $row_special_auth->role_id = $role_id;
            $row_special_auth->special_type = 1;
            $row_special_auth->auth_code = $auth;
            $row_special_auth->auth_type = $type;
            $rt_2 = $row_special_auth->insert();
            if (!$rt_2->STS) {
                return new result(false, 'Add failed--' . $rt_2->MSG);
            }
        }

        foreach ($limit_auth as $auth) {
            $row_special_auth = $m_special_auth->newRow();
            $row_special_auth->role_id = $role_id;
            $row_special_auth->special_type = 2;
            $row_special_auth->auth_code = $auth;
            $row_special_auth->auth_type = $type;
            $rt_3 = $row_special_auth->insert();
            if (!$rt_3->STS) {
                return new result(false, 'Add failed--' . $rt_3->MSG);
            }
        }

        return new result(true);
    }

    /**
     * 编辑role
     * @param $param
     * @return result
     */
    public function editRole($param)
    {
        $uid = intval($param['uid']);
        $role_name = trim($param['role_name']);
        $auth_group = $param['auth_group'];
        $auth_select = $param['auth_select'];
        $auth_group_counter = $param['auth_group_counter'];
        $auth_select_counter = $param['auth_select_counter'];
        $remark = $param['remark'];
        $role_status = intval($param['role_status']);
        if (!$role_name) {
            return new result(false, 'Role name cannot be empty!');
        }
        $m_um_role = M('um_role');
        $m_um_role_group = M('um_role_group');
        $m_special_auth = M('um_special_auth');

        $row = $m_um_role->getRow(array('uid' => $uid));
        if (empty($row)) {
            return new result(false, 'Invalid Id!');
        }

        $chk_name = $m_um_role->getRow(array('role_name' => $role_name, 'uid' => array('neq', $uid)));
        if ($chk_name) {
            return new result(false, 'Name text exists!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row->role_name = $role_name;
            $row->role_status = $role_status;
            $row->role_status = $role_status;
            $row->update_time = Now();
            $row->remark = $remark;
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt_1->MSG);
            }

            $rt_5 = $m_um_role_group->delete(array('role_id' => $uid));
            if (!$rt_5->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt_5->MSG);
            }
            $rt_6 = $m_special_auth->delete(array('role_id' => $uid));
            if (!$rt_6->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt_6->MSG);
            }

            $rt_2 = $this->addRoleAuth($uid, $auth_group, $auth_select, authTypeEnum::BACK_OFFICE);
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, $rt_2->MSG);
            }

            $rt_3 = $this->addRoleAuth($uid, $auth_group_counter, $auth_select_counter, authTypeEnum::COUNTER);
            if (!$rt_3->STS) {
                $conn->rollback();
                return new result(false, $rt_3->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Edit Successful');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 删除role
     * @param $uid
     * @return result
     */
    public function deleteRole($uid)
    {
        $m_um_role = M('um_role');
        $m_um_role_group = M('um_role_group');
        $m_special_auth = M('um_special_auth');
        $m_um_user_role = M('um_user_role');

        $uid = intval($uid);
        $row = $m_um_role->getRow(array('uid' => $uid));
        if (empty($row)) {
            return new result(false, 'Invalid Id!');
        }

        $chk_used = $m_um_user_role->find(array('role_id' => $uid));
        if ($chk_used) {
            return new result(false, 'Cannot be deleted because it has been used!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt_1 = $row->delete();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed--' . $rt_1->MSG);
            }

            $rt_2 = $m_um_role_group->delete(array('role_id' => $uid));
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed--' . $rt_2->MSG);
            }

            $rt_3 = $m_special_auth->delete(array('role_id' => $uid));
            if (!$rt_3->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed--' . $rt_3->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Delete Successful');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }

    }
}