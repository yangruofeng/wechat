<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/31
 * Time: 10:16
 */

/** 客户设备管理类
 * Class member_deviceClass
 */
class member_deviceClass
{

    public static function getDeviceRowInfo($guid,$device_id)
    {
        $m_device = new common_device_listModel();
        $row = $m_device->getRow(array(
            'obj_guid' => $guid,
            'device_id' => $device_id
        ));
        return $row;
    }

    public static function deviceIsTrusted($guid,$device_id)
    {
        $m_device = new common_device_listModel();
        $row = $m_device->getRow(array(
            'obj_guid' => $guid,
            'device_id' => $device_id,
            'is_trust' => 1
        ));
        return $row?true:false;
    }

    /** 登陆检查设备
     * @param $member_id
     * @param $device_id
     * @param $device_name
     * @return result
     */
    public static function checkLoginDevice($member_id,$device_id,$device_name)
    {
        $m_device = new common_device_listModel();
        $memberObj = new objectMemberClass($member_id);
        $is_device_need_verify = 0;

        if( $device_id ){

            // 是否第一次登陆
            $num = $m_device->getTotalDeviceNumByGuid($memberObj->object_id);

            if( $num > 0 ){
                // 非第一次登陆
                // 是否在可信任设备内
                if( !self::deviceIsTrusted($memberObj->object_id,$device_id) ){
                    $is_device_need_verify = 1;
                    // 不在信任设备内
                    // 是否登陆过
                    $device_row = self::getDeviceRowInfo($memberObj->object_id,$device_id);
                    if( !$device_row ){
                        // 添加一个设备记录
                        $new_device = $m_device->newRow();
                        $new_device->obj_type = $memberObj->object_type;
                        $new_device->obj_guid = $memberObj->object_id;
                        $new_device->device_id = $device_id;
                        $new_device->device_name = $device_name;
                        $new_device->create_time = Now();
                        $new_device->is_trust = 0;
                        $insert = $new_device->insert();
                        if( !$insert->STS ){
                            return new result(false,'Add new device fail.',null,errorCodesEnum::DB_ERROR);
                        }
                    }

                }

            }else{
                // 第一次登陆
                $is_device_need_verify = 0;
                // 插入一条信任设备记录
                $new_device = $m_device->newRow();
                $new_device->obj_type = $memberObj->object_type;
                $new_device->obj_guid = $memberObj->object_id;
                $new_device->device_id = $device_id;
                $new_device->device_name = $device_name;
                $new_device->create_time = Now();
                $new_device->is_trust = 1;
                $insert = $new_device->insert();
                if( !$insert->STS ){
                    return new result(false,'Add new device fail.',null,errorCodesEnum::DB_ERROR);
                }
            }
        }


        // 信任的设备列表
        $trust_device = $m_device->getTrustDeviceListByGUID($memberObj->object_id);
        return new result(true,'success',array(
            'is_device_need_verify' => $is_device_need_verify,
            'trust_device_list' => $trust_device
        ));
    }


    /** 添加信任设备
     * @param $member_id
     * @param $device_id
     * @param $device_name
     * @return result
     */
    public static function addTrustDevice($member_id,$device_id,$device_name)
    {
        $memberObj = new objectMemberClass($member_id);

        $m_device = new common_device_listModel();
        $device_row = self::getDeviceRowInfo($memberObj->object_id,$device_id);
        if( $device_row ){

            if( $device_row['is_trust'] ){
                return new result(true,'success',$device_row);
            }
            // 不是就更新
            $device_row->is_trust = 1;
            $device_row->update_time = Now();
            $up = $device_row->update();
            if( !$up->STS ){
                return new result(false,'Add fail.',null,errorCodesEnum::DB_ERROR);
            }

            return new result(true,'success',$device_row);

        }else{
            // 插入一条信任设备记录
            $new_device = $m_device->newRow();
            $new_device->obj_type = $memberObj->object_type;
            $new_device->obj_guid = $memberObj->object_id;
            $new_device->device_id = $device_id;
            $new_device->device_name = $device_name;
            $new_device->create_time = Now();
            $new_device->is_trust = 1;
            $insert = $new_device->insert();
            if( !$insert->STS ){
                return new result(false,'Add new device fail.',null,errorCodesEnum::DB_ERROR);
            }
            return new result(true,'success',$new_device);
        }

    }

    public static function scanAuthMemberLoginDeviceId($scan_member_id,$auth_member_id,$device_id,$device_name,$registrationId)
    {
        if( $scan_member_id != $auth_member_id ){
            return new result(false,'Invalid auth.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }
        $rt = self::addTrustDevice($auth_member_id,$device_id,$device_name);
        if( $rt->STS ){

            $registrationId=trim($registrationId);  // 推送到单一设备的方式
            // 推送消息到新设备，完成了认证 todo 只推送到新设备，旧设备不可接收
            $title = 'Device auth success';
            $body = "New device($device_name:$device_id) authorised success.";
            $extra = array(
                'notice_type' => jpushNoticeTypeEnum::SCAN_AUTH_LOGIN_DEVICE_OK,
                'login_device' => $device_id
            );
            member_messageClass::sendPushByDeviceRegistration($auth_member_id,$registrationId,$body,$extra);
        }
        return $rt;
    }


    public static function deleteTrustDevice($member_id,$device_id)
    {
        if( !$member_id || !$device_id ){
            return new result(false,'Invalid param.',null,errorCodesEnum::INVALID_PARAM);
        }
        $memberObj = new objectMemberClass($member_id);
        $m = new common_device_listModel();
        $row = $m->getRow(array(
            'obj_type' => $memberObj->object_type,
            'obj_guid' => $memberObj->object_id,
            'device_id' => $device_id
        ));
        if( !$row ){
            return new result(false,'No device.',null,errorCodesEnum::NO_DATA);
        }
        if( $row->is_trust != 1 ){
            return new result(true,'success');
        }
        $row->is_trust = 0;
        $row->update_time = Now();
        $up = $row->update();
        if( !$up->STS ){
            return new result(false,'Delete fail.',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success');
    }


    public static function addNewDeviceApply($params)
    {
        $member_id = $params['member_id'];
        $device_id = $params['device_id'];
        $device_name = trim($params['device_name']);
        $country_code = $params['country_code'];
        $phone_number = $params['phone_number'];
        $sms_id = $params['sms_id'];
        $sms_code = $params['sms_code'];
        if( !$device_id || !$country_code || !$phone_number || empty($_FILES['member_image']) ){
            return new result(false,'Invalid param.',null,errorCodesEnum::INVALID_PARAM);
        }
        $memberObj = new objectMemberClass($member_id);

        $chk = $memberObj->isCanLogin();
        if( !$chk->STS ){
            return $chk;
        }

        // todo 暂时取消，用户疯狂点，太烧钱
        /*$ck = (new phone_verify_codeModel())->verifyCode($sms_id,$sms_code);
        if( !$ck->STS ){
            return $ck;
        }*/

        $m_apply = new member_new_device_applyModel();

        // 查询是否有申请了
        $apply = $m_apply->getRow(array(
            'member_id' => $member_id,
            'device_id' => $device_id,
            'state' => newDeviceApplyStateEnum::CREATE
        ));
        if( $apply ){
            return new result(true,'success',$apply);
        }

        // 上传头像
        $default_dir = fileDirsEnum::MEMBER_AVATOR;
        $upload = new UploadFile();
        $upload->set('save_path',null);
        $upload->set('default_dir',$default_dir);
        $re = $upload->server2upun('member_image');
        if( $re == false ){
            return new result(false,'Upload photo fail',null,errorCodesEnum::API_FAILED);
        }
        $member_image = $upload->img_url;
        $phone_arr = tools::getFormatPhone($country_code,$phone_number);
        $contact_phone = $phone_arr['contact_phone'];




        $apply = $m_apply->newRow();
        $apply->member_id = $member_id;
        $apply->member_image = $member_image;
        $apply->contact_phone = $contact_phone;
        $apply->device_id = $device_id;
        $apply->device_name = $device_name;
        $apply->create_time = Now();
        $apply->state = newDeviceApplyStateEnum::CREATE;

        $insert = $apply->insert();
        if( !$insert->STS ){
            return new result(false,'Apply fail.',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$apply);
    }

    public static function getDeviceRowInfoByUid($uid)
    {
        $m_device = new member_new_device_applyModel();
        $sql = "select a.*,cm.obj_guid,cm.login_code,cm.display_name,cm.member_icon from member_new_device_apply a left join client_member cm on a.member_id = cm.uid where a.uid = $uid";
        return $m_device->reader->getRow($sql);
    }

    public static function checkNewDeviceApply($params)
    {
        $uid = $params['uid'];
        $state = $params['state'];
        $remark = $params['remark'];
        $operator_id = $params['operator_id'];
        $operator_name = $params['operator_name'];
        $m_device = new member_new_device_applyModel();
        $row = $m_device->getRow(array(
            'uid' => $uid
        ));
        if (!$row) {
            return new result(false, 'Invalid param.', null, errorCodesEnum::INVALID_PARAM);
        }

        //$conn = ormYo::Conn();
        try {
            //$conn->startTransaction();
            $row->operator_id = $operator_id;
            $row->operator_name = $operator_name;
            $row->state = $state;
            $row->remark = $remark;
            $row->update_time = Now();
            $up = $row->update();
            if (!$up->STS) {
               // $conn->rollback();
                return new result(false, 'Checkrd fail', null, errorCodesEnum::DB_ERROR);
            }

            if($state == newDeviceApplyStateEnum::PASS){
                $rt = self::addTrustDevice($row['member_id'],$row['device_id'],$row['device_name']);
                if (!$rt->STS) {
                   // $conn->rollback();
                    return new result(false, 'Add Trust fail.', null, errorCodesEnum::DB_ERROR);
                }
            }
            
           // $conn->submitTransaction();
            return new result(true, 'success');
        } catch (Exception $e) {
            return new result(false, $e->getMessage(), null, errorCodesEnum::DB_ERROR);
        }
    }
}