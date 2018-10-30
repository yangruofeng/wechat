<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/3
 * Time: 15:05
 */
class loanReportAnalysisClass
{

    public static function getAllOverdueContractData($condition)
    {
        $r = new ormReader();
        $day = $condition['day']?:date('Y-m-d');
        $currency = $condition['currency'];
        $where = '';
        $where .= " and lc.currency=".qstr($currency);
        if( $condition['branch_id'] ){
            $where .= " and lc.branch_id=".qstr($condition['branch_id']);
        }
        $sql = " select * from (
select  cm.display_name,cm.kh_display_name,lc.contract_sn,ls.contract_id,max(DATEDIFF('$day',ls.receivable_date)) overdue_day,
lc.currency,lc.apply_amount,sum(ls.receivable_principal-ls.paid_principal) principal_balance
from loan_installment_scheme  ls inner join loan_contract lc on ls.contract_id=lc.uid
left join client_member cm on lc.client_obj_guid=cm.obj_guid
where lc.state>='".loanContractStateEnum::PENDING_DISBURSE."' and lc.state!='".loanContractStateEnum::COMPLETE."' and ls.state!='".schemaStateTypeEnum::CANCEL."' and ls.state!='".schemaStateTypeEnum::COMPLETE."' $where
group by ls.contract_id
) xx where xx.overdue_day >0 order by xx.overdue_day asc ";
        $rows = $r->getRows($sql);
        // 封装数据
        $return_data = array(
            'range1_6' => array(
                'title' => 'Day Late: 1-6 Days',
                'list' => array()
            ),
            'range7_14' => array(
                'title' => 'Day Late: 7-14 Days',
                'list' => array()
            ),
            'range15_29' => array(
                'title' => 'Day Late: 15-29 Days',
                'list' => array()
            ),
            'range30_59' => array(
                'title' => 'Day Late: 30-59 Days',
                'list' => array()
            ),
            'range60_89' => array(
                'title' => 'Day Late: 60-89 Days',
                'list' => array()
            ),
            'range90_179' => array(
                'title' => 'Day Late: 90-179 Days',
                'list' => array()
            ),
            'range180_359' => array(
                'title' => 'Day Late: 180-359 Days',
                'list' => array()
            ),
            'range360' => array(
                'title' => 'Day Late: >=360 Days',
                'list' => array()
            ),
        );

        foreach( $rows as $v ){
            $days = $v['overdue_day'];
            if( $days >=1 && $days<=6 ){
                $return_data['range1_6']['list'][] = $v;
            }elseif(  $days >=7 && $days<=14 ){
                $return_data['range7_14']['list'][] = $v;
            }elseif( $days >=15 && $days<=29 ){
                $return_data['range15_29']['list'][] = $v;
            }elseif( $days >=30 && $days<=59 ){
                $return_data['range30_59']['list'][] = $v;
            }elseif( $days >=60 && $days<=89 ){
                $return_data['range60_89']['list'][] = $v;
            }elseif( $days >=90 && $days<=179 ){
                $return_data['range90_179']['list'][] = $v;
            }elseif( $days >=180 && $days<=359 ){
                $return_data['range180_359']['list'][] = $v;
            }else{
                $return_data['range360']['list'][] = $v;
            }
        }

        return $return_data;


    }

    public static function getDayDataOfLoan($condition)
    {
        $r = new ormReader();
        $where = " lc.state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE);
        if($condition['category']){
            $where .= " AND mcc.category_id = " . qstr($condition['category']);
        }
        if($condition['date_start'] == $condition['date_end']){
            $where .= ' and lc.start_date = ' .qstr(system_toolClass::getFormatEndDate($condition['date_end']));
        }else{
            $where .= ' and lc.start_date between '. qstr(system_toolClass::getFormatStartDate($condition['date_start'])) .' and ' . qstr(system_toolClass::getFormatEndDate($condition['date_end']));
        }
        if($condition['branch_id']){
            $where.=" and lc.branch_id=".qstr($condition['branch_id']);
        }

        $sql ="select DATE_FORMAT(lc.start_date,'%Y-%m-%d') start_date,count(DISTINCT  lc.account_id) client_count,
        count(lc.uid) contract_count from loan_contract lc 
        left join member_credit_category mcc on lc.member_credit_category_id = mcc.uid 
        where $where  group by  DATE_FORMAT(lc.start_date,'%Y-%m-%d') order by lc.start_date desc ";
        $rows = $r->getRows($sql);

        $list = array();
        foreach( $rows as $v ){
            $list[$v['start_date']]['loan']['client_count'] = $v['client_count'];
            $list[$v['start_date']]['loan']['contract_count'] = $v['contract_count'];
        }


        // 合计金额
        $sql = "select DATE_FORMAT(lc.start_date,'%Y-%m-%d') start_date, sum(lc.apply_amount) amount,lc.currency from loan_contract lc 
        left join member_credit_category mcc on lc.member_credit_category_id = mcc.uid 
        where $where  group by  DATE_FORMAT(lc.start_date,'%Y-%m-%d'),lc.currency order by lc.start_date desc 
        ";
        $currency_amount = $r->getRows($sql);

        foreach( $currency_amount as $v ){
            $list[$v['start_date']]['loan']['amount'][$v['currency']] = $v['amount'];
        }

        return $list;
    }

    public static function getDayDataOfRepayment($condition){
        $r = new ormReader();
        $where = " c.state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE) . " AND s.state = " . qstr(schemaStateTypeEnum::COMPLETE);
        if($condition['category']){
            $where .= " AND mcc.category_id = " . qstr($condition['category']);
        }
        if($condition['date_start'] == $condition['date_end']){
            $where .= ' and s.receivable_date = ' .qstr(system_toolClass::getFormatEndDate($condition['date_end']));
        }else{
            $where .= ' and s.receivable_date between '. qstr(system_toolClass::getFormatStartDate($condition['date_start'])) .' and ' . qstr(system_toolClass::getFormatEndDate($condition['date_end']));
        }
        if($condition['branch_id']){
            $where.=" and c.branch_id=".qstr($condition['branch_id']);
        }
        $sql = "select count(c.uid) count,DATE_FORMAT(s.receivable_date,'%Y-%m-%d') receivable_date,c.currency,sum(s.receivable_principal) amount,mcc.category_id from loan_installment_scheme s left join loan_contract c on c.uid = s.contract_id left join member_credit_category mcc on c.member_credit_category_id = mcc.uid where $where GROUP BY c.currency,s.receivable_date order by s.receivable_date desc";
        $rows = $r->getRows($sql);
        $sql = "select DATE_FORMAT(s.receivable_date,'%Y-%m-%d') receivable_date,count(DISTINCT c.account_id) count,mcc.category_id from loan_installment_scheme s left join loan_contract c on c.uid = s.contract_id left join member_credit_category mcc on c.member_credit_category_id = mcc.uid where $where GROUP BY s.receivable_date order by s.receivable_date desc";
        $client_rows = $r->getRows($sql);
        $client = array();
        foreach($client_rows as $v) {
            $client[$v['receivable_date']] = $v['count'];
        }
        $list = array();
        foreach($rows as $k => $v){
            $list[$v['receivable_date']]['repayment']['client_count'] = $client[$v['receivable_date']];
            $list[$v['receivable_date']]['repayment']['contract_count'] = $v['count'];
            $list[$v['receivable_date']]['repayment']['amount'][$v['currency']] = $v['amount'];
        }
        return $list;
    }

    public static function getDayDataOfPendingRepayment($condition){
        $r = new ormReader();
        $where = " c.state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE) . " AND s.state > " . qstr(schemaStateTypeEnum::CANCEL) . " and s.state < " . qstr(schemaStateTypeEnum::COMPLETE);
        if($condition['category']){
            $where .= " AND mcc.category_id = " . qstr($condition['category']);
        }
        if($condition['date_start'] == $condition['date_end']){
            $where .= ' and s.receivable_date = ' .qstr(system_toolClass::getFormatEndDate($condition['date_end']));
        }else{
            $where .= ' and s.receivable_date between '. qstr(system_toolClass::getFormatStartDate($condition['date_start'])) .' and ' . qstr(system_toolClass::getFormatEndDate($condition['date_end']));
        }
        if($condition['branch_id']){
            $where.=" and c.branch_id=".qstr($condition['branch_id']);
        }
        $sql = "select count(c.uid) count,DATE_FORMAT(s.receivable_date,'%Y-%m-%d') receivable_date,c.currency,sum(s.receivable_principal) amount,mcc.category_id from loan_installment_scheme s left join loan_contract c on c.uid = s.contract_id left join member_credit_category mcc on c.member_credit_category_id = mcc.uid where $where GROUP BY c.currency,s.receivable_date order by s.receivable_date desc";
        $rows = $r->getRows($sql);
        $sql = "select DATE_FORMAT(s.receivable_date,'%Y-%m-%d') receivable_date,count(DISTINCT c.account_id) count,mcc.category_id from loan_installment_scheme s left join loan_contract c on c.uid = s.contract_id left join member_credit_category mcc on c.member_credit_category_id = mcc.uid where $where GROUP BY s.receivable_date order by s.receivable_date desc";
        $client_rows = $r->getRows($sql);
        $client = array();
        foreach($client_rows as $v) {
            $client[$v['receivable_date']] = $v['count'];
        }
        $list = array();
        foreach($rows as $k => $v){
            $list[$v['receivable_date']]['pending_repayment']['client_count'] = $client[$v['receivable_date']];
            $list[$v['receivable_date']]['pending_repayment']['contract_count'] = $v['count'];
            $list[$v['receivable_date']]['pending_repayment']['amount'][$v['currency']] = $v['amount'];
        }
        return $list;
    }

    public function getDayAlarmByDate($date, $condition){
        $r = new ormReader();
        $where = " c.state >= " . qstr(loanContractStateEnum::PENDING_DISBURSE) . " AND s.state > " . qstr(schemaStateTypeEnum::CANCEL) . " and s.state < " . qstr(schemaStateTypeEnum::COMPLETE) . " AND s.receivable_date = " . qstr($date);
        if($condition['category']){
            $where .= " AND mcc.category_id = " . qstr($condition['category']);
        }
        if($condition['branch_id']){
            $where.=" and c.branch_id=".qstr($condition['branch_id']);
        }

        $sql = "select c.contract_sn,c.apply_amount,DATE_FORMAT(s.receivable_date,'%y-%m-%d') receivable_date,c.currency,cm.display_name,cm.obj_guid,s.receivable_principal,s.ref_amount,mcc.category_id ";
        $sql.=" from loan_installment_scheme s left join loan_contract c on c.uid = s.contract_id ";
        $sql.=" inner join client_member cm on c.client_obj_guid=cm.obj_guid and c.client_obj_type=1";
        $sql.=" left join member_credit_category mcc on c.member_credit_category_id = mcc.uid where $where";
        $rows = $r->getRows($sql);
        return $rows;
    }
}