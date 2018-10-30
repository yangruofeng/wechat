<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/9/17
 * Time: 14:40
 */
class member_v2Control extends memberControl
{

    public function changeTradingPasswordRequestOp()
    {
        $chk =  $this->checkToken();
        if( !$chk->STS ){
            return $chk;
        }
        $params = array_merge($_GET,$_POST);
        // 先处理图片的上传
        $member_image = $_FILES['member_image'];
        if( empty($member_image) || $member_image['size'] <=0 ){
            return new result(false,'No upload member image.',null,errorCodesEnum::INVALID_PARAM);
        }
        $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
        $upload->set('save_path', null);
        $upload->set('default_dir', 'member_avator');
        $re = $upload->server2upun('member_image');
        if ($re == false) {
            return new result(false, 'Upload photo fail:'.$upload->getError(), null, errorCodesEnum::UPLOAD_PIC_TO_UPYUN_FAIL);
        }
        $img_path = $upload->img_url;
        $params['member_image'] = $img_path;
        $rt = member_profileClass::changeTradingPasswordRequest($params);
        return $rt;
    }

    public function getLastChangeTradingPasswordRequestOp()
    {
        $chk =  $this->checkToken();
        if( !$chk->STS ){
            return $chk;
        }
        $params = array_merge($_GET,$_POST);
        $member_id = $params['member_id'];
        $m = new member_change_trading_password_requestModel();
        $data = null;
        $last_request = $m->getMemberLastRequest($member_id);
        if( $last_request ){
            if( $last_request['state'] != commonApproveStateEnum::CANCEL
                && $last_request['state'] != commonApproveStateEnum::REJECT
                && $last_request['state'] != commonApproveStateEnum::PASS
            ){
                unset($last_request['new_password']);
                $data = $last_request;
            }
        }

        return new result(true,'success',array(
            'last_request' => $data
        ));

    }


}