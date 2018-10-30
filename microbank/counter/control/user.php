<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/7
 * Time: 15:48
 */
class userControl extends counter_baseControl
{

    public function __construct()
    {
        parent::__construct();
        Tpl::setDir("user");
        Language::read('user');
        Tpl::setLayout('home_layout');
    }

    /**
     * 用户修改登录密码
     */
    public function changeLoginPasswordOp()
    {
        Tpl::showPage("user.change_login_pwd");
    }

    /**
     * 用户修改交易密码
     */
    public function setTradingPasswordOp()
    {
        $user_id = $this->user_id;
        $m_user = M('um_user');
        $user = $m_user->find(array('uid'=>$user_id));
        Tpl::output('user',$user);
        Tpl::showPage("user.set_trading_pwd");
    }


    public function forgotTradingPasswordOp()
    {
        $user_id = $this->user_id;
        $rt = userClass::forgotTradingPasswordOp($user_id);
        if( !$rt->STS ){
            showMessage('Handle fail:'.$rt->MSG);
        }

        $this->setTradingPasswordOp();

    }


    /**
     * 修改密码
     * @param $p
     * @return result
     */
    public function apiChangePasswordOp($p)
    {
        $p['user_id'] = $this->user_id;
        if (trim($p['new_password'] != trim($p['verify_password']))) {
            return new result(false, 'Verify password error!');
        }
        $class_user = new userClass();
        $rt = $class_user->changePassword($p);
        return $rt;
    }


    /**
     * 验证码
     * @param $p
     * @return result
     */
    public function  sendVerifyCodeOp($p)
    {
        $data = $p;
        $url = ENTRY_API_SITE_URL . '/phone.code.send.php';
        $rt = curl_post($url, $data);
        debug($rt);
        $rt = json_decode($rt, true);
        if ($rt['STS']) {
            return new result(true, L('tip_success'), $rt['DATA']);
        } else {
            return new result(false, L('tip_code_' . $rt['CODE']));
        }
    }

    /**
     * 发送验证码
     * @param $p
     * @return result
     */
    public function sendVerifyCodeByUidOp()
    {
        $user_id = $this->user_id;
        $m_um_user = M('um_user');
        $use_info =  $m_um_user->find(array('uid' => $user_id));
        $phone_arr = tools::separatePhone($use_info['mobile_phone']);

        $param = array();
        $param['country_code'] = $phone_arr[0];
        $param['phone'] = $phone_arr[1];
        $rt = $this->sendVerifyCodeOp($param);
        if ($rt->STS) {
            return new result(true, L('tip_success'), $rt->DATA);
        } else {
            return new result(false, L('tip_code_' . $rt->CODE), array('code' => $rt->CODE, 'msg' => $rt->MSG));
        }
    }


    public function verifyChangeTradePwdOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $user_id = $this->user_id;
        $old_pwd = trim($params['old_pwd']);
        $new_pwd = trim($params['new_pwd']);
        $is_first_setting = $params['is_first_set'];
        $m_um_user = M('um_user');
        $row = $m_um_user->getRow($user_id);
        if (!$row) {
            showMessage('Invalid Id!');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();

        // 交易密码是6位数字
        if( !preg_match('/\d{6}/',$new_pwd) ){
            showMessage('Trading password should be a six digit number.');
        }


        try{

            if( $is_first_setting ){
                // 首次设置，验证登陆密码
                if( $row->password != md5($old_pwd) ){
                    $conn->rollback();
                    showMessage('Login password error.');
                }
            }else{
                // 修改设置，验证原来的交易密码
                if ( md5($old_pwd) != $row->trading_password) {
                    $conn->rollback();
                    showMessage('Old trading password error!');
                }
            }

            $rt = userClass::commonUpdateUserTradePassword($user_id, $new_pwd);
            if ($rt->STS) {
                $conn->submitTransaction();
                Tpl::showPage('set.trading.pwd.success','msg_layout','user');
                //showMessage('Success');
            } else {
                $conn->rollback();
                showMessage($rt->MSG);
            }


        }catch (Exception $e ){
            $conn->rollback();
            showMessage($e->getMessage());
        }


    }


    /**
       更改交易密码
     */
    public function verifyChangeTradePwd_oldOp()
    {
        $user_id = $this->user_id;
        $old_pwd = trim($_POST['old_pwd']);
        $new_pwd = trim($_POST['new_pwd']);
        $verify_id = intval($_POST['verify_id']);
        $verify_code = trim($_POST['verify_code']);
        $m_um_user = M('um_user');
        $row = $m_um_user->getRow($user_id);
        if (!$row) {
            showMessage('Invalid Id!');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        if ($verify_code) {
            $m_verify_code = new phone_verify_codeModel();
            $row = $m_verify_code->getRow(array(
                'uid' => $verify_id,
                'verify_code' => $verify_code,
                'state' => 0
            ));
            if (!$row) {
                $conn->rollback();
                showMessage('SMS code error!');
            }
            $row->state = 1;
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                showMessage('SMS code error!' . $rt->MSG);
            }

        } else {
            if (empty($old_pwd)) {
                $conn->rollback();
                showMessage('To change the trading password, must enter the verification code or the original password.');
            }
            if (md5($old_pwd) != $row->trading_password) {
                $conn->rollback();
                showMessage('Old password error!');
            }
        }

        $rt = userClass::commonUpdateUserTradePassword($user_id, $new_pwd);
        if ($rt->STS) {
            $conn->submitTransaction();
            Tpl::output('msg', $rt->MSG);
            Tpl::showpage('set.trading.pwd.success', 'msg_layout');
        } else {
            $conn->rollback();
            showMessage($rt->MSG);
        }

    }

    /**
     * user 信息
    */
    public function myProfileOp()
    {
        $class_user = new userClass();
        $rt = $class_user->getUserInfo($this->user_id);
        Tpl::output("user_info", $rt->DATA);
        Tpl::showPage("user.profile");
    }


    public function updateProfileOp($p)
    {
        $user_name = trim($p['user_name']);
        $mobile_phone = trim($p['mobile_phone']);
        $email = trim($p['email']);
        if (empty($user_name)) {
            return new result(false, 'The user name cannot be empty!');
        }
        $m = M("um_user");
        $user = $m->getRow($this->user_id);
        $user->user_name = $user_name;
        $user->mobile_phone = $mobile_phone;
        $user->email = $email;
        $user->update_time = Now();
        $rt = $user->update();
        if ($rt->STS) {
            return new result(true, 'Update successful!');
        } else {
            return new result(false, 'Update failure!');
        }
    }

    public function userIconOp()
    {
        $class_user = new userClass();
        $rt = $class_user->getUserInfo($this->user_id);
        Tpl::output("user_info", $rt->DATA);
        Tpl::showPage("user.icon");
    }


    public function updateUserIconOp($p)
    {
        $srcImg = $p['src_img'];
        if (!$srcImg) return new result(false, "Source Image is Emptry");
        $user_id = $this->user_id;

        if (!$user_id) return new result(false, "Invalid Session,Please Login Again");
        //把图片从draft移动到avatar目录

        $avatar_path = _UPLOAD_ . "/avatar";
        if (!is_dir($avatar_path)) {
            if (!@mkdir($avatar_path, 0755)) {
                return new result(false, "Make Folder Failed");
            }
        }
        $src_img = $avatar_path . "/" . $srcImg;
        $draft_img = _UPLOAD_ . "/draft/" . $srcImg;
        if (file_exists($draft_img)) {
            rename($draft_img, $src_img);
            @chmod($src_img, 0755);
            @unlink($draft_img);
        }
        $file = pathinfo($src_img);
        $ext = $file['extension'];
        //剪切缩略图
        $args = array();
        $args['src'] = $src_img;
        $iconImg = getUniqueNumber() . "." . $ext;
        $args['dst'] = $avatar_path . "/" . $iconImg;
        $args['x1'] = $p['cords_x1'];
        $args['x2'] = $p['cords_x2'];
        $args['y1'] = $p['cords_y1'];
        $args['y2'] = $p['cords_y2'];
        $args['w'] = $p['cords_w'];
        $args['h'] = $p['cords_h'];
        $args['src_max_w'] = 550;
        $result = imageHandler::cutImage($args);
        if ($result->STS) {
            //保存数据库参数
            $m = M("um_user");
            $user = $m->getRow($user_id);
            $user->user_image = $srcImg;
            $user->user_icon = $iconImg;
            $user->update_time = Now();
            $profile = my_json_decode($user->profile);
            $profile['cords'] = array(
                "x" => $p['cords_x1'],
                "x2" => $p['cords_x2'],
                "y" => $p['cords_y1'],
                "y2" => $p['cords_y2'],
                "w" => $p['cords_w'],
                "h" => $p['cords_h'],
            );
            $user->profile = my_json_encode($profile);
            $rt = $user->update();
            if ($rt->STS) {
                setSessionVar("user_info", $user->toArray());
            }
            return new result(true, '', array('icon' => getUserIcon($iconImg)));
        } else {
            return $result;
        }
    }

    public function securityCardResetOp() {
        $m = new um_user_cardModel();

        if (!checkSubmit()) {
            $cards = $m->getListByUserID($this->user_id);
            Tpl::output("user_card_list", array_column($cards, "card_no"));
            Tpl::output("user_info", $this->user_info);
            Tpl::output("operator_info", $this->user_info);
            Tpl::showPage("user.card.reset");
        } else {
            $params = array_merge(array(),$_GET,$_POST);
            if (!$m->checkCardOwner($this->user_id, $params['card_no'])) {
                showMessage("Card is not own to you");
                return;
            }

            $rt = icCardClass::initializeCard($params['card_no'], $params["initial_info"], $this->user_info);
            if (!$rt->STS) {
                showMessage($rt->MSG);
            } else {
                showMessage("Reset Successful", "javascript:parent.location.reload()");
            }
        }
    }

    public function checkUserCardOp($p) {
        $m = new um_user_cardModel();
        if (!$m->checkCardOwner($this->user_id, $p['card_no'])) {
            return new result(false, 'Card is not own to you', null, errorCodesEnum::NOT_PERMITTED);
        } else {
            return new result(true);
        }
    }
}