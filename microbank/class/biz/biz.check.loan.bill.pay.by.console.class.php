<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/12
 * Time: 10:54
 */
class bizCheckLoanBillPayByConsoleClass extends bizBaseClass
{
    public function __construct()
    {
        $is_open = $this->checkBizOpen();
        if (!$is_open->STS) {
            throw new Exception('Function close!');
        }

        $this->scene_code = bizSceneEnum::BACK_OFFICE;
        $this->biz_code = bizCodeEnum::CHECK_LOAN_BILL_PAY_BY_CONSOLE;
        $this->bizModel = new biz_loan_billpay_checkModel();
    }

    public function checkBizOpen()
    {
        return new result(true);
    }

    public function getBizDetailById($id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->find(array(
            'uid' => $id
        ));
        return $biz;
    }


    public  function getCheckList($pageNumber, $pageSize, $filter = array())
    {
        return $this->bizModel->getBillPayCheckList($pageNumber, $pageSize, $filter);
    }


    public static function getLoanSchemasDetailByBillCode($bill_code)
    {
        $list = member_loan_schemaClass::getMemberLoanSchemesByBillCode($bill_code);

        if( empty($list) ){
            return array();
        }
        // 优先取出应该还的
        $today = date('Y-m-d 23:59:59');
        $payment_list = array();
        foreach( $list as $v ){
            if( $v['receivable_date'] <= $today ){
                $payment_list[] = $v;
            }
        }
        if( empty($payment_list) ){
            // 如果没有逾期的，取最前五期就行了
            $num = 1;
            foreach( $list as $v ){

                if( $num <= 5 ){
                    $payment_list[] = $v;
                }else{
                    break;
                }
                $num++;
            }
        }

        return $payment_list;

    }


    public function execute($user_id,$bank_id,$bill_code,$loan_schema_arr,$deposit_amount,$remark)
    {
        $deposit_amount = round($deposit_amount,2);
        if( $deposit_amount <= 0 ){
            return new result(false,'Invalid amount:'.$deposit_amount,null,errorCodesEnum::INVALID_AMOUNT);
        }

        if( empty($loan_schema_arr) ){
            return new result(false,'Empty loan schema.',null,errorCodesEnum::INVALID_PARAM);
        }

        if( !is_array($loan_schema_arr) ){
            return new result(false,'Invalid schema format.',null,errorCodesEnum::INVALID_PARAM);
        }

        $userObj = new objectUserClass($user_id);
        $chk = $userObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }
        $bank_info = (new site_bankModel())->find(array(
            'uid' => $bank_id
        ));
        if( !$bank_info ){
            return new result(false,'No bank info:'.$bank_id,null,errorCodesEnum::NO_DATA);
        }

        $schema_list = (new loan_installment_schemeModel())->getSchemaDetailByIds($loan_schema_arr);
        $schema_info = current($schema_list);
        $contract_id = $schema_info['contract_id'];
        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_id);


        $m_biz = $this->bizModel;
        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->bill_code = $bill_code;
        $biz->member_id = $member_info['uid'];
        $biz->bank_id = $bank_info['uid'];
        $biz->bank_name = $bank_info['bank_name'];
        $biz->bank_account_no = $bank_info['bank_account_no'];
        $biz->currency = $bank_info['currency'];
        $biz->amount = $deposit_amount;
        $biz->remark = $remark;
        $biz->pay_time = Now();
        $biz->state = bizStateEnum::CREATE;
        $biz->operator_id = $userObj->user_id;
        $biz->operator_name = $userObj->user_name;
        $biz->create_time = Now();
        $biz->branch_id = $userObj->branch_id;

        $insert = $biz->insert();
        if( !$insert->STS ){
            return $insert;
        }

        $biz_id = $biz->uid;

        // 插入详细
        $m_detail = new biz_loan_billpay_check_detailModel();
        foreach( $schema_list as $v ){
            $row = $m_detail->newRow();
            $row->biz_id = $biz_id;
            $row->schema_id = $v['uid'];
            $row->contract_id = $v['contract_id'];
            $insert = $row->insert();
            if( !$insert->STS ){
                return $insert;
            }
        }

        // 执行还款
        $rt = loanRepaymentWorkerClass::schemasRepaymentByBank($userObj->user_id,
            $member_info['uid'],$loan_schema_arr,$bank_id,$deposit_amount,$bank_info['currency']);
        if( !$rt->STS ){
            $biz->state = bizStateEnum::FAIL;
            $biz->update_time = Now();
            $up = $biz->update();
            return $rt;
        }

        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return $up;
        }
        return new result(true,'success',array(
            'biz_id' => $biz_id
        ));

    }
}