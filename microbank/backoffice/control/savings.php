<?php

class savingsControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('enum,savings');
        Tpl::setLayout("empty_layout");
        Tpl::setDir("savings");
    }

    /**
     * 存款分类
     */
    public function categoryOp()
    {
        $m = new savings_categoryModel();
        $list = $m->getCategoryList();
        Tpl::output("list", $list);
        Tpl::showPage("savings.category");
    }

    /**
     * 添加/编辑分类
     */
    public function editCategoryOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $m_category = new savings_categoryModel();
        if ($params['form_submit'] == 'ok') {
            $params['operator_id'] = $this->user_id;
            if (intval($params['uid'])) {
                $rt = $m_category->editCategory($params);
            } else {
                $rt = $m_category->addCategory($params);
            }
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('savings', 'category', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($params['form_submit']);
                showMessage($rt->MSG, getUrl('savings', 'editCategory', $params, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            if (intval($params['uid'])) {
                $info = $m_category->getCategoryInfoById(intval($params['uid']));
                $info = array_merge(array(), $info, $params);
                Tpl::output('current_title', 'Edit Category');
            } else {
                $info = array_merge(array(), $params);
                Tpl::output('current_title', 'Add Category');
            }
            Tpl::output('info', $info);

            $category_type = (new savingsCategoryType)->Dictionary();
            Tpl::output('category_type', $category_type);
            Tpl::showPage("savings.category.edit");
        }
    }

    /**
     * 删除分类
     * @param $p
     * @return result
     */
    public function removeCategoryOp($p)
    {
        $uid = intval($p['uid']);
        $m_category = new savings_categoryModel();
        $rt = $m_category->deleteCategory($uid);
        return $rt;
    }

    /**
     * 产品页
     */
    public function productOp()
    {
        $state_arr = (new savingsProductStateEnum())->Dictionary();
        unset($state_arr[savingsProductStateEnum::CANCEL]);
        Tpl::output('state_arr', $state_arr);

        $m_category = new savings_categoryModel();
        $category_list = $m_category->getCategoryList(array('state' => savingsCategoryState::ACTIVE));
        Tpl::output("category_list", $category_list);
        Tpl::showPage("savings.product");
    }

    /**
     * 获取产品列表
     * @param $p
     * @return ormCollection
     */
    public function getProductListOp($p)
    {
        $filters = array();
        $filters['search_text'] = trim($p['search_text']);
        $filters['category_id'] = intval($p['category_id']);
        $filters['state'] = intval($p['state']);
        $product_list = savingsProductClass::getProductList($filters);
        return $product_list;
    }

    /**
     * 添加/编辑产品
     * @param $p
     */
    public function editProductPageOp($p=array())
    {
        if ($p === null) $p = array();
        $params = array_merge(array(), $_GET, $_POST, $p);

        $m_category = new savings_categoryModel();
        $category_list = $m_category->getCategoryList(array('state' => savingsCategoryState::ACTIVE));
        Tpl::output("category_list", $category_list);
        Tpl::output("tab", $params['tab'] ?: 'page-1');

        if (intval($params['uid'])) {
            $product_info = savingsProductClass::getProductInfoById(intval($params['uid']));
            if ($product_info['state'] == savingsProductStateEnum::CANCEL) {
                showMessage('Historical products can not be modified.');
            }
            $product_info = array_merge(array(), $product_info, $params);
            Tpl::output('product_info', $product_info);

            if (in_array($product_info['state'], array(savingsProductStateEnum::TEMP, savingsProductStateEnum::INACTIVE))) {
                $state = array(
                    'state' => savingsProductStateEnum::ACTIVE,
                    'title' => 'Active',
                );
            } else {
                $state = array(
                    'state' => savingsProductStateEnum::INACTIVE,
                    'title' => 'Inactive',
                );
            }
            Tpl::output('state', $state);

            $category_info = current(array_filter((array)$category_list, function($v) use($product_info){
                return $v['uid'] == $product_info['category_id'];
            }));
            Tpl::output('category_info', $category_info);

            Tpl::output('current_title', 'Edit Product');
        } else {
            Tpl::output('current_title', 'Add Product');
        }

        $detail_type = $this->getSavingsDetailType();
        Tpl::output("detail_type", $detail_type);

        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);

        $period_unit = (new savingsPeriodUnitEnum())->Dictionary();
        Tpl::output("period_unit", $period_unit);

        Tpl::showPage("savings.product.edit");
    }

    /**
     * 添加/编辑产品主信息
     */
    public function submitProductMainOp()
    {
        $params = array_merge(array(), $_POST);
        $params['creator_id'] = $this->user_id;
        if ($params['uid']) {
            $params['creator_id'] = $this->user_id;
            $rt = savingsProductClass::editProductMain($params);
            unset($params['act']);
            unset($params['op']);
            if ($rt->STS && $params['tab']) {
                $this->editProductPageOp(array(
                    'uid' => $params['uid'],
                    'tab' => $params['tab']
                ));
                exit;
            } else if (!$rt->STS) {
                unset($params['tab']);
            }
            showMessage($rt->MSG, getUrl('savings', 'editProductPage', $params, false, BACK_OFFICE_SITE_URL));
        } else {
            $rt = savingsProductClass::addProductMain($params);
            $data = $rt->DATA;
            if ($rt->STS && $params['tab']) {
                $this->editProductPageOp(array(
                    'uid' => $data['uid'],
                    'tab' => $params['tab']
                ));
                exit;
            } else if ($rt->STS) {
                $data = $rt->DATA;
                $data['tab'] = $params['tab'];
                $params = $data;
            } else {
                unset($params['act']);
                unset($params['op']);
                unset($params['tab']);
            }
            showMessage($rt->MSG, getUrl('savings', 'editProductPage', $params, false, BACK_OFFICE_SITE_URL));
        }
    }

    /**
     * 编辑产品详情
     * @param $p
     * @return result
     */
    public function updateProductDescriptionOp($p)
    {
        $product_id = intval($p['uid']);
        $fld_name = trim($p['name']);
        $fld_text = $p['val'];
        $rt = savingsProductClass::editProductDetail($product_id, $fld_name, $fld_text);
        return $rt;
    }

    /**
     * 保存setting&limit
     */
    public function submitProductSettingOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $rt = savingsProductClass::editProductSetting($p);
        unset($p['act']);
        unset($p['op']);
        if ($rt->STS && $p['tab'] != 'page-2') {
            $_GET['uid'] = $p['uid'];
            $_GET['tab'] = $p['tab'];
            $this->editProductPageOp();
            exit;
        } else if ($rt->STS) {
            $p['tab'] = 'page-2';
        }
        showMessage($rt->MSG, getUrl('savings', 'editProductPage', $p, false, BACK_OFFICE_SITE_URL));
    }

    /**
     * 更改产品状态
     * @param $p
     * @return result
     */
    public function changeProductStateOp($p)
    {
        $uid = intval($p['uid']);
        $state = intval($p['state']);
        $rt = savingsProductClass::changeProductState($uid, $state);
        return $rt;
    }

    /**
     * 删除产品
     * @param $p
     * @return mixed
     */
    public function removeProductOp($p)
    {
        $uid = intval($p['uid']);
        $rt = savingsProductClass::removeProduct($uid);
        return $rt;
    }

    /**
     * 获取模板内容
     * @param $p
     */
    public function getTempByCategoryIdOp($p)
    {
        $category_id = intval($p['category_id']);
        $temp_info = (new savings_category_templateModel())->getTempByCategoryId($category_id);
        unset($temp_info['uid']);
        Tpl::output('temp_info', $temp_info);

        $detail_type = $this->getSavingsDetailType();
        Tpl::output('detail_type', $detail_type);
    }

    /**
     * 详情类型
     * @return array
     */
    private function getSavingsDetailType()
    {
        $detail_type = array(
            'product_feature' => 'Summary',
            'product_description' => 'Description',
            'product_qualification' => 'Client Qualification',
            'product_required' => 'Documents Required',
            'product_notice' => 'Notice',
        );
        return $detail_type;
    }

}
