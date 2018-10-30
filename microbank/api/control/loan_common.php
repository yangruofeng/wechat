<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/13
 * Time: 10:17
 */
class loan_commonControl extends bank_apiControl
{

    public function newCalculatorOp()
    {

        $params = array_merge(array(), $_GET, $_POST);
        $loan_amount = $params['loan_amount'];
        $loan_period = $params['loan_period'];
        $loan_period_unit = $params['loan_period_unit'];
        $repayment_type = $params['repayment_type'];
        $repayment_period = $params['repayment_period'];
        if ($loan_amount < 1) {
            return new result(false, 'Invalid amount', null, errorCodesEnum::INVALID_PARAM);
        }
        if ($loan_period <= 0) {
            return new result(false, 'Invalid loan period', null, errorCodesEnum::INVALID_PARAM);
        }
        $re = (new loan_baseClass())->calculator($loan_amount, $loan_period, $loan_period_unit, $repayment_type, $repayment_period);
        return $re;
    }

    public function contractDetailOp()
    {
        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);
        $contract_id = $params['contract_id'];
        $re = loan_contractClass::getLoanContractDetailInfo($contract_id);
        return $re;
    }

    public function appLoanApplyOp()
    {
        // 添加到咨询表

        $params = array_merge(array(), $_GET, $_POST);
        $member_id = intval($params['member_id']);

        $amount = round($params['amount'], 2);
        $propose = $params['loan_propose'];
        $loan_time = intval($params['loan_time']);
        $loan_time_unit = $params['loan_time_unit'];
        $mortgage = $params['mortgage'];  // 多个用,隔开
        $currency = $params['currency'] ?: currencyEnum::USD;

        if ($amount <= 0) {
            return new result(false, 'Invalid amount', null, errorCodesEnum::INVALID_PARAM);
        }

        $branch_id = null;

        $operator_id = 0;
        $operator_name = null;
        $state = loanConsultStateEnum::CREATE;

        // 登陆会员
        if ($member_id) {
            $m_member = new memberModel();
            $member = $m_member->getRow($member_id);
            if (!$member) {
                return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
            }

            $branch_id = $member->branch_id;

            $applicant_name = $member->display_name ?: ($member->login_code ?: 'Unknown');
            $applicant_address = null;  // member 地址
            $contact_phone = $member->phone_id;

            $m_member_follow_officer = M('member_follow_officer');
            $operator = $m_member_follow_officer->find(array('member_id' => $member_id, 'officer_type' => 1, 'is_active' => 1));
            if ($operator) {
                $operator_id = $operator['officer_id'];
                $operator_name = $operator['officer_name'];
                $state = loanConsultStateEnum::LOCKED;
            }

        } else {
            // 没登陆
            $applicant_name = $params['name'];
            $applicant_address = $params['address'];
            $country_code = $params['country_code'];
            $phone = $params['phone'];
            $sms_id = $params['sms_id'];
            $sms_code = $params['sms_code'];
            if (!$applicant_name || !$applicant_address || !$country_code || !$phone || !$sms_id || !$sms_code) {
                return new result(false, 'Lack param', null, errorCodesEnum::DATA_LACK);
            }
            $phone_arr = tools::getFormatPhone($country_code, $phone);
            $contact_phone = $phone_arr['contact_phone'];
            if (!isPhoneNumber($contact_phone)) {
                return new result(false, 'Invalid phone', null, errorCodesEnum::INVALID_PHONE_NUMBER);
            }
            // 验证码
            $m_sms = new phone_verify_codeModel();
            $row = $m_sms->getRow(array(
                'uid' => $sms_id,
                'verify_code' => $sms_code
            ));
            if (!$row) {
                return new result(false, 'Code error', null, errorCodesEnum::SMS_CODE_ERROR);
            }

        }

        $m_apply = new loan_consultModel();

        $apply = $m_apply->newRow();
        $apply->member_id = $member_id;
        $apply->applicant_name = $applicant_name;
        $apply->address = $applicant_address;
        $apply->apply_amount = $amount;
        $apply->currency = $currency;
        $apply->loan_time = $loan_time;
        $apply->loan_time_unit = $loan_time_unit;
        $apply->mortgage = $mortgage;
        $apply->loan_purpose = $propose;
        $apply->contact_phone = $contact_phone;
        $apply->apply_time = Now();
        $apply->request_source = loanConsultSourceEnum::MEMBER_APP;
        if ($branch_id) {
            $apply->branch_id = $branch_id;
        }
        $apply->creator_id = 0;
        $apply->creator_name = 'System';
        $apply->create_time = Now();
        $apply->operator_id = intval($operator_id);
        $apply->operator_name = $operator_name ?: '';
        $apply->state = intval($state);
        $insert = $apply->insert();
        if (!$insert->STS) {
            return new result(false, 'Apply fail', null, errorCodesEnum::DB_ERROR);
        }
        if($operator_id){
            $task_msg="Get New Consultation From Member-App 【".$contact_phone."】 At ".Now();
            $ret_task=taskControllerClass::handleNewTask($apply->uid,userTaskTypeEnum::OPERATOR_MY_CONSULT,$operator_id,objGuidTypeEnum::UM_USER,0,objGuidTypeEnum::SYSTEM,$task_msg);
        }

        return new result(true, 'success', $apply);

    }

    public function contractCancelOp()
    {
        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);
        $contract_id = $params['contract_id'];

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {

            $bizClass = new bizMemberLoanByMemberAppClass();
            $re = $bizClass->cancelContract($contract_id);
            if (!$re->STS) {
                $conn->rollback();
                return $re;
            }
            $conn->submitTransaction();
            return $re;

        } catch (Exception $e) {
            $conn->rollback();
            return new result(false, $e->getMessage(), null, $e->getCode() ?: errorCodesEnum::UNEXPECTED_DATA);
        }

    }

    /** 还款请求
     * @return result
     */
    public function repaymentApplyOp()
    {
        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);

        // APP 暂时不支持现金方式
        $repayment_way = $params['repayment_way'];
        if ($repayment_way == repaymentWayEnum::CASH) {
            return new result(false, 'Not support!', null, errorCodesEnum::NOT_SUPPORTED);
        }

        $bizClass = new bizMemberLoanRepaymentByMemberAppClass();

        // 检查密码

        $chk = $bizClass->checkMemberTradingPassword($params);
        if (!$chk->STS) {
            return $chk;
        }

        $re = $bizClass->bizStart($params);
        if (!$re->STS) {
            return $re;
        }
        return $re;


    }

    /** 还款请求取消
     * @return result
     */
    public function repaymentApplyCancelOp()
    {
        // todo 暂时没使用，加传member_id参数
        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);
        $request_id = $params['request_id'];
        $m = new loan_request_repaymentModel();
        $request = $m->getRow($request_id);
        if (!$request) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        if ($request->state != requestRepaymentStateEnum::CREATE) {
            return new result(false, 'Handling...', null, errorCodesEnum::HANDLING_LOCKED);
        }
        $delete = $request->delete();
        if (!$delete->STS) {
            return new result(false, 'Cancel fail', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success');
    }


    public function getContractPayableInfoOp()
    {
        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);
        $contract_id = $params['contract_id'];
        if ($contract_id <= 0) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $re = loan_contractClass::getContractLeftPayableInfo($contract_id);
        return $re;
    }


    public function calculateContractPayOffDetailOp()
    {
        return new result(false,'No user',null,errorCodesEnum::NOT_SUPPORTED);
        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);
        $contract_id = $params['contract_id'];
        if ($contract_id <= 0) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $re = loan_contractClass::calculateContractPrepaymentOffAmount($contract_id);
        return $re;
    }

    public function getPrepaymentDetailOp()
    {
        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);
        $contract_id = $params['contract_id'];
        $m_contract = new loan_contractModel();
        $contract_info = $m_contract->getRow($contract_id);
        if (!$contract_info) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        $re = loan_contractClass::getContractPrepaymentDetailInfo($contract_id);
        if (!$re->STS) {
            return $re;
        }
        $data_return = $re->DATA;

        $data = array(
            'contract_info' => $contract_info,
            'total_overdue_amount' => $data_return['total_overdue_amount'],
            'total_need_pay' => $data_return['total_need_pay'],
            'total_left_periods' => $data_return['total_left_periods'],
            'total_left_principal' => $data_return['total_left_principal'],
        );

        $r = new ormReader();
        // 查询最近申请
        $request = loan_contractClass::getContractLastPrepaymentRequest($contract_id);
        // 过滤掉处理完成的，其他的需要展示给客户
        if ($request) {
            if ($request['state'] == prepaymentApplyStateEnum::SUCCESS) {
                $request = null;
            }
            if ($request['state'] == prepaymentApplyStateEnum::RECEIVED) {
                $request = null;
            }

        }
        if ($request) {
            $apply_id = $request['uid'];
            $sql = "select * from loan_request_repayment where prepayment_apply_id='$apply_id' order by create_time desc ";
            $prepayment_payment_record = $r->getRows($sql);

        } else {
            $prepayment_payment_record = null;
        }


        $data['last_prepayment_request'] = $request;
        $data['prepayment_payment_record'] = $prepayment_payment_record;

        return new result(true, 'success', $data);

    }

    /** 还款方式选择后预览金额
     * @return result
     */
    public function prepaymentPreviewOp()
    {
        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);
        $re = loan_contractClass::prepaymentPreview($params);

        return $re;
    }

    public function prepaymentApplyOp()
    {
        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);

        $bizClass = new bizMemberPrepaymentByMemberAppClass();
        $re = $bizClass->prepaymentApply($params);
        if (!$re->STS) {
            return $re;
        }
        return $re;

    }


    public function prepaymentAddPaymentInfoOp()
    {

        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);

        // 检查密码
        $member_id = $params['member_id'];
        $apply_id = $params['request_id'];
        $sign = $params['sign'];
        $memberObj = new objectMemberClass($member_id);
        $self_sign = md5($member_id . $apply_id . $memberObj->trading_password);
        $chk = $memberObj->checkTradingPasswordSign($sign, $self_sign, 'Loan prepayment');
        if (!$chk->STS) {
            return $chk;
        }

        $bizClass = new bizMemberPrepaymentByMemberAppClass();
        $re = $bizClass->applyPayment($params);
        if (!$re->STS) {
            logger::record('loan_prepayment_by_balance',json_encode($re));
            return $re;
        }
        return $re;

    }


    public function getSchemaRepaymentDetailOp()
    {
        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);
        $schema_id = intval($params['schema_id']);
        $schema_info = (new loan_installment_schemeModel())->getSchemaDetailById($schema_id);
        if (!$schema_info) {
            return new result(false, 'No schema info.', null, errorCodesEnum::NO_DATA);
        }

        $schema_info['receivable_date'] = date('Y-m-d', strtotime($schema_info['receivable_date']));
        $schema_info['penalty_start_date'] = date('Y-m-d', strtotime($schema_info['penalty_start_date']));
        $schema_info['penalty'] = round(loan_baseClass::calculateSchemaRepaymentPenalties($schema_info['uid']), 2);

        if (time() >= strtotime($schema_info['penalty_start_date']) && $schema_info['state'] >= schemaStateTypeEnum::CREATE
            && $schema_info['state'] < schemaStateTypeEnum::COMPLETE
        ) {
            $schema_info['is_overdue'] = 1;
        } else {
            $schema_info['is_overdue'] = 0;
        }

        $list = loan_contractClass::getSchemaRepaymentDetail($schema_id);

        return new result(true, 'success', array(
            'scheme_info' => $schema_info,
            'repayment_detail' => $list
        ));
    }


    public function getSchemaDisbursementDetailOp()
    {
        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);
        $schema_id = $params['schema_id'];
        $m = new loan_disbursement_schemeModel();
        $schema_info = $m->getRow($schema_id);
        if (!$schema_info) {
            return new result(false, 'No schema info.', null, errorCodesEnum::NO_DATA);
        }

        $schema_info['disbursable_date'] = date('Y-m-d', strtotime($schema_info['disbursable_date']));
        if (date('Y-m-d') > $schema_info['disbursable_date'] && $schema_info['state'] >= schemaStateTypeEnum::CREATE
            && $schema_info['state'] < schemaStateTypeEnum::COMPLETE
        ) {
            $schema_info['is_overdue'] = 1;
        } else {
            $schema_info['is_overdue'] = 0;
        }

        $list = (new loan_disbursement_schemeModel())->getSchemaDisbursementDetail($schema_id);
        return new result(true, 'success', array(
            'scheme_info' => $schema_info,
            'disburse_list' => $list
        ));
    }


    public function loanApplyPreviewOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        return credit_loanClass::loanConsultBaseInfoPreview($params);
    }

    public function getRepaymentLogDetailInfoOp()
    {
        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);
        $uid = intval($params['uid']);
        $detail = (new loan_repaymentModel())->getDetailInfoById($uid);
        if( !$detail ){
            return new result(false,'No data:'.$uid,null,errorCodesEnum::NO_DATA);
        }
        return new result(true,'success',array(
            'detail' => $detail
        ));

    }


    public function loanPrepaymentGetContractListOp()
    {
        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);
        $member_id = intval($params['member_id']);
        $member_category_id = $params['member_credit_category_id'];
        $bizClass = new bizMemberPrepaymentByMemberAppClass();
        return $bizClass->getContractListByCategory($member_id,$member_category_id);
    }

    public function loanPrepaymentStartOp()
    {
        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);
        $member_id = $params['member_id'];
        $contract_ids = $params['contract_ids'];
        $repayment_way = $params['repayment_way'];
        $contract_list = explode(',',trim($contract_ids,','));
        $bizClass = new bizMemberPrepaymentByMemberAppClass();
        return $bizClass->selectContract($member_id,$contract_list,$repayment_way);
    }

    public function loanPrepaymentConfirmOp()
    {

        $re = $this->checkToken();
        if (!$re->STS) {
            return $re;
        }
        $params = array_merge(array(), $_GET, $_POST);
        $member_id = $params['member_id'];
        $biz_id = $params['biz_id'];
        $sign = $params['sign'];

        // 构造签名
        $memberObj = new objectMemberClass($member_id);
        $self_sign = md5($biz_id.$memberObj->trading_password);


        $bizClass = new bizMemberPrepaymentByMemberAppClass();
        $rt = $bizClass->verifyMemberTradingPasswordBySign($member_id,$sign,$self_sign,'Loan repayment');
        if( !$rt->STS ){
            return $rt;
        }

        return $bizClass->confirmRepayment($biz_id);


    }


}