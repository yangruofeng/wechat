<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/30
 * Time: 17:51
 */
class um_user_groupModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('um_user_group');

    }
    public  function getListByGroupKey($group_key){
        $rows=$this->reader->getRows( "select a.uid,a.user_id,b.user_code,b.user_name from um_user_group a "
            ."join um_user b on a.user_id=b.uid where a.group_key=".qstr($group_key)." and b.user_status>=1");
        return $rows;
    }


}