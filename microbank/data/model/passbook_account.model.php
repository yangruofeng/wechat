<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/26
 * Time: 17:29
 */
class passbook_accountModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('passbook_account');
    }

    public function loadAccountListByGUIDs($guidList) {
        $filter = join(",", array_map(function($v) {
            return qstr($v);
        },$guidList));

        $sql = <<<SQL
select a.obj_guid, b.* from passbook a 
inner join passbook_account b on b.book_id = a.uid
where obj_guid in ($filter)
SQL;

        return $this->reader->getRows($sql);
    }

    public function loadAccountsByFlows($flows)
    {
        $filter = join(",", array_map(function ($v) {
            return qstr($v->account_id);
        }, $flows->toArray()));

        $ret = array();
        foreach ($this->getRows("uid in ($filter)") as $item) {
            $ret[$item->uid] = $item;
        }
        return $ret;
    }

    public function fillSavingsPassbookAccountInfoForMembers(&$memberList) {
        $guid_map = array();
        foreach ($memberList as $i => $member) {
            if ($member['obj_guid']) $guid_map[$member['obj_guid']] = $i;
        }

        if (!empty($guid_map)) {
            $account_list = $this->loadAccountListByGUIDs(array_keys($guid_map));

            foreach ($account_list as $account_info) {
                $guid = $account_info['obj_guid'];
                if (!$memberList[$guid_map[$guid]]['accounts']) $memberList[$guid_map[$guid]]['accounts']=array();
                $memberList[$guid_map[$guid]]['accounts'][]=$account_info;
            }
        }

        return $memberList;
    }
    public function fillSavingsPassbookAccountInfoForUsers(&$memberList) {
        $guid_map = array();
        foreach ($memberList as $i => $member) {
            if ($member['obj_guid']) $guid_map[$member['obj_guid']] = $i;
        }

        if (!empty($guid_map)) {
            $account_list = $this->loadAccountListByGUIDs(array_keys($guid_map));

            foreach ($account_list as $account_info) {
                $guid = $account_info['obj_guid'];
                if (!$memberList[$guid_map[$guid]]['accounts']) $memberList[$guid_map[$guid]]['accounts']=array();
                $memberList[$guid_map[$guid]]['accounts'][]=$account_info;
            }
        }

        return $memberList;
    }
    public function fillPassbookAccountInfoForBranches(&$branchList) {
        $guid_map = array();
        foreach ($branchList as $i => $branch) {
            if ($branch['obj_guid']) $guid_map[$branch['obj_guid']] = $i;
        }

        if (!empty($guid_map)) {
            $account_list = $this->loadAccountListByGUIDs(array_keys($guid_map));

            foreach ($account_list as $account_info) {
                $guid = $account_info['obj_guid'];
                if (!$branchList[$guid_map[$guid]]['accounts']) $branchList[$guid_map[$guid]]['accounts']=array();
                $branchList[$guid_map[$guid]]['accounts'][]=$account_info;
            }
        }

        return $branchList;
    }

    public function fillPassbookAccountInfoForBanks(&$bankList) {
        $guid_map = array();
        foreach ($bankList as $i => $bank) {
            if ($bank['obj_guid']) $guid_map[$bank['obj_guid']] = $i;
        }

        if (!empty($guid_map)) {
            $account_list = $this->loadAccountListByGUIDs(array_keys($guid_map));

            foreach ($account_list as $account_info) {
                $guid = $account_info['obj_guid'];
                if (!$bankList[$guid_map[$guid]]['accounts']) $bankList[$guid_map[$guid]]['accounts']=array();
                $bankList[$guid_map[$guid]]['accounts'][]=$account_info;
            }
        }

        return $bankList;
    }
}