<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/30
 * Time: 10:25
 */
class member_creditModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('member_credit');
    }

    /**
     * 获取用户信用
     */
    public function getMemberCreditInfo($member_id){
        $sql = "select * from member_credit where member_id = '$member_id'";
        $info = $this->reader->getRow($sql);
        return $info;  
    }
}