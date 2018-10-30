<?php

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/5/3
 * Time: 23:39
 */
class credit_researchClass
{
    /**
     * 获取client的request
     * @param $member_id
     * @return null
     */
    public static function getClientRequestCredit($member_id)
    {
        $member_id = intval($member_id);
        if (!$member_id) return null;
        $m = M("member_credit_request");
        $row = $m->orderBy('uid DESC')->find(array("member_id" => $member_id));
        if ($row) {
            $m_member_credit_request_relative = M('member_credit_request_relative');
            $relative_list = $m_member_credit_request_relative->select(array('request_id' => $row['uid']));
            $relative_list = resetArrayKey($relative_list, "uid");
            $row['relative_list'] = $relative_list;
        }
        return $row;
    }

    public static function getClientCreditRequestDetailById($request_id)
    {
        $m = new member_credit_requestModel();
        $request = $m->find(array(
            'uid' => $request_id
        ));
        if( $request ){
            $m_member_credit_request_relative = M('member_credit_request_relative');
            $relative_list = $m_member_credit_request_relative->select(array('request_id' => $request['uid']));
            $relative_list = resetArrayKey($relative_list, "uid");
            $request['relative_list'] = $relative_list;
        }
        return $request;
    }

    /**
     * 编辑或者添加client的request  弃用
     * @param $p
     */
    public static function updateClientRequestCredit($p)
    {
        if (!intval($p['member_id'])) return new result(false, "Invalid Parameter:Member-id");
        //不检查member-id的合理性
        if (!$p['credit'] || !$p['terms'] || !$p['operator_id']) {
            return new result(false, "Invalid Parameter:Require to input credit/terms/operator_id");
        }

        $m = M("member_credit_request");

        $member_id = intval($p['member_id']);
        $last_request = $m->orderBy('uid DESC')->find(array("member_id" => $member_id));
        if ($last_request && $last_request['state'] == 0) {
            $update_arr = array(
                'uid' => $last_request['uid'],
                'credit' => round($p['credit'], 2),
                'terms' => intval($p['terms']),
                'purpose' => trim($p['purpose']),
                'update_operator_id' => intval($p['operator_id']),
                'update_operator_name' => trim($p['operator_name']),
                'update_time' => Now(),
            );
            $rt = $m->update($update_arr);
        } else {
            $row = $m->newRow();
            $row->member_id = $member_id;
            $row->credit = round($p['credit'], 2);
            $row->terms = intval($p['terms']);
            $row->purpose = trim($p['purpose']);
            $row->state = 0;
            $row->operator_id = intval($p['operator_id']);
            $row->operator_name = trim($p['operator_name']);
            $row->create_time = Now();
            $rt = $row->insert();
        }
        return $rt;
    }

    /**
     * @param $uid
     * @return bool|mixed
     */
    public static function getMemberSalaryIncomeResearch($uid)
    {
        $m_research = new member_income_salaryModel();
        $income_research = $m_research->find(array('uid' => $uid));
        if ($income_research) {
            $m_member_income_salary_image = M('member_income_salary_image');
            $images = $m_member_income_salary_image->select(array('salary_id' => $income_research['uid']));
            $income_research['images'] = $images;
        }
        return $income_research;
    }

    /**
     * 添加客户收入调查信息
     * @param $income_info *收入信息
     *  member_id
     *  relative_id
     *  relative_name
     *  company_name
     *  company_phone
     *  position
     *  salary
     * @param $image_files
     *  array('a.png','b.png')
     * @param $operator_type
     * @param $operator_id
     * @return result
     */
    public static function addMemberSalaryIncomeResearch($income_info, $image_files, $operator_type, $operator_id)
    {
        $member_id = intval($income_info['member_id']);
        $company_name = $income_info['company_name'];
        $company_phone = $income_info['company_phone'];
        $position = $income_info['position'];
        $salary = round($income_info['salary'], 2);
        $coord_x = round($income_info['coord_x'], 6);
        $coord_y = round($income_info['coord_y'], 6);
        $address_detail = $income_info['address_detail'];
        if (!$member_id || $salary <= 0 || !$company_name) {
            return new result(false, 'Invalid param.', null, errorCodesEnum::INVALID_PARAM);
        }

        $operator = new objectUserClass($operator_id);
        $chk = $operator->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $m_research = new member_income_salaryModel();
        $research = $m_research->newRow();
        $research->member_id = $member_id;
        $research->company_name = $company_name;
        $research->company_phone = $company_phone;
        $research->position = $position;
        $research->salary = $salary;
        $research->operator_type = $operator_type;
        $research->operator_id = $operator_id;
        $research->operator_name = $operator->user_name;
        $research->update_operator_type = $operator_type;
        $research->update_operator_id = $operator_id;
        $research->update_operator_name = $operator->user_name;
        $research->create_time = Now();
        $research->coord_x = $coord_x;
        $research->coord_y = $coord_y;
        $research->address_detail = $address_detail;
        if (isset($income_info['relative_id'])) {
            $research->relative_id = intval($income_info['relative_id']);
            $research->relative_name = $income_info['relative_name'];
        }
        $insert = $research->insert();
        if (!$insert->STS) {
            return new result(false, 'Add income fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }
        $research_id = $research->uid;
        if (!empty($image_files)) {
            $sql = "insert into member_income_salary_image(salary_id,image_url) values ";
            $sql_arr = array();
            foreach ($image_files as $path) {
                $sql_arr[] = "('$research_id',".qstr($path).")";
            }
            $sql .= implode(',', $sql_arr);
            $insert = $m_research->conn->execute($sql);
            if (!$insert->STS) {
                return new result(false, 'Add income image fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
            }
        }
        return new result(true, 'success', $research);
    }


    /**
     * 修改客户收入调查信息
     * @param $research_id *调查ID
     * @param $income_info
     *  company_name
     *  company_phone
     *  position
     *  salary
     *  delete_image_ids  2,3,5,6
     * @param $image_files
     *  array('a.png','b.png')
     * @param $operator_type
     * @param $operator_id
     * @return result
     */
    public static function editMemberSalaryIncomeResearch($research_id, $income_info, $image_files, $operator_type, $operator_id)
    {
        $company_name = $income_info['company_name'];
        $company_phone = $income_info['company_phone'];
        $position = $income_info['position'];
        $salary = round($income_info['salary'], 2);
        $coord_x = round($income_info['coord_x'], 6);
        $coord_y = round($income_info['coord_y'], 6);
        $address_detail = $income_info['address_detail'];
        if (!$research_id || $salary <= 0 || !$company_name) {
            return new result(false, 'Invalid param.', null, errorCodesEnum::INVALID_PARAM);
        }

        $operator = new objectUserClass($operator_id);
        $chk = $operator->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $m_research = new member_income_salaryModel();
        $research = $m_research->getRow($research_id);
        if (!$research) {
            return new result(false, 'No research info:' . $research_id, null, errorCodesEnum::INVALID_PARAM);
        }
        if (isset($income_info['relative_id'])) {
            $research->relative_id = intval($income_info['relative_id']);
            $research->relative_name = $income_info['relative_name'];
        }
        $research->company_name = $company_name;
        $research->company_phone = $company_phone;
        $research->position = $position;
        $research->salary = $salary;
        $research->operator_type = $operator_type;
        $research->update_operator_id = $operator_id;
        $research->update_operator_name = $operator->user_name;
        $research->update_time = Now();
        $research->coord_x = $coord_x;
        $research->coord_y = $coord_y;
        $research->address_detail = $address_detail;
        $up = $research->update();
        if (!$up->STS) {
            return new result(false, 'Update research fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }


        // 要删除的图片
        $ids = trim($income_info['delete_image_ids']);
        if ($ids) {
            $ids = trim($ids, ',');
            $sql = "delete from member_income_salary_image where salary_id='$research_id' and uid in ($ids) ";
            $del = $m_research->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Delete old image fail.', null, errorCodesEnum::DB_ERROR);
            }
        }


        // 插入新的图片
        if (!empty($image_files)) {
            $sql = "insert into member_income_salary_image(salary_id,image_url) values ";
            $sql_arr = array();
            foreach ($image_files as $path) {
                $sql_arr[] = "('$research_id',".qstr($path).")";
            }
            $sql .= implode(',', $sql_arr);
            $insert = $m_research->conn->execute($sql);
            if (!$insert->STS) {
                return new result(false, 'Add income image fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
            }
        }
        return new result(true, 'success', $research);
    }


    public static function editMemberIncomeSalaryAddress($params)
    {
        $income_salary_id = intval($params['income_salary_id']);
        $coord_x = round($params['coord_x'], 6);
        $coord_y = round($params['coord_y'], 6);
        $address_detail = $params['address_detail'];
        $m = new member_income_salaryModel();
        $income_salary = $m->getRow($income_salary_id);
        if( !$income_salary ){
            return new result(false,'No income salary info:'.$income_salary_id,null,errorCodesEnum::INVALID_PARAM);
        }
        $income_salary->update_time = Now();
        $income_salary->coord_x = $coord_x;
        $income_salary->coord_y = $coord_y;
        $income_salary->address_detail = $address_detail;
        $up = $income_salary->update();
        if (!$up->STS) {
            return new result(false, 'Update research fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');

    }


    /**
     * 删除收入调查记录
     * @param $uid
     * @return result
     */
    public static function deleteMemberIncomeSalaryByUid($uid)
    {
        $uid = intval($uid);
        $conn = ormYo::Conn();
        // 删除主记录
        $sql = "delete from member_income_salary where uid='$uid' ";
        $del = $conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete fail.', null, errorCodesEnum::DB_ERROR);
        }

        // 删除图片记录
        $sql = "delete from member_income_salary_image where salary_id='$uid' ";
        $del = $conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete image fail.', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true);

    }


    /**
     * 获取Officer提交的member的收入调查
     * @param $member_id
     * @param $include_all //true不过滤数据，兼容以前的需求，如果为false，则要不是state<100,要不是grant_id=$grant_id
     * @return bool|mixed|null
     */
    public static function getOfficerLastSubmitMemberSalaryResearch($member_id,$include_all=true,$grant_id=0)
    {
        if (!$member_id) {
            return null;
        }

        $m = new member_income_salaryModel();
        $arr_filter=array('member_id' => $member_id);
        if(!$include_all){
            if($grant_id>0){
                $arr_filter['grant_id']=$grant_id;
            }else{
                $arr_filter['state']=array("<",100);
            }
        }
        $list = $m->orderBy('uid desc')->select($arr_filter);

        $salary_list = array();
        $m_member_income_salary_image = new member_income_salary_imageModel();
        foreach ($list as $research) {
            $images = $m_member_income_salary_image->select(array(
                'salary_id' => $research['uid']
            ));
            $image_list = array();
            foreach ($images as $v) {
                $v['image_url'] = getImageUrl($v['image_url']);
                $image_list[] = $v;
            }
            $research['image_list'] = $image_list;
            $salary_list[] = $research;
        }

        return $salary_list;
    }

    /**
     * 添加客户商业调查
     * @param $member_id
     * @param $business_info
     *  relative_id
     *  relative_name
     *  industry_id
     *  industry_place_id
     *  business_employees
     *  business_income
     *  business_expense
     *  business_profit
     *  industry_research_json
     *  research_text_src  -客户提供的调查数据
     *  delete_image_ids  2,3,5,6 想删除的图片id
     * @param $operator_type
     * @param $operator_id
     * @param array $images_files @格式修改，增加图片来源标识
     *   array(
     *       array(
     *           'image_url' => 'a.png',
     *           'image_source' => 0
     *       )
     *   )
     * @bool array $is_replace_image
     * @return result
     */
    public static function addMemberBusinessResearch($member_id, $business_info, $operator_type, $operator_id, $images_files = array())
    {

        $branch_code = trim($business_info['branch_code']);
        $industry_id = intval($business_info['industry_id']);
        $industry_place_id = intval($business_info['industry_place_id']);
        $industry_place_text = $business_info['industry_place_text'];
        $employees = intval($business_info['business_employees']);
        $income_business = round($business_info['business_income'], 2);
        $expense = round($business_info['business_expense'], 2);
        //$profit = round($business_info['business_profit'], 2);
        $profit = $income_business - $expense;
        $industry_research_json = ($business_info['industry_research_json']); // 外部处理
        $research_text_src = $business_info['research_text_src'];

        $coord_x = $business_info['coord_x'];
        $coord_y = $business_info['coord_y'];
        $address_detail = $business_info['address_detail'];

        $m_industry = new common_industryModel();
        $m_industry_research = new member_income_businessModel();

        if (!$branch_code) {
            return new result(false, 'Branch code is empty.', null, errorCodesEnum::INVALID_PARAM);
        }

        // 判断是否重复的code
        $code = $m_industry_research->getRow(array(
            'industry_id' => $industry_id,
            'member_id' => $member_id,
            'branch_code' => $branch_code
        ));

        // todo 是否自动跳到编辑
        if ($code) {
//            return new result(false, 'Branch name already exists.', null, errorCodesEnum::INVALID_PARAM);
        }

        $userObj = new objectUserClass($operator_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $member_info = (new memberModel())->getMemberInfoById($member_id);
        if (!$member_info) {
            return new result(false, 'No member info:' . $member_id, null, errorCodesEnum::MEMBER_NOT_EXIST);
        }


        try {

            $industry_info = $m_industry->getRow($industry_id);
            if (!$industry_info) {
                return new result(false, 'No industry info:' . $industry_id, null, errorCodesEnum::INVALID_PARAM);
            }


            // 插入调查明细
            $research = $m_industry_research->newRow();
            $research->branch_code = $branch_code;
            $research->member_id = $member_id;
            $research->industry_id = $industry_id;
            $research->industry_name = $industry_info->industry_name;

            // todo 暂时只有输入了
            $research->industry_place = 0;
            $research->industry_place_text = $industry_place_text;

            /*if ($industry_place_id > 0) {

                $industry_place = (new common_industry_placeModel())->getRow($industry_place_id);
                if (!$industry_place) {
                    return new result(false, 'No industry place info:' . $industry_place_id, null, errorCodesEnum::INVALID_PARAM);
                }
                $industry_place_text = $industry_place->place;
                $research->industry_place = $industry_place_id;
                $research->industry_place_text = $industry_place_text;

            } else {
                $research->industry_place = 0;
                $research->industry_place_text = $industry_place_text;
            }*/


            $research->research_text = $industry_research_json;
            $research->research_text_src = $research_text_src;
            $research->employees = $employees;
            $research->income = $income_business;
            $research->expense = $expense;
            $research->profit = $profit;
            $research->operator_type = $operator_type;
            $research->operator_id = $userObj->user_id;
            $research->operator_name = $userObj->user_name;
            $research->coord_x = $coord_x;
            $research->coord_y = $coord_y;
            $research->address_detail = $address_detail;
            $research->create_time = Now();
            $research->update_time = Now();


            $ins = $research->insert();
            if (!$ins->STS) {
                return new result(false, 'Insert research detail fail.', null, errorCodesEnum::DB_ERROR);
            }

            if (isset($business_info['relative_id'])) {
                // 有 0 的传值问题
                // 添加关系人
                if (!$business_info['relative_id']) {
                    $business_info['relative_id'] = '0';
                }
                $relative_ids = explode(',', trim($business_info['relative_id'], ','));
                if (!empty($relative_ids)) {
                    $rt = self::incomeBusinessAddRelativeList($member_info, $research->uid, $relative_ids);
                    if (!$rt->STS) {
                        return $rt;
                    }
                }

            }

            // 需要删除的图片
            $delete_image_ids = trim($business_info['delete_image_ids']);
            if ($delete_image_ids) {
                $delete_image_ids = trim($delete_image_ids, ',');
                $sql = "delete from member_income_business_image where industry_id='$industry_id' and 
                uid in ( $delete_image_ids ) ";
                $del = $m_industry_research->conn->execute($sql);
                if (!$del->STS) {
                    return new result(false, 'Delete image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            if ($business_info['change_state_images']) {
                $image_id_str = implode(',', $business_info['change_state_images']);
                $sql = "update member_income_business_image set is_delete = 1 where industry_id = '$industry_id' and uid in ( $image_id_str )";
                $up = $m_industry_research->conn->execute($sql);
                if (!$up->STS) {
                    return new result(false, 'Update image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            if (!empty($images_files)) {
                $rt = self::addIndustryBusinessImage($research->uid, $member_id, $industry_id, $operator_id, $images_files);
                if (!$rt->STS) {
                    return $rt;
                }
            }
        } catch (Exception $e) {
            return new result(false, $e->getMessage(), null, errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true, 'success', $research);

    }


    public static function editIncomeBusinessInfo($params, $operator_type, $operator_id, $images_files = array())
    {
        $research_id = intval($params['research_id']);
        $m_income_business = new member_income_businessModel();
        $research = $m_income_business->getRow($research_id);
        if (!$research) {
            return new result(false, 'No research info:' . $research_id, null, errorCodesEnum::INVALID_PARAM);
        }

        $business_info = $params;


        //$industry_place_id = intval($business_info['industry_place_id']);
        $industry_place_id = 0;  // 取消选择的方式了
        $industry_place_text = $business_info['industry_place_text'];
        $employees = intval($business_info['business_employees']);
        $income_business = round($business_info['business_income'], 2);
        $expense = round($business_info['business_expense'], 2);
        //$profit = round($business_info['business_profit'], 2);
        $profit = $income_business - $expense;
        $industry_research_json = $business_info['industry_research_json']; // 外部处理
        $research_text_src = $business_info['research_text_src'];

        $coord_x = round($business_info['coord_x'],6);
        $coord_y = round($business_info['coord_y'],6);
        $address_detail = $business_info['address_detail'];

        $userObj = new objectUserClass($operator_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $member_id = $research['member_id'];
        $member_info = (new memberModel())->find(array(
            'uid' => $member_id
        ));
        if (!$member_info) {
            return new result(false, 'No member info:' . $member_id, null, errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $industry_id = $research->industry_id;

        //$m_industry = new common_industryModel();
        $m_industry_research = new member_income_businessModel();

        try {

            if ($industry_place_id > 0) {

                $industry_place = (new common_industry_placeModel())->getRow($industry_place_id);
                if (!$industry_place) {
                    return new result(false, 'No industry place info:' . $industry_place_id, null, errorCodesEnum::INVALID_PARAM);
                }
                $industry_place_text = $industry_place->place;
                $research->industry_place = $industry_place_id;
                $research->industry_place_text = $industry_place_text;

            } else {
                $research->industry_place = 0;
                $research->industry_place_text = $industry_place_text;
            }


            $research->research_text = $industry_research_json;
            $research->research_text_src = $research_text_src;
            $research->employees = $employees;
            $research->income = $income_business;
            $research->expense = $expense;
            $research->profit = $profit;
            $research->update_operator_type = $operator_type;
            $research->update_operator_id = $userObj->user_id;
            $research->update_operator_name = $userObj->user_name;
            $research->coord_x = $coord_x;
            $research->coord_y = $coord_y;
            $research->address_detail = $address_detail;
            $research->update_time = Now();


            $up = $research->update();
            if (!$up->STS) {
                return new result(false, 'Insert research detail fail.', null, errorCodesEnum::DB_ERROR);
            }

            if (isset($business_info['relative_id'])) {
                // 有 0 的传值问题
                // 添加关系人

                // 需要删除原来的
                $sql = "delete from member_income_business_owner where income_business_id=" . qstr($research->uid);
                $del = $m_income_business->conn->execute($sql);
                if (!$del->STS) {
                    return new result(false, 'Delete old data fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
                }

                if (!$business_info['relative_id']) {
                    $business_info['relative_id'] = '0';
                }
                $relative_ids = explode(',', trim($business_info['relative_id'], ','));
                if (!empty($relative_ids)) {
                    $rt = self::incomeBusinessAddRelativeList($member_info, $research->uid, $relative_ids);
                    if (!$rt->STS) {
                        return $rt;
                    }
                }

            }

            // 需要删除的图片
            $delete_image_ids = trim($business_info['delete_image_ids']);
            if ($delete_image_ids) {
                $delete_image_ids = trim($delete_image_ids, ',');
                $sql = "delete from member_income_business_image where industry_id='$industry_id' and 
                uid in ( $delete_image_ids ) ";
                $del = $m_industry_research->conn->execute($sql);
                if (!$del->STS) {
                    return new result(false, 'Delete image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            if ($business_info['change_state_images']) {
                $image_id_str = implode(',', $business_info['change_state_images']);
                $sql = "update member_income_business_image set is_delete = 1 where industry_id = '$industry_id' and uid in ( $image_id_str )";
                $up = $m_industry_research->conn->execute($sql);
                if (!$up->STS) {
                    return new result(false, 'Update image fail', null, errorCodesEnum::DB_ERROR);
                }
            }

            if (!empty($images_files)) {
                $rt = self::addIndustryBusinessImage($research_id, $member_id, $industry_id, $operator_id, $images_files);
                if (!$rt->STS) {
                    return $rt;
                }
            }
        } catch (Exception $e) {
            return new result(false, $e->getMessage(), null, errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true, 'success', $research);


    }

    public static function editMemberBusinessAddress($params)
    {
        $income_business_id = intval($params['income_business_id']);
        $coord_x = round($params['coord_x'],6);
        $coord_y = round($params['coord_y'],6);
        $address_detail = $params['address_detail'];
        $m = new member_income_businessModel();
        $income_business = $m->getRow($income_business_id);
        if( !$income_business ){
            return new result(false,'No income business:'.$income_business_id,null,errorCodesEnum::INVALID_PARAM);
        }
        $income_business->coord_x = $coord_x;
        $income_business->coord_y = $coord_y;
        $income_business->address_detail = $address_detail;
        $income_business->update_time = Now();
        $up = $income_business->update();
        if( !$up->STS ){
            return new result(false,'Edit fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }
        return new result(true,'success');
    }


    public static function incomeBusinessAddRelativeList($member_info, $income_business_id, $relative_ids)
    {
        if (empty($relative_ids)) {
            return new result(true);
        }

        $income_business_id = intval($income_business_id);

        $all_relative = array();
        if (in_array(0, $relative_ids)) {
            $all_relative[0] = $member_info['display_name'] ?: $member_info['login_code'].'(own)';
        }

        $conn = ormYo::Conn();
        $r = new ormReader();
        // 查关系人的名字
        $sql = "select * from member_credit_request_relative where uid in (" . implode(',', $relative_ids) . ") ";
        $list = $r->getRows($sql);
        foreach ($list as $v) {
            $all_relative[$v['uid']] = $v['name'];
        }

        $sql = "insert into member_income_business_owner(income_business_id,relative_id,relative_name)
          values ";

        $sql_arr = array();
        foreach ($all_relative as $id => $name) {
            $sql_arr[] = "('$income_business_id','$id','$name')";
        }
        $sql .= implode(',', $sql_arr);
        $insert = $conn->execute($sql);
        if (!$insert) {
            return new result(false, 'Insert relative fail.', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true);

    }


    /**
     * 添加商业图片(可单独添加)
     * @param $research_id
     * @param $member_id
     * @param $industry_id
     * @param $operator_id
     * @param $image_files
     * @param $is_replace
     *  array('a.png','b.png')
     * @return result
     */
    public static function addIndustryBusinessImage($research_id, $member_id, $industry_id, $operator_id, $image_files)
    {
        $research_id = intval($research_id);
        if (empty($image_files)) {
            return new result(true);
        }
        $m_industry = new common_industryModel();
        /*$industry_info = $m_industry->getRow($industry_id);
        if (!$industry_info) {
            return new result(false, 'No industry info:' . $industry_id, null, errorCodesEnum::INVALID_PARAM);
        }*/

        $userObj = new objectUserClass($operator_id);

        $sql = "insert into member_income_business_image(income_business_id,member_id,industry_id,image_url,operator_id,operator_name,create_time,image_source)
        values ";
        $sql_arr = array();
        $create_time = Now();
        foreach ($image_files as $v) {
            $image_path = $v['image_url'];
            $image_source = $v['image_source'];
            $temp = array(
                qstr($research_id),
                qstr($member_id),
                qstr($industry_id),
                qstr($image_path),
                qstr($userObj->user_id),
                qstr($userObj->user_name),
                qstr($create_time),
                qstr($image_source)
            );
            $sql_arr[] = "(".implode(',',$temp).")";
        }
        $sql .= implode(',', $sql_arr);
        $insert = $m_industry->conn->execute($sql);
        if (!$insert->STS) {
            return new result(false, 'Insert business image fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        return new result(true);
    }

    public static function deleteAllMemberIncomeBusiness($member_id, $industry_id, $branch_code)
    {
        $m = new member_income_businessModel();
        $research_list = $m->select(array(
            'member_id' => $member_id,
            'industry_id' => $industry_id,
            'branch_code' => $branch_code,
        ));

        foreach ($research_list as $research) {
            $rt = self::deleteMemberIncomeBusiness($research['uid']);
            if (!$rt->STS) {
                return $rt;
            }
        }

        return new result(true);
    }

    public static function deleteMemberIncomeBusiness($uid)
    {
        $m = new member_income_businessModel();
        $research = $m->getRow(array(
            'uid' => $uid
        ));
        if (!$research) {
            return new result(false, 'Not found research info:' . $uid, null, errorCodesEnum::INVALID_PARAM);
        }

        $member_id = $research['member_id'];
        $industry_id = $research['industry_id'];
        $branch_code = $research['branch_code'];

        $rows = $m->getRows(array(
            'member_id' => $member_id,
            'industry_id' => $industry_id,
            'branch_code' => $branch_code,
            'operator_id' => $research['operator_id']
        ));

        $ids = array(0);  // 防止为空
        foreach ($rows as $v) {
            $ids[] = $v['uid'];
        }

        $ids_str = implode(',', $ids);

        // 删除调查数据
        $sql = "delete from member_income_business where uid in ($ids_str) ";
        $del = $m->conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Del fail:' . $del->MSG, null, errorCodesEnum::DB_ERROR);
        }

        // 删除下关系人的数据
        $sql = "delete from member_income_business_owner where income_business_id in ($ids_str) ";
        $m->conn->execute($sql);

        // 图片删除
        $sql = "delete from member_income_business_image where income_business_id in ($ids_str) ";
        $m->conn->execute($sql);

        return new result(true);
    }


    public static function getBusinessIncomeResearchDetailById($uid, $is_full_img_url = true)
    {
        $m = new member_income_businessModel();
        $research = $m->find(array(
            'uid' => $uid
        ));
        if (!$research) {
            return null;
        }
        // 拿到关系人
        $sql = "select * from member_income_business_owner where
            income_business_id='" . $research['uid'] . "' order by relative_id";
        $list = $m->reader->getRows($sql);
        $research['relative_list'] = $list;
        // 只拿自己的图片
        $research['image_list'] = self::getMemberBusinessImageOfIndustry($uid, $is_full_img_url);
        return $research;

    }


    /**
     * 获取调查记录
     * @param $member_id
     * @param $industry_id
     * @param $officer_id
     * @return bool|mixed|null
     */
    public static function getLastMemberBusinessIncomeResearchOfOfficer($member_id, $industry_id, $officer_id = null, $include_image = true)
    {

        $m = new member_income_businessModel();

        $where = array(
            'member_id' => $member_id,
            'industry_id' => $industry_id,
        );
        if ($officer_id) {
            $where['operator_id'] = $officer_id;
        }
        $research = $m->orderBy('uid DESC')->find($where);
        if ($research) {
            // 拿到关系人
            $sql = "select * from member_income_business_owner where
            income_business_id='" . $research['uid'] . "' order by relative_id";
            $list = $m->reader->getRows($sql);
            $research['relative_list'] = $list;
        }
        if ($include_image) {
            $research_id = intval($research['uid']);
            $research['image_list'] = self::getMemberIncomeBusinessImageByResearchId($research_id);
        }
        return $research;
    }


    public static function getMemberIncomeBusinessImageByResearchId($research_id)
    {
        $images = (new member_income_business_imageModel())->select(array(
            'income_business_id' => $research_id,
            'is_delete' => 0
        ));
        $image_list = array();
        foreach ($images as $v) {
            $v['image_path'] = $v['image_url'];
            $image_url = getImageUrl($v['image_url']);
            $v['image_url'] = $image_url;
            $image_list[] = $v;
        }
        return $image_list;
    }


    public static function getMemberBusinessImageOfIndustry($income_business_id, $is_full_url = true)
    {
        $images = (new member_income_business_imageModel())->select(array(
            'income_business_id' => $income_business_id,
            'is_delete' => 0
        ));
        $image_list = array();
        foreach ($images as $v) {
            if ($is_full_url) {
                $image_url = getImageUrl($v['image_url']);
            } else {
                $image_url = $v['image_url'];
            }
            $v['image_url'] = $image_url;
            $image_list[] = $v;
        }
        return $image_list;
    }

    /**
     * 弃用
     * 计算co调查平均值
     * @param $industry_id
     * @param $research_list
     */
    public static function avgCoBusinessResearch($industry_id, $research_list)
    {
        $employees = 0;
        $income = 0;
        $income_sub = array();
        $expense = 0;
        $expense_sub = array();
        $industry_place = 0;
        $industry_place_text = '';
        $research_text = '';
        $default_address = '';
        $default_x = 0;
        $default_y = 0;

        $m_common_industry = new common_industryModel();
        $industry_info = $m_common_industry->getIndustryInfo($industry_id);
        $industry_income_text = $industry_info['industry_income_text'];
        $industry_expense_text = $industry_info['industry_expense_text'];

        foreach ($research_list as $val) {
            $research_text = my_json_decode($val['research_text']);
            foreach ($research_text as $key => $sub) {
                if ($industry_income_text[$key]) {
                    $income += $sub;
                    $income_sub[$key] = round($income_sub[$key], 2) + $sub;
                }
                if ($industry_expense_text[$key]) {
                    $expense += $sub;
                    $expense_sub[$key] = round($expense_sub[$key], 2) + $sub;
                }
            }

            $employees += $val['employees'];
            $industry_place = $val['industry_place'];
            $industry_place_text = $val['industry_place_text'];
            $research_text = $val['research_text'];
            if ($val['address_detail']) {
                $default_address = $val['address_detail'];
            }
            if ($val['coord_x'] > 0) {
                $default_x = $val['coord_x'];
                $default_y = $val['coord_y'];
            }
        }

        $research_text = my_json_decode($research_text);
        foreach ($research_text as $k => $v) {
            if ($industry_income_text[$k]) {
                $research_text[$k] = round($income_sub[$k] / count($research_list), 2);
            }
            if ($industry_expense_text[$k]) {
                $research_text[$k] = round($expense_sub[$k] / count($research_list), 2);
            }
        }

        $employees_avg = round($employees / count($research_list), 2);
        $income_avg = round($income / count($research_list), 2);
        $expense_avg = round($expense / count($research_list), 2);
        $profit_avg = $income_avg - $expense_avg;
        $research['industry_place'] = $industry_place;
        $research['industry_place_text'] = $industry_place_text;
        $research['research_text'] = my_json_encode($research_text);
        $research['employees'] = $employees_avg;
        $research['income'] = $income_avg;
        $research['expense'] = $expense_avg;
        $research['profit'] = $profit_avg;
        $research['type'] = 'co_avg';
        $research['address_detail'] = $default_address;
        $research['coord_x'] = $default_x;
        $research['coord_y'] = $default_y;
        $research['relative_list'] = end($research_list)['relative_list'];
        $research['business_image'] = end($research_list)['business_image'];
        return $research;
    }

    /**
     * 弃用
     * 获取co调查列表
     * @param $member_id
     * @param $industry_id
     * @param $branch_code
     * @param $request_by_bm
     * @return bool|mixed|null
     */
    public static function getCoMemberBusinessIncomeResearchList($member_id, $industry_id, $branch_code, $request_by_bm = false)
    {
        $r = new ormReader();
        if ($request_by_bm) {
            $special_where = "(operator_type='" . researchPositionTypeEnum::CREDIT_OFFICER . "' or operator_type='" . researchPositionTypeEnum::OPERATOR . "')";
        } else {
            $special_where = "operator_type='" . researchPositionTypeEnum::CREDIT_OFFICER . "'";
        }


        $sql = "SELECT * FROM member_income_business WHERE uid IN (SELECT MAX(uid) FROM member_income_business WHERE member_id = $member_id AND branch_code =" . qstr($branch_code)
            . "  AND industry_id = $industry_id"
            . " AND " . $special_where . " GROUP BY operator_id)";
        $research_list = $r->getRows($sql);
        $research_list = resetArrayKey($research_list, 'operator_id');
        $m_member_income_business_image = M('member_income_business_image');
        $m_member_income_business_owner = M('member_income_business_owner');
        foreach ($research_list as $key => $research) {
            $income_business_id = $research['uid'];
            $business_image = $m_member_income_business_image->select(array('income_business_id' => $income_business_id, 'is_delete' => 0));
            $research['business_image'] = $business_image;

            $business_owner = $m_member_income_business_owner->select(array('income_business_id' => $income_business_id));
            $research['relative_list'] = $business_owner;
            $research_list[$key] = $research;
        }
        return $research_list;
    }


    /**
     * 添加其他收入
     * @param $member_id
     * @param $attachment_info
     * @param $image_files
     * @param $operator_id
     * @return result
     */
    public static function addMemberAttachmentResearch($member_id, $attachment_info, $image_files, $operator_id)
    {
        $member_id = intval($member_id);
        $title = trim($attachment_info['title']);
        $ext_type = intval($attachment_info['ext_type']);
        $ext_amount = round($attachment_info['ext_amount'], 2);
        $remark = trim($attachment_info['remark']);
        $operator_id = intval($operator_id);

        $userObj = new objectUserClass($operator_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $m_member_attachment = M('member_attachment');
        try {
            // 插入调查明细
            $research = $m_member_attachment->newRow();
            $research->member_id = $member_id;
            $research->title = $title;
            $research->ext_type = $ext_type;
            $research->ext_amount = $ext_amount;
            $research->remark = $remark;
            $research->operator_id = $userObj->user_id;
            $research->operator_name = $userObj->user_name;
            $research->update_operator_id = $userObj->user_id;
            $research->update_operator_name = $userObj->user_name;
            $research->create_time = Now();
            $research->update_time = Now();
            $ins = $research->insert();
            if (!$ins->STS) {
                return new result(false, 'Insert research detail fail.', null, errorCodesEnum::DB_ERROR);
            }

            $attachment_id = intval($ins->AUTO_ID);
            if (!empty($image_files)) {
                $sql = "insert into member_attachment_image(attachment_id,image_url) values ";
                $sql_arr = array();
                foreach ($image_files as $path) {
                    $sql_arr[] = "('$attachment_id',".qstr($path).")";
                }
                $sql .= implode(',', $sql_arr);
                $insert = $m_member_attachment->conn->execute($sql);
                if (!$insert->STS) {
                    return new result(false, 'Add images fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
                }
            }

            return new result(true, 'success', $research);
        } catch (Exception $e) {
            return new result(false, $e->getMessage(), null, errorCodesEnum::UNEXPECTED_DATA);
        }
    }

    /**
     * @param $research_id
     * @param $attachment_info
     *   delete_image_ids  2,3,5,6
     * @param $image_files
     * @param $operator_id
     * @return result
     */
    public static function editMemberAttachmentResearch($research_id, $attachment_info, $image_files, $operator_id)
    {
        $research_id = intval($research_id);
        $title = trim($attachment_info['title']);
        $ext_type = intval($attachment_info['ext_type']);
        $ext_amount = round($attachment_info['ext_amount'], 2);
        $remark = trim($attachment_info['remark']);
        $operator_id = intval($operator_id);

        $userObj = new objectUserClass($operator_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $m_member_attachment = M('member_attachment');
        $research = $m_member_attachment->getRow($research_id);
        if (!$research) {
            return new result(false, 'No research info:' . $research_id, null, errorCodesEnum::INVALID_PARAM);
        }

        try {
            $research->title = $title;
            $research->ext_type = $ext_type;
            $research->ext_amount = $ext_amount;
            $research->remark = $remark;
            $research->update_operator_id = $userObj->user_id;
            $research->update_operator_name = $userObj->user_name;
            $research->update_time = Now();
            $up = $research->update();
            if (!$up->STS) {
                return new result(false, 'Update research detail fail.', null, errorCodesEnum::DB_ERROR);
            }

            // 删除原来的图片
            $ids = trim($attachment_info['delete_image_ids']);
            if ($ids) {
                $ids = trim($ids, ',');
                $sql = "delete from member_attachment_image where attachment_id='$research_id' and uid in ($ids) ";
                $del = $m_member_attachment->conn->execute($sql);
                if (!$del->STS) {
                    return new result(false, 'Delete old image fail.', null, errorCodesEnum::DB_ERROR);
                }
            }


            if (!empty($image_files)) {
                $sql = "insert into member_attachment_image(attachment_id,image_url) values ";
                $sql_arr = array();
                foreach ($image_files as $path) {
                    $sql_arr[] = "('$research_id',".qstr($path).")";
                }
                $sql .= implode(',', $sql_arr);
                $insert = $m_member_attachment->conn->execute($sql);
                if (!$insert->STS) {
                    return new result(false, 'Add images fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
                }
            }

            return new result(true, 'success', $research);
        } catch (Exception $e) {
            return new result(false, $e->getMessage(), null, errorCodesEnum::UNEXPECTED_DATA);
        }
    }

    /** 删除其他收入调查记录
     * @param $uid
     * @return result
     */
    public static function deleteMemberAttachmentByUid($uid)
    {
        $uid = intval($uid);
        $conn = ormYo::Conn();
        // 删除主记录
        $sql = "delete from member_attachment where uid='$uid' ";
        $del = $conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete fail.', null, errorCodesEnum::DB_ERROR);
        }

        // 删除图片记录
        $sql = "delete from member_attachment_image where attachment_id='$uid' ";
        $del = $conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete image fail.', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true);

    }

    /**
     *
     * @param $uid
     * @return bool|mixed
     */
    public static function getMemberAttachmentResearch($uid)
    {
        $m_research = new member_attachmentModel();
        $income_research = $m_research->find(array('uid' => $uid));
        if ($income_research) {
            $m_member_attachment_image = M('member_attachment_image');
            $images = $m_member_attachment_image->select(array('attachment_id' => $income_research['uid']));
            $income_research['images'] = $images;
        }
        return $income_research;
    }


    /**
     * 获取member的其他调查
     * @param $member_id
     * @include_all //true的时候不过滤，false的时候如果grant-id>0就按grant过滤，否则过滤state<100
     * @return array|null
     */
    public static function getMemberAttachmentList($member_id,$include_all=true,$grant_id=0)
    {
        if (!$member_id) {
            return null;
        }
        $m = new member_attachmentModel();
        $arr_filter=array('member_id' => $member_id);
        if(!$include_all){
            if($grant_id>0){
                $arr_filter['grant_id']=$grant_id;
            }else{
                $arr_filter['state']=array("<",100);
            }
        }
        $list = $m->orderBy('uid desc')->select($arr_filter);

        $m_images = new member_attachment_imageModel();
        $attachment_list = array();
        foreach ($list as $research) {
            $images = $m_images->select(array(
                'attachment_id' => $research['uid']
            ));
            $image_list = array();
            foreach ($images as $image) {
                $image['image_url'] = getImageUrl($image['image_url']);
                $image_list[] = $image;
            }
            $research['image_list'] = $image_list;
            $attachment_list[] = $research;
        }
        return $attachment_list;
    }


    /**
     * 添加资产租赁信息
     * @param $asset_id
     * @param $rental_info
     *  renter
     *  monthly_rent
     *  remark
     * @param $image_files
     *  array('a.png','b.png')
     * @param $operator_type
     * @param $operator_id
     * @return result
     */
    public static function addMemberAssetRentalResearch($asset_id, $rental_info, $image_files, $operator_type, $operator_id)
    {

        $renter = $rental_info['renter'];
        $monthly_rent = round($rental_info['monthly_rent'], 2);
        $remark = $rental_info['remark'];
        if (!$renter || $monthly_rent <= 0) {
            return new result(false, 'Invalid param.', null, errorCodesEnum::INVALID_PARAM);
        }

        $asset_info = (new member_assetsModel())->getRow($asset_id);
        if (!$asset_info) {
            return new result(false, 'No asset info:' . $asset_id);
        }

        $userObj = new objectUserClass($operator_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }


        $m = new member_assets_rentalModel();
        $research = $m->newRow();
        $research->member_id = $asset_info->member_id;
        $research->asset_id = $asset_id;
        $research->renter = $renter;
        $research->monthly_rent = $monthly_rent;
        $research->remark = $remark;
        $research->operator_id = $operator_id;
        $research->operator_type = $operator_type;
        $research->operator_name = $userObj->user_name;
        $research->update_operator_id = $operator_id;
        $research->update_operator_type = $operator_type;
        $research->update_operator_name = $userObj->user_name;
        $research->create_time = Now();
        $insert = $research->insert();
        if (!$insert->STS) {
            return new result(false, 'Insert rental info fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        $research_id = $research->uid;
        if (!empty($image_files)) {
            $sql = "insert into member_assets_rental_image(rental_id,image_path) values ";
            $sql_arr = array();
            foreach ($image_files as $path) {
                $sql_arr[] = "('$research_id',".qstr($path).")";
            }
            $sql .= implode(',', $sql_arr);
            $insert = $m->conn->execute($sql);
            if (!$insert->STS) {
                return new result(false, 'Add images fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true, 'success', $research);

    }


    /** 编辑资产租赁信息
     * @param $research_id
     * @param $rental_info
     *  renter
     *  monthly_rent
     *  remark
     *  delete_image_ids  2,3,5,6
     * @param $image_files
     *  array('a.png','b.png')
     * @param $operator_type
     * @param $operator_id
     * @return result
     */
    public static function editMemberAssetRentalResearch($research_id, $rental_info, $image_files, $operator_type, $operator_id)
    {
        $renter = $rental_info['renter'];
        $monthly_rent = round($rental_info['monthly_rent'], 2);
        $remark = $rental_info['remark'];
        if (!$renter || $monthly_rent <= 0) {
            return new result(false, 'Invalid param.', null, errorCodesEnum::INVALID_PARAM);
        }

        $userObj = new objectUserClass($operator_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $m = new member_assets_rentalModel();
        $research = $m->getRow($research_id);
        if (!$research) {
            return new result(false, 'No rental info:' . $research_id, null, errorCodesEnum::INVALID_PARAM);
        }

        $research->renter = $renter;
        $research->monthly_rent = $monthly_rent;
        $research->remark = $remark;
        $research->update_operator_id = $operator_id;
        $research->update_operator_type = $operator_type;
        $research->update_operator_name = $userObj->user_name;
        $research->update_time = Now();
        $up = $research->update();
        if (!$up->STS) {
            return new result(false, 'Update rental info fail:' . $up->MSG, null, errorCodesEnum::DB_ERROR);
        }

        // 删除原来的图片
        $ids = trim($rental_info['delete_image_ids']);
        if ($ids) {
            $ids = trim($ids, ',');
            // 删除图片
            $sql = "delete from member_assets_rental_image where rental_id='$research_id' and uid in ($ids) ";
            $del = $m->conn->execute($sql);
            if (!$del->STS) {
                return new result(false, 'Delete old image fail.', null, errorCodesEnum::DB_ERROR);
            }
        }


        // 插入新图片
        if (!empty($image_files)) {
            $sql = "insert into member_assets_rental_image(rental_id,image_path) values ";
            $sql_arr = array();
            foreach ($image_files as $path) {
                $sql_arr[] = "('$research_id',".qstr($path).")";
            }
            $sql .= implode(',', $sql_arr);
            $insert = $m->conn->execute($sql);
            if (!$insert->STS) {
                return new result(false, 'Add images fail:' . $insert->MSG, null, errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true, 'success', $research);

    }

    /** 删除资产租金收入调查记录
     * @param $uid
     * @return result
     */
    public static function deleteMemberAssetRentalByUid($uid)
    {
        $uid = intval($uid);
        $conn = ormYo::Conn();
        // 删除主记录
        $sql = "delete from member_assets_rental where uid='$uid' ";
        $del = $conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete fail.', null, errorCodesEnum::DB_ERROR);
        }

        // 删除图片记录
        $sql = "delete from member_assets_rental_image where rental_id='$uid' ";
        $del = $conn->execute($sql);
        if (!$del->STS) {
            return new result(false, 'Delete image fail.', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true);

    }


    /**
     * 获取资产的租赁信息
     * @param $asset_id
     * @return bool|mixed|null
     */
    public static function getLastMemberAssetRentalResearch($asset_id)
    {
        if (!$asset_id) {
            return null;
        }
        $m = new member_assets_rentalModel();
        $research = $m->orderBy('uid DESC')->find(array(
            'asset_id' => $asset_id
        ));

        if ($research) {
            $images = (new member_assets_rental_imageModel())->select(array(
                'rental_id' => $research['uid']
            ));
            $image_list = array();
            foreach ($images as $v) {
                $v['image_path'] = getImageUrl($v['image_path']);
                $image_list[] = $v;
            }
            $research['image_list'] = $image_list;
        }
        return $research;
    }

    /**
     * 获取Officer提交的member的租金收入调查
     * @param $member_id
     * @return bool|mixed|null
     */
    public static function getMemberRentalResearch($member_id)
    {
        if (!$member_id) {
            return null;
        }

        $m = new member_assets_rentalModel();
        $list = $m->orderBy('uid desc')->select(array(
            'member_id' => $member_id,
        ));

        $rental_list = array();
        $m_member_assets_rental_image = new member_assets_rental_imageModel();
        foreach ($list as $research) {
            $images = $m_member_assets_rental_image->select(array(
                'rental_id' => $research['uid']
            ));
            $image_list = array();
            foreach ($images as $v) {
                $v['image_url'] = getImageUrl($v['image_url']);
                $image_list[] = $v;
            }
            $research['image_list'] = $image_list;
            $rental_list[] = $research;
        }

        return resetArrayKey($rental_list, 'asset_id');
    }

    /**
     * 修改资产估值
     * @param $asset_id
     * @param $evaluation
     * @param $remark
     * @param $operator_id
     * @return ormResult|result
     */
    public static function editAssetEvaluate($asset_id, $evaluation, $remark, $operator_id)
    {
        $asset_id = intval($asset_id);
        $evaluation = round($evaluation, 2);
        $remark = trim($remark);
        $operator_id = intval($operator_id);

        if ($evaluation < 0) {
            return new result(false, 'Invalid param.', null, errorCodesEnum::INVALID_PARAM);
        }

        $userObj = new objectUserClass($operator_id);
        $chk = $userObj->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        $m_member_assets = M('member_assets');
        $asset_info = $m_member_assets->find(array('uid' => $asset_id));
        if (!$asset_info) {
            return new result(false, 'Invalid Asset Id.');
        }

        $m_member_assets_evaluate = M('member_assets_evaluate');
        $row = $m_member_assets_evaluate->newRow();
        $row->branch_id = $userObj->branch_id;
        $row->member_id = $asset_info['member_id'];
        $row->evaluator_type = $userObj->position == userPositionEnum::BRANCH_MANAGER ? 1 : 0;
        $row->evaluate_time = Now();
        $row->operator_id = $userObj->user_id;
        $row->operator_name = $userObj->user_name;
        $row->member_assets_id = $asset_id;
        $row->evaluation = $evaluation;
        $row->remark = $remark;
        $rt = $row->insert();
        return $rt;
    }


    public static function assetAddSurveyInfo($asset_id, $survey_json, $officer_id)
    {
        $asset_id = intval($asset_id);
        $m_asset = new member_assetsModel();
        $asset_info = $m_asset->getRow($asset_id);
        if (!$asset_info) {
            return new result(false, 'No asset info:' . $asset_id, null, errorCodesEnum::INVALID_PARAM);
        }
        /*if( !member_assetsClass::assetIsCanEdit($asset_info) ){
            return new result(false,'Asset can not edit.',null,errorCodesEnum::UN_EDITABLE);
        }*/
        $officer = new objectUserClass($officer_id);
        $chk = $officer->checkValid();
        if (!$chk->STS) {
            return $chk;
        }

        // 加日志
        $m_log = new member_asset_researchModel();
        $log = $m_log->newRow();
        $log->member_id = $asset_info->member_id;
        $log->member_asset_id = $asset_id;
        $log->research_text = $survey_json;
        $log->operator_id = $officer_id;
        $log->operator_name = $officer->user_name;
        $log->branch_id = $officer->branch_id;
        $log->create_time = Now();
        $log->update_time = Now();
        $insert = $log->insert();
        if (!$insert->STS) {
            return new result(false, 'Add asset survey fail.', null, errorCodesEnum::DB_ERROR);
        }

        $asset_info->research_text = $survey_json;
        $asset_info->update_time = Now();
        $up = $asset_info->update();
        if (!$up->STS) {
            return new result(false, 'Update asset fail.', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success');

    }

    /**
     * 获取co添加历史
     * @param $member_id
     * @param $branch_id
     * @return array
     */
    public static function getCoSuggestCredit($member_id)
    {
        $r = new ormReader();
        $sql = "select * from member_follow_officer WHERE member_id = $member_id AND is_active = 1";
        $co_list = $r->getRows($sql);
        if (!$co_list) {
            return array();
        }
        $co_ids = array_column($co_list, 'officer_id');
        $co_id_str = "(" . implode(',', $co_ids) . ")";

        $sql = "SELECT * from member_credit_suggest WHERE uid IN (SELECT max(uid) FROM member_credit_suggest WHERE member_id = $member_id AND request_type = 0 AND operator_id IN $co_id_str GROUP BY operator_id)";
        $suggest_list = $r->getRows($sql);
        return resetArrayKey($suggest_list, 'operator_id');
    }

    public static function getLastSuggestCreditByOfficerId($member_id, $officer_id)
    {
        $r = new ormReader();
        $sql = "select * from member_credit_suggest where member_id='" . $member_id . "' and operator_id='" . $officer_id . "' order by uid desc";
        $item = $r->getRow($sql);
        if (count($item)) {
            $m_member_credit_suggest = new member_credit_suggestModel();
            $item['suggest_detail_list'] = $m_member_credit_suggest->getSuggestDetailBySuggestId($item['uid']);
            $item['suggest_rate'] = $m_member_credit_suggest->getSuggestRateBySuggestId($item['uid']);
            $item['suggest_product'] = $m_member_credit_suggest->getSuggestProductBySuggestId($item['uid']);
        }
        return $item;
    }
    public static function getLastSuggestCreditBySuggestId($suggest_id)
    {
        $r = new ormReader();
        $sql = "select * from member_credit_suggest where uid='" . $suggest_id . "'";
        $item = $r->getRow($sql);
        if (count($item)) {
            $m_member_credit_suggest = new member_credit_suggestModel();
            $item['suggest_detail_list'] = $m_member_credit_suggest->getSuggestDetailBySuggestId($item['uid']);
            $item['suggest_rate'] = $m_member_credit_suggest->getSuggestRateBySuggestId($item['uid']);
            $item['suggest_product'] = $m_member_credit_suggest->getSuggestProductBySuggestId($item['uid']);
        }
        return $item;
    }



    /**
     * 获取会员收入和支出记录
     * @param $member_id
     * @param $operator_id
     */
    public function getMemberIncomeAndExpenseList($member_id, $operator_id)
    {
        //salary
        $salary = self::getOfficerLastSubmitMemberSalaryResearch($member_id);

        //Rental
        $rental = self::getMemberRentalResearch($member_id);

        //Business
        $business = self::getMemberBusinessResearch($member_id, $operator_id);

        //Attachment
        $attachment = self::getMemberAttachmentList($member_id,false);
        $attachment_income = array();
        $attachment_expense = array();
        foreach ($attachment as $val) {
            if ($val['ext_type'] == 1) {
                $attachment_income[] = $val;
            }
            if ($val['ext_type'] == 2) {
                $attachment_expense[] = $val;
            }
        }

        $data['income']['salary'] = $salary;
        $data['income']['rental'] = $rental;
        $data['income']['business'] = $business;
        $data['income']['attachment'] = $attachment_income;
        $data['expense']['attachment'] = $attachment_expense;
        return $data;
    }

    /**
     * 获取Officer提交的member的租金收入调查
     * @param $member_id
     * @param $operator_id
     * @return bool|mixed|null
     */
    public static function getMemberBusinessResearch($member_id, $operator_id,$grant_id=0)
    {
        if (!$member_id || !$operator_id) {
            return null;
        }
        if($grant_id>0){
            $sql = "SELECT * FROM member_income_business WHERE uid IN (SELECT MAX(uid) FROM member_income_business WHERE member_id = $member_id and grant_id='".$grant_id."' AND operator_id = $operator_id GROUP BY industry_id)";
        }else{
            $sql = "SELECT * FROM member_income_business WHERE uid IN (SELECT MAX(uid) FROM member_income_business WHERE member_id = $member_id and state<100 AND operator_id = $operator_id GROUP BY industry_id)";
        }

        $r = new ormReader();

        $list = $r->getRows($sql);
        if ($list) {
            $business_income_ids = array_column($list, 'uid');
//            $business_income_id_str = "(" . implode(',', $business_income_ids) . ")";
//            $sql = "SELECT * FROM member_income_business_owner WHERE income_business_id IN $business_income_id_str";
//            $income_business_owner = $r->getRows($sql);
//            $income_business_owner_new = array();
//            foreach ($income_business_owner as $owner) {
//                $income_business_owner_new[$owner['income_business_id']][] = $owner;
//            }
//
//            $sql = "SELECT * FROM member_income_business_image WHERE income_business_id IN $business_income_id_str";
//            $income_business_image = $r->getRows($sql);
//            $income_business_image_new = array();
//            foreach ($income_business_image as $image) {
//                $income_business_image_new[$owner['income_business_id']][] = $image;
//            }

            $income_business_image = self::getMemberIncomeBusinessImage($business_income_ids);
            $income_business_owner = self::getMemberIncomeBusinessOwner($business_income_ids);

            foreach ($list as $key => $income) {
                $owner = $income_business_owner[$income['uid']];
                $image = $income_business_image[$income['uid']];
                $income['owner_list'] = $owner;
                $income['image_list'] = $image;
                $list[$key] = $income;
            }
        }
        return $list;
    }


    /**
     * client相关信息
     * @param $member_id
     * @return array
     */
    public static function getMemberMemberReferenceInfo($member_id,$grant_id=0)
    {
        $reference_info = array();
        $r = new ormReader();
        //cbc
        $m_client_cbc = M('client_cbc');
        $client_cbc = $m_client_cbc->orderBy('uid DESC')->find(array('client_id' => $member_id, "client_type" => 0, 'state' => 1));
        $reference_info['client_cbc'] = $client_cbc;

        // 收入的调查
        //租金
        $rental_research = self::getMemberRentalResearch($member_id);
        $reference_info['rental_research'] = $rental_research;

        //salary income
        $salary_income = self::getOfficerLastSubmitMemberSalaryResearch($member_id,false,$grant_id);
        $reference_info['salary_income'] = $salary_income;

        //Attachment
        $attachment = self::getMemberAttachmentList($member_id,false,$grant_id);
        $reference_info['attachment'] = $attachment;

        //Business
        $co_list = memberClass::getMemberCreditOfficerList($member_id);
        $co_list = resetArrayKey($co_list, "officer_id");

        $member_industry_info = memberClass::getMemberIndustryInfo($member_id);
        $m_common_industry = new common_industryModel();
        foreach ($member_industry_info as $key => $val) {
            $industry_info = $m_common_industry->getIndustryInfo($val['uid']);
            if($grant_id>0){
                $sql = "select branch_code from member_income_business WHERE industry_id = " . intval($val['uid']) . " and grant_id='".$grant_id."' AND member_id = " . $member_id . " GROUP BY branch_code";
            }else{
                $sql = "select branch_code from member_income_business WHERE industry_id = " . intval($val['uid']) . " and state<100 AND member_id = " . $member_id . " GROUP BY branch_code";
            }

            $branch_code = $r->getRows($sql);
            $industry_info['branch_code'] = $branch_code;
            $member_industry_info[$key] = $industry_info;
        }
        $reference_info['member_industry_info'] = $member_industry_info;

        $co_research = array();
        foreach ($co_list as $co) {
            $business_research = self::getMemberBusinessResearch($member_id, $co['officer_id'],$grant_id);
            foreach ($business_research as $research) {
                $co_research[$co['officer_id']][$research['industry_id']][$research['branch_code']] = $research;
            }
        }
        if($grant_id>0){
            $sql = "SELECT * FROM member_income_business WHERE uid IN (SELECT MAX(uid) FROM member_income_business WHERE member_id = $member_id and grant_id=$grant_id AND operator_type = 1 GROUP BY industry_id)";
        }else{
            $sql = "SELECT * FROM member_income_business WHERE uid IN (SELECT MAX(uid) FROM member_income_business WHERE member_id = $member_id and state<100 AND operator_type = 1 GROUP BY industry_id)";
        }


        $bm_research = $r->getRows($sql);
        $business_research_operator = $co_list;
        if ($bm_research) {
            $business_income_ids = array_column($bm_research, 'uid');
            $income_business_image = self::getMemberIncomeBusinessImage($business_income_ids);
            $income_business_owner = self::getMemberIncomeBusinessOwner($business_income_ids);

//            $business_income_id_str = "(" . implode(',', $business_income_ids) . ")";
//            $sql = "SELECT * FROM member_income_business_owner WHERE income_business_id IN $business_income_id_str";
//            $income_business_owner = $r->getRows($sql);
//            $income_business_owner_new = array();
//            foreach ($income_business_owner as $owner) {
//                $income_business_owner_new[$owner['income_business_id']][] = $owner;
//            }
//
//            $sql = "SELECT * FROM member_income_business_image WHERE income_business_id IN $business_income_id_str";
//            $income_business_image = $r->getRows($sql);
//            $income_business_image_new = array();
//            foreach ($income_business_image as $image) {
//                $income_business_image_new[$image['income_business_id']][] = $image;
//            }

            foreach ($bm_research as $key => $income) {
                $owner = $income_business_owner[$income['uid']];
                $image = $income_business_image[$income['uid']];
                $income['owner_list'] = $owner;
                $income['image_list'] = $image;
                $bm_research[$key] = $income;
            }

            $first_bm_research = reset($bm_research);
            array_unshift($business_research_operator, array('officer_id' => $first_bm_research['operator_id'], 'officer_name' => $first_bm_research['operator_name']));
            foreach ($bm_research as $research) {
                $co_research[$first_bm_research['operator_id']][$research['industry_id']][$research['branch_code']] = $research;
            }
        }

        $reference_info['business_research_operator'] = $business_research_operator;
        $reference_info['co_research'] = $co_research;

        //check list
        $at_list = memberIdentityClass::getIdentityType();
        $at_list = array_keys($at_list);
        $cert_type = '(' . implode(',', $at_list) . ')';
        $sql = "SELECT * FROM member_verify_cert WHERE uid IN (SELECT MAX(uid) FROM member_verify_cert WHERE member_id = $member_id AND cert_type IN $cert_type AND verify_state = " . certStateEnum::PASS . " GROUP BY cert_type)";
        $check_list = $r->getRows($sql);
        $check_list = resetArrayKey($check_list, 'cert_type');
        $m_member_verify_cert_image = M('member_verify_cert_image');
        foreach ($check_list as $key => $val) {
            $val['images'] = $m_member_verify_cert_image->select(array('cert_id' => $val['uid']));
            $check_list[$key] = $val;
        }
        $reference_info['check_list'] = $check_list;

        //assets
        $m_member_assets = M('member_assets');
        $assets = $m_member_assets->orderBy('asset_type ASC')->select(array('member_id' => $member_id, 'asset_state' => array('>=', assetStateEnum::CERTIFIED)));
        $reference_info['assets'] = $assets;

        $assets_group = array();
        foreach ($assets as $asset) {
            $asset['images'] = $m_member_verify_cert_image->select(array('cert_id' => $asset['cert_id']));
            $assets_group[$asset['asset_type']][] = $asset;
        }
        $reference_info['assets_group'] = $assets_group;

        //assets evaluate
        $sql = "SELECT * FROM member_assets_evaluate WHERE uid IN (SELECT MAX(uid) FROM member_assets_evaluate WHERE member_id = $member_id AND evaluator_type = 1 GROUP BY member_assets_id)";
        $assets_evaluate_list = $r->getRows($sql);
        $reference_info['assets_evaluate_list'] = resetArrayKey($assets_evaluate_list, 'member_assets_id');

        return $reference_info;
    }

    public static function getMemberIncomeBusinessImage($business_income_id)
    {
        if (!is_array($business_income_id)) {
            $business_income_id = array(
                intval($business_income_id)
            );
        }

        $business_income_id_str = "(" . implode(',', $business_income_id) . ")";
        $sql = "SELECT * FROM member_income_business_image WHERE income_business_id IN $business_income_id_str";
        $r = new ormReader();
        $income_business_image = $r->getRows($sql);
        $income_business_image_new = array();
        foreach ($income_business_image as $image) {
            $income_business_image_new[$image['income_business_id']][] = $image;
        }
        return $income_business_image_new;
    }

    public static function getMemberIncomeBusinessOwner($business_income_id)
    {
        if (!is_array($business_income_id)) {
            $business_income_id = array(
                intval($business_income_id)
            );
        }

        $business_income_id_str = "(" . implode(',', $business_income_id) . ")";
        $sql = "SELECT * FROM member_income_business_owner WHERE income_business_id IN $business_income_id_str";
        $r = new ormReader();
        $income_business_owner = $r->getRows($sql);
        $income_business_owner_new = array();
        foreach ($income_business_owner as $owner) {
            $income_business_owner_new[$owner['income_business_id']][] = $owner;
        }
        return $income_business_owner_new;
    }

    /*
     * 获取一个客户的信用分析，operator_id可以是operator/co/bm/customer_service
     */
    public static function getSystemAnalysisCreditOfMember($member_id, $operator_id, $operator_position = userPositionEnum::CREDIT_OFFICER,$grant_id=0)
    {
        //co和bm的分析数据，business都来自自己的调查一样分析，基于所有
        //operator&customer-service
        //operator&customer-service
        //client request

        $member_request = self::getClientRequestCredit($member_id);
        $relative_ids = array('0');//要判断资产是否属于ids
        if (!$member_request || $member_request['state'] != creditRequestStateEnum::CREATE) {
            $member_request = array();
        } else {
            $relative_list = $member_request['relative_list'];
            if (is_array($relative_list)) {
                $relative_ids = array_merge($relative_ids, array_keys($relative_list));
            }
        }

        if($grant_id>0){
            $arr_filter=array(
                'member_id' => $member_id,
                "grant_id"=>$grant_id
            );
        }else{
            $arr_filter=array(
                'member_id' => $member_id,
                "state"=>array("<",100)
            );
        }
        //获取salary
        $m_salary = new member_income_salaryModel();
        $list = $m_salary->orderBy('uid desc')->select($arr_filter);
        $salary = $list;
        //获取资产
        $asset_by_type = credit_officerClass::getMemberAssetsListAndEvaluateOfOfficerGroupByType($member_id, $operator_id, $operator_position);
        $analysis_asset=array();
        $cert_list = (new certificationTypeEnum())->Dictionary();

        if(!count($asset_by_type)){
            $analysis_asset[]="Never verified any asset, <kbd>no asset can mortgage</kbd>";
        }

        $asset = array();
        foreach ($asset_by_type as $arr) {
            if (is_array($arr)) {
                foreach ($arr as $item) {
                    if (!$item['mortgage_state']) {//没有抵押的才能继续
                        $owner_id_list = $item['owner_id_list'];//这个只是id的一维数组
                        $is_valid_owner = true;
                        foreach ($owner_id_list as $owner_id) {
                            if (!in_array($owner_id, $relative_ids)) {//属于关联人列表的才能继续
                                $is_valid_owner = false;
                                break;
                            }
                        }
                        if ($is_valid_owner) {
                            $asset[] = $item;
                        }else{
                            $analysis_asset[]=$item['uid'].".Not Allowed to Mortgage <kbd>".$item['asset_name'] . "(" . $cert_list[$item['asset_type']] . ")"."</kbd>,Invalid Owner:<kbd>".$relative_list[$owner_id]['name']."</kbd>";
                        }
                    }//end if
                }// end for each
            }
        }




        //获取business
        $business = self::getMemberIncomeBusinessAnalysis($member_id, $operator_id, $operator_position,$grant_id);
        //获取pay_to_cbc
        $m_cbc = new client_cbcModel();
        $last_cbc = $m_cbc->orderBy("uid desc")->find(array("client_id" => $member_id, "client_type" => 0));


        //Attachment
        $attachment = self::getMemberAttachmentList($member_id,false,$grant_id);
        $attachment_income = array();
        $attachment_expense = array();
        foreach ($attachment as $val) {
            if ($val['ext_type'] == 1) {
                $attachment_income[] = $val;
            }
            if ($val['ext_type'] == 2) {
                $attachment_expense[] = $val;
            }
        }

        //suggest_profile
        $m_dict = new core_dictionaryModel();
        $setting = $m_dict->getDictValue(dictionaryKeyEnum::CREDIT_GRANT_RATE);
        //$ret['rate_discount'] = $setting;

        $ret = array(
            "member_request" => $member_request ?: array(),
            "income" => array(),
            "expense" => array(),
            "ability" => 0,
            "suggest" => array(),
            "rate_discount" => $setting,
            "all_asset" => resetArrayKey($asset, "uid")
        );
        $ability = 0;
        $total_income=0;
        $total_expense=0;
        if (count($salary)) {
            foreach ($salary as $item) {
                $credit_item = array(
                    "credit_key" => $item['company_name'],
                    "credit_val" => $item['salary'],
                    "credit_rate" => ($setting['default_salary_rate'] ?: 100) . '%',
                    "credit" => intval($item['salary'] * ($setting['default_salary_rate'] ?: 100) / 100),
                    "remark" => 'Get ' . $setting['default_salary_rate'] . "% from System.Developer Setting(Can be chang)"
                );
                $ability += $credit_item['credit'];
                $total_income+=$credit_item['credit'];
                $ret['income']['salary'][] = array_merge($item, $credit_item);
            }
        }

        if (count($asset)) {
            foreach ($asset as $item) {
                if ($item['officer_rent'] > 0) {
                    $credit_item = array(
                        "credit_key" => $item['asset_name'] . "(" . $cert_list[$item['asset_type']] . ")",
                        "credit_val" => $item['officer_rent'],
                        "credit_rate" => (($setting['default_rental_rate'] ?: 100)) . '%',
                        "credit" => intval($item['officer_rent'] * ($setting['default_rental_rate'] ?: 100) / 100),
                        "remark" => 'Get ' . $setting['default_rental_rate'] . "% from System.Developer Setting(Can be chang)"
                    );
                    $ability += $credit_item['credit'];
                    $total_income+=$credit_item['credit'];
                    $ret['income']['rental'][] = array_merge($item, $credit_item);
                }
            }
        }

        if ($business) {
            foreach ($business as $item) {
                $profit = 0;
                /*
                foreach ($item['income_business'] as $research) {
                    $profit += $research['profit'];
                }
                */
                $profit += $item['profit'];
                if ($profit == 0) continue;
                $credit_item = array(
                    "credit_key" => $item['industry_name'],
                    "credit_val" => $profit,
                    "credit_rate" => ($item['credit_rate'] ?: 100) . '%',
                    "credit" => intval($profit * ($item['credit_rate'] ?: 100) / 100),
                    "remark" => 'Get ' . $item['credit_rate'] . "% from Industry Setting(Can be chang)"
                );
                $ability += $credit_item['credit'];
                $total_income+=$credit_item['credit'];
                $ret['income']['business'][] = array_merge($item, $credit_item);
            }
        }
        if ($attachment_income) {
            foreach ($attachment_income as $item) {
                if ($item['ext_amount'] > 0) {
                    $credit_item = array(
                        "credit_key" => $item['title'],
                        "credit_val" => $item['ext_amount'],
                        "credit_rate" => (($setting['default_attachment_rate'] ?: 100)) . '%',
                        "credit" => intval($item['ext_amount'] * ($setting['default_attachment_rate'] ?: 100) / 100),
                        "remark" => 'Get ' . $setting['default_attachment_rate'] . "% from System.Developer Setting(Can be chang)"
                    );
                    $ability += $credit_item['credit'];
                    $total_income+=$credit_item['credit'];
                    $ret['income']['attachment'][] = array_merge($item, $credit_item);
                }
            }
        }

        if ($last_cbc) {
            if ($last_cbc['pay_to_cbc'] > 0) {
                $credit_item = array(
                    "credit_key" => "Pay To Other Bank",
                    "credit_val" => $last_cbc['pay_to_cbc'],
                    "credit_rate" => '100%',
                    "credit" => $last_cbc['pay_to_cbc'],
                    "remark" => "No discount for expense"
                );
                $ability -= $credit_item['credit'];
                $total_expense+=$credit_item['credit'];
                $ret['expense']['CBC'][] = array_merge($last_cbc, $credit_item);
            }
        }

        if ($attachment_expense) {
            foreach ($attachment_expense as $item) {
                if ($item['ext_amount'] > 0) {
                    $credit_item = array(
                        "credit_key" => $item['title'],
                        "credit_val" => $item['ext_amount'],
                        "credit_rate" => "100%",
                        "credit" => $item['ext_amount'],
                        "remark" => "No discount for expense"
                    );
                    $ability -= $credit_item['credit'];
                    $total_expense+=$credit_item['credit'];
                    $ret['expense']['attachment'][] = array_merge($item, $credit_item);
                }
            }
        }
        $ability = $ability ?: 0;
        $ret['ability'] = $ability;
        $ret['total_income']=$total_income;
        $ret['total_expense']=$total_expense;
        $ret['suggest'] = array(
            "terms" => ($member_request['terms'] > 0 ? min($member_request['terms'], $setting['default_max_terms']) : ($setting['default_terms'] ?: 1))
        );
        $ret['suggest']['terms_remark'] = "Get from client-request,not allow more than max_terms(" . $setting['default_max_terms'] . ") of System.Developer Setting.";

        $tip = array();
        if ($ability <= 0) {
            $ability = 0;
            $tip[] = "System analysis the repayment ability less than 0,please double-check";
        }

        $default_credit = $ability * 0.95 * ($setting['default_terms'] ?: 1);
        $default_credit=intval($default_credit);
        $sys_default = $default_credit;
        if ($default_credit > $member_request['credit']) {
            $tip[] = "We can ask client to loan more as " . intval($default_credit) . "(no mortgage)";
            $default_credit = $member_request['credit'];
        }
        $ret['suggest']['default_credit'] = intval($default_credit);
        $ret['suggest']['default_credit_remark'] = '=Take minimum between <br/>【repay_ability('.$ability.')*95% * '.$setting['default_terms'].'(months)='.$sys_default.'】<br/>  【request-credit('.$member_request['credit'].')】';
        //"not require mortgage for default_credit,it equals 【ability*95%*default_terms(" . $setting['default_terms'] . ")】,5% discount off ability for pay to interest.";


        $rate_asset = global_settingClass::getAssetsCreditGrantRateAndDefaultInterest();//默认抵押物估值折率
        $allowed_mortgage_type=member_assetsClass::getMortgageType();
        $allowed_collateral_type=member_assetsClass::getCollateralType();
        $collateral_list=array();
        $ret['rate_increase'] = $rate_asset;
        $total_increase = 0;
        if (count($asset)) {
            foreach ($asset as $item) {
                if(in_array($item['asset_type'],$allowed_collateral_type)){
                    //担保类型不抵押
                    $collateral_list[$item['uid']]=$item;
                    continue;
                }else{
                    if(!in_array($item['asset_type'],$allowed_mortgage_type)){
                        continue;
                    }
                }
                if ($item['officer_evaluation'] > 0) {
                    $credit_item = array(
                        "credit_key" => $item['asset_name'] . "(" . $cert_list[$item['asset_type']] . ")",
                        "credit_val" => $item['officer_evaluation'],
                        "credit_rate" => (($rate_asset[$item['asset_type']] ?: 0) * 100) . '%',  // 0就是0
                        "credit" => intval($item['officer_evaluation'] * ($rate_asset[$item['asset_type']] ?: 0))  // 0就是0
                    );
                    $total_increase += $credit_item['credit'];
                    $ret['suggest']['increase'][$item['uid']] = array_merge($item, $credit_item);
                } else {
                    $tip[] = "Not Set Evaluation For Assets:" . $item['asset_name'] . "(" . $cert_list[$item['asset_type']] . ") By Yourself,Not Allowed to Choose for Mortgage";
                    $analysis_asset[]="Not Allowed to Mortgage <kbd>" . $item['asset_name'] . "(" . $cert_list[$item['asset_type']] . ")</kbd>, No Set Evaluation By Yourself";
                }
            }
        }
        $sys_max = $sys_default + $total_increase;
        $sys_max2 = $ability * 0.95 * $ret['suggest']['terms'];
        if ($sys_max > $member_request['credit']) {
            $tip[] = "We can ask client to loan more as " . intval($sys_max) . "(with mortgage)";
        }

        $ret['suggest']['collateral']=$collateral_list;
        $max_credit = $member_request['credit'] > 0 ? min($sys_max, $sys_max2, $member_request['credit']) : $default_credit;
        $ret['suggest']['max_credit'] = intval($max_credit);
        $ret['suggest']['max_credit_remark'] = "<p>require mortgage assets,take minimum between :</p> <p>1.【ability(".$ability.")*95%*request_terms(".$ret['suggest']['terms'].")=".$sys_max2."】</p>";
        $ret['suggest']['max_credit_remark'].=' <p> 2. 【request_credit('.$member_request['credit'].')】</p>';
        $ret['suggest']['max_credit_remark'].='<p> 3.【increase_with_mortgage('.$total_increase.') + default_credit('.$default_credit.')='.($total_increase+$default_credit).'】</p><p> 5% discount off ability for pay to interest.</p>';
        $ret['suggest']['increase_remark'] = 'client can get more credit while mortgaged the assets, not allow more than the max_credit';
        $ret['suggest']['tip'] = $tip;
        $ret['analysis_asset']=$analysis_asset;
        return $ret;
    }

    public static function getMemberIncomeBusinessAnalysis($member_id, $operator_id, $operator_position,$grant_id=0)
    {
        $r = new ormReader();
        $member_id = intval($member_id);
        $operator_id = intval($operator_id);
        $special_where="";
        if($grant_id>0){
            $special_where=" and grant_id=".qstr($grant_id);
        }else{
            $special_where=" and state<100";
        }

        $sql = "SELECT ci.* FROM member_industry mi INNER JOIN common_industry ci ON mi.industry_id = ci.uid WHERE mi.state = 1 AND mi.member_id = " . $member_id;
        $member_industry = $r->getRows($sql);
        if ($member_industry) {
            $sql = "select * from member_income_business WHERE uid IN (SELECT MAX(uid) FROM member_income_business ";
            $sql.=" WHERE member_id = $member_id AND operator_id ='" . $operator_id . "' ".$special_where." GROUP BY industry_id,branch_code)";
            $business_income = $r->getRows($sql);
            $business_income = resetArrayKey($business_income, 'uid');
            $industry_income = array();
            foreach ($business_income as $bitem) {
                $industry_income[$bitem['industry_id']][] = $bitem;
            }

            $sql = "select distinct industry_id,branch_code from member_income_business where member_id='".$member_id."'".$special_where;
            $branch_code_list_rows = $r->getRows($sql);
            $branch_code_list = array();
            foreach ($branch_code_list_rows as $row) {
                $branch_code_list[$row['industry_id']][] = $row['branch_code'];
            }

            foreach ($member_industry as $key => $industry) {
                $industry_uid = $industry['uid'];
                $branch_code = $branch_code_list[$industry_uid];
                $income = array(
                    'income' => 0,
                    'expense' => 0,
                    'profit' => 0
                );
                $analysis_tip=array();
                if ($industry_income[$industry_uid]) {
                    $industry_income[$industry_uid] = resetArrayKey($industry_income[$industry_uid], "branch_code");

                    foreach ($branch_code as $code) {
                        $analysis_tip[$code]=self::formatBusinessIndustryResearchText($industry,$industry_income[$industry_uid][$code]['research_text']);
                        $income['income'] += intval($industry_income[$industry_uid][$code]['income']);
                        $income['expense'] += intval($industry_income[$industry_uid][$code]['expense']);
                        $income['profit'] += intval($industry_income[$industry_uid][$code]['profit']);
                    }
                } else {
                    foreach ($branch_code as $code) {
                        $new_income = array();
                        if ($operator_position != userPositionEnum::BRANCH_MANAGER && $operator_position != userPositionEnum::CREDIT_OFFICER) {
                            $new_income = credit_officerClass::getMemberBusinessResearchOfBM($member_id, $industry_uid, $code);
                            if (!$new_income) {
                                $new_income = credit_officerClass::getMemberBusinessResearchAverageOfCO($member_id, $industry_uid, $code);
                            }
                        }
                        if ($new_income) {
                            $analysis_tip[$code]=self::formatBusinessIndustryResearchText($industry,$new_income['research_text']);
                            $income['income'] += intval($new_income['income']);
                            $income['expense'] += intval($new_income['expense']);
                            $income['profit'] += intval($new_income['profit']);
                        }
                    }
                }

                if ($income) {
                    $industry['research_text'] = $income['research_text'];
                    $industry['employees'] = $income['employees'];
                    $industry['income'] = $income['income'];
                    $industry['expense'] = $income['expense'];
                    $industry['profit'] = $income['profit'];
                    $industry['relative_id'] = $income['relative_id'];
                    $industry['relative_name'] = $income['relative_name'];
                    $industry['analysis_detail']=urlencode(my_json_encode($analysis_tip));
                }
                $member_industry[$key] = $industry;
            }
        }

        return $member_industry;
    }
    public static function formatBusinessIndustryResearchText($setting_industry,$research_text){
        if(!$research_text) return array();
        $arr_item=my_json_decode($setting_industry['industry_json_kh']);
        $arr_type=my_json_decode($setting_industry['industry_json_type']);
        $arr_research=my_json_decode($research_text);
        $ret=array();
        foreach($arr_item as $k=>$item){
            if($arr_type[$k]==surveyType::INCOME || $arr_type[$k]==surveyType::EXPENSE){
                $ret[]=array(
                    "survey_name"=>$item,
                    "survey_type"=>$arr_type[$k],
                    "result"=>$arr_research[$k]
                );
            }
        }
        return $ret;
    }


}