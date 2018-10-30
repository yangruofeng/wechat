<?php

class bankDataClass
{
    public static function getBankList()
    {
        $sql = "SELECT * FROM site_bank WHERE account_state = 1 ";
        $r = new ormReader();
        $bank_list = $r->getRows($sql);
        return $bank_list;
    }

    public static function getBankInfo($uid)
    {
        $uid = intval($uid);
        $r = new ormReader();
        $sql = "SELECT b.*,branch.branch_name FROM site_bank b INNER JOIN site_branch branch ON b.branch_id = branch.uid WHERE b.uid = " . $uid;
        $data = $r->getRow($sql);
        $obj_bank = new objectSysBankClass($uid);
        $balance = $obj_bank->getPassbookCurrencyAccountDetail();
        $data['bank_balance'] = $balance;
        return $data;
    }
}