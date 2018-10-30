<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/25
 * Time: 13:47
 */
class member_relativeClass
{
    public function __construct()
    {
    }


    public static function getMemberRelativeList($member_id)
    {
        // 只拿最近一次的，原来的可能是作废了
        $member_id = intval($member_id);
        $r = new ormReader();

        $sql = "select * from member_credit_request where member_id='$member_id'
        order by uid desc ";
        $last_request = $r->getRow($sql);
        $request_id = $last_request?$last_request['uid']:0;

        $sql = "select * from member_credit_request_relative where request_id='$request_id'
        order by `name` ";

        return $r->getRows($sql);

    }

    public static function getRelativeCanOperateState($relative_info)
    {
        $is_editable = 1;
        $is_deletable = 1;

        $m_request = new member_credit_requestModel();
        // 是否可编辑
        $request_info = $m_request->getRow($relative_info['request_id']);
        if( $request_info['state'] >= creditRequestStateEnum::GRANTED ){
            $is_editable = 0;
            $is_deletable = 0;
        }

        $relative_id = intval($relative_info['uid']);

        // 是否可删除
        // 有资产关系
        //$sql = "SELECT COUNT(mao.uid) cnt FROM member_assets_owner mao INNER JOIN member_assets ma ON mao.member_asset_id = ma.uid WHERE mao.relative_id='".$relative_info['uid']."' AND ma.mortgage_state='1'";
        $sql = "SELECT COUNT(mao.uid) cnt FROM member_assets_owner mao  WHERE mao.relative_id='".$relative_info['uid']."' ";
        $num = $m_request->reader->getOne($sql);
        if( $num > 0 ){
            $is_deletable = 0;
        }

        // 有商业关系
        $sql = "select count(uid) cnt from member_income_business_owner where relative_id='$relative_id' ";
        $num = $m_request->reader->getOne($sql);
        if( $num > 0 ){
            $is_deletable = 0;
        }
        //有salary
        $sql = "select count(uid) cnt from member_income_salary where relative_id='$relative_id' ";
        $num = $m_request->reader->getOne($sql);
        if( $num > 0 ){
            $is_deletable = 0;
        }

        return array(
            'is_editable' => $is_editable,
            'is_deletable' => $is_deletable
        );
    }

    public static function getProfileCertTypeArr()
    {
        $class_member_profile = new member_profileClass();
        $arr = $class_member_profile->profile_type;
        return $arr;
    }


    public static function getRelativeProfileCertType()
    {
        $data = (new member_profileClass())->getInitPageData();
        //unset($data[certificationTypeEnum::ID]);
        return $data;
    }

    public static function getRelativeProfileCertDataByType($type)
    {
        $data = self::getRelativeProfileCertType();
        return $data[$type];
    }

    public static function getProfileCertTypeAndResult($relative_id=0)
    {
        $cert_type = self::getRelativeProfileCertType();
        // 获取结果
        $type = array_keys($cert_type);
        if( empty($type) ){
            $cert_result = array();
        }else{
            $sql_type = array();
            foreach ($type as $v){
                $sql_type[] = qstr($v);
            }
            $r = new ormReader();
            $sql = "select cr.* from (select * from member_relative_verify_cert order by create_time desc ) cr 
             where cr.cert_type in (".join(',',$sql_type).") and cr.relative_id=".qstr($relative_id)." 
             group by cr.relative_id,cr.cert_type ";
            $list = $r->getRows($sql);
            $cert_result = resetArrayKey($list,'cert_type');
        }
        foreach( $cert_type as $k=>$v ){
            $cert_type[$k]['relative_cert_result'] = $cert_result[$k];
        }
        return $cert_type;
    }


    public static function profileCert($params, $source = certSourceTypeEnum::OPERATOR)
    {
        $cert_type = $params['type'];
        switch ( $cert_type ){
            case certificationTypeEnum::ID:
                return self::idVerifyCert($params,$source);
                break;
            default:
                return self::commonProfileVerifyCert($params,$source);
        }

    }

    public static function idVerifyCert($params, $source = certSourceTypeEnum::OPERATOR)
    {
        $cert_type = $params['type'];
        $relative_id = intval($params['relative_id']);
        $m_relative = new member_credit_request_relativeModel();
        $relative = $m_relative->getRow($relative_id);
        if( !$relative ){
            return new result(false,'No found relative:'.$relative_id,null,errorCodesEnum::INVALID_PARAM);
        }

        $member_id = $relative['member_id']?:0;
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

        $init_cert_data = self::getRelativeProfileCertType();
        $page_data = $init_cert_data[$cert_type];
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


        // 先更新信息到relative上
        //$relative->id_sn = $params['cert_sn'];
        $relative->id_front_image = $image_key_url[certImageKeyEnum::ID_FRONT];
        $relative->id_back_image = $image_key_url[certImageKeyEnum::ID_BACK];
        $relative->update_time = Now();
        $relative->update();


        $m_cert = new member_relative_verify_certModel();

        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'relative_id' => $relative_id,
            'cert_type' => $cert_type
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {
            // 删除图片
            $sql = "delete from member_relative_verify_cert_image where cert_id=" . qstr($o_cert_row['uid']);
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
        $sql = "update member_relative_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where relative_id='" . $relative_id . "'
                 and cert_type='" . $cert_type . "' and verify_state='" . certStateEnum::PASS . "'  ";
        $up = $m_cert->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Update history cert fail:'.$up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        // 添加新记录
        $new_row = $m_cert->newRow();
        $new_row->member_id = $member_id;
        $new_row->relative_id = $relative_id;
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
            // 多一个名字的输入
            $kh_name = $params['cert_name_kh'];
            $en_name = $params['cert_name'];
            $name_json = json_encode(array(
                'en' => $en_name,
                'kh' => $kh_name,
                'zh_cn' => ''
            ));
            $new_row->cert_name_json = $name_json;
        }

        $insert = $new_row->insert();
        if (!$insert->STS) {
            return new result(false, 'Add cert fail:'.$insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        $cert_id = $new_row->uid;

        // 一次性插入图片
        if( !empty($image_arr) ){
            $sql = "insert into member_relative_verify_cert_image(cert_id,image_key,image_url,image_sha,image_source,creator_id,creator_name) values    ";
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

    public static function commonProfileVerifyCert($params, $source = certSourceTypeEnum::OPERATOR)
    {
        $cert_type = $params['type'];

        $support_type = self::getProfileCertTypeArr();
        if( !in_array($cert_type,$support_type) ){
            return new result(false,'Not support cert type:'.$cert_type,null,errorCodesEnum::NOT_SUPPORTED);
        }

        $relative_id = intval($params['relative_id']);
        $m_relative = new member_credit_request_relativeModel();
        $relative = $m_relative->getRow($relative_id);
        if( !$relative ){
            return new result(false,'No found relative:'.$relative_id,null,errorCodesEnum::INVALID_PARAM);
        }

        $member_id = $relative['member_id']?:0;
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

        $init_cert_data = self::getRelativeProfileCertType();
        $page_data = $init_cert_data[$cert_type];
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


        $m_cert = new member_relative_verify_certModel();

        // 查询最后一条认证记录
        $o_cert_row = $m_cert->orderBy('uid desc')->getRow(array(
            'relative_id' => $relative_id,
            'cert_type' => $cert_type
        ));

        if ($o_cert_row && $o_cert_row['verify_state'] == certStateEnum::CREATE) {
            // 删除图片
            $sql = "delete from member_relative_verify_cert_image where cert_id=" . qstr($o_cert_row['uid']);
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
        $sql = "update member_relative_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where relative_id='" . $relative_id . "'
                 and cert_type='" . $cert_type . "' and verify_state='" . certStateEnum::PASS . "'  ";
        $up = $m_cert->conn->execute($sql);
        if (!$up->STS) {
            return new result(false, 'Update history cert fail:'.$up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        // 添加新记录
        $new_row = $m_cert->newRow();
        $new_row->member_id = $member_id;
        $new_row->relative_id = $relative_id;
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
            $sql = "insert into member_relative_verify_cert_image(cert_id,image_key,image_url,image_sha,image_source,creator_id,creator_name) values    ";
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


    public static function getProfileCertResultByType($type,$relative_id)
    {
        $page_data = self::getRelativeProfileCertDataByType($type);
        if( !$page_data ){
            return new result(false,'Un support type.',null,errorCodesEnum::NOT_SUPPORTED);
        }
        // 提交的数据
        $m = new member_relative_verify_certModel();
        $last_cert = $m->getLastCertResultByType($type,$relative_id);

        if( $type == certificationTypeEnum::ID ){
            $name_alias = my_json_decode($last_cert['cert_name_json']);
            $last_cert['cert_name_kh'] = $name_alias['kh'];
        }

        // 格式化page data
        $input_list = $page_data['input_field_list'];
        if( !empty($input_list) ){
            foreach( $input_list as $k=>$v ){
                $input_list[$k]['field_value'] = $last_cert[$v['field_name']];
            }
            $page_data['input_field_list'] = $input_list;
        }

        $image_field_list = $page_data['upload_image_list'];
        if( !empty($image_field_list) ){
            $submit_image = $last_cert['image_list'];
            $submit_image = resetArrayKey($submit_image,'image_key');
            foreach( $image_field_list as $k=>$v ){
                $image_field_list[$k]['fill_image'] = $submit_image[$v['field_name']]['image_url'];
            }
            $page_data['upload_image_list'] = $image_field_list;
        }

        return new result(true,'success',array(
            'page_data' => $page_data,
            'cert_result' => $last_cert
        ));

    }

}