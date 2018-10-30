<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/28
 * Time: 10:38
 */
class credit_loanClass extends loan_baseClass
{

    protected static $product_info = null;


    /** 获取信用贷产品信息
     * @return bool|mixed|null
     */
    public static function getProductInfo()
    {
        if (self::$product_info == null) {
            $m_product = new loan_productModel();
            $product = $m_product->orderBy('uid desc')->getRow(array(
                'category' => loanProductCategoryEnum::CREDIT_LOAN,
                //'state' => loanProductStateEnum::ACTIVE
            ));
            if ($product) {
                self::$product_info = $product;
            }
        }
        return self::$product_info;
    }

    public static function getCertLevelCalValue()
    {
        return array(
            certificationTypeEnum::ID => certTypeCalculateValueEnum::ID,
            certificationTypeEnum::FAIMILYBOOK => certTypeCalculateValueEnum::FAMILY_BOOK,
            certificationTypeEnum::PASSPORT => certTypeCalculateValueEnum::PASSPORT,
            certificationTypeEnum::HOUSE => certTypeCalculateValueEnum::HOUSE_CERT,
            certificationTypeEnum::CAR => certTypeCalculateValueEnum::CAR_CERT,
            certificationTypeEnum::WORK_CERTIFICATION => certTypeCalculateValueEnum::WORK_CERT,
            //certificationTypeEnum::CIVIL_SERVANT => certTypeCalculateValueEnum::CIVIL_SERVANT,
            //certificationTypeEnum::FAMILY_RELATIONSHIP => certTypeCalculateValueEnum::FAMILY_RELATION,
            certificationTypeEnum::LAND => certTypeCalculateValueEnum::LAND_CERT,
            certificationTypeEnum::RESIDENT_BOOK => certTypeCalculateValueEnum::RESIDENT_BOOK,
            certificationTypeEnum::MOTORBIKE => certTypeCalculateValueEnum::MOTORBIKE
        );
    }


    public static function addCreditLevel($params)
    {
        $level_type = intval($params['level_type']);
        $min_amount = intval($params['min_amount']);
        $max_amount = intval($params['max_amount']);
        $disburse_time = intval($params['disburse_time']) ?: 0;
        $time_unit = $params['disburse_time_unit'] ?: 1;
        $cert_list = $params['cert_list'];

        if ($min_amount < 0 || $max_amount <= 0) {
            return new result(false, 'Amount Invalid', null, errorCodesEnum::INVALID_PARAM);
        }
        if ($min_amount >= $max_amount) {
            return new result(false, 'Amount Invalid', null, errorCodesEnum::INVALID_PARAM);
        }
        if (empty($cert_list)) {
            return new result(false, 'Did not select certification', null, errorCodesEnum::INVALID_PARAM);
        }

        // 首先计算匹配值
        $cert_cal_value = self::getCertLevelCalValue();
        $sum = 0;
        foreach ($cert_list as $type) {
            $sum = $sum | $cert_cal_value[$type];
        }

        $m_level = new loan_credit_cert_levelModel();
        $m_cert = new loan_credit_level_cert_listModel();

        $level = $m_level->newRow();
        $level->level_type = $level_type;
        $level->match_value = $sum;
        $level->min_amount = $min_amount;
        $level->max_amount = $max_amount;
        $level->disburse_time = $disburse_time;
        $level->disburse_time_unit = $time_unit;
        $level->create_time = Now();
        $in = $level->insert();
        if (!$in->STS) {
            return new result(false, 'Add level fail', null, errorCodesEnum::DB_ERROR);
        }

        // 组装sql
        $values_arr = array();
        reset($cert_list);
        foreach ($cert_list as $type) {
            $str = "('" . $level->uid . "','" . $type . "','" . $cert_cal_value[$type] . "')";
            $values_arr[] = $str;
        }

        $values = trim(join(',', $values_arr), ',');
        $sql = "insert into loan_credit_level_cert_list(cert_level_id,cert_type,cal_value) values  " . $values;
        $in = $m_level->conn->execute($sql);
        if (!$in->STS) {
            return new result(false, 'Add cert list fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true);
    }

    public static function editCreditLevel($params)
    {
        $uid = $params['uid'];
        $level_type = intval($params['level_type']);
        $min_amount = intval($params['min_amount']);
        $max_amount = intval($params['max_amount']);
        $disburse_time = intval($params['disburse_time']) ?: 0;
        $time_unit = $params['disburse_time_unit'] ?: 1;
        $cert_list = $params['cert_list'];

        if ($min_amount < 0 || $max_amount <= 0) {
            return new result(false, 'Amount Invalid', null, errorCodesEnum::INVALID_PARAM);
        }
        if ($min_amount >= $max_amount) {
            return new result(false, 'Amount Invalid', null, errorCodesEnum::INVALID_PARAM);
        }
        if (empty($cert_list)) {
            return new result(false, 'Did not select certification', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_level = new loan_credit_cert_levelModel();
        $level = $m_level->getRow($uid);
        if (!$level) {
            return new result(false, 'No data', null, errorCodesEnum::UNEXPECTED_DATA);
        }

        // 首先计算匹配值
        $cert_cal_value = self::getCertLevelCalValue();
        $sum = 0;
        foreach ($cert_list as $type) {
            $sum = $sum | $cert_cal_value[$type];
        }

        // 更新level
        $level->match_value = $sum;
        $level->level_type = $level_type;
        $level->min_amount = $min_amount;
        $level->max_amount = $max_amount;
        $level->disburse_time = $disburse_time;
        $level->disburse_time_unit = $time_unit;
        $level->update_time = Now();
        $up = $level->update();
        if (!$up->STS) {
            return new result(false, 'Update level fail', null, errorCodesEnum::DB_ERROR);
        }

        // 删除原list
        $sql = "delete from loan_credit_level_cert_list where cert_level_id='$uid' ";
        $del = $m_level->conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete old lost fail', null, errorCodesEnum::DB_ERROR);
        }

        // 添加新list
        $values_arr = array();
        reset($cert_list);
        foreach ($cert_list as $type) {
            $str = "('" . $level->uid . "','" . $type . "','" . $cert_cal_value[$type] . "')";
            $values_arr[] = $str;
        }

        $values = trim(join(',', $values_arr), ',');
        $sql = "insert into loan_credit_level_cert_list(cert_level_id,cert_type,cal_value) values  " . $values;
        $in = $m_level->conn->execute($sql);
        if (!$in->STS) {
            return new result(false, 'Add cert list fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');
    }

    public static function getCreditLevelList($type = 'all')
    {
        $m_level_cert = new loan_credit_level_cert_listModel();
        if ($type == 'all') {
            $sql = "select * from loan_credit_cert_level order by level_type asc,max_amount asc ";
        } else {
            $type = intval($type);
            $sql = "select * from loan_credit_cert_level where level_type='$type' order by level_type asc,max_amount asc ";
        }

        $level = $m_level_cert->reader->getRows($sql);
        $return = array();
        if (count($level) > 0) {
            foreach ($level as $k => $v) {
                $item = $v;
                $lists = $m_level_cert->select(array(
                    'cert_level_id' => $v['uid']
                ));
                $item['cert_list'] = array_column($lists, 'cert_type');
                $return[] = $item;
            }
        }
        return $return;
    }

    public static function deleteCreditLevel($id)
    {
        $id = intval($id);
        if (!$id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::DB_ERROR);
        }
        $m_level = new loan_credit_cert_levelModel();
        $sql = "delete from loan_credit_cert_level where uid='$id'";
        $d = $m_level->conn->execute($sql);
        if (!$d->STS) {
            return new result(false, 'Delete fail', null, errorCodesEnum::DB_ERROR);
        }
        $sql = "delete from loan_credit_level_cert_list where cert_level_id='$id'";
        $d = $m_level->conn->execute($sql);
        if (!$d->STS) {
            return new result(false, 'Delete fail', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success');
    }


    public function creditLoanMemberCertDetail($params)
    {
        $product_info = self::getProductInfo();
        $product_id = $product_info ? $product_info['uid'] : 0;
        $member_id = intval($params['member_id']);
        $re = memberClass::getMemberCertStateOrCount($member_id);

        if (!$re->STS) {
            return $re;
        }
        $cert_list = $re->DATA;

        return new result(true, 'success', array(
            'product_id' => $product_id,
            'product_info' => $product_info,
            'cert_list' => $cert_list
        ));

    }


    public function getBindInsuranceProduct($params)
    {
        $product_id = intval($params['loan_product_id']);
        $re = parent::getLoanProductBindInsuranceProduct($product_id);
        if (!$re->STS) {
            return $re;
        }
        $data = $re->DATA;
        return new result(true, 'success', $data);
    }


    /** 信用贷提现
     * @param $params
     * @return result
     */
    public function withdraw($member_category_id, $member_id, $amount, $loan_period, $loan_period_unit,$currency=currencyEnum::USD,$create_source=contractCreateSourceEnum::MEMBER_APP,$extent_param=array())
    {
        // 检查功能是否开启
        if ( global_settingClass::isForbiddenLoan() ) {
            return new result(false, 'Function closed', null, errorCodesEnum::FUNCTION_CLOSED);
        }

        $withdraw_amount = round($amount, 2);
        if ($withdraw_amount <= 0) {
            return new result(false, 'Invalid amount', null, errorCodesEnum::WITHDRAW_AMOUNT_INVALID);
        }


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


        if (!$member_id) {
            return new result(false, 'Invalid member', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'Invalid member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 检查member的贷款限制
        $memberObj = new objectMemberClass($member_id);
        $chk = $memberObj->isLimitCreditLoan();
        if (!$chk->STS) {
            return $chk;
        }

        $m_loan_account = new loan_accountModel();
        $loan_account = $m_loan_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        if (!$loan_account) {
            $loan_account = $m_loan_account->newRow();
            $loan_account->obj_guid = $member->obj_guid;
            $loan_account->account_type = loanAccountTypeEnum::MEMBER;
            $loan_account->update_time = Now();
            $insert = $loan_account->insert();
            if (!$insert->STS) {
                return new result(false, 'No loan account', null, errorCodesEnum::NO_LOAN_ACCOUNT);
            }
        }

        // 获取信用信息
        $credit_info = memberClass::getCreditBalance($member_id);
        $credit = $credit_info['credit'];
        $balance = $credit_info['balance'];
        $is_active = $credit_info['is_active'];

        // 检查信用是否可用
        if ($is_active != 1) {
            return new result(false, 'Credit can not use now.', null, errorCodesEnum::CREDIT_CAN_NOT_USE);
        }

        // 折算成消耗信用值
        if( $currency == currencyEnum::USD ){
            $credit_amount = ceil($withdraw_amount);
            if($withdraw_amount>$credit_category['credit_usd_balance']){
                return new result(false, 'Out of category credit balance', null, errorCodesEnum::OUT_OF_CREDIT_BALANCE);
            }
        }else{

            // 这里其实是用USD来换其他币种的
            if($currency==currencyEnum::KHR){
                $ex_rate = 4000;//global_settingClass::getCurrencyRateBetween(currencyEnum::USD,$currency);
                if( $ex_rate <= 0 ){
                    return new result(false,'No set currency rate:'.currencyEnum::USD.'-'.$currency,null,errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
                }
                $credit_amount = ceil($withdraw_amount/$ex_rate);
                if($withdraw_amount>$credit_category['credit_khr_balance']){
                    return new result(false, 'Out of category credit balance', null, errorCodesEnum::OUT_OF_CREDIT_BALANCE);
                }
            }else{
                return new result(false,"Not Support Currency As ".$currency." Yet");
            }
        }


        if ($credit_amount > $credit) {
            return new result(false, 'Out of credit', null, errorCodesEnum::OUT_OF_ACCOUNT_CREDIT);
        }

        // 首先检查是否超出单个的信用余额
        if( $credit_amount > $credit_category['credit_balance'] ){
            return new result(false, 'Out of category credit balance', null, errorCodesEnum::OUT_OF_CREDIT_BALANCE);
        }

        // 超出总信用余额
        if ($credit_amount > $balance) {
            return new result(false, 'Out of total credit balance', null, errorCodesEnum::OUT_OF_CREDIT_BALANCE);
        }

        // 超出单次额度
        $global_setting = global_settingClass::getCommonSetting();
        $single_limit = intval($global_setting['withdrawal_single_limit']);
        if ($single_limit > 0 && $credit_amount > $single_limit) {
            return new result(false, 'Out of single limit', null, errorCodesEnum::OUT_OF_PER_WITHDRAW);
        }

        if( $create_source != contractCreateSourceEnum::COUNTER){
            //  柜台是否存在未完成的贷款
            $num = (new loan_contractModel())->getUnConfirmedContractNumBySource($loan_account['uid'],contractCreateSourceEnum::COUNTER);
            if( $num > 0 ){
                return new result(false,'Have unprocessed contract.',null,errorCodesEnum::HAVE_UNPROCESSED_CONTRACT);
            }
        }


        $loan_period = intval($loan_period);

        $rt = loan_baseClass::calLoanDays($loan_period,$loan_period_unit);
        if( !$rt->STS ){
            return $rt;
        }
        $loan_days = $rt->DATA;

        // 组装合同参数
        $data = array(
            'member_id' => $member_id,
            'product_id' => $member_category_id,
            'amount' => $withdraw_amount,
            'currency' => $currency,
            'loan_period' => $loan_period,
            'loan_period_unit' => $loan_period_unit,
            'repayment_type' => $product->interest_type,
            'repayment_period' => $product->repayment_type,
            'handle_account_id' => 0,
            'credit_amount' => $withdraw_amount,
            'credit_amount_currency'=>$currency
        );

        // 匹配利率信息
        $interest_re = self::getLoanInterestDetail($member_id,$product->uid, $withdraw_amount, $currency, $loan_days,array(
            'member_credit_category_id' => $member_category_id
        ));
        if (!$interest_re->STS) {
            return $interest_re;
        }
        $interest_data = $interest_re->DATA;
        $interest_info = $interest_data['interest_info'];
        $size_rate_id = $interest_info['uid'];
        $special_rate_id = $interest_info['special_rate_id']?: 0;

        $interest_info['product_size_rate_id'] = $size_rate_id;
        $interest_info['product_special_rate_id'] = $special_rate_id;
        $interest_info['is_full_interest'] = $product->is_full_interest_prepayment;

        $p =  array_merge((array)$extent_param,$data);
        $re = $this->createContract($p, $interest_info, true,$create_source);  // 创建合同
        return $re;
    }

    /** 合同确认，信用贷直接把合同状态更新为待放款
     * @param $contract_id
     * @param array $extent
     * @return result
     */
    public function confirmContract($contract_id)
    {

        //启动事务
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt = loan_baseClass::confirmContractToExecute($contract_id);
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }


    public static function getCreditLevelByAmount($amount, $currency = 'USD')
    {
        $rate = currency::getRateBetween($currency, currency::USD);
        $amount = round($amount * $rate, 2);
        $level_list = self::getCreditLevelList();

        $match_level = null;
        $max_level = null;
        $max_amount = 0;
        foreach ($level_list as $level) {

            // 最高等级的
            if ($level['max_amount'] >= $max_amount) {
                $max_level = $level;
                $max_amount = $level['max_amount'];
            }

            if ($amount >= $level['min_amount'] && $amount <= $level['max_amount']) {
                $match_level = $level;
            }
        }
        return $match_level ?: $max_level;
    }


    /** 获取客户信用贷全部待还计划
     * @param $member_id
     * @return ormCollection
     */
    public static function getMemberAllCreditLoanUncompletedSchemas($member_id)
    {
        $r = new ormReader();
        $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account ? $loan_account['uid'] : 0;
        $sql = "select s.* from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid left join loan_product p on p.uid=c.product_id 
        where c.account_id='$account_id' and  c.product_category='".loanProductCategoryEnum::CREDIT_LOAN."' and  c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "' and c.state<'" . loanContractStateEnum::COMPLETE . "' and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "'
        order by s.receivable_date asc ";

        $schemas = $r->getRows($sql);
        return $schemas;
    }

    /** 获取客户信用贷下期应还计划
     * @param $member_id
     * @return ormCollection
     */
    public static function getMemberCreditLoanNextRepaymentSchema($member_id)
    {
        $r = new ormReader();
        $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
        $account_id = $loan_account ? $loan_account['uid'] : 0;
        $sql = "select s.* from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid left join loan_product p on p.uid=c.product_id 
        where c.account_id='$account_id' and c.product_category='".loanProductCategoryEnum::CREDIT_LOAN."' and  c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "' and c.state<'" . loanContractStateEnum::COMPLETE . "' and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "'
        and s.receivable_date>='" . date('Y-m-d') . "'  order by s.receivable_date asc ";

        $schemas = $r->getRow($sql);
        return $schemas;
    }


    public static function getLoanMaxMonthByDefaultWay($product_id)
    {
        $r = new ormReader();
        $sql = "select min(min_term_days) min_days,max(max_term_days) max_days from loan_product_size_rate where 
         product_id='$product_id' and currency='" . currencyEnum::USD . "' and interest_payment='" . interestPaymentEnum::ANNUITY_SCHEME . "' and interest_rate_period='" . interestRatePeriodEnum::MONTHLY . "' ";
        $row = $r->getRow($sql);
        if (!$row) {
            return new result(false, 'No set rate', null, errorCodesEnum::LOAN_PRODUCT_UNSHELVE);
        }
        $min_days = $row['min_days'];
        $max_days = $row['max_days'];

        $min_month = floor($min_days / 30);
        $max_month = floor($max_days / 30);

        if ($max_month < 1) {
            return new result(false, 'No set rate', null, errorCodesEnum::LOAN_PRODUCT_UNSHELVE);
        }

        if ($min_month < 1) {
            $min_month = 1;
        }

        return new result(true, 'success', array(
            'min_month' => $min_month,
            'max_month' => $max_month
        ));
    }


    /** 获取会员信用贷剩余贷款时间（月数）
     * @param $member_id
     * @return float|int
     */
    public static function getMemberCreditLoanMaxMonth($member_id)
    {
        $credit_info = (new member_creditModel())->find(array(
            'member_id' => $member_id
        ));
        if (!$credit_info) {
            return 0;
        }
        $expire_time = $credit_info['expire_time'];
        // 剩余月数
        $expire_day = date('Y-m-d', strtotime($expire_time));
        $today = date('Y-m-d');

        $left_months = ceil((strtotime($expire_day) - strtotime($today)) / (30 * 24 * 3600));
        if ($left_months <= 0) {
            $left_months = 0;
        }
        return $left_months;
    }


    public static function getMemberLoanOptionByCategoryNew($member_id,$member_category_id,$is_show_all=true)
    {
        $m_member_category = new member_credit_categoryModel();
        $r = $m_member_category->reader;
        $member_category = $m_member_category->getRow(array(
            'uid' => $member_category_id
        ));
        if( !$member_category ){
            return new result(false,'No category:'.$member_category_id,null,errorCodesEnum::INVALID_PARAM);
        }
        $sub_product_id = $member_category['sub_product_id'];

        $loan_category = (new loan_categoryModel())->find(array(
            'uid' => $member_category['category_id']
        ));

        $credit_info = (new member_creditModel())->find(array(
            'member_id' => $member_id
        ));
        if (!$credit_info) {
            return new result(false,'Not grant credit yet.',null,errorCodesEnum::MEMBER_UN_GRANT_CREDIT);
        }

        $ret = array(
            //"terms" => array(),//贷款周期的可选项
            //"term_type" => 0, //贷款周期的类型,0表示月，1表示天
            'currency_list' => array(),
            'is_one_time' => $member_category['is_one_time']?1:0,
            'special_key'=>$loan_category['special_key'],
            'page_tip' => null,
            'is_fix_loan_term' => 0,
            'fix_loan_term_data' => array(
                'term_type' => 1,
                'term_value' => 0
            ),
        );

        $expire_time = $credit_info['expire_time'];
        // 剩余天数
        $expire_day = date('Y-m-d', strtotime($expire_time));
        $today = date('Y-m-d');
        $left_days = ceil((strtotime($expire_day) - strtotime($today)) / (24 * 3600));
        if( $left_days <=0  ){
            return new result(true,'success',$ret);
        }

        $credit_left_days = $left_days;

        $is_special_days_item = null;

        if( $member_category['is_one_time'] || $loan_category['special_key'] == specialLoanCateKeyEnum::QUICK_LOAN ){

            // one time 没有选择，只能贷剩余的周期
            $is_special_days_item['days'] = $left_days;
            if( $left_days < 30 ){
                $is_special_days_item['term_type'] = 1;
                $is_special_days_item['terms'][] = $left_days;
                $ret['is_fix_loan_term'] = 1;
                $ret['fix_loan_term_data'] = array(
                    'term_type' => 1,
                    'term_value' => $left_days
                );
            }else{
                $left_month = ceil($left_days/30);
                $is_special_days_item['term_type'] = 0;
                $is_special_days_item['terms'][] = $left_month;
                $ret['is_fix_loan_term'] = 1;
                $ret['fix_loan_term_data'] = array(
                    'term_type' => 0,
                    'term_value' => $left_month
                );
            }

        }

        if( $loan_category['is_special'] ){
            switch( $loan_category['special_key']  ){
                case specialLoanCateKeyEnum::FIX_REPAYMENT_DATE:
                    // 只能贷款到固定日期
                    $today = date('Y-m-d');
                    $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
                    $repayment_date = loan_accountClass::getSuperLoanRepaymentDateByAccountInfo($loan_account,$today);
                    $days = ceil( (strtotime($repayment_date)-strtotime($today))/86400 );
                    if( $days <= 0 ){
                        $days = 0;
                    }
                    $is_special_days_item['days'] = $left_days;
                    $is_special_days_item['term_type'] = 1;
                    $is_special_days_item['terms'][] = $days;
                    $ret['page_tip'] = "Loan repayment deadline: ".$repayment_date;
                    $ret['is_fix_loan_term'] = 1;
                    $ret['fix_loan_term_data'] = array(
                        'term_type' => 1,
                        'term_value' => $days
                    );
                    break;
                default:
                    break;
            }
        }




        $currency_list = array();

        // 首先获取匹配的利息行
        $interest_list = loan_productClass::getSizeRateByPackageId($member_category['interest_package_id'],$sub_product_id);

        foreach( $interest_list as $k=>$v ){

            if( !$v['is_active'] ){
                continue;
            }

            if( !$is_show_all ){
                if( !$v['is_show_for_client'] ){
                    continue;  // 过滤
                }
            }

            $currency = $v['currency'];

            if( $currency == currencyEnum::USD ){
                $amount = $member_category['credit_usd_balance'];
            }elseif( $currency == currencyEnum::KHR ){
                $amount = $member_category['credit_khr_balance'];
            }else{
                $amount = 0;
            }

            // 没有余额就不返回了
            if( $amount <= 0 ){
                continue;
            }


            // 再格式化可贷款时间
            $temp = array();
            $temp['credit_amount'] = $amount;
            $temp['currency'] = $currency;
            $temp['loan_size_min'] = $v['loan_size_min'];
            $temp['loan_size_max'] = $v['loan_size_max'];
            $temp['min_term_days'] = $v['min_term_days'];
            $temp['max_term_days'] = $v['max_term_days'];

            $temp['special_service_fee'] = $v['service_fee'].($v['service_fee_type']==1?'':'%');

            $min_days = min($credit_left_days,$v['min_term_days']);
            $max_days = min($credit_left_days,$v['max_term_days']);

            if( $min_days > $max_days ){
                continue;
            }

            if( $is_special_days_item ){
                $temp['term_type'] = $is_special_days_item['term_type'];
                $temp['terms'] = $is_special_days_item['terms'];

            }else{

                if( $min_days < 30 && $max_days < 30  ){

                    $temp['term_type'] = 1;
                    for( $i=$min_days;$i<=$max_days;$i++){
                        $temp['terms'][] = $i;
                    }
                }else{
                    $min_month = ceil($min_days/30);
                    $max_month = floor($max_days/30);
                    $temp['term_type'] = 0;
                    for( $i=$min_month;$i<=$max_month;$i++ ){
                        $temp['terms'][] = $i;
                    }

                }
            }


            $currency_list[$currency][] = $temp;

        }


        $ret['usable_currency'] = array_keys($currency_list);
        $ret['currency_list'] = $currency_list;

        return new result(true,'success',$ret);


    }


    public static function getMemberLoanOptionByCategory($member_id,$member_category_id,$is_show_all=true)
    {
        $m_member_category = new member_credit_categoryModel();
        $r = $m_member_category->reader;
        $member_category = $m_member_category->getRow(array(
            'uid' => $member_category_id
        ));
        if( !$member_category ){
            return new result(false,'No category:'.$member_category_id,null,errorCodesEnum::INVALID_PARAM);
        }
        $sub_product_id = $member_category['sub_product_id'];

        $loan_category = (new loan_categoryModel())->find(array(
            'uid' => $member_category['category_id']
        ));

        // 获取币种列表
        $currency_list = loan_productClass::getCurrencyListAndAmountRangeByProduct($sub_product_id);
        if( count($currency_list) < 1 ){
            return new result(false,'Product not set loan currency.',null,errorCodesEnum::NO_LOAN_INTEREST);
        }

        $credit_info = (new member_creditModel())->find(array(
            'member_id' => $member_id
        ));
        if (!$credit_info) {
            return new result(false,'Not grant credit yet.',null,errorCodesEnum::MEMBER_UN_GRANT_CREDIT);
        }

        $ret = array(
            "terms" => array(),//贷款周期的可选项
            "term_type" => 0, //贷款周期的类型,0表示月，1表示天
            'currency_list' => array(),
            'is_one_time' => $member_category['is_one_time']?1:0,
            'is_fix_loan_terms' => 0
        );

        $expire_time = $credit_info['expire_time'];
        // 剩余天数
        $expire_day = date('Y-m-d', strtotime($expire_time));
        $today = date('Y-m-d');
        $left_days = ceil((strtotime($expire_day) - strtotime($today)) / (24 * 3600));
        if( $left_days <=0  ){
            return new result(true,'success',$ret);
        }

        foreach( $currency_list as $k=>$v ){

            $currency = $v['currency'];
            if( $currency == currencyEnum::USD ){
                $amount = $member_category['credit_usd_balance'];
            }elseif( $currency == currencyEnum::KHR ){
                $amount = $member_category['credit_khr_balance'];
            }else{
                $amount = 0;
            }

            // 没有余额就不返回了
            if( $amount <= 0 ){
                break;
            }

            $currency_list[$k]['loan_amount'] = $amount;
            $currency_list[$k]['term_type'] = 0;
            $currency_list[$k]['terms'] = 0;

            // 自动选择的日期
            $sql = "select max_term_days from loan_product_size_rate where product_id='$sub_product_id' and currency=".qstr($currency).
                " and loan_size_min<='$amount' and loan_size_max>='$amount' ";
            $max_days = $r->getOne($sql)?:0;


            if( $max_days > 0  ){

                // 有设置这个利率
                $loan_days = min($max_days,$left_days);
                if( $loan_days < 30 ){
                    $currency_list[$k]['term_type'] = 1;
                    $currency_list[$k]['terms'] = $loan_days;
                }else{
                    // 折算成可贷的月份
                    $currency_list[$k]['term_type'] = 0;
                    if( $left_days < $max_days ){
                        $currency_list[$k]['terms'] = ceil($loan_days/30);
                    }else{
                        $currency_list[$k]['terms'] = floor($loan_days/30);  // 要往下浮动一个月
                    }

                }
            }

        }

        //$credit_balance = $member_category['credit_balance'];

        $ret['currency_list'] = $currency_list;

        if( $loan_category['is_special'] ){
            switch( $loan_category['special_key']  ){
                case specialLoanCateKeyEnum::FIX_REPAYMENT_DATE:
                    // 只能贷款到固定日期
                    $today = date('Y-m-d');
                    $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
                    $repayment_date = loan_accountClass::getSuperLoanRepaymentDateByAccountInfo($loan_account,$today);
                    $days = ceil( (strtotime($repayment_date)-strtotime($today))/86400 );
                    if( $days <= 0 ){
                        $days = 0;
                    }
                    $ret['is_fix_loan_terms'] = 1;
                    $ret['term_type'] = 1;
                    $ret['terms'][] = $days;
                    return new result(true,'success',$ret);
                    break;
                default:
                    break;
            }
        }


        if( $member_category['is_one_time'] || $loan_category['special_key'] == specialLoanCateKeyEnum::QUICK_LOAN ){

            // one time 没有选择，只能贷剩余的周期
            if( $left_days < 30 ){
                $ret['term_type'] = 1;
                $ret['terms'][] = $left_days;
            }else{
                $left_month = ceil($left_days/30);
                $ret['term_type'] = 0;
                $ret['terms'][] = $left_month;
            }
            $ret['is_fix_loan_terms'] = 1;

            return new result(true,'success',$ret);

        }else{

            $valid_terms = self::getMemberCreditLoanValidTerms($sub_product_id,$member_id);
            $ret = array_merge($ret,(array)$valid_terms);
            return new result(true,'success',$ret);

        }
    }

    /*
     * 获取一个二级产品的有效借款周期
     * 可选周期是一个数组，周期类型0表示月，1表示天
     */
    public static function getMemberCreditLoanValidTerms($sub_product_id, $member_id)
    {

        $ret = array(
            "terms" => array(),//贷款周期的可选项
            "term_type" => 0//贷款周期的类型
        );

        // $member_left_months=self::getMemberCreditLoanMaxMonth($member_id);
        //为了减少误差，不直接调用
        $credit_info = (new member_creditModel())->find(array(
            'member_id' => $member_id
        ));
        if (!$credit_info) {
            return $ret;
        }
        $expire_time = $credit_info['expire_time'];
        // 剩余月数
        $expire_day = date('Y-m-d', strtotime($expire_time));
        $today = date('Y-m-d');
        $left_days = ceil((strtotime($expire_day) - strtotime($today)) / (24 * 3600));

        if ($left_days <= 0) {
            return $ret;
        }

        $m = new loan_sub_productModel();
        $row = $m->find(array("uid" => $sub_product_id, "state" => loanProductStateEnum::ACTIVE));
        if (!$row) {
            return $ret;
        }
        $sql = "select max(max_term_days) from loan_product_size_rate where product_id='" . $sub_product_id . "'";
        $max_days = $m->reader->getOne($sql);
        if (!$max_days) {
            return $ret;
        }
        if ($max_days > $left_days) {
            $max_days = $left_days;
        }
        $max_months = ceil($max_days / 30);
        $term_type = 0;//0表示months,1表示days
        $terms = array();

        if (interestTypeClass::isOnetimeRepayment($row['interest_type'])) {
            $term_type = 1;
            if ($max_days <= 30) {
                for ($i = 1; $i <= $max_days; $i++) {
                    $terms[] = $i;
                }
            } else {

                // 换成月
                $term_type = 0;
                for( $i=1;$i<=$max_months;$i++){
                    $terms[] = $i;
                }

               /* for ($i = 1; $i <= 30; $i++) {
                    $terms[] = $i;
                }
                $idx = ceil($max_days / 30);//浮动1个月，floor
                for ($i = 2; $i <= $idx; $i++) {
                    $terms[] = $i * 30;
                }*/

            }
        } else {

            if( $row['interest_type'] == interestPaymentEnum::SEMI_BALLOON_INTEREST ){

                $loan_account_info = loan_accountClass::getLoanAccountInfoByMemberId($member_id);
                $principal_month = $loan_account_info['principal_periods']?:6;
                $idx = floor( ($max_months) / $principal_month );
                for ($i = 1; $i <= $idx; $i++) {
                    $terms[] = $i * $principal_month;
                }

            }else{

                switch ($row['repayment_type']) {
                    case interestRatePeriodEnum::DAILY:
                        $term_type = 1;
                        if ($max_days <= 30) {
                            for ($i = 1; $i <= $max_days; $i++) {
                                $terms[] = $i;
                            }
                        } else {
                            for ($i = 1; $i <= 30; $i++) {
                                $terms[] = $i;
                            }
                            $idx = ceil($max_days / 30);//浮动1个月，floor
                            for ($i = 2; $i <= $idx; $i++) {
                                $terms[] = $i * 30;
                            }
                        }
                        break;
                    case interestRatePeriodEnum::MONTHLY:
                        for ($i = 1; $i <= $max_months; $i++) {
                            $terms[] = $i;
                        }
                        break;
                    case interestRatePeriodEnum::QUARTER:
                        $idx = floor(($max_months + 1) / 4);//浮动1个月，floor
                        for ($i = 1; $i <= $idx; $i++) {
                            $terms[] = $i * 4;
                        }
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        $idx = floor(($max_months + 1) / 6);//浮动1个月，floor
                        for ($i = 1; $i <= $idx; $i++) {
                            $terms[] = $i * 6;
                        }
                        break;
                    case interestRatePeriodEnum::YEARLY:
                        $idx = floor(($max_months + 1) / 12);//浮动1个月，floor
                        for ($i = 1; $i <= $idx; $i++) {
                            $terms[] = $i * 12;
                        }
                        break;
                    case interestRatePeriodEnum::WEEKLY:
                        $term_type = 1;
                        $idx = ceil($max_days / 7);//浮动1个月，floor
                        for ($i = 1; $i <= $idx; $i++) {
                            $terms[] = $i * 7;
                        }
                        break;
                    default:
                        break;
                }

            }


        }

        $ret = array(
            "terms" => $terms ?: array(),//贷款周期的可选项
            "term_type" => $term_type ?: 0//贷款周期的类型
        );
        return $ret;
    }


    public static function loanConsultBaseInfoPreview($params)
    {
        $amount = intval($params['amount']);
        $currency = currencyEnum::USD;
        $loan_month = intval($params['loan_time']);
        $loan_days = $loan_month*30;
        if( $amount <=0 || $loan_days <=0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        // 默认取信用贷的利率
        $credit_product = credit_loanClass::getProductInfo();
        if( !$credit_product ){
            return new result(false,'No alive product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }

        $product_id = $credit_product['uid'];

        $r = new ormReader();
        // 首先匹配选中条件的
        $sql = "select * from loan_product_size_rate where loan_size_min<='$amount'
and loan_size_max>='$amount' and currency='$currency' and min_term_days<='$loan_days' and max_term_days>='$loan_days' 
and interest_payment='".interestPaymentEnum::ANNUITY_SCHEME."' and interest_rate_period='".interestRatePeriodEnum::MONTHLY."' ";
        $rate = $r->getRow($sql);
        if( !$rate ){
            $sql = "select * from loan_product_size_rate order by interest_rate desc,loan_size_max desc,loan_size_min desc ";
            $rate = $r->getRow($sql);
            if( !$rate ){
                return new result(false,'No match rate',null,errorCodesEnum::NO_LOAN_INTEREST);
            }
        }

        $interest_info = (array)$rate;

        $interest_rate = $rate['interest_rate'];
        $rate_re = loan_baseClass::interestRateConversion($interest_rate,$rate['interest_rate_unit'],interestRatePeriodEnum::MONTHLY);
        if( $rate_re->STS ){
            $interest_rate = $rate_re->DATA;
        }

        $operation_rate = $rate['operation_fee'];
        $operate_re = loan_baseClass::interestRateConversion($operation_rate,$rate['operation_fee_unit'],interestRatePeriodEnum::MONTHLY);
        if( $operate_re->STS ){
            $operation_rate = $operate_re->DATA;
            $rate['operation_fee'] = $operation_rate;
        }

        $total_rate = round($interest_rate+$operation_rate,2); // 展示值

        $interestClass = interestTypeClass::getInstance(interestPaymentEnum::ANNUITY_SCHEME,interestRatePeriodEnum::MONTHLY);
        $periods = $interestClass->getRepaymentPeriods($loan_month, loanPeriodUnitEnum::MONTH);
        $preview_re = $interestClass->getInstallmentSchema($amount,$periods,$interest_info);
        $preview_data = $preview_re->DATA;
        $repayment_total = $preview_data['payment_total'];
        return new result(true,'success',array(
            'total_interest_rate' => $total_rate.'%',
            'total_repayment_amount' => $repayment_total['total_payment'],
            //'repayment_schema' => $preview_data['payment_schema']
        ));

    }

}