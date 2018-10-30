<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 8/23/2018
 * Time: 11:04 AM
 */
class web_credit_v2Control extends web_creditControl{
    public function __construct()
    {
        parent::__construct();
        Tpl::setDir("web_credit_v2");
    }
    function removeImageOfAssetItemOp($p){
        $uid=$p['uid'];
        $m=new member_verify_cert_imageModel();
        $row=$m->getRow($uid);
        if(!$row){
            return new result(false,"Invalid Parameter:No record found");
        }
        return $row->delete();
    }

    /** 修改资产的证件时间
     * @param $p
     * @return result
     */
    public function editAssetIssuedDateOp($p)
    {
        $asset_id = $p['asset_id'];
        $issued_date = $p['issued_date'];
        return member_assetsClass::editAssetIssuedDate($asset_id,$issued_date);
    }
    public function editSuggestCreditPageOp(){
        $member_id = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        //获取最新申请，没有申请的情况下不能创建suggest
        $last_request = credit_researchClass::getClientRequestCredit($member_id);
        $client_relative = null;

        if (!$last_request || $last_request['state'] != creditRequestStateEnum::CREATE) {
            Tpl::showPage("suggest.credit.invalid");

        } else {
            $client_relative = $last_request['relative_list'];
        }
        //      $client_relative=memberClass::getMemberCurrentRelative($member_id);
        Tpl::output("client_relative", $client_relative);

        if ($this->user_position == userPositionEnum::OPERATOR) {
            $request_by_bm = false;
        } else {
            $request_by_bm = true;
        }
        $analysis = credit_researchClass::getSystemAnalysisCreditOfMember($member_id, $this->user_id, $this->user_position);
        Tpl::output("analysis", $analysis);
        $member_asset = $analysis['suggest']['increase'];

        if (is_array($member_asset)) {
            $member_asset = resetArrayKey($member_asset, "uid");
        }
        Tpl::output('member_assets', $member_asset);

        //信用贷产品列表
        $prod_list = loan_categoryClass::getMemberCreditCategoryList($member_id);
        Tpl::output("product_list", $prod_list);
        if(!count($prod_list)){
            showMessage("Please set <kbd>Credit Category</kbd> At first");
        }


        //最新记录
        $m_member_credit_suggest = new member_credit_suggestModel();
        $last_suggest = $m_member_credit_suggest->getLastSuggestOfOperator($member_id, $this->user_id);
        //如果request-item小于最后授信时间，则不用最后一条suggest
        $last_grant = member_credit_grantClass::getMemberLastGrantInfo($member_id);

        if ($last_suggest && $last_grant && $last_suggest['request_time'] < $last_grant['grant_time']) {
            $last_suggest = null;
        }
        TPL::output("last_grant", $last_grant);

        // 判断是否可以编辑
        if ($last_suggest) {
            if ($last_suggest['state'] == memberCreditSuggestEnum::PENDING_APPROVE
                || $last_suggest['state'] == memberCreditSuggestEnum::APPROVING
            ) {
                $last_suggest['is_can_not_edit'] = 1;
            } else {
                $last_suggest['is_can_not_edit'] = 0;
            }
            $last_suggest['source_desc'] = "Default Data From Last Suggest By Self At: " . $last_suggest['request_time'];
        }

        //co 列表
        $co_list = memberClass::getMemberCreditOfficerList($member_id, $request_by_bm);
        $co_list = resetArrayKey($co_list, "officer_id");
        $co_suggest_list = array();
        foreach ($co_list as $co_id => $co) {
            $co_suggest_list[$co_id] = credit_researchClass::getLastSuggestCreditByOfficerId($member_id, $co_id);
        }
        $avg_list = array();//排除没数据的
        foreach ($co_suggest_list as $item) {
            if (is_array($item)) {
                if ($item['request_time'] >= $last_grant['grant_time']) {
                    $avg_list[] = $item;
                }
            }
        }


        if (count($avg_list) > 1) {//计算co平均值
            //因为不同co可能指定不同的资产甚至不同的贷款产品，所以这里不取平均值，只取最后一个提交的
            $avg_co_suggest = end($avg_list);
            /*
            $avg_co_suggest = $this->avgCoSuggest($avg_list, $member_asset, $prod_list);
            $avg_co_suggest['officer_name'] = '--AVG--';
            $co_suggest_list[] = $avg_co_suggest;
            */
        } elseif (count($avg_list) == 1) {
            $avg_co_suggest = current($avg_list);
        }

        if (!$avg_co_suggest) {
            $avg_co_suggest = array(
                "monthly_repayment_ability" => $analysis['ability'],
                "credit_terms" => $analysis['suggest']['terms'],
                "max_credit" => $analysis['suggest']['max_credit'],
                "default_credit" => $analysis['suggest']['default_credit'],
                "source_type" => 2,
                "source_desc" => "Default Data From System Analysis!"
            );
            if ($analysis['suggest']['increase']) {
                $increase = array();
                foreach ($analysis['suggest']['increase'] as $item) {
                    $increase[] = array(
                        'member_asset_id' => $item['uid'],
                        "credit" => $item['credit']
                    );
                }
                $increase = resetArrayKey($increase, "member_asset_id");
                $avg_co_suggest['suggest_detail_list'] = $increase;
            }
        } else {
            $avg_co_suggest['source_type'] = 1;
            $avg_co_suggest['source_desc'] = "Default Data From Suggestion By " . $avg_co_suggest['operator_name'] . ",Suggest Time:" . $avg_co_suggest['request_time'];
        }


        Tpl::output('last_suggest', $last_suggest ?: $avg_co_suggest);

        Tpl::output('co_list', $co_list);
        Tpl::output('co_suggest_list', $co_suggest_list);

        $analysis2=creditFlowClass::getSystemAnalysisCreditOfMember($member_id,$this->user_id,$this->user_position);
        Tpl::output("new_analysis",$analysis2);

        Tpl::showPage("suggest.credit.operator");
    }

    /**
     * 根据传入的数据去匹配fee和利息
     * @param $p
     */
    function ajaxMatchFeeAndInterestOp($p){
        if(!$p){
            return new result(false,"Invalid Parameter");
        }
        $officer_id = $this->user_id;
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
                    "fee_setting"=>$fee_setting,
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
                    'is_interest_editable' => $is_interest_editable
                );
            }

        }
        if($max_credit!=$p['max_credit']){
            return new result(false,"required:Max_Credit<kbd>".$p['max_credit']."</kbd> = Total Credit Of Category<kbd>".$max_credit."</kbd>");
        }
        //多个category的话，就传0，只取默认设置
        //$fee_setting=loanSettingClass::matchLoanFeeOfSetting($max_credit,count($cate_id_list)>1?0:current($cate_id_list));

        //co 列表
        $co_list = memberClass::getMemberCreditOfficerList($member_id, true);
        $co_list = resetArrayKey($co_list, "officer_id");
        $co_suggest_list = array();
        foreach ($co_list as $co_id => $co) {
            $co_suggest_list[$co_id] = credit_researchClass::getLastSuggestCreditByOfficerId($member_id, $co_id);
        }
        $avg_list = array();//排除没数据的
        foreach ($co_suggest_list as $item) {
            if (is_array($item)) {
                if ($item['request_time'] >= $last_grant['grant_time']) {
                    $avg_list[] = $item;
                }
            }
        }

        $last_suggest = credit_researchClass::getLastSuggestCreditByOfficerId($member_id, $officer_id);
        if(!$last_suggest){
            $last_suggest=end($avg_list);
        }


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
        "currency_list":[{"credit_category_id":"139","credit":"23000","credit_usd":"11500","credit_khr":"46000000","interest_rate":"1.20"}],
        "loan_fee":"1.00",
        "admin_fee":"10.00",
        "loan_fee_type":0,
        "admin_fee_type":0
        }}:
         * */
        $p['request_type']=researchPositionTypeEnum::BRANCH_MANAGER;
        $p['officer_id']=$this->user_id;

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

    /**
     * 锁定或者解除member的单项功能
     * @param $p
     * @return ormResult|result
     * @throws Exception
     */
    function  ajaxLockMemberAuthorityOp($p){
        $member_id=$p['member_id'];
        $auth_type=$p['auth_type'];
        $state=$p['state']; //0 =>lock, 1=>unlock
        $m=new client_blackModel();
        $row=$m->getRow(array("member_id"=>$member_id,"type"=>$auth_type));
        if($row){
            if(!$state){//lock
                return new result(true);
            }else{//unlock
                return $row->delete();
            }
        }else{
            if(!$state){//lock
                $row=$m->newRow();
                $row->member_id=$member_id;
                $row->type=$auth_type;
                $row->auditor_id=$this->user_id;
                $row->auditor_name=$this->user_name;
                $row->update_time=Now();
                return $row->insert();
            }else{//unlock
                return new result(true);
            }
        }
    }
    public function editMemberCreditCategoryProductPageV2Op()
    {

        $member_id = $_GET['member_id'];
        $uid = $_GET['uid'];
        $m = new member_credit_categoryModel();
        $item = $m->find(array("uid" => $uid));
        if (!$item) {
            showMessage("Invalid Parameter:No Row Found");
        }
        Tpl::output("category_info", $item);


        $default_item=(new loan_categoryModel())->getCategoryItem($item['category_id']);

        //输出可选的sub_product
        $sub_list = loan_productClass::getAllActiveSubProductList();
        $arr_sub = array();
        foreach ($sub_list as $item) {
            if(in_array($item['uid'],$default_item['allowed_sub_product_id'])){
                $arr_sub[] = array("sub_product_id" => $item['uid'], "sub_product_name" => $item['sub_product_name']);
            }
        }
        Tpl::output("sub_list", $arr_sub);

        //package_list
        $package_list = loan_productClass::getProductPackageList();
        $list_package=array();
        foreach($package_list as $package_item){
            if(in_array($package_item['uid'],$default_item['allowed_interest_package_id'])){
                $list_package[$package_item['uid']]=$package_item;
            }
        }
        Tpl::output('package_list', $list_package);
        Tpl::output("member_id", $member_id);
        Tpl::output("uid", $uid);
        Tpl::setDir("web_credit");
        Tpl::showPage("client.credit.category.editor");


    }
    /**
     * 修改credit-controller页面
     */
    public function editMemberCCPageOp()
    {
        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        //member co list
        $member_co_list = M('member_follow_officer')->select(array('member_id' => $uid, 'is_active' => 1));
        Tpl::output('member_co_list', $member_co_list);
        //co list
        $co_list = userClass::getAllCreditControllerList();
        Tpl::output('co_list', $co_list);
        Tpl::setDir("web_credit");
        Tpl::showPage('client.cc.edit');
    }

    /**
     * 修改credit-controller
     */
    public function editMemberCCOp()
    {
        $member_id = intval($_POST['member_id']);
        if (is_array($_POST['co_id'])) {
            $co_arr = $_POST['co_id'];
        } else if ($_POST['co_id']) {
            $co_arr = array($_POST['co_id']);
        } else {
            $co_arr = array();
        }

        $class_member = new memberClass();
        $rt = $class_member->setMemberCC($member_id, $co_arr);
        if (!$rt->STS) {
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            showMessage('Edit successful.', getUrl('web_credit', 'creditClient', array('uid' => $member_id), false, BACK_OFFICE_SITE_URL));
        }
    }
    /**
     * 修改credit-controller页面
     */
    public function editMemberRCPageOp()
    {
        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        //member co list
        $member_co_list = M('member_follow_officer')->select(array('member_id' => $uid, 'is_active' => 1));
        Tpl::output('member_co_list', $member_co_list);
        //co list
        $co_list = userClass::getAllRiskControllerList();
        Tpl::output('co_list', $co_list);
        Tpl::setDir("web_credit");
        Tpl::showPage('client.rc.edit');
    }

    /**
     * 修改credit-controller
     */
    public function editMemberRCOp()
    {
        $member_id = intval($_POST['member_id']);
        if (is_array($_POST['co_id'])) {
            $co_arr = $_POST['co_id'];
        } else if ($_POST['co_id']) {
            $co_arr = array($_POST['co_id']);
        } else {
            $co_arr = array();
        }

        $class_member = new memberClass();
        $rt = $class_member->setMemberRC($member_id, $co_arr);
        if (!$rt->STS) {
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            showMessage('Edit successful.', getUrl('web_credit', 'creditClient', array('uid' => $member_id), false, BACK_OFFICE_SITE_URL));
        }
    }
}