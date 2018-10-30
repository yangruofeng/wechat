<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/30
 * Time: 13:08
 */

class loan_disbursement_schemeModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('loan_disbursement_scheme');
    }

    /** 获取计划的详细放款明细
     * @param $schema_id
     * @return ormCollection
     */
    public function getSchemaDisbursementDetail($schema_id)
    {
        $schema_id = intval($schema_id);
        $sql = "select * from loan_disbursement where scheme_id='$schema_id'  and state='".disbursementStateEnum::DONE."'
        order by uid desc ";
        return $this->reader->getRows($sql);
    }


    /** 获取自动放款的所有计划
     * @return ormCollection
     */
    public function getAllAutoDisbursementSchemaList()
    {
        $r = new ormReader();
        $sql = "select s.* from loan_disbursement_scheme s left join loan_contract c on c.uid=s.contract_id 
          where c.state>='".loanContractStateEnum::PENDING_DISBURSE."' and c.state<'".loanContractStateEnum::COMPLETE."' 
          and s.state in ('".schemaStateTypeEnum::CREATE."','".schemaStateTypeEnum::FAILURE."') 
          and s.state!=".qstr(schemaStateTypeEnum::PENDING_MANUAL_EXECUTE)."
           and s.disbursable_date <= '".date('Y-m-d H:i:s')."' ";

        $tasks = $r->getRows($sql);
        return $tasks;
    }

    /** 获取合同下需要手工执行的计划
     * @param $contract_id
     * @return ormCollection
     */
    public function getPendingManualDisburseSchemaOfContract($contract_id)
    {
        $sql = "select * from loan_disbursement_scheme where 
          contract_id=".qstr($contract_id)." and state=".qstr(schemaStateTypeEnum::PENDING_MANUAL_EXECUTE).
        "  and disbursable_date <= '".date('Y-m-d H:i:s')."'  ";
        return $this->reader->getRows($sql);
    }

}