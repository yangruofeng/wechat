<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/20
 * Time: 18:00
 */
class test_owenModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('test_owen');
    }

    /*
     * 获取用户列表  model
     * @param  $page_num   当前页数
     * @Param  $page_size  每页条数
     * @return array
     * @return total 总条数
     * @return pageNumber  当前页数
     * @return pageSize    每页多少条
     * @return data 数据
     */
    public  function getUserList($page_num,$page_size){
        $sql  = "SELECT * FROM test_user ";
        $r    = new ormReader();
        $page = $r->getPage($sql,$page_num,$page_size);

        $rows = $page->rows;

        $return = array(
            'total' => $page->count,
            //'total_pages' => $page->pageCount,
            'pageNumber' => $page_num,
            'pageSize' => $page_size,
            'data' => $rows
        );

        return $return;

    }

}