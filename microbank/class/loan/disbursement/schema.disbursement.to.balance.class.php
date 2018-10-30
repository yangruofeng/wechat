<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/7
 * Time: 18:23
 */
class schemaDisbursementToBalanceClass
{
    protected $schema_info;
    protected $contract_info;
    protected $member_info;

    public function __construct($schema_id)
    {
        $m = new loan_disbursement_schemeModel();
        $schema = $m->getRow($schema_id);
        if( !$schema ){
            throw new Exception('No schema info:'.$schema_id,errorCodesEnum::NO_DATA);
        }

        $contract_info = (new loan_contractModel())->getRow($schema->contract_id);
        if( !$contract_info ){
            throw new Exception('No contract info:'.$schema->contract_id,errorCodesEnum::NO_CONTRACT);
        }

        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_info['uid']);
        if( !$member_info ){
            throw new Exception('No member info.',errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $this->schema_info = $schema;
        $this->contract_info = $contract_info;
        $this->member_info = $member_info;
    }


    public function execute()
    {

        $schema = $this->schema_info;
        $contract_info = $this->contract_info;

        if( !loan_contractClass::loanContractIsUnderExecuting($contract_info) ){
            return new result(false, 'Invalid contract state', null, errorCodesEnum::UN_MATCH_OPERATION);
        }

        // 检查计划状态
        if( $schema->state == schemaStateTypeEnum::COMPLETE || $schema->state == schemaStateTypeEnum::CANCEL ){
            return new result(true,'success');
        }

        // 检查计划时间
        if (strtotime($schema->disbursable_date) > time()) {
            return new result(false, 'The planned time has not been reached', null, errorCodesEnum::UN_MATCH_OPERATION);
        }

        if( $contract_info->state != loanContractStateEnum::PROCESSING ){
            $contract_info->state = loanContractStateEnum::PROCESSING;
            $contract_info->update_time = Now();
            $rt = $contract_info->update();
            if (!$rt->STS) {
                return new result(false, 'Update contract state failed', null, errorCodesEnum::DB_ERROR);
            }
        }


        $schema->state = schemaStateTypeEnum::GOING;
        $schema->execute_time = Now();
        $rt = $schema->update();
        if (!$rt->STS) {
            return new result(false, 'Update schema state failed', null, errorCodesEnum::DB_ERROR);
        }


        $handler_info = member_handlerClass::getHandlerInfoById($schema->account_handler_id);

        $disbursement_model = new loan_disbursementModel();
        $disbursement_log = $disbursement_model->newRow();
        $disbursement_log->scheme_id = $schema->uid;
        $disbursement_log->contract_id = $schema->contract_id;
        $disbursement_log->currency = $contract_info->currency;
        $disbursement_log->amount = $schema->amount;
        $disbursement_log->receiver_id = $handler_info?$handler_info['uid']:0;
        $disbursement_log->receiver_type = $handler_info?$handler_info['handler_type']:null;
        $disbursement_log->receiver_name = $handler_info?$handler_info['handler_name']:null;
        $disbursement_log->receiver_phone = $handler_info?$handler_info['handler_phone']:null;
        $disbursement_log->receiver_account = $handler_info?$handler_info['handler_account']:null;
        $disbursement_log->receiver_property = $handler_info?$handler_info['handler_property']:null;
        $disbursement_log->create_time = date("Y-m-d H:i:s");
        $disbursement_log->update_time = Now();
        $disbursement_log->branch_id = 0;
        $disbursement_log->teller_id = 0;
        $disbursement_log->teller_name = null;
        $disbursement_log->creator_id = 0;
        $disbursement_log->creator_name = 'System';
        $disbursement_log->gl_invoice_id = 0;

        $disbursement_log->state = disbursementStateEnum::GOING;
        $rt = $disbursement_log->insert();
        if (!$rt->STS) {
            return new result(false, 'Insert disbursement log failed', null, errorCodesEnum::DB_ERROR);
        }

        $ret = passbookWorkerClass::disburseLoan($schema['uid']);  // 转账业务处理

        if ($ret->STS) {
            $disbursement_log->state = disbursementStateEnum::DONE;
            $disbursement_log->update_time = Now();
            $rt = $disbursement_log->update();
            if (!$rt->STS) {
                return new result(false, 'Disburse succeed but update failed.', null, errorCodesEnum::DB_ERROR);
            }

            $schema->state = schemaStateTypeEnum::COMPLETE;
            $schema->execute_time = Now();
            $schema->done_time = date("Y-m-d H:i:s");
            $disbursement_log->update_time = Now();
            $rt = $schema->update();
            if (!$rt->STS) {
                return new result(false, 'Disburse succeed but update failed.', null, errorCodesEnum::DB_ERROR);
            }
            return new result(true);

        } else {

            $disbursement_log->state = disbursementStateEnum::FAILED;
            $disbursement_log->update_time = Now();
            $rt = $disbursement_log->update();
            if (!$rt->STS) {
                return new result(false, 'Disburse and update failed.', null, errorCodesEnum::DB_ERROR, $ret);
            }

            $schema->state = schemaStateTypeEnum::FAILURE;
            $schema->update_time = Now();
            $rt = $schema->update();
            if (!$rt->STS) {
                return new result(false, 'Disburse and update failed.', null, errorCodesEnum::DB_ERROR, $ret);
            }

            return $ret;

        }

    }

}