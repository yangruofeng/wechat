<?php

class wap_operator_baseControl extends control{
    public $user_id;
    public $token;
    function __construct(){
        Language::read('act,label,tip,define,certification');
        $this->user_id=cookie('member_id');
        $this->token=cookie('token');
    }

    /**
     * wap检查token(Page)
     * @return result
     */
    public function pageCheckToken(){
        $token = cookie('token');
        $m_um_user_token = new um_user_tokenModel();
        $ret = $m_um_user_token->checkToken($token);
        if( !$ret->STS ){
            Tpl::setLayout('empty_layout');
            Tpl::setDir('login');
            Tpl::output('msg', L('tip_code_'.$ret->CODE));
            if($ret->CODE == errorCodesEnum::INVALID_TOKEN || $ret->CODE == errorCodesEnum::NO_LOGIN){
                setNcCookie('token', '');
                setNcCookie('member_id', '');
                setNcCookie('user_code', '');
                setNcCookie('user_name', '');
                setNcCookie('obj_guid', '');
                setNcCookie('user_position', '');
                setNcCookie('branch_id', '');
                setNcCookie('member_name', '');

                Tpl::showPage('tip_login');
              }
              Tpl::showPage('tip_error');
          }
    }

    /**
     * wap检查token(Ajax)
     * @return result
     */
    public function ajaxCheckToken(){
        $token = cookie('token');
        $m_um_user_token = new um_user_tokenModel();
        $ret = $m_um_user_token->checkToken($token);
        if( !$ret->STS ){
            if($ret->CODE == errorCodesEnum::INVALID_TOKEN || $ret->CODE == errorCodesEnum::NO_LOGIN){
                setNcCookie('token', '');
                setNcCookie('member_id', '');
                setNcCookie('user_code', '');
                setNcCookie('user_name', '');
                return new result(false, L('tip_code_'.$ret->CODE), array(), $ret->CODE);
              }
            return new result(false, L('tip_code_'.$ret->CODE));
          }
        return new result(true);
    }

    /**
     * wap错误信息
     * @return result
     */
    public function pageErrorMsg($msg){
        Tpl::setLayout('empty_layout');
        Tpl::setDir('login');
        Tpl::output('msg', $msg);
        Tpl::showPage('tip_error');
    }

}
