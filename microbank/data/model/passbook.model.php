<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/26
 * Time: 17:28
 */
class passbookModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('passbook');
    }

    public function loadPassbooksByFlows($accounts)
    {
        $filter = join(",", array_map(function ($v) {
            return qstr($v->book_id);
        }, $accounts));

        $ret = array();
        foreach ($this->getRows("uid in ($filter)") as $item) {
            $ret[$item->uid] = $item;
        }
        return $ret;
    }

    public function getAssetsTotal() {
        $where = "b.book_type = " . qstr(passbookTypeEnum::ASSET);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getLiabilitiesTotal() {
        $where = "b.book_type = " . qstr(passbookTypeEnum::DEBT);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getEquitiesTotal() {
        $where = "b.book_type = " . qstr(passbookTypeEnum::EQUITY);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getAssetCashOnHand() {
        $where = "b.obj_type = 'client_member' and b.book_type=" . qstr(passbookTypeEnum::ASSET);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getAssetCashOnHandListOfCreditOfficer($search_text, $pageNumber, $pageSize) {
        $where = "user_position = " . qstr(userPositionEnum::CREDIT_OFFICER);

        if (trim($search_text)) {
            $where .= " AND (user_name LIKE '%" . trim($search_text) . "%' OR user_code LIKE '%" . trim($search_text) . "%' OR mobile_phone LIKE '%" . trim($search_text) . "%')";
        }
        $order = ' uid asc';
        $sql = <<<SQL
select uid 
from um_user 
where $where
order by $order
SQL;
        $data = $this->reader->getPage($sql, $pageNumber, $pageSize); //CO分页
        //相关CO账户信息
        $start = ($pageNumber - 1) * $pageSize;
        $limit = "$start,$pageSize";
        $where1 = "b.book_type=".qstr(passbookTypeEnum::ASSET)." or b.book_type is null ";

        $sql1 = <<<SQL
select u.uid,b.uid pid,u.user_code,u.user_name,u.obj_guid,u.mobile_phone,u.user_position,b.book_type,b.state,a.currency, sum(a.balance + a.outstanding) amount 
from passbook_account a 
inner join passbook b on b.uid = a.book_id 
right join (select * from um_user where $where order by $order limit $limit) u on u.obj_guid = b.obj_guid 
where $where1
group by u.uid,a.currency
SQL;
        
        $ret = array();
        foreach ($this->reader->getRows($sql1) as $row) {
            if(!$ret[$row['uid']]){
                $temp = array(); 
            }
            $ret[$row['uid']] = $row;
            $temp[$row['currency']] = $row['amount'];
            $ret[$row['uid']]['children'] = $temp;
        }

        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $ret,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public function getAssetCashOnHandTotalOfCreditOfficer() {
        $where = "c.user_position = " . qstr(userPositionEnum::CREDIT_OFFICER). " and b.book_type=" . qstr(passbookTypeEnum::ASSET);;

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
inner join um_user c on c.obj_guid = b.obj_guid
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getAssetCashOnHandListOfTeller($search_text, $pageNumber, $pageSize) {
        $where = "user_position in (" .
            qstr(userPositionEnum::TELLER) . "," .
            qstr(userPositionEnum::CHIEF_TELLER) . ")";

            
        if (trim($search_text)) {
            $where .= " AND (user_name LIKE '%" . trim($search_text) . "%' OR user_code LIKE '%" . trim($search_text) . "%' OR mobile_phone LIKE '%" . trim($search_text) . "%')";
        }
        $order = ' uid asc';
        $sql = <<<SQL
select uid 
from um_user 
where $where 
order by $order
SQL;

        $data = $this->reader->getPage($sql, $pageNumber, $pageSize); //teller分页
        //相关teller账户信息
        $start = ($pageNumber - 1) * $pageSize;
        $limit = "$start,$pageSize";
        $where1 = "b.book_type=".qstr(passbookTypeEnum::ASSET)." or b.book_type is null ";
        $sql1 = <<<SQL
select u.uid,b.uid pid,u.user_code,u.user_name,u.obj_guid,u.mobile_phone,u.user_position,b.book_type,b.state,a.currency, sum(a.balance + a.outstanding) amount 
from passbook_account a 
inner join passbook b on b.uid = a.book_id 
right join (select * from um_user where $where order by $order limit $limit) u on u.obj_guid = b.obj_guid 
where $where1
group by u.uid,a.currency
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql1) as $row) {
            if(!$ret[$row['uid']]){
                $temp = array(); 
            }
            $ret[$row['uid']] = $row;
            $temp[$row['currency']] = $row['amount'];
            $ret[$row['uid']]['children'] = $temp;
        }

        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $ret,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public function getAssetPassbookFlow($pid, $pageNumber, $pageSize){
        $where = "p.uid = '$pid'";
        $order = "f.uid desc";
        $sql = <<<SQL
select f.*,a.currency  
from passbook_account_flow f left join passbook_account a on f.account_id = a.uid 
left join passbook p on a.book_id = p.uid 
where $where 
order by $order
SQL;

        $data = $this->reader->getPage($sql, $pageNumber, $pageSize); //teller分页
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $data->rows,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }   

    public function getAssetCashOnHandTotalOfTeller() {
        $where = "c.user_position in (" .
            qstr(userPositionEnum::TELLER) . "," .
            qstr(userPositionEnum::CHIEF_TELLER) . ") and b.book_type=" . qstr(passbookTypeEnum::ASSET);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
inner join um_user c on c.obj_guid = b.obj_guid
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getAssetCashOnHandListOfOtherUser($search_text, $pageNumber, $pageSize) {
        $where = "user_position not in (" .
            qstr(userPositionEnum::CREDIT_OFFICER) . "," .
            qstr(userPositionEnum::TELLER) . "," .
            qstr(userPositionEnum::CHIEF_TELLER) . ")";

            if (trim($search_text)) {
                $where .= " AND (user_name LIKE '%" . trim($search_text) . "%' OR user_code LIKE '%" . trim($search_text) . "%' OR mobile_phone LIKE '%" . trim($search_text) . "%')";
            }
            $order = ' uid asc';
            $sql = <<<SQL
select uid 
from um_user 
where $where 
order by $order
SQL;


        $data = $this->reader->getPage($sql, $pageNumber, $pageSize); //other分页
        //相关其他帐号账户信息
        $start = ($pageNumber - 1) * $pageSize;
        $limit = "$start,$pageSize";
        $where1 = "b.book_type=".qstr(passbookTypeEnum::ASSET)." or b.book_type is null ";



        $sql1 = <<<SQL
select u.uid,b.uid pid,u.user_code,u.user_name,u.obj_guid,u.mobile_phone,u.user_position,b.book_type,b.state,a.currency, sum(a.balance + a.outstanding) amount 
from passbook_account a 
inner join passbook b on b.uid = a.book_id 
right join (select * from um_user where $where order by $order limit $limit) u on u.obj_guid = b.obj_guid 
where $where1
group by u.uid,a.currency
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql1) as $row) {
            if(!$ret[$row['uid']]){
                $temp = array(); 
            }
            $ret[$row['uid']] = $row;
            
            $temp[$row['currency']] = $row['amount'];
            $ret[$row['uid']]['children'] = $temp;
        }

        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $ret,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }


    public function getAssetCashOnHandTotalOfOtherUser() {
        $where = "c.user_position not in (" .
            qstr(userPositionEnum::CREDIT_OFFICER) . "," .
            qstr(userPositionEnum::TELLER) . "," .
            qstr(userPositionEnum::CHIEF_TELLER) . ") and b.book_type=" . qstr(passbookTypeEnum::ASSET);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
inner join um_user c on c.obj_guid = b.obj_guid
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }


    public function getAssetCashInVaultTotalOfHq() {
        $where = "c.account_code = " . qstr(systemAccountCodeEnum::HQ_CIV) . " and b.book_type=" . qstr(passbookTypeEnum::ASSET);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
inner join gl_account c on c.obj_guid = b.obj_guid
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getCashInVaultListOfHq($search_text, $pageNumber, $pageSize) {
        $where = "a.account_code = ".qstr(systemAccountCodeEnum::HQ_CIV);
        if (trim($search_text)) {
            $where .= " AND (a.account_name LIKE '%" . trim($search_text) . "%' OR a.account_code LIKE '%" . trim($search_text) . "%' OR p.obj_guid LIKE '%" . trim($search_text) . "%')";
        }
        $order = ' p.uid asc';

        $sql = <<<SQL
select p.uid,p.obj_guid,p.obj_type,a.account_code,a.account_name 
from passbook p left join gl_account a on p.obj_guid = a.obj_guid 
where $where 
order by $order 
SQL;

        $data = $this->reader->getPage($sql, $pageNumber, $pageSize); //other分页
        //相关其他帐号账户信息
        $start = ($pageNumber - 1) * $pageSize;
        $limit = "$start,$pageSize";

        $sql1 = <<<SQL
select b.uid pid,b.bid uid,b.obj_guid,b.account_code,b.account_name,a.currency, sum(a.balance + a.outstanding) amount from 
(select p.uid,p.obj_guid,p.obj_type,a.uid bid,a.account_code,a.account_name from passbook p left join gl_account a on p.obj_guid = a.obj_guid where $where limit $limit) b  
left join passbook_account a on b.uid = a.book_id 
group by b.uid,a.currency 
SQL;


        $ret = array();
        foreach ($this->reader->getRows($sql1) as $row) {
            if(!$ret[$row['uid']]){
                $temp = array(); 
            }
            $ret[$row['uid']] = $row;
            $temp[$row['currency']] = $row['amount'];
            $ret[$row['uid']]['children'] = $temp;
        }


        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $ret,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public function getAssetCashInVaultTotalOfBranch() {
        $where = "b.obj_type = 'branch' and b.book_type=" . qstr(passbookTypeEnum::ASSET);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getCashInVaultListOfBranch($search_text, $pageNumber, $pageSize) {
        $where = "p.obj_type = 'branch' and p.book_type = ".qstr(passbookTypeEnum::ASSET);
        if (trim($search_text)) {
            $where .= " and (p.obj_guid = '" . trim($search_text) . "' OR b.branch_code LIKE '%" . trim($search_text) . "%' OR b.branch_name LIKE '%" . trim($search_text) . "%' OR b.contact_phone LIKE '%" . trim($search_text) . "%')";
        }
        $order = ' p.uid asc';

        $sql = <<<SQL
select p.uid,p.obj_guid,p.obj_type,b.branch_code,b.branch_name,b.contact_phone 
from passbook p left join site_branch b on p.obj_guid = b.obj_guid 
where $where 
order by $order
SQL;

        $data = $this->reader->getPage($sql, $pageNumber, $pageSize); //other分页
        //相关其他帐号账户信息
        $start = ($pageNumber - 1) * $pageSize;
        $limit = "$start,$pageSize";

        $sql1 = <<<SQL
select b.uid pid,b.bid uid,b.obj_guid,b.branch_code account_code,b.branch_name account_name,b.contact_phone,a.currency, sum(a.balance + a.outstanding) amount from 
(select p.uid,p.obj_guid,p.obj_type,b.uid bid,b.branch_code,b.branch_name,b.contact_phone from passbook p left join site_branch b on p.obj_guid = b.obj_guid where $where limit $limit) b  
left join passbook_account a on b.uid = a.book_id 
group by b.uid,a.currency 
SQL;


        $ret = array();
        foreach ($this->reader->getRows($sql1) as $row) {
            if(!$ret[$row['uid']]){
                $temp = array(); 
            }
            $ret[$row['uid']] = $row;
            $temp[$row['currency']] = $row['amount'];
            $ret[$row['uid']]['children'] = $temp;
        }


        $total = $data->count;
        $pageTotal = $data->pageCount; 

        return array(
            "sts" => true,
            "data" => $ret,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public function getAssetCashInVaultEachBranches() {
        $where = "b.obj_type = 'branch' and b.book_type=" . qstr(passbookTypeEnum::ASSET);

        $sql = <<<SQL
select c.branch_name, a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
left join site_branch c on c.obj_guid = b.obj_guid
where $where
group by c.branch_name, a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $branch_name = $row['branch_name'] ?: "Unknown Branches";
            if (!$ret[$branch_name]) {
                $ret[$branch_name] = array(
                    'amount' => array()
                );
            }
            $ret[$branch_name]['amount'][$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getAssetReceivableInterest() {
        $where = "c.account_code = " . qstr(systemAccountCodeEnum::RECEIVABLE_LOAN_INTEREST) . " and b.book_type=" . qstr(passbookTypeEnum::ASSET);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
inner join gl_account c on c.obj_guid = b.obj_guid
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getAssetOfGlAccount() {
        $where = "b.obj_type = 'gl_account' and b.book_type=" . qstr(passbookTypeEnum::ASSET);
        $where .= " and c.account_code not in (" .
            qstr(systemAccountCodeEnum::RECEIVABLE_LOAN_INTEREST) . "," .
            qstr(systemAccountCodeEnum::HQ_CIV) . ")";

        $sql = <<<SQL
select c.uid, c.account_name, a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
inner join gl_account c on c.obj_guid = b.obj_guid
where $where
group by c.uid, a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $account_name = $row['account_name'] ?: "Unknown Asset Account - " . $row['uid'];
            if (!$ret[$account_name]) {
                $ret[$account_name] = array(
                    'amount' => array()
                );
            }
            $ret[$account_name]['amount'][$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getAssetLongTermReceivablePrincipal() {
        $where = "b.obj_type = 'client_long_loan' and b.book_type = " . qstr(passbookTypeEnum::ASSET);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getReceivablePrincipalListOfLongTerm($search_text, $pageNumber, $pageSize) {
        $where = "b.obj_type = 'client_long_loan' and b.book_type = 'asset'";
        if (trim($search_text)) {
            $where .= " and (b.obj_guid = '" . trim($search_text) . "' OR m.login_code LIKE '%" . trim($search_text) . "%' OR m.display_name LIKE '%" . trim($search_text) . "%' OR m.phone_id LIKE '%" . trim($search_text) . "%')";
        }
        $order = ' uid asc';

        $sql = <<<SQL
select b.uid,b.obj_guid,b.obj_type,m.login_code,m.display_name,m.phone_id from passbook b 
left join client_member m on b.obj_guid = m.long_loan_guid 
where $where 
order by $order 
SQL;

        $data = $this->reader->getPage($sql, $pageNumber, $pageSize); //other分页
        //相关其他帐号账户信息
        $start = ($pageNumber - 1) * $pageSize;
        $limit = "$start,$pageSize";

        $sql1 = <<<SQL
select b.uid,b.mid,b.obj_guid,b.login_code,b.phone_id,a.currency, sum(a.balance + a.outstanding) amount from 
(select b.uid,b.obj_guid,b.obj_type,m.uid mid,m.login_code,m.display_name,m.phone_id from passbook b left join client_member m on b.obj_guid = m.long_loan_guid where $where limit $limit) b 
left join passbook_account a on b.uid = a.book_id 
group by b.uid,a.currency 
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql1) as $row) {
            if(!$ret[$row['uid']]){
                $temp = array(); 
            }
            $ret[$row['uid']] = $row;
            $temp[$row['currency']] = $row['amount'];
            $ret[$row['uid']]['children'] = $temp;
        }


        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $ret,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public function getAssetShortTermReceivablePrincipal() {
        $where = "b.obj_type = 'client_short_loan' and b.book_type = " . qstr(passbookTypeEnum::ASSET);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;
        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getReceivablePrincipalListOfShortTerm($search_text, $pageNumber, $pageSize) {
        $where = "b.obj_type = 'client_short_loan' and b.book_type = 'asset'";
        if (trim($search_text)) {
            $where .= " and (b.obj_guid = '" . trim($search_text) . "' OR m.login_code LIKE '%" . trim($search_text) . "%' OR m.display_name LIKE '%" . trim($search_text) . "%' OR m.phone_id LIKE '%" . trim($search_text) . "%')";
        }
        $order = ' uid asc';

        $sql = <<<SQL
select b.uid,b.obj_guid,b.obj_type,m.login_code,m.display_name,m.phone_id from passbook b 
left join client_member m on b.obj_guid = m.short_loan_guid 
where $where 
order by $order 
SQL;

        $data = $this->reader->getPage($sql, $pageNumber, $pageSize); //other分页
        //相关其他帐号账户信息
        $start = ($pageNumber - 1) * $pageSize;
        $limit = "$start,$pageSize";

        $sql1 = <<<SQL
select b.uid,b.mid,b.obj_guid,b.login_code,b.phone_id,a.currency, sum(a.balance + a.outstanding) amount from 
(select b.uid,b.obj_guid,b.obj_type,m.uid mid,m.login_code,m.display_name,m.phone_id from passbook b left join client_member m on b.obj_guid = m.short_loan_guid where $where limit $limit) b 
left join passbook_account a on b.uid = a.book_id 
group by b.uid,a.currency 
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql1) as $row) {
            if(!$ret[$row['uid']]){
                $temp = array(); 
            }
            $ret[$row['uid']] = $row;
            $temp[$row['currency']] = $row['amount'];
            $ret[$row['uid']]['children'] = $temp;
        }


        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $ret,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public function getAssetBankTotal() {
        $where = "b.obj_type = 'bank' and b.book_type = " . qstr(passbookTypeEnum::ASSET);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getAssetEachBank() {
        $where = "b.obj_type = 'bank' and b.book_type=" . qstr(passbookTypeEnum::ASSET);

        $sql = <<<SQL
select c.uid, b.uid pid, concat(c.bank_name, '(', c.bank_account_no,')') bank_name, a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
left join site_bank c on c.obj_guid = b.obj_guid
where $where
group by c.uid, a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $bank_name = $row['bank_name'] ?: "Unknown Bank";
            $k = $row['uid'];
            $y = $row['pid'];
            if (!$ret[$bank_name]) {
                $ret[$bank_name] = array(
                    'amount' => array(),
                    'description' => $bank_name,
                    'uid' => $k,
                    'pid' => $y,
                    'type' => 'bank',
                );
            }
            $ret[$bank_name]['amount'][$row['currency']] = $row['amount'];
        }

        return $ret;
    }

    public function getAssetOther() {
        $where = "b.book_type = " . qstr(passbookTypeEnum::ASSET) .
            " and b.obj_type not in ('bank', 'branch', 'client_long_loan', 'client_short_loan', 'gl_account', 'user')";

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getLiabilityLongTermDeposit() {
        $where = "b.obj_type = 'client_long_deposit' and b.book_type = " . qstr(passbookTypeEnum::DEBT);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getLiabilityShortTermDeposit() {
        $where = "b.obj_type = 'client_short_deposit' and b.book_type = " . qstr(passbookTypeEnum::DEBT);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getLiabilitySavings() {
        $where = "b.obj_type = 'client_member' and b.book_type = " . qstr(passbookTypeEnum::DEBT);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getLiabilityListOfSavings($search_text, $pageNumber, $pageSize) {
        $where = "b.obj_type = 'client_member' and b.book_type = " . qstr(passbookTypeEnum::DEBT);
        if (trim($search_text)) {
            $where .= " and (b.obj_guid = '" . trim($search_text) . "' OR m.login_code LIKE '%" . trim($search_text) . "%' OR m.display_name LIKE '%" . trim($search_text) . "%' OR m.phone_id LIKE '%" . trim($search_text) . "%')";
        }
        $order = ' uid asc';

        $sql = <<<SQL
select b.uid,b.obj_guid,b.obj_type,m.login_code,m.display_name,m.phone_id from passbook b 
left join client_member m on b.obj_guid = m.obj_guid 
where $where 
order by $order 
SQL;

        $data = $this->reader->getPage($sql, $pageNumber, $pageSize); //other分页
        //相关其他帐号账户信息
        $start = ($pageNumber - 1) * $pageSize;
        $limit = "$start,$pageSize";

        $sql1 = <<<SQL
select b.uid,b.mid,b.obj_guid,b.login_code,b.phone_id,a.currency, sum(a.balance + a.outstanding) amount from (
select b.uid,b.obj_guid,b.obj_type,m.uid mid,m.login_code,m.display_name,m.phone_id from passbook b left join client_member m on b.obj_guid = m.obj_guid where $where order by $order limit $limit) b 
left join passbook_account a on b.uid = a.book_id group by b.uid,a.currency 
SQL;


        $ret = array();
        foreach ($this->reader->getRows($sql1) as $row) {
            if(!$ret[$row['uid']]){
                $temp = array(); 
            }
            $ret[$row['uid']] = $row;
            $temp[$row['currency']] = $row['amount'];
            $ret[$row['uid']]['children'] = $temp;
        }


        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $ret,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public function getLiabilityPayableOfPartner() {
        $where = "b.obj_type = 'partner' and b.book_type = " . qstr(passbookTypeEnum::DEBT);

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getLiabilityOfGlAccount() {
        $where = "b.obj_type = 'gl_account' and b.book_type=" . qstr(passbookTypeEnum::DEBT);

        $sql = <<<SQL
select c.uid, c.account_name, a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
inner join gl_account c on c.obj_guid = b.obj_guid
where $where
group by c.uid, a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $account_name = $row['account_name'] ?: "Unknown Liability Account - " . $row['uid'];
            if (!$ret[$account_name]) {
                $ret[$account_name] = array(
                    'amount' => array()
                );
            }
            $ret[$account_name]['amount'][$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getLiabilityOther() {
        $where = "b.book_type = " . qstr(passbookTypeEnum::DEBT) .
            " and b.obj_type not in ('partner', 'client_member', 'client_short_deposit', 'client_long_deposit', 'gl_account')";

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getEquityOfGlAccount() {
        $where = "b.obj_type = 'gl_account' and b.book_type=" . qstr(passbookTypeEnum::EQUITY);

        $sql = <<<SQL
select c.uid, c.account_name, a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
inner join gl_account c on c.obj_guid = b.obj_guid
where $where
group by c.uid, a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $account_name = $row['account_name'] ?: "Unknown Equity Account - " . $row['uid'];
            if (!$ret[$account_name]) {
                $ret[$account_name] = array(
                    'amount' => array()
                );
            }
            $ret[$account_name]['amount'][$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getEquityOther() {
        $where = "b.book_type = " . qstr(passbookTypeEnum::EQUITY) .
            " and b.obj_type not in ('gl_account')";

        $sql = <<<SQL
select a.currency, sum(a.balance + a.outstanding) amount
from passbook_account a 
inner join passbook b on b.uid = a.book_id
where $where
group by a.currency
having amount != 0
SQL;

        $ret = array();
        foreach ($this->reader->getRows($sql) as $row) {
            $ret[$row['currency']] = $row['amount'];
        }
        return $ret;
    }

    public function getIncomeStatementList($searchs, $pageNumber, $pageSize) {
        $where = "c.category = " . qstr(passbookTypeEnum::PROFIT) . " and (f.create_time BETWEEN '" . $searchs['d1'] . "' AND '" . $searchs['d2'] . "')";

        if($searchs['account_name']){
            $where .= " and c.account_name like '%" . trim($searchs['account_name']) . "%'";
        }

        $sql = <<<SQL
select DATE_FORMAT(f.create_time, "%Y-%m-%d") create_time,b.obj_guid,f.uid, c.account_code,c.account_name, a.currency, sum(f.credit - f.debit) amount 
from passbook_account_flow f 
inner join passbook_account a on f.account_id = a.uid 
inner join passbook b on b.uid = a.book_id
inner join gl_account c on c.obj_guid = b.obj_guid 
where $where 
group by DATE_FORMAT( f.create_time, "%Y-%m-%d" ),a.currency   
order by f.create_time desc
SQL;

        $data = $this->reader->getPage($sql, $pageNumber, $pageSize); //CO分页
        $ret = array();
        foreach ($data->rows as $row) {
            if(!$ret[$row['create_time']]){
                $temp = array(); 
            }
            $ret[$row['create_time']] = $row;
            $temp[$row['currency']] = $row['amount'];
            $ret[$row['create_time']]['children'] = $temp;
        }

        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $ret,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );
    }

    public function getIncomeStatementTotal($date){
        $where = "c.category = " . qstr(passbookTypeEnum::PROFIT). " and DATE_FORMAT(f.create_time, '%Y-%m-%d') = '$date'";
        $sql = <<<SQL
select DATE_FORMAT(f.create_time, "%Y-%m-%d") create_date, f.*, a.currency, sum(f.credit - f.debit) amount from passbook_account_flow f 
inner join passbook_account a on f.account_id = a.uid 
inner join passbook b on b.uid = a.book_id 
inner join gl_account c on c.obj_guid = b.obj_guid
where $where 
group by a.currency 
SQL;

        $data = $this->reader->getRows($sql);
        $ret = array();
        foreach ($data as $row) {
            if(!$ret[$row['create_date']]){
                $temp = array(); 
            }
            $ret[$row['create_date']] = $row;
            $temp[$row['currency']] = $row['amount'];
            $ret[$row['create_date']]['children'] = $temp;
        }
        return $ret;
    }

    public function getIncomeStatementFlowList($date, $currency = null, $pageNumber, $pageSize){
        $where = "c.category = " . qstr(passbookTypeEnum::PROFIT). " and DATE_FORMAT(f.create_time, '%Y-%m-%d') = '$date'";
        if($currency){
            $where .= " and a.currency = " . qstr($currency);
        }
        $sql = <<<SQL
select f.*, a.currency, c.account_name, sum(f.credit - f.debit) amount from passbook_account_flow f 
inner join passbook_account a on f.account_id = a.uid 
inner join passbook b on b.uid = a.book_id 
inner join gl_account c on c.obj_guid = b.obj_guid 
where $where 
group by f.uid 
order by f.create_time desc
SQL;

        $data = $this->reader->getPage($sql, $pageNumber, $pageSize);
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $data->rows,
            "total" => $total,
            "pageTotal" => $pageTotal,
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize
        );

    }
}