<?php

class back_office_baseControl extends control
{
    public $user_id;
    public $user_name;
    public $user_info;
    public $auth_list;
    public $user_position;
    public $branch_id;
    public $branch_name;
    protected $userObj;


    function __construct()
    {
        //parent::__construct();
        //重构control的构造函数
        if ($_GET['cross_domain_uid'] && $_GET['cross_domain_passport']) {
            $ret = userClass::autoLoginByCrossDomain($_GET['cross_domain_uid'], $_GET['cross_domain_passport']);
        }
        parent::__construct();
        $this->checkLogin();

        //if (!$this->checkSecurity()) die("Access Denied");

        Language::read('auth,define,common_lang');
        $user = userBase::Current();
        $user_info = $user->property->toArray();
        $this->user_info = $user_info;
        $this->user_id = $user_info['uid'];
        $this->user_name = $user_info['user_code'];
        $this->user_position = $user_info['user_position'];
        $auth_arr = $user->getAuthList();
        $this->auth_list = $auth_arr['back_office'];

        $userObj = new objectUserClass($user_info['uid']);
        $this->userObj = $userObj;
        $this->branch_id = $userObj->branch_id;
        $this->branch_name = $userObj->branch_name;

        $is_system_close = userClass::chkSystemIsClose($this->user_position);
        if ($is_system_close) {
            $this->alertExit("System Closed.");
        }

        if (in_array($this->user_position, array(userPositionEnum::BACK_OFFICER))) {
            Tpl::output('is_console', true);
        }
    }

    public function getUserObj()
    {
        return $this->userObj;
    }

    protected function checkSecurity()
    {
        if (global_settingClass::getCommonSetting()['backoffice_deny_without_client']) {
            return $_COOKIE['SITE_PRIVATE_KEY'] == md5(date("Ydm"));
        } else {
            return true;
        }
    }

    /**
     * 获取用户当前执行的biz任务
     */
    protected function getProcessingTask()
    {
        $task = taskControllerClass::getProcessingTask($this->user_id);
        if (count($task)) {
            $task['cancel_url'] = getUrl('operator', 'abandonTask', array(), false, BACK_OFFICE_SITE_URL);
        } else {
            $task = array(
                'url' => '',
                'cancel_url' => '',
                'title' => "<None>"
            );
        }
        Tpl::output('processing_task', $task);
    }

    /**
     * 完成msg类型任务（就是点击一下就可以）
     * @param $p
     * @return mixed
     */
    public function finishMsgTaskOp($p)
    {
        $ret = taskControllerClass::finishTask($p['task_id'], $p['task_type'], $p['receiver_id'], $p['receiver_type']);
        return $ret;
    }


    public function getExportUrlOp($p)
    {
        $act = trim($p['act']);
        $op = trim($p['op']);
        if (!$act || !$op) {
            return new result(false, 'Param Error.');
        }
        unset($p['act']);
        unset($p['op']);
        $url = getUrl($act, $op, $p, false, BACK_OFFICE_SITE_URL);
        return new result(true, '', $url);
    }


}
