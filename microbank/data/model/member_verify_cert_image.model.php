<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/4
 * Time: 10:05
 */
class member_verify_cert_imageModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_verify_cert_image');
    }

    public function getVerifyCertImagesByIds($ids){
        $sql = "select * from member_verify_cert_image where cert_id IN ($ids);";
        $list = $this->reader->getRows($sql);
        return $list;
    }
}