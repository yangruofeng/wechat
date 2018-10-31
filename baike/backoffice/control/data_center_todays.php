<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/17/2018
 * Time: 11:26 AM
 */
class data_center_todaysControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('operator');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Dev");
        Tpl::setDir("data_center_todays");
    }

    public function indexOp()
    {
        $date = $_GET['date'];
        $condition = array(
            "date_end" => $date?:date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $data = todayDataClass::getTodaySystemInfo($date);
        Tpl::output("data", $data);
        Tpl::showPage("index");
    }


}