<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 10:44
 */
class helpControl extends bank_apiControl
{

    public function getHelpCategoryOp()
    {
        $category = (new helpCategoryEnum())->Dictionary();
        return new result(true, '', $category);
    }

    public function getHelpListOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $m_common_cms = M('common_cms');

        $re = $m_common_cms->getHelpList($params);
        return $re;
    }

    public function helpDetailOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $m_common_cms = M('common_cms');
        $uid = intval($params['uid']);
        $re = $m_common_cms->getHelpDetail($uid);
        return $re;
    }
    public function getBranchListOp(){
        $m=new site_branchModel();
        $rows=$m->select(array("status"=>1));
        $data=array();
        foreach($rows as $row){
            $data[]=array(
                "branch_id"=>$row['uid'],
                "branch_name"=>$row['branch_name'],
                "address"=>$row['address_region']
            );
        }
        return new result(true,"",$data);
    }
    public function getBranchItemOp(){
        $params = array_merge(array(), $_GET, $_POST);
        $cls=new branchClass();
        $info=$cls->getBranchInfo($params['branch_id']);
        if(!$info){
            return new result(false,'No Branch Found',null,errorCodesEnum::INVALID_PARAM);
        }
        unset($info['limit_arr']);
        $info['service_time_start']='AM 8:00';
        $info['service_time_end']='PM 5:00';
        return new result(true,"",$info);
    }

}
