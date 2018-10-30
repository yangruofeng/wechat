<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/16
 * Time: 15:53
 */
class member_assetsClass
{
    public $asset_type = array(
        certificationTypeEnum::LAND,
        certificationTypeEnum::HOUSE,
        certificationTypeEnum::STORE,
        certificationTypeEnum::CAR,
        certificationTypeEnum::MOTORBIKE,
        certificationTypeEnum::DEGREE
    );
    public $cert_sample_image;

    public $type_icon;

    public function __construct()
    {
        Language::read('certification');
        $this->cert_sample_image = global_settingClass::getCertSampleImage();
        $this->type_icon = global_settingClass::getCertTypeIcon();
    }

    public function getAssetType()
    {
        return $this->asset_type;
    }

    /**
     * 获取可抵押类型(隶属于资产)
     */
    public static function getMortgageType(){
       return array(
            certificationTypeEnum::LAND,
            certificationTypeEnum::HOUSE,
            certificationTypeEnum::STORE
        );
    }
    /**
     * 获取可担保类型（隶属于资产）
     * @return array
     */
    public static function getCollateralType(){
        return array(
            certificationTypeEnum::MOTORBIKE,
            certificationTypeEnum::CAR,
            certificationTypeEnum::DEGREE
        );
    }

    public function _initAPPAssetCertPageByType($type)
    {
        switch ($type) {
            case certificationTypeEnum::HOUSE:
                return $this->getHousePageData();
                break;
            case certificationTypeEnum::CAR:
                return $this->getCarPageData();
                break;
            case certificationTypeEnum::LAND:
                return $this->getLandPageData();
                break;
            case certificationTypeEnum::MOTORBIKE:
                return $this->getMotorBikePageData();
                break;
            case certificationTypeEnum::STORE:
                return $this->getStorePageData();
                break;
            case certificationTypeEnum::DEGREE:
                return $this->getDegreePageData();
                break;
            default:
                throw new Exception('Not Support asset type.', errorCodesEnum::NOT_SUPPORTED);

        }
    }

    public function _initAPPCertPage()
    {
        return array(
            $this->getMotorBikePageData(),
            $this->getCarPageData(),
            $this->getHousePageData(),
            $this->getLandPageData(),
            $this->getStorePageData(),
            $this->getDegreePageData()
        );

    }


    /** 获得公共输入字段
     * @return array
     */
    protected function getCommonAssetFieldList()
    {
        return array(
            array(
                'field_name' => 'asset_name',
                'field_label' => L('cert_asset_name'),
                'field_type' => 'input',
                'value_type' => 'string',
                'select_list' => '',
                'is_required' => 1,
            ),
            array(
                'field_name' => 'asset_sn',
                'field_label' => L('cert_asset_sn'),
                'field_type' => 'input',
                'value_type' => 'string',
                'select_list' => '',
                'is_required' => 1,
            ),
            array(
                'field_name' => 'asset_cert_type',
                'field_label' => L('cert_asset_cert_type'),
                'field_type' => 'select',
                'select_list' => array(
                    array(
                        'value' => assetsCertTypeEnum::SOFT,
                        'name' => L('cert_asset_cert_type_soft'),
                    ),
                    array(
                        'value' => assetsCertTypeEnum::HARD,
                        'name' => L('cert_asset_cert_type_hard'),
                    ),
                ),
                'value_type' => 'string',
                'is_required' => 1,
            ),
            array(
                'field_name' => 'certificate_time',
                'field_label' => L('cert_asset_certificate_time'),
                'field_type' => 'input',
                'value_type' => 'date',
                'select_list' => '',
                'is_required' => 1,
            )
        );
    }

    /**
     * 因为degree不需要选soft/hard，需要选择的是学历
     */
    protected function getDegreeFieldList(){
        return array(
            array(
                'field_name' => 'asset_name',
                'field_label' => L('cert_asset_name'),
                'field_type' => 'input',
                'value_type' => 'string',
                'select_list' => '',
                'is_required' => 1,
            ),
            array(
                'field_name' => 'asset_sn',
                'field_label' => L('cert_asset_sn'),
                'field_type' => 'input',
                'value_type' => 'string',
                'select_list' => '',
                'is_required' => 1,
            ),
            array(
                'field_name' => 'asset_cert_type',
                'field_label' => L('cert_asset_cert_type'),
                'field_type' => 'select',
                'select_list' => array(
                    array(
                        'value' => degreeTypeEnum::HIGH_SCHOOL,
                        'name' => L('cert_degree_hight_school'),
                    ),
                    array(
                        'value' => degreeTypeEnum::TECHNICAL_TRAINING,
                        'name' => L('cert_degree_technical_training'),
                    ),
                    array(
                        'value' => degreeTypeEnum::BACHELOR,
                        'name' => L('cert_degree_bachelor'),
                    ),
                    array(
                        'value' => degreeTypeEnum::MASTER,
                        'name' => L('cert_degree_master'),
                    ),
                    array(
                        'value' => degreeTypeEnum::DOCTOR,
                        'name' => L('cert_degree_doctor'),
                    )
                ),
                'value_type' => 'string',
                'is_required' => 1,
            ),
            array(
                'field_name' => 'certificate_time',
                'field_label' => L('cert_asset_certificate_time'),
                'field_type' => 'input',
                'value_type' => 'date',
                'select_list' => '',
                'is_required' => 1,
            )
        );
    }


    /** 获得该类型下需要的图片列表
     * @param $type
     * @return array
     */
    protected function getImageListByType($type)
    {
        $image_list = array();
        $set_list = $this->cert_sample_image[$type];
        foreach ($set_list as $key => $value) {

            $image_list[] = array(
                'field_name' => $key,
                'filed_label' => $value['des'],
                'is_required' => $value['is_required'] ?: 0,
                'sample_image' => $value['image']
            );
        }
        return $image_list;
    }


    public function getAssetPageDataByType($asset_type)
    {
        switch ($asset_type) {
            case certificationTypeEnum::CAR:
                return $this->getCarPageData();
                break;
            case certificationTypeEnum::HOUSE:
                return $this->getHousePageData();
                break;
            case certificationTypeEnum::LAND :
                return $this->getLandPageData();
                break;
            case certificationTypeEnum::MOTORBIKE:
                return $this->getMotorBikePageData();
                break;
            case certificationTypeEnum::STORE:
                return $this->getStorePageData();
                break;
            case certificationTypeEnum::DEGREE:
                return $this->getDegreePageData();
                break;
            default:
                throw new Exception('Not support asset type.', errorCodesEnum::NOT_SUPPORTED);
        }
    }


    protected function getCarPageData()
    {
        $type = certificationTypeEnum::CAR;
        $name = L('certification_car_asset');
        $common_filed = $this->getCommonAssetFieldList();

        $image_list = $this->getImageListByType($type);


        return array(
            'asset_type' => $type,
            'type_name' => $name,
            'type_icon' => $this->type_icon[$type],
            'is_need_map' => 0,
            'is_no_need_value'=> 0,
            'input_field_list' => $common_filed,
            'upload_image_list' => $image_list
        );
    }


    protected function getHousePageData()
    {
        $type = certificationTypeEnum::HOUSE;
        $name = L('certification_house_asset');
        $common_filed = $this->getCommonAssetFieldList();

        $image_list = $this->getImageListByType($type);

        return array(
            'asset_type' => $type,
            'type_name' => $name,
            'type_icon' => $this->type_icon[$type],
            'is_need_map' => 1,
            'is_no_need_value'=> 0,
            'input_field_list' => $common_filed,
            'upload_image_list' => $image_list
        );
    }

    protected function getLandPageData()
    {
        $type = certificationTypeEnum::LAND;
        $name = L('certification_land_asset');
        $common_filed = $this->getCommonAssetFieldList();

        $image_list = $this->getImageListByType($type);

        return array(
            'asset_type' => $type,
            'type_name' => $name,
            'type_icon' => $this->type_icon[$type],
            'is_need_map' => 1,
            'is_no_need_value'=> 0,
            'input_field_list' => $common_filed,
            'upload_image_list' => $image_list
        );
    }

    protected function getMotorBikePageData()
    {
        $type = certificationTypeEnum::MOTORBIKE;
        $name = L('certification_motorbike');
        $common_filed = $this->getCommonAssetFieldList();

        $image_list = $this->getImageListByType($type);

        return array(
            'asset_type' => $type,
            'type_name' => $name,
            'type_icon' => $this->type_icon[$type],
            'is_need_map' => 0,
            'is_no_need_value'=> 0,
            'input_field_list' => $common_filed,
            'upload_image_list' => $image_list
        );
    }

    protected function getStorePageData()
    {
        $type = certificationTypeEnum::STORE;
        $name = L('certification_store');
        $common_filed = $this->getCommonAssetFieldList();

        $image_list = $this->getImageListByType($type);

        return array(
            'asset_type' => $type,
            'type_name' => $name,
            'type_icon' => $this->type_icon[$type],
            'is_need_map' => 1,
            'is_no_need_value'=> 0,
            'input_field_list' => $common_filed,
            'upload_image_list' => $image_list
        );
    }

    protected function getDegreePageData()
    {
        $type = certificationTypeEnum::DEGREE;
        $name = L('certification_degree');
        $common_filed = $this->getDegreeFieldList();

        $image_list = $this->getImageListByType($type);

        return array(
            'asset_type' => $type,
            'type_name' => $name,
            'type_icon' => $this->type_icon[certificationTypeEnum::RESIDENT_BOOK],
            'is_need_map' => 0,
            'is_no_need_value'=> 1,
            'input_field_list' => $common_filed,
            'upload_image_list' => $image_list
        );
    }

    public function getMemberAssetNumOfAllType($member_id)
    {
        $asset_type = $this->asset_type;
        $r = new ormReader();
        $sql = "select count(uid) cnt,asset_type from member_assets where member_id=" . qstr($member_id) .
            " and asset_state>=" . qstr(assetStateEnum::CERTIFIED) . " group by asset_type";
        $rows = $r->getRows($sql);
        $return = array();
        foreach ($rows as $v) {
            $return[$v['asset_type']] = $v['cnt'];
        }
        $format_array = array();
        foreach ($asset_type as $type) {
            $format_array[$type] = intval($return[$type]);
        }
        return $format_array;

    }


    /** 资产是否可编辑
     * @param $asset_info
     * @return bool
     */
    public static function assetIsCanEdit($asset_info)
    {
        // 抵押状态下，不可修改信息
        if ($asset_info['mortgage_state'] == 1) {
            return false;
        }

        // 已经授信了，不可修改信息
        if ($asset_info['asset_state'] >= assetStateEnum::GRANTED) {
            return false;
        }

        return true;
    }


    /**
     * @param $asset_id
     * @return result
     * @throws Exception
     */
    public static function assetIsCanDelete($asset_id)
    {
        $m_asset = new member_assetsModel();
        $asset_info = $m_asset->getRow(array(
            'uid' => $asset_id
        ));

        // 抵押状态下，不可删除
        if ($asset_info['mortgage_state'] == 1) {
            return new result(false, 'Have been mortgaged');
        }

        //检查是否在suggest和grant列表中
        $r = new ormReader();
        $suggest_state = array(
            memberCreditSuggestEnum::CREATE,
            memberCreditSuggestEnum::PENDING_APPROVE,
            memberCreditSuggestEnum::APPROVING,
            memberCreditSuggestEnum::PASS,
        );
        $sql = "SELECT * FROM member_credit_suggest mcs"
            . " INNER JOIN member_credit_suggest_detail mcsd ON mcsd.credit_suggest_id = mcs.uid"
            . " WHERE mcs.state IN (" . implode(',', $suggest_state) . ") AND mcsd.member_asset_id = " . intval($asset_id);
        $check_1 = $r->getRow($sql);
        if ($check_1) {
            return new result(false, 'It\'s already in the suggest list.');
        }

        return new result(true);
    }


    /** 删除单项资产
     * @param $asset_id
     * @return result
     */
    public static function assetDeleteById($asset_id)
    {
        $m_asset = new member_assetsModel();
        $asset = $m_asset->getRow(array(
            'uid' => $asset_id
        ));
        if (!$asset) {
            return new result(false, 'Invalid asset', null, errorCodesEnum::INVALID_PARAM);
        }

        $rt = self::assetIsCanDelete($asset_id);
        if (!$rt->STS) {
            return new result(false, 'Can not delete.', null, errorCodesEnum::UN_DELETABLE);
        }

        // 软删除
        $asset->asset_state = assetStateEnum::CANCEL;
        $asset->update_time = Now();
        $up = $asset->update();
        if (!$up->STS) {
            return new result(false, 'Delete fail', null, errorCodesEnum::DB_ERROR);
        }

        // 更新认证记录的状态
        $m_cert = new member_verify_certModel();
        $cert = $m_cert->getRow($asset->cert_id);
        if (!$cert) {
            return new result(false, 'Invalid asset', null, errorCodesEnum::INVALID_PARAM);
        }
        $cert->verify_state = certStateEnum::CANCEL;
        $cert->update_time = Now();
        $rt = $cert->update();
        if ($rt->STS) {
            return new result(true, 'success');
        } else {
            return new result(false, 'Delete fail', null, errorCodesEnum::DB_ERROR);
        }

    }

    /** 修改资产状态
     * @param $asset_id
     * @param $is_invalid
     * @return result
     */
    public static function updateAssetState($asset_id, $is_invalid)
    {
        $asset = (new member_assetsModel())->getRow($asset_id);
        if (!$asset) {
            return new result(false, 'No asset info:' . $asset_id, null, errorCodesEnum::INVALID_PARAM);
        }
        // todo 其他状态
        if ($is_invalid) {

            if ($asset['mortgage_state'] == 1) {
                return new result(false, 'Can not cancel.', null, errorCodesEnum::UN_MATCH_OPERATION);
            }

            if ($asset->asset_state == assetStateEnum::INVALID) {
                return new result(true, 'success');
            }

            // 作废
            $asset->asset_state = assetStateEnum::INVALID;
            $asset->update_time = Now();
            $up = $asset->update();
            if (!$up->STS) {
                return new result(false, 'Delete fail.', null, errorCodesEnum::DB_ERROR);
            }
            // 更新认证记录的状态
            $m_cert = new member_verify_certModel();
            $cert = $m_cert->getRow($asset->cert_id);
            if ($cert) {
                $cert->verify_state = certStateEnum::EXPIRED;
                $cert->update_time = Now();
                $up = $cert->update();
                if (!$up->STS) {
                    return new result(false, 'Delete fail', null, errorCodesEnum::DB_ERROR);
                }
            }

        } else {

            // 恢复

            if ($asset->asset_state >= assetStateEnum::CERTIFIED) {
                return new result(true, 'success');
            }
            $asset->asset_state = assetStateEnum::CERTIFIED;
            $asset->update_time = Now();
            $up = $asset->update();
            if (!$up->STS) {
                return new result(false, 'Handle fail.', null, errorCodesEnum::DB_ERROR);
            }
            // 更新认证记录的状态
            $m_cert = new member_verify_certModel();
            $cert = $m_cert->getRow($asset->cert_id);
            if ($cert) {
                $cert->verify_state = certStateEnum::PASS;
                $cert->update_time = Now();
                $up = $cert->update();
                if (!$up->STS) {
                    return new result(false, 'Handle fail', null, errorCodesEnum::DB_ERROR);
                }
            }
        }
        return new result(true);
    }


    /**
     * 资产详情
     * @param $asset_id
     * @return bool|mixed|null
     */
    public function getAssetDetailById($asset_id)
    {
        $m_member_assets = M('member_assets');
        $asset_info = $m_member_assets->find(array('uid' => $asset_id));
        if ($asset_info) {
            $cert_id = $asset_info['cert_id'];
            $m_member_verify_cert_image = M('member_verify_cert_image');
            $asset_images = $m_member_verify_cert_image->select(array('cert_id' => $cert_id));
            $asset_info['asset_images'] = $asset_images;
        }
        return $asset_info;
    }

    /**
     * 资产估值详情
     * @param $asset_id
     * @param $operator_id
     * @return bool|mixed|null
     */
    public function getAssetEvaluateByOperatorId($asset_id, $operator_id)
    {
        $userObj = new objectUserClass($operator_id);
        $is_bm = $userObj->position == userPositionEnum::BRANCH_MANAGER ? true : false;
        if ($is_bm) {
            $where = array(
                'branch_id' => $userObj->branch_id,
                'evaluator_type' => 1,
                'member_assets_id' => intval($asset_id),
            );
        } else {
            $where = array(
                'branch_id' => $userObj->branch_id,
                'operator_id' => $operator_id,
                'member_assets_id' => intval($asset_id),
            );
        }
        $m_member_assets_evaluate = M('member_assets_evaluate');
        $asset_evaluate = $m_member_assets_evaluate->orderBy('uid DESC')->find($where);
        if (!$asset_evaluate && $is_bm) {
            $asset_evaluate_list = $m_member_assets_evaluate->select(array('branch_id' => $userObj->branch_id, 'evaluator_type' => 0, 'member_assets_id' => intval($asset_id)));
            if (count($asset_evaluate_list)) {
                $evaluation_total = 0;
                $remark = '';
                foreach ($asset_evaluate_list as $val) {
                    $evaluation_total += $val['evaluation'];
                    $remark = $val['remark'];
                }
                $evaluation_avg = round($evaluation_total / count($asset_evaluate_list), 2);
                $asset_evaluate['evaluation'] = $evaluation_avg;
                $asset_evaluate['remark'] = $remark;
                $asset_evaluate['type'] = 'co_avg';
            }
        }
        return $asset_evaluate;
    }

    public static function editAssetCertType($asset_id, $cert_type)
    {
        $m = new member_assetsModel();
        $asset = $m->getRow(array(
            'uid' => $asset_id
        ));
        if (!$asset) {
            return new result(false, 'Not found asset info:' . $asset_id, null, errorCodesEnum::NO_DATA);
        }

        if (!$cert_type) {
            return new result(false, 'Error cert type:' . $cert_type, null, errorCodesEnum::INVALID_PARAM);
        }

        if (!self::assetIsCanEdit($asset)) {
            return new result(false, 'Asset info can not edit now!', null, errorCodesEnum::UN_EDITABLE);
        }

        if ($asset->asset_cert_type != $cert_type) {
            $asset->asset_cert_type = $cert_type;
            $asset->update_time = Now();
            $up = $asset->update();
            if (!$up->STS) {
                return new result(false, 'Update asset info fail.', null, errorCodesEnum::DB_ERROR);
            }
        }
        return new result(true, 'success');
    }

    public static function editAssetIssuedDate($asset_id, $issued_date)
    {
        if (!$asset_id || !$issued_date) {
            return new result(false, 'Invalid param.', null, errorCodesEnum::INVALID_PARAM);
        }
        $asset_info = (new member_assetsModel())->getRow($asset_id);
        if (!$asset_info) {
            return new result(false, 'No asset info:' . $asset_id, null, errorCodesEnum::NO_DATA);
        }
        $cert_id = $asset_info['cert_id'];
        $cert_row = (new member_verify_certModel())->getRow($cert_id);
        if (!$cert_row) {
            return new result(false, 'No asset cert info.', null, errorCodesEnum::NO_DATA);
        }
        $cert_row->cert_issue_time = date('Y-m-d', strtotime($issued_date));
        $cert_row->update_time = Now();
        $up = $cert_row->update();
        if (!$up->STS) {
            return new result(false, 'Modify fail.' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success');
    }


    /** 修改资产基本信息
     * @param $params
     * @return result
     */
    public static function editMemberAssetBaseInfo($params,$is_BM=false)
    {
        $asset_id = $params['asset_id'];
        $asset_name = trim($params['asset_name']);
        $asset_sn = trim($params['asset_sn']);
        $relative_id = $params['relative_id'];
        $cert_issue_time = date('Y-m-d', strtotime($params['cert_issue_time']));

        if( !$asset_id || !$asset_name || !$asset_sn ){
            return new result(false,'Empty params.',null,errorCodesEnum::INVALID_PARAM);
        }

        $m_asset = new member_assetsModel();
        $asset_row = $m_asset->getRow($asset_id);
        if( !$asset_row ){
            return new result(false,'Invalid asset id:'.$asset_id,null,errorCodesEnum::NO_DATA);
        }

        // todo BM不限制编辑权限的问题
        if( !$is_BM){
            if (!self::assetIsCanEdit($asset_row)) {
                return new result(false, 'Asset can not edit.', null, errorCodesEnum::UN_EDITABLE);
            }
        }

        $cert_id = $asset_row->cert_id;
        $m_cert = new member_verify_certModel();
        $cert_row = $m_cert->getRow($cert_id);
        if( !$cert_row ){
            return new result(false,'No cert info:'.$cert_id,null,errorCodesEnum::NO_DATA);
        }
        $cert_row->cert_issue_time = $cert_issue_time;
        $cert_row->update_time = Now();
        $up = $cert_row->update();
        if( !$up->STS ){
            return $up;
        }

        $asset_row->asset_name = $asset_name;
        $asset_row->asset_sn = $asset_sn;
        $asset_row->update_time = Now();
        $up = $asset_row->update();
        if( !$up->STS ){
            return $up;
        }

        $member = (new memberModel())->find(array('uid' => $asset_row['member_id']));
        if( !$member ){
            return new result(false,'No member info:'.$asset_row['member_id'],null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $rt = self::editMemberAssetOwnersByIds($member, $asset_row->uid, $relative_id);
        if( !$rt->STS ){
            return $rt;
        }

        return new result(true,'success');
    }



    /**
     * 编辑asset信息
     * @param $asset_id
     * @param $asset_info
     * @param $asset_images
     * @param $is_del_image
     * @return result
     */
    public function editAssetInfo($asset_id, $asset_info, $asset_images, $is_del_image = false)
    {

        $asset_id = intval($asset_id);
        $asset_name = trim($asset_info['asset_name']);
        $asset_sn = trim($asset_info['asset_sn']);
        $asset_type = $asset_info['asset_type'];
        $relative_id = $asset_info['relative_id'];
        $cert_issue_time = date('Y-m-d', strtotime($asset_info['cert_issue_time']));

        if( !$asset_name || !$asset_sn ){
            return new result(false,'Empty params.',null,errorCodesEnum::INVALID_PARAM);
        }


        $m_member_assets = M('member_assets');
        $asset_row = $m_member_assets->getRow(array('uid' => $asset_id));
        if (!$asset_row) {
            return new result(false, 'Invalid Id.');
        }

        if (!self::assetIsCanEdit($asset_row)) {
            return new result(false, 'Asset can not edit.', null, errorCodesEnum::UN_EDITABLE);
        }

        // 小方法不要用事务了，别的地方可能会调用
        try {
            $cert_id = $asset_row['cert_id'];
            $m_member_verify_cert = M('member_verify_cert');
            $cert_row = $m_member_verify_cert->getRow(array('uid' => $cert_id));
            if( !$cert_row ){
                return new result(false,'No cert info:'.$cert_id,null,errorCodesEnum::NO_DATA);
            }
            $cert_row->cert_issue_time = $cert_issue_time;
            $cert_row->cert_type = $asset_type;
            $cert_row->update_time = Now();
            $rt_1 = $cert_row->update();
            if (!$rt_1->STS) {
                return $rt_1;
            }

            $asset_row->asset_name = $asset_name;
            $asset_row->asset_type = $asset_type;
            $asset_row->asset_sn = $asset_sn;
            $asset_row->update_time = Now();
            $rt_2 = $asset_row->update();
            if (!$rt_2->STS) {
                return $rt_2;
            }

            // todo 没有判断必须传的图片

            // 删除原来的图片
            if ($is_del_image) {
                $sql = "delete from member_verify_cert_image where cert_id = " . $cert_id;
                $del = $m_member_assets->conn->execute($sql);
                if (!$del->STS) {
                    return new result(false, 'Delete old image fail.', null, errorCodesEnum::DB_ERROR);
                }
            }

            if (!empty($asset_images)) {
                $sql = "insert into member_verify_cert_image(cert_id,image_key,image_url,image_sha) values ";
                $sql_arr = array();
                foreach ($asset_images as $key => $path) {
                    $full_path = getImageUrl($path);
                    $image_sha = sha1_file($full_path);
                    $sql_arr[] = "('$cert_id','$key','$path','$image_sha')";
                }
                $sql .= implode(',', $sql_arr);
                $insert = $m_member_assets->conn->execute($sql);
                if (!$insert->STS) {
                    return new result(false, 'Add images fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
                }
            }

            $member = (new memberModel())->find(array('uid' => $asset_row['member_id']));
            if( !$member ){
                return new result(false,'No member info:'.$asset_row['member_id'],null,errorCodesEnum::NO_DATA);
            }

            $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset_row->uid, $relative_id);
            if (!$rt->STS) {
                return $rt;
            }

            return new result(true, 'success', $asset_info);
        } catch (Exception $ex) {
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 插入资产的保存流水
     */
    public static function insertStorageFlow($args)
    {
        if (!$args['member_asset_id']) {
            return new result(false, "Invalid Parameter:require asset-id to insert storage");
        }
        if (!$args['to_operator_id']) {
            //return new result(false,"Invalid Parameter:require opeartor-id to insert storage");
        }
        $m = M("member_assets_storage");
        $row = $m->newRow();
        $row->member_asset_id = $args['member_asset_id'];
        $row->mortgage_id = intval($args['mortgage_id']);
        $row->contract_no = $args['contract_no'];
        $row->from_branch_id = intval($args['from_branch_id']);
        $row->from_branch_name = $args['from_branch_name'];
        $row->from_operator_id = intval($args['from_operator_id']);
        $row->from_operator_name = $args['from_operator_name'];
        $row->to_branch_id = intval($args['to_branch_id']);
        $row->to_branch_name = $args['to_branch_name'];
        $row->to_operator_id = intval($args['to_operator_id']);
        $row->to_operator_name = $args['to_operator_name'];
        $row->flow_type = intval($args['flow_type']);
        $row->remark = $args['remark'];
        $row->create_time = Now();
        $row->creator_id = intval($args['creator_id']);
        $row->creator_name = $args['creator_name'];
        $row->is_history = 0;
        $row->is_pending = intval($args['is_pending']) ?: 0;
        $row->safe_id = intval($args['safe_id']);
        $row->safe_branch_id = intval($args['safe_branch_id']);
        $ret = $row->insert();
        if ($ret->STS) {
            if (!$row->is_pending) {
                $sql = "update member_assets_storage set is_history=1 where member_asset_id='" . $args['member_asset_id'] . "' and uid!='" . $row->uid . "'";
                $ret_update = $m->conn->execute($sql);
            }
        }
        return $ret;
    }

    /**
     * 获取一个资产的保存记录
     * @param $asset_id
     * @return mixed
     */
    public static function getAssetStorageFlow($asset_id)
    {
        $m = M("member_assets_storage");
        $rows = $m->select(array("member_asset_id" => $asset_id));
        return $rows;
    }

    public static function getAssetRelativeContract($asset_id)
    {
        //获取asset对应的grant-id
        $m_mortgage = new member_asset_mortgageModel();
        $mortgage_list = $m_mortgage->select(array("member_asset_id" => $asset_id));
        if (count($mortgage_list)) {
            $rows = resetArrayKey($mortgage_list, "grant_id");
            $grant_list = array_keys($rows);
            $str_grant = implode("','", $grant_list);
            $m_loan = new loan_contractModel();
            $sql = "SELECT a.uid,a.contract_sn,a.sub_product_name,a.start_date,a.receivable_principal principal_out,a.state,mcc.alias "
                . ",SUM(b.receivable_principal) principal_in,a.receivable_principal-IFNULL(SUM(b.receivable_principal),0) principal_outstanding"
                . " FROM loan_contract a"
                . " LEFT JOIN loan_installment_scheme b ON a.uid=b.`contract_id`"
                . " LEFT JOIN member_credit_category mcc ON a.member_credit_category_id=mcc.`uid`"
                . " WHERE b.state=" . qstr(schemaStateTypeEnum::COMPLETE) . " and a.credit_grant_id in ('" . $str_grant . "')"
                . " GROUP BY b.contract_id";
            $loan_list = $m_loan->reader->getRows($sql);
            $total_remains = 0;
            if (count($loan_list)) {
                foreach ($loan_list as $loan_item) {
                    $total_remains += $loan_item['principal_outstanding'];
                }
            }
            return array(
                "contract_list" => $loan_list,
                "principal_outstanding" => $total_remains
            );
        }
        return array();
    }

    public static function getAssetWithdrawRequestHistory($asset_id)
    {
        $m = M("member_asset_request_withdraw");
        $rows = $m->select(array("member_asset_id" => $asset_id));
        return $rows;
    }

    public static function saveRequestWithdraw($p)
    {
        $uid = $p['uid'];
        $m = M("member_asset_request_withdraw");
        if (!$uid) {
            //插入
            $remark = trim($p['remark']);
            $member_asset_id = intval($p['member_asset_id']);
            if (!$remark || !$member_asset_id) {
                return new result(false, "Invalid Parameter:Require to input remark");
            }
            $row = $m->newRow();
            $row->member_asset_id = $member_asset_id;
            $row->remark = $remark;
            $row->create_time = Now();
            $row->creator_id = $p['operator_id'];
            $row->creator_name = $p['operator_name'];
            $row->state = assetRequestWithdrawStateEnum::PENDING_APPROVE;
            return $row->insert();
        }
        $row = $m->getRow($uid);
        if (!$row) {
            return new result(false, "Invalid Object:No Row Found By " . $uid);
        }
        if (isset($p['remark']) && trim($p['remark'])) {
            $row->remark = trim($p['remark']);
        }
        if (isset($p['state'])) $row->state = intval($p['state']);
        if (isset($p['operator_id'])) $row->update_operator_id = intval($p['operator_id']);
        if (isset($p['operator_name'])) $row->update_operator_name = $p['operator_name'];
        if (isset($p['auditor_id'])) {
            $row->auditor_id = $p['auditor_id'];
            $row->update_operator_id = $p['auditor_id'];
            $row->audit_time = Now();
        }
        if (isset($p['auditor_name'])) {
            $row->auditor_name = $p['auditor_name'];
            $row->update_operator_name = $p['auditor_name'];
        }

        $row->update_time = Now();
        return $row->update();
    }

    public static function insertStorageFlowAsTransfer($asset_id, $receiver_id, $sender_id, $safe_id = null, $safe_branch_id = null)
    {
        if (!$asset_id) return new result(false, "Invalid Parameter:No Asset ID");
        $m = new member_assets_storageModel();
        $item = $m->find(array(
            "from_operator_id" => $sender_id,
            "member_asset_id" => $asset_id,
            "is_pending" => 1
        ));
        if ($item) {
            return new result(false, "There has one record to pending receive of this asset");
        }
        $receiver = new objectUserClass($receiver_id);
        $sender = new objectUserClass($sender_id);
        $ret = self::insertStorageFlow(array(
            "member_asset_id" => $asset_id,
            "from_operator_id" => $sender->user_id,
            "from_operator_name" => $sender->user_name,
            "from_branch_id" => $sender->branch_id,
            "from_branch_name" => $sender->branch_name,
            "to_operator_id" => $receiver->user_id,
            "to_operator_name" => $receiver->user_name,
            "to_branch_id" => $receiver->branch_id,
            "to_branch_name" => $receiver->branch_name,
            "flow_type" => assetStorageFlowType::TRANSFER,
            "is_pending" => 1,
            "creator_id" => $sender->user_id,
            "creator_name" => $sender->user_name,
            "safe_id" => $safe_id,
            "safe_branch_id" => $safe_branch_id
        ));
        return $ret;
    }

    public static function deleteStorageFlow($request_id)
    {
        $m = new member_assets_storageModel();
        $row = $m->getRow($request_id);
        if ($row) {
            $asset_id = $row->member_asset_id;
            if ($row->is_pending) {
                //找到上一条，还原is_history
                $ret = $row->delete();
            } else {
                $ret = new result(false, "not allowed to delete this record:already received by receiver");
            }
            return $ret;
        } else {
            return new result(false, "Invalid Object:" . $request_id);
        }
    }


    /** 一般的资产转移，不需要选择保险柜
     * @param $request_id
     * @return ormResult|result
     */
    public static function receiveAssetFromTransfer($request_id)
    {
        $m = new member_assets_storageModel();
        $row = $m->getRow($request_id);
        if ($row) {
            $asset_id = $row->member_asset_id;
            if ($row->is_pending) {
                //找到上一条，还原is_history
                $row->is_pending = 0;
                $ret = $row->update();
                if ($ret->STS) {
                    $sql = "update member_assets_storage set is_history=1 where member_asset_id='" . $row->member_asset_id . "' and uid!='" . $row->uid . "'";
                    $ret = $m->conn->execute($sql);
                } else {
                    return new result(false, 'Update storage info fail:' . $ret->MSG);
                }

            } else {
                $ret = new result(false, "already received by receiver");
            }
            return $ret;
        } else {
            return new result(false, "Invalid Object:" . $request_id);
        }
    }

    /** CT接收资产，一定要选择保险柜
     * @param $request_id
     * @param $safe_id
     * @param null $remark
     * @return ormResult|result
     */
    public static function receiveAssetFromTransferByCT($request_id, $safe_id, $remark = null)
    {
        $m = new member_assets_storageModel();
        $m_safe = new site_branch_safeModel();
        $safe_info = $m_safe->getSafeInfoById($safe_id);
        if (!$safe_info) {
            return new result(false, 'Not found safe info:' . $safe_id);
        }
        $safe_branch_id = $safe_info['branch_id'];
        $row = $m->getRow($request_id);
        if ($row) {
            $asset_id = $row->member_asset_id;
            if ($row->is_pending) {
                //找到上一条，还原is_history
                $row->is_pending = 0;
                $row->safe_branch_id = $safe_branch_id;
                $row->safe_id = $safe_id;
                $row->remark = $remark;
                $ret = $row->update();
                if ($ret->STS) {
                    $sql = "update member_assets_storage set is_history=1 where member_asset_id='" . $row->member_asset_id . "' and uid!='" . $row->uid . "'";
                    $ret = $m->conn->execute($sql);
                } else {
                    return new result(false, 'Update storage info fail:' . $ret->MSG);
                }

            } else {
                $ret = new result(false, "already received by receiver");
            }
            return $ret;
        } else {
            return new result(false, "Invalid Object:" . $request_id);
        }
    }


    public static function editMemberAssetOwnersByIds($member_info, $asset_id, $owner_ids)
    {
        $owner_ids = trim($owner_ids, ',');
        $ids_array = explode(',', $owner_ids);
        if (empty($ids_array)) {
            return new result(true);
        }

        $r = new ormReader();
        $sql = "select * from member_credit_request_relative where uid in (" . implode(',', $ids_array) . ")";
        $rows = $r->getRows($sql);
        $owner_array = resetArrayKey($rows, 'uid');
        $format_array = array();
        foreach ($ids_array as $uid) {
            if ($uid == 0) {
                $format_array[] = array(
                    'relative_id' => 0,
                    'relative_name' => $member_info['display_name'] ?: $member_info['login_code'].'(own)'
                );
            } else {

                if (empty($owner_array[$uid])) {
                    return new result(false, 'Not found relative person:' . $uid, null, errorCodesEnum::NO_DATA);
                }
                $format_array[] = array(
                    'relative_id' => $uid,
                    'relative_name' => $owner_array[$uid]['name']
                );
            }
        }

        return self::editMemberAssetOwnersByFormatArray($asset_id, $format_array);

    }


    public static function editMemberAssetOwnersByFormatArray($asset_id, $owners_array)
    {
        if (empty($owners_array)) {
            return new result(false, 'Empty owners.', null, errorCodesEnum::INVALID_PARAM);
        }
        $conn = ormYo::Conn();
        // 删除原来的
        $sql = "delete from member_assets_owner where member_asset_id=" . qstr($asset_id);
        $del = $conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete old owners fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
        }

        $sql = "insert into member_assets_owner(member_asset_id,relative_id,relative_name) values  ";
        $sql_arr = array();
        foreach ($owners_array as $owners) {
            $temp = "('$asset_id','" . $owners['relative_id'] . "','" . $owners['relative_name'] . "')";
            $sql_arr[] = $temp;
        }
        $sql .= implode(',', $sql_arr);
        $insert = $conn->execute($sql);
        if (!$insert->STS) {
            return new result(false, 'Add fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        return new  result(true);

    }


    /** 获得客户资产抵押的资产类型
     * @param $member_id
     */
    public static function getMemberAssetMortgagedType($member_id)
    {
        $m = new member_assetsModel();
        $list = $m->select(array(
            'member_id' => $member_id,
            'asset_state' => array('>=', assetStateEnum::CERTIFIED),
            'mortgage_state' => 1
        ));
        if (count($list) < 1) {
            return null;
        }

        $is_hard = false;
        foreach ($list as $v) {
            if ($v['asset_cert_type'] == assetsCertTypeEnum::HARD) {
                $is_hard = true;
                break;
            }
        }
        return $is_hard ? assetsCertTypeEnum::HARD : assetsCertTypeEnum::SOFT;
    }


    public static function getAssetMortgageImages($asset_id)
    {
        $asset_id = intval($asset_id);
        $r = new ormReader();
        // 没有图片的就过滤了
        $sql = "select smm.*,sm.member_asset_id from member_asset_mortgage sm inner join member_asset_mortgage_image smm on smm.asset_mortgage_id=sm.uid
          where sm.member_asset_id='$asset_id' and mortgage_type='1' and smm.image_path is not null ";
        return $r->getRows($sql);
    }

    /**
     * 判断资产是否可以重新授信
     * @param $asset_id
     */
    public static function checkAssetIdValidOfGrantCredit($asset_id)
    {
        //已经抵押的不能重新授信
        $sql = "SELECT * FROM member_assets WHERE uid=" . qstr($asset_id);
        $r = new ormReader();
        $row = $r->getRow($sql);
        if (!$row) {
            return new result(false, "Invalid Asset-ID,No Found");
        }
        if ($row['mortgage_state']) {
            return new result(false, $row['asset_name'] . " has been mortgaged");
        }
        //已经授信等待签合同的不能重新授信
        $sql = "SELECT c.asset_name,b.`state` FROM member_credit_grant_assets a INNER JOIN member_credit_grant b ON a.grant_id=b.`uid` ";
        $sql .= " INNER JOIN member_assets c ON a.`member_asset_id`=c.`uid` INNER JOIN member_authorized_contract d ON a.`grant_id`=d.`grant_credit_id` ";
        $sql .= " WHERE a.member_asset_id=" . qstr($asset_id) . " and b.state='" . commonApproveStateEnum::PASS . "' and d.uid is null";
        $r = new ormReader();
        $row = $r->getRow($sql);
        if ($row) {
            return new result(false, $row['asset_name'] . " has been grant-credit,pending sign agreement");
        }
        return new result(true);
    }

    public static function getAllAssetListOfMember($member_id)
    {
        $sql = "select * from member_assets where member_id=" . qstr($member_id);
        $r = new ormReader();
        $list = $r->getRows($sql);
        $ret = array();
        $type_list = (new certificationTypeEnum())->Dictionary();
        foreach ($list as $item) {
            $item['asset_type'] = $type_list[$item['asset_type']];
            $ret[$item['uid']] = $item;
        }
        return $ret;
    }

    /**
     * 保存资产
     * @param $params
     * @param $source
     * @return result
     */
    public static function addAsset($params, $source)
    {
        $member_id = intval($params['member_id']);
        $asset_name = trim($params['asset_name']);
        $asset_sn = trim($params['asset_sn']);
        $asset_type = $params['asset_type'];
        $asset_cert_type = $params['asset_cert_type'];
        $files = $_FILES;
        if ($params['cert_issue_time']) {
            $cert_issue_time = date('Y-m-d', strtotime($params['cert_issue_time']));
        }
        $file_dir = fileDirsEnum::MEMBER_ASSETS;

        $m_member = new memberModel();
        $member = $m_member->getRow(array(
            'uid' => $member_id
        ));
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::INVALID_PARAM);
        }

        if (!$asset_name) {
            return new result(false, 'No asset name.', null, errorCodesEnum::INVALID_PARAM);
        }

        if (!$asset_sn) {
            return new result(false, 'No asset sn.', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_asset = new member_assetsModel();
        $chk_sn = $m_asset->find(array(
                'asset_sn' => $asset_sn,
                'asset_type' => $asset_type,
                'asset_state' => array('>=', assetStateEnum::CERTIFIED)
            )
        );
        if ($chk_sn) {
            return new result(false, 'Asset sn already existed.', null, errorCodesEnum::ASSET_SN_DUPLICATION);
        }

        $image_structure = global_settingClass::getCertImageStructure();
        $stt = $image_structure[$asset_type];
        // key 是上传表单名
        $photos = array_column($stt, 'file_key');

        // 保存目录
        $save_path = $file_dir;

        $image_arr = array();

        foreach ($photos as $photo) {

            if (!empty($files[$photo])) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($photo);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_path = $upload->img_url;
                $image_arr[$photo] = array(
                    'image_url' => $img_path,
                    'image_sha' => sha1_file($upload->full_path)
                );
                unset($upload);
            }
        }

        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();

        // 额外的图片
        foreach ($_FILES as $key => $u_file) {

            if (startWith($key, 'asset_image')) {

                if (!empty($u_file)) {
                    $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                    $upload->set('save_path', null);
                    $upload->set('default_dir', $save_path);
                    $re = $upload->server2upun($key);
                    if ($re == false) {
                        return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                    }

                    if ($params[$key . '_key']) {
                        $image_key = $params[$key . '_key'];
                    } else {
                        $image_key = mt_rand(10, 99) . time();
                    }

                    $image_arr[$image_key] = array(
                        'image_url' => $upload->img_url,
                        'image_sha' => sha1_file($upload->full_path)
                    );
                    unset($upload);
                }
            }
        }

        $now = Now();
        $new_row = $m_cert->newRow();
        $new_row->member_id = $member_id;
        $new_row->cert_type = $asset_type;
        $new_row->verify_state = certStateEnum::CREATE;
        $new_row->source_type = intval($source);
        if ($cert_issue_time) {
            $new_row->cert_issue_time = $cert_issue_time;
        }
        $new_row->create_time = $now;
        $insert = $new_row->insert();
        if (!$insert->STS) {
            return new result(false, 'Add cert fail', null, errorCodesEnum::DB_ERROR);
        }
        $cert_id = $new_row->uid;

        foreach ($image_arr as $key => $value) {
            $row = $m_image->newRow();
            $row->cert_id = $new_row->uid;
            $row->image_key = $key;
            $row->image_url = $value['image_url'];
            $row->image_sha = $value['image_sha'];
            $insert = $row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert image fail', null, errorCodesEnum::DB_ERROR);
            }
        }

        // 插入资产表
        $asset = $m_asset->newRow();
        $asset->cert_id = $cert_id;
        $asset->member_id = $member_id;
        $asset->asset_name = $asset_name;
        $asset->asset_type = $asset_type;
        $asset->asset_sn = $asset_sn;
        $asset->asset_cert_type = $asset_cert_type;
        $asset->create_time = $now;
        $insert = $asset->insert();
        if (!$insert->STS) {
            return new result(false, 'Add member asset fail', null, errorCodesEnum::DB_ERROR);
        }

        $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset->uid, $params['relative_id']);
        if (!$rt->STS) {
            return $rt;
        }

        return new result(true, 'success', array(
            'cert_result' => $new_row,
            'extend_info' => null
        ));
    }


    public static function addAssetNew($params, $source)
    {
        $cert_type = $params['type'];
        $member_id = $params['member_id'];
        $memberObj = new objectMemberClass($member_id);
        $member = $memberObj->object_info;

        $asset_name = trim($params['asset_name']);
        $asset_sn = trim($params['asset_sn']);
        $asset_cert_type = $params['asset_cert_type'];
        $cert_issue_time = $params['certificate_time'] ? date('Y-m-d', strtotime($params['certificate_time'])) : null;

        if ($params['officer_id']) {
            $user_info = (new um_userModel())->getUserInfoById($params['officer_id']);
            $user_id = $user_info['uid'];
            $user_name = $user_info['user_name'];
        } else {
            $user_id = 0;
            $user_name = 'System';
        }


        $coord_x = round($params['coord_x'], 6);
        $coord_y = round($params['coord_y'], 6);
        $address_detail = $params['address_detail'];

        $image_list = $params['image_list'];
        $image_key_url = array();
        $image_arr = array();
        foreach( $image_list as $k=>$image){
            if( empty($image['image_url']) ){
                continue;
            }
            $image_key_url[$image['image_key']] = $image['image_url'];
            $image_arr[] = array(
                'image_key' => $image['image_key'],
                'image_url' => $image['image_url'],
                'image_sha' => '',
                'image_source' => $image['image_source']
            );
        }

        $assetClass = new member_assetsClass();
        $init_page_data = $assetClass->getAssetPageDataByType($cert_type);

        // 检查必传字段
        $filed_arr = $init_page_data['input_field_list'];
        foreach ($filed_arr as $item) {
            if ($item['is_required']) {
                if (!$params[$item['field_name']]) {
                    return new result(false, 'Lack of param:' . $item['field_name'], null, errorCodesEnum::DATA_LACK);
                }
            }
        }

        $image_field_arr = $init_page_data['upload_image_list'];
        foreach ($image_field_arr as $v) {
            if ($v['is_required']) {
                if (empty($image_key_url[$v['field_name']])) {
                    return new result(false, 'Lack of photo:' . $v['field_name'], null, errorCodesEnum::INVALID_PARAM);
                }
            }
        }


        $m_asset = new member_assetsModel();
        $m_cert = new member_verify_certModel();
        $m_image = new member_verify_cert_imageModel();
        $chk_sn = $m_asset->find(array(
            'asset_sn' => $asset_sn,
            'asset_type' => $cert_type,
            'asset_state' => array('>=', assetStateEnum::CERTIFIED)
        ));
        if ($chk_sn) {
            return new result(false, 'Asset sn already existed.', null, errorCodesEnum::ASSET_SN_DUPLICATION);
        }


        $o_cert_row = null;
        if ($params['cert_id']) {
            $cert_id = intval($params['cert_id']);
            $o_cert_row = $m_cert->getRow($cert_id);
        }

        if ($o_cert_row) {


            // 是编辑
            if ($o_cert_row->cert_type != $cert_type || $o_cert_row['verify_state'] != certStateEnum::CREATE) {
                return new result(false, 'Un-match cert type.', null, errorCodesEnum::UN_MATCH_OPERATION);
            }

            // 更新主认证信息
            $o_cert_row->cert_issue_time = $cert_issue_time;
            $o_cert_row->creator_id = $user_id;
            $o_cert_row->creator_name = $user_name;
            $o_cert_row->update_time = Now();
            $o_cert_row->update();

            // 更新资产信息
            $asset_info = $m_asset->getRow(array(
                'cert_id' => $o_cert_row->uid
            ));
            if (!$asset_info) {
                return new result(false, 'No asset info:cert id ' . $o_cert_row->uid, null, errorCodesEnum::INVALID_PARAM);
            }

            // 资产是否可编辑
            if (!member_assetsClass::assetIsCanEdit($asset_info)) {
                return new result(false, 'Un editable.', null, errorCodesEnum::UN_EDITABLE);
            }


            // 重新插入图片
            $sql = "delete from member_verify_cert_image where cert_id='" . $o_cert_row->uid . "' ";
            $del = $m_cert->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Delete old image fail.', null, errorCodesEnum::DB_ERROR);
            }

            if( !empty($image_arr) ){
                // 用语句插入
                $insert_sql = "insert into member_verify_cert_image(cert_id,image_key,image_url,image_sha,image_source,creator_id,creator_name)
                values ";
                $sql_arr = array();
                foreach ($image_arr as $key => $value) {
                    $temp = array(
                        qstr($o_cert_row->uid),
                        qstr($value['image_key']),
                        qstr($value['image_url']),
                        qstr($value['image_sha']),
                        qstr($value['image_source']),
                        qstr($user_id),
                        qstr($user_name)
                    );
                    $sql_arr[] = "(".implode(',',$temp).")";

                }
                $insert_sql .= implode(',',$sql_arr);
                $insert = $m_asset->conn->execute($insert_sql);
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail:'.$insert->MSG, null, errorCodesEnum::DB_ERROR);
                }
            }

            $asset_info->asset_name = $asset_name;
            $asset_info->asset_sn = $asset_sn;
            $asset_info->asset_cert_type = $asset_cert_type;
            $asset_info->coord_x = $coord_x;
            $asset_info->coord_y = $coord_y;
            $asset_info->address_detail = $address_detail;
            $asset_info->update_time = Now();
            $up = $asset_info->update();
            if (!$up->STS) {
                return new result(false, 'Edit asset fail.', null, errorCodesEnum::DB_ERROR);
            }

            if ($params['relative_id'] == null) {
                $params['relative_id'] = '0';
            }
            $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset_info->uid, $params['relative_id']);
            if (!$rt->STS) {
                return $rt;
            }


            return new result(true, 'success', array(
                'cert_result' => $o_cert_row,
                'extend_info' => null
            ));


        } else {


            $now = Now();
            $new_row = $m_cert->newRow();
            $new_row->member_id = $member_id;
            $new_row->cert_type = $cert_type;
            $new_row->verify_state = certStateEnum::CREATE;
            $new_row->source_type = intval($source);
            $new_row->create_time = $now;
            $new_row->cert_issue_time = $cert_issue_time;
            $new_row->creator_id = $user_id;
            $new_row->creator_name = $user_name;
            $insert = $new_row->insert();
            if (!$insert->STS) {
                return new result(false, 'Add cert fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
            }
            $cert_id = $new_row->uid;

            if( !empty($image_arr) ){
                // 用语句插入
                $insert_sql = "insert into member_verify_cert_image(cert_id,image_key,image_url,image_sha,image_source,creator_id,creator_name)
                values ";
                $sql_arr = array();
                foreach ($image_arr as $key => $value) {

                    $temp = array(
                        qstr($cert_id),
                        qstr($value['image_key']),
                        qstr($value['image_url']),
                        qstr($value['image_sha']),
                        qstr($value['image_source']),
                        qstr($user_id),
                        qstr($user_name)
                    );
                    $sql_arr[] = "(".implode(',',$temp).")";
                }
                $insert_sql .= implode(',',$sql_arr);
                $insert = $m_asset->conn->execute($insert_sql);
                if (!$insert->STS) {
                    return new result(false, 'Add cert image fail:'.$insert->MSG, null, errorCodesEnum::DB_ERROR);
                }
            }

            // 插入资产表
            $asset = $m_asset->newRow();
            $asset->cert_id = $cert_id;
            $asset->member_id = $member_id;
            $asset->asset_name = $asset_name;
            $asset->asset_type = $cert_type;
            $asset->asset_sn = $asset_sn;
            $asset->asset_cert_type = $asset_cert_type;
            $asset->coord_x = $coord_x;
            $asset->coord_y = $coord_y;
            $asset->address_detail = $address_detail;
            $asset->create_time = $now;
            $insert = $asset->insert();
            if (!$insert->STS) {
                return new result(false, 'Add member asset fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
            }

            if ($params['relative_id'] == null) {
                $params['relative_id'] = '0';
            }
            $rt = member_assetsClass::editMemberAssetOwnersByIds($member, $asset->uid, $params['relative_id']);
            if (!$rt->STS) {
                return $rt;
            }


            return new result(true, 'success', array(
                'cert_result' => $new_row,
                'extend_info' => null
            ));
        }

    }
}