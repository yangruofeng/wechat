<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/25
 * Time: 10:26
 */
class suggest_creditControl extends wap_operator_baseControl{
    public function __construct()
    {
        parent::__construct();
        //Language::read('act,label,tip');
        Tpl::setLayout('weui_layout');
        Tpl::setDir('suggest_credit');
    }
    function editCreditPageOp(){
        $member_id = intval($_GET['id']);
        $officer_id = intval(cookie('member_id'));
        $um_info = M('um_user')->find(array('uid' => $officer_id));
        $officer_position = $um_info['user_position'];
        Tpl::output('html_title', 'Suggest for Credit');
        Tpl::output('header_title', 'Suggest for Credit');
        $back_url="javascript:history.back(-1);";

        //信用贷产品列表
        $prod_list = loan_categoryClass::getMemberCreditCategoryList($member_id);
        //如果没设置产品,跳转到警告页面
        if(!$prod_list){
            Tpl::output("without_timeout",true);
            showMessage("<h5>Not allow to edit credit</h5><h3>Please set credit-category at first</h3>",$back_url,10);
        }

        Tpl::output("product_list", $prod_list);



        //获取最新申请，没有申请的情况下不能创建suggest
        $last_request = credit_researchClass::getClientRequestCredit($member_id);
        if (!$last_request || $last_request['state'] != creditRequestStateEnum::CREATE) {
            Tpl::output("without_timeout",true);
            showMessage("<h5>Not allow to edit credit</h5><h3>Please add credit-request of client at first</h3>",$back_url,10);
            //Tpl::showPage("credit.add.invalid");
        }


        $analysis = credit_researchClass::getSystemAnalysisCreditOfMember($member_id, $officer_id, $officer_position);
        Tpl::output("analysis", $analysis);
        $member_asset = $analysis['suggest']['increase'];
        if (is_array($member_asset)) {
            $member_asset = resetArrayKey($member_asset, "uid");
        }
        Tpl::output('member_assets', $member_asset);

        //最后一次提交的建议
        $last_suggest = credit_researchClass::getLastSuggestCreditByOfficerId($member_id, $officer_id);
        //如果request-item小于最后授信时间，则不用最后一条suggest
        $last_grant = member_credit_grantClass::getMemberLastGrantInfo($member_id);
        if ($last_suggest && $last_grant && $last_suggest['request_time'] < $last_grant['grant_time']) {
            $last_suggest = null;
        }
        if (!is_array($last_suggest)) {
            $last_suggest = array(
                "monthly_repayment_ability" => $analysis['ability'],
                "credit_terms" => $analysis['suggest']['terms'],
                "max_credit" => $analysis['suggest']['max_credit'],
                "default_credit" => $analysis['suggest']['default_credit'],
                "default_credit_category_id"=>current($prod_list)['uid'],
                "is_system" => 1
            );
            if ($analysis['suggest']['increase']) {
                $increase = array();
                foreach ($analysis['suggest']['increase'] as $item) {
                    $increase[] = array(
                        'member_asset_id' => $item['uid'],
                        'member_credit_category_id'=>current($prod_list)['uid'],
                        "credit" => $item['credit']
                    );
                }
                $increase = resetArrayKey($increase, "member_asset_id");
                $last_suggest['suggest_detail_list'] = $increase;
            }
        }

        $analysis2=creditFlowClass::getSystemAnalysisCreditOfMember($member_id,$officer_id,$officer_position);
        Tpl::output("new_analysis",$analysis2);

        Tpl::output("last_suggest", $last_suggest);

        Tpl::showPage('credit.index');
    }

    /**
     * 根据传入的数据去匹配fee和利息
     * @param $p
     */
    function ajaxMatchFeeAndInterestOp($p){
        if(!$p){
            return new result(false,"Invalid Parameter");
        }
        $officer_id = intval(cookie('member_id'));
        $member_id=$p['member_id'];
        $credit_terms=$p['credit_terms'];
        $cate_list=$p['cate_list'];
        $credit_category=loan_categoryClass::getMemberCreditCategoryList($member_id);
        $ret_cate=array();
        if(!$p['max_credit']){
            return new result(false,"Required:Max-Credit Must More Than <kbd>0</kbd>");
        }
        $max_credit=0;
        $cate_id_list=array();
        foreach($cate_list as $cate_id=>$cate_credit){
            if(!$cate_id){
                return new result(false,"No Setting Credit-Category!");
            }
            $new_rate_item=array();
            if($cate_credit>0){
                $cate_id_list[]=$cate_id;
                $max_credit+=$cate_credit;
                $cate_interest=$credit_category[$cate_id]['interest_rate_list'];
                $new_cate=array_merge($credit_category[$cate_id],array("credit_usd"=>$cate_credit,"credit_terms"=>$credit_terms));
                $ret_match=loan_categoryClass::matchInterestForCategory($cate_interest,$new_cate,false);
                $ret_usd=$ret_match['usd'];
                if($ret_usd['is_matched']){
                    foreach($ret_usd['list'] as $match_item){
                        if($match_item['is_matched']){
                            $new_rate_item=$match_item;
                            break;
                        }
                    }
                }else{
                    $str_msg=join($ret_usd['msg']," , ");
                    $str_msg="<kbd>".$str_msg."</kbd>";
                    return new result(false,"No Matched Interest For ".$new_cate['alias'].",Reason:".$str_msg.",Source:".$cate_credit."(USD)/".$credit_terms."(Month)");
                }
                $limit_rate=array();
                $limit_fee=array();
                if($new_rate_item['interest_rate']>0){
                    $limit_rate[]=$new_rate_item['interest_rate'];
                }
                if($new_rate_item['operation_fee']>0){
                    $limit_fee[]=$new_rate_item['operation_fee'];
                }
                if($new_rate_item['interest_rate_mortgage1']>0){
                    $limit_rate[]=$new_rate_item['interest_rate_mortgage1'];
                }
                if($new_rate_item['operation_fee_mortgage1']>0){
                    $limit_fee[]=$new_rate_item['operation_fee_mortgage1'];
                }

                if($new_rate_item['interest_rate_mortgage2']>0){
                    $limit_rate[]=$new_rate_item['interest_rate_mortgage2'];
                }
                if($new_rate_item['operation_fee_mortgage2']>0){
                    $limit_fee[]=$new_rate_item['operation_fee_mortgage2'];
                }
                $default_rate=0;
                $default_fee=0;
                if(!count($limit_rate)){
                    $default_rate=0;
                }else{
                    $default_rate=min($limit_rate);
                }
                if(!count($limit_fee)){
                    $default_fee=0;
                }else{
                    $default_fee=min($limit_fee);
                }

                // 利率是否可编辑
                if( $new_cate['is_one_time'] || $new_cate['special_key'] == specialLoanCateKeyEnum::QUICK_LOAN ){
                    $is_interest_editable = true;
                }else{
                    $is_interest_editable = false;
                }

                $fee_setting=loanSettingClass::matchLoanFeeOfSetting($cate_credit,$credit_category[$cate_id]['category_id']);
                $ret_cate[]=array(
                    'uid'=>$cate_id,
                    "sub_product_name"=>$new_cate['sub_product_name'],
                    "is_one_time"=>$new_cate['is_one_time'],
                    "is_special"=>$new_cate['is_special'],
                    "special_key"=>$new_cate['special_key'],
                    "member_category_id"=>$cate_id,
                    "alias"=>$new_cate['alias'],
                    "credit"=>$cate_credit,
                    "credit_khr"=>0,
                    "credit_usd"=>$cate_credit,
                    "default_interest_rate"=>$default_rate,
                    "default_operation_fee"=>$default_fee,
                    "interest_list"=>$ret_usd['list'],
                    "fee_setting"=>$fee_setting,
                    'is_interest_editable' => $is_interest_editable

                );
            }

        }
        if($max_credit!=$p['max_credit']){
            return new result(false,"required:Max_Credit<kbd>".$p['max_credit']."</kbd> = Total Credit Of Category<kbd>".$max_credit."</kbd>");
        }
        //多个category的话，就传0，只取默认设置
        //$fee_setting=loanSettingClass::matchLoanFeeOfSetting($max_credit,count($cate_id_list)>1?0:current($cate_id_list));

        $last_suggest = credit_researchClass::getLastSuggestCreditByOfficerId($member_id, $officer_id);


        $ret=array(
            "category_list"=>$ret_cate,
            "max_credit"=>$max_credit,
            "credit_terms"=>$credit_terms,
            "last_suggest"=>$last_suggest
        );
        return new result(true,"",$ret);

    }

    /**
     * 保存co提交的信用建议
     * @param $p
     * @return result
     * @throws Exception
     */
    function ajaxSubmitSuggestCreditOp($p){
        /*
         传入格式
        {"repay_ability":"1076.44",
        "credit_terms":"60",
        "max_credit":"23000",
        "default_credit":"3000",
        "default_credit_category_id":"139",
        "request_type":"0",
        "member_id":"434",
        "officer_id":"144",
        "token":"ab5d930cd7dc984d9d06d23e3608a9d9",
        "is_append":"0",
        "asset_list":[{"asset_id":599,"credit":10000,"credit_category_id":"139"},
        {"asset_id":600,"credit":10000,"credit_category_id":"139"}],
        "currency_list":[{"credit_category_id":"139","credit":"23000","credit_usd":"11500","credit_khr":"46000000","interest_rate":"1.20","loan_fee":0.1,"loan_fee_type":0}],
        "collateral_list":[599,222]
        }}:
         * */
        $p['request_type']=researchPositionTypeEnum::CREDIT_OFFICER;
        $conn=ormYo::Conn();
        $conn->startTransaction();
        $ret=creditFlowClass::submitMemberSuggestCredit($p);
        if ($ret->STS) {
            $conn->submitTransaction();
            return new result(true, $ret->MSG, null);
        } else {
            $conn->rollback();
            return new result(false, $ret->MSG, null);
        }
    }

}