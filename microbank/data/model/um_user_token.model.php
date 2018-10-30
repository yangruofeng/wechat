<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/1
 * Time: 15:41
 */
class um_user_tokenModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('um_user_token');
    }

    public function checkToken($token)
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

        // 检查过期时间,超过7*24小时重新登录
        if( ( strtotime($row['create_time']) + 7*24*3600*365 ) < time() ){
            $row->delete();
            return new result(false,'Invalid token',null,errorCodesEnum::INVALID_TOKEN);
        }

        // 检查user的合法性
        $userObj = new objectUserClass($row['user_id']);
        $chk = $userObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        return new result(true,"",array("user_id"=>$row->user_id,"user_name"=>$row->user_name,"user_position"=>$userObj->position,"branch_id"=>$userObj->branch_id));
    }

}