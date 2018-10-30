<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/30
 * Time: 10:16
 */
class loan_installment_schemeModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('loan_installment_scheme');
    }


    public function getAllNeedRepaymentSchema()
    {

        $r = new ormReader();
        $sql = "
select s.* from loan_installment_scheme s 
inner join loan_contract c on c.uid=s.contract_id 
left join loan_installment_scheme_script e on e.scheme_id = s.uid
where c.state>=".qstr(loanContractStateEnum::PENDING_DISBURSE)." and c.state<".qstr(loanContractStateEnum::COMPLETE)." 
 and s.state>=".qstr(schemaStateTypeEnum::CREATE)." and s.state<".qstr(schemaStateTypeEnum::COMPLETE)."
 and (e.next_execute_time is null 
  or (e.next_execute_time < ".qstr(time())." and e.is_suspended = 0)
 )
 and s.receivable_date < ".qstr(date('Y-m-d'));  // 24:00 后再执行

        $tasks = $r->getRows($sql);
        return $tasks;
    }

    public function scriptExecutingPrepare($schemeId) {
        $m = new loan_installment_scheme_scriptModel();
        $script_info = $m->getRow(array('scheme_id' => $schemeId));
        if (!$script_info) {
            $script_info = $m->newRow();
            $script_info->scheme_id = $schemeId;
            $script_info->is_suspended = 1;
            $script_info->last_execute_time = time();
            $script_info->last_error_code = 0;
            $script_info->finish_time = null;
            $rt = $script_info->insert();
            if (!$rt->STS) {
                return new result(false, $rt->MSG, null, errorCodesEnum::DB_ERROR);
            }
        } else {
            if ($script_info->is_suspended)
                return new result(false, 'Task is suspended', errorCodesEnum::INVALID_STATE);
            if ($script_info->next_execute_time > time())
                return new result(false, 'Cannot retry now', errorCodesEnum::INVALID_STATE);

            $script_info->is_suspended = 1;
            $script_info->last_execute_time = time();
            $script_info->last_error_code = 0;
            $script_info->finish_time = null;
            $rt = $script_info->update();
            if (!$rt->STS) {
                return new result(false, $rt->MSG, null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true);
    }

    public function scriptExecutingFinish($schemeId, $errorCode, $isSuspended=0, $nextExecuteTime=0) {
        $m = new loan_installment_scheme_scriptModel();
        $script_info = $m->getRow(array('scheme_id' => $schemeId));

        if (!$script_info) {
            return new result(false, "Task not found", null, errorCodesEnum::UNEXPECTED_DATA);
        }
        if ($nextExecuteTime<0)
            $nextExecuteTime = 0;

        $script_info->is_suspended = $isSuspended;
        $script_info->last_error_code = $errorCode;
        $script_info->next_execute_time = $nextExecuteTime ?: $script_info->last_execute_time + C("retry_interval_task_script_failed");
        $script_info->finish_time = date("Y-m-d H:i:s");
        $rt = $script_info->update();
        if (!$rt->STS) {
            return new result(false, $rt->MSG, null, errorCodesEnum::DB_ERROR);
        } else {
            return new result(true);
        }
    }

    public function getSchemaDetailById($id)
    {
        $sql = "select s.*,c.product_id,c.contract_sn,c.account_id,c.currency,c.repayment_type,c.repayment_period from loan_installment_scheme s inner join loan_contract c on c.uid=s.contract_id 
        where s.uid='$id'";
        return $this->reader->getRow($sql);
    }

    /** 获取计划的详细还款明细
     * @param $schema_id
     * @return ormCollection
     */
    public function getSchemaRepaymentDetail($schema_id)
    {
        $schema_id = intval($schema_id);
        $sql = "select * from loan_repayment where scheme_id='$schema_id'
        and state=".qstr(repaymentStateEnum::DONE)."
        order by uid desc ";
        return $this->reader->getRows($sql);
    }

    public function getSchemaDetailByIds($ids=array())
    {
        if( empty($ids) ){
            return null;
        }
        $ids = array_merge(array(),(array)$ids);
        $ids[] = 0;
        $sql = "select s.*,c.product_id,c.contract_sn,c.account_id,c.currency,c.repayment_type,c.repayment_period from loan_installment_scheme s inner join loan_contract c on c.uid=s.contract_id 
        where s.uid in (".implode(',',$ids).") ";
        return $this->reader->getRows($sql);
    }
}