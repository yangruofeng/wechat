<?php

/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/3/1
 * Time: 15:40
 */
class passbook_account_flowModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('passbook_account_flow');
    }


    public function getFlowDetailById($id)
    {
        $sql = <<<SQL
select af.*,acc.currency,t.category,t.trading_type,t.is_outstanding,t.subject t_subject,t.remark t_remark
from passbook_account_flow af left join passbook_trading t on t.uid=af.trade_id
left join passbook_account acc on acc.uid=af.account_id where af.uid='$id'
SQL;
        return $this->reader->getRow($sql);
    }


    /** 获取流水的分类数据
     * @param $passbook
     * @param $currency
     * @param $pageNum
     * @param $pageSize
     * @param null $type
     * @return mixed|ormPageResult
     */
    public function getPassbookFlowByType($passbook, $currency, $pageNum, $pageSize, $type = null, $filters = array())
    {
        $where = array();
        $where[] = "acc.book_id = " . $passbook->getBookId();
        $where[] = "acc.currency=" . qstr($currency);

        $balanceIncreaseDirection = $passbook->getBalanceIncreaseDirection();

        if ($balanceIncreaseDirection == accountingDirectionEnum::DEBIT) {
            $where[] = "(af.state = " . passbookAccountFlowStateEnum::DONE . " or (af.state = " . passbookAccountFlowStateEnum::OUTSTANDING . " and af.credit > 0))";
        } else {
            $where[] = "(af.state = " . passbookAccountFlowStateEnum::DONE . " or (af.state = " . passbookAccountFlowStateEnum::OUTSTANDING . " and af.debit > 0))";
        }

        if ($type) {

            // 收入
            if ($type == 1) {

                if ($balanceIncreaseDirection == accountingDirectionEnum::CREDIT) {
                    $where[] = " (af.credit > 0) ";
                } else {
                    $where[] = " (af.debit > 0) ";
                }


            } else {
                // 支出
                if ($balanceIncreaseDirection == accountingDirectionEnum::CREDIT) {
                    $where[] = " (af.debit > 0) ";
                } else {
                    $where[] = " (af.credit > 0) ";
                }
            }
        }

        $where_str = join(" AND ", $where);
        $sql = <<<SQL
select af.*,acc.currency,t.category,t.trading_type,t.is_outstanding,t.subject t_subject,t.remark t_remark
from passbook_account_flow af left join passbook_trading t on t.uid=af.trade_id
left join passbook_account acc on acc.uid=af.account_id where $where_str
order by af.uid desc
SQL;
        $list = $this->reader->getPage($sql, $pageNum, $pageSize);
        return $list;
    }


    /**
     * @param $passbook passbookClass
     * @param $currency
     * @param array $filters
     */
    public function searchAllFlow($passbook, $currency, $filters = array())
    {
        $where = array();
        $where[] = "acc.book_id = " . $passbook->getBookId();
        $where[] = "acc.currency=" . qstr($currency);
        if ($passbook->getBalanceIncreaseDirection() == accountingDirectionEnum::DEBIT) {
            $where[] = "(af.state = " . passbookAccountFlowStateEnum::DONE . " or (af.state = " . passbookAccountFlowStateEnum::OUTSTANDING . " and af.credit > 0))";
        } else {
            $where[] = "(af.state = " . passbookAccountFlowStateEnum::DONE . " or (af.state = " . passbookAccountFlowStateEnum::OUTSTANDING . " and af.debit > 0))";
        }

        if ($filters['trading_type']) {
            $where[] = " t.trading_type='" . $filters['trading_type'] . "' ";
        }

        if ($filters['min_amount']) {
            $min_amount = round($filters['min_amount'], 2);
            $where[] = "( (af.credit+af.debit) >= '$min_amount' ) ";
        }

        if ($filters['max_amount']) {
            $max_amount = round($filters['max_amount'], 2);
            $where[] = "( (af.credit+af.debit) <= '$max_amount' ) ";
        }

        if ($filters['start_date']) {
            $start_date = date('Y-m-d 00:00:00', strtotime($filters['start_date']));
            $where[] = "af.create_time>='$start_date' ";
        }

        if ($filters['end_date']) {
            $end_date = date('Y-m-d 23:59:59', strtotime($filters['end_date']));
            $where[] = "af.create_time<='$end_date' ";
        }

        $where_str = join(" AND ", $where);
        $sql = <<<SQL
select af.*,acc.currency,t.category,t.trading_type,t.is_outstanding,t.subject t_subject,t.remark t_remark
from passbook_account_flow af left join passbook_trading t on t.uid=af.trade_id
left join passbook_account acc on acc.uid=af.account_id where $where_str
order by af.uid asc
SQL;
        $list = $this->reader->getRows($sql);
        return $list;
    }


    /**
     * @param $passbook passbookClass
     * @param $currency
     * @param $pageNum
     * @param $pageSize
     * @param array $filters
     * @return ormPageResult
     */
    public function searchFlowListByBookAndCurrency($passbook, $currency, $pageNum, $pageSize, $filters = array())
    {
        $where = array();
        $where[] = "acc.book_id = " . $passbook->getBookId();
        $where[] = "acc.currency=" . qstr($currency);
        if ($passbook->getBalanceIncreaseDirection() == accountingDirectionEnum::DEBIT) {
            $where[] = "(af.state = " . passbookAccountFlowStateEnum::DONE . " or (af.state = " . passbookAccountFlowStateEnum::OUTSTANDING . " and af.credit > 0))";
        } else {
            $where[] = "(af.state = " . passbookAccountFlowStateEnum::DONE . " or (af.state = " . passbookAccountFlowStateEnum::OUTSTANDING . " and af.debit > 0))";
        }

        if ($filters['trading_type']) {
            $where[] = " t.trading_type='" . $filters['trading_type'] . "' ";
        }

        if ($filters['min_amount']) {
            $min_amount = round($filters['min_amount'], 2);
            $where[] = "( (af.credit+af.debit) >= '$min_amount' ) ";
        }

        if ($filters['max_amount']) {
            $max_amount = round($filters['max_amount'], 2);
            $where[] = "( (af.credit+af.debit) <= '$max_amount' ) ";
        }

        if ($filters['start_date']) {
            $start_date = date('Y-m-d 00:00:00', strtotime($filters['start_date']));
            $where[] = "af.create_time>='$start_date' ";
        }

        if ($filters['end_date']) {
            $end_date = date('Y-m-d 23:59:59', strtotime($filters['end_date']));
            $where[] = "af.create_time<='$end_date' ";
        }

        $where_str = join(" AND ", $where);

//        $sql = <<<SQL
//select af.*,date_format(af.update_time,'%Y-%m') date_month,acc.currency,t.category,t.trading_type
//from passbook_account_flow af left join passbook_trading t on t.uid=af.trade_id
//left join passbook_account acc on acc.uid=af.account_id where $where_str
//order by af.state, af.update_time desc, af.uid desc
//SQL;

        $sql = <<<SQL
select af.*,date_format(af.update_time,'%Y-%m') date_month,acc.currency,t.category,t.trading_type,t.is_outstanding,t.remark tmark,t.sys_memo
from passbook_account_flow af left join passbook_trading t on t.uid=af.trade_id
left join passbook_account acc on acc.uid=af.account_id where $where_str
order by af.state, af.update_time desc, af.uid desc
SQL;

        return $this->reader->getPage($sql, $pageNum, $pageSize);
    }

    /**
     * @param $passbook passbookClass
     * @param $currency
     * @param $monthArray
     * @return ormCollection
     */
    public function getMonthSummaryByBookAndCurrency($passbook, $currency, $monthArray)
    {

        $where = array();
        $where[] = "acc.book_id = " . $passbook->getBookId();
        $where[] = "acc.currency=" . qstr($currency);
        $where[] = "af.state = " . passbookAccountFlowStateEnum::DONE;
        if (!empty($monthArray)) {
            // 防止空数据的SQL IN 的错误
            $where[] = "date_format(af.update_time, '%Y-%m') in (" . join(",", array_map(function ($v) {
                    return qstr($v);
                }, $monthArray)) . ")";
        }

        $where_str = join(" AND ", $where);

        $sql = <<<SQL
select sum(af.credit) total_credit,sum(af.debit) total_debit,date_format(af.update_time,'%Y-%m') date_month 
from passbook_account_flow af 
left join passbook_account acc on acc.uid=af.account_id 
where $where_str 
group by date_format(af.create_time,'%Y-%m')
SQL;

        return $this->reader->getRows($sql);
    }


    /**
     * @param $passbook  passbookClass
     * @param $currency
     * @param $day
     * @param $page_num
     * @param $page_size
     */
    public function getBookAccountFlowOfTradingByDay($passbook,$currency,$day,$page_num,$page_size,$filter = array())
    {

        // 不要outstanding 的数据
        $where = array();
        $where[] = " acc.book_id = " . $passbook->getBookId();
        $where[] = " acc.currency=" . qstr($currency);
        $where[] = " af.state=".qstr(passbookAccountFlowStateEnum::DONE);
        $where[] = " t.is_outstanding!='1' ";

        $day_time = strtotime($day);
        $day_start = date('Y-m-d 00:00:00',$day_time);
        $day_end = date('Y-m-d 23:59:59',$day_time);

        $where[] = " af.update_time>=".qstr($day_start);
        $where[] = " af.update_time<=".qstr($day_end);

        $where_str = join(" AND ", $where);

        // 先获取分页的trading数据
        $sql = <<<SQL
select DISTINCT t.uid
from passbook_account_flow af left join passbook_account acc on acc.uid=af.account_id
left join passbook_trading t on t.uid=af.trade_id where $where_str
order by t.uid asc
SQL;
        $page_data = $this->reader->getPage($sql,$page_num,$page_size);

        $trading_list = $page_data->rows;

        $flow_list = array();
        if( count($trading_list) > 0 ){
            $trading_ids = array();
            foreach( $trading_list as $v ){
                $trading_ids[] = qstr($v['uid']);
            }
            $in_str = implode(',',$trading_ids);
            $sql = <<<SQL
select af.*,acc.book_id,p.book_name,p.book_code,p.parent_book_code,p.book_type,p.obj_type,p.obj_guid,
acc.currency,t.category,t.trading_type,t.is_outstanding,t.subject t_subject,t.remark t_remark,t.sys_memo
from passbook_account_flow af left join passbook_trading t on t.uid=af.trade_id
left join passbook_account acc on acc.uid=af.account_id
left join passbook p on p.uid=acc.book_id
where t.uid in ($in_str) and acc.currency='$currency'
order by t.uid asc,af.uid asc
SQL;
            $flow_list = $this->reader->getRows($sql);
        }

        $page_data->rows = $flow_list;
        return $page_data;
    }


}