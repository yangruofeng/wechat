<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/12
 * Time: 10:56
 */
class member_handlerClass
{


    /** 获得handler信息
     * @param $handler_id
     * @return mixed|null
     */
    public static function getHandlerInfoById($handler_id)
    {
        $m = new member_account_handlerModel();
        $handler_info = $m->getRow($handler_id);
        if( $handler_info ){
            return $handler_info;
        }
        return null;
    }

    public static function getPartnerCodeByHandlerInfo($handler_info)
    {
        switch ( $handler_info['handler_type'] )
        {
            case memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY:
                return partnerEnum::ACE;
                break;
            default:
                return '';
                break;
        }
    }


    /** 获得合作商的业务开启状态
     * @param $handler_id
     * @return bool
     */
    public static function checkHandlerFunctionIsClosedById($handler_id)
    {
        $handler_info = self::getHandlerInfoById($handler_id);
        $handler_type = $handler_info['handler_type'];
        switch( $handler_type ){
            case memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY:
                return global_settingClass::isACEBusinessClosed();
                break;
            default:
                return false;
        }
    }


    /** 获得handler的类型名字
     * @param $handler_type
     * @return string
     */
    public static function getHandlerTypeName($handler_type)
    {
        switch( $handler_type )
        {
            case memberAccountHandlerTypeEnum::CASH:
                return 'cash';
                break;
            case memberAccountHandlerTypeEnum::BANK :
                return 'bank transfer';
                break;
            case memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY:
                return 'asiaweiluy';
                break;
            case memberAccountHandlerTypeEnum::PARTNER_LOAN:
                return 'loan';
                break;
            case memberAccountHandlerTypeEnum::PASSBOOK:
                return 'balance';
                break;
            default:
                return 'unknown';
        }
    }


    /** 贷款默认的收放款账户
     * @param $member_id
     * @return null
     */
    public static function getMemberDefaultLoanHandler($member_id)
    {
        return self::getMemberDefaultAceHandlerInfo($member_id);
    }


    /** 获取会员默认绑定的ACE账户
     * @param $member_id
     * @return null
     */
    public static function getMemberDefaultAceHandlerInfo($member_id)
    {
        $m = new member_account_handlerModel();
        $handler_info = $m->orderBy('uid desc')->getRow(array(
            'member_id' =>$member_id,
            'handler_type' => memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));
        if( $handler_info ){
            return $handler_info;
        }
        return null;
    }

    /** 获取会员默认绑定的储蓄账户
     * @param $member_id
     * @return null
     */
    public static function getMemberDefaultPassbookHandlerInfo($member_id)
    {
        $m = new member_account_handlerModel();
        $handler_info = $m->orderBy('uid desc')->getRow(array(
            'member_id' =>$member_id,
            'handler_type' => memberAccountHandlerTypeEnum::PASSBOOK,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));
        if( $handler_info ){
            return $handler_info;
        }else{

            $member = (new memberModel())->find(array(
                'uid' => $member_id
            ));
            if( !$member ){
                return null;
            }
            // 创建一个储蓄账户的handler
            $m_handler = new member_account_handlerModel();
            $handler = $m_handler->newRow();
            $handler->member_id = $member['uid'];
            $handler->handler_type = memberAccountHandlerTypeEnum::PASSBOOK;
            $handler->handler_name = $member['display_name']?:$member['login_code'];
            $handler->handler_account = $member['login_code'];
            $handler->handler_phone = $member['phone_id'];
            $handler->is_verified = 1;
            $handler->state = accountHandlerStateEnum::ACTIVE;
            $handler->create_time = Now();
            $insert3 = $handler->insert();

            if( !$insert3->STS ){
                return null;
            }
            return $handler;
        }

    }


    /** 获取会员默认绑定的贷款操作账户（贷款作为一个特殊的操作账户）
     * @param $member_id
     * @return null
     */
    public static function getMemberDefaultPartnerLoanHandlerInfo($member_id)
    {
        $m = new member_account_handlerModel();
        $handler_info = $m->orderBy('uid desc')->getRow(array(
            'member_id' =>$member_id,
            'handler_type' => memberAccountHandlerTypeEnum::PARTNER_LOAN,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));
        if( $handler_info ){
            return $handler_info;
        }
        return null;
    }


    /** 获取会员默认绑定的现金账户
     * @param $member_id
     * @return null
     */
    public static function getMemberDefaultCashHandlerInfo($member_id)
    {
        $m = new member_account_handlerModel();
        $handler_info = $m->orderBy('uid desc')->getRow(array(
            'member_id' =>$member_id,
            'handler_type' => memberAccountHandlerTypeEnum::CASH,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));
        if( $handler_info ){
            return $handler_info;
        }else{

            $member = (new memberModel())->find(array(
                'uid' => $member_id
            ));
            if( !$member ){
                return null;
            }
            // 创建一个CASH账户的handler
            $handler = $m->newRow();
            $handler->member_id = $member['uid'];
            $handler->handler_type = memberAccountHandlerTypeEnum::CASH;
            $handler->handler_name = $member['display_name']?:$member['login_code'];
            $handler->handler_account = $member['login_code'];
            $handler->handler_phone = $member['phone_id'];
            $handler->is_verified = 1;
            $handler->state = accountHandlerStateEnum::ACTIVE;
            $handler->create_time = Now();
            $insert3 = $handler->insert();
            if( !$insert3->STS ){
                return null;
            }

            return $handler;

        }

    }


    /** 获取会员默认绑定的合作银行账户
     * @param $member_id
     * @return null
     */
    public static function getMemberDefaultBankHandlerInfo($member_id)
    {
        $m = new member_account_handlerModel();
        $handler_info = $m->orderBy('uid desc')->getRow(array(
            'member_id' =>$member_id,
            'handler_type' => memberAccountHandlerTypeEnum::BANK,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));
        if( $handler_info ){
            return $handler_info;
        }
        return null;
    }


    public static function getMemberBindBankList($member_id)
    {
        $m = new member_account_handlerModel();

        // ace 信息
        $ace_bank = self::getMemberDefaultAceHandlerInfo($member_id);
        if( $ace_bank ){
            $ace_bank['handler_account'] = maskInfo($ace_bank['handler_account']);
            $ace_bank['handler_phone'] = maskInfo($ace_bank['handler_phone']);
        }

        $list = $m->select(array(
            'member_id' =>$member_id,
            'handler_type' => memberAccountHandlerTypeEnum::BANK,
            'is_verified' => 1,
            'state' => accountHandlerStateEnum::ACTIVE
        ));



        $return = array();

        foreach( $list as $bank ){
            $temp = array();
            $temp['uid'] = $bank['uid'];
            $bank_info = @json_decode($bank['handler_property'],true);

            $temp['handler_name'] = maskInfo($bank['handler_name']);
            $temp['handler_account'] = '**** **** **** '.substr($bank['handler_account'],-4);
            $temp['handler_phone'] = maskInfo($bank['handler_phone']);
            $logo_url = global_settingClass::getBankLogoByBankCode($bank_info['bank_code']);
            $temp['bank_logo'] = $logo_url;
            $temp['bank_name'] = $bank_info['bank_name'];
            $temp['bank_currency'] = $bank_info['currency'];
            $temp['bank_code'] = $bank_info['bank_code'];
            $temp['bank_detail_info'] = $bank_info;
            $return[] = $temp;
        }

        return array(
            'partner_ace' => $ace_bank,
            'bank_list' => $return
        );

    }


    /** 获取member绑定的所有银行列表
     * @param $member_id
     * @return ormCollection
     */
    public static function getMemberAllBankHandlerList($member_id)
    {
        $r = new ormReader();
        $sql = "select * from member_account_handler where member_id='$member_id' and is_verified='1' 
        and state='".accountHandlerStateEnum::ACTIVE."' and handler_type in('".memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY."','".memberAccountHandlerTypeEnum::BANK."') 
         order by state!='".memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY."',uid desc ";
        $list = $r->getRows($sql);
        return $list;
    }


    /** 获取member绑定的所有网上银行列表
     * @param $member_id
     * @return ormCollection
     */
    public static function getMemberBindAllOnlineBankList($member_id)
    {
        $r = new ormReader();
        $sql = "select * from member_account_handler where member_id='$member_id' and is_verified='1' 
        and state='".accountHandlerStateEnum::ACTIVE."' and handler_type in('".memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY."')  ";
        $list = $r->getRows($sql);
        return $list;
    }


    /** 获取member绑定的合作公司列表
     * @param $member_id
     * @return ormCollection
     */
    public static function getMemberBindPartnerHandlerList($member_id)
    {
        $r = new ormReader();
        $sql = "select * from member_account_handler where member_id='$member_id' and is_verified='1' 
        and state='".accountHandlerStateEnum::ACTIVE."' and handler_type in('".memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY."')  ";
        $list = $r->getRows($sql);
        return $list;
    }


    /** 绑定ACE开始(编辑也是重新绑定)
     * @param $params
     * @return result
     */
    public static function bindAceStart($params)
    {
        if( global_settingClass::isACEBusinessClosed() ){
            return new result(false,'ACE business closed.',null,errorCodesEnum::FUNCTION_CLOSED);
        }
        $member_id = $params['member_id'];
        $country_code = $params['country_code'];
        $phone = $params['phone'];
        $sign = trim($params['sign']);

        $memberObj = new objectMemberClass($member_id);

        if( !$country_code || !$phone  ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $phone_arr = tools::getFormatPhone($country_code, $phone);
        $contact_phone = $phone_arr['contact_phone'];

        // 检查交易密码
        $self_sign = md5($member_id.$memberObj->trading_password);
        $chk = $memberObj->checkTradingPasswordSign($sign,$self_sign,'Bind Ace account.');
        if( !$chk->STS ){
            return $chk;
        }

        $aceAccount = asiaweiluyClass::formatAceAccountByPhone($country_code,$phone);
        $chk = asiaweiluyClass::verifyAceAccount($aceAccount);
        if( !$chk->STS ){
            return $chk;
        }

        $is = $chk->DATA;
        if( !$is ){
            return new result(false,'Not ace member',null,errorCodesEnum::ACE_ACCOUNT_NOT_EXIST);
        }

        // 插入临时记录
        $m_handler = new member_account_handlerModel();
        $account_handler = $m_handler->newRow();
        $account_handler->member_id = $member_id;
        $account_handler->handler_type = memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY;
        $account_handler->handler_name = $memberObj->member_account;
        $account_handler->handler_account = $aceAccount;
        $account_handler->handler_phone = $contact_phone;
        $account_handler->is_verified = 0;
        $account_handler->bank_id = 0;
        $account_handler->bank_code = 'ace';
        $account_handler->bank_name = 'Asiaweiluy';
        $account_handler->state = accountHandlerStateEnum::TEMP;
        $account_handler->create_time = Now();
        $insert = $account_handler->insert();
        if (!$insert->STS) {
            return new result(false, 'Bind fail', null, errorCodesEnum::DB_ERROR);
        }

        $bind_id = $account_handler->uid;

        $re = asiaweiluyClass::bindAccountSendVerifyCode($aceAccount);
        if( !$re->STS ){
            return $re;
        }

        $data = $re->DATA;
        $data['bind_id'] = $bind_id;
        return new result(true,'success',$data);

    }


    /** 绑定ACE结束
     * @param $params
     * @return result
     */
    public static function bindAceFinish($params)
    {
        if( global_settingClass::isACEBusinessClosed() ){
            return new result(false,'ACE business closed.',null,errorCodesEnum::FUNCTION_CLOSED);
        }
        $member_id = intval($params['member_id']);
        $sms_id = $params['sms_id'];
        $sms_code = $params['sms_code'];
        $bind_id = intval($params['bind_id']);
        if ( !$member_id || !$sms_id || !$sms_code || !$bind_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_handler = new member_account_handlerModel();
        $account_handler = $m_handler->getRow(array(
            'uid' => $bind_id,
            'member_id' => $member_id,
            'handler_type' => memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY,
            'state' => accountHandlerStateEnum::TEMP
        ));

        if( !$account_handler ){
            return new result(false,'Invalid bind id:'.$bind_id,null,errorCodesEnum::INVALID_PARAM);
        }

        // 验证验证码
        $re = asiaweiluyClass::bindAccountCheckVerifyCode($sms_id, $sms_code);
        if (!$re->STS) {
            return $re;
        }
        $ok = $re->DATA;
        if ($ok != 1) {
            return new result(false, 'Wrong code', null, errorCodesEnum::SMS_CODE_ERROR);
        }

        // 更新有的为历史记录
        $sql = "update member_account_handler set state='".accountHandlerStateEnum::HISTORY."',update_time='".Now()."' 
        where member_id='$member_id' and handler_type='".memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY."' 
        and state='".accountHandlerStateEnum::ACTIVE."' ";
        $up = $m_handler->conn->execute($sql);
        if( !$up->STS ){
            return new result(false,'Update old account fail.',null,errorCodesEnum::DB_ERROR);
        }

        // 更新当前记录
        $account_handler->is_verified = 1;
        $account_handler->state = accountHandlerStateEnum::ACTIVE;
        $account_handler->update_time = Now();
        $up = $account_handler->update();
        if( !$up->STS ){
            return new result(false,'Bind confirm fail.',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $account_handler);
    }


    public static function getAllClientBindAceAccount($page_num,$page_size,$filter=array())
    {
        $where = '';
        if( $filter['phoneNumber'] ){
            $where .= " and h.handler_phone like '%".$filter['phoneNumber']."%' ";
        }
        $r = new ormReader();
        $sql = " select h.*,m.obj_guid member_guid,m.login_code,m.display_name,m.phone_id from member_account_handler h left join client_member m on m.uid=h.member_id 
        where h.handler_type='".memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY."' and h.state='".accountHandlerStateEnum::ACTIVE."' $where ";
        return $r->getPage($sql,$page_num,$page_size);
    }


    /** 修改绑定的ACE账号,实际是新增一条
     * @param $params
     * @return result
     */
    public static function editBindAceInfo($params)
    {
        if( global_settingClass::isACEBusinessClosed() ){
            return new result(false,'ACE business closed.',null,errorCodesEnum::FUNCTION_CLOSED);
        }

        return self::bindAceFinish($params);

        /*$member_id = $params['member_id'];
        $handler_id = intval($params['account_handler_id']);

        $m_handler = new member_account_handlerModel();
        $account_handler = $m_handler->getRow(array(
            'uid' => $handler_id,
            'member_id' => $member_id
        ));
        if (!$account_handler) {
            return new result(false, 'Invalid param:'.$handler_id, errorCodesEnum::INVALID_PARAM);
        }
        // 更新原来账户状态
        $account_handler->state = accountHandlerStateEnum::HISTORY;
        $account_handler->update_time = Now();
        $up = $account_handler->update();
        if (!$up->STS) {
            return new result(false, 'Update history fail', null, errorCodesEnum::DB_ERROR);
        }*/


    }


    /** ACE解除绑定开始
     * @param $member_id
     * @param $handler_id
     * @param $sign,md5(member_id+交易密码)
     * @return result
     */
    public static function unbindAceStart($member_id,$handler_id,$sign)
    {
        if( global_settingClass::isACEBusinessClosed() ){
            return new result(false,'ACE business closed.',null,errorCodesEnum::FUNCTION_CLOSED);
        }

        if( !$member_id || !$handler_id ){
            return new result(false,'Invalid param.',null,errorCodesEnum::INVALID_PARAM);
        }

        $memberObj = new objectMemberClass($member_id);
        // 检查交易密码
        $self_sign = md5($member_id.$memberObj->trading_password);
        $chk = $memberObj->checkTradingPasswordSign($sign,$self_sign,'Unbind Ace account.');
        if( !$chk->STS ){
            return $chk;
        }

        $m_handler = new member_account_handlerModel();
        $handler = $m_handler->getRow(array(
            'member_id' => $member_id,
            'uid' => $handler_id,
            'handler_type' => memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY,
        ));
        if( !$handler ){
            return new result(false,'Invalid handler id:'.$handler_id,null,errorCodesEnum::INVALID_PARAM);
        }


        // 开始发送短信
        $aceAccount = $handler->handler_account;
        $rt = asiaweiluyClass::aceUnbindStart($aceAccount);
        return $rt;
    }

    /** ACE解除绑定成功
     * @param $params
     * @return result
     */
    public static function unbindAceFinish($params)
    {
        if( global_settingClass::isACEBusinessClosed() ){
            return new result(false,'ACE business closed.',null,errorCodesEnum::FUNCTION_CLOSED);
        }

        $member_id = $params['member_id'];
        $handler_id = $params['handler_id'];
        $sms_id = $params['sms_id'];
        $sms_code = $params['sms_code'];
        if( !$member_id || !$handler_id || !$sms_id  ){
            return new result(false,'Invalid param.',null,errorCodesEnum::INVALID_PARAM);
        }

        $m_handler = new member_account_handlerModel();
        $handler = $m_handler->getRow(array(
            'member_id' => $member_id,
            'uid' => $handler_id,
            'handler_type' => memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY,
        ));
        if( !$handler ){
            return new result(false,'Invalid handler id:'.$handler_id,null,errorCodesEnum::INVALID_PARAM);
        }


        // 验证短信
        $rt = asiaweiluyClass::aceUnbindFinish($sms_id,$sms_code);
        if( !$rt->STS ){
            return $rt;
        }

        $data = $rt->DATA;
        if( $data['is_success'] != 1 ){
            return new result(false,'Sms code error.',null,errorCodesEnum::SMS_CODE_ERROR);
        }

        // 更新为历史记录
        $handler->state = accountHandlerStateEnum::HISTORY;
        $handler->update_time = Now();
        $up = $handler->update();
        if( !$up->STS ){
            return new result(false,'Update row fail.',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');

    }

}