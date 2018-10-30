<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/17
 * Time: 15:51
 */
class loanPrepaymentByPartnerClass extends prepaymentPayBaseClass
{

    protected $member_id;
    protected $account_handler_id;
    protected $account_handler_info;

    public function __construct($apply_id,$account_handler_id)
    {
        $apply_info = (new loan_prepayment_applyModel())->getRow($apply_id);
        if( !$apply_info ){
            throw new Exception('No apply info:'.$apply_id,errorCodesEnum::INVALID_PARAM);
        }

        $m_contract = new loan_contractModel();
        $contract_info = $m_contract->getRow($apply_info->contract_id);
        if( !$contract_info ){
            throw new Exception('No contract info:'.$apply_info->contract_id,errorCodesEnum::NO_CONTRACT);
        }

        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_info['uid']);
        if( !$member_info ){
            throw new Exception('Unknown loan client.',errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $handler_info = member_handlerClass::getHandlerInfoById($account_handler_id);
        if( !$handler_info ){
            throw new Exception('No handler info:'.$account_handler_id,errorCodesEnum::NO_ACCOUNT_HANDLER);
        }


        $this->apply_info = $apply_info;
        $this->contract_info = $contract_info;
        $this->member_id = $member_info['uid'];
        $this->member_info = $member_info;
        $this->account_handler_id = $account_handler_id;
        $this->account_handler_info = $handler_info;

    }

    public function getPayerInfo()
    {
       return $this->account_handler_info;
    }

    public function getHandlerInfo()
    {
        $handler_info = array();
        $handler_info['handler_id'] = 0;
        $handler_info['handler_name'] = 'System';
        return $handler_info;
    }

    public function execute()
    {

        $rt = $this->recalculateAmount();
        if( !$rt->STS ){
            return $rt;
        }

        $currency = $this->contract_info['currency'];
        $handler_name = member_handlerClass::getHandlerTypeName($this->account_handler_info['handler_type']);
        $total_amount = $this->apply_info['total_payable_amount'];
        $member_id = $this->member_info['uid'];
        $member_handler_id = $this->account_handler_id;

        $remark = "Loan prepayment by $handler_name :" . $this->contract_info['contract_sn'];


        $handlerClass = loan_handlerClass::getHandler($member_handler_id);
        $rt = $handlerClass->getHandlerMultiCurrencyBalance();
        if( !$rt->STS ){
            return $rt;
        }

        $handler_balance = $rt->DATA;
        $cal_handler_balance = array();
        // 过滤掉系统不支持的币种和没钱的币种
        foreach ((new currencyEnum())->toArray() as $currency) {
            if ($handler_balance[$currency] > 0) {
                $cal_handler_balance[$currency] = $handler_balance[$currency];
            }
        }

        $rt = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($total_amount, $currency, $cal_handler_balance);
        if( !$rt->STS ){
            return new result(false,'Balance not enough.',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
        }

        $deposit_amount = $rt->DATA['multi_currency'];

        $depositClass = new bizMemberDepositByPartnerClass(bizSceneEnum::APP_MEMBER);
        foreach ($deposit_amount as $c => $a) {

            $rt = $depositClass->bizStart($member_id,$a,$c,$member_handler_id,$remark);
            if (!$rt->STS) {
                return $rt;
            }
            $biz_id = $rt->DATA['biz_id'];

            $rt = $depositClass->bizSubmit($biz_id);
            if (!$rt->STS) {
                return $rt;
            }

        }

        $this->multi_currency = $deposit_amount;

        // 内部业务操作放在一个事务中
        $conn = ormYo::Conn();
        $conn->startTransaction();

        try{

            $rt = $this->handle();
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNKNOWN_ERROR);
        }



    }



}