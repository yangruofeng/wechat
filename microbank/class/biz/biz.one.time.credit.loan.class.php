<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/29
 * Time: 14:27
 */
class bizOneTimeCreditLoanClass extends bizBaseClass
{
    public function __construct($scene_code)
    {

        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!',errorCodesEnum::FUNCTION_CLOSED);
        }
        $this->scene_code = bizSceneEnum::COUNTER;  // 暂时只会在counter处理
        $this->biz_code = bizCodeEnum::ONE_TIME_CREDIT_LOAN;
        $this->bizModel = new biz_one_time_credit_loanModel();
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


    /** 1.获取客户可贷款的一次性产品列表
     * @param $member_id
     * @return array
     */
    public function getMemberOneTimeLoanList($member_id)
    {
        $r = new ormReader();
        $sql = "select mcc.*,sp.interest_type,sp.repayment_type,sp.sub_product_code,sp.sub_product_name from member_credit_category mcc left join loan_category lc
        on mcc.category_id=lc.uid inner join loan_sub_product sp on sp.uid=mcc.sub_product_id
        where lc.is_close='0' and mcc.is_close='0' and mcc.is_one_time='1'
        and mcc.member_id=".qstr($member_id);
        $rows = $r->getRows($sql);
        $list = array();
        if( count($rows) < 1 ){
            return $list;
        }

        // 获取授信信息
        $grant_info = member_credit_grantClass::getMemberLastGrantInfo($member_id);
        if( !$grant_info ){
            return $list;
        }


        $left_days = member_creditClass::getMemberCreditLeftDays($member_id);
        // 换成月
        $left_month = ceil($left_days/30);

        $loan_period = $left_month;
        $loan_period_unit = loanPeriodUnitEnum::MONTH;


        // 构造合同参数
        foreach( $rows as $v ){
            $list[$v['uid']] = array(
                'member_credit_category' => $v,
                'member_id' => $member_id,
                'product_name' => $v['alias'],
                'product_code' => $v['sub_product_code'],
                'member_credit_category_id' => $v['uid'],
                'sub_product_id' => $v['sub_product_id'],
                'grant_id' => $grant_info['uid'],
                'loan_period'=>$loan_period,
                'loan_period_unit' => $loan_period_unit,
                'repayment_type' => $v['interest_type'],
                'repayment_period' => $v['repayment_type'],
                'credit'=>$v['credit'],
                'credit_balance'=>$v['credit_balance'],
                'credit_usd'=>$v['credit_usd'],
                'credit_usd_balance'=>$v['credit_usd_balance'],
                'credit_khr'=>$v['credit_khr'],
                'credit_khr_balance'=>$v['credit_khr_balance'],
                'contract_param' => array()
            );
            if($v['credit_usd_balance']>0){
                $list[$v['uid']]['contract_param'][currencyEnum::USD]= array(
                    'member_id' => $member_id,
                    'product_id' => $v['uid'],
                    'amount' => $v['credit_usd_balance'],
                    'currency' => currencyEnum::USD,
                    'loan_period' => $loan_period,
                    'loan_period_unit' => $loan_period_unit,
                    'repayment_type' => $v['interest_type'],
                    'repayment_period' => $v['repayment_type'],
                    'credit_amount' => $v['credit_usd_balance']
                );
            }
            if($v['credit_khr_balance']>0){
                $list[$v['uid']]['contract_param'][currencyEnum::KHR]= array(
                    'member_id' => $member_id,
                    'product_id' => $v['uid'],
                    'amount' => $v['credit_khr_balance'],
                    'currency' => currencyEnum::KHR,
                    'loan_period' => $loan_period,
                    'loan_period_unit' => $loan_period_unit,
                    'repayment_type' => $v['interest_type'],
                    'repayment_period' => $v['repayment_type'],
                    'credit_amount' => $v['credit_khr_balance']
                );
            }
        }
        return $list;
    }


    public function insertLoanTask($user_id,$member_id,$member_category_id,$currency)
    {


        $m_biz = $this->bizModel;
        // 判断下是否存在待处理的任务
        $sql = "select count(*) cnt from biz_one_time_credit_loan where member_id=".qstr($member_id).
        " and member_credit_category_id=".qstr($member_category_id)." and currency=".qstr($currency)." and  state in 
        ('".bizStateEnum::APPROVED."','".bizStateEnum::PENDING_CONFIRM."') ";
        $num = $m_biz->reader->getOne($sql);
        if( $num > 0 ){
            // todo 取消任务的限制先
            //return new result(false,'Exists pending disburse contract task.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }


        $list = $this->getMemberOneTimeLoanList($member_id);
        $data = $list[$member_category_id];
        if( !$data ){
            return new result(false,'Invalid product:'.$member_category_id,null,errorCodesEnum::INVALID_PARAM);
        }
        if($currency==currencyEnum::USD){
            $credit_amount=$data['credit_usd_balance'];
        }else{
            $credit_amount=$data['credit_khr_balance'];
        }
        if(!$credit_amount){
            return new result(false,"No Credit Balance To Loan For ".$currency);
        }


        $userObj = new objectUserClass($user_id);
        $chk = $userObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }


        $row = $m_biz->newRow();
        $row->scene_code = $this->scene_code;
        $row->biz_code = $this->biz_code;
        $row->member_id = $member_id;
        $row->grant_id = $data['grant_id'];
        $row->member_credit_category_id = $data['member_credit_category_id'];
        $row->currency=$currency;
        $row->credit_amount=$credit_amount;
        $row->state = bizStateEnum::APPROVED; // todo 暂时自动审批
        $row->operator_id = 0;
        $row->operator_name = 'System';
        $row->operate_remark = 'System auto approved';
        $row->create_time = Now();
        $row->branch_id = $userObj->branch_id;
        $row->cashier_id = $user_id;
        $row->cashier_name = $userObj->user_name;
        $insert = $row->insert();
        if( !$insert->STS ){
            return new result(false,'Insert task fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'biz_id' => $row->uid
        ));

    }


    /** 审批任务，现在是自动审批
     * @param $biz_id
     * @param $user_id
     * @param $is_ok
     * @param $remark
     * @return result
     */
    public function approveTask($biz_id,$user_id,$is_ok,$remark)
    {
        $m_biz = $this->bizModel;
        $userObj = new objectUserClass($user_id);
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid biz id:'.$biz_id,null,errorCodesEnum::INVALID_PARAM);
        }
        if( $is_ok ){
            $biz->state = bizStateEnum::APPROVED;
        }else{
            $biz->state = bizStateEnum::REJECT;
        }
        $biz->operator_id = $user_id;
        $biz->operator_name = $userObj->user_name;
        $biz->operate_remark = $remark;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Approve fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success',array(
            'biz_id' => $biz_id
        ));

    }


    /** 放款开始，就是创建合同草稿
     * @param $biz_id
     * @return result
     */
    public function disburseStart($biz_id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid biz id:'.$biz_id,null,errorCodesEnum::INVALID_PARAM);
        }

        if( $biz->state == bizStateEnum::DONE ){
            return new result(false,'Have done.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        // 已经创建过合同了
        if( $biz->contract_id ){
            $rt = loan_contractClass::getLoanContractDetailInfo($biz->contract_id);
            if( !$rt->STS ){
                return new result(false,'Not found contract info:'.$biz->contract_id);
            }
            $rt->DATA['biz_id'] = $biz_id;
            return $rt;
        }


        $list = $this->getMemberOneTimeLoanList($biz->member_id);
        $loan_data = $list[$biz->member_credit_category_id];
        if( !$loan_data  ){
            return new result(false,'Invalid product:'.$biz->member_credit_category_id,null,errorCodesEnum::LOAN_PRODUCT_NX);
        }
        $member_id = $biz->member_id;

        $contract_param = $loan_data['contract_param'][$biz['currency']];
        if(!$contract_param){
            return new result(false,'Invalid product:'.$biz->member_credit_category_id,null,errorCodesEnum::LOAN_PRODUCT_NX);
        }

        $member_category_id = $loan_data['member_credit_category_id'];


        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            // todo 这种方式的全是信用贷产品的
            $extend_params = array(
                'creator_id' => $biz->cashier_id,
                'creator_name' => $biz->cashier_name,
                'branch_id' => $biz->branch_id
            );

            $rt = (new credit_loanClass())->withdraw($member_category_id,$member_id,
                $contract_param['amount'],$contract_param['loan_period'],$contract_param['loan_period_unit'],$contract_param['currency'],
                contractCreateSourceEnum::COUNTER,$extend_params);

            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }

            $contract_data = $rt->DATA;
            $contract_id = $contract_data['contract_id'];
            if( $contract_id <= 0 ){
                $conn->rollback();
                return new result(false,'Invalid contract id:'.$contract_id);
            }

            $conn->submitTransaction();

            // 更新biz

            $biz->contract_id = $contract_id;
            $biz->credit_amount = $contract_param['amount'];  // 更新为实际贷款金额
            $biz->state = bizStateEnum::PENDING_CONFIRM;
            $biz->update_time = Now();
            $up = $biz->update();
            if(  !$up->STS ){
                $conn->rollback();
                return new result(false,'Update fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
            }


            $contract_data['biz_id'] = $biz_id;

            return new result(true,'success',$contract_data);

        }catch( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNKNOWN_ERROR);
        }

    }


    /** 确认放款，合同进入执行状态
     * @param $biz_id
     * @return result
     */
    public function disburseConfirm($biz_id,$is_manual_disburse=false)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid biz id:'.$biz_id,null,errorCodesEnum::INVALID_PARAM);
        }
        if( $biz->state == bizStateEnum::DONE ){
            return new result(true,'success');
        }

        $contract_id = $biz->contract_id;
        if( !$contract_id ){
            return new result(false,'Task have not create contract yet!',null,errorCodesEnum::UN_MATCH_OPERATION);
        }
        $contract_info = (new loan_contractModel())->find(array('uid'=>$contract_id));
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $ret_data = array(
                'is_disburse_success' => 0,
                'contract_info' => $contract_info
            );

            if( $is_manual_disburse ){
                // 需要将放款计划状态更新为手工触发执行，脚本不自动执行
                $sql = "update loan_disbursement_scheme set state=".qstr(schemaStateTypeEnum::PENDING_MANUAL_EXECUTE)."
                where contract_id='$contract_id' and state=".qstr(schemaStateTypeEnum::CREATE);
                $up = $conn->execute($sql);
                if( !$up->STS ){
                    $conn->rollback();
                    return new result(false,'Update schema state fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
                }
            }

            $rt = loan_baseClass::confirmContractToExecute($contract_id);
            if( !$rt->STS ){
                $conn->rollback();
                $rt->DATA = $ret_data;
                return $rt;
            }

            // 更新biz
            $biz->state = bizStateEnum::DONE;
            $biz->update_time = Now();
            $up = $biz->update();
            if( !$up->STS ){
                $conn->rollback();
                return new result(false,'Update fail:'.$up->MSG,$ret_data,errorCodesEnum::DB_ERROR);
            }

            $conn->submitTransaction();

            $is_disburse_success = 0;
            // 马上进行放款
            if( $is_manual_disburse ){

                $is_disburse_success = 1;
                $m_schema = new loan_disbursement_schemeModel();
                $schemas = $m_schema->getPendingManualDisburseSchemaOfContract($contract_id);
                foreach( $schemas as $v ){
                    $rt = loanDisbursementWorkerClass::schemaDisburse($v['uid']);
                    if( !$rt->STS ){
                        $is_disburse_success = 0;
                        // 如果手工放款失败了，要将计划改成自动放款
                        $sql = "update loan_disbursement_scheme set state=".qstr(schemaStateTypeEnum::CREATE)."
                          where  uid =".qstr($v['uid']);
                        $up = $conn->execute($sql);
                    }
                }
            }

            return new result(true,'success',array(
                'is_disburse_success' => $is_disburse_success,
                'contract_info' => $contract_info
            ));


        }catch( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNKNOWN_ERROR);
        }


    }

    /** 取消放款
     * @param $biz_id
     * @return result
     */
    public function disburseCancel($biz_id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid biz id:'.$biz_id,null,errorCodesEnum::INVALID_PARAM);
        }
        if( $biz->state == bizStateEnum::CANCEL ){
            return new result(true,'success');
        }

        if( $biz->state == bizStateEnum::DONE ){
            return new result(false,'Can not cancel.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $contract_id = $biz->contract_id;

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            if( $contract_id ){
                $rt = loan_baseClass::cancelContract($contract_id);
                if( !$rt->STS ){
                    $conn->rollback();
                    return $rt;
                }
            }


            // 更新biz
            $biz->state = bizStateEnum::CANCEL;
            $biz->update_time = Now();
            $up = $biz->update();
            if( !$up->STS ){
                $conn->rollback();
                return new result(false,'Update fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
            }

            $conn->submitTransaction();

            return new result(true,'success');


        }catch( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNKNOWN_ERROR);
        }

    }


    public function isNeedCTApprove($biz_id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
           return false;
        }

        $member_category = (new member_credit_categoryModel())->find(array(
            'uid' => $biz->member_credit_category_id
        ));

        $multi_currency = array(
            currencyEnum::USD => $member_category['credit_balance']
        );
        $branch_id = $biz['branch_id'];
        // 注意，使用的是和贷款一样的设置code
        return $this->checkCounterBizIsNeedCTApproveByCode(bizCodeEnum::MEMBER_CREATE_LOAN_CONTRACT,$multi_currency,$branch_id);

    }

    public function checkMemberTradingPassword($biz_id, $password)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $member_id = $biz->member_id;
        $objectMember = new objectMemberClass($member_id);
        if( $password != md5($objectMember->trading_password) ){
            return new result(false,'Password error',null,errorCodesEnum::PASSWORD_ERROR);
        }

       /* $biz->member_trading_password = $objectMember->trading_password;
        $biz->update_time = Now();
        if( $biz->getRowState() == ormDataRow::ROWSTATE_MODIFIED ){
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Update biz client fail.',null,errorCodesEnum::DB_ERROR);
            }
        }*/

        return new result(true,'success',array(
            'biz_id' => $biz_id,
            'member_trading_password' => $objectMember->trading_password
        ));

    }

    public function checkTellerPassword($biz_id, $card_no, $key)
    {
        $m = $this->bizModel;
        $biz = $m->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info.',null,errorCodesEnum::NO_DATA);
        }
        $userObj = new objectUserClass($biz->cashier_id);
        $branch_id = $userObj->branch_id;
        $chk = $this->checkTellerAuth($biz->cashier_id,$branch_id,$card_no,$key);
        if( !$chk->STS ){
            return $chk;
        }
        /*$biz->cashier_name = $userObj->user_name;
        $biz->cashier_trading_password = $userObj->trading_password;
        $biz->update_time = Now();
        if( $biz->getRowState() == ormDataRow::ROWSTATE_MODIFIED ){  // getRowState
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Update teller info fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
            }
        }*/

        return new result(true,'success',array(
            'biz_id' => $biz_id,
            'cashier_id' => $biz->cashier_id,
            'cashier_name' => $userObj->user_name,
            'cashier_trading_password' => $userObj->trading_password
        ));
    }


    public function checkChiefTellerPassword($biz_id, $card_no, $key)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if (!$biz) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $cashier_id = $biz->cashier_id;
        $cashierObj = new objectUserClass($cashier_id);

        $branch_id = $cashierObj->branch_id;
        $rt = $this->checkChiefTellerAuth($branch_id, $card_no, $key);
        if( !$rt->STS ){
            return $rt;
        }
        $ct_id = $rt->DATA;
        $ctObj = new objectUserClass($ct_id);

       /* $biz->bm_id = $ct_id;
        $biz->bm_name = $ctObj->user_name;
        $biz->bm_trading_password = $ctObj->trading_password;
        $biz->update_time = Now();
        if( $biz->getRowState() == ormDataRow::ROWSTATE_MODIFIED ){
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Update chief teller info fail.',null,errorCodesEnum::DB_ERROR);
            }
        }*/

        return new result(true,'success',array(
            'biz_id' => $biz_id,
            'bm_id' => $ct_id,
            'bm_name' => $ctObj->user_name,
            'bm_trading_password' => $ctObj->trading_password
        ));
    }


    public function updateCheckInfo($biz_id,$update_arr)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info:'.$biz_id,null,errorCodesEnum::NO_DATA);
        }
        $update_arr['uid'] = $biz_id;
        $update_arr['update_time'] = Now();
        $up = $m_biz->update($update_arr);
        if( !$up->STS ){
            return new result(false,'Update info fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success',array(
            'biz_id' => $biz_id
        ));
    }

    /** 录入客户信息
     * @param $biz_id
     * @param $member_image
     * @return result
     */
    public function insertMemberInfo($biz_id,$member_image)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info:'.$biz_id,null,errorCodesEnum::NO_DATA);
        }
        $biz->member_image = $member_image;
        $biz->update_time = Now();
        $up = $biz->update();

        $m_image = new biz_scene_imageModel();
        $insert = $m_image->insertSceneImage($biz->member_id,$member_image,$this->biz_code,$this->scene_code);
        if( !$insert->STS  ){
            return $insert;
        }


        $biz->biz_id = $biz->uid;
        return new result(true,'success',array(
            'biz_id' => $biz->uid
        ));
    }

    public function getTaskList($member_id,$state){
        if($state == 2){
            $state_arr = array(
                bizStateEnum::PENDING_CONFIRM,
                bizStateEnum::APPROVED,
            );
        }elseif ($state == 3){
            $state_arr = array(
                bizStateEnum::DONE,
            );
        }else{
            $state_arr = array(
                bizStateEnum::PENDING_APPROVE,
            );
        }
        $state_arr = "(" . implode(',', $state_arr) . ")";
        $r = new ormReader();
        $sql = "select botcl.*, mcc.credit_balance,mcc.credit_usd_balance,mcc.credit_khr_balance,mcc.alias,lsp.sub_product_name,lc.apply_amount from biz_one_time_credit_loan botcl left join member_credit_category mcc
        on botcl.member_credit_category_id=mcc.uid LEFT JOIN loan_sub_product lsp on mcc.sub_product_id=lsp.uid LEFT JOIN loan_contract lc ON lc.uid = botcl.contract_id
         where botcl.state in $state_arr  and botcl.member_id=".qstr($member_id);

        $data = $r->getRows($sql);
        return $data;
    }
    

}