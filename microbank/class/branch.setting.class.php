<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 8/23/2018
 * Time: 9:59 AM
 */
class branchSettingClass
{
    public static function  getCounterBizSetting($branch_id)
    {
        $m_site_branch = new site_branchModel();
        $branch_info = $m_site_branch->getBranchInfoById($branch_id);
        if(!$branch_info){
            throw new Exception('Invalid branch id');

        }
        $profile = my_json_decode($branch_info['profile']);
        $counter_biz = $profile['limit_chief_teller_approve'];
        $is_branch_setting = '';
        foreach ($counter_biz as $value) {
            if ($value['is_require_ct_approve'] > 0) {
                $is_branch_setting = 1;
                break;
            }
        }
        if (!$is_branch_setting) {
            $counter_biz = (new common_counter_biz_settingModel())->getCounterBizSetting();
        }

        return $counter_biz;

    }





}