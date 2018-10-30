<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class common_civ_ext_typeModel extends tableModelBase
{
    public function  __construct()
    {
        parent::__construct('common_civ_ext_type');
    }

    /**
     * 添加branch
     * @param $p
     * @return result
     */
    public function addExtType($p)
    {
        $ext_type = intval($p['ext_type']);
        $trade_type = trim($p['trade_type']);
        $creator_id = intval($p['creator_id']);
        $creator_name = trim($p['creator_name']);
        if (empty($trade_type)) {
            return new result(false, 'Trade type cannot be empty!');
        }

        $chk_code = $this->getRow(array('trade_type' => $trade_type));
        if ($chk_code) {
            return new result(false, 'Type already existed!');
        }


        $row = $this->newRow();
        $row->ext_type = $ext_type;
        $row->trade_type = $trade_type;
        $row->creator_id = $creator_id;
        $row->creator_name = $creator_name;
        $row->create_time = Now();
        $row->update_time = Now();
        $rt = $row->insert();
        if (!$rt->STS) {
            return new result(false, 'Add failed1--' . $rt->MSG);
        } else {
            return new result(true, 'Add successful!');
        }
    }

    public function editExtType($p)
    {
        $uid = intval($p['uid']);
        $ext_type = intval($p['ext_type']);
        $trade_type = trim($p['trade_type']);
        if (empty($trade_type)) {
            return new result(false, 'Trade type cannot be empty!');
        }

        $chk_code = $this->getRow(array('trade_type' => $trade_type, 'uid' => array('neq', $uid)));
        if ($chk_code) {
            return new result(false, 'Type already existed!');
        }

        $row = $this->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id.');
        }
        $row->ext_type = $ext_type;
        $row->trade_type = $trade_type;
        $row->update_time = Now();
        $rt = $row->update();
        if (!$rt->STS) {
            return new result(false, 'Edit failed1--' . $rt->MSG);
        } else {
            return new result(true, 'Edit successful!');
        }
    }
    public static function getExtInType(){
        $m=new common_civ_ext_typeModel();
        $list=$m->select(array("ext_type"=>flagTypeEnum::INCOME));
        $list=resetArrayKey($list,"uid");
        return $list;
    }
    public static function getExtOutType(){
        $m=new common_civ_ext_typeModel();
        $list=$m->select(array("ext_type"=>flagTypeEnum::PAYOUT));
        $list=resetArrayKey($list,"uid");
        return $list;
    }


}
