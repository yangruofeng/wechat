<?php

class savings_v2Control extends savingsControl  {
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取资产概要信息
     * @return result
     *   DATA: {
     *   currency: 货币
     *   total_assets:  总资产，是下面的内容之和
     *   balance:   passbook 的余额
     *   [category_type]: {
     *       total: 当前category_type的持有产品金额合计（contract状态判断）
     *       earnings: {
     *           yesterday:  昨日收益
     *           last_month: 上月收益
     *           total: 总收益
     *       }
     *   }
     *   }, ...
     */
    public function assetsSummaryOp() {
        $ret = $this->checkToken();
        if (!$ret->STS) return $ret;

        $params = array_merge(array(), $_GET, $_POST);
        return memberSavingsV2Class::getAssetsSummary($params['member_id']);
    }

    /**
     * 获取category列表，category_type分Current/Terminal，不传同时返回
     * @return result
     *   DATA: [
     *   {
     *   id,
     *   name,
     *   interest_range,  category下所有有效产品的min/max interest_rate_yearly
     *   amount_range, cateogry下所有有效产品的 min limit_deposit_lowest_per_time / max limit_deposit_highest_per_client
     *   days_range,   category下所有有效产品的 min min_terms / max max_terms
     *   }, ...
     *   ]
     */
    public function categoryListOp() {
        $params = array_merge(array(), $_GET, $_POST);
        $category_model = new savings_categoryModel();
        $data = $category_model->getCategoryListWithProductSummary(array(
            'category_type' => $params['category_type'],
            'currency' => $params['currency'],
            'category_state' => $params['category_state']));
        return new result(true, null, $data);
    }

    /**
     * 获取指定category下所有产品列表（利率表）
     * @return result
     */
    public function productListOp() {
        $params = array_merge(array(), $_GET, $_POST);
        $product_model = new savingsProductClass();

        $data = $product_model->getProductList(array(
            'category_id' => $params['category_id'],
            'state' => savingsProductStateEnum::ACTIVE,
            'currency' => $params['currency']
        ));
        return new result(true, null, $data);
    }

    /**
     * 获取产品详细
     * @return result
     *   DATA:  savings_product row
     */
    public function productDetailOp() {
        $params = array_merge(array(), $_GET, $_POST);
        $product_model = new savings_productModel();
        $data = $product_model->getProductInfoById($params['product_id']);
        return new result(true, null, $data);
    }

    /**
     * 购买产品，预览
     * @return result
     */
    public function purchasePreviewOp() {
        $ret = $this->checkToken();
        if (!$ret->STS) return $ret;

        $params = array_merge(array(), $_GET, $_POST);
        $member_id = $params['member_id'];
        $amount = $params['amount'];
        $currency = $params['currency'];
        $termOpts = array();
        if ($params['term'] && !$params['end_date']) {
            $termOpts['term'] = $params['term'];
        } else if ($params['end_date'] && !$params['term']) {
            $termOpts['end_date'] = $params['end_date'];
        } else {
            return new result(false, 'The parameter of term and end_date must have and only one', errorCodesEnum::INVALID_PARAM);
        }
        $productOpts = array(
            'product_id' => $params['product_id'],
            'category_id' => $params['category_id']
        );

        $biz = new bizMemberPurchaseSavingsProductClass(bizSceneEnum::APP_MEMBER);
        return $biz->bizStart($member_id, $amount, $currency, $termOpts, $productOpts);
    }

    /**
     * 购买产品，确认付款
     * @return result
     */
    public function purchaseConfirmOp() {
        $ret = $this->checkToken();
        if (!$ret->STS) return $ret;

        $params = array_merge(array(), $_GET, $_POST);
        $biz = new bizMemberPurchaseSavingsProductClass(bizSceneEnum::APP_MEMBER);
        $ret = $biz->checkMemberTradingPasswordSign($params['biz_id'], $params['member_id'], $params['time'], $params['sign']);
        if (!$ret->STS) return $ret;
        return $biz->bizSubmit($params['biz_id']);
    }

    /**
     * 获取持仓概要内容
     * @return result
     *   DATA: {
     *       [category_type]: {
     *          summary: {
     *              [currency]: {
     *                  total: 当前category_type的持有产品金额合计（contract状态判断）
     *                  earnings: {
     *                      yesterday:  昨日收益
     *                      last_month: 上月收益
     *                      total: 总收益
     *                  },
     *              }
     *          },
     *          categorys: {
     *              [id]: {
     *                  id,
     *                  name,
     *                  interest_range,
     *                  amount_range,
     *                  days_range,
     *                  [currency]: {
     *                      total: 当前category下所有product的持仓合同金额合计
     *                      earnings: {
     *                          yesterday:  昨日收益
     *                          last_month: 上月收益
     *                          total: 总收益
     *                      }
     *                  }, ...
     *              }, ...
     *          }
     *       }
     *    }
     */
    public function positionSummaryOp() {
        $ret = $this->checkToken();
        if (!$ret->STS) return $ret;

        $params = array_merge(array(), $_GET, $_POST);
        return memberSavingsV2Class::getPositionSummary($params['member_id'], $params['category_type']);
    }

    /**
     * 获取指定category下所有product的合同（持仓）列表
     * @return result
     *   DATA: [
     *      {
     *          [contract properties],
     *          earnings: {
     *             yesterday:  昨日收益
     *             last_month: 上月收益
     *             total: 总收益
     *          }
     *      },...
     *   ]
     */
    public function positionDetailOp() {
        $ret = $this->checkToken();
        if (!$ret->STS) return $ret;

        $params = array_merge(array(), $_GET, $_POST);
        return memberSavingsV2Class::getPositionDetail($params['member_id'], $params['category_id']);
    }

    /**
     * 赎回产品申请
     */
    public function redeemPreviewOp() {
        $ret = $this->checkToken();
        if (!$ret->STS) return $ret;

        $params = array_merge(array(), $_GET, $_POST);
        $member_id = $params['member_id'];
        $amount = $params['amount'];
        $currency = $params['currency'];
        $product_id = $params['product_id'];

        $biz = new bizMemberRedeemSavingsProductClass(bizSceneEnum::APP_MEMBER);
        return $biz->bizStart($member_id, $amount, $currency, $product_id);
    }

    /**
     * 赎回产品确认
     * @return result
     */
    public function redeemConfirmOp() {
        $ret = $this->checkToken();
        if (!$ret->STS) return $ret;

        $params = array_merge(array(), $_GET, $_POST);
        $biz = new bizMemberRedeemSavingsProductClass(bizSceneEnum::APP_MEMBER);
        $ret = $biz->checkMemberTradingPasswordSign($params['biz_id'], $params['member_id'], $params['time'], $params['sign']);
        if (!$ret->STS) return $ret;
        return $biz->bizSubmit($params['biz_id']);
    }

    public function transactionListOp() {
        $ret = $this->checkToken();
        if (!$ret->STS) return $ret;

        $params = array_merge(array(), $_GET, $_POST);
        return memberSavingsV2Class::getTransactionList(
            $params['member_id'], $params['date_start'], $params['date_end'],
            $params['page'], $params['page_size'],
            $params['category_id'], $params['product_id']);
    }
}