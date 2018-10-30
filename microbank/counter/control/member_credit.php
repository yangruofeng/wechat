<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/5/27
 * Time: 23:52
 */
class member_creditControl extends counter_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('member');
        Tpl::setDir('member_credit_v2');
        Tpl::setLayout('home_layout');
        Tpl::output("sub_menu", $this->getMemberBusinessMenu());
    }

    /**
     * 显示信用合同授权主页面
     * @param $p
     */
    public function showClientCreditMainOp($p)
    {

        $member_id = $p['member_id'] ?: $_GET['member_id'];

        $m_member_authorized_contract = new member_authorized_contractModel();// M('member_authorized_contract');
        $m_member_credit_grant = new member_credit_grantModel();// M('member_credit_grant');
        $m_member_credit_grant_assets = new member_credit_grant_assetsModel();// M('member_credit_grant_assets');
        $m_member_asset_mortgage = new member_asset_mortgageModel();// M('member_asset_mortgage');

        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info", $client_info);
        Tpl::output("member_id", $member_id);

        $grant = $m_member_credit_grant->orderBy("uid desc")->find(array("member_id" => $member_id, "state" => commonApproveStateEnum::PASS));
        if( !$grant ){
            Tpl::showPage('credit.agreement.invalid.page');
        }

        //获取relative
        if ($grant['credit_request_id'] > 0) {
            $m_relative = M("member_credit_request_relative");
            $relative_list = $m_relative->select(array("request_id" => $grant['credit_request_id']));
            $grant['relative_list'] = $relative_list;
        }


        //授信资产
        //已抵押
        $is_assets = $m_member_credit_grant_assets->getCreditGrantAssets($grant['uid'], '1');
        //未抵押
        $assets = $m_member_credit_grant_assets->getCreditGrantAssets($grant['uid'], '0');
        $grant['asset_image'] = $m_member_credit_grant_assets->getCreditGrantAssetsImage($grant['uid']);

        $grant['is_assets'] = $is_assets;
        $grant['assets'] = $assets;
        //授信合同
        $contract = $m_member_authorized_contract->getConstructInfoByUid($grant['uid']);
        $grant['contract'] = $contract;
        //授信历史
        $client_authorized_history = array();
        if ($member_id) {
            $client_authorized_history = $m_member_authorized_contract->getConstructInfoByMemberId($member_id);
            //todo:过滤抵押
            $rows = $m_member_asset_mortgage->getAssetMortgagesAndContract();
            $contract_mortgage = array();
            $contract_mortgage_type = array();
            foreach ($rows as $k => $v) {
                $contract_mortgage[$v['contract_no']][] = $v['asset_type'];
                $contract_mortgage_type[$v['contract_no']] = $v['mortgage_type'];
            }
            foreach ($client_authorized_history as $k => $v) {
                $client_authorized_history[$k]['mortgage_type'] = $contract_mortgage_type[$v['contract_no']];
                $client_authorized_history[$k]['mortgages'] = $contract_mortgage[$v['contract_no']];
            }
        }
        $ret = array(
            "sts" => true,
            "detail" => $grant,
            "client_authorized_history" => $client_authorized_history
        );

        $exchange_list = $this->getExchangeRateUsdAndKhr();
        Tpl::output("exchange_list", $exchange_list);


        $prod_list = loan_categoryClass::getMemberCreditCategoryList($member_id);
        //输出利息类型(软硬)
        foreach($prod_list as $k=>$prod){
            $interest_type=loan_categoryClass::getCategoryInterestTypeByGrant($prod['uid']);
            $prod_list[$k]["interest_type"]=$interest_type;
        }
        Tpl::output("credit_category", $prod_list);


        //获取授信的多货币
        $m_grant_product=new member_credit_grant_productModel();
        $list_currency=$m_grant_product->select(array("grant_id"=>$grant['uid']));
        $total_fee=0;
        $total_fee_usd=0;
        $total_fee_khr=0;
        $arr_product=array();
        $is_only_super_loan=true;
        foreach($list_currency as $item_key=>$item){
            if($prod_list[$item['member_credit_category_id']]['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){
                $is_only_super_loan=false;
            }
            $sub_total=0;
            $sub_total_khr=0;
            if($item['loan_fee']>0 && $item['credit_usd']){
                if($item['loan_fee_type']){
                    $sub_total+=$item['loan_fee'];
                    $item['desc_loan_fee']=$item['loan_fee'];
                }else{
                    $sub_total+=$item['credit_usd']*$item['loan_fee']/100;
                    $item['desc_loan_fee']=$item['credit_usd'].' * <kbd>'.$item['loan_fee']."%</kbd>=".$item['credit_usd']*$item['loan_fee']/100;
                }
            }else{
                $item['desc_loan_fee']="N/A";
            }
            if($item['loan_fee_khr']>0 && $item['credit_khr']){
                if($item['loan_fee_type']){
                    $sub_total_khr+=$item['loan_fee_khr']/4000;
                    $item['desc_loan_fee_khr']=$item['loan_fee_khr'];
                }else{
                    $sub_total_khr+=$item['credit_khr']*$item['loan_fee_khr']/100/4000;
                    $item['desc_loan_fee_khr']=$item['credit_khr'].' * <kbd>'.$item['loan_fee_khr']."%</kbd>=".$item['credit_khr']*$item['loan_fee_khr']/100;
                }
            }else{
                $item['desc_loan_fee_khr']="N/A";
            }


            if($item['admin_fee']>0 && $item['credit_usd']){
                if($item['admin_fee_type']){
                    $sub_total+=$item['admin_fee'];
                    $item['desc_admin_fee']=$item['admin_fee'];
                }else{
                    $sub_total+=$item['credit_usd']*$item['admin_fee']/100;
                    $item['desc_admin_fee']=$item['credit_usd'].' * <kbd>'.$item['admin_fee']."%</kbd>=".$item['credit_usd']*$item['admin_fee']/100;
                }
            }else{
                $item['desc_admin_fee']="N/A";
            }

            if($item['admin_fee_khr']>0 && $item['credit_khr']>0){
                if($item['admin_fee_type']){
                    $sub_total_khr+=$item['admin_fee_khr']/4000;
                    $item['desc_admin_fee_khr']=$item['admin_fee_khr'];
                }else{
                    $sub_total_khr+=$item['credit_khr']*$item['admin_fee_khr']/100/4000;
                    $item['desc_admin_fee_khr']=$item['credit_khr'].' * <kbd>'.$item['admin_fee_khr']."%</kbd>=".$item['credit_khr']*$item['admin_fee_khr']/100;
                }
            }else{
                $item['desc_admin_fee_khr']="N/A";
            }

            if($item['annual_fee']>0 && $item['credit_usd']){
                if($item['annual_fee_type']){
                    $sub_total+=$item['annual_fee'];
                    $item['desc_annual_fee']=$item['annual_fee'];
                }else{
                    $sub_total+=$item['credit_usd']*$item['annual_fee']/100;
                    $item['desc_annual_fee']=$item['credit_usd'].' * <kbd>'.$item['annual_fee']."%</kbd>=".$item['credit_usd']*$item['annual_fee']/100;
                }
            }else{
                $item['desc_annual_fee']="N/A";
            }
            if($item['annual_fee_khr']>0 && $item['credit_khr']){
                if($item['annual_fee_type']){
                    $sub_total_khr+=$item['annual_fee_khr']/4000;
                    $item['desc_annual_fee_khr']=$item['annual_fee_khr'];
                }else{
                    $sub_total_khr+=$item['credit_khr']*$item['annual_fee_khr']/100/4000;
                    $item['desc_annual_fee_khr']=$item['credit_khr'].' * <kbd>'.$item['annual_fee_khr']."%</kbd>=".$item['credit_khr']*$item['annual_fee_khr']/100;
                }
            }else{
                $item['desc_annual_fee_khr']="N/A";
            }
            $item['sub_total']=$sub_total;
            $item['sub_total_khr']=$sub_total_khr;

            $total_fee+=$sub_total;
            $total_fee+=$sub_total_khr;

            $total_fee_usd+=$sub_total;
            $total_fee_khr+=$sub_total_khr*4000;

            $arr_product[$item_key]=$item;

        }

        Tpl::output('credit_currency',$arr_product);
        Tpl::output('total_fee',$total_fee);
        Tpl::output("total_fee_usd",$total_fee_usd);
        Tpl::output("total_fee_khr",$total_fee_khr);

        Tpl::output("is_only_super_loan",$is_only_super_loan);//只有super的时候要求只收现金

        // 处理汇率的问题（只要有KHR就用4000来计算）
        if(  $total_fee_khr > 0 ){
            Tpl::output('khr_usd_rate',1/4000);
            Tpl::output('usd_khr_rate',4000);

        }else{
            $khr_usd_rate = global_settingClass::getCurrencyRateBetween(currencyEnum::KHR, currencyEnum::USD);
            Tpl::output('khr_usd_rate',$khr_usd_rate);

            $usd_khr_rate = global_settingClass::getCurrencyRateBetween(currencyEnum::USD,currencyEnum::KHR);
            Tpl::output('usd_khr_rate',$usd_khr_rate);
        }




        // 处理贷款的问题
        $one_time_loan_list = array();
        $sql = "select mcgp.* from member_credit_grant_product mcgp left join member_credit_category mcc on mcc.uid=mcgp.member_credit_category_id
        where mcgp.grant_id=".qstr($grant['uid'])." and mcc.is_close!='1' and mcc.is_one_time='1'";
        $one_time_product = $m_member_credit_grant->reader->getRows($sql);
        foreach( $one_time_product as $vv ){
            $temp = array();
            $temp['credit_terms'] = $grant['credit_terms'];
            $temp['grant_product_info'] = $vv;
            $temp['member_category_info'] = $prod_list[$vv['member_credit_category_id']];

            $one_time_loan_list[] = $temp;
        }

        Tpl::output('one_time_loan_list',$one_time_loan_list);


        //是否已经设置交易密码
        Tpl::output("required_set_password",$client_info['trading_password']?0:1);
        // 是否在柜台录入指纹
        $finger_print = memberClass::isLoggingFingerprint($member_id);
        Tpl::output("required_set_finger",$finger_print?0:1);



        Tpl::output("data", $ret);
        Tpl::showPage("client.credit.index");

    }

    protected function getExchangeRateUsdAndKhr()
    {
        $exchange_list = array(
            'USD_KHR' => global_settingClass::getCurrencyRateBetween(currencyEnum::USD,currencyEnum::KHR),
            'KHR_USD' => global_settingClass::getCurrencyRateBetween(currencyEnum::KHR,currencyEnum::USD)
        );
        return $exchange_list;
    }

    /**
     * 提交授权合同
     */
    public function submitClientAuthorizeOp()
    {
        $params = array_merge(array(), $_GET, $_POST);

        $mortgage_list = urldecode($params['mortgage_list']);
        $mortgage_list = json_decode($mortgage_list, true);
        $contract_images = explode(',', $params['contract_images']);
        $currency_amount = array();
        $currency_list = (new currencyEnum())->Dictionary();
        foreach ($currency_list as $key => $currency) {
            $currency_amount[$key] = $params[strtolower($key) . '_amount'];
        }
       /* if ($params['form_submit'] == 'ok') {

            $conn = ormYo::Conn();
            $conn->startTransaction();
            $args = array(
                "operator_id" => $this->user_id,
                "operator_name" => $this->user_name,
                "branch_id" => $this->branch_id,
                "branch_name" => $this->branch_name,
                "trading_password" => $params['cashier_trading_password'],
                "member_trading_password" => $params['member_trading_password'],
                "grant_id" => $params['grant_id'],
                "member_id" => $params['member_id'],
                "member_image" => $params['member_image'],
                "total_credit"=>$params['total_credit'],
                "loan_fee" => $params['loan_fee'],
                'loan_fee_amount' => $params['loan_fee_amount'],
                'admin_fee_amount' => $params['admin_fee_amount'],
                "mortgage_list" => $mortgage_list,
                "contract_images" => $contract_images,
                "is_draft" => $params['is_draft'],
                "payment_way" => $params['fee_from'],
                'currency_amount'=>$currency_amount
            );
            $ret = member_credit_grantClass::signAuthorizedContract($args);
            if ($ret->STS) {
                $conn->submitTransaction();
                showMessage('Authorize successfully!', getUrl('member_credit', 'showClientCreditMain', array('member_id' => $params['member_id']), false, ENTRY_COUNTER_SITE_URL));
            } else {
                $conn->rollback();
                showMessage('Submit failed!' . $ret->MSG);
            }
        }*/
    }

    public function ajaxConfirmSignAuthoriseContractOp($p)
    {
        //return new result(false,'test',$p);
        $params = $p;
        $is_auto_disburse_one_time = intval($p['is_auto_disburse_one_time']);
        $total_loan_fee = round($params['loan_fee'],2);
        $payment_way=intval($params['fee_from']);
        $member_id = intval($params['member_id']);
        if(!$params['cashier_trading_password']){
            return new result(false,"Please Input <kbd>Cashier Trading Password</kbd>");
        }

        $member_info = (new memberModel())->getRow($member_id);
        if( !$member_info ){
            return new result(false,'Not found member:'.$member_id);
        }

        if( $payment_way == repaymentWayEnum::CASH ){
            $currency_amount = array();
            $currency_list = (new currencyEnum())->Dictionary();
            foreach ($currency_list as $key => $currency) {
                $currency_amount[$key] = $params[strtolower($key) . '_amount'];
            }
        }else{
            $currency_amount = array(
                currencyEnum::USD => $total_loan_fee
            );

        }

        // 过滤下0的金额
        foreach( $currency_amount as $c=>$a ){
            if( round($a,2) <= 0 ){
                unset($currency_amount[$c]);
            }
        }

        $contract_images=$params['contract_images'];
        if($contract_images){
            $contract_images=explode(",",$contract_images);
        }else{
            return new result(false,"Please Take photo for <kbd>Contract Photo</kbd>");
        }


        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            // 首先处理客户的交易密码
            if( $is_auto_disburse_one_time ){
                $member_trading_password = trim($params['member_trading_password']);
                $member_trading_password = md5('123456');
                if( !$member_trading_password ){
                    $conn->rollback();
                    return new result(false,'Please input client trading password.');
                }

                if( !$member_info['trading_password'] ){
                    $member_info->trading_password = $member_trading_password;
                    $member_info->update_time = Now();
                    $up = $member_info->update();
                    if( !$up->STS ){
                        $conn->rollback();
                        return new result(false,'Set trading password for client fail:'.$up->MSG);
                    }

                }else{
                    if( $member_info->trading_password != $member_trading_password ){
                        $conn->rollback();
                        return new result(false,'Client trading password error.');
                    }
                }

                // 指纹是在前端异步处理
            }

            $args =array_merge($p,array(
                "operator_id" => $this->user_id,
                "operator_name" => $this->user_name,
                "branch_id" => $this->branch_id,
                "branch_name" => $this->branch_name,
                "payment_way" => $payment_way,
                'currency_amount'=>$currency_amount,
                'contract_images'=>$contract_images,
            ));
            $ret = creditFlowClass::signCreditAgreementAtCounter($args);
            if ($ret->STS) {
                $conn->submitTransaction();

                $auth_contract_id = $ret->DATA['uid'];
                $ret->DATA['credit_contract_id'] = $auth_contract_id;
                $ret->DATA['is_auto_disburse_one_time'] = $is_auto_disburse_one_time;


            } else {
                $conn->rollback();
            }
            return $ret;

        }catch (Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage());
        }

    }


    public function grantCreditOneTimeLoanPreviewOp($p)
    {
        $member_id = intval($p['member_id']);
        $credit_contract_id = intval($p['credit_contract_id']);

        $data = array(
            'sts' => false,
            'data' => array(),
            'member_id' => $member_id
        );
        $rt = (new autoDisburseOneTimeByCounterClass())->loanPreview($credit_contract_id,$this->user_id);
        if( $rt->STS ){
            $data['sts'] = true;
            $data['data'] = $rt->DATA;
        }

        return $data;
    }


    public function grantCreditOneTimeLoanCancelOp($p)
    {
        $biz_ids = $p['biz_ids'];
        $o_class = new autoDisburseOneTimeByCounterClass();
        $rt = $o_class->loanCancel($biz_ids);
        return $rt;
    }

    public function grantCreditOneTimeLoanConfirmOp($p)
    {
        //return new result(false,'test',$p);
        // JS端传过来会出现多个是数组，一个是字符串的情况
        $biz_ids = $p['biz_ids'];
        if( !is_array($biz_ids) ){
            $biz_ids = (array)$biz_ids;
        }
        $o_class = new autoDisburseOneTimeByCounterClass();
        $rt = $o_class->loanConfirm($biz_ids);
        return $rt;
    }

    public function grantCreditOneTimeLoanWithdrawPageOp($p)
    {
        $member_id = intval($p['member_id']);
        if( !$member_id ){
            return array();
        }
        $memberObj = new objectMemberClass($member_id);
        $member_balance = $memberObj->getSavingsAccountBalance();
        return array(
            'member_id' => $member_id,
            'member_info' => $memberObj->object_info,
            'member_balance' => $member_balance
        );

    }

    public function grantCreditOneTimeLoanWithdrawConfirmOp($p)
    {
        //return new result(false,'test',$p);
        $member_id = $p['member_id'];
        $usd_amount = round($p['currency_usd'],2);
        $khr_amount = round($p['currency_khr'],2);
        $currency_amount = array();
        if( $usd_amount > 0 ){
            $currency_amount[currencyEnum::USD] = $usd_amount;
        }
        if( $khr_amount > 0 ){
            $currency_amount[currencyEnum::KHR] = $khr_amount;
        }
        if( empty($currency_amount) ){
            return new result(false,'No input withdraw amount.',null,errorCodesEnum::INVALID_PARAM);
        }

        $class = new autoDisburseOneTimeByCounterClass();
        $rt = $class->withdraw($member_id,$this->user_id,$currency_amount);
        return $rt;
    }


    /**
     * 获取CreditHistory 列表
     */
    public function getCreditHistoryListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $m_member_authorized_contract = new member_authorized_contractModel();
        $m_member_asset_mortgage = M('member_asset_mortgage');
        //授信合同
        $data = $m_member_authorized_contract->getConstructListByOfficerId($this->user_id, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        $member_ids = implode(',', array_filter(array_column($rows, 'member_id'))) ?: 0;
        if ($member_ids) {
            $list = $m_member_asset_mortgage->getAssetMortgagesAndContract();
            $contract_mortgage = array();
            foreach ($list as $k => $v) {
                $contract_mortgage[$v['authorized_contract_id']][] = $v['asset_type'];
            }
            foreach ($rows as $k => $v) {
                $rows[$k]['mortgages'] = $contract_mortgage[$v['uid']];
            }
        }
        $return =  array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $data->pageIndex,
            "pageTotal" => $pageTotal,
            "pageSize" => $data->pageSize,
        );
        return $return;
    }

    /*
     * 授权合同详细
     * */
    public function showAuthorizeContractDetailOp()
    {
        $uid = $_GET['uid'];
        /**
         * 授权合同详情
         */
        //合同基本信息
        $m_authorized_contract = new member_authorized_contractModel();
        $contract_info = $m_authorized_contract->getConstructBaseInfo($uid);
        //合同图片
        $contract_images = $m_authorized_contract->getConstructImages($uid);
        $contract_info['contract_images'] = array_column($contract_images, 'image_path');

        $member_id = $contract_info['member_id'];
        $m_grant = new member_credit_grantModel();
        $grant_row = $m_grant->find(array("uid" => $contract_info['grant_credit_id']));
        $contract_info['grant_info'] = $grant_row;

        $member_scene_image = objectMemberClass::getNewestSceneImage($member_id);
        Tpl::output('member_scene_image',$member_scene_image);

        //合同抵押物
        $mortgages = $m_authorized_contract->getConstructMortgages($contract_info['contract_no']);
        $contract_info['mortgages'] = $mortgages;
        Tpl::output("contract", $contract_info);


        // 判断此次合同是否可以cancel
        $is_can_cancel = member_credit_grantClass::isAuthorisedContractCanCancel($contract_info);

        Tpl::output('is_can_cancel',$is_can_cancel);


        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info", $client_info);
        Tpl::output("member_id", $member_id);
        Tpl::output("show_menu", "showClientCreditMain");
        Tpl::showPage("authorize.contract.detail");
    }

    public function cancelAuthorizeContractOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $uid = $params['uid'];
        //合同基本信息
        $m_authorized_contract = new member_authorized_contractModel();
        $contract_info = $m_authorized_contract->getConstructBaseInfo($uid);
        if( !$contract_info ){
            showMessage('No contract info:'.$uid);
        }
        $member_id = $contract_info['member_id'];
        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info", $client_info);
        Tpl::output('contract_info',$contract_info);

        Tpl::output("show_menu", "showClientCreditMain");
        Tpl::showpage('authorize.contract.cancel.page');
    }

    public function ajaxCancelCreditContractConfirmOp($p)
    {
        $params = $p;
        $params['cashier_id'] = $this->user_id;
        $bizClass = new bizCreditContractCancelClass();
        $rt = $bizClass->execute($params);
        return $rt;
    }

    function editAuthorizeContractOp()
    {

        $params = array_merge(array(), $_GET, $_POST);
        $received_list = urldecode($params['received_list']);
        $received_list = json_decode($received_list, true);
        $contract_images = explode(',', $params['contract_images']);
        $args['operator_id'] = $this->user_id;
        $args['operator_name'] = $this->user_name;
        $args['branch_id'] = $this->branch_id;
        $args['branch_name'] = $this->branch_name;
        $args['contract_id'] = $params['contract_id'];
        $args['member_image'] = $params['member_image'];
        $args['received_list'] = $received_list;
        $args['contract_images'] = $contract_images;
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $ret = member_credit_grantClass::updateAuthorizeContract($args);
        if ($ret->STS) {
            $conn->submitTransaction();
            showMessage('Authorize successfully!', getUrl('member_credit', 'showAuthorizeContractDetail', array('uid' => $params['contract_id']), false, ENTRY_COUNTER_SITE_URL));
        } else {
            $conn->rollback();
            showMessage('Submit failed!' . $ret->MSG, getUrl('member_credit', 'showAuthorizeContractDetail', array('uid' => $params['contract_id']), false, ENTRY_COUNTER_SITE_URL));
        }
    }

}