<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/22
 * Time: 14:12
 */
class member_change_photo_requestModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_change_photo_request');
    }


    public function getPageListByState($branch_id,$page_size,$page_number,$state,$filter=array())
    {
        $where = '';
        if( $filter['keyword'] ){
            $where .= " and (m.login_code like '%".qstr2($filter['keyword'])."%' or m.phone_id like '%".qstr2($filter['keyword'])."%') ";
        }
        $sql = "select r.*,m.login_code,m.phone_id,m.member_image,m.member_icon from member_change_photo_request r left join client_member m on m.uid=r.member_id 
        where r.state=".qstr($state)." $where ";

        return $this->reader->getPage($sql,$page_size,$page_number);
    }
}