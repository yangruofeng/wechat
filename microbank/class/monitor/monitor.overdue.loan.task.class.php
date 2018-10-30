<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 8/23/2018
 * Time: 3:42 PM
 */
class monitorOverdueLoanTaskClass extends userMonitorTaskClass{
    public $biz_code=userTaskTypeEnum::MONITOR_OVERDUE_LOAN;
    public function getTaskPendingCount($receiver_id,$last_time, $receiver_type)
    {

        $sql="SELECT COUNT(lis.uid) cnt FROM loan_installment_scheme lis ";
        $sql.=" INNER JOIN loan_contract lc ON lis.`contract_id`=lc.`uid` ";
        $sql.=" INNER JOIN client_member cm ON lc.`client_obj_guid`=cm.`obj_guid`";
        //$sql.=" WHERE lis.state<100 AND lis.`receivable_date`<NOW() AND lc.`state`>=0";
        $sql.=" WHERE lis.state!='".schemaStateTypeEnum::COMPLETE."' and lis.state!='".schemaStateTypeEnum::CANCEL."' AND lis.`receivable_date`<NOW() AND lc.`state`>='".loanContractStateEnum::PENDING_DISBURSE."'
         and lc.`state`<'".loanContractStateEnum::COMPLETE."' ";

        if($receiver_id && $receiver_type){//说明是取operator或者BM要看的
            if($receiver_type==objGuidTypeEnum::SITE_BRANCH){
                $sql.=" and cm.branch_id=".qstr($receiver_id);
            }
            if($receiver_type==objGuidTypeEnum::UM_USER){
                $sql.=" and cm.uid in (select member_id from member_follow_officer where officer_id='".$receiver_id."')";
            }

        }else{
            //取所有的
        }
        $r=new ormReader();
        $ret=$r->getOne($sql);
        return array(
            $this->biz_code => array(
                "count_pending" => $ret?:0,
                "count_new" => 0,
            )
        );
        //showMessage("Not Implement");
    }

    public function getTaskPendingList($receiver_id,$receiver_type)
    {
        $sql="SELECT lc.`contract_sn`,cm.`display_name`,cm.`obj_guid`,lc.`apply_amount`,mcc.`alias` credit_category,lc.`repayment_type`,";
        $sql.="lc.currency,lis.paid_principal,lis.initial_principal,lis.amount,lis.actual_payment_amount,lis.scheme_name,lis.`receivable_date`,lis.`penalty_start_date`,lis.`receivable_principal`,lc.branch_id,sb.branch_name FROM loan_installment_scheme lis ";
        $sql.=" INNER JOIN loan_contract lc ON lis.`contract_id`=lc.`uid` ";
        $sql.=" INNER JOIN member_credit_category mcc ON lc.`member_credit_category_id`=mcc.`uid`";
        $sql.=" INNER JOIN client_member cm ON lc.`client_obj_guid`=cm.`obj_guid`";
        $sql.=" INNER JOIN site_branch sb ON lc.`branch_id`=sb.`uid`";
        $sql.=" WHERE lis.state!='".schemaStateTypeEnum::COMPLETE."' and lis.state!='".schemaStateTypeEnum::CANCEL."' AND lis.`receivable_date`<NOW() AND lc.`state`>='".loanContractStateEnum::PENDING_DISBURSE."'
         and lc.`state`<'".loanContractStateEnum::COMPLETE."' ";

        if($receiver_id && $receiver_type){//说明是取operator或者BM要看的
            if($receiver_type==objGuidTypeEnum::SITE_BRANCH){
                $sql.=" and cm.branch_id=".qstr($receiver_id);
            }
            if($receiver_type==objGuidTypeEnum::UM_USER){
                $sql.=" and cm.uid in (select member_id from member_follow_officer where officer_id='".$receiver_id."')";
            }

        }else{
            //取所有的
        }
        $r=new ormReader();
        $ret=$r->getRows($sql);
        return $ret;
    }
}