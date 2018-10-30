<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class common_industry_placeModel extends tableModelBase
{

    public function  __construct()
    {
        parent::__construct('common_industry_place');
    }
    
    public function getIndustryPlaceList($search_text = '',$pageNumber = 1, $pageSize = 20)
    {
        $sql = "SELECT * FROM common_industry_place WHERE 1 = 1 ";
        $search_text = trim($search_text);
        if ($search_text) {
            $sql .= " AND place LIKE '%" . $search_text . "%'";
        }
        $sql .= " ORDER by uid DESC";
        $data = $this->reader->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count?:0;
        $pageTotal = $data->pageCount?:0;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    public function getIndustryPlaceById($uid){
        $info = $this->find(array('uid' => $uid));
        return $info;
    }

    public function addIndustryPlace($params){
        $place = trim($params['place']);
        $remark = trim($params['remark']);
        //$creator_id = intval($p['creator_id']);
        //$creator_name = trim($p['creator_name']);
        if (!$place || !$remark) {
            return new result(false, 'Param Error!');
        }
        $row = $this->newRow();
        $row->place = $place;
        $row->remark = $remark;
        $rt = $row->insert();
        return $rt;
    }

    public function editIndustryPlace($params)
    {
        $uid = intval($params['uid']);
        $place = trim($params['place']);
        $remark = trim($params['remark']);
        if (!$place || !$remark) {
            return new result(false, 'Param Error!');
        }

        $row = $this->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }
        $row->place = $place;
        $row->remark = $remark;
        $rt = $row->update();
        return $rt;
    }

    public function deletendustryPlace($uid){
        $row = $this->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }
        $rt = $row->delete();
        return $rt;
    }
}
