<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class common_industryModel extends tableModelBase
{

    public function  __construct()
    {
        parent::__construct('common_industry');
    }

    /**
     * 添加industry
     * @param $p
     * @return ormResult|result
     */
    public function addIndustry($p)
    {
        $industry_name = trim($p['industry_name']);
        $industry_code = trim($p['industry_code']);
        $industry_category = trim($p['industry_category']);
        $credit_rate = intval($p['credit_rate']) ?: 50;
        $state = intval($p['state']);
        $creator_id = intval($p['creator_id']);
        $creator_name = trim($p['creator_name']);
        $survey_name_arr = $p['survey_name'];
        $survey_code_arr = $p['survey_code'];
        $survey_name_kh_arr = $p['survey_name_kh'];
        $survey_type = $p['survey_type'];

        $lang_type = getLangTypeList();
        $industry_name_json = array();
        foreach ($lang_type as $lang_key) {
            $industry_name_json[$lang_key] = trim($p['industry_name_json_' . $lang_key]);
        }

        if (!$industry_name || !$industry_code) {
            return new result(false, 'Param Error!');
        }

        $chk_code = $this->find(array('industry_code' => $industry_code));
        if ($chk_code) {
            return new result(false, 'Code Exists!');
        }

        $industry_json_arr = array();
        $industry_json_kh_arr = array();
        $industry_json_type = array();
        foreach ($survey_code_arr as $key => $val) {
            $industry_json_arr[$val] = $survey_name_arr[$key];
            $industry_json_kh_arr[$val] = $survey_name_kh_arr[$key];
            $industry_json_type[$val] = $survey_type[$key];
        }

        $row = $this->newRow();
        $row->industry_name = $industry_name;
        $row->industry_code = $industry_code;
        $row->industry_category = $industry_category;
        $row->industry_name_json = my_json_encode($industry_name_json);

        // 分组排序
        $sort_industry_json = industryClass::sortResearchArrayByTypeArray($industry_json_arr, $industry_json_type);
        $sort_industry_json_kh = industryClass::sortResearchArrayByTypeArray($industry_json_kh_arr, $industry_json_type);

        $row->industry_json = my_json_encode($sort_industry_json);
        $row->industry_json_kh = my_json_encode($sort_industry_json_kh);
        $row->industry_json_type = my_json_encode($industry_json_type);
        $row->credit_rate = $credit_rate;
        $row->state = $state;
        $row->creator_id = $creator_id;
        $row->creator_name = $creator_name;
        $row->create_time = Now();
        $row->update_time = Now();
        $rt = $row->insert();
        return $rt;
    }

    public function editIndustry($p)
    {
        $uid = intval($p['uid']);
        $industry_code = trim($p['industry_code']);
        $industry_name = trim($p['industry_name']);
        $industry_category = trim($p['industry_category']);
        $state = intval($p['state']);
        $survey_name_arr = $p['survey_name'];
        $survey_code_arr = $p['survey_code'];
        $survey_name_kh_arr = $p['survey_name_kh'];
        $survey_type = $p['survey_type'];
        $credit_rate = intval($p['credit_rate']) ?: 50;
        if (!$industry_name) {
            return new result(false, 'Param Error!');
        }

        $chk_code = $this->find(array('industry_code' => $industry_code, 'uid' => array('neq', $uid)));
        if ($chk_code) {
            return new result(false, 'Code Exists!');
        }

        $lang_type = getLangTypeList();
        $industry_name_json = array();
        foreach ($lang_type as $lang_key) {
            $industry_name_json[$lang_key] = trim($p['industry_name_json_' . $lang_key]);
        }

        $industry_json_arr = array();
        $industry_json_kh_arr = array();
        $industry_json_type = array();
        foreach ($survey_code_arr as $key => $val) {
            $industry_json_arr[$val] = $survey_name_arr[$key];
            $industry_json_kh_arr[$val] = $survey_name_kh_arr[$key];
            $industry_json_type[$val] = $survey_type[$key];
        }

        $row = $this->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }
        $row->industry_code = $industry_code;
        $row->industry_name = $industry_name;
        $row->industry_category = $industry_category;
        $row->industry_name_json = my_json_encode($industry_name_json);

        // 分组排序
        $sort_industry_json = industryClass::sortResearchArrayByTypeArray($industry_json_arr, $industry_json_type);
        $sort_industry_json_kh = industryClass::sortResearchArrayByTypeArray($industry_json_kh_arr, $industry_json_type);

        $row->industry_json = my_json_encode($sort_industry_json);
        $row->industry_json_kh = my_json_encode($sort_industry_json_kh);
        $row->industry_json_type = my_json_encode($industry_json_type);
        $row->credit_rate = $credit_rate;
        $row->state = $state;
        $row->update = Now();
        $rt = $row->update();
        return $rt;
    }

    /**
     * 获取Industry Info
     * @param $industry_id
     * @return bool|mixed
     */
    public function getIndustryInfo($industry_id)
    {
        $industry_id = intval($industry_id);
        $industry_info = $this->find(array('uid' => $industry_id));
        if ($industry_info) {
            $industry_info['industry_name_arr'] = my_json_decode($industry_info['industry_name_json']);
            $industry_json = my_json_decode($industry_info['industry_json']);
            $industry_json_type = my_json_decode($industry_info['industry_json_type']);
            asort($industry_json_type);
            $industry_text_all = array();
            $industry_text = array();
            $industry_income_text = array();
            $industry_expense_text = array();
            foreach ($industry_json_type as $key => $val) {
                if ($val == 'income') {
                    $industry_income_text[$key] = $industry_json[$key];
                } elseif ($val == 'expense') {
                    $industry_expense_text[$key] = $industry_json[$key];
                } else {
                    $industry_text[$key]['type'] = $val;
                    $industry_text[$key]['name'] = $industry_json[$key];
                }
                $industry_text_all[$key]['type'] = $val;
                $industry_text_all[$key]['name'] = $industry_json[$key];
            }
            $industry_info['industry_text_all'] = $industry_text_all;
            $industry_info['industry_text'] = $industry_text;
            $industry_info['industry_income_text'] = $industry_income_text;
            $industry_info['industry_expense_text'] = $industry_expense_text;
        }
        return $industry_info;
    }
}
