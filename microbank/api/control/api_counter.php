<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/24
 * Time: 10:10
 */

class api_counterControl extends  bank_apiControl
{

    public function setClientMemberFingerprintOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $finger_index = intval($params['finger_index']);
        $feature_img = $params['feature_img'];
        $feature_data = $params['feature_data'];

        if( !$member_id || !$feature_img || !$feature_data ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $m = new common_fingerprint_libraryModel();
        $new_row = $m->newRow();
        $new_row->obj_type = objGuidTypeEnum::CLIENT_MEMBER;
        $new_row->obj_uid = $member_id;
        $new_row->finger_index = $finger_index;
        $new_row->feature_img = $feature_img;
        $new_row->feature_data = $feature_data;
        $new_row->create_time = Now();
        $insert = $new_row->insert();
        if( !$insert->STS ){
            return new result(false,'Set fail',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true);

    }

    public function getClientMemberFingerprintOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $m = new common_fingerprint_libraryModel();
        $finger = $m->orderBy('uid desc')->getRow(array(
            'obj_type' => objGuidTypeEnum::CLIENT_MEMBER,
            'obj_uid' => $member_id
        ));
        if( !$finger ){
            return new result(true,'success',null);
        }
        $return = array(
            'member_id' => $finger->obj_uid,
            'finger_index' => $finger->finger_index,
            'feature_img' => $finger->feature_img,
            'feature_data' => $finger->feature_data
        );
        return new result(true,'success',$return);
    }
}