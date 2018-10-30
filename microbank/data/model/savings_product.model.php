<?php

class savings_productModel extends tableModelBase
{
    private $table_name = 'savings_product';

    function __construct()
    {
        parent::__construct($this->table_name);
    }

    /**
     * 获取产品列表
     * @param $filters
     * @return ormCollection
     */
    public function getProductList($filters = array())
    {
        $sql = "SELECT sp.*,sc.category_name,sc.category_term_style FROM savings_product sp"
            . " LEFT JOIN savings_category sc ON sp.category_id = sc.uid"
            . " WHERE sp.state != " . qstr(savingsProductStateEnum::CANCEL);
        if (trim($filters['search_text'])) {
            $sql .= " AND (sp.product_code like '%" . trim($filters['search_text']) . "%'";
            $sql .= " OR sp.product_name like '%" . trim($filters['search_text']) . "%')";
        }
        if (intval($filters['category_id'])) {
            $sql .= " AND sp.category_id = " . intval($filters['category_id']);
        }
        if (intval($filters['state'])) {
            $sql .= " AND sp.state = " . intval($filters['state']);
        }
        if ($filters['currency']) {
            $sql .= " AND sp.currency = " . qstr($filters['currency']);
        }

        $product_list = $this->reader->getRows($sql);
        return $product_list;
    }

    /**
     * 获取产品详情(id)
     * @param $uid
     * @return bool|mixed|null
     */
    public function getProductInfoById($uid)
    {
        $product_info = $this->find(array('uid' => $uid));
        return $product_info;
    }
}