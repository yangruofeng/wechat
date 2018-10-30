<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/27
 * Time: 16:16
 */
class bizMemberCreateLoanContractClass extends bizBaseClass
{

    // 只是在counter的操作
    public function __construct($scene_code=bizSceneEnum::COUNTER)
    {

        $is_open = $this->checkBizOpen();
        if (!$is_open->STS) {
            throw new Exception('Function close!',errorCodesEnum::FUNCTION_CLOSED);
        }
        $this->bizModel = new biz_member_create_loan_contractModel();
        $this->scene_code = bizSceneEnum::COUNTER;
        $this->biz_code = bizCodeEnum::MEMBER_CREATE_LOAN_CONTRACT;
    }

    public function checkBizOpen()
    {
        if( global_settingClass::isForbiddenLoan() ){
            return new result(false,'Forbidden loan',null,errorCodesEnum::FUNCTION_CLOSED);
        }
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


    public function getContractSource()
    {
        switch ($this->scene_code)
        {
            case bizSceneEnum::COUNTER:
                $source = contractCreateSourceEnum::COUNTER;
                break;
            case bizSceneEnum::APP_MEMBER :
                $source = contractCreateSourceEnum::MEMBER_APP;
                break;
            default:
                return new result(false,'Not support.',null,errorCodesEnum::NOT_SUPPORTED);
        }
        return new result(true,'success',$source);
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

        $biz->member_trading_password = $objectMember->trading_password;
        $biz->update_time = Now();
        if( $biz->getRowState() == ormDataRow::ROWSTATE_MODIFIED ){
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Update biz client fail.',null,errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true,'success',array(
            'biz_id' => $biz_id
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
        $biz->cashier_name = $userObj->user_name;
        $biz->cashier_trading_password = $userObj->trading_password;
        $biz->update_time = Now();
        if( $biz->getRowState() == ormDataRow::ROWSTATE_MODIFIED ){
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Update teller info fail.',null,errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true,'success',array(
            'biz_id' => $biz_id
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
        $biz->bm_id = $ct_id;
        $biz->bm_name = $ctObj->user_name;
        $biz->bm_trading_password = $ctObj->trading_password;
        $biz->update_time = Now();
        if(  $biz->getRowState() == ormDataRow::ROWSTATE_MODIFIED ){
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Update chief teller info fail.',null,errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true,'success',array(
            'biz_id' => $biz_id
        ));
    }

    public function isNeedCTApprove($biz_id)
    {
        $biz_info = $this->getBizDetailById($biz_id);
        if( !$biz_info ){
            return true;
        }
        $branch_id = $biz_info['branch_id'];
        return $this->counterBizIsNeedCTApprove(array(
            $biz_info['currency'] => $biz_info['apply_amount']
        ),$branch_id);
    }



    public function createContract($cashier_id,$member_id,$member_category_id,$amount,$currency,$loan_period,$loan_period_unit,$remark=null)
    {

        $cashierObj = new objectUserClass($cashier_id);
        $chk = $cashierObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $m_biz = $this->bizModel;

        $amount = round($amount, 2);


        $m_member_category = new member_credit_categoryModel();
        $credit_category = $m_member_category->getRow(array(
            'uid' => $member_category_id
        ));
        if( !$credit_category ){
            return new result(false,'No credit category:'.$member_category_id,null,errorCodesEnum::INVALID_PARAM);
        }

        $sub_product_id = $credit_category['sub_product_id'];

        $m_sub_product = new loan_sub_productModel();
        $product = $m_sub_product->getRow($sub_product_id);
        if (!$product) {
            return new result(false, 'No release product', null, errorCodesEnum::NO_LOAN_PRODUCT);
        }

        $main_product = (new loan_productModel())->getRow($product->product_id);
        if( !$main_product ){
            return new result(false,'No main product info.',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }


        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'Invalid member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $memberObj = new objectMemberClass($member_id);
        $chk = $memberObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        // 是否在黑名单
        $black_list = $memberObj->getBlackList();
        if( in_array(blackTypeEnum::LOAN,$black_list) ){
            return new result(false,'Member is in black list for loan.',null,errorCodesEnum::MEMBER_IS_IN_BLACK_LIST);
        }


        $m_loan_account = new loan_accountModel();
        $loan_account = $m_loan_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));

        if (!$loan_account) {
            return new result(false, 'No loan account', null, errorCodesEnum::NO_LOAN_ACCOUNT);
        }




        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->contract_id = 0;
        $biz->member_id = $member_id;
        $biz->member_credit_category_id = $member_category_id;
        $biz->sub_product_id = $sub_product_id;
        $biz->apply_amount = $amount;
        $biz->currency = $currency;
        $biz->cashier_id = $cashier_id;
        $biz->remark = $remark;
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $biz->update_time = Now();
        $biz->branch_id = $cashierObj->branch_id;
        $insert = $biz->insert();
        if (!$insert->STS) {
            return new result(false, 'Fail:'.$insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        $source = contractCreateSourceEnum::COUNTER;

        // 将临时合同取消
        $sql = "update loan_contract set state='".loanContractStateEnum::CANCEL."',update_time='".Now()."'
                where account_id='".$loan_account['uid']."' and state='".loanContractStateEnum::CREATE."' ";
        $up = $m_member->conn->execute($sql);
        if( !$up->STS ){
            return new result(false,'Cancel previous temp contract fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        $rt = loan_baseClass::calLoanDays($loan_period,$loan_period_unit);
        if( !$rt->STS ){
            return $rt;
        }

        $loan_days = $rt->DATA;

        if( $main_product->category == loanProductCategoryEnum::CREDIT_LOAN ){

            $extend_params = array(
                'creator_id' => $cashierObj->user_id,
                'creator_name' => $cashierObj->user_name,
                'branch_id' => $cashierObj->branch_id
            );

            // 信用贷产品
            $rt = (new credit_loanClass())->withdraw($member_category_id,$member_id,$amount,$loan_period,$loan_period_unit,$currency,$source,$extend_params);
            if( !$rt->STS ){
                return $rt;
            }

            $contract_data = $rt->DATA;

        }else{


            // todo 非信用贷暂时不处理信用的抵押物利率
            // 查询利率信息
            $rt = loan_baseClass::getLoanInterestDetail($member_id,$sub_product_id,$amount,$currency,$loan_days);
            if( !$rt->STS  ){
                return $rt;
            }

            $interest_info = $rt->DATA['interest_info'];

            // 组装合同参数
            $data = array(
                'member_id' => $member_id,
                'product_id' => $member_category_id,  // todo 非信用贷没有member_category怎么办？？？？？
                'amount' => $amount,
                'currency' => $currency,
                'loan_period' => $loan_period,
                'loan_period_unit' => $loan_period_unit,
                'repayment_type' => $product->interest_type,
                'repayment_period' => $product->repayment_type,
                'handle_account_id' => 0,
                'creator_id' => $cashierObj->user_id,
                'creator_name' => $cashierObj->user_name,
                'branch_id' => $cashierObj->branch_id
            );

            $rt = (new loan_baseClass())->createContract($data,$interest_info,true,$source);
            if( !$rt->STS ){
                return $rt;
            }

            $contract_data = $rt->DATA;

        }

        $contract_id = $contract_data['contract_id'];

        $biz->contract_id = $contract_id;
        $biz->state = bizStateEnum::PENDING_CONFIRM;
        $biz->update_time = Now();
        $update = $biz->update();
        if (!$update->STS) {
            return new result(false, 'Update fail.', null, errorCodesEnum::DB_ERROR);
        }

        $contract_data['biz_id'] = $biz->uid;
        return new result(true, 'success', $contract_data);

    }


    /** 录入客户信息
     * @param $biz_id
     * @param $member_image
     * @return result
     */
    public function insertMemberInfo($biz_id,$member_image)
    {
        if( !$member_image ){
            return new result(false,'No member image.',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info:'.$biz_id,null,errorCodesEnum::NO_DATA);
        }
        $biz->member_image = $member_image;
        $biz->update_time = Now();
        if( $biz->getRowState() == ormDataRow::ROWSTATE_MODIFIED ){
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Insert client info fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
            }
        }


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


    // 确认合同
    public function confirmContract($biz_id)
    {

        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if (!$biz) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        if ($biz->state == bizStateEnum::DONE) {
            return new result(true, 'success', $biz);
        }

        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $ret = $biz->update();
        if (!$ret->STS) {
            return new result(false, 'Update biz failed - ' . $ret->MSG, null, errorCodesEnum::DB_ERROR);
        }

        $contract_id = $biz->contract_id;

        $rt = loan_baseClass::confirmContractToExecute($contract_id);
        return $rt;

    }


    /** 取消本次操作
     * @param $biz_id
     * @return result
     */
    public function cancelContract($biz_id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if (!$biz) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        if ($biz->state == bizStateEnum::DONE) {
            return new result(false, 'Contract is going,can not cancel.', null,errorCodesEnum::UN_MATCH_OPERATION);
        }
        $contract_id = $biz->contract_id;
        $rt = loan_baseClass::cancelContract($contract_id);
        if( !$rt->STS ){
            return $rt;
        }

        // 更新biz状态
        $biz->state = bizStateEnum::CANCEL;
        $biz->update_time = Now();
        $ret = $biz->update();
        if (!$ret->STS) {
            return new result(false, 'Update biz failed - ' . $ret->MSG, null, errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');

    }

}