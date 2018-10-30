<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/20
 * Time: 13:47
 */
class smsClass
{

    /** 获取号码信息发送的冷却时间
     * @param $country_code
     * @param $phone_number
     * @return false|int
     */
    public static function getSendCoolTimeOfPhone($country_code,$phone_number)
    {
        if( !$country_code || !$phone_number ){
            return 0;
        }

        $format_phone = tools::getFormatPhone($country_code,$phone_number);
        $contact_phone = $format_phone['contact_phone'];
        $m = new phone_verify_codeModel();
        $row = $m->orderBy('uid desc')->getRow(array(
            'phone_id' => $contact_phone
        ));
        if( !$row ){
            return 0;  // 不在冷却中
        }

        $send_time = strtotime($row->create_time);
        $end_time = $send_time+phoneCodeCDEnum::CD;
        $c_time = time();
        if( $end_time > $c_time ){
            $cd = $end_time-$c_time;
            return $cd;
        }
        return 0;
    }

    /** 发送短信验证码
     * @param $country_code
     * @param $phone_number
     * @return result
     */
    public static function sendVerifyCode($country_code,$phone_number)
    {
        $format_phone = tools::getFormatPhone($country_code,$phone_number);
        $contact_phone = $format_phone['contact_phone'];
        // 检查合理性
        if( !isPhoneNumber($contact_phone) ){
            return new result(false,'Invalid phone',null,errorCodesEnum::INVALID_PHONE_NUMBER);
        }

        $m_phone_verify_code = M('phone_verify_code');

        // 是否在冷却时间内
        $verify_row = $m_phone_verify_code->orderBy('uid desc')->find(array(
            'phone_id' => $contact_phone,
        ));

        $last_time = 0;
        if( $verify_row && $verify_row['create_time'] ){
            $last_time = strtotime($verify_row['create_time']);
        }
        if( (time()-$last_time) < phoneCodeCDEnum::CD ){
            return new result(false,'In cool time,please wait a moment.',null,errorCodesEnum::UNDER_COOL_TIME);
        }


        // 发送短信验证码(4位)
        $verify_code = mt_rand(1000,9999);

        $smsHandler = new smsHandler();
        $rt = $smsHandler->sendVerifyCode($contact_phone,$verify_code);
        if( !$rt->STS ){
            // todo 没有成功返回成功不是有BUG？
            //return new result(false,'Send code fail: '.$rt->MSG,null,errorCodesEnum::SMS_CODE_SEND_FAIL);
        }

        $sms_row = $rt->DATA;
        $new_row = $m_phone_verify_code->newRow();
        $new_row->phone_country = $country_code;
        $new_row->phone_id = $contact_phone;
        $new_row->verify_code = $verify_code;
        $new_row->create_time = Now();
        $new_row->sms_id = $sms_row->uid;
        $insert = $new_row->insert();
        if( !$insert->STS ){
            return new result(false,'Insert verify code fail',null,errorCodesEnum::DB_ERROR);
        }
        $verify_id = $insert->AUTO_ID;

        return new result(true,'success',array(
            'verify_id' => $verify_id,
            'phone_id' => $contact_phone
        ));
    }

    /** API校验短信验证码
     * @param $params
     * @return result
     */
    public static function apiCheckVerifyCode($params)
    {
        $verify_id = $params['verify_id'];
        $verify_code = $params['verify_code'];
        if( !$verify_id || !$verify_code ){
            return new result(false,'Invalid param',null,errorCodesEnum::DATA_LACK);
        }
        $m_phone_verify_code = new phone_verify_codeModel();
        $rt = $m_phone_verify_code->verifyCode($verify_id,$verify_code);
        if( !$rt->STS ){
            return $rt;
        }
        $data = $rt->DATA;

        // 会员电话认证
        if( isset($params['is_certificate']) && $params['is_certificate'] == 1 ){
            $m_member = new memberModel();
            $member = $m_member->getRow(array(
                'phone_id' => $data['phone_id'],
                'is_verify_phone' => 0
            ));
            if( $member ){
                $member->is_verify_phone = 1;
                $member->verify_phone_time = date('Y-m-d H:i:s');
                $update = $member->update();
                if( !$update->STS ){
                    return new result(false,'Certificate fail',null,errorCodesEnum::DB_ERROR);
                }

            }
        }
        return new result(true,'Verify success');
    }

}