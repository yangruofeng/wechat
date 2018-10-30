<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/5
 * Time: 9:38
 */
class member_deviceControl extends bank_apiControl
{

    public function scanVerifyLoginDeviceOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $scan_member_id = $params['member_id'];
        $verify_member_id = $params['auth_member_id'];
        $device_id = $params['device_id'];
        $device_name = $params['device_name']?:'Unknown device';
        $registration_id = $params['registration_id'];
        return member_deviceClass::scanAuthMemberLoginDeviceId($scan_member_id,$verify_member_id,$device_id,$device_name,$registration_id);

    }

    public function getMemberDeviceListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);
        $guid = memberClass::getGUIDByMemberId($member_id);
        $m = new common_device_listModel();
        $list = $m->getTrustDeviceListByGUID($guid);
        return new result(true,'success',array(
            'list' => $list
        ));
    }

    public function deleteDeviceOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);
        $local_device_id = $params['local_device_id'];
        $delete_device_id = $params['delete_device_id'];
        if( $local_device_id == $delete_device_id ){
            return new result(false,'Un deletable.',null,errorCodesEnum::UN_DELETABLE);
        }
        return member_deviceClass::deleteTrustDevice($member_id,$delete_device_id);

    }

    public function addNewDeviceApplyOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        return member_deviceClass::addNewDeviceApply($params);

    }


}