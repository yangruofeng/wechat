<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/30
 * Time: 17:41
 */
class common_device_listModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('common_device_list');
    }

    public function getTotalDeviceNumByGuid($guid)
    {
        $sql = "select count(*) cnt from common_device_list where obj_guid=".qstr($guid);
        return $this->reader->getOne($sql);
    }

    public function getTrustDeviceListByGUID($guid)
    {
        $sql = "select * from common_device_list where obj_guid=".qstr($guid)." and is_trust=1 ";
        return $this->reader->getRows($sql);
    }
}