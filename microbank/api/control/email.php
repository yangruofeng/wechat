<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 14:07
 */
class emailControl extends bank_apiControl
{

    /**
     * 邮件发送冷却时间
     * @return result
     */
    public function coolTimeOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        if( !$params['email'] || !isEmail($params['email']) ){
            return new result(false,'Email error',null,errorCodesEnum::INVALID_PARAM);
        }
        $email = $params['email'];
        $m = new common_verify_emailModel();
        $row = $m->orderBy('uid desc')->getRow(array(
            'email' => $email
        ));
        if( !$row ){
            return new result(true,'success',0);  // 不在冷却中
        }

        $send_time = strtotime($row->create_time);
        $end_time = $send_time+emailCoolTimeEnum::CD;
        $c_time = time();
        if( $end_time > $c_time ){
            $cd = $end_time-$c_time;
            return new result(true,'success',$cd);
        }
        return new result(true,'success',0);
    }


    /**
     * 发送邮件激活邮件
     * @return result
     */
    public function sendVerifyEmailOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        if( empty($params['email']) ){
            return new result(false,'Lack of email',null,errorCodesEnum::DATA_LACK);
        }
        $email = $params['email'];

        // 验证合法性
        if( !isEmail($email) ){
            return new result(false,'Invalid email',null,errorCodesEnum::INVALID_PARAM);
        }

        // 是否重复验证
        $m = new common_verify_emailModel();
        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'email' => $email
        ));
        if( $member && $member->is_verify_email ){
            return new result(false,'Email has been verified',null,errorCodesEnum::DATA_DUPLICATED);
        }

        // 是否在冷却时间内
        $row = $m->orderBy('uid desc')->getRow(array(
           'email' => $email,
           'state' => 0
        ));
        $last_time = $row?strtotime($row->create_time):0;
        if( (time()-$last_time) <= emailCoolTimeEnum::CD ){
            return new result(false,'Frequent operation',null,errorCodesEnum::UNDER_COOL_TIME);
        }

        // 插入记录
        $verify_key = md5($email.time());
        $new_row = $m->newRow();
        $new_row->email = $email;
        $new_row->verify_key = $verify_key;
        $new_row->create_time = Now();
        $insert = $new_row->insert();
        if( !$insert->STS ){
            return new result(false,'Log fail',null,errorCodesEnum::DB_ERROR);
        }

        // 发送邮件
        $subject = 'Verify Email';

        $verify_url = ltrim(getConf('entry_api_url'),'/').'/email.verify.confirm.php?vid='.$new_row->uid.'&vkey='.$verify_key;

$message = <<<TOT
<div >
    <h4 style="margin-top: 20px;">Thanks for your support, please click the link below to complete the e-mail verification; if failed, you can copy the address and open in a browser.</h4>
    <p>Please complete the e-mail verification as soon as possible via the verification address: <a href="$verify_url" target="_blank">{$verify_url}</a></p>
</div>
TOT;
        $handler = new Email();
        $re = $handler->send_sys_email($email,$subject,$message);
        if( !$re ){
            return new result(false,'Send email fail',null,errorCodesEnum::API_FAILED);
        }

        return new result(true,'Success');

    }

    /**
     * 邮箱验证确认页面
     * html页面展示
     */
    public function verifyConfirmOp()
    {

        $return = array(
            'status' => false,
            'msg' => 'Oops! E-mail verification failed, please re-verify e-mail!',
            'url' => '',
        );
        $params = array_merge(array(),$_GET,$_POST);
        $vid = $params['vid'];
        $vkey = $params['vkey'];
        $m = new common_verify_emailModel();
        $m_member = new memberModel();
        $row = $m->getRow(array(
            'uid' => $vid
        ));

        if( $row && ($row->verify_key == $vkey) ){

            $row->state = 1;
            $up = $row->update();
            if( $up->STS ){
                $sql = "update client_member set is_verify_email=1,verify_email_time='".date('Y-m-d H:i:s')."' where email='".$row->email."' ";
                $update = $m_member->conn->execute($sql);
                if( $update->STS ){
                    $return['status'] = true;
                    $return['msg'] = 'Congratulations, e-mail verification succeeded!';
                }

            }
        }

        Tpl::output('html_title','Verify Email');
        Tpl::setLayout('home_layout');
        Tpl::setDir('home');
        Tpl::output('verify_result',$return);
        Tpl::showpage('email.verify.confirm');
    }

}