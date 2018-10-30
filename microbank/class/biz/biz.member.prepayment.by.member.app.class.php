<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/19
 * Time: 15:43
 */
class bizMemberPrepaymentByMemberAppClass extends bizBaseClass
{
    public function __construct()
    {

        $is_open = $this->checkBizOpen();
        if (!$is_open->STS) {
            throw new Exception('Function close!', errorCodesEnum::FUNCTION_CLOSED);
        }

        $this->scene_code = bizSceneEnum::APP_MEMBER;
        $this->biz_code = bizCodeEnum::MEMBER_PREPAYMENT_BY_MEMBER_APP;
        $this->bizModel = new biz_member_prepaymentModel();
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


    public function prepaymentApply($params)
    {
        $prepayment_type = intval($params['prepayment_type']);
        $member_id = $params['member_id'];
        $memberObj = new objectMemberClass($member_id);

        $rt = loan_contractClass::prepaymentApply($params);
        if( !$rt->STS ){
            return $rt;
        }
        $prepayment_request = $rt->DATA;

        $m_biz = $this->bizModel;
        $biz = $m_biz->newRow();
        $biz->scene_code = $this->scene_code;
        $biz->biz_code = $this->biz_code;
        $biz->apply_id = $prepayment_request['uid'];
        $biz->prepayment_type = $prepayment_type;
        $biz->member_id = $member_id;
        $biz->operator_id = 0;
        $biz->operator_name = 'System';
        $biz->branch_id = $memberObj->branch_id;
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $insert = $biz->insert();
        if (!$insert->STS) {
            return new result(false, 'Insert biz fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        $prepayment_request['biz_id'] = $biz->uid;

        return new result(true,'success',$prepayment_request);
    }

    public function applyPayment($params)
    {
        $m_biz = $this->bizModel;
        $apply_id = intval($params['request_id']);
        $repayment_way = $params['repayment_way'];
        $biz = $m_biz->getRow(array(
            'apply_id' => $apply_id
        ));

        $rt = loan_contractClass::prepaymentApplyAddPaymentInfo($params);
        if( !$rt->STS ){

            if( $biz ){
                if( $rt->CODE == errorCodesEnum::UNEXPECTED_DATA ){
                    $biz->state = bizStateEnum::PENDING_CHECK;
                }else{
                    $biz->state = bizStateEnum::FAIL;
                }
                $biz->repayment_way = $repayment_way;
                $biz->update_time = Now();
                $biz->update();
            }
            return $rt;
        }

        if( $biz ){
            $biz->repayment_way = $repayment_way;
            $biz->state = bizStateEnum::DONE;
            $biz->update_time = Now();
            $biz->update();
        }

        return $rt;

    }



    /**********  新方式的还款，实际就是提前还款 *************/

    public function getContractListByCategory($member_id,$member_category_id)
    {
        $m = new loan_contractModel();
        $list = $m->getExecutingContractByCategory($member_id,$member_category_id);
        if( count($list) > 0 ){

            $return = array();
            $m_contract = new loan_contractModel();
            foreach( $list as $contract ){


                $left_schemas = $m_contract->getContractUncompletedSchemas($contract['uid']);
                $min_date = loan_contractClass::getPrepaymentInterestMinTermDay($contract);
                $deadline_date = max(date('Y-m-d'),$min_date);

                $prepaymentClass = loanPrepaymentClass::getInstance($contract);
                $rt = $prepaymentClass->getPrepaymentDetailByAllPaid($deadline_date);
                if( !$rt->STS ){
                    //return $rt;
                    // 不支持提前还款的就不返回
                    continue;
                }
                $prepayment_detail = $rt->DATA;
                $return[] = array(
                    'contract_id' => $contract['uid'],
                    'contract_sn' => $contract['contract_sn'],
                    'currency' => $contract['currency'],
                    'deadline_date' => $prepayment_detail['cut_off_date'],
                    'prepayment_principal' => $prepayment_detail['prepayment_principal'],
                    'left_period' => count($left_schemas),
                    'total_prepayment_amount' => $prepayment_detail['total_prepayment_amount'],
                    'total_paid_principal' => $prepayment_detail['total_paid_principal'],
                    'total_paid_interest' => $prepayment_detail['total_paid_interest'],
                    'total_paid_operation_fee' => $prepayment_detail['total_paid_operation_fee'],
                    //'total_paid_penalty' => $prepayment_detail['total_paid_penalty'],
                    'start_date' => $contract['start_date'],
                    'end_date' => $contract['end_date'],
                );
            }
        }else{
            $return = array();
        }

        return new result(true,'success',array(
            'list' => $return
        ));
    }



    public function  selectContract($member_id,$contracts,$repayment_way)
    {
        $m_biz = $this->bizModel;
        if( empty($contracts) ){
            return new result(false,'No contracts selected.',null,errorCodesEnum::INVALID_PARAM);
        }

        $m_contract = new loan_contractModel();

        $memberObj = new objectMemberClass($member_id);

        $payment_detail = array();

        $currency_amount = array();
        // 计算所有合同的还款明细
        foreach( $contracts as $contract_id ){

            $contract = $m_contract->getRow($contract_id);
            if (!$contract) {
                return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
            }

            $min_date = loan_contractClass::getPrepaymentInterestMinTermDay($contract);
            $deadline_date = max(date('Y-m-d'),$min_date);

            $prepaymentClass = loanPrepaymentClass::getInstance($contract);
            $rt = $prepaymentClass->getPrepaymentDetailByAllPaid($deadline_date);
            if( !$rt->STS ){
                return $rt;
            }
            $prepayment_detail = $rt->DATA;
            $currency_amount[$contract['currency']] += $prepayment_detail['total_prepayment_amount'];
            $payment_detail[$contract_id] = array(
                'contract_info' => $contract,
                'prepayment_detail' => $prepayment_detail
            );
        }

        $biz = $m_biz->newRow();
        $biz->scene_code = $this->scene_code;
        $biz->biz_code = $this->biz_code;
        $biz->apply_id = 0;
        $biz->prepayment_type = prepaymentRequestTypeEnum::FULL_AMOUNT;
        $biz->repayment_way = $repayment_way;
        $biz->member_id = $member_id;
        $biz->operator_id = 0;
        $biz->operator_name = 'System';
        $biz->operate_remark = 'APP prepayment';
        $biz->branch_id = $memberObj->branch_id;
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $insert = $biz->insert();
        if (!$insert->STS) {
            return new result(false, 'Insert biz fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        $biz_id = $biz->uid;


        // 插入apply
        $m_apply = new loan_prepayment_applyModel();

        foreach( $payment_detail as $item ){

            $contract_info = $item['contract_info'];
            $prepaymentInfo = $item['prepayment_detail'];

            $request = $m_apply->newRow();
            $request->contract_id = $contract_info['uid'];
            $request->deadline_date = $prepaymentInfo['cut_off_date'];
            $request->payable_principal = $prepaymentInfo['total_paid_principal'];
            $request->payable_interest = $prepaymentInfo['total_paid_interest'];
            $request->payable_operation_fee = $prepaymentInfo['total_paid_operation_fee'];
            $request->payable_penalty =  $prepaymentInfo['total_paid_penalty'];
            $request->total_payable_amount = $prepaymentInfo['total_prepayment_amount'];
            $request->loss_interest = $prepaymentInfo['loss_interest']?:0;
            $request->loss_operation_fee = $prepaymentInfo['loss_operation_fee']?:0;
            $request->currency = $contract_info['currency'];
            $request->prepayment_type = prepaymentRequestTypeEnum::FULL_AMOUNT;
            $request->repay_period = 0;
            $request->apply_principal_amount = 0;
            $request->apply_time = Now();
            $request->biz_id = $biz_id;
            // 自动审批了 todo 不用配置的了？？
            $request->auditor_id = 0;
            $request->auditor_name = 'System';
            $request->audit_remark = 'Auto approved';
            $request->audit_time = Now();
            $request->state = prepaymentApplyStateEnum::APPROVED;
            $in = $request->insert();
            if (!$in->STS) {
                return new result(false, 'Insert apply fail:'.$in->MSG, null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true,'success',array(
            'biz_id' => $biz_id
        ));
    }




    public function confirmRepayment($biz_id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info:'.$biz_id,null,errorCodesEnum::INVALID_PARAM);
        }

        if( $biz->state == bizStateEnum::DONE ){
            return new result(true,'Done.');
        }

        if( $biz->biz_code != $this->biz_code ){
            return new result(false,'Un match biz info.',null,errorCodesEnum::INVALID_PARAM);
        }

        // 获得所有的apply
        $m_prepayment_apply = new loan_prepayment_applyModel();
        $apply_list = $m_prepayment_apply->select(array(
            'biz_id' => $biz_id
        ));
        if( count($apply_list) < 1 ){
            return new result(false,'No apply info',null,errorCodesEnum::NO_DATA);
        }

        foreach( $apply_list as $apply ){
            // todo 暂时只处理余额的方式
            $rt = loan_contractClass::prepaymentApplyAddPaymentInfo(array(
                'request_id' => $apply['uid'],
                'repayment_way' => $biz->repayment_way,
                'member_id' => $biz->member_id
            ));
            if( !$rt->STS ){
                return $rt;
            }
        }

        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Update biz fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');

    }

}