<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/9/17
 * Time: 13:46
 */
class member_change_trading_password_requestModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_change_trading_password_request');
    }

    public function getListOfPage($page_number,$page_size,$filter=array())
    {
        $where = '';
        if( $filter['search_text'] ){
            $where .= " and (cm.obj_guid=".qstr($filter['search_text'])." or cm.phone_id like '%".qstr2($filter['search_text'])."%' 
             or cm.login_code like '%".qstr2($filter['search_text'])."%' ) ";
        }
        if( $filter['state'] !== null ){
            $where .= " and r.state=".qstr($filter['state']);
        }
        $sql = "select r.*,cm.obj_guid,cm.login_code,cm.display_name,cm.phone_id,cm.member_image client_original_image from member_change_trading_password_request r left join client_member cm 
        on cm.uid=r.member_id where 1=1  $where ";
        return $this->reader->getPage($sql,$page_number,$page_size);
    }

    public function getMemberLastRequest($member_id)
    {
        $member_id = intval($member_id);
        $request = $this->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id
        ));
        return $request;
    }

}