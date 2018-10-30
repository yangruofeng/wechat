<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/17/2018
 * Time: 11:26 AM
 */
class data_center_dailyControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('operator');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Dev");
        Tpl::setDir("data_center_daily");
    }

    public function indexOp()
    {
        $condition = array(
            "date_start" => date('Y-m-d'),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage("index");
    }

    public function getInfoOp($p){
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        return todayDataClass::getTodaySystemInfo($date_start, $date_end);
    }


}