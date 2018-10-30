<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/29
 * Time: 下午4:08
 */
class member_voucherControl extends counter_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('member');
        Tpl::setDir('member_voucher');
        Tpl::setLayout('home_layout');
        Tpl::output("sub_menu",$this->getMemberBusinessMenu());
    }

    public function clientVoucherIndexOp(){
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $trade_type = global_settingClass::getAllTradingType();
        Tpl::output("trade_type", $trade_type);
        $member_id=$_GET['member_id'];
        $client_info=counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info",$client_info);
        Tpl::output("member_id",$member_id);
        Tpl::showPage("client.voucher.index");
    }

    public function getClientVoucherListOp($p){
        $trade_id = $p['trade_id'];
        $member_id = $p['member_id'];
        $memberObj = new objectMemberClass($member_id);
        $member_passbook = $memberObj->getSavingsPassbook();
        $trade_type = $p['trade_type'];
        $remark = $p['remark'];
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $pageNumber = $p['pageNumber'];
        $pageSize = $p['pageSize'];
        $filters = array(
            'trade_id' => $trade_id,
            'trade_type' => $trade_type,
            'remark' => $remark,
            'start_date' => $date_start,
            'end_date' => $date_end
        );
        $data = counter_codClass::getCounterVoucherData($member_passbook, $pageNumber, $pageSize, $filters);
        return $data;
    }

}



