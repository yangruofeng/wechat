<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/20
 * Time: 18:00
 */
class biz_civ_adjustModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('biz_civ_adjust');
    }
    public function getHistoryList($flag,$pageNumber,$pageSize=20){
        $sql="select biz.*,ct.trade_type from  biz_civ_adjust biz left join common_civ_ext_type ct on biz.ext_trade_type=ct.uid";
        $sql.=" where biz.flag=".qstr($flag);
        $data=$this->reader->getPage($sql,$pageNumber,$pageSize);
        return $data;
    }
}