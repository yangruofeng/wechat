<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/9
 * Time: 13:56
 */
class bizMemberLockHandleClass extends bizBaseClass
{

    public function __construct()
    {

        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!');
        }
        $this->scene_code = bizSceneEnum::COUNTER;
        $this->biz_code = bizCodeEnum::MEMBER_UNLOCK_BY_COUNTER;
        $this->bizModel = new biz_member_lock_handleModel();
    }

    public function getBizDetailById($id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->find(array(
            'uid' => $id
        ));
        return $biz;
    }

    public function checkBizOpen()
    {
        return new result(true);
    }

    public function execute($user_id,$member_id,$member_image,$sms_id,$sms_code,$type)
    {
        $userObj = new objectUserClass($user_id);
            $chk = $userObj->checkValid();
            if( !$chk->STS ){
                return $chk;
        }

        $member = (new memberModel())->getRow($member_id);
        if( !$member ){
            return new result(false,'Member not exists.',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 检查验证码
        $m_verify_code = new phone_verify_codeModel();
        $chk = $m_verify_code->verifyCode($sms_id,$sms_code);
        if( !$chk->STS ){
            return $chk;
        }

        $biz = $this->bizModel->newRow();
        $biz->scene_code = $this->scene_code;
        $biz->biz_code = $this->biz_code;
        $biz->member_id = $member_id;
        $biz->handle_type = $member_id;
        $biz->member_image = $member_image;
        $biz->handle_type = $type;
        $biz->state = bizStateEnum::CREATE;
        $biz->operator_id = $userObj->user_id;
        $biz->operator_name = $userObj->user_name;
        $biz->create_time = Now();
        $biz->update_time = Now();
        $biz->branch_id = $userObj->branch_id;
        $insert = $biz->insert();
        if( !$insert->STS ){
            return new result(false,'Insert biz fail.'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        // 更新状态
        $now = Now();
        if( $type == handleLockTypeEnum::UNLOCK ){

            // 统一在内部处理旧状态，不每次从外部查询
            $member_property = my_json_decode($member->member_property);
            $old_member_state =  $member_property[memberPropertyKeyEnum::ORIGINAL_STATE];

            if($old_member_state){
                $member->member_state = $old_member_state;
            }else{

                if( $member->id_sn && $member->id_expire_time > $now ){
                    $member->member_state = memberStateEnum::VERIFIED;
                }else{
                    $member->member_state = memberStateEnum::CHECKED;
                }
            }

        }else{

            // 锁定
            $member_property = my_json_decode($member->member_property);
            // 保存原来的状态
            $member_property[memberPropertyKeyEnum::ORIGINAL_STATE] = $member->member_state;

            $member->member_state = memberStateEnum::TEMP_LOCKING;
            $member->member_property = json_encode($member_property);

        }

        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Update state fail.'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Update biz fail.'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        $biz->biz_id = $biz->uid;

        return new result(true,'success',$biz);

    }

}