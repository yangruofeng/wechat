<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/1
 * Time: 15:18
 */
class officerControl extends bank_apiControl
{

    // operator app 操作类

    /** 账号登陆
     * @return result
     */
    public function codeLoginOp()
    {

        $params = array_merge($_GET,$_POST);
        $user_code = trim($params['user_code']);
        $password = trim($params['password']);
        $client_type = $params['client_type'];

        $rt = credit_officerClass::loginByloginCode($user_code,$password,$client_type);
        return $rt;
    }

    public function gestureLoginOp()
    {

        $params = array_merge($_GET,$_POST);
        $officer_id = $params['officer_id'];
        $gesture_password = trim($params['gesture_password']);
        $client_type = $params['client_type'];

        $rt = credit_officerClass::loginByGesture($officer_id,$gesture_password,$client_type);
        return $rt;
    }

    public function fingerprintLoginOp()
    {

        $params = array_merge($_GET,$_POST);
        $officer_id = $params['officer_id'];
        $fingerprint_password = trim($params['fingerprint_password']);
        $client_type = $params['client_type'];

        $rt = credit_officerClass::loginByFingerprint($officer_id,$fingerprint_password,$client_type);
        return $rt;
    }

    /** 用户登陆
     * @return result
     */
    public function loginOp()
    {
        // 首先检查APP是否被关闭了
        $app_state = global_settingClass::getCreditOfficerAppClosedState();
        if( $app_state['is_closed'] ){
            return new result(false,'App closed!',$app_state['closed_reason'],errorCodesEnum::APP_CLOSED);
        }

        $params =  $params = array_merge($_GET,$_POST);
        $login_type = intval($params['login_type']);
        switch( $login_type ){
            case 0:
                // 账号
                $rt = $this->codeLoginOp();
                break;
            case 1:
                // 手势密码
                $rt = $this->gestureLoginOp();
                break;
            case 2:
                // 指纹密码
                $rt = $this->fingerprintLoginOp();
                break;
            default:
                $rt = $this->codeLoginOp();

        }
        return $rt;
    }

    public function logoutOp()
    {
        $params = array_merge($_GET,$_POST);
        $officer_id = $params['officer_id'];
        $token = $params['token'];
        $client_type = $params['client_type'];

        $rt = credit_officerClass::logout($officer_id,$client_type);
        return $rt;

    }

    public function submitMemberCertIdOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::idVerifyCert($params,certSourceTypeEnum::OPERATOR);
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

    public function submitMemberCertFamilyBookOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::familyBookVerifyCert($params,certSourceTypeEnum::OPERATOR);
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

    public function submitMemberCertResidentBookOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::residentBookCert($params,certSourceTypeEnum::OPERATOR);
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

    public function submitMemberCertWorkOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::workCert($params,certSourceTypeEnum::OPERATOR);
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


    public function submitMemberCertAssetsNewOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }else{
            $token=$re->DATA;
        }
        $params = array_merge(array(),$_GET,$_POST);

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{
            $re = memberClass::assetCertNew($params,certSourceTypeEnum::OPERATOR);

            if( !$re->STS ){
                $conn->rollback();
                return $re;
            }else{
                $conn->submitTransaction();
            }

            //add by tim, 当后台设置co提交的资料是否需要operator审批，不需要的话就自动审批
            $cert_row=$re->DATA['cert_result'];
            if($cert_row && $cert_row instanceof ormDataRow){
                if($cert_row->verify_state==certStateEnum::CREATE){
                    $is_auto=!global_settingClass::isAllowOperatorApproveAssetsByCO();
                    if($is_auto){
                        $cert_row->verify_state=certStateEnum::PASS;
                        $cert_row->verify_remark="Auto Approve By System";
                        $cert_row->auditor_id=$token['user_id'];
                        $cert_row->auditor_name=$token['user_name'];
                        $cert_row->auditor_time=Now();
                        $ret_update=$cert_row->update();
                        if($ret_update->STS){
                            $m_asset=new member_assetsModel();
                            $asset_row=$m_asset->getRow(array("cert_id"=>$cert_row->uid));
                            if($asset_row){
                                $asset_row->asset_state=assetStateEnum::CERTIFIED;
                                $asset_row->update_time=Now();
                                $ret_update=$asset_row->update();
                            }
                        }
                    }

                }
            }

            return $re;

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }



    public function submitMemberCertAssetsOp()
    {
        return new result(false,'Give up use',null,errorCodesEnum::NOT_SUPPORTED);
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }else{
            $token=$re->DATA;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = memberClass::assetCert($params,certSourceTypeEnum::OPERATOR);
            if( $re->STS ){
                $conn->submitTransaction();
                //add by tim, 当后台设置co提交的资料是否需要operator审批，不需要的话就自动审批
                $cert_row=$re->DATA['cert_result'];
                if($cert_row && $cert_row instanceof ormDataRow){
                    if($cert_row->verify_state==certStateEnum::CREATE){
                        $is_auto=!global_settingClass::isAllowOperatorApproveAssetsByCO();
                        if($is_auto){
                            $cert_row->verify_state=certStateEnum::PASS;
                            $cert_row->verify_remark="Auto Approve By System";
                            $cert_row->auditor_id=$token['user_id'];
                            $cert_row->auditor_name=$token['user_name'];
                            $cert_row->auditor_time=Now();
                            $ret_update=$cert_row->update();
                            if($ret_update->STS){
                                $m_asset=new member_assetsModel();
                                $asset_row=$m_asset->getRow(array("cert_id"=>$cert_row->uid));
                                if($asset_row){
                                    $asset_row->asset_state=assetStateEnum::CERTIFIED;
                                    $asset_row->update_time=Now();
                                    $ret_update=$asset_row->update();
                                }
                            }
                        }

                    }
                }


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


    public function addExtendImageForMemberAssetOp()
    {

        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::addExtendImageForAsset($params);
    }

    public function searchMemberOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $member = memberClass::searchMember($params);
        return new result(true,'success',$member);
    }



    public function getMemberAllCertResultOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $re = memberClass::getMemberCertStateOrCount($member_id);
        return $re;
    }


    public function getMemberCertDetailInfoOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $re = memberClass::getMemberCertResult($params);
        return $re;
    }


    public function getCoFollowedMemberOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $chk_data=$re->DATA;
        $position=$chk_data['user_position'];
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = intval($params['officer_id']);
        if($position==userPositionEnum::CHIEF_CREDIT_OFFICER){
            $list=chief_credit_officerClass::getFollowedMemberList($chk_data['branch_id']);
        }else{
            $list = credit_officerClass::getFollowedMemberList($officer_id);
        }

        return new result(true,'success',$list);

    }


    /** 为客户提交贷款申请
     * @return result
     */
    public function addLoanRequestOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);

        return credit_officerClass::submitLoanRequestForMember($params);

    }


    public function addMemberGuaranteeRequestOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }

        $params = array_merge(array(),$_GET,$_POST);

        return credit_officerClass::submitGuaranteeRequestForMember($params);

    }


    public function getMemberGuaranteeListOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }

        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $list = memberClass::getMemberPassedGuaranteeList($member_id);

        return new result(true,'success',$list);

    }

    public function getMemberLoanRequestListOp()
    {
        return new result(true);
        $params = array_merge(array(),$_GET,$_POST);
        return memberClass::getMemberLoanApplyList($params);
    }


    public function getCoBoundLoanRequestOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }

        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::getBoundLoanRequest($params);

    }


    public function getLoanRequestDetailOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $request_id = $params['request_id'];
        $m_loan_apply = new loan_applyModel();
        $apply = $m_loan_apply->find(array(
            'uid' => $request_id
        ));
        if( !$apply ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        return new result(true,'success',array(
            'request_detail' => $apply
        ));
    }


    public function getAllLoanProductOp()
    {
        $m = new loan_productModel();
        $list = $m->getAllProductListOfSimpleData();
        return new result(true,'success',array(
            'list' => $list
        ));
    }


    public function loanRequestBindMemberOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }

        $params = array_merge(array(),$_GET,$_POST);
        $request_id = $params['request_id'];
        $member_id = $params['member_id'];
        return credit_officerClass::bindMemberForLoanRequest($request_id,$member_id);

    }


    public function loanRequestCheckOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }

        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::loanRequestCheck($params);

    }


    public function loanRequestBindProductOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::loanRequestBindProduct($params);

    }


    public function loanRequestCoApprovedOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $request_id = $params['request_id'];
        return credit_officerClass::loanRequestLastApproved($request_id);

    }


    public function getBoundMemberLoanContractListOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 10000;

        $page_data = userClass::getUserBoundMemberLoanContractList($officer_id,$page_num,$page_size);
        return new result(true,'success',$page_data);

    }


    public function getLoanContractDetailOp()
    {

        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $contract_id = $params['contract_id'];
        $re = loan_contractClass::getLoanContractDetailInfo($contract_id);
        return $re;

    }


    /** 签到
     * @return result
     */
    public function signInOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::signIn($params);
    }

    public function getOfficerFootprintOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $date = trim($params['date']);
        $lists = userClass::getUserFootPrintOfDay($officer_id,$date);
        return new result(true,'success',$lists);
    }


    public function getOfficerFootprintListOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $page_num = intval($params['page_num'])?:1;
        $page_size = intval($params['page_size'])?:100000;
        $page_data = userClass::getUserFootprintPageListGroupByDay($officer_id,$page_num,$page_size);
        return new result(true,'success',$page_data);
    }


    public function getOfficerBaseInfoOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        return userClass::getUserBaseInfo($officer_id);
    }

    /** 获取对客户资产的估值
     * @return result
     */
    public function getMemberAssetsEvaluateOp(){
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $officer_id = $params['officer_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $data = credit_officerClass::getLatestMemberAssetEvaluation($officer_id,$member_id);
        return new result(true,'success',$data);
    }


    public function getMemberAllAssetsListOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $officer_id = $params['officer_id'];
        if( $member_id <= 0 ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $data = credit_officerClass::getMemberAssetsListAndEvaluateOfOfficerGroupByType($member_id,$officer_id,userPositionEnum::CREDIT_OFFICER,array(
            'is_include_invalid' => 1
        ));

        // 获得系统设置的信息
        $page_data = (new member_assetsClass())->_initAPPCertPage();
        foreach( $page_data as $key=>$value ){
            $asset_type = $value['asset_type'];
            $member_asset_list = $data[$asset_type]?:null;
            $value['member_asset_list'] = $member_asset_list;
            $page_data[$key] = $value;
        }

        return new result(true,'success',array(
            'asset_page_data' => $page_data,
            'list' => $data
        ));
    }

    public function getMemberAssetDetailOp()
    {
        return new result(false,'Give up using.',null,errorCodesEnum::NOT_SUPPORTED);
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $asset_id = $params['asset_id'];
        $m = new member_assetsModel();
        $asset = $m->getRow($asset_id);
        if( !$asset ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        return new result(true,'success',array(
            'asset_detail' => $asset
        ));
    }

    public function submitMemberAssetsEvaluateOp(){
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::submitMemberAssetsEvaluate($params);
    }


    /** 对某项资产的评估历史
     * @return result
     */
    public function getMemberAssetEvaluateHistoryOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $asset_id = $params['asset_id'];
        $officer_id = $params['officer_id'];
        $list = userClass::getOneAssetEvaluateHistoryForMember($officer_id,$asset_id);
        return new result(true,'success',array(
            'list' => $list
        ));
    }


    /** 获取员工评估member信用的参考和系统授信建议
     * @return result
     */
    public function getMemberCreditReferenceOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);
        $officer_id = intval($params['officer_id']);
        return userClass::getMemberCreditReferenceInfo($member_id,$officer_id);

    }



    /** 提交信用授信建议
     * @return result
     */
    public function submitMemberSuggestCreditOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::submitMemberSuggestCredit($params);
    }


    public function getMemberCreditSuggestHistoryOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);
        $officer_id = intval($params['officer_id']);
        $list = userClass::getMemberCreditSuggestHistoryByUser($member_id,$officer_id);
        return new result(true,'success',array(
            'list' => $list
        ));
    }



    public function getMemberWorkDetailOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);
        $m = new member_workModel();
        $work = $m->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id
        ));

        return new result(true,'success',array(
            'work_detail' => $work
        ));

    }

    public function getMemberAssessmentOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);

        // 会员评估
        $data = memberClass::getMemberAssessment($member_id);

        return new result(true,'success',$data);
    }


    public function getTaskSummaryOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = intval($params['officer_id']);

        $data = credit_officerClass::getTaskSummary($officer_id);

        return new result(true,'success',$data);

    }


    public function getMemberResidencePlaceOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $lang = $params['lang']?:'en';
        return memberClass::getMemberResidencePlace($member_id,$lang);
    }

    public function getMemberResidenceAddressMapInfoOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $officer_id = intval($params['officer_id']);
        $map_info = memberClass::getMemberResidenceAddressMapInfo($member_id);
        return new result(true,'success',array(
            'map_info' => $map_info
        ));

    }


    public function editMemberResidencePlaceOp()
    {

        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = userClass::editMemberResidencePlace($params);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch( Exception $e )
        {
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }

    public function editMemberResidenceAddressMapInfoOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = userClass::editMemberResidencePlaceMapInfo($params);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch( Exception $e )
        {
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }


    public function editLoginPasswordOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = intval($params['officer_id']);
        $old_pwd = trim($params['old_pwd']);
        $new_pwd = trim($params['new_pwd']);
        return userClass::changeLoginPasswordByOldPassword($officer_id,$old_pwd,$new_pwd);

    }


    public function getMemberDetailInfoOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $officer_id = intval($params['officer_id']);
        if( !$member_id  ){
            return new result(false,'Lack of param.',null,errorCodesEnum::INVALID_PARAM);
        }
        $member_info = memberClass::getMemberBaseInfo($member_id);
        if( !$member_info ){
            return new result(false,'Member not exist',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // officer对member的各项评估结果
        $data = userClass::getMemberAllResearchByUser($officer_id,$member_id,false);

        return new result(true,'success',array(
            'member_detail' => $member_info,
            'officer_research_result' => $data
        ));

    }


    public function verifyLoginPasswordOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $password = $params['password'];
        $is_right = userClass::verifyLoginPassword($officer_id,$password);
        return new result(true,'success',array(
            'is_ok' => $is_right
        ));

    }

    public function setTradingPasswordOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $login_pwd = $params['login_password'];
        $trading_password = $params['trading_password'];
        return userClass::setTradingPassword($officer_id,$login_pwd,$trading_password);
    }

    public function setGesturePasswordOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $gesture_password = $params['gesture_password'];

        return userClass::setGesturePassword($officer_id,$gesture_password);
    }

    public function forgotGesturePasswordOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];

        return userClass::clearGesturePassword($officer_id);
    }

    public function setFingerprintPasswordOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $fingerprint_password = trim($params['fingerprint_password']);
        return userClass::setFingerprintPassword($officer_id,$fingerprint_password);
    }


    /** 电话注册会员
     * @return result
     */
    public function memberPhoneRegisterOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $params['open_source'] = memberSourceEnum::CO;
        return memberClass::phoneRegisterNew($params);
    }


    public function submitMemberIncomeSalaryOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = credit_officerClass::addMemberIncomeSalary($params,$_FILES);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch (Exception $e){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }

    public function editMemberIncomeSalaryOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = credit_officerClass::editMemberIncomeSalary($params,$_FILES);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch (Exception $e){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }

    public function getLastMemberIncomeSalaryOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        return credit_officerClass::getMemberIncomeSalaryListAndSummary($member_id,false);

    }

    public function deleteMemberIncomeSalaryOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $uid = $params['uid'];
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = credit_researchClass::deleteMemberIncomeSalaryByUid($uid);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch (Exception $e){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }


    public function submitMemberAssetsRentalResearchOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = credit_officerClass::addMemberAssetRentalResearch($params,$_FILES);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch (Exception $e){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }

    public function getLastMemberAssetRentalResearchOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $asset_id = $params['asset_id'];
        $detail = credit_researchClass::getLastMemberAssetRentalResearch($asset_id);
        return new result(true,'success',array(
            'detail' => $detail
        ));
    }

    public function editMemberAssetRentalResearchOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = credit_officerClass::editMemberAssetRentalResearch($params,$_FILES);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch (Exception $e){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }


    public function deleteMemberAssetRentalResearchOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $uid = $params['uid'];
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = credit_researchClass::deleteMemberAssetRentalByUid($uid);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch (Exception $e){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }


    public function getMemberIndustryListOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $member_id = $params['member_id'];

        // 兼容旧方式的数据
        $industry_list = memberClass::getMemberIndustryInfo($member_id,false);
        $total_income = 0;
        foreach( $industry_list as $v ){
            $total_income += $v['profit'];
        }


        $data = memberBusinessClass::getMemberIndustryAndResearchByOfficer($member_id,$officer_id,false);
        return new result(true,'success',array(
            'total_income' => $total_income,
            'list' => $industry_list,
            'total_profit' => $data['total_profit'],
            'industry_list' => $data['industry_list']
        ));
    }


    /** 提交商业收入调查
     * @return result
     */
    public function submitMemberIncomeBusinessOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = credit_officerClass::addMemberIncomeBusinessResearch($params,$_FILES);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch (Exception $e){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }


    public function getMemberIncomeBusinessResearchDetailOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $user_data=$re->DATA;
        $user_id=$user_data['user_id'];
        $user_position=$user_data['user_position'];
        $user_name = $user_data['user_name'];
        $branch_id=$user_data['branch_id'];

        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $industry_id = $params['industry_id'];
        $uid = intval($params['research_id']);

        $arr_label=array("left"=>"Client","right"=>"Me");


        if( $uid ){
            $rt =  memberBusinessClass::getMemberIncomeBusinessEditInfo($uid);
            $rt->DATA['label'] = $arr_label;
            return $rt;
        }else{
            $branch_code=$params['branch_code'];
            $research=array();

            if($user_position==userPositionEnum::CHIEF_CREDIT_OFFICER){
                //取最后一个co的调查
                $research = memberBusinessClass::getLastMemberIncomeBusinessInfoByIndustryBranchCode($member_id,$industry_id,$branch_code);
                $arr_label['left']=$research['reference_name'];
            }elseif($user_position==userPositionEnum::CREDIT_CONTROLLER || $user_position==userPositionEnum::RISK_CONTROLLER){
                //取非自己的最后调查结果
                $research = memberBusinessClass::getLastMemberIncomeBusinessInfoByIndustryBranchCode($member_id,$industry_id,$branch_code,$user_id);
                $arr_label['left']=$research['reference_name'];
            }
            $data = memberBusinessClass::getMemberIncomeBusinessPageData($member_id,$industry_id,$research);
            $data['label']=$arr_label;
            return  new result(true,'success',$data);
        }

    }

    public function deleteMemberIncomeBusinessOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $uid = intval($params['research_id']);
        $member_id = $params['member_id'];
        $industry_id = $params['industry_id'];

        if( $uid > 0 ){
            return credit_researchClass::deleteMemberIncomeBusiness($uid);
        }else{
            return new result(true,'success');
        }


    }



    public function getMemberIncomeBusinessAddDataOp()
    {
        // 弃用了，合并到getMemberIncomeBusinessResearchDetailOp方法，research_id值为0即可
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $industry_id = $params['industry_id'];

        $arr_label=array("left"=>"Client","right"=>"Me");
        //如果是chief-credit-officer,则可以修改最后一个co的调查结果

        //如果是credit-controller和risk-controller,则可以修改任何一个人的调查结果
        $research = array();
        $data = memberBusinessClass::getMemberIncomeBusinessPageData($member_id,$industry_id,$research);
        $data['label']=$arr_label;
        return new result(true,'success',$data);
    }

    public function getLastMemberIncomeBusinessResearchOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $member_id = $params['member_id'];
        $industry_id = $params['industry_id'];

        $research = credit_researchClass::getLastMemberBusinessIncomeResearchOfOfficer($member_id,$industry_id,$officer_id);
        $data = memberBusinessClass::getMemberIncomeBusinessPageData($member_id,$industry_id,$research);
        return new result(true,'success',$data);

    }


    /** 获取客户的历史收入调查报告
     * @return result
     */
    public function getMemberIncomeResearchHistoryOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $member_id = $params['member_id'];
        $list = userClass::getMemberIncomeResearchHistoryOfUser($member_id,$officer_id);
        return new result(true,'success',array(
            'list' => $list
        ));
    }


    /** 获取客户可跟进的CO
     * @return result
     */
    public function getMemberCreditOfficerListOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $list = memberClass::getMemberCreditOfficerList($member_id);
        return new result(true,'success',array(
            'list' => $list
        ));
    }


    /** 获得分配到CO的贷款咨询
     * @return result
     */
    public function getLoanConsultListOp()
    {

        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::getAllotLoanConsultPageListResult($params);

    }


    /** 处理贷款咨询
     * @return result
     */
    public function submitLoanConsultHandleOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::submitLoanConsultHandle($params);

    }

    public function getOverdueContractListInfoOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $list = credit_officerClass::getAllAllotOverdueContractListInfo($officer_id);
        return new result(true,'success',array(
            'list' => $list
        ));
    }

    public function getOverdueContractTaskListOp(){
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::getAllotOverdueContractListResult($params);
    }

    public function getOverdueContractTaskDetailOp(){
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $uid = $params['uid'];
        return credit_officerClass::getOverdueContractTaskDetail($uid);

    }



    public function submitEditOverdueOp(){
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::submitOverDueContractDunInfo($params);
    }

    public function submitMemberBusinessSceneOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $research_id = intval($params['research_id']);
        $member_id = $params['member_id'];
        $officer_id = $params['officer_id'];
        $industry_id = $params['industry_id'];
        $files = $_FILES;
        return credit_officerClass::submitMemberBusinessImage($research_id,$member_id,$industry_id,$files,$officer_id);
    }

    public function getMemberBusinessSceneOp()
    {

        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $list = credit_officerClass::getMemberBusinessPhotoPageList($params);
        return new result(true,'success',$list);
    }

    public function deleteMemberBusinessSceneOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $uid = $params['uid'];
        $officer_id = $params['officer_id'];

        return credit_officerClass::deleteMemberBusinessPhoto($uid,$officer_id);
    }

    public function submitMemberBusinessScene_oldOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $type = intval($params['type'])?:businessPhotoTypeEnum::PLACE_SCENE;
        $member_id = $params['member_id'];
        $officer_id = $params['officer_id'];
        $files = $_FILES;
        $remark = $params['remark'];
        return credit_officerClass::submitMemberBusinessPhotoOld($type,$officer_id,$member_id,$files,$remark);
    }


    public function submitMemberContractPhotoOp()
    {
        return new result(false,'Closed',null,errorCodesEnum::FUNCTION_CLOSED);
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $officer_id = $params['officer_id'];
        $files = $_FILES;
        $remark = $params['remark'];
        return credit_officerClass::submitMemberContractPhoto($officer_id,$member_id,$files,$remark);
    }

    public function getMemberContractPhotoOp()
    {
        return new result(false,'Closed',null,errorCodesEnum::FUNCTION_CLOSED);
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $list = credit_officerClass::getMemberContractPhotoPageList($params);
        return new result(true,'success',$list);
    }

    public function deleteMemberContractPhotoOp()
    {
        return new result(false,'Closed',null,errorCodesEnum::FUNCTION_CLOSED);
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::deleteMemberContractPhoto($params);
    }


    public function receiveLoanFromMemberOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::receiveLoanFromMember($params);
    }

    public function getBranchTellerListOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $list = userClass::getBranchTellerListOfUser($officer_id);
        // 处理
        $teller_list = array();
        foreach( $list as $user ){
            $teller_list[] = array(
                'teller_id' => $user['uid'],
                'teller_name' => $user['user_name']
            );
        }

        return new result(true,'success',array(
            'list' => $teller_list
        ));
    }

    public function transferToTellerSubmitOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::transferToTellerSubmit($params);
    }


    public function getReceiveLoanListFromClientOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $page_list = credit_officerClass::getReceiveLoanListFromClient($params);
        return new result(true,'success',$page_list);
    }

    public function getTransferToTellerListOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $page_list = credit_officerClass::getTransferToTellerList($params);
        return new result(true,'success',$page_list);
    }

    public function getPassbookFlowByTypeOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $currency = $params['currency'];
        $page_num = $params['page_num'];
        $page_size = $params['page_size'];
        $type = $params['type'];  // 0 all  1 income  -1 out
        $page_list = userClass::getUserPassbookFlowByType($officer_id,$currency,$page_num,$page_size,$type);
        return new result(true,'success',$page_list);

    }

    public function getPassbookFlowDetailOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $uid = $params['uid'];
        $m_flow = new passbook_account_flowModel();
        $detail = $m_flow->getFlowDetailById($uid);
        if( !$detail ){
            return new result(false,'Invalid param.',null,errorCodesEnum::INVALID_PARAM);
        }
        $trading_type_lang = enum_langClass::getPassbookTradingTypeLang();
        $detail['trading_type_lang'] = ($trading_type_lang[$detail['trading_type']])?:$detail['trading_type'];

        return new result(true,'success',array(
            'flow_detail' => $detail
        ));

    }


    public function deleteMemberAssetOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $asset_id = $params['asset_id'];
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{
            $rt = credit_officerClass::deleteMemberAsset($asset_id,$officer_id);assetStateEnum::CERTIFIED;
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

    public function updateMemberAssetStateOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $officer_id = $params['officer_id'];
        $asset_id = $params['asset_id'];
        $is_invalid = $params['is_invalid']?1:0;
        $rt = credit_officerClass::updateMemberAssetState($asset_id,$officer_id,$is_invalid);
        return $rt;
    }


    public function memberAssetAddSurveyInfoOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $asset_id = $params['asset_id'];
        $officer_id = $params['officer_id'];
        $research_json = urldecode($params['asset_research_json']);
        if( !$asset_id || !$officer_id ){
            return new result(false,'Invalid param.',null,errorCodesEnum::INVALID_PARAM);
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = credit_researchClass::assetAddSurveyInfo($asset_id,$research_json,$officer_id);
            if( !$rt->STS ){
                $conn->rollback();
            }else{
                $conn->submitTransaction();
            }
            return $rt;

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }

    public function getCheckMemberAssetDetailInfoOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $asset_id = $params['asset_id'];
        $officer_id = $params['officer_id'];
        return credit_officerClass::getMemberAssetDetailAndResearchInfo($asset_id,$officer_id);

    }

    public function editMemberAssetAddressOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::editMemberAssetAddress($params);
    }


    public function editMemberAssetRelativeOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::editMemberAssetRelative($params);
    }


    public function submitMemberAttachmentOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = credit_officerClass::addMemberAttachment($params,$_FILES);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch (Exception $e){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }

    public function editMemberAttachmentOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = credit_officerClass::editMemberAttachment($params,$_FILES);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch (Exception $e){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }

    }

    public function getMemberAttachmentListOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $list = credit_researchClass::getMemberAttachmentList($member_id,false);
        $total_income = member_statisticsClass::getMemberTotalOtherAttachmentIncome($member_id);
        return new result(true,'success',array(
            'total_income' => $total_income,
            'list' => $list
        ));
    }


    public function deleteMemberAttachmentOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $uid = $params['uid'];
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = credit_researchClass::deleteMemberAttachmentByUid($uid);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch (Exception $e){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }


    public function getMemberCreditRequestListOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $m = new member_credit_requestModel();
        $list = $m->getMemberRequestListAndRelative($member_id);
        $is_can_add_new = $m->isCanAddNewRequest($member_id);

        return new result(true,'success',array(
            'list' => $list,
            'is_can_add_new_request' => $is_can_add_new
        ));
    }

    public function getMemberCreditRequestDetailOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $request_id = $params['request_id'];
        $detail = credit_officerClass::getMemberCreditRequestDetail($request_id);
        if( !$detail ){
            return new result(false,'No info.',null,errorCodesEnum::INVALID_PARAM);
        }
        return new result(true,'success',array(
            'detail_info' => $detail
        ));
    }


    public function editMemberCreditRequestOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::editMemberCreditRequest($params);

    }

    public function addCreditRequestRelativeOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::addCreditRequestRelative($params);
    }


    public function editCreditRequestRelativeOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::editCreditRequestRelative($params);
    }

    public function deleteCreditRequestRelationOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $relation_id = intval($params['relation_id']);
        return credit_officerClass::deleteCreditRequestRelative($relation_id);
    }


    public function getMemberRelativeListOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = intval($params['member_id']);
        $list = member_relativeClass::getMemberRelativeList($member_id);
        return new result(true,'success',array(
            'list' => $list
        ));
    }


    public function editAvatorOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $user_id = $params['officer_id'];
        if( empty($_FILES['user_image']) ){
            return new result(false,'No upload image.',null,errorCodesEnum::INVALID_PARAM);
        }

        $save_path = 'user';
        $upload = new UploadFile();
        $upload->set('save_path', null);
        $upload->set('default_dir', $save_path);
        $re = $upload->server2upun('user_image');
        if ($re == false) {
            return new result(false, 'Upload photo fail:'.$upload->getError(), null, errorCodesEnum::UPLOAD_PIC_TO_UPYUN_FAIL);
        }
        $img_path = $upload->img_url;

        return userClass::editAvator($user_id,$img_path);

    }


    public function editLoginAccountOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $user_id = $params['officer_id'];
        $login_account = $params['login_account'];
        return userClass::editLoginAccount($user_id,$login_account);
    }


    public function editPhoneNumberOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return userClass::editPhoneNumber($params);
    }

    public function editEmailOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $user_id = $params['officer_id'];
        $email = $params['email'];
        return userClass::editEmail($user_id,$email);

    }


    public function getMemberPersonalFileCertResultOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $member_id = $params['member_id'];
        $member_profile = new member_profileClass();
        $file_data = $member_profile->getMemberPersonalCertAndInitData($member_id);
        return new result(true,'success',array(
            'page_data' => $file_data
        ));

    }


    public function submitMemberPersonalFileCertOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return member_profileClass::submitPersonalFileCert($params,certSourceTypeEnum::OPERATOR);

    }



}