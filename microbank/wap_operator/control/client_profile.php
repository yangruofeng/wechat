<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/30
 * Time: 21:43
 */
class client_profileControl extends wap_operator_baseControl{
    public function __construct()
    {
        parent::__construct();
        //Language::read('act,label,tip');
        Tpl::setLayout('weui_layout');
        Tpl::setDir('client_profile');
    }
    public function profileIndexPageOp(){
        $this->pageCheckToken();
        $params['type'] = 1;
        $params['guid'] = $_GET['cid'];
        $member = memberClass::searchMember($params);
        Tpl::output('data', $member);
        $member_id = intval($_GET['id']);
        Tpl::output('work_info', L("work_type_" . $member['work_type']));
        //product list


        $residence = memberClass::getMemberResidencePlace($member_id);
        Tpl::output('residence', $residence->DATA['address_info']);


        $account_model = new loan_accountModel();
        $account_info = $account_model->getRow(array('obj_guid' => $member['obj_guid']));
        Tpl::output('due_date', $account_info->due_date);
        Tpl::output('principal_period', $account_info->principal_periods);


        $list=loan_categoryClass::getMemberCreditCategorySetting($member_id);
        Tpl::output("category",$list);

        $m_loan_sub_prod=new loan_sub_productModel();
        $semi_list=$m_loan_sub_prod->select(array("interest_type"=>interestPaymentEnum::SEMI_BALLOON_INTEREST));
        $semi_list=array_keys(resetArrayKey($semi_list,"uid"));

        $semi_balloon=false;
        foreach($list as $prod){
            if(in_array($prod['sub_product_id'],$semi_list)){
                $semi_balloon=true;
            }
        }
        Tpl::output("show_semi_balloon",$semi_balloon);






        Tpl::output('html_title', 'Profile');
        Tpl::output('header_title', 'Profile');
        Tpl::showPage('profile.index');
    }
    public function ajaxSubmitLoanCategoryStateOp($p)
    {
        $officer_id = cookie('member_id');
        $member_id = intval($p['member_id']);
        $category_id = intval($p['category_id']);
        $is_close = intval($p['state']);
        $params['officer_id'] = $officer_id;
        $params['member_id'] = $member_id;
        $params['category_id'] = $category_id;
        $params['is_close'] = $is_close;
        //查询member_credit_category 是否存在
        $m_member_category = new member_credit_categoryModel();
        $info = $m_member_category->find(array('member_id' => $member_id, 'category_id' => $category_id));
        if ($info) { //存在则更改is_close
            $ret = $m_member_category->updateMemberState($params);
        } else {//否则添加
            $ret = $m_member_category->addMemberCategory($params);
        }
        return $ret;
    }
    public function editCreditCategoryItemPageOp(){
        $category_id=$_GET['cate_id'];
        $member_id=$_GET['member_id'];

        $m = new member_credit_categoryModel();
        $item = $m->find(array("member_id" => $member_id,"category_id"=>$category_id));
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
        Tpl::showPage("profile.category.item.edit");
    }
    public function ajaxSaveMemberCategoryItemOp($p){
        $p = $_POST;
        if (!$p['member_id']) {
            return new result(false,"Required Member ID");
        }
        if (!$p['sub_product_id']) {
            return new result(false,"Required to input Repayment ID");
        }
        if(!$p['interest_package_id']){
            return new result(false,"Required to input interest-package");
        }

        $m_member_category = new member_credit_categoryModel();
        $row = $m_member_category->getRow(array("member_id" => $p['member_id'], "category_id" => $p['category_id']));
        if (!$row) {
            return new result(false,"This Credit Category has not been set up yet");
        }
        $row->sub_product_id = $p['sub_product_id'];
        if(isset($p['is_one_time'])){
            $row->is_one_time = intval($p['is_one_time']);
        }
        $row->interest_package_id = intval($p['interest_package_id']);
        $row->update_time = Now();
        $row->update_operator_id =cookie("member_id");
        $ret = $row->update();
        return $ret;
    }
    public function ajaxSubmitClientRepaymentDayOp($p)
    {
        $cid = intval($p['cid']);
        $day = intval($p['day']);
        $m_loan_account = M('loan_account');
        $row = $m_loan_account->getRow(array('obj_guid' => $cid));
        if (!$row->uid) {
            return new result(false, L('tip_code_' . errorCodesEnum::NO_LOAN_ACCOUNT));
        }
        $ret = loan_accountClass::editLoanAccountDueDate($cid, $day);
        return $ret;
    }

    public function ajaxSubmitClientPrincipalPeriodOp($p)
    {
        $cid = intval($p['cid']);
        $period = intval($p['period']);
        $m_loan_account = M('loan_account');
        $row = $m_loan_account->getRow(array('obj_guid' => $cid));
        if (!$row->uid) {
            return new result(false, L('tip_code_' . errorCodesEnum::NO_LOAN_ACCOUNT));
        }
        $ret = loan_accountClass::editLoanAccountPrincipalPeriod($cid, $period);
        return $ret;
    }
}