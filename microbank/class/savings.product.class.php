<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/26
 * Time: 15:15
 */
class savingsProductClass
{
    /**
     * 获取产品列表
     * @param $filters
     * @return ormCollection
     */
    public static function getProductList($filters = array())
    {
        return (new savings_productModel())->getProductList($filters);
    }

    /**
     * 获取产品详情(id)
     * @param $uid
     * @return bool|mixed|null
     */
    public static function getProductInfoById($uid)
    {
        $m_savings_product = new savings_productModel();
        $product_info = $m_savings_product->getProductInfoById($uid);
        return $product_info;
    }

    /**
     * 添加产品
     * @param $params
     * @return result
     */
    public static function addProductMain($params)
    {
        $m_savings_product = new savings_productModel();
        $chk_code = $m_savings_product->find(array(
            'product_code' => trim($params['product_code']),
            'state' => array('neq', savingsProductStateEnum::CANCEL),
        ));
        if ($chk_code) {
            return new result(false, 'Repeated code.', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_savings_category_template = new savings_category_templateModel();
        $category_template = $m_savings_category_template->find(array(
            'category_id' => intval($params['category_id'])
        ));
        $category_template = is_array($category_template) ? $category_template : array();
        foreach ($category_template as $k => $v) {
            if (in_array($k, array('uid', 'update_time'))) continue;
            if (!isset($params[$k]) || $params[$k] === "") {
                $params[$k] = $v;
            }
        }

        $rt = self::insertProduct($params);
        if ($rt->STS) {
            return new result(true, 'Add successful.', $rt->DATA);
        } else {
            return new result(false, 'Add failed--' . $rt->MSG);
        }
    }

    /**
     * 编辑
     * @param $params
     * @return result
     */
    public static function editProductMain($params)
    {
        $uid = intval($params['uid']);
        $m_savings_product = new savings_productModel();
        $row = $m_savings_product->getRow($uid);
        if (!$row || ($row['state'] == savingsProductStateEnum::CANCEL)) {
            return new result(false, 'Invalid id.', null, errorCodesEnum::INVALID_PARAM);
        }
        $params['category_id'] = $row->category_id;

        $rt_init = self::initProductParams($params);
        if (!$rt_init->STS) {
            return $rt_init;
        }

        $chk_code = $m_savings_product->find(array(
            'product_code' => trim($params['product_code']),
            'uid' => array('neq', $uid),
            'state' => array('neq', savingsProductStateEnum::CANCEL),
        ));
        if ($chk_code) {
            return new result(false, 'Repeated code.');
        }

        $params = $rt_init->DATA;
        // $row->category_id = $params['category_id'];  编辑不修改category_id
        $row->product_code = $params['product_code'];
        $row->product_name = $params['product_name'];
        $row->interest_rate = $params['interest_rate'];
        $row->interest_rate_unit = $params['interest_rate_unit'];
        $row->interest_rate_yearly = $params['interest_rate_yearly'];
        $row->min_terms = $params['min_terms'];
        $row->max_terms = $params['max_terms'];
        $row->currency = $params['currency'];
        $row->update_time = Now();
        $rt = $row->update();

        if ($rt->STS) {
            return new result(true, 'Edit successful.');
        } else {
            return new result(false, 'Edit failed--' . $rt->MSG);
        }
    }

    /**
     * 检查参数
     * @param $params
     * @return result
     */
    private static function initProductParams($params)
    {
        $params['product_code'] = trim($params['product_code']);
        $params['product_name'] = trim($params['product_name']);
        $params['category_id'] = intval($params['category_id']);
        $params['min_terms'] = intval($params['min_terms']);
        $params['max_terms'] = $params['max_terms'] ? intval($params['max_terms']) : '';
        $params['interest_rate'] = round($params['interest_rate'], 2);
        $params['interest_rate_unit'] = trim($params['interest_rate_unit']);
        $params['interest_rate_yearly'] = self::getInterestRateYearly($params['interest_rate'], $params['interest_rate_unit']);

        if (empty($params['product_code']) || empty($params['product_name']) || !$params['category_id'] || $params['interest_rate'] < 0) {
            return new result(false, 'Param error.');
        }

        $category_model = new savings_categoryModel();
        $category_info = $category_model->getCategoryInfoById($params['category_id']);
        if ($category_info['category_term_style'] == savingsCategoryTermStyleEnum::FIXED) {
            $params['max_terms'] = $params['min_terms'];
        } else if ($category_info['category_term_style'] == savingsCategoryTermStyleEnum::FREE) {
            $params['max_terms'] = -1;
        }

        if ($params['max_terms'] > 0 && $params['min_terms'] > $params['max_terms']) {
            return new result(false, 'The min terms can not be greater than the max terms.');
        }

        return new result(true, '', $params);
    }

    /**
     * 获取年利率
     * @param $interest_rate
     * @param $interest_rate_unit
     * @return mixed
     */
    private static function getInterestRateYearly($interest_rate, $interest_rate_unit)
    {
        if ($interest_rate_unit == savingsPeriodUnitEnum::DAILY) {
            $interest_rate = $interest_rate * 365;
        } elseif ($interest_rate_unit == savingsPeriodUnitEnum::MONTHLY) {
            $interest_rate = $interest_rate * 12;
        } else {
            $interest_rate = $interest_rate;
        }
        return $interest_rate;
    }

    /**
     * 存入产品表
     * @param $params
     * @return result
     */
    private static function insertProduct($params)
    {
        $rt_init = self::initProductParams($params);
        if (!$rt_init->STS) {
            return $rt_init;
        }
        $params = $rt_init->DATA;

        $obj_user = new objectUserClass(intval($params['creator_id']));
        $m_savings_product = M('savings_product');
        $row = $m_savings_product->newRow($params);
        $row->state = savingsProductStateEnum::TEMP;
        $row->creator_id = $obj_user->user_id;
        $row->creator_name = $obj_user->user_name;
        $row->create_time = Now();
        $row->update_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Add successful.', array('uid' => $rt->AUTO_ID));
        } else {
            return new result(false, 'Add failed--' . $rt->MSG);
        }
    }

    /**
     * 编辑产品detail
     * @param $product_id
     * @param $fld_name
     * @param $fld_text
     * @return result
     */
    public static function editProductDetail($product_id, $fld_name, $fld_text)
    {
        $m_savings_product = M('savings_product');
        $row = $m_savings_product->getRow($product_id);
        if (!$row) {
            return new result(false, 'Invalid id.');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $row->$fld_name = $fld_text;
        $row->update_time = Now();
        $rt_1 = $row->update();
        if (!$rt_1->STS) {
            $conn->rollback();
            return new result(false, 'Update failed.');
        }

        $rt_2 = self::insertCategoryDetail($row->category_id, $fld_name, $fld_text);
        if (!$rt_2->STS) {
            $conn->rollback();
            return new result(false, $rt_2->MSG);
        }
        $conn->submitTransaction();
        return new result(true, 'Update successful.');
    }

    /**
     * 保存模板
     * @param $category_id
     * @param $fld_name
     * @param $fld_text
     * @return ormResult|result
     */
    private static function insertCategoryDetail($category_id, $fld_name, $fld_text)
    {
        $m_savings_category_template = M('savings_category_template');
        $row = $m_savings_category_template->getRow(array('category_id' => $category_id));
        if (!$row) {
            $row = $m_savings_category_template->newRow();
            $row->category_id = $category_id;
            $row->$fld_name = $fld_text;
            $row->update_time = Now();
            $rt = $row->insert();
        } else {
            $row->$fld_name = $fld_text;
            $row->update_time = Now();
            $rt = $row->update();
        }
        return $rt;
    }

    /**
     * 保存setting & limit
     * @param $params
     * @return result
     */
    public static function editProductSetting($params)
    {
        $product_id = intval($params['uid']);
        $m_savings_product = M('savings_product');
        $row = $m_savings_product->getRow($product_id);
        if (!$row) {
            return new result(false, 'Invalid id.');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $row->limit_deposit_lowest_per_time = round($params['limit_deposit_lowest_per_time'], 2);
        $row->limit_deposit_highest_per_time = round($params['limit_deposit_highest_per_time'], 2);
        $row->limit_deposit_highest_per_day = round($params['limit_deposit_highest_per_day'], 2);
        $row->limit_deposit_highest_per_client = round($params['limit_deposit_highest_per_client'], 2);
        $row->limit_withdraw_lowest_per_time = round($params['limit_withdraw_lowest_per_time'], 2);
        $row->limit_withdraw_highest_per_time = round($params['limit_withdraw_highest_per_time'], 2);
        $row->limit_withdraw_highest_per_day = round($params['limit_withdraw_highest_per_day'], 2);
        $row->is_allow_auto_renew = intval($params['is_allow_auto_renew']);
        $row->is_allow_prior_withdraw = intval($params['is_allow_prior_withdraw']);
        $row->is_withdraw_need_password = intval($params['is_withdraw_need_password']);
        $row->is_withdraw_need_id_card = intval($params['is_withdraw_need_id_card']);
        $row->is_withdraw_allow_agency = intval($params['is_withdraw_allow_agency']);
        $row->is_withdraw_need_book = intval($params['is_withdraw_need_book']);
        if (intval($params['is_withdraw_need_book'])) {
            $row->withdraw_book_days = intval($params['withdraw_book_days']);
        } else {
            $row->withdraw_book_days = 0;
        }

        $row->update_time = Now();
        $rt_1 = $row->update();
        if (!$rt_1->STS) {
            $conn->rollback();
            return new result(false, 'Update failed.');
        }

        $rt_2 = self::insertCategorySetting($row->category_id, $params);
        if (!$rt_2->STS) {
            $conn->rollback();
            return new result(false, $rt_2->MSG);
        }
        $conn->submitTransaction();
        return new result(true, 'Update successful.');
    }

    /**
     * 保存setting& limit tem
     * @param $category_id
     * @param $params
     * @return ormResult|result
     */
    private static function insertCategorySetting($category_id, $params)
    {
        $m_savings_category_template = M('savings_category_template');
        $row = $m_savings_category_template->getRow(array('category_id' => $category_id));
        if (!$row) {
            $row = $m_savings_category_template->newRow();
            $row->category_id = $category_id;
            $is_insert = true;
        }
        $row->limit_deposit_lowest_per_time = round($params['limit_deposit_lowest_per_time'], 2);
        $row->limit_deposit_highest_per_time = round($params['limit_deposit_highest_per_time'], 2);
        $row->limit_deposit_highest_per_day = round($params['limit_deposit_highest_per_day'], 2);
        $row->limit_deposit_highest_per_client = round($params['limit_deposit_highest_per_client'], 2);
        $row->limit_withdraw_lowest_per_time = round($params['limit_withdraw_lowest_per_time'], 2);
        $row->limit_withdraw_highest_per_time = round($params['limit_withdraw_highest_per_time'], 2);
        $row->limit_withdraw_highest_per_day = round($params['limit_withdraw_highest_per_day'], 2);
        $row->is_allow_auto_renew = intval($params['is_allow_auto_renew']);
        $row->is_allow_prior_withdraw = intval($params['is_allow_prior_withdraw']);
        $row->is_withdraw_need_password = intval($params['is_withdraw_need_password']);
        $row->is_withdraw_need_id_card = intval($params['is_withdraw_need_id_card']);
        $row->is_withdraw_need_book = intval($params['is_withdraw_need_book']);
        $row->is_withdraw_allow_agency = intval($params['is_withdraw_allow_agency']);
        $row->withdraw_book_days = intval($params['withdraw_book_days']);
        $row->update_time = Now();
        if ($is_insert) {
            $rt = $row->insert();
        } else {
            $rt = $row->update();
        }
        return $rt;
    }

    /**
     * 更改产品状态
     * @param $uid
     * @param $state
     * @return result
     */
    public static function changeProductState($uid, $state)
    {
        $m_savings_product = M('savings_product');
        $row = $m_savings_product->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid id.');
        }
        if ($row['state'] == savingsProductStateEnum::CANCEL) {
            return new result(false, 'Historical products can not be modified.');
        }
        if ($state == savingsProductStateEnum::TEMP) {
            return new result(false, 'State error.');
        }

        $row->state = $state;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Successful.');
        } else {
            return new result(false, 'Failed.');
        }
    }

    /**
     * 删除产品
     * @param $uid
     * @return result
     */
    public static function removeProduct($uid)
    {
        $m_savings_product = M('savings_product');
        $row = $m_savings_product->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid id.');
        }
        $row->state = savingsProductStateEnum::CANCEL;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Delete Successful.');
        } else {
            return new result(false, 'Delete failed.');
        }

    }

    public static function getObjTypeByMinTerms($minTerms) {
        if ($minTerms < 365) {
            return objGuidTypeEnum::SHORT_DEPOSIT;
        } else {
            return objGuidTypeEnum::LONG_DEPOSIT;
        }
    }

    public static function getGUID($productId, $return_info = false)
    {
        $product_model = new savings_productModel();
        $product_info = $product_model->getRow($productId);
        if (!$product_info) throw new Exception("Product $productId not found");

        if (!$product_info->obj_guid) {
            $product_info->obj_guid = generateGuid($product_info->uid, self::getObjTypeByMinTerms($product_info->min_terms));
            $ret = $product_info->update();
            if (!$ret->STS) {
                throw new Exception("Generate GUID for user failed - " . $ret->MSG);
            }
        }
        if ($return_info) {
            return $product_info->toArray();
        } else {
            return $product_info->obj_guid;
        }
    }

    public static function findMatchedProductInCategory($categoryId, $amount, $currency, $termOpts) {
        $product_model = new savings_productModel();
        $condition = array();
        $condition[]= new ormParameter(null, 'category_id', $categoryId);
        $condition[]= new ormParameter(null, 'limit_deposit_lowest_per_time', $amount, "<=");
        $condition[]= new ormParameter(null, 'currency', $currency);
        if ($termOpts['term']) {
            $term = $termOpts['term'];
        } else if ($termOpts['end_date']) {
            $term = (strtotime($termOpts['end_date']) - strtotime(date("Y-m-d"))) / (24*3600);
        } else {
            return new result(false, 'The term or end_date must and only have one', null, errorCodesEnum::INVALID_PARAM);
        }
        if ($term) {
            $condition[] = new ormParameter(null, 'min_terms', $term, "<=");
            $condition[] = new ormParameter(null, 'max_terms', $term, ">=");
        }

        $rows = $product_model->getRows($condition);
        if (!empty($rows))
            return new result(true, null, current($rows));
        else
            return new result(true);
    }

}