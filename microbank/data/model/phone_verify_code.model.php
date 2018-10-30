<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 15:18
 */
class phone_verify_codeModel extends tableModelBase
{

    public function __construct()
    {
        parent::__construct('common_verify_code');
    }


    /** 验证短信验证码
     * @param $sms_id
     * @param $sms_code
     * @return result
     */
    public function verifyCode($sms_id,$sms_code)
    {
        $row = $this->getRow($sms_id);
        if( !$row ){
            return new result(false,'No code',null,errorCodesEnum::SMS_CODE_ERROR);
        }

        // 超过有效时间
        if( ( strtotime($row->create_time)+300 ) < time() ){
            return new result(false,'Code expired',null,errorCodesEnum::SMS_CODE_ERROR);
        }
        if( $row->verify_code != $sms_code){
            return new result(false,'Code error',null,errorCodesEnum::SMS_CODE_ERROR);
        }
        $row->state = 1;
        $row->update();
        return new result(true,'success',$row);
    }

}