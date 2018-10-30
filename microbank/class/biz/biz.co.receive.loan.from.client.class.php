<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/20
 * Time: 16:46
 */
class bizCoReceiveLoanFromClientClass extends bizBaseClass
{

    public function __construct()
    {

        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!');
        }
        $this->scene_code = bizSceneEnum::APP_CO;
        $this->biz_code = bizCodeEnum::CO_RECEIVE_LOAN_FROM_MEMBER;
        $this->bizModel = new biz_co_receive_loan_from_memberModel();
    }

    public function getBizDetailById($id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->find(array(
            'uid' => $id
        ));
        return $biz;
    }

    public function checkBizOpen()
    {
        return new result(true);
    }


    public function execute($contract_id,$user_id,$user_trading_password,$amount,$currency,$remark=null)
    {
        $amount = round($amount,2);
        if( $amount <= 0 ){
            return new result(false,'Invalid Amount.',null,errorCodesEnum::INVALID_AMOUNT);
        }
        $userObj = new objectUserClass($user_id);
        $chk = $userObj->checkTradingPassword($user_trading_password);
        if( !$chk->STS ){
            return $chk;
        }

        $m_contract = new loan_contractModel();
        $contract_info = $m_contract->getRow($contract_id);
        if( !$contract_info ){
            return new result(false,'No contract info.',null,errorCodesEnum::NO_CONTRACT);
        }

        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_id);
        if( !$member_info ){
            return new result(false,'No member info.',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $member_id = intval($member_info['uid']);

        // 合同是否有未还清计划
        $list = $m_contract->getContractUncompletedSchemas($contract_id);
        if( count($list) < 1 ){
            return new result(false,'Contract has no payable schema.',null,errorCodesEnum::NO_DATA);
        }

        $biz = $this->bizModel->newRow();
        $biz->scene_code = $this->scene_code;
        $biz->biz_code = $this->biz_code;
        $biz->member_id = $member_id;
        $biz->loan_contract_id = $contract_id;
        $biz->operator_id = $userObj->user_id;
        $biz->operator_name = $userObj->user_name;
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->remark = $remark;
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $biz->update_time = Now();
        $biz->branch_id = $userObj->branch_id;
        $insert = $biz->insert();
        if( !$insert->STS ){
            return new result(false,'Insert biz fail.',null,errorCodesEnum::DB_ERROR);
        }

        // 获取计划
        $rt = loan_contractClass::getRepaymentSchemaByAmount($contract_id,$amount,$currency);
        if( !$rt->STS ){
            return $rt;
        }
        $paid_schemas = $rt->DATA['repayment_schema'];
        $contract_payable_amount = $rt->DATA['contract_payable_amount'];
        $contract_currency = $contract_info['currency'];

        if( count($paid_schemas) < 1 ){
            return new result(false,'Amount not enough.',null,errorCodesEnum::AMOUNT_TOO_LITTLE);
        }

        $schema_ids = array();
        foreach( $paid_schemas as $v ){
            $schema_ids = $v['uid'];
        }

        // 全部存入合同货币
        $exchange_to_currency = array(
            $contract_currency => -1
        );
        $rt = loanRepaymentWorkerClass::schemasRepaymentByCash($member_id,$userObj->user_id,$schema_ids,$amount,$currency,null,$exchange_to_currency);
        if( !$rt->STS ){
           return $rt;
        }

        // 一次性操作了

        // 先将钱存入账户(存入需要扣款的账户)
        /*$mark = "Loan repayment to credit officer: ".$userObj->user_name;
        $rt = passbookWorkerClass::memberDepositByCash($member_id,$userObj->user_id,$amount,$currency,$mark,null,array(
            $contract_currency => $contract_payable_amount
        ));
        if( !$rt->STS ){
            $biz->state = bizStateEnum::FAIL;
            $biz->update_time = Now();
            $biz->update();
            return $rt;
        }

        foreach( $paid_schemas as $v ) {

            $schema_id = $v['uid'];
            $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($schema_id);
            $rt = (new schemaRepaymentByCashClass($user_id, $schema_id, $penalty, $amount, $currency))->repaymentExecute();

            if (!$rt->STS) {
                return $rt;
            }

        }*/


        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Update biz info fail.',null,errorCodesEnum::DB_ERROR);
        }

        $biz->biz_id = $biz->uid;

        // 完成
        return new result(true,'success',$biz);


    }

}