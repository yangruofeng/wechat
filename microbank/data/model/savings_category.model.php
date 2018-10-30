<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/10
 * Time: 9:38
 */
class savings_categoryModel extends tableModelBase
{
    private $table_name = 'savings_category';

    function __construct()
    {
        parent::__construct($this->table_name);
    }

    /**
     * 获取分类列表
     * @param array $filters
     * @return ormCollection
     */
    public function getCategoryList($filters = array())
    {
        $sql = "SELECT * FROM $this->table_name WHERE 1 = 1";
        if (isset($filters['state'])) {
            $sql .= " AND state = " . qstr(intval($filters['state']));
        } else {
            $sql .= " AND state in (" . qstr(savingsCategoryState::ACTIVE) . "," . qstr(savingsCategoryState::INACTIVE) .")";
        }
        if ($filters['category_type']) {
            $sql .= " AND category_type = " . qstr($filters['category_type']);
        }
        $list = $this->reader->getRows($sql);
        return $list;
    }

    /**
     * 获取分类列表（包含产品概要信息）
     * @param array $filters
     * @return ormCollection
     */
    public function getCategoryListWithProductSummary($filters = array()) {
        $filter_array = array();
        $filter_array[]="b.state = " . qstr(savingsProductStateEnum::ACTIVE);
        if ($filters['category_state']) {
            $filter_array[]="a.state = " .qstr($filters['category_state']);
        } else {
            $filter_array[]="a.state in (" . qstr(savingsCategoryState::ACTIVE) . "," . qstr(savingsCategoryState::INACTIVE) . ")";
        }
        if ($filters['category_type']) {
            $filter_array[]= "a.category_type = " . qstr($filters['category_type']);
        }
        if ($filters['currency']) {
            $filter_array[]= "b.currency = " . qstr($filters['currency']);
        }
        $filter_sql = join(" AND ", $filter_array);
        $sql = <<<SQL
SELECT a.uid, b.currency, a.category_code, a.category_name, a.category_icon, a.category_term_style,
  min(b.interest_rate_yearly) min_interest_rate_yearly, 
  max(b.interest_rate_yearly) max_interest_rate_yearly,
  min(b.limit_deposit_lowest_per_time) min_amount,
  max(ifnull(nullif(b.limit_deposit_highest_per_client,0),10000000000)) max_amount,
  min(b.min_terms) min_terms,
  max(ifnull(nullif(b.max_terms,0),10000000)) max_terms,
  a.state
FROM savings_category a 
inner join savings_product b on b.category_id = a.uid
where $filter_sql
group by a.uid, b.currency
SQL;

        $list = $this->reader->getRows($sql);
        foreach ($list as $i => $row) {
            if ($row['category_icon']) {
                $row['category_icon'] = getImageUrl($row['category_icon'], imageThumbVersion::SMALL_IMG);
                $list[$i] = $row;
            }
        }
        return $list;
    }

    /**
     * 获取分类详情
     * @param $uid
     * @return bool|mixed
     */
    public function getCategoryInfoById($uid)
    {
        $info = $this->find(array('uid' => $uid));
        return $info;
    }

    /**
     * 添加分类
     * @param $params
     * @return result
     */
    public function addCategory($params)
    {
        $category_code = trim($params['category_code']);
        $category_name = trim($params['category_name']);
        $category_type = trim($params['category_type']);
        $category_term_style = intval($params['category_term_style']);
        $category_description = $params['category_description'];
        $state = intval($params['state']);
        $creator_id = intval($params['operator_id']);
        $obj_user = new objectUserClass($creator_id);

        $chk_code = $this->find(array('category_code' => $category_code));
        if ($chk_code) {
            return new result(false, 'Repeat code.');
        }

        $row = $this->newRow();
        $row->category_code = $category_code;
        $row->category_name = $category_name;
        $row->category_type = $category_type;
        $row->category_icon = $params['category_icon'];
        $row->category_term_style = $category_term_style;
        $row->category_description = $category_description;
        $row->state = $state;
        $row->create_time = Now();
        $row->creator_id = $creator_id;
        $row->creator_name = $obj_user->user_name;
        $row->update_time = Now();
        $insert = $row->insert();
        if ($insert->STS) {
            return new result(true, 'Add successful.');
        } else {
            return new result(true, 'Add failed.');
        }
    }

    /**
     * 编辑分类
     * @param $params
     * @return result
     */
    public function editCategory($params)
    {
        $uid = intval($params['uid']);
        $category_code = trim($params['category_code']);
        $category_name = trim($params['category_name']);
        $category_type = trim($params['category_type']);
        $category_term_style = intval($params['category_term_style']);
        $category_description = $params['category_description'];
        $state = intval($params['state']);
        $creator_id = intval($params['operator_id']);
        $obj_user = new objectUserClass($creator_id);

        $chk_code = $this->find(array('category_code' => $category_code, 'uid' => array('neq', $uid)));
        if ($chk_code) {
            return new result(false, 'Repeat code.');
        }

        $row = $this->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid id.');
        }
        $row->category_code = $category_code;
        $row->category_name = $category_name;
        $row->category_type = $category_type;
        $row->category_icon = $params['category_icon'];
        $row->category_term_style = $category_term_style;
        $row->category_description = $category_description;
        $row->state = $state;
        $row->create_time = Now();
        $row->creator_id = $creator_id;
        $row->creator_name = $obj_user->user_name;
        $row->update_time = Now();
        $update = $row->update();
        if ($update->STS) {
            return new result(true, 'Edit successful.');
        } else {
            return new result(true, 'Edit failed.');
        }
    }

    /**
     * 删除分类
     * @param $uid
     * @return result
     */
    public function deleteCategory($uid)
    {
        $row = $this->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid id.');
        }

        $product_list = savingsProductClass::getProductList(array('category_id' => $uid));
        if ($product_list) {
            return new result(false, 'The category has products.');
        }

        $rt = $row->delete();
        if ($rt->STS) {
            return new result(true, 'Delete successful.');
        } else {
            return new result(true, 'Delete failed.');
        }
    }
}