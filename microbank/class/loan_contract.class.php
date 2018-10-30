<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/28
 * Time: 14:36
 */
class loan_contractClass
{
    public function __construct()
    {
    }


    public static function getContractUncompletedSchemas($contract_id)
    {
        return (new loan_contractModel())->getContractUncompletedSchemas($contract_id);
    }

    /** 合同是否在执行中（确认且合同未完成）
     * @param $contract_info
     * @return bool
     */
    public static function loanContractIsUnderExecuting($contract_info)
    {
        $contract_state = $contract_info['state'];

        if ($contract_state < loanContractStateEnum::PENDING_DISBURSE ||
            $contract_state >= loanContractStateEnum::COMPLETE
        ) {
            return false;
        }
        return true;
    }

    /** 贷款合同是否支持提前还款
     * @param $contract_info
     * @return bool
     */
    public static function isSupportPrepayment($contract_info)
    {
        $sub_product_info = (new loan_sub_productModel())->getRow($contract_info['sub_product_id']);
        // todo 全息应该也支持提前还
        if ($sub_product_info['is_full_interest_prepayment'] == 1
            || $contract_info['repayment_type'] == interestPaymentEnum::ANYTIME_SINGLE_REPAYMENT
        ) {
            return false;
        }
        return true;
    }

    public static function contractAddBillPayCode($contract_info,$member_info)
    {

        $contract_id = intval($contract_info['uid']);
        $member_id = $member_info['uid'];
        $branch_id = intval($member_info['branch_id']);
        $sub_product_id = $contract_info['sub_product_id'];
        $member_category = (new member_credit_categoryModel())->find(array(
            'uid' => $contract_info['member_credit_category_id']
        ));
        $loan_category_id = $member_category['category_id'];

        // 获得总行的银行卡
        $bank_list = bank_accountClass::getHQBillPayBankList();

        $conn = ormYo::Conn();

        if (count($bank_list) > 0) {

            $field_array = array(
                'contract_id',
                'bill_code',
                'bank_id',
                'bank_code',
                'currency',
                'bank_name',
                'bank_account_no',
                'bank_account_name',
                'create_time'

            );
            $sql = "insert into loan_contract_billpay_code(".implode(',',$field_array).") values  ";
            $sql_arr = array();

            // 所有的billcode都是一样的
            $bill_code = bank_accountClass::getBillPayCodeByContractSn($contract_info['virtual_contract_sn']);
            foreach ($bank_list as $bank ) {

                //$bill_code = bank_accountClass::generateBillPayCode($branch_id,$sub_product_id,$member_id);
                $temp = array(
                    "'$contract_id'",
                    "'$bill_code'",
                    "'".$bank['uid']."'",
                    "'".$bank['bank_code']."'",
                    "'".$bank['currency']."'",
                    "'".$bank['bank_name']."'",
                    "'".$bank['bank_account_no']."'",
                    "'".$bank['bank_account_name']."'",
                    "'".Now()."'"
                );
                $sql_arr[] = "(".implode(',',$temp).")";
            }
            $sql .= implode(',', $sql_arr);
            $insert = $conn->execute($sql);
            if (!$insert->STS) {
                return new result(false, 'Insert bill pay code fail.', null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true, 'success');
    }



    public static function getLoanContractMemberInfo($contract_id)
    {
        $r = new ormReader();

        $sql = "select m.*,c.account_id loan_account_id from loan_contract c left join loan_account a on a.uid=c.account_id inner join client_member m 
        on m.obj_guid=a.obj_guid where c.uid='" . $contract_id . "' ";

        $member_info = $r->getRow($sql);

        return $member_info;
    }


    /** 获得贷款合同详细信息
     * @param $contract_id
     * @return result
     */
    public static function getLoanContractDetailInfo($contract_id)
    {
        $contract_id = intval($contract_id);
        $m_loan_contract = new loan_contractModel();
        $contract = $m_loan_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        $m_product = new loan_productModel();
        $m_sub_product = new loan_sub_productModel();
        $m_rate = new loan_product_size_rateModel();
        $m_installment = new loan_installment_schemeModel();
        $m_distribute = new loan_disbursement_schemeModel();
        $m_special_rate = new loan_product_special_rateModel();
        $m_member_credit_category = new member_credit_categoryModel();

        $product = $m_product->getRow($contract->product_id);

        $product_category = $m_member_credit_category->getRow($contract->member_credit_category_id);

        $sub_product_info = $m_sub_product->getRow(intval($contract->sub_product_id));

        $interest_info = $contract;
        $size_interest = $m_rate->getRow($contract->product_size_rate_id);
        $special_rate = null;
        if ($contract['product_special_rate_id']) {
            $special_rate = $m_special_rate->getRow($contract['product_special_rate_id']);
        }

        $distribute_schema = $m_distribute->select(array(
            'contract_id' => $contract_id
        ));

        $client_receive_amount = 0;
        $total_deduct_interest = $total_deduct_operation_fee = 0;
        $total_deduct_service_fee = 0;

        foreach ($distribute_schema as $k => $v) {
            $item = $v;
            $client_receive_amount += $v['amount'];
            $total_deduct_interest += $v['deduct_interest'];
            $total_deduct_operation_fee += $v['deduct_operation_fee'];
            $total_deduct_service_fee += $v['deduct_service_fee'];
            $item['disbursable_date'] = date('Y-m-d', strtotime($v['disbursable_date']));
            if( date('Y-m-d') > $item['disbursable_date'] && $item['state'] >= schemaStateTypeEnum::CREATE
                && $item['state'] < schemaStateTypeEnum::COMPLETE
            ){
                $item['is_overdue'] = 1;
            }else{
                $item['is_overdue'] = 0;
            }
            $distribute_schema[$k] = $item;
        }


        $installment_schema = $m_installment->select(array(
            'contract_id' => $contract_id,
        ));

        $contract_total_repayment = 0;
        foreach ($installment_schema as $k => $v) {
            $contract_total_repayment += $v['amount'];
            $item = $v;
            $item['receivable_date'] = date('Y-m-d', strtotime($v['receivable_date']));
            $item['penalty_start_date'] = date('Y-m-d', strtotime($v['penalty_start_date']));
            $item['penalty'] = round(loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']), 2);
            if( time() >= strtotime($item['penalty_start_date']) && $item['state'] >= schemaStateTypeEnum::CREATE
                && $item['state'] < schemaStateTypeEnum::COMPLETE
            ){
                $item['is_overdue'] = 1;
            }else{
                $item['is_overdue'] = 0;
            }
            $installment_schema[$k] = $item;
        }

        // 保险合同
        $sql = "select c.*,i.item_code,i.item_name from insurance_contract c left join insurance_product_item i on c.product_item_id=i.uid where 
             c.loan_contract_id='$contract_id' ";
        $insurances = $m_loan_contract->reader->getRows($sql);

        if (!interestTypeClass::isPeriodicRepayment($contract->repayment_type)) {
            $due_type = 'once';
        } else {
            switch ($contract->repayment_period) {
                case interestRatePeriodEnum::YEARLY:
                case interestRatePeriodEnum::SEMI_YEARLY:
                case interestRatePeriodEnum::QUARTER:
                    $due_type = interestRatePeriodEnum::YEARLY;
                    break;
                case interestRatePeriodEnum::MONTHLY:
                    $due_type = interestRatePeriodEnum::MONTHLY;
                    break;
                case interestRatePeriodEnum::WEEKLY:
                    $due_type = interestRatePeriodEnum::WEEKLY;
                    break;
                case interestRatePeriodEnum::DAILY:
                    $due_type = interestRatePeriodEnum::DAILY;
                    break;
                default:
                    $due_type = '';
            }
        }

        // 是否可还款
        $is_can_repay = 0;
        if (loan_contractClass::loanContractIsUnderExecuting($contract)) {
            $is_can_repay = 1;
        }

        // 是否支持提前还款


        if (self::isSupportPrepayment($contract)) {
            $is_can_prepayment = 1;
        } else {
            $is_can_prepayment = 0;
        }


        $member_info = self::getLoanContractMemberInfo($contract_id);


        // bill pay  只获取还存在银行卡的列表
        $sql = "select c.* from loan_contract_billpay_code c  inner join site_bank b
        on c.bank_id=b.uid  where c.contract_id='$contract_id' ";
        $bill_pay_list = $m_loan_contract->reader->getRows($sql);
        foreach( $bill_pay_list as $k=>$v ){
            $v['logo'] = global_settingClass::getBankLogoByBankCode($v['bank_code']);
            $bill_pay_list[$k] = $v;
        }

        // 没还的罚金
        $penalty_list = $m_loan_contract->getContractUnPaidPenaltyList($contract_id);

        // 剩余应还信息
        $left_payable_info = self::getContractRemainAmountInfo($contract_id);

        $return = array(
            'contract_id' => $contract->uid,
            'contract_sn' => $contract->contract_sn,
            'virtual_contract_sn' => $contract->virtual_contract_sn,
            'is_can_repay' => $is_can_repay,
            'is_can_prepayment' => $is_can_prepayment,
            'loan_amount' => $contract->apply_amount,
            'currency' => $contract->currency,
            'loan_period_value' => $contract->loan_period_value,
            'loan_period_unit' => $contract->loan_period_unit,
            'repayment_type' => $contract->repayment_type,
            'repayment_period' => $contract->repayment_period,
            'due_date' => $contract->due_date,
            'due_date_type' => $due_type,
            'due_date_type_val' => $contract->due_date_type,
            'interest_rate' => $interest_info['interest_rate'],
            'interest_rate_type' => $interest_info['interest_rate_type'],
            'interest_rate_unit' => $interest_info['interest_rate_unit'],
            'total_admin_fee' => $contract->receivable_admin_fee,
            'total_loan_fee' => $contract->receivable_loan_fee,
            'total_insurance_fee' => $contract->receivable_insurance_fee,
            'total_operation_fee' => $contract->receivable_operation_fee,
            'total_service_fee' => $contract->receivable_service_fee,
            'total_interest' => $contract->receivable_interest,
            'total_deduct_interest' => $total_deduct_interest,
            'total_deduct_operation_fee' => $total_deduct_operation_fee,
            'total_deduct_service_fee' => $total_deduct_service_fee,
            'actual_receive_amount' => $client_receive_amount,
            'total_repayment' => $contract_total_repayment,
            'lending_time' => $contract->create_time,
            'loan_product_info' => $product,
            'loan_sub_product_info' => $sub_product_info,
            'interest_info' => $interest_info, // 实际计算利率
            'size_rate' => $size_interest,
            'special_rate' => $special_rate ?: null,
            'contract_info' => $contract,
            'member_info' => $member_info,
            'loan_disbursement_scheme' => $distribute_schema,
            'loan_installment_scheme' => $installment_schema,
            'bind_insurance' => $insurances,
            'next_repayment_detail_info' => null,
            'bill_pay_list' => $bill_pay_list,
            'contract_penalty_list' => $penalty_list,
            'remain_payable_amount' => $left_payable_info,
            'product_category_name' => $product_category->alias
        );

        return new result(true, 'success', $return);

    }


    /** 插入合同需要处理的全部罚金
     * @param $contract_info
     * @return result
     */
    public static function insertPendingHandlePenaltyOfContract($contract_info)
    {
        $m_contract = new loan_contractModel();
        $contract_id = $contract_info['uid'];
        $loan_account_id = $contract_info['account_id'];
        $contract_currency = $contract_info['currency'];
        $overdue_schema = $m_contract->getContractOverdueSchemas($contract_id);
        $penalty_arr = array();
        foreach( $overdue_schema as $v ){
            $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
            if( $penalty > 0 ){
                $penalty_arr[] = array(
                    'schema_id' => $v['uid'],
                    'penalty' => $penalty
                );
            }
        }
        if( !empty($penalty_arr) ){
            $sql = "insert into loan_penalty(account_id,contract_id,scheme_id,currency,penalty_amount,state,create_time) values  ";
            $sql_arr = array();
            foreach( $penalty_arr as $vv){
                $sql_arr[] = "('$loan_account_id','$contract_id','".$vv['schema_id']."','$contract_currency','".$vv['penalty']."','".loanPenaltyHandlerStateEnum::CREATE."','".Now()."')";
            }
            $sql .= implode(',',$sql_arr);
            $insert = $m_contract->conn->execute($sql);
            if( !$insert->STS ){
                return new result(false,'Insert penalty fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true,'success');
    }

    /** 还款后更新合同状态
     * @param $contract_id
     * @return result
     */
    public static function updateContractStateAfterRepayment($contract_id)
    {
        $m_contract = new loan_contractModel();
        $contract_info = $m_contract->getRow($contract_id);
        if (!$contract_info) {
            return new result(false, 'No contract info:' . $contract_id, null, errorCodesEnum::NO_CONTRACT);
        }
        if ($m_contract->contractIsPaidOff($contract_id)) {

            if ($m_contract->contractIsRemainPenalty($contract_id)) {

                $contract_info->state = loanContractStateEnum::ONLY_PENALTY;
                $contract_info->update_time = Now();
                $up = $contract_info->update();
                if (!$up->STS) {
                    return new result(false, 'Update contract fail.', null, errorCodesEnum::DB_ERROR);
                }
                return new result(true, 'success');

            } else {
                return self::contractComplete($contract_id);
            }
        }

        return new result(true, 'success');
    }

    /** 获取贷款合同的利率信息
     * @param $contract_id
     * @return result
     */
    public static function getContractInterestInfo($contract_id)
    {
        $m_contract = new loan_contractModel();
        $contract = $m_contract->find(array(
            'uid' => $contract_id
        ));
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        $interest_info = self::getContractInterestInfoByContractInfo($contract);

        return new result(true, 'success', $interest_info);

    }


    /** 通过合同信息获取利率信息
     * @param $contract
     * @return mixed
     */
    public static function getContractInterestInfoByContractInfo($contract)
    {
        $interest_info = $contract;
        $interest_info['interest_payment'] = $contract['repayment_type'];
        $interest_info['interest_rate_period'] = $contract['repayment_period'];
        return $interest_info;
    }


    /** 获取计划的详细还款明细
     * @param $schema_id
     * @return ormCollection
     */
    public static function getSchemaRepaymentDetail($schema_id)
    {
        $m = new loan_installment_schemeModel();
        $list = $m->getSchemaRepaymentDetail($schema_id);

        // 整理还款方式
        foreach ($list as $k => $v) {
            $v['repayment_type_des'] = ucwords(member_handlerClass::getHandlerTypeName($v['payer_type']));
            $list[$k] = $v;
        }
        return $list;
    }

    public static function getContractRemainAmountInfo($contract_id)
    {
        $contract_model = new loan_contractModel();
        $left_schemas = $contract_model->getContractUncompletedSchemas($contract_id);
        $left_principal = $left_interest = $left_operation_fee = 0;
        foreach( $left_schemas as $schema ){
            $left_principal += $schema['receivable_principal'] - $schema['paid_principal'];
            $left_interest += $schema['receivable_interest'] - $schema['paid_interest'];
            $left_operation_fee += $schema['receivable_operation_fee'] - $schema['paid_operation_fee'];
        }
        $total_amount = $left_principal+$left_interest+$left_operation_fee;
        return array(
            'total' => $total_amount,
            'principal' => $left_principal,
            'interest' => $left_interest,
            'operation_fee' => $left_operation_fee
        );
    }

    /** 获取合同剩余应还信息
     * @param $contract_id
     * @return result
     */
    public static function getContractLeftPayableInfo($contract_id)
    {
        $contract_model = new loan_contractModel();
        $today = date('Y-m-d');
        $current_schema = null;
        $left_schemas = $contract_model->getContractUncompletedSchemas($contract_id);

        // 计算欠款
        $total_principal = $total_penalty = $total_amount = $overdue_amount = 0;
        foreach ($left_schemas as $v) {

            $left_principal = ($v['receivable_principal'] - $v['paid_principal']);

            $total_principal += $left_principal;

            $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
            $amount = $v['amount'] - $v['actual_payment_amount'] + $penalty;

            $v['left_amount'] = $amount;

            if ($v['receivable_date'] < $today) {
                $overdue_amount += $v['amount'] - $v['actual_payment_amount'];  // 逾期本息
            }

            if (!$current_schema) {
                if ($v['receivable_date'] >= $today) {
                    $current_schema = $v;
                }
            }

            $total_penalty += $penalty;
            $total_amount += $amount;
        }

        if ($current_schema) {
            $next_repayment_date = date('Y-m-d', strtotime($current_schema['receivable_date']));
            $next_repayment_amount = $current_schema['left_amount'];
        } else {
            $next_repayment_date = null;
            $next_repayment_amount = 0;
        }

        // 是否存在未处理完的请求
        $m_request = new loan_request_repaymentModel();
        $sql = "select r.*,d.contract_id,d.scheme_id from loan_request_repayment_detail d left join  loan_request_repayment r 
        on d.request_id=r.uid where d.contract_id='$contract_id' order by d.uid desc ";
        $last_request_repayment_info = $m_request->reader->getRow($sql);

        $has_request = null;
        if ($last_request_repayment_info && $last_request_repayment_info->state != requestRepaymentStateEnum::SUCCESS) {
            $has_request = $last_request_repayment_info;
        }

        return new result(true, 'success', array(
            'left_period' => count($left_schemas),
            'next_repayment_date' => $next_repayment_date,
            'next_repayment_amount' => $next_repayment_amount,
            'total_overdue_penalty' => $total_penalty,
            'total_overdue_amount' => $overdue_amount,
            'total_payable_principal' => $total_principal,
            'total_payable_amount' => $total_amount,
            'last_request_repayment_info' => $has_request
        ));
    }


    /** 贷款周期转换成贷款时间
     * @param $periodCount
     * @param $periodType
     * @return array
     * @throws Exception
     */
    public static function periodsToTime($periodCount, $periodType)
    {
        switch ($periodType) {
            case interestRatePeriodEnum::YEARLY:
                $loan_time = $periodCount;
                $loan_time_unit = loanPeriodUnitEnum::YEAR;
                break;
            case interestRatePeriodEnum::SEMI_YEARLY:
                $loan_time = $periodCount * 6;
                $loan_time_unit = loanPeriodUnitEnum::MONTH;
                break;
            case interestRatePeriodEnum::QUARTER:
                $loan_time = $periodCount * 3;
                $loan_time_unit = loanPeriodUnitEnum::MONTH;
                break;
            case interestRatePeriodEnum::MONTHLY:
                $loan_time = $periodCount;
                $loan_time_unit = loanPeriodUnitEnum::MONTH;
                break;
            case interestRatePeriodEnum::WEEKLY:
                $loan_time = $periodCount * 7;
                $loan_time_unit = loanPeriodUnitEnum::DAY;
                break;
            case interestRatePeriodEnum::DAILY:
                $loan_time = $periodCount;
                $loan_time_unit = loanPeriodUnitEnum::DAY;
                break;
            default:
                throw new Exception("Unknown period type - " . $periodType, errorCodesEnum::UNEXPECTED_DATA);
        }

        return array($loan_time, $loan_time_unit);
    }


    /** 正常还款申请
     * @param $params
     * @return result
     */
    public static function repaymentApply($params)
    {
        return self::schemaRepaymentApply($params);
    }


    /** 插入计划还款的相关信息
     * @param $schema_list
     * @param $repayment_way
     * @param $member_id
     * @param $extend_params
     * @return result
     */
    public static function insertSchemaRepaymentApplyInfo($schema_list,$repayment_way,$member_id,$extend_params)
    {
        $memberObj = new objectMemberClass($member_id);

        $m_apply = new loan_request_repaymentModel();
        $request = $m_apply->newRow();
        switch ($repayment_way) {
            case repaymentWayEnum::AUTO_DEDUCTION :
                $member_handler_id = $extend_params['handler_id'];
                $handler_info = member_handlerClass::getHandlerInfoById($member_handler_id);
                if (!$handler_info) {
                    return new result(false, 'No handler info.', null, errorCodesEnum::NO_ACCOUNT_HANDLER);
                }
                $request->payer_id = $handler_info->uid;
                $request->payer_type = $handler_info->handler_type;
                $request->payer_name = $handler_info->handler_name;
                $request->payer_phone = $handler_info->handler_phone;
                $request->payer_account = $handler_info->handler_account;
                break;
            case repaymentWayEnum::BANK_TRANSFER :

                if (empty($_FILES['receipt_image']) && empty($_FILES['receipt_image1']) && empty($_FILES['receipt_image2'])) {
                    return new result(false, 'Did not upload receipt image.', null, errorCodesEnum::INVALID_PARAM);
                }

                $default_dir = 'loan/receipt';
                if (!empty($_FILES['receipt_image'])) {


                    $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                    $upload->set('save_path', null);
                    $upload->set('default_dir', $default_dir);
                    $re = $upload->server2upun('receipt_image');
                    if ($re == false) {
                        return new result(false, 'Upload image fail', null, errorCodesEnum::API_FAILED);
                    }
                    $img_path = $upload->img_url;
                    unset($upload);
                    $request->request_img = $img_path;
                }

                if (!empty($_FILES['receipt_image1'])) {


                    $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                    $upload->set('save_path', null);
                    $upload->set('default_dir', $default_dir);
                    $re = $upload->server2upun('receipt_image1');
                    if ($re == false) {
                        return new result(false, 'Upload image fail', null, errorCodesEnum::API_FAILED);
                    }
                    $img_path = $upload->img_url;
                    unset($upload);
                    $request->request_img1 = $img_path;
                }

                if (!empty($_FILES['receipt_image2'])) {


                    $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                    $upload->set('save_path', null);
                    $upload->set('default_dir', $default_dir);
                    $re = $upload->server2upun('receipt_image2');
                    if ($re == false) {
                        return new result(false, 'Upload image fail', null, errorCodesEnum::API_FAILED);
                    }
                    $img_path = $upload->img_url;
                    unset($upload);
                    $request->request_img2 = $img_path;
                }
                $request->payer_id = 0;
                $request->payer_type = memberAccountHandlerTypeEnum::BANK;

                break;
            case repaymentWayEnum::PASSBOOK :
                $passbook_handler = member_handlerClass::getMemberDefaultPassbookHandlerInfo($member_id);
                if (!$passbook_handler) {
                    return new result(false, 'No passbook handler.', null, errorCodesEnum::NO_ACCOUNT_HANDLER);
                }
                $request->payer_id = $passbook_handler['uid'];
                $request->payer_type = $passbook_handler['handler_type'];
                $request->payer_name = $passbook_handler['handler_name'];
                $request->payer_phone = $passbook_handler['handler_phone'];
                $request->payer_account = $passbook_handler['handler_account'];
                break;
            case repaymentWayEnum::CASH :
                $request->payer_id = 0;
                $request->payer_type = memberAccountHandlerTypeEnum::CASH;
                $request->payer_name = $memberObj->member_account;
                $request->payer_phone = $memberObj->object_info['phone_id'];
                $request->payer_account = $memberObj->member_account;
                break;
            default :
                return new result(false, 'Not support way.', null, errorCodesEnum::NOT_SUPPORTED);
        }

        $request->member_id = $member_id;
        $request->type = requestRepaymentTypeEnum::SCHEME;
        $request->repayment_way = $repayment_way;
        $request->request_remark = $extend_params['remark'];
        $request->create_time = Now();
        $request->state = requestRepaymentStateEnum::CREATE;
        $insert1 = $request->insert();
        if (!$insert1->STS) {
            return new result(false, 'Request fail.', null, errorCodesEnum::DB_ERROR);
        }

        // 插入计划附表

        if (empty($schema_list)) {
            return new result(false, 'Error schema ids.', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_schema = new loan_installment_schemeModel();
        foreach ($schema_list as $schema_id) {
            $schema = $m_schema->getRow($schema_id);
            if (!$schema) {
                continue;
            }
            $sql = "insert into loan_request_repayment_detail(request_id,contract_id,scheme_id) VALUES 
            ('" . $request->uid . "','" . $schema->contract_id . "','" . $schema->uid . "')";
            $insert = $m_apply->conn->execute($sql);
            if (!$insert->STS) {
                return new result(false, 'Insert schema fail.', null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true,'success',$request);
    }

    /** 按计划还款申请(已经转移到biz处理)
     * @param $params
     * @return result
     */
    public static function schemaRepaymentApply($params)
    {
        $schema_ids = trim($params['schema_ids']);
        $repayment_way = $params['repayment_way'];
        $member_id = $params['member_id'];

        if (!$schema_ids) {
            return new result(false, 'No schema.', null, errorCodesEnum::INVALID_PARAM);
        }


        $schema_list = explode(',',$schema_ids);
        $rt = self::insertSchemaRepaymentApplyInfo($schema_list,$repayment_way,$member_id,$params);
        if( !$rt->STS ){
            return $rt;
        }

        $request = $rt->DATA;


        // 都成功后，如果是自动扣款的，马上执行
        if ($repayment_way == repaymentWayEnum::AUTO_DEDUCTION || $repayment_way == repaymentWayEnum::PASSBOOK) {
            $handler_info = array(
                'handler_id' => 0,
                'handler_name' => 'System',
                'handle_remark' => 'System handle',
                'handle_time' => Now()
            );
            $rt = self::requestRepaymentConfirmReceived($request->uid, array(), $handler_info);
            if (!$rt->STS) {
                return $rt;
            }
        }

        return new result(true, 'success',$request);

    }


    /** 获取偿还金额可以偿还的计划
     * @param $contract_id
     * @param $amount
     * @param $currency
     * @return result
     */
    public static function getRepaymentSchemaByAmount($contract_id, $amount, $currency)
    {
        $contract_id = intval($contract_id);
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        // 买入还款货币汇率
        $exchange_rate = global_settingClass::getCurrencyRateBetween($currency, $contract->currency);
        if ($exchange_rate <= 0) {
            return new result(false, 'Not set currency exchange rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
        }

        $amount = round($amount * $exchange_rate, 2);
        $return = array();

        // 剩余未还清计划
        $lists = $m_contract->getContractUncompletedSchemas($contract_id);

        if (count($lists) < 1) {
            return new result(true, 'All payed');
        }


        // 能还多少还多少
        // 罚金不在还款的里面，单独处理
        $left_amount = $amount;

        // 实际还款金额
        $contract_paid_amount = 0;

        foreach ($lists as $schema) {


            if ($left_amount > 0) {

                $pay_amount = $schema['amount'] - $schema['actual_payment_amount'];

                $ap_amount = ($left_amount>=$pay_amount)?$pay_amount:$left_amount;
                $contract_paid_amount += $ap_amount;
                $schema['ap_amount'] = $ap_amount;
                $schema['ap_penalty'] = 0;
                $return[] = $schema;

                $left_amount -= $ap_amount;

               /* $ap_amount = $schema['amount'] - $schema['actual_payment_amount'];
                $need_pay = $ap_amount;
                $contract_paid_amount += $need_pay;

                if ($left_amount >= $need_pay) {
                    $schema['ap_amount'] = $ap_amount;
                    $schema['ap_penalty'] = 0;
                    $return[] = $schema;
                    $left_amount -= $need_pay;
                }*/

            }

        }


        return new result(true, 'success', array(
            'repayment_amount_balance' => $left_amount,
            'repayment_schema' => $return,
            'contract_payable_amount' => $contract_paid_amount
        ));
    }


    /** 提前还款查看合同信息
     * @param $contract_id
     * @return result
     */
    public static function getContractPrepaymentDetailInfo($contract_id)
    {
        $today = date('Y-m-d');
        $m_contract = new loan_contractModel();
        $left_schema = $m_contract->getContractUncompletedSchemas($contract_id);

        $overdue_schema = $normal_schema = array();
        // 分割逾期的
        foreach ($left_schema as $v) {
            if ($v['receivable_date'] < $today) {
                $overdue_schema[] = $v;
            } else {
                $normal_schema[] = $v;
            }
        }

        // 计算必须还的部分
        $need_pay_principal = 0;
        $need_pay_interest = 0;
        $need_pay_operation_fee = 0;
        $need_pay_penalty = 0;


        foreach ($overdue_schema as $v) {

            // 罚金分开处理，这里不显示罚金的信息
            //$v['penalty'] = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
            //$need_pay_penalty += $v['penalty'];
            $need_pay_principal += $v['receivable_principal'];
            $need_pay_interest += $v['receivable_interest'];
            $need_pay_operation_fee += $v['receivable_operation_fee'];

        }

        $need_pay_total = $need_pay_principal+$need_pay_interest+$need_pay_operation_fee+$need_pay_penalty;

        $total_left_periods = count($normal_schema);
        $first_normal_schema = reset($normal_schema);
        $total_left_principal = $first_normal_schema['initial_principal']?:0;

        return new result(true, 'success', array(
            'total_overdue_amount' => $need_pay_total,
            'total_left_principal' => $total_left_principal,
            'total_left_periods' => $total_left_periods,
            'total_need_pay' => array(
                'total' => $need_pay_total,
                'principal' => $need_pay_principal,
                'interest' => $need_pay_interest,
                'operation_fee' => $need_pay_operation_fee,
                'penalty' => $need_pay_penalty
            ),
            'schema_detail' => array(
                'overdue_schema' => $overdue_schema,
                'next_schema' => null,
                'last_schema' => $normal_schema
            ),
        ));
    }


    /** 提前还款信息
     * @param $contract_id
     * @return result
     */
    public static function getPrepaymentDetail_old($contract_id)
    {
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        $re = self::getContractInterestInfo($contract_id);
        if (!$re->STS) {
            return $re;
        }
        $interest_info = $re->DATA;

        $rt = loan_contractClass::getContractSchemaDataForPrepaymentCalculation($contract['uid']);
        if (!$rt->STS) {
            return $rt;
        }
        $group_schema = $rt->DATA;
        $overdue_schema = $group_schema['overdue_schema'];
        $next_schema = $group_schema['current_schema'];
        $last_schema = $group_schema['remain_schema'];
        $next_repayment_date = $group_schema['cutoff_date'];


        //统计
        $total_overdue = $total_next_repay = $total_left_principal = 0;
        $need_pay_principal = 0;
        $need_pay_penalty = 0;

        foreach ($overdue_schema as $v) {
            $v['penalty'] = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
            $total_overdue += $v['amount'] - $v['actual_payment_amount'] + $v['penalty'];
            $need_pay_penalty += $v['penalty'];

            if ($v['actual_payment_amount'] >= $v['receivable_principal']) {
                $principal = 0;
            } else {
                $principal = $v['receivable_principal'] - $v['actual_payment_amount'];
            }
            $need_pay_principal += $principal;
        }

        foreach ($next_schema as $v) {
            $total_next_repay += $v['amount'] - $v['actual_payment_amount'];

            if ($v['actual_payment_amount'] >= $v['receivable_principal']) {
                $principal = 0;
            } else {
                $principal = $v['receivable_principal'] - $v['actual_payment_amount'];
            }
            $need_pay_principal += $principal;

        }

        $need_pay_total = $total_overdue + $total_next_repay;
        $need_pay_interest = $need_pay_total - $need_pay_principal - $need_pay_penalty;


        if (empty($last_schema)) {
            $total_left_periods = 0;
        } else {
            $total_left_periods = count($last_schema);
        }

        foreach ($last_schema as $k => $v) {

            if ($v['actual_payment_amount'] >= $v['receivable_principal']) {
                $principal = 0;
            } else {
                $principal = $v['receivable_principal'] - $v['actual_payment_amount'];
            }
            $v['remaining_principal'] = $principal;
            $total_left_principal += $principal;

            $last_schema[$k] = $v;
        }


        return new result(true, 'success', array(
            'total_overdue_amount' => $total_overdue,
            'next_repayment_date' => $next_repayment_date,
            'next_repayment_amount' => $total_next_repay,
            'total_left_principal' => $total_left_principal,
            'total_left_periods' => $total_left_periods,
            'total_need_pay' => array(
                'total' => $need_pay_total,
                'principal' => $need_pay_principal,
                'interest' => $need_pay_interest,
                'penalty' => $need_pay_penalty
            ),
            'schema_detail' => array(
                'overdue_schema' => $overdue_schema,
                'next_schema' => $next_schema,
                'last_schema' => $last_schema
            ),
            'contract_detail' => array(
                'contract_info' => $contract,
                'interest_detail' => $interest_info
            )
        ));

    }


    /** 获取最近的合同提前还款申请
     * @param $contract_id
     * @return null
     */
    public static function getContractLastPrepaymentRequest($contract_id)
    {
        $request = null;
        // 新申请
        $r = new ormReader();
        $sql = "select * from loan_prepayment_apply where contract_id='$contract_id'  order by uid desc ";
        $new = $r->getRow($sql);
        $request = $new ?: null;

        return $request;
    }



    /** 提前还款申请
     * @param $params
     * @return result
     */
    public static function prepaymentApply($params)
    {

        $contract_id = intval($params['contract_id']);
        $prepayment_type = intval($params['prepayment_type']);
        $repay_period = intval($params['repay_period']);
        $amount = round($params['amount'],2);

        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        if( !self::loanContractIsUnderExecuting($contract)){
            return new result(false,'Invalid contract state:'.$contract_id,null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        if( !self::isSupportPrepayment($contract) ){
            return new result(false,'Not support prepayment.',null,errorCodesEnum::NOT_SUPPORT_PREPAYMENT);
        }


        $p_re = self::prepaymentPreview($params);
        if (!$p_re->STS) {
            return $p_re;
        }
        $prepaymentInfo = $p_re->DATA;

        $m_apply = new loan_prepayment_applyModel();
        $request = $m_apply->newRow();
        $request->contract_id = $contract_id;
        $request->deadline_date = $prepaymentInfo['cut_off_date'];
        $request->payable_principal = $prepaymentInfo['total_paid_principal'];
        $request->payable_interest = $prepaymentInfo['total_paid_interest'];
        $request->payable_operation_fee = $prepaymentInfo['total_paid_operation_fee'];
        $request->payable_penalty = 0;  // $prepaymentInfo['total_paid_penalty']
        $request->total_payable_amount = $prepaymentInfo['total_prepayment_amount'];
        $request->loss_interest = $prepaymentInfo['loss_interest']?:0;
        $request->loss_operation_fee = $prepaymentInfo['loss_operation_fee']?:0;
        $request->currency = $contract->currency;
        $request->prepayment_type = $prepayment_type;
        $request->repay_period = $repay_period;
        $request->apply_principal_amount = $amount;
        $request->apply_time = Now();
        $in = $request->insert();

        if (!$in->STS) {
            return new result(false, 'DB error', null, errorCodesEnum::DB_ERROR);
        }

        // 是否自动审批
        if (!global_settingClass::isPrepaymentRequestNeedApproved()) {

            $request->state = prepaymentApplyStateEnum::APPROVED;
            $request->auditor_id = 0;
            $request->auditor_name = 'System';
            $request->audit_remark = 'Auto approved';
            $request->audit_time = Now();
            $request->update_time = Now();
            $up = $request->update();
            if (!$up->STS) {
                return new result(false, 'Auto approved fail.', null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true, 'success', $request);

    }


    public static function getContractLastPayOffDay($contract_info)
    {
        $r = new ormReader();
        // 找到最后一期还清的计划时间
        $sql = "select max(receivable_date) from loan_installment_scheme where contract_id='".$contract_info['uid']."'
            and state='".schemaStateTypeEnum::COMPLETE."' ";
        $end_receivable_date = $r->getOne($sql);
        $end_receivable_date = $end_receivable_date?:$contract_info['start_date'];
        return $end_receivable_date;
    }


    /** 获取合同提前还款最少要计算至利息的日期
     * @param $contract_info
     * @return bool|string
     */
    public static function getPrepaymentInterestMinTermDay($contract_info)
    {

        $loan_days = $contract_info['loan_term_day'];

        $cate_id = 0;
        if( $contract_info['member_credit_category_id'] ){
            // 是否有category
            $m = new member_credit_categoryModel();
            $member_category = $m->getRow(array(
                'uid' => $contract_info['member_credit_category_id']
            ));
            $cate_id = $member_category['category_id'];
        }


        // 获取最低要计算利息的天数
        $min_cal_days = global_settingClass::getInterestMindaysByLoanDays($loan_days,$cate_id);  // loan days 是贷款合同的总天数
        if( $min_cal_days > 0 ){
            return date('Y-m-d',strtotime($contract_info['start_date'])+86400*$min_cal_days);
        }else{
            //return $contract_info['start_date'];
            return date('Y-m-d');
        }

    }


    /** 提前还款分方式应还金额详情(预览)
     *  如部分还款，全额还款等
     * @param $params
     * @return result
     */
    public static function prepaymentPreview($params)
    {
        $contract_id = intval($params['contract_id']);
        $prepayment_type = intval($params['prepayment_type']);

        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        // 判断是否支持提前还款
        /*if( !self::isSupportPrepayment($contract) ){
            return new result(false,'Not support prepayment.',null,errorCodesEnum::NOT_SUPPORT_PREPAYMENT);
        }*/

        $prepaymentClass = loanPrepaymentClass::getInstance($contract);


        if( $params['deadline_date'] ){
            $deadline_date = $params['deadline_date'];
        }else{
            // 系统设置的处理时间
            $days = intval(global_settingClass::getLoanPrepaymentApplyValidDays());
            //$deadline_date = date('Y-m-d',time()+$days*24*3600);
            $deadline_date = date('Y-m-d');
            $min_contract_date = loan_contractClass::getPrepaymentInterestMinTermDay($contract);
            $deadline_date = max($deadline_date,$min_contract_date);
        }


        switch ($prepayment_type) {
            case prepaymentRequestTypeEnum::PARTLY:
                $amount = round($params['amount'],2);
                return new result(false,'Not support yet.',null,errorCodesEnum::NOT_SUPPORTED);
                break;
            case prepaymentRequestTypeEnum::FULL_AMOUNT :
                return $prepaymentClass->getPrepaymentDetailByAllPaid($deadline_date);
                break;
            case prepaymentRequestTypeEnum::LEFT_PERIOD:
                $payment_period = intval($params['repay_period']);
                return new result(false,'Not support yet.',null,errorCodesEnum::NOT_SUPPORTED);
                break;
            default:
                return new result(false, 'Un supported type', null, errorCodesEnum::NOT_SUPPORTED);
                break;
        }

    }



    /** 提前还款申请通过后还款
     * @param $params
     * @return result
     */
    public static function prepaymentApplyAddPaymentInfo($params)
    {
        $apply_id = intval($params['request_id']);
        $repayment_way = $params['repayment_way'];
        $member_id = $params['member_id'];
        $remark = $params['remark'];

        $prepayment_apply = (new loan_prepayment_applyModel())->getRow($apply_id);
        if( !$prepayment_apply ){
            return new result(false,'No apply info',null,errorCodesEnum::INVALID_PARAM);
        }

        if( $prepayment_apply->state < prepaymentApplyStateEnum::APPROVED ){
            return new result(false,'Un approved.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $memberObj = new objectMemberClass($member_id);

        $m_apply = new loan_request_repaymentModel();
        $request = $m_apply->newRow();
        switch ($repayment_way) {
            case repaymentWayEnum::AUTO_DEDUCTION :
                $member_handler_id = $params['handler_id'];
                $handler_info = member_handlerClass::getHandlerInfoById($member_handler_id);
                if (!$handler_info) {
                    return new result(false, 'No handler info.', null, errorCodesEnum::NO_ACCOUNT_HANDLER);
                }
                $request->payer_id = $handler_info->uid;
                $request->payer_type = $handler_info->handler_type;
                $request->payer_name = $handler_info->handler_name;
                $request->payer_phone = $handler_info->handler_phone;
                $request->payer_account = $handler_info->handler_account;
                break;
            case repaymentWayEnum::BANK_TRANSFER :

                if (empty($_FILES['receipt_image']) && empty($_FILES['receipt_image1']) && empty($_FILES['receipt_image2'])) {
                    return new result(false, 'Did not upload receipt image.', null, errorCodesEnum::INVALID_PARAM);
                }

                $default_dir = 'loan/receipt';
                if (!empty($_FILES['receipt_image'])) {


                    $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                    $upload->set('save_path', null);
                    $upload->set('default_dir', $default_dir);
                    $re = $upload->server2upun('receipt_image');
                    if ($re == false) {
                        return new result(false, 'Upload image fail', null, errorCodesEnum::API_FAILED);
                    }
                    $img_path = $upload->img_url;
                    unset($upload);
                    $request->request_img = $img_path;
                }

                if (!empty($_FILES['receipt_image1'])) {


                    $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                    $upload->set('save_path', null);
                    $upload->set('default_dir', $default_dir);
                    $re = $upload->server2upun('receipt_image1');
                    if ($re == false) {
                        return new result(false, 'Upload image fail', null, errorCodesEnum::API_FAILED);
                    }
                    $img_path = $upload->img_url;
                    unset($upload);
                    $request->request_img1 = $img_path;
                }

                if (!empty($_FILES['receipt_image2'])) {


                    $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                    $upload->set('save_path', null);
                    $upload->set('default_dir', $default_dir);
                    $re = $upload->server2upun('receipt_image2');
                    if ($re == false) {
                        return new result(false, 'Upload image fail', null, errorCodesEnum::API_FAILED);
                    }
                    $img_path = $upload->img_url;
                    unset($upload);
                    $request->request_img2 = $img_path;
                }
                $request->payer_id = 0;
                $request->payer_type = memberAccountHandlerTypeEnum::BANK;

                break;
            case repaymentWayEnum::PASSBOOK :
                $passbook_handler = member_handlerClass::getMemberDefaultPassbookHandlerInfo($member_id);
                if (!$passbook_handler) {
                    return new result(false, 'No passbook handler.', null, errorCodesEnum::NO_ACCOUNT_HANDLER);
                }
                $request->payer_id = $passbook_handler['uid'];
                $request->payer_type = $passbook_handler['handler_type'];
                $request->payer_name = $passbook_handler['handler_name'];
                $request->payer_phone = $passbook_handler['handler_phone'];
                $request->payer_account = $passbook_handler['handler_account'];
                break;
            default :
                return new result(false, 'Not support way.', null, errorCodesEnum::NOT_SUPPORTED);
        }

        $request->member_id = $member_id;
        $request->type = requestRepaymentTypeEnum::BALANCE;
        $request->prepayment_apply_id = $apply_id;
        $request->repayment_way = $repayment_way;
        $request->request_remark = $remark;
        $request->amount = $prepayment_apply['total_payable_amount'];
        $request->currency = $prepayment_apply['currency'];
        $request->create_time = Now();
        $request->state = requestRepaymentStateEnum::CREATE;
        $insert1 = $request->insert();
        if (!$insert1->STS) {
            return new result(false, 'Request fail.', null, errorCodesEnum::DB_ERROR);
        }


        // 都成功后，如果是自动扣款的，马上执行
        if ($repayment_way == repaymentWayEnum::AUTO_DEDUCTION || $repayment_way == repaymentWayEnum::PASSBOOK) {
            $handler_info = array(
                'handler_id' => 0,
                'handler_name' => 'System',
                'handle_remark' => 'System handle',
                'handle_time' => Now()
            );
            $rt = self::requestRepaymentConfirmReceived($request->uid, array(), $handler_info);
            if (!$rt->STS) {
                return $rt;
            }
        }

        return new result(true, 'success',$prepayment_apply);




    }


    /** 获取提前还款的计划分组信息
     * @param $contract_id
     * @return array|result
     */
    public static function getContractSchemaDataForPrepaymentCalculation($contract_id)
    {
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }

        $today = date('Y-m-d');
        $left_schema = $m_contract->getContractUncompletedSchemas($contract_id);

        $overdue_schema = $next_schema = $last_schema = array();

        // 不是分期的重新计算
        $temp_schema = array();
        // 分割逾期的
        foreach ($left_schema as $v) {
            if ($v['receivable_date'] < $today) {
                $overdue_schema[] = $v;
            } else {
                $temp_schema[] = $v;
            }
        }

        // 当还的(就是算利息的)
        $next_repayment_date = null;
        if ($temp_schema[0]) {
            $next1 = $temp_schema[0];

            // 是否提前还清了
            // 最近应还一期的还款日期
            /*$sql = "select * from loan_installment_scheme where contract_id='$contract_id' and receivable_date>='$today' and
            state!='" . schemaStateTypeEnum::CANCEL . "' order by receivable_date asc ";
            $lately = $m_contract->reader->getRow($sql);*/

            $next_schema[] = $next1;
            unset($temp_schema[0]);
            // 判断是否差异5天以上
            $next_day = date('Y-m-d', strtotime($next1['receivable_date']));
            $next_repayment_date = $next_day;
            $seconds = strtotime($next_day) - strtotime($today);
            if (ceil($seconds / 86400) < 5) {

                if ($temp_schema[1]) {
                    $next2 = $temp_schema[1];
                    $next_schema[] = $next2;
                    $next_repayment_date = date('Y-m-d', strtotime($next2['receivable_date']));
                    unset($temp_schema[1]);
                }

            }



            /*if ($next1['receivable_date'] > $lately['receivable_date']) {
                // 已还，没有必还本息
                // 要有截止利息日
            } else {

                $next_schema[] = $next1;
                unset($temp_schema[0]);
                // 判断是否差异5天以上
                $next_day = date('Y-m-d', strtotime($next1['receivable_date']));
                $next_repayment_date = $next_day;
                $seconds = strtotime($next_day) - strtotime($today);
                if (ceil($seconds / 86400) < 5) {

                    if ($temp_schema[1]) {
                        $next2 = $temp_schema[1];
                        $next_schema[] = $next2;
                        $next_repayment_date = date('Y-m-d', strtotime($next2['receivable_date']));
                        unset($temp_schema[1]);
                    }

                }

            }*/


        }else{
            $next_repayment_date = date('Y-m-d',time()+global_settingClass::getLoanPrepaymentApplyValidDays()*86400);
        }

        // 剩下的计划
        if (count($temp_schema) >= 1) {
            $last_schema = array_values($temp_schema);  // 重置key
        }

        $return = array(
            'cutoff_date' => $next_repayment_date,
            'overdue_schema' => $overdue_schema,
            'current_schema' => $next_schema,
            'remain_schema' => $last_schema
        );

        return new result(true,'success',$return);


    }



    /** 还款请求查账确认
     * @param $request_id
     * @param $extend_info  // 各方式的详细信息
     * @param $handler_info
     * @return result
     */
    public static function requestRepaymentConfirmReceived($request_id, $extend_info, $handler_info)
    {

        $m_request = new loan_request_repaymentModel();
        $request = $m_request->getRow($request_id);
        if (!$request) {
            return new result(false, 'No request info', null, errorCodesEnum::INVALID_PARAM);
        }

        if ($request->state == requestRepaymentStateEnum::SUCCESS) {
            return new result(true, 'success');
        }

        $member_id = $request['member_id'];

        // 处理合同
        if ($request->type == requestRepaymentTypeEnum::SCHEME) {  // 计划还款

            // 查询还款的计划列表
            $schema_list = (new loan_request_repayment_detailModel())->getRows(array(
                'request_id' => $request_id
            ));
            if (count($schema_list) < 1) {
                return new result(true, 'success');
            }


            $schema_ids = array();
            foreach ($schema_list as $v) {
                $schema_ids[] = $v['scheme_id'];
            }

            switch ($request->repayment_way) {
                case repaymentWayEnum::CASH :
                    $cashier_id = $extend_info['cashier_id'];
                    $amount = round($extend_info['amount']);
                    $currency = $extend_info['currency'];
                    $multi_currency = $extend_info['multi_currency'];
                    $rt = loanRepaymentWorkerClass::schemasRepaymentByCash($member_id,$cashier_id,$schema_ids,$amount,$currency,$multi_currency);
                    if (!$rt->STS) {
                        return $rt;
                    }
                    break;
                case repaymentWayEnum::AUTO_DEDUCTION :

                    $rt = loanRepaymentWorkerClass::schemasRepaymentByPartner($schema_ids, $request->payer_id);
                    if (!$rt->STS) {
                        return $rt;
                    }
                    break;
                case repaymentWayEnum::PASSBOOK:
                    $rt = loanRepaymentWorkerClass::schemasRepaymentByBalance($schema_ids);
                    if (!$rt->STS) {
                        return $rt;
                    }
                    break;
                case repaymentWayEnum::BANK_TRANSFER :
                    $cashier_id = $extend_info['cashier_id'];
                    $sys_bank_id = $extend_info['sys_bank_id'];
                    $amount = round($extend_info['amount']);
                    $currency = $extend_info['currency'];
                    $rt = loanRepaymentWorkerClass::schemasRepaymentByBank($cashier_id,$member_id,$schema_ids,$sys_bank_id,$amount,$currency);
                    if (!$rt->STS) {
                        return $rt;
                    }
                    break;
                default :
                    return new result(false,'Unknown way.',null,errorCodesEnum::NOT_SUPPORTED);

            }


        } else {

            // 提前还款
            $apply_id = $request->prepayment_apply_id;
            switch ($request->repayment_way) {
                case repaymentWayEnum::CASH :
                    return new result(false, 'Un completed.', null, errorCodesEnum::UN_MATCH_OPERATION);
                    break;
                case repaymentWayEnum::AUTO_DEDUCTION :

                    $rt = loanRepaymentWorkerClass::prepaymentByPartner($apply_id, $request->payer_id);
                    if (!$rt->STS) {
                        return $rt;
                    }
                    break;
                case repaymentWayEnum::PASSBOOK:
                    $rt = loanRepaymentWorkerClass::prepaymentByBalance($apply_id);
                    if (!$rt->STS) {
                        return $rt;
                    }
                    break;
                case repaymentWayEnum::BANK_TRANSFER :
                    return new result(false, 'Un completed.', null, errorCodesEnum::UN_MATCH_OPERATION);
                    break;
                default :
                    return new result(false,'Unknown way.',null,errorCodesEnum::NOT_SUPPORTED);

            }

        }

        $request->handler_id = $handler_info['handler_id'];
        $request->handler_name = $handler_info['handler_name'];
        $request->handle_remark = $handler_info['handle_remark'];
        $request->handle_time = Now();
        $request->state = requestRepaymentStateEnum::SUCCESS;
        $up = $request->update();
        if (!$up->STS) {
            return new result(false, 'Update request fail.', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');


    }


    /** 提前还款后重新插入还款计划
     * @param $contract_info
     * @param $remain_schema
     * @return result
     */
    public static function insertNewInstallmentSchemaAfterPrepayment($contract_info,$remain_schema)
    {

        $contract_id = $contract_info['uid'];


        $grace_days = $contract_info['grace_days'];
        $account_handler_id = $contract_info['account_handler_id'];
        $schema_state = schemaStateTypeEnum::CREATE;

        $new_payment_schema = array();
        // 插入新计划
        if( count($remain_schema) > 0 ){


            $m_payment_schema = new loan_installment_schemeModel();

            $sql = "select max(scheme_idx) from loan_installment_scheme where contract_id='".$contract_id."' ";
            $max_schema_idx =  $m_payment_schema->reader->getOne($sql) ?:0;


            // 单一语句执行，循环执行速度超鸡慢
            $field_array = array(
                'contract_id',
                'scheme_idx',
                'scheme_name',
                'initial_principal',
                'interest_date',
                'receivable_date',
                'penalty_start_date',
                'receivable_principal',
                'receivable_interest',
                'receivable_operation_fee',
                'receivable_admin_fee',
                'ref_amount',
                'amount',
                'account_handler_id',
                'state',
                'create_time'
            );
            $insert_sql = "insert into loan_installment_scheme(".join(',',$field_array).") values  ";
            $sql_array = array();


            $schema_interest_date = date('Y-m-d');
            $counter = 1;
            reset($payment_schema);
            $scheme_idx = $max_schema_idx+1;

            foreach( $payment_schema as $instalment_schema ){

                // 严格按照上面定义的字段插入顺序
                $temp = array(
                    'contract_id' => $contract_id,
                    'scheme_idx' => $scheme_idx,
                    'scheme_name' => 'Period '.$scheme_idx,
                    'initial_principal' => $instalment_schema['initial_principal'],
                    'interest_date' => $schema_interest_date,
                    'receivable_date' => $instalment_schema['receive_date'],
                    'penalty_start_date' => date('Y-m-d',strtotime($instalment_schema['receive_date'])+$grace_days*24*3600),
                    'receivable_principal' => $instalment_schema['receivable_principal'],
                    'receivable_interest' => $instalment_schema['receivable_interest'],
                    'receivable_operation_fee' => $instalment_schema['receivable_operation_fee'],
                    'receivable_admin_fee' => 0,
                    'ref_amount' => $instalment_schema['amount'],
                    'amount' => $instalment_schema['amount'],
                    'account_handler_id' => $account_handler_id,
                    'state' => $schema_state,
                    'create_time' => Now()
                );
                $str = "( '".$temp['contract_id']."',";
                $str .= "'".$temp['scheme_idx']."',";
                $str .= "'".$temp['scheme_name']."',";
                $str .= "'".$temp['initial_principal']."',";
                $str .= "'".$temp['interest_date']."',";
                $str .= "'".$temp['receivable_date']."',";
                $str .= "'".$temp['penalty_start_date']."',";
                $str .= "'".$temp['receivable_principal']."',";
                $str .= "'".$temp['receivable_interest']."',";
                $str .= "'".$temp['receivable_operation_fee']."',";
                $str .= "'".$temp['receivable_admin_fee']."',";
                $str .= "'".$temp['ref_amount']."',";
                $str .= "'".$temp['amount']."',";
                $str .= "'".$temp['account_handler_id']."',";
                $str .= "'".$temp['state']."',";
                $str .= "'".$temp['create_time']."' )";

                $sql_array[] = $str;
                $new_payment_schema[] = $temp;
                $schema_interest_date = $temp['receivable_date'];
                $counter++;  // 放到最后处理
                $scheme_idx++;
            }

            // 拼接sql
            $insert_sql .= trim(join(',',$sql_array),',');

            $re = $m_payment_schema->conn->execute($insert_sql);
            if( !$re->STS ){

                return new result(false,'Insert payment schema fail: '.$re->MSG,null,errorCodesEnum::DB_ERROR);
            }

        }

        return new result(true,'success',$new_payment_schema);
    }


    /** 合同还款完成处理（complete）
     * @param $contract_id
     * @return result
     */
    public static function contractComplete($contract_id)
    {
        $contract_id = intval($contract_id);
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if (!$contract) {
            return new result(false, 'No contract', null, errorCodesEnum::NO_CONTRACT);
        }


        // 计算合同最终罚金
        $sql = "select sum(settle_penalty) from loan_installment_scheme where contract_id='$contract_id' and state!='" . schemaStateTypeEnum::CANCEL . "' ";
        $penalty = $m_contract->reader->getOne($sql);
        $penalty = round($penalty, 2);

        // 更新合同状态
        $contract->receivable_penalty = $penalty;
        $contract->state = loanContractStateEnum::COMPLETE;
        $contract->finish_time = Now();
        $up = $contract->update();
        if (!$up->STS) {
            return new result(false, 'Update fail', null, errorCodesEnum::DB_ERROR);
        }

        //更新相关保险的合同
        $sql = "update insurance_contract set state='" . insuranceContractStateEnum::COMPLETE . "' where loan_contract_id='$contract_id' ";
        $up = $m_contract->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Update fail', null, errorCodesEnum::DB_ERROR);
        }

        $member_info = self::getLoanContractMemberInfo($contract_id);
        if (!$member_info) {
            return new result(false, 'No member info.', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $member_id = $member_info['uid'];

        // 归还信用
        $product_info = (new loan_productModel())->getRow($contract->product_id);
        if ($product_info && $product_info['category'] == loanProductCategoryEnum::CREDIT_LOAN) {
            //检查对应的grant的状态
            $grant_id=$contract->credit_grant_id;
            $chk_grant=(new member_credit_grantModel())->find(array("uid"=>$grant_id));
            if(!$chk_grant || $chk_grant['state']!=commonApproveStateEnum::PASS){
                //这些情况不还了
            }else{

                // 计算信用值
                if( $contract['credit_amount'] > 0 ){
                    $credit_amount = $contract['credit_amount'];
                }else{
                    // 兼容原来的
                    $rate = global_settingClass::getCurrencyRateBetween($contract['currency'],currencyEnum::USD);
                    $credit_amount = ceil($contract['apply_amount']*$rate);
                }
                // 信用贷还款增加信用余额
                $re = member_creditClass::addCreditBalance(
                    creditEventTypeEnum::CREDIT_LOAN,
                    $contract->member_credit_category_id,
                    $credit_amount,
                    $contract['currency'],
                    'Contract Completed: ' . $contract->contract_sn);
                if (!$re->STS) {
                    return $re;
                }

            }

        }


        $title = 'Loan Contract Completed';
        $body = "Congratulations! You have paid off all your loans for your loan contract(contract sn: " . $contract->contract_sn . ")!";
        $send = member_messageClass::sendSystemMessage($member_id, $title, $body);

        return new result(true, 'success', $contract);
    }

    /** 客户贷款合同数统计
     * @param $account_id
     * @param int $type
     * @return int
     */
    public static function getLoanAccountContractNumSummary($account_id, $type = 0)
    {
        $account_id = intval($account_id);
        $r = new ormReader();
        $num = 0;
        switch ($type) {
            case 0:
                // all
                $sql = "select count(*) from loan_contract where account_id='$account_id' and state>='" . loanContractStateEnum::PENDING_APPROVAL . "' ";
                $num = $r->getOne($sql);
                break;
            case 1:
                // 正常执行的(含逾期合同)
                $sql = "select count(*) from loan_contract where account_id='$account_id' and state>='" . loanContractStateEnum::PENDING_DISBURSE . "'
                and state < '" . loanContractStateEnum::COMPLETE . "' ";
                $num = $r->getOne($sql);
                break;
            case 2:
                // 延期的
                $sql = "select count(DISTINCT s.contract_id) from loan_installment_scheme s inner join loan_contract c on c.uid=s.contract_id and c.account_id='$account_id' and 
                c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "' and c.state<'" . loanContractStateEnum::COMPLETE . "'
                and s.state!='" . schemaStateTypeEnum::CANCEL . "' and  s.state!='" . schemaStateTypeEnum::COMPLETE . "' and s.receivable_date<'" . date('Y-m-d') . "' ";
                $num = $r->getOne($sql);
                break;
            case 3:
                // 被拒绝的
                $sql = "select count(*) from loan_contract where account_id='$account_id' and state=" . qstr(loanContractStateEnum::REFUSED);
                $num = $r->getOne($sql);
                break;
            case 4:
                // write off
                $sql = "select count(*) from loan_contract where account_id='$account_id' and state='" . loanContractStateEnum::WRITE_OFF . "' ";
                $num = $r->getOne($sql);
                break;
            case 5:
                // 正常完成的
                $sql = "select count(*) from loan_contract where account_id='$account_id' and state='" . loanContractStateEnum::COMPLETE . "' ";
                $num = $r->getOne($sql);
                break;
            case 6:
                // 待审核的
                $sql = "select count(*) from loan_contract where account_id='$account_id' and state='" . loanContractStateEnum::PENDING_APPROVAL . "' ";
                $num = $r->getOne($sql);
                break;
            case 7:
                // 正常执行的(含逾期合同)
                $sql = "select count(*) from loan_contract where account_id='$account_id' and state>='" . loanContractStateEnum::PENDING_DISBURSE . "'";
                $num = $r->getOne($sql);
                break;
            default:
                break;
        }
        return $num;
    }





    /** 贷款合同添加担保人
     * @param $contract_id
     * @param $guarantor_list
     * @return result
     */
    public static function contractAddGuarantor($contract_id, $guarantor_list)
    {
        $contract_id = intval($contract_id);
        if (empty($guarantor_list) || !is_array($guarantor_list)) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $values = array();
        $in_str = implode(',', $guarantor_list);
        $sql = "select * from client_member where uid in ($in_str)";
        $member_list = $m_member->reader->getRows($sql);

        $sql = "insert into loan_contract_guarantor(contract_id,guarantor_id,guarantor_name,update_time) values ";
        foreach ($member_list as $member) {
            $member_name = $member['display_name'] ?: $member['login_code'];
            $temp = "('$contract_id','" . $member['uid'] . "','$member_name','" . Now() . "')";
            $values[] = $temp;
        }
        $sql .= implode(',', $values);
        $ret = $m_member->conn->execute($sql);
        if (!$ret->STS) {
            return new result(false, 'Db error ' . $ret->MSG, null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success');
    }

    public static function contractAddMortgage($contract_id, $mortgage_list)
    {
        $contract_id = intval($contract_id);
        if (empty($mortgage_list) || !is_array($mortgage_list)) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m = new member_assetsModel();
        $in_str = implode(',', $mortgage_list);
        $sql = "select * from member_assets where uid in ($in_str) ";
        $asset_list = $m->reader->getRows($sql);

        $values = array();
        $sql = "insert into loan_contract_mortgage(contract_id,asset_id,update_time) values ";
        foreach ($asset_list as $asset) {
            $temp = "('$contract_id','" . $asset['uid'] . "','" . Now() . "')";
            $values[] = $temp;
        }

        $sql .= implode(',', $values);
        $insert = $m->conn->execute($sql);
        if (!$insert->STS) {
            return new result(false, 'Db error ' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        $now = Now();
        $up_sql = "update member_assets set mortgage_state='1',mortgage_time='$now' where uid in ( $in_str ) ";
        $up = $m->conn->execute($up_sql);
        if (!$up->STS) {
            return new result(false, 'Db error ' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');


    }

    public static function contractAddFiles($contract_id, $files_list)
    {
        $contract_id = intval($contract_id);
        if (empty($mortgage_list) || !is_array($mortgage_list)) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m = new loan_contract_filesModel();

        $sql = "insert into loan_contract_files(contract_id,file_type,file_name,file_path,create_time) values ";
        $values = array();
        foreach ($files_list as $file_path) {
            $extend_info = pathinfo($file_path);
            $file_type = $extend_info['extension'];
            $file_name = $extend_info['filename'];
            $time = Now();
            $temp = "('$contract_id','$file_type','$file_name','$file_path','$time')";
            $values[] = $temp;
        }
        $sql .= implode(',', $values);
        $insert = $m->conn->execute($sql);
        if (!$insert->STS) {
            return new result(false, 'Db error ' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');
    }


    /** 合同进入执行状态逻辑
     * @param $contract_id
     */
    public static function contractConfirmToExecute($contract_id)
    {
        $m_contract = new loan_contractModel();
        $sql = "update loan_contract set state='" . loanContractStateEnum::PENDING_DISBURSE . "',update_time='" . Now() . "' where uid ='$contract_id' ";
        $up = $m_contract->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Db error ' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success');
    }

    /**
     * 获取提前还款申请
     * @param $pageNumber
     * @param $pageSize
     * @param $filter
     * @return array
     */
    public function getLoanPrepaymentApplyList($pageNumber, $pageSize, $filter)
    {
        $r = new ormReader();
        $sql = "SELECT lpa.*,lc.contract_sn,lc.product_name,lc.sub_product_name,cm.login_code,cm.display_name FROM loan_prepayment_apply lpa"
            . " INNER JOIN loan_contract lc ON lpa.contract_id = lc.uid"
            . " INNER JOIN loan_account la ON lc.account_id = la.uid"
            . " INNER JOIN client_member cm ON la.obj_guid = cm.obj_guid WHERE 1 = 1";
        if (intval($filter['state']) == prepaymentApplyStateEnum::APPROVED) {
            $sql .= " AND lpa.state >= " . prepaymentApplyStateEnum::APPROVED;
        } else {
            $sql .= " AND lpa.state = " . intval($filter['state']);
        }

        if (trim($filter['search_text'])) {
            $sql .= " AND (lc.contract_sn = " . qstr(trim($filter['search_text'])) . " OR cm.login_code LIKE '%" . qstr2(trim($filter['search_text'])) . "')";
        }
        $sql .= " ORDER BY lpa.uid DESC";

        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 接任务
     * @param $uid
     * @param $auditor_id
     * @return result
     */
    public function getRepaymentRequestTask($uid, $auditor_id)
    {
        $uid = intval($uid);
        $auditor_id = intval($auditor_id);
        $m_loan_prepayment_apply = M('loan_prepayment_apply');
        $row = $m_loan_prepayment_apply->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id.');
        }
        if ($row->state > prepaymentApplyStateEnum::AUDITING) {
            return new result(false, 'Audited.');
        } else if ($row->state == prepaymentApplyStateEnum::AUDITING) {
            if ($row->auditor_id == $auditor_id) {
                return new result(true);
            } else {
                return new result(false, 'Others are already reviewing it.');
            }
        } else {
            $auditor = new objectUserClass($auditor_id);
            $chk = $auditor->checkValid();
            if (!$chk->STS) {
                return $chk;
            }

            $row->state = prepaymentApplyStateEnum::AUDITING;
            $row->auditor_id = $auditor_id;
            $row->auditor_name = $auditor->user_name;
            $row->update_time = Now();
            $rt = $row->update();
            return $rt;
        }
    }

    /**
     * 取消任务
     * @param $uid
     * @param $auditor_id
     * @return result
     */
    public function abandonPrepaymentRequestTask($uid, $auditor_id)
    {
        $uid = intval($uid);
        $auditor_id = intval($auditor_id);
        $m_loan_prepayment_apply = M('loan_prepayment_apply');
        $row = $m_loan_prepayment_apply->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id.');
        }

        if ($row->state != prepaymentApplyStateEnum::AUDITING) {
            return new result(false, 'It\'s not being reviewed.');
        }

        if ($row->auditor_id != $auditor_id) {
            return new result(false, 'Others are already reviewing it.');
        }

        $row->state = prepaymentApplyStateEnum::CREATE;
        $row->auditor_id = '';
        $row->auditor_name = '';
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Abandon Successful.');
        } else {
            return new result(true, 'Abandon Failed.');
        }
    }

    /**
     * 获取提前还款申请详情
     * @param $uid
     * @return ormDataRow
     */
    public function getRepaymentRequestDetail($uid)
    {
        $uid = intval($uid);
        $r = new ormReader();
        $sql = "SELECT lpa.*,lc.contract_sn,lc.product_name,lc.sub_product_name,cm.login_code,cm.display_name FROM loan_prepayment_apply lpa"
            . " INNER JOIN loan_contract lc ON lpa.contract_id = lc.uid"
            . " INNER JOIN loan_account la ON lc.account_id = la.uid"
            . " INNER JOIN client_member cm ON la.obj_guid = cm.obj_guid WHERE lpa.uid = $uid";
        $request_detail = $r->getRow($sql);
        return $request_detail;
    }

    /**
     * @param $uid
     * @param $state
     * @param $audit_remark
     * @param $auditor_id
     * @return result
     */
    public function auditPrepaymentRequest($uid, $state, $audit_remark, $auditor_id)
    {
        $uid = intval($uid);
        $state = intval($state);
        $audit_remark = trim($audit_remark);
        $auditor_id = intval($auditor_id);
        $m_loan_prepayment_apply = M('loan_prepayment_apply');
        $row = $m_loan_prepayment_apply->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id.');
        }

        if ($row->state != prepaymentApplyStateEnum::AUDITING) {
            return new result(false, 'It\'s not being reviewed.');
        }

        if ($row->auditor_id != $auditor_id) {
            return new result(false, 'Others are already reviewing it.');
        }

        $row->state = $state == prepaymentApplyStateEnum::APPROVED ? $state : prepaymentApplyStateEnum::DISAPPROVE;
        $row->audit_remark = $audit_remark;
        $row->audit_time = Now();
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Audit Successful.');
        } else {
            return new result(true, 'Audit Failed.');
        }

    }
}