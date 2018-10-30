<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/28
 * Time: 10:30
 */
class officer_v2Control extends officerControl
{

    public function editMemberBusinessAddressOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_researchClass::editMemberBusinessAddress($params);

    }

    public function editMemberIncomeSalaryAddressOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_researchClass::editMemberIncomeSalaryAddress($params);
    }

    public function submitMemberAssetCertNewOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }else{
            $token=$re->DATA;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $params['image_list'] = json_decode(urldecode($params['image_list']),true);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{
            $re = member_assetsClass::addAssetNew($params,certSourceTypeEnum::OPERATOR);

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

            $member_id = $params['member_id'];
            $officer_id = $params['officer_id'];

            $image_list = json_decode(urldecode($params['image_list']),true);

            $params['industry_research_json'] = urldecode($params['industry_research_json']);
            $params['research_text_src'] = urldecode(trim($params['research_text_src']));

            if( $params['research_id'] ){
                $rt = credit_researchClass::editIncomeBusinessInfo($params,operatorTypeEnum::CO,$officer_id,$image_list);

            }else{
                $rt = credit_researchClass::addMemberBusinessResearch($member_id, $params, operatorTypeEnum::CO, $officer_id, $image_list);
            }
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

    public function addExtendImageForMemberAssetOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $params['image_list'] = json_decode(urldecode($params['image_list']),true);
        return credit_officer_v2Class::addExtendImageForAsset($params);
    }

    public function submitMemberProfileCertV2Op()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }

        $params = array_merge(array(),$_GET,$_POST);
        $params['user_id'] = $params['officer_id'];
        $params['image_list'] = json_decode(urldecode($params['image_list']),true);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = member_profileClass::submitPersonalFileCertV2($params,certSourceTypeEnum::OPERATOR);

            if( !$re->STS ){
                $conn->rollback();
                return $re;
            }else{
                $conn->submitTransaction();
            }

            return $re;

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }

    public function submitMemberIncomeSalaryV2Op()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $officer_id = $params['officer_id'];
            $image_list = json_decode(urldecode($params['image_list']),true);
            $image_files = array();
            foreach( $image_list as $v ){
                $image_files[] = $v['image_url'];
            }

            $rt = credit_researchClass::addMemberSalaryIncomeResearch($params, $image_files, operatorTypeEnum::CO, $officer_id);
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

    public function editMemberIncomeSalaryV2Op()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $officer_id = $params['officer_id'];
            $uid = $params['uid'];

            $image_list = json_decode(urldecode($params['image_list']),true);
            $image_files = array();
            foreach( $image_list as $v ){
                $image_files[] = $v['image_url'];
            }

            $rt = credit_researchClass::editMemberSalaryIncomeResearch($uid, $params, $image_files, operatorTypeEnum::CO, $officer_id);
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

            $photos = array();
            $image_list = json_decode(urldecode($params['image_list']),true);
            foreach( $image_list as $v ){
                if( empty($v['image_url']) ){
                    continue;
                }
                $photos[] = $v['image_url'];
            }

            $member_id = $params['member_id'];
            $officer_id = $params['officer_id'];
            $rt = credit_researchClass::addMemberAttachmentResearch($member_id, $params, $photos, $officer_id);
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

            $photos = array();
            $image_list = json_decode(urldecode($params['image_list']),true);
            foreach( $image_list as $v ){
                if( empty($v['image_url']) ){
                    continue;
                }
                $photos[] = $v['image_url'];
            }

            $uid = $params['uid'];
            $officer_id = $params['officer_id'];
            $rt = credit_researchClass::editMemberAttachmentResearch($uid, $params, $photos, $officer_id);
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

            $asset_id = $params['asset_id'];
            $officer_id = $params['officer_id'];
            $images_files = array();
            $image_list = json_decode(urldecode($params['image_list']),true);
            foreach( $image_list as $v ){
                if( empty($v['image_url']) ){
                    continue;
                }
                $images_files[] = $v['image_url'];
            }

            $rt = credit_researchClass::addMemberAssetRentalResearch($asset_id, $params, $images_files, operatorTypeEnum::CO, $officer_id);
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

            $officer_id = $params['officer_id'];
            $research_id = $params['uid'];

            $images_files = array();
            $image_list = json_decode(urldecode($params['image_list']),true);
            foreach( $image_list as $v ){
                if( empty($v['image_url']) ){
                    continue;
                }
                $images_files[] = $v['image_url'];
            }

            $rt = credit_researchClass::editMemberAssetRentalResearch($research_id, $params, $images_files, operatorTypeEnum::CO, $officer_id);
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

    public function addCreditRequestRelativeOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{
            $rt = credit_officerClass::addCreditRequestRelative($params,false);
            if( !$rt->STS ){
                $conn->rollback();
            }else{
                $conn->submitTransaction();
            }
            return $rt;

        }catch (Exception $e){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }

    public function editCreditRequestRelativeOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        return credit_officerClass::editCreditRequestRelative($params,false);
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
        $filter = array(
            'type' => $params['type']
        );
        $list = $m->getMemberRequestListAndRelative($member_id,$filter);
        $is_can_add_new = $m->isCanAddNewRequest($member_id);

        return new result(true,'success',array(
            'list' => $list,
            'is_can_add_new_request' => $is_can_add_new
        ));
    }

    public function getMemberRelativeProfileCertTypeOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $relative_id = intval($params['relative_id']);
        $data = member_relativeClass::getProfileCertTypeAndResult($relative_id);
        $data = array_values($data);  // 重置为列表
        return new result(true,'success',array(
            'cert_type' => $data
        ));
    }


    public function submitMemberRelativeProfileCertOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $params['user_id'] = $params['officer_id'];
        $params['image_list'] = json_decode(urldecode($params['image_list']),true);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $re = member_relativeClass::profileCert($params,certSourceTypeEnum::OPERATOR);
            if( !$re->STS ){
                $conn->rollback();
                return $re;
            }else{
                $conn->submitTransaction();
            }

            return $re;

        }catch ( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }


    public function getMemberRelativeProfileCertResultOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $type = $params['type'];
        $relative_id = intval($params['relative_id']);
        return member_relativeClass::getProfileCertResultByType($type,$relative_id);

    }

    public function getMemberCreditRequestDetailOp()
    {
        $re = $this->checkOperator();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge(array(),$_GET,$_POST);
        $request_id = $params['request_id'];
        $detail = credit_officerClass::getMemberCreditRequestDetail($request_id,false);
        if( !$detail ){
            return new result(false,'No info.',null,errorCodesEnum::INVALID_PARAM);
        }
        return new result(true,'success',array(
            'detail_info' => $detail
        ));
    }

}