<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/12
 * Time: 14:08
 */
class objectMemberClass extends objectBaseClass
{
    public $member_id = null;
    public $member_account = null;
    public $display_name = null;
    public $trading_password = null;
    public $work_type = null;
    public $grade_code;
    public $grade_info = null;
    public $loan_account_info=null;
    public $member_property=null;
    public $branch_id=null;

    private $passbook = null;

    public static function getNewestSceneImage($member_id)
    {
        $ret=(new biz_scene_imageModel())->getMemberNewestSceneImage($member_id);
        return $ret['image'];
    }

    public function __construct($member_id)
    {
        $member_id = intval($member_id);
        $this->_initObject($member_id);
    }

    protected function _initObject($member_id)
    {
        $m = new memberModel();
        $member = $m->getRow($member_id);
        if( !$member ){
            throw new Exception('Member not found:'.$member_id,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $this->object_id = $member['obj_guid'];
        $this->object_type = objGuidTypeEnum::CLIENT_MEMBER;
        $this->object_info = $member;
        $this->trading_password = $member['trading_password'];
        $this->member_id = $member->uid;
        $this->member_account = $member->login_code;
        $this->work_type = $member->work_type;
        $this->grade_code = $member->member_grade;
        if( $member->member_grade ){
            $grade_info = (new member_gradeModel())->find(array(
                'grade_code' => $member->member_grade
            ));
            $this->grade_info = $grade_info;
        }
        $this->branch_id = $member['branch_id'];
        $this->display_name = $member['display_name']?:$member['login_code'];

        $this->member_property = json_decode($member->member_property,true);
        $this->loan_account_info = memberClass::getLoanAccountInfoByMemberId($member_id);

    }


    public function isCanLogin()
    {
        $member_state = $this->object_info['member_state'];
        if( $member_state == memberStateEnum::CANCEL ){
            return new result(false,'Member has been cancelled',null,errorCodesEnum::USER_BEEN_CANCELLED);
        }

        if( $member_state == memberStateEnum::TEMP_LOCKING ){
            return new result(false,'Member locked',null,errorCodesEnum::USER_LOCKED);
        }
        if( $member_state == memberStateEnum::SYSTEM_LOCKING ){
            return new result(false,'Member locked',null,errorCodesEnum::USER_LOCKED);
        }

        $black_list = $this->getBlackList();
        if( in_array(blackTypeEnum::LOGIN,$black_list) ){
            return new result(false,'Member is in black list for login.',null,errorCodesEnum::MEMBER_IS_IN_BLACK_LIST);
        }
        return new result(true);
    }

    /** 检查是否能进行业务操作
     * @return result
     */
    public function checkValid()
    {
        $member_state = $this->object_info['member_state'];
        if( $member_state == memberStateEnum::CANCEL ){
            return new result(false,'Member has been cancelled',null,errorCodesEnum::USER_BEEN_CANCELLED);
        }

        if( $member_state == memberStateEnum::CREATE ){
            return new result(false,'Member not checked.',null,errorCodesEnum::MEMBER_UN_CHECKED);
        }
        if( $member_state == memberStateEnum::TEMP_LOCKING ){
            return new result(false,'Member locked',null,errorCodesEnum::USER_LOCKED);
        }
        if( $member_state == memberStateEnum::SYSTEM_LOCKING ){
            return new result(false,'Member locked',null,errorCodesEnum::USER_LOCKED);
        }
        if( $member_state != memberStateEnum::VERIFIED ){
            return new result(false,'Member not verified.',null,errorCodesEnum::NOT_CERTIFICATE_ID);
        }
        return new result(true);
    }


    public function getBlackList()
    {
        $member_id = $this->member_id;
        $r = new ormReader();
        $sql = " select * from client_black where member_id='$member_id' group by `type` ";
        $rows = $r->getRows($sql);
        $list = array();
        foreach( $rows as $v ){
            $list[] = $v['type'];
        }
        return $list;
    }




    /** 信用贷是否在黑名单
     * @return result
     */
    public function isLimitCreditLoan()
    {
        // 检查黑名单
        $m = new client_blackModel();
        $black = $m->getRow(array(
            'member_id' => $this->member_id,
            'type' => blackTypeEnum::LOAN
        ));
        if( $black ){
            return new result(false,'Client is in black list!',null,errorCodesEnum::MEMBER_IS_IN_BLACK_LIST);
        }
        return new result(true,'success');

    }


    /** 验证交易密码
     * @param $input_pwd
     * @return result
     */
    public function checkTradingPassword($input_pwd,$remark='',$is_md5=false)
    {
        // 首先检查是否超过5次
        $m_log = new member_verify_trading_password_logModel();
        $times = $m_log->getDayErrorTimes($this->member_id);
        if( $times >= 5 ){
            return new result(false,'Password wrong too many times.',null,errorCodesEnum::PASSWORD_ERROR_MORE_TIMES);
        }

        if( !$this->trading_password ){
            return new result(false,'Not set trading password',null,errorCodesEnum::NOT_SET_TRADING_PASSWORD);
        }

        if( $is_md5 ){
            $trading_password = $input_pwd;
        }else{
            $trading_password = md5($input_pwd);
        }


        if(  $trading_password != $this->trading_password  ){
            $m_log->addLog($this->member_id,$input_pwd,1,$remark);
            return new result(false,'Password error',null,errorCodesEnum::PASSWORD_ERROR);
        }

        // 正确的情况下不记录真实密码
        $input_pwd = '******';
        $m_log->addLog($this->member_id,$input_pwd,0,$remark);

        return new result(true,'success');

    }


    /** 签名方式验证交易密码
     * @param $check_sign
     * @param $self_sign
     * @param string $remark
     * @return result
     */
    public function checkTradingPasswordSign($check_sign,$self_sign,$remark='')
    {
        // 首先检查是否超过5次
        $m_log = new member_verify_trading_password_logModel();
        $times = $m_log->getDayErrorTimes($this->member_id);
        if( $times >= 5 ){
            return new result(false,'Password wrong too many times.',null,errorCodesEnum::PASSWORD_ERROR_MORE_TIMES);
        }

        if( !$this->trading_password ){
            return new result(false,'Not set trading password',null,errorCodesEnum::NOT_SET_TRADING_PASSWORD);
        }

        if(  $check_sign != $self_sign  ){
            $m_log->addLog($this->member_id,'******',1,$remark);
            return new result(false,'Password error',null,errorCodesEnum::PASSWORD_ERROR);
        }
        // 正确的情况下不记录真实密码
        $input_pwd = '******';
        $m_log->addLog($this->member_id,$input_pwd,0,$remark);

        return new result(true,'success');
    }


    /** 获取储蓄账户
     * @return passbookClass
     */
    public function getSavingsPassbook()
    {
        if( !$this->passbook ){
            $this->passbook = passbookClass::getSavingsPassbookOfMemberGUID($this->object_id);
        }
        return $this->passbook;
    }

    /**
     * 获取储蓄账户余额
     * @return mixed
     */
    public function getSavingsAccountBalance()
    {
        $passbook = $this->getSavingsPassbook();
        $cny_balance = $passbook->getAccountBalance();
        return $cny_balance;
    }


    /** 获取每月的贷款支出
     * @param string $currency
     */
    public function getMonthlyExpenseOfLoan($currency=currencyEnum::USD)
    {
        $contract_list = memberClass::getMemberAllLoanContractUnderExecuting($this->member_id);
        // 只计算分期的
        $r = new ormReader();
        $total_amount = 0;
        foreach( $contract_list as $contract_info ){

            if( interestTypeClass::isPeriodicRepayment($contract_info['repayment_type']) ){

                $sql = "select sum(amount-actual_payment_amount) total_amount,count(uid) total_period from loan_installment_scheme where contract_id='".$contract_info['uid']."' 
                and state>'".schemaStateTypeEnum::CANCEL."' and state<'".schemaStateTypeEnum::COMPLETE."' ";
                $row = $r->getRow($sql);
                if( $row ){
                    $period_amount = round($row['total_amount']/$row['total_period'],2);
                    // 换到月
                    $rt = loan_baseClass::interestRateConversion($period_amount,$contract_info['repayment_period'],interestRatePeriodEnum::MONTHLY);
                    if( $rt->STS ){
                        $period_amount = $rt->DATA;
                    }

                    // 切换货币
                    $exchange_rate = global_settingClass::getCurrencyRateBetween($contract_info['currency'],$currency);
                    $total_amount += $period_amount*$exchange_rate;

                }


            }

        }
        return round($total_amount,2);
    }


    /** 获取每月支出
     * @param string $currency
     */
    public function getMonthlyExpense($currency=currencyEnum::USD)
    {
        $total_amount = 0;

        $loan_amount = $this->getMonthlyExpenseOfLoan($currency);
        $total_amount += $loan_amount;

        return $total_amount;

    }





}