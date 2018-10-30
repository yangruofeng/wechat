<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/17/2018
 * Time: 11:26 AM
 */
class data_center_productControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('enum,loan,certification');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Back Office");
        Tpl::setDir("data_center_product");
    }

    public function productInterestOp()
    {
        $m_loan_category = M('loan_category');
        $product_list = $m_loan_category->getRows(array('is_close' => 0));
        Tpl::output('product_list', $product_list);
        Tpl::showPage('product.interest.page');
    }

    public function getSizeRateListOp($p)
    {
        $category_id = intval($p['category_id']);
        $m_loan_category = M('loan_category');
        $category = $m_loan_category->find(array('uid' => $category_id));
        $interest_package_id = $category['interest_package_id'];

        $m_loan_product_package = M('loan_product_package');
        $package_info = $m_loan_product_package->find(array('uid' => $interest_package_id));
        $category['default_package'] = $package_info['package'];

        $m_loan_product = M('loan_sub_product');
        $loan_product = $m_loan_product->find(array('uid' => $category['default_product_id']));
        $category['default_repayment'] = $loan_product['sub_product_name'];
        Tpl::output("category", $category);

        $arr = loan_productClass::getSizeRateByPackageIdGroupByProduct($interest_package_id);
        foreach ($arr as $k1 => $v1) {
            if ($v1['state'] != loanProductStateEnum::ACTIVE) {
                unset($arr[$k1]);
            }

            foreach ($v1['size_rate'] as $k2 => $v2) {
                if (!$v2['is_active']) {
                    unset($v1['size_rate'][$k2]);
                }
            }
            $arr[$k1] = $v1;
        }
        Tpl::output("list", $arr);
    }

}