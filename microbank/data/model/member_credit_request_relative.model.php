<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/24
 * Time: 15:45
 */
class member_credit_request_relativeModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_credit_request_relative');
    }

    public function updateRelativeIDCertInfo($param)
    {

        $relative = $this->getRow($param['relative_id']);
        if( !$relative ){
            return new result(false,'Invalid param of relative-id:'.$param['relative_id'],null,errorCodesEnum::INVALID_PARAM);
        }


        $id_en_name_json = json_encode(array('en_family_name' => $param['en_family_name'], 'en_given_name' => $param['en_given_name']));
        $id_kh_name_json = json_encode(array('kh_family_name' => $param['kh_family_name'], 'kh_given_name' => $param['kh_given_name']));

        $relative->initials = strtoupper(substr($param['en_family_name'], 0, 1));
        $relative->display_name = $param['en_family_name'] . ' ' . $param['en_given_name'];
        $relative->kh_display_name = $param['kh_family_name'] . ' ' . $param['kh_given_name'];
        $relative->id_sn = $param['cert_sn'];
        $relative->id_type = $param['id_type'];
        $relative->nationality = $param['nationality'];
        $relative->id_en_name_json = $id_en_name_json;
        $relative->id_kh_name_json = $id_kh_name_json;
        $relative->id_address1 = intval($param['id_address1']);
        $relative->id_address2 = intval($param['id_address2']);
        $relative->id_address3 = intval($param['id_address3']);
        $relative->id_address4 = intval($param['id_address4']);
        $relative->id_expire_time = $param['cert_expire_time'];
        $relative->address = trim($param['cert_addr']);
        $relative->address_detail = trim($param['cert_addr_detail']);
        if (trim($param['gender'])) {
            $relative->gender = trim($param['gender']);
        }
        if (trim($param['birthday'])) {
            $relative->birthday = trim($param['birthday']);
        }

        $relative->update_time = Now();
        $up = $relative->update();

        if (!$up->STS) {
            return new result(false, 'Update Id Fail.' . $up->MSG);
        }

        return new result(true, 'Update Id Successful.');

    }
}