<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/16
 * Time: 17:27
 */
class bizMemberLoanRepaymentByMemberAppClass extends bizBaseClass
{

    public function __construct()
    {

        $is_open = $this->checkBizOpen();
        if (!$is_open->STS) {
            throw new Exception('Function close!');
        }

        $this->scene_code = bizSceneEnum::APP_MEMBER;
        $this->biz_code = bizCodeEnum::MEMBER_LOAN_REPAYMENT_BY_MEMBER_APP;
        $this->bizModel = new biz_member_loan_repaymentModel();
    }

    public function checkBizOpen()
    {
        if( global_settingClass::isForbiddenReturnLoan() ){
            return new result(false,'Close loan repayment.',null,errorCodesEnum::FUNCTION_CLOSED);
        }
        return new result(true);
    }

    public function getBizDetailById($id)
    {
        return $this->bizModel->find(array(
            'uid' => $id
        ));
    }


    public function checkMemberTradingPassword($params)
    {
        $member_id = $params['member_id'];
        $sign = $params['sign'];
        $memberObj = new objectMemberClass($member_id);
        $self_sign = md5($member_id . $memberObj->trading_password);
        $chk = $memberObj->checkTradingPasswordSign($sign, $self_sign, 'Loan repayment by member app');
        return $chk;
    }


    public function bizStart($params)
    {
        $m_biz = $this->bizModel;

        $member_id = $params['member_id'];
        $schema_ids = trim($params['schema_ids']);
        $schema_list = explode(',',$schema_ids);

        if (!$schema_ids) {
            return new result(false, 'No schema.', null, errorCodesEnum::INVALID_PARAM);
        }


        $currency_total = array();
        $m_schema = new loan_installment_schemeModel();
        $schema_list_info = $m_schema->getSchemaDetailByIds($schema_list);
        foreach ($schema_list_info as $schema_info) {
            $rt = loan_contractClass::getContractInterestInfo($schema_info['contract_id']);
            if (!$rt->STS) {
                continue;
            }
            $interest_info = $rt->DATA;
            $interestClass = interestTypeClass::getInstance($schema_info['repayment_type'], $schema_info['repayment_period']);
            $schema_info = $interestClass->calculateRepaymentInterestOfSchema($schema_info, $interest_info);
            $total_amount = $schema_info['amount'] - $schema_info['actual_payment_amount'];

            if ($currency_total[$schema_info['currency']]) {
                $currency_total[$schema_info['currency']] += $total_amount;
            } else {
                $currency_total[$schema_info['currency']] = $total_amount;
            }

        }

        $repayment_way = $params['repayment_way'];

        $memberObj = new objectMemberClass($member_id);

        // 插入业务表
        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->repayment_type = 0;
        $biz->repayment_way = $repayment_way;
        $biz->request_id = 0;
        $biz->member_id = $member_id;
        $biz->branch_id = $memberObj->branch_id;
        $biz->create_time = Now();
        $insert = $biz->insert();
        if (!$insert->STS) {
            return new result(false, 'Insert biz fail.', null, errorCodesEnum::DB_ERROR);
        }

        $biz_id = $biz->uid;

        // 插入金额详细
        // 插入应还的金额统计
        $sql = "insert into biz_member_loan_repayment_detail(biz_id,currency,amount,amount_type) values ";
        $sql_arr = array();
        foreach ($currency_total as $c => $a) {
            $sql_arr[] = "('$biz_id','$c','$a','0')";
        }
        $sql .= implode(',', $sql_arr);
        $insert = $this->bizModel->conn->execute($sql);
        if (!$insert->STS) {
            return new result(false, 'Insert currency amount fail.', null, errorCodesEnum::DB_ERROR);
        }

        // todo 实际偿还的金额详细(只有余额方式)
        $sql = "insert into biz_member_loan_repayment_detail(biz_id,currency,amount,amount_type) values ";
        $sql_arr = array();
        foreach ($currency_total as $c => $a) {
            $sql_arr[] = "('$biz_id','$c','$a','1')";
        }
        $sql .= implode(',', $sql_arr);
        $insert = $this->bizModel->conn->execute($sql);
        if (!$insert->STS) {
            return new result(false, 'Insert currency amount fail.', null, errorCodesEnum::DB_ERROR);
        }


        // 插入详细的计划列表信息
        $schema_list = explode(',',$schema_ids);
        $rt = loan_contractClass::insertSchemaRepaymentApplyInfo($schema_list,$repayment_way,$member_id,$params);
        if( !$rt->STS ){
            return $rt;
        }

        $request = $rt->DATA;

        $biz->request_id = $request['uid'];
        $biz->update_time = Now();
        $up = $biz->update();
        if (!$up->STS) {
            return new result(false, 'Update biz fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        // 都成功后，如果是自动扣款的，马上执行
        if ($repayment_way == repaymentWayEnum::AUTO_DEDUCTION || $repayment_way == repaymentWayEnum::PASSBOOK) {
            $handler_info = array(
                'handler_id' => 0,
                'handler_name' => 'System',
                'handle_remark' => 'System handle',
                'handle_time' => Now()
            );
            $rt = loan_contractClass::requestRepaymentConfirmReceived($request->uid, array(), $handler_info);
            if (!$rt->STS) {

                $biz->state = bizStateEnum::FAIL;
                $biz->update_time = Now();
                $biz->update();
                return $rt;
            }

            // 完成了
            $biz->state = bizStateEnum::DONE;
            $biz->update_time = Now();
            $up = $biz->update();
            if (!$up->STS) {
                return new result(false, 'Update biz fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true, 'success',$request);

    }

}