<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/17/2018
 * Time: 11:26 AM
 */
class data_center_staffControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('operator');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Dev");
        Tpl::setDir("data_center_staff");
    }

    /**
     * 首页
     */
    public function indexOp()
    {
        $m_branch = M('site_branch');
        $branch_list = $m_branch->select(array('status' => 1));
        Tpl::output('branch_list', $branch_list);
        Tpl::showPage("index");
    }

    /**
     * 获取staff list
     * @param $p
     * @return array
     */
    public function getStaffListOp($p)
    {
        $filters = array(
            'pageNumber' => trim($p['pageNumber']),
            'pageSize' => trim($p['pageSize']),
            'search_text' => trim($p['staff']),
            'country_code' => trim($p['country_code']),
            'phone_number' => trim($p['phone_number']),
            'branch_id' => intval($p['branch_id'])
        );
        return userClass::getUserList($filters);
    }

    /**
     * Staff Detail
     */
    public function showStaffDetailPageOp()
    {
        $uid = intval($_GET['uid']);
        $info = userClass::getUserBaseInfo($uid);
        Tpl::output('info', $info->DATA['user_info']);
        Tpl::showPage("staff.detail");
    }

    /**
     * Staff Log Page
     */
    public function showStaffLogPageOp($p)
    {
        $uid = intval($p['uid']);
        Tpl::output('uid', $uid);
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $client_type = staffDataClass::getClientLogType();
        Tpl::output('client_type', $client_type);
    }

    /**
     * Staff Log Data
     */
    public function getStaffLogListOp($p)
    {
        $uid = intval($p['uid']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $client_type = trim($p['client_type']);

        $filters = array(
            'uid' => $uid,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'client_type' => $client_type,
        );
        $data = staffDataClass::getStaffLogList($pageNumber, $pageSize, $filters);
        return $data;
    }

    /**
     * Authorization Page
     */
    public function showAuthorizationOp($p)
    {
        $user = new userBase($p['staff_id']);
        $user_auth = $user->getAuthList(); //当前用户权限
        $auth_list = $this->getAuthList();
        $auth_group_back_office = $auth_list['auth_group_back_office'];
        foreach($auth_group_back_office as $k => $group) {
            $str = L('auth_' . $k);
            foreach ($group as $auth) {
                if (in_array($auth, $user_auth['back_office'])) {
                    $allow_auth_back_office[$str][] = L('auth_' . strtolower($auth));
                }
            }
        }
            Tpl::output('auth_group', $allow_auth_back_office);
    }

    /**
     * 获取auth列表
     * @return array
     */
    private function getAuthList()
    {
        $define_auth_group = authBase::getAllAuthGroup();
        $define_auth_group_counter = $define_auth_group['counter'];
        $define_auth_group_back_office = $define_auth_group['back_office'];
        $auth_group_back_office = array();
        foreach ($define_auth_group_back_office as $key => $r) {
            $role = authBase::getAuthGroup($r, authTypeEnum::BACK_OFFICE);
            if (!$role) continue;
            $auth_group_list = $role->getAuthList();
            $auth_group_key = $role->getGroupKey();
            $auth_group_back_office[$auth_group_key] = $auth_group_list;
        }

        $auth_group_counter = array();
        foreach ($define_auth_group_counter as $key => $r) {
            $role = authBase::getAuthGroup($r, authTypeEnum::COUNTER);
            if (!$role) continue;
            $auth_group_list = $role->getAuthList();
            $auth_group_key = $role->getGroupKey();
            $auth_group_counter[$auth_group_key] = $auth_group_list;
        }
        return array('auth_group_back_office' => $auth_group_back_office, 'auth_group_counter' => $auth_group_counter);
    }

}