<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 8/15/2015
 * Time: 11:08 PM
 */
class core_gen_idModel extends tableModelBase
{

    public function  __construct()
    {
        $this->is_root_table = true;
        parent::__construct("core_gen_id");
    }

    /*
     * 在需要手工编号时，为了防止并发的一种特别处理方式。
     * */
    public function genId($id_key, $start_number = 1)
    {
        if (!$id_key) return false;
        $sql = "INSERT core_gen_id (id_key,id_num) SELECT " . qstr($id_key) . ",IFNULL((SELECT MAX(id_num)+1 id_num FROM core_gen_id WHERE id_key=" . qstr($id_key) . "),$start_number)";
        $item = $this->conn->execute($sql);
        $uid = $item->AUTO_ID;
        $row = $this->getRow($uid);
        return $row->id_num;
    }
    public static function getGUID($id_key, $start_number = 1){
        $md=new core_gen_idModel();
        return $md->genId($id_key,$start_number);
    }
}