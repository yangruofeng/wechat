<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/9
 * Time: 16:00
 */
class memberClass
{
    private $member_info;

    public function __construct($memberInfo)
    {
        $this->member_info = $memberInfo;
    }

    public function getInstanceProperty()
    {
        if ($this->member_info instanceof ormDataRow) {
            $arr = $this->member_info->toArray();
            $arr['member_property'] = my_json_decode($arr['member_property']);
            return $arr;
        } else {
            return array();
        }
    }

    public static function getInstanceByID($memberId)
    {
        $member_model = new memberModel();
        $member_info = $member_model->getRow($memberId);
        if (!$member_info) throw new Exception("Member not found - ID: $memberId");
        return new memberClass($member_info);
    }

    public static function getGUIDByMemberId($member_id)
    {
        $member_model = new memberModel();
        $member_info = $member_model->getRow($member_id);
        return $member_info['obj_guid'];
    }

    public static function getInstanceByGUID($memberGUID)
    {
        $member_model = new memberModel();
        $member_info = $member_model->getRow(array('obj_guid' => $memberGUID));
        if (!$member_info) throw new Exception("Member not found - GUID: $memberGUID");
        return new memberClass($member_info);
    }

    public static function getMemberInfoByGUID($guid)
    {
        $member_model = new memberModel();
        $info = $member_model->find(array('obj_guid' => $guid));
        return $info;
    }

    /** member GUID生成规则  使用core的公共函数
     * @param int $uid
     * @return int
     */
    public static function generateMemberGuid($member_id = 0)
    {
        $uid = intval($member_id);
        $guid = intval(strval(objGuidTypeEnum::CLIENT_MEMBER) . str_pad($uid, 6, '0', STR_PAD_LEFT));
        return $guid;
    }

    /** 检查账号格式
     * @param $account
     * @return bool
     */
    public static function isValidAccount($account)
    {
        // 是否以字母开头
        $re = preg_match("/^[a-z]/i", $account);
        if (!$re) {
            return false;
        }

        // 是否存在空格
        $space = preg_match("/\s+/", $account);
        if ($space) {
            return false;
        }
        // 长度5位及以上
        $len = strlen($account);
        if ($len < 5) {
            return false;
        }
        return true;
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

    /** 电话是否已经被注册
     * @param $country_code
     * @param $phone_number
     * @return int
     */
    public static function checkPhoneNumberIsRegistered($country_code, $phone_number)
    {
        $format_phone = tools::getFormatPhone($country_code, $phone_number);
        $contact_phone = $format_phone['contact_phone'];

        // 判断是否被其他member注册过
        $is_registered = 0;
        $m_member = new memberModel();
        $row = $m_member->getRow(array(
            'phone_id' => $contact_phone,
        ));
        if ($row) {
            $is_registered = 1;
        }
        return $is_registered;
    }


    /** 检查登陆账号是否存在
     * @param $account
     * @return int
     */
    public static function checkLoginAccountIsExist($account)
    {
        $is = 0;
        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'login_code' => $account
        ));
        if ($member) {
            $is = 1;
        }
        return $is;
    }


    /** 是否设置交易密码
     * @param $member_id
     * @return
     */
    public static function isSetTradingPassword($member_id)
    {
        $return = array(
            'is_set' => 0,
            'verify_amount' => 0,
            'currency' => null
        );

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return $return;
        }
        if ($member->trading_password) {
            $return = array(
                'is_set' => 1,
                'verify_amount' => $member->trading_verify_amount ?: 0,
                'currency' => $member->trading_verify_currency ?: 'USD'
            );
        }
        return $return;
    }


    /** 更新member的通行令牌
     * @param $passport_type
     * @param $member_id
     * @param $old_account
     * @param $new_account
     * @param $token
     * @return result
     */
    public static function updateMemberPassport($passport_type, $member_id, $old_account, $new_account, $token)
    {
        $m = new member_passportModel();
        $sql = "delete from member_passport where member_id='$member_id' and passport_type='$passport_type'
        and passport_account='$old_account' ";
        $del = $m->conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete old passport fail.', null, errorCodesEnum::DB_ERROR);
        }
        $passport = $m->newRow();
        $passport->member_id = $member_id;
        $passport->passport_type = $passport_type;
        $passport->passport_account = $new_account;
        $passport->passport_token = $token;
        $passport->expire_seconds = 0;
        $passport->is_invalid = 0;
        $insert = $passport->insert();
        if (!$insert->STS) {
            return new result(false, 'Create new passport fail.', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true);
    }


    /** 电话注册会员
     * @param $params
     * @return result
     */
    public static function phoneRegisterNew($params)
    {
        $country_code = $params['country_code'];
        $phone = $params['phone'];
        $sms_id = $params['sms_id'];
        $sms_code = $params['sms_code'];

        $format_phone = tools::getFormatPhone($country_code, $phone);
        $contact_phone = $format_phone['contact_phone'];

        // 检查电话格式
        if (!isPhoneNumber($contact_phone)) {
            return new result(false, 'Invalid phone number', null, errorCodesEnum::INVALID_PHONE_NUMBER);
        }

        $login_account = trim($params['login_code']);
        $password = $params['password'];

        if (!$country_code || !$phone || !$password || !$login_account) {
            return new result(false, 'Invalid param', array($country_code, $phone, $password, $login_account), errorCodesEnum::INVALID_PARAM);
        }

        if ($params['open_source'] == memberSourceEnum::COUNTER) {
            $params['member_icon'] = $params['member_image'];
        } else {
            // 头像
            if (empty($_FILES['photo'])) {
                return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
            }

            $default_dir = 'avator';
            $upload = new UploadFile();
            $upload->set('save_path', null);
            $upload->set('default_dir', $default_dir);
            $re = $upload->server2upun('photo');
            if ($re == false) {
                logger::record('member_register', $upload->getError());
                return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
            }
            $img_path = $upload->img_url;

            $params['member_image'] = $img_path;
            $params['member_icon'] = $img_path;
        }


        // 验证验证码
        $m_verify_code = new phone_verify_codeModel();
        $row = $m_verify_code->getRow(array(
            'uid' => $sms_id,
            'verify_code' => $sms_code,
        ));
        if (!$row) {
            return new result(false, 'SMS code error', null, errorCodesEnum::SMS_CODE_ERROR);
        }

        $params['phone_number'] = $params['phone'];
        $params['is_verify_phone'] = 1;

        // 检查account格式
        $valid = self::isValidAccount($login_account);
        if (!$valid) {
            return new result(false, 'Not supported account', null, errorCodesEnum::ACCOUNT_NOT_VALID);
        }

        // 检查密码
        $valid = self::isValidPassword($password);
        if (!$valid) {
            return new result(false, 'Password not strong', null, errorCodesEnum::PASSWORD_NOT_STRONG);
        }

        $conn = ormYo::Conn();

        try {
            $conn->startTransaction();
            if ($params['open_source'] == memberSourceEnum::CO || $params['open_source'] == memberSourceEnum::COUNTER) {
                $officer_id = intval($params['officer_id']);
                $userObject = new objectUserClass($officer_id);

                $params['creator_id'] = $officer_id;
                $params['creator_name'] = $userObject->object_info['user_name'];
                $params['branch_id'] = $userObject->branch_id;

                $member_state = $params['open_source'] == memberSourceEnum::CO ? memberStateEnum::CHECKED : memberStateEnum::CREATE;
                $params['member_state'] = $member_state;

                $rt = memberClass::addMember($params);
                if (!$rt->STS) {
                    $conn->rollback();
                    return $rt;
                }
                $member = $rt->DATA;

                $officer_id = intval($params['officer_id']);
                $is_primary = 0;
                if ($params['open_source'] == memberSourceEnum::CO) {
                    $is_primary = 1;//co注册的自动设置为primary
                }
                $re = self::memberBindOfficer($member->uid, $officer_id, $is_primary);
                if (!$re->STS) {
                    $conn->rollback();
                    return $re;
                }
                $task_msg = "Register New Client 【" . $params['login_code'] . "】 By 【" . $userObject->user_name . "】 At " . Now();
                taskControllerClass::handleNewTask($member->uid, userTaskTypeEnum::BM_NEW_CLIENT, $userObject->branch_id, objGuidTypeEnum::SITE_BRANCH, $officer_id, objGuidTypeEnum::UM_USER, $task_msg);

            } else {
                $rt = memberClass::addMember($params);
                if (!$rt->STS) {
                    $conn->rollback();
                    return $rt;
                }
                $member = $rt->DATA;
            }

            // 柜台注册增加用户居住地址
            if ($params['open_source'] == memberSourceEnum::COUNTER) {
                //把counter注册的添加到跟踪表
                /*
                 $officer_id = intval($params['creator_id']);
                 $re = self::memberBindOfficer($member->uid, $officer_id);
                 if (!$re->STS) {
                     $conn->rollback();
                     return $re;
                 }
                */
                // 添加地址
                $data = array(
                    'officer_id' => $params['creator_id'],
                    'member_id' => $member['uid'],
                    'id1' => $params['id1'],
                    'id2' => $params['id2'],
                    'id3' => $params['id3'],
                    'id4' => $params['id4'],
                    'full_text' => $params['full_text'],
                    'cord_x' => $params['cord_x'],
                    'cord_y' => $params['cord_y']
                );
                $rt = userClass::editMemberResidencePlace($data);
                if (!$rt->STS) {
                    $conn->rollback();
                    return $rt;
                }
            }

            $conn->submitTransaction();

            return new result(true, 'Success', $member);

        } catch (Exception $e) {

            return new result(false, $e->getMessage(), null, errorCodesEnum::DB_ERROR);
        }

    }


    public static function memberBindOfficer($member_id, $officer_id, $is_primary = 0, $check_unique_operator = 0)
    {
        $member_id = intval($member_id);
        $officer_id = intval($officer_id);
        $m_officer = new member_follow_officerModel();
        if ($check_unique_operator > 0) {
            $chk_row = $m_officer->find(array("member_id" => $member_id, "officer_type" => 1));
            if ($chk_row) {
                return new result(false, "Already Got The Client By Operator:['" . $chk_row['officer_name'] . "']");
            }
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $officer_info = (new um_userModel())->getRow($officer_id);
        if (!$officer_info) {
            return new result(false, 'User not exist', null, errorCodesEnum::USER_NOT_EXISTS);
        }

        $officer_name = $officer_info->user_name;
        $user_pos = $officer_info->user_position;
        $officer_type = 0;
        switch ($user_pos) {
            case userPositionEnum::CREDIT_OFFICER:
                $officer_type = 0;
                break;
            case userPositionEnum::OPERATOR:
                $officer_type = 1;
                break;
            case userPositionEnum::CUSTOMER_SERVICE:
                $officer_type = 2;
                break;
            default:
                $officer_type = 99;//未知
        }


        // 插入新的跟进officer,member和officer是多对多的

        //如果已经有了就不插入
        $tmp_row = $m_officer->getRow(array("member_id" => $member_id, "officer_id" => $officer_id));
        if ($tmp_row) {
            return new result(true, "success", $tmp_row);
        }

        $officer = $m_officer->newRow();
        $officer->member_id = $member_id;
        $officer->officer_id = $officer_id;
        $officer->officer_name = $officer_name;
        $officer->is_active = 1;
        $officer->officer_type = $officer_type;
        $officer->is_primary = $is_primary;
        $officer->update_time = Now();
        $insert = $officer->insert();
        if (!$insert->STS) {
            return new result(false, 'Bind fail', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success', $officer);
    }


    /** 获取贷款账户
     * @param $member_id
     */
    public static function getLoanAccountInfoByMemberId($member_id)
    {
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return array();
        }

        $m_account = new loan_accountModel();
        $account = $m_account->find(array(
            'obj_guid' => $member['obj_guid']
        ));
        return $account ?: array();
    }


    /** 获取保险账户
     * @param $member_id
     * @return bool
     */
    public static function getInsuranceAccountInfoByMemberId($member_id)
    {
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return false;
        }

        $m_account = new insurance_accountModel();
        $account = $m_account->find(array(
            'obj_guid' => $member['obj_guid']
        ));
        return $account ?: false;
    }


    /** 新增会员
     * @param $params
     * @return result
     */
    public static function addMember($params)
    {

        $m_member = new memberModel();

        // 重复检测 用户、电话、邮箱
        $login_code = $params['login_code'];
        if ($login_code) {

            $member = $m_member->getRow(array(
                'login_code' => $login_code
            ));
            if ($member) {
                return new result(false, 'Member exist', null, errorCodesEnum::USER_EXIST);
            }

            // 检查account格式
            $valid = self::isValidAccount($login_code);
            if (!$valid) {
                return new result(false, 'Not supported account', null, errorCodesEnum::ACCOUNT_NOT_VALID);
            }

        } else {
            // 系统自己生成login_code
            // 97,122 小写
            $login_code = chr(rand(65, 90)) . substr(md5(microtime()), 0, 7) . rand(10, 99);
        }

        $contact_phone = null;
        if ($params['phone'] || $params['phone_number']) {
            $phone_number = $params['phone'] ?: $params['phone_number'];
            $format_phone = tools::getFormatPhone($params['country_code'], $phone_number);
            $contact_phone = $format_phone['contact_phone'];
        }


        if ($contact_phone) {
            $member = $m_member->getRow(array(
                'phone_id' => $contact_phone
            ));
            if ($member) {
                return new result(false, 'Phone used', null, errorCodesEnum::PHONE_USED);
            }
        }

        //  邮箱唯一
        if ($params['email']) {
            $member = $m_member->getRow(array(
                'email' => $params['email']
            ));
            if ($member) {
                return new result(false, 'Email used', null, errorCodesEnum::EMAIL_BEEN_REGISTERED);
            }
        }

        // 密码强度
        $valid = self::isValidPassword($params['password']);
        if (!$valid) {
            return new result(false, 'Password not strong', null, errorCodesEnum::PASSWORD_NOT_STRONG);
        }

        $now = date('Y-m-d H:i:s');

        $password = intval($params['open_source']) == memberSourceEnum::COUNTER ? trim($params['password']) : md5(trim($params['password']));
        $trading_password = intval($params['open_source']) == memberSourceEnum::COUNTER ? trim($params['trading_password']) : md5(trim($params['trading_password']));

        $member = $m_member->newRow();
        $member->obj_guid = 0;
        $member->login_code = $login_code;
        $member->login_password = $password;
        if ($params['trading_password']) $member->trading_password = $trading_password;
        $member->family_name = $params['family_name'];
        $member->given_name = $params['given_name'];
        $member->initials = strtoupper(substr(trim($params['family_name']), 0, 1));  // todo 默认是英语的
        if ($params['family_name'] || $params['given_name']) {
            $member->display_name = $params['family_name'] . ' ' . $params['given_name'];
        }
        $member->alias_name = $params['alias_name'];
        $member->gender = $params['gender'];
        $member->civil_status = $params['civil_status'];
        $member->birthday = $params['birthday'];
        $member->phone_country = $params['country_code'];
        $member->phone_number = $params['phone_number'];
        $member->phone_id = $contact_phone;
        if ($params['is_verify_phone']) {
            $member->is_verify_phone = 1;
            $member->verify_phone_time = $now;
        }
        $member->email = $params['email'];
        if ($params['is_verify_email']) {
            $member->is_verify_email = 1;
            $member->verify_email_time = $now;
        }
        $member->member_property = $params['member_property'];
        $member->member_profile = $params['member_profile'];
        $member->member_grade = $params['member_grade'] ?: null;
        $member->member_image = $params['member_image'];
        $member->member_icon = $params['member_icon'];
        $member->open_source = isset($params['open_source']) ? intval($params['open_source']) : memberSourceEnum::ONLINE;
        $member->open_org = $params['open_org'] ?: 0;
        $member->open_addr = $params['open_addr'] ?: null;
        $member->member_state = intval($params['member_state']) > 0 ? intval($params['member_state']) : memberStateEnum::CREATE;
        $member->create_time = date('Y-m-d H:i:s');
        $member->branch_id = $params['branch_id'];
        $member->work_type = $params['work_type'];
        if ($params['creator_id']) {
            $member->creator_id = intval($params['creator_id']);
            $member->creator_name = $params['creator_name'];
        } else {
            $member->creator_id = 0;
            $member->creator_name = 'System';
        }
        $member->register_location = $params['register_location'];
        $member->is_with_business = intval($params['is_with_business']) ? 1 : 0;

        $insert = $member->insert();
        if (!$insert->STS) {
            return new result(false, 'Create member fail', null, errorCodesEnum::DB_ERROR);
        }

        // 1位type+6位id
        $member->obj_guid = generateGuid($member->uid, objGuidTypeEnum::CLIENT_MEMBER);  // self::generateMemberGuid($member->uid)
        $up = $member->update();
        if (!$up->STS) {
            return new result(false, 'Create member GUID fail', null, errorCodesEnum::DB_ERROR);
        }

        // 是否有行业信息
        if (!empty($params['member_industry']) && is_array($params['member_industry'])) {
            // 一维数组 array(1,2,3)
            $rt = self::setMemberIndustry($member->uid, $params['member_industry']);
            if (!$rt->STS) {
                return $rt;
            }
        }
        //判断consult里有没有接手的officer，有的话要绑定到follow-user, add by tim
        if ($member->open_source == memberSourceEnum::ONLINE) {
            $m_consult = M("loan_consult");
            $tmp_consult = $m_consult->orderBy("uid desc")->find(array("contact_phone" => $member->phone_id));
            if ($tmp_consult) {
                if ($tmp_consult['operator_id'] > 0) {
                    $tmp_ret = self::memberBindOfficer($member->uid, $tmp_consult['operator_id']);
                }
            }
        }


        // 创建通行令牌 code
        if ($member->login_code) {
            $sql = "insert into member_passport(member_id,passport_account,passport_token) values ('" . $member['uid'] . "','" . $member['login_code'] . "','" . $member['login_password'] . "')";
            $do = $m_member->conn->execute($sql);
            if (!$do->STS) {
                return new result(false, 'Register fail', null, errorCodesEnum::DB_ERROR);
            }
        }

        if ($member->is_verify_phone) {
            $sql = "insert into member_passport(member_id,passport_account,passport_token) values ('" . $member['uid'] . "','" . $member['phone_id'] . "','" . $member['login_password'] . "') ";
            $do = $m_member->conn->execute($sql);
            if (!$do->STS) {
                return new result(false, 'Register fail', null, errorCodesEnum::DB_ERROR);
            }
        }

        if ($member->is_verify_email) {
            $sql = "insert into member_passport(member_id,passport_account,passport_token) values ('" . $member['uid'] . "','" . $member['email'] . "','" . $member['login_password'] . "') ";
            $do = $m_member->conn->execute($sql);
            if (!$do->STS) {
                return new result(false, 'Register fail', null, errorCodesEnum::DB_ERROR);
            }
        }


        // 创建贷款账户
        $m_loan_account = new loan_accountModel();
        $loan_account = $m_loan_account->newRow();
        $loan_account->obj_guid = $member['obj_guid'];
        $loan_account->account_type = loanAccountTypeEnum::MEMBER;
        $insert = $loan_account->insert();
        if (!$insert->STS) {
            return new result(false, 'Create loan account fail', null, errorCodesEnum::DB_ERROR);
        }

        // 注册初始信用
        /* 注释这个功能是因为信用要按产品来分
        $is_allow = global_settingClass::isAllowRegisterToSendCredit();
        if ($is_allow) {
            $common_setting = global_settingClass::getCommonSetting();
            $register_credit = intval($common_setting['credit_register']);

            if ($register_credit > 0) {

                // 默认一个月
                $re = member_creditClass::creditGrant($member->uid, $register_credit, 1);
                if (!$re->STS) {
                    return $re;
                }

            }
        }
        */


        // 创建保险账户
        $m_insurance_account = new insurance_accountModel();
        $insurance_account = $m_insurance_account->newRow();
        $insurance_account->obj_guid = $member['obj_guid'];
        $insert2 = $insurance_account->insert();
        if (!$insert2->STS) {
            return new result(false, 'Create insurance account fail', null, errorCodesEnum::DB_ERROR);
        }


        // 自动创建一个储蓄账户的handler
        $m_handler = new member_account_handlerModel();
        $handler = $m_handler->newRow();
        $handler->member_id = $member->uid;
        $handler->handler_type = memberAccountHandlerTypeEnum::PASSBOOK;
        $handler->handler_name = $member->display_name ?: $member->login_code;
        $handler->handler_account = $member->login_code;
        $handler->handler_phone = $member->phone_id;
        $handler->is_verified = 1;
        $handler->state = accountHandlerStateEnum::ACTIVE;
        $handler->create_time = Now();
        $insert3 = $handler->insert();
        if (!$insert3->STS) {
            return new result(false, 'Create passbook handler fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $member);
    }


    public static function editMemberLoginCode($member_id, $login_code)
    {
        $member_id = intval($member_id);
        if (!$member_id || !$login_code) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $is_exist = self::checkLoginAccountIsExist($login_code);
        if ($is_exist) {
            return new result(false, 'Account exist', null, errorCodesEnum::USER_EXIST);
        }

        // 检查account格式
        $valid = self::isValidAccount($login_code);
        if (!$valid) {
            return new result(false, 'Not supported account', null, errorCodesEnum::ACCOUNT_NOT_VALID);
        }

        $old_login_code = $member->login_code;


        $member->login_code = $login_code;

        $member_property = my_json_decode($member->member_property);
        $member_property['original_member_state'] = $member->member_state;

        // 修改会员状态为待check 保存当前状态
        $member->member_state = memberStateEnum::CREATE;
        $member->member_property = json_encode($member_property);
        $member->update_time = Now();
        $up = $member->update();
        if (!$up->STS) {
            return new result(false, 'Edit fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        // 删除旧passport
        $sql = "delete from member_passport where member_id='$member_id' and passport_type='0' and passport_account='$old_login_code' ";
        $del = $m_member->conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete old passport fail.', null, errorCodesEnum::DB_ERROR);
        }

        // 插入passport
        $m_passport = new member_passportModel();
        $passport = $m_passport->newRow();
        $passport->member_id = $member_id;
        $passport->passport_type = 0;
        $passport->passport_account = $member->login_code;
        $passport->passport_token = $member->login_password;
        $insert = $passport->insert();
        if (!$insert->STS) {
            return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', array(
            'login_code' => $member->login_code
        ));
    }


    /** 更新会员密码操作
     * @param $member_id
     * @param $password
     */
    public static function commonUpdateMemberPassword($member_id, $password)
    {
        $member_id = intval($member_id);
        if ($member_id <= 0 || empty($password)) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 密码强度检测
        $valid = self::isValidPassword($password);
        if (!$valid) {
            return new result(false, 'Password not strong', null, errorCodesEnum::PASSWORD_NOT_STRONG);
        }


        if ($member->login_password == md5($password)) {
            return new result(false, 'New password is same as old password.', null, errorCodesEnum::SAME_PASSWORD);
        }

        // 更新密码
        $member->login_password = md5($password);
        $member->update_time = Now();
        $up = $member->update();
        if (!$up->STS) {
            return new result(false, 'Reset fail', null, errorCodesEnum::DB_ERROR);
        }

        // 更新新令牌
        $sql = "update member_passport set passport_token='" . md5($password) . "' where member_id='" . $member->uid . "' and passport_type='0' ";
        $up = $m_member->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Reset fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');
    }

    /** 更新交易密码操作
     * @param $member_id
     * @param $password
     */
    public static function commonUpdateMemberTradePassword($member_id, $password,$is_md5=false)
    {
        $member_id = intval($member_id);
        if ($member_id <= 0 || empty($password)) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 更新密码
        if( $is_md5 ){
            $member->trading_password = $password;
        }else{
            $member->trading_password = md5($password);
        }

        $member->update_time = Now();
        $up = $member->update();
        if (!$up->STS) {
            return new result(false, 'Reset fail:'.$up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');
    }


    /** 更新会员电话操作
     * @param $member_id
     * @param $password
     */
    public static function commonUpdateMemberPhoneNumber($member_id, $country_code, $phone_number)
    {

        $member_id = intval($member_id);
        if ($member_id <= 0 || !$country_code || !$phone_number) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $format_phone = tools::getFormatPhone($country_code, $phone_number);
        $contact_phone = $format_phone['contact_phone'];

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $old_phone = $member->phone_id;

        // 更新手机号
        $member->phone_country = $country_code;
        $member->phone_number = $phone_number;
        $member->phone_id = $contact_phone;
        $member->is_verify_phone = 1;
        $member->verify_phone_time = Now();
        $member->update_time = Now();
        $up = $member->update();
        if (!$up->STS) {
            return new result(false, 'Reset fail', null, errorCodesEnum::DB_ERROR);
        }

        // 删除旧passport
        $sql = "delete from member_passport where member_id='$member_id' and passport_type='0' and passport_account='$old_phone' ";
        $del = $m_member->conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete old passport fail.', null, errorCodesEnum::DB_ERROR);
        }

        // 插入新的passport
        $m_passport = new member_passportModel();
        $passport = $m_passport->newRow();
        $passport->member_id = $member_id;
        $passport->passport_type = 0;
        $passport->passport_account = $member->phone_id;
        $passport->passport_token = $member->login_password;
        $insert = $passport->insert();
        if (!$insert->STS) {
            return new result(false, 'Add new passport fail.', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');
    }


    /** 短信重置密码
     * @param $params
     * @return result
     */
    public static function resetPwdBySms($params)
    {

        // 检查功能是否开启
        $is_can = global_settingClass::isCanResetPassword();
        if (!$is_can) {
            return new result(false, 'Function closed', null, errorCodesEnum::FUNCTION_CLOSED);
        }

        $country_code = $params['country_code'];
        $phone_number = $params['phone'];
        $format_phone = tools::getFormatPhone($country_code, $phone_number);
        $contact_phone = $format_phone['contact_phone'];
        $new_pwd = $params['password'];

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'phone_id' => $contact_phone
        ));

        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $verify_id = $params['sms_id'];
        $verify_code = $params['sms_code'];

        $m_phone_code = new phone_verify_codeModel();
        $row = $m_phone_code->getRow(array(
            'uid' => $verify_id,
            'verify_code' => $verify_code
        ));
        if (!$row) {
            return new result(false, 'Phone code not right', null, errorCodesEnum::SMS_CODE_ERROR);
        }

        $re = self::commonUpdateMemberPassword($member->uid, $new_pwd);
        return $re;

    }


    /** 修改登录密码
     * @param $params
     * @return result
     */
    public static function changePassword($params)
    {

        // 检查功能是否开启
        $is_can = global_settingClass::isCanResetPassword();
        if (!$is_can) {
            return new result(false, 'Function closed', null, errorCodesEnum::FUNCTION_CLOSED);
        }

        $member_id = $params['member_id'];
        $old_pwd = $params['old_pwd'];
        $new_pwd = $params['new_pwd'];

        if (!$member_id || !$old_pwd || !$new_pwd) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        if ($member->login_password != md5($old_pwd)) {
            return new result(false, 'The old password is not right', null, errorCodesEnum::PASSWORD_ERROR);
        }


        $re = self::commonUpdateMemberPassword($member_id, $new_pwd);
        return $re;

    }

    /** 修改交易密码
     * @param $params
     * @return result
     */
    public static function changeTradePassword($params)
    {

        // 检查功能是否开启
        $is_can = global_settingClass::isCanResetPassword();
        if (!$is_can) {
            return new result(false, 'Function closed', null, errorCodesEnum::FUNCTION_CLOSED);
        }

        $member_id = $params['member_id'];
        $old_pwd = $params['old_pwd'];
        $new_pwd = $params['new_pwd'];

        if (!$member_id || !$old_pwd || !$new_pwd) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        if ($member->trading_password != md5($old_pwd)) {
            return new result(false, 'The old password is not right', null, errorCodesEnum::PASSWORD_ERROR);
        }


        $re = self::commonUpdateMemberTradePassword($member_id, $new_pwd);
        return $re;

    }


    /** 登陆成功日志记录
     * @param $member
     * @param $login_code
     * @param $client_id
     * @param $client_type
     * @return result
     */
    protected static function loginSuccess($member, $login_code, $client_id, $client_type, $device_id = null, $device_name = null)
    {


        // 更新登陆信息
        $login_ip = getIp();
        $member->last_login_time = Now();
        $member->last_login_ip = $login_ip;
        $member->update();


        $m_member_token = new member_tokenModel();
        // 删除无效token
        $sql = "delete from member_token where member_id='" . $member->uid . "' ";
        $del = $m_member_token->conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete token fail', null, errorCodesEnum::DB_ERROR);
        }

        // 创建新token令牌
        $token_row = $m_member_token->newRow();
        $token_row->member_id = $member->uid;
        $token_row->login_code = $login_code;
        $token_row->token = md5($login_code . time());
        $token_row->create_time = Now();
        $token_row->login_time = Now();
        $token_row->client_type = $client_type;
        $insert = $token_row->insert();
        if (!$insert->STS) {
            return new result(false, 'Create token fail', null, errorCodesEnum::DB_ERROR);
        }

        // 添加登陆日志
        $m_member_login_log = new member_login_logModel();
        $log = $m_member_login_log->newRow();
        $log->member_id = $member->uid;
        $log->client_id = $client_id;
        $log->client_type = $client_type;
        $log->device_id = $device_id;
        $log->device_name = $device_name;
        $log->login_time = Now();
        $log->login_ip = $login_ip;
        $log->login_area = '';  // todo ip获取区域？
        $insert = $log->insert();
        if (!$insert->STS) {
            return new result(false, 'Log error', null, errorCodesEnum::DB_ERROR);
        }

        $member_info = self::getMemberBaseInfo($member['uid']);

        return new result(true, 'Login success', array(
            'token' => $token_row->token,
            'member_info' => $member_info
        ));
    }


    public static function getMemberBaseInfo($member_id)
    {
        $return = null;
        $m_member = new memberModel();
        $member = $m_member->find(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return $return;
        }

        $member_info = $member;

        $member_info['member_image'] = getImageUrl($member_info['member_image']);
        $member_info['member_icon'] = getImageUrl($member_info['member_icon']);
        $member_info['member_property'] = my_json_decode($member_info['member_property']);

        // 是否锁定CO对客户的资料编辑
        $member_info['is_lock_for_co_edit'] = $member_info['member_property'][memberPropertyKeyEnum::LOCK_FOR_CO] ? 1 : 0;


        $member_info['is_set_trading_password'] = 0;
        if ($member_info['trading_password']) {
            $member_info['is_set_trading_password'] = 1;
        }

        unset($member_info['login_password']);
        unset($member_info['trading_password']);

        // 获得会员等级
        $member_info['grade_code'] = '';
        $member_info['grade_caption'] = '';
        $m_member_grade = new member_gradeModel();
        $grade_info = $m_member_grade->getRow(array(
            'uid' => $member->member_grade,
        ));
        if ($grade_info) {
            $member_info['grade_code'] = $grade_info->grade_code;
            $member_info['grade_caption'] = $grade_info->grade_caption;
        }
        //获取状态
        $state_list = (new memberStateEnum())->Dictionary();
        $member_info['member_state_text'] = $state_list[$member_info['member_state']];


        // 是否认证身份证
        $member_info['is_verify_id_card'] = 0;
        if ($member_info['id_sn'] && $member_info['id_expire_time'] > date('Y-m-d 00:00:00')) {
            $member_info['is_verify_id_card'] = 1;
        }

        // 是否录入指纹
        $member_info['is_verify_fingerprint'] = self::isLoggingFingerprint($member_info['uid']);

        //生意
        $member_info['member_industry'] = self::getMemberIndustryInfo($member_id);

        // 储蓄账户余额
        $memberObject = new objectMemberClass($member_id);
        $cny_balance = $memberObject->getSavingsAccountBalance();
        $member_info['savings_balance'] = $cny_balance;

        // 信用信息
        $credit_info = self::getCreditBalance($member_id);
        $member_info['credit_info'] = $credit_info;

        return $member_info;

    }

    /** 密码登陆
     * @param $params
     * @return result
     */
    public static function passwordLogin($params, $is_check_device = true)
    {
        $login_type = $params['login_type'];
        $password = $params['login_password'];
        $token = md5($password);
        $device_id = trim($params['device_id']); // 设备号
        $device_name = trim($params['device_name']) ?: 'Unknown device';
        $sign = $params['sign'];
        $member = null;
        $login_code = '';
        switch ($login_type) {
            case memberLoginTypeEnum::LOGIN_CODE :
                $login_code = $params['login_code'];
                $sign_str = $login_code;
                $re = self::verifyMemberPassport($login_code, $sign, $sign_str);
                if (!$re->STS) {
                    return $re;
                }
                $member = $re->DATA;
                break;
            case memberLoginTypeEnum::PHONE :
                $country_code = $params['country_code'];
                $phone = $params['phone'];
                $format_phone = tools::getFormatPhone($country_code, $phone);
                $login_code = $format_phone['contact_phone'];
                $sign_str = $country_code . $phone;
                $re = self::verifyMemberPassport($login_code, $sign, $sign_str);
                if (!$re->STS) {
                    return $re;
                }
                $member = $re->DATA;
                break;
            case memberLoginTypeEnum::EMAIL :
                $login_code = $params['email'];
                $sign_str = $login_code;
                $re = self::verifyMemberPassport($login_code, $sign, $sign_str);
                if (!$re->STS) {
                    return $re;
                }
                $member = $re->DATA;
                break;
            default:
                return new result(false, 'Un support type', null, errorCodesEnum::NOT_SUPPORTED);
        }

        $client_id = $params['client_id'] ? intval($params['client_id']) : 0;
        $client_type = $params['client_type'];

        if (!$member) {
            return new result(false, 'Login fail', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $memberObj = new objectMemberClass($member['uid']);
        $chk = $memberObj->isCanLogin();
        if (!$chk->STS) {
            return $chk;
        }

        // 先检查设备，不能销毁掉有效token
        if ($is_check_device) {

            $rt = member_deviceClass::checkLoginDevice($member['uid'], $device_id, $device_name);
            if (!$rt->STS) {
                return $rt;
            }
            $device_info = $rt->DATA;
            if ($device_info['is_device_need_verify']) {

                $member_info = self::getMemberBaseInfo($member['uid']);
                // 此时不创建token
                return new result(true, 'Login success', array(
                    'token' => '',
                    'is_device_need_verify' => 1,
                    'member_info' => $member_info
                ));

            } else {
                $rt = self::loginSuccess($member, $login_code, $client_id, $client_type, $device_id, $device_name);
                return $rt;
            }


        } else {
            $rt = self::loginSuccess($member, $login_code, $client_id, $client_type, $device_id, $device_name);
            return $rt;
        }


    }


    /** 登陆设备验证通过后，获取身份token
     * @param $params
     * @return result
     */
    public static function getTokenAfterDeviceVerify($params)
    {
        $member_id = intval($params['member_id']);
        $client_id = $params['client_id'] ? intval($params['client_id']) : 0;
        $client_type = $params['client_type'];
        $device_id = trim($params['device_id']); // 设备号
        $device_name = trim($params['device_name']) ?: 'Unknown device';
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member:' . $member_id, null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        return self::loginSuccess($member, $member['login_code'], $client_id, $client_type, $device_id, $device_name);
    }

    /** 手势密码登陆
     * @param $params
     * @return result
     */
    public static function gestureLogin($params)
    {
        $member_id = $params['member_id'];
        //$gesture_pwd = $params['gesture_password'];
        $device_id = trim($params['device_id']); // 设备号
        $sign = trim($params['sign']);
        if (!$member_id || !$device_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $memberObj = new objectMemberClass($member['uid']);
        $chk = $memberObj->isCanLogin();
        if (!$chk->STS) {
            return $chk;
        }

        // 检查设备是否还是可信任的
        $is_trust = member_deviceClass::deviceIsTrusted($member['obj_guid'], $device_id);
        if (!$is_trust) {
            return new result(false, 'Device not support gesture login.', null, errorCodesEnum::DEVICE_NOT_SUPPORT_OTHER_LOGIN_WAY);
        }


        // 检测密码
        $self_sign = md5($member_id . md5($member->gesture_password));
        if ($sign != $self_sign) {
            return new result(false, 'Pwd error', null, errorCodesEnum::PASSWORD_ERROR);
        }

        /* if ($member->gesture_password != $gesture_pwd) {
             return new result(false, 'Pwd error', null, errorCodesEnum::PASSWORD_ERROR);
         }*/

        $client_id = $params['client_id'] ? intval($params['client_id']) : 0;
        $client_type = $params['client_type'];


        return self::loginSuccess($member, $member->login_code, $client_id, $client_type);

    }

    public static function fingerprintLogin($params)
    {
        $member_id = $params['member_id'];
        $fingerprint = trim($params['fingerprint']);
        $device_id = trim($params['device_id']); // 设备号
        $sign = trim($params['sign']);
        if (!$member_id || !$fingerprint || !$device_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $memberObj = new objectMemberClass($member['uid']);
        $chk = $memberObj->isCanLogin();
        if (!$chk->STS) {
            return $chk;
        }

        // 检查设备是否还是可信任的
        $is_trust = member_deviceClass::deviceIsTrusted($member['obj_guid'], $device_id);
        if (!$is_trust) {
            return new result(false, 'Device not support fingerprint login.', null, errorCodesEnum::DEVICE_NOT_SUPPORT_OTHER_LOGIN_WAY);
        }

        // url 解码
        $fingerprint = urldecode($fingerprint);

        // 不在服务器检查，无法检测
        $self_sign = md5($member_id . md5($fingerprint));
        if ($sign != $self_sign) {
            return new result(false, 'Fingerprint error', null, errorCodesEnum::PASSWORD_ERROR);
        }

        /*if ($member->fingerprint != $fingerprint) {
            return new result(false, 'Fingerprint error', null, errorCodesEnum::PASSWORD_ERROR);
        }*/

        $client_id = $params['client_id'] ? intval($params['client_id']) : 0;
        $client_type = $params['client_type'];


        return self::loginSuccess($member, $member->login_code, $client_id, $client_type);

    }

    public static function verifyLoginPassword($member_id, $password)
    {
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        if ($member->login_password != md5($password)) {
            return new result(true, 'success', 0);
        }
        return new result(true, 'success', 1);

    }

    public static function verifyMemberPassport($account, $sign, $sign_str, $pass_type = 0)
    {
        $m_member = new memberModel();
        $m_member_passport = new member_passportModel();
        // 取最新，废弃历史的
        $passport = $m_member_passport->orderBy('uid desc')->getRow(array(  // 有可能有不同来源同一个passport_account
            'passport_type' => $pass_type,
            'passport_account' => $account,
            'is_invalid' => 0
        ));
        if (!$passport) {
            return new result(false, 'No passport', null, errorCodesEnum::NO_PASSPORT);
        }

        if ($passport['expire_seconds'] && $passport['expire_seconds'] < time()) {
            return new result(false, 'Passport expired', null, errorCodesEnum::PASSPORT_EXPIRED);
        }

        // 检查签名
        $self_sign = md5($sign_str . $passport->passport_token);
        if ($sign != $self_sign) {
            return new result(false, 'Password error', null, errorCodesEnum::PASSWORD_ERROR);
        }

        $member = $m_member->getRow($passport->member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        return new result(true, 'success', $member);

    }


    /** 检查通行令牌
     * @param $account
     * @param $token
     * @param int $pass_type 0 密码 1 第三方
     * @return result
     */
    public static function verifyMemberPassport_old($account, $token, $pass_type = 0)
    {
        $m_member = new memberModel();
        $m_member_passport = new member_passportModel();
        // 取最新，废弃历史的
        $passport = $m_member_passport->orderBy('uid desc')->getRow(array(  // 有可能有不同来源同一个passport_account
            'passport_type' => $pass_type,
            'passport_account' => $account,
            'is_invalid' => 0
        ));
        if (!$passport) {
            return new result(false, 'No passport', null, errorCodesEnum::NO_PASSPORT);
        }

        if ($passport['expire_seconds'] && $passport['expire_seconds'] < time()) {
            return new result(false, 'Passport expired', null, errorCodesEnum::PASSPORT_EXPIRED);
        }

        if ($passport->passport_token != $token) {
            return new result(false, 'Password error', null, errorCodesEnum::PASSWORD_ERROR);
        }

        $member = $m_member->getRow($passport->member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        return new result(true, 'success', $member);

    }


    public static function idVerifyCertNew($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::ID;
        // 保存目录
        $save_path = fileDirsEnum::ID;

        $cert_files = $_FILES;

        $page_data = (new member_profileClass())->getInitPageData()[$cert_type];
        if (!empty($page_data['input_field_list'])) {
            foreach ($page_data['input_field_list'] as $item) {
                if ($item['is_required'] && !$params[$item['field_name']]) {
                    return new result(false, 'Invalid param:' . $item['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }

        if (!empty($page_data['upload_image_list'])) {
            foreach ($page_data['upload_image_list'] as $item) {
                if ($item['is_required'] && !$cert_files[$item['field_name']]) {
                    return new result(false, 'Invalid param:' . $item['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }

        $member_id = intval($params['member_id']);
        $en_name = $params['cert_name'];
        $kh_name = $params['cert_name_kh'];
        $cert_sn = $params['cert_sn'];

        $name_json = json_encode(array(
            'en' => $en_name,
            'kh' => $kh_name,
            'zh_cn' => ''
        ));

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 检查是否被他人认证过
        $sql = "select * from member_verify_cert where member_id!='$member_id' and cert_type='" . certificationTypeEnum::ID . "'
        and cert_sn='$cert_sn' and verify_state='" . certStateEnum::PASS . "'  order by uid desc";
        $other = $m_member->reader->getRow($sql);
        if ($other) {
            return new result(false, 'ID has been certificated', null, errorCodesEnum::ID_SN_HAS_CERTIFICATED);
        }


        $image_arr = array();
        foreach ($cert_files as $field => $photo) {

            if (!empty($photo) && $photo['size'] > 0) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail:'.$upload->getError(), null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->img_url;
                $full_path = $upload->full_path;
                $image_arr[] = array(
                    'image_url' => $img_path,
                    'image_sha' => sha1_file($full_path),
                    'image_key' => $field
                );
                unset($upload);
            }

        }


        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();


        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => $cert_type
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {
            // 删除图片
            $sql = "delete from member_verify_cert_image where cert_id=" . qstr($o_cert_row['uid']);
            $del = $m_cert->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Del image fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
            }

            // 删除记录
            $del = $o_cert_row->delete();
            if (!$del->STS) {
                return new result(false, 'Del cert row fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
            }


        }

        //更新原来通过的为过期状态
        $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . $cert_type . "' and verify_state='" . certStateEnum::PASS . "'  ";
        $up = $m_cert->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
        }

        // 添加新记录
        $new_row = $m_cert->newRow();
        $new_row->member_id = $member_id;
        $new_row->cert_type = $cert_type;
        $new_row->cert_name = $en_name;
        $new_row->cert_name_json = $name_json;
        $new_row->cert_sn = $cert_sn;
        $new_row->verify_state = certStateEnum::CREATE;
        $new_row->source_type = $source;
        $new_row->create_time = Now();
        $insert = $new_row->insert();
        if (!$insert->STS) {
            return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
        }

        foreach ($image_arr as $value) {
            $row = $m_image->newRow();
            $row->cert_id = $new_row->uid;
            $row->image_key = $value['image_key'];
            $row->image_url = $value['image_url'];
            $row->image_sha = $value['image_sha'];
            $insert = $row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true, 'success', array(
            'cert_result' => $new_row,
            'extend_info' => null
        ));


    }


    /** 身份证认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function idVerifyCert($params, $source = 0)
    {
        $member_id = intval($params['member_id']);
        $en_name = $params['name_en'];
        $kh_name = $params['name_kh'];
        $cert_sn = $params['cert_sn'];
        if (!$cert_sn || !$en_name) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $name_json = json_encode(array(
            'en' => $en_name,
            'kh' => $kh_name,
            'zh_cn' => ''
        ));

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }

        // 检查是否被他人认证过
        $sql = "select * from member_verify_cert where member_id!='$member_id' and cert_type='" . certificationTypeEnum::ID . "'
        and cert_sn='$cert_sn' and verify_state='" . certStateEnum::PASS . "'  order by uid desc";
        $other = $m_member->reader->getRow($sql);
        if ($other) {
            return new result(false, 'ID has been certificated', null, errorCodesEnum::ID_SN_HAS_CERTIFICATED);
        }

        $files = $_FILES;
        $hand_photo = $files['hand_photo'];  // 不强迫
        $front_photo = $files['front_photo'];
        $back_photo = $files['back_photo'];

        if (empty($front_photo) || empty($back_photo)) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }

        // key 是上传表单名
        $photos = array(
            //'hand_photo' => '',
            'front_photo' => '',
            'back_photo' => ''
        );

        // 保存目录
        $save_path = fileDirsEnum::ID;

        foreach ($photos as $field => $photo) {

            if (!empty($files[$field])) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->img_url;
                $photos[$field] = $img_path;
                unset($upload);
            }

        }

        $image_arr = array(
            certImageKeyEnum::ID_FRONT => array(
                'image_url' => $photos['front_photo'],
                'image_sha' => sha1_file(getImageUrl($photos['front_photo']))
            ),
            certImageKeyEnum::ID_BACK => array(
                'image_url' => $photos['back_photo'],
                'image_sha' => sha1_file(getImageUrl($photos['back_photo']))
            ),
        );

        // 如果上传了手持照片
        if (!empty($files['hand_photo'])) {
            $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
            $upload->set('save_path', null);
            $upload->set('default_dir', $save_path);
            $re = $upload->server2upun('hand_photo');
            if ($re == false) {
                return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
            }
            $img_path = $upload->img_url;
            $img_url = $upload->full_path;
            unset($upload);
            $image_arr[certImageKeyEnum::ID_HANDHELD] = array(
                'image_url' => $img_path,
                'image_sha' => sha1_file($img_url)
            );
        }

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();


        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => certificationTypeEnum::ID
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {

            // edit
            $o_cert_row->cert_name = $en_name;
            $o_cert_row->cert_name_json = $name_json;
            $o_cert_row->cert_sn = $cert_sn;
            $up = $o_cert_row->update();
            if (!$up->STS) {
                return new result(false, 'Modify fail', null, errorCodesEnum::DB_ERROR);
            }


            $images = $m_image->getRows(array(
                'cert_id' => $o_cert_row->uid
            ));

            foreach ($images as $image_row) {
                $image_row->image_url = $image_arr[$image_row['image_key']]['image_url'];
                $image_row->image_sha = $image_arr[$image_row['image_key']]['image_sha'];
                $up = $image_row->update();
                if (!$up->STS) {
                    return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));

        } else {


            //更新原来通过的为过期状态
            $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . certificationTypeEnum::ID . "' and verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_cert->conn->execute($sql);
            if (!$up->STS) {
                return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
            }

            // add
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = certificationTypeEnum::ID;
            $new_row->cert_name = $en_name;
            $new_row->cert_name_json = $name_json;
            $new_row->cert_sn = $cert_sn;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }


    }


    public static function familyBookVerifyCertNew($params, $source = certSourceTypeEnum::MEMBER)
    {
        $cert_type = certificationTypeEnum::FAIMILYBOOK;
        // 文件保存目录
        $save_path = fileDirsEnum::FAMILY_BOOK;

        $member_id = intval($params['member_id']);

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $cert_files = $_FILES;
        $page_data = (new member_profileClass())->getInitPageData()[$cert_type];
        if (!empty($page_data['input_field_list'])) {
            foreach ($page_data['input_field_list'] as $item) {
                if ($item['is_required'] && !$params[$item['field_name']]) {
                    return new result(false, 'Invalid param:' . $item['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }

        if (!empty($page_data['upload_image_list'])) {
            foreach ($page_data['upload_image_list'] as $item) {
                if ($item['is_required'] && !$cert_files[$item['field_name']]) {
                    return new result(false, 'Invalid param:' . $item['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }


        $image_arr = array();
        foreach ($cert_files as $field => $photo) {

            if (!empty($photo) && $photo['size'] > 0) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail:'.$upload->getError(), null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->img_url;
                $full_path = $upload->full_path;
                $image_arr[] = array(
                    'image_url' => $img_path,
                    'image_sha' => sha1_file($full_path),
                    'image_key' => $field
                );
                unset($upload);
            }

        }


        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();


        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => $cert_type
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {
            // 删除图片
            $sql = "delete from member_verify_cert_image where cert_id=" . qstr($o_cert_row['uid']);
            $del = $m_cert->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Del image fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
            }

            // 删除记录
            $del = $o_cert_row->delete();
            if (!$del->STS) {
                return new result(false, 'Del cert row fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
            }


        }

        //更新原来通过的为过期状态
        $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . $cert_type . "' and verify_state='" . certStateEnum::PASS . "'  ";
        $up = $m_cert->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
        }

        // 添加新记录
        $new_row = $m_cert->newRow();
        $new_row->member_id = $member_id;
        $new_row->cert_type = $cert_type;
        $new_row->verify_state = certStateEnum::CREATE;
        $new_row->source_type = $source;
        $new_row->create_time = Now();
        $insert = $new_row->insert();
        if (!$insert->STS) {
            return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
        }

        foreach ($image_arr as $value) {
            $row = $m_image->newRow();
            $row->cert_id = $new_row->uid;
            $row->image_key = $value['image_key'];
            $row->image_url = $value['image_url'];
            $row->image_sha = $value['image_sha'];
            $insert = $row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true, 'success', array(
            'cert_result' => $new_row,
            'extend_info' => null
        ));

    }

    /** 户口本认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function familyBookVerifyCert($params, $source = 0)
    {
        $member_id = intval($params['member_id']);
        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }
        $files = $_FILES;
        $householder_photo = $files['householder_photo'];
        $front_photo = $files['front_photo'];
        $back_photo = $files['back_photo'];

        if (empty($householder_photo) || empty($front_photo) || empty($back_photo)) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }

        // key 是上传表单名
        $photos = array(
            'front_photo' => '',
            'back_photo' => '',
            'householder_photo' => '',
        );

        // 保存目录
        $save_path = fileDirsEnum::FAMILY_BOOK;

        foreach ($photos as $field => $photo) {

            $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
            $upload->set('save_path', null);
            $upload->set('default_dir', $save_path);
            $re = $upload->server2upun($field);
            if ($re == false) {
                return new result(false, 'Upload photo fail:' . $field, null, errorCodesEnum::API_FAILED);
            }
            $img_path = $upload->img_url;
            $photos[$field] = $img_path;
            unset($upload);
        }

        $image_arr = array(
            certImageKeyEnum::FAMILY_BOOK_FRONT => array(
                'image_url' => $photos['front_photo'],
                'image_sha' => sha1_file(getImageUrl($photos['front_photo']))
            ),
            certImageKeyEnum::FAMILY_BOOK_BACK => array(
                'image_url' => $photos['back_photo'],
                'image_sha' => sha1_file(getImageUrl($photos['back_photo']))
            ),
            certImageKeyEnum::FAMILY_BOOK_HOUSEHOLD => array(
                'image_url' => $photos['householder_photo'],
                'image_sha' => sha1_file(getImageUrl($photos['householder_photo']))
            ),
            /* certImageKeyEnum::FAMILY_RELATION_CERT_PHOTO => array(
                 'image_url' => $photos['relation_photo'],
                 'image_sha' => sha1_file(getImageUrl($photos['relation_photo']))
             )*/
        );

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();


        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => certificationTypeEnum::FAIMILYBOOK
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {

            // edit

            $images = $m_image->getRows(array(
                'cert_id' => $o_cert_row->uid
            ));

            foreach ($images as $image_row) {
                $image_row->image_url = $image_arr[$image_row['image_key']]['image_url'];
                $image_row->image_sha = $image_arr[$image_row['image_key']]['image_sha'];
                $up = $image_row->update();
                if (!$up->STS) {
                    return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));

        } else {


            //更新原来通过的为过期状态
            $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . certificationTypeEnum::FAIMILYBOOK . "' and verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_cert->conn->execute($sql);
            if (!$up->STS) {
                return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
            }

            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = certificationTypeEnum::FAIMILYBOOK;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }


    }


    public static function residentBookCertNew($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::RESIDENT_BOOK;
        // 文件保存目录
        $save_path = fileDirsEnum::RESIDENT_BOOK;

        $member_id = intval($params['member_id']);

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $cert_files = $_FILES;
        $page_data = (new member_profileClass())->getInitPageData()[$cert_type];
        if (!empty($page_data['input_field_list'])) {
            foreach ($page_data['input_field_list'] as $item) {
                if ($item['is_required'] && !$params[$item['field_name']]) {
                    return new result(false, 'Invalid param:' . $item['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }

        if (!empty($page_data['upload_image_list'])) {
            foreach ($page_data['upload_image_list'] as $item) {
                if ($item['is_required'] && !$cert_files[$item['field_name']]) {
                    return new result(false, 'Invalid param:' . $item['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }


        $image_arr = array();
        foreach ($cert_files as $field => $photo) {

            if (!empty($photo) && $photo['size'] > 0) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail:'.$upload->getError(), null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->img_url;
                $full_path = $upload->full_path;
                $image_arr[] = array(
                    'image_url' => $img_path,
                    'image_sha' => sha1_file($full_path),
                    'image_key' => $field
                );
                unset($upload);
            }

        }


        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();


        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => $cert_type
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {
            // 删除图片
            $sql = "delete from member_verify_cert_image where cert_id=" . qstr($o_cert_row['uid']);
            $del = $m_cert->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Del image fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
            }

            // 删除记录
            $del = $o_cert_row->delete();
            if (!$del->STS) {
                return new result(false, 'Del cert row fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
            }


        }

        //更新原来通过的为过期状态
        $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . $cert_type . "' and verify_state='" . certStateEnum::PASS . "'  ";
        $up = $m_cert->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
        }

        // 添加新记录
        $new_row = $m_cert->newRow();
        $new_row->member_id = $member_id;
        $new_row->cert_type = $cert_type;
        $new_row->verify_state = certStateEnum::CREATE;
        $new_row->source_type = $source;
        $new_row->create_time = Now();
        $insert = $new_row->insert();
        if (!$insert->STS) {
            return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
        }

        foreach ($image_arr as $value) {
            $row = $m_image->newRow();
            $row->cert_id = $new_row->uid;
            $row->image_key = $value['image_key'];
            $row->image_url = $value['image_url'];
            $row->image_sha = $value['image_sha'];
            $insert = $row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true, 'success', array(
            'cert_result' => $new_row,
            'extend_info' => null
        ));

    }

    /** 居住证认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function residentBookCert($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::RESIDENT_BOOK;
        $member_id = intval($params['member_id']);
        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }
        $files = $_FILES;
        $front_photo = $files['front_photo'];
        $back_photo = $files['back_photo'];

        if (empty($front_photo) || empty($back_photo)) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }

        // key 是上传表单名
        $photos = array(
            'front_photo' => '',
            'back_photo' => '',
        );

        // 保存目录
        $save_path = fileDirsEnum::RESIDENT_BOOK;

        foreach ($photos as $field => $photo) {

            $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
            $upload->set('save_path', null);
            $upload->set('default_dir', $save_path);
            $re = $upload->server2upun($field);
            if ($re == false) {
                return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
            }
            $img_path = $upload->img_url;
            $photos[$field] = $img_path;
            unset($upload);
        }

        $image_arr = array(
            certImageKeyEnum::RESIDENT_BOOK_FRONT => array(
                'image_url' => $photos['front_photo'],
                'image_sha' => sha1_file(getImageUrl($photos['front_photo']))
            ),
            certImageKeyEnum::RESIDENT_BOOK_BACK => array(
                'image_url' => $photos['back_photo'],
                'image_sha' => sha1_file(getImageUrl($photos['back_photo']))
            )
        );

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();

        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => $cert_type
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {

            // edit
            $images = $m_image->getRows(array(
                'cert_id' => $o_cert_row->uid
            ));
            foreach ($images as $image_row) {
                $image_row->image_url = $image_arr[$image_row['image_key']]['image_url'];
                $image_row->image_sha = $image_arr[$image_row['image_key']]['image_sha'];
                $up = $image_row->update();
                if (!$up->STS) {
                    return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));

        } else {


            //更新原来通过的为过期状态
            $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . $cert_type . "' and verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_cert->conn->execute($sql);
            if (!$up->STS) {
                return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
            }

            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = $cert_type;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }


    }


    /**  护照认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function passportCertNew($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::PASSPORT;
        // 文件保存目录
        $save_path = fileDirsEnum::PASSPORT;

        $member_id = intval($params['member_id']);

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $cert_files = $_FILES;
        $page_data = (new member_profileClass())->getInitPageData()[$cert_type];
        if (!empty($page_data['input_field_list'])) {
            foreach ($page_data['input_field_list'] as $item) {
                if ($item['is_required'] && !$params[$item['field_name']]) {
                    return new result(false, 'Invalid param:' . $item['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }

        if (!empty($page_data['upload_image_list'])) {
            foreach ($page_data['upload_image_list'] as $item) {
                if ($item['is_required'] && !$cert_files[$item['field_name']]) {
                    return new result(false, 'Invalid param:' . $item['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }


        $image_arr = array();
        foreach ($cert_files as $field => $photo) {

            if (!empty($photo) && $photo['size'] > 0) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail:'.$upload->getError(), null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->img_url;
                $full_path = $upload->full_path;
                $image_arr[] = array(
                    'image_url' => $img_path,
                    'image_sha' => sha1_file($full_path),
                    'image_key' => $field
                );
                unset($upload);
            }

        }


        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();


        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => $cert_type
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {
            // 删除图片
            $sql = "delete from member_verify_cert_image where cert_id=" . qstr($o_cert_row['uid']);
            $del = $m_cert->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Del image fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
            }

            // 删除记录
            $del = $o_cert_row->delete();
            if (!$del->STS) {
                return new result(false, 'Del cert row fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
            }


        }

        //更新原来通过的为过期状态
        $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . $cert_type . "' and verify_state='" . certStateEnum::PASS . "'  ";
        $up = $m_cert->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
        }

        // 添加新记录
        $new_row = $m_cert->newRow();
        $new_row->member_id = $member_id;
        $new_row->cert_type = $cert_type;
        $new_row->verify_state = certStateEnum::CREATE;
        $new_row->source_type = $source;
        $new_row->create_time = Now();
        $insert = $new_row->insert();
        if (!$insert->STS) {
            return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
        }

        foreach ($image_arr as $value) {
            $row = $m_image->newRow();
            $row->cert_id = $new_row->uid;
            $row->image_key = $value['image_key'];
            $row->image_url = $value['image_url'];
            $row->image_sha = $value['image_sha'];
            $insert = $row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true, 'success', array(
            'cert_result' => $new_row,
            'extend_info' => null
        ));

    }


    /** 出生证明
     * @param $params
     * @param int $source
     * @return result
     */
    public static function birthdayCertNew($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::BIRTH_CERTIFICATE;
        // 文件保存目录
        $save_path = fileDirsEnum::BIRTH_CERT;

        $member_id = intval($params['member_id']);

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $cert_files = $_FILES;
        $page_data = (new member_profileClass())->getInitPageData()[$cert_type];
        if (!empty($page_data['input_field_list'])) {
            foreach ($page_data['input_field_list'] as $item) {
                if ($item['is_required'] && !$params[$item['field_name']]) {
                    return new result(false, 'Invalid param:' . $item['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }

        if (!empty($page_data['upload_image_list'])) {
            foreach ($page_data['upload_image_list'] as $item) {
                if ($item['is_required'] && !$cert_files[$item['field_name']]) {
                    return new result(false, 'Invalid param:' . $item['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }


        $image_arr = array();
        foreach ($cert_files as $field => $photo) {

            if (!empty($photo) && $photo['size'] > 0) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail:'.$upload->getError(), null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->img_url;
                $full_path = $upload->full_path;
                $image_arr[] = array(
                    'image_url' => $img_path,
                    'image_sha' => sha1_file($full_path),
                    'image_key' => $field
                );
                unset($upload);
            }

        }


        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();


        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => $cert_type
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {
            // 删除图片
            $sql = "delete from member_verify_cert_image where cert_id=" . qstr($o_cert_row['uid']);
            $del = $m_cert->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Del image fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
            }

            // 删除记录
            $del = $o_cert_row->delete();
            if (!$del->STS) {
                return new result(false, 'Del cert row fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
            }


        }

        //更新原来通过的为过期状态
        $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . $cert_type . "' and verify_state='" . certStateEnum::PASS . "'  ";
        $up = $m_cert->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
        }

        // 添加新记录
        $new_row = $m_cert->newRow();
        $new_row->member_id = $member_id;
        $new_row->cert_type = $cert_type;
        $new_row->verify_state = certStateEnum::CREATE;
        $new_row->source_type = $source;
        $new_row->create_time = Now();
        $insert = $new_row->insert();
        if (!$insert->STS) {
            return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
        }

        foreach ($image_arr as $value) {
            $row = $m_image->newRow();
            $row->cert_id = $new_row->uid;
            $row->image_key = $value['image_key'];
            $row->image_url = $value['image_url'];
            $row->image_sha = $value['image_sha'];
            $insert = $row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true, 'success', array(
            'cert_result' => $new_row,
            'extend_info' => null
        ));

    }


    /** 会员家庭关系证明(已取消)
     * @param $params
     * @param int $source
     * @return result
     */
    public static function familyRelationshipCert($params, $source = 0)
    {
        $member_id = intval($params['member_id']);
        $relation_type = $params['relation_type'];
        $relation_name = $params['relation_name'];
        $relation_cert_type = $params['relation_cert_type'];
        $country_code = $params['country_code'];
        $relation_phone = $params['relation_phone']; // relation_phone

        if (empty($_FILES['relation_cert_photo'])) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $phone_arr = tools::getFormatPhone($country_code, $relation_phone);
        $contact_phone = $phone_arr['contact_phone'];

        // 保存目录
        $save_path = fileDirsEnum::FAMILY_RELATION;
        $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
        $upload->set('save_path', null);
        $upload->set('default_dir', $save_path);
        $re = $upload->server2upun('relation_cert_photo');
        if ($re == false) {
            return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
        }
        $img_path = $upload->img_url;
        unset($upload);

        $image_arr = array(
            certImageKeyEnum::FAMILY_RELATION_CERT_PHOTO => array(
                'image_url' => $img_path,
                'image_sha' => sha1_file(getImageUrl($img_path))
            )
        );


        $m_cert = new member_verify_certModel();
        $m_family = new member_familyModel();
        $m_image = new member_verify_cert_imageModel();

        $o_cert_row = null;
        if ($params['cert_id']) {
            $cert_id = intval($params['cert_id']);
            $o_cert_row = $m_cert->getRow($cert_id);
        }

        if ($o_cert_row && $o_cert_row->verify_state == certStateEnum::CREATE) {

            // edit
            $images = $m_image->getRows(array(
                'cert_id' => $o_cert_row->uid
            ));
            foreach ($images as $image_row) {
                $image_row->image_url = $image_arr[$image_row['image_key']]['image_url'];
                $image_row->image_sha = $image_arr[$image_row['image_key']]['image_sha'];
                $up = $image_row->update();
                if (!$up->STS) {
                    return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            $family_row = $m_family->getRow(array(
                'cert_id' => $o_cert_row->uid
            ));
            if ($family_row) {
                $family_row->relation_type = $relation_type;
                $family_row->relation_name = $relation_name;
                $family_row->relation_cert_type = $relation_cert_type;
                $family_row->relation_cert_photo = $img_path;
                $family_row->relation_phone = $contact_phone;
                $up = $family_row->update();
                if (!$up->STS) {
                    return new result(false, 'Modify fail', null, errorCodesEnum::DB_ERROR);
                }

            } else {
                $family_row = $m_family->newRow();
                $family_row->cert_id = $o_cert_row->uid;
                $family_row->member_id = $member_id;
                $family_row->relation_type = $relation_type;
                $family_row->relation_name = $relation_name;
                $family_row->relation_cert_type = $relation_cert_type;
                $family_row->relation_cert_photo = $img_path;
                $family_row->relation_phone = $contact_phone;
                $family_row->create_time = Now();
                $in = $family_row->insert();
                if (!$in->STS) {
                    return new result(false, 'Add family member fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => $family_row
            ));


        } else {

            // add
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = certificationTypeEnum::FAMILY_RELATIONSHIP;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            $row = $m_family->newRow();
            $row->cert_id = $new_row->uid;
            $row->member_id = $member_id;
            $row->relation_type = $relation_type;
            $row->relation_name = $relation_name;
            $row->relation_cert_type = $relation_cert_type;
            $row->relation_cert_photo = $img_path;
            $row->relation_phone = $contact_phone;
            $row->create_time = Now();
            $in = $row->insert();
            if (!$in->STS) {
                return new result(false, 'Add family member fail', null, errorCodesEnum::DB_ERROR);
            }
            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => $row
            ));

        }


    }


    /** 会员工作认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function workCert($params, $source = 0)
    {
        $member_id = $params['member_id'];
        $company_name = $params['company_name'];
        $company_addr = $params['company_address'];
        $position = $params['position'];
        $is_government = 0;
        $company_phone = $params['company_phone'];
        $monthly_income = round($params['monthly_income']);
        $currency = $params['currency'];


        $front_photo = $_FILES['work_card'];
        $back_photo = $_FILES['employment_certification'];
        if (empty($front_photo) || empty($back_photo)) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // key 是上传表单名
        $photos = array(
            'work_card' => '',
            'employment_certification' => '',
        );

        // 保存目录
        $save_path = fileDirsEnum::WORK_CERT;

        foreach ($photos as $field => $photo) {

            $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
            $upload->set('save_path', null);
            $upload->set('default_dir', $save_path);
            $re = $upload->server2upun($field);
            if ($re == false) {
                return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
            }
            $img_path = $upload->img_url;
            $photos[$field] = $img_path;
            unset($upload);
        }

        $image_arr = array(
            certImageKeyEnum::WORK_CARD => array(
                'image_url' => $photos['work_card'],
                'image_sha' => sha1_file(getImageUrl($photos['work_card']))
            ),
            certImageKeyEnum::WORK_EMPLOYMENT_CERTIFICATION => array(
                'image_url' => $photos['employment_certification'],
                'image_sha' => sha1_file(getImageUrl($photos['employment_certification']))
            )
        );


        $m_cert = new member_verify_certModel();
        $m_work = new member_workModel();
        $m_image = new member_verify_cert_imageModel();


        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => certificationTypeEnum::WORK_CERTIFICATION
        ));

        if ($o_cert_row && $o_cert_row->verify_state == certStateEnum::CREATE) {

            // 编辑
            $images = $m_image->getRows(array(
                'cert_id' => $o_cert_row->uid
            ));
            foreach ($images as $image_row) {
                $image_row->image_url = $image_arr[$image_row['image_key']]['image_url'];
                $image_row->image_sha = $image_arr[$image_row['image_key']]['image_sha'];
                $up = $image_row->update();
                if (!$up->STS) {
                    return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            $work_row = $m_work->getRow(array(
                'cert_id' => $o_cert_row->uid
            ));

            if ($work_row) {
                $work_row->company_name = $company_name;
                $work_row->company_addr = $company_addr;
                $work_row->company_phone = $company_phone;
                $work_row->month_salary = $monthly_income;
                $work_row->currency = $currency;
                $work_row->position = $position;
                $work_row->is_government = $is_government;
                $work_row->state = workStateStateEnum::CREATE;
                $up = $work_row->update();
                if (!$up->STS) {
                    return new result(false, 'Modify fail', null, errorCodesEnum::DB_ERROR);
                }

            } else {
                $work_row = $m_work->newRow();
                $work_row->cert_id = $o_cert_row->uid;
                $work_row->member_id = $member_id;
                $work_row->company_name = $company_name;
                $work_row->company_addr = $company_addr;
                $work_row->company_phone = $company_phone;
                $work_row->month_salary = $monthly_income;
                $work_row->currency = $currency;
                $work_row->position = $position;
                $work_row->is_government = $is_government;
                $work_row->create_time = Now();
                $in = $work_row->insert();
                if (!$in->STS) {
                    return new result(false, 'Add work cert fail', null, errorCodesEnum::DB_ERROR);
                }

            }

            $work_row->photo1 = $photos['work_card'];
            $work_row->photo2 = $photos['employment_certification'];

            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => $work_row
            ));

        } else {


            //更新原来通过的为过期状态
            $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . certificationTypeEnum::WORK_CERTIFICATION . "' and verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_cert->conn->execute($sql);
            if (!$up->STS) {
                return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
            }

            // 当前只能有一条合法的，其余的更新为历史
            $sql = "update member_work set state='" . workStateStateEnum::HISTORY . "' where member_id='" . $member_id . "' and state='" . workStateStateEnum::VALID . "' ";
            $up = $m_cert->conn->execute($sql);
            if (!$up->STS) {
                return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
            }


            // 新增
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = certificationTypeEnum::WORK_CERTIFICATION;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            $row = $m_work->newRow();
            $row->cert_id = $new_row->uid;
            $row->member_id = $member_id;
            $row->company_name = $company_name;
            $row->company_addr = $company_addr;
            $row->company_phone = $company_phone;
            $row->month_salary = $monthly_income;
            $row->currency = $currency;
            $row->position = $position;
            $row->is_government = $is_government;
            $row->create_time = Now();
            $in = $row->insert();
            if (!$in->STS) {
                return new result(false, 'Add work cert fail', null, errorCodesEnum::DB_ERROR);
            }

            $row->photo1 = $photos['work_card'];
            $row->photo2 = $photos['employment_certification'];

            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => $row
            ));

        }


    }

    /** 资产认证
     * @param $param
     * @return result
     */
    public static function assetCert($params, $source = 0)
    {
        // 可以多条
        $asset_type = $params['type'];
        switch ($asset_type) {
            case 'motorbike':
                return self::motorbikeCert($params, $source);
                break;
            case 'house':
                return self::houseCert($params, $source);
                break;
            case 'store':
                return self::storeCert($params, $source);
                break;
            case 'car':
                return self::carCert($params, $source);
                break;
            case 'land':
                return self::landCert($params, $source);
                break;
            default:
                return new result(false, 'Unsurpport type', null, errorCodesEnum::NOT_SUPPORTED);
        }

    }

    public static function assetCertNew($params, $source = 0)
    {
        $cert_type = $params['type'];
        $file_dir = fileDirsEnum::MEMBER_ASSETS;
        $member_id = $params['member_id'];
        $memberObj = new objectMemberClass($member_id);
        $member = $memberObj->object_info;

        $asset_name = trim($params['asset_name']);
        $asset_sn = trim($params['asset_sn']);
        $asset_cert_type = $params['asset_cert_type'];
        $asset_image_source = json_decode(urldecode($params['asset_image_type']), true);
        $cert_issue_time = $params['certificate_time'] ? date('Y-m-d', strtotime($params['certificate_time'])) : null;

        if ($params['officer_id']) {
            $user_info = (new um_userModel())->getUserInfoById($params['officer_id']);
            $user_id = $user_info['uid'];
            $user_name = $user_info['user_name'];
        } else {
            $user_id = 0;
            $user_name = 'System';
        }

        $asset_files = $_FILES;

        $coord_x = round($params['coord_x'], 6);
        $coord_y = round($params['coord_y'], 6);
        $address_detail = $params['address_detail'];

        $assetClass = new member_assetsClass();
        $init_page_data = $assetClass->getAssetPageDataByType($cert_type);

        // 检查必传字段
        $filed_arr = $init_page_data['input_field_list'];
        foreach ($filed_arr as $item) {
            if ($item['is_required']) {
                if (!$params[$item['field_name']]) {
                    return new result(false, 'Lack of param:' . $item['field_name'], null, errorCodesEnum::DATA_LACK);
                }
            }
        }

        $image_field_arr = $init_page_data['upload_image_list'];
        foreach ($image_field_arr as $v) {
            if ($v['is_required']) {
                if (empty($asset_files[$v['field_name']])) {
                    return new result(false, 'Lack of photo:' . $v['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }


        $m_asset = new member_assetsModel();
        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();
        $chk_sn = $m_asset->find(array(
            'asset_sn' => $asset_sn,
            'asset_type' => $cert_type,
            'asset_state' => array('>=', assetStateEnum::CERTIFIED)
        ));
        if ($chk_sn) {
            return new result(false, 'Asset sn already existed.', null, errorCodesEnum::ASSET_SN_DUPLICATION);
        }

        $image_arr = array();
        foreach ($asset_files as $field => $v) {
            if (!empty($v) && $v['size'] > 0) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $file_dir);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail:' . $upload->getError(), null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->img_url;
                if (startWith($field, 'asset_image')) {
                    if ($params[$field . '_key']) {
                        $image_key = $params[$field . '_key'];
                    } else {
                        $image_key = mt_rand(10, 99) . time();
                    }
                } else {
                    $image_key = $field;
                }
                $image_source = $asset_image_source[$field] ? imageSourceEnum::ALBUM : imageSourceEnum::CAMERA;
                $image_arr[] = array(
                    'image_key' => $image_key,
                    'image_url' => $img_path,
                    'image_sha' => sha1_file($upload->full_path),
                    'image_source' => $image_source

                );
                unset($upload);
            }
        }

        // todo 更换检查方式
        $o_cert_row = null;
        if ($params['cert_id']) {
            $cert_id = intval($params['cert_id']);
            $o_cert_row = $m_cert->getRow($cert_id);
        }

        if ($o_cert_row) {


            // 是编辑
            if ($o_cert_row->cert_type != $cert_type || $o_cert_row['verify_state'] != certStateEnum::CREATE) {
                return new result(false, 'Un-match cert type.', null, errorCodesEnum::UN_MATCH_OPERATION);
            }

            // 更新主认证信息
            $o_cert_row->cert_issue_time = $cert_issue_time;
            $o_cert_row->creator_id = $user_id;
            $o_cert_row->creator_name = $user_name;
            $o_cert_row->update_time = Now();
            $o_cert_row->update();

            // 更新资产信息
            $asset_info = $m_asset->getRow(array(
                'cert_id' => $o_cert_row->uid
            ));
            if (!$asset_info) {
                return new result(false, 'No asset info:cert id ' . $o_cert_row->uid, null, errorCodesEnum::INVALID_PARAM);
            }

            // 资产是否可编辑
            if (!member_assetsClass::assetIsCanEdit($asset_info)) {
                return new result(false, 'Un editable.', null, errorCodesEnum::UN_EDITABLE);
            }


            // 重新插入图片
            $sql = "delete from member_verify_cert_image where cert_id='" . $o_cert_row->uid . "' ";
            $del = $m_cert->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Delete old image fail.', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $o_cert_row->uid;
                $row->image_key = $value['image_key'];
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $row->image_source = $value['image_source'];
                $row->creator_id = $user_id;
                $row->creator_name = $user_name;
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            $asset_info->asset_name = $asset_name;
            $asset_info->asset_sn = $asset_sn;
            $asset_info->asset_cert_type = $asset_cert_type;
            $asset_info->coord_x = $coord_x;
            $asset_info->coord_y = $coord_y;
            $asset_info->address_detail = $address_detail;
            $asset_info->update_time = Now();
            $up = $asset_info->update();
            if (!$up->STS) {
                return new result(false, 'Edit asset fail.', null, errorCodesEnum::DB_ERROR);
            }

            if ($params['relative_id'] == null) {
                $params['relative_id'] = '0';
            }
            $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset_info->uid, $params['relative_id']);
            if (!$rt->STS) {
                return $rt;
            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));


        } else {


            $now = Now();
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = $cert_type;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = $now;
            $new_row->cert_issue_time = $cert_issue_time;
            $new_row->creator_id = $user_id;
            $new_row->creator_name = $user_name;
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
            }
            $cert_id = $new_row->uid;

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $value['image_key'];
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $row->image_source = $value['image_source'];
                $row->creator_id = $user_id;
                $row->creator_name = $user_name;
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            // 插入资产表
            $asset = $m_asset->newRow();
            $asset->cert_id = $cert_id;
            $asset->member_id = $member_id;
            $asset->asset_name = $asset_name;
            $asset->asset_type = $cert_type;
            $asset->asset_sn = $asset_sn;
            $asset->asset_cert_type = $asset_cert_type;
            $asset->coord_x = $coord_x;
            $asset->coord_y = $coord_y;
            $asset->address_detail = $address_detail;
            $asset->create_time = $now;
            $insert = $asset->insert();
            if (!$insert->STS) {
                return new result(false, 'Add member asset fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
            }

            if ($params['relative_id'] == null) {
                $params['relative_id'] = '0';
            }
            $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset->uid, $params['relative_id']);
            if (!$rt->STS) {
                return $rt;
            }


            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }


    }


    /** 摩托车认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function motorbikeCert($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::MOTORBIKE;
        $file_dir = fileDirsEnum::MEMBER_ASSETS;

        $member_id = intval($params['member_id']);
        $asset_name = trim($params['asset_name']);
        $asset_sn = trim($params['asset_sn']);
        $asset_cert_type = $params['asset_cert_type'];
        if ($params['cert_issue_time']) {
            $cert_issue_time = date('Y-m-d', strtotime($params['cert_issue_time']));
        }

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }
        $files = $_FILES;
        $motorbike_photo = $files['motorbike_photo'];
        $certificate_front = $files['certificate_front'];
        $certificate_back = $files['certificate_back'];

        if (!$asset_name) {
            return new result(false, 'No asset name.', null, errorCodesEnum::INVALID_PARAM);
        }

        if (!$asset_sn) {
            return new result(false, 'No asset sn.', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_asset = new member_assetsModel();
        $chk_sn = $m_asset->find(array(
                'asset_sn' => $asset_sn,
                'asset_type' => $cert_type,
                'asset_state' => array('>=', assetStateEnum::CERTIFIED)
            )
        );
        // todo check 如果是认证后修改有bug，现在是认证不可修改
        if ($chk_sn) {
            return new result(false, 'Asset sn already existed.', null, errorCodesEnum::ASSET_SN_DUPLICATION);
        }

        /* if (empty($motorbike_photo) || empty($certificate_front) || empty($certificate_back)) {
             return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
         }*/

        // key 是上传表单名
        $photos = array(
            'motorbike_photo' => certImageKeyEnum::MOTORBIKE_PHOTO,
            'certificate_front' => certImageKeyEnum::MOTORBIKE_CERT_FRONT,
            'certificate_back' => certImageKeyEnum::MOTORBIKE_CERT_BACK
        );

        // 保存目录
        $save_path = $file_dir;

        $image_arr = array();

        foreach ($photos as $field => $photo) {

            if (!empty($files[$field])) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->img_url;
                $image_arr[$photos[$field]] = array(
                    'image_url' => $img_path,
                    'image_sha' => sha1_file($upload->full_path)
                );
                //$photos[$field] = $img_path;

                unset($upload);
            }

        }

        /*$image_arr = array(
            certImageKeyEnum::MOTORBIKE_PHOTO => array(
                'image_url' => $photos['motorbike_photo'],
                'image_sha' => sha1_file(getImageUrl($photos['motorbike_photo']))

            ),
            certImageKeyEnum::MOTORBIKE_CERT_FRONT => array(
                'image_url' => $photos['certificate_front'],
                'image_sha' => sha1_file(getImageUrl($photos['certificate_front']))
            ),
            certImageKeyEnum::MOTORBIKE_CERT_BACK => array(
                'image_url' => $photos['certificate_back'],
                'image_sha' => sha1_file(getImageUrl($photos['certificate_back']))
            )
        );*/

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();

        // 额外的图片
        foreach ($_FILES as $key => $u_file) {

            if (startWith($key, 'asset_image')) {

                if (!empty($u_file)) {
                    $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                    $upload->set('save_path', null);
                    $upload->set('default_dir', $save_path);
                    $re = $upload->server2upun($key);
                    if ($re == false) {
                        return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                    }

                    if ($params[$key . '_key']) {
                        $image_key = $params[$key . '_key'];
                    } else {
                        $image_key = mt_rand(10, 99) . time();
                    }

                    $image_arr[$image_key] = array(
                        'image_url' => $upload->img_url,
                        'image_sha' => sha1_file($upload->full_path)
                    );
                    unset($upload);
                }
            }
        }

        $o_cert_row = null;
        if ($params['cert_id']) {
            $cert_id = intval($params['cert_id']);
            $o_cert_row = $m_cert->getRow($cert_id);
        }

        if ($o_cert_row) {


            // 是编辑
            if ($o_cert_row->cert_type != $cert_type || $o_cert_row['verify_state'] != certStateEnum::CREATE) {
                return new result(false, 'Un-match cert type.', null, errorCodesEnum::UN_MATCH_OPERATION);
            }

            if ($cert_issue_time) {
                $o_cert_row->cert_issue_time = $cert_issue_time;
                $o_cert_row->update_time = Now();
                $rt = $o_cert_row->update();
                if (!$rt->STS) {
                    return new result(false, 'Edit asset fail.', null, errorCodesEnum::DB_ERROR);
                }
            }

            // 更新资产信息
            $asset_info = $m_asset->getRow(array(
                'cert_id' => $o_cert_row->uid
            ));
            if (!$asset_info) {
                return new result(false, 'No asset info:cert id ' . $o_cert_row->uid, null, errorCodesEnum::INVALID_PARAM);
            }

            // 资产是否可编辑
            if (!member_assetsClass::assetIsCanEdit($asset_info)) {
                return new result(false, 'Un editable.', null, errorCodesEnum::UN_EDITABLE);
            }


            // 重新插入图片
            $sql = "delete from member_verify_cert_image where cert_id='" . $o_cert_row->uid . "' ";
            $del = $m_cert->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Delete old image fail.', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $o_cert_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            $asset_info->asset_name = $asset_name;
            $asset_info->asset_sn = $asset_sn;
            $asset_info->asset_cert_type = $asset_cert_type;
            $asset_info->update_time = Now();
            $up = $asset_info->update();
            if (!$up->STS) {
                return new result(false, 'Edit asset fail.', null, errorCodesEnum::DB_ERROR);
            }

//            if ($params['relative_id']) {
            $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset_info->uid, $params['relative_id']);
            if (!$rt->STS) {
                return $rt;
            }
//            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));


        } else {

            $now = Now();
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = $cert_type;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            if ($cert_issue_time) {
                $new_row->cert_issue_time = $cert_issue_time;
            }
            $new_row->create_time = $now;
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }
            $cert_id = $new_row->uid;

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            // 插入资产表
            $asset = $m_asset->newRow();
            $asset->cert_id = $cert_id;
            $asset->member_id = $member_id;
            $asset->asset_name = $asset_name;
            $asset->asset_type = $cert_type;
            $asset->asset_sn = $asset_sn;
            $asset->asset_cert_type = $asset_cert_type;
            $asset->create_time = $now;
            $insert = $asset->insert();
            if (!$insert->STS) {
                return new result(false, 'Add member asset fail', null, errorCodesEnum::DB_ERROR);
            }

//            if ($params['relative_id']) {
            $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset->uid, $params['relative_id']);
            if (!$rt->STS) {
                return $rt;
            }
//            }

            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }
    }


    /** 汽车认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function carCert($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::CAR;
        $file_dir = fileDirsEnum::MEMBER_ASSETS;

        $member_id = intval($params['member_id']);
        $asset_name = trim($params['asset_name']);
        $asset_sn = trim($params['asset_sn']);
        $asset_cert_type = $params['asset_cert_type'];
        if ($params['cert_issue_time']) {
            $cert_issue_time = date('Y-m-d', strtotime($params['cert_issue_time']));
        }

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }

        if (!$asset_name) {
            return new result(false, 'No asset name.', null, errorCodesEnum::INVALID_PARAM);
        }

        if (!$asset_sn) {
            return new result(false, 'No asset sn.', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_asset = new member_assetsModel();
        $chk_sn = $m_asset->find(array(
                'asset_sn' => $asset_sn,
                'asset_type' => $cert_type,
                'asset_state' => array('>=', assetStateEnum::CERTIFIED)
            )
        );
        if ($chk_sn) {
            return new result(false, 'Asset sn already existed.', null, errorCodesEnum::ASSET_SN_DUPLICATION);
        }

        $files = $_FILES;
        $car_front = $files['car_front'];
        $car_back = $files['car_back'];
        $certificate_front = $files['certificate_front'];
        $certificate_back = $files['certificate_back'];

        /*if (empty($car_front) || empty($car_back) || empty($certificate_front) || empty($certificate_back)) {
            return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
        }*/

        // key 是上传表单名
        $photos = array(
            'car_front' => certImageKeyEnum::CAR_FRONT,
            'car_back' => certImageKeyEnum::CAR_BACK,
            'certificate_front' => certImageKeyEnum::CAR_CERT_FRONT,
            'certificate_back' => certImageKeyEnum::CAR_CERT_BACK
        );

        // 保存目录
        $save_path = $file_dir;

        $image_arr = array();

        foreach ($photos as $field => $photo) {

            if (!empty($files[$field])) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->img_url;
                $image_arr[$photos[$field]] = array(
                    'image_url' => $img_path,
                    'image_sha' => sha1_file($upload->full_path)
                );
                //$photos[$field] = $img_path;
                unset($upload);
            }
        }

        /* $image_arr = array(
             certImageKeyEnum::CAR_FRONT => array(
                 'image_url' => $photos['car_front'],
                 'image_sha' => sha1_file(getImageUrl($photos['car_front']))
             ),
             certImageKeyEnum::CAR_BACK => array(
                 'image_url' => $photos['car_back'],
                 'image_sha' => sha1_file(getImageUrl($photos['car_back']))
             ),
             certImageKeyEnum::CAR_CERT_FRONT => array(
                 'image_url' => $photos['certificate_front'],
                 'image_sha' => sha1_file(getImageUrl($photos['certificate_front']))
             ),
             certImageKeyEnum::CAR_CERT_BACK => array(
                 'image_url' => $photos['certificate_back'],
                 'image_sha' => sha1_file(getImageUrl($photos['certificate_back']))
             )
         );*/

        // 额外的图片
        foreach ($_FILES as $key => $u_file) {

            if (startWith($key, 'asset_image')) {

                if (!empty($u_file)) {
                    $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                    $upload->set('save_path', null);
                    $upload->set('default_dir', $save_path);
                    $re = $upload->server2upun($key);
                    if ($re == false) {
                        return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                    }

                    if ($params[$key . '_key']) {
                        $image_key = $params[$key . '_key'];
                    } else {
                        $image_key = mt_rand(10, 99) . time();
                    }
                    $image_arr[$image_key] = array(
                        'image_url' => $upload->img_url,
                        'image_sha' => sha1_file($upload->full_path)
                    );
                    unset($upload);
                }
            }
        }

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();

        $o_cert_row = null;
        if ($params['cert_id']) {
            $cert_id = intval($params['cert_id']);
            $o_cert_row = $m_cert->getRow($cert_id);
        }

        if ($o_cert_row) {


            // 是编辑
            if ($o_cert_row->cert_type != $cert_type || $o_cert_row['verify_state'] != certStateEnum::CREATE) {
                return new result(false, 'Un-match cert type.', null, errorCodesEnum::UN_MATCH_OPERATION);
            }

            if ($cert_issue_time) {
                $o_cert_row->cert_issue_time = $cert_issue_time;
                $o_cert_row->update_time = Now();
                $rt = $o_cert_row->update();
                if (!$rt->STS) {
                    return new result(false, 'Edit asset fail.', null, errorCodesEnum::DB_ERROR);
                }
            }

            // 更新资产信息
            $asset_info = $m_asset->getRow(array(
                'cert_id' => $o_cert_row->uid
            ));
            if (!$asset_info) {
                return new result(false, 'No asset info:cert id ' . $o_cert_row->uid, null, errorCodesEnum::INVALID_PARAM);
            }

            // 资产是否可编辑
            if (!member_assetsClass::assetIsCanEdit($asset_info)) {
                return new result(false, 'Un editable.', null, errorCodesEnum::UN_EDITABLE);
            }


            // 重新插入图片
            $sql = "delete from member_verify_cert_image where cert_id='" . $o_cert_row->uid . "' ";
            $del = $m_cert->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Delete old image fail.', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $o_cert_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }


            $asset_info->asset_name = $asset_name;
            $asset_info->asset_sn = $asset_sn;
            $asset_info->asset_cert_type = $asset_cert_type;
            $asset_info->update_time = Now();
            $up = $asset_info->update();
            if (!$up->STS) {
                return new result(false, 'Edit asset fail.', null, errorCodesEnum::DB_ERROR);
            }

            if ($params['relative_id']) {
                $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset_info->uid, $params['relative_id']);
                if (!$rt->STS) {
                    return $rt;
                }
            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));

        } else {


            $now = Now();
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = $cert_type;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            if ($cert_issue_time) {
                $new_row->cert_issue_time = $cert_issue_time;
            }
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }
            $cert_id = $new_row->uid;

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            // 插入资产表
            $m_asset = new member_assetsModel();
            $asset = $m_asset->newRow();
            $asset->cert_id = $cert_id;
            $asset->member_id = $member_id;
            $asset->asset_name = $asset_name;
            $asset->asset_type = $cert_type;
            $asset->asset_sn = $asset_sn;
            $asset->asset_cert_type = $asset_cert_type;
            $asset->create_time = $now;
            $insert = $asset->insert();
            if (!$insert->STS) {
                return new result(false, 'Add member asset fail', null, errorCodesEnum::DB_ERROR);
            }

//            if ($params['relative_id']) {
            $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset->uid, $params['relative_id']);
            if (!$rt->STS) {
                return $rt;
            }
//            }


            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }
    }


    /** 房屋认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function houseCert($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::HOUSE;
        $file_dir = fileDirsEnum::MEMBER_ASSETS;

        $member_id = intval($params['member_id']);
        $x_coordinate = $params['x_coordinate'];
        $y_coordinate = $params['y_coordinate'];
        $asset_name = trim($params['asset_name']);
        $asset_sn = trim($params['asset_sn']);
        $asset_cert_type = $params['asset_cert_type'];
        if ($params['cert_issue_time']) {
            $cert_issue_time = date('Y-m-d', strtotime($params['cert_issue_time']));
        }


        /*if (empty($x_coordinate) || empty($y_coordinate) || !is_numeric($x_coordinate) || !is_numeric($y_coordinate)) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }*/

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }

        if (!$asset_name) {
            return new result(false, 'No asset name.', null, errorCodesEnum::INVALID_PARAM);
        }

        if (!$asset_sn) {
            return new result(false, 'No asset sn.', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_asset = new member_assetsModel();
        $chk_sn = $m_asset->find(array(
                'asset_sn' => $asset_sn,
                'asset_type' => $cert_type,
                'asset_state' => array('>=', assetStateEnum::CERTIFIED)
            )
        );
        if ($chk_sn) {
            return new result(false, 'Asset sn already existed.', null, errorCodesEnum::ASSET_SN_DUPLICATION);
        }

        $files = $_FILES;
        $house_property_card = $files['property_card'];
        $house_front = $files['house_front'];
        $house_front_road = $files['house_front_road'];
        $house_side_face = $files['house_side_face'];

        /* if (empty($house_property_card) || empty($house_front) || empty($house_front_road) || empty($house_side_face)) {
             return new result(false, 'No upload photo', null, errorCodesEnum::INVALID_PARAM);
         }*/

        // key 是上传表单名
        $photos = array(
            'property_card' => certImageKeyEnum::HOUSE_PROPERTY_CARD,
            'house_front' => certImageKeyEnum::HOUSE_FRONT,
            'house_front_road' => certImageKeyEnum::HOUSE_FRONT_ROAD,
            'house_side_face' => certImageKeyEnum::HOUSE_SIDE_FACE,
            'house_inside' => certImageKeyEnum::HOUSE_INSIDE,
            'house_relationships_certify' => certImageKeyEnum::HOUSE_RELATIONSHIPS_CERTIFY
        );

        // 保存目录
        $save_path = $file_dir;

        $image_arr = array();
        foreach ($photos as $field => $photo) {

            if (!empty($files[$field])) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->img_url;
                $image_arr[$photos[$field]] = array(
                    'image_url' => $img_path,
                    'image_sha' => sha1_file($upload->full_path)
                );
                //$photos[$field] = $img_path;
                unset($upload);
            }
        }

        /*$image_arr = array(
            certImageKeyEnum::HOUSE_PROPERTY_CARD => array(
                'image_url' => $photos['property_card'],
                'image_sha' => sha1_file(getImageUrl($photos['property_card']))
            ),
            certImageKeyEnum::HOUSE_FRONT => array(
                'image_url' => $photos['house_front'],
                'image_sha' => sha1_file(getImageUrl($photos['house_front']))
            ),
            certImageKeyEnum::HOUSE_FRONT_ROAD => array(
                'image_url' => $photos['house_front_road'],
                'image_sha' => sha1_file(getImageUrl($photos['house_front_road']))
            ),
            certImageKeyEnum::HOUSE_SIDE_FACE => array(
                'image_url' => $photos['house_side_face'],
                'image_sha' => sha1_file(getImageUrl($photos['house_side_face']))
            )

        );*/
        /*if ($photos['house_inside']) {
            $image_arr[certImageKeyEnum::HOUSE_INSIDE] = array(
                'image_url' => $photos['house_inside'],
                'image_sha' => sha1_file(getImageUrl($photos['house_inside']))
            );
        }

        if ($photos['house_relationships_certify']) {
            $image_arr[certImageKeyEnum::HOUSE_RELATIONSHIPS_CERTIFY] = array(
                'image_url' => $photos['house_relationships_certify'],
                'image_sha' => sha1_file(getImageUrl($photos['house_relationships_certify']))
            );
        }*/

        // 额外的图片
        foreach ($_FILES as $key => $u_file) {

            if (startWith($key, 'asset_image')) {

                if (!empty($u_file)) {
                    $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                    $upload->set('save_path', null);
                    $upload->set('default_dir', $save_path);
                    $re = $upload->server2upun($key);
                    if ($re == false) {
                        return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                    }

                    if ($params[$key . '_key']) {
                        $image_key = $params[$key . '_key'];
                    } else {
                        $image_key = mt_rand(10, 99) . time();
                    }
                    $image_arr[$image_key] = array(
                        'image_url' => $upload->img_url,
                        'image_sha' => sha1_file($upload->full_path)
                    );
                    unset($upload);
                }
            }
        }

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();

        $o_cert_row = null;
        if ($params['cert_id']) {
            $cert_id = intval($params['cert_id']);
            $o_cert_row = $m_cert->getRow($cert_id);
        }

        if ($o_cert_row) {

            // 是编辑
            if ($o_cert_row->cert_type != $cert_type || $o_cert_row['verify_state'] != certStateEnum::CREATE) {
                return new result(false, 'Un-match cert type.', null, errorCodesEnum::UN_MATCH_OPERATION);
            }

            if ($cert_issue_time) {
                $o_cert_row->cert_issue_time = $cert_issue_time;
                $o_cert_row->update_time = Now();
                $rt = $o_cert_row->update();
                if (!$rt->STS) {
                    return new result(false, 'Edit asset fail.', null, errorCodesEnum::DB_ERROR);
                }
            }

            // 更新资产信息
            $asset_info = $m_asset->getRow(array(
                'cert_id' => $o_cert_row->uid
            ));
            if (!$asset_info) {
                return new result(false, 'No asset info:cert id ' . $o_cert_row->uid, null, errorCodesEnum::INVALID_PARAM);
            }

            // 资产是否可编辑
            if (!member_assetsClass::assetIsCanEdit($asset_info)) {
                return new result(false, 'Un editable.', null, errorCodesEnum::UN_EDITABLE);
            }


            // edit
            $o_cert_row->x_coordinate = $x_coordinate;
            $o_cert_row->y_coordinate = $y_coordinate;

            $up = $o_cert_row->update();
            if (!$up->STS) {
                return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
            }


            // 重新插入图片
            $sql = "delete from member_verify_cert_image where cert_id='" . $o_cert_row->uid . "' ";
            $del = $m_cert->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Delete old image fail.', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $o_cert_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            $asset_info->asset_name = $asset_name;
            $asset_info->asset_sn = $asset_sn;
            $asset_info->asset_cert_type = $asset_cert_type;
            $asset_info->update_time = Now();
            $up = $asset_info->update();
            if (!$up->STS) {
                return new result(false, 'Edit asset fail.', null, errorCodesEnum::DB_ERROR);
            }

//            if ($params['relative_id']) {
            $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset_info->uid, $params['relative_id']);
            if (!$rt->STS) {
                return $rt;
            }
//            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));

        } else {


            $now = Now();
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = $cert_type;
            $new_row->x_coordinate = $x_coordinate;
            $new_row->y_coordinate = $y_coordinate;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            if ($cert_issue_time) {
                $new_row->cert_issue_time = $cert_issue_time;
            }
            $new_row->create_time = $now;
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }
            $cert_id = $new_row->uid;

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            // 插入资产表
            $m_asset = new member_assetsModel();
            $asset = $m_asset->newRow();
            $asset->cert_id = $cert_id;
            $asset->member_id = $member_id;
            $asset->asset_name = $asset_name;
            $asset->asset_type = $cert_type;
            $asset->asset_sn = $asset_sn;
            $asset->asset_cert_type = $asset_cert_type;
            $asset->create_time = $now;
            $insert = $asset->insert();
            if (!$insert->STS) {
                return new result(false, 'Add member asset fail', null, errorCodesEnum::DB_ERROR);
            }

//            if ($params['relative_id']) {
            $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset->uid, $params['relative_id']);
            if (!$rt->STS) {
                return $rt;
            }
//            }


            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }
    }

    /** 店铺认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function storeCert($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::STORE;
        $file_dir = fileDirsEnum::MEMBER_ASSETS;

        $member_id = intval($params['member_id']);
        $x_coordinate = $params['x_coordinate'];
        $y_coordinate = $params['y_coordinate'];
        $asset_name = trim($params['asset_name']);
        $asset_sn = trim($params['asset_sn']);
        $asset_cert_type = $params['asset_cert_type'];
        if ($params['cert_issue_time']) {
            $cert_issue_time = date('Y-m-d', strtotime($params['cert_issue_time']));
        }

        /*if (empty($x_coordinate) || empty($y_coordinate) || !is_numeric($x_coordinate) || !is_numeric($y_coordinate)) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }*/

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }

        if (!$asset_name) {
            return new result(false, 'No asset name.', null, errorCodesEnum::INVALID_PARAM);
        }

        if (!$asset_sn) {
            return new result(false, 'No asset sn.', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_asset = new member_assetsModel();
        $chk_sn = $m_asset->find(array(
                'asset_sn' => $asset_sn,
                'asset_type' => $cert_type,
                'asset_state' => array('>=', assetStateEnum::CERTIFIED)
            )
        );
        if ($chk_sn) {
            return new result(false, 'Asset sn already existed.', null, errorCodesEnum::ASSET_SN_DUPLICATION);
        }

        $files = $_FILES;

        // key 是上传表单名
        $photos = array(
            'business_license' => certImageKeyEnum::STORE_BUSINESS_LICENSE,
            'position' => certImageKeyEnum::STORE_POSITION,
            'store_photo' => certImageKeyEnum::STORE_STORE_PHOTO,
            'market_photo' => certImageKeyEnum::STORE_MARKET_PHOTO,
        );

        // 保存目录
        $save_path = $file_dir;

        $image_arr = array();
        foreach ($photos as $field => $photo) {

            if (!empty($files[$field])) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->img_url;
                $image_arr[$photos[$field]] = array(
                    'image_url' => $img_path,
                    'image_sha' => sha1_file($upload->full_path)
                );
                //$photos[$field] = $img_path;
                unset($upload);
            }
        }

        // 额外的图片
        foreach ($_FILES as $key => $u_file) {

            if (startWith($key, 'asset_image')) {

                if (!empty($u_file)) {
                    $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                    $upload->set('save_path', null);
                    $upload->set('default_dir', $save_path);
                    $re = $upload->server2upun($key);
                    if ($re == false) {
                        return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                    }

                    if ($params[$key . '_key']) {
                        $image_key = $params[$key . '_key'];
                    } else {
                        $image_key = mt_rand(10, 99) . time();
                    }
                    $image_arr[$image_key] = array(
                        'image_url' => $upload->img_url,
                        'image_sha' => sha1_file($upload->full_path)
                    );
                    unset($upload);
                }
            }
        }

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();

        $o_cert_row = null;
        if ($params['cert_id']) {
            $cert_id = intval($params['cert_id']);
            $o_cert_row = $m_cert->getRow($cert_id);
        }

        if ($o_cert_row) {

            // 是编辑
            if ($o_cert_row->cert_type != $cert_type || $o_cert_row['verify_state'] != certStateEnum::CREATE) {
                return new result(false, 'Un-match cert type.', null, errorCodesEnum::UN_MATCH_OPERATION);
            }

            if ($cert_issue_time) {
                $o_cert_row->cert_issue_time = $cert_issue_time;
                $o_cert_row->update_time = Now();
                $rt = $o_cert_row->update();
                if (!$rt->STS) {
                    return new result(false, 'Edit asset fail.', null, errorCodesEnum::DB_ERROR);
                }
            }

            // 更新资产信息
            $asset_info = $m_asset->getRow(array(
                'cert_id' => $o_cert_row->uid
            ));
            if (!$asset_info) {
                return new result(false, 'No asset info:cert id ' . $o_cert_row->uid, null, errorCodesEnum::INVALID_PARAM);
            }

            // 资产是否可编辑
            if (!member_assetsClass::assetIsCanEdit($asset_info)) {
                return new result(false, 'Un editable.', null, errorCodesEnum::UN_EDITABLE);
            }


            // edit
            $o_cert_row->x_coordinate = $x_coordinate;
            $o_cert_row->y_coordinate = $y_coordinate;

            $up = $o_cert_row->update();
            if (!$up->STS) {
                return new result(false, 'Edit fail', null, errorCodesEnum::DB_ERROR);
            }


            // 重新插入图片
            $sql = "delete from member_verify_cert_image where cert_id='" . $o_cert_row->uid . "' ";
            $del = $m_cert->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Delete old image fail.', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $o_cert_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            $asset_info->asset_name = $asset_name;
            $asset_info->asset_sn = $asset_sn;
            $asset_info->asset_cert_type = $asset_cert_type;
            $asset_info->update_time = Now();
            $up = $asset_info->update();
            if (!$up->STS) {
                return new result(false, 'Edit asset fail.', null, errorCodesEnum::DB_ERROR);
            }

            if ($params['relative_id']) {
                $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset_info->uid, $params['relative_id']);
                if (!$rt->STS) {
                    return $rt;
                }
            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));

        } else {


            $now = Now();
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = $cert_type;
            $new_row->x_coordinate = $x_coordinate;
            $new_row->y_coordinate = $y_coordinate;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            if ($cert_issue_time) {
                $new_row->cert_issue_time = $cert_issue_time;
            }
            $new_row->create_time = $now;
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }
            $cert_id = $new_row->uid;

            foreach ($image_arr as $key => $value) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $value['image_url'];
                $row->image_sha = $value['image_sha'];
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            // 插入资产表
            $m_asset = new member_assetsModel();
            $asset = $m_asset->newRow();
            $asset->cert_id = $cert_id;
            $asset->member_id = $member_id;
            $asset->asset_name = $asset_name;
            $asset->asset_type = $cert_type;
            $asset->asset_sn = $asset_sn;
            $asset->asset_cert_type = $asset_cert_type;
            $asset->create_time = $now;
            $insert = $asset->insert();
            if (!$insert->STS) {
                return new result(false, 'Add member asset fail', null, errorCodesEnum::DB_ERROR);
            }

//            if ($params['relative_id']) {
            $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset->uid, $params['relative_id']);
            if (!$rt->STS) {
                return $rt;
            }
//            }


            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }
    }


    /** 土地认证
     * @param $params
     * @param int $source
     * @return result
     */
    public static function landCert($params, $source = 0)
    {
        $cert_type = certificationTypeEnum::LAND;
        $file_dir = fileDirsEnum::MEMBER_ASSETS;

        $member_id = intval($params['member_id']);
        $asset_name = trim($params['asset_name']);
        $asset_sn = trim($params['asset_sn']);
        $asset_cert_type = $params['asset_cert_type'];
        if ($params['cert_issue_time']) {
            $cert_issue_time = date('Y-m-d', strtotime($params['cert_issue_time']));
        }

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }

        if (!$asset_name) {
            return new result(false, 'No asset name.', null, errorCodesEnum::INVALID_PARAM);
        }

        if (!$asset_sn) {
            return new result(false, 'No asset sn.', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_asset = new member_assetsModel();
        $chk_sn = $m_asset->find(array(
                'asset_sn' => $asset_sn,
                'asset_type' => $cert_type,
                'asset_state' => array('>=', assetStateEnum::CERTIFIED)
            )
        );
        if ($chk_sn) {
            return new result(false, 'Asset sn already existed.', null, errorCodesEnum::ASSET_SN_DUPLICATION);
        }

        $files = $_FILES;

        // key 是上传表单名
        $photos = array(
            'property_card' => certImageKeyEnum::LAND_PROPERTY_CARD,
            'trading_record' => certImageKeyEnum::LAND_TRADING_RECORD
        );

        // 保存目录
        $save_path = $file_dir;
        $image_arr = array();
        foreach ($photos as $field => $photo) {

            if (!empty($files[$field])) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($field);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->img_url;
                $image_arr[$photos[$field]] = array(
                    'image_url' => $img_path,
                    'image_sha' => sha1_file($upload->full_path)
                );
                //$photos[$field] = $img_path;
                unset($upload);
            }
        }

        debug($image_arr);

        /* $image_arr = array(
             certImageKeyEnum::LAND_PROPERTY_CARD => array(
                 'image_url' => $photos['property_card'],
                 'image_sha' => sha1_file(getImageUrl($photos['property_card']))
             )
         );*/

        // 传非必须的 trading_record
        /* if (!empty($files['trading_record'])) {
             $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
             $upload->set('save_path', null);
             $upload->set('default_dir', $save_path);
             $re = $upload->server2upun('trading_record');
             if ($re == false) {
                 return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
             }
             $img_path = $upload->img_url;
             $img_url = $upload->full_path;
             unset($upload);
             $image_arr[certImageKeyEnum::LAND_TRADING_RECORD] = array(
                 'image_url' => $img_path,
                 'image_sha' => sha1_file($img_url)
             );
         }*/

        // 额外的图片
        foreach ($_FILES as $key => $u_file) {
            if (startWith($key, 'asset_image')) {

                if (!empty($u_file)) {

                    $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                    $upload->set('save_path', null);
                    $upload->set('default_dir', $save_path);
                    $re = $upload->server2upun($key);
                    if ($re == false) {
                        return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                    }

                    if ($params[$key . '_key']) {
                        $image_key = $params[$key . '_key'];
                    } else {
                        $image_key = mt_rand(10, 99) . time();
                    }
                    $image_arr[$image_key] = array(
                        'image_url' => $upload->img_url,
                        'image_sha' => sha1_file($upload->full_path)
                    );
                    unset($upload);
                }
            }
        }

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();

        $o_cert_row = null;
        if ($params['cert_id']) {
            $cert_id = intval($params['cert_id']);
            $o_cert_row = $m_cert->getRow($cert_id);
        }

        if ($o_cert_row) {

            // 是编辑
            if ($o_cert_row->cert_type != $cert_type || $o_cert_row['verify_state'] != certStateEnum::CREATE) {
                return new result(false, 'Un-match cert type.', null, errorCodesEnum::UN_MATCH_OPERATION);
            }

            if ($cert_issue_time) {
                $o_cert_row->cert_issue_time = $cert_issue_time;
                $o_cert_row->update_time = Now();
                $rt = $o_cert_row->update();
                if (!$rt->STS) {
                    return new result(false, 'Edit asset fail.', null, errorCodesEnum::DB_ERROR);
                }
            }

            // 更新资产信息
            $asset_info = $m_asset->getRow(array(
                'cert_id' => $o_cert_row->uid
            ));
            if (!$asset_info) {
                return new result(false, 'No asset info:cert id ' . $o_cert_row->uid, null, errorCodesEnum::INVALID_PARAM);
            }

            // 资产是否可编辑
            if (!member_assetsClass::assetIsCanEdit($asset_info)) {
                return new result(false, 'Un editable.', null, errorCodesEnum::UN_EDITABLE);
            }


            // 重新插入图片
            $sql = "delete from member_verify_cert_image where cert_id='" . $o_cert_row->uid . "' ";
            $del = $m_cert->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Delete old image fail.', null, errorCodesEnum::DB_ERROR);
            }

            foreach ($image_arr as $key => $value) {
                if ($value['image_url']) {
                    $row = $m_image->newRow();
                    $row->cert_id = $o_cert_row->uid;
                    $row->image_key = $key;
                    $row->image_url = $value['image_url'];
                    $row->image_sha = $value['image_sha'];
                    $insert = $row->insert();
                    if (!$insert->STS) {
                        return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                    }
                }

            }

            $asset_info->asset_name = $asset_name;
            $asset_info->asset_sn = $asset_sn;
            $asset_info->asset_cert_type = $asset_cert_type;
            $asset_info->update_time = Now();
            $up = $asset_info->update();
            if (!$up->STS) {
                return new result(false, 'Edit asset fail.', null, errorCodesEnum::DB_ERROR);
            }

//            if ($params['relative_id']) {
            $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset_info->uid, $params['relative_id']);
            if (!$rt->STS) {
                return $rt;
            }
//            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));

        } else {
            $now = Now();
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = $cert_type;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            if ($cert_issue_time) {
                $new_row->cert_issue_time = $cert_issue_time;
            }
            $new_row->create_time = $now;
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
            }
            $cert_id = $new_row->uid;

            foreach ($image_arr as $key => $value) {

                if ($value['image_url']) {
                    $row = $m_image->newRow();
                    $row->cert_id = $new_row->uid;
                    $row->image_key = $key;
                    $row->image_url = $value['image_url'];
                    $row->image_sha = $value['image_sha'];
                    $insert = $row->insert();
                    if (!$insert->STS) {
                        return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
                    }
                }

            }

            // 插入资产表
            $m_asset = new member_assetsModel();
            $asset = $m_asset->newRow();
            $asset->cert_id = $cert_id;
            $asset->asset_name = $asset_name;
            $asset->member_id = $member_id;
            $asset->asset_type = $cert_type;
            $asset->asset_sn = $asset_sn;
            $asset->asset_cert_type = $asset_cert_type;
            $asset->create_time = $now;
            $insert = $asset->insert();
            if (!$insert->STS) {
                return new result(false, 'Add member asset fail', null, errorCodesEnum::DB_ERROR);
            }

//            if ($params['relative_id']) {
            $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset->uid, $params['relative_id']);
            if (!$rt->STS) {
                return $rt;
            }
//            }


            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }
    }


    /** 获取会员绑定的ACE账户信息
     * @param $params
     * @return result
     */
    public static function getMemberLoanAceAccountInfo($member_id)
    {
        $member_id = intval($member_id);
        $ace_info = member_handlerClass::getMemberDefaultAceHandlerInfo($member_id);
        if ($ace_info) {
            // 屏蔽账号信息
            $ace_info['handler_account'] = maskInfo($ace_info['handler_account']);
            // 屏蔽电话
            $ace_info['handler_phone'] = maskInfo($ace_info['handler_phone']);
        } else {
            $ace_info = null;
        }

        return new result(true, 'success', $ace_info);

    }


    /** 会员是否录入指纹
     * @param $member_id
     * @return int
     */
    public static function isLoggingFingerprint($member_id)
    {
        $m_member = new memberModel();
        $member = $m_member->find(array(
            'uid' => $member_id
        ));
        $fingerprint_cert = 0;
        $m_fingerprint = new common_fingerprint_libraryModel();
        $fingerprint = $m_fingerprint->orderBy('uid desc')->getRow(array(
            'obj_type' => objGuidTypeEnum::CLIENT_MEMBER,
            'obj_uid' => $member['obj_guid']
        ));
        if ($fingerprint) {
            $fingerprint_cert = 1;
        }
        return $fingerprint_cert;
    }


    /** 会员是否签订授权合同
     * @param $member_id
     * @return int
     */
    public static function isSignAuthorizedContract($member_id)
    {
        // 授权合同
        $authorized_contract = 0;
        // 最后一次授信
        $grant_credit = member_credit_grantClass::getMemberLastGrantInfo($member_id);
        if (!$grant_credit) {
            return $authorized_contract;
        }

        $where = array();
        $where[] = " member_id='$member_id' ";
        $where[] = " grant_credit_id=" . qstr($grant_credit['uid']);
        $where[] = " state>'" . authorizedContractStateEnum::CREATE . "' ";

        $r = new ormReader();
        $sql = "select * from member_authorized_contract where " . implode('AND', $where) . " order by uid desc ";
        $contract = $r->getRow($sql);
        if ($contract) {
            $authorized_contract = 1;
        } else {
            $authorized_contract = -1;
        }
        return $authorized_contract;
    }

    /** 获取会员的信用余额
     * @param $member_id
     * @return
     */
    public static function getCreditBalance($member_id)
    {

        $member_id = intval($member_id);
        $m_credit = new member_creditModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member_id
        ));

        // 检查信用激活情况

        // 指纹
        $fingerprint_cert = self::isLoggingFingerprint($member_id);

        // 授权合同
        $authorized_contract = self::isSignAuthorizedContract($member_id);

        // 是否检查指纹
        $check_fingerprint_cert = global_settingClass::isCheckCreditFingerprintCert();
        // 是否检查授权合同
        $check_authorized_contract = global_settingClass::isCheckCreditAuthorizedContract();

        $is_active = 1;
        if ($check_fingerprint_cert && !$fingerprint_cert) {
            $is_active = 0;
        }
        // todo 现在是取消合同的限制了
        /*if ($check_authorized_contract && $authorized_contract <= 0) {
            $is_active = 0;
        }*/

        $credit_process = array(
            creditProcessEnum::FINGERPRINT => array(
                'is_check' => $check_fingerprint_cert,
                'is_complete' => $fingerprint_cert
            ),
            creditProcessEnum::AUTHORIZED_CONTRACT => array(
                'is_check' => $check_authorized_contract,
                'is_complete' => $authorized_contract
            )
        );


        if (!$member_credit) {

            return array(
                'is_active' => $is_active,
                'credit' => 0,
                'balance' => 0,
                'evaluate_time' => null,
                'expire_time' => null,
                'credit_process' => $credit_process,
                'credit_terms' => 0
            );

        }

        $expire_timestamp = strtotime($member_credit->expire_time);

        // 信用过期了
        if ($member_credit['expire_time'] && ($expire_timestamp <= time())) {
            return array(
                'is_active' => 0,
                'credit' => $member_credit->credit,
                'balance' => 0,
                'evaluate_time' => $member_credit->grant_time,
                'expire_time' => $member_credit->expire_time,
                'credit_process' => $credit_process,
                'credit_terms' => $member_credit->credit_terms
            );
        }

        // 计算信用衰减
        /*$left_months = ceil(($expire_timestamp-time())/(30*86400) );
        $show_balance = intval($member_credit->credit_balance * $left_months/$member_credit->credit_terms);
        if( $show_balance > $member_credit->credit ){
            $show_balance = $member_credit->credit;
        }*/

        return array(
            'is_active' => $is_active,
            'credit' => $member_credit->credit,
            'balance' => $member_credit->credit_balance,
            'evaluate_time' => $member_credit->grant_time,
            'expire_time' => $member_credit->expire_time,
            'credit_process' => $credit_process,
            'credit_terms' => $member_credit->credit_terms
        );

    }

    /** 会员的贷款余额 返回USD的基础单位
     * @param $member_id
     * @return result
     */
    public static function getLoanBalance($member_id)
    {
        $member_id = intval($member_id);
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_account = new loan_accountModel();
        $loan_account = $m_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        $account_id = $loan_account ? $loan_account->uid : 0;
        // 贷款待放款的都算
        $sql = "select s.*,c.currency from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid  where c.account_id='" . $account_id . "'  and c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "'
        and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "' ";
        $result = $m_member->reader->getRows($sql);
        $total_debt = 0;
        if (count($result) > 0) {
            // 计算方式-> 执行中的未还款金额
            foreach ($result as $v) {
                if ($v['state'] != schemaStateTypeEnum::COMPLETE) {
                    // 不同币种的换算问题
                    $source = ($v['currency']);
                    $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
                    if ($rate <= 0) {
                        return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
                    }
                    $total_debt += round($v['receivable_principal'] * $rate, 2);  // 只计算本金
                }
            }
        }
        return new result(true, 'success', $total_debt);
    }


    /** 获得贷款总额
     * @param $member_id
     * @return result
     */
    public static function getLoanTotal($member_id)
    {
        $member_id = intval($member_id);
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_account = new loan_accountModel();
        $loan_account = $m_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        $account_id = $loan_account ? $loan_account->uid : 0;
        //$m_contract = new loan_contractModel();
        // 贷款待放款的都算
        $sql = "select * from loan_contract where account_id='$account_id' and state >='" . loanContractStateEnum::PENDING_DISBURSE . "' ";
        $rows = $m_member->reader->getRows($sql);
        $total = 0;
        if (count($rows) > 0) {

            foreach ($rows as $v) {
                $source = ($v['currency']);
                $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
                if ($rate <= 0) {
                    return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
                }
                $total += round($v['receivable_principal'] * $rate, 2);
            }
        }
        return new result(true, 'success', $total);
    }


    /** 获得贷款应还总额
     * @param $member_id
     * @return result
     */
    public static function getLoanTotalRepayable($member_id)
    {
        $member_id = intval($member_id);
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_account = new loan_accountModel();
        $loan_account = $m_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        $account_id = $loan_account ? $loan_account->uid : 0;

        // 贷款待放款的都算
        $sql = "select s.*,c.currency from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid  where c.account_id='" . $account_id . "'  and c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "'
         and c.state<'" . loanContractStateEnum::COMPLETE . "'
        and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "' ";

        $rows = $m_member->reader->getRows($sql);
        $total = 0;
        if (count($rows) > 0) {

            foreach ($rows as $v) {

                if ($v['state'] != schemaStateTypeEnum::COMPLETE) {
                    $penalty = 0;
                    if ($v['penalty_start_date'] < date('Y-m-d')) {
                        $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
                    }

                    $total_amount = $v['amount'] - $v['actual_payment_amount'] + $penalty;
                    $source = strtoupper($v['currency']);
                    $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
                    if ($rate <= 0) {
                        return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
                    }
                    $total += round($total_amount * $rate, 2);
                }

            }

        }
        return new result(true, 'success', $total);
    }


    /** 获取会员保险总额
     * @param $member_id
     * @return result
     */
    public static function getMemberInsuranceTotal($member_id)
    {
        $member_id = intval($member_id);
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_account = new insurance_accountModel();
        $loan_account = $m_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        $account_id = $loan_account ? $loan_account->uid : 0;
        // 代收款保险的都算
        $sql = "select * from insurance_contract where account_id='$account_id' and state>='" . insuranceContractStateEnum::PENDING_RECEIPT . "' ";
        $rows = $m_member->reader->getRows($sql);
        $total = 0;
        if (count($rows) > 0) {

            foreach ($rows as $v) {
                $source = strtoupper($v['currency']);
                $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
                if ($rate <= 0) {
                    return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
                }
                $total += round($v['price'] * $rate, 2);
            }
        }
        return new result(true, 'success', $total);
    }


    public static function getMemberWriteOffContractTotal($member_id)
    {
        $account = self::getLoanAccountInfoByMemberId($member_id);
        $account_id = $account ? $account['uid'] : 0;
        $r = new ormReader();
        $sql = "select * from loan_contract where account_id='$account_id' and state='" . loanContractStateEnum::WRITE_OFF . "' ";
        $total = 0;
        $lists = $r->getRows($sql);
        foreach ($lists as $v) {
            $principal = $v['receivable_principal'];
            $source = $v['currency'];
            $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
            if ($rate <= 0) {
                return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
            }
            $total += round($principal * $rate, 2);
        }
        return new result(true, 'success', $total);
    }

    public static function getMemberOutstandingWriteOffContractTotal($member_id)
    {
        $account = self::getLoanAccountInfoByMemberId($member_id);
        $account_id = $account ? $account['uid'] : 0;
        $r = new ormReader();
        $sql = "select * from loan_contract where account_id='$account_id' and state='" . loanContractStateEnum::WRITE_OFF . "' ";
        $total = 0;
        $lists = $r->getRows($sql);
        foreach ($lists as $v) {
            $outstanding = $v['loss_principal'];
            $source = $v['currency'];
            $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
            if ($rate <= 0) {
                return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
            }
            $total += round($outstanding * $rate, 2);
        }
        return new result(true, 'success', $total);
    }


    /** 获取会员认证的简单结果(yes or no)
     * @param $member_id
     * @return array|result
     */
    public static function getMemberSimpleCertResult($member_id)
    {
        $re = self::getMemberCertStateOrCount($member_id);
        if (!$re->STS) {
            return $re;
        }
        $list = $re->DATA;
        // 只有一条的
        $one_type = array(
            certificationTypeEnum::ID,
            certificationTypeEnum::PASSPORT,
            certificationTypeEnum::FAIMILYBOOK,
            //certificationTypeEnum::WORK_CERTIFICATION,
            certificationTypeEnum::RESIDENT_BOOK,
            certificationTypeEnum::BIRTH_CERTIFICATE
        );
        $result = array();
        foreach ($list as $key => $value) {

            if (in_array($key, $one_type)) {

                if ($value == certStateEnum::PASS) {
                    $is = 1;
                } else {
                    $is = 0;
                }
                $result[$key] = $is;
            } else {
                $result[$key] = ($value > 0) ? 1 : 0;
            }
        }

        return new result(true, 'success', $result);
    }


    /** 获取member各项认证的状态或数量
     * @param $member_id
     * @return array|result
     */
    public static function getMemberCertStateOrCount($member_id)
    {
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $type = array();
        $cert = (new certificationTypeEnum())->toArray();
        foreach ($cert as $const => $v) {
            $type[$v] = -10;  // 没有认证过
        }

        // 只有一条的
        $one_type = array(
            certificationTypeEnum::ID,
            certificationTypeEnum::PASSPORT,
            certificationTypeEnum::FAIMILYBOOK,
            certificationTypeEnum::WORK_CERTIFICATION,
            certificationTypeEnum::RESIDENT_BOOK,
            certificationTypeEnum::BIRTH_CERTIFICATE
        );
        // 取最新认证的记录作为认证结果
        $sql = "select * from ( select * from member_verify_cert where member_id='$member_id' order by uid desc ) x group by member_id,cert_type ";
        $results = $m_member->reader->getRows($sql);
        if (count($results) > 0) {
            foreach ($results as $item) {
                $cert_type = $item['cert_type'];
                if (in_array($cert_type, $one_type)) {
                    $type[$cert_type] = $item['verify_state'] ?: 0;
                }
            }
        }

        // 非一条的
        foreach ($type as $k => $v) {
            if (!in_array($k, $one_type)) {
                $sql = "select count(*) from member_verify_cert where member_id='$member_id' and cert_type='$k' and verify_state='" . certStateEnum::PASS . "' ";
                $count = $m_member->reader->getOne($sql);
                $type[$k] = $count ?: 0;
            }
        }

        // 担保人
        $sql = "select count(*) from member_guarantee where member_id='$member_id' and relation_state='" . memberGuaranteeStateEnum::ACCEPT . "' ";
        $num = $m_member->reader->getOne($sql);
        $type[certificationTypeEnum::GUARANTEE_RELATIONSHIP] = $num;


        return new result(true, 'success', $type);

    }


    public static function getMemberAssetCertSummary($member_id)
    {
        $assetClass = new member_assetsClass();
        $page_data = $assetClass->_initAPPCertPage();
        $member_asset_num = $assetClass->getMemberAssetNumOfAllType($member_id);
        $return = array();
        foreach ($page_data as $v) {
            $temp = array();
            $temp['asset_type'] = $v['asset_type'];
            $temp['type_name'] = $v['type_name'];
            $temp['type_icon'] = $v['type_icon'];
            $temp['member_asset_num'] = $member_asset_num[$v['asset_type']];
            $return[] = $temp;
        }
        return $return;

    }

    /** 获取会员所有的认证通过情况(API弃用)
     * @param $member_id
     * @return result
     */
    public static function getAllCertDetail($member_id)
    {
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $type = array();
        $cert = (new certificationTypeEnum())->toArray();
        foreach ($cert as $const => $v) {
            $type[$v] = null;  // 没有认证过
        }

        $m_cert = new member_verify_certModel();
        // 取最新认证的记录作为认证结果
        $sql = "select * from ( select * from member_verify_cert where member_id='$member_id' order by uid desc ) x group by member_id,cert_type ";
        $results = $m_member->reader->getRows($sql);

        if (count($results) > 0) {
            foreach ($results as $item) {
                $type[$item['cert_type']] = $item;
            }
        }


        return new result(true, 'success', $type);

    }


    /** 获取贷款合同列表
     * @param array $params
     * @return result
     */
    public static function getLoanContractList($params = array())
    {

        $member_id = intval($params['member_id']);
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_loan_account = new loan_accountModel();
        $loan_account = $m_loan_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        if (!$loan_account) {
            return new result(false, 'No account', null, errorCodesEnum::NO_LOAN_ACCOUNT);
        }
        $account_id = $loan_account->uid;

        if (isset($params['loan_type'])) {
            $loan_type = intval($params['loan_type']);
        } else {
            $loan_type = 0;
        }

        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 10000;
        $type = $params['type'];

        if ($loan_type == 0) {

            switch ($type) {
                case 1: // all
                    $sql = "select c.*,mcc.alias from loan_contract c"
                        . " left join loan_product p on p.uid = c.product_id"
                        . " left join member_credit_category mcc on mcc.uid = c.member_credit_category_id"
                        . " where c.account_id='$account_id' and c.state >= " . qstr(loanContractStateEnum::CREATE)
                        . " order by c.uid desc";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                    break;
                case 2: // 执行中的( 待放款+进行中)
                    $sql = "select c.*,mcc.alias from loan_contract c"
                        . " left join loan_product p on p.uid = c.product_id"
                        . " left join member_credit_category mcc on mcc.uid = c.member_credit_category_id"
                        . " where c.account_id='$account_id' and c.state>=" . qstr(loanContractStateEnum::PENDING_DISBURSE) . " and c.state<" . qstr(loanContractStateEnum::COMPLETE)
                        . " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                    break;
                case 3: // 待审核的
                    $sql = "select c.* from loan_contract c left join loan_product p on p.uid=c.product_id where c.account_id='$account_id' and c.state>='" . loanContractStateEnum::CREATE . "' 
                    and c.state<='" . loanContractStateEnum::PENDING_APPROVAL . "'  ";
                    $sql .= " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                    break;
                case 4:  // 有逾期的
                    $sql = "select c.* from loan_contract c left join loan_installment_scheme s on s.contract_id=c.uid where c.account_id='$account_id' and c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "'
                    and c.state <'" . loanContractStateEnum::COMPLETE . "'
                    and s.state !='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "' and date_format(s.receivable_date,'%Y%m%d') < '" . date('Ymd') . "' group by c.uid ";
                    $sql .= " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                    break;
                case 5:
                    // 还款完成的
                    $sql = "select c.* from loan_contract c  where c.account_id='$account_id' and c.state='" . loanContractStateEnum::COMPLETE . "' ";
                    $sql .= " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                    break;
                case 6:
                    //  正常执行无逾期的
                    $sql = "select c.*,s.receivable_date from loan_contract c  left join (select * from loan_installment_scheme where  state !='" . schemaStateTypeEnum::CANCEL . "' and state !='" . schemaStateTypeEnum::COMPLETE . "' and date_format(receivable_date,'%Y%m%d') < '" . date('Ymd') . "' ) s on s.contract_id=c.uid 
                    where c.account_id='$account_id' and c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "' and c.state<'" . loanContractStateEnum::COMPLETE . "'
                    and s.receivable_date is null  group by c.uid";
                    $sql .= " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                    break;
                case 20: // 信用贷合同
                    $sql = "select c.* from loan_contract c where  c.product_category='" . loanProductCategoryEnum::CREDIT_LOAN . "' and  c.account_id='$account_id' and c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "' ";
                    $sql .= " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                    break;
                default:
                    $sql = "select c.* from loan_contract c  where c.account_id='$account_id' and c.state>='" . loanContractStateEnum::CREATE . "' ";
                    $sql .= " order by c.uid desc ";
                    $list = $m_member->reader->getPage($sql, $page_num, $page_size);
            }

            $count = $list->count;
            $page_count = $list->pageCount;
            $contracts = $list->rows;

        } else {
            // todo 暂时没有
            /*$sql = "select * from loan_contract where 1=0 ";
            $list = $m_member->reader->getPage($sql,$page_num,$page_size);*/

            $count = 0;
            $page_count = 0;
            $contracts = null;
        }


        $reader = new ormReader();
        if (count($contracts) > 0) {
            foreach ($contracts as $k => $v) {
                // 未还款统计信息
                $contract_id = $v['uid'];
                $item = $v;
                $sql = "select count(uid) left_period,sum(receivable_principal) left_principal from loan_installment_scheme where contract_id='$contract_id'  and state !='" . schemaStateTypeEnum::COMPLETE . "' and state !='" . schemaStateTypeEnum::CANCEL . "' ";
                $re = $reader->getRow($sql);
                $item['left_period'] = $re['left_period'];
                $item['left_principal'] = $re['left_principal'] ?: 0;
                $contracts[$k] = $item;

            }
        }

        return new result(true, 'success', array(
            'total_num' => $count,
            'total_pages' => $page_count,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $contracts ?: null
        ));

    }

    /** 获取保险合同列表
     * @param $params
     * @return result
     */
    public static function getInsuranceContractList($params)
    {
        $member_id = intval($params['member_id']);
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_insurance_account = new insurance_accountModel();
        $insurance_account = $m_insurance_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        if (!$insurance_account) {
            return new result(false, 'No account', null, errorCodesEnum::NO_LOAN_ACCOUNT);
        }
        $account_id = $insurance_account->uid;

        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 10000;

        $type = $params['type'];
        switch ($type) {
            case 1:  // all
                $sql = "select c.*,p.product_code,p.product_name,i.item_code product_item_code,i.item_name product_item_name from insurance_contract c left join insurance_product p on c.product_id=p.uid left join insurance_product_item i on i.uid=c.product_item_id  where c.account_id='$account_id' and c.state>='" . insuranceContractStateEnum::CREATE . "' ";
                $sql .= " order by c.uid desc ";
                $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                break;
            case 2: // processing (待收款+进行中)
                $sql = "select c.*,p.product_code,p.product_name,i.item_code product_item_code,i.item_name product_item_name from insurance_contract c left join insurance_product p on c.product_id=p.uid left join insurance_product_item i on i.uid=c.product_item_id where c.account_id='$account_id' and c.state in('" . insuranceContractStateEnum::PENDING_RECEIPT . "','" . insuranceContractStateEnum::PROCESSING . "') ";
                $sql .= " order by c.uid desc ";
                $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                break;
            case 3:  // pending approval
                $sql = "select c.*,p.product_code,p.product_name,i.item_code product_item_code,i.item_name product_item_name from insurance_contract c left join insurance_product p on c.product_id=p.uid left join insurance_product_item i on i.uid=c.product_item_id where c.account_id='$account_id' and c.state in('" . insuranceContractStateEnum::CREATE . "','" . insuranceContractStateEnum::PENDING_APPROVAL . "') ";
                $sql .= " order by c.uid desc ";
                $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                break;
            case 4: // expired
                $sql = "select c.*,p.product_code,p.product_name,i.item_code product_item_code,i.item_name product_item_name from insurance_contract c left join insurance_product p on c.product_id=p.uid left join insurance_product_item i on i.uid=c.product_item_id where c.account_id='$account_id' and c.state>='" . insuranceContractStateEnum::CREATE . "' and c.end_date is not null and date_format(c.end_date,'%Y%m%d') < '" . date('Ymd') . "' ";
                $sql .= " order by c.uid desc ";
                $list = $m_member->reader->getPage($sql, $page_num, $page_size);
                break;
            default: // all
                $sql = "select c.*,p.product_code,p.product_name,i.item_code product_item_code,i.item_name product_item_name from insurance_contract c left join insurance_product p on c.product_id=p.uid left join insurance_product_item i on i.uid=c.product_item_id where c.account_id='$account_id' and c.state>='" . insuranceContractStateEnum::CREATE . "' ";
                $sql .= " order by c.uid desc ";
                $list = $m_member->reader->getPage($sql, $page_num, $page_size);
        }
        $count = $list->count;
        $page_count = $list->pageCount;
        $contracts = $list->rows;
        return new result(true, 'success', array(
            'total_num' => $count,
            'total_pages' => $page_count,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $contracts ?: null
        ));
    }


    /** 获得用户的认证结果
     * @param $params
     * @return result
     */
    public static function getMemberCertResult($params)
    {
        $member_id = intval($params['member_id']);
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $type = $params['type'];
        $m_cert = new member_verify_certModel();

        $row = null;
        $extend = null;
        $asset_page_data = null;
        $asset_type_name = null;

        $is_asset = false;
        $assetClass = new member_assetsClass();
        $asset_type = $assetClass->asset_type;
        if (in_array($type, $asset_type)) {
            $is_asset = true;
            $enum_lang = enum_langClass::getCertificationTypeEnumLang();
            // 资产
            $row = $m_cert->orderBy('uid desc')->getRow(array(
                'member_id' => $member_id,
                'cert_type' => $type
            ));  // 最新一条的
            $sql = "select a.*,c.verify_state,c.verify_remark,c.source_type from member_assets a left join member_verify_cert c on c.uid=a.cert_id where c.member_id='$member_id'
                and c.cert_type='$type' and a.asset_state!='" . assetStateEnum::CANCEL . "' order by a.uid desc ";
            $extend = $m_cert->reader->getRows($sql);
            $m_image = new member_verify_cert_imageModel();
            foreach ($extend as $k => $v) {
                $images = $m_image->getRows(array(
                    'cert_id' => $v['cert_id']
                ));
                $image_list = array();
                foreach ($images as $item) {
                    $image_list[$item['image_key']] = getImageUrl($item['image_url']);
                }
                $v['cert_images'] = $image_list;
                $v['main_image'] = current($image_list);
                $extend[$k] = $v;
            }

            $asset_type_name = $enum_lang[$type];
            $asset_page_data = (new member_assetsClass())->_initAPPAssetCertPageByType($type);

        } else {

            switch ($type) {
                case certificationTypeEnum::RESIDENT_BOOK :
                    $row = $m_cert->orderBy('uid desc')->getRow(array(
                        'member_id' => $member_id,
                        'cert_type' => $type
                    ));
                    break;
                case certificationTypeEnum::ID:
                    $row = $m_cert->orderBy('uid desc')->getRow(array(
                        'member_id' => $member_id,
                        'cert_type' => $type
                    ));
                    break;
                case certificationTypeEnum::FAIMILYBOOK:
                    $row = $m_cert->orderBy('uid desc')->getRow(array(
                        'member_id' => $member_id,
                        'cert_type' => $type
                    ));
                    break;
                case certificationTypeEnum::PASSPORT :
                    $row = $m_cert->orderBy('uid desc')->getRow(array(
                        'member_id' => $member_id,
                        'cert_type' => $type
                    ));
                    break;
                case certificationTypeEnum::STORE:
                case certificationTypeEnum::MOTORBIKE :
                case certificationTypeEnum::CAR :
                case certificationTypeEnum::HOUSE :
                case certificationTypeEnum::LAND :
                    $is_asset = true;
                    $enum_lang = enum_langClass::getCertificationTypeEnumLang();
                    // 资产
                    $row = $m_cert->orderBy('uid desc')->getRow(array(
                        'member_id' => $member_id,
                        'cert_type' => $type
                    ));  // 最新一条的
                    $sql = "select a.*,c.verify_state,c.verify_remark,c.source_type from member_assets a left join member_verify_cert c on c.uid=a.cert_id where c.member_id='$member_id'
                and c.cert_type='$type' and a.asset_state!='" . assetStateEnum::CANCEL . "' order by a.uid desc ";
                    $extend = $m_cert->reader->getRows($sql);
                    $m_image = new member_verify_cert_imageModel();
                    foreach ($extend as $k => $v) {
                        $images = $m_image->getRows(array(
                            'cert_id' => $v['cert_id']
                        ));
                        $image_list = array();
                        foreach ($images as $item) {
                            $image_list[$item['image_key']] = getImageUrl($item['image_url']);
                        }
                        $v['cert_images'] = $image_list;
                        $v['main_image'] = current($image_list);
                        $extend[$k] = $v;
                    }

                    $asset_type_name = $enum_lang[$type];
                    $asset_page_data = (new member_assetsClass())->_initAPPAssetCertPageByType($type);
                    break;
                case certificationTypeEnum::WORK_CERTIFICATION :
                    $row = $m_cert->orderBy('uid desc')->getRow(array(
                        'member_id' => $member_id,
                        'cert_type' => $type
                    ));
                    $m_work = new member_workModel();
                    $extend = $m_work->getRow(array(
                        'cert_id' => $row ? $row->uid : 0
                    ));
                    break;
                default :
                    $row = null;

            }
        }

        return new result(true, 'success', array(
            'cert_result' => $row ?: null,
            'extend_info' => $extend ?: null,
            'asset_page_data' => $asset_page_data,
            'asset_type_name' => $asset_type_name
        ));
    }


    /** 获取用户账户统计
     * @param $member_id
     * @return mixed|null|result
     */
    public static function getMemberAccountSumInfo($member_id)
    {
        $member_id = intval($member_id);

        $loan_account = self::getLoanAccountInfoByMemberId($member_id);

        $credit = self::getCreditBalance($member_id);

        $loan_balance = self::getLoanBalance($member_id);
        $loan_balance = $loan_balance->STS ? $loan_balance->DATA : 0;

        $loan_total = self::getLoanTotal($member_id);
        $loan_total = $loan_total->STS ? $loan_total->DATA : 0;

        $loan_total_repayable = self::getLoanTotalRepayable($member_id);
        $loan_total_repayable = $loan_total_repayable->STS ? $loan_total_repayable->DATA : 0;

        $insurance_total = self::getMemberInsuranceTotal($member_id);
        $insurance_total = $insurance_total->STS ? $insurance_total->DATA : 0;

        $reader = new ormReader();
        $account_id = intval($loan_account['uid']);
        $sql = "select count(*) from loan_contract where account_id='$account_id' and state in ('" . loanContractStateEnum::PENDING_DISBURSE . "','" . loanContractStateEnum::PROCESSING . "') ";
        $processing_loan_contracts = $reader->getOne($sql);
        $processing_loan_contracts = $processing_loan_contracts ?: 0;

        $insurance_account = self::getInsuranceAccountInfoByMemberId($member_id);
        $insurance_account_id = intval($insurance_account['uid']);
        $sql = "select count(*) from insurance_contract where account_id='$insurance_account_id' and state in ('" . insuranceContractStateEnum::PENDING_RECEIPT . "','" . insuranceContractStateEnum::PROCESSING . "') ";
        $processing_insurance_contracts = $reader->getOne($sql);
        $processing_insurance_contracts = $processing_insurance_contracts ?: 0;


        return new result(true, 'success', array(
            'credit' => $credit,
            'loan_total' => $loan_total,
            'loan_balance' => $loan_balance,
            'loan_total_repayable' => $loan_total_repayable,
            'insurance_total' => $insurance_total,
            'processing_loan_contracts' => $processing_loan_contracts,
            'processing_insurance_contracts' => $processing_insurance_contracts
        ));


    }


    public static function getMemberLoanConsultList($params)
    {
        $member_id = intval($params['member_id']);
        $reader = new ormReader();
        $page_num = intval($params['page_num']) ? intval($params['page_num']) : 1;
        $page_size = intval($params['page_size']) ? intval($params['page_size']) : 100000;
        $sql = "select * from loan_consult where member_id='$member_id' order by uid desc ";
        $rows = $reader->getPage($sql, $page_num, $page_size);
        return new result(true, 'success', array(
            'total_num' => $rows->count,
            'total_pages' => $rows->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $rows->rows
        ));
    }


    public static function getMemberCreditHistory($params)
    {
        $member_id = $params['member_id'];
        $page_num = intval($params['page_num']) ? intval($params['page_num']) : 1;
        $page_size = intval($params['page_size']) ? intval($params['page_size']) : 100000;

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $sql = "select * from loan_credit_release where obj_guid='" . $member->obj_guid . "' order by uid desc ";
        $rows = $m_member->reader->getPage($sql, $page_num, $page_size);

        return new result(true, '', array(
            'total_num' => $rows->count,
            'total_pages' => $rows->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $rows->rows ? $rows->rows : null
        ));
    }

    public static function getLoanBindAutoDeductionAccount($member_id)
    {

        $r = new ormReader();
        $sql = "select uid,handler_type,handler_name,handler_account,handler_phone,handler_property from member_account_handler where member_id='$member_id' and is_verified=1 and state='" . accountHandlerStateEnum::ACTIVE . "'
        and handler_type in('" . memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY . "','" . memberAccountHandlerTypeEnum::PASSBOOK . "')";
        $rows = $r->getRows($sql);
        $list = array();
        if (count($rows) > 0) {
            foreach ($rows as $v) {
                $v['handler_account'] = maskInfo($v['handler_account']);
                $list[] = $v;
            }
        }

        return new result(true, 'success', $list);


    }

    /** 资产删除
     * @param $member_id
     * @param $asset_id
     * @return result
     */
    public static function deleteAsset($member_id, $asset_id)
    {
        $m_asset = new member_assetsModel();
        $asset = $m_asset->getRow(array(
            'member_id' => $member_id,
            'uid' => $asset_id
        ));
        if (!$asset) {
            return new result(false, 'Invalid asset', null, errorCodesEnum::INVALID_PARAM);
        }
        return member_assetsClass::assetDeleteById($asset_id);
    }


    /** 移除家庭关系
     * @param $member_id
     * @param $relation_id
     * @return result
     */
    public static function deleteFamilyRelationship($member_id, $relation_id)
    {

        $m_family = new member_familyModel();
        $family = $m_family->getRow(array(
            'uid' => $relation_id,
            'member_id' => $member_id
        ));
        if (!$family) {
            return new result(false, 'Invalid family relationship', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_cert = new member_verify_certModel();
        $cert = $m_cert->getRow($family->cert_id);

        // 审核中不能删除
        if ($cert && $cert['verify_state'] == certStateEnum::LOCK) {
            return new result(false, 'Delete fail', null, errorCodesEnum::APPROVING_CAN_NOT_DELETE);
        }

        // 移除关系
        $family->relation_state = memberFamilyStateEnum::REMOVE;
        $family->update_time = Now();
        $up = $family->update();
        if (!$up->STS) {
            return new result(false, 'Delete fail', null, errorCodesEnum::DB_ERROR);
        }
        // 更新认证记录的状态
        if ($cert) {
            if ($cert->verify_state != certStateEnum::EXPIRED) {
                $cert->verify_state = certStateEnum::EXPIRED;
                $up = $cert->update();
                if (!$up->STS) {
                    return new result(false, 'Delete fail', null, errorCodesEnum::DB_ERROR);
                }
            }

        }
        return new result(true, 'success');

    }


    /** 获取贷款统计
     * @param $member_id
     * @param int $type
     *  1 自己贷款的  2 作为担保人的
     * @param int $summary_type
     * @return result
     */
    public static function getMemberLoanSummary($member_id, $type = 1, $summary_type = 0)
    {
        $loan_account = self::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account ? $loan_account['uid'] : 0;
        // 返回数据格式
        $return = array(
            'contract_num_summary' => array(
                'total_contracts' => 0,
                'processing_contracts' => 0,
                'normal_processing_contracts' => 0,
                'delinquent_contracts' => 0,
                'complete_contracts' => 0,
                'rejected_contracts' => 0,
                'pending_approval_contracts' => 0,
                'write_off_contracts' => 0
            ),
            'contract_amount_summary' => array(
                'total_principal' => 0,
                'total_liabilities' => 0,
                'total_payable_principal' => 0,
                'total_write_off_amount' => 0,
                'total_outstanding_write_off_balance' => 0
            ),
            'next_schema' => null
        );

        $r = new ormReader();
        switch ($type) {
            case 1:
                // 自己贷款的
                $total_contracts = loan_contractClass::getLoanAccountContractNumSummary($account_id, $summary_type);
                $processing_contracts = loan_contractClass::getLoanAccountContractNumSummary($account_id, 1);
                $delinquent_contracts = loan_contractClass::getLoanAccountContractNumSummary($account_id, 2);

                $pending_approval_contracts = loan_contractClass::getLoanAccountContractNumSummary($account_id, 6);
                $return['contract_num_summary']['total_contracts'] = $total_contracts;
                $return['contract_num_summary']['processing_contracts'] = $processing_contracts;
                $return['contract_num_summary']['delinquent_contracts'] = $delinquent_contracts;
                $return['contract_num_summary']['normal_processing_contracts'] = $processing_contracts - $delinquent_contracts;
                $return['contract_num_summary']['complete_contracts'] = loan_contractClass::getLoanAccountContractNumSummary($account_id, 5);
                $return['contract_num_summary']['rejected_contracts'] = loan_contractClass::getLoanAccountContractNumSummary($account_id, 3);
                $return['contract_num_summary']['pending_approval_contracts'] = $pending_approval_contracts;
                $return['contract_num_summary']['write_off_contracts'] = loan_contractClass::getLoanAccountContractNumSummary($account_id, 4);

                $loan_total = 0;
                $loan_re = self::getLoanTotal($member_id);
                if ($loan_re->STS) {
                    $loan_total = $loan_re->DATA;
                }
                $payable_total = 0;
                $payable_re = self::getLoanTotalRepayable($member_id);
                if ($payable_re->STS) {
                    $payable_total = $payable_re->DATA;
                }

                $write_off_total = 0;
                $off_re1 = self::getMemberWriteOffContractTotal($member_id);
                if ($off_re1->STS) {
                    $write_off_total = $off_re1->DATA;
                }

                $outstanding_write_off_total = 0;
                $off_re2 = self::getMemberOutstandingWriteOffContractTotal($member_id);
                if ($off_re2->STS) {
                    $outstanding_write_off_total = $off_re2->DATA;
                }

                $return['contract_amount_summary']['total_principal'] = $loan_total;
                $return['contract_amount_summary']['total_liabilities'] = $payable_total;
                $return['contract_amount_summary']['total_write_off_amount'] = $write_off_total;
                $return['contract_amount_summary']['total_outstanding_write_off_balance'] = $outstanding_write_off_total;

                // 合计应还的本金
                $currency_amount = member_statisticsClass::getMemberTotalPayableLoanPrincipalGroupByCurrency($member_id);
                $total_payable_principal = 0;
                foreach ($currency_amount as $currency => $amount) {
                    $ex_rate = global_settingClass::getCurrencyRateBetween($currency, currencyEnum::USD);
                    $total_payable_principal += round($amount * $ex_rate, 2);
                }

                $return['contract_amount_summary']['total_payable_principal'] = $total_payable_principal;


                // 下期应还
                /* $sql = "select s.*,c.currency from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid  where c.account_id='" . $account_id . "'  and c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "'
         and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "' and s.receivable_date>='" . date('Y-m-d') . "' order by s.receivable_date asc ";
                 $schema = $r->getRow($sql);
                 if ($schema) {
                     $return['next_schema'] = array(
                         'repayment_time' => date('Y-m-d', strtotime($schema['receivable_date'])),
                         'repayment_amount' => $schema['amount'] - $schema['actual_payment_amount'],
                         'currency' => $schema['currency']
                     );
                 }*/

                break;
            case 2:
                // 担保的贷款
                break;
            default:
                break;
        }

        return new result(true, 'success', $return);
    }


    /** 客户下期应还单次计划
     * @param $member_id
     * @return ormDataRow
     */
    public static function getMemberLoanNextRepaymentSchema($member_id)
    {
        $r = new ormReader();
        $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account ? $loan_account['uid'] : 0;
        $sql = "select s.*,c.currency,c.contract_sn from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid  where c.account_id='$account_id'
        and  c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "' and c.state<'" . loanContractStateEnum::COMPLETE . "' and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "'
        and s.receivable_date>='" . date('Y-m-d') . "'  order by s.receivable_date asc ";

        $schemas = $r->getRow($sql);
        return $schemas;
    }


    /** 客户下次应还日所有待还计划
     * @param $member_id
     * @return null|ormCollection
     */
    public static function getMemberLoanNextRepaymentDaySchemaList($member_id, $filter = array(), $is_all = false)
    {
        $r = new ormReader();
        $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account ? $loan_account['uid'] : 0;
        $date_where = "";

        /* if ($filter['product_code']) {
             $date_where .= " and c.sub_product_code=" . qstr($filter['product_code']);
         }*/

        if ($filter['member_credit_category_id']) {
            $date_where .= " and c.member_credit_category_id=" . qstr($filter['member_credit_category_id']);
        }


        $sql = "select s.*,c.currency from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid  where c.account_id='$account_id'
        and  c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "' and c.state<'" . loanContractStateEnum::COMPLETE . "' 
        and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "'
        and s.receivable_date>='" . date('Y-m-d') . "' $date_where  order by s.receivable_date asc ";

        $schema = $r->getRow($sql);

        if (!$schema) {
            return null;
        }

        $date = $schema['receivable_date'];

        $where = '';


        /* if ($filter['product_code']) {
             $where .= " and c.sub_product_code=" . qstr($filter['product_code']);
         }*/

        if ($filter['member_credit_category_id']) {
            $where .= " and c.member_credit_category_id=" . qstr($filter['member_credit_category_id']);
        }

        //edit by tim: 因为要把逾期的表现出来，所以加了一个receiveable_date<收款日
        //要把所有single的都表现出来(single的是可以随时还)，所以加了一个or 条件
        if (!$is_all) {//目前看好像is_all没区分查询条件
            $where .= " and  (s.receivable_date<='$date' or c.repayment_type='" . interestPaymentEnum::ANYTIME_SINGLE_REPAYMENT . "' or c.repayment_type='" . interestPaymentEnum::ADVANCE_SINGLE_REPAYMENT . "') ";
        } else {
            $where .= " and  (s.receivable_date<='$date' or c.repayment_type='" . interestPaymentEnum::ANYTIME_SINGLE_REPAYMENT . "' or c.repayment_type='" . interestPaymentEnum::ADVANCE_SINGLE_REPAYMENT . "') ";
        }

        // 获得所有的计划列表
        $sql = "select s.*,c.currency,c.contract_sn,c.repayment_type,c.repayment_period from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid  where c.account_id='$account_id'
            and  c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "' and c.state<'" . loanContractStateEnum::COMPLETE . "' and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "'
             $where  order by s.receivable_date asc ";
        $list = $r->getRows($sql);
        return $list;

    }

    /** 获取某日下的还款计划
     * @param $member_id
     * @param $day
     * @return ormCollection
     */
    public static function getMemberLoanPendingRepaymentSchemaListByDay($member_id, $day)
    {
        $r = new ormReader();
        $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account ? $loan_account['uid'] : 0;
        $day = date('Y-m-d', strtotime($day));
        // 获得所有的计划列表
        $sql = "select s.*,c.currency,c.contract_sn,c.repayment_type,c.repayment_period from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid  where c.account_id='$account_id'
            and  c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "' and c.state<'" . loanContractStateEnum::COMPLETE . "' and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "'
            and DATE_FORMAT(s.receivable_date,'%Y-%m-%d')='$day' ";
        $list = $r->getRows($sql);
        return $list;
    }

    /** 会员信用激活信息
     * @param $member_id
     * @return result
     */
    public static function getMemberCreditProcess($member_id)
    {

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {

            //return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);

            // 是否检查指纹
            $check_fingerprint_cert = global_settingClass::isCheckCreditFingerprintCert();
            // 是否检查授权合同
            $check_authorized_contract = global_settingClass::isCheckCreditAuthorizedContract();

            return new result(true, 'success', array(
                'phone' => array(
                    'is_must' => 1,
                    'is_complete' => 0,
                ),
                'personal_info' => array(
                    'is_must' => 1,
                    'is_complete' => 0,
                ),
                'assets_cert' => array(
                    'is_must' => 0,
                    'is_complete' => 0,
                ),
                'fingerprint' => array(
                    'is_must' => $check_fingerprint_cert,
                    'is_complete' => 0,
                ),
                'authorized_contract' => array(
                    'is_must' => $check_authorized_contract,
                    'is_complete' => 0,
                ),
                'credit_info' => array(
                    'credit' => 0,
                    'balance' => 0,
                ),
                'is_active' => 0,
            ));
        }

        $credit = memberClass::getCreditBalance($member_id);

        $cert_list = array();
        $re = self::getMemberCertStateOrCount($member_id);
        if ($re->STS) {
            $cert_list = $re->DATA;
        }


        $return = array();
        $return['phone'] = array(
            'is_must' => 1,
            'is_complete' => $member->is_verify_phone ? 1 : 0
        );

        $personal_info = 0;
        if ($cert_list[certificationTypeEnum::ID] == certStateEnum::PASS
            || $cert_list[certificationTypeEnum::FAIMILYBOOK] == certStateEnum::PASS
            || $cert_list[certificationTypeEnum::RESIDENT_BOOK] == certStateEnum::PASS
            || $cert_list[certificationTypeEnum::WORK_CERTIFICATION] == certStateEnum::PASS
        ) {
            $personal_info = 1;
        }

        $return['personal_info'] = array(
            'is_must' => 1,
            'is_complete' => $personal_info
        );

        $assets_cert = 0;
        if ($cert_list[certificationTypeEnum::MOTORBIKE] > 0
            || $cert_list[certificationTypeEnum::CAR] > 0
            || $cert_list[certificationTypeEnum::HOUSE] > 0
            || $cert_list[certificationTypeEnum::LAND] > 0
        ) {
            $assets_cert = 1;
        }

        $return['assets_cert'] = array(
            'is_must' => 0,
            'is_complete' => $assets_cert
        );

        $fingerprint_cert = $credit['credit_process'][creditProcessEnum::FINGERPRINT];

        $return['fingerprint'] = array(
            'is_must' => $fingerprint_cert['is_check'],
            'is_complete' => $fingerprint_cert['is_complete']
        );

        $authorized_contract = $credit['credit_process'][creditProcessEnum::AUTHORIZED_CONTRACT];
        $return['authorized_contract'] = array(
            'is_must' => $authorized_contract['is_check'],
            'is_complete' => $authorized_contract['is_complete']
        );

        $return['credit_info'] = $credit;

        $return['is_active'] = $credit['is_active'];


        // 授信详细
        $m_credit_grant = new member_credit_grantModel();
        $grant_detail = $m_credit_grant->orderBy('uid desc')->find(array(
            'member_id' => $member_id,
            'state' => commonApproveStateEnum::PASS
        ));

        if ($grant_detail) {
            $sql = "select g.*,a.asset_type,a.asset_name,a.valuation from member_credit_grant_assets g left join member_assets a on a.uid=g.member_asset_id
            where grant_id='" . $grant_detail['uid'] . "' ";
            $list = $m_credit_grant->reader->getRows($sql);
            $grant_detail['assets_credit_list'] = $list;
        }

        $return['credit_grant_detail'] = $grant_detail;

        return new result(true, 'success', $return);

    }


    /** 获得会员贷款收款记录
     * @param $member_id
     * @return ormCollection
     */
    public static function getLoanReceivedRecord($member_id)
    {
        $loan_account = self::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account ? $loan_account['uid'] : 0;
        $r = new ormReader();
        $sql = "select r.amount,r.currency,r.contract_id,r.create_time,c.contract_sn,DATE_FORMAT(r.create_time,'%Y-%m') month_time,DATE_FORMAT(r.create_time,'%m-%d %H:%i') day_time from loan_disbursement r left join loan_contract c on c.uid=r.contract_id where c.account_id='$account_id'
        and r.state='" . disbursementStateEnum::DONE . "' order by r.create_time desc ";
        $rows = $r->getRows($sql);
        return $rows;

    }

    /** 获得会员贷款还款记录
     * @param $member_id
     * @return
     */
    public static function getLoanRepaymentRecord($member_id, $page_num, $page_size, $contract_id = null, $filter = array())
    {
        $loan_account = self::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account ? $loan_account['uid'] : 0;

        $r = new ormReader();

        $where = '';
        if ($contract_id) {
            $where .= " and r.contract_id='$contract_id' ";
        }

        if ($filter['product_id']) {

            $where .= " and c.sub_product_id=" . qstr($filter['product_id']);
        }

        if ($filter['member_credit_category_id']) {
            $where .= " and c.member_credit_category_id=" . qstr($filter['member_credit_category_id']);
        }

        // 还款合计
        $sum_sql = "select sum(r.payer_amount) amount,r.payer_currency currency from loan_repayment r left join loan_contract c on c.uid=r.contract_id
        left join loan_sub_product sp on sp.uid=c.sub_product_id
        where c.account_id='$account_id'
        and r.state='" . repaymentStateEnum::DONE . "' $where group by r.payer_currency order by r.payer_currency ";
        $sum = $r->getRows($sum_sql);
        $repayment_total = $sum;

        $sql = "select r.*,c.contract_sn from loan_repayment r left join loan_contract c on c.uid=r.contract_id 
        left join loan_sub_product sp on sp.uid=c.sub_product_id
        where c.account_id='$account_id'
        and r.state='" . repaymentStateEnum::DONE . "' $where order by r.create_time desc ";

        $page = $r->getPage($sql, $page_num, $page_size);
        return array(
            'repayment_total' => $repayment_total,
            'total_num' => $page->count,
            'total_pages' => $page->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $page->rows
        );
    }


    /** 获取分币种贷款所有欠款
     * @param $member_id
     */
    public static function getMemberLoanTotalPendingRepaymentAmountGroupByCurrency($member_id)
    {
        $currency_total = array();
        $schemas = self::getMemberAllPendingRepaymentSchema($member_id);
        foreach ($schemas as $v) {
            $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
            $amount = $v['amount'] - $v['actual_payment_amount'];
            $total_amount = $amount + $penalty;
            if ($currency_total[$v['currency']]) {
                $currency_total[$v['currency']] += $total_amount;
            } else {
                $currency_total[$v['currency']] = $total_amount;
            }
        }
        return $currency_total;
    }


    /** 获取member所有还款计划（合并同日的）
     * @param $member_id
     * @return array
     */
    public static function getMemberAllPendingRepaymentSchemaGroupByDay($member_id, $filter = array())
    {
        $currency_total = array();
        $list = array();
        $schemas = self::getMemberAllPendingRepaymentSchema($member_id, $filter);
        foreach ($schemas as $k => $v) {
            $day = date('Y-m-d', strtotime($v['receivable_date']));

            $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
            $amount = $v['amount'] - $v['actual_payment_amount'];
            $total_amount = $amount + $penalty;
            $v['penalty'] = $penalty;
            $v['pending_repayment_amount'] = $total_amount;
            $schemas[$k] = $v;
            if ($currency_total[$v['currency']]) {
                $currency_total[$v['currency']] += $total_amount;
            } else {
                $currency_total[$v['currency']] = $total_amount;
            }

            if ($list[$day]) {

                $list[$day]['list'][] = $v;
                if ($list[$day]['repayment_total'][$v['currency']]) {

                    $list[$day]['repayment_total'][$v['currency']] += $total_amount;
                } else {
                    $list[$day]['repayment_total'][$v['currency']] = $total_amount;
                }

            } else {
                $list[$day] = array(
                    'date' => $day,
                    'repayment_total' => array(
                        $v['currency'] => $total_amount
                    ),
                    'list' => array(
                        $v
                    )
                );
            }

        }

        $list = array_values($list);
        return array(
            'pending_repayment_total' => $currency_total,
            'list' => $list
        );
    }


    /** 获取member贷款所有应还计划
     * @param $member_id
     * @param $filter
     * @return ormCollection
     */
    public static function getMemberAllPendingRepaymentSchema($member_id, $filter = array())
    {
        $loan_account = self::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account ? $loan_account['uid'] : 0;

        $r = new ormReader();

        $where = '';
        if ($filter['product_id']) {
            $product_info = (new loan_sub_productModel())->find(array(
                'uid' => $filter['product_id']
            ));
            $where .= " and c.sub_product_code='" . $product_info['sub_product_code'] . "' ";
        }
        if ($filter['sub_product_id']) {
            $where .= " and c.sub_product_id='" . $filter['sub_product_id'] . "' ";
        }

        if ($filter['member_credit_category_id']) {
            $where .= " and c.member_credit_category_id=" . qstr($filter['member_credit_category_id']);
        }

        if ($filter['contract_id']) {
            $where .= " and c.uid='" . $filter['contract_id'] . "' ";
        }

        $sql = "select s.*,c.contract_sn,c.currency,c.repayment_type,c.repayment_period from loan_installment_scheme s
        left join loan_contract c on s.contract_id = c.uid  
        where c.account_id='$account_id' and c.state >='" . loanContractStateEnum::PENDING_DISBURSE . "'
        and c.state < '" . loanContractStateEnum::COMPLETE . "' and s.state != '" . schemaStateTypeEnum::CANCEL . "'
        and s.state != '" . schemaStateTypeEnum::COMPLETE . "' $where order by s.receivable_date asc ";

        return $r->getRows($sql);
    }


    /** 获取member待还款的所有计划列表
     * @param $member_id
     * @param $page_num
     * @param $page_size
     * @return array
     */
    public static function getLoanPendingRepaymentSchema($member_id, $page_num, $page_size, $filter = array())
    {
        $loan_account = self::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account ? $loan_account['uid'] : 0;

        $r = new ormReader();

        $where = '';
        /* if ($filter['product_id']) {
             $product_info = (new loan_sub_productModel())->find(array(
                 'uid' => $filter['product_id']
             ));
             $where .= " and c.sub_product_code='" . $product_info['sub_product_code'] . "' ";
         }*/

        if ($filter['member_credit_category_id']) {
            $where .= " and c.member_credit_category_id=" . qstr($filter['member_credit_category_id']);
        }

        //合计
        $sum_sql = "select c.currency,sum((s.amount-s.actual_payment_amount)) amount from loan_installment_scheme s
        left join loan_contract c on s.contract_id=c.uid  
        where c.account_id='$account_id' and c.state >='" . loanContractStateEnum::PENDING_DISBURSE . "'
        and c.state < '" . loanContractStateEnum::COMPLETE . "' and s.state != '" . schemaStateTypeEnum::CANCEL . "' and s.state != '" . schemaStateTypeEnum::COMPLETE . "'
         $where group by c.currency order by c.currency asc ";

        $sum = $r->getRows($sum_sql);
        $repayment_total = $sum;

        $sql = "select s.*,(s.amount-s.actual_payment_amount) pending_repayment_amount,c.contract_sn,c.currency,c.repayment_type,c.repayment_period from loan_installment_scheme s
        left join loan_contract c on s.contract_id=c.uid  
        where c.account_id='$account_id' and c.state >='" . loanContractStateEnum::PENDING_DISBURSE . "'
        and c.state < '" . loanContractStateEnum::COMPLETE . "' and s.state != '" . schemaStateTypeEnum::CANCEL . "'
        and s.state != '" . schemaStateTypeEnum::COMPLETE . "' order by s.receivable_date asc ";

        $page = $r->getPage($sql, $page_num, $page_size);
        return array(
            'pending_repayment_total' => $repayment_total,
            'total_num' => $page->count,
            'total_pages' => $page->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $page->rows
        );
    }

    public static function searchMember($params)
    {
        $m = new memberModel();
        $type = $params['type'];
        switch ($type) {
            case 0:
                // uid
                $member = $m->find(array(
                    'uid' => intval($params['member_id'])
                ));
                break;
            case 1:
                // guid
                $guid = $params['guid'];
                $member = $m->find(array(
                    'obj_guid' => $guid
                ));
                break;
            case 2:
                // phone
                $country_code = $params['country_code'];
                $phone = $params['phone_number'];
                $phone_arr = tools::getFormatPhone($country_code, $phone);
                $contact_phone = $phone_arr['contact_phone'];
                $member = $m->find(array(
                    'phone_id' => $contact_phone
                ));
                break;
            case 3:
                // code
                $login_code = $params['login_code'];
                $member = $m->find(array(
                    'login_code' => $login_code
                ));
                break;
            case 4:
                // code
                $login_code = $params['display_name'];
                $member = $m->find(array(
                    'display_name' => $login_code
                ));
                break;
            default:
                return null;
                break;
        }
        if (!$member) {
            return null;
        }

        $member['member_image'] = getImageUrl($member['member_image']);
        $member['member_icon'] = getImageUrl($member['member_icon']);
        if ($member['trading_password']) {
            $member['has_trading_password'] = true;
        }

       /* unset($member['login_password']);
        unset($member['trading_password']);
        unset($member['gesture_password']);*/

        $state_txt_list = (new memberStateEnum())->Dictionary();
        $member['member_state_text'] = $state_txt_list[$member['member_state']];

        // 信用值

        $credit_info = self::getCreditBalance($member['uid']);

        $member['credit'] = $credit_info['credit'];
        $member['credit_balance'] = $credit_info['balance'];
        $member['credit_is_active'] = $credit_info['is_active'];
        $member['credit_terms'] = $credit_info['credit_terms'];
        $member['credit_detail'] = $credit_info;

        return $member;

    }


    /** 精确搜索member,防止信息泄露
     * @param $keyword
     * @return ormCollection
     */
    public static function searchMemberList($keyword)
    {
        $r = new ormReader();
        $sql = "select uid,obj_guid,login_code,display_name,phone_country,phone_number,phone_id,member_state,member_icon from client_member
          where (login_code='$keyword' or phone_number='$keyword')";
        $list = $r->getRows($sql);
        foreach ($list as $key => $value) {
            $value['member_icon'] = getImageUrl($value['member_icon']);
            $list[$key] = $value;
        }
        return $list;
    }


    public static function getMemberPassedGuaranteeList($member_id)
    {
        // 我的担保人列表
        $r = new ormReader();
        $sql = "select g.*,m.login_code,m.display_name,m.kh_display_name,m.member_icon,m.member_image,m.phone_id,d.item_name_json relation_type_name_json from member_guarantee g left join client_member m on m.uid=g.relation_member_id
  left join core_definition d on d.item_code=g.relation_type and d.category='" . userDefineEnum::GUARANTEE_RELATIONSHIP . "' where g.member_id='$member_id' and g.relation_state='" . memberGuaranteeStateEnum::ACCEPT . "'  ";

        $list1 = $r->getRows($sql);
        foreach ($list1 as $key => $value) {
            $value['member_icon'] = getImageUrl($value['member_icon']);
            $value['member_image'] = getImageUrl($value['member_image']);
            $list1[$key] = $value;
        }


        // 作为担保人的（通过的）
        $sql = "select g.*,m.login_code,m.display_name,m.kh_display_name,m.member_icon,m.member_image,m.phone_id,d.item_name_json relation_type_name_json from member_guarantee g left join client_member m on m.uid=g.member_id
  left join core_definition d on d.item_code=g.relation_type and d.category='" . userDefineEnum::GUARANTEE_RELATIONSHIP . "' where g.relation_member_id='$member_id' and g.relation_state='" . memberGuaranteeStateEnum::ACCEPT . "'  ";
        $list2 = $r->getRows($sql);
        foreach ($list2 as $key => $value) {
            $value['member_icon'] = getImageUrl($value['member_icon']);
            $value['member_image'] = getImageUrl($value['member_image']);
            $list2[$key] = $value;
        }

        return array(
            'guarantee_list' => $list1,
            'as_guarantee_list' => $list2
        );
    }

    public function getSavingsGUID()
    {
        return $this->member_info->obj_guid;
    }

    public function getShortLoanGUID()
    {
        if (!$this->member_info->short_loan_guid) {
            $this->member_info->short_loan_guid = generateGuid($this->member_info->uid, objGuidTypeEnum::SHORT_LOAN);
            $ret = $this->member_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate short loan account GUID for member failed - " . $ret->MSG);
            }
        }

        return $this->member_info->short_loan_guid;
    }

    public function getLongLoanGUID()
    {
        if (!$this->member_info->long_loan_guid) {
            $this->member_info->long_loan_guid = generateGuid($this->member_info->uid, objGuidTypeEnum::LONG_LOAN);
            $ret = $this->member_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate long loan account GUID for member failed - " . $ret->MSG);
            }
        }

        return $this->member_info->long_loan_guid;
    }

    public function getShortDepositGUID()
    {
        if (!$this->member_info->short_deposit_guid) {
            $this->member_info->short_deposit_guid = generateGuid($this->member_info->uid, objGuidTypeEnum::SHORT_DEPOSIT);
            $ret = $this->member_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate short deposit account GUID for member failed - " . $ret->MSG);
            }
        }

        return $this->member_info->short_deposit_guid;
    }

    public function getLongDepositGUID()
    {
        if (!$this->member_info->long_deposit_guid) {
            $this->member_info->long_deposit_guid = generateGuid($this->member_info->uid, objGuidTypeEnum::LONG_DEPOSIT);
            $ret = $this->member_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate long deposit account GUID for member failed - " . $ret->MSG);
            }
        }

        return $this->member_info->long_deposit_guid;
    }

    public static function memberBindBankAccount($params)
    {
        $member_id = intval($params['member_id']);
        $bank_id = $params['bank_id'];
        $account_name = $params['account_name'];
        $account_no = $params['account_no'];
        if ($member_id <= 0 || $bank_id <= 0 || !$account_name || !$account_no) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_bank = new common_bank_listsModel();
        $bank_info = $m_bank->find(array(
            'uid' => $bank_id
        ));
        if (!$bank_info) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $contact_phone = null;
        if ($bank_info['bank_code'] == 'wing') {
            $country_code = $params['country_code'];
            $phone_number = $params['phone_number'];
            if (!$country_code || !$phone_number) {
                return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
            }
            $phone_arr = tools::getFormatPhone($country_code, $phone_number);
            $contact_phone = $phone_arr['contact_phone'];
        }

        $m_handler = new member_account_handlerModel();

        // 重复检测
        $old = $m_handler->getRow(array(
            'member_id' => $member_id,
            'handler_type' => memberAccountHandlerTypeEnum::BANK,
            'handler_account' => $account_no,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE,
            'bank_code' => $bank_info['bank_code'],
        ));
        if ($old) {
            return new result(false, 'Have bound', null, errorCodesEnum::BANK_ALREADY_BOUND);
        }


        $handler = $m_handler->newRow();
        $handler->member_id = intval($member_id);
        $handler->handler_type = memberAccountHandlerTypeEnum::BANK;
        $handler->handler_name = $account_name;
        $handler->handler_account = $account_no;
        $handler->handler_phone = $contact_phone;
        $handler->handler_property = json_encode($bank_info);
        $handler->is_verified = 1;
        $handler->bank_id = $bank_info['uid'];
        $handler->bank_code = $bank_info['bank_code'];
        $handler->bank_currency = $bank_info['currency'];
        $handler->bank_name = $bank_info['bank_name'];
        $handler->state = accountHandlerStateEnum::ACTIVE;
        $handler->create_time = Now();
        $insert = $handler->insert();
        if (!$insert->STS) {
            return new result(false, 'Db error', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');

    }


    public static function getMemberMortgagedGoodsList($member_id)
    {
        $m = new member_assetsModel();
        $list = $m->select(array(
            'member_id' => $member_id,
            'asset_state' => array('>=', assetStateEnum::CERTIFIED),
            'mortgage_state' => 1
        ));
        if (count($list) < 1) {
            return null;
        }
        $return = array();
        $m_image = new member_verify_cert_imageModel();
        foreach ($list as $value) {
            // 图片
            $images = $m_image->select(array(
                'cert_id' => $value['cert_id']
            ));
            $one = current($images);
            $value['main_image'] = $one['image_url'];
            $return[] = $value;
        }

        return $return;
    }

    //获取member已经抵押的资产  放到资产类
    public static function getMemberMortgagedAssetList($member_id)
    {
        //还要表现资产的取出申请状态

    }


    public static function getMemberAssessment($member_id)
    {

        $r = new ormReader();

        // 资产估值
        $sql = "select sum(valuation) total from member_assets where member_id='$member_id' and asset_state >= '" . assetStateEnum::CERTIFIED . "' ";
        $asset_value = ($r->getOne($sql)) ?: 0;

        // 业务盈利能力
        $business_profitability = 0;

        return array(
            'asset_evaluation' => $asset_value,
            'business_profitability' => $business_profitability
        );

    }


    public static function getMemberLoanHistory($member_id, $page_num, $page_size, $filters = array())
    {
        $member_id = intval($member_id);

        $loan_account = self::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account ? $loan_account['uid'] : 0;

        $r = new  ormReader();

        $where = '';
        /* if ($filters['product_id']) {

             $where .= " and c.sub_product_id=" . qstr($filters['product_id']);
         }*/

        if ($filters['member_credit_category_id']) {
            $where .= " and c.member_credit_category_id=" . qstr($filters['member_credit_category_id']);
        }

        $sql = "select c.*,p.category  from loan_contract c left join loan_product p on p.uid=c.product_id
          where c.account_id='$account_id' and c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "' $where order by c.uid desc  ";

        $page = $r->getPage($sql, $page_num, $page_size);

        return new result(true, '', array(
            'total_num' => $page->count,
            'total_pages' => $page->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $page->rows
        ));


    }

    /**
     * 检查交易密码
     * @param string $tradingPassword md5加密后的交易密码
     * @return bool
     */
    public static function checkTradingPassword($memberId, $tradingPassword)
    {
        $m_member = new memberModel();
        $member = $m_member->getRow($memberId);
        if (!$member) {
            return false;
        }

        if ($member->trading_password) {
            if ($tradingPassword == $member->trading_password) {
                return true;
            } else {
                return false;
            }
        } else {
            if (!$tradingPassword) {
                return true;
            } else {
                return false;
            }
        }
    }

    /** 获取member单日提现总额
     * @param $member_id
     * @param $day
     * @return float|int
     */
    public static function getMemberDayWithdrawSum($member_id, $day)
    {
        $r = new ormReader();
        $sql = "select * from biz_member_withdraw where member_id='$member_id' and date_format(create_time,'%Y-%m-%d')='$day'
        and state='" . bizStateEnum::DONE . "' ";
        $rows = $r->getRows($sql);
        $total = 0;
        foreach ($rows as $v) {
            $exchange_rate = global_settingClass::getCurrencyRateBetween($v['currency'], currencyEnum::USD);
            $total += $v['amount'] * $exchange_rate;
        }
        $total = round($total, 2);
        return $total;
    }


    /** 获取member单日充值总额
     * @param $member_id
     * @param $day
     * @return float|int
     */
    public static function getMemberDayDepositSum($member_id, $day)
    {
        $r = new ormReader();
        $sql = "select * from biz_member_deposit where member_id='$member_id' and date_format(create_time,'%Y-%m-%d')='$day'
        and state='" . bizStateEnum::DONE . "' ";
        $rows = $r->getRows($sql);
        $total = 0;
        foreach ($rows as $v) {
            $exchange_rate = global_settingClass::getCurrencyRateBetween($v['currency'], currencyEnum::USD);
            $total += $v['amount'] * $exchange_rate;
        }
        $total = round($total, 2);
        return $total;
    }

    /** 获取member单日转账总额
     * @param $member_id
     * @param $day
     * @return float|int
     */
    public static function getMemberDayTransferSum($member_id, $day)
    {
        $r = new ormReader();
        $sql = "select * from biz_member_transfer where member_id='$member_id' and date_format(create_time,'%Y-%m-%d')='$day'
        and state='" . bizStateEnum::DONE . "' ";
        $rows = $r->getRows($sql);
        $total = 0;
        foreach ($rows as $v) {
            $exchange_rate = global_settingClass::getCurrencyRateBetween($v['currency'], currencyEnum::USD);
            $total += $v['amount'] * $exchange_rate;
        }
        $total = round($total, 2);
        return $total;
    }

    /**
     * 修改member co
     * @param $member_id
     * @param $co_arr
     * @return result
     */
    public function setMemberCo($member_id, $co_arr)
    {
        $member_id = intval($member_id);

        if (!$member_id) {
            return new result(false, 'Param Error!');
        }

        $m_member_follow_officer = M('member_follow_officer');
        $m_um_user = M('um_user');
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $sql = "update member_follow_officer,um_user set member_follow_officer.is_active = 0,member_follow_officer.update_time = " . qstr(Now())
                . " WHERE member_follow_officer.member_id = '" . $member_id . "' and member_follow_officer.officer_id=um_user.uid and um_user.user_position='" . userPositionEnum::CREDIT_OFFICER . "'";
            $rt_1 = $m_member_follow_officer->conn->execute($sql);
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Edit Failure!');
            }

            foreach ($co_arr as $co_id) {
                $row = $m_member_follow_officer->getRow(array('member_id' => $member_id, 'officer_id' => $co_id));
                if ($row) {
                    $row->is_active = 1;
                    $rt_2 = $row->update();
                } else {
                    $user_info = $m_um_user->find(array('uid' => $co_id));
                    $row = $m_member_follow_officer->newRow();
                    $row->member_id = $member_id;
                    $row->officer_id = $co_id;
                    $row->officer_name = $user_info['user_name'];
                    $row->officer_type = 0;
                    $row->is_active = 1;
                    $row->update_time = Now();
                    $rt_2 = $row->insert();
                }
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Edit Failure!');
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Edit Successful!');

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 设置member的credit controller
     * @param $member_id
     * @param $co_arr
     * @return result
     * @throws Exception
     */
    public function setMemberCC($member_id, $co_arr)
    {
        $member_id = intval($member_id);

        if (!$member_id) {
            return new result(false, 'Param Error!');
        }

        $m_member_follow_officer = M('member_follow_officer');
        $m_um_user = M('um_user');
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $sql = "update member_follow_officer,um_user set member_follow_officer.is_active = 0,member_follow_officer.update_time = " . qstr(Now())
                . " WHERE member_follow_officer.member_id = '" . $member_id . "' and member_follow_officer.officer_id=um_user.uid and um_user.user_position='" . userPositionEnum::CREDIT_CONTROLLER . "'";
            $rt_1 = $m_member_follow_officer->conn->execute($sql);
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Edit Failure!');
            }

            foreach ($co_arr as $co_id) {
                $row = $m_member_follow_officer->getRow(array('member_id' => $member_id, 'officer_id' => $co_id));
                if ($row) {
                    $row->is_active = 1;
                    $rt_2 = $row->update();
                } else {
                    $user_info = $m_um_user->find(array('uid' => $co_id));
                    $row = $m_member_follow_officer->newRow();
                    $row->member_id = $member_id;
                    $row->officer_id = $co_id;
                    $row->officer_name = $user_info['user_name'];
                    $row->officer_type = 0;
                    $row->is_active = 1;
                    $row->update_time = Now();
                    $rt_2 = $row->insert();
                }
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Edit Failure!');
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Edit Successful!');

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }
    /**
     * 设置member的credit controller
     * @param $member_id
     * @param $co_arr
     * @return result
     * @throws Exception
     */
    public function setMemberRC($member_id, $co_arr)
    {
        $member_id = intval($member_id);

        if (!$member_id) {
            return new result(false, 'Param Error!');
        }

        $m_member_follow_officer = M('member_follow_officer');
        $m_um_user = M('um_user');
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $sql = "update member_follow_officer,um_user set member_follow_officer.is_active = 0,member_follow_officer.update_time = " . qstr(Now())
                . " WHERE member_follow_officer.member_id = '" . $member_id . "' and member_follow_officer.officer_id=um_user.uid and um_user.user_position='" . userPositionEnum::RISK_CONTROLLER . "'";
            $rt_1 = $m_member_follow_officer->conn->execute($sql);
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Edit Failure!');
            }

            foreach ($co_arr as $co_id) {
                $row = $m_member_follow_officer->getRow(array('member_id' => $member_id, 'officer_id' => $co_id));
                if ($row) {
                    $row->is_active = 1;
                    $rt_2 = $row->update();
                } else {
                    $user_info = $m_um_user->find(array('uid' => $co_id));
                    $row = $m_member_follow_officer->newRow();
                    $row->member_id = $member_id;
                    $row->officer_id = $co_id;
                    $row->officer_name = $user_info['user_name'];
                    $row->officer_type = 0;
                    $row->is_active = 1;
                    $row->update_time = Now();
                    $rt_2 = $row->insert();
                }
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Edit Failure!');
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Edit Successful!');

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }
    /** 获得会员抵押的资产列表
     * @param $member_id
     * @return null|ormCollection
     */
    public static function getMemberMortgageAssetList($member_id)
    {
        $r = new ormReader();
        $sql = "select * from member_assets where member_id='$member_id' and asset_state >='" . assetStateEnum::CERTIFIED . "'
        and mortgage_state=1 ";
        $rows = $r->getRows($sql);
        return (count($rows) > 0) ? $rows : null;
    }


    public static function getMemberIndustryInfo($member_id, $is_set_key = true)
    {
        $r = new ormReader();
        $sql = "select i.*,r.profit,r.income,r.expense,r.employees from member_industry a inner join common_industry i on i.uid=a.industry_id left join (
        select * from ( select * from member_income_business where member_id='$member_id' order by create_time desc) x  
        group by member_id,industry_id) r on r.industry_id=a.industry_id
        where a.member_id='$member_id' and a.state='" . memberIndustryStateEnum::ACTIVE . "'";

        $list = $r->getRows($sql);
        if ($is_set_key) {
            $arr = resetArrayKey($list, "uid");
            return $arr;
        }
        return $list;

    }

    public static function setMemberIndustry($member_id, $industry_list)
    {
        $m = M("member_industry");
        $sql = "update member_industry set state='" . memberIndustryStateEnum::HISTORY . "',update_time='" . Now() . "' where member_id='" . $member_id . "'";
        $rt_1 = $m->conn->execute($sql);
        if (!$rt_1->STS) {
            return new result(false, 'Update industry info fail.', null, errorCodesEnum::DB_ERROR);
        }
        if (count($industry_list)) {
            foreach ($industry_list as $kid) {
                $row = $m->newRow();
                $row->member_id = $member_id;
                $row->industry_id = $kid;
                $row->create_time = Now();
                $row->update_time = Now();
                $row->state = memberIndustryStateEnum::ACTIVE;
                $insert = $row->insert();
                if (!$insert->STS) {
                    return new result(false, 'Set industry fail.', null, errorCodesEnum::DB_ERROR);
                }
            }
        }
        return new result(true);
    }


    public static function getMemberResidencePlace($member_id, $lang = 'en')
    {
        $member = (new memberModel())->getRow($member_id);
        if (!$member) {
            return new result(false, 'Member not exist', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_address = new common_addressModel();
        $address = $m_address->getMemberResidencePlaceByGuid($member->obj_guid);
        if ($address) {
            // 得到分级地址的信息
            $ids = array(
                'id1' => $address['id1'] ?: 0,
                'id2' => $address['id2'] ?: 0,
                'id3' => $address['id3'] ?: 0,
                'id4' => $address['id4'] ?: 0,
            );
            $sql = "select * from core_tree where  uid in (" . implode(',', $ids) . " ) ";
            $rows = $m_address->reader->getRows($sql);
            if (!empty($rows)) {
                $rows = resetArrayKey($rows, 'uid');
            }
            foreach ($ids as $key => $uid) {
                $text_key = $key . '_text';
                if ($rows[$uid]) {
                    $node_text = $rows[$uid]['node_text'];
                    $text_alias = json_decode($rows[$uid]['node_text_alias'], true);
                    $address[$text_key] = $text_alias[$lang] ?: $node_text;
                } else {
                    $address[$text_key] = '';
                }
            }

        }

        // 设置的地图定位信息
        $map_info = (new member_address_map_detailModel())->getResidenceAddressMapInfo($member_id);
        return new result(true, 'success', array(
            'address_info' => $address,
            'address_map_info' => $map_info
        ));
    }


    public static function getMemberResidenceAddressMapInfo($member_id)
    {
        return (new member_address_map_detailModel())->getResidenceAddressMapInfo($member_id);
    }


    public static function getMemberCreditOfficerList($member_id, $request_by_bm = true)
    {
        $r = new ormReader();
        if ($request_by_bm) {
            $sql = "select a.*,u.user_code,u.user_position,u.mobile_phone from member_follow_officer a inner join um_user u on a.officer_id = u.uid where a.member_id='$member_id' and a.is_active=1 ";
        } else {
            $sql = "select a.*,um_user.user_code,um_user.user_position,um_user.mobile_phone from member_follow_officer a inner join um_user on a.officer_id = um_user.uid where a.member_id='$member_id' and a.is_active=1 and (um_user.user_position='" . userPositionEnum::CREDIT_OFFICER . "' or um_user.user_position='" . userPositionEnum::OPERATOR . "') ";
        }

        $list = $r->getRows($sql);
        return $list;
    }


    public static function addGuaranteeApply($params)
    {
        $member_id = $params['member_id'];
        if ($member_id <= 0) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $country_code = $params['country_code'];
        $phone = $params['phone'];
        $relation_type = $params['relation_type'];
        $guarantee_member_account = trim($params['guarantee_member_account']);

        $m_member = new memberModel();

        $o_member = $m_member->getRow($member_id);
        if (!$o_member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $relate_member = $m_member->getRow(array(
            'login_code' => $guarantee_member_account
        ));

        if (!$relate_member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $m_guarantee = new member_guaranteeModel();
        $new_row = $m_guarantee->newRow();
        $new_row->member_id = $member_id;
        $new_row->relation_member_id = $relate_member->uid;
        $new_row->relation_type = $relation_type;
        $new_row->create_time = Now();
        $new_row->relation_state = memberGuaranteeStateEnum::CREATE;
        $insert = $new_row->insert();
        if (!$insert->STS) {
            return new result(false, 'Add fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');
    }


    public static function guaranteeApplyHandle($params)
    {
        $member_id = $params['member_id'];
        if ($member_id <= 0) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $uid = $params['uid'];
        $state = $params['state'];
        $m_guarantee = new member_guaranteeModel();
        $row = $m_guarantee->getRow(array(
            'relation_member_id' => $member_id,
            'uid' => $uid
        ));
        if (!$row) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        if ($state == 0) {
            $row->relation_state = memberGuaranteeStateEnum::REJECT;
        } else {
            $row->relation_state = memberGuaranteeStateEnum::ACCEPT;
        }
        $row->update_time = Now();
        $up = $row->update();
        if (!$up->STS) {
            return new result(false, 'Update fail', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success');
    }

    public static function getMemberLimit($member_grade, $limit_key)
    {
        $m_common_limit_member = M('common_limit_member');
        $limit_info = $m_common_limit_member->find(array('member_grade' => $member_grade, 'limit_key' => $limit_key));
        $all_currency = (new currencyEnum())->toArray();
        $limit_arr = array();
        if (!$limit_info) {
            foreach ($all_currency as $key => $currency) {
                $limit_arr['per_time'][$key] = 'Not limit';
                $limit_arr['per_day'][$key] = 'Not limit';
            }
        } else {
            foreach ($all_currency as $key => $currency) {
                $exchange_rate = global_settingClass::getCurrencyRateBetween(currencyEnum::USD, $key);
                if ($exchange_rate <= 0) {
                    return new result(false, 'Not set currency exchange rate:' . $currency . '-' . $currency);
                }
                if ($limit_info['per_time'] < 0) {
                    $limit_arr['per_time'][$key] = 'Not limit';
                } else {
                    $limit_arr['per_time'][$key] = ncPriceFormat(round($exchange_rate * $limit_info['per_time'], 2));
                }

                if ($limit_info['per_day'] < 0) {
                    $limit_arr['per_day'][$key] = 'Not limit';
                } else {
                    $limit_arr['per_day'][$key] = ncPriceFormat(round($exchange_rate * $limit_info['per_day'], 2));
                }
            }
        }

        return $limit_arr;
    }

    public function adjustMemberState($params)
    {
        $member_id = $params['uid'];
        $member_state = $params['member_state'];
        if ($member_id <= 0) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $row = $m_member->getRow(array(
            'uid' => $member_id
        ));
        $row->member_state = $member_state;
        $row->update_time = Now();
        $up = $row->update();
        if (!$up->STS) {
            return new result(false, 'Adjust fail', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'Success');
    }

    public function deleteMember($member_id)
    {
        if ($member_id <= 0) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $m_member_passport = new member_passportModel();
        $row = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$row) {
            return new result(false, 'Member not found', null, errorCodesEnum::UNEXPECTED_DATA);
        }
        $conn = ormYo::Conn();
        try {
            $conn->startTransaction();
            $row->phone_country = '';
            $row->phone_number = '';
            $row->phone_id = '';
            $row->is_verify_phone = 0;
            $row->member_state = memberStateEnum::CANCEL;
            $row->update_time = Now();
            $up = $row->update();
            if (!$up->STS) {
                $conn->rollback();
                return new result(false, 'Delete member fail', null, errorCodesEnum::DB_ERROR);
            }
            $sql = "delete from member_passport where member_id='$member_id'";
            $del = $m_member_passport->conn->execute($sql);
            if (!$del->STS) {
                $conn->rollback();
                return new result(false, 'Delete member fail.', null, errorCodesEnum::DB_ERROR);
            }
            $conn->submitTransaction();
            return new result(true, 'success');
        } catch (Exception $e) {
            return new result(false, $e->getMessage(), null, errorCodesEnum::DB_ERROR);
        }

    }

    public function resetMemberPassword($params)
    {
        $member_id = $params['uid'];
        $password = $params['password'];
        $confirm_password = $params['confirm_password'];
        if ($member_id <= 0) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        if ($password != $confirm_password) {
            return new result(false, 'Please input the same password.', null, errorCodesEnum::UNEXPECTED_DATA);
        }
        $m_member = new memberModel();
        $row = $m_member->getRow(array(
            'uid' => $member_id
        ));
        $row->login_password = md5($password);
        $row->update_time = Now();
        $up = $row->update();
        if (!$up->STS) {
            return new result(false, 'Set password fail', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'Success');
    }

    /**
     * 修改branch
     * @param $member_id
     * @param $branch_id
     * @return ormResult|result
     */
    public static function resetMemberBranch($member_id, $branch_id, $operator_id, $operator_name)
    {
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            return new result(false, 'Invalid Id.');
        }
        $old_branch_id = $row->branch_id;
        if ($row->branch_id == $branch_id) {
            return new result(false, 'Unmodified branch.');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $row->branch_id = $branch_id;
        $row->update_time = Now();
        $rt_1 = $row->update();
        if (!$rt_1->STS) {
            $conn->rollback();
            return $rt_1;
        }

        $sql = 'UPDATE member_follow_officer SET is_active = 0,update_time = "' . Now() . '" WHERE officer_type = 0 AND member_id = ' . $member_id;
        $rt_2 = $m_client_member->reader->conn->execute($sql);
        if (!$rt_2->STS) {
            $conn->rollback();
            return $rt_2;
        }
        if ($old_branch_id > 0) {
            $ret1 = taskControllerClass::cancelTaskById($member_id, userTaskTypeEnum::BM_NEW_CLIENT, $old_branch_id, objGuidTypeEnum::SITE_BRANCH);
        }
        $msg = "Get New Client 【" . $row->login_code . "】 From " . strtoupper($operator_name) . " At " . Now();
        $ret2 = taskControllerClass::handleNewTask($member_id, userTaskTypeEnum::BM_NEW_CLIENT, $branch_id, objGuidTypeEnum::SITE_BRANCH, $operator_id, objGuidTypeEnum::UM_USER, $msg);

        $conn->submitTransaction();
        return new result(true, 'Setting successful');
    }

    /**
     * 获取会员信息
     * @param $uid
     * @param $operator_id
     * @param $operator_position
     * @return result
     */
    public function getMemberDetailAndResearch($uid, $operator_id, $operator_position)
    {
        $member_detail = array();
        $uid = intval($uid);

        //client info
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            return new result(false, 'Invalid Id.');
        }
        $branch = M('site_branch')->find(array('uid' => $client_info['branch_id']));
        $client_info['branch_name'] = $branch['branch_name'];
        $member_detail['client_info'] = $client_info;
        $member_detail['credit_info'] = $client_info['credit_info'];


        //Residence
        $m_common_address = new common_addressModel();
        $residence = $m_common_address->getMemberResidencePlaceByGuid($client_info['obj_guid']);
        $member_detail['residence'] = $residence;

        //Google Map
        $m_member_address_map_detail = M('member_address_map_detail');
        $map_detail = $m_member_address_map_detail->orderBy('uid DESC')->find(array('member_id' => $uid));
        $member_detail['map_detail'] = $map_detail;

        $m_member_follow_officer = new member_follow_officerModel();
        $operator = $m_member_follow_officer->getOperatorInfoByMemberId($uid);
        $member_detail['operator'] = $operator;

        //member co list
        $member_co_list = $this->getMemberCoList($uid);
        $member_detail['member_co_list'] = $member_co_list;

        $member_detail['member_cc_list']=$this->getMemberCCList($uid);
        $member_detail['member_rc_list']=$this->getMemberRCList($uid);


        //$member_detail['allow_product'] = self::getMemberCreditLoanProduct($uid);
        $member_detail['credit_category'] = loan_categoryClass::getMemberCreditCategoryList($uid);
        //loan-account
        $member_detail['loan_account'] = self::getLoanAccountInfoByMemberId($uid);

        //identity
        $identity_list = $this->getMemberIdentityById($uid);
        $member_detail['identity_list'] = $identity_list;

        //CBC
        $member_cbc = M('client_cbc')->orderBy('uid DESC')->select(array('client_id' => $uid, "client_type" => 0));
        $member_detail['member_cbc'] = $member_cbc;


        //client request
        $client_request = M('member_credit_request')->orderBy('uid DESC')->find(array('member_id' => $uid));
        $member_detail['client_request'] = $client_request;

        $member_detail['client_relative'] = self::getMemberCurrentRelative($uid);

        //assets
        $assets = credit_officerClass::getMemberAssetsListAndEvaluateOfOfficerGroupByType($uid, $operator_id, $operator_position, array('is_include_invalid' => 1));// $class_member_assets->getAssetEvaluateAndRentalByOperatorId($uid, $operator_id);
        $member_detail['assets'] = $assets;

        //Business research
        $member_detail['business_income'] = $this->getMemberBusinessIncomeResearch($uid, $operator_id);

        //income salary
        $salary_income = M('member_income_salary')->select(array('member_id' => $uid));
        $member_detail['salary_income'] = $salary_income;

        //attachment
        $attachment_income = M('member_attachment')->select(array('member_id' => $uid));
        $member_detail['attachment_income'] = $attachment_income;

        //credit suggest
        $suggest_list = M('member_credit_suggest')->getLastSuggestOfOperator($uid, $operator_id);
        $member_detail['suggest_list'] = $suggest_list;


        $is_voting_suggest = M('member_credit_suggest')->find(array('member_id' => $uid, 'state' => memberCreditSuggestEnum::APPROVING));
        $member_detail['is_voting_suggest'] = $is_voting_suggest ? true : false;

        return new result(true, '', $member_detail);
    }

    /**
     * 获取co列表
     * @param $member_id
     * @return array
     */
    public function getMemberCoList($member_id)
    {
        $r = new ormReader();
        $sql = "SELECT mfo.*,uu.user_code,uu.mobile_phone,uu.user_position FROM member_follow_officer mfo INNER JOIN um_user uu ON mfo.officer_id = uu.uid WHERE mfo.is_active = 1 AND uu.user_position='".userPositionEnum::CREDIT_OFFICER."' AND mfo.member_id = " . intval($member_id);
        $co_list = $r->getRows($sql);
        return $co_list;
    }

    /**
     * 获取member的credit controller 列表
     * @param $member_id
     */
    public function getMemberCCList($member_id){
        $r = new ormReader();
        $sql = "SELECT mfo.*,uu.user_code,uu.mobile_phone,uu.user_position FROM member_follow_officer mfo INNER JOIN um_user uu ON mfo.officer_id = uu.uid WHERE mfo.is_active = 1 AND uu.user_position='".userPositionEnum::CREDIT_CONTROLLER."' AND mfo.member_id = " . intval($member_id);
        $co_list = $r->getRows($sql);
        return $co_list;
    }

    /**
     * 获取member的risk controller列表
     * @param $member_id
     */
    public function getMemberRCList($member_id){
        $r = new ormReader();
        $sql = "SELECT mfo.*,uu.user_code,uu.mobile_phone,uu.user_position FROM member_follow_officer mfo INNER JOIN um_user uu ON mfo.officer_id = uu.uid WHERE mfo.is_active = 1 AND uu.user_position='".userPositionEnum::RISK_CONTROLLER."' AND mfo.member_id = " . intval($member_id);
        $co_list = $r->getRows($sql);
        return $co_list;
    }

    /**
     * 获取会员身份信息
     * @param $id
     * @return array
     */
    public function getMemberIdentityById($id)
    {
        $r = new ormReader();
        $id = intval($id);
        $identity_type = memberIdentityClass::getIdentityType();
        $identity_type_key = array_keys($identity_type);
        $identity_type_str = '(' . implode(',', $identity_type_key) . ')';

//        $data = self::getAllCertDetail($id);
//        $identity_list = $data->DATA;
//        $identity_list = resetArrayKey($identity_list, 'cert_type');

        $sql = "SELECT * FROM member_verify_cert WHERE uid IN (SELECT MAX(uid) FROM member_verify_cert WHERE member_id = $id AND cert_type IN $identity_type_str GROUP BY cert_type)";
        $identity_list = $r->getRows($sql);
        $identity_list = resetArrayKey($identity_list, 'cert_type');

        $identity_type_new = array();
        foreach ($identity_type as $key => $val) {
            $identity_type_new[$key]['name'] = $val;
            $identity_type_new[$key]['detail'] = $identity_list[$key] ?: array();

            if ($key == certificationTypeEnum::ID && $identity_list[$key]) {
                if ($identity_list[$key]['cert_expire_time'] < Now()) {
                    $identity_type_new[$key]['expired_time'] = dateFormat($identity_list[$key]['cert_expire_time']);
                } elseif ($identity_list[$key]['cert_expire_time'] < dateAdd(Now(), 90)) {
                    $identity_type_new[$key]['will_be_expired'] = dateFormat($identity_list[$key]['cert_expire_time']);
                }
            }

            $new_identity = $identity_list[$key];
            if ($new_identity && $new_identity['uid'] != $identity_list[$key]['uid']) {
                $identity_type_new[$key]['is_new'] = true;
            } else {
                $identity_type_new[$key]['is_new'] = false;
            }
        }
        return $identity_type_new;
    }

    /**
     * 会员商业收入调查
     * @param $member_id
     * @param $operator_id
     * @return ormCollection
     */
    public static function getMemberBusinessIncomeResearch($member_id, $operator_id = 0)
    {
        $r = new ormReader();
        $member_id = intval($member_id);
        $operator_id = intval($operator_id);

        $sql = "SELECT ci.* FROM member_industry mi INNER JOIN common_industry ci ON mi.industry_id = ci.uid WHERE mi.state = 1 AND mi.member_id = " . $member_id;
        $member_industry = $r->getRows($sql);
        $member_industry = resetArrayKey($member_industry, 'uid');
        if ($member_industry) {
            $industry_ids = array_column($member_industry, 'uid');
            $industry_id_str = "(" . implode(',', $industry_ids) . ")";

            $sql = "SELECT * FROM member_income_business WHERE uid IN (SELECT max(uid) FROM member_income_business WHERE member_id = $member_id AND operator_id = $operator_id AND industry_id IN $industry_id_str GROUP BY branch_code)";
            $business_income = $r->getRows($sql);

            $branch_code = array();
            if ($business_income) {
                $business_income_ids = array_column($business_income, 'uid');
//                $business_income_id_str = "(" . implode(',', $business_income_ids) . ")";
//                $sql = "SELECT * FROM member_income_business_owner WHERE income_business_id IN $business_income_id_str";
//                $income_business_owner = $r->getRows($sql);
//                $income_business_owner_new = array();
//                foreach ($income_business_owner as $owner) {
//                    $income_business_owner_new[$owner['income_business_id']][] = $owner;
//                }
//
//                $sql = "SELECT * FROM member_income_business_image WHERE income_business_id IN $business_income_id_str";
//                $income_business_image = $r->getRows($sql);
//                $income_business_image_new = array();
//                foreach ($income_business_image as $image) {
//                    $income_business_image_new[$owner['income_business_id']][] = $image;
//                }
                $income_business_image = credit_researchClass::getMemberIncomeBusinessImage($business_income_ids);
                $income_business_owner = credit_researchClass::getMemberIncomeBusinessOwner($business_income_ids);

                foreach ($business_income as $income) {
                    $branch_code[] = $income['branch_code'];
                    $industry_id = $income['industry_id'];
                    $owner = $income_business_owner[$income['uid']];
                    $image = $income_business_image[$income['uid']];
                    $income['owner_list'] = $owner;
                    $income['image_list'] = $image;
                    $member_industry[$industry_id]['income_business'][] = $income;
                }
            }

            $sql = "SELECT branch_code,industry_id FROM member_income_business WHERE member_id = $member_id AND industry_id IN $industry_id_str";
            if (!empty($branch_code)) {
                $branch_code_str = "('" . implode("','", $branch_code) . "')";
                $sql .= " AND branch_code NOT IN $branch_code_str";
            }
            $sql .= " GROUP BY branch_code";
            $branch_code = $r->getRows($sql);
            foreach ($branch_code as $code) {
                $industry_id = $code['industry_id'];
                $code['is_add'] = true;
                $member_industry[$industry_id]['income_business'][] = $code;
            }
        }
        return $member_industry;
    }

    /**
     * 修改工作类型
     * @param $member_id
     * @param $work_type
     * @return result
     */
    public function editMemberWorkType($member_id, $work_type)
    {
        $work_type = trim($work_type);
        if (!$work_type) {
            return new result(false, 'Invalid work type.');
        }

        $row = M('client_member')->getRow(intval($member_id));
        if (!$row) {
            return new result(false, 'Invalid Id');
        }

        $row->work_type = $work_type;
        $row->update_time = Now();
        $rt = $row->update();
        return $rt;
    }

    /**
     * 修改商业
     * @param $member_id
     * @param $is_with_business
     * @param $member_industry
     * @return result
     */
    public function editMemberBusiness($member_id, $is_with_business, $member_industry)
    {
        $row = M('client_member')->getRow(intval($member_id));
        if (!$row) {
            return new result(false, 'Invalid Id');
        }

        if ($row->is_with_business != $is_with_business) {
            $row->is_with_business = $is_with_business;
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                return $rt_1;
            }
        }

        //处理industry list
        $m_common_industry = M('common_industry');
        $industry_list = $m_common_industry->select(array('state' => 1));
        $arr_member_industry = array();
        if ($is_with_business) {
            foreach ($industry_list as $ik => $iv) {
                if (intval($member_industry['industry_item_' . $iv['uid']]) > 0) {
                    $arr_member_industry[] = $iv['uid'];
                }
            }
        }

        $rt_2 = self::setMemberIndustry($member_id, $arr_member_industry);
        if ($rt_2->STS) {
            return new result(true);
        } else {
            return $rt_2;
        }
    }

    /**
     * 设置限制产品
     * @param $member_id
     * @param $allow_product
     * @param $operator_id
     */
    public function setMemberLimitProduct($member_id, $allow_product, $operator_id)
    {
        $member_id = intval($member_id);

        $userObj = new objectUserClass($operator_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        //product list
        $class_loan_product = new loan_productClass();
        $product_list = $class_loan_product->getValidSubProductList();
        foreach ($product_list as $key => $product) {
            if (in_array($product['sub_product_code'], $allow_product)) {
                unset($product_list[$key]);
            }
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $m_member_limit_loan_product = M('member_limit_loan_product');
            $rt_1 = $m_member_limit_loan_product->delete(array('member_id' => $member_id));
            if (!$rt_1->STS) {
                $conn->rollback();
                return $rt_1;
            }

            foreach ($product_list as $limit_product) {
                $row = $m_member_limit_loan_product->newRow();
                $row->member_id = $member_id;
                $row->product_code = $limit_product['sub_product_code'];
                $row->operator_id = $userObj->user_id;
                $row->operator_name = $userObj->user_name;
                $row->create_time = Now();
                $rt_2 = $row->insert();
                if (!$rt_2) {
                    $conn->rollback();
                    return $rt_2;
                }
            }
            $conn->submitTransaction();
            return new result(true);
        } catch (Exception $e) {
            return new result(false, $e->getMessage(), null, errorCodesEnum::DB_ERROR);
        }
    }

    /**
     * 获取最新member信用申请
     * @param $member_id
     * @return mixed
     */
    public function getLastMemberCreditRequest($member_id)
    {
        $m_member_credit_request = M('member_credit_request');
        $member_request = $m_member_credit_request->orderBy('uid DESC')->find(array('member_id' => intval($member_id)));
        return $member_request;
    }

    /**
     * 获取member有效的产品
     * @param $member_id
     */
    public static function getMemberCreditLoanProduct($member_id)
    {
        //product list
        $class_loan_product = new loan_productClass();
        $product_list = $class_loan_product->getValidSubProductList();

        //limit product
        $limit_product = M('member_limit_loan_product')->select(array('member_id' => $member_id));
        $limit_product = array_column($limit_product, 'product_code');

        foreach ($product_list as $key => $product) {
            if (in_array($product['sub_product_code'], $limit_product)) {
                unset($product_list[$key]);
            }
        }
        foreach ($product_list as $k => $v) {
            $rate = loan_productCLass::getMinMonthlyRate($v['uid'], 'max');
            $product_list[$k]['max_rate_mortgage'] = $rate;
        }
        $product_list = resetArrayKey($product_list, "uid");
        return $product_list;
    }


    /** 获取member在执行中的全部贷款合同
     * @param $member_id
     * @param array $filter
     * @return ormCollection
     */
    public static function getMemberAllLoanContractUnderExecuting($member_id, $filter = array())
    {
        $r = new ormReader();
        $loan_account = self::getLoanAccountInfoByMemberId($member_id);
        $loan_account_id = intval($loan_account['uid']);
        $where = " account_id='$loan_account_id' and state>='" . loanContractStateEnum::PENDING_DISBURSE . "'
        and state <'" . loanContractStateEnum::COMPLETE . "' ";
        $sql = "select * from loan_contract where $where ";
        return $r->getRows($sql);
    }

    //获取member正在执行中的担保人列表
    public static function getMemberCurrentRelative($member_id)
    {
        $m_req = new member_credit_requestModel();
        $row = $m_req->orderBy("uid desc")->find(array("member_id" => $member_id, "state" => array(">=", 0)));
        if ($row) {
            $req_id = $row['uid'];
            $m_relative = new member_credit_request_relativeModel();
            $rows = $m_relative->select(array("request_id" => $req_id));
            $rows = resetArrayKey($rows, "uid");
            return $rows;
        }
        return null;
    }

    //获取member还没执行的grant_credit
    public static function getMemberNewGrantCredit($member_id)
    {
        $sql = "SELECT * FROM member_credit_grant WHERE member_id='$member_id' "
            . " AND uid NOT IN (SELECT grant_credit_id FROM `member_authorized_contract` WHERE contract_type=1 AND member_id='$member_id')"
            . " order by uid desc";

        $r = new ormReader();
        $item = $r->getRow($sql);
        return $item;
    }


    /**
     * 修改branch
     * @param $member_id
     * @param $branch_id
     * @return ormResult|result
     */
    public function resetMemberOperator($member_id, $officer_id)
    {
        $member_id = intval($member_id);
        $officer_id = intval($officer_id);

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $officer_info = (new um_userModel())->getRow($officer_id);
        if (!$officer_info) {
            return new result(false, 'User not exist', null, errorCodesEnum::USER_NOT_EXISTS);
        }

        $officer_name = $officer_info->user_name;

        $conn = ormYo::Conn();
        $conn->startTransaction();

        //更改以前operator为is_active
        $m_officer = new member_follow_officerModel();
        $sql = 'update member_follow_officer set is_active = 0,update_time = "' . Now() . '" WHERE officer_type = 1 and member_id = ' . $member_id;
        $rt_2 = $m_officer->reader->conn->execute($sql);
        if (!$rt_2->STS) {
            $conn->rollback();
            return $rt_2;
        }
        //插入当前operator
        $officer = $m_officer->newRow();
        $officer->member_id = $member_id;
        $officer->officer_id = $officer_id;
        $officer->officer_name = $officer_name;
        $officer->is_active = 1;
        $officer->officer_type = 1;
        $officer->update_time = Now();
        $insert = $officer->insert();
        if (!$insert->STS) {
            $conn->rollback();
            return new result(false, 'Bind fail', null, errorCodesEnum::DB_ERROR);
        }

        //修改client_member中operator
        $member->operator_id = $officer_id;
        $member->operator_name = $officer_name;
        $member->update_time = Now();
        $rt_1 = $member->update();
        if (!$rt_1->STS) {
            $conn->rollback();
            return new result(false, 'Bind fail', null, errorCodesEnum::DB_ERROR);
        }

        $conn->submitTransaction();
        return new result(true, 'Setting successful');
    }

    public static function changeMemberState($member_id, $member_state, $remark, $creator_id, $verify_type = 0, $is_conn = true)
    {
        $member_state = intval($member_state);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow(intval($member_id));
        if (!$row) {
            return new result(false, 'Invalid Id.');
        }

        $obj_user = new objectUserClass($creator_id);
        $creator_name = $obj_user->user_name;

        if ($is_conn) {
            $conn = ormYo::Conn();
            $conn->startTransaction();
        }

        $member_property = my_json_decode($row->member_property);
        $original_member_state = $row->member_state;
        if (is_array($member_property)) {
            $member_property['original_member_state'] = $original_member_state;
        } else {
            $member_property = array(
                'original_member_state' => $original_member_state
            );
        }

        $row->member_state = $member_state;

        if ($member_state == memberStateEnum::VERIFIED) {
            $row->verify_type = intval($verify_type);
            $row->verify_remark = $remark;
        }

        $row->member_property = my_json_encode($member_property);
        $row->update_time = Now();
        $rt = $row->update();
        if (!$rt->STS) {
            if ($is_conn) $conn->rollback();
            return new result(false, 'Change Failed.' . $rt->MSG);
        }

        $m_member_state_log = M('member_state_log');
        $row_log = $m_member_state_log->newRow();
        $row_log->member_id = $member_id;
        $row_log->original_state = $original_member_state;
        $row_log->current_state = $member_state;
        $row_log->creator_id = $creator_id;
        $row_log->creator_name = $creator_name;
        $row_log->create_time = Now();
        $row_log->remark = $remark;
        $rt = $row_log->insert();
        if (!$rt->STS) {
            if ($is_conn) $conn->rollback();
            return new result(false, 'Change Failed.' . $rt->MSG);
        }

        if ($is_conn) $conn->submitTransaction();
        return new result(true, 'Change Successful.');
    }

}