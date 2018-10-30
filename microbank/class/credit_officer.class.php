<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/2
 * Time: 14:55
 */
class credit_officerClass
{


    public static function loginByloginCode($user_code, $password, $client_type)
    {
        if (!$user_code || !$password) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_user = new um_userModel();
        $user = $m_user->getRow(array(
            'user_code' => $user_code
        ));
        if (!$user) {
            return new result(false, 'No user', null, errorCodesEnum::USER_NOT_EXISTS);
        }
        if (md5($password) != $user->password) {
            return new result(false, 'Password error', null, errorCodesEnum::PASSWORD_ERROR);
        }

        // 检查user的合法性
        $userObj = new objectUserClass($user['uid']);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }


        // 检查职位
        $user_position = $user->user_position;
        $allowed_position=array(userPositionEnum::RISK_CONTROLLER,userPositionEnum::CREDIT_CONTROLLER,userPositionEnum::CHIEF_CREDIT_OFFICER,userPositionEnum::CREDIT_OFFICER);
        if (!in_array($user_position,$allowed_position)) {
            return new result(false, 'No Permission To Access:Invalid Role', null, errorCodesEnum::NO_LOGIN_ACCESS);
        }

        $rt = userClass::userLoginSuccess($user->uid, $client_type);
        return $rt;

    }

    public static function loginByGesture($officer_id, $gesture_password, $client_type)
    {
        if (!$officer_id || !$gesture_password) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_user = new um_userModel();
        $user = $m_user->getRow($officer_id);
        if (!$user) {
            return new result(false, 'No user', null, errorCodesEnum::USER_NOT_EXISTS);
        }

        if ($gesture_password != $user->gesture_password) {
            return new result(false, 'Password error', null, errorCodesEnum::PASSWORD_ERROR);
        }


        // 检查user的合法性
        $userObj = new objectUserClass($user['uid']);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        // 检查职位
        $user_position = $user->user_position;
        if ($user_position != userPositionEnum::CREDIT_OFFICER) {
            return new result(false, 'No login access', null, errorCodesEnum::NO_LOGIN_ACCESS);
        }

        $rt = userClass::userLoginSuccess($user->uid, $client_type);
        return $rt;

    }


    public static function loginByFingerprint($officer_id, $fingerprint_password, $client_type)
    {
        if (!$officer_id || !$fingerprint_password) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_user = new um_userModel();
        $user = $m_user->getRow($officer_id);
        if (!$user) {
            return new result(false, 'No user', null, errorCodesEnum::USER_NOT_EXISTS);
        }

        if ($fingerprint_password != $user->fingerprint_password) {
            return new result(false, 'Password error', null, errorCodesEnum::PASSWORD_ERROR);
        }


        // 检查user的合法性
        $userObj = new objectUserClass($user['uid']);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        // 检查职位
        $user_position = $user->user_position;
        if ($user_position != userPositionEnum::CREDIT_OFFICER) {
            return new result(false, 'No login access', null, errorCodesEnum::NO_LOGIN_ACCESS);
        }

        $rt = userClass::userLoginSuccess($user->uid, $client_type);
        return $rt;

    }

    public static function logout($officer_id, $client_type)
    {
        // 记录日志
        $m_log = new um_user_logModel();
        $log = $m_log->orderBy('uid desc ')->getRow(array(
            'user_id' => $officer_id,
            'client_type' => $client_type
        ));
        if ($log) {
            $log->logout_time = Now();
            $log->update_time = Now();
            $log->update();
        }

        //销毁token（所有，单设备支持）
        $sql = "delete from um_user_token where user_id='$officer_id' ";
        $del = $m_log->conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Logout fail', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true);
    }


    public static function addMemberIncomeSalary($params, $files)
    {

        $officer_id = $params['officer_id'];

        $images_files = array();
        if (!empty($files)) {
            $default_dir = fileDirsEnum::MEMBER_SALARY;
            foreach ($files as $key => $value) {
                $upload = new UploadFile();
                $upload->set('save_path', null);
                $upload->set('default_dir', $default_dir);
                $re = $upload->server2upun($key);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_url = $upload->img_url;
                $images_files[] = $img_url;
            }
        }

        $rt = credit_researchClass::addMemberSalaryIncomeResearch($params, $images_files, operatorTypeEnum::CO, $officer_id);
        return $rt;


    }

    public static function editMemberIncomeSalary($params, $files)
    {

        $officer_id = $params['officer_id'];
        $uid = $params['uid'];

        $images_files = array();
        if (!empty($files)) {
            $default_dir = fileDirsEnum::MEMBER_SALARY;
            foreach ($files as $key => $value) {
                $upload = new UploadFile();
                $upload->set('save_path', null);
                $upload->set('default_dir', $default_dir);
                $re = $upload->server2upun($key);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_url = $upload->img_url;
                $images_files[] = $img_url;
            }
        }

        $rt = credit_researchClass::editMemberSalaryIncomeResearch($uid, $params, $images_files, operatorTypeEnum::CO, $officer_id);
        return $rt;
    }


    public static function getMemberIncomeSalaryListAndSummary($member_id,$include_all=true)
    {
        $list = credit_researchClass::getOfficerLastSubmitMemberSalaryResearch($member_id,$include_all);
        $total_income = member_statisticsClass::getMemberTotalIncomeSalary($member_id,$include_all);
        return new result(true, 'success', array(
            'total_income' => $total_income,
            'list' => $list
        ));
    }


    public static function addMemberAssetRentalResearch($params, $files)
    {
        $asset_id = $params['asset_id'];
        $officer_id = $params['officer_id'];
        $images_files = array();
        if (!empty($files)) {
            $default_dir = fileDirsEnum::MEMBER_ASSETS_RENTAL;
            foreach ($files as $key => $value) {
                $upload = new UploadFile();
                $upload->set('save_path', null);
                $upload->set('default_dir', $default_dir);
                $re = $upload->server2upun($key);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_url = $upload->img_url;
                $images_files[] = $img_url;
            }
        }

        $rt = credit_researchClass::addMemberAssetRentalResearch($asset_id, $params, $images_files, operatorTypeEnum::CO, $officer_id);
        return $rt;
    }

    public static function editMemberAssetRentalResearch($params, $files)
    {

        $officer_id = $params['officer_id'];
        $research_id = $params['uid'];

        $images_files = array();
        if (!empty($files)) {
            $default_dir = fileDirsEnum::MEMBER_ASSETS_RENTAL;
            foreach ($files as $key => $value) {
                $upload = new UploadFile();
                $upload->set('save_path', null);
                $upload->set('default_dir', $default_dir);
                $re = $upload->server2upun($key);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_url = $upload->img_url;
                $images_files[] = $img_url;
            }
        }

        $rt = credit_researchClass::editMemberAssetRentalResearch($research_id, $params, $images_files, operatorTypeEnum::CO, $officer_id);
        return $rt;
    }


    public static function addMemberIncomeBusinessResearch($params, $files)
    {
        $member_id = $params['member_id'];
        $officer_id = $params['officer_id'];
        $asset_image_source = json_decode(urldecode($params['image_source']), true);

        $images_files = array();
        if (!empty($files)) {
            $default_dir = fileDirsEnum::MEMBER_BUSINESS;
            foreach ($files as $key => $value) {
                $upload = new UploadFile();
                $upload->set('save_path', null);
                $upload->set('default_dir', $default_dir);
                $re = $upload->server2upun($key);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_url = $upload->img_url;
                $images_files[] = array(
                    'image_url' => $img_url,
                    'image_source' => $asset_image_source[$key] ? imageSourceEnum::ALBUM : imageSourceEnum::CAMERA,
                );
            }
        }

        $params['industry_research_json'] = urldecode($params['industry_research_json']);
        $params['research_text_src'] = urldecode(trim($params['research_text_src']));

        if( $params['research_id'] ){
            $rt = credit_researchClass::editIncomeBusinessInfo($params,operatorTypeEnum::CO,$officer_id,$images_files);
            return $rt;

        }else{
            $rt = credit_researchClass::addMemberBusinessResearch($member_id, $params, operatorTypeEnum::CO, $officer_id, $images_files);
            return $rt;
        }


    }


    /** 获取officer最近一次提交的客户收入调查结果
     * @param $officer_id
     * @param $member_id
     * @return null
     */
    public static function getLastSubmitMemberIncomeResearch($officer_id, $member_id)
    {
        $m = new member_income_researchModel();
        if ($officer_id) {
            $row = $m->orderBy('uid desc')->find(array(
                'member_id' => $member_id,
                'operator_id' => $officer_id
            ));
        } else {
            $row = $m->orderBy('uid desc')->find(array(
                'member_id' => $member_id,
                'researcher_type' => 1
            ));
        }

        if ($row) {
            // 合同
            $contracts = (new member_lease_contractModel())->getRows(array(
                'income_research_id' => $row['uid']
            ));
            $contract_list = array();
            if (count($contracts) > 0) {
                foreach ($contracts as $v) {
                    $v['file_path'] = UPYUN_URL . '/' . $v['file_path'];
                    $contract_list[$v['type']][] = $v;
                }
            }
            $row['member_lease_contract'] = $contract_list;

            //Business
            $industry_research = (new common_industry_researchModel())->select(array(
                'research_id' => $row['uid']
            ));
            $row['member_industry_research'] = $industry_research;

            return $row;

        } else {
            return null;
        }

    }

    /**
     * 设置会员co
     * @param $param
     * @return result
     */
    public static function setOverdueContractCo($param)
    {
        $scheme_id = intval($param['scheme_id']);
        $member_id = intval($param['member_id']);
        $co_id = $param['co_id'];
        if (!$scheme_id) {
            return new result(false, 'Param Error!');
        }
        if (!$member_id) {
            return new result(false, 'Param Error!');
        }
        $m_co_overdue_contract_task = M('co_overdue_contract_task');
        $m_um_user = M('um_user');
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            //$sql = "update co_overdue_contract_task set state = 0,update_time = '" . Now() . "' WHERE contract_id = '$contract_id'";
            $sql = "update co_overdue_contract_task set state = 0 WHERE scheme_id = '$scheme_id'";
            $rt_1 = $m_co_overdue_contract_task->conn->execute($sql);
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Edit Failure!');
            }

            $user_info = $m_um_user->find(array('uid' => $co_id));
            $row = $m_co_overdue_contract_task->newRow();
            $row->scheme_id = $scheme_id;
            $row->member_id = $member_id;
            $row->co_id = $co_id;
            $row->co_name = $user_info['user_name'];
            $row->operator_id = $param['operator_id'];
            $row->operator_name = $param['operator_name'];
            $row->state = 1;
            $row->create_time = Now();
            $rt_2 = $row->insert();
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Edit Failure!');
            }

            $conn->submitTransaction();
            return new result(true, 'Edit Successful!');

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }


    public static function getLatestMemberAssetEvaluation($officer_id, $member_id)
    {

        $list = userClass::getMemberAllAssetsEvaluationOfUser($member_id, $officer_id);
        $total_asset = 0;
        $asset_name_lang = enum_langClass::getAssetsType();
        foreach ($list as $key=>$v) {
            $list[$key]['asset_type_name'] = $asset_name_lang[$v['asset_type']];
            $total_asset += $v['valuation'];
        }

        return array(
            'total_amount' => $total_asset,
            'list' => $list
        );
    }


    public static function getMemberAssetsListAndEvaluateOfOfficerGroupByType($member_id, $officer_id, $officer_type = userPositionEnum::CREDIT_OFFICER, $filter = array())
    {
        $m = new member_assetsModel();
        if ($filter['is_include_invalid']) {
            $assets = $m->orderBy('asset_type asc')->select(array(
                'member_id' => $member_id,
                'asset_state' => array('>=', assetStateEnum::INVALID)
            ));
        } else {
            $assets = $m->orderBy('asset_type asc')->select(array(
                'member_id' => $member_id,
                'asset_state' => array('>=', assetStateEnum::CERTIFIED)
            ));
        }

        // 展示最后一次评估的结果
        $asset_list = array();
        $m_image = new member_verify_cert_imageModel();
        foreach ($assets as $v) {

            // 图片列表
            $images = $m_image->select(array(
                'cert_id' => $v['cert_id']
            ));
            $image_list = array();
            foreach ($images as $image) {
                $image['image_url'] = getImageUrl($image['image_url']);
                $image_list[] = $image;
            }
            //获取本资产的拥有者列表
            $sql="select relative_id from member_assets_owner where member_asset_id=".qstr($v['uid']);
            $owner_list=$m->reader->getRows($sql);
            foreach($owner_list as $owner_id){
                $v['owner_id_list'][]=$owner_id['relative_id']?:0;
            }


            // 最后一次估价
            if ($officer_id) {
                $sql = "select evaluation from member_assets_evaluate where operator_id='$officer_id' and
            member_assets_id='" . $v['uid'] . "' order by uid desc  ";
            } else {
                $sql = "select evaluation from member_assets_evaluate where  member_assets_id='" . $v['uid'] . "' order by uid desc  ";

            }


            $evaluation = ($m->reader->getOne($sql)) ?: null;
            if (!$evaluation) {
                if (!in_array($officer_type, array(userPositionEnum::CREDIT_OFFICER, userPositionEnum::BRANCH_MANAGER, userPositionEnum::ROOT))) {
                    //非co bm和root，就优先取bm的估价
                    $evaluation = self::getMemberAssetValuationOfBM($v['uid']);
                    if (!$evaluation) {
                        //次之取co平均值
                        $evaluation = self::getMemberAssetValuationAverageOfCO($v['uid']);
                    }
                }
            }

            // 最后提交的租金信息
            $sql = "select monthly_rent from member_assets_rental where asset_id='" . $v['uid'] . "' order by uid desc ";
            $asset_rent = ($m->reader->getOne($sql)) ?: null;
            $v['officer_evaluation'] = $evaluation;
            $v['officer_rent'] = $asset_rent;
            $asset_list[$v['asset_type']][] = $v;
        }

        return $asset_list;

    }

    /**
     * 获取一个资产的co估价平均值
     * @param $asset_id
     * @return int
     */
    public static function getMemberAssetValuationAverageOfCO($asset_id)
    {
        $r = new ormReader();
        $where = " where member_assets_id='" . $asset_id . "' and evaluator_type='" . researchPositionTypeEnum::CREDIT_OFFICER . "'";
        $sql = "select avg(a.`evaluation`) val from  "
            . " (select b.* from (select * from member_assets_evaluate " . $where . ") b inner join "
            . " (select distinct operator_id,max(uid) mid from member_assets_evaluate " . $where . " group by operator_id) c on b.uid=c.mid) a";

        return round($r->getOne($sql), 2) ?: 0;
    }

    public static function getMemberAssetValuationOfCO($asset_id)
    {
        $r = new ormReader();
        $where = " where member_assets_id='" . $asset_id . "' and evaluator_type != " . qstr(researchPositionTypeEnum::BRANCH_MANAGER);
        $sql = "select b.* from (select * from member_assets_evaluate " . $where . ") b inner join "
            . " (select distinct operator_id,max(uid) mid from member_assets_evaluate " . $where . " group by operator_id) c on b.uid = c.mid";
        $sql.=" inner join member_follow_officer mfo on c.operator_id=mfo.officer_id and b.member_id=mfo.member_id AND mfo.`is_active`=1";

        $rows = $r->getRows($sql);
        if ($rows) {
            $avg_val = avgArrayByKey($rows, "evaluation");
            $avg_item = array(
                "operator_name" => '--AVG--',
                'evaluate_time' => Now(),
                'evaluation' => $avg_val,
                'remark' => 'Average from CO'
            );
            $rows[] = $avg_item;
        }
        return $rows;
    }

    /**
     * 获取一个资产的bm估价值
     * @param $asset_id
     * @return int
     */
    public static function getMemberAssetValuationOfBM($asset_id)
    {
        $r = new ormReader();
        $sql = "select evaluation from member_assets_evaluate where member_assets_id='" . $asset_id . "' and evaluator_type='" . researchPositionTypeEnum::BRANCH_MANAGER . "' order by uid desc";
        return $r->getOne($sql) ?: 0;
    }

    /**
     * 获取一个行业的co调查平均值
     * @param $member_id
     * @param $industry_id
     * @return int
     */
    public static function getMemberBusinessResearchAverageOfCO($member_id, $industry_id,$branch_code)
    {
        $r = new ormReader();
        $where = " where member_id='" . $member_id . "' and industry_id='" . $industry_id . "' and branch_code='".$branch_code."' and operator_type='" . researchPositionTypeEnum::CREDIT_OFFICER . "' and state<100 ";
        $sql = "select avg(income) income,avg(expense) expense,avg(profit) profit,avg(employees) employees from  "
            . " (select b.* from (select * from member_income_business " . $where . ") b inner join "
            . " (select distinct operator_id,max(uid) mid from member_income_business " . $where . " group by operator_id) c on b.uid=c.mid) a";
        $avg_item = $r->getRow($sql);
        $sql = "select * from member_income_business " . $where . " order by uid desc";
        $last_row = $r->getRow($sql);
        if ($last_row) {
            $last_row['income'] = round($avg_item['income'], 2) ?: 0;
            $last_row['expense'] = round($avg_item['expense'], 2) ?: 0;
            $last_row['profit'] = round($avg_item['profit'], 2) ?: 0;
            $last_row['employees'] = intval($avg_item['employees'] ?: 0);
        }
        return $last_row;
    }

    /**
     * 获取一个行业的bm调查值
     * @param $member_id
     * @param $industry_id
     * @return string
     */
    public static function getMemberBusinessResearchOfBM($member_id, $industry_id,$branch_code)
    {
        $r = new ormReader();
        $where = " where member_id='" . $member_id . "' and industry_id='" . $industry_id . "' and branch_code='".$branch_code."' and operator_type='" . researchPositionTypeEnum::BRANCH_MANAGER . "' and state<100";
        $sql = "select * from member_income_business " . $where . " order by uid desc";
        return $r->getRow($sql);
    }


    /** 获取单项资产的详情和最后调查信息
     * @param $asset_id
     * @param $officer_id
     * @param $is_bm_research
     * @return result
     */
    public static function getMemberAssetDetailAndResearchInfo($asset_id, $officer_id = 0, $is_bm_research = false)
    {
        $m_asset = new member_assetsModel();
        $asset_info = $m_asset->find(array(
            'uid' => $asset_id
        ));
        if (!$asset_info) {
            return new result(false, 'No asset info:' . $asset_id, null, errorCodesEnum::INVALID_PARAM);
        }

        $member_id = $asset_info['member_id'];

        // cert info
        $cert_info = (new member_verify_certModel())->find(array(
            'uid' => $asset_info['cert_id']
        ));

        $asset_info['cert_issue_time'] = $cert_info['cert_issue_time'];

        $m_member_assets_owner = M('member_assets_owner');
        $asset_owner = $m_member_assets_owner->select(array('member_asset_id' => $asset_id));
        $asset_info['relative_list'] = $asset_owner;

        $asset_owner_ids = array();
        foreach( $asset_owner as $v ){
            $asset_owner_ids[] = $v['relative_id'];
        }

        //所有关系人列表
        $all_relative_list = array();
        $own_is_owner = in_array(0,$asset_owner_ids);
        $all_relative_list[] = array(
            'relative_id' => 0,
            'relative_name' => 'Own',
            'is_owner' => $own_is_owner?1:0
        );
        $list = member_relativeClass::getMemberRelativeList($member_id);
        foreach( $list as $v ){
            $temp = array(
                'relative_id' => $v['uid'],
                'relative_name' => $v['name'],
                'is_owner' => 0
            );
            if( in_array($v['uid'],$asset_owner_ids) ){
                $temp['is_owner'] = 1;
            }
            $all_relative_list[] = $temp;
        }

        $asset_info['all_relative_list'] = $all_relative_list;

        $m_image = new member_verify_cert_imageModel();
        // 资产图片
        $images = $m_image->select(array(
            'cert_id' => $asset_info['cert_id']
        ));

        $image_list = array();
        $image_list_group_creator = array();
        foreach ($images as $image) {
            $image['image_url'] = getImageUrl($image['image_url']);
            $image_list[] = $image;

            $image_list_group_creator[$image['creator_id']][] = $image;
        }
        $asset_info['image_list'] = $image_list;
        $asset_info['image_list_group_creator'] = $image_list_group_creator;

        if ($is_bm_research) {
            $sql = "select evaluation from member_assets_evaluate where evaluator_type=1 and
            member_assets_id='$asset_id' order by uid desc  ";
        } else {
            $sql = "select evaluation from member_assets_evaluate where operator_id='$officer_id' and
            member_assets_id='$asset_id' order by uid desc  ";
        }
        $evaluation = ($m_asset->reader->getOne($sql)) ?: null;

        // 最后提交的租金信息
        $sql = "select monthly_rent from member_assets_rental where asset_id='$asset_id' order by uid desc ";
        $asset_rent = ($m_asset->reader->getOne($sql)) ?: null;
        $asset_info['officer_evaluation'] = $evaluation;
        $asset_info['officer_rent'] = $asset_rent;

        // 资产需要调查项内容
        $sql = "select * from common_asset_survey where asset_type=" . qstr($asset_info['asset_type']);
        $survey_info = $m_asset->reader->getRow($sql);
        $asset_info['survey_info'] = $survey_info;

        // 资产拥有人列表
        $sql = "select * from member_assets_owner where member_asset_id=" . qstr($asset_id) . " order by relative_id asc ";
        $list = $m_asset->reader->getRows($sql);
        $asset_info['relative_list'] = $list;

        // 资产类型名字
        $enum_lang = enum_langClass::getCertificationTypeEnumLang();
        $asset_info['asset_type_name'] = $enum_lang[$asset_info['asset_type']];

        return new result(true, 'success', $asset_info);
    }


    public static function getFollowedMemberList($officer_id)
    {
        // 过滤无效参数
        if (!$officer_id) {
            return null;
        }
        $list = (new um_userModel())->getUserFollowedMemberList($officer_id);
        foreach ($list as $k => $v) {
            $v['member_image'] = getImageUrl($v['member_image']);
            $v['member_icon'] = getImageUrl($v['member_icon']);
            // 处理信用和放款状态
            $credit_state = '';
            if( $v['credit'] > 0 ){
                if( $v['credit_balance'] < $v['credit'] ){
                    $credit_state = 'disbursed'; // 已放款
                }
            }else{
                // 没有信用
                if( $v['co_to_bm_credit_state'] == commonApproveStateEnum::REJECT ){
                    $credit_state = 'bm_reject';
                }
            }
            $v['client_credit_handle_state'] = $credit_state;
            $list[$k] = $v;
        }
        return $list;
    }


    public static function submitLoanRequestForMember($params)
    {
        $officer_id = $params['officer_id'];
        $member_id = intval($params['member_id']);
        $amount = round($params['amount'], 2);
        $currency = $params['currency'];
        $loan_time = intval($params['loan_time']);
        $loan_time_unit = $params['loan_time_unit'];


        if ($amount <= 0 || !$member_id || !$currency || $loan_time <= 0 || !$loan_time_unit) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }


        $m_user = new um_userModel();
        $officer = $m_user->getRow($officer_id);
        if (!$officer) {
            return new result(false, 'Invalid operator', null, errorCodesEnum::USER_NOT_EXISTS);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'Member not exist', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $applicant_name = $member->display_name ?: ($member->login_code ?: 'Unknown');
        $applicant_address = null;  // member 地址
        $contact_phone = $member->phone_id;

        $m_apply = new loan_applyModel();

        $apply = $m_apply->newRow();
        $apply->member_id = $member_id;
        $apply->applicant_name = $applicant_name;
        $apply->applicant_address = $applicant_address;
        $apply->apply_amount = $amount;
        $apply->currency = $currency;
        $apply->loan_time = $loan_time;
        $apply->loan_time_unit = $loan_time_unit;
        $apply->contact_phone = $contact_phone;
        $apply->apply_time = Now();
        $apply->request_source = loanApplySourceEnum::OPERATOR_APP;
        $apply->credit_officer_id = $officer_id;
        $apply->credit_officer_name = $officer->user_name;
        $apply->creator_id = $officer_id;
        $apply->creator_name = $officer->user_name;
        $apply->state = loanApplyStateEnum::ALLOT_CO;  // 直接状态到分配CO
        $insert = $apply->insert();
        if (!$insert->STS) {
            return new result(false, 'Apply fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $apply);
    }


    public static function submitGuaranteeRequestForMember($params)
    {
        $member_id = $params['member_id'];
        if ($member_id <= 0) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $country_code = $params['country_code'];
        $phone = $params['phone'];
        $relation_type = $params['relation_type'];
        $guarantee_member_account = trim($params['guarantee_member_account']);

        $m_member = new memberModel();

        $o_member = $m_member->getRow($member_id);
        if (!$o_member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $relate_member = $m_member->getRow(array(
            'login_code' => $guarantee_member_account
        ));

        if (!$relate_member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $m_guarantee = new member_guaranteeModel();
        $new_row = $m_guarantee->newRow();
        $new_row->member_id = $member_id;
        $new_row->relation_member_id = $relate_member->uid;
        $new_row->relation_type = $relation_type;
        $new_row->create_time = Now();
        $new_row->relation_state = memberGuaranteeStateEnum::CREATE;
        $insert = $new_row->insert();
        if (!$insert->STS) {
            return new result(false, 'Add fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $new_row);
    }


    public static function getBoundLoanRequest($params)
    {
        $officer_id = $params['officer_id'];
        $state = $params['state'];
        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 10000;
        $r = new ormReader();

        $where_str = '';
        switch ($state) {
            case 1:
                // 待处理
                $where_str .= " and state in('" . loanApplyStateEnum::ALLOT_CO . "','" . loanApplyStateEnum::CO_HANDING . "') ";
                break;
            case 2:
                // 拒绝的
                $where_str .= " and state='" . loanApplyStateEnum::CO_CANCEL . "' ";
                break;
            case 3:
                // 通过的
                $where_str .= " and state='" . loanApplyStateEnum::CO_APPROVED . "' ";
                break;
            default:
                break;
        }

        //  将CO该处理的排在前面
        $sql = "select * from loan_apply where credit_officer_id='$officer_id' $where_str order by 
        state not in ('" . loanApplyStateEnum::ALLOT_CO . "','" . loanApplyStateEnum::CO_HANDING . "'), apply_time desc ";


        $list = $r->getPage($sql, $page_num, $page_size);

        return new result(true, 'success', array(
            'total_num' => $list->count,
            'total_pages' => $list->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $list->rows
        ));
    }

    public static function bindMemberForLoanRequest($request_id, $member_id)
    {
        $m_loan_apply = new loan_applyModel();

        $apply = $m_loan_apply->getRow($request_id);
        if (!$apply) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        // 绑定client
        $apply->member_id = $member_id;
        $apply->update_time = Now();
        $up = $apply->update();
        if (!$up->STS) {
            return new result(false, 'Bind fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', array(
            'request_detail' => $apply
        ));
    }


    public static function loanRequestCheck($params)
    {
        $request_id = $params['request_id'];
        $officer_id = $params['officer_id'];
        $check_result = intval($params['check_result']);
        $remark = $params['remark'];

        $m_user = new um_userModel();
        $m_loan_apply = new loan_applyModel();
        $apply = $m_loan_apply->getRow($request_id);
        if (!$apply) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $user = $m_user->getRow($officer_id);
        if (!$user) {
            return new result(false, 'No user', null, errorCodesEnum::USER_NOT_EXISTS);
        }

        if ($apply->state == loanApplyStateEnum::OPERATOR_REJECT
            || $apply->state == loanApplyStateEnum::CO_CANCEL
        ) {
            return new result(false, 'Have canceled', null, errorCodesEnum::HAVE_CANCELED);
        }

        // 处理过了
        if ($apply->state >= loanApplyStateEnum::CO_APPROVED) {
            return new result(false, 'Handle yet', null, errorCodesEnum::HAVE_HANDLED);
        }

        if ($check_result == 1) {
            $apply->state = loanApplyStateEnum::CO_HANDING;
        } else {
            $apply->state = loanApplyStateEnum::CO_CANCEL;
        }

        $apply->co_id = $user->uid;
        $apply->co_name = $user->user_name;
        $apply->co_remark = $remark;
        $apply->update_time = Now();
        $up = $apply->update();
        if (!$up->STS) {
            return new result(false, 'Handle fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', array(
            'request_detail' => $apply
        ));

    }


    public static function loanRequestBindProduct($params)
    {
        $request_id = $params['request_id'];
        $product_id = $params['product_id'];
        $repayment_type = $params['repayment_type'];
        $repayment_period = $params['repayment_period'];

        $m_loan_apply = new loan_applyModel();
        $apply = $m_loan_apply->getRow($request_id);
        if (!$apply) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        if ($apply->state != loanApplyStateEnum::CO_HANDING) {
            return new result(false, 'Un-match operation', null, errorCodesEnum::UN_MATCH_OPERATION);
        }

        $m_member = new memberModel();
        $member = $m_member->find(array(
            'uid' => intval($apply->member_id)
        ));
        if (!$member) {
            return new result(false, 'Not bind Member', null, errorCodesEnum::UN_MATCH_OPERATION);
        }

        $m_loan_product = new loan_productModel();
        $product = $m_loan_product->getRow($product_id);
        if (!$product) {
            return new result(false, 'No loan product', null, errorCodesEnum::NO_LOAN_PRODUCT);
        }


        // 检查是否支持的还款方式
        $repayment_type_arr = (new interestPaymentEnum())->toArray();
        if (!in_array($repayment_type, $repayment_type_arr)) {
            return new result(false, 'Un-supported type', null, errorCodesEnum::NOT_SUPPORTED);
        }

        if ($repayment_type != interestPaymentEnum::SINGLE_REPAYMENT &&
            $repayment_type != interestPaymentEnum::ADVANCE_SINGLE_REPAYMENT
        ) {
            $repayment_period_arr = (new interestRatePeriodEnum())->toArray();
            if (!in_array($repayment_period, $repayment_period_arr)) {
                return new result(false, 'Un-supported type', null, errorCodesEnum::NOT_SUPPORTED);
            }
        }

        // 先写入记录
        $apply->product_id = $product->uid;
        $apply->product_name = $product->product_name;
        $apply->repayment_type = $repayment_type;
        $apply->repayment_period = $repayment_period;
        $apply->state = loanApplyStateEnum::CO_HANDING;
        $apply->update_time = Now();
        $up = $apply->update();
        if (!$up->STS) {
            return new result(false, 'Handle fail', null, errorCodesEnum::DB_ERROR);
        }

        $return = array(
            'request_detail' => $apply,
            'interest_info' => null
        );


        // 计算贷款天数
        $loan_days_re = loan_baseClass::calLoanDays($apply->loan_time, $apply->loan_time_unit);
        if (!$loan_days_re->STS) {
            return new result(true, 'success', $return);
        }
        $loan_days = $loan_days_re->DATA;

        $extend_info = $member;
        // 查询利率信息
        $re = loan_baseClass::getLoanInterestDetail($member['uid'], $product_id, $apply->apply_amount, $apply->currency, $loan_days, $extend_info);
        if (!$re->STS) {
            return new result(true, 'success', $return);  // 成功返回没有查询到利率信息
        }

        $data = $re->DATA;
        $interest_info = $data['interest_info'];
        $return['interest_info'] = $interest_info;
        $return['size_rate'] = $data['size_rate'];
        $return['special_rate'] = $data['special_rate'];

        // todo 是否在这步就写入利率信息等
        $apply->interest_rate = $interest_info['interest_rate'];
        $apply->interest_rate_type = $interest_info['interest_rate_type'] ? 1 : 0;
        $apply->interest_rate_unit = $interest_info['interest_rate_unit'];
        $apply->interest_min_value = round($interest_info['interest_min_value'], 2);
        $apply->operation_fee = $interest_info['operation_fee'];
        $apply->operation_fee_type = $interest_info['operation_fee_type'] ? 1 : 0;
        $apply->operation_fee_unit = $interest_info['operation_fee_unit'];
        $apply->operation_min_value = round($interest_info['operation_min_value'], 2);
        $apply->admin_fee = $interest_info['admin_fee'] ?: 0;
        $apply->admin_fee_type = $interest_info['admin_fee_type'] ?: 0;
        $apply->loan_fee = $interest_info['loan_fee'] ?: 0;
        $apply->loan_fee_type = $interest_info['loan_fee_type'] ?: 0;
        $apply->is_full_interest = $interest_info['is_full_interest'] ?: 0;
        $apply->prepayment_interest = $interest_info['prepayment_interest'] ?: 0;
        $apply->prepayment_interest_type = $interest_info['prepayment_interest_type'] ?: 0;
        $apply->penalty_rate = $interest_info['penalty_rate'] ?: $product->penalty_rate;
        $apply->penalty_divisor_days = $interest_info['penalty_divisor_days'] ?: $product->penalty_divisor_days;
        $apply->grace_days = intval($interest_info['grace_days']);
        $apply->update_time = Now();
        $up = $apply->update();
        if (!$up->STS) {
            return new result(false, 'Handle fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $return);

    }


    public static function loanRequestLastApproved($request_id)
    {
        $m_loan_apply = new loan_applyModel();
        $apply = $m_loan_apply->getRow($request_id);
        if (!$apply) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        if ($apply->state != loanApplyStateEnum::CO_HANDING) {
            return new result(false, 'Un-match operation', null, errorCodesEnum::UN_MATCH_OPERATION);
        }

        // 未完成全部步骤
        if (!$apply->member_id || !$apply->product_id || !$apply->interest_rate) {
            return new result(false, 'Un-match operation', null, errorCodesEnum::UN_MATCH_OPERATION);
        }

        $interest_info = $apply->toArray();

        $preview = (new loan_baseClass())->loanPreviewBeforeCreateContract($apply->apply_amount, $apply->currency, $apply->loan_time, $apply->loan_time_unit, $apply->repayment_type, $apply->repayment_period, $interest_info);
        if (!$preview->STS) {
            return new result(false, 'Un-match operation', null, errorCodesEnum::UN_MATCH_OPERATION);
        }

        $data = $preview->DATA;

        $apply->state = loanApplyStateEnum::CO_APPROVED;
        $apply->update_time = Now();
        $up = $apply->update();
        if (!$up->STS) {
            return new result(false, 'Handle fail', null, errorCodesEnum::DB_ERROR);
        }

        $return = array(
            'request_detail' => $apply,
            'preview_info' => $data
        );

        return new result(true, 'success', $return);

    }


    public static function signIn($params)
    {
        $officer_id = $params['officer_id'];
        $coord_x = $params['coord_x'];
        $coord_y = $params['coord_y'];
        if (!$officer_id || !$coord_x || !$coord_y) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $m_user = new um_userModel();
        $user = $m_user->getRow($officer_id);
        if (!$user) {
            return new result(false, 'No user', null, errorCodesEnum::USER_NOT_EXISTS);
        }
        $location = $params['location'];
        $remark = $params['remark'];
        $m = new um_user_trackModel();
        $track = $m->newRow();
        $track->user_id = $user->uid;
        $track->user_name = $user->user_name;
        $track->coord_x = $coord_x;
        $track->coord_y = $coord_y;
        $track->location = $location;
        $track->remark = $remark;
        $track->sign_day = date('Y-m-d');
        $track->sign_time = Now();
        $in = $track->insert();
        if (!$in->STS) {
            return new result(false, 'Db error', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true);
    }


    public static function editMemberAssetAddress($params)
    {
        $asset_id = intval($params['asset_id']);
        $coord_x = round($params['coord_x'], 6);
        $coord_y = round($params['coord_y'], 6);
        $address_detail = $params['address_detail'];
        $m_asset = new member_assetsModel();
        $asset_info = $m_asset->getRow($asset_id);
        if (!$asset_info) {
            return new result(false, 'No asset info:' . $asset_id, null, errorCodesEnum::INVALID_PARAM);
        }

        $asset_info->coord_x = $coord_x;
        $asset_info->coord_y = $coord_y;
        $asset_info->address_detail = $address_detail;
        $asset_info->update_time = Now();
        $up = $asset_info->update();
        if (!$up->STS) {
            return new result(false, 'Update fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }
        return new result(true);
    }


    public static function editMemberAssetRelative($params)
    {
        $asset_id = $params['asset_id'];
        $relative_ids = $params['relative_id'];
        if( !$asset_id ){
            return new result(false,'Invalid asset id:'.$asset_id,null,errorCodesEnum::INVALID_PARAM);
        }
        if( !$relative_ids ){
            $relative_ids = '0';
        }
        $asset_info = (new member_assetsModel())->getRow($asset_id);
        if( !$asset_info ){
            return new result(false,'No asset info:'.$asset_id,null,errorCodesEnum::NO_DATA);
        }

        $member_id = $asset_info['member_id'];
        $member_info = (new memberModel())->getRow($member_id);
        if( !$member_info ){
            return new result(false,'No member info:'.$member_id,null,errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $rt = member_assetsClass::editMemberAssetOwnersByIds($member_info,$asset_id,$relative_ids);
        return $rt;

    }

    public static function submitMemberAssetsEvaluate($params)
    {
        $asset_id = $params['id'];
        $amount = $params['valuation'];
        $officer_id = $params['officer_id'];
        $remark = $params['remark'];
        $evaluator_type = intval($params['evaluator_type']);
        if ($amount <= 0) {
            return new result(false, 'Invalid amount', null, errorCodesEnum::INVALID_AMOUNT);
        }
        $m_member_assets = new member_assetsModel();
        $assets = $m_member_assets->getRow($asset_id);
        if (!$assets) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        $officerObj = new objectUserClass($officer_id);

        // 只是提交建议
        $m_evaluate = new member_assets_evaluateModel();
        $evaluation = $m_evaluate->newRow();
        $evaluation->branch_id = $officerObj->branch_id;
        $evaluation->member_id = $assets->member_id;
        $evaluation->member_assets_id = $assets->uid;
        $evaluation->evaluator_type = $evaluator_type;
        $evaluation->evaluate_time = Now();
        $evaluation->operator_id = $officer_id;
        $evaluation->operator_name = $officerObj->user_name;
        $evaluation->evaluation = $amount;
        $evaluation->remark = $remark;
        $insert = $evaluation->insert();
        if (!$insert->STS) {
            return new result(false, 'Add evaluate log fail.', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', array(
            'request_detail' => $assets
        ));
    }


    public static function submitMemberSuggestCredit($params)
    {
        $member_id = intval($params['member_id']);
        $officer_id = intval($params['officer_id']);
        $member = (new memberModel())->getRow($member_id);
        if (!$member) {
            return new result(false, 'Member not exists.', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $userObj = new objectUserClass($officer_id);

        $monthly_repayment_ability = round($params['monthly_repayment_ability'], 2);
        $credit_terms = intval($params['credit_terms']);
        $default_credit = intval($params['default_credit']);
        $max_credit = intval($params['max_credit']);
        $client_request_credit = intval($params['client_request_credit']);
        $remark = $params['remark'];
        $request_type = intval($params['request_type']);

        $m_dict = new core_dictionaryModel();
        $credit_grant_profile = $m_dict->getDictValue(dictionaryKeyEnum::CREDIT_GRANT_RATE);
        if ($credit_terms > $credit_grant_profile['default_max_terms']) {
            return new result(false, 'The max terms can\'t greater than ' . $credit_grant_profile['default_max_terms']);
        }

        $asset_credit = @my_json_decode(urldecode($params['asset_credit']));
        //double check asset_credit+default_credit=max_credit
        $total_asset_credit = 0;
        foreach ($asset_credit as $item) {
            $total_asset_credit += $item['credit'] ?: 0;
        }
        if (($total_asset_credit + $default_credit) != $max_credit) {
            return new result(false, "Require: MaxCredit = DefaultCredit + (Increase Credit By Mortgaged)");
        }

        //double check currency credit
        $credit_currency=$params['credit_currency'];
        $credit_ccy_total=0;
        foreach($credit_currency as $ccy_item){

            $credit_ccy_total+=$ccy_item['credit_usd']+$ccy_item['credit_khr']/4000;
        }

        if($credit_ccy_total!=$max_credit){
            return new result(false, "Require: Total Currency Credit = USD_Credit + KHR_Credit");
        }

        //匹配不到利息不允许提交
        $category_setting=loan_categoryClass::getMemberCreditCategoryList($member_id);
        if($userObj->position==userPositionEnum::BRANCH_MANAGER){
            foreach($credit_currency as $chk_category){
                $chk_cate=$category_setting[$chk_category['member_credit_category_id']];
                $chk_cate['credit_usd']=$chk_category['credit_usd'];
                $chk_cate['credit_khr']=$chk_category['credit_khr'];
                $chk_cate['credit_terms']=$credit_terms;
                $chk_ret=loan_categoryClass::matchInterestForCategory($chk_cate['interest_rate_list'],$chk_cate,false);
                foreach($chk_ret as $chk_ret_ccy=>$chk_ret_item){
                    if(!$chk_ret_item['is_matched']){
                        $msg=$chk_ret_item['msg'];
                        $str_msg=join($msg," , ");
                        $str_msg="<kbd>".$str_msg."</kbd>";
                        return new result(false,"No Matched Interest For ".$chk_cate['alias']." ,Currency:".strtoupper($chk_ret_ccy).",Amount:".$chk_category['credit_'.$chk_ret_ccy]." MSG:".$str_msg);
                    }
                }
            }
        }

        $m = new member_credit_suggestModel();
        $row = $m->newRow();
        $row->branch_id = $userObj->branch_id;
        $row->member_id = $member_id;
        $row->request_type = $request_type;
        $row->request_time = Now();
        $row->operator_id = $userObj->user_id;
        $row->operator_name = $userObj->user_name;
        $row->client_request_credit = $client_request_credit;
        $row->monthly_repayment_ability = $monthly_repayment_ability;
        $row->default_credit = $default_credit;
        $row->default_credit_category_id = intval($params['default_credit_category_id']);
        $row->max_credit = $max_credit;
        $row->credit_terms = $credit_terms;
        $row->is_append = $params['is_append'] ?: 0;
        $row->remark = $remark;
        $insert = $row->insert();
        if (!$insert->STS) {
            return new result(false, $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        if (count($asset_credit)) {
            // 插入资产详细
            $sql = "insert into member_credit_suggest_detail(credit_suggest_id,member_asset_id,credit,member_credit_category_id) values ";
            $arr = array();
            $suggest_id = $row->uid;
            foreach ($asset_credit as $asset) {
                $str = "('$suggest_id','" . $asset['asset_id'] . "','" . $asset['credit'] . "','" . $asset['member_credit_category_id'] . "')";
                $arr[] = $str;
            }
            $sql .= implode(',', $arr);
            $insert = $m->conn->execute($sql);
            if (!$insert->STS) {
                return new result(false, 'Submit fail.', null, errorCodesEnum::DB_ERROR);
            }
        }
        if(count($credit_currency)){
            $m_credit_product=new member_credit_suggest_productModel();
            foreach($credit_currency as $ccy_item){
                $ccy_row=$m_credit_product->newRow($ccy_item);
                $ccy_row->credit_suggest_id=$row->uid;
                $ccy_row->credit=$ccy_item['credit_usd']+$ccy_item['credit_khr']/4000;
                $ccy_row->exchange_rate=4000;
                $ccy_sts=$ccy_row->insert();
                if(!$ccy_sts){
                    return $ccy_sts;
                }
            }
        }

        return new result(true, 'Save Successful.');

    }


    public static function getTaskSummary($user_id)
    {
        $r = new ormReader();

        // 待处理贷款咨询
        $sql = "select count(*) from loan_consult where co_id='$user_id' and state 
        in('" . loanConsultStateEnum::ALLOT_CO . "','" . loanConsultStateEnum::CO_HANDING . "') ";
        $loan_consult_num = $r->getOne($sql);
        $loan_consult_num = $loan_consult_num ?: 0;

        // 待处理贷款申请
        $sql = " select count(*) from loan_apply where credit_officer_id='$user_id' and state 
        in('" . loanApplyStateEnum::ALLOT_CO . "','" . loanApplyStateEnum::CO_HANDING . "') ";
        $loan_apply_num = $r->getOne($sql);
        $loan_apply_num = $loan_apply_num ?: 0;

        // 待处理逾期合同
        $sql = "select count(*) from co_overdue_contract_task where co_id='$user_id' and state!=2 ";
        $overdue_contract_num = $r->getOne($sql);
        $overdue_contract_num = $overdue_contract_num ?: 0;

        return array(
            'task_loan_consult' => $loan_consult_num,
            'task_loan_apply' => $loan_apply_num,
            'task_overdue_contract' => $overdue_contract_num
        );

    }


    public static function getAllotLoanConsultPageListResult($params)
    {
        $officer_id = $params['officer_id'];
        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 100000;
        $type = intval($params['state']);
        $r = new ormReader();

        switch ($type) {
            case 1:
                // pending check
                $where = " and state in('" . loanConsultStateEnum::ALLOT_CO . "','" . loanConsultStateEnum::CO_HANDING . "') ";
                break;
            case 2:
                // rejected
                $where = " and state='" . loanConsultStateEnum::CO_CANCEL . "' ";
                break;
            case 3:
                // approved
                $where = " and state='" . loanConsultStateEnum::CO_APPROVED . "' ";
                break;
            default:
                $where = '';
        }
        $sql = "select * from loan_consult where co_id='$officer_id' $where  order by uid desc";
        $page = $r->getPage($sql, $page_num, $page_size);
        return new result(true, 'success', array(
            'total_num' => $page->count,
            'total_pages' => $page->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $page->rows
        ));
    }

    public static function submitLoanConsultHandle($params)
    {
        $officer_id = $params['officer_id'];
        $consult_id = $params['consult_id'];
        $result = intval($params['result']) ? 1 : 0;
        $remark = $params['remark'];

        $m_user = new um_userModel();
        $officer = $m_user->getRow($officer_id);
        if (!$officer) {
            return new result(false, 'Invalid operator', null, errorCodesEnum::USER_NOT_EXISTS);
        }

        $m = new loan_consultModel();
        $consult = $m->getRow($consult_id);
        if (!$consult) {
            return new result(false, 'Invalid param.', null, errorCodesEnum::INVALID_PARAM);
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            if ($consult->member_id) {
                $m_follow_officer = new member_follow_officerModel();
                $follow = $m_follow_officer->getRow(array('member_id' => $consult->member_id, 'officer_id' => $officer_id, 'is_active' => 1));
                if (!$follow) {
                    $row = $m_follow_officer->newRow();
                    $row->member_id = $consult->member_id;
                    $row->officer_id = $officer_id;
                    $row->officer_name = $officer->user_name;
                    $row->is_active = 1;
                    $row->officer_type = 0;
                    $row->update_time = Now();
                    $rt_2 = $row->insert();
                    if (!$rt_2->STS) {
                        $conn->rollback();
                        return new result(false, 'Edit Failure!');
                    }
                }
            }

            $userObj = new objectUserClass($officer_id);
            $consult->co_id = $userObj->user_id;
            $consult->co_name = $userObj->user_name;
            $consult->co_remark = $remark;
            if ($result == 1) {
                $consult->state = loanConsultStateEnum::CO_APPROVED;
            } else {
                $consult->state = loanConsultStateEnum::CO_CANCEL;
            }
            $up = $consult->update();
            if (!$up->STS) {
                $conn->rollback();
                return new result(false, 'Handle fail.', null, errorCodesEnum::DB_ERROR);
            }
            $conn->submitTransaction();
            return new result(true, 'success', $consult);
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    public static function getAllAllotOverdueContractListInfo($user_id)
    {

        $r = new ormReader();
        // 未完成的逾期合同
        $sql = "select c.* from co_overdue_contract_task t inner join loan_contract c on c.uid=t.contract_id 
        where t.co_id='$user_id' and t.state!='2' ";
        $list = $r->getRows($sql);
        if (count($list) < 1) {
            return null;
        }

        $return_list = array();
        foreach ($list as $contract) {
            $contract_id = $contract['uid'];
            $temp = array();

            $temp['contract_id'] = $contract_id;
            $temp['contract_sn'] = $contract['contract_sn'];

            $member_info = loan_contractClass::getLoanContractMemberInfo($contract['uid']);

            $member_info['member_image'] = getImageUrl($member_info['member_image']);
            $member_info['member_icon'] = getImageUrl($member_info['member_icon']);

            $temp['member_info'] = $member_info;

            // 合同逾期时间+金额（用逾期时间）
            $sql = "select min(receivable_date) penalty_start_date,sum(amount) total_amount  from loan_installment_scheme where contract_id='$contract_id'
            and state!='" . schemaStateTypeEnum::CANCEL . "' and state!='" . schemaStateTypeEnum::COMPLETE . "'
            and date_format(receivable_date,'%Y-%m-%d')<'" . date('Y-m-d') . "' ";
            $row = $r->getRow($sql);
            $temp['penalty_start_date'] = $row['penalty_start_date'] ? date('Y-m-d', strtotime($row['penalty_start_date'])) : null;
            $temp['overdue_total_amount'] = $row['total_amount'] ?: 0;

            // 过滤掉没有逾期金额的又没更新状态的
            if ($temp['overdue_total_amount'] > 0) {
                $return_list[] = $temp;
            }


        }

        return $return_list;

    }

    public static function getAllotOverdueContractListResult($params)
    {
        $officer_id = $params['officer_id'];
        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 100000;
        $type = intval($params['state']);
        $r = new ormReader();

        switch ($type) {
            case 1:
                // pending check
                $where = " AND t.state = '1' ";
                break;
            case 2:
                // Done
                $where = " AND t.state='2' ";
                break;
            default:
                $where = '';
        }
        $sql = "SELECT t.uid task_id,lis.*,t.scheme_id,t.state task_state,m.login_code,m.phone_id,lc.virtual_contract_sn contract_sn,lc.currency FROM co_overdue_contract_task t"
            . " INNER JOIN client_member m ON t.member_id = m.uid"
            . " INNER JOIN loan_installment_scheme lis ON lis.uid = t.scheme_id"
            . " INNER JOIN loan_contract lc ON lis.contract_id = lc.uid"
            . " WHERE t.co_id='$officer_id' $where AND lis.state != " . schemaStateTypeEnum::CANCEL . " AND lis.state!=" . schemaStateTypeEnum::COMPLETE
            . " ORDER BY t.uid DESC";

        $page = $r->getPage($sql, $page_num, $page_size);
        $list = $page->rows;

        foreach ($list as $k => $v) {
            $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['scheme_id']);
            $payable_amount = $v['amount'] - $v['actual_payment_amount'];
            $total = $payable_amount + $penalty;
            $v['total'] = $total;
            $v['receivable_date'] = dateFormat($v['receivable_date']);
            $list[$k] = $v;
        }

        return new result(true, 'success', array(
            'total_num' => $page->count,
            'total_pages' => $page->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $list
        ));
    }


    public static function getOverdueContractTaskDetail($task_id)
    {
        $r = new ormReader();
        $uid = $task_id;

        // 任务信息
        $sql = "SELECT t.uid task_id,t.co_id,lis.*,t.scheme_id,t.state task_state,m.login_code,m.phone_id,lc.virtual_contract_sn contract_sn,lc.currency FROM co_overdue_contract_task t"
            . " INNER JOIN client_member m ON t.member_id = m.uid"
            . " INNER JOIN loan_installment_scheme lis ON lis.uid = t.scheme_id"
            . " INNER JOIN loan_contract lc ON lis.contract_id = lc.uid"
            . " WHERE t.uid='$uid' AND lis.state != " . schemaStateTypeEnum::CANCEL . " AND lis.state!=" . schemaStateTypeEnum::COMPLETE
            . " ORDER BY t.uid DESC";
        $detail = $r->getRow($sql);
        if (!$detail) {
            return new result(false, 'No task info.', null, errorCodesEnum::NO_DATA);
        }

        $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($detail['scheme_id']);
        $payable_amount = $detail['amount'] - $detail['actual_payment_amount'];
        $detail['payable_amount'] = $payable_amount;
        $detail['penalty'] = $penalty;

        $scheme_id = $detail['scheme_id'];
        $officer_id = $detail['co_id'];

        // 追债信息是写入loan_contract_dun表的
        $sql = "SELECT * FROM loan_contract_dun WHERE scheme_id='$scheme_id' AND officer_id='$officer_id' ORDER BY create_time DESC";
        $list = $r->getRows($sql);

        return new result(true, 'success', array(
            'detail' => $detail,
            'list' => $list
        ));
    }


    public static function submitOverDueContractDunInfo($params)
    {
        $uid = intval($params['uid']);
        $type = intval($params['type']);
        $remark = $params['remark'];
        $officer_id = intval($params['officer_id']);

        $m_task = new co_overdue_contract_taskModel();
        $task = $m_task->getRow($uid);
        if (!$task) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }

        if ($type == 2) {
            // done
            $task->state = 2;
            $task->update_time = Now();
            $task->remark = $remark;
            $up = $task->update();
            if (!$up->STS) {
                return new result(false, 'Bind fail', null, errorCodesEnum::DB_ERROR);
            }
            return new result(true, 'success');

        } else {

            return self::submitOverdueContractDunLog($task->scheme_id, $officer_id, $remark);
        }

    }

    public static function submitOverdueContractDunLog($scheme_id, $user_id, $remark)
    {
        $userObj = new objectUserClass($user_id);

        $m_loan_installment_scheme = M('loan_installment_scheme');
        $scheme = $m_loan_installment_scheme->getRow($scheme_id);
        $contract_id = $scheme->contract_id;

        $m = new loan_contract_dunModel();
        $log = $m->newRow();
        $log->contract_id = $contract_id;
        $log->scheme_id = $scheme_id;
        $log->branch_id = $userObj->branch_id;
        $log->officer_id = $userObj->user_id;
        $log->officer_name = $userObj->user_name;
        $log->dun_type = 'on_site';
        $log->dun_time = Now();
        $log->dun_response = $remark;
        $log->create_time = Now();
        $insert = $log->insert();
        if (!$insert->STS) {
            return new result(false, 'Add dun log fail.', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success', $log);
    }

    public static function submitMemberBusinessImage($research_id,$member_id, $industry_id, $files, $officer_id)
    {
        if (empty($files)) {
            return new result(false, 'No upload photo.', null, errorCodesEnum::INVALID_PARAM);
        }

        $images_files = array();
        if (!empty($files)) {
            $default_dir = fileDirsEnum::MEMBER_BUSINESS;
            foreach ($files as $key => $value) {
                $upload = new UploadFile();
                $upload->set('save_path', null);
                $upload->set('default_dir', $default_dir);
                $re = $upload->server2upun($key);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::API_FAILED);
                }
                $img_url = $upload->img_url;
                $images_files[] = $img_url;
            }
        }
        $rt = credit_researchClass::addIndustryBusinessImage($research_id,$member_id, $industry_id, $officer_id, $images_files);
        return $rt;
    }


    /** 获取客户商业调查图片
     * @param $params
     * @return array
     */
    public static function getMemberBusinessPhotoPageList($params)
    {
        //$officer_id = intval($params['officer_id']);
        //$type = intval($params['type']) ?: businessPhotoTypeEnum::PLACE_SCENE;
        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 100000;
        $member_id = intval($params['member_id']);
        $industry_id = intval($params['industry_id']);
        $r = new ormReader();

        $sql = "select * from member_income_business_image where  member_id='$member_id' and industry_id='$industry_id'  order by uid desc  ";
        $page = $r->getPage($sql, $page_num, $page_size);
        $list = $page->rows;
        foreach ($list as $k => $v) {
            $v['image_url'] = getImageUrl($v['image_url']);
            $list[$k] = $v;
        }
        return array(
            'total_num' => $page->count,
            'total_pages' => $page->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $list
        );

    }

    public static function deleteMemberBusinessPhoto($image_id, $officer_id)
    {
        // todo 是否验证权限
        $m = new member_income_business_imageModel();
        $row = $m->getRow($image_id);
        if (!$row) {
            return new result(false, 'No scene data.', null, errorCodesEnum::NO_DATA);
        }

        $data = $row->toArray();

        // 删除记录
        $del = $row->delete();
        if (!$del->STS) {
            return new result(false, 'Delete fail.', null, errorCodesEnum::DB_ERROR);
        }

        // 删除图片
        $upload = new UploadFile();
        $delete = $upload->deleteFile('/' . $data['image_url']);
        /*if (!$delete) {
            return new result(false, 'Delete image fail.', null, errorCodesEnum::UPYUN_HANDLE_FAIL);
        }*/

        return new result(true, 'success');
    }


    public static function deleteMemberBusinessPhoto_old($params)
    {
        return new result(false, 'Function closed.', null, errorCodesEnum::FUNCTION_CLOSED);
        $uid = $params['uid'];
        $officer_id = $params['officer_id'];
        $m = new member_business_photoModel();
        $row = $m->getRow($uid);
        if (!$row) {
            return new result(false, 'No scene data.', null, errorCodesEnum::NO_DATA);
        }

        /* if ($officer_id != $row->operator_id) {
             return new result(false, 'No access.', null, errorCodesEnum::NOT_PERMITTED);
         }*/

        $data = $row->toArray();

        // 删除记录
        $del = $row->delete();
        if (!$del->STS) {
            return new result(false, 'Delete fail.', null, errorCodesEnum::DB_ERROR);
        }

        // 删除图片
        $upload = new UploadFile();
        $delete = $upload->deleteFile('/' . $data['image_path']);
        if (!$delete) {
            return new result(false, 'Delete image fail.', null, errorCodesEnum::UPYUN_HANDLE_FAIL);
        }

        return new result(true, 'success');


    }

    public static function submitMemberContractPhoto($user_id, $member_id, $files, $remark)
    {
        if (empty($files)) {
            return new result(false, 'No upload photo.', null, errorCodesEnum::INVALID_PARAM);
        }

        $member = (new memberModel())->getRow($member_id);
        if (!$member) {
            return new result(false, 'Member not exist.', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $userObj = new objectUserClass($user_id);
        $user_name = $userObj->user_name;

        $save_path = 'contract_photo' . '/' . $member_id;

        $photos = array();

        foreach ($files as $key => $v) {

            if (!empty($v)) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($key);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::UPLOAD_PIC_TO_UPYUN_FAIL);
                }
                $img_path = $upload->img_url;
                $photos[] = $img_path;
                unset($upload);
            }
        }

        if (!empty($photos)) {

            $sql_arr = array();
            $time = Now();
            $sql = "insert into member_business_scene(income_research_id,member_id,file_path,operator_id,operator_name,update_time) values  ";
            foreach ($photos as $image) {
                $temp = "(0,'$member_id','$image','$user_id','$user_name','$time')";
                $sql_arr[] = $temp;
            }
            $sql .= implode(',', $sql_arr);
            $m_business_scene = new member_lease_contractModel();
            $insert = $m_business_scene->conn->execute($sql);
            if (!$insert->STS) {
                return new result(false, 'Insert business scene fail.', null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true, 'success');
    }


    public static function getMemberContractPhotoPageList($params)
    {
        // 不过滤user
        $officer_id = intval($params['officer_id']);
        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 100000;
        $member_id = intval($params['member_id']);
        $r = new ormReader();

        $sql = "select * from member_lease_contract where member_id='$member_id'  order by uid desc  ";
        $page = $r->getPage($sql, $page_num, $page_size);
        $list = $page->rows;
        foreach ($list as $k => $v) {
            $v['image_url'] = UPYUN_URL . '/' . $v['file_path'];
            $list[$k] = $v;
        }
        return array(
            'total_num' => $page->count,
            'total_pages' => $page->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $list
        );
    }

    public static function deleteMemberContractPhoto($params)
    {
        $uid = $params['uid'];
        $officer_id = $params['officer_id'];

        $m = new member_lease_contractModel();
        $row = $m->getRow($uid);
        if (!$row) {
            return new result(false, 'No photo data.', null, errorCodesEnum::NO_DATA);
        }

        $image_path = $row->file_path;

        // 删除记录
        $del = $row->delete();
        if (!$del->STS) {
            return new result(false, 'Delete fail.', null, errorCodesEnum::DB_ERROR);
        }

        if ($image_path) {
            // 删除图片
            $upload = new UploadFile();
            $delete = $upload->deleteFile('/' . $image_path);
            if (!$delete) {
                return new result(false, 'Delete image fail.', null, errorCodesEnum::UPYUN_HANDLE_FAIL);
            }
        }

        return new result(true, 'success');
    }


    public static function receiveLoanFromMember($params)
    {
        $loan_contract_id = $params['loan_contract_id'];
        $officer_id = $params['officer_id'];
        $trading_password = $params['trading_password'];
        $amount = round($params['amount'], 2);
        $currency = $params['currency'];
        $rt = (new bizCoReceiveLoanFromClientClass())->execute($loan_contract_id, $officer_id, $trading_password, $amount, $currency);
        return $rt;
    }

    public static function getReceiveLoanListFromClient($params)
    {

        $officer_id = intval($params['officer_id']);
        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 100000;

        $r = new ormReader();

        $sql = "select b.*,c.contract_sn,m.login_code member_code,m.display_name member_name,m.member_icon,m.phone_id 
        from biz_co_receive_loan_from_member b left join loan_contract c on c.uid=b.loan_contract_id 
        left join client_member m on m.uid=b.member_id where b.operator_id='$officer_id' and b.state='" . bizStateEnum::DONE . "'
        order by b.uid desc ";

        $page = $r->getPage($sql, $page_num, $page_size);
        $list = $page->rows;

        return array(
            'total_num' => $page->count,
            'total_pages' => $page->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $list
        );
    }

    public static function transferToTellerSubmit($params)
    {
        $co_id = $params['officer_id'];
        $co_trading_password = $params['trading_password'];
        $teller_id = $params['teller_id'];
        $amount = $params['amount'];
        $currency = $params['currency'];
        $remark = $params['remark'];
        $rt = (new bizCoTransferToTellerClass())->execute($co_id, $co_trading_password, $teller_id,
            $amount, $currency, $remark);
        return $rt;
    }

    public static function getTransferToTellerList($params)
    {
        $officer_id = intval($params['officer_id']);
        $page_num = $params['page_num'] ?: 1;
        $page_size = $params['page_size'] ?: 100000;

        $officer_info = (new um_userModel())->find(array(
            'uid' => $officer_id
        ));

        $obj_id = $officer_info['obj_guid'] ?: 0;

        $r = new ormReader();

        $sql = " select * from biz_obj_transfer where sender_obj_type='" . objGuidTypeEnum::UM_USER . "'
         and sender_obj_guid='$obj_id' order by uid desc  ";

        $page = $r->getPage($sql, $page_num, $page_size);
        $list = $page->rows;

        foreach ($list as $k => $v) {
            $url = getUrl('co_app', 'tellerReceiveConfirm', array('uid' => $v['uid']), false, WAP_OPERATOR_SITE_URL);
            $v['qrcode_content'] = $url;
            $list[$k] = $v;
        }

        return array(
            'total_num' => $page->count,
            'total_pages' => $page->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $list
        );
    }


    public static function deleteMemberAsset($asset_id, $officer_id)
    {
        $userObj = new objectUserClass($officer_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }
        // todo 验证权限
        return member_assetsClass::assetDeleteById($asset_id);
    }

    public static function updateMemberAssetState($asset_id, $officer_id, $is_invalid)
    {
        $userObj = new objectUserClass($officer_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }
        // todo 验证权限
        return member_assetsClass::updateAssetState($asset_id, $is_invalid);

    }



    public static function addExtendImageForAsset($params)
    {
        $asset_id = intval($params['asset_id']);
        $asset_image_source = json_decode(urldecode($params['asset_image_type']), true);
        $m_asset = new member_assetsModel();
        $asset_info = $m_asset->getRow($asset_id);
        if (!$asset_info) {
            return new result(false, 'No asset info:' . $asset_id, null, errorCodesEnum::INVALID_PARAM);
        }

        if ($params['officer_id']) {
            $user_info = (new um_userModel())->getUserInfoById($params['officer_id']);
            $user_id = $user_info['uid'];
            $user_name = $user_info['user_name'];
        } else {
            $user_id = 0;
            $user_name = 'Member self';
        }

        $photos = array();
        $save_path = fileDirsEnum::MEMBER_ASSETS;
        foreach ($_FILES as $key => $v) {
            if (!empty($v)) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($key);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::UPLOAD_PIC_TO_UPYUN_FAIL);
                }
                $img_path = $upload->img_url;
                $image_key = rand(100, 999) . time() . rand(1000, 9999);
                $photos[$image_key] = array(
                    'image_path' => $img_path,
                    'image_url' => $upload->full_path,
                    'image_source' => $asset_image_source[$key] ? imageSourceEnum::ALBUM : imageSourceEnum::CAMERA
                );
                unset($upload);
            }
        }
        if (!empty($photos)) {
            $cert_id = $asset_info->cert_id;
            $sql = "insert into member_verify_cert_image(cert_id,image_key,image_url,image_sha,image_source,creator_id,creator_name) values ";
            $sql_arr = array();
            foreach ($photos as $key => $v) {
                $sql_arr[] = "('$cert_id','$key','" . $v['image_path'] . "','" . sha1_file($v['image_url']) . "','" . $v['image_source'] . "',
                '$user_id','$user_name')";
            }
            $sql .= implode(',', $sql_arr);
            $insert = $m_asset->conn->execute($sql);
            if (!$insert->STS) {
                return new result(false, 'Add image fail.', null, errorCodesEnum::DB_ERROR);
            }
        }
        return new result(true);
    }


    public static function addMemberAttachment($params, $files)
    {
        $photos = array();
        $save_path = 'member_attachment';
        foreach ($files as $key => $v) {
            if (!empty($v)) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($key);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::UPLOAD_PIC_TO_UPYUN_FAIL);
                }
                $img_path = $upload->img_url;
                $photos[] = $img_path;
                unset($upload);
            }
        }

        $member_id = $params['member_id'];
        $officer_id = $params['officer_id'];
        return credit_researchClass::addMemberAttachmentResearch($member_id, $params, $photos, $officer_id);
    }


    public static function editMemberAttachment($params, $files)
    {
        $photos = array();
        $save_path = fileDirsEnum::MEMBER_ATTACHMENT;
        foreach ($files as $key => $v) {
            if (!empty($v)) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun($key);
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::UPLOAD_PIC_TO_UPYUN_FAIL);
                }
                $img_path = $upload->img_url;
                $photos[] = $img_path;
                unset($upload);
            }
        }

        $uid = $params['uid'];
        $officer_id = $params['officer_id'];
        return credit_researchClass::editMemberAttachmentResearch($uid, $params, $photos, $officer_id);
    }

    /**
     * 修改branch
     * @param $uid
     * @param $branch_id
     * @return ormResult|result
     */
    public function resetConsultApplicantBranch($uid, $branch_id, $officer_id, $officer_name)
    {
        $m_loan_consult = M('loan_consult');
        $row = $m_loan_consult->getRow($uid);
        $old_branch_id = $row->branch_id;
        if (!$row) {
            return new result(false, 'Invalid Id');
        }
        //$officerObj = new objectUserClass($officer_id);
        $row->branch_id = $branch_id;
        $row->state = loanConsultStateEnum::ALLOT_BRANCH;
        //$row->operator_id = $branch_id;
        //$row->operator_name = $officerObj->user_name;
        $row->update_time = Now();
        $rt_1 = $row->update();
        if ($rt_1->STS) {
            if ($old_branch_id > 0) {
                $ret1 = taskControllerClass::cancelTaskById($uid, userTaskTypeEnum::BM_NEW_CONSULT, $old_branch_id, objGuidTypeEnum::SITE_BRANCH);
            }
            $msg = "Get New Consultation  From " . strtoupper($officer_name) . " At " . Now();
            $ret2 = taskControllerClass::handleNewTask($uid, userTaskTypeEnum::BM_NEW_CONSULT, $branch_id, objGuidTypeEnum::SITE_BRANCH, $officer_id, objGuidTypeEnum::UM_USER, $msg);
        }


        return $rt_1;
    }

    /**
     * 修改 state
     * @param $uid
     * @param $state
     * @return ormResult|result
     */
    public function resetConsultApplicantState($uid, $state)
    {
        $m_loan_consult = M('loan_consult');
        $row = $m_loan_consult->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $row->state = $state;
        $row->update_time = Now();
        $rt_1 = $row->update();
        if (!$rt_1->STS) {
            $conn->rollback();
            return $rt_1;
        }

        $conn->submitTransaction();
        return new result(true, 'Setting successful');
    }

    public static function deleteMemberCBC($uid, $officer_id)
    {
        $userObj = new objectUserClass($officer_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $m = new client_cbcModel();
        $row = $m->getRow($uid);
        if (!$row) {
            return new result(false, 'No data.', null, errorCodesEnum::NO_DATA);
        }
        $data = $row->toArray();
        // 删除记录
        $del = $row->delete();
        if (!$del->STS) {
            return new result(false, 'Delete fail.', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success');

    }


    /** 添加信用申请
     * @param $params
     * @return result
     */
    public static function addMemberCreditRequest($params)
    {
        $credit = intval($params['credit']);
        $credit_terms = intval($params['credit_terms']);
        $purpose = trim($params['purpose']);
        $interest_rate = floatval($params['interest_rate']);
        $member_id = intval($params['member_id']);
        if ($member_id <= 0 || $credit < 0 || $credit_terms < 0) {
            return new result(false, 'Invalid param.', null, errorCodesEnum::INVALID_PARAM);
        }

        $m = new member_credit_requestModel();

        // 检查是否有未处理的申请
        $sql = "select count(*) from member_credit_request where member_id=" . qstr($member_id) . " and state!='" . creditRequestStateEnum::CANCEL . "'
        and state!='" . creditRequestStateEnum::DONE . "' ";
        $num = $m->reader->getOne($sql);
        if ($num > 0) {
            return new result(false, 'Have un-complete request.', null, errorCodesEnum::HAVE_UN_COMPLETE_REQUEST);
        }

        $officer_id = intval($params['officer_id']);
        $officer = new objectUserClass($officer_id);

        $request = $m->newRow();
        $request->member_id = $member_id;
        $request->credit = $credit;
        $request->terms = $credit_terms;
        $request->purpose = $purpose;
        $request->interest_rate = $interest_rate;
        $request->state = creditRequestStateEnum::CREATE;
        $request->operator_id = $officer_id;
        $request->operator_name = $officer->user_name;
        $request->create_time = Now();
        $insert = $request->insert();
        if (!$insert->STS) {
            return new result(false, 'Edit fail.', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success', $request);
    }


    public static function getMemberCreditRequestList($member_id)
    {
        $m = new member_credit_requestModel();
        $list = $m->getMemberRequestListAndRelative($member_id);
        return $list;
    }


    public static function getMemberCreditRequestDetail($request_id,$is_full_image_url=true)
    {
        $m = new member_credit_requestModel();
        $request = $m->find(array(
            'uid' => $request_id
        ));
        if ($request) {
            $list = $m->getRelativeListByRequestId($request_id,$is_full_image_url);
            // 每个关系人的状态
            foreach ($list as $k => $v) {
                $operate_state = member_relativeClass::getRelativeCanOperateState($v);
                $v['is_editable'] = $operate_state['is_editable'];
                $v['is_deletable'] = $operate_state['is_deletable'];
                $list[$k] = $v;
            }
            $request['relative_list'] = $list;
            $request['is_deletable'] = 0;
            if( $request['state'] == creditRequestStateEnum::CREATE  ){
                $request['is_deletable'] = 1;
            }
        }
        return $request;
    }


    /** 修改信用申请额度
     * @param $params
     * @return result
     */
    public static function editMemberCreditRequest($params)
    {
        if (!isset($params['uid']) || !$params['uid']) {
            return self::addMemberCreditRequest($params);
        }

        $uid = intval($params['uid']);
        $credit = intval($params['credit']);
        $credit_terms = intval($params['credit_terms']);
        $interest_rate = floatval($params['interest_rate']);
        $purpose = trim($params['purpose']);
        if ($credit < 0 || $credit_terms < 0 || !$uid) {
            return new result(false, 'Invalid param.', null, errorCodesEnum::INVALID_PARAM);
        }
        $officer_id = intval($params['officer_id']);
        $officer = new objectUserClass($officer_id);

        $m = new member_credit_requestModel();
        $request = $m->getRow($uid);
        if (!$request) {
            return new result(false, 'Invalid param.', null, errorCodesEnum::INVALID_PARAM);
        }
        if ($request->state != creditRequestStateEnum::CREATE) {
            return new result(false, "not allow to edit at current state!", null, errorCodesEnum::UN_EDITABLE);
        }
        $request->credit = $credit;
        $request->terms = $credit_terms;
        $request->purpose = $purpose;
        $request->interest_rate = $interest_rate;
        //$request->state = creditRequestStateEnum::CREATE;
        $request->update_operator_id = $officer_id;
        $request->update_operator_name = $officer->user_name;
        $request->update_time = Now();
        $up = $request->update();
        if (!$up->STS) {
            return new result(false, 'Edit fail.', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');
    }


    public static function addCreditRequestRelative($params, $is_upload_img = true)
    {
        $request_id = intval($params['credit_request_id']);
        $officer_id = intval($params['officer_id']);
        $officer = new objectUserClass($officer_id);

        $relation_type = $params['relation_type'];
        $relation_name = $params['relation_name'];
        $relation_name_code = $params['relation_name_code'];
        $name = $params['name'];
        $gender = $params['gender'];
        $country_code = $params['country_code'];
        $phone_number = $params['phone_number'];
        $phone_arr = tools::getFormatPhone($country_code, $phone_number);
        $contact_phone = $phone_arr['contact_phone'];
        $id_sn = $params['id_sn'];

        if ( !$name ) {
            return new result(false, 'Lack param.', null, errorCodesEnum::DATA_LACK);
        }

        $m = new member_credit_request_relativeModel();

        // 身份证已经抽出，不再检查
        //检查电话号码和身份证号在同一个关系表中的唯一性 by tim
       /* $sql = "select count(uid) cnt from member_credit_request_relative where request_id='" . $request_id . "' and  id_sn='" . $id_sn . "' ";
        $chk_cnt = $m->reader->getOne($sql);
        if ($chk_cnt > 0) {
            return new result(false, "The phone or ID-SN has already existed.", null, errorCodesEnum::RELATIVE_PERSON_ALREADY_EXISTS);
        }*/

        $m_request = new member_credit_requestModel();
        $credit_request = $m_request->getRow($request_id);
        if( !$credit_request ){
            return new result(false,'Not found credit request:'.$request_id,null,errorCodesEnum::INVALID_PARAM);
        }

        $relation = $m->newRow();
        $relation->request_id = $request_id;
        $relation->relation_type = $relation_type;
        $relation->relation_name = $relation_name;
        $relation->relation_name_code = $relation_name_code;
        $relation->name = $name;
        $relation->gender = $gender;
        if( $phone_number ){
            $relation->country_code = $country_code;
            $relation->phone_number = $phone_number;
            $relation->contact_phone = $contact_phone;
        }
        $relation->id_sn = $id_sn;


        // co上传图片
        if ($is_upload_img) {
            $save_path = fileDirsEnum::MEMBER_RELATION;
            if (empty($_FILES['headshot'])) {
                return new result(false, 'Lack param.', null, errorCodesEnum::DATA_LACK);
            }

            $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
            $upload->set('save_path', null);
            $upload->set('default_dir', $save_path);
            $re = $upload->server2upun('headshot');
            if ($re == false) {
                return new result(false, 'Upload photo fail', null, errorCodesEnum::UPLOAD_PIC_TO_UPYUN_FAIL);
            }
            $img_path = $upload->img_url;
            $relation->headshot = $img_path;
            unset($upload);


            if ($_FILES['id_front_image']) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun('id_front_image');
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::UPLOAD_PIC_TO_UPYUN_FAIL);
                }
                $img_path = $upload->img_url;
                $relation->id_front_image = $img_path;
                unset($upload);
            }

            if ($_FILES['id_back_image']) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun('id_back_image');
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::UPLOAD_PIC_TO_UPYUN_FAIL);
                }
                $img_path = $upload->img_url;
                $relation->id_back_image = $img_path;
                unset($upload);
            }
        } else {
            // 一定要传头像
            if( empty($params['headshot']) ){
                return new result(false, 'Not upload relative headshot.', null, errorCodesEnum::DATA_LACK);
            }
            $relation->headshot = trim($params['headshot']);
            $relation->id_front_image = trim($params['id_front_image']);
            $relation->id_back_image = trim($params['id_back_image']);

        }

        $relation->member_id = $credit_request['member_id'];
        $relation->operator_id = $officer->user_id;
        $relation->operator_name = $officer->user_name;
        $relation->create_time = Now();
        $insert = $relation->insert();
        if (!$insert->STS) {
            return new result(false, 'Add fail.', null, errorCodesEnum::DB_ERROR);
        }

        // 如果有提交个人资料
        if( $params['is_submit_profile']){
            $params['user_id'] = $params['officer_id'];
            $params['image_list'] = json_decode(urldecode($params['image_list']),true);
            $params['relative_id'] = $relation->uid;
            $re = member_relativeClass::profileCert($params,certSourceTypeEnum::OPERATOR);
            if( !$re->STS ){
                return $re;
            }
        }

        return new result(true, 'success', $relation);

    }

    public static function editCreditRequestRelative($params, $is_upload_img = true)
    {
        $relation_id = intval($params['relation_id']);
        $officer_id = intval($params['officer_id']);
        $officer = new objectUserClass($officer_id);

        $relation_type = $params['relation_type'];
        $relation_name = $params['relation_name'];
        $relation_name_code = $params['relation_name_code'];
        $name = $params['name'];
        $gender = $params['gender'];
        $country_code = $params['country_code'];
        $phone_number = $params['phone_number'];
        $phone_arr = tools::getFormatPhone($country_code, $phone_number);
        $contact_phone = $phone_arr['contact_phone'];
        $id_sn = $params['id_sn'];

        if ( !$name ) {
            return new result(false, 'Lack param.', null, errorCodesEnum::DATA_LACK);
        }

        $m = new member_credit_request_relativeModel();
        $relation = $m->getRow($relation_id);
        if (!$relation) {
            return new result(false, 'No relation info:'.$relation_id, null, errorCodesEnum::INVALID_PARAM);
        }

        $operate_state = member_relativeClass::getRelativeCanOperateState($relation);
        if (!$operate_state['is_editable']) {
            return new result(false, 'Un editable.', null, errorCodesEnum::UN_EDITABLE);
        }


        // 身份证已经抽出，不再检查
        //检查电话号码和身份证号在同一个关系表中的唯一性 by tim
        /*$sql = "select count(uid) cnt from member_credit_request_relative where request_id='" . $relation->request_id . "' and uid!='" . $relation_id . "' and  id_sn='" . $id_sn . "' ";
        $chk_cnt = $m->reader->getOne($sql);
        if ($chk_cnt > 0) {
            return new result(false, "The phone or ID-SN has already existed", null, errorCodesEnum::RELATIVE_PERSON_ALREADY_EXISTS);
        }*/

        $relation->relation_type = $relation_type;
        $relation->relation_name = $relation_name;
        $relation->relation_name_code = $relation_name_code;
        $relation->name = $name;
        $relation->gender = $gender;
        if( $phone_number ){
            $relation->country_code = $country_code;
            $relation->phone_number = $phone_number;
            $relation->contact_phone = $contact_phone;
        }

        $relation->id_sn = $id_sn;

        // co上传图片
        if ($is_upload_img) {

            $save_path = fileDirsEnum::MEMBER_RELATION;

            // APP端可以选择不修改图片

            if (!empty($_FILES['headshot'])) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun('headshot');
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::UPLOAD_PIC_TO_UPYUN_FAIL);
                }
                $img_path = $upload->img_url;
                $relation->headshot = $img_path;
                unset($upload);
            }


            if ($_FILES['id_front_image']) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun('id_front_image');
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::UPLOAD_PIC_TO_UPYUN_FAIL);
                }
                $img_path = $upload->img_url;
                $relation->id_front_image = $img_path;
                unset($upload);
            }

            if ($_FILES['id_back_image']) {
                $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
                $upload->set('save_path', null);
                $upload->set('default_dir', $save_path);
                $re = $upload->server2upun('id_back_image');
                if ($re == false) {
                    return new result(false, 'Upload photo fail', null, errorCodesEnum::UPLOAD_PIC_TO_UPYUN_FAIL);
                }
                $img_path = $upload->img_url;
                $relation->id_back_image = $img_path;
                unset($upload);
            }
        } else {
            // 一定要传头像
            if( empty($params['headshot']) ){
                return new result(false, 'Not upload relative headshot.', null, errorCodesEnum::DATA_LACK);
            }
            $relation->headshot = trim($params['headshot']);
            $relation->id_front_image = trim($params['id_front_image']);
            $relation->id_back_image = trim($params['id_back_image']);
        }

        $relation->update_operator_id = $officer->user_id;
        $relation->update_operator_name = $officer->user_name;
        $relation->update_time = Now();
        $up = $relation->update();
        if (!$up->STS) {
            return new result(false, 'Edit fail:'.$up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $relation);

    }

    public static function deleteCreditRequestRelative($relation_id)
    {
        $m = new member_credit_request_relativeModel();
        $relation = $m->getRow($relation_id);
        if (!$relation) {
            return new result(false, 'No info.', null, errorCodesEnum::INVALID_PARAM);
        }

        $operate_state = member_relativeClass::getRelativeCanOperateState($relation);
        if (!$operate_state['is_deletable']) {
            return new result(false, 'Un deletable.', null, errorCodesEnum::UN_DELETABLE);
        }

        $del = $relation->delete();
        if (!$del->STS) {
            return new result(false, 'Del fail.', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true, 'success');
    }


    public static function checkMemberChangePhoto($params)
    {
        $uid = intval($params['uid']);
        $m = new member_change_photo_requestModel();
        $request = $m->getRow($uid);
        if (!$request) {
            return new result(false, 'No request info:' . $uid, null, errorCodesEnum::NO_DATA);
        }

        $member = (new memberModel())->getRow($request->member_id);
        if (!$member) {
            return new result(false, 'No member info:' . $request->member_id, null, errorCodesEnum::NO_DATA);
        }
        $remark = $params['remark'];
        $check_result = $params['check_result'];
        if ($check_result == 1) {

            $request->state = commonApproveStateEnum::PASS;
            $request->operator_id = $params['user_id'];
            $request->operator_name = $params['user_name'];
            $request->update_time = Now();
            $request->remark = $remark;
            $up = $request->update();
            if (!$up->STS) {
                return new result(false, 'Handle fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
            }

            $member->member_image = $request->new_image;
            $member->member_icon = $request->new_image;
            $member->update_time = Now();
            $up = $member->update();
            if (!$up->STS) {
                return new result(false, 'Handle fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
            }


        } else {
            $request->state = commonApproveStateEnum::REJECT;
            $request->operator_id = $params['user_id'];
            $request->operator_name = $params['user_name'];
            $request->update_time = Now();
            $request->remark = $remark;
            $up = $request->update();
            if (!$up->STS) {
                return new result(false, 'Handle fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
            }
            return new result(true);
        }

        return new result(true);
    }

    public static function getCoListByBranchId($branch_id)
    {
        $branch_id = intval($branch_id);
        $r = new ormReader();
        $sql = "SELECT uu.* FROM um_user uu"
            . " INNER JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " WHERE sd.branch_id = $branch_id AND uu.user_status = 1 AND uu.user_position = " . qstr(userPositionEnum::CREDIT_OFFICER);
        return $r->getRows($sql);
    }


}