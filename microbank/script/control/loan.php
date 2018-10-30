<?php

class loanControl {
    public function __construct()
    {
    }


    public function disbursementTestOp()
    {
        $r = new ormReader();
        $sql = "select s.* from loan_disbursement_scheme s left join loan_contract c on c.uid=s.contract_id 
          where c.state>='".loanContractStateEnum::PENDING_DISBURSE."' and c.state<'".loanContractStateEnum::COMPLETE."' 
          and s.state in ('".schemaStateTypeEnum::CREATE."','".schemaStateTypeEnum::FAILURE."') 
           and s.disbursable_date <= '".date('Y-m-d H:i:s')."' order by s.uid desc ";

        $schema = $r->getRow($sql);

        if( !$schema ){
            die('No pending disbursement schema.');
        }

        print_r($schema);

        $ret = array(
            'succeed' => 0,
            'failed' => 0,
            'skipped' => 0
        );
        $rt = loanDisbursementWorkerClass::schemaDisburse($schema['uid']);
        if (!$rt->STS) {
            logger::record("exec_disbursement_schema_script", $rt->MSG . "\n" . my_json_encode($schema) );
            $ret['failed'] += 1;
        } else {
            $ret['succeed'] += 1;
        }
        print_r($rt);
    }


    public function repaymentTestOp()
    {
        $tasks = (new loan_installment_schemeModel())->getAllNeedRepaymentSchema();
        $schema = current($tasks);
        $ret = array(
            'succeed' => 0,
            'failed' => 0,
            'skipped' => 0
        );
        print_r($schema);

        $schema_id = $schema['uid'];
        $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($schema_id);
        $class_instance = new schemaRepaymentByBalanceClass($schema_id,$penalty);
        $class_instance->is_script_execute = 1;
        $rt = $class_instance->repaymentExecute();
        if (!$rt->STS) {

            $ret['failed'] += 1;
        } else {

            $ret['succeed'] += 1;
        }

        return new result(true, null, $ret);
    }

    public function exec_disbursement_schemaOp() {

        $tasks = (new loan_disbursement_schemeModel())->getAllAutoDisbursementSchemaList();
        $ret = array(
            'succeed' => 0,
            'failed' => 0,
            'skipped' => 0
        );
        $m_script = new loan_disburse_scheme_script_taskModel();
        foreach ($tasks as $schema) {

            try{

                $rt = $m_script->scriptExecutingPrepare($schema['uid']);
                if( !$rt->STS ){
                    logger::record("exec_disbursement_schema_script", json_encode($rt) . "\n" .my_json_encode($schema));
                    $ret['failed'] += 1;
                    continue;
                }

                $rt = loanDisbursementWorkerClass::schemaDisburse($schema['uid']);
                if( !$rt->STS ){
                    logger::record("exec_disbursement_schema_script", json_encode($rt) . "\n" . my_json_encode($schema) );
                    $ret['failed'] += 1;

                    // 如果失败了，就5分钟后再试
                    $m_script->scriptExecutingFinish($schema['uid'],$rt->CODE,json_encode($rt),
                        0,time()+300);
                }
                $ret['succeed'] += 1;

                // 成功就不需要处理了

            }catch( Exception $e ){
                logger::record("exec_disbursement_schema_script", $e->getMessage() . "\n" . my_json_encode($schema) );
                $ret['failed'] += 1;
                continue;
            }


        }

        return new result(true, null, $ret);

    }

    public function schemaRepaymentExecuteOp()
    {
        $m = new loan_installment_schemeModel();
        $tasks = $m->getAllNeedRepaymentSchema();
        $ret = array(
            'succeed' => 0,
            'failed' => 0,
            'skipped' => 0
        );

        foreach ($tasks as $schema) {

            try{
                $schema_id = $schema['uid'];
                $rt = $m->scriptExecutingPrepare($schema_id);
                if (!$rt->STS) {
                    logger::record("exec_repayment_schema_script", json_encode($rt) . "\n" .my_json_encode($schema));
                    $ret['failed'] += 1;
                    continue;
                }

                $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($schema_id);

                $class_instance = new schemaRepaymentByBalanceClass($schema_id,$penalty);
                $class_instance->is_script_execute = true;

                $rt = $class_instance->repaymentExecute();
                if( !$rt->STS ){
                    logger::record("exec_repayment_schema_script", json_encode($rt) . "\n" .my_json_encode($schema));
                    $ret['failed'] += 1;

                    switch ($rt->CODE) {
                        case errorCodesEnum::BALANCE_NOT_ENOUGH:
                            // 余额不足的继续脚本执行
                            // 指定下次重试时间的，可以设置第三个参数$nextExecuteTime
                            $m->scriptExecutingFinish($schema_id, $rt->CODE);
                            break;
                        default:
                            // 其他情况暂停脚本执行
                            $m->scriptExecutingFinish($schema_id, $rt->CODE, 1);
                            break;
                    };
                }else {
                    $ret['succeed'] += 1;

                    // 成功的情况，可能没有还完，至少12小时再尝试扣一次
                    $m->scriptExecutingFinish($schema_id, 200, 0, 12 * 3600);
                }

            }catch( Exception $e ){
                $ret['failed'] += 1;
                logger::record("exec_repayment_schema_script", $e->getMessage() . "\n" .my_json_encode($schema));
                continue;
            }



        }


        return new result(true, null, $ret);
    }



}