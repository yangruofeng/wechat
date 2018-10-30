<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/9/26
 * Time: 14:17
 */
class superLoanReportClass
{

    public static function getDailyReportData($date,$page_number,$page_size,$filter=array())
    {
        $day_end = date('Y-m-d 23:59:59',strtotime($date));
        $r = new ormReader();


        // 先做客户的分页
        $sql1 = "select lc.client_obj_guid,cm.uid from loan_contract lc INNER join member_credit_category mcc on mcc.uid=lc.member_credit_category_id  
        inner join client_member cm on cm.obj_guid=lc.client_obj_guid  
        left join site_branch sb on sb.uid=cm.branch_id 
        where lc.state>='".loanContractStateEnum::PENDING_DISBURSE."' and lc.start_date<='$day_end' ";
        if( $filter['loan_category_id'] ){
            $sql1 .= " and mcc.category_id=".qstr($filter['loan_category_id']);
        }
        if( $filter['branch_id'] ){
            $sql1 .= " and sb.uid=".qstr($filter['branch_id']);
        }
        if( $filter['currency'] ){
            $sql1 .= " and lc.currency=".qstr($filter['currency']);
        }
        if( $filter['search_text']){
            $sql1 .= " and (cm.obj_guid=".qstr($filter['search_text'])." or cm.display_name like '%".qstr2($filter['search_text'])."%'
             or cm.phone_id like '%".qstr2($filter['search_text'])."%') ";
        }

        $sql1 .= " group by lc.client_obj_guid ";

        $page_data = $r->getPage($sql1,$page_number,$page_size);

        $page_data->total = $page_data->count;
        $page_data->pageTotal = $page_data->pageCount;
        $page_data->pageNumber = $page_number;
        $page_data->pageSize = $page_size;

        $client = $page_data->rows;
        $ids = array(0);
        $member_ids = array(0);
        foreach( $client as $v ){
            $ids[] = $v['client_obj_guid'];
            $member_ids[] = $v['uid'];
        }

        // 查super loan授信的cycle
        $sql_cycle = " select mcg.member_id,count(mcg.uid) cnt from member_credit_grant_product mcgp 
        inner join member_credit_grant mcg on mcg.uid=mcgp.grant_id 
        left join member_credit_category mcc on mcc.uid=mcgp.member_credit_category_id 
        where mcg.state='".commonApproveStateEnum::PASS."'
        ";
        if( $filter['loan_category_id'] ){
            $sql_cycle .= " and mcc.category_id=".qstr($filter['loan_category_id']);
        }
        $sql_cycle .= " group by mcg.member_id ";
        $list = $r->getRows($sql_cycle);
        $grant_cycle = array();
        foreach( $list as $v ){
            $grant_cycle[$v['member_id']] = $v['cnt'];
        }



        $sql = "select cm.*,cm.uid member_id,mcc.credit,mcc.credit_usd,mcc.credit_khr,mc.grant_time credit_grant_time,sb.branch_name,address.full_text client_address,
            address.id1_text,address.id2_text,address.id3_text,address.id4_text,
            mco.officer_name,mah.handler_account ace_account,lc.apply_amount,lc.state contract_state,lc.end_date,lr.contract_repayment_amount 
            from loan_contract lc 
            INNER join member_credit_category mcc on mcc.uid=lc.member_credit_category_id 
            inner join client_member cm on cm.obj_guid=lc.client_obj_guid  
            left join site_branch sb on sb.uid=cm.branch_id 
            left join member_credit mc on mc.member_id=cm.uid
            left join ( select * from member_follow_officer where is_active=1 and officer_type=0 group by member_id 
            order by is_primary desc ) mco on mco.member_id=cm.uid 
            left join (select * from common_address where address_category='".addressCategoryEnum::MEMBER_RESIDENCE_PLACE."' 
            and state=1 order by create_time desc ) address on address.obj_guid=cm.obj_guid
            left join (select * from member_account_handler where 
            handler_type='".memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY."' and is_verified=1 and state='".accountHandlerStateEnum::ACTIVE."' 
            order by update_time ) mah on mah.member_id=cm.uid  
            left join (select contract_id,sum(amount) contract_repayment_amount from loan_repayment where state='".repaymentStateEnum::DONE."' group by contract_id ) 
            lr on lc.uid=lr.contract_id
            where lc.state>='".loanContractStateEnum::PENDING_DISBURSE."' and lc.start_date<='$day_end'  and lc.client_obj_guid in (".join(',',$ids).") ";
        if( $filter['loan_category_id'] ){
            $sql .= " and mcc.category_id=".qstr($filter['loan_category_id']);
        }
        if( $filter['branch_id'] ){
            $sql .= " and sb.uid=".qstr($filter['branch_id']);
        }
        if( $filter['currency'] ){
            $sql .= " and lc.currency=".qstr($filter['currency']);
        }

        $sql .= " group by cm.uid,lc.uid ";
        $sql .= " order by lc.start_date ";

        $list = $r->getRows($sql);
        // 合计数据，一个客户一条
        $format_data = array();

       /* echo '<pre>';
        var_export($list);
        echo '</pre>';*/

        foreach( $list as $v ){

            $temp = $format_data[$v['member_id']];
            if( $temp ){
            }else{
                if( $filter['currency'] == currencyEnum::KHR ){
                    $v['category_credit'] = $v['credit_khr'];
                }else{
                    $v['category_credit'] = $v['credit_usd'];
                }
                $temp = $v;
                $temp['loan_cycle'] = $grant_cycle[$v['member_id']]?:0;
            }

            // 做叠加的字段不能是查询字段
            $temp['withdraw_number'] += 1;
            $temp['loan_amount'] += $v['apply_amount'];

            $temp['repayment_amount'] += $v['contract_repayment_amount'];

            if( $v['contract_state'] == loanContractStateEnum::COMPLETE ){

                //$temp['repayment_amount'] += $v['apply_amount'];

            }else{

                if( !$temp['maturity_date'] ){
                    $temp['maturity_date'] = $v['end_date'];
                }elseif( $v['end_date'] > $temp['maturity_date'] ){
                    $temp['maturity_date'] = $v['end_date'];
                }

                if( !$temp['closed_date'] ){
                    $temp['closed_date'] = $v['end_date'];
                }elseif ( $v['end_date'] > $temp['closed_date'] ){
                    $temp['closed_date'] = $v['end_date'];
                }

                if( !$temp['repayment_date'] ){
                    $temp['repayment_date'] = $v['end_date'];
                }elseif( $v['end_date'] < $temp['repayment_date'] ){
                    $temp['repayment_date'] = $v['end_date'];
                }

                // 逾期的
                if( date('Y-m-d',strtotime($v['end_date'])) <  date('Y-m-d',strtotime($day_end)) ){
                    $temp['loan_arrea'] += $v['apply_amount'];
                }

                $day_diff = system_toolClass::diffBetweenTwoDays($day_end,$v['end_date']);
                if( $day_diff >0 && $day_diff>$temp['day_late'] ){
                    $temp['day_late'] = $day_diff;
                }

            }


            $format_data[$v['member_id']] = $temp;

        }

        $page_data->data = $format_data;

        return $page_data;

    }
    //Member Credit  获取
    public static function getCreditList($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = trim($p['search_text']);
        $category_id = $p['category_id'];
        //$currency = trim($p['currency']);
        //$date_start = $p['date_start'];
        //$date_end = $p['date_end'];
        $branch_id=intval($p['branch_id']);
        $sql="SELECT mcc.*,cm.obj_guid,cm.display_name,cm.login_code,cm.phone_id,cm.branch_id,sb.branch_name FROM member_credit_category mcc ";
        $sql.=" inner join client_member cm on mcc.member_id=cm.uid";
        $sql.=" inner join site_branch sb on cm.branch_id=sb.uid";
        $sql.=" WHERE mcc.category_id='".$category_id."' and mcc.credit>0";
        if($branch_id>0){
            $sql.=" and sb.uid=".qstr($branch_id);
        }
        if( $search_text ){
            $sql .= " and (cm.obj_guid=".qstr($search_text)." or cm.display_name like '%".qstr2($search_text)."%'
             or cm.phone_id like '%".qstr2($search_text)."%') ";
        }
        $r = new ormReader();
        $ret = $r->getPage($sql, $pageNumber, $pageSize);
        $ret=$ret->toArray();
        //取聚合值
        $list_member=$ret['data'];

        $list_member=resetArrayKey($list_member,"uid");

        $mcc_ids=array_keys($list_member);
        $str_mcc_ids=implode("','",$mcc_ids);
        $sql="SELECT member_credit_category_id mcc_id,COUNT(uid) loan_times,SUM(apply_amount) loan_amount,SUM(receivable_service_fee) service_fee,";
        $sql.="sum(case when state=100 then 0 else apply_amount end) outstanding";
        $sql.=" FROM loan_contract where member_credit_category_id in('".$str_mcc_ids."') and state>='".loanContractStateEnum::PENDING_DISBURSE."'  GROUP BY member_credit_category_id";
        $rows=$r->getRows($sql);
        $rows=resetArrayKey($rows,"mcc_id");
        $list=array();
        foreach($list_member as $item){
            $cnt_item=$rows[$item['uid']];
            $list[]=array_merge($item,array(
                "loan_amount"=>$cnt_item['loan_amount']?:0,
                "loan_times"=>$cnt_item['loan_times']?:0,
                "service_fee"=>$cnt_item['service_fee']?:0,
                "outstanding"=>$cnt_item['outstanding']?:0
            ));
        }
        $ret['data']=$list;
        return $ret;
    }
    //Loan Transaction 获取数据
    public static function getTrxLoanList($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = trim($p['search_text']);
        $category_id = $p['category_id'];
        //$currency = trim($p['currency']);
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $branch_id=intval($p['branch_id']);
        $sql="SELECT lc.*,cm.`display_name`,cm.`phone_id`,sb.branch_name FROM loan_contract lc ";
        $sql.=" INNER JOIN client_member cm ON lc.`client_obj_guid`=cm.`obj_guid` AND lc.`client_obj_type`=1";
        $sql.=" inner join site_branch sb on cm.branch_id=sb.uid";
        $sql.=" inner join member_credit_category mcc on cm.uid=mcc.member_id and lc.member_credit_category_id=mcc.uid";
        $sql.=" where lc.start_date between ".qstr($date_start)." and ".qstr($date_end);
        $sql.= " and lc.state>=".qstr(loanContractStateEnum::PENDING_DISBURSE);
        $sql.=" and mcc.category_id=".qstr($category_id);
        if($branch_id>0){
            $sql.=" and cm.branch_id=".qstr($branch_id);
        }
        if( $search_text ){
            $sql .= " and (cm.obj_guid=".qstr($search_text)." or cm.display_name like '%".qstr2($search_text)."%'
             or cm.phone_id like '%".qstr2($search_text)."%') ";
        }
        $r=new ormReader();
        $ret = $r->getPage($sql, $pageNumber, $pageSize);
        $ret=$ret->toArray();


        //计算还款
        $list_items=$ret['data'];
        $list_items=resetArrayKey($list_items,"uid");
        $lc_ids=array_keys($list_items);
        $str_lc_ids=implode("','",$lc_ids);
        $sql="select contract_id,sum(amount) amt from loan_repayment where contract_id in ('".$str_lc_ids."') and state=100 group by contract_id";
        $rows=$r->getRows($sql);
        $list=array();
        foreach($list_items as $item){
            $cnt_item=$rows[$item['uid']];
            $list[]=array_merge($item,array(
                "repaid"=>$cnt_item['amt']?:0,
                "outstanding"=>$item['apply_amount']-($cnt_item['amt']?:0)
            ));
        }
        $ret['data']=$list;
        return $ret;

    }
    //Repay Transaction 获取数据
    public static function getTrxRepayList($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = trim($p['search_text']);
        $category_id = $p['category_id'];
        //$currency = trim($p['currency']);
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $branch_id=intval($p['branch_id']);
        $sql="SELECT lr.`create_time`,lr.`amount`,lc.`client_obj_guid`,lc.apply_amount,lc.`start_date`,lc.`end_date`,lc.`contract_sn`,cm.`display_name`,cm.`phone_id`,sb.branch_name FROM loan_repayment lr";
        $sql.=" INNER JOIN loan_contract  lc ON lr.`contract_id`=lc.uid";
        $sql.=" INNER JOIN client_member cm ON lc.`client_obj_guid`=cm.`obj_guid` AND lc.`client_obj_type`=1";
        $sql.=" inner join site_branch sb on cm.branch_id=sb.uid";
        $sql.=" inner join member_credit_category mcc on cm.uid=mcc.member_id and lc.member_credit_category_id=mcc.uid";
        $sql.=" where lr.create_time between ".qstr($date_start)." and ".qstr($date_end);
        $sql.=" and lc.state>=".qstr(loanContractStateEnum::PENDING_DISBURSE);
        $sql.=" and mcc.category_id=".qstr($category_id);
        if($branch_id>0){
            $sql.=" and cm.branch_id=".qstr($branch_id);
        }
        if( $search_text ){
            $sql .= " and (cm.obj_guid=".qstr($search_text)." or cm.display_name like '%".qstr2($search_text)."%'
             or cm.phone_id like '%".qstr2($search_text)."%') ";
        }

        $sql.=" order by lr.uid desc";
        $r=new ormReader();
        $ret = $r->getPage($sql, $pageNumber, $pageSize);
        $ret=$ret->toArray();

        return $ret;

    }

}