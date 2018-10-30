<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/11
 * Time: 15:01
 */

// 贷款基类
class loan_baseClass
{
    public function __construct()
    {

    }


    /** 信用额度计算器
     * @param $params
     * @return result
     */
    public static function creditLimitCalculator($params)
    {

        $level = array();
        $m = new loan_credit_cert_levelModel();
        $level_list = $m->getAll();
        foreach( $level_list as $value ){
            $item = array(
                'value' => $value['match_value'],
                'credit' => $value['max_amount']?:$value['min_amount'],
            );
            $level[] = $item;
        }

        // 先计算组合值
        $sum = 0;
        $values = trim(($params['values']));
        $values = trim($values,',');
        if( $values ){
            $arr = explode(',',$values);
            foreach( $arr as $v){
                $v = intval($v);
                $sum = $sum | $v;
            }
        }


        // 匹配设置的组合
        $re = array();
        foreach( $level as $k=>$v ){
            if( ($v['value'] & $sum) == $v['value'] ){
                $re[$k] = $v;
            }
        }

        // 取设置的最大信用值
        $credit = 0;
        if( !empty($re) ){
            foreach( $re as $item ){
                if( $item['credit'] > $credit ){
                    $credit = $item['credit'];
                }
            }
        }

        return new result(true,'success',$credit);
    }

    /** 贷款合同编码
     * @param $member_guid
     * @param bool $is_temp ,是否临时合同编号 true 是 false 否
     * @return string
     */
    public static function generateLoanContractSn($grant_id,$branch_id,$member_obj_id,$cate_code,$sub_product_code,$is_temp=true)
    {
        $r = new ormReader();
        $branch_info = (new site_branchModel())->getBranchInfoById($branch_id);
        $branch_code = $branch_info['branch_number_code']?:'000';
        $member_uid = substr($member_obj_id,-6);

        // 获取当前是第几次授信
        $grant_info = (new member_credit_grantModel())->find(array(
            'uid' => $grant_id
        ));
        if( $grant_info ){
            $sql = "select count(*) cnt from member_credit_grant where member_id=".qstr($grant_info['member_id']).
                " and uid<".qstr($grant_id)." and state=".qstr(commonApproveStateEnum::PASS);
            $grant_circle = $r->getOne($sql) + 1;
        }else{
            $grant_circle = 1;
        }


        if( $is_temp ){
            // 临时合同编号
            $sql = "select count(*) cnt from loan_contract where client_obj_guid='$member_obj_id'  ";
            $num = $r->getOne($sql);
            $c_num = $num+1;
        }else{
            // 正式合同编号(规避了并发的情况)
            $sql = "select count(*) cnt from loan_contract where client_obj_guid='$member_obj_id'
            and state>=".qstr(loanContractStateEnum::PENDING_DISBURSE);
            $num = $r->getOne($sql);
            $c_num = $num+1;
        }

        $loan_circle = $c_num;

        //$code = (intval($branch_code)+intval($cate_code)+intval($member_uid)+intval($sub_product_code)+$loan_circle)%10;
        /*$sn = $branch_code.'-'.$cate_code.'-'.$sub_product_code.'-'.$member_uid.'-'.
            str_pad($loan_circle,3,'0',STR_PAD_LEFT).'-'.$code;*/

        $sn = $branch_code.'-'.$cate_code.'-'.$sub_product_code.'-'.$member_uid.'-'.
            str_pad($grant_circle,2,'0',STR_PAD_LEFT).'-'.str_pad($loan_circle,3,'0',STR_PAD_LEFT);

        return $is_temp?$sn.'-X':$sn;

    }


    /** 贷款合同编码
     * @param $member_guid
     * @param bool $is_temp ,是否临时合同编号 true 是 false 否
     * @return string
     */
    public static function generateLoanContractSn_old($product_code,$member_guid,$is_temp=true)
    {
        $m_account = new loan_accountModel();
        $account = $m_account->getRow(array(
            'obj_guid' => $member_guid
        ));
        $account_id = $account?$account->uid:0;
        //$prefix = contractPrefixSNEnum::LOAN;


        if( $is_temp ){
            // 临时合同编号
            /*$key = 'tmp_loan_contract_'.$member_guid;
            $num = (new core_gen_idModel())->genId($key);
            $c_num = intval($num)?:1;*/
            $sql = "select count(*) cnt from loan_contract where account_id='$account_id' ";
            $num = $m_account->reader->getOne($sql);
            $c_num = $num+1;
        }else{
            // 正式合同编号(规避了并发的情况)
            $sql = "select count(*) cnt from loan_contract where account_id='$account_id' 
            and state>=".qstr(loanContractStateEnum::PENDING_DISBURSE);
            $num = $m_account->reader->getOne($sql);
            $c_num = $num+1;
        }

        $code = ($product_code+$member_guid+$c_num)%10;  // 验证码 1位
        $sn = str_pad($product_code,3,'0',STR_PAD_LEFT) . '-'.$member_guid.'-'.str_pad($c_num,3,'0',STR_PAD_LEFT).'-'.$code;

        return $is_temp?$sn.'-X':$sn;

    }

    public static function generateInnerContractSn($grant_id,$branch_id,$member_obj_id,$cate_code,$sub_product_code)
    {
        $r = new ormReader();
        $branch_info = (new site_branchModel())->getBranchInfoById($branch_id);
        $branch_code = $branch_info['branch_number_code']?:'000';
        $member_uid = substr($member_obj_id,-6);

        // 获取当前是第几次授信
        $grant_info = (new member_credit_grantModel())->find(array(
            'uid' => $grant_id
        ));

        if( $grant_info ){
            $sql = "select count(*) from member_credit_grant where member_id=".qstr($grant_info['member_id']).
                " and uid<".qstr($grant_id)." and state=".qstr(commonApproveStateEnum::PASS);
            $grant_circle = $r->getOne($sql) + 1;
        }else{
            $grant_circle = 1;
        }



        /*$sql = "select count(uid) cnt from loan_contract where client_obj_guid=".qstr($member_obj_id).
        " and credit_grant_id=".qstr($grant_id)." and state>=".qstr(loanContractStateEnum::PENDING_DISBURSE);
        $loan_num = $r->getOne($sql);
        $loan_circle = $loan_num+1;*/

        $code = (intval($branch_code)+intval($cate_code)+intval($member_uid)+intval($sub_product_code)+$grant_circle)%10;
        $sn = $branch_code.'-'.$cate_code.'-'.$sub_product_code.'-'.$member_uid.'-'.
            str_pad($grant_circle,2,'0',STR_PAD_LEFT).'-'.$code;
        return $sn;

    }


    /** 保险合同编号规则
     * @param int $member_guid
     * @return string
     */
    public static function generateInsuranceContractSn($member_guid=0)
    {
        $m_account = new insurance_accountModel();
        $account = $m_account->getRow(array(
            'obj_guid' => $member_guid
        ));
        $account_id = $account?$account->uid:0;
        $sql = "select count(*) from insurance_contract where account_id='$account_id' ";
        $num = $m_account->reader->getOne($sql);
        $c_num = $num+1;
        $prefix = contractPrefixSNEnum::INSURANCE;
        $code = ($prefix+$member_guid+$c_num)%10;  // 验证码 1位
        $sn = $prefix . '-'.$member_guid.'-'.str_pad($c_num,3,'0',STR_PAD_LEFT).'-'.$code;
        return $sn;
    }


    /** 计算贷款天数
     * @param $value
     * @param $unit
     * @return result
     */
    public static function calLoanDays($value,$unit)
    {
        $value = intval($value);
        $loan_days = 0;
        switch($unit){
            case loanPeriodUnitEnum::DAY:
                $loan_days = $value;
                break;
            case loanPeriodUnitEnum::MONTH:
                $loan_days = $value*30;
                break;
            case loanPeriodUnitEnum::YEAR :
                $loan_days = $value*360;  // 按360天算
                break;
            default:
                return new result(false,'Non supported loan period type',null,errorCodesEnum::NOT_SUPPORTED);
        }
        return new result(true,'success',$loan_days);
    }



    /** 计算分期还款期数
     * @param $loan_time *贷款时间
     * @param $loan_time_unit  *贷款时间单位
     * @param $payment_type *还款方式
     * @param $payment_period *还款周期，比如一月还一次，一周还一次
     * @return result
     */
    public static function calPaymentPeriod($loan_time,$loan_time_unit,$payment_type,$payment_period)
    {
        // $loan_time,$loan_time_unit,$payment_type,$payment_period

        if( $loan_time < 1 ){
            return new result(false,'Time error',null,errorCodesEnum::NOT_SUPPORTED);
        }

        if( $payment_type == interestPaymentEnum::SINGLE_REPAYMENT || $payment_type == interestPaymentEnum::ADVANCE_SINGLE_REPAYMENT){
            // 一次性还款没有累计计算利息
            return new result(true,'success',1);
        }

        switch( $loan_time_unit ){
            case loanPeriodUnitEnum::YEAR :
                switch( $payment_period ){
                    case interestRatePeriodEnum::YEARLY :
                        $total_period = ceil($loan_time);
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY :
                        $total_period = ceil($loan_time*2);
                        break;
                    case interestRatePeriodEnum::QUARTER :
                        $total_period = ceil($loan_time*4);
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $total_period = ceil($loan_time*12);
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $total_period = ceil($loan_time*365/7);
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $total_period = ceil($loan_time*365);
                        break;
                    default:
                        return new result(false,'Not supported payment period',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            case loanPeriodUnitEnum::MONTH :
                switch( $payment_period ){
                    case interestRatePeriodEnum::YEARLY :
                        $total_period = ceil($loan_time/12);
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY :
                        $total_period = ceil($loan_time/6);
                        break;
                    case interestRatePeriodEnum::QUARTER :
                        $total_period = ceil($loan_time/3);
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $total_period = ceil($loan_time);
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $total_period = ceil($loan_time*30/7);
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $total_period = ceil($loan_time*30);
                        break;
                    default:
                        return new result(false,'Not supported payment period',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            case loanPeriodUnitEnum::DAY :
                switch( $payment_period ){
                    case interestRatePeriodEnum::YEARLY :
                        $total_period = ceil($loan_time/365);
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY :
                        $total_period = ceil($loan_time*2/365);
                        break;
                    case interestRatePeriodEnum::QUARTER :
                        $total_period = ceil($loan_time/120);
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $total_period = ceil($loan_time/30);
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $total_period = ceil($loan_time/7);
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $total_period = ceil($loan_time);
                        break;
                    default:
                        return new result(false,'Not supported payment period',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            default:
                return new result(false,'Not supported loan time type',null,errorCodesEnum::NOT_SUPPORTED);
        }


        $total_period = ($total_period >=1 )?$total_period:1;
        return new result(true,'success',$total_period);
    }


    /** 获得贷款还款日
     * @param $loan_days
     * @param $repayment_type
     * @param $repayment_period
     * @param $first_payment_date -第一次还款日
     * @param $last_payment_date - 最后一次还款日
     * @return array()
     */
    public static function getLoanDueDate($repayment_type,$repayment_period,$first_payment_date,$last_payment_date)
    {
        $first_payment_time = strtotime($first_payment_date);
        $due_date = '';
        $due_date_type = dueDateTypeEnum::FIXED_DATE;
        if( !interestTypeClass::isPeriodicRepayment($repayment_type) ){
            $due_date = date('Y-m-d',strtotime($last_payment_date));
        }else{

            switch( $repayment_period ){
                case interestRatePeriodEnum::DAILY:
                    $due_date_type = dueDateTypeEnum::PER_DAY;
                    break;
                case interestRatePeriodEnum::WEEKLY:
                    $due_date = date('w',$first_payment_time);  // 每周几 0-6
                    $due_date_type = dueDateTypeEnum::PER_WEEK;
                    break;
                case interestRatePeriodEnum::MONTHLY :
                    $due_date = date('d',$first_payment_time);
                    $due_date_type = dueDateTypeEnum::PER_MONTH;
                    break;
                case interestRatePeriodEnum::QUARTER :
                    $first = date('m-d',$first_payment_time);
                    $second = date('m-d',strtotime('+3 month',$first_payment_time));
                    $third = date('m-d',strtotime('+6 month',$first_payment_time));
                    $fourth = date('m-d',strtotime('+9 month',$first_payment_time));
                    $due_date = $first.','.$second.','.$third.','.$fourth;
                    $due_date_type = dueDateTypeEnum::PER_YEAR;
                    break;
                case interestRatePeriodEnum::SEMI_YEARLY :
                    $first = date('m-d',$first_payment_time);
                    $second = date('m-d',strtotime('+6 month',$first_payment_time));
                    $due_date = $first.','.$second;
                    $due_date_type = dueDateTypeEnum::PER_YEAR;
                    break;
                case interestRatePeriodEnum::YEARLY :
                    $due_date = date('m-d',$first_payment_time);
                    $due_date_type = dueDateTypeEnum::PER_YEAR;
                    break;
                default:
                    $due_date = '';
            }

        }
        return array(
            'due_date' => $due_date,
            'due_date_type' => $due_date_type
        );

    }





    /**  获得贷款详细产品和利率信息
     * @param $member_id
     * @param $product_id  ->二级产品的ID
     * @param $loan_amount
     * @param $currency
     * @param $loan_days
     * @param $payment_type
     * @param $payment_period
     * @param array $extend_info
     * @return result
     */
    public static function getLoanInterestDetail($member_id,$product_id,$loan_amount,$currency,$loan_days,$extend_info=array())
    {
        $m_sub_product = new loan_sub_productModel();
        $product_info = $m_sub_product->find(array(
            'uid' => $product_id,
        ));
        if( !$product_info ){
            return new result(false,'No product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }

        $member_id = intval($member_id);
        $loan_days = intval($loan_days);
        $loan_amount = round($loan_amount,2);

        $reader = new ormReader();

        // 基础利率
        // 贷款时长、金额、货币
        $sql = "select * from loan_product_size_rate where product_id='".$product_info['uid']."' and loan_size_min<='$loan_amount' ";
        $sql .= " and loan_size_max>='$loan_amount'  ";
        $sql .= " and min_term_days<='$loan_days' and max_term_days>='$loan_days' and currency='$currency'
        and (interest_rate+interest_rate_mortgage1+interest_rate_mortgage2+interest_min_value+operation_fee+operation_fee_mortgage1+operation_fee_mortgage2+operation_min_value+service_fee) >0
        order by interest_rate desc ";
        $interest_info = $reader->getRow($sql);


        if( !$interest_info ){
            logger::record('interest_sql',$sql,'loan_interest_match');
            return new result(false,'Did not set interest: '." $loan_amount ".$currency.' --- '.$loan_days.' days ',null,errorCodesEnum::NO_LOAN_INTEREST);
        }

        $credit_grant = member_credit_grantClass::getMemberLastGrantInfo($member_id);


        // 是否有抵押物
        // 修正抵押类型的利率

        if( $extend_info['member_credit_category_id'] ){

            // 得到产品下的资产列表
            $grant_id = intval($credit_grant['uid']);
            $m_member_category = new member_credit_categoryModel();
            $member_category = $m_member_category->find(array(
                'uid' => $extend_info['member_credit_category_id']
            ));

            $loan_category = (new loan_categoryModel())->find(array(
                'uid' => $member_category['category_id']
            ));

            // 优先取package的设置的利率
            $interest_list = loan_productClass::getSizeRateByPackageId($member_category['interest_package_id'],$product_id);

            if( count($interest_list) > 0 ){
                // 匹配条件
                foreach( $interest_list as $item ){

                    if( $item['loan_size_min'] <= $loan_amount
                        && $item['loan_size_max'] >= $loan_amount
                        && $item['min_term_days'] <= $loan_days
                        && $item['max_term_days'] >= $loan_days
                        && $item['currency'] == $currency
                    ){

                        // 过滤掉is_active=0的，add by tim
                        if( $item['is_active'] ){

                            // 过滤掉0的
                            if( $item['interest_rate'] <=0
                                && $item['interest_rate_mortgage1'] <= 0
                                && $item['interest_rate_mortgage2']<=0
                                && $item['interest_min_value'] <=0
                                && $item['operation_fee'] <=0
                                && $item['operation_fee_mortgage1'] <=0
                                && $item['operation_fee_mortgage2'] <= 0
                                && $item['operation_min_value'] <= 0
                                && $item['service_fee'] <= 0
                            ){
                                continue;

                            }else{
                                $interest_info = $item;
                                break;
                            }
                        }

                    }

                }
            }

            if( !$interest_info ){ //add by tim,认为利息都要去设置category-id 的special-rate的is_active
                return new result(false,'Did not set interest: '." $loan_amount ".$currency.' --- '.$loan_days.' days ',null,errorCodesEnum::NO_LOAN_INTEREST);
            }


            $asset_list = $m_member_category->getMortgagedAssetListByCategoryId($extend_info['member_credit_category_id'],$grant_id);
            if( $asset_list ){
                $mortgage_hard = false;
                foreach( $asset_list as $v ){
                    if( $v['asset_cert_type'] == assetsCertTypeEnum::HARD ){
                        $mortgage_hard = true;
                        break;
                    }
                }

                if( $mortgage_hard ){

                    $interest_info['interest_rate'] = $interest_info['interest_rate_mortgage2'];
                    $interest_info['operation_fee'] = $interest_info['operation_fee_mortgage2'];

                }else{

                    $interest_info['interest_rate'] = $interest_info['interest_rate_mortgage1'];
                    $interest_info['operation_fee'] = $interest_info['operation_fee_mortgage1'];

                }

            }

            // todo 暂时 one time 才取设置的特殊值
            if( $member_category['is_one_time'] || $loan_category['special_key'] == specialLoanCateKeyEnum::QUICK_LOAN ){
                // 最后再来匹配member的特殊利率
                switch( $currency ){
                    case currencyEnum::USD:
                        $interest_info['interest_rate'] = $member_category['interest_rate_usd'];
                        $interest_info['operation_fee'] = $member_category['operation_fee_usd'];
                        break;
                    case currencyEnum::KHR:
                        $interest_info['interest_rate'] = $member_category['interest_rate_khr'];
                        $interest_info['operation_fee'] = $member_category['operation_fee_khr'];
                        break;
                    default:
                        break;
                }
            }





        }else{

            // todo 信用贷已经取消从member的资产判断了，资产已分配到category上
            /*if( $member_id > 0 ){
                $mortgage_list = memberClass::getMemberMortgageAssetList($member_id);

                if( !empty($mortgage_list) ){

                    $mortgage_hard = false;
                    foreach( $mortgage_list as $v ){
                        if( $v['asset_cert_type'] == assetsCertTypeEnum::HARD ){
                            $mortgage_hard = true;
                            break;
                        }
                    }

                    if( $mortgage_hard ){

                        $interest_info['interest_rate'] = $interest_info['interest_rate_mortgage2'];
                        $interest_info['operation_fee'] = $interest_info['operation_fee_mortgage2'];

                    }else{

                        $interest_info['interest_rate'] = $interest_info['interest_rate_mortgage1'];
                        $interest_info['operation_fee'] = $interest_info['operation_fee_mortgage1'];

                    }

                }
            }*/

        }



        $special_rate = array();
        if( $interest_info['special_rate_id'] > 0 ){
            $special_rate = $interest_info;
            $special_rate['uid'] = $interest_info['special_rate_id'];
        }

        return new result(true,'success',array(
            'product_info' => $product_info,
            'interest_info' => $interest_info,  // 计算用利率
            'size_rate' => $interest_info,
            'special_rate' => $special_rate
        ));
    }




    /** 获得贷款详细产品和利率信息
     * @param $loan_amount     *贷款金额
     * @param $loan_days       *贷款天数
     * @param $payment_type    *还款方式
     * @param $payment_period  *还款周期
     * @return result
     */
    public static function getLoanInterestDetail_old($member_id,$product_id,$loan_amount,$currency,$loan_days,$payment_type,$payment_period,$extend_info=array())
    {
        $m_sub_product = new loan_sub_productModel();
        $product_info = $m_sub_product->find(array(
            'uid' => $product_id,
        ));
        if( !$product_info ){
            return new result(false,'No product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }

        $loan_days = intval($loan_days);
        $loan_amount = round($loan_amount,2);

        $reader = new ormReader();

        // 贷款时长、金额、货币、还款方式、还款周期满足
        $sql = "select * from loan_product_size_rate where product_id='".$product_info['uid']."' and loan_size_min<='$loan_amount' ";
        $sql .= " and loan_size_max>='$loan_amount'  ";
        $sql .= " and min_term_days<='$loan_days' and max_term_days>='$loan_days' and currency='$currency' ";


        $interest_array = $reader->getRows($sql);
        if( count($interest_array) < 1 ){
            return new result(false,'Did not set interest: '." $loan_amount --- ".$currency.' --- '.$loan_days.' --- '.$payment_type.' --- '.$payment_period,null,errorCodesEnum::NO_LOAN_INTEREST);
        }

        $interest_info = $reader->getRow($sql);
        if( !$interest_info ){
            return new result(false,'Did not set interest: '." $loan_amount --- ".$currency.' --- '.$loan_days.' --- '.$payment_type.' --- '.$payment_period,null,errorCodesEnum::NO_LOAN_INTEREST);
        }

        //$interest_info = array();
        // 附加条件利率
        $guarantee_type = $extend_info['guarantee_type']?$extend_info['guarantee_type']:null;
        $mortgage_type = $extend_info['mortgage_type']?$extend_info['mortgage_type']:null;

        // 先取主利率
       /* foreach( $interest_array as $interest ){

            if( $interest['guarantee_type'] == $guarantee_type && $interest['mortgage_type'] == $mortgage_type  ){
                $interest_info = $interest;
                break;
            }else{

                // 高配到低配
                if( $guarantee_type && !$mortgage_type && $interest['guarantee_type'] == $guarantee_type ){
                    $interest_info = $interest;
                    break;
                }elseif( $mortgage_type && !$guarantee_type && $interest['mortgage_type'] == $mortgage_type ){
                    $interest_info = $interest;
                    break;
                }else{
                    $interest_info = $interest; // 取到最新设置的一个利率
                }

            }

        }*/


        if( !$interest_info || empty($interest_info) ){
            return new result(false,'No interest info',null,errorCodesEnum::NO_LOAN_INTEREST);
        }

        $size_rate = $interest_info;

        // 再去优先匹配特殊利率
        $member_grade = $extend_info['member_grade'];
        $is_staff = $extend_info['is_staff']?1:0;
        $is_government = $extend_info['is_government']?1:0;
        $is_rival_client = $extend_info['is_rival_client']?1:0;



        $special_rate = null;
        // 先处理特殊客户的利率，会员等级有可能有等级没设置利率的情况
        if( $is_staff || $is_government ||  $is_rival_client ){

            $sql = "select * from loan_product_special_rate where size_rate_id='".$interest_info['uid']."' and 1=1 ";
            // 多个条件满足只匹配一个 优先顺序是内部员工-对手客户-政府员工
            if( $is_staff ){
                $sql .= " and client_type='".clientTypeRateEnum::STAFF."' ";
            }elseif( $is_rival_client ){
                $sql .= " and client_type='".clientTypeRateEnum::RIVAL_CLIENT."' ";
            }else{
                $sql .= " and client_type='".clientTypeRateEnum::GOVERNMENT."' ";
            }
            $sql .= " order by interest_rate desc ";

            $rows = $reader->getRows($sql);
            if( count($rows) > 0 ){

                // 初始化一个基本利率
                $special_rate = reset($rows);
                // 同时有member_grade
                if( $member_grade ){
                    foreach( $rows as $v ){
                        if( $v['client_grade'] == $member_grade ){  // 匹配等级
                            $special_rate = $v;
                            break;
                        }
                    }
                }

            }

        }else{

            // 只有等级
            if( $member_grade ){
                $sql = "select * from loan_product_special_rate where size_rate_id='".$interest_info['uid']."' and client_grade='".$member_grade."' and ( client_type is null or client_type=0 )  order by interest_rate desc ";
                $special_rate = $reader->getRow($sql);
            }
        }

        // 存在特殊利率
        if( $special_rate ){

            unset($special_rate['uid']);
            $interest_info = array_merge((array)$interest_info,(array)$special_rate);

            /*$interest_info['interest_rate'] = $special_rate['interest_rate'];
            $interest_info['interest_rate_type'] = $special_rate['interest_rate_type'];
            $interest_info['interest_min_value'] = $special_rate['interest_min_value'];
            $interest_info['admin_fee'] = $special_rate['admin_fee'];
            $interest_info['admin_fee_type'] = $special_rate['admin_fee_type'];
            $interest_info['operation_fee'] = $special_rate['operation_fee'];
            $interest_info['operation_fee_type'] = $special_rate['operation_fee_type'];
            $interest_info['operation_min_value'] = $special_rate['operation_min_value'];*/
        }

        // 修正抵押类型的利率
        if( $extend_info['mortgage_soft'] || $extend_info['mortgage_hard'] ){
            if( $interest_info['interest_rate_mortgage1'] > 0 ){
                $interest_info['interest_rate'] = $interest_info['interest_rate_mortgage1'];
            }

        }


        return new result(true,'success',array(
            'product_info' => $product_info,
            'interest_info' => $interest_info,  // 计算用利率
            'size_rate' => $size_rate,
            'special_rate' => $special_rate
        ));
    }


    /** 利率转换
     * @param $value
     * @param $from_type @当前利率周期
     * @param $to_type @目标利率周期
     * @param $true_days @一年天数类型 true 365 false 360
     * @return result
     */
    public static function interestRateConversion($value,$from_type,$to_type,$true_days=false)
    {
        $new_value = $value;
        $year_days = $true_days?365:360;
        switch( $from_type ){
            case interestRatePeriodEnum::YEARLY :
                switch( $to_type ){
                    case interestRatePeriodEnum::YEARLY :
                        $new_value = $value;
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        $new_value = $value/2;
                        break;
                    case interestRatePeriodEnum::QUARTER:
                        $new_value = $value/4;
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $new_value = $value/12;
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $new_value = $value/$year_days*7;
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $new_value = $value/$year_days;
                        break;
                    default:
                        return new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            case interestRatePeriodEnum::SEMI_YEARLY:
                switch( $to_type ){
                    case interestRatePeriodEnum::YEARLY :
                        $new_value = $value*2;
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        $new_value = $value;
                        break;
                    case interestRatePeriodEnum::QUARTER:
                        $new_value = $value/2;
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $new_value = $value/6;
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $new_value = $value*2/$year_days*7;
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $new_value = $value*2/$year_days;
                        break;
                    default:
                        return new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            case interestRatePeriodEnum::QUARTER:
                switch( $to_type ){
                    case interestRatePeriodEnum::YEARLY :
                        $new_value = $value*4;
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        $new_value = $value*2;
                        break;
                    case interestRatePeriodEnum::QUARTER:
                        $new_value = $value;
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $new_value = $value/3;
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $new_value = $value*4/$year_days*7;
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $new_value = $value*4/$year_days;
                        break;
                    default:
                        return new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            case interestRatePeriodEnum::MONTHLY :
                switch( $to_type ){
                    case interestRatePeriodEnum::YEARLY :
                        $new_value = $value*12;
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        $new_value = $value*6;
                        break;
                    case interestRatePeriodEnum::QUARTER:
                        $new_value = $value*3;
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $new_value = $value;
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $new_value = $value*12/$year_days*7;
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $new_value = $value*12/$year_days;
                        break;
                    default:
                        return new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            case interestRatePeriodEnum::WEEKLY :
                switch( $to_type ){
                    case interestRatePeriodEnum::YEARLY :
                        $new_value = $value/7*$year_days;
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        $new_value = $value/7*$year_days/2;
                        break;
                    case interestRatePeriodEnum::QUARTER:
                        $new_value = $value/7*$year_days/4;
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $new_value = $value/7*$year_days/12;
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $new_value = $value;
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $new_value = $value/7;
                        break;
                    default:
                        return new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            case interestRatePeriodEnum::DAILY :
                switch( $to_type ){
                    case interestRatePeriodEnum::YEARLY :
                        $new_value = $value*$year_days;
                        break;
                    case interestRatePeriodEnum::SEMI_YEARLY:
                        $new_value = $value*$year_days/2;
                        break;
                    case interestRatePeriodEnum::QUARTER:
                        $new_value = $value*$year_days/4;
                        break;
                    case interestRatePeriodEnum::MONTHLY :
                        $new_value = $value*$year_days/12;
                        break;
                    case interestRatePeriodEnum::WEEKLY :
                        $new_value = $value*7;
                        break;
                    case interestRatePeriodEnum::DAILY :
                        $new_value = $value;
                        break;
                    default:
                        return new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
                }
                break;
            default:
                return new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
        }

        return new result(true,'success',$new_value);
    }


    /** 计算贷款的结束日期时间戳
     * @param $loan_period
     * @param $loan_period_unit
     * @param $startTimestamp
     * @return result
     */
    public static function getLoanEndDateTimestamp($loan_period,$loan_period_unit,$startTimestamp)
    {
        $loan_period = intval($loan_period);

        switch( $loan_period_unit ){
            case loanPeriodUnitEnum::YEAR:
                $entTimestamp = strtotime('+'.$loan_period.' year',$startTimestamp);
                break;
            case loanPeriodUnitEnum::MONTH:
                $entTimestamp = strtotime('+'.$loan_period.' month',$startTimestamp);
                break;
            case loanPeriodUnitEnum::DAY:
                $entTimestamp = strtotime('+'.$loan_period.' day',$startTimestamp);
                break;
            default:
                return new result(false,'Not supported loan period type.',null,errorCodesEnum::NOT_SUPPORTED);
        }

        return new result(true,'success',$entTimestamp);
    }


    /** 获得分期的时间间隔
     * @param $payment_period
     * @return result
     */
    public static function getInstalmentPaymentTimeInterval($payment_period)
    {
        // 分期
        switch ( $payment_period ) {
            case interestRatePeriodEnum::YEARLY :
                $arr = array(
                    'value' => 1,
                    'unit' => 'year'
                );
                break;
            case interestRatePeriodEnum::SEMI_YEARLY :
                $arr = array(
                    'value' => 6,
                    'unit' => 'month'
                );
                break;
            case interestRatePeriodEnum::QUARTER :
                $arr = array(
                    'value' => 3,
                    'unit' => 'month'
                );
                break;
            case interestRatePeriodEnum::MONTHLY :
                $arr = array(
                    'value' => 1,
                    'unit' => 'month'
                );
                break;
            case interestRatePeriodEnum::WEEKLY :
                $arr = array(
                    'value' => 1,
                    'unit' => 'week'
                );
                break;
            case interestRatePeriodEnum::DAILY :
                $arr = array(
                    'value' => 1,
                    'unit' => 'day'
                );
                break;
            default:
                return new result(false, 'Not supported payment period', null, errorCodesEnum::NOT_SUPPORTED);
        }
        return new result(true,'success',$arr);
    }


    /** 验证贷款时间和还款周期是否匹配
     * @return bool
     */
    public static function verifyLoanTimeAndRepaymentPeriod($loan_time,$loan_time_unit,$repayment_type,$repayment_period)
    {
        if( interestTypeClass::isOnetimeRepayment($repayment_type) ){
            return true;
        }else{

            switch ($loan_time_unit){
                case loanPeriodUnitEnum::YEAR :
                    return true;
                    break;
                case loanPeriodUnitEnum::MONTH :
                    switch ($repayment_period)
                    {
                        case interestRatePeriodEnum::YEARLY:
                            return $loan_time>=12?true:false;
                            break;
                        case interestRatePeriodEnum::SEMI_YEARLY:
                            return $loan_time>=6?true:false;
                            break;
                        case interestRatePeriodEnum::QUARTER:
                            return $loan_time>=3?true:false;
                            break;
                        default:
                            return true;
                    }
                    break;
                case loanPeriodUnitEnum::DAY :
                    if( $repayment_period == interestRatePeriodEnum::DAILY ){
                        return true;
                    }
                    if( $repayment_period == interestRatePeriodEnum::WEEKLY ){
                        return $loan_time>=7?true:false;
                    }
                    return false;
                    break;
                default:
                    return false;
            }
        }
        return false;
    }


    /**  获得还款详细
     * @param $loan_amount
     * @param $loan_days *具体贷款天数
     * @param $loan_time *贷款时间
     * @$loan_time_unit *贷款时间单位，如year month day
     * @param $interest_info
     * @param $payment_type
     * @param $payment_period
     * @return mixed|null|result
     */
    public function getPaymentDetail($loan_amount,$loan_days,$loan_time,$loan_time_unit,$interest_info,$payment_type,$payment_period,$start_date=null,$first_repayment_date=null,$loan_account_info=array())
    {
        try {
            $principal_paid_month = $loan_account_info['principal_periods']?:6;
            $interest_class = interestTypeClass::getInstance($payment_type, $payment_period,$principal_paid_month);
            $periods = $interest_class->getRepaymentPeriods($loan_time, $loan_time_unit,$start_date,$first_repayment_date);
            return $interest_class->getInstallmentSchema($loan_amount, $periods, $interest_info);
        } catch(Exception $ex) {
            return new result(false, $ex->getMessage(), null, $ex->getCode());
        }

    }


    public  function getRepaymentSchemaOfAllType($payment_type,$loan_amount,$loan_days,$interest_info,$total_period)
    {

        switch( $payment_type ){
            case interestPaymentEnum::SINGLE_REPAYMENT :
                $re = $this->getPaymentDetailOfSingleRepayment($loan_amount,$loan_days,$interest_info,$total_period);
                break;
            case interestPaymentEnum::ADVANCE_SINGLE_REPAYMENT:
                $re = $this->getPaymentDetailOfAdvanceSingleRepayment($loan_amount,$loan_days,$interest_info,$total_period);
                break;
            case interestPaymentEnum::FIXED_PRINCIPAL :
                $re = $this->getPaymentDetailOfFixedPrincipal($loan_amount,$loan_days,$interest_info,$total_period);
                break;
            case interestPaymentEnum::ANNUITY_SCHEME :
                $re = $this->getPaymentDetailOfAnnuitySchema($loan_amount,$loan_days,$interest_info,$total_period);
                break;
            case interestPaymentEnum::FLAT_INTEREST :
                $re = $this->getPaymentDetailOfFlatInterest($loan_amount,$loan_days,$interest_info,$total_period);
                break;
            case interestPaymentEnum::BALLOON_INTEREST :
                $re = $this->getPaymentDetailOfBalloonInterest($loan_amount,$loan_days,$interest_info,$total_period);
                break;
            default:
                return new result(false,'Not supported payment type',null,errorCodesEnum::NOT_SUPPORTED);
        }

        return $re;
    }

    /** 一次还款详细
     * @param $loan_amount
     * @param $loan_period
     * @param $interest_info  ***利率信息是换算过的期利率（如设置的年利率，按月还，利率是换算过的月利率）
     * @param $payment_period
     * @return result
     */
    protected function getPaymentDetailOfSingleRepayment($loan_amount,$loan_period,$interest_info,$payment_period)
    {

        $min_interest = $interest_info['interest_min_value'];

        if( $interest_info['interest_rate_type'] == 1 ){
            $interest_rate = $interest_info['interest_rate'];
            $re = loan_calculatorClass::single_repayment_getPaymentSchemaByFixInterestAmount($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }else{
            $interest_rate = $interest_info['interest_rate']/100;
            $re = loan_calculatorClass::single_repayment_getPaymentSchemaByFixInterest($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }
        if( !$re->STS ){
            return new result(false,'Loan calculate fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true,'success',$re->DATA);
    }

    /** 提前扣利息一次还款详细
     * @param $loan_amount
     * @param $loan_period
     * @param $interest_info  ***利率信息是换算过的期利率（如设置的年利率，按月还，利率是换算过的月利率）
     * @param $payment_period
     * @return result
     */
    protected function getPaymentDetailOfAdvanceSingleRepayment($loan_amount,$loan_period,$interest_info,$payment_period)
    {

        $min_interest = $interest_info['interest_min_value'];

        $interest_rate = $interest_info['interest_rate'] / 100;
        $re = loan_calculatorClass::advance_single_repayment_getPaymentSchemaByFixInterest($loan_amount, $interest_rate, $payment_period, $interest_info, $min_interest);
        if (!$re->STS) {
            return new result(false, 'Loan calculate fail', null, errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true, 'success', $re->DATA);
    }

    /** 等额本金还款详细
     * @param $loan_amount
     * @param $loan_period
     * @param $interest_info
     * @param $payment_period
     * @return result
     */
    protected function getPaymentDetailOfFixedPrincipal($loan_amount,$loan_period,$interest_info,$payment_period)
    {


        $min_interest = $interest_info['interest_min_value'];

        if( $interest_info['interest_rate_type'] == 1 ){
            $interest_rate = $interest_info['interest_rate'];
            $re = loan_calculatorClass::fixed_principle_getPaymentSchemaByFixInterestAmount($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }else{
            $interest_rate = $interest_info['interest_rate']/100;
            $re = loan_calculatorClass::fixed_principle_getPaymentSchemaByFixInterest($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }
        if( !$re->STS ){
            return new result(false,'Loan calculate fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true,'success',$re->DATA);
    }


    /**  等额本息还款详细
     * @param $loan_amount
     * @param $loan_period
     * @param $interest_info
     * @param $payment_period
     * @return result
     */
    protected function getPaymentDetailOfAnnuitySchema($loan_amount,$loan_period,$interest_info,$payment_period)
    {

        $min_interest = $interest_info['interest_min_value'];

        if( $interest_info['interest_rate_type'] == 1 ){
            $interest_rate = $interest_info['interest_rate'];
            $re = loan_calculatorClass::annuity_schema_getPaymentSchemaByFixInterestAmount($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }else{
            $interest_rate = $interest_info['interest_rate']/100;
            $re = loan_calculatorClass::annuity_schema_getPaymentSchemaByFixInterest($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }
        if( !$re->STS ){
            return new result(false,'Loan calculate fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true,'success',$re->DATA);
    }

    /** 固定期息还款详细
     * @param $loan_amount
     * @param $loan_period
     * @param $interest_info
     * @param $payment_period
     * @return result
     */
    protected function getPaymentDetailOfFlatInterest($loan_amount,$loan_period,$interest_info,$payment_period)
    {

        $min_interest = $interest_info['interest_min_value'];

        if( $interest_info['interest_rate_type'] == 1 ){
            $interest_rate = $interest_info['interest_rate'];
            $re = loan_calculatorClass::flat_interest_getPaymentSchemaByFixInterestAmount($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }else{
            $interest_rate = $interest_info['interest_rate']/100;
            $re = loan_calculatorClass::flat_interest_getPaymentSchemaByFixInterest($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }
        if( !$re->STS ){
            return new result(false,'Loan calculate fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true,'success',$re->DATA);
    }

    /** 先利息后本金还款详细
     * @param $loan_amount
     * @param $loan_period
     * @param $interest_info
     * @param $payment_period
     * @return result
     */
    protected function getPaymentDetailOfBalloonInterest($loan_amount,$loan_period,$interest_info,$payment_period)
    {

        $min_interest = $interest_info['interest_min_value'];

        if( $interest_info['interest_rate_type'] == 1 ){
            $interest_rate = $interest_info['interest_rate'];
            $re = loan_calculatorClass::balloon_interest_getPaymentSchemaByFixInterestAmount($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }else{
            $interest_rate = $interest_info['interest_rate']/100;
            $re = loan_calculatorClass::balloon_interest_getPaymentSchemaByFixInterest($loan_amount,$interest_rate,$payment_period,$interest_info,$min_interest);
        }
        if( !$re->STS ){
            return new result(false,'Loan calculate fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true,'success',$re->DATA);
    }


    public function calculator($loan_amount,$loan_period,$loan_period_unit,$repayment_type,$repayment_period,$currency='USD',$extend=array())
    {
        $re = self::calLoanDays($loan_period,$loan_period_unit);
        if( !$re->STS ){
            return $re;
        }
        $loan_days = $re->DATA;
        $reader = new ormReader();

        if( interestTypeClass::isOnetimeRepayment($repayment_type) ){
            // 贷款时长、金额、还款方式满足
            $sql = "select r.*,p.sub_product_code product_code,p.sub_product_name product_name,r.currency from loan_product_size_rate r inner join loan_sub_product p on r.product_id=p.uid  where  r.loan_size_min<='$loan_amount' ";
            $sql .= " and r.loan_size_max>='$loan_amount' and r.interest_payment='$repayment_type'  ";
            $sql .= " and r.min_term_days<='$loan_days' and r.max_term_days>='$loan_days' and p.state='".loanProductStateEnum::ACTIVE."' ";
        }else{
            // 贷款时长、金额、还款方式、还款周期满足
            $sql = "select r.*,p.sub_product_code product_code,p.sub_product_name product_name,r.currency from loan_product_size_rate r inner join loan_sub_product p on r.product_id=p.uid where  r.loan_size_min<='$loan_amount' ";
            $sql .= " and r.loan_size_max>='$loan_amount' and r.interest_payment='$repayment_type' and r.interest_rate_period='$repayment_period'  ";
            $sql .= " and r.min_term_days<='$loan_days' and r.max_term_days>='$loan_days' and p.state='".loanProductStateEnum::ACTIVE."' ";
        }

        $products = $reader->getRows($sql);

        if( count($products) <1 ){
            return new result(false,'No matched product',null,errorCodesEnum::NO_LOAN_INTEREST);
        }

        $params = array(
            'amount' => $loan_amount,
            'loan_period' => $loan_period,
            'loan_period_unit' => $loan_period_unit,
            'repayment_type' => $repayment_type,
            'repayment_period' => $repayment_period
        );
        $list = array();
        foreach( $products as $product ){
            $product_id = $product['product_id'];
            $params['product_id'] = $product_id;
            $re = self::loanPreview($params);
            if( $re->STS ){
                $data = $re->DATA;
                $data['product_info'] = array(
                    'product_id' => $product_id,
                    'product_name' => $product['product_name'],
                    'product_code' => $product['product_code'],
                );
                $list[] = $data;
            }
        }

        return new result(true,'success',$list);

    }


    /** 创建贷款绑定保险产品合同
     * @param $loan_amount * 贷款金额
     * @param $loan_contract_id
     * @param $insurance_item_id
     * @param $member_id
     * @param int $amount
     * @param $is_temp bool 是否临时合同
     * @param array $extent
     * @return result
     */
    public function createLoanInsuranceContract($loan_amount,$loan_contract_id,$insurance_item_id,$member_id,$currency='USD',$extent=array())
    {

        $m_item = new insurance_product_itemModel();
        $m_member = new memberModel();
        $m_insurance = new insurance_productModel();
        $insurance_item = $m_item->getRow($insurance_item_id);
        if( !$insurance_item_id ){
            return new result(false,'Unknown insurance item',null,errorCodesEnum::NO_INSURANCE_ITEM);
        }
        $insurance_product_id = $insurance_item->product_id;
        $insurance_product = $m_insurance->getRow($insurance_product_id);
        if( !$insurance_product ){
            return new result(false,'No insurance product',null,errorCodesEnum::NO_INSURANCE_PRODUCT);
        }

        if( $insurance_product->state != insuranceProductStateEnum::ACTIVE ){
            return new result(false,'Insurance non execute product',null,errorCodesEnum::INSURANCE_PRODUCT_NX);
        }

        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::INVALID_PARAM);
        }


        $m_account = new insurance_accountModel();
        $insurance_account = $m_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        if( !$insurance_account){
            $insurance_account = $m_account->newRow();
            $insurance_account->obj_guid = $member->obj_guid;
            $insurance_account->account_type = insuranceAccountTypeEnum::MEMBER;
            $insurance_account->update_time = Now();
            $in = $insurance_account->insert();
            if( !$in->STS ){
                return new result(false,'Create insurance account fail',null,errorCodesEnum::DB_ERROR);
            }
        }
        $insurance_account_id = $insurance_account->uid;
        $m_contract = new insurance_contractModel();
        $contract_sn = self::generateInsuranceContractSn($member->obj_guid);

        $contract = $m_contract->newRow();
        $contract->account_id = $insurance_account_id;
        $contract->contract_sn = $contract_sn;
        $contract->create_time = Now();
        $contract->creator_id = 0;
        $contract->creator_name = 'System';
        $contract->product_id = $insurance_product_id;
        $contract->product_item_id = $insurance_item_id;
        $contract->start_date = Now();
        if( $insurance_item->is_fixed_valid_days ){
            $days = intval($insurance_item->fixed_valid_days);
            $contract->end_date = date('Y-m-d H:i:s',time()+$days*24*3600);
        }

        $loan_amount = round($loan_amount,2);
        // 保额 价格
        if( $insurance_item->is_fixed_amount ){
            $insurance_amount = $insurance_item->fixed_amount;  // 保额
            $insurance_price = $insurance_item->fixed_price;
        }else{
            $insurance_amount = $loan_amount;
            $insurance_price = $loan_amount*($insurance_item['price_rate']); // todo 具体百分比还是计算值

        }

        $contract->currency = $currency;
        $contract->start_insured_amount = $insurance_amount;
        $contract->price = $insurance_price;
        $contract->state = insuranceContractStateEnum::CREATE;
        $contract->loan_contract_id = $loan_contract_id;
        $insert = $contract->insert();
        if( !$insert->STS ){
            return new result(false,' Create insurance contract fail',null,errorCodesEnum::DB_ERROR);
        }

        // 创建计划,一次缴费
        $m_handler = new member_account_handlerModel();
        $handler = $m_handler->getRow(array(
            'member_id' => $member_id,
            'handler_type' => memberAccountHandlerTypeEnum::PARTNER_LOAN,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));
        $handler_id = $handler?$handler['uid']:0;
        $m_schema = new insurance_payment_schemeModel();
        $payment_schema = $m_schema->newRow();
        $payment_schema->contract_id = $contract->uid;
        $payment_schema->scheme_idx = 1;
        $payment_schema->scheme_name = 'Period 1';
        $payment_schema->payable_date = date('Y-m-d');
        $payment_schema->amount = $contract->price;
        $payment_schema->account_handler_id = $handler_id;
        $payment_schema->state = insuranceContractStateEnum::CREATE;
        $payment_schema->create_time = Now();
        $in = $payment_schema->insert();
        if( !$in->STS ){
            return new result(false,'Create insurance payment schema fail',null,errorCodesEnum::DB_ERROR);
        }

        // 受益人
        $m_benefit = new insurance_contract_beneficiaryModel();
        $new_row = $m_benefit->newRow();
        $new_row->contract_id = $contract->uid;
        $new_row->benefit_index = 1;
        $new_row->benefit_name = $member->display_name?:$member->login_code;
        $new_row->benefit_phone = $member->phone_id;
        $new_row->benefit_addr = '';
        $insert2 = $new_row->insert();
        if( !$insert2->STS ){
            return new result(false,'Create insurance contract benefit fail',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success',$contract);

    }


    public function loanPreviewBeforeCreateContract($loan_amount,$currency,$loan_time,$loan_time_unit,$repayment_type,$repayment_period,$interest_info)
    {

        // 计算目标天数
        $rt = self::calLoanDays($loan_time,$loan_time_unit);
        if( !$rt->STS ){
            return $rt;
        }
        $loan_days = $rt->DATA;
        if( $loan_days <= 0 ){
            return new result(false,'Invalid loan days',null,errorCodesEnum::INVALID_AMOUNT);
        }

        // 获得还款计划
        $payment_re = $this->getPaymentDetail($loan_amount,$loan_days,$loan_time,$loan_time_unit,$interest_info,$repayment_type,$repayment_period);
        if( !$payment_re->STS ){
            return new result(false,'Create installment schema fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }
        $re_data = $payment_re->DATA;
        $total_payment = $re_data['payment_total'];
        $payment_schema = $re_data['payment_schema'];

        // 管理费
        $admin_fee = 0;
        if( $interest_info['admin_fee'] ){

            if( $interest_info['admin_fee_type'] == 1 ){
                $admin_fee = $interest_info['admin_fee']+0;
            }else{
                $admin_fee = $loan_amount*( $interest_info['admin_fee']/100);
            }
        }

        // 贷款手续费
        $loan_fee = 0;
        if( $interest_info['loan_fee'] > 0 ){

            if( $interest_info['loan_fee_type'] == 1 ){
                $loan_fee = $interest_info['loan_fee'];
            }else{
                $loan_fee = round($loan_amount*($interest_info['loan_fee']/100),2);
            }
        }

        $return = array(
            'loan_amount' => $loan_amount,
            'currency' => $currency,
            'loan_time' => $loan_time,
            'loan_time_unit' => $loan_time_unit,
            'repayment_type' => $repayment_type,
            'repayment_period' => $repayment_period,
            'admin_fee' => $admin_fee,
            'loan_fee' => $loan_fee,
            'disbursement_amount' => $loan_amount-$admin_fee-$loan_fee,
            'total_interest' => $total_payment['total_interest'],
            'total_operation_fee' => $total_payment['total_operator_fee'],
            'interest_info' => $interest_info,
            'installment_schema' => $payment_schema,
            'total_repayment_detail' => $total_payment
        );

       return new result(true,'success',$return);


    }


    public static function loanPreviewBySizeRateInfo($size_rate)
    {

        $sub_product_info = (new loan_sub_productModel())->find(array(
            'uid' => $size_rate['product_id']
        ));

        if (!$sub_product_info) {
            return new result(false,'No product info:' . $size_rate['product_id'],null,errorCodesEnum::NO_DATA);
        }

        $product_id = $sub_product_info['uid'];
        $loan_amount = $size_rate['loan_size_max'];
        $currency = $size_rate['currency'];
        $repayment_type = $sub_product_info['interest_type'];
        $repayment_period = $sub_product_info['repayment_type'];

        $loan_days = $size_rate['max_term_days'];

        // 贷款周期需要换算一下
        if (interestTypeClass::isOnetimeRepayment($sub_product_info['interest_type'])) {
            $loan_period = $loan_days;
            $loan_period_unit = loanPeriodUnitEnum::DAY;
        } else {


            // 换算成周期单位
            switch ($repayment_period) {
                case interestRatePeriodEnum::DAILY:
                    $loan_period = $loan_days;
                    $loan_period_unit = loanPeriodUnitEnum::DAY;
                    break;
                case interestRatePeriodEnum::WEEKLY:
                case interestRatePeriodEnum::MONTHLY:
                case interestRatePeriodEnum::QUARTER:
                    $loan_period = floor($loan_days / 30);
                    $loan_period_unit = loanPeriodUnitEnum::MONTH;
                    break;
                case interestRatePeriodEnum::SEMI_YEARLY:
                case interestRatePeriodEnum::YEARLY:
                    $loan_period = floor($loan_days / 360);
                    $loan_period_unit = loanPeriodUnitEnum::YEAR;
                    break;
                default:
                    $loan_period = floor($loan_days / 30);
                    $loan_period_unit = loanPeriodUnitEnum::MONTH;

            }

        }


        $interest_info = $size_rate;
        if( $interest_info['interest_rate'] <=0 ){
            $interest_info['interest_rate'] = $interest_info['interest_rate_mortgage1'];
            $interest_info['operation_fee'] = $interest_info['operation_fee_mortgage1'];
        }
        if( $interest_info['interest_rate'] <=0 ){
            $interest_info['interest_rate'] = $interest_info['interest_rate_mortgage2'];
            $interest_info['operation_fee'] = $interest_info['operation_fee_mortgage2'];
        }


        $admin_fee = $operator_fee = 0;
        if( $interest_info['admin_fee'] ){

            if( $interest_info['admin_fee_type'] == 1 ){
                $admin_fee = $interest_info['admin_fee'];
            }else{
                $admin_fee = $loan_amount*( $interest_info['admin_fee']/100);
            }
        }

        // 贷款手续费
        $loan_fee = 0;
        if( $interest_info['loan_fee'] > 0 ){

            if( $interest_info['loan_fee_type'] == 1 ){
                $loan_fee = $interest_info['loan_fee'];
            }else{
                $loan_fee = round($loan_amount*($interest_info['loan_fee']/100),2);
            }
        }


        $payment_re = self::getPaymentDetail($loan_amount,$loan_days,$loan_period,$loan_period_unit,$interest_info,$repayment_type,$repayment_period);
        if( !$payment_re->STS ){
            return new result(false,'Calculate fail',null,errorCodesEnum::DB_ERROR);
        }

        $re_data = $payment_re->DATA;
        $total_payment = $re_data['payment_total'];
        $payment_schema = $re_data['payment_schema'];

        $return = array(
            'loan_amount' => $loan_amount,
            'currency' => $currency,
            'loan_period_value' => $loan_period,
            'loan_period_unit' => $loan_period_unit,
            'repayment_type' => $repayment_type,
            'repayment_period' => $repayment_period,
            'interest_rate' => $interest_info['interest_rate'],
            'interest_rate_type' => $interest_info['interest_rate_type'],
            'interest_rate_unit' => $interest_info['interest_rate_unit'],
            'admin_fee' => $admin_fee,
            'loan_fee' => $loan_fee,
            'arrival_amount' => $loan_amount-$admin_fee-$loan_fee,
            'product_info' => $sub_product_info,
            'interest_info' => $interest_info,
            'period_repayment_amount' => $total_payment['total_period_pay'],
            'total_repayment'=> $total_payment,
            'repayment_schema' => $payment_schema
        );
        return new result(true,'success',$return);
    }


    /** 贷款预览（创建合同前）
     *  针对某一产品的预览
     * @param $params
     * @return result
     */
    public function loanPreview($params)
    {
        $product_id = $params['product_id']?:0;
        $loan_amount = $params['amount'];
        $currency = $params['currency']?:currencyEnum::USD;
        $loan_period = intval($params['loan_period']);  // 贷款周期
        $loan_period_unit = $params['loan_period_unit'];

        // 产品信息
        $m_product = new loan_sub_productModel();
        $product_info = $m_product->getRow(array(
            'uid' => $product_id
        ));
        if( !$product_info ){
            return new result(false,'No this product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }

        if( $product_info->state == loanProductStateEnum::HISTORY ){
            return new result(false,'Product is un-shelve',null,errorCodesEnum::LOAN_PRODUCT_UNSHELVE);
        }

        if( $product_info->state != loanProductStateEnum::ACTIVE ){
            return new result(false,'Non execute product version ',null,errorCodesEnum::LOAN_PRODUCT_NX);
        }

        $re = self::calLoanDays($loan_period,$loan_period_unit);
        if( !$re->STS ){
            return $re;
        }
        $loan_days = $re->DATA;

        $payment_type = $product_info['interest_type'];
        $payment_period = $product_info['repayment_type'];



        if( $loan_amount <0 || $loan_period<0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }



        $interest_re = self::getLoanInterestDetail(0,$product_id,$loan_amount,$currency,$loan_days);

        if( !$interest_re->STS ){
            return $interest_re;
        }
        $interest_data = $interest_re->DATA;

        $interest_info = $interest_data['interest_info'];

        $admin_fee = $operator_fee = 0;
        if( $interest_info['admin_fee'] ){

            if( $interest_info['admin_fee_type'] == 1 ){
                $admin_fee = $interest_info['admin_fee'];
            }else{
                $admin_fee = $loan_amount*( $interest_info['admin_fee']/100);
            }
        }

        // 贷款手续费
        $loan_fee = 0;
        if( $interest_info['loan_fee'] > 0 ){

            if( $interest_info['loan_fee_type'] == 1 ){
                $loan_fee = $interest_info['loan_fee'];
            }else{
                $loan_fee = round($loan_amount*($interest_info['loan_fee']/100),2);
            }
        }


        $payment_re = self::getPaymentDetail($loan_amount,$loan_days,$loan_period,$loan_period_unit,$interest_info,$payment_type,$payment_period);
        if( !$payment_re->STS ){
            return new result(false,'Calculate fail',null,errorCodesEnum::DB_ERROR);
        }

        $re_data = $payment_re->DATA;
        $total_payment = $re_data['payment_total'];
        $payment_schema = $re_data['payment_schema'];
        $return = array(
            'loan_amount' => $loan_amount,
            'currency' => $currency,
            'loan_period_value' => $loan_period,
            'loan_period_unit' => $loan_period_unit,
            'repayment_type' => $payment_type,
            'repayment_period' => $payment_period,
            'interest_rate' => $interest_info['interest_rate'],
            'interest_rate_type' => $interest_info['interest_rate_type'],
            'interest_rate_unit' => $interest_info['interest_rate_unit'],
            'admin_fee' => $admin_fee,
            'loan_fee' => $loan_fee,
            'arrival_amount' => $loan_amount-$admin_fee-$loan_fee,
            'product_info' => $product_info,
            'interest_info' => $interest_info,
            'period_repayment_amount' => $total_payment['total_period_pay'],
            'total_repayment'=> $total_payment,
            'repayment_schema' => $payment_schema
        );
        return new result(true,'success',$return);

    }



    /**
     * @param $loan_params  **贷款参数
     *  array(
     *   member_id   会员ID
     *   product_id  member_credit_category.uid
     *   credit_amount  信用贷消耗的信用值
     *   amount       贷款金额
     *   currency     币种
     *   loan_period   贷款周期
     *   loan_period_unit  贷款周期单位（年、月等）
     *   repayment_type    还款方式
     *   repayment_period  还款周期
     *   handle_account_id  绑定的操作账户ID
     *   insurance_item_id 绑定的保险项目 如2,3,5
     *   application_id  申请ID
     *   mortgage_type  抵押类型
     *   guarantee_type  担保类型
     *   creator_id  创建人
     *   creator_name
     *   branch_id  分行ID
     * )
     * @param $interest_info  **利率信息
     * *  array(
     *   product_size_rate_id  有的话就传
     *   product_special_rate_id  有的话就传
     *   interest_rate
     *   interest_rate_type
     *   interest_rate_unit
     *   interest_min_value
     *   operation_fee
     *   operation_fee_unit
     *   operation_fee_type
     *   operation_min_value
     *   admin_fee
     *   admin_fee_type
     *   loan_fee
     *   loan_fee_type
     *   is_full_interest
     *   prepayment_interest
     *   prepayment_interest_type
     *   penalty_rate
     *   penalty_divisor_days
     *   grace_days
     * )
     * @param bool $is_period_limit  是否检查周期还款能力
     * @param $create_source ->创建来源
     * @return bool|ormResult|result
     */
    public function createContract($loan_params,$interest_info,$is_period_limit=true,$create_source=contractCreateSourceEnum::MEMBER_APP){

        $params = $loan_params;
        $member_id = $params['member_id'];

        $member_category_id = $params['product_id'];
        $loan_amount = round($params['amount'],2);
        $loan_period = intval($params['loan_period']);
        $loan_period_unit = $params['loan_period_unit'];
        $currency = $params['currency']?:currency::USD;
        $payment_type = trim($params['repayment_type']);
        $payment_period = trim($params['repayment_period']);
        $handle_account_id = intval($params['handle_account_id']);
        $credit_amount = intval($params['credit_amount']);

        // 计算目标天数
        $rt = self::calLoanDays($loan_period,$loan_period_unit);
        if( !$rt->STS ){
            return $rt;
        }
        $loan_days = $rt->DATA;
        if( $loan_days <= 0 ){
            return new result(false,'Invalid loan days',null,errorCodesEnum::INVALID_AMOUNT);
        }


        $m_contract = new loan_contractModel();
        // 检查member
        $m_member = new memberModel();
        $member_info = $m_member->getRow($member_id);
        if( !$member_info ){
            return new result(false,'No client member',null,errorCodesEnum::UNEXPECTED_DATA);
        }
        $memberObj = new objectMemberClass($member_id);
        $chk = $memberObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        // 贷款账户
        $m_account = new loan_accountModel();
        $loan_account_info = $m_account->getRow(array(
            'obj_guid' => $member_info->obj_guid,
            'account_type' => loanAccountTypeEnum::MEMBER
        ));
        if( !$loan_account_info ){
            $loan_account_info = $m_account->newRow();
            $loan_account_info->obj_guid = $member_info->obj_guid;
            $d = date('d');
            if( $d > 28 ){
                $d = '01';  // 特殊日调整为1号
            }
            $loan_account_info->due_date = $d;
            $loan_account_info->account_type = loanAccountTypeEnum::MEMBER;
            $insert = $loan_account_info->insert();
            if( !$insert->STS ){
                return new result(false,'Loan account error: '.$insert->MSG,null,errorCodesEnum::DB_ERROR);
            }
        }

        // 自动维护还款日
        if( !$loan_account_info->due_date ){
            $d = date('d');
            if( $d > 28 ){
                $d = '01';  // 特殊日调整为1号
            }
            $loan_account_info->due_date = $d;
            $loan_account_info->update_time = Now();
            $up = $loan_account_info->update();
            if( !$up->STS ){
                return new result(false,'Update loan due date fail.',null,errorCodesEnum::DB_ERROR);
            }
        }


        $m_account_handler = new member_account_handlerModel();
        // 有选择操作账户
        $account_handler = null;
        if( $handle_account_id ){
            $account_handler = $m_account_handler->getRow(array(
                'uid' => $handle_account_id,
            ));
            if( !$account_handler ){
                return new result(false,'No account handler',null,errorCodesEnum::NO_ACCOUNT_HANDLER);
            }
        }else{
            // 没有选择账户默认使用储蓄账户
            $account_handler = member_handlerClass::getMemberDefaultPassbookHandlerInfo($member_id);
        }

        $m_member_category = new member_credit_categoryModel();
        $member_category = $m_member_category->getRow(array(
            'uid' => $member_category_id
        ));
        if( !$member_category ){
            return new result(false,'No credit category:'.$member_category_id,null,errorCodesEnum::INVALID_PARAM);
        }

        if( $member_category['is_close'] == 1 ){
            return new result(false,'Limit use.',null,errorCodesEnum::LIMIT_MEMBER_LOAN_PRODUCT);
        }

        $m_loan_category = new loan_categoryModel();
        $loan_category = $m_loan_category->getRow($member_category['category_id']);
        if( !$loan_category ){
            return new result(false,'No loan category:'.$member_category['category_id'],null,errorCodesEnum::INVALID_PARAM);
        }

        if( $loan_category['is_close'] ){
            return new result(false,'Product is un-shelve',null,errorCodesEnum::LOAN_PRODUCT_UNSHELVE);
        }

        // 检查category的限制合同数,是否超出产品合同限制
        $going_contracts = $m_contract->getGoingContractsOfLoanCategoryByMemberGUID($loan_category['uid'],$member_info['obj_guid']);
        if( $loan_category['max_contracts_per_client'] && $going_contracts>=$loan_category['max_contracts_per_client'] ){
            return new result(false,'Exceed max contracts of product:'.$loan_category['max_contracts_per_client'],null,errorCodesEnum::EXCEED_MAX_CONTRACTS_PER_CLIENT_OF_PRODUCT);
        }


        $product_id = $member_category['sub_product_id'];
        // 产品信息
        $m_product = new loan_productModel();

        $m_sub_product = new loan_sub_productModel();
        $product_info = $m_sub_product->getRow(array(
            'uid' => $product_id
        ));
        if( !$product_info ){
            return new result(false,'No this product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }

        if( $product_info->state == loanProductStateEnum::HISTORY ){
            return new result(false,'Product is un-shelve',null,errorCodesEnum::LOAN_PRODUCT_UNSHELVE);
        }

        if( $product_info->state != loanProductStateEnum::ACTIVE ){
            return new result(false,'Non execute product version ',null,errorCodesEnum::LOAN_PRODUCT_NX);
        }

        $main_product_info = $m_product->getRow($product_info['product_id']);
        if( !$main_product_info ){
            return new result(false,'No main product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }


        // 利息和operation fee都设置为0的情况下，不能贷款,需要检查设置
        if( $interest_info['interest_rate'] <= 0
            && $interest_info['interest_min_value'] <= 0
            && $interest_info['operation_fee']<=0
            && $interest_info['operation_min_value'] <= 0
            && $interest_info['service_fee'] <= 0
        ){
            return new result(false,'Interest rate and operation fee are set to be 0,can not loan.',null,errorCodesEnum::NO_LOAN_INTEREST);
        }
        $interest_info['currency']=$currency; //add by tim, 为了installment-scheme对货币做特殊处理


        $specialLoanCateKey = null;
        if( $loan_category['is_special'] && $loan_category['special_key'] ){
            $specialLoanCateKey = $loan_category['special_key'];
        }


        // 首先检查贷款时间和还款方式、周期是否合理
        $re = self::verifyLoanTimeAndRepaymentPeriod($loan_period,$loan_period_unit,$payment_type,$payment_period);
        if( !$re ){
            return new result(false,'Not supported repayment type',null,errorCodesEnum::REPAYMENT_UN_MATCH_LOAN_TIME);
        }


        $today = date('Y-m-d');
        $is_single_repayment = interestTypeClass::isOnetimeRepayment($payment_type);

        $disburse_date = $today;
        $disburse_date_timestamp = strtotime($disburse_date);
        $interest_start_day = $disburse_date;   // 利息计算的起始时间
        $interest_start_timestamp = $disburse_date_timestamp;

        // 合同起止时间
        $contract_s_time = $interest_start_timestamp;
        $rt = self::getLoanEndDateTimestamp($loan_period,$loan_period_unit,$contract_s_time);
        if( !$rt->STS ){
            return $rt;
        }
        $contract_e_time = $rt->DATA;



        // 是否要重置还款日期
        $is_fix_loan_repayment_date = global_settingClass::loanIsFixClientRepaymentDate();

        if( $specialLoanCateKey == specialLoanCateKeyEnum::QUICK_LOAN ){
            $is_fix_loan_repayment_date = false;  // todo quick loan 按贷款日来处理还款日
        }


        // 计算第一次还款日
        $first_repayment_day = null;
        if( $specialLoanCateKey == specialLoanCateKeyEnum::FIX_REPAYMENT_DATE ){

            /*switch( $specialLoanCateKey ){
                case specialLoanCateKeyEnum::FIX_REPAYMENT_DATE:
                    $first_repayment_day = loan_accountClass::getSuperLoanRepaymentDateByAccountInfo($loan_account_info,$today);
                    break;
                default:
                    return new result(false,'Unknown special loan category:'.$specialLoanCateKey,
                        null,errorCodesEnum::NOT_SUPPORTED);
            }*/
            $first_repayment_day = loan_accountClass::getSuperLoanRepaymentDateByAccountInfo($loan_account_info,$today);

        }else{

            //$calFirstRepaymentDate = null;
            // 计算第一次还款日期
            if( !$is_single_repayment ){

                $adjust_start_date = null;
                // 需要调整还款日
                if( $is_fix_loan_repayment_date ){
                    $day = $loan_account_info->due_date;
                    $adjust_start_date = date("Y-m-$day",$interest_start_timestamp);
                    $first_repayment_day = interestTypeClass::getPeriodicFirstRepaymentDate($payment_period,$interest_start_day,$adjust_start_date);
                    //$calFirstRepaymentDate = $first_repayment_day;
                }else{
                    //$calFirstRepaymentDate = null;
                    $adjust_start_date = $today;
                    $first_repayment_day = interestTypeClass::getPeriodicFirstRepaymentDate($payment_period,$interest_start_day,$adjust_start_date);

                }
                $first_repayment_date_timestamp = strtotime($first_repayment_day);

            }else{
                //$calFirstRepaymentDate = null;
                $first_repayment_date_timestamp = $contract_e_time;
            }

            $first_repayment_day = date('Y-m-d',$first_repayment_date_timestamp);

        }



        // 利率信息由外部传进来
        // 还款计划
        $payment_re = $this->getPaymentDetail($loan_amount,$loan_days,$loan_period,$loan_period_unit,$interest_info,$payment_type,$payment_period,$interest_start_day,$first_repayment_day,$loan_account_info);
        if( !$payment_re->STS ){
            return new result(false,'Create installment schema fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }
        $re_data = $payment_re->DATA;
        $total_payment = $re_data['payment_total'];
        $payment_schema = $re_data['payment_schema'];
        $average_period_amount = $total_payment['total_period_pay'];
        if( empty($payment_schema) ){
            return new result(false,'Cal payment schema fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }


        // todo 暂时关闭还款能力的检查
        /*if( $is_period_limit ){
            //检查member的月还款能力，一次性还款的就不检查了
            if( !$is_single_repayment ){

                $month_ability = $loan_account_info['repayment_ability'];
                // 货币转换
                $exchange_rate = global_settingClass::getCurrencyRateBetween(currencyEnum::USD,$currency);
                if( $exchange_rate > 0 ){
                    $month_ability = round($month_ability*$exchange_rate,2);
                }

                $average_monthly_amount = $average_period_amount;
                $rt = self::interestRateConversion($average_period_amount,$payment_period,interestRatePeriodEnum::MONTHLY);
                if( $rt->STS ){
                    $average_monthly_amount = $rt->DATA;
                }

                // 支出,包括正在进行的贷款，保险等等
                $member_monthly_expense = $memberObj->getMonthlyExpense($currency);

                if( $month_ability < ($member_monthly_expense+$average_monthly_amount) ){
                    return new result(false,'Insufficient repayment capacity',null,errorCodesEnum::INSUFFICIENT_REPAYMENT_CAPACITY);
                }
            }
        }*/


        $last_credit_grant = member_credit_grantClass::getMemberLastGrantInfo($member_id);
        $credit_grant_id = $last_credit_grant['uid'];
        $credit_request = (new member_credit_requestModel())->find(array(
            'uid' => intval($last_credit_grant['credit_request_id'])
        ));


            //使用新的产品编码
            if( $currency == currencyEnum::KHR ){
                $inner_cate_code = $contract_sn_product_code = $loan_category['product_code_khr'];
            }else{
                $inner_cate_code = $contract_sn_product_code = $loan_category['product_code_usd'];
            }


            $branch_id = intval($params['branch_id'])?:$member_info['branch_id'];


            $contract_sn = self::generateLoanContractSn($credit_grant_id,$branch_id,$member_info['obj_guid'],$inner_cate_code,$product_info['number_code'],true);
            $virtual_contract_sn = self::generateLoanContractSn($credit_grant_id,$branch_id,$member_info['obj_guid'],$inner_cate_code,$product_info['number_code'],false);

            $inner_contract_sn = self::generateInnerContractSn($credit_grant_id,$branch_id,$member_info['obj_guid'],$inner_cate_code,$product_info['number_code']);
            // 贷款次数 有合同就算
            $sql = "select count(*) total  from loan_contract where account_id='".$loan_account_info->uid."'
              and state>=".qstr(loanContractStateEnum::PENDING_DISBURSE);
            $loan_cycle = $m_contract->reader->getOne($sql);
            $loan_cycle += 1;

            // 管理费
            $admin_fee = 0;

            // todo 暂时取消产品的，只收取授信的一次
            /*if( $interest_info['admin_fee'] ){

                if( $interest_info['admin_fee_type'] == 1 ){
                    $admin_fee = $interest_info['admin_fee']+0;
                }else{
                    $admin_fee = $loan_amount*( $interest_info['admin_fee']/100);
                }
            }*/

            // 贷款手续费
            $loan_fee = 0;

            // todo 暂时取消产品的，只收取授信的一次
            /*if( $interest_info['loan_fee'] > 0 ){

                if( $interest_info['loan_fee_type'] == 1 ){
                    $loan_fee = $interest_info['loan_fee'];
                }else{
                    $loan_fee = round($loan_amount*($interest_info['loan_fee']/100),2);
                }
            }*/


            $service_fee = 0;
            if( $interest_info['service_fee'] > 0 ){
                if( $interest_info['service_fee_type'] == 1 ){
                    $service_fee = $interest_info['service_fee'];
                }else{
                    $service_fee = round($loan_amount*($interest_info['service_fee']/100),2);
                }
            }

            $grace_days = intval($product_info['grace_days']);




            // 创建贷款合同
            $new_contract = $m_contract->newRow();
            $new_contract->account_id = $loan_account_info->uid;
            $new_contract->client_obj_type = objGuidTypeEnum::CLIENT_MEMBER;
            $new_contract->client_obj_guid = $member_info['obj_guid'];

            // 只是信用贷产品记录，其他贷款可能是不消耗信用的
            if( $main_product_info['category'] == loanProductCategoryEnum::CREDIT_LOAN ){
                $new_contract->credit_grant_id = $credit_grant_id;
            }
            $new_contract->account_handler_id = $account_handler?$account_handler['uid']:0;
            $new_contract->contract_sn = $contract_sn;
            $new_contract->virtual_contract_sn = $virtual_contract_sn;
            $new_contract->inner_contract_sn = $inner_contract_sn;
            $new_contract->member_credit_category_id = $member_category_id;
            $new_contract->credit_amount = $credit_amount;
            $new_contract->product_category = $main_product_info['category'];
            $new_contract->product_id = $main_product_info['uid'];
            $new_contract->product_code = $main_product_info['product_code'];
            $new_contract->product_name = $main_product_info['product_name'];
            $new_contract->sub_product_id = $product_info['uid'];
            $new_contract->sub_product_code = $product_info['sub_product_code'];
            $new_contract->sub_product_name = $product_info['sub_product_name'];


            if( isset($interest_info['product_size_rate_id']) ){
                $new_contract->product_size_rate_id = intval($interest_info['product_size_rate_id']);
            }

            if( isset($interest_info['product_special_rate_id']) ){
                $new_contract->product_special_rate_id = intval($interest_info['product_special_rate_id']);
            }

            $new_contract->currency = $currency;  // $currency  $interest_info['currency']
            $new_contract->apply_amount = $loan_amount;
            $new_contract->application_id = intval($params['application_id']);
            $new_contract->propose = $params['propose']?:$credit_request['purpose'];
            //$new_contract->due_date = $due_date;
            //$new_contract->due_date_type = $due_date_type;
            $new_contract->repayment_type = $payment_type;
            $new_contract->repayment_period = $payment_period;
            $new_contract->loan_cycle = $loan_cycle;
            $new_contract->loan_actual_cycle = $loan_cycle;
            $new_contract->loan_term_day = $loan_days;
            $new_contract->loan_period_value = $loan_period;
            $new_contract->loan_period_unit = $loan_period_unit;
            $new_contract->mortgage_type = $params['mortgage_type'];
            $new_contract->guarantee_type = $params['guarantee_type'];
            $new_contract->installment_frequencies = count($payment_schema);
            $new_contract->interest_rate = $interest_info['interest_rate'];
            $new_contract->interest_rate_type = $interest_info['interest_rate_type']?1:0;
            $new_contract->interest_rate_unit = $interest_info['interest_rate_unit'];
            $new_contract->interest_min_value = round($interest_info['interest_min_value'],2);
            $new_contract->operation_fee = $interest_info['operation_fee'];
            $new_contract->operation_fee_type = $interest_info['operation_fee_type']?1:0;
            $new_contract->operation_fee_unit = $interest_info['operation_fee_unit'];
            $new_contract->operation_min_value = round($interest_info['operation_min_value'],2);

            //$new_contract->admin_fee = $interest_info['admin_fee']?:0;  // todo check 暂时都取消
            $new_contract->admin_fee = 0;
            $new_contract->admin_fee_type = $interest_info['admin_fee_type']?:0;
            //$new_contract->loan_fee = $interest_info['loan_fee']?:0;  // todo check 暂时都取消
            $new_contract->loan_fee = 0;
            $new_contract->loan_fee_type = $interest_info['loan_fee_type']?:0;

            $new_contract->is_full_interest = $product_info['is_full_interest_prepayment']?:0;
            $new_contract->prepayment_interest = $product_info['prepayment_interest']?:0;
            $new_contract->prepayment_interest_type = $product_info['prepayment_interest_type']?:0;

            $new_contract->penalty_rate = $product_info->penalty_rate;
            $new_contract->penalty_divisor_days = $product_info->penalty_divisor_days;
            $new_contract->grace_days = $grace_days;
            $new_contract->penalty_is_compound_interest = $product_info['penalty_is_compound_interest'];

            if( $payment_type == interestPaymentEnum::BALLOON_INTEREST ){
                $new_contract->is_balloon_payment = 1;
            }
            $new_contract->is_advance_interest = intval($params['is_advance_interest']);
            $new_contract->ref_interest = $interest_info['interest_rate'];
            $new_contract->ref_admin_fee = $admin_fee;
            $new_contract->ref_loan_fee = $loan_fee;
            $new_contract->ref_operation_fee = $total_payment['total_operator_fee']+0;
            $new_contract->receivable_principal = $loan_amount;
            $new_contract->receivable_interest = $total_payment['total_interest']+0;
            $new_contract->receivable_admin_fee = $admin_fee;
            $new_contract->receivable_loan_fee = $loan_fee;
            $new_contract->receivable_operation_fee = $total_payment['total_operator_fee']+0;
            $new_contract->receivable_annual_fee = 0; // 暂时没有
            $new_contract->receivable_service_fee = $service_fee;

            $new_contract->start_date = date('Y-m-d',$contract_s_time);
            $new_contract->end_date = date('Y-m-d',$contract_e_time);
            if( $params['creator_id'] ){
                $new_contract->creator_id = intval($params['creator_id']);
                $new_contract->creator_name = $params['creator_name'];
            }else{
                $new_contract->creator_id = 0;
                $new_contract->creator_name = 'System';
            }
            $new_contract->branch_id = $branch_id;
            $new_contract->create_time = Now();
            $new_contract->create_source = $create_source;
            $new_contract->state = loanContractStateEnum::CREATE;
            $insert1 = $new_contract->insert();
            if( !$insert1->STS ){

                return new result(false,'Create contract fail '.$insert1->MSG,null,errorCodesEnum::DB_ERROR);
            }

            $loan_contract_id = $new_contract->uid;

            // 生成bill pay code
            $rt = loan_contractClass::contractAddBillPayCode($new_contract,$member_info);
            if( !$rt->STS ){
                return $rt;
            }

            // 处理绑定的保险产品
            $insurance_total_amount = 0;

            //
            /*$insurance_item_id = trim($params['insurance_item_id'],',');
            if( $insurance_item_id ){

                $insurance_items = explode(',',$insurance_item_id);
                $insurance_contract_list = array();
                if( count($insurance_items) > 0 ){

                    foreach( $insurance_items as $item_id){
                        $item_id = intval($item_id);
                        if( $item_id ){
                            $re = $this->createLoanInsuranceContract($loan_amount,$loan_contract_id,$item_id,$member_id,$new_contract->currency,array());
                            if( !$re->STS ){

                                return new result(false,$re->MSG,null,errorCodesEnum::CREATE_INSURANCE_CONTRACT_FAIL);
                            }
                            $insurance_contract = $re->DATA;
                            $insurance_total_amount += $insurance_contract['price'];
                            $insurance_contract_list[] = $insurance_contract;
                        }
                    }

                    // 更新到贷款产品
                    $new_contract->is_insured = 1;
                    $new_contract->receivable_insurance_fee = $insurance_total_amount;
                    $up = $new_contract->update();
                    if( !$up->STS ){

                        return new result(false,'Update loan contract fail',null,errorCodesEnum::DB_ERROR);
                    }

                }

            }*/



            // 所有计划状态
            $schema_state = schemaStateTypeEnum::CREATE;


            // 插入放款计划表  一次性放款 todo 分期放款
            $new_distribute_schema = array();
            $m_distribute_schema = new loan_disbursement_schemeModel();
            $distribute_schema = $m_distribute_schema->newRow();
            $distribute_schema->contract_id = $new_contract->uid;
            $distribute_schema->scheme_idx = 1;
            $distribute_schema->disbursable_date = date('Y-m-d');
            $distribute_schema->create_time = Now();
            $distribute_schema->principal = $loan_amount;
            $distribute_schema->deduct_annual_fee = 0;
            $distribute_schema->deduct_interest = $total_payment['deduct_interest'] ?: 0;
            $distribute_schema->deduct_admin_fee = $admin_fee;
            $distribute_schema->deduct_loan_fee = $loan_fee;
            $distribute_schema->deduct_operation_fee = $total_payment['deduct_operation_fee'] ?: 0;
            // 保险费
            $insurance_fee = 0;
            if( $new_contract->receivable_insurance_fee > 0 ){
                $insurance_fee = $new_contract->receivable_insurance_fee;
            }
            $distribute_schema->deduct_insurance_fee = $insurance_fee;
            $distribute_schema->deduct_service_fee = $total_payment['deduct_service_fee'] ?: 0;

            $total_deduct_amount = $distribute_schema->deduct_annual_fee+$distribute_schema->deduct_interest+
                $distribute_schema->deduct_admin_fee+$distribute_schema->deduct_loan_fee+$distribute_schema->deduct_operation_fee+
                $distribute_schema->deduct_insurance_fee+$distribute_schema->deduct_service_fee;
            $total_deduct_amount = round($total_deduct_amount,2);

            if( $total_deduct_amount>=$loan_amount ){
                return new result(false,'Loan amount not enough for deduct amount:'.$total_deduct_amount,null,errorCodesEnum::AMOUNT_TOO_LITTLE);
            }

            $distribute_schema->amount = $loan_amount - $total_deduct_amount;
            $distribute_schema->account_handler_id = $account_handler?$account_handler->uid:0;
            $distribute_schema->disbursement_org = '';
            $distribute_schema->state = $schema_state;
            $insert = $distribute_schema->insert();
            if( !$insert->STS ){

                return new result(false,'Insert distribute schema fail',null,errorCodesEnum::DB_ERROR);
            }
            $new_distribute_schema[] = $distribute_schema;




        $last_payment_date = end($payment_schema)['receive_date'];
        //  计算还款日
        $due_date_array = self::getLoanDueDate($payment_type,$payment_period,$first_repayment_day?:date('Y-m-d'),$last_payment_date);
        $due_date = $due_date_array['due_date'];
        $due_date_type = intval($due_date_array['due_date_type']);
        $new_contract->due_date = $due_date;
        $new_contract->due_date_type = $due_date_type;
        $new_contract->update_time = Now();

        // 插入还款计划表
        $m_payment_schema = new loan_installment_schemeModel();

        $create_time = date('Y-m-d H:i:s');
        $new_payment_schema = array();

        $total_period = count($payment_schema);


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


        $schema_interest_date = $interest_start_day;
        $counter = 1;
        reset($payment_schema);
        foreach( $payment_schema as $instalment_schema ){

            // 严格按照上面定义的字段插入顺序
            $temp = array(
                'contract_id' => $new_contract->uid,
                'scheme_idx' => $instalment_schema['scheme_index'],
                'scheme_name' => 'Period '.$instalment_schema['scheme_index'],
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
                'account_handler_id' => $account_handler?$account_handler['uid']:0,
                'state' => $schema_state,
                'create_time' => $create_time
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
        }

        // 拼接sql
        $insert_sql .= trim(join(',',$sql_array),',');

        $re = $m_payment_schema->conn->execute($insert_sql);
        if( !$re->STS ){

            return new result(false,'Insert payment schema fail: '.$re->MSG,null,errorCodesEnum::DB_ERROR);
        }

        // 更新合同的结束日期
        $new_contract->end_date = end($payment_schema)['receive_date'];
        // 重新计算贷款天数
        $new_days = ceil( (strtotime($new_contract->end_date)-strtotime($new_contract->start_date))/86400 );
        $new_contract->loan_term_day = $new_days;
        $new_contract->update_time = Now();
        $up = $new_contract->update();
        if( !$up->STS ){
            return new result(false,'Update loan contract fail.',null,errorCodesEnum::DB_ERROR);
        }



           // 旧方式插入还款计划
            /*if( $is_single_repayment ){  // 一次还款

                $instalment_schema = current($payment_schema);
                if( !$instalment_schema || !is_array($instalment_schema)){
                    return new result(false,'Unknown installment data',null,errorCodesEnum::UNEXPECTED_DATA);
                }

                $receive_date = date('Y-m-d',$contract_e_time);
                $schema_row = $m_payment_schema->newRow();
                $schema_row->contract_id = $new_contract->uid;
                $schema_row->scheme_idx = $instalment_schema['scheme_index'];
                $schema_row->scheme_name = 'Period '.$instalment_schema['scheme_index'];
                $schema_row->initial_principal = $instalment_schema['initial_principal'];
                $schema_row->interest_date = date("Y-m-d", $contract_s_time);
                $schema_row->receivable_date = $receive_date;
                $schema_row->penalty_start_date = date('Y-m-d',$contract_e_time+$grace_days*24*3600);
                $schema_row->receivable_principal = $instalment_schema['receivable_principal'];
                $schema_row->receivable_interest = $instalment_schema['receivable_interest'];
                $schema_row->receivable_operation_fee = $instalment_schema['receivable_operation_fee'];
                $schema_row->receivable_admin_fee = 0;
                $schema_row->ref_amount = $instalment_schema['amount'];
                $schema_row->amount = $instalment_schema['amount'];
                $schema_row->account_handler_id = $account_handler?$account_handler['uid']:0;
                $schema_row->state = $schema_state;
                $schema_row->create_time = date('Y-m-d H:i:s');
                $insert = $schema_row->insert();
                if( !$insert->STS ){

                    return new result(false,'Insert schema fail',null,errorCodesEnum::DB_ERROR);
                }
                $new_payment_schema[] = $schema_row;

                $new_contract->due_date = $receive_date;
                $new_contract->due_date_type = dueDateTypeEnum::FIXED_DATE;
                $new_contract->update_time = Now();
                $up = $new_contract->update();
                if( !$up->STS ){
                    return new result(false,'Update loan contract fail.',null,errorCodesEnum::DB_ERROR);
                }



            }else{  // 分期还款


                //  计算还款日
                $due_date_array = self::getLoanDueDate($loan_days,$payment_type,$payment_period,date('Y-m-d',$first_repayment_date_timestamp));
                $due_date = $due_date_array['due_date'];
                $due_date_type = intval($due_date_array['due_date_type']);
                $new_contract->due_date = $due_date;
                $new_contract->due_date_type = $due_date_type;
                $new_contract->update_time = Now();
                $up = $new_contract->update();
                if( !$up->STS ){
                    return new result(false,'Update loan contract fail.',null,errorCodesEnum::DB_ERROR);
                }


                $create_time = date('Y-m-d H:i:s');
                $new_payment_schema = array();

                $total_period = count($payment_schema);


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


                $schema_interest_date = date("Y-m-d", $contract_s_time);
                $counter = 1;
                $total_mantissa = 0;
                reset($payment_schema);
                //$new_payment_time = $first_repayment_date_timestamp;
                foreach( $payment_schema as $instalment_schema ){

                    // 处理每期还款的小数问题,取整，小数部分累计到最后一期
                    if( $counter == $total_period ){
                        // 将本金调整到和贷款总额一样
                        $pay_amount = round($instalment_schema['amount']+$total_mantissa,2);
                    }else{
                        // 向下取整
                        $pay_amount = floor($instalment_schema['amount']);
                        $left = $instalment_schema['amount']-$pay_amount;
                        $total_mantissa = $total_mantissa+$left;
                    }

                    // 严格按照上面定义的字段插入顺序
                    $temp = array(
                        'contract_id' => $new_contract->uid,
                        'scheme_idx' => $instalment_schema['scheme_index'],
                        'scheme_name' => 'Period '.$instalment_schema['scheme_index'],
                        'initial_principal' => $instalment_schema['initial_principal'],
                        'interest_date' => $schema_interest_date,
                        //'receivable_date' => date('Y-m-d',$new_payment_time),
                        'receivable_date' => $instalment_schema['receive_date'],

                        'penalty_start_date' => date('Y-m-d',strtotime($instalment_schema['receive_date'])+$grace_days*24*3600),
                        'receivable_principal' => $instalment_schema['receivable_principal'],
                        'receivable_interest' => $instalment_schema['receivable_interest'],
                        'receivable_operation_fee' => $instalment_schema['receivable_operation_fee'],
                        'receivable_admin_fee' => 0,
                        'ref_amount' => $instalment_schema['amount'],
                        'amount' => $pay_amount,
                        'account_handler_id' => $account_handler?$account_handler['uid']:0,
                        'state' => $schema_state,
                        'create_time' => $create_time
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

                    // 用初始累计的方式，放弃上期增加的方式
                    //$new_payment_time = strtotime('+'.$counter*$time_interval_value.' '.$time_interval_unit,$first_repayment_date_timestamp);

                    $schema_interest_date = $temp['receivable_date'];
                    $counter++;  // 放到最后处理
                }

                // 拼接sql
                $insert_sql .= trim(join(',',$sql_array),',');

                $re = $m_payment_schema->conn->execute($insert_sql);
                if( !$re->STS ){

                    return new result(false,'Insert payment schema fail: '.$re->MSG,null,errorCodesEnum::DB_ERROR);
                }

                // 更新合同的结束日期
                $new_contract->end_date = end($payment_schema)['receive_date'];
                $new_contract->update_time = Now();
                $up = $new_contract->update();
                if( !$up->STS ){
                    return new result(false,'Update loan contract fail.',null,errorCodesEnum::DB_ERROR);
                }

            }*/

        //如果是KHR，需要格式化最小单位为100
        if($currency==currencyEnum::KHR){
            /*
             * 放到scheme里去格式化
             * $ret_round=self::updateRoundOfContractForKHR($loan_contract_id);
            if(!$ret_round->STS){
                return $ret_round;
            }
            */
        }

        $re = loan_contractClass::getLoanContractDetailInfo($new_contract->uid);
        if( !$re->STS ){
            return $re;
        }
        $return_info = $re->DATA;

        return new result(true,'success',$return_info);


    }


    /*
         * 格式化KHR贷款,因为面值最小是100，四舍五入（50为界）
     * ，每一条scheme的子项 round 100
     * total repayment round 1000
         */
    public static function updateRoundOfContractForKHR($contract_id){
        //先格式化installment-scheme
        $r=new ormReader();
        $sql="select * from loan_installment_scheme where contract_id=".qstr($contract_id)." order by uid";
        $arr_scheme=$r->getRows($sql);
        $list_sql=array();
        $next_init_principal=0;
        $total_fee=0;
        $total_interest=0;
        $last_i=count($arr_scheme)-1;
        foreach($arr_scheme as $i=>$item){
            $new_ref_amt=floor($item['ref_amount']/100)*100+($item['ref_amount']%100>=50?100:0);
            $new_rp_interest=floor($item['receivable_interest']/100)*100+($item['receivable_interest']%100>=50?100:0);
            $new_rp_operation_fee=floor($item['receivable_operation_fee']/100)*100+($item['receivable_operation_fee']%100>=50?100:0);

            if($i==$last_i){
                //最后一行要使剩余本金等于应收本金
                if($next_init_principal>0){
                    $new_rp_principal=$next_init_principal;
                }else{
                    $new_rp_principal=$item['initial_principal'];//说明只有一行数据
                }
                $new_ref_amt=$new_rp_principal+$new_rp_operation_fee+$new_rp_interest;
            }else{
                $new_rp_principal=$new_ref_amt-$new_rp_interest-$new_rp_operation_fee;
            }
            //重新把new_ref_amt格式化到1000

            $remainder=$new_ref_amt%1000;
            if($remainder>0){
                $ext_amt=1000-$remainder;
                $new_ref_amt+=$ext_amt;
                //最后一期要把多收的加在利息上,才能保证本金的应收和期初相等
                if($i==$last_i){
                    $new_rp_interest+=$ext_amt;
                }else{
                    $new_rp_principal+=$ext_amt;
                }
            }
            $new_amt=$new_ref_amt;



            $sql="update loan_installment_scheme";
            $sql.=" set ";
            $sql.=" receivable_principal=".$new_rp_principal;
            $sql.=",receivable_interest=".$new_rp_interest;
            $sql.=",receivable_operation_fee=".$new_rp_operation_fee;
            $sql.=",ref_amount=".$new_ref_amt;
            $sql.=",amount=".$new_amt;
            if($next_init_principal>0){
                $sql.=",initial_principal=".$next_init_principal;
            }
            $sql.=" where uid=".qstr($item['uid']);
            $list_sql[]=$sql;
            $next_init_principal=($next_init_principal>0?$next_init_principal:$item['initial_principal'])-$new_rp_principal;
            if($next_init_principal<0){
                $next_init_principal=0;
            }
            $total_fee+=$new_rp_operation_fee;
            $total_interest+=$new_rp_interest;
        }
        //格式化主表
        $sql="update loan_contract set";
        $sql.=" ref_operation_fee=".$total_fee;
        $sql.=",receivable_operation_fee=".$total_fee;
        $sql.=",receivable_interest=".$total_interest;
        $sql.=" where uid=".qstr($contract_id);
        $list_sql[]=$sql;
        if(count($list_sql)){
            //$sql=join($list_sql,";"); 一次请求不支持多个sql语句执行，这里存在一个效率问题了
            foreach($list_sql as $sql){
                $ret=$r->conn->execute($sql);
                if(!$ret->STS){
                    return $ret;
                }
            }
            return new result(true,"SUCCESS ROUND");
        }else{
            return new result(true,"SUCCESS ROUND");
        }
    }


    /** 取消合同
     * @param $contract_id
     * @return result
     */
    public static function cancelContract($contract_id)
    {
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow($contract_id);
        if( !$contract ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        if( $contract->state == loanContractStateEnum::CANCEL ){
            return new result(true,'success');
        }

        if( $contract->state >= loanContractStateEnum::PENDING_DISBURSE ){
            return new result(false,'Can not cancel',null,errorCodesEnum::CAN_NOT_CANCEL_CONTRACT);
        }

        // 更新贷款合同状态
        $contract->state = loanContractStateEnum::CANCEL;
        $up = $contract->update();
        if( !$up->STS ){
            return new result(false,'DB error',null,errorCodesEnum::DB_ERROR);
        }

        // 更新关联的保险合同状态
        $sql = "update insurance_contract set state='".insuranceContractStateEnum::CANCEL."' where loan_contract_id='".$contract->uid."' ";
        $up = $m_contract->conn->execute($sql);
        if( !$up->STS ){
            return new result(false,'DB error',null,errorCodesEnum::DB_ERROR);
        }


        return new result(true,'success');
    }


    /** 合同确认开始执行
     * @param $contract_id
     * @param array $extend_info
     * @return result
     */
    public static function confirmContractToExecute($contract_id,$extend_info = array() )
    {
        $m_contract = new loan_contractModel();
        $contract = $m_contract->getRow(array(
            'uid' => $contract_id
        ));

        if( !$contract ){
            return new result(false,'No contract info:'.$contract_id,null,errorCodesEnum::NO_CONTRACT);
        }

        // 已经确认过了
        if( $contract->state >= loanContractStateEnum::PENDING_DISBURSE ){
            return new result(true);
        }

        if( $contract->state <= loanContractStateEnum::CANCEL ){
            return new result(false,'Contract has been cancelled.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_id);
        if( !$member_info ){
            return new result(false,'Not found loan member info:'.$contract_id,null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $member_id = $member_info['uid'];

        $member_category = (new member_credit_categoryModel())->find(array(
            'uid' => $contract['member_credit_category_id']
        ));
        if( !$member_category ){
            return new result(false,'No member category product:'.$contract['member_credit_category_id'],null,errorCodesEnum::NO_DATA);
        }
        $loan_category = (new loan_categoryModel())->find(array(
            'uid' => $member_category['category_id']
        ));
        if( !$loan_category ){
            return new result(false,'No loan Category info:'.$member_category['category_id'],null,errorCodesEnum::NO_DATA);
        }

        // 检查category的限制合同数,是否超出产品合同限制
        $going_contracts = $m_contract->getGoingContractsOfLoanCategoryByMemberGUID($loan_category['uid'],$member_info['obj_guid']);
        if( $loan_category['max_contracts_per_client'] && $going_contracts>=$loan_category['max_contracts_per_client'] ){
            return new result(false,'Exceed max contracts of product:'.$loan_category['max_contracts_per_client'],null,errorCodesEnum::EXCEED_MAX_CONTRACTS_PER_CLIENT_OF_PRODUCT);
        }


        $sub_product_info = (new loan_sub_productModel())->find(array(
            'uid' => $contract['sub_product_id']
        ));
        if( !$sub_product_info ){
            return new result(false,'No sub product info:'.$contract['sub_product_id'],null,errorCodesEnum::NO_DATA);
        }

        $currency = $contract['currency'];
        //使用新的产品编码
        if( $currency == currencyEnum::KHR ){
            $contract_sn_product_code = $loan_category['product_code_khr'];
        }else{
            $contract_sn_product_code = $loan_category['product_code_usd'];
        }


        $branch_id = $contract['branch_id'];
        // 合同重新编号
        $contract_sn = self::generateLoanContractSn(
            $contract['credit_grant_id'],
            $branch_id,
            $member_info['obj_guid'],
            $contract_sn_product_code,
            $sub_product_info['number_code'],
            false
        );
        $inner_contract_sn = self::generateInnerContractSn(
            $contract['credit_grant_id'],
            $branch_id,
            $member_info['obj_guid'],
            $contract_sn_product_code,
            $sub_product_info['number_code']
        );



        $conn = $m_contract->conn;

        // 脚本处理授权合同fee

        // 更新真实的贷款次数
        $sql = "select count(uid) cnt from loan_contract where account_id=".qstr($contract->account_id).
        " and state>=".qstr(loanContractStateEnum::PENDING_DISBURSE);
        $num = $m_contract->reader->getOne($sql);

        // 更新贷款合同状态
        $contract->loan_actual_cycle = $num+1;
        $contract->contract_sn = $contract_sn;
        $contract->inner_contract_sn = $inner_contract_sn;
        $contract->state = loanContractStateEnum::PENDING_DISBURSE;
        $contract->update_time = Now();
        $up = $contract->update();
        if( !$up->STS ){
            return new result(false,'Update contract fail',null,errorCodesEnum::DB_ERROR);
        }

        // todo 暂时的方式，一起更新下billcode
        $bill_code = bank_accountClass::getBillPayCodeByContractSn($contract_sn);
        $sql = "update loan_contract_billpay_code set bill_code='$bill_code' where contract_id='$contract_id' ";
        $up = $conn->execute($sql);



        // 更新绑定的保险合同状态 （贷款扣款）
        $sql = "update insurance_contract set state='".insuranceContractStateEnum::PROCESSING."' where loan_contract_id='$contract_id' ";
        $up = $conn->execute($sql);
        if( !$up->STS ){
            return new result(false,'Update insurance contract fail',null,errorCodesEnum::DB_ERROR);
        }



        // 是否信用贷
        $main_product_info = (new loan_productModel())->getRow($contract->product_id);
        if( !$main_product_info ){
            return new result(false,'No main product info:'.$contract->product_id,null,errorCodesEnum::NO_LOAN_PRODUCT);
        }

        if( $main_product_info['category'] == loanProductCategoryEnum::CREDIT_LOAN  ){

            // 折算成USD
           /* $ex_rate = global_settingClass::getCurrencyRateBetween($contract->currency,currencyEnum::USD);
            if( $ex_rate <= 0 ){
                return new result(false,'No set currency rate:'.$contract->currency.'-'.currencyEnum::USD,null,errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
            }
            $credit_amount = ceil($contract->receivable_principal*$ex_rate);*/
            $credit_amount = $contract['credit_amount'];

            // 信用贷款扣减信用余额
            $re = member_creditClass::minusCreditBalance(
                creditEventTypeEnum::CREDIT_LOAN,
                $contract->member_credit_category_id,
                $credit_amount,
                $contract['currency'],
                'Contract Confirmed: ' . $contract->contract_sn);
            if( !$re->STS ){
                return $re;
            }
        }

        // 向用户发送消息
        $title = 'Confirm Contract Success';
        $body = 'Your loan contract has come into force,just wait for distributing!';
        member_messageClass::sendSystemMessage($member_id,$title,$body);

        return new result(true,'success');

    }


    /** 获取贷款产品绑定的保险产品
     * @param $loan_product_id
     * @return result
     */
    public function getLoanProductBindInsuranceProduct($loan_product_id)
    {
        if( !$loan_product_id ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $sql = "select i.* from insurance_product_relationship r inner join insurance_product_item i on r.insurance_product_item_id=i.uid left join insurance_product p on i.product_id=p.uid where r.loan_product_id='".$loan_product_id."' and  p.state='".insuranceProductStateEnum::ACTIVE."' ";
        $reader = new ormReader();
        $list = $reader->getRows($sql);
        return new result(true,'success',$list);
    }



    /** 计算逾期还款计划的罚金
     * @param $scheme_id
     * @return int
     */
    public static function calculateSchemaRepaymentPenalties($scheme_id,$term_date=null)
    {

        $penalties = 0;
        $scheme_id = intval($scheme_id);

        $r = new ormReader();
        $sql = "select s.*,c.penalty_rate,c.penalty_divisor_days,c.apply_amount,c.penalty_is_compound_interest from loan_installment_scheme s inner join loan_contract c on s.contract_id=c.uid  where s.uid='$scheme_id' ";
        $schema = $r->getRow($sql);
        if( !$schema ){
            return $penalties;
        }

        $ref_penalty = $schema['settle_penalty'] - $schema['deduction_penalty'] - $schema['paid_penalty'];
        if( $ref_penalty < 0 ){
            $ref_penalty = 0;
        }


        if( $schema['penalty_rate'] <= 0 || $schema['penalty_divisor_days']<=0 || $schema['state'] == schemaStateTypeEnum::COMPLETE ){
            return $ref_penalty;
        }

        // 年-月-日
        if( $schema['last_repayment_time'] ){
            // 已还过部分本金
            $penalty_timestamp = strtotime($schema['last_repayment_time']);
            $penalty_day_time = strtotime(date('Y-m-d',$penalty_timestamp));
        }else{
            $penalty_timestamp = $schema['penalty_start_date']?strtotime($schema['penalty_start_date']):0;
            $penalty_day_time = strtotime(date('Y-m-d',$penalty_timestamp));
        }


        if( !$term_date ){
            $today_time = strtotime(date('Y-m-d'));
        }else{
            $today_time = strtotime($term_date);
        }


        if( $penalty_day_time >= $today_time ){
            return $ref_penalty;
        }

        // 计算相差天数
        $days = ceil(($today_time-$penalty_day_time)/96400);
        if( $days <= 0 ){
            return $ref_penalty;
        }

        // 日罚息
        $round_rate = $schema['penalty_rate']/100;
        if( $schema['penalty_divisor_days'] <= 0 ){
            $day_rate = $round_rate;
        }else{
            $day_rate = $round_rate/$schema['penalty_divisor_days'];  // 百分比
        }


        $base_amount = $schema['amount']-$schema['actual_payment_amount'];
        // 只有一种罚金基数  未还本息产生的罚金
        // 单、复利计算
        if( $schema['penalty_is_compound_interest'] ){

            // 复利
            // 计算几个周期
            $round = floor($days/$schema['penalty_divisor_days']);
            // 计算周期余数
            $mod_days = $days % $schema['penalty_divisor_days'];
            $new_penalty = $base_amount*($round_rate *( (1+$round)*$round/2 )+$day_rate*$mod_days);
            $penalties = $new_penalty+$ref_penalty;

        }else{

            // 单利
            // 合计罚息
            $total_rate = $day_rate*$days;
            $new_penalty = $base_amount*$total_rate;
            $penalties = $ref_penalty + $new_penalty;
        }


        if( $penalties < 0 ){
            $penalties = 0;
        }

        return round($penalties,2);

    }


    public function createContractByApply($apply_id,$user_id,$user_name)
    {
        $m_apply = new loan_applyModel();
        $apply = $m_apply->getRow($apply_id);
        if( !$apply->STS ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        if( $apply->state != loanApplyStateEnum::ALL_APPROVED ){
            return new result(false,'Un-expect handle',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $interest_info = $apply->toArray();

        $loan_params = array(
            'member_id' => $apply->member_id,
            'product_id' => $apply->product_id,
            'amount' => $apply->apply_amount,
            'currency' => $apply->currency,
            'loan_period' => $apply->loan_time,
            'loan_period_unit' => $apply->loan_time_unit,
            'repayment_type' => $apply->repayment_type,
            'repayment_period' => $apply->repayment_period,
            'handle_account_id' => 0,
            'insurance_item_id' => null,
            'application_id' => $apply_id,
            'mortgage_type' => null,
            'guarantee_type' => null,
            'creator_id' => $user_id,
            'creator_name' => $user_name
        );

        $re = self::createContract($loan_params,$interest_info);
        if( !$re->STS ){
            return $re;
        }

        $re_data = $re->DATA;

        $apply->state = loanApplyStateEnum::DONE;
        $apply->update_time = Now();
        $up = $apply->update();
        if( !$up->STS ){
            return new result(false,'Db error '.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$re_data);

    }




}