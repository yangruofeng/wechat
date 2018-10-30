<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/2
 * Time: 11:23
 */

class member_tokenModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_token');
    }


    public function checkToken($token,$member_id)
    {
        if( !$token ){
            return new result(false,'Invalid token',null,errorCodesEnum::NO_LOGIN);
        }
        $row = $this->orderBy('uid desc')->getRow(array(
            'token' => $token
        ));
        if( !$row ){
            return new result(false,'Invalid token',null,errorCodesEnum::INVALID_TOKEN);
        }
        // 检查过期时间,超过12小时重新登录 ->1年
        if( ( strtotime($row['create_time']) + 12*3600*365 ) < time() ){
            $row->delete();
            return new result(false,'Invalid token',null,errorCodesEnum::INVALID_TOKEN);
        }

        // 检查是否本人token
        if( $row->member_id != $member_id ){
            return new result(false,'Token not match member.',null,errorCodesEnum::INVALID_TOKEN);
        }

        // 再检查member是否可登陆
        $memberObj = new objectMemberClass($row->member_id);
        $chk = $memberObj->isCanLogin();
        if( !$chk->STS ){
            $row->delete();
            return $chk;
        }

        return new result(true);
    }
}