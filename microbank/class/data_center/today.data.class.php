<?php

class todayDataClass
{
   public static function getTodaySystemInfo($date_start, $date_end){
       $r = new ormReader();

       // 格式化时间
       $date_start = date('Y-m-d 00:00:00',strtotime($date_start));
       $date_end = date('Y-m-d 23:59:59',strtotime($date_end));

       if($date_start == $date_end){
           $time = " >= " . qstr($date_end);
       }else{
           $time = " BETWEEN ".qstr($date_start)." AND ". qstr($date_end);
       }

       /*  新客户统计  */
       $sql = "select count(uid) cnt from client_member where create_time  " . $time;
       $new_client = $r->getOne($sql);
       $data['client'] = $new_client;


       /*  贷款统计  */
       $sql = "select currency,count(uid) contract_num,count(DISTINCT account_id) client_num,sum(apply_amount) amount from loan_contract where state >= ". qstr(loanContractStateEnum::PENDING_DISBURSE) ." and start_date " . $time  . " GROUP BY currency";
       $loan = $r->getRows($sql);
       $new_loan = array();
       foreach($loan as $k => $v){
           $temp = array(
               'currency' => $v['currency'],
               'count' => $v['contract_num'],
               'client_count' => $v['client_num'],
               'amount' => $v['amount']
           );
           $new_loan[$v['currency']] = $temp;
       }
       $data['loan'] = $new_loan;


       /* 放款统计 */
       $sql = "select currency,count(DISTINCT contract_id) contract_num,sum(amount) amount from loan_disbursement where state=".qstr(disbursementStateEnum::DONE)." and update_time $time group by currency";
       $disbursement = $r->getRows($sql);
       $new_disbursement = array();
       foreach($disbursement as $k => $v){
           $v['count'] = $v['contract_num'];
           $new_disbursement[$v['currency']] = $v;
       }
       $data['disbursement'] = $new_disbursement;

       /*  存款统计 */
       $sql = "select currency,count(DISTINCT member_id) client_num,sum(amount) amount from biz_member_deposit where state = ". qstr(bizStateEnum::DONE) ." and update_time " . $time . " group by currency";
       $deposit = $r->getRows($sql);
       $new_deposit = array();
       foreach($deposit as $k => $v){
           $v['count'] = $v['client_num'];
           $new_deposit[$v['currency']] = $v;
       }
       $data['deposit'] = $new_deposit;

       /* 取款统计 */
       $sql = "select currency,count(DISTINCT member_id) client_num,sum(amount) amount from biz_member_withdraw where state = ". qstr(bizStateEnum::DONE) ." and update_time " . $time . " group by currency";
       $withdraw = $r->getRows($sql);
       $new_withdraw = array();
       foreach($withdraw as $k => $v){
           $v['count'] = $v['client_num'];
           $new_withdraw[$v['currency']] = $v;
       }
       $data['withdraw'] = $new_withdraw;
       return $data;
   }

}