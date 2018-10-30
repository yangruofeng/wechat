<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/16
 * Time: 10:26
 */
class bizMemberLoanByMemberAppClass extends bizBaseClass
{

    public function __construct($scene_code = bizSceneEnum::APP_MEMBER)
    {

        $is_open = $this->checkBizOpen();
        if (!$is_open->STS) {
            throw new Exception('Function close!');
        }

        $this->scene_code = bizSceneEnum::APP_MEMBER;
        $this->biz_code = bizCodeEnum::MEMBER_LOAN_BY_MEMBER_APP;
        $this->bizModel = new biz_member_create_loan_contractModel();
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
        return $this->bizModel->find(array(
            'uid' => $id
        ));
    }


    public function checkMemberTradingPassword($contract_id,$sign)
    {
        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_id);
        $memberObj = new objectMemberClass($member_info['uid']);
        // 改用签名的方式
        $self_sign = md5($contract_id.$memberObj->trading_password);
        $chk = $memberObj->checkTradingPasswordSign($sign,$self_sign,'Credit loan confirm');
        return $chk;

    }


    public function bizStart($member_id,$member_category_id,$amount,$currency,$loan_term,$loan_term_unit)
    {
        $m_biz = $this->bizModel;

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



        $loan_account_info = $memberObj->loan_account_info;

        //判断柜台是否存在未处理的合同
        $num = (new loan_contractModel())->getUnConfirmedContractNumBySource($loan_account_info['uid'],contractCreateSourceEnum::COUNTER);
        if( $num > 0 ){
            return new result(false,'Have unprocessed contract yet.',null,errorCodesEnum::HAVE_UNPROCESSED_CONTRACT);
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
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $biz->update_time = Now();
        $biz->branch_id = $memberObj->branch_id;
        $insert = $biz->insert();
        if (!$insert->STS) {
            return new result(false, 'Fail:'.$insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        $source = contractCreateSourceEnum::MEMBER_APP;

        if( $main_product->category == loanProductCategoryEnum::CREDIT_LOAN ){

            // 信用贷产品
            $rt = (new credit_loanClass())->withdraw($member_category_id,$member_id,$amount,$loan_term,$loan_term_unit,$currency,$source);
            if( !$rt->STS ){
                return $rt;
            }

            $contract_data = $rt->DATA;

        }else{
            return new result(false,'Not support now.',null,errorCodesEnum::NOT_SUPPORTED);
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


    public function confirmContract($contract_id)
    {

        $rt = loan_baseClass::confirmContractToExecute($contract_id);
        if( !$rt->STS ){
            return $rt;
        }

        $biz = $this->bizModel->getRow(array(
            'contract_id' => $contract_id
        ));
        if( $biz ){
            $biz->state = bizStateEnum::DONE;
            $biz->update_time = Now();
            $update = $biz->update();
            if (!$update->STS) {
                return new result(false, 'Update fail.', null, errorCodesEnum::DB_ERROR);
            }
        }
        return new result(true);

    }


    public function cancelContract($contract_id)
    {
        $rt = loan_baseClass::cancelContract($contract_id);
        if( !$rt->STS ){
            return $rt;
        }

        $biz = $this->bizModel->getRow(array(
            'contract_id' => $contract_id
        ));
        if( $biz ){
            $biz->state = bizStateEnum::CANCEL;
            $biz->update_time = Now();
            $update = $biz->update();
            if (!$update->STS) {
                return new result(false, 'Update fail.', null, errorCodesEnum::DB_ERROR);
            }
        }
        return new result(true);

    }

}