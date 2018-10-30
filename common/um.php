<?php

class userBase
{
    public $property = array();
    private $roleList;
    private $authList;
    public $cache = array();//缓存数据
    static $current_user;

    public static function Current($key)
    {
        if (self::$current_user instanceof userBase) {
            return self::$current_user;
        } else {
            $user_info = operator::getUserInfo_old($key);
            if (!$user_info) return null;
            self::$current_user = new userBase($user_info);
            return self::$current_user;
        }
    }

    public function __construct($uid, $app_code = null)
    {
        if (is_numeric($uid)) {
            $orm = M("um_user");
            $this->property = $orm->getRow($uid);
        } else {
            if ($uid instanceof ormDataRow) {
                $this->property = $uid;
            } else {
                if (is_array($uid)) {
                    $this->property = new ormDataRow($uid);
                }
            }
        }
        if (!count($this->property)) {
            throw new Exception("Invalid User Information");
        }
    }

    public function getRoleList()
    {
        if ($this->roleList) return $this->roleList;
        $r = new ormReader();
        $sql = "SELECT ur.uid,ur.role_name FROM um_user_role uur LEFT JOIN um_role ur ON uur.role_id = ur.uid WHERE uur.user_id = " . $this->property->uid;
        $arr = $r->getRows($sql);
        $this->roleList = $arr;
        return $arr ?: array();
    }

    public function getAuthList()
    {
        if (is_array($this->authList)) return $this->authList;
        $role_list = $this->roleList ?: $this->getRoleList();

        $auth_back_office = array();
        $auth_counter = array();
        $class_role = new role();
        foreach ($role_list as $role) {
            $rt_role = $class_role->getRoleInfo($role['uid']);
            $auth_back_office = array_merge($auth_back_office, $rt_role->DATA['allow_back_office']['allow_auth']);
            $auth_counter = array_merge($auth_counter, $rt_role->DATA['allow_counter']['allow_auth']);
        }
        $auth_back_office = array_unique($auth_back_office);
        $auth_counter = array_unique($auth_counter);

        //去掉受限制的
        $arr_limit = $this->getUserLimitAuthList(authTypeEnum::BACK_OFFICE);
        $auth_back_office = array_diff($auth_back_office, $arr_limit);

        //增加特殊允许的
        $arr_allow = $this->getUserAllowAuthList(authTypeEnum::BACK_OFFICE);
        $auth_back_office = array_merge(array(), $auth_back_office, $arr_allow);
        $this->authList['back_office'] = array_unique($auth_back_office);

        //去掉受限制的
        $arr_limit = $this->getUserLimitAuthList(authTypeEnum::COUNTER);
        $auth_counter = array_diff($auth_counter, $arr_limit);

        //增加特殊允许的
        $arr_allow = $this->getUserAllowAuthList(authTypeEnum::COUNTER);
        $auth_counter = array_merge(array(), $auth_counter, $arr_allow);
        $this->authList['counter'] = array_unique($auth_counter);
        return $this->authList;
    }

    public function checkAuth($auth_code, $type)
    {
        $arr = $this->getAuthList();
        return in_array($auth_code, $arr[$type]);
    }

    public function checkRole($role_code)
    {
        $arr = $this->getRoleList();
        return in_array($role_code, $arr);
    }

    public function getUserLimitAuthList($type)
    {
        $m = M("um_special_auth");
        $arr = $m->select(array('user_id' => $this->property->uid, 'special_type' => 2, 'auth_type' => $type));
        $arr = array_column($arr, 'auth_code');
        return $arr ?: array();
    }

    public function getUserAllowAuthList($type)
    {
        $m = M("um_special_auth");
        $arr = $m->select(array('user_id' => $this->property->uid, 'special_type' => 1, 'auth_type' => $type));
        $arr = array_column($arr, 'auth_code');
        return $arr ?: array();
    }

    //获取用户基本信息
    public static function getPropertyByUserId($user_id)
    {
        $m = M("um_user");
        return $m->find(array("uid" => $user_id));
    }

}

class authBase
{
    public static function getAuthGroup($_role_code, $auth_type = 'back_office')
    {
        $auth_group_list = self::getAllAuthGroup();
        $auth_group = $auth_group_list[$auth_type];
        if (in_array($_role_code, $auth_group)) {
            if ($auth_type != authTypeEnum::BACK_OFFICE) {
                $_role_code = $auth_type . '_' . $_role_code;
            }
            $cls_name = "authGroup_" . $_role_code;
            $role = new $cls_name();
            $role->Code = $_role_code;
            return $role;
        } else {
            return 0;
        }
    }

    public static function getAllAuthGroup()
    {
        $arr = get_declared_classes();
        $rt = array();
        foreach ($arr as $k => $v) {
            if (startWith($v, "authGroup_counter_")) {
                $rt['counter'][] = substr($v, strlen("authGroup_counter_"));
                continue;
            }
            if (startWith($v, "authGroup_")) {
                $rt['back_office'][] = substr($v, strlen("authGroup_"));
            }
        }
        return $rt;
    }

}

