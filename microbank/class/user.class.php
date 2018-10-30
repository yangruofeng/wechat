<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/11/7
 * Time: 9:35
 */
class userClass
{
    /**
     * User列表
     * @param $p
     * @return array
     */
    public static function getUserList($p)
    {
        $r = new ormReader();
        $sql = "SELECT uu.*,sb.branch_name,sd.depart_name,IFNULL(uc.card_no,'') ic_card FROM um_user uu"
            . " left JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " left JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " LEFT JOIN um_user_card uc ON uu.uid = uc.user_id AND uc.state = 1 "
            . " WHERE uu.is_system_account = 0 AND uu.user_status >= 0";
        $search_text = trim($p['search_text']);
        if ($search_text) {
            $sql .= " AND uu.user_code LIKE '%" . qstr2($search_text) . "%' OR uu.user_name LIKE '%" . qstr2($search_text) . "%'";
        }

        if ($p['phone_number']) {
            $phone = tools::getFormatPhone($p['country_code'], $p['phone_number']);
            $sql .= " AND uu.mobile_phone like '%" . $phone['contact_phone'] . "%'";
        }

        $branch_id = intval($p['branch_id']);
        if ($branch_id) {
            $sql .= " AND sd.branch_id = " . $branch_id;
        }

        if (!$p['is_root']) {
            $sql .= " and uu.user_position!='" . userPositionEnum::ROOT . "'";
            $sql .= " and uu.user_position!='" . userPositionEnum::DEVELOPER . "'";
        }
        $limit_position = $p['limit_position'];
        if (is_array($limit_position)) {
            if (count($limit_position)) {
                $str_pos = implode("','", $limit_position);
                $sql .= " and uu.user_position in ('" . $str_pos . "')";
            }
        }

        $sql .= " ORDER by uu.uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $user_ids = implode(',', array_column($rows, 'uid'));
            $sql = "SELECT uur.user_id,ur.role_name FROM um_user_role uur LEFT JOIN um_role ur ON uur.role_id = ur.uid WHERE user_id IN ($user_ids)";
            $role_arr = $r->getRows($sql);
            $role_arr_new = array();
            foreach ($role_arr as $val) {
                $role_arr_new[$val['user_id']][] = $val['role_name'];
            }
            $num = ($pageNumber - 1) * $pageSize;
            foreach ($rows as $key => $row) {
                $row['no'] = ++$num;
                $row['role_group'] = $role_arr_new[$row['uid']];
                unset($row['password']);
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
     * 获取user信息
     * @param $uid
     * @return result
     */
    public function getUserInfo($uid)
    {
        $uid = intval($uid);
        $m_um_role_role = M('um_user_role');
        $m_special_auth = M('um_special_auth');
        $r = new ormReader();
        $sql = 'SELECT uu.*,sd.depart_name,sd.branch_id,sb.branch_name FROM um_user uu' .
            ' LEFT JOIN site_depart sd ON uu.depart_id = sd.uid' .
            ' LEFT JOIN site_branch sb ON sd.branch_id = sb.uid' .
            ' WHERE uu.uid = ' . $uid;
        $user_info = $r->getRow($sql);
        if (empty($user_info)) {
            return new result(false, 'Invalid Id');
        }

        $role_arr = $m_um_role_role->select(array('user_id' => $uid));
        $special_auth = $m_special_auth->select(array('user_id' => $uid));
        $role_arr = array_column($role_arr, 'role_id');

        $class_role = new role();
        $back_office_auth = array();
        $counter_auth = array();
        foreach ($role_arr as $role_id) {
            $rt = $class_role->getRoleInfo($role_id);
            $back_office_auth = array_merge($back_office_auth, $rt->DATA['allow_back_office']['allow_auth']);
            $counter_auth = array_merge($counter_auth, $rt->DATA['allow_counter']['allow_auth']);
        }
        $back_office_auth = array_unique($back_office_auth);
        $counter_auth = array_unique($counter_auth);
        $allow_auth_back_office = array();
        $limit_auth_back_office = array();
        $allow_auth_counter = array();
        $limit_auth_counter = array();
        foreach ($special_auth as $auth) {
            if ($auth['auth_type'] == authTypeEnum::BACK_OFFICE) {
                if ($auth['special_type'] == 1) {
                    $allow_auth_back_office[] = $auth['auth_code'];
                }
                if ($auth['special_type'] == 2) {
                    $limit_auth_back_office[] = $auth['auth_code'];
                }
            }

            if ($auth['auth_type'] == authTypeEnum::COUNTER) {
                if ($auth['special_type'] == 1) {
                    $allow_auth_counter[] = $auth['auth_code'];
                }
                if ($auth['special_type'] == 2) {
                    $limit_auth_counter[] = $auth['auth_code'];
                }
            }
        }

        $back_office_auth = array_merge($back_office_auth, $allow_auth_back_office);
        $back_office_auth = array_unique($back_office_auth);
        $back_office_auth = array_diff($back_office_auth, $limit_auth_back_office);

        $counter_auth = array_merge($counter_auth, $allow_auth_counter);
        $counter_auth = array_unique($counter_auth);
        $counter_auth = array_diff($counter_auth, $limit_auth_counter);

        $user_info['role_arr'] = $role_arr;
        $user_info['back_office_auth'] = $back_office_auth;
        $user_info['counter_auth'] = $counter_auth;
        return new result(true, '', $user_info);
    }

    /**
     * 添加user
     * @param $param
     * @return result
     */
    public function addUser($param)
    {
        $user_code = trim($param['user_code']);
        $user_name = trim($param['user_name']);
        $password = trim($param['password']);
        $depart_id = intval($param['depart_id']);
        $role_select = $param['role_select'];
        $auth_select = $param['auth_select'];
        $auth_select_counter = $param['auth_select_counter'];
        $user_position = $param['user_position'];
        $country_code = trim($param['country_code']);
        $phone = trim($param['phone']);
        $remark = $param['remark'];
        $user_status = intval($param['user_status']);
        $creator_id = intval($param['creator_id']);
        $creator_name = $param['creator_name'];
        if (!$user_code || !$user_name) {
            return new result(false, 'User code or name cannot be empty!');
        }
        if (!$depart_id) {
            return new result(false, 'Please select department!');
        }
        $m_um_user = M('um_user');
        $m_um_role_role = M('um_user_role');

        $chk_code = $m_um_user->getRow(array('user_code' => $user_code, 'user_status' => array('neq', -1)));
        if ($chk_code) {
            return new result(false, 'Code exists!');
        }

        $format_phone = tools::getFormatPhone($country_code, $phone);
        $contact_phone = $format_phone['contact_phone'];

        // 检查合理性
        if (!isPhoneNumber($contact_phone)) {
            return new result(false, 'Invalid phone', null, errorCodesEnum::INVALID_PARAM);
        }

        // 判断是否被其他user使用
        $chk_phone = $m_um_user->find(array('mobile_phone' => $contact_phone, 'user_status' => array('neq', -1)));
        if ($chk_phone) {
            return new result(false, 'The phone number has been used.');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $m_um_user->newRow();
            $row->user_code = $user_code;
            $row->user_name = $user_name;
            $row->password = md5($password);
            $row->depart_id = $depart_id;
            $row->user_status = $user_status;
            $row->user_position = $user_position;
            $row->mobile_phone = $contact_phone;
            $row->obj_guid = '';

            $row->creator_id = $creator_id;
            $row->creator_name = $creator_name;
            $row->create_time = Now();
            $row->remark = $remark;
            $rt_1 = $row->insert();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Add failed--' . $rt_1->MSG);
            }

            $back_office_auth_role = array();
            $counter_auth_role = array();
            $class_role = new role();
            foreach ($role_select as $role) {
                $row_role = $m_um_role_role->newRow();
                $row_role->user_id = $rt_1->AUTO_ID;
                $row_role->role_id = $role;
                $rt_2 = $row_role->insert();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Add failed--' . $rt_2->MSG);
                }

                $rt_3 = $class_role->getRoleInfo($role);
                $back_office_auth_role = array_merge($back_office_auth_role, $rt_3->DATA['allow_back_office']['allow_auth']);
                $counter_auth_role = array_merge($counter_auth_role, $rt_3->DATA['allow_counter']['allow_auth']);
            }

            $rt_4 = $this->addUserSpecialAuth($rt_1->AUTO_ID, $back_office_auth_role, $auth_select, authTypeEnum::BACK_OFFICE);
            if (!$rt_4->STS) {
                $conn->rollback();
                return new result(false, $rt_4->MSG);
            }
            $rt_5 = $this->addUserSpecialAuth($rt_1->AUTO_ID, $counter_auth_role, $auth_select_counter, authTypeEnum::COUNTER);
            if (!$rt_5->STS) {
                $conn->rollback();
                return new result(false, $rt_5->MSG);
            }

            $row->obj_guid = generateGuid($rt_1->AUTO_ID, objGuidTypeEnum::UM_USER);
            $rt_6 = $row->update();
            if (!$rt_6->STS) {
                $conn->rollback();
                return new result(false, 'Add failed--' . $rt_6->MSG);
            }
            $conn->submitTransaction();
            return new result(true, 'Add Successful');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    private function addUserSpecialAuth($uid, $role_auth, $select_auth, $type)
    {
        $role_auth = array_unique($role_auth);
        $allow_auth = array_diff($select_auth, $role_auth);
        $limit_auth = array_diff($role_auth, $select_auth);
        $m_special_auth = M('um_special_auth');

        foreach ($allow_auth as $auth) {
            $row_special_auth = $m_special_auth->newRow();
            $row_special_auth->user_id = $uid;
            $row_special_auth->special_type = 1;
            $row_special_auth->auth_code = $auth;
            $row_special_auth->auth_type = $type;
            $rt_3 = $row_special_auth->insert();
            if (!$rt_3->STS) {
                return new result(false, 'Add failed--' . $rt_3->MSG);
            }
        }

        foreach ($limit_auth as $auth) {
            $row_special_auth = $m_special_auth->newRow();
            $row_special_auth->user_id = $uid;
            $row_special_auth->special_type = 2;
            $row_special_auth->auth_code = $auth;
            $row_special_auth->auth_type = $type;
            $rt_4 = $row_special_auth->insert();
            if (!$rt_4->STS) {
                return new result(false, 'Add failed--' . $rt_4->MSG);
            }
        }
        return new result(true);
    }

    /**
     * 编辑user
     * @param $param
     * @return result
     */
    public function editUser($param)
    {
        $uid = intval($param['uid']);
        $user_code = trim($param['user_code']);
        $user_name = trim($param['user_name']);
        $password = trim($param['password']);
        $depart_id = intval($param['depart_id']);
        $role_select = $param['role_select'];
        $auth_select = $param['auth_select'];
        $auth_select_counter = $param['auth_select_counter'];
        $user_position = $param['user_position'];
        $country_code = trim($param['country_code']);
        $phone = trim($param['phone']);
        $remark = $param['remark'];
        $user_status = intval($param['user_status']);
        if (!$user_code || !$user_name) {
            return new result(false, 'User code or name cannot be empty!');
        }
        if (!$depart_id) {
            return new result(false, 'Please select department!');
        }

        $format_phone = tools::getFormatPhone($country_code, $phone);
        $contact_phone = $format_phone['contact_phone'];

        // 检查合理性
        if (!isPhoneNumber($contact_phone)) {
            return new result(false, 'Invalid phone', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_um_user = M('um_user');
        $m_um_user_role = M('um_user_role');
        $m_special_auth = M('um_special_auth');

        // 判断是否被其他user使用
        $chk_phone = $m_um_user->find(array('uid' => array('neq', $uid), 'user_status' => array('neq', -1), 'mobile_phone' => $contact_phone));
        if ($chk_phone) {
            return new result(false, 'The phone number has been used.');
        }

        $row = $m_um_user->getRow(array('uid' => $uid));
        if (empty($row)) {
            return new result(false, 'Invalid Id!');
        }

        $chk_code = $m_um_user->getRow(array('user_code' => $user_code, 'user_status' => array('neq', -1), 'uid' => array('neq', $uid)));
        if ($chk_code) {
            return new result(false, 'Code exists!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row->user_code = $user_code;
            $row->user_name = $user_name;
            if ($password) {
                $row->password = md5($password);
            }
            $row->depart_id = $depart_id;
            $row->user_position = $user_position;
            $row->mobile_phone = $contact_phone;
            $row->user_status = $user_status;
            $row->update_time = Now();
            $row->remark = $remark;
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt_1->MSG);
            }

            $rt_5 = $m_um_user_role->delete(array('user_id' => $uid));
            if (!$rt_5->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt_5->MSG);
            }

            $back_office_auth_role = array();
            $counter_auth_role = array();
            $class_role = new role();
            foreach ($role_select as $role) {
                $row_role = $m_um_user_role->newRow();
                $row_role->user_id = $uid;
                $row_role->role_id = $role;
                $rt_2 = $row_role->insert();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Edit failed--' . $rt_2->MSG);
                }

                $rt_3 = $class_role->getRoleInfo($role);
                $back_office_auth_role = array_merge($back_office_auth_role, $rt_3->DATA['allow_back_office']['allow_auth']);
                $counter_auth_role = array_merge($counter_auth_role, $rt_3->DATA['allow_counter']['allow_auth']);
            }

            $rt_6 = $m_special_auth->delete(array('user_id' => $uid));
            if (!$rt_6->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed--' . $rt_6->MSG);
            }

            $rt_4 = $this->addUserSpecialAuth($uid, $back_office_auth_role, $auth_select, authTypeEnum::BACK_OFFICE);
            if (!$rt_4->STS) {
                $conn->rollback();
                return new result(false, $rt_4->MSG);
            }
            $rt_5 = $this->addUserSpecialAuth($uid, $counter_auth_role, $auth_select_counter, authTypeEnum::COUNTER);
            if (!$rt_5->STS) {
                $conn->rollback();
                return new result(false, $rt_5->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Edit Successful');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 删除user
     * @param $uid
     * @return result
     */
    public function deleteUser($uid)
    {
        $m_um_user = M('um_user');
        $uid = intval($uid);
        $row = $m_um_user->getRow(array('uid' => $uid));
        if (empty($row)) {
            return new result(false, 'Invalid Id!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row->user_status = -1;
            $row->user_code = $row->user_code . '-to-delete';
            $row->update_time = Now();
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return $rt_1;
            }

            $sql = "UPDATE member_follow_officer SET is_active = 0 WHERE officer_id = " . $uid;
            $rt_2 = $m_um_user->conn->execute($sql);
            if (!$rt_2->STS) {
                $conn->rollback();
                return $rt_2;
            }

            $conn->submitTransaction();
            return new result(true, 'Delete Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /** 检查密码强度
     * @param $password
     * @return bool
     */
    public static function isValidPassword($password)
    {
        // 是否有空格
        $space = preg_match("/\s/", $password);
        if ($space) {
            return false;
        }
        // 长度6位及以上
        $len = strlen($password);
        if ($len < 6) {
            return false;
        }
        return true;
    }

    /**
     * 修改密码
     * @param $p
     * @return result
     */
    public function changePassword($p)
    {
        $uid = intval($p['user_id']);
        $old_password = trim($p['old_password']);
        $new_password = trim($p['new_password']);
        $m_um_user = M('um_user');
        $row = $m_um_user->getRow($uid);
        if ($row->password != md5($old_password)) {
            return new result(false, 'Old password error!');
        }

        if ($row->password == md5($new_password)) {
            return new result(false, 'The new password is the same as the old password!');
        }

        if (!preg_match("/^[a-zA-Z0-9]{6,18}$/", $new_password)) {
            return new result(false, 'The password must be 6-18 digits or letters!');
        }

        $row->password = md5($new_password);
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Change Successful! Please Login again');
        } else {
            return new result(false, 'Change failure!');
        }
    }


    public static function isLoginAccountUsed($login_account)
    {
        $r = new ormReader();
        $sql = "select uid from um_user where user_code=" . qstr($login_account);
        $row = $r->getRow($sql);
        return $row ? true : false;
    }


    public static function editLoginAccount($user_id, $login_account)
    {
        if (!$login_account) {
            return new result(false, 'Empty login account', null, errorCodesEnum::INVALID_PARAM);
        }
        $user_id = intval($user_id);
        $m = new um_userModel();
        $user = $m->getRow($user_id);
        if (!$user) {
            return new result(false, 'Not found user:' . $user_id, null, errorCodesEnum::NO_DATA);
        }

        if (self::isLoginAccountUsed($login_account)) {
            return new result(false, 'Login account used.', null, errorCodesEnum::USER_EXIST);
        }

        $user->user_code = $login_account;
        $user->update_time = Now();
        $up = $user->update();
        if (!$up->STS) {
            return new result(false, 'Edit fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        return new result(true);


    }

    public static function editAvator($user_id, $image_path)
    {
        $user_id = intval($user_id);
        $m = new um_userModel();
        $user = $m->getRow($user_id);
        if (!$user) {
            return new result(false, 'Not found user:' . $user_id, null, errorCodesEnum::NO_DATA);
        }
        $user->user_image = $image_path;
        $user->user_icon = $image_path;
        $user->update_time = Now();
        $up = $user->update();
        if (!$up->STS) {
            return new result(false, 'Edit fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', array(
            'user_image' => getImageUrl($image_path)
        ));

    }


    public static function editPhoneNumber($params)
    {
        $user_id = intval($params['officer_id']);
        $country_code = $params['country_code'];
        $phone_number = $params['phone_number'];
        $sms_id = $params['sms_id'];
        $sms_code = $params['sms_code'];
        if (!$user_id || !$country_code || !$phone_number) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_user = new um_userModel();
        $user = $m_user->getRow($user_id);
        if (!$user) {
            return new result(false, 'No User', null, errorCodesEnum::USER_NOT_EXISTS);
        }

        $chk = (new phone_verify_codeModel())->verifyCode($sms_id, $sms_code);
        if (!$chk->STS) {
            return $chk;
        }

        $phone_arr = tools::getFormatPhone($country_code, $phone_number);
        $contract_phone = $phone_arr['contact_phone'];
        $user->country_code = $country_code;
        $user->phone_number = $phone_number;
        $user->mobile_phone = $contract_phone;
        $user->update_time = Now();
        $up = $user->update();
        if (!$up->STS) {
            return new result(false, 'Edit fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }
        return new result(true);
    }


    public static function editEmail($user_id, $email)
    {
        $user_id = intval($user_id);
        if (!$user_id || !$email) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        if (!isEmail($email)) {
            return new result(false, 'Not a email address:' . $email, null, errorCodesEnum::INVALID_EMAIL);
        }

        $m = new um_userModel();
        $user = $m->getRow($user_id);
        if (!$user) {
            return new result(false, 'Not found user:' . $user_id, null, errorCodesEnum::NO_DATA);
        }

        $user->email = $email;
        $user->update_time = Now();
        $up = $user->update();
        if (!$up->STS) {
            return new result(false, 'Edit fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }
        return new result(true);
    }


    public static function forgotTradingPasswordOp($user_id)
    {
        $user_id = intval($user_id);
        $m_user = new um_userModel();
        $user = $m_user->getRow($user_id);
        if (!$user) {
            return new result(false, 'No User', null, errorCodesEnum::USER_NOT_EXISTS);
        }
        $user->trading_password = null;
        $user->update_time = Now();
        $user->trading_pwd_update_time = Now();
        $up = $user->update();
        if( !$up->STS ){
            return new result(false,'Set fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success');
    }


    /** 更新交易密码操作
     * @param $member_id
     * @param $password
     */
    public static function commonUpdateUserTradePassword($user_id, $password)
    {
        $user_id = intval($user_id);
        if ($user_id <= 0 || empty($password)) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_user = new um_userModel();
        $user = $m_user->getRow($user_id);
        if (!$user) {
            return new result(false, 'No User', null, errorCodesEnum::USER_NOT_EXISTS);
        }

        // 密码强度检测
        $valid = self::isValidPassword($password);
        if (!$valid) {
            return new result(false, 'Password not strong', null, errorCodesEnum::PASSWORD_NOT_STRONG);
        }

        // 更新密码
        $user->trading_password = md5($password);
        $user->trading_pwd_update_time = now();
        $up = $user->update();
        if (!$up->STS) {
            return new result(false, 'Reset fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'Setting Trading Password Success');
    }


    /**
     * 添加event
     * @param $p
     * @return result
     */
    public function addEvent($p)
    {
        $event_code = trim($p['event_code']);
        $description = trim($p['description']);
        $min_point = round($p['min_point'], 2);
        $max_point = round($p['max_point'], 2);
        $status = intval($p['status']);
        $creator_id = $p['creator_id'];
        $creator_name = $p['creator_name'];

        if (empty($event_code) || empty($description)) {
            return new result(false, 'Param Error!');
        }

        if ($max_point <= $min_point) {
            return new result(false, 'Max point must be greater than min point!');
        }

        $m_hr_point_event = M('hr_point_event');

        $ckh_code = $m_hr_point_event->find(array('event_code' => $event_code));
        if ($ckh_code) {
            return new result(false, 'Code Repeat!');
        }

        $row = $m_hr_point_event->newRow();
        $row->event_code = $event_code;
        $row->description = $description;
        $row->min_point = $min_point;
        $row->max_point = $max_point;
        $row->status = $status;
        $row->is_system = 0;
        $row->creator_id = $creator_id;
        $row->creator_name = $creator_name;
        $row->create_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Add successful!');
        } else {
            return new result(false, 'Add failed!');
        }
    }

    /**
     * 修改event
     * @param $p
     * @return result
     */
    public function editEvent($p)
    {
        $uid = intval($p['uid']);
        $event_code = trim($p['event_code']);
        $description = trim($p['description']);
        $min_point = round($p['min_point'], 2);
        $max_point = round($p['max_point'], 2);
        $status = intval($p['status']);

        if (empty($event_code) || empty($description)) {
            return new result(false, 'Param Error!');
        }

        if ($max_point <= $min_point) {
            return new result(false, 'Max point must be greater than min point!');
        }

        $m_hr_point_event = M('hr_point_event');
        $ckh_code = $m_hr_point_event->find(array('event_code' => $event_code, 'uid' => array('neq', $uid)));
        if ($ckh_code) {
            return new result(false, 'Code Repeat!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $m_hr_point_event->getRow($uid);
            if ($row->is_system == 1) {
                $conn->rollback();
                return new result(false, 'Invalid Id!');
            }
            $row->event_code = $event_code;
            $row->description = $description;
            $row->min_point = $min_point;
            $row->max_point = $max_point;
            $row->status = $status;
            $row->update_time = Now();
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Edit failed!');
            }

            if ($status == 0) {
                $r = new ormReader();
                $sql = "select hpd.uid from hr_point_depart hpd INNER JOIN hr_point_period hpp ON hpd.period_id = hpp.uid WHERE hpp.status = 0 ";
                $point_depart = $r->getRows($sql);
                if ($point_depart) {
                    $point_depart_ids = implode(',', array_column($point_depart, 'uid'));
                    $sql = "DELETE FROM hr_point_user WHERE point_event_id = $uid AND point_depart_id IN (" . $point_depart_ids . ")";
                    $rt_1 = $r->conn->execute($sql);
                    if (!$rt_1->STS) {
                        $conn->rollback();
                        return new result(false, 'Edit failed!');
                    }
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Edit successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 移除事件
     * @param $uid
     * @return result
     */
    public function deleteEvent($uid)
    {
        $m_hr_point_event = M('hr_point_event');
        $row = $m_hr_point_event->getRow($uid);
        if ($row->is_system == 1) {
            return new result(false, 'Invalid Id!');
        }

        $rt = $row->delete();
        if ($rt->STS) {
            return new result(true, 'Delete successful!');
        } else {
            return new result(true, 'Delete failed!');
        }
    }

    /**
     * 增加期间
     * @param $p
     * @return result
     */
    public function addPeriod($p)
    {
        $period = trim($p['period']);
        $end_date = date('Y-m-d', strtotime($p['end_date']));
        $creator_id = $p['creator_id'];
        $creator_name = $p['creator_name'];

        $r = new ormReader();
        $sql = "SELECT MAX(end_date) end_date FROM hr_point_period";
        $prev_end_date = $r->getOne($sql);
        if ($prev_end_date) {
            $start_date = date('Y-m-d', strtotime("$prev_end_date +1 day"));
        } else {
            $start_date = date('Y-m-d', strtotime($p['start_date']));
        }

        if ($start_date > $end_date) {
            return new result(false, 'The start date should not be greater than the end date!');
        }

        $m_hr_point_period = M('hr_point_period');
        $chk = $m_hr_point_period->find(array('period' => $period));
        if ($chk) {
            return new result(false, 'Period repeat!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $m_hr_point_period->newRow();
            $row->period = $period;
            $row->start_date = $start_date;
            $row->end_date = $end_date;
            $row->status = 0;
            $row->creator_id = $creator_id;
            $row->creator_name = $creator_name;
            $row->create_time = Now();
            $rt = $row->insert();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(true, 'Add failed!');
            }

            $m_hr_point_depart = M('hr_point_depart');
            $sql = "SELECT * FROM site_depart";
            $depart_list = $r->getRows($sql);
            foreach ($depart_list as $depart) {
                $row_1 = $m_hr_point_depart->newRow();
                $row_1->depart_id = $depart['uid'];
                $row_1->period_id = $rt->AUTO_ID;
                $row_1->status = 0;
                $rt_1 = $row_1->insert();
                if (!$rt_1->STS) {
                    $conn->rollback();
                    return new result(true, 'Add failed!');
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Add successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 修改期间
     * @param $p
     * @return result
     */
    public function editPeriod($p)
    {
        $uid = intval($p['uid']);
        $period = trim($p['period']);

        $r = new ormReader();
        $sql = "SELECT max(uid) max_uid FROM hr_point_period";
        $max_uid = $r->getOne($sql);
        if ($max_uid == $uid) {
            $end_date = date('Y-m-d', strtotime($p['end_date']));
        }

        $m_hr_point_period = M('hr_point_period');
        $chk = $m_hr_point_period->find(array('period' => $period, 'uid' => array('neq', $uid)));
        if ($chk) {
            return new result(false, 'Period repeat!');
        }

        $row = $m_hr_point_period->getRow($uid);

        if ($row->start_date > $end_date) {
            return new result(false, 'The start date should not be greater than the end date!');
        }

        $row->period = $period;

        if ($end_date) {
            $row->end_date = $end_date;
        }

        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Edit successful!');
        } else {
            return new result(true, 'Edit failed!');
        }
    }

    /**
     * 删除区间
     */
    public function deletePeriod($uid)
    {
        $r = new ormReader();
        $sql = "SELECT max(uid) max_uid FROM hr_point_period";
        $max_uid = $r->getOne($sql);
        if ($max_uid != $uid) {
            return new result(false, 'Param Error!');
        }

        $m_hr_point_period = M('hr_point_period');
        $row = $m_hr_point_period->getRow($uid);
        if ($row['status'] == 100) {
            return new result(false, 'Param Error!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt = $row->delete();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed!');
            }

            $m_hr_point_depart = M('hr_point_depart');
            $rt_1 = $m_hr_point_depart->delete(array('period_id' => $uid));
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed!');
            }

            $conn->submitTransaction();
            return new result(true, 'Delete successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 开启区间
     * @param $p
     * @return result
     */
    public function activeDepartPeriod($p)
    {
        $uid = intval($p['uid']);
        $handler_id = intval($p['handler_id']);
        $handler_name = trim($p['handler_name']);
        $m_hr_point_depart = M('hr_point_depart');
        $row = $m_hr_point_depart->getRow($uid);
        if ($row->status != 100) {
            return new result(false, 'Param Error!');
        }
        $m_hr_point_period = M('hr_point_period');
        $point_period = $m_hr_point_period->find(array('uid' => $row['period_id']));
        if ($point_period['status'] == 100) {
            return new result(false, 'Period Closed!');
        }

        $row->status = 0;
        $row->handler_id = $handler_id;
        $row->handler_name = $handler_name;
        $row->handle_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Active Successful!');
        } else {
            return new result(false, 'Active Failed!');
        }
    }

    private function chkPeriodIsProcessing($uid)
    {
        $m_hr_point_period = M('hr_point_period');
        $hr_point_period = $m_hr_point_period->getRow($uid);
        if ($hr_point_period['start_date'] <= Now()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 计算user系统积分
     * @param $p
     * @return result
     */
    public function calculateSystemPoint($p)
    {
        $uid = intval($p['uid']);
        $handler_id = intval($p['handler_id']);
        $handler_name = trim($p['handler_name']);
        $m_hr_point_depart = M('hr_point_depart');
        $point_depart = $m_hr_point_depart->find(array('uid' => $uid));
        if (!$point_depart) {
            return new result(false, 'Invalid Id!');
        }

        if (!$this->chkPeriodIsProcessing($point_depart['period_id'])) {
            return new result(false, 'Invalid Id!');
        }

        $m_site_depart = M('site_depart');
        $depart_info = $m_site_depart->find(array('uid' => $point_depart['depart_id']));
        if ($depart_info['leader'] != $handler_id && $depart_info['assistant'] != $handler_id) {
            return new result(false, 'Invalid Id!');
        }

        $m_um_user = M('um_user');
        $user_list = $m_um_user->select(array('depart_id' => $point_depart['uid'], 'user_status' => 1));

        $m_hr_point_user = M('hr_point_user');
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            foreach ($user_list as $user) {
                $system_point_arr = $this->calculateSystemPointByUser($user['uid'], $uid);
                foreach ($system_point_arr as $event_id => $point) {
                    $row = $m_hr_point_user->getRow(array('user_id' => $user['uid'], 'point_depart_id' => $uid, 'point_event_id' => $event_id));
                    if (!$row) {
                        $row = $m_hr_point_user->newRow();
                        $row->user_id = $user['uid'];
                        $row->point_depart_id = $uid;
                        $row->point_event_id = $event_id;
                        $row->point = $point;
                        $row->handler_id = $handler_id;
                        $row->handler_name = $handler_name;
                        $row->create_time = Now();
                        $rt = $row->insert();
                    } else {
                        $row->point = $point;
                        $row->update_time = Now();
                        $rt = $row->update();
                    }

                    if (!$rt->STS) {
                        $conn->rollback();
                        return new result(false, 'Calculate Failed!' . $rt->MSG);
                    }
                }
            }
            $conn->submitTransaction();
            return new result(true, 'Calculate Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * @param $user_id
     * @param $point_depart_id
     * @return array
     * todo::系统积分
     */
    private function calculateSystemPointByUser($user_id, $point_depart_id)
    {
        $m_hr_point_event = M('hr_point_event');
        $point_event = $m_hr_point_event->select(array('is_system' => 1, 'status' => 100));
        $point_arr = array();
        foreach ($point_event as $event) {
            $point_arr[$event['uid']] = 10;
        }
        return $point_arr;
    }

    /**
     * 自定义项评分
     * @param $p
     * @return result
     */
    public function evaluateUserPoint($p)
    {
        $depart_period_id = intval($p['depart_period']);
        $user_id = intval($p['user_id']);
        $event_id = intval($p['event_id']);
        $score = round($p['score'], 1);
        $handler_id = intval($p['handler_id']);
        $handler_name = trim($p['handler_name']);

        if ($depart_period_id <= 0 || $user_id <= 0 || $event_id <= 0 || $score < 0) {
            return new result(false, "Param Error!");
        }

        $m_hr_point_depart = M('hr_point_depart');
        $point_depart = $m_hr_point_depart->find(array('uid' => $depart_period_id));
        if (!$point_depart) {
            return new result(false, 'Invalid Id!');
        }

        if (!$this->chkPeriodIsProcessing($point_depart['period_id'])) {
            return new result(false, 'Invalid Id!');
        }

        $m_site_depart = M('site_depart');
        $depart_info = $m_site_depart->find(array('uid' => $point_depart['depart_id']));
        if ($depart_info['leader'] != $handler_id && $depart_info['assistant'] != $handler_id) {
            return new result(false, 'Invalid Id!');
        }

        $m_hr_point_event = M('hr_point_event');
        $point_event = $m_hr_point_event->find(array('uid' => $event_id));
        if (!$point_event) {
            return new result(false, 'Invalid Event Id!');
        }

        $point = round($point_event['max_point'] * $score / 5, 2);


        $m_hr_point_user = M('hr_point_user');
        $row = $m_hr_point_user->getRow(array('user_id' => $user_id, 'point_depart_id' => $depart_period_id, 'point_event_id' => $event_id));
        if (!$row) {
            $row = $m_hr_point_user->newRow();
            $row->user_id = $user_id;
            $row->point_depart_id = $depart_period_id;
            $row->point_event_id = $event_id;
            $row->point = $point;
            $row->rate_score = $score;
            $row->handler_id = $handler_id;
            $row->handler_name = $handler_name;
            $row->create_time = Now();
            $rt = $row->insert();
        } else {
            $row->point = $point;
            $row->rate_score = $score;
            $row->update_time = Now();
            $rt = $row->update();
        }

        if (!$rt->STS) {
            return new result(false, 'Evaluate Failed!');
        }

        $r = new ormReader();
        $sql = "select SUM(point) point_total from hr_point_user WHERE user_id = $user_id AND point_depart_id = $depart_period_id";
        $point_total = $r->getOne($sql);
        return new result(true, 'Evaluate Successful!', array('point_total' => ncPriceFormat($point_total), 'point' => ncPriceFormat($point), 'score' => $score));
    }

    /**
     * 关闭
     * @param $p
     * @return result
     */
    public function closeDepartPeriod($p)
    {
        $uid = intval($p['uid']);
        $handler_id = intval($p['handler_id']);
        $handler_name = trim($p['handler_name']);
        $m_hr_point_depart = M('hr_point_depart');
        $point_depart = $m_hr_point_depart->getRow(array('uid' => $uid));
        if (!$point_depart) {
            return new result(false, 'Invalid Id!');
        }

        if (!$this->chkPeriodIsProcessing($point_depart['period_id'])) {
            return new result(false, 'Invalid Id!');
        }

        $m_site_depart = M('site_depart');
        $depart_info = $m_site_depart->find(array('uid' => $point_depart['depart_id']));
        if ($depart_info['leader'] != $handler_id && $depart_info['assistant'] != $handler_id) {
            return new result(false, 'Invalid Id!');
        }

        $m_um_user = M('um_user');
        $user_list = $m_um_user->select(array('depart_id' => $point_depart['uid'], 'user_status' => 1));

        $m_hr_point_event = M('hr_point_event');
        $point_event = $m_hr_point_event->select(array('status' => 100));

        $m_hr_point_user = M('hr_point_user');
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            foreach ($user_list as $user) {
                foreach ($point_event as $event) {
                    $row = $m_hr_point_user->getRow(array('user_id' => $user['uid'], 'point_depart_id' => $uid, 'point_event_id' => $event['uid']));
                    if (!$row) {
                        $row = $m_hr_point_user->newRow();
                        $row->user_id = $user['uid'];
                        $row->point_depart_id = $uid;
                        $row->point_event_id = $event['uid'];
                        $row->point = 0;
                        $row->handler_id = $handler_id;
                        $row->handler_name = $handler_name;
                        $row->create_time = Now();
                        $rt = $row->insert();
                        if (!$rt->STS) {
                            $conn->rollback();
                            return new result(false, 'Insert Failed!' . $rt->MSG);
                        }
                    }
                }
            }

            $point_depart->status = 100;
            $point_depart->handler_id = $handler_id;
            $point_depart->handler_name = $handler_id;
            $point_depart->handle_time = Now();
            $rt = $point_depart->update();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Update Failed!' . $rt->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Close Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 新添加部门增加point区间
     * @param $depart_id
     * @return ormResult|result
     */
    public function addPointDepartPeriodByDepartId($depart_id)
    {
        $r = new ormReader();
        $sql = "select uid from hr_point_period WHERE end_date > '" . Now() . "'";
        $point_period = $r->getRows($sql);

        $m_hr_point_depart = M('hr_point_depart');
        foreach ($point_period as $val) {
            $row = $m_hr_point_depart->newRow();
            $row->depart_id = $depart_id;
            $row->period_id = $val['uid'];
            $row->status = 0;
            $rt = $row->insert();
            if (!$rt->STS) {
                return $rt;
            }
        }

        return new result(true);
    }

    public static function getGUID($userId, $return_account = false)
    {
        $user_model = new um_userModel();
        $user_info = $user_model->getRow($userId);
        if (!$user_info) throw new Exception("User $userId not found");

        if (!$user_info->obj_guid) {
            $user_info->obj_guid = generateGuid($user_info->uid, objGuidTypeEnum::UM_USER);
            $ret = $user_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for user failed - " . $ret->MSG);
            }
        }
        if ($return_account) {
            return $user_info->toArray();
        } else {
            return $user_info->obj_guid;
        }
    }

    /** 获取账户余额
     * @param $user_id
     * @return array
     */
    public static function getPassbookBalanceOfUser($user_id)
    {
        $userObj = new objectUserClass($user_id);
        return $userObj->getPassbookBalance();
    }

    /** 获取所有货币账户的详细
     * @param $user_id
     * @return array
     */
    public static function getPassbookAccountAllCurrencyDetailOfUser($user_id)
    {
        $userObj = new objectUserClass($user_id);
        return $userObj->getAccountAllCurrencyDetail();
    }


    public static function getUserPassbookFlowByType($user_id, $currency, $page_num, $page_size, $type = null)
    {
        $userObj = new objectUserClass($user_id);
        $passbook = $userObj->getUserPassbook();
        $page_num = $page_num ?: 1;
        $page_size = $page_size ?: 100000;
        $m_flow = new passbook_account_flowModel();
        $page_list = $m_flow->getPassbookFlowByType($passbook, $currency, $page_num, $page_size, $type);
        $list = $page_list->rows;

        $trading_type_lang = enum_langClass::getPassbookTradingTypeLang();
        foreach ($list as $k => $v) {
            $v['trading_type_lang'] = $trading_type_lang[$v['trading_type']];
            $list[$k] = $v;
        }

        return array(
            'total_num' => $page_list->count,
            'total_pages' => $page_list->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $list
        );

    }

    /** 获取账户流水
     * @param $user_id
     * @param $start_date
     * @param $end_date
     * @param string $currency
     * @param string $biz_type
     * @return ormCollection
     */
    public static function getCashFlowOfUser($user_id, $start_date, $end_date, $currency = '', $biz_type = '')
    {
        $userObj = new objectUserClass($user_id);
        $passbook = $userObj->getUserPassbook();
        $passbook_id = $passbook->getBookId();

        $start_date = date('Y-m-d 00:00:00', strtotime($start_date));
        $end_date = date('Y-m-d 23:59:59', strtotime($end_date));
        $where = " acc.book_id='$passbook_id' and  af.create_time>='$start_date' and af.create_time<='$end_date' ";
        if ($currency) {
            $where .= " and acc.currency='$currency' ";
        }
        if ($biz_type) {
            $where .= " and t.trading_type='$biz_type' ";
        }
        $r = new ormReader();
        $sql = "select af.*,date_format(af.create_time,'%Y-%m') date_month,acc.currency,t.category,t.trading_type,t.subject from passbook_account_flow af left join passbook_trading t on t.uid=af.trade_id  
        left join passbook_account acc on acc.uid=af.account_id where  $where  order by af.create_time desc ";

        $list = $r->getRows($sql);
        return $list;
    }


    /** 登陆成功
     * @param $user_id
     * @param $client_type
     * @return result
     */
    public static function userLoginSuccess($user_id, $client_type)
    {
        $m_user = new um_userModel();
        $user = $m_user->getRow($user_id);
        if (!$user) {
            return new result(false, 'User not exist', null, errorCodesEnum::USER_NOT_EXISTS);
        }

        // 创建登陆日志
        $now = Now();
        $ip = getIp();
        $user->last_login_time = $now;
        $user->last_login_ip = $ip;
        $up = $user->update();
        if (!$up->STS) {
            return new result(false, 'Db error', null, errorCodesEnum::DB_ERROR);
        }


        $m_user_log = new um_user_logModel();
        $re = $m_user_log->recordLogin($user->uid, $client_type);
        if (!$re->STS) {
            return new result(false, 'Db error', null, errorCodesEnum::DB_ERROR);
        }

        // 删掉无效token(单一设备登陆支持)
        $sql = "delete from um_user_token where user_id='" . $user->uid . "' ";
        $del = $m_user_log->conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Login fail', null, errorCodesEnum::DB_ERROR);
        }

        // 创建新token
        $token = md5($user->user_code . time());
        $m_user_token = new um_user_tokenModel();
        $user_token = $m_user_token->newRow();
        $user_token->user_id = $user->uid;
        $user_token->user_code = $user->user_code;
        $user_token->token = $token;
        $user_token->client_type = $client_type;
        $user_token->create_time = $now;
        $user_token->login_time = $now;
        $insert = $user_token->insert();
        if (!$insert->STS) {
            return new result(false, 'Db error', null, errorCodesEnum::DB_ERROR);
        }

        $user_info = $user->toArray();
        $user_info['is_set_trading_password'] = $user_info['trading_password'] ? 1 : 0;
        unset($user_info['password']);
        unset($user_info['trading_password']);
        return new result(true, 'success', array(
            'user_info' => $user_info,
            'token' => $token
        ));
    }


    public static function getUserBoundMemberLoanContractList($user_id, $page_num, $page_size)
    {
        $r = new ormReader();
        $page_num = $page_num ?: 1;
        $page_size = $page_size ?: 10000;

        $sql = "select c.*,m.obj_guid member_guid,m.login_code,m.display_name,m.kh_display_name from member_follow_officer o inner join client_member m on m.uid=o.member_id 
        inner join loan_account a on a.obj_guid=m.obj_guid inner join loan_contract c on c.account_id=a.uid 
        where o.officer_id='$user_id' and o.is_active='1' and c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "' order by c.create_time desc ";

        $list = $r->getPage($sql, $page_num, $page_size);

        return array(
            'total_num' => $list->count,
            'total_pages' => $list->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $list->rows
        );

    }


    public static function getUserFootPrintOfDay($user_id, $day)
    {
        $r = new ormReader();
        $sql = "select * from um_user_track where user_id='$user_id' and sign_day='$day' order by sign_time asc";
        $lists = $r->getRows($sql);
        return $lists;
    }

    public static function getUserFootprintPageListGroupByDay($user_id, $page_num, $page_size)
    {
        $r = new ormReader();
        $sql = "select  *,DATE_FORMAT(sign_time,'%Y-%m') sign_month from um_user_track where user_id='$user_id' group by sign_day order by sign_time desc ";
        $re = $r->getPage($sql, $page_num, $page_size);
        return array(
            'total_num' => $re->count,
            'total_pages' => $re->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $re->rows
        );
    }

    public static function getUserBaseInfo($user_id)
    {
        if ($user_id <= 0) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m = new um_userModel();
        $info = $m->getRow($user_id);
        if (!$info) {
            return new result(false, 'No user ', null, errorCodesEnum::USER_NOT_EXISTS);
        }
        $info = $info->toArray();

        $info['is_setting_trading_password'] = $info['trading_password'] ? 1 : 0;
        unset($info['password']);
        unset($info['trading_password']);

        $info['user_image'] = getImageUrl($info['user_image']);
        $info['user_icon'] = getImageUrl($info['user_icon']);

        $userObj = new objectUserClass($user_id);
        $balance = $userObj->getPassbookBalance();

        $info['branch_id'] = $userObj->branch_id;
        $info['branch_name'] = $userObj->branch_name;

        return new result(true, 'success', array(
            'user_info' => $info,
            'balance' => $balance
        ));

    }

    public static function getOneAssetEvaluateHistoryForMember($user_id, $member_asset_id)
    {
        $r = new ormReader();
        $sql = "select * from member_assets_evaluate where member_assets_id='$member_asset_id' and operator_id='$user_id' 
        order by evaluate_time desc ";
        $list = $r->getRows($sql);
        return $list;
    }

    public static function getMemberAllAssetsEvaluationOfUser($member_id, $user_id)
    {
        $r = new ormReader();

        // 列出所有资产，用left join
        $sql = "select a.uid,a.asset_name,a.asset_type,a.mortgage_state,e.evaluation,e.evaluation valuation,e.remark from member_assets a 
        left join ( select * from (select * from member_assets_evaluate 
        where operator_id='$user_id'  order by member_assets_id desc,evaluate_time desc) x group by x.member_assets_id ) e  
        on a.uid=e.member_assets_id where a.asset_state >='" . assetStateEnum::CERTIFIED . "' and a.member_id='$member_id'
          order by a.uid desc";
        return $r->getRows($sql);

    }

    public static function getMemberCreditReferenceInfo($member_id, $user_id)
    {
        $member = (new memberModel())->getRow($member_id);
        if (!$member) {
            return new result(false, 'Member not exists.', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 收入的调查
        $m_income = new member_income_researchModel();
        $row = $m_income->orderBy('research_time desc')->find(array(
            'member_id' => $member_id,
            'operator_id' => $user_id
        ));
        $total_income = null;
        if ($row) {
            $total_income = ($row['income_rental_land'] + $row['income_rental_housing'] + $row['income_business']
                + $row['income_salary'] + $row['income_others']);
        }

        $asset_list = self::getMemberAllAssetsEvaluationOfUser($member_id, $user_id);
        $assets_total = 0;
        foreach ($asset_list as $v) {
            $assets_total += $v['evaluation'];
        }

        $rate_set = global_settingClass::getCreditGrantRateAndDefaultInterest();
        //获取最后的行业内调查数据
        $sql = "SELECT a.`credit_rate`,a.`industry_name`,b.`profit` FROM common_industry a INNER JOIN common_industry_research b ON a.uid=b.`industry_id` WHERE b.`research_id`='" . $row['uid'] . "' ";
        $industry_list = $m_income->reader->getRows($sql);

        // 最后一次的提交记录
        $m_suggest = new member_credit_suggestModel();
        $last_suggest = $m_suggest->orderBy('uid desc')->find(array(
            'member_id' => $member_id,
            'operator_id' => $user_id
        ));
        if ($last_suggest) {
            $sql = "select e.*,a.asset_type,a.mortgage_state from member_credit_suggest_detail e left join member_assets a on a.uid=e.member_asset_id 
            where e.credit_suggest_id='" . $last_suggest['uid'] . "' ";
            $list = $m_suggest->reader->getRows($sql);
            $last_suggest['suggest_detail_list'] = $list;
            $sql = "select * from member_credit_suggest_rate where credit_suggest_id='" . $last_suggest['uid'] . "'";
            $list = $m_suggest->reader->getRows($sql);
            $last_suggest['suggest_rate_list'] = $list ?: array();
        }


        return new result(true, 'success', array(
            'total_income' => $total_income,
            'total_assets_evaluation' => $assets_total,
            'asset_list' => $asset_list,
            "income_list" => $row,
            "industry_list" => $industry_list ?: array(),
            'credit_system_rate' => $rate_set,
            'last_submit_suggest' => $last_suggest
        ));
    }


    public static function getMemberCreditSuggestHistoryByUser($member_id, $user_id)
    {
        $m = new member_credit_suggestModel();
        $rows = $m->orderBy('uid desc')->getRows(array(
            'member_id' => $member_id,
            'operator_id' => $user_id
        ));
        $list = $rows->toArray();
        return $list;
    }


    public static function editMemberResidencePlace($params)
    {
        $officer_id = intval($params['officer_id']);
        $member_id = $params['member_id'];
        $member = (new memberModel())->getRow($member_id);
        if (!$member) {
            return new result(false, 'Member not exist', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $m_address = new common_addressModel();

        $type = addressCategoryEnum::MEMBER_RESIDENCE_PLACE;
        $id1 = intval($params['id1']);
        $id2 = intval($params['id2']);
        $id3 = intval($params['id3']);
        $id4 = intval($params['id4']);
        $address_detail = $params['address_detail'];
        $street = $params['street'];
        $house_number = $params['house_number'];
        $address_group = $params['address_group'];
        $full_text = $params['full_text'];
        $cord_x = round($params['cord_x'], 6);
        $cord_y = round($params['cord_y'], 6);

        $ids_arr= array($id1,$id2,$id3,$id4);
        $sql = "select * from core_tree where uid in (".join(',',$ids_arr).") ";
        $tree_arr = $m_address->reader->getRows($sql);
        $tree_arr = resetArrayKey($tree_arr,'uid');



        // 更新原来的为历史记录
        $sql = "update common_address set state='" . addressStateEnum::INACTIVE . "' where obj_type='" . objGuidTypeEnum::CLIENT_MEMBER . "'
        and obj_guid='" . $member->obj_guid . "' and address_category='$type' ";
        $up = $m_address->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Update history data fail.', null, errorCodesEnum::DB_ERROR);
        }

        // 插入新记录
        $new_row = $m_address->newRow();
        $new_row->obj_type = objGuidTypeEnum::CLIENT_MEMBER;
        $new_row->obj_guid = $member->obj_guid;
        $new_row->address_category = $type;
        $new_row->id1 = $id1;
        $new_row->id1_text = $tree_arr[$id1]['node_text'];
        $new_row->id1_text_json = $tree_arr[$id1]['node_text_alias'];
        $new_row->id2 = $id2;
        $new_row->id2_text = $tree_arr[$id2]['node_text'];
        $new_row->id2_text_json = $tree_arr[$id2]['node_text_alias'];
        $new_row->id3 = $id3;
        $new_row->id3_text = $tree_arr[$id3]['node_text'];
        $new_row->id3_text_json = $tree_arr[$id3]['node_text_alias'];
        $new_row->id4 = $id4;
        $new_row->id4_text = $tree_arr[$id4]['node_text'];
        $new_row->id4_text_json = $tree_arr[$id4]['node_text_alias'];
        $new_row->coord_x = $cord_x;
        $new_row->coord_y = $cord_y;
        $new_row->address_detail = $address_detail;
        $new_row->street = $street;
        $new_row->house_number = $house_number;
        $new_row->address_group = $address_group;
        $new_row->full_text = $full_text;
        $new_row->create_time = Now();
        $new_row->state = addressStateEnum::ACTIVE;
        $insert = $new_row->insert();
        if (!$insert->STS) {
            return new result(false, 'Add fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $new_row);

    }


    public static function editMemberResidencePlaceMapInfo($params)
    {
        $officer_id = intval($params['officer_id']);
        $member_id = $params['member_id'];
        $member = (new memberModel())->getRow($member_id);
        if (!$member) {
            return new result(false, 'Member not exist', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $location = $params['location'];
        $cord_x = round($params['cord_x'], 6);
        $cord_y = round($params['cord_y'], 6);

        $officer = new objectUserClass($officer_id);

        $m_map = new member_address_map_detailModel();
        $map = $m_map->newRow();
        $map->address_type = addressCategoryEnum::MEMBER_RESIDENCE_PLACE;
        $map->member_id = $member_id;
        $map->coord_x = $cord_x;
        $map->coord_y = $cord_y;
        $map->location = $location;
        $map->user_id = $officer_id;
        $map->user_name = $officer->user_name;
        $map->update_time = Now();
        $insert = $map->insert();
        if (!$insert->STS) {
            return new result(false, 'Add fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $map);
    }


    public static function changeLoginPasswordByOldPassword($user_id, $old_pwd, $new_pwd)
    {
        $user_id = intval($user_id);
        if (!$user_id || !$old_pwd || !$new_pwd) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $user = (new um_userModel())->getRow($user_id);
        if (!$user) {
            return new result(false, 'User not exist', null, errorCodesEnum::USER_NOT_EXISTS);
        }

        if ($user->password != md5($old_pwd)) {
            return new result(false, 'Password error', null, errorCodesEnum::PASSWORD_ERROR);
        }

        if ($user->password == md5($new_pwd)) {
            return new result(false, 'Same password', null, errorCodesEnum::SAME_PASSWORD);
        }

        $user->password = md5($new_pwd);
        $user->update_time = Now();
        $up = $user->update();
        if (!$up->STS) {
            return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');
    }

    public static function verifyLoginPassword($user_id, $password)
    {
        $m_user = new um_userModel();
        $user = $m_user->getRow($user_id);
        if (!$user) {
            return 0;
        }
        $is_right = 0;
        if ($user->password == md5($password)) {
            $is_right = 1;
        }

        return $is_right;
    }


    public static function clearGesturePassword($user_id)
    {

        $m_user = new um_userModel();
        $user = $m_user->getRow($user_id);
        if (!$user) {
            return new result(false, 'User not exist', null, errorCodesEnum::USER_NOT_EXISTS);
        }

        $user->gesture_password = null;
        $user->update_time = Now();
        $up = $user->update();
        if (!$up->STS) {
            return new result(false, 'Set fail', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success', array(
            'gesture_password' => null
        ));
    }

    public static function setTradingPassword($user_id, $login_password, $trading_password)
    {
        if (!$user_id || !$login_password || !$trading_password) {
            return new result(false, 'Invalid param.', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_user = new um_userModel();
        $user = $m_user->getRow($user_id);
        if (!$user) {
            return new result(false, 'User not exist', null, errorCodesEnum::USER_NOT_EXISTS);
        }
        if ($user->password != md5($login_password)) {
            return new result(false, 'Login password error.', null, errorCodesEnum::PASSWORD_ERROR);
        }
        $user->trading_password = md5($trading_password);
        $user->update_time = Now();
        $user->trading_pwd_update_time = Now();
        $up = $user->update();
        if (!$up->STS) {
            return new result(false, 'Set fail.', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success', array(
            'trading_password' => $trading_password
        ));
    }

    public static function setGesturePassword($user_id, $gesture_password)
    {
        $m_user = new um_userModel();
        $user = $m_user->getRow($user_id);
        if (!$user) {
            return new result(false, 'User not exist', null, errorCodesEnum::USER_NOT_EXISTS);
        }
        if (!$gesture_password) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $user->gesture_password = $gesture_password;
        $user->update_time = Now();
        $up = $user->update();
        if (!$up->STS) {
            return new result(false, 'Set fail', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success', array(
            'gesture_password' => $gesture_password
        ));
    }

    public static function setFingerprintPassword($user_id, $fingerprint_password)
    {
        $user_id = intval($user_id);
        $m_user = new um_userModel();
        $user = $m_user->getRow($user_id);
        if (!$user) {
            return new result(false, 'User not exist', null, errorCodesEnum::USER_NOT_EXISTS);
        }

        if (!$fingerprint_password) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $user->fingerprint_password = $fingerprint_password;
        $user->update_time = Now();
        $up = $user->update();
        if (!$up->STS) {
            return new result(false, 'Set fail', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success', array(
            'fingerprint_password' => $fingerprint_password
        ));
    }

    public static function getMemberIncomeResearchHistoryOfUser($member_id, $user_id)
    {
        $m = new member_income_researchModel();
        $rows = $m->orderBy('research_time desc')->getRows(array(
            'member_id' => $member_id,
            'operator_id' => $user_id
        ));
        $list = $rows->toArray();
        return $list;
    }

    public static function addIcCard($userId, $cardNo)
    {
        $card_model = new common_ic_cardModel();
        $card_info = $card_model->getRow(array('card_no' => $cardNo));
        if (!$card_info) return new result(false, 'Card not found', null, errorCodesEnum::IC_CARD_NOT_FOUND);
        if ($card_info->expire_time < time()) return new result(false, 'Card expired', null, errorCodesEnum::IC_CARD_EXPIRED);

        $user_card_model = new um_user_cardModel();
        $bind_info = $user_card_model->getRow(array(
            'card_no' => $cardNo,
            'state' => 1
        ));
        if ($bind_info) {
            //要去找到具体的人，提示才友好
            $old_user_id = $bind_info->user_id;
            $um_user = new um_userModel();
            $old_user = $um_user->find(array("uid" => $old_user_id));
            if ($old_user) {
                $str_old_user = $old_user['user_code'] . "【" . $old_user['user_name'] . "】";
                return new result(false, 'The card has been bound to user: ' . $str_old_user, null, errorCodesEnum::IC_CARD_BOUND);
            } else {
                //删除
                $ret_delete = $bind_info->delete();
            }
        }

        $bind_info = $user_card_model->newRow();
        $bind_info->user_id = $userId;
        $bind_info->card_no = $cardNo;
        $bind_info->state = 1;
        $bind_info->create_time = date("Y-m-d H:i:s");
        $bind_info->update_time = date("Y-m-d H:i:s");
        $ret = $bind_info->insert();

        if (!$ret->STS)
            return new result(false, $ret->MSG, null, errorCodesEnum::DB_ERROR);
        else
            return new result(true);
    }

    public static function deleteBoundIcCard($bindId)
    {
        $user_card_model = new um_user_cardModel();
        $bind_info = $user_card_model->getRow($bindId);
        if (!$bind_info) return new result(false, 'Bind info not found', null, errorCodesEnum::UNEXPECTED_DATA);

        $bind_info->state = 0;
        $bind_info->update_time = date("Y-m-d H:i:s");
        $ret = $bind_info->update();

        if (!$ret->STS)
            return new result(false, $ret->MSG, null, errorCodesEnum::DB_ERROR);
        else
            return new result(true);
    }


    public static function getMemberAllResearchByUser($user_id, $member_id,$include_all=true)
    {

        $data = array();

        $r = new ormReader();
        $memberObj = new objectMemberClass($member_id);

        // information
        $work_type = $memberObj->work_type;
        $residence_place = (new common_addressModel())->getMemberResidencePlaceByGuid($memberObj->object_id);
        if ($work_type && $residence_place) {
            $data['information']['is_done'] = 1;
        } else {
            $data['information']['is_done'] = 0;
        }

        // verification file
        $sql = "select auditor_time,create_time from member_verify_cert where member_id='$member_id' and 
        verify_state='" . certStateEnum::PASS . "' order by uid desc ";
        $row = $r->getRow($sql);
        if ($row) {
            $data['verification_file'] = array(
                'is_submit' => 1,
                'last_time' => $row['auditor_time'] ?: $row['create_time']
            );
        } else {
            $data['verification_file'] = array(
                'is_submit' => 0,
                'last_time' => null
            );
        }

        // 资产总数
        $data['total_asset_num'] = member_statisticsClass::getMemberTotalAssetNum($member_id);

        // assets evaluate
        $sql = "select evaluate_time from member_assets_evaluate where member_id='$member_id' and operator_id='$user_id' 
        order by uid desc ";
        $last_time = $r->getOne($sql);
        if ($last_time) {
            $data['assets_evaluate'] = array(
                'is_submit' => 1,
                'last_time' => $last_time
            );
        } else {
            $data['assets_evaluate'] = array(
                'is_submit' => 0,
                'last_time' => null
            );
        }

        // 合计工资收入
        $data['total_income_salary'] = member_statisticsClass::getMemberTotalIncomeSalary($member_id,$include_all);

        // 合计商业收入(多店铺形式)
        $data['total_income_business'] = memberBusinessClass::getMemberBusinessTotalIncomeOfOfficer($member_id,$user_id,$include_all);

        // 合计其他收入
        $data['total_income_attachment'] = member_statisticsClass::getMemberTotalOtherAttachmentIncome($member_id);

        // business scene
        /* $sql = "select create_time from member_business_photo where `type`='" . businessPhotoTypeEnum::PLACE_SCENE . "' and member_id='$member_id' and operator_id='$user_id' order by uid desc ";
         $last_time = $r->getOne($sql);
         if ($last_time) {
             $data['business_scene'] = array(
                 'is_submit' => 1,
                 'last_time' => $last_time
             );
         } else {
             $data['business_scene'] = array(
                 'is_submit' => 0,
                 'last_time' => null
             );
         }*/

        // business contract photo
        /*$sql = "select create_time from member_business_photo where `type`='" . businessPhotoTypeEnum::CONTRACT . "' and member_id='$member_id' and operator_id='$user_id' order by uid desc ";
        $last_time = $r->getOne($sql);
        if ($last_time) {
            $data['business_contract_photo'] = array(
                'is_submit' => 1,
                'last_time' => $last_time
            );
        } else {
            $data['business_contract_photo'] = array(
                'is_submit' => 0,
                'last_time' => null
            );
        }*/

        // income research
        /* $sql = "select research_time from member_income_research where member_id='$member_id' and operator_id='$user_id' order by uid desc ";
         $last_time = $r->getOne($sql);
         if ($last_time) {
             $data['income_research'] = array(
                 'is_submit' => 1,
                 'last_time' => $last_time
             );
         } else {
             $data['income_research'] = array(
                 'is_submit' => 0,
                 'last_time' => null
             );
         }*/

        // suggest for credit
        $sql = "select * from member_credit_suggest where member_id='$member_id' and operator_id='$user_id' order by uid desc ";
        $last_suggest = $r->getRow($sql);  // request_time
        if ($last_suggest) {
            $data['suggest_credit'] = array(
                'is_submit' => 1,
                'last_time' => $last_suggest['request_time']
            );
        } else {
            $data['suggest_credit'] = array(
                'is_submit' => 0,
                'last_time' => null
            );
        }

        return $data;

    }

    public static function getBranchTellerListOfBranch($branch_id)
    {
        $r = new ormReader();
        $sql = "select u.*,d.branch_id from um_user u left join site_depart d on u.depart_id=d.uid where d.branch_id='$branch_id' 
        and u.user_position='" . userPositionEnum::TELLER . "' and u.user_status=1 ";
        return $r->getRows($sql);
    }

    public static function getBranchTellerListOfUser($user_id)
    {
        $userObj = new objectUserClass($user_id);
        $branch_id = $userObj->branch_id;
        return self::getBranchTellerListOfBranch($branch_id);
    }

    public static function getBranchLimit($branch_id, $limit_key)
    {
        $m_site_branch_limit = M('site_branch_limit');
        $limit_info = $m_site_branch_limit->find(array('branch_id' => $branch_id, 'limit_key' => $limit_key));
        $all_currency = (new currencyEnum())->toArray();
        $limit_arr = array();
        if (!$limit_info) {
            foreach ($all_currency as $key => $currency) {
                $limit_arr[$key] = 'Not limit';
            }
        } else {
            foreach ($all_currency as $key => $currency) {
                $exchange_rate = global_settingClass::getCurrencyRateBetween(currencyEnum::USD, $key);
                if ($exchange_rate <= 0) {
                    return new result(false, 'Not set currency exchange rate:' . $currency . '-' . $currency);
                }

                if ($limit_info['max_per_day'] < 0) {
                    $limit_arr[$key] = 'Not limit';
                } else {
                    $limit_arr[$key] = ncPriceFormat(round($exchange_rate * $limit_info['max_per_day'], 2));
                }
            }
        }

        return $limit_arr;
    }

    /**
     * co /customer service / operator
     */
    public function getCoList($branch_id)
    {
        $r = new ormReader();
        $sql = "SELECT uu.* FROM um_user uu INNER JOIN site_depart sd ON uu.depart_id = sd.uid WHERE sd.branch_id = '" . $branch_id . "' AND uu.user_status = 1 AND uu.user_position = '" . userPositionEnum::CREDIT_OFFICER . "'";
        $co_list = $r->getRows($sql);
        return $co_list;
    }

    /*
     * 跨域登陆,token_passport是密码两次md5的结果
     *
     */
    public static function autoLoginByCrossDomain($token_uid, $token_passport)
    {

        if (empty($token_uid)) {
            return new result(false, 'The account cannot be empty!');
        }

        if (empty($token_passport)) {
            return new result(false, 'The password cannot be empty!');
        }

        $m_um_user = M('um_user');
        $user = $m_um_user->getRow(array(
            "uid" => $token_uid,
        ));

        if (empty($user)) {
            return new result(false, 'Account error!');
        }

        if ($user->user_status == 0) {
            return new result(false, 'Deactivated account!');
        }

        if (empty($user) || md5($user->password) != $token_passport) {
            return new result(false, 'Password error!');
        }


        $data_update = array(
            'uid' => $user->uid,
            'last_login_time' => Now(),
            'last_login_ip' => getIp()
        );
        $m_um_user->update($data_update);
        $user_arr = $user->toArray();

        $m_site_depart = M('site_depart');
        $depart = $m_site_depart->find(array('uid' => $user_arr['depart_id']));
        $user_arr['branch_id'] = $depart['branch_id'];

        setSessionVar("user_info", $user_arr);
        setSessionVar("is_login", "ok");

        $m_um_user_log = M('um_user_log');
        $m_um_user_log->recordLogin($user->uid, 'web');


        return new result(true, '', array('new_url' => ENTRY_DESKTOP_SITE_URL . DS . 'index.php'));
    }


    /** 解除ACE绑定
     * @param $handler_id
     * @param $user_id
     * @param null $code
     * @return result
     */
    public static function unbindAceAccountForClient($handler_id, $user_id, $code = null)
    {
        $userObj = new objectUserClass($user_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return new result(false, 'User not access.', null, errorCodesEnum::UN_MATCH_OPERATION);
        }

        $m_handler = new member_account_handlerModel();
        $handler = $m_handler->getRow($handler_id);
        if (!$handler) {
            return new result(false, 'No handler info.', null, errorCodesEnum::NO_DATA);
        }

        // 解除开始
        $rt = asiaweiluyClass::aceUnbindStart($handler->handler_account);
        if (!$rt->STS) {
            // 某些特殊的错误，认为是成功的，比如非member，非绑定member这种在ACE不存在了的情况
            $rt_code = $rt->CODE;
            if ($rt_code == errorCodesEnum::ACE_NOT_SIGNED_MEMBER || $rt_code == errorCodesEnum::NOT_ACE_MEMBER) {
                // 更新状态
                $handler->state = accountHandlerStateEnum::HISTORY;
                $handler->update_time = Now();
                $up = $handler->update();
                if (!$up->STS) {
                    return new result(false, 'Unbind fail!Db error.', null, errorCodesEnum::DB_ERROR);
                }

                // 记录日志
                $m_log = new user_unbind_client_handlerModel();
                $log = $m_log->newRow();
                $log->member_id = $handler->member_id;  // handler_type  handler_account
                $log->handler_id = $handler->uid;
                $log->handler_type = $handler->handler_type;
                $log->handler_account = $handler->handler_account;
                $log->operator_id = $userObj->user_id;
                $log->operator_name = $userObj->user_name;
                $log->time = Now();
                $insert = $log->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add log fail.', null, errorCodesEnum::DB_ERROR);
                }

                return new result(true, 'Success');
            }
            return $rt;
        }
        $data = $rt->DATA;
        $verify_id = $data['verify_id'];

        // 解除结束
        $rt = asiaweiluyClass::aceUnbindFinish($verify_id, $code);
        if (!$rt->STS) {
            return $rt;
        }
        $re_data = $rt->DATA;
        if (!$re_data['is_success']) {
            return new result(false, 'Unbind fail.', null, errorCodesEnum::UNEXPECTED_DATA);
        }

        // 更新状态
        $handler->state = accountHandlerStateEnum::HISTORY;
        $handler->update_time = Now();
        $up = $handler->update();
        if (!$up->STS) {
            return new result(false, 'Unbind fail!Db error.', null, errorCodesEnum::DB_ERROR);
        }

        // 记录日志
        $m_log = new user_unbind_client_handlerModel();
        $log = $m_log->newRow();
        $log->member_id = $handler->member_id;  // handler_type  handler_account
        $log->handler_id = $handler->uid;
        $log->handler_type = $handler->handler_type;
        $log->handler_account = $handler->handler_account;
        $log->operator_id = $userObj->user_id;
        $log->operator_name = $userObj->user_name;
        $log->time = Now();
        $insert = $log->insert();
        if (!$insert->STS) {
            return new result(false, 'Add log fail.', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');
    }


    public static function chkSystemIsClose($user_position)
    {
        if ($user_position == userPositionEnum::DEVELOPER) {
            return false;
        }
        switch ($user_position) {
            case userPositionEnum::BRANCH_MANAGER:
                $system_key = 'system_close_branch_manager';
                break;
            case userPositionEnum::OPERATOR:
                $system_key = 'system_close_operator';
                break;
            case userPositionEnum::CHIEF_TELLER:
            case userPositionEnum::TELLER:
            case userPositionEnum::CUSTOMER_SERVICE:
                $system_key = 'system_close_counter';
                break;
            default:
                $system_key = 'system_close_console';
        }

        $m_core_dictionary = M('core_dictionary');
        $row = $m_core_dictionary->getRow(array('dict_key' => $system_key));
        if (!$row) {
            return false;
        }
        $value = my_json_decode($row['dict_value']);
        if ($value['state'] == 1) {
            return false;
        } else {
            return true;
        }

    }

    public function editUserCredit($param)
    {
        $cashier_id = intval($param['cashier_id']);
        $credit = round($param['credit'], 2);
        $remark = trim($param['remark']);
        $operator_id = intval($param['operator_id']);
        $operator_name = trim($param['operator_name']);
        $m_um_user = M('um_user');
        $row = $m_um_user->getRow($cashier_id);
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
            $row_flow->receiver_id = $cashier_id;
            $row_flow->receiver_type = objGuidTypeEnum::UM_USER;
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


    public static function checkMemberChangeTradingPassword($params)
    {
        $uid = $params['uid'];
        $result = $params['check_result'];
        $remark = $params['remark'];
        $user_id = $params['user_id'];
        $user_name = $params['user_name'];
        $m = new member_change_trading_password_requestModel();
        $request = $m->getRow(array(
            'uid' => $uid
        ));
        if( !$request ){
            return new result(false,'No found request info:'.$uid,null,errorCodesEnum::NO_DATA);
        }

        $request->operator_id = $user_id;
        $request->operator_name = $user_name;
        $request->operate_time = Now();
        $request->operate_remark = $remark;
        $request->update_time = Now();
        if( $result == 1 ){
            $request->state = commonApproveStateEnum::PASS;
            $up = $request->update();
            if( !$up->STS ){
                return $up;
            }
            // 更新member的trading password
            $rt = memberClass::commonUpdateMemberTradePassword($request['member_id'],$request['new_password'],true);
            return $rt;
        }else{
            $request->state = commonApproveStateEnum::REJECT;
            $up = $request->update();
            return $up;
        }

    }
    public static function getAllCreditControllerList(){
        $r = new ormReader();
        $sql = "SELECT uu.* FROM um_user uu WHERE uu.user_status = 1 AND uu.user_position = '" . userPositionEnum::CREDIT_CONTROLLER . "'";
        $co_list = $r->getRows($sql);
        return $co_list;
    }
    public static function getAllRiskControllerList(){
        $r = new ormReader();
        $sql = "SELECT uu.* FROM um_user uu WHERE uu.user_status = 1 AND uu.user_position = '" . userPositionEnum::RISK_CONTROLLER . "'";
        $co_list = $r->getRows($sql);
        return $co_list;
    }


}