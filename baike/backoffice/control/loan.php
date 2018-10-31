<?php

class loanControl extends back_office_baseControl
{

    public function __construct()
    {
        parent::__construct();
        Language::read('enum,loan,certification');
        Tpl::setLayout("empty_layout");
        Tpl::setDir("loan");
    }

    public function contractOp()
    {
        Tpl::showPage("contract");
    }

    public function getInsurancePrice($uid = 0)
    {
        $r = new ormReader();
        $sql1 = "select loan_contract_id,sum(price) as price from insurance_contract GROUP BY loan_contract_id";
        if ($uid) {
            $sql1 = "select loan_contract_id,sum(price) as price from insurance_contract where loan_contract_id = " . $uid . " GROUP BY loan_contract_id";
        }
        $insurance = $r->getRows($sql1);
        $insurance_arr = array();
        foreach ($insurance as $key => $value) {
            $insurance_arr[$value['loan_contract_id']] = $value;
        }
        return $insurance_arr;
    }

    public function getContractListOp($p)
    {
        $r = new ormReader();
        $sql = "SELECT contract.*,account.obj_guid,member.uid as member_id,member.display_name,member.alias_name,member.phone_id,member.email FROM loan_contract as contract"
            . " inner join loan_account as account on contract.account_id = account.uid"
            . " left join client_member as member on account.obj_guid = member.obj_guid where contract.state != -1 ";
        if ($p['item']) {
            $sql .= " and contract.contract_sn = '" . $p['item'] . "'";
        }
        if ($p['name']) {
            $sql .= ' and member.display_name like "%' . $p['name'] . '%"';
        }
        if ($p['date']) {
            $sql .= ' and contract.start_date > "' . $p['date'] . '"';
        }
        if ($p['state'] > -1) {
            $sql .= " and contract.state = " . $p['state'];
        }
        $sql .= " ORDER BY contract.create_time desc";
        $insurance_arr = $this->getInsurancePrice();

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $insurance = $insurance_arr;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        $sql1 = "select count(uid) as count from loan_contract where state = " . loanContractStateEnum::WRITE_OFF;
        $count_write_off = $r->getRow($sql1);
        $sql2 = "select count(uid) as count from loan_contract where state != " . loanContractStateEnum::WRITE_OFF . " and state != " . loanContractStateEnum::COMPLETE;
        $count_in = $r->getRow($sql2);
        return array(
            "sts" => true,
            "data" => $rows,
            "insurance" => $insurance_arr,
            "total" => $total,
            "count_write_off" => $count_write_off['count'],
            "count_in" => $count_in['count'],
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
        Tpl::showPage("contract.list");
    }

    public function contractDetailOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $r = new ormReader();
        $uid = intval($p['uid']);

        $sql = "SELECT contract.*,account.obj_guid,product.uid product_id,product.product_code,product.product_name,product.product_description,product.product_feature,member.uid as member_id,member.login_code,member.display_name,member.alias_name,member.phone_id,member.email FROM loan_contract as contract"
            . " inner join loan_account as account on contract.account_id = account.uid"
            . " left join client_member as member on account.obj_guid = member.obj_guid"
            . " left join loan_product as product on contract.product_id = product.uid where contract.uid = " . $uid;
        $data = $r->getRow($sql);
        if (!$data) {
            showMessage('No contract!');
        }

        $contract_id = $data['uid'];

        // 待还金额
        $re = loan_contractClass::getContractLeftPayableInfo($contract_id);
        $payable_info = $re->DATA;
        $data['left_principal'] = $payable_info['total_payable_amount'];
        Tpl::output("detail", $data);

        $sql1 = "select * from loan_disbursement_scheme where contract_id = " . $uid;
        $disbursement = $r->getRows($sql1);
        Tpl::output("disbursement", $disbursement);


//        $sql3 = "SELECT * FROM loan_deducting_penalties WHERE contract_id = $uid AND state <= " . loanDeductingPenaltiesState::PROCESSING;
//        $deducting_penalties = $r->getRow($sql3);
//        if ($deducting_penalties) {
//            Tpl::output("is_deducting_penalties", false);
//        } else {
//            Tpl::output("is_deducting_penalties", true);
//        }

        $sql2 = "select * from loan_installment_scheme where state!='" . schemaStateTypeEnum::CANCEL . "' and  contract_id = " . $data['uid'];
        $installment = $r->getRows($sql2);
        $penalties_total = 0;
        $time = date('Y-m-d 23:59:59', time());
        $repayment_arr = array();
        foreach ($installment as $key => $val) {
            if ($val['penalty_start_date'] <= $time && $val['state'] != schemaStateTypeEnum::COMPLETE) {
                $penalties = loan_baseClass::calculateSchemaRepaymentPenalties($val['uid']);
                $val['penalties'] = $penalties;
//                $val['amount'] += $penalties;
                $penalties_total += $penalties;
                $installment[$key] = $val;
            }
            if ($val['receivable_date'] <= $time && $val['state'] != schemaStateTypeEnum::COMPLETE) {
                $repayment_arr[] = $val;
            }
        }
        $sql = "SELECT * FROM loan_repayment WHERE contract_id = " . $uid . " AND state = " . loanRepaymentStateEnum::SUCCESS;
        $repayment_history = $r->getRows($sql);
        Tpl::output("repayment_history", $repayment_history);

        Tpl::output("penalties_total", $penalties_total);
        Tpl::output("installment", $installment);
        $insurance_arr = $this->getInsurancePrice($p['uid']);
        Tpl::output("insurance", $insurance_arr);
        Tpl::output("repayment_arr", $repayment_arr);

        Tpl::output("source", $_GET['source']);

        Tpl::showPage("contract.detail");
    }

    /**
     * 主产品列表页
     */
    public function productOp()
    {
        $r = new ormReader();
        $sql = "select * from loan_product ";
        $product_list = $r->getRows($sql);
        $m_loan_product = new loan_productModel();
        foreach ($product_list as $key => $product) {

            // 获取产品的子产品
            $sub_products = $m_loan_product->getAllSubProductOfMainProductId($product['uid']);
            foreach ($sub_products as $k => $v) {
                $v['product_loan_summary'] = statisticsClass::getSubProductLoanSummary($v['uid']);
                $sub_products[$k] = $v;
            }
            $product_list[$key]['sub_products'] = $sub_products;

        }

        Tpl::output("product_list", $product_list);
        Tpl::showPage("product.list");
    }


    public function mainProductAddSubProductOp()
    {
        $params = array_merge($_GET, $_POST);
        $main_product_id = $params['main_id'];
        $m_product = new loan_productModel();
        $product_info = $m_product->getMainProductInfoById($main_product_id);
        if (!$product_info) {
            showMessage('No product info!');
        }

        // 利率类型
        $interest_type = (new interestPaymentEnum())->toArray();
        foreach ($interest_type as $key => $v) {
            if ($v == interestPaymentEnum::SINGLE_REPAYMENT) {
                unset($interest_type[$key]);
            }
        }
        Tpl::output('interest_type', $interest_type);

        $repayment_frequency = array(
            interestRatePeriodEnum::MONTHLY
        );
        Tpl::output('repayment_frequency', $repayment_frequency);

        Tpl::output('main_product_info', $product_info);
        Tpl::output('sub_product_info', null);
        Tpl::showPage("product.add.sub.product");
    }


    public function addSubProductSubmitOp($params)
    {
        $params['user_id'] = $this->user_id;
        $params['user_name'] = $this->user_name;
        return loan_productClass::addNewSubProductOfBaseInfo($params);
    }

    public function editSubProductSubmitOp($params)
    {
        $params['user_id'] = $this->user_id;
        $params['user_name'] = $this->user_name;
        return loan_productClass::editNewSubProductOfBaseInfo($params);
    }

    public function editSubProductOp()
    {
        $params = array_merge($_GET, $_POST);
        $sub_id = $params['uid'];
        $m_sub_product = new loan_sub_productModel();
        $sub_product = $m_sub_product->find(array(
            'uid' => $sub_id
        ));
        if (!$sub_product) {
            showMessage('No product info :' . $sub_id);
        }

        // 利率类型
        $interest_type = (new interestPaymentEnum())->toArray();
        foreach ($interest_type as $key => $v) {
            if ($v == interestPaymentEnum::SINGLE_REPAYMENT) {
                unset($interest_type[$key]);
            }
        }
        Tpl::output('interest_type', $interest_type);

        $repayment_frequency = array(
            interestRatePeriodEnum::MONTHLY
        );
        Tpl::output('repayment_frequency', $repayment_frequency);


        $m_product = new loan_productModel();
        $main_product = $m_product->getRow($sub_product['product_id']);
        Tpl::output('main_product_info', $main_product);
        Tpl::output('sub_product_info', $sub_product);
        Tpl::showPage("product.add.sub.product");

    }

    /**
     * 系列历史版本
     */
    public function showProductHistoryOp()
    {
        $uid = intval($_REQUEST['uid']);
        $m_sub_product = new loan_sub_productModel();
        $row = $m_sub_product->getRow($uid);
        if (!$row) {
            showMessage('Invalid Id!');
        }
        $product_history = $m_sub_product->orderBy('uid DESC')->select(array('product_key' => $row['product_key']));
        foreach ($product_history as $key => $product) {
            $product_contract = $this->getProductContractById($product['uid']);
            $product_history[$key]['loan_contract'] = $product_contract['loan_count'] ?: 0;
            $product_history[$key]['loan_client'] = $product_contract['loan_client'] ?: 0;
            $product_history[$key]['loan_ceiling'] = $product_contract['loan_ceiling'] ?: 0;
            $product_history[$key]['loan_balance'] = $product_contract['loan_balance'] ?: 0;
        }
        Tpl::output("product_history", $product_history);
        Tpl::showPage("product.history");
    }

    /**
     * 获取产品合同信息
     * @param $product_id
     * @return ormDataRow
     */
    private function getProductContractById($product_id)
    {
        $r = new ormReader();
        $sql = "SELECT COUNT(*) loan_count,SUM(apply_amount) loan_ceiling,sum(receivable_principal+receivable_interest+receivable_operation_fee+receivable_annual_fee+receivable_penalty-loss_principal-loss_interest-loss_operation_fee-loss_annual_fee-loss_penalty) receivable FROM loan_contract WHERE product_id = " . $product_id;
        $product_contract = $r->getRow($sql);
        $sql = "SELECT SUM(lr.amount) repayment FROM loan_repayment AS lr INNER JOIN loan_contract AS lc ON lr.contract_id = lc.uid WHERE lr.state = 100 AND lc.product_id = " . $product_id;
        $repayment = $r->getOne($sql);
        $loan_balance = $product_contract['receivable'] - $repayment;
        $sql = "SELECT COUNT(member.uid) loan_client FROM loan_contract AS contract"
            . " INNER JOIN loan_account AS account ON contract.account_id = account.uid"
            . " INNER JOIN client_member AS member ON account.obj_guid = member.obj_guid WHERE contract.product_id = " . $product_id . " GROUP BY member.uid";
        $loan_client = $r->getOne($sql);
        $product_contract['loan_balance'] = $loan_balance;
        $product_contract['loan_client'] = $loan_client;
        return $product_contract;
    }

    /**
     * 展示产品信息
     */
    public function showProductOp()
    {

        $params = array_merge($_GET, $_POST);
        $sub_id = $params['uid'];
        $m_sub_product = new loan_sub_productModel();
        $sub_product = $m_sub_product->find(array(
            'uid' => $sub_id
        ));
        if (!$sub_product) {
            showMessage('No product info :' . $sub_id);
        }
        $m_product = new loan_productModel();
        $main_product = $m_product->getRow($sub_product['product_id']);
        Tpl::output('main_product_info', $main_product);
        Tpl::output('sub_product_info', $sub_product);

        Tpl::output("is_edit", isset($_GET['is_edit']) ? $_GET['is_edit'] : true);
        Tpl::showPage('product.info');
    }

    /**
     * 添加页
     */
    public function addProductOp()
    {
        $currency_list = C('currency');
        Tpl::output("currency_list", $currency_list);

        $penalty_on = (new penaltyOnEnum())->Dictionary();
        $interest_payment = (new interestPaymentEnum())->Dictionary();
        $interest_rate_period = (new interestRatePeriodEnum())->Dictionary();

        Tpl::output("interest_payment", $interest_payment);
        Tpl::output("penalty_on", $penalty_on);
        Tpl::output("interest_rate_period", $interest_rate_period);

        $m_core_definition = M('core_definition');
        $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type', 'guarantee_type'));
        Tpl::output("mortgage_type", $define_arr['mortgage_type']);
        Tpl::output("guarantee_type", $define_arr['guarantee_type']);
        Tpl::showPage('main.product.add');
    }

    /**
     * 编辑产品
     */
    public function editProductOp()
    {
        $uid = intval($_REQUEST['uid']);
        $class_product = new loan_productClass();
        $rt = $class_product->getMainProductInfoById($uid);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }
        Tpl::output('product_info', $rt->DATA);

        $m_core_definition = M('core_definition');
        $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type', 'guarantee_type'));
        Tpl::output("mortgage_type", $define_arr['mortgage_type']);
        Tpl::output("guarantee_type", $define_arr['guarantee_type']);
        Tpl::showPage('main.product.add');
    }

    /**
     * 保存产品主要信息
     * @param $p
     * @return result
     */
    public function insertProductMainOp($p)
    {
        $p['creator_id'] = $this->user_id;
        $p['creator_name'] = $this->user_name;
        $class_product = new loan_productClass();
        $rt = $class_product->insertProductMain($p);
        return $rt;
    }

    /**
     * 更新产品主要信息
     * @param $p
     * @return result
     */
    public function updateProductMainOp($p)
    {
        $class_product = new loan_productClass();
        $rt = $class_product->updateProductMain($p);
        return $rt;
    }

    /**
     * 保存罚金信息
     * @param $p
     * @return result
     */
    public function updateProductPenaltyOp($p)
    {
        $class_product = new loan_productClass();
        $rt = $class_product->updateProductPenalty($p);
        return $rt;
    }

    /**
     * 利率列表
     * @param $p
     * @return array
     */
    public function getSizeRateListOp($p)
    {
        $class_product = new loan_productClass();
        $rt = $class_product->getSizeRateList($p);

        $m_core_definition = M('core_definition');
        $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type', 'guarantee_type'));
        $rt['mortgage_type'] = $define_arr['mortgage_type'];
        $rt['guarantee_type'] = $define_arr['guarantee_type'];
        $rt['type'] = $p['type'];
        return $rt;
    }

    /**
     * 保存size利率
     * @param $p
     * @return result
     */
    public function insertSizeRateOp($p)
    {
        $class_product = new loan_productClass();
        $rt = $class_product->insertSizeRate($p);
        return $rt;
    }

    /**
     * 更新size利率
     * @param $p
     * @return result
     */
    public function updateSizeRateOp($p)
    {
        $class_product = new loan_productClass();
        $rt = $class_product->updateSizeRate($p);
        return $rt;
    }

    /**
     * 保存size利率
     * @param $p
     * @return result
     */
    public function removeSizeRateOp($p)
    {
        $class_product = new loan_productClass();
        $rt = $class_product->removeSizeRate($p);
        return $rt;
    }

    /**
     * 更新贷款条件
     * @param $p
     * @return result
     */
    public function updateMainProductConditionOp($p)
    {
        $class_product = new loan_productClass();
        $rt = $class_product->updateMainProductCondition($p);
        return $rt;
    }

    /**
     * 更新详情
     * @param $p
     * @return result
     */
    public function updateMainProductDescriptionOp($p)
    {
        $class_product = new loan_productClass();
        $rt = $class_product->updateMainProductDescription($p);
        return $rt;
    }


    public function releaseMainProductOp($p)
    {
        $product_id = $p['uid'];
        $m_loan_product = new loan_productModel();
        $product_info = $m_loan_product->getRow($product_id);
        if (!$product_info) {
            return new result(false, 'No product info-' . $product_id);
        }
        $product_info->state = loanProductStateEnum::ACTIVE;
        $product_info->update_time = Now();
        $up = $product_info->update();
        if (!$up->STS) {
            return new result(false, 'Release fail:' . $up->MSG);
        }
        return new result(true, 'Release Successful!');
    }

    /**
     * 发布产品
     * @param $p
     * @return result
     */
    public function releaseProductOp($p)
    {
        $uid = intval($p['uid']);
        $class_product = new loan_productClass();
        $rt = $class_product->changeProductState($uid, loanProductStateEnum::ACTIVE);
        if ($rt->STS) {
            return new result(true, 'Release Successful!');
        } else {
            return new result(false, 'Release Failure!');
        }
    }


    public function unShelveMainProductOp($p)
    {
        $product_id = $p['uid'];
        $m_loan_product = new loan_productModel();
        $product_info = $m_loan_product->getRow($product_id);
        if (!$product_info) {
            return new result(false, 'No product info-' . $product_id);
        }
        $product_info->state = loanProductStateEnum::INACTIVE;
        $product_info->update_time = Now();
        $up = $product_info->update();
        if (!$up->STS) {
            return new result(false, 'Release fail:' . $up->MSG);
        }
        return new result(true, 'Inactive Successful!');
    }

    /**
     * 产品下架
     * @param $p
     * @return result
     */
    public function unShelveProductOp($p)
    {
        $uid = intval($p['uid']);
        $class_product = new loan_productClass();
        $rt = $class_product->changeProductState($uid, loanProductStateEnum::INACTIVE);
        if ($rt->STS) {
            return new result(true, 'Inactive Successful!');
        } else {
            return new result(false, 'Inactive Failure!');
        }
    }


    public function sizeRateLoanPreviewOp()
    {
        $param = array_merge(array(), $_GET, $_POST);
        $size_rate_id = $param['size_rate_id'];
        if (!$size_rate_id) {
            showMessage('Invalid size rate id:' . $size_rate_id);
        }
        $m_size_rate = new loan_product_size_rateModel();
        $size_rate = $m_size_rate->find(array(
            'uid' => $size_rate_id
        ));
        if (!$size_rate) {
            showMessage('Invalid size rate id:' . $size_rate_id);
        }


        $rt = loan_baseClass::loanPreviewBySizeRateInfo($size_rate);
        if (!$rt->STS) {
            showMessage('Calculate fail:' . $rt->MSG);
        }

        $preview_info = $rt->DATA;
        //print_r($preview_info);

        Tpl::output('sub_product', $preview_info['product_info']);
        Tpl::output('size_rate', $size_rate);
        Tpl::output('preview_info', $preview_info);
        Tpl::showPage('size.rate.loan.preview');
    }


    public function loanFeeSettingOp()
    {
        $category_id = intval($_GET['category_id']);
        $m = new loan_categoryModel();
        if ($category_id == 0) {
            $category_info = array('category_name' => 'Default Setting');
        } else {
            $category_info = $m->find(array("uid" => $category_id));
            if (!$category_info) {
                showMessage("Invalid Credit-Category");
            }
        }
        Tpl::output("category_info", $category_info);

        $m = new loan_fee_settingModel();
        $list = $m->getSettingListOfCategoryId($category_id);
        if (empty($list) && $category_id > 0) {
            $rt = $m->copyDefaultSetting($category_id);
            if (!$rt->STS) {
                showMessage($rt->MSG);
            }
            $list = $m->getSettingListOfCategoryId($category_id);
        }

        Tpl::output('list', $list);

        $category_list = loan_categoryClass::getAllCategoryList();
        Tpl::output("category_list", $category_list);
        Tpl::output("category_id", $category_id);

        Tpl::showpage('loan.fee.setting');
    }

    public function editLoanFeeSettingOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $uid = $params['uid'];
        $m = new loan_fee_settingModel();
        if ($params['form_submit'] == 'ok') {
            $rt = $m->updateInfo($params);
            if (!$rt->STS) {
                showMessage('Save fail:' . $rt->MSG);
            }
            showMessage('Save success.', getUrl('loan', 'loanFeeSetting', array("category_id"=>$params['category_id']?:0), false, BACK_OFFICE_SITE_URL));
            die;
        }

        $info = array();
        if ($uid) {
            $info = $m->getInfoById($uid);
            $category_id = $info['category_id'];
        } else {
            $category_id = intval($params['category_id']);
        }

        $m = new loan_categoryModel();
        if ($category_id == 0) {
            $category_info = array('uid' => 0, 'category_name' => 'Default Setting');
        } else {
            $category_info = $m->find(array("uid" => $category_id));
            if (!$category_info) {
                showMessage("Invalid Credit-Category");
            }
        }
        Tpl::output("category_info", $category_info);

        Tpl::output('setting_info', $info);
        Tpl::showpage('loan.fee.setting.edit');

    }

    public function deleteLoanFeeSettingOp($p)
    {
        $uid = $p['uid'];
        $m = new loan_fee_settingModel();
        return $m->deleteInfoById($uid);
    }

    /**
     * 逾期合同
     */
    public function overdueOp()
    {
        Tpl::showPage('contract.overdue');
    }

    /**
     * @param $p
     * @return array
     */
    public function getOverdueListOp($p)
    {
        $r = new ormReader();
        $sql = "SELECT lis.contract_id,SUM(lis.amount) amount,COUNT(lis.uid) num ,MIN(lis.receivable_date) receivable_date" .
            " FROM loan_installment_scheme lis WHERE lis.state = 0 AND lis.receivable_date < '" . Now() . "' GROUP BY lis.contract_id ORDER BY lis.receivable_date DESC";

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $contract_ids = array_column($rows, 'contract_id');
            $contract_id_str = '(' . implode(',', $contract_ids) . ')';
            $sql = "SELECT lc.uid,lc.contract_sn,cm.display_name,cm.phone_id" .
                " FROM loan_contract lc LEFT JOIN loan_account la ON lc.account_id = la.uid" .
                " LEFT JOIN client_member cm ON la.obj_guid = cm.obj_guid" .
                " WHERE lc.uid IN $contract_id_str";
            $arr = $r->getRows($sql);
            $arr = resetArrayKey($arr, 'uid');
            foreach ($rows as $key => $row) {
                $contract_id = $row['contract_id'];
                $row = array_merge($row, $arr[$contract_id]);
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
        );
    }

    /**
     * 特殊利率,暂时只提供产品包的特殊情况
     */
    public function specialRateOp()
    {

        $size_rate_id = intval($_GET['size_rate_id']);
        $m_size_rate = new loan_product_size_rateModel();
        $size_rate_row = $m_size_rate->find(array("uid" => $size_rate_id));
        Tpl::output("size_rate", $size_rate_row);
        $product_id = $size_rate_row['product_id'];
        $m_sub_product = new loan_sub_productModel();
        $product_row = $m_sub_product->find(array("uid" => $product_id));
        Tpl::output("sub_product", $product_row);

        $class_product = new loan_productClass();
        $special_rate_list = $class_product->getSpecialRateList($size_rate_id);
        $special_rate_list = resetArrayKey($special_rate_list, "special_grade");
        Tpl::output('special_rate_list', $special_rate_list);
        Tpl::output('product_id', $product_id);
        Tpl::output('size_rate_id', $size_rate_id);

        $package_list = $class_product->getProductPackageList();
        Tpl::output("package_list", $package_list);
        Tpl::showpage('special.rate');
    }

    /**
     * 提交特殊利率设置，目前只处理special_type=0(package)的情况，以后也容易扩展
     */
    public function submitSpecialRateSettingOp()
    {
        $p = $_POST;
        $package_id = $p['package_id'];
        if (!count($package_id)) {
            showMessage("Nothing can be saved");
        }

        $p['operator_id'] = $this->user_id;
        $p['operator_name'] = $this->user_name;
        $ret = loan_productClass::saveSpecialLoanRateOfPackage($p);
        showMessage($ret->MSG, getUrl('loan', 'specialRate', array("size_rate_id" => $p['size_rate_id']), false, BACK_OFFICE_SITE_URL));
    }


    /**
     * 增加特殊利率
     * @param $p
     * @return result
     */
    /*
    public function insertSpecialSizeRateOp($p)
    {
        $class_product = new loan_productClass();
        if (!trim($p['client_type']) && !trim($p['client_grade'])) {
            return new result(false, 'Please set member grade or member type first!');
        }
        $rt = $class_product->insertSpecialSizeRate($p);
        if ($rt->STS) {
            $data = $rt->DATA;
            $url = getUrl('loan', 'specialRate', array('size_rate_id' => $data['size_rate_id'], 'product_id' => $data['product_id']), false, BACK_OFFICE_SITE_URL);
            $rt->DATA = array('url' => $url);
            return $rt;
        } else {
            return $rt;
        }
    }
    */

    /**
     * 编辑特殊利率
     * @param $p
     * @return result
     */
    /*
    public function updateSpecialSizeRateOp($p)
    {
        $class_product = new loan_productClass();
        if (!trim($p['client_type']) && !trim($p['client_grade'])) {
            return new result(false, 'Please set member grade or member type first!');
        }
        $rt = $class_product->updateSpecialSizeRate($p);
        if ($rt->STS) {
            $data = $rt->DATA;
            $url = getUrl('loan', 'specialRate', array('size_rate_id' => $data['size_rate_id'], 'product_id' => $data['product_id']), false, BACK_OFFICE_SITE_URL);
            $rt->DATA = array('url' => $url);
            return $rt;
        } else {
            return $rt;
        }
    }
*/
    /**
     * 移除特殊利率
     * @param $p
     * @return result
     */
    /*
    public function removeSpecialSizeRateOp($p)
    {
        $class_product = new loan_productClass();
        $rt = $class_product->removeSpecialSizeRate($p);
        if ($rt->STS) {
            $data = $rt->DATA;
            $url = getUrl('loan', 'specialRate', array('size_rate_id' => $data['size_rate_id'], 'product_id' => $data['product_id']), false, BACK_OFFICE_SITE_URL);
            $rt->DATA = array('url' => $url);
            return $rt;
        } else {
            return $rt;
        }
    }
    */

    /**
     * 还款请求
     */
    public function requestToRepaymentOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);

        $request_state = (new requestRepaymentStateEnum())->Dictionary();

        Tpl::output('request_state', $request_state);
        Tpl::showPage('request.repayment');

    }

    /**
     * 获取还款申请列表
     * @param $p
     * @return array
     */
    public function getRequestRepaymentListOpOld($p)
    {
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $type = trim($p['type']);

        $r = new ormReader();
        $sql = "SELECT lrr.*  FROM loan_request_repayment lrr"
            //  . " INNER JOIN loan_contract lc ON lrr.contract_id = lc.uid"
            //  . " LEFT JOIN loan_installment_scheme lis ON lrr.scheme_id = lis.uid"
            . " WHERE (lrr.create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        if ($p['state'] >= 0) {
            $sql .= " AND lrr.state = " . intval($p['state']);
        }
        if ($type) {
            $sql .= " AND lrr.type = '" . $type . "'";
        }
        if (trim($p['search_text'])) {
            $sql .= " AND lc.contract_sn like '%" . trim($p['search_text']) . "%'";
        }
        $sql .= " ORDER BY lrr.create_time DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
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
            "cur_uid" => $this->user_id,
        );
    }


    /**
     * 获取还款申请列表
     * @param $p
     * @return array
     */
    public function getRequestRepaymentListOp($p)
    {
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $type = trim($p['type']);

        $r = new ormReader();
        $sql = "SELECT lr.*,lc.contract_sn,lc.repayment_type,lis.receivable_principal,lis.receivable_interest,lis.receivable_operation_fee,lis.receivable_date,lis.amount r_amount,lis.actual_payment_amount FROM loan_repayment lr" . " INNER JOIN loan_contract lc ON lr.contract_id = lc.uid" . " LEFT JOIN loan_installment_scheme lis ON lr.scheme_id = lis.uid" . " WHERE (lr.create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";

        $sql1 = "SELECT lc.currency,sum(lr.amount) amount  FROM loan_repayment lr" . " INNER JOIN loan_contract lc ON lr.contract_id = lc.uid" . " LEFT JOIN loan_installment_scheme lis ON lr.scheme_id = lis.uid" . " WHERE (lr.create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";

        if ($p['state'] >= 0) {
            $sql .= " AND lr.state = " . intval($p['state']);
            $sql1 .= " AND lr.state = " . intval($p['state']);
        }
        if ($type == 'schema') {
            $sql .= " AND lr.scheme_id > 0";
            $sql1 .= " AND lr.scheme_id > 0";
        } elseif ($type == 'prepayment') {
            $sql .= " AND lr.scheme_id = 0";
            $sql1 .= " AND lr.scheme_id = 0";
        }
        if (trim($p['search_text'])) {
            $sql .= " AND lc.contract_sn like '%" . trim($p['search_text']) . "%'";
            $sql1 .= " AND lc.contract_sn like '%" . trim($p['search_text']) . "%'";
        }
        $sql .= " ORDER BY lr.create_time DESC";
        $sql1 .= " group by lc.currency ORDER BY lr.create_time DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        $count = $r->getRows($sql1);
        $count_new = array();
        foreach ($count as $k => $v) {
            $count_new[$v['currency']] = $v['amount'];
        }
        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "cur_uid" => $this->user_id,
            "count" => $count_new,
        );
    }

    /**
     * 审核还款申请
     */
    public function auditRequestRepaymentOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $m_loan_request_repayment = M('loan_request_repayment');
        $row = $m_loan_request_repayment->getRow(array('uid' => $uid));
        if (!$row) {
            showMessage('Invalid Id!');
        }

        //审核中
        if ($row->state == requestRepaymentStateEnum::PROCESSING) {
            // 超时放开，让别人可审 1小时
            if ((strtotime($row['update_time']) + 3600) < time()) {
                $row->state = requestRepaymentStateEnum::PROCESSING;
                $row->handler_id = $this->user_id;
                $row->handler_name = $this->user_name;
                $row->update_time = Now();
                $up = $row->update();
                if (!$up->STS) {
                    showMessage($up->MSG);
                }
            }
        } elseif ($row->state == requestRepaymentStateEnum::CREATE) {
            $row->state = requestRepaymentStateEnum::PROCESSING;
            $row->handler_id = $this->user_id;
            $row->handler_name = $this->user_name;
            $row->update_time = Now();
            $rt = $row->update();
            if (!$rt->STS) {
                showMessage($rt->MSG);
            }
        }
        $lock = false;
        if ($this->user_id != $row['handler_id']) {
            //审核中
            $lock = true;
        }
        Tpl::output('lock', $lock);
        $r = new ormReader();
        $sql = "SELECT lrr.*,lc.contract_sn"
            . " FROM loan_request_repayment lrr"
            . " INNER JOIN loan_contract lc ON lrr.contract_id = lc.uid"
            . " WHERE lrr.uid =" . $uid;
        $detail = $r->getRow($sql);
        Tpl::output('detail', $detail);

        $repayment_type_lang = enum_langClass::getPaymentTypeLang();
        Tpl::output('repayment_type_lang', $repayment_type_lang);

        if ($row['type'] == 'schema') {

            $rt = loan_contractClass::getRepaymentSchemaByAmount($detail['contract_id'], $detail['amount'], $row['currency']);
            if (!$rt->STS) {
                showMessage($rt->MSG);
            }
            $repayment_schema = $rt->DATA;
            $expired_schema = array();
            $unexpired_schema = array();

            $today_start = strtotime(date('Y-m-d 00:00:00', time()));
            foreach ($repayment_schema['repayment_schema'] as $schema) {
                $receivable_date = strtotime(date('Y-m-d 00:00:00', strtotime($schema['receivable_date'])));
                if ($receivable_date <= $today_start) {
                    $expired_schema[] = $schema;
                } else {
                    $unexpired_schema[] = $schema;
                }
            }

            Tpl::output('expired_schema', $expired_schema);
            Tpl::output('unexpired_schema', $unexpired_schema);
            Tpl::showpage('request.repayment.audit');
        } else {
            $prepayment_apply_id = $row['prepayment_apply_id'];
            $m_loan_prepayment_apply = M('loan_prepayment_apply');
            $prepayment_apply = $m_loan_prepayment_apply->find(array('uid' => $prepayment_apply_id));
            Tpl::output('prepayment_apply', $prepayment_apply);
            Tpl::showpage('request.prepayment.handle');
        }
    }

    /**
     * 确定还款到账
     * @param $p
     * @return result
     */
    public function auditRepaymentOp($p)
    {
        $uid = intval($p['uid']);
        $type = $p['type'];
        $remark = trim($p['remark']);

        $payer_name = trim($p['payer_name']);
        $payer_type = trim($p['payer_type']);
        $payer_account = trim($p['payer_account']);
        $payer_phone = trim($p['payer_phone']);
        $bank_name = trim($p['bank_name']);
        $bank_account_name = trim($p['bank_account_name']);
        $bank_account_no = trim($p['bank_account_no']);

        $m_loan_request_repayment = M('loan_request_repayment');
        $row = $m_loan_request_repayment->getRow($uid);
        if (!$row) {
            return new result(false, 'No request info');
        }

        if ($payer_name) $row->payer_name = $payer_name;
        if ($payer_type) $row->payer_type = $payer_type;
        if ($payer_account) $row->payer_account = $payer_account;
        if ($payer_phone) $row->payer_phone = $payer_phone;
        if ($bank_name) $row->bank_name = $bank_name;
        if ($bank_account_name) $row->bank_account_name = $bank_account_name;
        if ($bank_account_no) $row->bank_account_no = $bank_account_no;

        $row->handler_id = $this->user_id;
        $row->handler_name = $this->user_name;
        $row->handle_remark = $remark;
        $row->handle_time = Now();

        if ($type == 'offline_failure') {
            $row->state = requestRepaymentStateEnum::FAILED;
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                return new result(false, 'Handle failure!');
            }
            return new result(true, 'Handle successful!');
        } else {
            $row->state = requestRepaymentStateEnum::PROCESSING;
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                return new result(false, 'Handle failure!');
            }

            $request_id = $row->uid;
            if ($p['received_date']) {
                $received_date = date('Y-m-d H:i:s', strtotime($p['received_date']));
            } else {
                $received_date = date('Y-m-d H:i:s');
            }

            $handler_info = array(
                'handler_id' => $this->user_id,
                'handler_name' => $this->user_name,
                'handle_remark' => $remark,
                'handle_time' => Now()
            );

            $re = loan_contractClass::requestRepaymentConfirmReceived($request_id, array(), $handler_info);
            if (!$re->STS) {
                return new result(false, $re->MSG);
            }
            return $re;
        }
    }

    /**
     * 查看提前还款申请情况
     */
    public function viewRequestRepaymentOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $r = new ormReader();
        $sql = "SELECT lrr.*,lc.contract_sn"
            . " FROM loan_prepayment_apply lrr"
            . " INNER JOIN loan_contract lc ON lrr.contract_id = lc.uid"
            . " WHERE lrr.uid =" . $uid;
        $detail = $r->getRow($sql);
        if (!$detail) {
            showMessage('Invalid Id!');
        }
        Tpl::output('detail', $detail);
        Tpl::showpage('request.prepayment.view');
    }

    /**
     * 查看还款明细
     */
    public function viewRequestRepaymentDetailOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $r = new ormReader();
        $sql = "select r.*,c.contract_sn from loan_request_repayment r left join loan_contract c on r.contract_id=c.uid where r.uid='$uid' ";
        $detail = $r->getRow($sql);
        $payment_type_lang = enum_langClass::getPaymentTypeLang();
        Tpl::output('detail', $detail);
        Tpl::output('payment_type_lang', $payment_type_lang);
        Tpl::showpage('request.repayment.view');
    }

    /**
     * @param $p
     * @return result
     */
    public function modifyPenaltiesOp($p)
    {
        $uid = intval($p['uid']);
        $r = new ormReader();

        $sql = "SELECT * FROM loan_contract WHERE uid = $uid AND state > " . loanContractStateEnum::PENDING_APPROVAL;
        $loan_contract = $r->getRow($sql);
        if (!$loan_contract) {
            return array("sts" => false);
        }

        $sql3 = "SELECT * FROM loan_deducting_penalties WHERE contract_id = $uid AND state <= " . loanDeductingPenaltiesState::PROCESSING;
        $deducting_penalties = $r->getRow($sql3);

        $sql = "SELECT * FROM loan_installment_scheme WHERE contract_id = $uid AND state < " . schemaStateTypeEnum::COMPLETE . " AND penalty_start_date < '" . Now() . "'";
        $scheme_list = $r->getRows($sql);
        $penalties_total = 0;
        $deduction_total = 0;
        foreach ($scheme_list as $key => $scheme) {
            $penalties = loan_baseClass::calculateSchemaRepaymentPenalties($scheme['uid']);
            $scheme['penalties'] = $penalties;
            $penalties_total += $penalties;
            $deduction_total += $scheme['deduction_penalty'];
            $scheme_list[$key] = $scheme;
        }

        return array(
            "sts" => true,
            "loan_contract" => $loan_contract,
            "deducting_penalties" => $deducting_penalties,
            "penalties_total" => $penalties_total,
            "deduction_total" => $deduction_total,
            "data" => $scheme_list
        );
    }

    /**
     * 更新详情
     * @param $p
     * @return result
     */
    public function updateSubDescriptionOp($p)
    {
        $class_product = new loan_productClass();
        $rt = $class_product->updateSubProductDescription($p);
        return $rt;
    }

    /**
     * 保存减免罚息申请
     * @param $p
     * @return result
     */
    public function savePenaltiesApplyOp($p)
    {
        $contract_id = intval($p['uid']);
        $deducting_penalties = round($p['deducting_penalties'], 2);
        $remark = $p['remark'];
        $r = new ormReader();
        $sql = "SELECT * FROM loan_contract WHERE uid = $contract_id AND state > " . loanContractStateEnum::PENDING_APPROVAL;
        $loan_contract = $r->getRow($sql);
        if (!$loan_contract) {
            return new result(false, 'Invalid Id!');
        }

        $sql3 = "SELECT * FROM loan_deducting_penalties WHERE contract_id = $contract_id AND state <= " . loanDeductingPenaltiesState::PROCESSING;
        $deducting_penalties_apply = $r->getRow($sql3);
        if ($deducting_penalties_apply) {
            return new result(false, 'There has been an unaudited application!');
        }

        $sql = "SELECT * FROM loan_installment_scheme WHERE contract_id = $contract_id AND state < " . schemaStateTypeEnum::COMPLETE . " AND penalty_start_date < '" . Now() . "'";
        $scheme_list = $r->getRows($sql);
        $penalties_total = 0;
        foreach ($scheme_list as $key => $scheme) {
            $penalties = loan_baseClass::calculateSchemaRepaymentPenalties($scheme['uid']);
            $penalties_total += $penalties;
        }

        if ($deducting_penalties > $penalties_total) {
            return new result(false, 'It can\'t be greater than penalties total!');
        }

        $m_loan_deducting_penalties = M('loan_deducting_penalties');
        $row = $m_loan_deducting_penalties->newRow();
        $row->contract_id = $contract_id;
        $row->deducting_penalties = $deducting_penalties;
        $row->type = 1;
        $row->remark = $remark;
        $row->state = loanDeductingPenaltiesState::CREATE;
        $row->creator_id = $this->user_id;
        $row->creator_name = $this->user_name;
        $row->creator_name = $this->user_name;
        $row->create_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Add Successful!');
        } else {
            return new result(false, 'Add Failure!');
        }
    }

    /**
     * 减免罚息
     */
    public function deductingPenaltiesOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage('deducting_penalties');
    }

    /**
     * @param $p
     * @return array
     */
    public function getDeductingPenaltiesListOp($p)
    {
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $r = new ormReader();
        $sql = "SELECT ldp.*,lc.contract_sn FROM loan_deducting_penalties ldp"
            . " INNER JOIN loan_contract lc ON ldp.contract_id = lc.uid"
            . " WHERE (ldp.create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        if (trim($p['search_text'])) {
            $sql .= " AND lc.contract_sn like '%" . trim($p['search_text']) . "%'";
        }
        $sql .= " ORDER BY ldp.create_time DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
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
     * 审核减免申请
     */
    public function showAuditPenaltiesOp()
    {
        $uid = intval($_GET['uid']);
        $m_loan_deducting_penalties = M('loan_deducting_penalties');
        $deducting_penalties = $m_loan_deducting_penalties->find(array('uid' => $uid));
        if (!$deducting_penalties || $deducting_penalties['state'] != loanDeductingPenaltiesState::CREATE) {
            showMessage('Invalid Id!');
        }

        $r = new ormReader();
        $contract_id = intval($deducting_penalties['contract_id']);

        $sql = "SELECT lc.*,cm.display_name,cm.login_code  FROM loan_contract lc LEFT JOIN loan_account la ON lc.account_id = la.uid"
            . " LEFT JOIN client_member cm ON la.obj_guid = cm.obj_guid"
            . " WHERE lc.uid = $contract_id AND lc.state > " . loanContractStateEnum::PENDING_APPROVAL;

        $loan_contract = $r->getRow($sql);
        if (!$loan_contract) {
            showMessage('Invalid Id!');
        }

        $sql = "SELECT * FROM loan_installment_scheme WHERE contract_id = $contract_id AND state < " . schemaStateTypeEnum::COMPLETE . " AND penalty_start_date < '" . Now() . "'";
        $scheme_list = $r->getRows($sql);
        $penalties_total = 0;
        $deduction_total = 0;
        foreach ($scheme_list as $key => $scheme) {
            $penalties = loan_baseClass::calculateSchemaRepaymentPenalties($scheme['uid']);
            $scheme['penalties'] = $penalties;
            $penalties_total += $penalties;
            $deduction_total += $scheme['deduction_penalty'];
            $scheme_list[$key] = $scheme;
        }

        $loan_contract['penalties_total'] = $penalties_total;
        $loan_contract['deduction_total'] = $deduction_total;
        Tpl::output('deducting_penalties', $deducting_penalties);
        Tpl::output('loan_contract', $loan_contract);
        Tpl::output('scheme_list', $scheme_list);
        Tpl::showpage('audit.penalties');
    }

    /**
     * 审核
     * @param $p
     * @return result
     */
    public function auditPenaltiesOp($p)
    {
        $uid = intval($p['uid']);
        $type = $p['type'];
        $r = new ormReader();
        $m_loan_deducting_penalties = M('loan_deducting_penalties');
        $m_loan_installment_scheme = M('loan_installment_scheme');

        $deducting_penalties_row = $m_loan_deducting_penalties->getRow(array('uid' => $uid));
        if (!$deducting_penalties_row || $deducting_penalties_row['state'] != loanDeductingPenaltiesState::CREATE) {
            return new result(false, 'Invalid Id!');
        }

        if ($type == 'disapprove') {
            $deducting_penalties_row->state = loanDeductingPenaltiesState::DISAPPROVE;
            $deducting_penalties_row->auditor_id = $this->user_id;
            $deducting_penalties_row->auditor_id = $this->user_name;
            $deducting_penalties_row->audit_time = Now();
            $rt = $deducting_penalties_row->update();
            if ($rt->STS) {
                return new result(true, 'Audit Successful!');
            } else {
                return new result(true, 'Audit Failure!');
            }
        } else {
            $conn = ormYo::Conn();
            $conn->startTransaction();

            try {
                $deducting_penalties_row->state = loanDeductingPenaltiesState::USED;
                $deducting_penalties_row->auditor_id = $this->user_id;
                $deducting_penalties_row->auditor_name = $this->user_name;
                $deducting_penalties_row->audit_time = Now();
                $rt_1 = $deducting_penalties_row->update();
                if (!$rt_1->STS) {
                    $conn->rollback();
                    return new result(true, 'Audit Failure!');
                }

                $deducting_penalties = $deducting_penalties_row['deducting_penalties'];
                $contract_id = $deducting_penalties_row['contract_id'];

                $sql = "SELECT * FROM loan_installment_scheme WHERE contract_id = $contract_id AND state!=" . schemaStateTypeEnum::CANCEL . " AND state <  " . schemaStateTypeEnum::COMPLETE . " AND penalty_start_date < '" . Now() . "'";
                $scheme_list = $r->getRows($sql);
                if (!$scheme_list) {
                    $conn->rollback();
                    return new result(true, 'Invalid Id!');
                }
                foreach ($scheme_list as $scheme) {
                    $penalties = loan_baseClass::calculateSchemaRepaymentPenalties($scheme['uid']);
                    if ($penalties <= 0) continue;
                    if ($penalties > $deducting_penalties) {
                        $deduction_penalty = $deducting_penalties + $scheme['deduction_penalty'];
                        $rt = $m_loan_installment_scheme->update(array('uid' => $scheme['uid'], 'deduction_penalty' => $deduction_penalty));
                        if (!$rt->STS) {
                            $conn->rollback();
                            return new result(true, 'Audit Failure!');
                        } else {
                            break;
                        }
                    } else {
                        $deducting_penalties -= $penalties;
                        $deduction_penalty = $penalties + $scheme['deduction_penalty'];
                        $update = array(
                            'uid' => $scheme['uid'],
                            'deduction_penalty' => $deduction_penalty
                        );
                        if ($scheme['actual_payment_amount'] >= $scheme['amount']) {
                            $update['state'] = schemaStateTypeEnum::COMPLETE;
                            $update['done_time'] = Now();
                        }

                        $rt = $m_loan_installment_scheme->update($update);
                        if (!$rt->STS) {
                            $conn->rollback();
                            return new result(true, 'Audit Failure!');
                        }
                    }
                }

                $rt_2 = loan_contractClass::updateContractStateAfterRepayment($contract_id);
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return $rt_2;
                }

                $conn->submitTransaction();
                return new result(true, 'Audit Successful!');
            } catch (Exception $ex) {
                $conn->rollback();
                return new result(false, $ex->getMessage());
            }
        }
    }

    /**
     * 确认还款
     * @param $p
     * @return result
     */
    public function submitRepaymentOp($p)
    {

        $uid = intval($p['uid']);
        $repayment_total = round($p['repayment_total'], 2);
        $remark = trim($p['remark']);
        $currency = $p['currency'] ? $p['currency'] : currencyEnum::USD;

        $class_user = new userClass();
        $user_info = $class_user->getUserInfo($this->user_id);

        $payment_info = array(
            'branch_id' => $user_info->DATA['branch_id'],
            'teller_id' => $this->user_id,
            'teller_name' => $this->user_name,
            'creator_id' => $this->user_id,
            'creator_name' => $this->user_name,
            'remark' => $remark
        );

        try {
            $conn = ormYo::Conn();
            $conn->startTransaction();
            $rt = loan_contractClass::schemaManualRepaymentByCash($uid, $repayment_total, $currency, $user_info->DATA['branch_id'], $this->user_id, $this->user_name);
            if ($rt->STS) {
                $conn->submitTransaction();
                return new result(true, 'Repayment successful!');
            } else {
                $conn->rollback();
                return new result(false, 'Repayment failure!');
            }

        } catch (Exception $e) {
            return new result(false, 'Repayment failure!' . $e->getMessage());
        }
    }

    public function prepaymentLimitOp()
    {
        //$m_loan_repayment_limit = M('loan_repayment_limit');
        //$list = $m_loan_repayment_limit->getrepaymentLimitList();
        $limit_keys = global_settingClass::getLoanPrepaymentLimitKey();
        Tpl::output("limit_keys", $limit_keys);
        $list = global_settingClass::getLoanPrepaymentLimitSetting();
        //$list = global_settingClass::getLoanPrepaymentLimitSettingOfCategoryId(0);
        Tpl::output('list', $list);

        $category_list = loan_categoryClass::getAllCategoryList();
        Tpl::output("category_list", $category_list);
        Tpl::showpage('prepayment_limit');
    }

    public function editPrepaymentLimitOp($p)
    {
        $m = new loan_repayment_limitModel();
        $row = $m->getRow(array("loan_days" => $p['loan_days'], "category_id" => 0));
        if ($p['limit_days'] > $p['loan_days']) {
            return new result(false, "limit days can\'t than loan days.");
        }
        if (!$row) {
            $row = $m->newRow(array(
                "loan_days" => $p['loan_days'],
                "limit_days" => $p['limit_days'],
            ));
            $ret = $row->insert();
        } else {
            $row->limit_days = $p['limit_days'];
            $ret = $row->update();
        }

        if ($ret->STS) {
            $limit_keys = global_settingClass::getLoanPrepaymentLimitKey();
            if ($p['limit_days'] > 0) {
                $title = $limit_keys[$p['limit_days']];
            } else {
                $title = "No Limit";
            }
            return new result(true, "", array("title" => $title));
        } else {
            return $ret;
        }

    }


    public function productPackagePageOp()
    {
        $m = M("loan_product_package");
        $rows = $m->getAll();
        Tpl::output("list", $rows);
        Tpl::showPage("product.package.list");
    }

    public function addProductPackagePageOp()
    {
        $m = M("loan_product_package");
        $rows = $m->orderBy("package")->select("1=1");
        Tpl::output("package_list", $rows);
        Tpl::showPage("product.package.edit");
    }

    public function editProductPackagePageOp()
    {
        $uid = $_GET['package_id'];
        if (!$uid) {
            showMessage("Invalid Parameter:Package ID");
        }
        $m = M("loan_product_package");
        $row = $m->find(array("uid" => $uid));
        Tpl::output("package_item", $row);
        Tpl::showPage("product.package.edit");
    }

    public function submitProductPackageItemOp()
    {
        $param = $_POST;
        $param['creator_id'] = $this->user_id;
        $param['creator_name'] = $this->user_name;
        $param['create_time'] = Now();
        $param['update_time'] = Now();
        if ($param['uid'] > 0) {
            $ret = loan_productClass::editProductPackage($param);
        } else {
            $ret = loan_productClass::addProductPackage($param);
        }
        showMessage($ret->MSG ?: 'Success!', getUrl('loan', 'productPackagePage', array(), false, BACK_OFFICE_SITE_URL));
    }

    public function deleteProductPackageOp($args)
    {
        if (!$args['uid']) {
            return new result(false, "Invalid Parameter:Require To Input ID");
        }
        return loan_productClass::deleteProductPackage($args['uid']);
    }

    public function showPackageSizeRateOp()
    {
        $uid = $_GET['package_id'];
        $package_name = $_GET['package_name'];
        $arr = loan_productClass::getSizeRateByPackageIdGroupByProduct($uid);
        Tpl::output("list", $arr);
        Tpl::output("package_name", $package_name);
        Tpl::showPage("product.package.item.interest");
    }

    public function categoryOp()
    {
        $m = new loan_categoryModel();
        $rows = $m->getCategoryList(true);
        Tpl::output("list", $rows);
        Tpl::showPage("category.list");
    }

    public function editCategoryPageOp()
    {
        //输出可选的sub_product
        $sub_list = loan_productClass::getAllActiveSubProductList();
        $arr_sub = array();
        foreach ($sub_list as $item) {
            $arr_sub[] = array("sub_product_id" => $item['uid'], "sub_product_name" => $item['sub_product_name']);
        }
        Tpl::output("sub_list", $arr_sub);
        $code_list = (new loanCategoryCodeEnum())->Dictionary();
        Tpl::output("code_list", $code_list);
        //输出package-list
        $package_list = loan_productClass::getProductPackageList();
        Tpl::output("package_list", $package_list);

        $uid = $_GET['uid'];
        if ($uid) {
            $cur_item = (new loan_categoryModel())->getCategoryItem($uid);
            if ($cur_item) {
                $cur_item['category_lang'] = my_json_decode($cur_item['category_lang']);
            }
            Tpl::output("category_info", $cur_item);
        }
        Tpl::showPage("category.editor");
    }

    public function submitCategoryEditorOp()
    {
        $p = $_POST;


        //格式化$p
        if (!trim($p['category_name'])) {
            showMessage("Required to input category name");
        }
        if( !$p['product_code_usd'] || !$p['product_code_khr'] ){
            showMessage('Empty product code.');
        }
        $lang = array();
        foreach ($p['lang_key'] as $i => $lk) {
            $lang[$lk] = trim($p['category_lang'][$i]) ?: trim($p['category_name']);
        }
        $p['category_lang'] = my_json_encode($lang);

        $m = new loan_categoryModel();
        if (!$p['uid']) {
            // 检查code是否重复
            $usd_code = $m->getRow(array(
                'product_code_usd' => $p['product_code_usd']
            ));
            if( $usd_code ){
                showMessage('Product code exist:'.$p['product_code_usd']);
            }
            $khr_code = $m->getRow(array(
                'product_code_khr' => $p['product_code_khr']
            ));
            if( $khr_code ){
                showMessage('Product code exist:'.$p['product_code_khr']);
            }
            $row = $m->newRow($p);
            $row->creator_id = $this->user_id;
            $row->creator_name = $this->user_name;
            $row->create_time = Now();
            $row->update_time = Now();
            $row->update_operator_id = $this->user_id;
            $row->is_one_time = intval($p['is_one_time']);
            $row->interest_package_id = intval($p['interest_package_id']);
            $row->is_close = intval($p['is_close']);
            $row->product_code_usd = $p['product_code_usd'];
            $row->product_code_khr = $p['product_code_khr'];
            $row->max_contracts_per_client = intval($p['max_contracts_per_client'])?:null;
            $row->is_only_loan_by_app = intval($p['is_only_loan_by_app']);
            if( $p['special_cate_key'] ){
                $row->is_special = 1;
                $row->special_key = $p['special_cate_key'];
            }else{
                $row->is_special = 0;
                $row->special_key = null;
            }
            $ret = $row->insert();
        } else {
            $usd_code = $m->getRow(array(
                'uid' => array('neq',$p['uid']),
                'product_code_usd' => $p['product_code_usd']
            ));
            if( $usd_code ){
                showMessage('Product code exist:'.$p['product_code_usd']);
            }
            $khr_code = $m->getRow(array(
                'uid' => array('neq',$p['uid']),
                'product_code_khr' => $p['product_code_khr']
            ));
            if( $khr_code ){
                showMessage('Product code exist:'.$p['product_code_khr']);
            }
            $row = $m->getRow($p['uid']);
            $row->matchData($p);
            $row->is_one_time = intval($p['is_one_time']);
            $row->update_time = Now();
            $row->update_operator_id = $this->user_id;
            $row->interest_package_id = intval($p['interest_package_id']);
            $row->is_close = intval($p['is_close']);
            $row->product_code_usd = $p['product_code_usd'];
            $row->product_code_khr = $p['product_code_khr'];
            $row->max_contracts_per_client = intval($p['max_contracts_per_client'])?:null;
            $row->is_only_loan_by_app = intval($p['is_only_loan_by_app']);
            if( $p['special_cate_key'] ){
                $row->is_special = 1;
                $row->special_key = $p['special_cate_key'];
            }else{
                $row->is_special = 0;
                $row->special_key = null;
            }
            $ret = $row->update();
        }
        if ($ret->STS) {
            showMessage("Submit Success!", getUrl("loan", "category", array(), false, BACK_OFFICE_SITE_URL));
        } else {
            showMessage($ret->MSG);
        }
    }

    public function removeCreditCategoryOp($p)
    {
        $uid = $p['uid'];
        if (!$p['uid']) {
            return new result(false, "Invalid Parameters");
        }
        //判断有没有在member_credit_category
        $m = new member_credit_categoryModel();
        $chk = $m->find(array("category_id" => $uid));
        if ($chk) {
            return new result(false, "Not allowed to delete,this category has been used!");
        }
        $m = new loan_categoryModel();
        $row = $m->getRow($uid);
        if (!$row) {
            return new result(false, "Invalid Parameter,No category found!");
        }
        return $row->delete();
    }

    public function updateCategoryDescriptionOp($p)
    {
        $uid = intval($p['category_id']);
        $name = $p['name'];
        $val = $p['val'];

        $m = new loan_categoryModel();
        $row = $m->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Product!');
        }
        $row->$name = $val;
        $row->update_time = Now();
        $row->update_operator_id = $this->user_id;
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Update Successful!');
        } else {
            return new result(false, 'Update Failure!');
        }
    }

    public function editPackageSizeRateOp()
    {
        $uid = intval($_GET['package_id']);
        $package_name = $_GET['package_name'];
        $arr = loan_productClass::getSizeRateByPackageIdGroupByProduct($uid);
        Tpl::output("list", $arr);
        Tpl::output("package_name", $package_name);
        Tpl::output("special_grade", $uid);
        Tpl::showPage("product.package.item.interest.edit");
    }

    public function setSpecialRateStateOfClientOp($p)
    {
        $is_show_for_client = intval($p['is_show_for_client']);
        $p['fld_name']='is_show_for_client';
        $p['val']=$is_show_for_client;
        $p['user_id'] = $this->user_id;
        $p['user_name'] = $this->user_name;
        $rt = loan_productClass::savePackageSizeRate($p);
        return $rt;
    }
    public function setSpecialRateStateOfActiveOp($p)
    {
        $is_show_for_client = intval($p['is_active']);
        $p['fld_name']='is_active';
        $p['val']=$is_show_for_client;
        $p['user_id'] = $this->user_id;
        $p['user_name'] = $this->user_name;
        $rt = loan_productClass::savePackageSizeRate($p);
        return $rt;
    }

    public function savePackageSizeRateForItemOp($p)
    {
        $p['user_id'] = $this->user_id;
        $p['user_name'] = $this->user_name;
        $rt = loan_productClass::savePackageSizeRate($p);
        return $rt;
    }

    public function setPrepaymentLimitSpecialPageOp()
    {
        $category_id = $_GET['category_id']?:0;
        $m = new loan_categoryModel();
        $category_info = $m->find(array("uid" => $category_id));
        if (!$category_info) {
            showMessage("Invalid Credit-Category");
        }
        Tpl::output("category_info", $category_info);

        $limit_keys = global_settingClass::getLoanPrepaymentLimitKey();
        Tpl::output("limit_keys", $limit_keys);
        $list = global_settingClass::getLoanPrepaymentLimitSettingOfCategoryId($category_id);

        Tpl::output('list', $list);

        $category_list = loan_categoryClass::getAllCategoryList();
        Tpl::output("category_list", $category_list);
        Tpl::showpage('prepayment_limit_special');
    }

    public function editPrepaymentLimitSpecialOp($p)
    {
        $m = new loan_repayment_limitModel();
        $row = $m->getRow(array("loan_days" => $p['loan_days'], "category_id" => $p['category_id']));
        if ($p['limit_days'] > $p['loan_days']) {
            return new result(false, "limit days can\'t than loan days.");
        }
        if (!$row) {
            $row = $m->newRow(array(
                "category_id" => $p['category_id'],
                "loan_days" => $p['loan_days'],
                "limit_days" => $p['limit_days'],
            ));
            $ret = $row->insert();
        } else {
            $row->limit_days = $p['limit_days'];
            $ret = $row->update();
        }

        if ($ret->STS) {
            $limit_keys = global_settingClass::getLoanPrepaymentLimitKey();
            if ($p['limit_days'] > 0) {
                $title = $limit_keys[$p['limit_days']];
            } else {
                $title = "No Limit";
            }
            return new result(true, "", array("title" => $title));
        } else {
            return $ret;
        }
    }

}
