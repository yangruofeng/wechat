<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/17/2018
 * Time: 11:26 AM
 */
class data_center_partnerControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('operator');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Dev");
        Tpl::setDir("data_center_partner");
    }

    /**
     * 首页
     */
    public function indexOp()
    {
        $partner_list = partnerDataClass::getPartnerList();
        Tpl::output("partner_list", $partner_list);
        Tpl::showPage("index");
    }

    /**
     * 获取partner信息
     * @param $p
     * @return array
     */
    public function getPartnerInfoOp($p)
    {
        $uid = intval($p['uid']);
        if ($uid) {
            $partner_info = partnerDataClass::getPartnerInfo($uid);
            $data = array(
                'data' => $partner_info,
                'currency_list' => (new currencyEnum())->Dictionary(),
            );
        } else {
            $data = array();
        }
        return $data;
    }

}