<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/29
 * Time: 14:51
 */
class loan_contractModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('loan_contract');
    }


    /** 获取合同未还清计划
     * @param $contract_id
     */
    public function getContractUncompletedSchemas($contract_id)
    {
        $contract_id = intval($contract_id);
        $state_filter = "s.state!=" . qstr(schemaStateTypeEnum::CANCEL) . " and s.state!=" . qstr(schemaStateTypeEnum::COMPLETE);

        $sql = <<<SQL
select s.*,c.currency,c.contract_sn from loan_installment_scheme s
left join loan_contract c on c.uid=s.contract_id
where s.contract_id='$contract_id' and $state_filter
order by s.receivable_date asc
SQL;

        $rows = $this->reader->getRows($sql);
        return $rows;
    }


    public function getContractOverdueSchemas($contract_id)
    {
        $contract_id = intval($contract_id);
        $state_filter = "s.state!=" . qstr(schemaStateTypeEnum::CANCEL) . " and s.state!=" . qstr(schemaStateTypeEnum::COMPLETE);

        $today = date('Y-m-d');
        $sql = <<<SQL
select s.*,c.currency,c.contract_sn from loan_installment_scheme s
left join loan_contract c on c.uid=s.contract_id
where s.contract_id='$contract_id' and DATE_FORMAT(s.receivable_date,'%Y-%m-%d')<'$today' and $state_filter
order by s.receivable_date asc
SQL;

        $rows = $this->reader->getRows($sql);
        return $rows;
    }

    /** 合同是否全部还清计划
     * @param $contract_id
     * @return bool
     */
    public function contractIsPaidOff($contract_id)
    {
        $contract_id = intval($contract_id);
        $sql = "select count(*) from loan_installment_scheme where contract_id='$contract_id' and state!=" . qstr(schemaStateTypeEnum::CANCEL) . " and state!=" . qstr(schemaStateTypeEnum::COMPLETE);
        $count = $this->reader->getOne($sql);
        if ($count <= 0) {
            return true;
        }
        return false;
    }

    /** 合同是否有罚金未还清
     * @param $contract_id
     * @return bool
     */
    public function contractIsRemainPenalty($contract_id)
    {
        $contract_id = intval($contract_id);
        $sql = "select count(*) from loan_penalty where contract_id='$contract_id' and state != " . qstr(loanPenaltyHandlerStateEnum::DONE);
        $count = $this->reader->getOne($sql);
        if ($count > 0) {
            return true;
        }
        return false;
    }

    /** 获取合同未还清的罚金列表
     * @param $contract_id
     * @return bool
     */
    public function getContractUnPaidPenaltyList($contract_id)
    {
        $contract_id = intval($contract_id);
        $sql = "select * from loan_penalty where contract_id='$contract_id' and state != " . qstr(loanPenaltyHandlerStateEnum::DONE);
        $rows = $this->reader->getRows($sql);
        return $rows;
    }

    public function getContractByGrantId($grant_id)
    {
        $list = $this->orderBy('uid DESC')->select(array('credit_grant_id' => intval($grant_id), 'state' => array('>=', loanContractStateEnum::PENDING_DISBURSE)));
        return $list;
    }


    public function getUnConfirmedContractNumBySource($loan_account_id,$source)
    {
        $r = new ormReader();
        $sql = "select count(*) cnt from loan_contract where account_id=".qstr($loan_account_id)." 
        and create_source=".qstr($source)." and state>='".loanContractStateEnum::CREATE."' and state <'".loanContractStateEnum::PENDING_APPROVAL."' 
        and state !='".loanContractStateEnum::REFUSED."' ";
        return $r->getOne($sql)?:0;
    }


    public function searchContractListForWrittenOff($key_type,$key_word)
    {
        $where = '';
        switch( $key_type ){
            case 1:  // contract sn
                $where = " and c.contract_sn=".qstr($key_word);
                break;
            case 2:  // phone
                $where = " and m.phone_id like '%".qstr2($key_word)."%' ";
                break;
            case 3:  // name
                $where = " and (m.display_name like '%".qstr2($key_word)."%' or m.kh_display_name like '%".qstr2($key_word)."%' ) ";
                break;
            case 4:  // member guid
                $where = " and m.obj_guid=".qstr($key_word);
                break;
            default:
                $where = " and c.contract_sn=".qstr($key_word);
        }
        $sql = "select c.*,m.login_code,m.obj_guid member_cid,m.display_name,m.kh_display_name,m.phone_id,wf.uid is_apply_off,wf.state written_off_state
        from loan_contract c left join loan_account acc on acc.uid=c.account_id
        left join client_member m on m.obj_guid=acc.obj_guid left join loan_writtenoff wf on wf.contract_id=c.uid
        where c.state>='".loanContractStateEnum::PENDING_DISBURSE."' and c.state<'".loanContractStateEnum::COMPLETE."' $where group by c.uid
        ";

        return $this->reader->getRows($sql);


    }


    public function getMemberLoanOrRepayListOfProduct($member_id,$member_credit_category_id,$page_num=1,$page_size=100000)
    {
        $r = $this->reader;
        $member_credit_category_id = intval($member_credit_category_id);
        $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account?$loan_account['uid']:0;
        $sql = "
select * from (

select uid,uid contract_id,apply_amount amount,currency,create_time,'loan' as data_type from loan_contract  where 
account_id='$account_id' and member_credit_category_id='$member_credit_category_id' and state>='".loanContractStateEnum::PENDING_DISBURSE."'

union all 

select r.uid,r.contract_id,r.payer_amount amount,r.payer_currency currency,r.create_time,'repayment' as data_type from loan_repayment r
left join loan_contract c on c.uid=r.contract_id  where  c.account_id='$account_id' and c.member_credit_category_id='$member_credit_category_id' 
and r.state='".repaymentStateEnum::DONE."'

) x  order by create_time desc
";
        $page_data = $r->getPage($sql,$page_num,$page_size);
        return $page_data;

    }


    public function getExecutingContractByCategory($member_id,$member_category_id)
    {
        $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
        $sql = "select * from loan_contract where account_id=".qstr($loan_account['uid'])." and member_credit_category_id=".qstr($member_category_id).
        " and state>=".qstr(loanContractStateEnum::PENDING_DISBURSE)." and state<".qstr(loanContractStateEnum::ONLY_PENALTY)." order by end_date ";
        return $this->reader->getRows($sql);
    }


    public function getGoingContractsOfLoanCategoryByMemberGUID($category_id,$member_guid)
    {
        $sql = "select count(lc.uid) cnt  from loan_contract lc left join member_credit_category mcc on mcc.uid=lc.member_credit_category_id 
        where mcc.category_id=".qstr($category_id)." and lc.client_obj_guid=".qstr($member_guid)." and lc.state>=".qstr(loanContractStateEnum::PENDING_DISBURSE).
        " and lc.state<".qstr(loanContractStateEnum::COMPLETE);
        $num = $this->reader->getOne($sql);
        return intval($num);
    }


}