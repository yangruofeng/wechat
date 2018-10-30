<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/9
 * Time: 13:40
 */
class member_credit_requestModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_credit_request');
    }


    public function isCanAddNewRequest($member_id)
    {

        $is_can_add = 1;
        // 判断是否存在未处理完成的请求
        $sql = "select count(*) cnt from member_credit_request where member_id=" . qstr($member_id) . " and state>='" . creditRequestStateEnum::CREATE . "'
        and state<'" . creditRequestStateEnum::DONE . "' ";
        $num = $this->reader->getOne($sql);
        if ($num > 0) {
            $is_can_add = 0;
        }
        return $is_can_add;
    }

    public function getMemberRequestList($member_id,$filter=array())
    {
        $member_id = intval($member_id);
        $where = '';
        if( isset($filter['type']) ){

            if( $filter['type'] == 1 ){
                $where .= " and state=".qstr(creditRequestStateEnum::DONE);
            }else{
                $where .= " and state!=".qstr(creditRequestStateEnum::DONE);
            }
        }
        $sql = "select * from member_credit_request where member_id='$member_id' $where order by uid desc ";
        $list = $this->reader->getRows($sql);
        return $list;
    }



    public function getMemberRequestListAndRelative($member_id,$filter=array())
    {
        $list = $this->getMemberRequestList($member_id,$filter);
        //  relative_list
        foreach( $list as $k=>$v ){
            $relative_list = $this->getRelativeListByRequestId($v['uid']);
            $v['relative_list'] = $relative_list;
            $list[$k] = $v;
        }
        return $list;
    }

    public function getRelativeListByRequestId($request_id,$is_full_image_url=true)
    {
        $request_id = intval($request_id);
        $sql = "select * from member_credit_request_relative where request_id='$request_id' order by `name` asc";
        $list = $this->reader->getRows($sql);

        if( $is_full_image_url ){
            foreach ($list as $k => $v) {
                $v['headshot'] = getImageUrl($v['headshot']);
                $v['id_front_image'] = getImageUrl($v['id_front_image']);
                $v['id_back_image'] = getImageUrl($v['id_back_image']);
                $list[$k] = $v;
            }
        }
        return $list;
    }


    public function getNewestRequest($member_id)
    {
        return $this->orderBy('uid desc')->find(array(
            'member_id' => $member_id
        ));
    }

}