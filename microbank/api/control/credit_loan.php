<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/28
 * Time: 10:36
 */
class credit_loanControl extends bank_apiControl
{


    public function calculatorOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        $loan_amount = $params['amount'];
        if( $loan_amount < 100 ){
            return new result(false,'Amount not supported',null,errorCodesEnum::NOT_SUPPORTED);
        }
        $loan_time = intval($params['loan_period']);  // 单位月
        if( $loan_time < 1 ){
            return new result(false,'Time not supported',null,errorCodesEnum::NOT_SUPPORTED);
        }
        $year_interest = $params['interest']/100;
        if( $year_interest <= 0 ){
            return new result(false,'Interest not supported',null,errorCodesEnum::NOT_SUPPORTED);
        }
        $payment_type = $params['repayment_type'];

        try {
            $interest_info = array(
                'interest_rate' => $year_interest,
                'interest_rate_type' => interestRatePeriodEnum::YEARLY
            );
            $interest_class = interestTypeClass::getInstance($payment_type, interestRatePeriodEnum::MONTHLY);
            $periods = $interest_class->getRepaymentPeriods($loan_time, loanPeriodUnitEnum::MONTH);
            $schema = $interest_class->getInstallmentSchema($loan_amount, $periods, $interest_info);
        } catch (Exception $ex) {
            return new result(false, $ex->getMessage(), null, $ex->getCode());
        }

        if( !$schema->STS ){
            return new result(false,'Calculate fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }
        $pay_schema = $schema->DATA;

        return new result(true,'success',array(
            'total_summation' => array(
                'payment_period' => ($pay_schema['payment_total']['total_period_pay']),
                'total_interest' => ($pay_schema['payment_total']['total_interest']),
                'payment_total' => ($pay_schema['payment_total']['total_payment']),
            ),
            'payment_schema' => $pay_schema['payment_schema']
        ));

    }


    public function getCreditAndCertListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);

        $credit = memberClass::getCreditBalance($member_id);

        $creditLoan = new credit_loanClass();
        $re = $creditLoan->creditLoanMemberCertDetail($params);
        if( !$re->STS ){
            return $re;
        }
        // 资产动态数据
        $member_asset_info = memberClass::getMemberAssetCertSummary($member_id);
        $product_id = $re->DATA['product_id'];
        $cert_list = $re->DATA['cert_list'];

        // 个人资料动态数据
        $personal_file_data = (new member_profileClass())->getMemberPersonalCertAndInitData($member_id);

        return new result(true,'success',array(
            'credit_info' => $credit,
            'product_id' => $product_id,
            'cert_list' => $cert_list,
            'member_asset_info' => $member_asset_info,
            'member_personal_file' => $personal_file_data
        ));

    }

    public function loanPreviewOp()
    {

        $params = $params = array_merge(array(),$_GET,$_POST);
        $creditLoan = new credit_loanClass();
        $re = $creditLoan->loanPreview($params);
        return $re;

    }

    public function getLoanProposeOp()
    {

        $m = new core_definitionModel();
        $rows = $m->select(array(
            'category' => 'loan_use'
        ));
        return new result(true,'success',$rows);
    }

    /** 信用贷提现(合同创建)
     * @return result
     */
    public function creditLoanWithdrawOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $withdraw_amount = round($params['amount'],2);
        $loan_period = intval($params['loan_period']);
        $term_type = intval($params['term_type']);
        $sub_product_id = $params['loan_product_id']?:0;  // 是member_credit_category.uid
        $currency = $params['currency']?:currencyEnum::USD;


        if ($term_type == 1) {
            $loan_period_type = loanPeriodUnitEnum::DAY;
        } else {
            $loan_period_type = loanPeriodUnitEnum::MONTH;
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = (new bizMemberLoanByMemberAppClass())->bizStart($member_id,$sub_product_id,$withdraw_amount,$currency,
                $loan_period,$loan_period_type);

            if( !$re->STS ){
                $conn->rollback();
                return $re;
            }

            $conn->submitTransaction();
            return $re;

        }catch ( Exception $e){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }


    /** 信用贷合同确认
     * @return result
     */
    public function contractConfirmOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
          return $re;
        }
        $params = $params = array_merge(array(),$_GET,$_POST);
        $contract_id = $params['contract_id'];
        $sign = $params['sign'];
        if( !$contract_id ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $bizClass = new bizMemberLoanByMemberAppClass();
        $chk = $bizClass->checkMemberTradingPassword($contract_id,$sign);
        if( !$chk->STS ){
            return $chk;
        }

        //启动事务
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt = $bizClass->confirmContract($contract_id);
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false,$ex->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }


    }

    /** 获取绑定的保险产品
     * @return result
     */
    public function getLoanBindInsuranceListOp()
    {
        $params = $params = array_merge(array(),$_GET,$_POST);
        $creditLoan = new credit_loanClass();
        $re = $creditLoan->getBindInsuranceProduct($params);
        return $re;
    }


    public function getCreditLoanLevelOp()
    {
        $params = $params = array_merge(array(),$_GET,$_POST);
        $type = $params['level_type'];
        switch( $type ){
            case 0:
                $level_type = creditLevelTypeEnum::MEMBER;
                break;
            case 1:
                $level_type = creditLevelTypeEnum::MERCHANT;
                break;
            default:
                $level_type = 'all';
        }

        $list = credit_loanClass::getCreditLevelList($level_type);
        return new result(true,'success',$list);
    }


    public function creditLimitCalculatorOp()
    {
        $params = $params = array_merge(array(),$_GET,$_POST);
        $re = loan_baseClass::creditLimitCalculator($params);
        return $re;
    }


    public function creditLoanIndexOp()
    {
        /*$re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }*/
        $param = array_merge(array(),$_GET,$_POST);
        $member_id = intval($param['member_id']);

        $credit_balance = memberClass::getCreditBalance($member_id);


        $product_id = 0;
        $credit_loan_product = credit_loanClass::getProductInfo();
        if( $credit_loan_product ){
            $product_id = $credit_loan_product['uid'];
        }



        // 是否显示授信利率
       /* $is_use_grant_rate = global_settingClass::creditLoanIsUseCreditGrantRate();
        if( $is_use_grant_rate ){
            // 检查是否有授信
            $credit_grant_detail = (new member_credit_grantModel())->orderBy('uid desc')->getRow(array(
                'member_id' => $member_id
            ));
            if( $credit_grant_detail ){
                if( $credit_grant_detail->interest_without_mortgage ){
                    // 展示用的利率更改
                    $monthly_min_rate_desc = round($credit_grant_detail->interest_without_mortgage,2).'%';
                }
            }
        }*/


        if( $member_id ){

            // 获取所有二级产品 (包含柜台的)
            $sub_product_list = loan_productClass::getMemberCanLoanSubProductList($member_id,1);
            foreach( $sub_product_list as $k=>$v ){

                $v['sub_product_icon'] = global_settingClass::getLoanProductIconByInterestType($v['interest_type']);
                $min_monthly_rate = loan_productClass::getMinMonthlyRate($v['uid']);
                $v['monthly_min_rate'] = $min_monthly_rate.'%';
                $sub_product_list[$k] = $v;
            }

        }else{

            // 获取所有二级产品
            $sub_product_list = loan_productClass::getAllActiveSubProductList();
            foreach( $sub_product_list as $k=>$v ){

                $v['sub_product_icon'] = global_settingClass::getLoanProductIconByInterestType($v['interest_type']);
                $min_monthly_rate = loan_productClass::getMinMonthlyRate($v['uid']);
                $v['monthly_min_rate'] = $min_monthly_rate.'%';
                $sub_product_list[$k] = $v;
            }
        }

        // 新方式取得产品列表
        $product_category_list = loan_categoryClass::getAllCreditCategoryListOfMember($member_id);

        // 取语言包
        $lang = $param['lang'];
        foreach( $product_category_list as $key=>$v ){

            $product_category_list[$key]['category_name'] = $v['category_lang'][$lang]?:$v['category_name'];
        }


        return new result(true,'success',array(
            'product_id' => $product_id,
            'sub_product_list' => $sub_product_list,
            'credit_info' => $credit_balance,
            'monthly_min_rate' => 0.00,
            'product_category_list' => $product_category_list
        ));

    }

    public function getProductRateCreditLevelOp()
    {
        $param = array_merge(array(),$_GET,$_POST);
        $rate_id = $param['rate_id'];
        $m_rate = new loan_product_size_rateModel();
        $rate = $m_rate->getRow($rate_id);
        if( !$rate ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $level = credit_loanClass::getCreditLevelByAmount($rate['loan_size_max'],$rate['currency']);
        return new result(true,'success',$level);
    }

    public function getLoanMaxMonthOp()
    {
        $param = array_merge(array(),$_GET,$_POST);
        $member_id = $param['member_id'];
        $product_id = $param['product_id'];
        $member_credit_category_id = $param['member_credit_category_id'];

        // todo 需要重新修改
        // 兼容demo的用法
        if( $member_credit_category_id > 0 ){

            return credit_loanClass::getMemberLoanOptionByCategory($member_id,$member_credit_category_id);

        }else{

            $ret = credit_loanClass::getMemberCreditLoanValidTerms($product_id,$member_id);

            // 获取币种列表
            $currency_list = loan_productClass::getCurrencyListAndAmountRangeByProduct($product_id);
            if( count($currency_list) < 1 ){
                return new result(false,'Product not set loan currency.',null,errorCodesEnum::NO_LOAN_INTEREST);
            }

            $ret['currency_list'] = $currency_list;

            return new result(true,'success',$ret);
        }


    }

    public function getCategoryLoanOptionOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $param = array_merge(array(),$_GET,$_POST);
        $member_id = $param['member_id'];
        $member_category_id = $param['member_category_id'];
        return credit_loanClass::getMemberLoanOptionByCategoryNew($member_id,$member_category_id,false);
    }

}