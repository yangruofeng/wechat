<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2018/10/07
 * Time: 14:13
 */
class staffClass
{
    //model数组
    public static $model_arr = array();

    //获取model
    private static function getModel($model_name)
    {
        if (!self::$model_arr[$model_name]) {
            $m = M($model_name);
            self::$model_arr[$model_name] = $m;
        }
        return self::$model_arr[$model_name];
    }

    /**
     * 获取员工列表
     * @param $pageNumber
     * @param $pageSize
     * @param $filters
     * @return array
     */
    public static function getStaffPage($pageNumber, $pageSize, $filters)
    {
        $r = new ormReader();
        $sql = "SELECT s.*,sb.branch_name,sd.depart_name FROM staff s"
            . " left JOIN site_depart sd ON s.depart_id = sd.uid"
            . " left JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE 1 = 1";

        if (trim($filters['search_text'])) {
            $search_text = qstr2(trim($filters['search_text']));
            $sql .= " AND (s.display_name LIKE '%" . $search_text . "%' OR s.um_account LIKE '%" . $search_text . "%' OR s.mobile_phone LIKE '%" . $search_text . "%')";
        }

        if (intval($filters['branch_id'])) {
            $sql .= " AND sd.branch_id = " . qstr(intval($filters['branch_id']));
        }

        if (trim($filters['staff_status'])) {
            $sql .= " AND s.staff_status = " . qstr(trim($filters['staff_status']));
        }

        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 20;
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
     * 获取staff信息
     * @param $uid
     * @return ormDataRow
     */
    public static function getStaffInfoById($uid)
    {
        $uid = intval($uid);
        $r = new ormReader();
        $sql = "SELECT s.*,sb.branch_name,sd.branch_id,sd.depart_name FROM staff s"
            . " left JOIN site_depart sd ON s.depart_id = sd.uid"
            . " left JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE s.uid = $uid";
        $info = $r->getRow($sql);

        $identity_list = self::getStaffIdentityByStaffId($uid);
        $info['identity_list'] = $identity_list;
        return $info;
    }

    /**
     * 获取员工Identity信息
     * @param $staff_id
     * @return array
     */
    public static function getStaffIdentityByStaffId($staff_id)
    {
        $r = new ormReader();
        $staff_id = intval($staff_id);
        $identity_type = self::getIdentityType();
        $identity_type_key = array_keys($identity_type);
        $identity_type_str = '(' . implode(',', $identity_type_key) . ')';

        $sql = "SELECT * FROM staff_identity WHERE uid IN (SELECT max(uid) FROM staff_identity WHERE staff_id = $staff_id AND identity_type IN $identity_type_str GROUP BY identity_type)";
        $identity_list = $r->getRows($sql);
        $identity_list = resetArrayKey($identity_list, 'identity_type');

        $identity_type_new = array();
        foreach ($identity_type as $key => $val) {
            $identity_type_new[$key]['name'] = $val;
            $identity_type_new[$key]['detail'] = $identity_list[$key] ?: array();

            if ($key == certificationTypeEnum::ID && $identity_list[$key]) {
                if ($identity_list[$key]['expire_time'] < Now()) {
                    $identity_type_new[$key]['expired_time'] = dateFormat($identity_list[$key]['expire_time']);
                } elseif ($identity_list[$key]['expire_time'] < dateAdd(Now(), 90)) {
                    $identity_type_new[$key]['will_be_expired'] = dateFormat($identity_list[$key]['expire_time']);
                }
            }
        }
        return $identity_type_new;
    }

    /**
     * 设置account
     * @param $user_code
     * @param $staff_id
     * @return result
     */
    public static function settingAccount($user_code, $staff_id = 0)
    {
        $user_code = trim($user_code);
        $m_staff = self::getModel('staff');
        $staff_info = $m_staff->find(array(
            'um_account' => $user_code,
            'uid' => array('neq', $staff_id),
            'staff_status' => array('<=', staffStatusEnum::REGULAR_EMPLOYEE)
        ));
        if ($staff_info) {
            return new result(false, 'The user account has been bound.');
        }
        $m_um_user = self::getModel('um_user');
        $user_info = $m_um_user->find(array('user_code' => $user_code));
        if (!$user_info) {
            return new result(false, 'The account does not exist.');
        }
        $depart_id = intval($user_info['depart_id']);
        if ($depart_id) {
            $m_site_depart = self::getModel('site_depart');
            $depart_info = $m_site_depart->find($depart_id);
        }

        $data = array(
            'country_code' => $user_info['country_code'],
            'phone_number' => $user_info['phone_number'],
            'branch_id' => $depart_info['branch_id'],
            'depart_id' => $depart_id,
            'user_position' => $user_info['user_position'],
        );
        return new result(true, '', $data);
    }

    /**
     * 保存Staff
     * @param $params
     * @return result
     */
    public static function addStaff($params)
    {
        //格式化参数
        $init_rt = self::initStaffParams($params);
        if (!$init_rt->STS) {
            return $init_rt;
        } else {
            $init_params = $init_rt->DATA;
        }

        $m_staff = self::getModel('staff');
        //判断account是否存在/staff不存在
        if ($init_params['um_account']) {
            $rt_chk = self::checkUserAccount($init_params['um_account']);
            if (!$rt_chk->STS) {
                return $rt_chk;
            }
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            //保存staff
            $row = $m_staff->newRow($init_params);
            $row->staff_status = staffStatusEnum::ON_TRIAL;
            $row->creator_id = $init_params['operator_id'];
            $row->creator_name = $init_params['operator_name'];;
            $row->create_time = Now();
            $row->update_time = Now();
            $rt_1 = $row->insert();
            if (!$rt_1->STS) {
                $conn->rollback();
                return $rt_1;
            }

            //生成Guid
            $row->obj_guid = generateGuid($rt_1->AUTO_ID, objGuidTypeEnum::STAFF);
            $rt_2 = $row->update();
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, $rt_2->MSG);
            }

            //保存log
            $rt_3 = self::insertStaffStateLog($rt_1->AUTO_ID, null, staffStatusEnum::ON_TRIAL, 'New entry', $init_params['operator_id']);
            if (!$rt_3->STS) {
                $conn->rollback();
                return $rt_3;
            }

            $conn->submitTransaction();
            return new result(true, 'Add successful.');

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 保存Staff state log
     * @param $staff_id
     * @param $original_state
     * @param $current_state
     * @param $remark
     * @param $creator_id
     * @return ormResult
     */
    public static function insertStaffStateLog($staff_id, $original_state, $current_state, $remark, $creator_id)
    {
        $user_obj = new objectUserClass($creator_id);
        $m_staff_state_log = self::getModel('staff_state_log');
        $log_row = $m_staff_state_log->newRow();
        $log_row->staff_id = $staff_id;
        $log_row->original_state = $original_state;
        $log_row->current_state = $current_state;
        $log_row->remark = $remark;
        $log_row->creator_id = $creator_id;
        $log_row->creator_name = $user_obj->user_name;
        $log_row->create_time = Now();
        $rt = $log_row->insert();
        return $rt;
    }

    /**
     * 编辑staff
     * @param $params
     * @return ormResult|result
     */
    public static function editStaff($params)
    {
        //格式化参数
        $init_rt = self::initStaffParams($params);
        if (!$init_rt->STS) {
            return $init_rt;
        } else {
            $init_params = $init_rt->DATA;
        }
        $staff_id = $init_params['uid'];

        $m_staff = self::getModel('staff');
        $row = $m_staff->getRow($staff_id);
        if (!$row) {
            return new result(false, 'Invalid Id.');
        }

        //判断account是否存在/staff不存在
        if ($init_params['um_account']) {
            $chk_account = self::checkUserAccount($init_params['um_account'], $staff_id);
            if (!$chk_account->STS) {
                return $chk_account;
            }
        }

        //检查身份证及绑定员工信息
        $chk_id = self::chkIdCard($init_params['id_card_number'], $staff_id);
        if (!$chk_id->STS) {
            return $chk_id;
        }

        //修改staff
        $init_params['update_time'] = Now();
        $init_params['update_id'] = $init_params['operator_id'];
        $init_params['update_name'] = $init_params['operator_name'];
        $rt = $m_staff->update($init_params);
        if (!$rt->STS) {
            return $rt;
        }

        return new result(true, 'Edit successful.');
    }

    /**
     * 格式化参数
     * @param $params
     * @return ormResult|result
     */
    private static function initStaffParams($params)
    {
        $init_params = array(
            'uid' => intval($params['uid']),
            'first_name' => trim($params['first_name']),
            'last_name' => trim($params['last_name']),
            'display_name' => trim($params['last_name']) . ' ' . trim($params['first_name']),
            'staff_address' => trim($params['staff_address']),
            'staff_icon' => trim($params['staff_icon']),
            'country_code' => trim($params['country_code']),
            'phone_number' => trim($params['phone_number']),
            'depart_id' => intval($params['depart_id']),
            'staff_position' => trim($params['staff_position']),
            'entry_time' => trim($params['entry_time']),
            'um_account' => trim($params['um_account']),
            'remark' => trim($params['remark']),
            'operator_id' => intval($params['operator_id'])
        );

        //参数是否为空
        if (!$init_params['first_name'] || !$init_params['last_name'] || !$init_params['depart_id'] || !$init_params['staff_icon'] || !$init_params['phone_number'] || !$init_params['entry_time']) {
            return new result(false, 'Param Error.');
        }

        //格式化电话
        $format_phone = tools::getFormatPhone($init_params['country_code'], $init_params['phone_number']);
        $init_params['mobile_phone'] = $format_phone['contact_phone'];

        //获取操作人信息
        $user_obj = new objectUserClass($init_params['operator_id']);
        $init_params['operator_name'] = $user_obj->user_name;
        return new result(true, '', $init_params);
    }

    /**
     * 检验Account
     * @param $user_account
     * @param $staff_id
     * @return result
     */
    private static function checkUserAccount($user_account, $staff_id = 0)
    {
        $m_um_user = self::getModel('um_user');
        $m_staff = self::getModel('staff');
        $user_info = $m_um_user->find(array('user_code' => $user_account));
        if (!$user_info) {
            return new result(false, 'The account does not exist.');
        }

        $staff_info = $m_staff->find(array(
            'um_account' => $user_account,
            'uid' => array('neq', $staff_id),
            'staff_status' => array('<=', staffStatusEnum::REGULAR_EMPLOYEE)
        ));
        if ($staff_info) {
            return new result(false, 'The user account has been bound.');
        }
        return new result(true);
    }

    /**
     * 根据身份证查看员工情况
     * @param $id_card
     * @param int $staff_id
     * @return result
     */
    private static function chkIdCard($id_card, $staff_id = 0)
    {
        $m_staff = self::getModel('staff');
        $chk_id_number = $m_staff->find(array(
            'id_card_number' => $id_card,
            'uid' => array('neq', $staff_id),
        ));
        if ($chk_id_number && $chk_id_number['staff_status'] == staffStatusEnum::ABNORMAL_DIMISSION) {
            //异常离职
            $m_staff_state_log = self::getModel('staff_state_log');
            $last_log = $m_staff_state_log->orderBy('uid DESC')->find(array(
                'staff_id' => $chk_id_number['uid'],
                'current_state' => staffStatusEnum::ABNORMAL_DIMISSION
            ));
            $msg = 'Abnormal turnover: ' . $last_log['remark'];
            return new result(false, $msg);
        }

        if ($chk_id_number && $chk_id_number['staff_status'] < staffStatusEnum::NORMAL_DIMISSION) {
            //身份证已经存在
            return new result(false, 'Id-card already exists.');
        }
        return new result(true);
    }

    /**
     * 获取状态修改日志
     * @param $pageNumber
     * @param $pageSize
     * @param $filters
     * @return array
     */
    public static function getStaffStateLogList($pageNumber, $pageSize, $filters)
    {
        $r = new ormReader();
        $sql = "SELECT * FROM staff_state_log WHERE 1 = 1";

        if (intval($filters['staff_id'])) {
            $sql .= " AND staff_id = " . qstr(intval($filters['staff_id']));
        }
        $sql .= " ORDER BY uid DESC";
        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 20;
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
     * 修改staff状态
     * @param $staff_id
     * @param $current_state
     * @param $remark
     * @param $operator_id
     * @return ormResult|result
     */
    public static function changeStaffStatus($staff_id, $current_state, $remark, $operator_id)
    {
        $m_staff = self::getModel('staff');
        $row = $m_staff->getRow(intval($staff_id));
        if (!$row) {
            return new result(false, 'Invalid Id.');
        }

        $user_obj = new objectUserClass(intval($operator_id));
        $operator_name = $user_obj->user_name;
        $original_state = $row['staff_status'];

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row->staff_status = $current_state;
            $row->update_id = intval($operator_id);
            $row->update_name = $operator_name;
            $row->update_time = Now();
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return $rt_1;
            }
            $rt_2 = self::insertStaffStateLog($staff_id, $original_state, $current_state, $remark, $operator_id);
            if (!$rt_2->STS) {
                $conn->rollback();
                return $rt_2;
            }
            $conn->submitTransaction();
            return new result(true, 'Change successful.');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 获取指纹信息
     * @param $filter
     * @return result
     */
    public static function getStaffFingermark($filter)
    {
        $m_staff = self::getModel('staff');
        $staff_info = $m_staff->find($filter);
        if (!$staff_info) {
            return new result(false, 'No search for staff.');
        }
        $staff_info['staff_icon'] = getImageUrl($staff_info['staff_icon'], null, null);
        $staff_info['staff_status'] = L("staff_status_" . $staff_info['staff_status']);

        $m_common_fingerprint_library = self::getModel('common_fingerprint_library');
        $fingerprint_info = $m_common_fingerprint_library->orderBy('uid DESC')->find(array('obj_uid' => $staff_info['obj_guid']));
        if ($fingerprint_info) {
            $staff_info['feature_img'] = $fingerprint_info['feature_img'];
            $staff_info['certification_status'] = 'Registered';
            $staff_info['certification_time'] = timeFormat($fingerprint_info['create_time']);
        } else {
            $staff_info['feature_img'] = '';
            $staff_info['certification_status'] = 'Unregistered';
            $staff_info['certification_time'] = 'N/A';
        }
        return new result(true, '', $staff_info);
    }

    /**
     * 获取证件类型
     * @return array
     */
    public static function getIdentityType()
    {
        return array(
            certificationTypeEnum::ID => "Identity Card",
            certificationTypeEnum::FAIMILYBOOK => "Family Book",
            certificationTypeEnum::PASSPORT => "Passport",
            certificationTypeEnum::RESIDENT_BOOK => "Resident Book",
            certificationTypeEnum::BIRTH_CERTIFICATE => "Birth Certificate",
        );
    }

    /**
     * 保存新证件
     * @param $p
     * @return result
     */
    public static function saveStaffIdentityOp($p)
    {
        $uid = intval($p['staff_id']);
        $cert_type = intval($p['cert_type']);
        $creator_id = intval($p['creator_id']);

        $m_staff = self::getModel('staff');
        $staff_row = $m_staff->getRow(array('uid' => $uid));
        if (!$staff_row) {
            return new result(false, 'No eligible staff.');
        }

        $image_structure = global_settingClass::getCertImageStructure();
        $stt = $image_structure[$cert_type];
        $file_keys = array_column($stt, 'file_key');

        $cert_images = array();
        foreach ($file_keys as $key) {
            $cert_file = trim($p[$key]);
            if (!$cert_file) return new result(false, 'Please upload image!');
            $cert_images[$key] = $cert_file;
        }

        $obj_user = new objectUserClass($creator_id);
        $creator_name = $obj_user->user_name;

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            //更新原来通过的为过期状态
            $sql = "UPDATE staff_identity SET identity_state = 0 WHERE staff_id = $uid AND identity_type = " . qstr($cert_type);
            $up = $m_staff->conn->execute($sql);
            if (!$up->STS) {
                $conn->rollback();
                return new result(false, 'Update history identity fail.');
            }

            $m_staff_identity = self::getModel('staff_identity');
            $m_image = self::getModel('staff_identity_image');

            $new_row = $m_staff_identity->newRow();
            $new_row->staff_id = $uid;
            $new_row->identity_type = $cert_type;
            $new_row->identity_state = 1;
            if (trim($p['expire_date'])) $new_row->expire_time = trim($p['expire_date']);
            $new_row->creator_id = $creator_id;
            $new_row->creator_name = $creator_name;
            $new_row->create_time = Now();
            $new_row->update_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                $conn->rollback();
                return new result(false, 'Save Failed');
            }

            foreach ($cert_images as $key => $img) {
                $row = $m_image->newRow();
                $row->identity_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $img;
                $row->image_sha = sha1_file(getImageUrl($img));
                $insert = $row->insert();
                if (!$insert->STS) {
                    $conn->rollback();
                    return new result(false, 'Add cert image fail');
                }
            }

            if ($cert_type == certificationTypeEnum::ID) {
                $rt = self::editStaffIdCard($uid, $p);
                if (!$rt->STS) {
                    $conn->rollback();
                    return $rt;
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Upload Successful!');

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 修改staff身份证信息
     * @param $staff_id
     * @param $p
     * @return result
     */
    private static function editStaffIdCard($staff_id, $p)
    {
        $m_staff = self::getModel('staff');
        $staff_row = $m_staff->getRow(array('uid' => intval($staff_id)));
        if (!$staff_row) {
            return new result(false, 'No eligible staff.');
        }

        $id_number = trim($p['id_number']);
        $expire_date = date('Y-m-d', strtotime($p['expire_date']));
        $gender = trim($p['gender']);
        $civil_status = trim($p['civil_status']);
        $birthday = date('Y-m-d', strtotime($p['birthday']));
        $birth_country = trim($p['birth_country']);

        $birth_province = intval($p['birth_province']);
        $birth_district = intval($p['birth_district']);
        $birth_commune = intval($p['birth_commune']);
        $birth_village = intval($p['birth_village']);
        $address = trim($p['address']);

        $address_detail_arr = array();
        if ($address) {
            $address_detail_arr[] = $address;
        }
        $m_core_tree = M('core_tree');
        if ($birth_village) {
            $birth_village_info = $m_core_tree->find(array('uid' => $birth_village));
            $address_detail_arr[] = $birth_village_info['node_text'];
        }
        if ($birth_commune) {
            $birth_commune_info = $m_core_tree->find(array('uid' => $birth_commune));
            $address_detail_arr[] = $birth_commune_info['node_text'];
        }
        if ($birth_district) {
            $birth_district_info = $m_core_tree->find(array('uid' => $birth_district));
            $address_detail_arr[] = $birth_district_info['node_text'];
        }
        if ($birth_province) {
            $birth_province_info = $m_core_tree->find(array('uid' => $birth_province));
            $address_detail_arr[] = $birth_province_info['node_text'];
        }
        $address_detail = implode(', ', $address_detail_arr);

        $kh_family_name = trim($p['kh_family_name']);
        $kh_given_name = trim($p['kh_given_name']);
        $kh_second_name = trim($p['kh_second_name']);
        $kh_third_name = trim($p['kh_third_name']);

        $en_family_name = trim($p['en_family_name']);
        $en_given_name = trim($p['en_given_name']);
        $en_second_name = trim($p['en_second_name']);
        $en_third_name = trim($p['en_third_name']);

        if (!$id_number || !$birth_country || $birth_province == 0) {
            return new result(false, 'Param Error.');
        }

        $chk_id = $m_staff->find(array(
            'id_number' => $id_number,
            'uid' => array('neq', $staff_id),
            'staff_status' => array('<=', staffStatusEnum::REGULAR_EMPLOYEE)
        ));
        if ($chk_id) {
            return new result(false, 'Identity card already exists.');
        }

        $staff_row->id_number = $id_number;
        $staff_row->id_type = 0;
        $staff_row->nationality = $birth_country;
        $staff_row->id_en_name_json = my_json_encode(
            array(
                'en_family_name' => $en_family_name,
                'en_given_name' => $en_given_name,
                'en_second_name' => $en_second_name,
                'en_third_name' => $en_third_name,
            )
        );
        $staff_row->id_kh_name_json = my_json_encode(
            array(
                'kh_family_name' => $kh_family_name,
                'kh_given_name' => $kh_given_name,
                'kh_second_name' => $kh_second_name,
                'kh_third_name' => $kh_third_name,
            )
        );

        $staff_row->first_name = $en_given_name;
        $staff_row->last_name = $en_family_name;
        $staff_row->display_name = $en_family_name . ' ' . $en_given_name;
        $staff_row->kh_display_name = $kh_family_name . ' ' . $kh_given_name;
        $staff_row->alias_name = my_json_encode(array(
            'en' => $staff_row->display_name,
            'kh' => $staff_row->kh_display_name
        ));
        $staff_row->gender = $gender;
        $staff_row->civil_status = $civil_status;
        $staff_row->birthday = $birthday;

        $staff_row->id_address1 = $birth_province;
        $staff_row->id_address2 = $birth_district;
        $staff_row->id_address3 = $birth_commune;
        $staff_row->id_address4 = $birth_village;
        $staff_row->address_detail = $address;
        $staff_row->full_address = $address_detail;
        $staff_row->id_expire_time = $expire_date;
        $staff_row->update_time = Now();

        $rt = $staff_row->update();
        if (!$rt->STS) {
            return new result(false, 'Edit id-card failed.');
        }
        return new result(true);
    }

    /**
     * 证件信息
     * @param $uid
     * @return mixed
     */
    public static function getStaffIdentityById($uid)
    {
        $m_staff_identity = self::getModel('staff_identity');
        $m_image = self::getModel('staff_identity_image');
        $identity_info = $m_staff_identity->find(array('uid' => $uid));

        $images = $m_image->select(array('identity_id' => $uid));
        $identity_info['images'] = resetArrayKey($images, 'image_key');
        return $identity_info;
    }

    /**
     * 设置证件过期
     * @param $uid
     * @return result
     */
    public static function setIdentityExpired($uid)
    {
        $m_staff_identity = self::getModel('staff_identity');
        $identity_row = $m_staff_identity->getRow(array('uid' => $uid));
        if (!$identity_row) {
            return new result(false, 'Invalid id.');
        }
        $identity_row->identity_state = 0;
        $identity_row->update_time = Now();
        $rt = $identity_row->update();
        if ($rt->STS) {
            return new result(true, 'Set successful.');
        } else {
            return new result(false, 'Set failed.');
        }
    }
}