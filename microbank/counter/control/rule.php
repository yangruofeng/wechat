<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/12
 * Time: 13:55
 */
class ruleControl extends counter_baseControl
{

    public function __construct()
    {
        parent::__construct();
        Tpl::setDir("rule");
        Language::read('member');
        Tpl::setLayout('home_layout');
        $this->outputSubMenu('rule');

    }

    public function counterBizOp()
    {
        $branch_id = $this->branch_id;
        $set_list = enum_langClass::getCounterBizLang();
        $branch_rule = branchSettingClass::getCounterBizSetting($branch_id);
        Tpl::output('setting_value',$branch_rule);
        Tpl::output('list', $set_list);
        Tpl::showPage('counter.biz');
    }


}