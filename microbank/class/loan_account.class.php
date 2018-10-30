<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/11/7
 * Time: 9:35
 */
class loan_accountClass
{
    private $account_info;

    public function __construct($accountInfo)
    {
        $this->account_info = $accountInfo;

    }
    public function getAccountInfo(){
        if($this->account_info instanceof ormDataRow){
            return $this->account_info->toArray();
        }else{
            return array();
        }
    }

    public static function getLoanAccountInfoByMemberId($member_id)
    {
        $r = new ormReader();
        $sql = "select a.* from loan_account a inner join client_member m on a.obj_guid=m.obj_guid
        where m.uid=".qstr($member_id);
        return $r->getRow($sql);
    }
    public static function getLoanAccountRowByMemberId($member_id){
        $mb=new memberModel();
        $member_info=$mb->find(array("uid"=>$member_id));
        $obj_guid=$member_info['obj_guid'];
        $m=new loan_accountModel();
        $row=$m->getRow(array("obj_guid"=>$obj_guid));
        return $row;
    }

    public function getSavingsGUID() {
        switch ($this->account_info->account_type) {
            case loanAccountTypeEnum::MEMBER:
                // 储蓄账户直接是loan_account的obj_guid
                return $this->account_info->obj_guid;
            default:
                throw new Exception("Account type not supported now - " . $this->account_info->account_type);
        }
    }

    public function getShortLoanGUID() {
        switch ($this->account_info->account_type) {
            case loanAccountTypeEnum::MEMBER:
                return memberClass::getInstanceByGUID($this->account_info->obj_guid)->getShortLoanGUID();
            default:
                throw new Exception("Account type not supported now - " . $this->account_info->account_type);
        }
    }

    public function getLongLoanGUID() {
        switch ($this->account_info->account_type) {
            case loanAccountTypeEnum::MEMBER:
                return memberClass::getInstanceByGUID($this->account_info->obj_guid)->getLongLoanGUID();
            default:
                throw new Exception("Account type not supported now - " . $this->account_info->account_type);
        }
    }

    public function getShortDepositGUID() {
        switch ($this->account_info->account_type) {
            case loanAccountTypeEnum::MEMBER:
                return memberClass::getInstanceByGUID($this->account_info->obj_guid)->getShortDepositGUID();
            default:
                throw new Exception("Account type not supported now - " . $this->account_info->account_type);
        }
    }

    public function getLongDepositGUID() {
        switch ($this->account_info->account_type) {
            case loanAccountTypeEnum::MEMBER:
                return memberClass::getInstanceByGUID($this->account_info->obj_guid)->getLongDepositGUID();
            default:
                throw new Exception("Account type not supported now - " . $this->account_info->account_type);
        }
    }

    public function editLoanAccountDueDate($obj_guid, $day){
        $m_loan_account = new loan_accountModel();
        $row = $m_loan_account->getRow(array('obj_guid' => $obj_guid));
        if ($row) {
            $row->due_date = $day;
            $row->update_time = Now();
            $ret = $row->update();
            if (!$ret->STS) {
                return new result(false, 'Edit Failure!');
            }
        }else{
            return new result(false, 'Edit Failure!');
        }
        return new result(true, 'Edit Successful!');
    }

    public function editLoanAccountPrincipalPeriod($obj_guid, $period){
        $m_loan_account = new loan_accountModel();
        $row = $m_loan_account->getRow(array('obj_guid' => $obj_guid));
        if ($row) {
            $row->principal_periods = $period;
            $row->update_time = Now();
            $ret = $row->update();
            if (!$ret->STS) {
                return new result(false, 'Edit Failure!');
            }
        }else{
            return new result(false, 'Edit Failure!');
        }
        return new result(true, 'Edit Successful!');
    }


    public static function getSuperLoanRepaymentDateByAccountInfo($loan_account_info,$start_day)
    {
        // 直接是一个月后
        return date('Y-m-d',strtotime('+1 month',strtotime($start_day)));
        /*$day = $loan_account_info['due_date']?:date('d');
        $start_day_time = strtotime($start_day);
        $s_day = date('d',$start_day_time);
        $current_day = date("Y-m-$day",$start_day_time);

        if( intval($s_day) < intval($day) ){
            return $current_day;
        }
        return date('Y-m-d',strtotime('+1 month',strtotime($current_day)));*/
    }

    
}
