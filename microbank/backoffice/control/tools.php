<?php

class toolsControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('certification');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Home");
        Tpl::setDir("tools");
    }

    /**
     * 计算器
     */
    public function calculatorOp()
    {

        //$class_product = new loan_productClass();
        //$valid_products = $class_product->getValidProductList();
        $valid_products = loan_productClass::getAllActiveSubProductList();

        Tpl::output("valid_products", $valid_products);

        $m_core_definition = M('core_definition');
        $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type', 'guarantee_type'));
        Tpl::output("mortgage_type", $define_arr['mortgage_type']);
        Tpl::output("guarantee_type", $define_arr['guarantee_type']);

        $interest_payment = (new interestPaymentEnum())->Dictionary();
        $interest_rate_period = (new interestRatePeriodEnum())->Dictionary();
        Tpl::output("interest_payment", $interest_payment);
        Tpl::output("interest_rate_period", $interest_rate_period);

        Tpl::showPage("calculator");
    }

    /**
     * 贷款计算
     * @param $p
     * @return result
     */
    public function loanPreviewOp($p)
    {
        $creditLoan = new credit_loanClass();
        $re = $creditLoan->loanPreview($p);
        if (!$re->STS) {
            return $re;
        }
        $data = $re->DATA;
        $data_new = array();
        $data_new['loan_amount'] = ncAmountFormat($data['total_repayment']['total_principal']);
        $data_new['repayment_amount'] = ncAmountFormat($data['total_repayment']['total_payment']);
        $data_new['arrival_amount'] = ncAmountFormat($data['arrival_amount']);
        $data_new['service_charge'] = ncAmountFormat($data['loan_fee']);
        $data_new['total_interest'] = ncAmountFormat($data['total_repayment']['total_interest']);
        $data_new['period_repayment_amount'] = ncAmountFormat($data['period_repayment_amount']);
        $data_new['interest_rate'] = $data['interest_rate_type'] == 0 ? ($data['interest_rate'] . '%') : ncAmountFormat($data['interest_rate']);
        $data_new['interest_rate_unit'] = $data['interest_rate_unit'];
        $data_new['repayment_number'] = count($data['repayment_schema']);
        if ($data_new['repayment_number'] > 1) {
            $first_repayment = array_shift($data['repayment_schema']);
            $second_repayment = array_shift($data['repayment_schema']);
            if ($first_repayment['amount'] == $second_repayment['amount']) {
                $data_new['each_repayment'] = ncAmountFormat($first_repayment['amount']);
                $data_new['single_repayment'] = 0;
                $data_new['first_repayment'] = 0;
            } else {
                $data_new['first_repayment'] = ncAmountFormat($first_repayment['amount']);
                $data_new['single_repayment'] = 0;
                $data_new['each_repayment'] = 0;
            }
        } else {
            $first_repayment = array_shift($data['repayment_schema']);
            $data_new['single_repayment'] = ncAmountFormat($first_repayment['amount']);
            $data_new['first_repayment'] = 0;
            $data_new['each_repayment'] = 0;
        }
        $data_new['operation_fee'] = ncAmountFormat($first_repayment['receivable_operation_fee']);
        $re->DATA = $data_new;
        return $re;
    }

    /**
     * 搜索身份证信息
     */
    public function searchIdSnOp()
    {
        Tpl::showPage('search.id.sn');
    }

    /**
     * 获取身份证信息
     * @param $p
     * @return array
     */
    public function getIdInfoBySnOp($p)
    {
        Language::read('operator');
        $search_text = trim($p['search_text']);
        $m_member_verify_cert = M('member_verify_cert');
        $m_member_verify_cert_image = M('member_verify_cert_image');
        $m_client_member = M('client_member');
        $cert_info = $m_member_verify_cert->find(array('cert_sn' => $search_text));
        if ($cert_info) {
            $cert_info['cert_images'] = $m_member_verify_cert_image->select(array('cert_id' => $cert_info['uid']));
            $client_info = $m_client_member->find(array('uid' => $cert_info['member_id']));
        }

        $sample_images = global_settingClass::getCertSampleImage();
        return array(
            'sts' => true,
            'data' => $cert_info,
            'client_info' => $client_info,
            'cert_sample_images' => $sample_images
        );
    }

    /**
     * 清楚client错误密码输入次数
     */
    public function clearTradingPwdLockOp()
    {
        Tpl::showPage('clear.trading.pwd.lock');
    }

    /**
     * 获取会员交易密码输入错误情况
     */
    public function getMemberTradingPwdInfoOp($p)
    {
        $country_code = trim($p['country_code']);
        $phone = trim($p['phone']);

        $format_phone = tools::getFormatPhone($country_code, $phone);
        $contact_phone = $format_phone['contact_phone'];

        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('phone_id' => $contact_phone, 'is_verify_phone' => 1));
        if (!$client_info) {
            return array(
                'sts' => true,
                'data' => array()
            );
        }

        $m_member_verify_trading_password_log = M('member_verify_trading_password_log');
        $today_error_times = $m_member_verify_trading_password_log->getDayErrorTimes($client_info['uid']);
        $client_info['today_error_times'] = intval($today_error_times);
        return array(
            'sts' => true,
            'data' => $client_info
        );
    }

    public function clearMemberErrorTradingPwdTimesOp($p)
    {
        $member_id = intval($p['member_id']);
        $m_member_verify_trading_password_log = M('member_verify_trading_password_log');
        return $m_member_verify_trading_password_log->clearErrorTimes($member_id);
    }

    /**
     * 搜索资产信息
     */
    public function searchAssetSnOp()
    {
        $verify_field = enum_langClass::getCertificationTypeEnumLang();
        $asset_type = array(
            certificationTypeEnum::CAR => $verify_field[certificationTypeEnum::CAR],
            certificationTypeEnum::HOUSE => $verify_field[certificationTypeEnum::HOUSE],
            certificationTypeEnum::LAND => $verify_field[certificationTypeEnum::LAND],
            certificationTypeEnum::MOTORBIKE => $verify_field[certificationTypeEnum::MOTORBIKE],
        );
        Tpl::output('asset_type', $asset_type);
        Tpl::showPage('search.asset.sn');
    }

    /**
     * 获取资产信息
     * @param $p
     * @return array
     */
    public function getAssetInfoBySnOp($p)
    {
        $asset_sn = trim($p['search_text']);
        $asset_type = trim($p['asset_type']);

        $m_member_assets = M('member_assets');
        $asset_info = $m_member_assets->find(array(
                'asset_sn' => $asset_sn,
                'asset_type' => $asset_type,
                'asset_state' => array('>=', assetStateEnum::CERTIFIED)
            )
        );

        if ($asset_info) {
            $asset_id = intval($asset_info['uid']);
            $ret = credit_officerClass::getMemberAssetDetailAndResearchInfo($asset_id, $this->user_id, true);
            $asset = $ret->DATA;

            $member_id = $asset['member_id'];
            $client_info = memberClass::getMemberBaseInfo($member_id);

            $r = new ormReader();
            $sql = "SELECT * FROM member_assets_evaluate WHERE member_assets_id = $asset_id AND evaluator_type = 1 ";
            $asset_evaluate = $r->getRow($sql);

            $m = new member_assets_rentalModel();
            $asset_rental = $m->orderBy('uid desc')->find(array(
                'asset_id' => $asset_id,
            ));

            if ($asset_rental) {
                $m_member_assets_rental_image = new member_assets_rental_imageModel();
                $images = $m_member_assets_rental_image->select(array(
                    'rental_id' => $asset_rental['uid']
                ));
                $asset_rental['images'] = $images;
            }

            //对应的grant的还贷情况
            $loan_ret = member_assetsClass::getAssetRelativeContract($asset_id);
            $principal_outstanding = $loan_ret['principal_outstanding'];
            $loan_list = $loan_ret['contract_list'];

            //保存流水
            $storage_list = member_assetsClass::getAssetStorageFlow($asset_id);

            //获取本行的teller_id
            $receiver_list = counter_baseClass::getBranchUserListOfTeller($this->branch_id);

            //获取未接受列表，可以删除
            $m_transfer = new member_assets_storageModel();
            $request_transfer = $m_transfer->select(array("from_operator_id" => $this->user_id, "is_pending" => 1, "flow_type" => assetStorageFlowType::TRANSFER, "member_asset_id" => $asset_id));
        }

        return array(
            'sts' => true,
            'data' => $asset,
            'client_info' => $client_info,
            'asset_evaluate' => $asset_evaluate,
            'asset_rental' => $asset_rental,
            'loan_list' => $loan_list,
            'principal_outstanding' => $principal_outstanding,
            'storage_list' => $storage_list,
            'receiver_list' => $receiver_list,
            'request_transfer' => $request_transfer,
        );
    }

    /**
     * 搜索客户，只读
     */
    public function searchClientOp()
    {
        Tpl::showPage("search.client.phone");

    }

    public function getMemberInfoByPhoneOp($p)
    {
        $country_code = trim($p['country_code']);
        $phone = trim($p['phone_number']);

        $format_phone = tools::getFormatPhone($country_code, $phone);
        $contact_phone = $format_phone['contact_phone'];

        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('phone_id' => $contact_phone, 'is_verify_phone' => 1));
        if (!$client_info) {
            return array(
                'sts' => false,
            );
        }
        $member_id = $client_info['uid'];
        $rt = memberInfoClass::getMemberDetail($member_id);
        $data = $rt->DATA;
        $data['sts'] = true;
        return $data;
    }


    public function searchAceAccountOp()
    {

        Tpl::showPage('search.ace.account');
    }

    public function getAceAccountListOp($p)
    {
        $page_num = intval($p['pageNumber']);
        $page_size = intval($p['pageSize']);
        $page_list = member_handlerClass::getAllClientBindAceAccount($page_num, $page_size, $p);

        return array(
            "sts" => true,
            "data" => $page_list->rows,
            "total" => $page_list->count,
            "pageNumber" => $page_list->pageIndex,
            "pageTotal" => $page_list->pageCount,
            "pageSize" => $page_list->pageSize
        );
    }

    public function googleMapOp()
    {
        $array = array(
            0 => array('x' => 11.558075, 'y' => 104.931961),
            1 => array('x' => 11.562784, 'y' => 104.923657),
            2 => array('x' => 11.553906, 'y' => 104.915844),
            3 => array('x' => 11.547974, 'y' => 104.919400),
        );
        Tpl::output('coord_json', my_json_encode($array));
        Tpl::showPage("google.map");
    }
    public function testJsOp(){
        Tpl::showPage("test.js");
    }
}
