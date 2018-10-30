<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class core_definitionModel extends tableModelBase
{
    public function  __construct()
    {
        parent::__construct('core_definition');
    }

    /**
     * 系统定义
     * 弃用
     */
    public function initSystemDefine()
    {
        $system_define = (new systemDefineEnum())->Dictionary();
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $category_arr = array_keys($system_define);
        if ($category_arr) {
            $sql = "DELETE FROM core_definition WHERE category NOT IN ('" . implode("','", $category_arr) . "') AND is_system = 1";
            $rt = $this->conn->execute($sql);
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }
        } else {
            $rt = $this->delete(array('is_system' => 1));
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }
        }

        foreach ($system_define as $key => $define) {
            $define_enum = (new $key())->Dictionary();
            $rows = $this->getRows(array('category' => $key, 'is_system' => 1));
            foreach ($rows as $row) {
                if (array_key_exists($row['item_code'], $define_enum)) {
                    unset($define_enum[$row['item_code']]);
                } else {
                    $rt_1 = $row->delete();
                    if (!$rt_1->STS) {
                        $conn->rollback();
                        return $rt_1;
                    }
                }
            }

            foreach ($define_enum as $k => $v) {
                $row = $this->newRow();
                $row->category = $key;
                $row->category_name = ucwords(strtolower($define));
                $row->item_name = ucwords(strtolower($v));
                $row->item_code = $k;
                $row->is_system = 1;
                $rt_2 = $row->insert();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return $rt_2;
                }
            }
        }

        $conn->submitTransaction();
        return new result(true);
    }

    /**
     * 获取define列表
     * @param $p
     * @return array
     */
    public function getDefineList($p)
    {
        $search_text = trim($p['search_text']);
        $is_system = intval($p['is_system']);
        $r = new ormReader();
        $sql = "SELECT category FROM core_definition WHERE is_system = $is_system";
        if ($search_text) {
            $sql .= " AND category LIKE '%" . $search_text . "%'";
        }
        $sql .= ' GROUP BY category';
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        $category_list = array();
        if ($rows) {
            $category_arr = array_column($rows, 'category');
            $sql = "SELECT * FROM core_definition WHERE category IN ('" . implode("','", $category_arr) . "')";
            $rows = $r->getRows($sql);
            foreach ($rows as $val) {
                $category_list[$val['category']][] = $val;
            }
        }

        return array(
            "sts" => true,
            "data" => $category_list,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 获取category arr
     * @param $category_arr
     * @param int $is_system
     * @return ormCollection
     */
    public function getDefineByCategory($category_arr, $is_system = 0)
    {
        if (empty($category_arr)) {
            return array();
        }
        $category_str = "('" . implode("','", $category_arr) . "')";
        $r = new ormReader();
        $sql = "SELECT * FROM core_definition WHERE category IN $category_str AND is_system = $is_system";
        $arr = $r->getRows($sql);
        $define_arr = array();
        $current_lang = Language::currentCode();
        foreach ($arr as $val) {
            if (!$define_arr[$val['category']]) {
                $category_name_arr = my_json_decode($val['category_name_json']);
                $define_arr[$val['category']]['name'] = $category_name_arr[$current_lang] ?: $val['category_name'];
            }
            $item_name_arr = my_json_decode($val['item_name_json']);
            if ($val['item_code']) $define_arr[$val['category']]['item_list'][$val['item_code']] = $item_name_arr[$current_lang] ?: $val['item_name'];
        }
        return $define_arr;
    }

    /**
     * 修改分类名称
     * @param $p
     * @return result
     */
    public function editCategoryName($p)
    {
        $category = trim($p['category']);
        $category_name = trim($p['category_name']);
        $category_name_json = $this->createLangJson($p);
        if (empty($category)) {
            return new result(false, 'Category cannot be empty!');
        }

        $sql = "UPDATE core_definition SET category_name = '" . $category_name . "', category_name_json = '" . $category_name_json . "' WHERE category = '" . $category . "'";
        $rt = $this->conn->execute($sql);
        if ($rt->STS) {
            return new result(true, 'Edit successful!');
        } else {
            return new result(false, 'Edit failed--' . $rt->MSG);
        }
    }

    /**
     * 生成多语言json
     * @param $p
     * @return mixed|string
     */
    private function createLangJson($p)
    {
        $lang_arr = array();
        $lang_list = C('lang_type_list');
        foreach ($lang_list as $key => $lang) {
            $lang_arr[$key] = trim($p[$key]);
        }
        return my_json_encode($lang_arr);
    }

    /**
     * 添加define item
     * @param $p
     * @return result
     * @throws Exception
     */
    public function addDefineItem($p)
    {
        $category = trim($p['category']);
        $item_code = trim($p['item_code']);
        $item_name = trim($p['item_name']);
        $item_name_json = $this->createLangJson($p);
        $item_desc = trim($p['item_desc']);
        $item_value = round($p['item_value'], 2);

        if (empty($item_code)) {
            return new result(false, 'Item code cannot be empty!');
        }

        if (empty($item_name)) {
            return new result(false, 'Item name cannot be empty!');
        }

        $row = $this->getRow(array('category' => $category));
        if ($row->item_code) {
            $category_name = $row->category_name;
            $category_name_json = $row->category_name_json;
            $row = $this->newRow();
            $row->category = $category;
            $row->category_name = $category_name;
            $row->category_name_json = $category_name_json;
            $is_insert = true;
        } else {
            $is_insert = false;
        }

        $row->item_code = $item_code;
        $row->item_name = $item_name;
        $row->item_name_json = $item_name_json;
        $row->item_desc = $item_desc;
        $row->item_value = $item_value;
        $row->is_system = 0;
        if ($is_insert) {
            $rt = $row->insert();
        } else {
            $rt = $row->update();
        }
        if ($rt->STS) {
            return new result(true, 'Add successful!');
        } else {
            return new result(false, 'Add failed--' . $rt->MSG);
        }
    }

    /**
     * 编辑define item
     * @param $p
     * @return result
     * @throws Exception
     */
    public function editDefineItem($p)
    {
        $uid = intval($p['uid']);
        $item_code = trim($p['item_code']);
        $item_name = trim($p['item_name']);
        $item_name_json = $this->createLangJson($p);
        $item_desc = trim($p['item_desc']);
        $item_value = round($p['item_value'], 2);
        if (empty($item_name)) {
            return new result(false, 'Item name cannot be empty!');
        }

        if (empty($item_code)) {
            return new result(false, 'Item code cannot be empty!');
        }

        $row = $this->getRow(array('uid' => $uid));
        if ($row->item_code == $item_code && $row->item_name == $item_name && $row->item_name_json == $item_name_json && $row->item_desc == $item_desc && $row->item_value == $item_value) {
            return new result(false, 'No Change!');
        }

        if ($row->is_system == 0) {
            $row->item_code = $item_code;
        }
        $row->item_name = $item_name;
        $row->item_name_json = $item_name_json;
        $row->item_desc = $item_desc;
        $row->item_value = $item_value;
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Edit successful!');
        } else {
            return new result(false, 'Edit failed--' . $rt->MSG);
        }
    }

    /**
     * 移除item
     * @param $p
     * @return result
     * @throws Exception
     */
    public function removeDefineItem($p)
    {
        $uid = intval($p['uid']);
        $row = $this->getRow(array('uid' => $uid, 'is_system' => 0));
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }

        $chk_other = $this->find(array('category' => $row['category'], 'is_system' => 0, 'uid' => array('neq', $uid)));
        if ($chk_other) {
            $rt = $row->delete();
        } else {
            $row->item_code = '';
            $row->item_name = '';
            $row->item_desc = '';
            $row->item_value = '';
            $rt = $row->update();
        }

        if ($rt->STS) {
            return new result(true, 'Remove Successful!');
        } else {
            return new result(false, 'Remove Failure!');
        }
    }

    /**
     * user定义
     * @return result
     */
    public function initUserDefine()
    {
        $user_define = (new userDefineEnum())->Dictionary();
        $conn = ormYo::Conn();
        $conn->startTransaction();

        $category_arr = array_keys($user_define);
        if ($category_arr) {
            $sql = "DELETE FROM core_definition WHERE category NOT IN ('" . implode("','", $category_arr) . "') AND is_system = 0";
            $rt = $this->conn->execute($sql);
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }
        } else {
            $rt = $this->delete(array('is_system' => 0));
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }
        }

        foreach ($user_define as $key => $define) {
            $chk_init = $this->find(array('category' => $key, 'is_system' => 0));
            if ($chk_init) continue;
            $row = $this->newRow();
            $row->category = $key;
            $row->category_name = ucwords(strtolower($define));
            $row->item_code = '';
            $row->is_system = 0;
            $rt = $row->insert();
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }
        }
        $conn->submitTransaction();
        return new result(true);
    }


    public function getItemNameByLang($category,$item_code,$lang)
    {
        $row = $this->find(array(
            'category' => $category,
            'item_code' => $item_code
        ));
        if( $row ){
            $name_json = @json_decode($row['item_name_json'],true);
            return $name_json[$lang]?:$row['item_name'];
        }
        return '';
    }
}
