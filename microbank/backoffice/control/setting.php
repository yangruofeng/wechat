<?php

class settingControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Company Info");
        Tpl::setDir("setting");

        Language::read('setting,certification');
        $verify_field = enum_langClass::getCertificationTypeEnumLang();
        Tpl::output("cert_verify_lang", $verify_field);
    }

    /**
     * 查看公司信息
     */
    public function companyInfoOp()
    {
        $m_core_dictionary = M('core_dictionary');
        $data = $m_core_dictionary->getDictionary('company_config');
        if ($data) {
            tpl::output('company_config', my_json_decode($data['dict_value']));
        }
        Tpl::showPage("company.info");
    }

    /**
     * 修改公司信息
     */
    public function editCompanyInfoOp()
    {
        $m_core_dictionary = M('core_dictionary');
        if ($_POST['form_submit'] == 'ok') {
            $param = $_POST;
            unset($param['form_submit']);
            if (empty($param['hotline'])) {
                $param['hotline'] = array();
            } else {
                $param['hotline'] = array_unique($param['hotline']);
            }

            $rt = $m_core_dictionary->updateDictionary('company_config', my_json_encode($param));
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('setting', 'companyInfo', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage($rt->MSG);
            }
        } else {
            $data = $m_core_dictionary->getDictionary('company_config');
            if ($data) {
                $company_config = my_json_decode($data['dict_value']);
                tpl::output('company_config', $company_config);
            }
            $address_id = $company_config['address_id'];
            $m_core_tree = M('core_tree');
            $region_list = $m_core_tree->getParentAndBrotherById($address_id, 'region');
            Tpl::output('region_list', $region_list);
            Tpl::showPage("company.edit");
        }
    }

    /**
     * 编码规则
     */
    public function codingRuleOp()
    {
        var_dump('Todo soon');
//        Tpl::showPage("coding_rule");
    }

    /**
     * 系统枚举定义
     * 弃用
     */
    public function systemDefineOp()
    {
        $m_core_definition = M('core_definition');
        $rt = $m_core_definition->initSystemDefine();
        if (!$rt->STS) {
            showMessage('Init Failure!');
        } else {
            Tpl::showpage('system.define');
        }
    }

    /**
     * 获取define列表
     * @param $p
     * @return mixed
     */
    public function getDefineListOp($p)
    {
        $m_core_definition = M('core_definition');
        $define_list = $m_core_definition->getDefineList($p);
        return $define_list;
    }

    /**
     * 修改define 分类名称
     * @param $p
     */
    public function editCategoryNameOp($p)
    {
        $m_core_definition = M('core_definition');
        $rt = $m_core_definition->editCategoryName($p);
        return $rt;
    }

    /**
     * 编辑define item
     * @param $p
     * @return mixed
     */
    public function editDefineItemOp($p)
    {
        $m_core_definition = M('core_definition');
        $rt = $m_core_definition->editDefineItem($p);
        return $rt;
    }

    /**
     * user.define
     */
    public function shortCodeOp()
    {
        $m_core_definition = M('core_definition');
        $rt = $m_core_definition->initUserDefine();
        if (!$rt->STS) {
            showMessage('Init Failure!');
        } else {
            $lang_list = C('lang_type_list');
            Tpl::output('lang_list', $lang_list);
            Tpl::showpage('user.define');
        }
    }

    /**
     * 添加define item
     * @param $p
     * @return mixed
     */
    public function addDefineItemOp($p)
    {
        $m_core_definition = M('core_definition');
        $rt = $m_core_definition->addDefineItem($p);
        return $rt;
    }

    /**
     * 移除define item
     * @param $p
     * @return mixed
     */
    public function removeDefineItemOp($p)
    {
        $m_core_definition = M('core_definition');
        $rt = $m_core_definition->removeDefineItem($p);
        return $rt;
    }


    public function creditLevelOp()
    {
        $return = credit_loanClass::getCreditLevelList();
        $type_lang = array(
            creditLevelTypeEnum::MEMBER => 'Member',
            creditLevelTypeEnum::MERCHANT => 'Merchant'
        );
        Tpl::output('credit_level', $return);
        Tpl::output('level_type_lang', $type_lang);
        Tpl::showPage('credit.level.list');
    }

    public function addCreditLevelOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        if ($params['form_submit'] == 'ok') {
            $min_amount = $params['min_amount'];
            $max_amount = $params['max_amount'];

            $cert_list = $params['cert_list'];
            if ($min_amount < 0 || $max_amount <= 0) {
                showMessage('Amount Invalid');
            }
            if ($min_amount >= $max_amount) {
                showMessage('Min amount more than max amount');
            }

            if (empty($cert_list)) {
                showMessage('Did not select certification');
            }

            $conn = ormYo::Conn();

            try {
                $conn->startTransaction();

                $re = credit_loanClass::addCreditLevel($params);
                if (!$re->STS) {
                    $conn->rollback();
                    showMessage('Add fail');
                }

                $conn->submitTransaction();
                showMessage('Add success', getUrl('setting', 'creditLevel', array(), false, BACK_OFFICE_SITE_URL));

            } catch (Exception $e) {
                showMessage('Add fail');
            }

        }
        Tpl::showPage('credit.level.add');
    }

    public function editCreditLevelOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        if ($params['form_submit'] == 'ok') {

            $min_amount = $params['min_amount'];
            $max_amount = $params['max_amount'];

            $cert_list = $params['cert_list'];
            if ($min_amount < 0 || $max_amount <= 0) {
                showMessage('Amount Invalid');
            }
            if ($min_amount >= $max_amount) {
                showMessage('Min amount more than max amount');
            }

            if (empty($cert_list)) {
                showMessage('Did not select certification');
            }

            $conn = ormYo::Conn();
            try {
                $conn->startTransaction();
                $re = credit_loanClass::editCreditLevel($params);
                if (!$re->STS) {
                    $conn->rollback();
                    showMessage('Edit fail');
                }
                $conn->submitTransaction();
                showMessage('Edit success');

            } catch (Exception $e) {
                showMessage('Edit fail');
            }

        } else {
            $uid = $params['uid'];
            if (!$uid) {
                showMessage('Invalid param');
            }
            $m_level = new loan_credit_cert_levelModel();
            $row = $m_level->getRow($uid);
            if (!$row) {
                showMessage('No data!');
            }
            $sql = "select cert_type from loan_credit_level_cert_list where cert_level_id='$uid' ";
            $cert_list = array();
            $list = $m_level->reader->getRows($sql);
            foreach ($list as $v) {
                $cert_list[] = $v['cert_type'];
            }

            $level_info = $row->toArray();
            $level_info['cert_list'] = $cert_list;
            Tpl::output('level_info', $level_info);
            Tpl::showPage('credit.level.edit');
        }
    }

    public function deleteCreditLevelOp($p)
    {
        $id = $p['id'];
        return credit_loanClass::deleteCreditLevel($id);
    }

    /**
     * 获取地址选项
     * @param $p
     * @return array
     */
    public function getAreaListOp($p)
    {
        $pid = intval($p['uid']);
        $m_core_tree = M('core_tree');
        $list = $m_core_tree->getChildByPid($pid, 'region');
        return array('list' => $list);
    }

    /**
     * 行业
     */
    public function industryOp()
    {
        Tpl::showPage('industry');
    }

    /**
     * 获取行业列表
     * @param $p
     * @return array
     */
    public function getIndustryListOp($p)
    {
        $r = new ormReader();
        $sql = "SELECT * FROM common_industry WHERE 1 = 1";
        $search_text = trim($p['search_text']);
        if ($search_text) {
            $sql .= " AND industry_name LIKE '%" . $search_text . "%'";
        }
        $sql .= " ORDER by uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 添加Industry
     */
    public function addIndustryOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $m_common_industry = new common_industryModel();
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $rt = $m_common_industry->addIndustry($p);
            if ($rt->STS) {
                showMessage('Add Successful!', getUrl('setting', 'industry', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('setting', 'addIndustry', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $m_core_definition = M('core_definition');
            $define_arr = $m_core_definition->getDefineByCategory(array('industry_category'));
            Tpl::output("industry_category", $define_arr['industry_category']);

            $survey_type = (new surveyType())->Dictionary();
            Tpl::output("survey_type", $survey_type);
            Tpl::showPage('industry.add');
        }
    }

    /**
     * 编辑Industry
     */
    public function editIndustryOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_common_industry = new common_industryModel();
        if ($p['form_submit'] == 'ok') {
            $rt = $m_common_industry->editIndustry($p);
            if ($rt->STS) {
                showMessage('Edit Successful!', getUrl('setting', 'industry', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('setting', 'editIndustry', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $uid = intval($p['uid']);
            $industry_info = $m_common_industry->find(array('uid' => $uid));
            Tpl::output('industry_info', $industry_info);

            $m_core_definition = M('core_definition');
            $define_arr = $m_core_definition->getDefineByCategory(array('industry_category'));
            Tpl::output("industry_category", $define_arr['industry_category']);

            $survey_type = (new surveyType())->Dictionary();
            Tpl::output("survey_type", $survey_type);

            Tpl::showPage('industry.edit');
        }
    }

    /**
     * 删除industry
     * @throws Exception
     */
    public function deleteIndustryOp()
    {
        $uid = intval($_GET['uid']);
        $m_common_industry = new common_industryModel();
        $row = $m_common_industry->getRow(array('uid' => $uid));
        if (!$row) {
            showMessage('Invalid Id!');
        }

        $m_member_industry = M('member_industry');
        $chk_industry = $m_member_industry->find(array('industry_id' => $uid));
        if ($chk_industry) {
            showMessage('Cannot delete, has been used!');
        }

        $rt = $row->delete();
        if ($rt->STS) {
            showMessage('Delete Successful!');
        } else {
            showMessage('Delete Failure!');
        }
    }

    /**
     * industry place
     */
    public function industryPlaceOp()
    {
        Tpl::showPage('industry_place');
    }

    /**
     * get industry place list
     */
    public function getIndustryPlaceListOp($p)
    {
        $search_text = trim($p['search_text']);
        $m_common_industry_place = M('common_industry_place');
        $pageNumber = intval($pageNumber) ?: 1;
        $pageSize = intval($pageSize) ?: 20;
        $list = $m_common_industry_place->getIndustryPlaceList($search_text, $pageNumber, $pageSize);
        return $list;
    }

    /**
     * 添加industry place
     */
    public function addIndustryPlaceOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $m_common_industry_place = M('common_industry_place');
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $rt = $m_common_industry_place->addIndustryPlace($p);
            if ($rt->STS) {
                showMessage('Add Successful!', getUrl('setting', 'industryPlace', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('setting', 'addIndustryPlace', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            Tpl::showPage('industry_place.add');
        }
    }

    /**
     * 修改industry place
     */
    public function editIndustryPlaceOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_common_industry_place = M('common_industry_place');
        if ($p['form_submit'] == 'ok') {
            $rt = $m_common_industry_place->editIndustryPlace($p);
            if ($rt->STS) {
                showMessage('Edit Successful!', getUrl('setting', 'industryPlace', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('setting', 'editIndustryPlace', array(), false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $info = $m_common_industry_place->getIndustryPlaceById(intval($p['uid']));
            Tpl::output('info', $info);
            Tpl::showPage('industry_place.edit');
        }
    }

    /**
     * 删除industry place
     */
    public function deleteIndustryPlaceOp()
    {
        $m_common_industry_place = M('common_industry_place');
        $row = $m_common_industry_place->deletendustryPlace(intval($_GET['uid']));
        if ($row->STS) {
            showMessage('Delete Successful!');
        } else {
            showMessage('Delete Failure!');
        }
    }

    /**
     *
     */
    public function assetSurveyOp()
    {
        $assetClass = new member_assetsClass();

        $asset_type = $assetClass->asset_type;
        Tpl::output('asset_type', $asset_type);

        $m_common_asset_survey = M('common_asset_survey');
        $asset_survey = $m_common_asset_survey->select(array('uid' => array('neq', 0)));
        $asset_survey = resetArrayKey($asset_survey, 'asset_type');
        Tpl::output('asset_survey', $asset_survey);
        Tpl::showPage('asset.survey');
    }

    public function editAssetSurveyOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_common_asset_survey = M('common_asset_survey');
        if ($p['form_submit'] == 'ok') {
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $rt = $m_common_asset_survey->editAssetSurvey($p);
            if ($rt->STS) {
                showMessage('Edit Successful!', getUrl('setting', 'assetSurvey', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('setting', 'editAssetSurvey', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $asset_type = trim($p['asset_type']);
            $asset_survey = $m_common_asset_survey->find(array('asset_type' => $asset_type));
            Tpl::output('asset_type', $asset_type);
            Tpl::output('asset_survey', $asset_survey);

            $survey_type = (new assetSurveyType())->Dictionary();
            Tpl::output("survey_type", $survey_type);
            Tpl::showPage('asset.survey.edit');
        }
    }

    public function deleteAssetSurveyOp($p)
    {
        $asset_type = trim($p['asset_type']);
        $m_common_asset_survey = M('common_asset_survey');
        $rt = $m_common_asset_survey->deleteAssetSurvey($asset_type);
        return $rt;
    }
}
