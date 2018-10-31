<?php

/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2017/10/16
 * Time: 10:44
 */
class indexControl extends baseControl
{
    public $has_task = false;

    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("book_layout");
        Tpl::setDir("layout");
        Tpl::output("html_title", "Back Office");
        Tpl::output("user_info", $this->user_info);
        //菜单处理
        $menu_items = $this->getResetMenu();
        $this->has_task = false;
        foreach ($menu_items as $item) {
            if ($item['task_type']) {
                $this->has_task = true;
                break;
            }
        }
        Tpl::output("has_task", $this->has_task);
        Tpl::output("menu_items", $menu_items);

        //不同职位  不同展示
        if ($this->user_position == userPositionEnum::OPERATOR) {
            Tpl::output('is_operator', true);
            Tpl::output('system_title', 'Operator');
        }
        if ($this->user_position == userPositionEnum::BRANCH_MANAGER) {
            Tpl::output('is_sub', true);
            Tpl::output('system_title', 'Branch Manager');
        }

        if ($this->user_position == userPositionEnum::DEVELOPER) {
            Tpl::output('is_sub', true);
            Tpl::output('system_title', 'Developer');
        }
    }

    public function indexOp()
    {
        if ($this->has_task) {
            $rt = $this->getTaskPendingCountOp(array("task_time" => 0));
            if ($rt->STS) {
                $list = $rt->DATA;
                $list = $list['list'];
                Tpl::output("task_num", $list);
            }
        }
        Tpl::showPage("null_layout");
    }

    public function getTaskPendingCountOp($p)
    {
       	$st = microtime(true);
        $last_time = $p['task_time'];
        if ($this->user_position == userPositionEnum::BRANCH_MANAGER) {
            $ret = taskControllerClass::getPendingTaskCount($this->user_info['branch_id'], $last_time,objGuidTypeEnum::SITE_BRANCH);
            $tp = array($this->user_info['branch_id'], $last_time,objGuidTypeEnum::SITE_BRANCH);
        }elseif($this->user_position==userPositionEnum::OPERATOR) {
            $ret = taskControllerClass::getPendingTaskCount($this->user_id, $last_time,objGuidTypeEnum::UM_USER);
            $tp = array($this->user_id, $last_time,objGuidTypeEnum::UM_USER);
        }else{
            $ret = taskControllerClass::getPendingTaskCount($this->user_id, $last_time);
            $tp = array($this->user_id, $last_time);
        }
        $ts = microtime(true) - $st;
        if ($ts > 1) {
            debug("time=$ts, params=" . json_encode($tp));
        } 

        if (!count($ret)) {
            return new result(false, "NO TASK");
        }
        return new result(true, "", array(
            "task_time" => Now(),
            "list" => $ret
        ));
    }
}
