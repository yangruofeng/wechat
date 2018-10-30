<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 11:20
 */
class member_cert_logModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_cert_log');
    }

    public function insertCertLog($cert_id,$cert_type = 1)
    {
        $row = $this->newRow();
        $row->cert_type = $cert_type; //1 operator认证  2 co资产评估
        $row->cert_id = $cert_id;
        $row->state = 0;
        $row->create_time = Now();
        $rt = $row->insert();
        return $rt;
    }
}