<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/10
 * Time: 9:38
 */
class savings_category_templateModel extends tableModelBase
{
    private $table_name = 'savings_category_template';

    function __construct()
    {
        parent::__construct($this->table_name);
    }

    /**
     * 获取模板
     * @param $category_id
     * @return bool|mixed
     */
    public function getTempByCategoryId($category_id)
    {
        $temp = $this->find(array('category_id' => $category_id));
        return $temp;
    }
}