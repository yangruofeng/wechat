<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/17/2018
 * Time: 11:26 AM
 */
class data_center_bankControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('operator');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Dev");
        Tpl::setDir("data_center_bank");
    }

    public function indexOp()
    {
        $bank_list = bankDataClass::getBankList();
        Tpl::output("bank_list", $bank_list);
        Tpl::showPage("index");
    }

    public function getBankInfoOp($p)
    {
        $uid = intval($p['uid']);
        if ($uid) {
            $bank_info = bankDataClass::getBankInfo($uid);
            $data = array(
                'data' => $bank_info,
            );
        } else {
            $data = array();
        }
        return $data;
    }
}