<?php

class partnerDataClass
{
    public static function getPartnerList()
    {
        $sql = "SELECT * FROM partner WHERE is_active = 1 ";
        $r = new ormReader();
        $partner_list = $r->getRows($sql);
        return $partner_list;
    }

    /**
     * 获取partner信息
     * @param $uid
     * @return array|bool|mixed|null
     */
    public static function getPartnerInfo($uid)
    {
        $uid = intval($uid);
        $m_partner = M('partner');
        $partner_info = $m_partner->find(array('uid' => $uid));
        if (!$partner_info) {
            return array();
        }
        $obj_partner = new objectPartnerClass($uid);
        $balance = $obj_partner->getPassbookCurrencyAccountDetail();
        $partner_info['balance'] = $balance;

        return $partner_info;
    }

    public static function getPartnerTransactions($partner_id, $pageNumber, $pageSize)
    {
        $partner_id = intval($partner_id);
        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 20;

        $obj_partner = new objectPartnerClass($partner_id);
        $obj_passbook = $obj_partner->getPassbook();
        $passbook_id = $obj_passbook->getBookId();


    }

//    public static function

    public static function getPartnerVoucher($partner_id, $pageNumber, $pageSize)
    {
        $partner_id = intval($partner_id);
        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 20;

    }

}