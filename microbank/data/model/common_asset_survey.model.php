<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class common_asset_surveyModel extends tableModelBase
{

    public function  __construct()
    {
        parent::__construct('common_asset_survey');
    }

    public function editAssetSurvey($p)
    {
        $asset_type = trim($p['asset_type']);
        $survey_name_arr = $p['survey_name'];
        $survey_code_arr = $p['survey_code'];
        $survey_name_kh_arr = $p['survey_name_kh'];
        $survey_type = $p['survey_type'];

        $survey_json_arr = array();
        $survey_json_kh_arr = array();
        $survey_json_type = array();
        foreach ($survey_code_arr as $key => $val) {
            $survey_json_arr[$val] = $survey_name_arr[$key];
            $survey_json_kh_arr[$val] = $survey_name_kh_arr[$key];
            $survey_json_type[$val] = $survey_type[$key];
        }

        $row = $this->getRow(array('asset_type' => $asset_type));
        if (!$row) {
            $is_insert = true;
            $row = $this->newRow();
            $row->asset_type = $asset_type;
            $row->creator_id = $p['creator_id'];
            $row->creator_name = $p['creator_name'];
            $row->create_time = Now();
        } else {
            $row->update_id = $p['creator_id'];
            $row->update_name = $p['creator_name'];
            $row->update_time = Now();
        }

        $row->survey_json = my_json_encode($survey_json_arr);
        $row->survey_json_kh = my_json_encode($survey_json_kh_arr);
        $row->survey_json_type = my_json_encode($survey_json_type);
        if ($is_insert) {
            $rt = $row->insert();
        } else {
            $rt = $row->update();
        }
        return $rt;
    }

    public function deleteAssetSurvey($asset_type)
    {
        $row = $this->getRow(array('asset_type' => $asset_type));
        if (!$row) {
            return new result('Invalid Id.');
        }
        $rt = $row->delete();
        if ($rt->STS) {
            return new result(true, 'Delete Successful.');
        } else {
            return new result(true, 'Delete Failed.');
        }
    }
}
