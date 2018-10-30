<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 10:44
 */

class memberControl extends bank_apiControl
{


    /** 电话注册
     * @return result
     */
    public function phoneRegisterNewOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $params['phone_number'] = $params['phone'];
        return memberClass::phoneRegisterNew($params);
    }


    public function checkLoginAccountIsExistOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $login_code = $params['login_code'];
        $is = memberClass::checkLoginAccountIsExist($login_code);
        return new result(true,'success',array(
            'is_exist' => $is
        ));
    }


    public function verifyLoginPasswordOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $password = $params['password'];
        return memberClass::verifyLoginPassword($member_id,$password);
    }


    /** 密码登陆
     * @return result
     */
    public function passwordLoginOp()
    {
        // 首先检查APP是否被关闭了
        $app_state = global_settingClass::getMemberAppClosedState();
        if( $app_state['is_closed'] ){
            return new result(false,'App closed!',$app_state['closed_reason'],errorCodesEnum::APP_CLOSED);
        }
        $params = array_merge(array(),$_GET,$_POST);
        $rt = memberClass::passwordLogin($params,true);
        return $rt;

    }

    /** 验证设备通过后，获取登陆token
     * @return result
     */
    public function getLoginTokenByDeviceVerifyOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        return memberClass::getTokenAfterDeviceVerify($params);
    }


    /** 手势密码登陆
     * @return result
     */
    public function gestureLoginOp()
    {
        // 首先检查APP是否被关闭了
        $app_state = global_settingClass::getMemberAppClosedState();
        if( $app_state['is_closed'] ){
            return new result(false,'App closed!',$app_state['closed_reason'],errorCodesEnum::APP_CLOSED);
        }
        $params = array_merge(array(),$_GET,$_POST);
        return memberClass::gestureLogin($params);

    }

    /** 指纹登陆
     * @return result
     */
    public function fingerprintLoginOp()
    {
        // 首先检查APP是否被关闭了
        $app_state = global_settingClass::getMemberAppClosedState();
        if( $app_state['is_closed'] ){
            return new result(false,'App closed!',$app_state['closed_reason'],errorCodesEnum::APP_CLOSED);
        }
        $params = array_merge(array(),$_GET,$_POST);
        return memberClass::fingerprintLogin($params);
    }


    /**
     * APP 退出登录
     * @return result
     */
    public function appLogoutOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);
        if( !isset($params['member_id']) || !isset($params['client_type']) ){
            return new result(false,'Param lack',null,errorCodesEnum::DATA_LACK);
        }
        if( !$member_id ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        // 销毁token
        $m_member_token = new member_tokenModel();
        $where = " member_id='$member_id' ";
        $m_member_token->deleteWhere($where);

        $m_login_log = new member_login_logModel();
        $login_log = $m_login_log->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id,
            'client_type' => $params['client_type'],
        ));
        if( $login_log ){
            $login_log->logout_time = Now();
            $login_log->update_time = Now();
            $login_log->update();
        }

        return new result(true,'success');


    }

    /**
     * 忘记密码，重置密码
     * @return result
     */
    public function resetPwdOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $type = $params['type'];

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            switch( $type ){
                case 'sms':
                    $rt = memberClass::resetPwdBySms($params);
                    break;
                default:
                    $rt = new result(false,'Not supported type',null,errorCodesEnum::NOT_SUPPORTED);
            }
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }

            $conn->submitTransaction();
            return $rt;

        }catch (Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }


    }

    public function lockMemberOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        return member_profileClass::lockMember($member_id);
    }


    public function getMemberBaseInfoOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        //$member_id = $params['member_id'];
        $query_member_id = intval($params['query_member_id']);
        $member_info = memberClass::getMemberBaseInfo($query_member_id);
        return new result(true,'success',$member_info);
    }



    /** 身份证认证
     * @return result
     */
    public function idVerifyCertOp()
    {
        set_time_limit(120);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::idVerifyCert($params,0);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }


    /** 户口本认证
     * @return result
     */
    public function familyBookCertOp()
    {
        set_time_limit(120);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::familyBookVerifyCert($params,0);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }


    /** 居住证认证
     * @return result
     */
    public function residentBookCertOp()
    {
        set_time_limit(120);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::residentBookCert($params,0);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }



    /** 家庭关系认证
     * @return result
     */
    public function familyRelationshipCertOp()
    {
        set_time_limit(120);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::familyRelationshipCert($params,0);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }


    /** 工作认证
     * @return result
     */
    public function workCertOp()
    {
        set_time_limit(120);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::workCert($params,0);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }


    }

    /** 资产认证
     * @return result
     */
    public function assetCertOp()
    {
        set_time_limit(120);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::assetCert($params,0);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }

    public function assetCertNewOp()
    {
        set_time_limit(120);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::assetCertNew($params,certSourceTypeEnum::MEMBER);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }



    /** 贷款业务的
     * @return result
     */
    public function getMemberAceAccountInfoOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $re = memberClass::getMemberLoanAceAccountInfo($member_id);
        return $re;
    }



    public function message_listOp() {
        $re = $this->checkToken();
        if (!$re->STS) return $re;

        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if ($member_id <=0){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $page_num = $params['page_num'];
        $page_size = $params['page_size'];

        return member_messageClass::getReceivedMessages($member_id, $page_num, $page_size);
    }

    public function message_unread_countOp() {
        $re = $this->checkToken();
        if (!$re->STS) return $re;

        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if ($member_id <=0){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        return member_messageClass::getUnreadMessagesCount($member_id);
    }

    public function message_readOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <=0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $message_id = $params['message_id'];
        return member_messageClass::readMessage($member_id,$message_id);
    }

    public function message_deleteOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <=0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $messages = $params['message_id_list'];
        return member_messageClass::deleteMessages($member_id,$messages);
    }

    public function loanContractListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <=0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $re = memberClass::getLoanContractList($params);
        return $re;

    }

    public function changePwdOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::changePassword($params);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }


    }


    public function getCertedResultOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $re = memberClass::getMemberCertResult($params);
        return $re;
    }


    public function getAccountIndexInfoOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $re = memberClass::getMemberAccountSumInfo($member_id);
        return $re;
    }

    public function getInsuranceContractListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $re = memberClass::getInsuranceContractList($params);
        return $re;
    }


    public function getMemberLoanApplyListOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $re = memberClass::getMemberLoanConsultList($params);
        return $re;
    }

    public function getCreditHistoryOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return memberClass::getMemberCreditHistory($params);
    }


    public function editAvatorOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        return member_profileClass::editAvatar($member_id,$_FILES);
    }

    public function editLoginCodeOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $login_code = $params['login_code'];
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::editMemberLoginCode($member_id,$login_code);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }

    public function editNicknameOp()
    {
        return new result(false,'Function close',null,errorCodesEnum::FUNCTION_CLOSED);
    }

    public function getMemberQrcodeImageOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $url = ENTRY_API_SITE_URL.'/member.qrcode.image.php?member_id='.$member_id;
        return new result(true,'success',$url);
    }

    public function setGesturePasswordOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $gesture_pwd = trim($params['gesture_password']);
        return member_profileClass::setGesturePassword($member_id,$gesture_pwd);
    }

    public function setFingerprintOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $fingerprint = trim($params['fingerprint']);
        return member_profileClass::setFingerprintPassword($member_id,$fingerprint);

    }


    public function setTradingPasswordOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return member_profileClass::setTradingPasswordByLoginPasswordAndIdSn($params);

    }

    public function isSetTradingPasswordOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $return = memberClass::isSetTradingPassword($member_id);
        return new result(true,'success',$return);
    }


    public function setTradingPasswordVerifyAmountOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $amount = $params['amount'];
        $currency = $params['currency'];
        return member_profileClass::setTradingPasswordVerifyAmount($member_id,$amount,$currency);
    }



    public function getLoanBindAutoDeductionAccountOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        return memberClass::getLoanBindAutoDeductionAccount($member_id);

    }

    public function forgotGestureOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        return member_profileClass::forgotGesturePassword($member_id);
    }


    public function assetDeleteOp()
    {

        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $asset_id = $params['asset_id'];

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::deleteAsset($member_id,$asset_id);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }

    public function deleteFamilyRelationshipOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $relation_id = $params['relation_id'];

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::deleteFamilyRelationship($member_id,$relation_id);
            if( $re->STS ){
                $conn->submitTransaction();
                return $re;
            }else{
                $conn->rollback();
                return $re;
            }

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }


    public function getMemberLoanSummaryOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $own_re = memberClass::getMemberLoanSummary($member_id,1);
        if( !$own_re->STS ){
            return $own_re;
        }
        $own_loan_summary = $own_re->DATA;

        $guarantee_loan_re = memberClass::getMemberLoanSummary($member_id,2);
        if( !$guarantee_loan_re->STS ){
            return $guarantee_loan_re;
        }
        $guarantee_loan_summary = $guarantee_loan_re->DATA;

        return new result(true,'success',array(
            'own_loan_summary' => $own_loan_summary,
            'as_guarantee_loan_summary' => $guarantee_loan_summary
        ));

    }


    /** 添加担保人
     * @return result
     */
    public function addGuaranteeOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);

        return memberClass::addGuaranteeApply($params);

    }


    public function guaranteeConfirmOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return memberClass::guaranteeApplyHandle($params);

    }


    public function getMemberGuaranteeListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        // 担保人列表
        $m = new member_guaranteeModel();
        $list1 = $m->getMemberGuaranteeList($member_id);


        // 作为担保人的（申请+通过的）
        $list2 = $m->getAsGuaranteeMemberList($member_id);


        return new result(true,'success',array(
            'guarantee_list' => $list1,
            'apply_list' => $list2
        ));

    }


    public function queryMemberCreditOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $credit = memberClass::getCreditBalance($member_id);
        return new result(true,'success',$credit);
    }


    public function getMemberCreditProcessOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id'])?:0;
        return memberClass::getMemberCreditProcess($member_id);
    }


    public function getMemberLoanReceivedRecordOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $list = memberClass::getLoanReceivedRecord($member_id);
        return new result(true,'success',$list);
    }

    public function getMemberLoanRepaymentRecordOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $contract_id = $params['contract_id'];
        $page_num = $params['page_num']?:1;
        $page_size = $params['page_size']?:100000;
        $data = memberClass::getLoanRepaymentRecord($member_id,$page_num,$page_size,$contract_id,$params);
        return new result(true,'success',$data);
    }

    public function bindBankAccountOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $re = memberClass::memberBindBankAccount($params);
        return $re;

    }

    public function getBindBankListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $bank_list = member_handlerClass::getMemberBindBankList($member_id);
        return new result(true,'success',$bank_list);
    }


    public function getMemberMortgageGoodsListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $list = memberClass::getMemberMortgagedGoodsList($member_id);
        return new result(true,'success',array(
            'list' => $list
        ));
    }

    public function deleteBindBankOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $bind_id = $params['bind_id'];
        $m_handler = new member_account_handlerModel();
        $handler = $m_handler->getRow(array(
            'uid' => $bind_id,
            'member_id' => $member_id
        ));
        if( !$handler ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $handler->state = accountHandlerStateEnum::HISTORY;
        $handler->update_time = Now();
        $up = $handler->update();
        if( !$up->STS ){
            return new result(false,'Delete fail',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success');

    }

    public function prepaymentApplyCancelOp()
    {
        // todo 好像是没使用了
        return new result(false,'No used.',null,errorCodesEnum::FUNCTION_CLOSED);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $apply_id = $params['apply_id'];
        $m = new loan_prepayment_applyModel();
        $apply = $m->getRow($apply_id);
        if( !$apply ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        if( $apply->state != prepaymentApplyStateEnum::CREATE ){
            return new result(false,'Handling...',null,errorCodesEnum::HANDLING_LOCKED);
        }
        $delete = $apply->delete();
        if( !$delete->STS ){
            return new result(false,'Cancel fail',null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success');
    }

    public function getSavingsBalanceOp()
    {

        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'Member not exist',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $memberObject = new objectMemberClass($member_id);
        $cny_balance = $memberObject->getSavingsAccountBalance();

        return new result(true,'success',array(
            'savings_balance' => $cny_balance
        ));
    }


    /** 存取款绑定账户
     * @return result
     */
    public function getMemberBizAccountHandlerOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];

        $list = member_handlerClass::getMemberBindAllOnlineBankList($member_id);

        $re_list = array();
        foreach( $list as $v ){

            $temp = array(
                'handler_id' => $v['uid'],
                'name' => $v['bank_name'],
                'logo' => global_settingClass::getBankLogoByBankCode($v['bank_code']),
                'short_account' => substr($v['handler_account'],-4),
                'is_supported' => 1,
                'is_online_bank' => 1,
            );

            $re_list[] = $temp;
        }

        return new result(true,'success',array(
            'bank_list' => $re_list
        ));
    }

    public function addMemberAddressOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return member_profileClass::addCommonAddress($params);

    }


    public function getMemberLoanHistoryOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $page_num = $params['page_num'];
        $page_size = $params['page_size'];


        $re = memberClass::getMemberLoanHistory($member_id,$page_num,$page_size,$params);

        return $re;

    }


    public function getLoanPendingRepaymentListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $page_num = $params['page_num']?:1;
        $page_size = $params['page_size']?:100000;

        $data = memberClass::getLoanPendingRepaymentSchema($member_id,$page_num,$page_size,$params);

        return new result(true,'success',$data);

    }

    public function getLoanPendingRepaymentListGroupByDayOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $data = memberClass::getMemberAllPendingRepaymentSchemaGroupByDay($member_id,$params);
        return new result(true,'success',$data);
    }


    public function searchMemberOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $keyword = $params['keyword'];
        $list = memberClass::searchMemberList($keyword);
        return new result(true,'success',array(
            'member_list' => $list
        ));


    }


    public function setTradingPasswordByRegisterOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $trading_password = $params['trading_password'];
        $sms_id = $params['sms_id'];
        $sms_code = $params['sms_code'];
        return member_profileClass::setTradingPasswordBySms($member_id,$sms_id,$sms_code,$trading_password);
    }


    /** 通过短信设置交易密码
     * @return result
     */
    public function setTradingPasswordBySmsOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $trading_password = $params['trading_password'];
        $sms_id = $params['sms_id'];
        $sms_code = $params['sms_code'];
        return member_profileClass::setTradingPasswordBySms($member_id,$sms_id,$sms_code,$trading_password);
    }


    public function changeTradingPasswordOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $old_password = $params['old_password'];
        $new_password = $params['new_password'];
        return member_profileClass::changeTradingPassWordByOldPassword($member_id,$old_password,$new_password);
    }


    public function getMemberAllTradingTypeOp()
    {
        $list = global_settingClass::getMemberTradingType();
        return new result(true,'success',array(
            'list' => $list
        ));
    }


    public function getMemberNextRepaymentSchemaOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];

        $nextRepaymentSchema = member_loan_schemaClass::getMemberNextRepaymentSchema($member_id,$params);

        return new result(true,'success',$nextRepaymentSchema);

    }


    public function getRelativePersonListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $relative_list = member_relativeClass::getMemberRelativeList($member_id);
        return new result(true,'success',array(
            'list' => $relative_list
        ));
    }
    public function getModuleEntranceOp(){
        $setting=global_settingClass::getModuleBusinessSetting(bizSceneEnum::APP_MEMBER);
        $ret=array(
            moduleBusinessEnum::MODULE_HOME=>array_merge($setting[moduleBusinessEnum::MODULE_HOME],array("list"=>array(
                $setting[moduleBusinessEnum::MODULE_DEPOSIT],
                $setting[moduleBusinessEnum::MODULE_WITHDRAW],
                $setting[moduleBusinessEnum::MODULE_CREDIT],
                $setting[moduleBusinessEnum::MODULE_CERTIFICATION],
                $setting[moduleBusinessEnum::MODULE_EXCHANGE],
                $setting[moduleBusinessEnum::MODULE_BRANCH]
            ))),
            moduleBusinessEnum::MODULE_SERVICE=>array_merge($setting[moduleBusinessEnum::MODULE_SERVICE],array("list"=>array())),
            moduleBusinessEnum::MODULE_SAVINGS=>array_merge($setting[moduleBusinessEnum::MODULE_SAVINGS],array("list"=>array()))
        );
        return new result(true,'success',$ret);
    }


    public function getMemberCreditHistoryOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $list = member_creditClass::getMemberCreditHistory($member_id);
        return new result(true,'success',array(
            'list' => $list
        ));
    }

    public function submitPersonalFileOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return member_profileClass::submitPersonalFileCert($params,certSourceTypeEnum::MEMBER);
    }



}
