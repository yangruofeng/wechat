<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/2
 * Time: 14:12
 */
class schemaRepaymentByBalanceClass extends schemaNormalRepaymentClass
{

    protected $schema_id;
    protected $exchange_rate;
    protected $is_deduct_from_ace;
    public $is_script_execute=0;


    public function __construct($schema_id,$penalty,$is_deduct_from_ace=0)
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

        $this->schema_id = $schema_id;
        $this->penalty = $penalty;
        $this->exchange_rate = 1;

        $this->is_schema_amount_repayment = false;  // 多样扣款
        $this->is_deduct_from_ace = $is_deduct_from_ace;

        $this->schema_info = $scheme_info;
        $this->contract_info = $contract_info;
        $this->member_info = $member_info;

    }


    public function getPayerInfo()
    {
        return member_handlerClass::getMemberDefaultPassbookHandlerInfo($this->member_info['uid']);
    }

    public function getCreatorInfo()
    {
        return array(
            'creator_id' => 0,
            'creator_name' => 'System',
            'teller_id' => 0,
            'teller_name' => null,
            'branch_id' =>0
        );
    }





    /** 正常触发执行
     * @return result
     */
    public function normalExecute()
    {
        $scheme_info = $this->schema_info;
        $schema_amount = $scheme_info['amount'] - $scheme_info['actual_payment_amount'];
        $currency = $this->contract_info['currency'];

        // 自动分配有账户的钱
        $member_id = $this->member_info['uid'];
        $memberObj = new objectMemberClass($member_id);
        $member_balance = $memberObj->getSavingsAccountBalance();
        $paid_currency_amount = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($schema_amount,$currency,$member_balance);

        $this->multi_currency = $paid_currency_amount->DATA['multi_currency'];
        $rt = $this->execute();
        return $rt;

    }


    /** 执行还款
     * @return result
     */
    public function repaymentExecute()
    {
        $scheme_info = $this->schema_info;
        $schema_amount = $scheme_info['amount'] - $scheme_info['actual_payment_amount'];
        $currency = $this->contract_info['currency'];

        // 自动分配有账户的钱
        $member_id = $this->member_info['uid'];
        $memberObj = new objectMemberClass($this->member_info['uid']);
        $member_balance = $memberObj->getSavingsAccountBalance();
        $paid_currency_amount = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($schema_amount,$currency,$member_balance);
        $this->multi_currency = $paid_currency_amount->DATA['multi_currency'];

        if( !$paid_currency_amount->STS ){

            if( $this->is_deduct_from_ace ){

                $left_amount = $paid_currency_amount->DATA['left_amount'];
                // 获取列表
                $ace_handler = member_handlerClass::getMemberDefaultAceHandlerInfo($this->member_info['uid']);
                if( $ace_handler ){
                    $handlerClass = loan_handlerClass::getHandler($ace_handler['uid']);
                    if( $handlerClass ) {
                        // 先查询余额
                        $rt = $handlerClass->getHandlerMultiCurrencyBalance();
                        if (!$rt->STS) {
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

                        $remark = 'Loan repayment:' . $this->contract_info['contract_sn'];

                        // 有多少扣多少
                        $rt = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($left_amount, $currency, $cal_handler_balance);
                        $deposit_amount = $rt->DATA['multi_currency'];

                        if ($this->is_script_execute) {
                            $depositClass = new bizMemberDepositByPartnerClass(bizSceneEnum::SCRIPT);
                        } else {
                            $depositClass = new bizMemberDepositByPartnerClass(bizSceneEnum::APP_MEMBER);
                        }

                        foreach ($deposit_amount as $c => $a) {

                            $rt = $depositClass->bizStart($member_id,$a,$c, $ace_handler['uid'],$remark);
                            if (!$rt->STS) {
                                return $rt;
                            }
                            $biz_id = $rt->DATA['biz_id'];

                            $rt = $depositClass->bizSubmit($biz_id);
                            if (!$rt->STS) {
                                return $rt;
                            }

                        }

                        // 存完后重新计算扣款
                        $new_member_balance = $memberObj->getSavingsAccountBalance();
                        $paid_currency_amount = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($schema_amount,$currency,$new_member_balance);
                        $this->multi_currency = $paid_currency_amount->DATA['multi_currency'];

                    }
                }
            }
        }

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