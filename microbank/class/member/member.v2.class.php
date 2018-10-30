<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/9/20
 * Time: 10:26
 */
class memberV2Class extends memberClass
{

    public static function idVerifyCertV2($params, $source = certSourceTypeEnum::MEMBER)
    {
        $cert_type = certificationTypeEnum::ID;

        $member_id = intval($params['member_id']);
        $en_name = $params['cert_name'];
        $kh_name = $params['cert_name_kh'];
        $cert_sn = $params['cert_sn'];
        $cert_files = $params['image_list'];
        $user_id = $params['user_id'];
        if( $user_id ){
            $user_info = (new um_userModel())->getUserInfoById($user_id);
            if( !$user_info ){
                return new result(false,'No found user:'.$user_id,null,errorCodesEnum::INVALID_PARAM);
            }
            $creator_id = $user_info['uid'];
            $creator_name = $user_info['user_name'];
        }else{
            $creator_id = 0;
            $creator_name = 'System';
        }

        $image_arr = array();
        $image_key_url = array();
        foreach( $cert_files as $image ){
            if( !$image['image_url'] ){
                continue;
            }
            $image_key_url[$image['image_key']] = $image['image_url'];
            $image_arr[] = array(
                'image_key' => $image['image_key'],
                'image_url' => $image['image_url'],
                'image_sha' => null,  // todo 暂时取消后台的生成，考虑前端生成
                'image_source' => intval($image['image_source']),
            );
        }

        $page_data = (new member_profileClass())->getInitPageData()[$cert_type];
        if (!empty($page_data['input_field_list'])) {
            foreach ($page_data['input_field_list'] as $item) {
                if ($item['is_required'] && !$params[$item['field_name']]) {
                    return new result(false, 'Empty param:' . $item['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }

        if (!empty($page_data['upload_image_list'])) {
            foreach ($page_data['upload_image_list'] as $item) {
                if ($item['is_required'] && empty($image_key_url[$item['field_name']]) ) {
                    return new result(false, 'Invalid param:' . $item['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }


        $name_json = json_encode(array(
            'en' => $en_name,
            'kh' => $kh_name,
            'zh_cn' => ''
        ));

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 检查是否被他人认证过
        $sql = "select * from member_verify_cert where member_id!='$member_id' and cert_type='" . $cert_type . "'
        and cert_sn='$cert_sn' and verify_state='" . certStateEnum::PASS . "'  order by uid desc";
        $other = $m_member->reader->getRow($sql);
        if ($other) {
            return new result(false, 'ID has been certificated', null, errorCodesEnum::ID_SN_HAS_CERTIFICATED);
        }




        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();


        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => $cert_type
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {
            // 删除图片
            $sql = "delete from member_verify_cert_image where cert_id=" . qstr($o_cert_row['uid']);
            $del = $m_cert->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Delete image fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
            }

            // 删除记录
            $del = $o_cert_row->delete();
            if (!$del->STS) {
                return new result(false, 'Delete cert row fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
            }


        }

        //更新原来通过的为过期状态
        $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . $cert_type . "' and verify_state='" . certStateEnum::PASS . "'  ";
        $up = $m_cert->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Update history cert fail', null, errorCodesEnum::DB_ERROR);
        }

        // 添加新记录
        $new_row = $m_cert->newRow();
        $new_row->member_id = $member_id;
        $new_row->cert_type = $cert_type;
        $new_row->cert_name = $en_name;
        $new_row->cert_name_json = $name_json;
        $new_row->cert_sn = $cert_sn;
        $new_row->verify_state = certStateEnum::CREATE;
        $new_row->source_type = $source;
        $new_row->creator_id = $creator_id;
        $new_row->creator_name = $creator_name;
        $new_row->create_time = Now();
        $insert = $new_row->insert();
        if (!$insert->STS) {
            return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
        }

        $cert_id = $new_row->uid;

        // 一次性插入图片
        if( !empty($image_arr) ){
            $sql = "insert into member_verify_cert_image(cert_id,image_key,image_url,image_sha,image_source,creator_id,creator_name) values    ";
            $values_arr = array();
            foreach( $image_arr as $value ){
                $temp = array(
                    qstr($cert_id),
                    qstr($value['image_key']),
                    qstr($value['image_url']),
                    qstr($value['image_sha']),
                    qstr($value['image_source']),
                    qstr($creator_id),
                    qstr($creator_name)
                );

                $values_arr[] = "(".implode(',',$temp).")";
            }
            $sql .= implode(',',$values_arr);
            $insert = $m_cert->conn->execute($sql);
            if( !$insert->STS ){
                return new result(false,'Insert cert image fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true, 'success', array(
            'cert_result' => $new_row,
            'extend_info' => null
        ));


    }


    public static function commonProfileVerifyCert($params, $source = certSourceTypeEnum::MEMBER)
    {
        $cert_type = $params['type'];
        $member_id = intval($params['member_id']);
        $cert_files = $params['image_list'];
        $user_id = $params['user_id'];
        if( $user_id ){
            $user_info = (new um_userModel())->getUserInfoById($user_id);
            if( !$user_info ){
                return new result(false,'No found user:'.$user_id,null,errorCodesEnum::INVALID_PARAM);
            }
            $creator_id = $user_info['uid'];
            $creator_name = $user_info['user_name'];
        }else{
            $creator_id = 0;
            $creator_name = 'System';
        }

        $image_arr = array();
        $image_key_url = array();
        foreach( $cert_files as $image ){
            if( !$image['image_url'] ){
                continue;
            }
            $image_key_url[$image['image_key']] = $image['image_url'];
            $image_arr[] = array(
                'image_key' => $image['image_key'],
                'image_url' => $image['image_url'],
                'image_sha' => null,  // todo 暂时取消后台的生成，考虑前端生成
                'image_source' => intval($image['image_source']),
            );
        }

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $page_data = (new member_profileClass())->getInitPageData()[$cert_type];
        if (!empty($page_data['input_field_list'])) {
            foreach ($page_data['input_field_list'] as $item) {
                if ($item['is_required'] && !$params[$item['field_name']]) {
                    return new result(false, 'Empty param:' . $item['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }

        if (!empty($page_data['upload_image_list'])) {
            foreach ($page_data['upload_image_list'] as $item) {
                if ($item['is_required'] && empty($image_key_url[$item['field_name']]) ) {
                    return new result(false, 'Empty param:' . $item['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }


        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();


        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'cert_type' => $cert_type
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {
            // 删除图片
            $sql = "delete from member_verify_cert_image where cert_id=" . qstr($o_cert_row['uid']);
            $del = $m_cert->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Delete image fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
            }

            // 删除记录
            $del = $o_cert_row->delete();
            if (!$del->STS) {
                return new result(false, 'Delete cert row fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
            }


        }

        //更新原来通过的为过期状态
        $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $member_id . "'
                 and cert_type='" . $cert_type . "' and verify_state='" . certStateEnum::PASS . "'  ";
        $up = $m_cert->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Update history cert fail:'.$up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        // 添加新记录
        $new_row = $m_cert->newRow();
        $new_row->member_id = $member_id;
        $new_row->cert_type = $cert_type;
        $new_row->verify_state = certStateEnum::CREATE;
        $new_row->source_type = $source;
        $new_row->creator_id = $creator_id;
        $new_row->creator_name = $creator_name;
        $new_row->create_time = Now();
        // 动态字段
        if (!empty($page_data['input_field_list'])) {
            foreach ($page_data['input_field_list'] as $item) {
                $new_row[$item['field_name']] = $params[$item['field_name']];
            }
        }

        $insert = $new_row->insert();
        if (!$insert->STS) {
            return new result(false, 'Add cert fail:'.$insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        $cert_id = $new_row->uid;

        // 一次性插入图片
        if( !empty($image_arr) ){
            $sql = "insert into member_verify_cert_image(cert_id,image_key,image_url,image_sha,image_source,creator_id,creator_name) values    ";
            $values_arr = array();
            foreach( $image_arr as $value ){
                $temp = array(
                    qstr($cert_id),
                    qstr($value['image_key']),
                    qstr($value['image_url']),
                    qstr($value['image_sha']),
                    qstr($value['image_source']),
                    qstr($creator_id),
                    qstr($creator_name)
                );

                $values_arr[] = "(".implode(',',$temp).")";
            }
            $sql .= implode(',',$values_arr);
            $insert = $m_cert->conn->execute($sql);
            if( !$insert->STS ){
                return new result(false,'Insert cert image fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true, 'success', array(
            'cert_result' => $new_row,
            'extend_info' => null
        ));
    }


    public static function familyBookVerifyCertV2($params, $source = certSourceTypeEnum::MEMBER)
    {
        $params['type'] = certificationTypeEnum::FAIMILYBOOK;
        return self::commonProfileVerifyCert($params,$source);
    }

    public static function residentBookVerifyCertV2($params, $source = certSourceTypeEnum::MEMBER)
    {
        $params['type'] = certificationTypeEnum::RESIDENT_BOOK;
        return self::commonProfileVerifyCert($params,$source);
    }

    public static function passportVerifyCertV2($params, $source = certSourceTypeEnum::MEMBER)
    {
        $params['type'] = certificationTypeEnum::PASSPORT;
        return self::commonProfileVerifyCert($params,$source);
    }

    public static function birthdayVerifyCertV2($params, $source = certSourceTypeEnum::MEMBER)
    {
        $params['type'] = certificationTypeEnum::BIRTH_CERTIFICATE;
        return self::commonProfileVerifyCert($params,$source);
    }


}