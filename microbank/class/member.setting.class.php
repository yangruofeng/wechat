<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 8/29/2018
 * Time: 10:32 AM
 */
class memberSettingClass{
    /**
     * 获取一个member的分项权限
     * @param $member_id
     * @return array
     */
    public static function getMemberAuthority($member_id){
        $blackList=(new blackTypeEnum())->Dictionary();
        $arr=array();
        $m_black=new client_blackModel();
        $rows=$m_black->select(array("member_id"=>$member_id));
        $rows=resetArrayKey($rows,"type");
        foreach($blackList as $k=>$item){
            $arr[$k]=array(
                "auth_key"=>$k,
                "auth_text"=>ucwords($item),
                "is_active"=>($rows[$k]?0:1)
            );
        }
        return $arr;
    }
    public static function checkMemberAuthorityByType($member_id,$auth_type){
        $auth_list=self::getMemberAuthority($member_id);
        if(!$auth_list[$auth_type]){
           return true;
        }
        if(!$auth_list[$auth_type]['is_active']){
            return false;
        }else{
            return true;
        }
    }

}