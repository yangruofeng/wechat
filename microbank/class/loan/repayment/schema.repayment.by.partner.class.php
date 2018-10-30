<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/2
 * Time: 13:26
 */
class schemaRepaymentByPartnerClass extends schemaNormalRepaymentClass
{

    protected  $schema_id;
    protected  $exchange_rate;
    protected  $member_handler_id;
    protected  $member_handler_info;

    public function __construct($schema_id,$penalty,$member_handler_id)
    {
        $scheme_info = (new loan_installment_schemeModel())->getRow($schema_id);
        if( !$scheme_info ){
            throw new Exception('Unknown schema id: '.$this->schema_id,errorCodesEnum::NO_DATA);
        }

        $contract_info = (new loan_contractModel())->getRow($scheme_info->contract_id);
        if( !$contract_info ){
            throw new Exception('No contract info of id:'.$scheme_info->contract_id,errorCodesEnum::NO_CONTRACT);
        }

        $member_info = loan_contractClass::getLoanContractMemberInfo($scheme_info->contract_id);
        if( !$member_info ){
            throw new Exception('No loan member info.',errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $handler_info = member_handlerClass::getHandlerInfoById($member_handler_id);
        if( !$handler_info ){
            throw new Exception('No handler info:'.$member_handler_id,errorCodesEnum::NO_ACCOUNT_HANDLER);
        }
        $this->schema_id = $schema_id;
        $this->exchange_rate = 1;
        $this->penalty = $penalty;

        $this->is_schema_amount_repayment = true;


        $this->member_handler_id = $member_handler_id;
        $this->member_handler_info = $handler_info;
        $this->schema_info = $scheme_info;
        $this->contract_info = $contract_info;
        $this->member_info = $member_info;

    }



    public function getPayerInfo()
    {
        return $this->member_handler_info;
    }

    public function getCreatorInfo()
    {
        return array(
            'creator_id' => 0,
            'creator_name' => 'System',
            'teller_id' => 0,
            'teller_name' => null,
            'branch_id' => 0
        );
    }


    public function repaymentExecute()
    {
        $scheme_info = $this->schema_info;
        $member_info = $this->member_info;
        $member_handler_id = $this->member_handler_info['uid'];
        $member_id = $member_info['uid'];

        $schema_amount = $scheme_info['amount'] - $scheme_info['actual_payment_amount'];
        $currency = $this->contract_info['currency'];

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

        $rt = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($schema_amount, $currency, $cal_handler_balance);
        $remark = 'Loan repayment:' . $this->contract_info['contract_sn'];
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

            $rt = $this->execute();
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