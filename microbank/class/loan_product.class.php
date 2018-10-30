<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/5
 * Time: 10:28
 */
class loan_productClass
{
    function __construct()
    {
    }

    /**
     * 获取产品列表
     * @return result
     */
    public function getValidProductList()
    {
        $m_loan_product = M('loan_product');
        $product_list = $m_loan_product->select(array('state' => loanProductStateEnum::ACTIVE));
        return $product_list;
    }

    /**
     * 获取可用子产品
     */
    public function getValidSubProductList()
    {
        $r = new ormReader();
        $sql = "SELECT lsp.*,lp.product_name FROM"
            . " loan_sub_product lsp INNER JOIN loan_product lp ON lsp.product_id = lp.uid"
            . " WHERE lsp.state = " . loanProductStateEnum::ACTIVE . " AND lp.state = " . loanProductStateEnum::ACTIVE
            . " ORDER BY lsp.product_id ASC";
        return $r->getRows($sql);
    }

    /**
     * 获取CO Member可用子产品
     */
    public function getCoMemberValidSubProductList($member_id)
    {
        $r = new ormReader();
        $sql = "SELECT lsp.*,lp.product_name,mlp.uid limit_uid FROM loan_sub_product lsp INNER JOIN loan_product lp ON lsp.product_id = lp.uid left join (select * from member_limit_loan_product where member_id = '$member_id' group by member_id,product_code) mlp on lsp.sub_product_code = mlp.product_code WHERE lsp.state = " . loanProductStateEnum::ACTIVE . " AND lp.state = " . loanProductStateEnum::ACTIVE . " ORDER BY lsp.uid ASC";
        return $r->getRows($sql);
    }

    /**
     * 获取产品详情
     * @param $uid
     * @return result
     */
    public function getMainProductInfoById($uid)
    {
        $uid = intval($uid);
        $m_loan_product = M('loan_product');
        $m_loan_product_condition = M('loan_product_condition');
        $product_info = $m_loan_product->find(array('uid' => $uid));
        if (!$product_info) {
            return new result(false, 'Invalid Id!');
        }
        $product_condition = $m_loan_product_condition->select(array('loan_product_id' => $uid));
        $product_info['condition'] = $product_condition;
        return new result(true, '', $product_info);
    }


    public static function addNewSubProductOfBaseInfo($params)
    {

        $m_product = new loan_productModel();
        $main_product_id = $params['main_product_id'];
        $main_product = $m_product->getRow($main_product_id);
        if (!$main_product) {
            return new result(false, 'No main product info.', null, errorCodesEnum::NO_DATA);
        }
        $product_name = $params['product_name'];
        $product_code = trim($params['product_code']);
        $interest_type = $params['interest_type'];
        $repayment_type = $params['repayment_type'];
        $is_full_interest_prepayment = intval($params['is_full_interest_prepayment']) ? 1 : 0;
        $is_approved_prepayment_request = intval($params['is_approved_prepayment_request']) ? 1 : 0;
        $is_only_for_counter = intval($params['is_only_for_counter']) ? 1 : 0;
        $max_contracts_per_client = intval($params['max_contracts_per_client']) ?: null;


        if (!$product_code || !$product_name || !$interest_type || !$repayment_type) {
            return new result(false, 'Invalid param.', null, errorCodesEnum::INVALID_PARAM);
        }
        //判断product-code是否已经存在
        $m_sub_product = new loan_sub_productModel();
        $chk_code = $m_sub_product->find(array("sub_product_code" => $product_code));
        if ($chk_code) {
            return new result(false, "Invalid Product-Code, Already Exist!");
        }

        $product_key = md5($product_name . $product_code . time());


        $sub_product = $m_sub_product->newRow();
        $sub_product->product_id = $main_product_id;
        $sub_product->sub_product_code = $product_code;
        $sub_product->sub_product_name = $product_name;
        $sub_product->sub_summary = trim($params['sub_summary']);
        $sub_product->repayment_type = $repayment_type;
        $sub_product->interest_type = $interest_type;
        $sub_product->is_full_interest_prepayment = $is_full_interest_prepayment;
        $sub_product->is_approved_prepayment_request = $is_approved_prepayment_request;
        $sub_product->is_advance_interest = intval($params['is_advance_interest']) ?: 0;
        $sub_product->is_only_for_counter = $is_only_for_counter;
        $sub_product->max_contracts_per_client = $max_contracts_per_client;
        $sub_product->product_key = $product_key;
        $sub_product->state = loanProductStateEnum::TEMP;
        $sub_product->creator_id = $params['user_id'];
        $sub_product->creator_name = $params['user_name'];
        $sub_product->create_time = Now();
        $sub_product->update_time = Now();
        $insert = $sub_product->insert();
        if (!$insert->STS) {
            return new result(false, 'Add product fail.', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success', array(
            'uid' => $sub_product->uid
        ));


    }

    public static function editNewSubProductOfBaseInfo($params)
    {
        if (!$params['uid']) return new result(false, "Invalid Sub-product-id");

        $m_sub_product = new loan_sub_productModel();
        $uid = $params['uid'];
        $row = $m_sub_product->getRow($uid);
        if (!$row) {
            if (!$params['uid']) return new result(false, "Invalid Sub-product-id");
        }
        $product_name = $params['product_name'];

        //判断product-code是否已经存在
        /*$product_code = trim($params['product_code']);
        $chk_code = $m_sub_product->find(array("sub_product_code" => $product_code, "uid" => array("neq", $uid)));
        if ($chk_code) {
            return new result(false, "Invalid Product-Code, Already Exist!");
        }*/

        $interest_type = $params['interest_type'];
        $repayment_type = $params['repayment_type'];
        $is_full_interest_prepayment = intval($params['is_full_interest_prepayment']) ? 1 : 0;
        $is_approved_prepayment_request = intval($params['is_approved_prepayment_request']) ? 1 : 0;
        $is_only_for_counter = intval($params['is_only_for_counter']) ? 1 : 0;
        $max_contracts_per_client = intval($params['max_contracts_per_client']) ?: null;

        //$row->sub_product_code = $params['product_code'];  // 不可编辑
        $row->sub_product_name = $product_name;
        $row->sub_summary = trim($params['sub_summary']);
        $row->repayment_type = $repayment_type;
        $row->interest_type = $interest_type;
        $row->is_full_interest_prepayment = $is_full_interest_prepayment;
        $row->is_approved_prepayment_request = $is_approved_prepayment_request;
        $row->is_advance_interest = intval($params['is_advance_interest']) ?: 0;
        $row->is_only_for_counter = $is_only_for_counter;
        $row->max_contracts_per_client = $max_contracts_per_client;
        $product_key = md5($row->product_name . $row->product_code . time());
        $row->product_key = $product_key;

        $ret = $row->update();
        if (!$ret->STS) return $ret;
        return new result(true, 'success', array(
            'uid' => $uid
        ));
    }

    /**
     * 重新创建temporary 产品
     * @param $uid
     * @return result
     */
    private function copyTemporaryProduct($uid)
    {
        $m_loan_product = new loan_sub_productModel();  // 二级产品
        $product_info = $m_loan_product->find(array('uid' => $uid));
        if (empty($product_info)) {
            return new result(false, 'Invalid Id!');
        }
        $product_key = $product_info['product_key'];
        $chk_temporary = $m_loan_product->find(array('product_key' => $product_key, 'state' => loanProductStateEnum::TEMP));
        if ($chk_temporary) {
            return new result(true, '', array('is_copy' => false, 'uid' => $chk_temporary['uid']));
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();

        try {

            $main_row = $m_loan_product->newRow($product_info);
            $main_row->state = loanProductStateEnum::TEMP;
            $main_row->update_time = Now();
            $rt_1 = $main_row->insert();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Failed!--' . $rt_1->MSG);
            }
            $new_product_id = $rt_1->AUTO_ID;

            $m_loan_product_condition = M('loan_product_condition');
            $product_condition_arr = $m_loan_product_condition->select(array('loan_product_id' => $uid));
            foreach ($product_condition_arr as $product_condition) {
                $product_condition_row = $m_loan_product_condition->newRow($product_condition);
                $product_condition_row->loan_product_id = $rt_1->AUTO_ID;
                $rt_2 = $product_condition_row->insert();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Failed!--' . $rt_2->MSG);
                }
            }

            $size_rate_map = array();
            $special_rate_map = array();
            $m_loan_product_size_rate = M('loan_product_size_rate');
            $m_loan_product_special_rate = M('loan_product_special_rate');
            $product_size_rate_arr = $m_loan_product_size_rate->select(array('sub_product_id' => $uid));
            foreach ($product_size_rate_arr as $product_size_rate) {
                $product_size_rate_row = $m_loan_product_size_rate->newRow($product_size_rate);
                $product_size_rate_row->sub_product_id = $rt_1->AUTO_ID;
                $product_size_rate_row->update_time = Now();
                $rt_3 = $product_size_rate_row->insert();
                if (!$rt_3->STS) {
                    $conn->rollback();
                    return new result(false, 'Failed!--' . $rt_3->MSG);
                } else {
                    $size_rate_map[$product_size_rate['uid']] = $rt_3->AUTO_ID;
                    $product_special_rate_arr = $m_loan_product_special_rate->select(array('size_rate_id' => $product_size_rate['uid']));
                    foreach ($product_special_rate_arr as $product_special_rate) {
                        $product_special_rate_row = $m_loan_product_special_rate->newRow($product_special_rate);
                        $product_special_rate_row->size_rate_id = $rt_3->AUTO_ID;
                        $product_special_rate_row->update_time = Now();
                        $rt_4 = $product_special_rate_row->insert();
                        if (!$rt_4->STS) {
                            $conn->rollback();
                            return new result(false, 'Failed!--' . $rt_4->MSG);
                        } else {
                            $special_rate_map[$product_special_rate['uid']] = $rt_4->AUTO_ID;
                        }
                    }
                }
            }

            // 移植绑定的保险产品
            // todo 更通用的方式来处理，不用每次移植
            $insurance_items = array();
            $m_insurance_relation = new insurance_product_relationshipModel();
            $insurances = $m_insurance_relation->getRows(array(
                'loan_product_id' => $uid
            ));
            if (count($insurances) > 0) {
                foreach ($insurances as $item) {
                    $new_insurance = $m_insurance_relation->newRow();
                    $new_insurance->loan_product_id = $new_product_id;
                    $new_insurance->insurance_product_item_id = $item['insurance_product_item_id'];
                    $new_insurance->type = $item['type'];
                    $rt = $new_insurance->insert();
                    if (!$rt->STS) {
                        $conn->rollback();
                        return new result(false, 'Failed!--' . $rt->MSG);
                    }
                    $insurance_items[] = $item['insurance_product_item_id'];
                }
            }
            $conn->submitTransaction();
            $uid = $rt_1->AUTO_ID;
            return new result(true, '', array('is_copy' => true, 'uid' => $uid, 'new_product_id' => $new_product_id, 'size_rate_map' => $size_rate_map, 'special_rate_map' => $special_rate_map));
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 保存产品主要信息
     * @param $p
     * @return result
     */
    public function insertProductMain($p)
    {
        $product_name = trim($p['product_name']);
        $product_code = trim($p['product_code']);
        $is_multi_contract = intval($p['is_multi_contract']);
        $is_advance_interest = intval($p['is_advance_interest']);
        $is_editable_interest = intval($p['is_editable_interest']);
        $is_editable_grace_days = intval($p['is_editable_grace_days']);
        $creator_id = intval($p['creator_id']);
        $creator_name = trim($p['creator_name']);

        if (empty($product_name)) {
            return new result(false, 'Name cannot be empty!');
        }
        if (empty($product_code)) {
            return new result(false, 'Code cannot be empty!');
        }

        $m_loan_product = M('loan_product');
        $condition = array('product_code' => $product_code);
        $chk_code = $m_loan_product->find($condition);
        if ($chk_code) {
            return new result(false, 'Code Exist!');
        }

        $row = $m_loan_product->newRow();
        $row->product_code = $product_code;
        $row->product_name = $product_name;
        $row->is_multi_contract = $is_multi_contract;
        $row->is_advance_interest = $is_advance_interest;
        $row->is_editable_interest = $is_editable_interest;
        $row->is_editable_grace_days = $is_editable_grace_days;
        $row->creator_id = $creator_id;
        $row->creator_name = $creator_name;
        $row->create_time = Now();
        $row->state = 10;
        $row->product_key = md5(uniqid());
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Save Successful!', array('uid' => $rt->AUTO_ID));
        } else {
            return new result(false, 'Save Failure!');
        }
    }

    /**
     * 修改商品主要信息
     * @param $p
     * @return result
     */
    public function updateProductMain($p)
    {
        $uid = trim($p['uid']);
        $product_name = trim($p['product_name']);
        $product_code = trim($p['product_code']);
        $is_multi_contract = intval($p['is_multi_contract']);
        $is_advance_interest = intval($p['is_advance_interest']);
        $is_editable_interest = intval($p['is_editable_interest']);
        $is_editable_grace_days = intval($p['is_editable_grace_days']);

        if (empty($product_name)) {
            return new result(false, 'Name cannot be empty!');
        }
        if (empty($product_code)) {
            return new result(false, 'Code cannot be empty!');
        }

        $m_loan_product = M('loan_product');
        $product_info = $m_loan_product->getRow($uid);
        if (!$product_info) {
            return new result(false, 'No main product info.', null, errorCodesEnum::NO_DATA);
        }


        $row = $m_loan_product->getRow(array('uid' => $uid));
        $chk_code = $m_loan_product->find(array('product_code' => $product_code, 'uid' => array('neq', $uid), 'product_key' => array('neq', $row['product_key'])));
        if ($chk_code) {
            return new result(false, 'Code Exist!');
        }

        $row->product_code = $product_code;
        $row->product_name = $product_name;
        $row->is_multi_contract = $is_multi_contract;
        $row->is_advance_interest = $is_advance_interest;
        $row->is_editable_interest = $is_editable_interest;
        $row->is_editable_grace_days = $is_editable_grace_days;
        $row->update_time = Now();

        $rt = $row->update();
        if (!$rt->STS) {
            return new result(false, 'Update Failure!');
        } else {
            $data = array('uid' => $uid);
            return new result(true, 'Update Successful!', $data);
        }

    }


    /**
     * 保存罚金信息
     * @param $p
     * @return result
     */
    public function updateProductPenalty($p)
    {
        $uid = intval($p['uid']);
        //$penalty_on = trim($p['penalty_on']);
        $penalty_rate = round($p['penalty_rate'], 2);
        $penalty_divisor_days = intval($p['penalty_divisor_days']);
        $grace_days = intval($p['grace_days']);
        $is_editable_penalty = intval($p['is_editable_penalty']) ? 1 : 0;
        $penalty_is_compound_interest = intval($p['penalty_is_compound_interest']) ? 1 : 0;
        $is_copy = 0;
        /*
        $rt = $this->copyTemporaryProduct($uid);
        if (!$rt->STS) {
            return $rt;
        } else {
            $uid = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];
        }
        */
        $m_sub_product = new loan_sub_productModel();
        $row = $m_sub_product->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Id --' . $uid);
        }

        if (empty($penalty_rate)) {
            return new result(false, 'Penalty rate be empty!');
        }
        if (empty($penalty_divisor_days)) {
            return new result(false, 'Penalty divisor days cannot be empty!');
        }

        $row->penalty_on = penaltyOnEnum::TOTAL;
        $row->penalty_rate = $penalty_rate;
        $row->penalty_divisor_days = $penalty_divisor_days;
        $row->grace_days = $grace_days;
        $row->is_editable_penalty = $is_editable_penalty;
        $row->penalty_is_compound_interest = $penalty_is_compound_interest;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            if ($is_copy) {
                $data = array('uid' => $uid);
            } else {
                $data = array();
            }
            return new result(true, 'Save Successful!', $data);
        } else {
            return new result(false, 'Save Failure!');
        }
    }

    /**
     * 获取利率设置列表
     * @param $p
     * @return array
     */
    public function getSizeRateList($p)
    {
        $product_id = intval($p['product_id']);
        $m_loan_product_size_rate = M('loan_product_size_rate');
        $list = $m_loan_product_size_rate->orderBy('interest_payment asc,loan_size_min asc,loan_size_max asc,min_term_days asc,max_term_days asc')->select(array('product_id' => $product_id));
        return array('STS' => true, 'data' => $list);
    }

    /**
     * 保存利率
     * @param $p
     * @return result
     */
    public function insertSizeRate($p)
    {

        $product_id = intval($p['product_id']);
        $currency = trim($p['currency']);
        $loan_size_min = round($p['loan_size_min'], 2);
        $loan_size_max = round($p['loan_size_max'], 2);
        $min_term_days = intval($p['min_term_days']);
        $max_term_days = intval($p['max_term_days']);

        $interest_rate = round($p['interest_rate'], 2);
        $interest_rate_unit = trim($p['interest_rate_unit']);
        $interest_min_value = round($p['interest_min_value'], 2);
        $interest_mortgage1 = round($p['interest_rate_mortgage1'], 2);
        $interest_mortgage2 = round($p['interest_rate_mortgage2'], 2);
        $admin_fee = round($p['admin_fee'], 2);
        $admin_fee_type = intval($p['admin_fee_type']);
        $loan_fee = round($p['loan_fee'], 2);
        $loan_fee_type = intval($p['loan_fee_type']);
        $operation_fee = round($p['operation_fee'], 2);
        $operation_fee_mortgage1 = round($p['operation_fee_mortgage1'], 2);
        $operation_fee_mortgage2 = round($p['operation_fee_mortgage2'], 2);
        $operation_fee_unit = trim($p['operation_fee_unit']);
        $operation_min_value = round($p['operation_min_value'], 2);
        $service_fee = round($p['service_fee'],2);
        $service_fee_type = intval($p['service_fee_type']);


        $m_sub_product = new loan_sub_productModel();
        $sub_product = $m_sub_product->getRow($product_id);
        if (!$sub_product) {
            return new result(false, 'No product info:' . $product_id, null, errorCodesEnum::NO_DATA);
        }

        if ($min_term_days > $max_term_days) {
            return new result(false, 'The minimum days cannot exceed the maximum days!');
        }

        if ($loan_size_min >= $loan_size_max) {
            return new result(false, 'The min amount cannot exceed the max amount!');
        }

        /*
        $rt = $this->copyTemporaryProduct($product_id);
        if (!$rt->STS) {
            return $rt;
        } else {
            $uid = $rt->DATA['uid'];
            $new_product_id = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];
        }
        */

        $is_copy = 0;

        $m_loan_product_size_rate = new loan_product_size_rateModel();

        $row = $m_loan_product_size_rate->newRow();
        $row->main_product_id = $sub_product->product_id;
        $row->product_id = $sub_product->uid;
        $row->currency = $currency;
        $row->loan_size_min = $loan_size_min;
        $row->loan_size_max = $loan_size_max;
        $row->min_term_days = $min_term_days;
        $row->max_term_days = $max_term_days;
        $row->interest_payment = $sub_product->interest_type;
        $row->interest_rate_period = $sub_product->repayment_type;
        $row->interest_min_value = $interest_min_value;
        $row->interest_rate = $interest_rate;
        $row->interest_rate_unit = $interest_rate_unit;
        $row->interest_rate_type = 0;
        $row->interest_rate_mortgage1 = $interest_mortgage1;
        $row->interest_rate_mortgage2 = $interest_mortgage2;
        $row->admin_fee = $admin_fee;
        $row->admin_fee_type = $admin_fee_type;
        $row->loan_fee = $loan_fee;
        $row->loan_fee_type = $loan_fee_type;
        $row->operation_fee = $operation_fee;
        $row->operation_fee_mortgage1 = $operation_fee_mortgage1;
        $row->operation_fee_mortgage2 = $operation_fee_mortgage2;
        $row->operation_fee_unit = $operation_fee_unit;
        $row->operation_fee_type = 0;
        $row->operation_min_value = $operation_min_value;
        $row->is_show_for_client=intval($p['is_show_for_client']);
        $row->update_time = Now();
        $row->service_fee = $service_fee;
        $row->service_fee_type = $service_fee_type;
        $rt = $row->insert();
        if ($rt->STS) {
            $data = array('size_rate_id' => $rt->AUTO_ID);
            if ($is_copy) {
                $data['uid'] = array('uid' => $uid);
                $data['new_product_id'] = $new_product_id;
            }
            return new result(true, 'Save Successful!', $data);
        } else {
            return new result(false, 'Save Failure!');
        }
    }

    /**
     * 更新利率
     * @param $p
     * @return result
     */
    public function updateSizeRate($p)
    {
        //$product_id = intval($p['product_id']);
        $size_rate_id = intval($p['size_rate_id']);
        $currency = trim($p['currency']);
        $loan_size_min = round($p['loan_size_min'], 2);
        $loan_size_max = round($p['loan_size_max'], 2);
        $min_term_days = intval($p['min_term_days']);
        $max_term_days = intval($p['max_term_days']);

        $interest_rate = round($p['interest_rate'], 2);
        $interest_rate_unit = trim($p['interest_rate_unit']);
        $interest_min_value = round($p['interest_min_value'], 2);
        $interest_mortgage1 = round($p['interest_rate_mortgage1'], 2);
        $interest_mortgage2 = round($p['interest_rate_mortgage2'], 2);
        $admin_fee = round($p['admin_fee'], 2);
        $admin_fee_type = intval($p['admin_fee_type']);
        $loan_fee = round($p['loan_fee'], 2);
        $loan_fee_type = intval($p['loan_fee_type']);
        $operation_fee = round($p['operation_fee'], 2);
        $operation_fee_mortgage1 = round($p['operation_fee_mortgage1'], 2);
        $operation_fee_mortgage2 = round($p['operation_fee_mortgage2'], 2);
        $operation_fee_unit = trim($p['operation_fee_unit']);
        $operation_min_value = round($p['operation_min_value'], 2);
        $service_fee = round($p['service_fee'],2);
        $service_fee_type = intval($p['service_fee_type']);



        if ($min_term_days > $max_term_days) {
            return new result(false, 'The minimum days cannot exceed the maximum days!');
        }

        if ($loan_size_min >= $loan_size_max) {
            return new result(false, 'The min amount cannot exceed the max amount!');
        }

        $m_loan_product_size_rate = M('loan_product_size_rate');
        $row = $m_loan_product_size_rate->getRow(array('uid' => $size_rate_id));
        if (!$row) {
            return new result(false, 'Invalid Size Rate!');
        }

        $product_id = $row->product_id;

        /*
        $rt = $this->copyTemporaryProduct($product_id);
        if (!$rt->STS) {
            return $rt;
        } else {
            $product_id = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];
            $size_rate_map = $rt->DATA['size_rate_map'];
            if ($size_rate_map) {
                $size_rate_id = $size_rate_map[$size_rate_id];
            }
        }
        */
        $is_copy = 0;


        $row->product_id = $product_id;
        $row->currency = $currency;
        $row->loan_size_min = $loan_size_min;
        $row->loan_size_max = $loan_size_max;
        $row->min_term_days = $min_term_days;
        $row->max_term_days = $max_term_days;
        $row->interest_rate = $interest_rate;
        $row->interest_rate_unit = $interest_rate_unit;
        $row->interest_rate_type = 0;
        $row->interest_min_value = $interest_min_value;
        $row->interest_rate_mortgage1 = $interest_mortgage1;
        $row->interest_rate_mortgage2 = $interest_mortgage2;
        $row->admin_fee = $admin_fee;
        $row->admin_fee_type = $admin_fee_type;
        $row->loan_fee = $loan_fee;
        $row->loan_fee_type = $loan_fee_type;
        $row->operation_fee = $operation_fee;
        $row->operation_fee_mortgage1 = $operation_fee_mortgage1;
        $row->operation_fee_mortgage2 = $operation_fee_mortgage2;
        $row->operation_fee_type = 0;
        $row->operation_fee_unit = $operation_fee_unit;
        $row->operation_min_value = $operation_min_value;
        $row->is_show_for_client=intval($p['is_show_for_client']);
        $row->update_time = Now();
        $row->service_fee = $service_fee;
        $row->service_fee_type = $service_fee_type;
        $rt = $row->update();
        if ($rt->STS) {
            if ($is_copy) {
                $data = array('uid' => $product_id);
            } else {
                $data = array();
            }
            return new result(true, 'Update Successful!', $data);
        } else {
            return new result(false, 'Update Failure!');
        }
    }

    /**
     * 移除利率
     * @param $p
     * @return result
     */
    public function removeSizeRate($p)
    {
        $size_rate_id = intval($p['size_rate_id']);
        $m_loan_product_size_rate = M('loan_product_size_rate');
        $row = $m_loan_product_size_rate->getRow(array('uid' => $size_rate_id));
        if (!$row) {
            return new result(false, 'Invalid Size Rate!');
        }

        /*
        $rt = $this->copyTemporaryProduct($row['sub_product_id']);
        if (!$rt->STS) {
            return $rt;
        } else {
            $product_id = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];
            $size_rate_map = $rt->DATA['size_rate_map'];
            if ($size_rate_map) {
                $size_rate_id = $size_rate_map[$size_rate_id];
            }
        }
        */

        $is_copy = 0;
        $product_id = $row->product_id;
        $row = $m_loan_product_size_rate->getRow(array('uid' => $size_rate_id));
        $rt = $row->delete();
        if (!$rt->STS) {

            return new result(false, 'Remove Failure!');
        }

        $rt_1 = $m_loan_product_special_rate = M('loan_product_special_rate')->delete(array('size_rate_id' => $size_rate_id));
        if (!$rt_1->STS) {

            return new result(false, 'Remove Failure!');
        }

        if ($is_copy) {
            $data = array('uid' => $product_id);
        } else {
            $data = array();
        }
        return new result(true, 'Remove Success!', $data);
    }

    /**
     * 编辑产品条件
     * @param $p
     * @return result
     */
    public function updateMainProductCondition($p)
    {
        $product_id = intval($p['product_id']);
        unset($p['product_id']);

        $m_loan_product_condition = M('loan_product_condition');
        $m_core_definition = M('core_definition');
        $conn = ormYo::Conn();
        $conn->startTransaction();

        try {
            $rt_1 = $m_loan_product_condition->delete(array('loan_product_id' => $product_id));
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Save Failure!');
            }

            foreach ($p as $key => $val) {
                if ($val != 1) continue;
                $definition_arr = explode(',', $key);
                $definition_category = $definition_arr[0];
                $definition_id = $definition_arr[1];
                $definition = $m_core_definition->find(array('uid' => $definition_id, 'category' => $definition_category));
                if (!$definition) {
                    $conn->rollback();
                    return new result(false, 'Invalid Definition!');
                }
                $row = $m_loan_product_condition->newRow();
                $row->loan_product_id = $product_id;
                $row->definition_category = $definition_category;
                $row->definition_id = $definition_id;
                $rt_2 = $row->insert();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Save Failure!');
                }
            }

            $conn->submitTransaction();
            $data = array('uid' => $product_id);
            return new result(true, 'Save Successful!', $data);

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 更新描述
     * @param $p
     * @return result
     */
    public function updateMainProductDescription($p)
    {
        $product_id = intval($p['product_id']);
        $name = 'product_' . $p['name'];
        $val = $p['val'];


        $m_loan_product = M('loan_product');
        $row = $m_loan_product->getRow(array('uid' => $product_id));
        if (!$row) {
            return new result(false, 'Invalid Product!');
        }
        $row->$name = $val;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            $data = array('uid' => $product_id);
            return new result(true, 'Update Successful!', $data);
        } else {
            return new result(false, 'Update Failure!');
        }
    }

    /**
     * 改变产品状态
     * @param $uid
     * @param $state
     * @return result
     * 一个系列同时只能有一个产品state 为20
     */
    public function changeProductState($uid, $state)
    {
        $m_loan_product = new loan_sub_productModel();
        $row = $m_loan_product->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Product!');
        }
        if ($row->state == $state) {
            return new result(true, 'success');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            if ($state == loanProductStateEnum::ACTIVE) {

                //更新
                $sql = "update loan_sub_product set state='" . loanProductStateEnum::HISTORY . "',update_time='" . Now() . "'
                where state='" . loanProductStateEnum::ACTIVE . "' and  product_key='" . $row['product_key'] . "' ";
                $up = $m_loan_product->conn->execute($sql);
                if (!$up->STS) {
                    $conn->rollback();
                    return new result(false, 'Update history fail.');
                }
            }
            $row->state = $state;
            $row->update_time = Now();
            $rt = $row->update();
            if ($rt->STS) {
                $conn->submitTransaction();
                return new result(true, 'Update Successful!');
            } else {
                $conn->rollback();
                return new result(false, 'Update Failure!');
            }
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 获取特殊利率
     * @param $size_rate_id
     * @return array|null
     */
    public function getSpecialRateList($size_rate_id)
    {
        $m_loan_product_special_rate = M('loan_product_special_rate');
        $special_rate_list = $m_loan_product_special_rate->orderBy('special_grade asc,special_type asc')->select(array('size_rate_id' => $size_rate_id));
        return $special_rate_list;
    }

    /**
     * 增加特殊利率
     * @param $p
     * @return result
     */
    public function insertSpecialSizeRate($p)
    {
        $product_id = intval($p['product_id']);
        $size_rate_id = intval($p['size_rate_id']);
        $client_grade = trim($p['client_grade']);
        $client_type = trim($p['client_type']);
        $interest_rate = round($p['interest_rate'], 2);
        $interest_rate_type = 0;
        $interest_min_value = round($p['interest_min_value'], 2);
        $admin_fee = round($p['admin_fee'], 2);
        $admin_fee_type = intval($p['admin_fee_type']);
        $loan_fee = round($p['loan_fee'], 2);
        $loan_fee_type = intval($p['loan_fee_type']);
        $operation_fee = round($p['operation_fee'], 2);
        $operation_fee_type = 0;
        $operation_min_value = round($p['operation_min_value'], 2);
        $is_full_interest = intval($p['is_full_interest']);
        if ($is_full_interest == 1) {
            $prepayment_interest = 0;
            $prepayment_interest_type = 0;
        } else {
            $prepayment_interest = round($p['prepayment_interest'], 2);
            $prepayment_interest_type = intval($p['prepayment_interest_type']);
        }

        $m_loan_product_special_rate = M('loan_product_special_rate');
        $chk_rate = $m_loan_product_special_rate->find(array('size_rate_id' => $size_rate_id, 'client_grade' => $client_grade, 'client_type' => $client_type));
        if ($chk_rate) {
            return new result(false, 'Conditions repeated!');
        }
        /*
                $rt = $this->copyTemporaryProduct($product_id);
                if (!$rt->STS) {
                    return $rt;
                } else {
                    $product_id = $rt->DATA['uid'];
                    $is_copy = $rt->DATA['is_copy'];

                    $size_rate_map = $rt->DATA['size_rate_map'];
                    if ($size_rate_map) {
                        $size_rate_id = $size_rate_map[$size_rate_id];
                    }

                }*/

        $row = $m_loan_product_special_rate->newRow();
        $row->size_rate_id = $size_rate_id;
        $row->client_grade = $client_grade ?: null;
        $row->client_type = $client_type ?: null;
        $row->interest_rate = $interest_rate;
        $row->interest_rate_type = $interest_rate_type;
        $row->interest_min_value = $interest_min_value;
        $row->admin_fee = $admin_fee;
        $row->admin_fee_type = $admin_fee_type;
        $row->loan_fee = $loan_fee;
        $row->loan_fee_type = $loan_fee_type;
        $row->operation_fee = $operation_fee;
        $row->operation_fee_type = $operation_fee_type;
        $row->operation_min_value = $operation_min_value;
        $row->is_full_interest = $is_full_interest;
        $row->prepayment_interest = $prepayment_interest;
        $row->prepayment_interest_type = $prepayment_interest_type;
        $row->update_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Add Successful!', array('product_id' => $product_id, 'size_rate_id' => $size_rate_id));
        } else {
            return new result(false, 'Add Failure!' . $rt->STS);
        }
    }

    /**
     * 更新特殊利率
     * @param $p
     * @return result
     */
    public function updateSpecialSizeRate($p)
    {
        $product_id = intval($p['product_id']);
        $uid = intval($p['uid']);
        $size_rate_id = intval($p['size_rate_id']);
        $client_grade = trim($p['client_grade']);
        $client_type = trim($p['client_type']);
        $interest_rate = round($p['interest_rate'], 2);
        $interest_rate_type = 0;
        $interest_min_value = round($p['interest_min_value'], 2);
        $admin_fee = round($p['admin_fee'], 2);
        $admin_fee_type = intval($p['admin_fee_type']);
        $loan_fee = round($p['loan_fee'], 2);
        $loan_fee_type = intval($p['loan_fee_type']);
        $operation_fee = round($p['operation_fee'], 2);
        $operation_fee_type = 0;
        $operation_min_value = round($p['operation_min_value'], 2);
        $is_full_interest = intval($p['is_full_interest']);
        if ($is_full_interest == 1) {
            $prepayment_interest = 0;
            $prepayment_interest_type = 0;
        } else {
            $prepayment_interest = round($p['prepayment_interest'], 2);
            $prepayment_interest_type = intval($p['prepayment_interest_type']);
        }

        $m_loan_product_special_rate = M('loan_product_special_rate');
        $chk_rate = $m_loan_product_special_rate->find(array('uid' => array('neq', $uid), 'size_rate_id' => $size_rate_id, 'client_grade' => $client_grade, 'client_type' => $client_type));
        if ($chk_rate) {
            return new result(false, 'Conditions repeated!');
        }

        /*$rt = $this->copyTemporaryProduct($product_id);
        if (!$rt->STS) {
            return $rt;
        } else {
            $product_id = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];

            $size_rate_map = $rt->DATA['size_rate_map'];
            if ($size_rate_map) {
                $size_rate_id = $size_rate_map[$size_rate_id];
            }

            $special_rate_map = $rt->DATA['special_rate_map'];
            if ($special_rate_map) {
                $uid = $special_rate_map[$uid];
            }
        }*/

        $row = $m_loan_product_special_rate->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }

        $row->client_grade = $client_grade;
        $row->client_type = $client_type ?: null;
        $row->interest_rate = $interest_rate ?: null;
        $row->interest_rate_type = $interest_rate_type;
        $row->interest_min_value = $interest_min_value;
        $row->admin_fee = $admin_fee;
        $row->admin_fee_type = $admin_fee_type;
        $row->loan_fee = $loan_fee;
        $row->loan_fee_type = $loan_fee_type;
        $row->operation_fee = $operation_fee;
        $row->operation_fee_type = $operation_fee_type;
        $row->operation_min_value = $operation_min_value;
        $row->is_full_interest = $is_full_interest;
        $row->prepayment_interest = $prepayment_interest;
        $row->prepayment_interest_type = $prepayment_interest_type;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Edit Successful!', array('product_id' => $product_id, 'size_rate_id' => $size_rate_id));
        } else {
            return new result(false, 'Edit Failure!');
        }
    }

    /**
     * 移除特殊汇率
     * @param $p
     * @return result
     */
    public function removeSpecialSizeRate($p)
    {
        $product_id = intval($p['product_id']);
        $size_rate_id = intval($p['size_rate_id']);
        $uid = intval($p['uid']);
        $m_loan_product_special_rate = M('loan_product_special_rate');
        $row = $m_loan_product_special_rate->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }

        /*$rt = $this->copyTemporaryProduct($product_id);
        if (!$rt->STS) {
            return $rt;
        } else {
            $product_id = $rt->DATA['uid'];
            $is_copy = $rt->DATA['is_copy'];

            $size_rate_map = $rt->DATA['size_rate_map'];
            if ($size_rate_map) {
                $size_rate_id = $size_rate_map[$size_rate_id];
            }

            $special_rate_map = $rt->DATA['special_rate_map'];
            if ($special_rate_map) {
                $uid = $special_rate_map[$uid];
            }
        }*/

        $row = $m_loan_product_special_rate->getRow(array('uid' => $uid));
        $rt = $row->delete();
        if (!$rt->STS) {
            return new result(false, 'Remove Failure!');
        } else {
            return new result(true, 'Remove Success!', array('product_id' => $product_id, 'size_rate_id' => $size_rate_id));
        }
    }


    public static function getAllProductList()
    {
        $m = new loan_productModel();
        $sql = "select * from loan_product where state='" . loanProductStateEnum::ACTIVE . "' ";
        $list = $m->reader->getRows($sql);
        return $list;
    }


    public static function getMainProductDetailInfo($product_id)
    {
        $m_loan_product = new loan_productModel();
        $product_info = $m_loan_product->find(array(
            'uid' => $product_id
        ));
        if (!$product_info) {
            return null;
        }

        // 处理JAVA不可解析的问题
        $product_info['product_description'] = rawurlencode($product_info['product_description']);
        $product_info['product_qualification'] = rawurlencode($product_info['product_qualification']);
        $product_info['product_feature'] = rawurlencode($product_info['product_feature']);
        $product_info['product_required'] = rawurlencode($product_info['product_required']);
        $product_info['product_notice'] = rawurlencode($product_info['product_notice']);

        /* $re = self::getProductDescribeRateList($product_id, 1, 100000);
         $rate_list = $re['list'];*/

        return array(
            'product_info' => $product_info,
            //'rate_list' => $rate_list,
        );
    }


    public static function getSubProductDetailInfo($sub_product_id)
    {
        $sub_product_id = intval($sub_product_id);
        $r = new ormReader();
        $sql = "select sp.*,p.category,p.product_code,p.product_name
        from loan_sub_product sp inner join loan_product p on p.uid=sp.product_id 
        where sp.uid='$sub_product_id' ";
        $product_info = $r->getRow($sql);

        // 处理JAVA不可解析的问题
        $product_info['product_description'] = rawurlencode($product_info['product_description']);
        $product_info['product_qualification'] = rawurlencode($product_info['product_qualification']);
        $product_info['product_feature'] = rawurlencode($product_info['product_feature']);
        $product_info['product_required'] = rawurlencode($product_info['product_required']);
        $product_info['product_notice'] = rawurlencode($product_info['product_notice']);

        $re = self::getProductDescribeRateList($sub_product_id, 1, 100000);
        $rate_list = $re['list'];
        return array(
            'product_info' => $product_info,
            'rate_list' => $rate_list,
        );
    }

    public function updateSubProductDescription($p)
    {
        $product_id = intval($p['product_id']);
        $name = 'product_' . $p['name'];
        $val = $p['val'];

        $m_loan_sub_product = M('loan_sub_product');
        $row = $m_loan_sub_product->getRow(array('uid' => $product_id));
        if (!$row) {
            return new result(false, 'Invalid Product!');
        }
        $row->$name = $val;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
//            $data = array('uid' => $product_id);
//            return new result(true, 'Update Successful!', $data);
            return new result(true, 'Update Successful!');
        } else {
            return new result(false, 'Update Failure!');
        }
    }


    public static function getProductRateList($product_id = 0, $page_num, $page_size, $currency = null)
    {
        $page_num = $page_num ?: 1;
        $page_size = $page_size ?: 100000;

        $r = new ormReader();
        $where = '';
        if ($currency) {
            $where = " and currency='$currency' ";
        }
        $sql = "select * from loan_product_size_rate where product_id='$product_id' $where group by product_id,currency,loan_size_min,loan_size_max,min_term_days,max_term_days,interest_payment,interest_rate_period 
        order by loan_size_min asc,loan_size_max asc,interest_payment asc,max_term_days asc ";
        $re = $r->getPage($sql, $page_num, $page_size);

        $rows = $re->rows;

        $return = array(
            'total_num' => $re->count,
            'total_pages' => $re->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $rows
        );

        return $return;
    }

    public static function getProductDescribeRateList($product_id, $page_num, $page_size, $currency = null)
    {
        $page_num = $page_num ?: 1;
        $page_size = $page_size ?: 100000;
        $rate_re = self::getProductRateList($product_id, $page_num, $page_size, $currency);
        $rate_list = $rate_re['list'];
        $return = array();
        foreach ($rate_list as $v) {

            // 日期转换
            if ($v['max_term_days'] >= 30) {
                $min = floor($v['min_term_days'] / 30);
                $max = ceil($v['max_term_days'] / 30);
                $v['loan_term_time'] = $min . '-' . $max . 'M';

            } else {
                $v['loan_term_time'] = intval($v['min_term_days']) . '-' . intval($v['max_term_days']) . 'D';
            }

            $v['interest_rate_des'] = $v['interest_rate'] . '%';
            $v['operation_fees_des'] = $v['operation_fee'] . '%';

            // 合并利率（interest+operate_fee）
            // todo 现在只有设置百分比
            $re = loan_baseClass::interestRateConversion($v['operation_fee'], $v['operation_fee_unit'], $v['interest_rate_unit']);
            $interest_sum = $v['interest_rate'];
            if ($re->STS) {
                $interest_sum += $re->DATA;
            }

            $v['total_rate_des_value'] = $interest_sum . '%';


            if ($v['admin_fee_type'] == 1) {
                $v['admin_fee_des_value'] = $v['admin_fee'];
            } else {
                $v['admin_fee_des_value'] = $v['admin_fee'] . '%';
            }


            if ($v['loan_fee_type'] == 1) {
                $v['loan_fee_des_value'] = $v['loan_fee'];
            } else {
                $v['loan_fee_des_value'] = $v['loan_fee'] . '%';
            }

            $item = $v;
            $item['repayment_type'] = $v['interest_payment'];
            $item['repayment_period'] = $v['interest_rate_period'];
            $item['loan_term_time'] = $v['loan_term_time'];
            $item['interest_rate_des_value'] = $v['interest_rate_des_value'];


            $return[] = $item;
        }

        $rate_re['list'] = $return;
        return $rate_re;
    }
    public static function getCategoryDescribeRateList($package_id,$product_id)
    {
        $rate_list=self::getSizeRateByPackageId($package_id,$product_id);
        $return = array();
        foreach ($rate_list as $v) {

            if( !$v['is_active']){
                continue;
            }
            if(!$v['is_show_for_client']) continue;
            unset($v['default_setting']);

            // 日期转换
            if ($v['max_term_days'] >= 30) {
                $min = floor($v['min_term_days'] / 30);
                $max = floor($v['max_term_days'] / 30);
                $v['loan_term_time'] = $min . '-' . $max . 'M';

            } else {
                $v['loan_term_time'] = intval($v['min_term_days']) . '-' . intval($v['max_term_days']) . 'D';
            }


            $v['interest_rate_des'] = $v['interest_rate']>0?$v['interest_rate']:($v['interest_rate_mortgage1']>0?$v['interest_rate_mortgage1']:$v['interest_rate_mortgage2']) . '%';
            $v['operation_fees_des'] = $v['operation_fee']>0?$v['operation_fee']:($v['operation_fee_mortgage1']>0?$v['operation_fee_mortgage1']:$v['operation_fee_mortgage2']) . '%';



            // 合并利率（interest+operate_fee）
            // todo 现在只有设置百分比
            $re = loan_baseClass::interestRateConversion($v['operation_fee'], $v['operation_fee_unit'], $v['interest_rate_unit']);
            $interest_sum = $v['interest_rate'];
            if ($re->STS) {
                $interest_sum += $re->DATA;
            }

            $v['total_rate_des_value'] = $interest_sum . '%';


            if ($v['admin_fee_type'] == 1) {
                $v['admin_fee_des_value'] = $v['admin_fee'];
            } else {
                $v['admin_fee_des_value'] = $v['admin_fee'] . '%';
            }


            if ($v['loan_fee_type'] == 1) {
                $v['loan_fee_des_value'] = $v['loan_fee'];
            } else {
                $v['loan_fee_des_value'] = $v['loan_fee'] . '%';
            }

            $item = $v;
            $item['repayment_type'] = $v['interest_payment'];
            $item['repayment_period'] = $v['interest_rate_period'];
            $item['loan_term_time'] = $v['loan_term_time'];
            $item['interest_rate_des_value'] = $v['interest_rate_des_value'];


            $return[] = $item;
        }

        $rate_re['list'] = $return;
        return $rate_re;
    }

    /** 获取产品某种周期的最低或最大利率
     * @param $product_id
     * @param $period_type
     * @param bool $max
     * @return float|mixed
     */
    public static function getProductMinOrMaxRateOfPeriodType($product_id, $period_type, $max = false, $interest_type = null)
    {
        $m_rate = new loan_product_size_rateModel();
        $rates = $m_rate->getRows(array(
            'product_id' => $product_id,
            //'interest_rate' => array('gt',0)
        ));
        if (count($rates) < 1) {
            return 0.00;
        }
        switch ($interest_type) {
            case assetsCertTypeEnum::SOFT:
                $interest_key = 'interest_rate_mortgage1';
                break;
            case assetsCertTypeEnum::HARD:
                $interest_key = 'interest_rate_mortgage2';
                break;
            default:
                $interest_key = 'interest_rate';
        }
        $rate_array = array();
        foreach ($rates as $rate) {
            $interest_rate = $rate[$interest_key];
            $in_re = loan_baseClass::interestRateConversion($interest_rate, $rate['interest_rate_unit'], $period_type);
            if ($in_re->STS) {
                $interest_rate = $in_re->DATA;
            }

            $o_rate = 0;  // operation fee 不计入展示
            /*$o_rate = $rate['operation_fee'];
            $o_re = loan_baseClass::interestRateConversion($o_rate, $rate['operation_fee_unit'], $period_type);
            if ($o_re->STS) {
                $o_rate = $o_re->DATA;
            }*/
            $total_rate = round($interest_rate + $o_rate, 2);
            if ($total_rate > 0) {
                $rate_array[] = $total_rate;
            }


        }
        asort($rate_array, SORT_NUMERIC);
        $value = $rate_array[0] ?: 0;
        if ($max) {
            $value = $rate_array[count($rate_array) - 1];
        }
        return round($value, 2);
    }


    public static function getMinMonthlyRate($product_id, $type = '', $interest_type = null)
    {
        $max = false;
        if ($type == 'max') {
            $max = true;
        }
        return self::getProductMinOrMaxRateOfPeriodType($product_id, interestRatePeriodEnum::MONTHLY, $max, $interest_type);
    }


    public static function getAllActiveSubProductList()
    {
        $r = new ormReader();
        $sql = "select sp.*,p.category,p.product_code,p.product_name from loan_sub_product sp inner join loan_product p on p.uid=sp.product_id 
        where p.state='" . loanProductStateEnum::ACTIVE . "' and sp.state='" . loanProductStateEnum::ACTIVE . "'
        order by sp.product_id,sp.uid";
        return $r->getRows($sql);
    }

    public static function getActiveSubProductListById($product_id)
    {
        $r = new ormReader();
        $sql = "select sp.*,p.category,p.product_code,p.product_name from loan_sub_product sp inner join loan_product p on p.uid=sp.product_id 
        where sp.product_id='$product_id' and p.state='" . loanProductStateEnum::ACTIVE . "' and sp.state='" . loanProductStateEnum::ACTIVE . "'
        order by sp.product_id,sp.uid";
        return $r->getRows($sql);
    }

    public static function getActiveSubProductListByUid($uid)
    {
        $r = new ormReader();
        $sql = "select sp.*,p.category,p.product_code,p.product_name from loan_sub_product sp inner join loan_product p on p.uid=sp.product_id 
        where sp.uid='$uid' and p.state='" . loanProductStateEnum::ACTIVE . "' and sp.state='" . loanProductStateEnum::ACTIVE . "'
        order by sp.product_id,sp.uid";
        return $r->getRow($sql);
    }


    /**
     * @param $member_id
     * @param int $is_counter 是否柜台 0 1
     * @return ormCollection
     */
    public static function getMemberCanLoanSubProductList($member_id, $is_counter = 0)
    {
        // 剔除掉限制贷款的产品
        if ($is_counter) {
            $where = " ";
        } else {
            $where = " and sp.is_only_for_counter=0 ";
        }
        $r = new ormReader();
        $sql = "select sp.*,p.category,p.product_code,p.product_name,ml.member_id from loan_sub_product sp inner join loan_product p on p.uid=sp.product_id 
        left join (select * from member_limit_loan_product where member_id='$member_id' )  ml on sp.sub_product_code=ml.product_code
        where  ml.member_id is null and p.state=" . qstr(loanProductStateEnum::ACTIVE) . " and sp.state=" . qstr(loanProductStateEnum::ACTIVE) . "
        $where  group by sp.uid
        order by sp.product_id,sp.uid";
        return $r->getRows($sql);

    }

    public static function getMemberCanLoanSubProductListForCounter($member_id)
    {
        $member_id = intval($member_id);
        $r = new ormReader();
        $sql = "select mcc.*,sp.sub_product_name,sp.sub_product_code from member_credit_category mcc
                inner join loan_sub_product sp on mcc.sub_product_id=sp.uid
                inner join loan_product p on p.uid=sp.product_id
                where p.state=" . qstr(loanProductStateEnum::ACTIVE) . " and sp.state=" . qstr(loanProductStateEnum::ACTIVE) . " and mcc.is_close=0 and mcc.is_one_time=0 and mcc.member_id=". qstr($member_id);

        return $r->getRows($sql);

    }

    /**
     * 设置限制产品（CO APP）
     * @param $member_id
     * @param $allow_product
     * @param $operator_id
     */
    public function setMemberLimitProduct($member_id, $product_code, $operator_id)
    {
        $member_id = intval($member_id);

        $userObj = new objectUserClass($operator_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }
        try {
            $m_member_limit_loan_product = M('member_limit_loan_product');
            $row = $m_member_limit_loan_product->newRow();
            $row->member_id = $member_id;
            $row->product_code = $product_code;
            $row->operator_id = $userObj->user_id;
            $row->operator_name = $userObj->user_name;
            $row->create_time = Now();
            $rt_2 = $row->insert();
            if (!$rt_2) {
                return $rt_2;
            }
            return new result(true);
        } catch (Exception $e) {
            return new result(false, $e->getMessage(), null, errorCodesEnum::DB_ERROR);
        }
    }

    public function deleteMemberLimitProduct($member_id, $product_code, $operator_id)
    {
        $member_id = intval($member_id);
        $userObj = new objectUserClass($operator_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }
        try {
            $m_member_limit_loan_product = M('member_limit_loan_product');
            $rt_1 = $m_member_limit_loan_product->delete(array('member_id' => $member_id, 'product_code' => $product_code));
            if (!$rt_1->STS) {
                return $rt_1;
            }
            return new result(true);
        } catch (Exception $e) {
            return new result(false, $e->getMessage(), null, errorCodesEnum::DB_ERROR);
        }
    }

    /**
     * 添加一个产品包
     * @param $args
     */
    public static function addProductPackage($args)
    {
        if (!$args['package']) {
            return new result(false, "Invalid Parameter:Require to input package name");
        }
        $m = M("loan_product_package");
        $row = $m->newRow($args);
        $ret = $row->insert();
        if ($args['copy_from']) {
            //复制一个package
            $m_rate = M("loan_product_special_rate");
            $rate_list = $m_rate->select(array("special_grade" => $args['copy_from'], "special_type" => 0));
            if (count($rate_list)) {
                foreach ($rate_list as $item) {
                    $rate = $m_rate->newRow($item);
                    $rate->special_grade = $row->uid;
                    $chk = $rate->insert();
                }
            }
        }
        return $ret;
    }

    public static function editProductPackage($args)
    {
        if (!$args['uid']) {
            return new result(false, "Invalid Parameter:Require to input package id");
        }
        $m = M("loan_product_package");
        $row = $m->getRow($args['uid']);
        if (!$row) {
            return new result(false, "Invalid Parameter:Require to input package id");
        }
        if (isset($args['package'])) {
            $row->package = $args['package'];
        }
        if (isset($args['remark'])) {
            $row->remark = $args['remark'];
        }
        $row->update_time = Now();
        return $row->update();
    }

    public static function deleteProductPackage($uid)
    {
        $m_package = M("loan_product_package");
        $row = $m_package->getRow($uid);
        if (!$row) {
            return new result(false, "Invalid Parameter:No Row Found!");
        }
        $m_package_rate = M("loan_product_special_rate");
        $chk = $m_package_rate->find(array("special_grade" => $uid, "special_type" => 0));
        if (is_array($chk)) {
            return new result(false, "This Package Has Already Used!");
        }
        $m_grant = new member_credit_grantModel();
        $chk = $m_grant->find(array("package_id" => $uid));
        if ($chk) {
            return new result(false, "This Package Has Already Used!");
        }

        return $row->delete();
    }

    public static function getProductPackageList()
    {
        $r = new ormReader();
        $sql = "select * from loan_product_package order by package";
        $arr = $r->getRows($sql);
        $arr = resetArrayKey($arr, "uid");
        return $arr;
    }

    public static function saveSpecialLoanRateOfPackage($p)
    {
        $package_id = $p['package_id'];
        if (!count($package_id)) {
            return new result(false, "Nothing can be saved");
        }
        $size_rate_id = $p['size_rate_id'];
        $m_size_rate = new loan_product_size_rateModel();
        $size_rate = $m_size_rate->find(array("uid" => $size_rate_id));
        if (!count($size_rate)) {
            return new result(false, "Nothing can be saved:No Default Rate");
        }
        $m_special = M("loan_product_special_rate");
        $conn = ormYo::Conn();
        $conn->startTransaction();
        foreach ($p['package_id'] as $idk => $package_id) {
            $row = $m_special->getRow(array("special_grade" => $package_id, "size_rate_id" => $size_rate_id, "special_type" => 0));
            $is_new = false;
            if (!$row) {
                $row = $m_special->newRow();
                $row->special_grade = $package_id;
                $row->special_type = 0;
                $row->size_rate_id = $size_rate_id;
                $is_new = true;
            }
            $row->interest_rate = floatval($p['package_interest_rate'][$idk]) ? floatval($p['package_interest_rate'][$idk]) : $size_rate['interest_rate'];
            $row->interest_rate_mortgage1 = floatval($p['package_interest_rate_mortgage1'][$idk]) ? floatval($p['package_interest_rate_mortgage1'][$idk]) : $size_rate['interest_rate_mortgage1'];
            $row->interest_rate_mortgage2 = floatval($p['package_interest_rate_mortgage2'][$idk]) ? floatval($p['package_interest_rate_mortgage2'][$idk]) : $size_rate['interest_rate_mortgage2'];
            $row->interest_min_value = floatval($p['package_interest_min_value'][$idk]) ? floatval($p['package_interest_min_value'][$idk]) : $size_rate['interest_min_value'];
            $row->admin_fee = floatval($p['package_admin_fee'][$idk]);
            $row->loan_fee = floatval($p['package_loan_fee'][$idk]);
            $row->operation_fee = floatval($p['package_operation_fee'][$idk]);
            $row->operation_fee_mortgage1 = floatval($p['package_operation_fee_mortgage1'][$idk]) ? floatval($p['package_operation_fee_mortgage1'][$idk]) : $size_rate['operation_fee_mortgage1'];
            $row->operation_fee_mortgage2 = floatval($p['package_operation_fee_mortgage2'][$idk]) ? floatval($p['package_operation_fee_mortgage2'][$idk]) : $size_rate['operation_fee_mortgage2'];
            $row->operation_min_value = floatval($p['package_operation_min_value'][$idk]);
            $row->service_fee = floatval($p['package_service_fee'][$idk])?:$size_rate['service_fee'];
            $row->service_fee_type = intval($p['package_service_fee_type'])?:$size_rate['service_fee_type'];
            $row->update_time = Now();
            $row->update_user_id = $p['operator_id'];
            $row->update_user_name = $p['operator_name'];
            if ($is_new) {
                $ret = $row->insert();
            } else {
                $ret = $row->update();
            }
            if (!$ret->STS) {
                $conn->rollback();
                return $ret;
            }
        }
        $conn->submitTransaction();
        return new result(true, "Save Success!");
    }


    /**
     * 根据产品包获取相关的利息设置
     * @param $package_id
     * @param $sub_product_id
     * @param $size_rate_id
     * @return array
     */
    public static function getSizeRateByPackageId($package_id, $sub_product_id = null, $size_rate_id = 0)
    {

        if ($sub_product_id > 0) {
            $sql = "select * from loan_product_size_rate where product_id=" . qstr($sub_product_id);
        } else {
            $sql = "select * from loan_product_size_rate where product_id in (select uid from loan_sub_product)";

        }
        $size_rate_id = intval($size_rate_id);
        if ($size_rate_id) {
            $sql .= " and uid='$size_rate_id' ";
        }

        $sql .= " order by currency,min_term_days,max_term_days,loan_size_min";

        $r = new ormReader();
        $default_rows = $r->getRows($sql);
        if ($package_id > 0) {
            $sql = "select * from loan_product_special_rate where special_grade='" . $package_id . "' and special_type=0 
              ";
            if ($size_rate_id) {
                $sql .= " and size_rate_id='$size_rate_id' ";
            }
            $sql .= " order by interest_rate asc ";
            $special_rows = $r->getRows($sql);
            $special_rows = resetArrayKey($special_rows, "size_rate_id");//认为同一个包的size_rate_id不会重复设置special
        } else {
            $special_rows = array();
        }

        if (empty($special_rows)) {
            // return $default_rows;
        }

        $arr = array();
        foreach ($default_rows as $item) {
            if ($special_rows[$item['uid']]) {
                $special_item = $special_rows[$item['uid']];

                $item = array_merge($item, array(
                    "interest_rate" => $special_item['interest_rate'],
                    "interest_rate_mortgage1" => $special_item['interest_rate_mortgage1'],
                    "interest_rate_mortgage2" => $special_item['interest_rate_mortgage2'],
                    "interest_min_value" => $special_item['interest_min_value'],
                    "admin_fee" => $special_item['admin_fee'],
                    "loan_fee" => $special_item['loan_fee'],
                    "operation_fee" => $special_item['operation_fee'],
                    'operation_fee_mortgage1' => $special_item['operation_fee_mortgage1'],
                    'operation_fee_mortgage2' => $special_item['operation_fee_mortgage2'],
                    "operation_min_value" => $special_item['operation_min_value'],
                    'service_fee' => $special_item['service_fee'],
                    //'service_fee_type' => $special_item['service_fee_type'],
                    "is_special" => 1,  //说明这一行是特殊设置
                    'special_rate_id' => $special_item['uid'],
                    'is_show_for_client'=>$special_item['is_show_for_client'],
                    'is_active'=>$special_item['is_active'],
                    'default_setting' => $item,
                ));
            } else {
                $item['default_setting'] = $item;
            }
            $arr[] = $item;
        }
        return $arr;
    }

    public static function getSizeRateByPackageIdGroupByProduct($package_id)
    {
        $size_rate = self::getSizeRateByPackageId($package_id);
        $r = new ormReader();
        $sql = "select * from loan_sub_product";
        $prod_list = $r->getRows($sql);
        $prod_list = resetArrayKey($prod_list, "uid");
        $arr = array();
        foreach ($prod_list as $k => $item) {
            $sr = array();
            foreach ($size_rate as $sitem) {
                if ($sitem['product_id'] == $k) {
                    $sr[] = $sitem;
                }
            }
            $item['size_rate'] = $sr;
            $arr[$k] = $item;
        }
        return $arr;
    }

    public static function getCurrencyListAndAmountRangeByProduct($sub_product_id)
    {
        $sub_product_id = intval($sub_product_id);
        $r = new ormReader();
        $sql = "select currency,min(loan_size_min) min_amount,max(loan_size_max) max_amount,min(min_term_days) min_days,max(max_term_days) max_days
        from loan_product_size_rate where product_id='$sub_product_id' group by currency order by currency not in (" . qstr(currencyEnum::USD) . "), currency";
        return $r->getRows($sql);
    }

    public static function savePackageSizeRate($p)
    {
        $size_rate_id = intval($p['size_rate_id']);
        $special_grade = intval($p['special_grade']);
        $val = $p['val'];
        $fld_name = trim($p['fld_name']);

        if (!$size_rate_id || !$special_grade || $val < 0 || !$fld_name) {
            return new result(false, 'Param Error.');
        }

        $m_loan_product_size_rate = M('loan_product_size_rate');
        $row_rate = $m_loan_product_size_rate->find(array('uid' => $size_rate_id));
        if (!$row_rate) {
            return new result(false, "Invalid Parameter:No Default Rate Row Found");
        }

        $if_diff = $row_rate[$fld_name] == $val;

        $m_loan_product_special_rate = M('loan_product_special_rate');
        $row = $m_loan_product_special_rate->getRow(
            array('size_rate_id' => $size_rate_id,
                'special_grade' => $special_grade,
                'special_type' => 0
            )
        );
        if ($row) {
            $row->$fld_name = $val;
            $row->update_time = Now();
            $row->update_user_id = $p['user_id'];
            $row->update_user_name = $p['user_name'];
            $rt = $row->update();
        } else {
            $row = $m_loan_product_special_rate->newRow($row_rate);
            $row->size_rate_id = $size_rate_id;
            $row->special_grade = $special_grade;
            $row->special_type = 0;
            $row->update_time = Now();
            $row->update_user_id = $p['user_id'];
            $row->update_user_name = $p['user_name'];
            $row->$fld_name = $val;
            $rt = $row->insert();
        }
        if ($rt->STS) {
            return new result(true, '', array('new_value' => $val));
        } else {
            return new result(false, 'Edit Failed.');
        }
    }

}