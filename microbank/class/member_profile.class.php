<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/20
 * Time: 11:27
 */
class member_profileClass
{
    public $profile_type = array(
        certificationTypeEnum::ID,
        certificationTypeEnum::FAIMILYBOOK,
        certificationTypeEnum::RESIDENT_BOOK,
        certificationTypeEnum::PASSPORT,
        certificationTypeEnum::BIRTH_CERTIFICATE
    );

    protected $cert_sample_image;
    protected $type_icon;

    public function __construct()
    {
        $this->cert_sample_image = $this->getProfileSampleImage();
        $this->type_icon = global_settingClass::getCertTypeIcon();
    }

    public function getProfileSampleImage()
    {
        $data = global_settingClass::getCertSampleImage();
        return $data;
    }

    public function getImageListByType($type)
    {
        $image_list = array();
        $set_list = $this->cert_sample_image[$type];
        foreach ($set_list as $key => $value) {

            $image_list[] = array(
                'field_name' => $key,
                'filed_label' => $value['des'],
                'is_required' => $value['is_required'] ?: 0,
                'sample_image' => $value['image']
            );
        }
        return $image_list;
    }



    public function getInitPageData()
    {
        $data = array(
            certificationTypeEnum::ID => $this->getInitPageDataOfId(),
            certificationTypeEnum::FAIMILYBOOK => $this->getInitPageDataOfFamilyBook(),
            certificationTypeEnum::RESIDENT_BOOK => $this->getInitPageDataOfResidentBook(),
            certificationTypeEnum::PASSPORT => $this->getInitPageDataOfPassport(),
            certificationTypeEnum::BIRTH_CERTIFICATE => $this->getInitPageDataOfBirthday()
        );
        return $data;
    }

    public function getInitPageDataOfId()
    {
        $type = certificationTypeEnum::ID;
        $common_filed = array(
            array(
                'field_name' => 'cert_name',
                'field_label' => L('cert_id_en_name'),
                'field_type' => 'input',
                'value_type' => 'string',
                'select_list' => '',
                'is_required' => 1,
            ),
            array(
                'field_name' => 'cert_name_kh',
                'field_label' => L('cert_id_kh_name'),
                'field_type' => 'input',
                'value_type' => 'string',
                'select_list' => '',
                'is_required' => 0,
            ),
            array(
                'field_name' => 'cert_sn',
                'field_label' => L('cert_id_sn'),
                'field_type' => 'input',
                'value_type' => 'string',
                'select_list' => '',
                'is_required' => 1,
            ),

        );
        $image_list = $this->getImageListByType($type);
        $data = array(
            'type' => $type,
            'type_name' => L('certification_id'),
            'type_icon' => $this->type_icon[$type],
            'is_need_map' => 0,
            'input_field_list' => $common_filed,
            'upload_image_list' => $image_list
        );
        return $data;
    }

    public function getInitPageDataOfFamilyBook()
    {
        $type = certificationTypeEnum::FAIMILYBOOK;
        $common_filed = null;
        $image_list = $this->getImageListByType($type);
        $data = array(
            'type' => $type,
            'type_name' => L('certification_family_book'),
            'type_icon' => $this->type_icon[$type],
            'is_need_map' => 0,
            'input_field_list' => $common_filed,
            'upload_image_list' => $image_list
        );
        return $data;
    }

    public function getInitPageDataOfResidentBook()
    {
        $type = certificationTypeEnum::RESIDENT_BOOK;
        $common_filed = null;
        $image_list = $this->getImageListByType($type);
        $data = array(
            'type' => $type,
            'type_name' => L('certification_resident_book'),
            'type_icon' => $this->type_icon[$type],
            'is_need_map' => 0,
            'input_field_list' => $common_filed,
            'upload_image_list' => $image_list
        );
        return $data;
    }

    public function getInitPageDataOfPassport()
    {
        $type = certificationTypeEnum::PASSPORT;
        $common_filed = null;
        $image_list = $this->getImageListByType($type);
        $data = array(
            'type' => $type,
            'type_name' => L('certification_passport'),
            'type_icon' => $this->type_icon[$type],
            'is_need_map' => 0,
            'input_field_list' => $common_filed,
            'upload_image_list' => $image_list
        );
        return $data;
    }

    public function getInitPageDataOfBirthday()
    {
        $type = certificationTypeEnum::BIRTH_CERTIFICATE;
        $common_filed = null;
        $image_list = $this->getImageListByType($type);
        $data = array(
            'type' => $type,
            'type_name' => L('certification_birthday'),
            'type_icon' => $this->type_icon[$type],
            'is_need_map' => 0,
            'input_field_list' => $common_filed,
            'upload_image_list' => $image_list
        );
        return $data;
    }

    public  function getMemberPersonalCertAndInitData($member_id)
    {
        $file_data = $this->getInitPageData();
        $file_data = array_values($file_data);
        $rt = memberClass::getMemberCertStateOrCount($member_id);
        $member_cert_result = $rt->DATA;

        $cert_detail = memberClass::getAllCertDetail($member_id);
        $cert_detail = $cert_detail->DATA;
        foreach( $file_data as $key=>$item ){
            $item['member_cert_result'] = isset($member_cert_result[$item['type']])?$member_cert_result[$item['type']]:-10;
            $item['cert_expire_time'] = $cert_detail[$item['type']]['cert_expire_time'];
            $file_data[$key] = $item;
        }
        return $file_data;
    }

    public static function submitPersonalFileCert($params,$source=certSourceTypeEnum::MEMBER)
    {

        $type = $params['type'];
        switch( $type ){
            case certificationTypeEnum::ID:
                return memberClass::idVerifyCertNew($params,$source);
                break;
            case certificationTypeEnum::FAIMILYBOOK:
                return memberClass::familyBookVerifyCertNew($params,$source);
                break;
            case certificationTypeEnum::RESIDENT_BOOK:
                return memberClass::residentBookCertNew($params,$source);
                break;
            case certificationTypeEnum::PASSPORT:
                return memberClass::passportCertNew($params,$source);
                break;
            case certificationTypeEnum::BIRTH_CERTIFICATE:
                return memberClass::birthdayCertNew($params,$source);
                break;
            default:
                return new result(false,'Unknown type',null,errorCodesEnum::NOT_SUPPORTED);
        }

    }

    public static function submitPersonalFileCertV2($params,$source=certSourceTypeEnum::MEMBER)
    {
        $type = $params['type'];
        switch( $type ){
            case certificationTypeEnum::ID:
                return memberV2Class::idVerifyCertV2($params,$source);
                break;
            case certificationTypeEnum::FAIMILYBOOK:
                return memberV2Class::familyBookVerifyCertV2($params,$source);
                break;
            case certificationTypeEnum::RESIDENT_BOOK:
                return memberV2Class::residentBookVerifyCertV2($params,$source);
                break;
            case certificationTypeEnum::PASSPORT:
                return memberV2Class::passportVerifyCertV2($params,$source);
                break;
            case certificationTypeEnum::BIRTH_CERTIFICATE:
                return memberV2Class::birthdayVerifyCertV2($params,$source);
                break;
            default:
                return new result(false,'Unknown type:'.$type,null,errorCodesEnum::NOT_SUPPORTED);
        }
    }


    public static function changeTradingPassWordByOldPassword($member_id,$old_pwd,$new_pwd)
    {
        if( !$member_id || !$old_pwd || !$new_pwd ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $memberObj = new objectMemberClass($member_id);
        $chk = $memberObj->checkTradingPassword($old_pwd);
        if(  !$chk->STS ){
            return $chk;
        }

        // 新旧密码一致
        if( $member->trading_password == md5($new_pwd) ){
            return new result(false,'Same trading password.',null,errorCodesEnum::SAME_PASSWORD);
        }

        $member->trading_password = md5($new_pwd);
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Set fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'trading_password' => $new_pwd
        ));
    }


    public static function setTradingPasswordBySms($member_id,$sms_id,$sms_code,$trading_password)
    {
        if( !$member_id  || !$trading_password || !$sms_id || !$sms_code){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }


        // 验证短信验证码
        $rt = (new phone_verify_codeModel())->verifyCode($sms_id,$sms_code);
        if( !$rt->STS ){
            return $rt;
        }

        // 新旧密码一致
        if( $member->trading_password == md5($trading_password) ){
            return new result(false,'Same trading password.',null,errorCodesEnum::SAME_PASSWORD);
        }

        $member->trading_password = md5($trading_password);
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Set fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'trading_password' => $trading_password
        ));

    }


    public static function addCommonAddress($params)
    {
        $member_id = $params['member_id'];
        $member = (new memberModel())->getRow($member_id);
        if( !$member ){
            return new result(false,'Member not exist',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $id1 = intval($params['id1']);
        $id2 = intval($params['id2']);
        $id3 = intval($params['id3']);
        $id4 = intval($params['id4']);
        $full_text = $params['full_text'];

        $m_address = new common_addressModel();
        $new_row = $m_address->newRow();
        $new_row->obj_type = objGuidTypeEnum::CLIENT_MEMBER;
        $new_row->obj_guid = $member->obj_guid;
        $new_row->id1 = $id1;
        $new_row->id2 = $id2;
        $new_row->id3 = $id3;
        $new_row->id4 = $id4;
        $new_row->full_text = $full_text;
        $new_row->create_time = Now();
        $insert = $new_row->insert();
        if( !$insert->STS ){
            return new result(false,'Add fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$new_row);
    }

    public static function setGesturePassword($member_id,$gesture_pwd)
    {
        if( !$member_id || !$gesture_pwd ){
            return new result(false,'Invalid Param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $member->gesture_password = $gesture_pwd;
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Set fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$member);
    }

    public static function forgotGesturePassword($member_id)
    {
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $member->gesture_password = null;
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new  result(false,'Reset fail',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success',array(
            'gesture_password' => $member['gesture_password']
        ));
    }

    public static function setTradingPasswordVerifyAmount($member_id,$amount,$currency)
    {
        if( !$member_id || !$amount || !$currency ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);

        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        if( !$member->trading_password ){
            return new result(false,'Not set trading password',null,errorCodesEnum::NOT_SET_TRADING_PASSWORD);
        }

        $member->trading_verify_amount = round($amount,2);
        $member->trading_verify_currency = $currency;
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Db error',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true);
    }


    public static function setTradingPasswordByLoginPasswordAndIdSn($params)
    {
        $member_id = $params['member_id'];
        $trading_password = $params['trading_password'];
        $login_password = $params['login_password'];
        $id_no = $params['id_no'];
        if( !$member_id || !$trading_password || !$login_password || !$id_no ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 验证登陆密码
        if( $member->login_password != md5($login_password) ){
            return new result(false,'Login password error',null,errorCodesEnum::PASSWORD_ERROR);
        }

        // 验证身份证号
        if( !$member->id_sn ){
            return new result(false,'Not certificate ID',null,errorCodesEnum::NOT_CERTIFICATE_ID);
        }
        $last_no = substr($member->id_sn,-4);
        if( $last_no != $id_no ){
            return new result(false,'ID sn error',null,errorCodesEnum::ID_SN_ERROR);
        }

        // 两次密码是否一致
        if( $member->trading_password ){
            if( $member->trading_password == md5($trading_password) ){
                return new result(false,'Same password',null,errorCodesEnum::SAME_PASSWORD);
            }
        }

        $member->trading_password = md5($trading_password);
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Set fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'trading_password' => $trading_password
        ));

    }

    public static function setFingerprintPassword($member_id,$fingerprint)
    {
        if( !$member_id || !$fingerprint ){
            return new result(false,'Invalid Param',null,errorCodesEnum::INVALID_PARAM);
        }
        // url解码
        $fingerprint = urldecode($fingerprint);

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $member->fingerprint = $fingerprint;
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Set fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'fingerprint' => $fingerprint
        ));
    }

    public static function editAvatar($member_id,$files)
    {
        $member_id = intval($member_id);
        if(  empty($files['avator']) ){
            return new result(false,'No upload image',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }


        $default_dir = fileDirsEnum::MEMBER_AVATOR;
        $upload = new UploadFile();
        $upload->set('save_path',null);
        $upload->set('default_dir',$default_dir);
        $re = $upload->server2upun('avator');
        if( $re == false ){
            return new result(false,'Upload photo fail',null,errorCodesEnum::API_FAILED);
        }
        $img_path = $upload->img_url;


        $m_request = new member_change_photo_requestModel();


        // 先查询是否有申请
        $old = $m_request->getRow(array(
            'member_id' => $member_id,
            'state' => commonApproveStateEnum::CREATE
        ));
        if( $old ){
            $old->old_image = $member['member_image'];
            $old->new_image = $img_path;
            $old->update_time = Now();
            $up = $old->update();
            if( !$up->STS ){
                return new result(false,'Update fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
            }
        }else{

            // 只是插入一条申请
            $request = $m_request->newRow();
            $request->member_id = $member_id;
            $request->old_image = $member['member_image'];
            $request->new_image = $img_path;
            $request->state = commonApproveStateEnum::CREATE;
            $request->create_time = Now();
            $insert = $request->insert();
            if( !$insert->STS ){
                return new result(false,'Request fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true,'success');

    }

    public static function lockMember($member_id)
    {
        $member_id = intval($member_id);
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'No member',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $member_property = my_json_decode($member->member_property);
        $member_property['original_member_state'] = $member->member_state;
        // 修改会员状态为lock 保存当前状态
        $member->member_state = memberStateEnum::TEMP_LOCKING;
        $member->member_property = json_encode($member_property);
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Edit fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true);
    }

    public static function changeTradingPasswordRequest($params)
    {
        $member_id = $params['member_id'];
        $new_password = $params['new_password'];
        $member_image = $params['member_image'];
        $sign = $params['sign'];
        if( !$member_id || !$new_password || !$member_image ){
            return new result(false,'Invalid params.',null,errorCodesEnum::INVALID_PARAM);
        }
        $memberObj = new objectMemberClass($member_id);

        // 检查旧的交易密码
        $self_sign = md5($member_id.$memberObj->trading_password);
        $chk = $memberObj->checkTradingPasswordSign($sign,$self_sign,'Change trading password request');
        if( !$chk->STS ){
            return $chk;
        }

        $m_request = new member_change_trading_password_requestModel();

        $request = $m_request->newRow();
        $request->member_id = $member_id;
        $request->member_image = $member_image;
        $request->new_password = $new_password;
        $request->state = commonApproveStateEnum::CREATE;
        $request->create_time = Now();
        $insert = $request->insert();
        if( !$insert->STS ){
            return new result(false,'Add request fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$request);
    }

}