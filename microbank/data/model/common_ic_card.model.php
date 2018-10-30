<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2018/4/3
 * Time: 18:05
 */

class common_ic_cardModel extends tableModelBase
{

    public function __construct()
    {
        parent::__construct('common_ic_card');
    }

    public function searchCardListByFreeText($searchText, $pageNumber, $pageSize) {
        if ($searchText) {
            $where = "card_no like '%" . qstr2($searchText) . "%' or create_user_name like '%" . qstr2($searchText) . "%'";
            $condition = array();
            $condition[] = new ormParameter($this->name, "sql", $where,  "sql");
        } else {
            $condition = null;
        }

        $rows = $this->getPage($pageNumber, $pageSize, $condition, null);
        $total = $this->totalCount;
        $pageTotal = $this->currentPage;

        return new result(true, null, array(
            'rows' => $rows,
            'total' => $total,
            'page_total' => $pageTotal
        ));
    }
}