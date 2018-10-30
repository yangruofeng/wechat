<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/6/9
 * Time: 22:30
 */
class member_loanControl extends counter_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('member');
        Tpl::setDir('member_loan');
        Tpl::setLayout('home_layout');
        Tpl::output("sub_menu", $this->getMemberBusinessMenu());
    }

    public function loanIndexOp()
    {
        $member_id = $_GET['member_id'];
        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info", $client_info);
        Tpl::output("member_id", $member_id);

        $member_asset_mortgage_type = member_assetsClass::getMemberAssetMortgagedType($member_id);

        $sub_product_list = loan_productClass::getMemberCanLoanSubProductListForCounter($member_id);
        Tpl::output("product_list", $sub_product_list);

        $credit_category = loan_categoryClass::getMemberCreditCategoryList($member_id);
        Tpl::output("credit_category", $credit_category);


        Tpl::showPage("loan.index.v2");
    }

    /**
     * 获取添加历史
     * @param $p
     * @return array
     */
    public function getAddContractListOp($p)
    {
        $cashier_id = $this->user_id;
        $r = new ormReader();
        $sql = "SELECT bmclc.*,lc.contract_sn,lsp.sub_product_name,lca.category_name,mcc.alias FROM biz_member_create_loan_contract bmclc"
            . " INNER JOIN member_credit_category mcc ON bmclc.member_credit_category_id = mcc.uid"
            . " INNER JOIN loan_sub_product lsp ON bmclc.sub_product_id=lsp.uid"
            . " INNER JOIN loan_contract lc ON bmclc.contract_id = lc.uid"
            . " INNER JOIN loan_category lca ON lca.uid=mcc.category_id"
            . " WHERE bmclc.state = '" . bizStateEnum::DONE . "' AND bmclc.cashier_id=" . $cashier_id;
        //debug($sql);
        $sql .= " ORDER BY bmclc.update_time DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 获取待取消贷款列表
     */
    public function getPendingCancelListOp($p)
    {
        $member_id = intval($p['member_id']);
        $r = new ormReader();
        $sql = "SELECT lc.*,mcc.alias FROM loan_contract lc"
            . " INNER JOIN member_credit_category mcc ON lc.member_credit_category_id = mcc.uid"
            . " INNER JOIN loan_account la on lc.account_id=la.uid"
            . " INNER JOIN client_member cm on la.obj_guid=cm.obj_guid"
            . " WHERE lc.state >= " . qstr(loanContractStateEnum::CREATE) . " AND lc.state <= " . qstr(loanContractStateEnum::PENDING_APPROVAL) . " AND cm.uid=" . $member_id;
        $sql .= " ORDER BY lc.uid DESC";
        $data = $r->getRows($sql);
        return array(
            "sts" => true,
            "data" => $data,
        );
    }

    public function pendingCancelOp($p)
    {
        $contract_id = intval($p['contract_id']);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt = loan_baseClass::cancelcontract($contract_id);
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return new result(true, 'Cancel Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }


    public function ajaxGetLoanCategoryOptionOp($p)
    {
        $sub_product_id = intval($p['sub_product_id']);
        $member_category_id = intval($p['m_uid']);  // member_credit_category.id
        $member_id = intval($p['member_id']);
        $rt = credit_loanClass::getMemberLoanOptionByCategoryNew($member_id, $member_category_id);
        // 展示利率信息
        $list = counter_memberClass::getMemberLoanProductInterestList($member_category_id, $sub_product_id, $member_id);
        $rt->DATA['rate_list'] = $list;
        return $rt;
    }

    /**
     * 获取时间币种列表
     * @param $p
     * @return result
     */
    public function getTimeAndCurrencyChooseOp($p)
    {

        $sub_product_id = intval($p['sub_product_id']);
        $m_uid = intval($p['m_uid']);  // member_credit_category.id
        $member_id = intval($p['member_id']);
        $r = new ormReader();
        $sql = "SELECT currency FROM loan_product_size_rate WHERE product_id =" . $sub_product_id;
        $sql .= " group by currency";
        $currency_info = $r->getRows($sql);
        //$loan_time = credit_loanClass::getMemberCreditLoanValidTerms($sub_product_id, $member_id);
        $rt = credit_loanClass::getMemberLoanOptionByCategory($member_id, $m_uid);
        if ($rt->STS) {
            $loan_time = $rt->DATA;
            $choose = array(
                'currency' => $currency_info,
                'loan_time' => $loan_time,
                'm_uid' => $m_uid
            );
            // 展示利率信息
            $list = counter_memberClass::getMemberLoanProductInterestList($m_uid, $sub_product_id, $member_id);
            $choose['rate_list'] = $list;

            return new result(true, '', $choose);
        } else {
            return new result(false, 'No Time Info');
        }
    }

    /**
     * 创建贷款第一步(创建合同草稿)
     */
    public function addLoanContractStepOneOp()
    {
        $p = array_merge(array(), $_GET, $_POST);

        $member_id = intval($p['member_uid']);
        $sub_product_id = intval($p['product_id']);
        $m_uid = intval($p['m_uid']);
        $amount = round($p['amount'], 2);
        $currency = trim($p['currency']);
        $loan_terms = intval($p['terms']);
        $term_type = trim($p['term_type']);

        // 格式化贷款周期后在调用
        $loan_period = $loan_terms;
        // 周期类型0表示月，1表示天
        if ($term_type == 1) {
            $loan_period_type = loanPeriodUnitEnum::DAY;
        } else {
            $loan_period_type = loanPeriodUnitEnum::MONTH;
        }

        $member_image = objectMemberClass::getNewestSceneImage($member_id);
        Tpl::output('member_scene_image', $member_image);

        // 业务操作放在bizClass里面去(第一步)
        try {
            $conn = ormYo::Conn();
            $conn->startTransaction();
            $bizClass = new bizMemberCreateLoanContractClass(bizSceneEnum::COUNTER);
            $rt = $bizClass->createContract($this->user_id, $member_id, $m_uid, $amount, $currency, $loan_period, $loan_period_type);
            if (!$rt->STS) {
                $conn->rollback();
                showMessage($rt->MSG);
            }
            $conn->submitTransaction();

            $data = $rt->DATA;

            Tpl::output('contract_detail_data',$data);

            $biz_id = $data['biz_id'];

            // 检查是否需要CT 验证
            $ct_check = $bizClass->isNeedCTApprove($biz_id);
            Tpl::output('is_ct_check', $ct_check);


            $m_member = M('client_member');
            $member_info = $m_member->find(array('uid' => $member_id));
            Tpl::output('member_info', $member_info);

            Tpl::output('biz_id', $data['biz_id']);
            Tpl::output('contract_id', $data['contract_id']);

            Tpl::output('first_repay', reset($data['loan_installment_scheme']));

            Tpl::output('due_date', $data['due_date']);
            Tpl::output('due_date_type', $data['due_date_type_val']);


            $total_repay = array(
                'total_repayment' => $data['total_repayment'],
                'total_loan' => $data['loan_amount'],
                'total_interest' => $data['total_interest'],
                'total_admin_fee' => $data['total_admin_fee'],
                'total_loan_fee' => $data['total_loan_fee'],
                'total_operation_fee' => $data['total_operation_fee'],
                'total_insurance_fee' => $data['total_insurance_fee'],
                'actual_receive_amount' => $data['actual_receive_amount'],
            );
            Tpl::output('total_repay', $total_repay);

            Tpl::output('loan_installment_scheme', $data['loan_installment_scheme']);
            Tpl::output('show_menu', 'loanIndex');
            Tpl::output("member_id", $member_id);
            Tpl::output('contract_info', $data['contract_info']);

            Tpl::showPage('contract.add.confirm');


        } catch (Exception $e) {
            showMessage($e->getMessage());

        }

    }


    /**
     * 确认添加合同
     */
    public function addContractSubmitOp($p)
    {
        $bizClass = new bizMemberCreateLoanContractClass(bizSceneEnum::COUNTER);
        $biz_id = intval($p['biz_id']);
        $cashier_card_no = trim($p['cashier_card_no']);
        $cashier_key = trim($p['cashier_key']);
        $client_trade_pwd = trim($p['client_trade_pwd']);
        $card_no = trim($p['chief_teller_card_no']);
        $key = trim($p['chief_teller_key']);
        $member_image = $p['add_contract_image'];

        // 先完成所有的验证（第二步）
        //验证密码
        $rt_1 = $bizClass->checkMemberTradingPassword($biz_id, $client_trade_pwd);
        if (!$rt_1->STS) {
            return new result(false, "Client Password Error:" . $rt_1->MSG);
        }
        $rt_2 = $bizClass->checkTellerPassword($biz_id, $cashier_card_no, $cashier_key);
        if (!$rt_2->STS) {
            return new result(false, 'Teller Trading Password Error:' . $rt_2->MSG);
        }

        if ($bizClass->isNeedCTApprove($biz_id)) {
            $rt_3 = $bizClass->checkChiefTellerPassword($biz_id, $card_no, $key);
            if (!$rt_3->STS) {
                return new result(false, 'Chief teller password error:' . $rt_3->MSG);
            }
        }


        // 执行最终步骤（第三步）
        //启动事务
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {

            $rt = $bizClass->insertMemberInfo($biz_id, $member_image);
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }

            $rt = $bizClass->confirmContract($biz_id);
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }

            $conn->submitTransaction();
            return new result(true, 'Create Successful!');

        } catch (Exception $ex) {

            $conn->rollback();
            return new result(false, $ex->getMessage());
        }

    }

    public function addContractCancelOp($p)
    {
        $biz_id = intval($p['biz_id']);
        $bizClass = new bizMemberCreateLoanContractClass(bizSceneEnum::COUNTER);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $rt = $bizClass->cancelContract($biz_id);
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return new result(true, 'Cancel Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }


    public function repaymentIndexOp()
    {
        $member_id = $_GET['member_id'];
        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        $member_scene_image = objectMemberClass::getNewestSceneImage($member_id);
        Tpl::output('member_scene_image', $member_scene_image);
        Tpl::output("client_info", $client_info);
        Tpl::output("member_id", $member_id);

        $repayment_plan = member_loan_schemaClass::getMemberPendingRepaymentContractGroupByProduct($client_info['uid']);
        Tpl::output('repayment_plan', $repayment_plan);
        Tpl::showPage("repayment.index");

    }

    /**
     * 获取还款计划列表
     * @param $p
     * @return result
     */
    public function getMemberRepaymentProductOp($p)
    {
        $member_id = $p['member_id'];
        $client_info = counter_baseClass::getMemberInfoByID($member_id);

        $repayment_plan = member_loan_schemaClass::getMemberAllLoanRepaymentSchemaGroupByProduct($client_info['uid']);
        return array(
            "sts" => true,
            "data" => $repayment_plan,
            "client_info" => $client_info,
        );
    }

    /**
     * 获取指定产品的下期还款计划
     * @param $p
     */
    public function getNextRepaymentByProductOp($p)
    {
        $member_id = intval($p['member_id']);
        $member_credit_category_id = intval($p['member_credit_category_id']);

        $client_info = memberClass::getMemberBaseInfo($member_id);

        $repayment_plan = member_loan_schemaClass::getMemberPendingRepaymentSchema($member_id, array("member_credit_category_id" => $member_credit_category_id));
        if (!$repayment_plan) {
            return array(
                "sts" => true,
                "data" => array(),
            );
        }
        $ret_list = array();
        foreach ($repayment_plan as $item) {
            if (!($ret_list[$item['contract_id']])) {
                $ret_list[$item['contract_id']] = array_merge($item, array("is_checked" => true));
            }
        }
        $currency_list = (new currencyEnum())->Dictionary();
        return array(
            "sts" => true,
            "schema_list" => $ret_list,
            "next_repayment_arr" => array(),
            "currency_list" => $currency_list,
            "member_id" => $member_id,
            "is_verify" => $client_info['member_state'] == memberStateEnum::VERIFIED ? true : false
        );
    }

    public function getRepaymentSchemaByContractOp($p)
    {
        $member_id = intval($p['member_id']);
        $contract_id = intval($p['contract_id']);
        $client_info = memberClass::getMemberBaseInfo($member_id);
        $repayment_plan = member_loan_schemaClass::getMemberPendingRepaymentSchema($member_id, array("contract_id" => $contract_id));
        if (!$repayment_plan) {
            return array(
                "sts" => true,
                "data" => array(),
            );
        }
        $ret_list = $repayment_plan;
        $ret_list[0]['is_checked'] = true;


        $currency_list = (new currencyEnum())->Dictionary();
        return array(
            "sts" => true,
            "schema_list" => $ret_list,
            "currency_list" => $currency_list,
            'hide_contract_sn' => true,
            "member_id" => $member_id,
            "is_verify" => $client_info['member_state'] == memberStateEnum::VERIFIED ? true : false
        );
    }

    /**
     * 指定产品下还款计划
     * @param $p
     * @return array
     */
    public function getRepaymentProductDetailOp($p)
    {
        $member_id = intval($p['member_id']);
        $sub_product_id = intval($p['sub_product_code']);

        $client_info = memberClass::getMemberBaseInfo($member_id);

        $repayment_plan = member_loan_schemaClass::getMemberAllLoanRepaymentSchemaGroupByProduct($member_id, false);
        if (!$repayment_plan) {
            return array(
                "sts" => true,
                "data" => array(),
            );
        }
        $repayment_product_detail = $repayment_plan[$sub_product_id];
        $next_repayment_arr = array();
        $end_this_month = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
        foreach ($repayment_product_detail['schema_list'] as $key => $val) {
            if ($end_this_month >= strtotime($val['receivable_date'])) {
                $repayment_product_detail['schema_list'][$key]['is_checked'] = true;
                $next_repayment_arr[$val['currency']] = round($next_repayment_arr[$val['currency']], 2) + $val['total_payable_amount'];
            } else {
                break;
            }
        }
        $currency_list = (new currencyEnum())->Dictionary();
        return array(
            "sts" => true,
            "data" => $repayment_product_detail,
            "next_repayment_arr" => $next_repayment_arr,
            "currency_list" => $currency_list,
            "member_id" => $member_id,
            "is_verify" => $client_info['member_state'] == memberStateEnum::VERIFIED ? true : false
        );
    }

    /**
     * 某一期还款详情
     * @param $p
     * @return array
     */
    public function getRepaymentPlanDetailOp($p)
    {
        $member_id = intval($p['member_id']);
        $plan_index = intval($p['plan_index']);
        $repayment_plan = memberClass::getMemberAllPendingRepaymentSchemaGroupByDay($member_id);
        $currency_list = (new currencyEnum())->Dictionary();
        return array(
            "sts" => true,
            "data" => $repayment_plan['list'][$plan_index],
            "currency_list" => $currency_list,
            "member_id" => $member_id
        );
    }

    protected function getExchangeRateUsdAndKhr($type = 0)
    {
        if ($type) {
            $exchange_rate = M('common_exchange_rate')->find(array('first_currency' => currencyEnum::USD, 'second_currency' => currencyEnum::KHR));
            if ($exchange_rate) {
                $exchange_list = array(
                    'USD_KHR' => $exchange_rate['buy_rate'] / $exchange_rate['buy_rate_unit'],
                    'KHR_USD' => $exchange_rate['sell_rate_unit'] / $exchange_rate['sell_rate'],
                );
            } else {
                $exchange_rate = M('common_exchange_rate')->find(array('second_currency' => currencyEnum::USD, 'first_currency' => currencyEnum::KHR));
                $exchange_list = array(
                    'USD_KHR' => $exchange_rate['sell_rate'] / $exchange_rate['sell_rate_unit'],
                    'KHR_USD' => $exchange_rate['buy_rate_unit'] / $exchange_rate['buy_rate'],
                );
            }
        } else {
            $exchange_list = array(
                'USD_KHR' => global_settingClass::getCurrencyRateBetween(currencyEnum::USD, currencyEnum::KHR),
                'KHR_USD' => global_settingClass::getCurrencyRateBetween(currencyEnum::KHR, currencyEnum::USD),
            );
        }
        return $exchange_list;

    }

    /**
     * 还款第一步
     */
    public function repaymentStepOneOp($p)
    {
        $member_id = intval($p['member_id']);
        $schema_id = $p['scheme_id'];
        if (!is_array($schema_id)) {
            $schema_id = array($schema_id);
        }
        $m_biz = bizFactoryClass::getInstance(bizSceneEnum::COUNTER, bizCodeEnum::MEMBER_LOAN_REPAYMENT_BY_CASH);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $m_biz->stepSelectSchemas($this->user_id, $member_id, $schema_id);
        if ($rt->STS) {
            $conn->submitTransaction();
            $currency_list = (new currencyEnum())->Dictionary();
            $exchange_list = $this->getExchangeRateUsdAndKhr(1);

            return array(
                "sts" => true,
                "data" => $rt->DATA,
                "currency_list" => $currency_list,
                "exchange_list" => $exchange_list,
            );
        } else {
            $conn->rollback();
            return array(
                "sts" => false,
                "msg" => $rt->MSG
            );
        }
    }

    /**
     * 还款第二步
     */
    public function repaymentStepTwoOp($p)
    {
        $biz_id = intval($p['biz_id']);
        $currency_list = (new currencyEnum())->Dictionary();
        $currency_amount = array();
        foreach ($currency_list as $key => $currency) {
            $currency_amount[$key] = $p[strtolower($key) . '_amount'];
        }
        $m_biz = new bizMemberLoanRepaymentByCashClass(bizSceneEnum::COUNTER);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $m_biz->stepInputAmount($biz_id, $currency_amount);
        if ($rt->STS) {
            $conn->submitTransaction();
            $is_ct_check = $m_biz->isNeedCTApprove($biz_id);
            $biz_detail = $m_biz->getBizDetailById($biz_id);
            $member_id = $biz_detail['member_id'];
            $member_scene_image = objectMemberClass::getNewestSceneImage($member_id);
            return array(
                "sts" => true,
                "biz_id" => $biz_id,
                "currency_amount" => $currency_amount,
                'is_ct_check' => $is_ct_check,
                'member_scene_image' => $member_scene_image
            );
        } else {
            $conn->rollback();
            return array(
                "sts" => false,
                "msg" => $rt->MSG
            );
        }
    }

    /**
     * 确定还款
     * @param $p
     * @return result
     * @throws Exception
     */
    public function submitRepaymentOp($p)
    {

        $biz_id = intval($p['biz_id']);
        $cashier_card_no = trim($p['cashier_card_no']);
        $cashier_key = trim($p['cashier_key']);
        $chief_teller_card_no = trim($p['chief_teller_card_no']);
        $chief_teller_key = trim($p['chief_teller_key']);
        $member_image = trim($p['member_image']);
        $m_biz = new bizMemberLoanRepaymentByCashClass(bizSceneEnum::COUNTER);

        $rt_1 = $m_biz->checkTellerPassword($biz_id, $cashier_card_no, $cashier_key);
        if (!$rt_1->STS) {
            return $rt_1;
        }

        if ($m_biz->isNeedCTApprove($biz_id)) {
            $rt_2 = $m_biz->checkChiefTellerPassword($biz_id, $chief_teller_card_no, $chief_teller_key);
            if (!$rt_2->STS) {
                return $rt_2;
            }
        }

        //  外部不使用事务,方法内处理事务
        $rt = $m_biz->insertMemberInfo($biz_id, $member_image);
        if (!$rt->STS) {
            return $rt;
        }

        $rt_3 = $m_biz->bizSubmit($biz_id);
        $ret=new result($rt_3->STS,$rt_3->MSG);
        debug($ret);
        return $ret;


    }

    /**
     * 获取还款列表
     * @param $p
     * @return array
     */
    public function getRepaymentListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $m_repayment = new biz_member_loan_repaymentModel();
        $list = $m_repayment->getRepaymentList($pageNumber, $pageSize, array('cashier_id' => $this->user_id));
        return $list;
    }

    public function penaltyIndexOp()
    {
        $member_id = $_GET['member_id'];
        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info", $client_info);
        Tpl::output("member_id", $member_id);

        // 获取待减免列表
        $loan_penalty = new loan_penaltyModel();
        $penalty_list = $loan_penalty->getPenaltyByMemberId($member_id);
        Tpl::output("penalty_plan_list", $penalty_list);

        //获取申请减免列表
        $loan_penalty_receipt = new loan_penalty_receiptModel();
        $pending_penalty = $loan_penalty_receipt->getPendingPenaltyByMemberId($member_id);
        Tpl::output("penalty_pending_list", $pending_penalty);

        Tpl::showPage("penalty.index");

    }

    /**
     * 获取历史列表
     * @param $p
     * @return array
     */
    public function getPenaltyHistoryListOp($p)
    {
        $cashier_id = $this->user_id;
        $r = new ormReader();
        $sql = "SELECT brmp.*,cm.login_code FROM biz_receive_member_penalty brmp INNER JOIN client_member cm ON brmp.member_id = cm.uid WHERE brmp.state = 100 AND brmp.operator_id=" . $cashier_id;
        $sql .= " ORDER BY brmp.uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        if ($rows) {
            $r = new ormReader();
            foreach ($rows as $key => $row) {
                $sql_2 = "SELECT brmpd.currency, brmpd.amount FROM biz_receive_member_penalty brmp LEFT JOIN biz_receive_member_penalty_detail brmpd ON brmp.uid = brmpd.biz_id WHERE brmp.uid = " . $row['uid'];
                $currency_amount = $r->getRows($sql_2);
                $currency_amount = resetArrayKey($currency_amount, 'currency');
                $row['USD'] = $currency_amount['USD'];
                $row['KHR'] = $currency_amount['KHR'];
                $rows[$key] = $row;
            }
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "current_user" => $this->user_id
        );
    }

    /**
     * 减免罚金第一步
     * @param $p
     * @return array|result
     */
    public function reducePenaltyApplyOneOp($p)
    {
        $member_id = intval($p['member_id']);
        $loan_penalty = new loan_penaltyModel();
        $penalty_list = $loan_penalty->getPenaltyByMemberId($member_id);
        if (!$penalty_list) {
            return array(
                "sts" => false,
                "data" => '',
            );
        }

        $currency_list = (new currencyEnum())->Dictionary();
        $currency_total = array();
        foreach ($penalty_list as $val) {
            $currency_total[$val['currency']] = round($currency_total[$val['currency']], 2) + $val['penalty_amount'];
        }

        $one_currency_total = array();
        foreach ($currency_list as $key => $currency) {
            foreach ($currency_total as $k_currency => $amount) {
                $exchange_rate = global_settingClass::getCurrencyRateBetween($k_currency, $key);
                if ($exchange_rate <= 0) {
                    return new result(false, 'Not set currency exchange rate:' . $k_currency . '-' . $key, null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
                }
                $one_currency_total[$key] += round($exchange_rate * $amount, 2);
            }
        }
        return array(
            "sts" => true,
            "currency_list" => $currency_list,
            "currency_total" => $currency_total,
            "one_currency_total" => $one_currency_total
        );
    }

    /**
     * 提交减免罚金申请
     */
    public function submitReducePenaltyApplyOp($p)
    {

        $member_id = intval($p['member_id']);
        $currency = trim($p['currency']);
        $deducting = round($p['deducting'], 2);
        $remark = trim($p['remark']);
        $creator_id = $this->user_id;
        $loan_penalty = new loan_penaltyModel();
        $penalty_list = $loan_penalty->getPenaltyByMemberId($member_id);
        if (!$penalty_list) {
            return new result(false, 'Penalty Empty.');
        }

        $penalty_ids = array_column($penalty_list, 'uid');
        $currency_total = array();
        foreach ($penalty_list as $val) {
            $currency_total[$val['currency']] = round($currency_total[$val['currency']], 2) + $val['penalty_amount'];
        }

        $receivable = 0;
        foreach ($currency_total as $k_currency => $amount) {
            $exchange_rate = global_settingClass::getCurrencyRateBetween($k_currency, $currency);
            if ($exchange_rate <= 0) {
                return new result(false, 'Not set currency exchange rate:' . $k_currency . '-' . $currency, null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
            }
            $receivable += round($exchange_rate * $amount, 2);
        }

        $penalty_info = array(
            'receivable' => $receivable,
            'deducting' => $deducting,
            'currency' => $currency,
            'remark' => $remark,
        );

        $class_member_penalty = new member_penaltyClass();
        $rt = $class_member_penalty->addReduceApply($member_id, $penalty_info, $penalty_ids, $creator_id);

        if ($rt->STS) {
            return new result(true, $rt->MSG);
        } else {
            return new result(false, $rt->MSG);
        }
    }

    /**
     * 确认交款金额
     */
    public function penaltyReceiveMoneyOp($p)
    {
        $user_id = $this->user_id;
        $member_id = intval($p['member_id']);
        $receipt_id = intval($p['receipt_id']);
        $biz_penalty = new bizReceiveLoanPenaltyClass(bizSceneEnum::COUNTER);
        $rt = $biz_penalty->bizStart($user_id, $member_id, $receipt_id);
        if (!$rt->STS) {
            return array(
                "sts" => false,
                "msg" => $rt->MSG
            );
        }
        $biz_id = $rt->DATA['biz_id'];

        $loan_penalty_receipt = new loan_penalty_receiptModel();
        $r = $loan_penalty_receipt->getPenaltyByUid($receipt_id);
        $currency = $r->DATA['currency'];
        $amount = $r->DATA['paid'];
        $currency_list = (new currencyEnum())->Dictionary();
        foreach ($currency_list as $key => $value) {
            $exchange_rate = global_settingClass::getCurrencyRateBetween($currency, $key);
            if ($exchange_rate <= 0) {
                return new result(false, 'Not set currency exchange rate:' . $currency . '-' . $key, null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
            }
            $currency_list[$key] = round($exchange_rate * $amount, 2);
        }

        if ($r->STS) {
            $exchange_list = $this->getExchangeRateUsdAndKhr(1);

            return array(
                "sts" => true,
                "data" => $r->DATA,
                "exchange_amount" => $currency_list,
                "receipt_id" => $receipt_id,
                "biz_id" => $biz_id,
                'exchange_list' => $exchange_list,
            );
        } else {
            return array(
                "sts" => false,
            );
        }
    }

    /**
     * 罚金第二步
     */
    public function penaltyStepTwoOp($p)
    {
        $biz_id = intval($p['biz_id']);
        $payment_way = intval($p['payment_way']);
        $default_currency = trim($p['default_currency']);
        $default_amount = round($p['default_amount'], 2);
        $default_currency_amount = array(
            $default_currency => $default_amount
        );
        $remark = trim($p['remark']);
        $currency_amount = array();
        $currency_list = (new currencyEnum())->Dictionary();
        foreach ($currency_list as $key => $currency) {
            $currency_amount[$key] = $p[strtolower($key) . '_amount'];
        }
        $biz_penalty = new bizReceiveLoanPenaltyClass(bizSceneEnum::COUNTER);
        if ($payment_way == repaymentWayEnum::PASSBOOK) {
            $rt_1 = $biz_penalty->receiveMoney($biz_id, $payment_way, $default_currency_amount, $remark);
            if (!$rt_1->STS) {
                return array(
                    "sts" => false,
                    "msg" => $rt_1->MSG,
                );
            }
        }
        if ($payment_way == repaymentWayEnum::CASH) {
            $rt_1 = $biz_penalty->receiveMoney($biz_id, $payment_way, $currency_amount, $remark);
            if (!$rt_1->STS) {
                return array(
                    "sts" => false,
                    "msg" => $rt_1->MSG,
                );
            }
        }
        $rt_2 = $biz_penalty->getBizDetailById($biz_id);
        $member_id = $rt_2['member_id'];

        $is_ct_check = $biz_penalty->isNeedCTApprove($biz_id);
        if ($rt_2) {
            $member_scene_image = objectMemberClass::getNewestSceneImage($member_id);
            return array(
                "sts" => true,
                "data" => $rt_2,
                'currency_amount' => $currency_amount,
                'default_currency_amount' => $default_currency_amount,
                'is_ct_check' => $is_ct_check,
                'member_scene_image' => $member_scene_image
            );
        } else {
            return array(
                "sts" => false,
            );
        }

    }

    /**
     * 确定罚金
     * @param $p
     * @return result
     * @throws Exception
     */
    public function submitPenaltyOp($p)
    {
        $biz_id = intval($p['biz_id']);
        $cashier_card_no = trim($p['cashier_card_no']);
        $cashier_key = trim($p['cashier_key']);
        $chief_teller_card_no = trim($p['chief_teller_card_no']);
        $chief_teller_key = trim($p['chief_teller_key']);
        $client_trade_pwd = trim($p['client_trade_pwd']);
        $member_image = trim($p['member_image']);
        $biz_penalty = new bizReceiveLoanPenaltyClass(bizSceneEnum::COUNTER);

        /* $rt_1 = $biz_penalty->checkMemberTradingPassword($biz_id, $client_trade_pwd);
         if (!$rt_1->STS) {
             return new result(false, "Client Password Error:" . $rt_1->MSG);
         }*/

        $rt_2 = $biz_penalty->checkTellerPassword($biz_id, $cashier_card_no, $cashier_key);
        if (!$rt_2->STS) {
            return new result(false, "Teller Password Error:" . $rt_2->MSG);
        }

        if ($biz_penalty->isNeedCTApprove($biz_id)) {
            $rt_3 = $biz_penalty->checkChiefTellerPassword($biz_id, $chief_teller_card_no, $chief_teller_key);
            if (!$rt_3->STS) {
                return new result(false, 'Chief Teller Trading Password Error:' . $rt_3->MSG);
            }
        }


        //启动事务
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {

            // 录入客户信息
            $rt = $biz_penalty->insertMemberInfo($biz_id, $member_image);
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Insert client info fail:' . $rt->MSG);
            }

            $rt_4 = $biz_penalty->bizSubmit($biz_id);
            if (!$rt_4->STS) {
                $conn->rollback();
                return new result(false, $rt_4->MSG);
            }
            $conn->submitTransaction();
            return new result(true, 'Submit Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    public function contractIndexOp()
    {
        $contract_id = intval($_GET['contract_id']);
        $m_loan_contract = M('loan_contract');
        $loan_contract = $m_loan_contract->find(array('uid' => $contract_id));
        if (!$loan_contract) {
            showMessage("No Contract Found By ID:" . $contract_id);
        }
        $contract_info = loan_contractClass::getLoanContractDetailInfo($contract_id);
        $loan_contract['product_category_name'] = $contract_info->DATA['product_category_name'];
        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_id);
        $member_id = $member_info['uid'];
        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info", $client_info);
        Tpl::output("member_id", $member_id);

        $rt_2 = loan_contractClass::getContractLeftPayableInfo($contract_id);
        $payable_info = $rt_2->DATA;
        $loan_contract['total_payable_principal'] = $payable_info['total_payable_principal'];
        $loan_contract['total_payable_amount'] = $payable_info['total_payable_amount'];

        Tpl::output("contract", $loan_contract);
        Tpl::output("show_menu", "index");
        Tpl::showPage("contract.index");
    }

    /**
     * 提前还款展示历史状态
     */
    public function getPrepaymentOp()
    {
        Tpl::output('show_menu', 'index');

        $contract_id = intval($_GET['contract_id']);
        $contract_info = loan_contractClass::getLoanContractDetailInfo($contract_id);
        if (!$contract_info->STS) {
            showMessage($contract_info->MSG);
        }
        $contract_info = $contract_info->DATA;
        Tpl::output('contract_info', $contract_info);

        $member_info = loan_contractClass::getLoanContractMemberInfo($contract_id);
        $member_id = $member_info['uid'];
        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info", $client_info);
        Tpl::output("member_id", $member_id);


        $prepayment_request = loan_contractClass::getContractLastPrepaymentRequest($contract_id);
        if ($prepayment_request['state'] == prepaymentApplyStateEnum::APPROVED) {//审核通过 重新计算提前还款额
            $prepayment_preview = loan_contractClass::prepaymentPreview(array(
                    'contract_id' => $prepayment_request['contract_id'],
                    'prepayment_type' => $prepayment_request['prepayment_type'],
                    'amount' => $prepayment_request['apply_principal_amount'],
                    'repay_period' => $prepayment_request['repay_period'],
                    'deadline_date' => $prepayment_request['deadline_date'],
                )
            );
            if (!$prepayment_preview->STS) {
                showMessage($prepayment_preview->MSG);
            }
            $prepayment_request['total_payable_amount'] = $prepayment_preview->DATA['total_prepayment_amount'];
        }

        $is_contract = false;
        $contract_state = $contract_info['contract_info']['state'];
        if (loan_contractClass::loanContractIsUnderExecuting($contract_info['contract_info'])) {
            $is_contract = true;
        }

        Tpl::output('contract_state', $contract_state);
        Tpl::output('is_contract', $is_contract);
        Tpl::output('prepayment_request', $prepayment_request);
        Tpl::showPage('prepayment.state');
    }

    /**
     * 获取提前还款历史记录
     */
    public function getPrepaymentListOp($p)
    {
        $cashier_id = $this->user_id;
        $r = new ormReader();
        $sql = "SELECT bmp.*,cm.login_code FROM biz_member_prepayment bmp INNER JOIN client_member cm ON bmp.member_id = cm.uid WHERE bmp.state = 100 AND bmp.cashier_id=" . $cashier_id;
        $sql .= " ORDER BY bmp.uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        if ($rows) {
            $r = new ormReader();
            foreach ($rows as $key => $row) {
                $sql_2 = "SELECT bmpd.currency, bmpd.amount FROM biz_member_prepayment bmp LEFT JOIN biz_member_prepayment_detail bmpd ON bmp.uid = bmpd.biz_id WHERE bmp.uid = " . $row['uid'];
                $currency_amount = $r->getRows($sql_2);
                $currency_amount = resetArrayKey($currency_amount, 'currency');
                $row['USD'] = $currency_amount['USD'];
                $row['KHR'] = $currency_amount['KHR'];
                $rows[$key] = $row;
            }
        }
        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "current_user" => $this->user_id
        );

    }

    /**
     * 申请提前还款
     */
    public function prepaymentApplyOp()
    {
        Tpl::output('show_menu', 'index');
        $contract_id = intval($_GET['contract_id']);

        $contract_info = loan_contractClass::getLoanContractDetailInfo($contract_id);
        if (!$contract_info->STS) {
            showMessage($contract_info->MSG);
        }
        Tpl::output('contract_info', $contract_info->DATA);

        $client_info = loan_contractClass::getLoanContractMemberInfo($contract_id);
        Tpl::output('client_info', $client_info);
        Tpl::output("member_id", $client_info['uid']);

        $prepayment_info = loan_contractClass::getContractPrepaymentDetailInfo($contract_id);
        Tpl::output('prepayment_info', $prepayment_info->DATA);
        Tpl::showPage('prepayment.apply');
    }

    /**
     * 确认提前还款申请
     */
    public function confirmPrepaymentApplyOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $contract_id = intval($params['contract_id']);
        $prepayment_type = intval($params['prepayment_type']);
        $contract_info = loan_contractClass::getLoanContractDetailInfo($contract_id);
        if (!$contract_info->STS) {
            showMessage($contract_info->MSG);
        }
        Tpl::output('contract_info', $contract_info->DATA);

        $client_info = loan_contractClass::getLoanContractMemberInfo($contract_id);
        Tpl::output('client_info', $client_info);
        Tpl::output("member_id", $client_info['uid']);
        Tpl::output("contract_id", $contract_id);


        $prepayment_preview = loan_contractClass::prepaymentPreview($params);
        if (!$prepayment_preview->STS) {
            showMessage($prepayment_preview->MSG);
        }
        Tpl::output('prepayment_type', $prepayment_type);
        Tpl::output('prepayment_preview', $prepayment_preview->DATA);
        Tpl::output('show_menu', 'index');
        Tpl::showPage('prepayment.apply.confirm');
    }

    /**
     * 提交申请
     */
    public function submitPrepaymentApplyOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $rt = loan_contractClass::prepaymentApply($params);
        if (!$rt->STS) {
            showMessage($rt->MSG, getUrl('member_loan', 'getPrepayment', array('contract_id' => $params['contract_id']), false, ENTRY_COUNTER_SITE_URL));
        }
        showMessage('Add successful', getUrl('member_loan', 'getPrepayment', array('contract_id' => $params['contract_id']), false, ENTRY_COUNTER_SITE_URL));

    }


    /**
     * 提前还款
     */
    public function submitPrepaymentOp()
    {
        $user_id = $this->user_id;
        $apply_id = intval($_GET['apply_id']);
        $m_loan_prepayment_apply = M('loan_prepayment_apply');
        $apply_info = $m_loan_prepayment_apply->find(array('uid' => $apply_id, 'state' => prepaymentApplyStateEnum::APPROVED));
        if (!$apply_info) {
            showMessage('No Approved Apply');
        } else {
            //审核通过 重新计算提前还款额
            $prepayment_preview = loan_contractClass::prepaymentPreview(array(
                    'contract_id' => $apply_info['contract_id'],
                    'prepayment_type' => $apply_info['prepayment_type'],
                    'amount' => $apply_info['apply_principal_amount'],
                    'repay_period' => $apply_info['repay_period'],
                    'deadline_date' => $apply_info['deadline_date'],
                )
            );
            if (!$prepayment_preview->STS) {
                showMessage($prepayment_preview->MSG);
            }
            $apply_info['total_payable_amount'] = $prepayment_preview->DATA['total_prepayment_amount'];
            $apply_info['payable_principal'] = $prepayment_preview->DATA['total_paid_principal'];
            $apply_info['payable_interest'] = $prepayment_preview->DATA['total_paid_interest'];
            $apply_info['payable_operation_fee'] = $prepayment_preview->DATA['total_paid_operation_fee'];
        }
        $contract_id = $apply_info['contract_id'];
        $client_info = loan_contractClass::getLoanContractMemberInfo($contract_id);
        $member_id = $client_info['uid'];
        $contract_info = loan_contractClass::getLoanContractDetailInfo($contract_id);
        Tpl::output("member_id", $member_id);
        Tpl::output("client_info", $client_info);

        $currency = $contract_info->DATA['currency'];
        $amount = $apply_info['total_payable_amount'];
        $currency_list = (new currencyEnum())->Dictionary();
        foreach ($currency_list as $key => $value) {
            $exchange_rate = global_settingClass::getCurrencyRateBetween($currency, $key);
            if ($exchange_rate <= 0) {
                return new result(false, 'Not set currency exchange rate:' . $currency . '-' . $key, null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
            }
            $currency_list[$key] = round($exchange_rate * $amount, 2);
        }
        $biz_prepayment = new bizMemberPrepaymentClass(bizSceneEnum::COUNTER);
        $rt = $biz_prepayment->bizStart($user_id, $member_id, $apply_id);
        if ($rt->STS) {
            $exchange_list = $this->getExchangeRateUsdAndKhr(1);
            $prepayment_info = array(
                "data" => $rt->DATA,
                "exchange_amount" => $currency_list,
                "apply_id" => $apply_id,
                'member_code' => $client_info['login_code'],
                'currency' => $currency,
                'contract_sn' => $contract_info->DATA['contract_sn'],
                'contract_id' => $contract_info->DATA['contract_id'],
                'apply_info' => $apply_info,
                "exchange_list" => $exchange_list,
            );
        } else {
            showMessage($rt->MSG);
        }
        Tpl::output('show_menu', 'index');
        Tpl::output('prepayment_info', $prepayment_info);
        Tpl::showPage('prepayment.submit');

    }

    /**
     * 确认收款信息
     */
    public function receivePrepaymentOp()
    {
        $contract_id = trim($_POST['contract_id']);
        $biz_id = intval($_POST['biz_id']);
        $repayment_way = trim($_POST['repayment_way']);
        $default_amount = round($_POST['default_amount'], 2);
        $default_currency = trim($_POST['default_currency']);
        $default_currency_amount = array(
            $default_currency => $default_amount
        );
        $currency_amount = array();
        $currency_list = (new currencyEnum())->Dictionary();
        foreach ($currency_list as $key => $currency) {
            $currency_amount[$key] = $_POST[strtolower($key) . '_amount'];
        }
        $client_info = loan_contractClass::getLoanContractMemberInfo($contract_id);
        $contract_info = loan_contractClass::getLoanContractDetailInfo($contract_id);
        $biz_prepayment = new bizMemberPrepaymentClass(bizSceneEnum::COUNTER);

        $member_scene_image = objectMemberClass::getNewestSceneImage($client_info['uid']);
        Tpl::output('member_scene_image', $member_scene_image);

        if ($repayment_way == repaymentWayEnum::PASSBOOK) {
            $confirm_info = $biz_prepayment->confirmPrepayment($biz_id, $repayment_way, $default_currency_amount);
            if (!$confirm_info->STS) {
                showMessage($confirm_info->MSG);
            }
        }

        if ($repayment_way == repaymentWayEnum::CASH) {
            $confirm_info = $biz_prepayment->confirmPrepayment($biz_id, $repayment_way, $currency_amount);
            if (!$confirm_info->STS) {
                showMessage($confirm_info->MSG);
            }
        }

        $is_ct_check = $biz_prepayment->isNeedCTApprove($biz_id);
        Tpl::output('is_ct_check', $is_ct_check);
        Tpl::output('client_info', $client_info);
        Tpl::output("member_id", $client_info['uid']);
        Tpl::output('contract_info', $contract_info->DATA);
        Tpl::output("contract_id", $contract_id);
        Tpl::output('confirm_info', $confirm_info->DATA);
        Tpl::output('currency_amount', $currency_amount);
        Tpl::output('default_currency_amount', $default_currency_amount);
        Tpl::output('show_menu', 'loan');
        Tpl::showPage('prepayment.confirm');
    }

    /**
     * 检查密码提交
     */
    public function confirmPrepaymentOp($p)
    {
        $biz_id = intval($p['biz_id']);
        $cashier_card_no = trim($p['cashier_card_no']);
        $cashier_key = trim($p['cashier_key']);
        $client_trade_pwd = trim($p['client_trade_pwd']);
        $ct_card_no = trim($p['chief_teller_card_no']);
        $ct_key = trim($p['chief_teller_key']);
        $member_image = $p['member_image'];

        //验证密码
        $class_member_prepayment = new bizMemberPrepaymentClass(bizSceneEnum::COUNTER);

        /*$rt_1 = $class_member_prepayment->checkMemberTradingPassword($biz_id, $client_trade_pwd);
        if (!$rt_1->STS) {
            return new result(false, $rt_1->MSG);
        }*/

        $rt_2 = $class_member_prepayment->checkTellerPassword($biz_id, $cashier_card_no, $cashier_key);
        if (!$rt_2->STS) {
            return new result(false, $rt_2->MSG);
        }

        if ($class_member_prepayment->isNeedCTApprove($biz_id)) {
            $rt_3 = $class_member_prepayment->checkChiefTellerPassword($biz_id, $ct_card_no, $ct_key);
            if (!$rt_3->STS) {
                return $rt_3;
            }
        }

        $rt = $class_member_prepayment->insertMemberInfo($biz_id, $member_image);
        if (!$rt->STS) {
            return $rt;
        }

        $rt_4 = $class_member_prepayment->bizSubmit($biz_id);
        if (!$rt_4->STS) {
            return new result(false, $rt_4->MSG);
        }

        $biz_info = $class_member_prepayment->getBizDetailById($biz_id);
        $m_loan_prepayment_apply = new loan_prepayment_applyModel();
        $rt_5 = $m_loan_prepayment_apply->updateApplyState($biz_info['apply_id'], prepaymentApplyStateEnum::SUCCESS, $this->user_id);
        if (!$rt_5->STS) {
            return $rt_5;
        }

        return new result(true, 'Prepayment Successful!');

    }

    /**
     * 获取合同还款列表
     * @param $p
     * @return array
     */
    public function getContractInstallmentSchemeOp($p)
    {
        $uid = intval($p['uid']);
        $rt_1 = loan_contractClass::getLoanContractDetailInfo($uid);
        if (!$rt_1->STS) {
            return array(
                "sts" => false
            );
        }
        $data = $rt_1->DATA;
        return array(
            "sts" => true,
            "data" => $data['loan_installment_scheme'],
        );
    }

    /**
     * 获取合同已还历史
     * @param $p
     * @return array
     */
    public function getContractRepaymentHistoryOp($p)
    {
        $uid = intval($p['uid']);
        $m_loan_installment_scheme = new loan_installment_schemeModel();
        $list = $m_loan_installment_scheme->select(array('contract_id' => $uid, 'state' => 100));
        return array(
            "sts" => true,
            "data" => $list,
        );
    }

    public function getContractBillPayListOp($p)
    {
        $uid = intval($p['uid']);
        $r = new ormReader();
        $sql = "SELECT * FROM loan_contract_billpay_code  WHERE contract_id = " . $uid;
        $list = $r->getRows($sql);
        return array(
            "sts" => true,
            "data" => $list
        );
    }

    /**
     * one time product
     */
    public function loanOneTimeIndexOp()
    {
        $member_id = $_GET['member_id'];
        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info", $client_info);
        Tpl::output("member_id", $member_id);

        $biz_One_Time_Credit_Loan = new bizOneTimeCreditLoanClass(bizSceneEnum::COUNTER);
        $memberOneTimeLoanList = $biz_One_Time_Credit_Loan->getMemberOneTimeLoanList($member_id);

        Tpl::output("memberOneTimeLoanList", $memberOneTimeLoanList);
        $credit_category = loan_categoryClass::getMemberCreditCategoryList($member_id);
        //输出利息类型(软硬)
        foreach ($credit_category as $k => $prod) {
            $interest_type = loan_categoryClass::getCategoryInterestTypeByGrant($prod['uid']);
            $credit_category[$k]["interest_type"] = $interest_type;
        }
        Tpl::output("credit_category", $credit_category);


        $memberOneTimeTaskList = $biz_One_Time_Credit_Loan->getTaskList($member_id, 1);
        Tpl::output("memberOneTimeTaskList", $memberOneTimeTaskList);

        Tpl::showPage("loan.one.time.index");
    }

    public function getOneTimeContractStateListOp($p)
    {
        $member_id = intval($p['member_id']);
        $state = intval($p['state']);
        $biz_One_Time_Credit_Loan = new bizOneTimeCreditLoanClass(bizSceneEnum::COUNTER);
        $memberOneTimeTaskList = $biz_One_Time_Credit_Loan->getTaskList($member_id, $state);
        Tpl::output('memberOneTimeTaskList', $memberOneTimeTaskList);
        Tpl::output('state', $state);

    }

    public function cancelOneTimeLoanTaskOp($p)
    {
        $biz_id = $p['biz_id'];
        $bizClass = new bizOneTimeCreditLoanClass(bizSceneEnum::COUNTER);
        $rt = $bizClass->disburseCancel($biz_id);
        return $rt;
    }

    public function submitOneTimeLoanApplyOp($p)
    {
        $member_id = intval($p['member_id']);
        $category_id = intval($p['category_id']);
        $currency = $p['currency'];

        $user_id = $this->user_id;
        $biz_One_Time_Credit_Loan = new bizOneTimeCreditLoanClass(bizSceneEnum::COUNTER);
        $rt = $biz_One_Time_Credit_Loan->insertLoanTask($user_id, $member_id, $category_id, $currency);
        if ($rt->STS) {
            return new result(true, 'Apply successful');
        } else {
            return new result(true, $rt->MSG);
        }
    }

    public function getAddOneTimeContractListOp($p)
    {
        $sql = "SELECT lc.*,lc.uid contract_id,mcc.alias FROM biz_one_time_credit_loan botcl
            INNER JOIN loan_contract lc ON botcl.contract_id = lc.uid
            INNER JOIN member_credit_category mcc ON lc.member_credit_category_id = mcc.uid
            WHERE botcl.cashier_id = " . $this->user_id . " AND botcl.state = 100";
        $sql .= " ORDER BY botcl.update_time DESC";
        $r = new ormReader();
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    public function oneTimeLoanDisburseOp()
    {
        $biz_id = intval($_GET['biz_id']);
        $biz_one_time_credit_loan = new bizOneTimeCreditLoanClass(bizSceneEnum::COUNTER);
        $rt = $biz_one_time_credit_loan->disburseStart($biz_id);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }

        $data = $rt->DATA;

        $biz_id = $data['biz_id'];

        // 检查是否需要CT 验证
        $ct_check = $biz_one_time_credit_loan->isNeedCTApprove($biz_id);
        Tpl::output('is_ct_check', $ct_check);

        Tpl::output('contract_detail_data',$data);


        $m_member = M('client_member');
        $member_id = $data['member_info']['uid'];
        $member_info = $m_member->find(array('uid' => $member_id));
        $member_scene_image = objectMemberClass::getNewestSceneImage($member_id);
        Tpl::output('member_scene_image', $member_scene_image);
        Tpl::output('member_info', $member_info);

        Tpl::output('biz_id', $data['biz_id']);
        Tpl::output('contract_id', $data['contract_id']);

        Tpl::output('loan_installment_scheme', $data['loan_installment_scheme']);
        Tpl::output('show_menu', 'loanOneTimeIndex');
        Tpl::output("member_id", $member_id);
        Tpl::output('contract_info', $data['contract_info']);

        Tpl::showPage('loan.one.time.add.confirm');

    }

    public function addOneTimeContractSubmitOp($p)
    {
        $biz_id = intval($p['biz_id']);
        $cashier_card_no = trim($p['cashier_card_no']);
        $cashier_key = trim($p['cashier_key']);
        $client_trade_pwd = trim($p['client_trade_pwd']);
        $card_no = trim($p['chief_teller_card_no']);
        $key = trim($p['chief_teller_key']);
        $member_image = $p['add_contract_image'];

        $biz_one_time_credit_loan = new bizOneTimeCreditLoanClass(bizSceneEnum::COUNTER);

        $update_arr = array();
        // 先完成所有的验证（第二步）

        //验证密码

        $rt_1 = $biz_one_time_credit_loan->checkMemberTradingPassword($biz_id, $client_trade_pwd);
        if (!$rt_1->STS) {
            return new result(false, "Client Password Error:" . $rt_1->MSG);
        }
        $update_arr = array_merge($update_arr, (array)$rt_1->DATA);

        $rt_2 = $biz_one_time_credit_loan->checkTellerPassword($biz_id, $cashier_card_no, $cashier_key);
        if (!$rt_2->STS) {
            return new result(false, 'Teller Trading Password Error:' . $rt_2->MSG);
        }

        $update_arr = array_merge($update_arr, (array)$rt_2->DATA);

        if ($biz_one_time_credit_loan->isNeedCTApprove($biz_id)) {
            $rt_3 = $biz_one_time_credit_loan->checkChiefTellerPassword($biz_id, $card_no, $key);
            if (!$rt_3->STS) {
                return new result(false, 'Chief teller password error:' . $rt_3->MSG);
            }
            $update_arr = array_merge($update_arr, (array)$rt_3->DATA);
        }

        $up = $biz_one_time_credit_loan->updateCheckInfo($biz_id, $update_arr);
        if (!$up->STS) {
            return $up;
        }

        $rt = $biz_one_time_credit_loan->insertMemberInfo($biz_id, $member_image);
        if (!$rt->STS) {
            return $rt;
        }

        $rt = $biz_one_time_credit_loan->disburseConfirm($biz_id);
        if (!$rt->STS) {
            return $rt;
        }
        return new result(true, 'Create Successful!');

    }


    public function oneTimeContractCancelOp($p)
    {
        $biz_id = intval($p['biz_id']);
        $biz_one_time_credit_loan = new bizOneTimeCreditLoanClass(bizSceneEnum::COUNTER);
        $rt = $biz_one_time_credit_loan->disburseCancel($biz_id);
        if (!$rt->STS) {
            return $rt;
        }
        return new result(true, 'Cancel Successful!');
    }

    public function showOneTimeLoanDisburseOp()
    {
        $biz_id = intval($_GET['biz_id']);
        $m_biz_one_time_credit_loan = M('biz_one_time_credit_loan');
        $one_time_credit_loan = $m_biz_one_time_credit_loan->find(array('uid' => $biz_id));
        if (!$one_time_credit_loan) {
            showMessage('Invalid Id.');
        }

        $member_id = $one_time_credit_loan['member_id'];
        $m_member = M('client_member');
        $member_info = $m_member->find(array('uid' => $member_id));
        Tpl::output('member_info', $member_info);

        $rt = loan_contractClass::getLoanContractDetailInfo($one_time_credit_loan['contract_id']);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }
        $data = $rt->DATA;

        Tpl::output('first_repay', reset($data['loan_installment_scheme']));

        Tpl::output('due_date', $data['due_date']);
        Tpl::output('due_date_type', $data['due_date_type_val']);


        $total_repay = array(
            'total_repayment' => $data['total_repayment'],
            'total_loan' => $data['loan_amount'],
            'total_interest' => $data['total_interest'],
            'total_admin_fee' => $data['total_admin_fee'],
            'total_loan_fee' => $data['total_loan_fee'],
            'total_operation_fee' => $data['total_operation_fee'],
            'total_insurance_fee' => $data['total_insurance_fee'],
            'actual_receive_amount' => $data['actual_receive_amount'],
        );
        Tpl::output('total_repay', $total_repay);

        Tpl::output('loan_installment_scheme', $data['loan_installment_scheme']);
        Tpl::output('show_menu', 'loanOneTimeIndex');
        Tpl::output("member_id", $member_id);
        Tpl::output('contract_info', $data['contract_info']);
        Tpl::output('contract_id', $one_time_credit_loan['contract_id']);

        Tpl::showPage('loan.one.time.contract.detail');

    }

}