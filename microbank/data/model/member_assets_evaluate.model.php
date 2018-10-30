<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/28
 * Time: 17:55
 */
class member_assets_evaluateModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_assets_evaluate');
    }


    /**
     * 获取CO用户审核的会员资产
     */
    public function getMemberAssetsEvaluate($member_id)
    {
        $sql = "select MAX(uid) as uid from member_assets_evaluate where evaluator_type = 0 and member_id = '$member_id' GROUP BY member_assets_id,operator_id ORDER BY evaluate_time desc";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    /**
     * 获取当前用户审核记录
     */
    public function getMemberAssetsEvaluateByAssetsId($asset_id)
    {
        $sql = "select mae.*,ma.asset_name from member_assets_evaluate mae LEFT JOIN member_assets ma ON mae.member_assets_id = ma.uid where mae.member_assets_id = '$asset_id' and mae.evaluator_type=1 order by mae.evaluate_time desc";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    /**
     * 获取CO用户审核的会员资产
     */
    public function getMemberAssetsEvaluateLast($member_id, $asset_id, $branch_id)
    {
        $sql = "SELECT * FROM member_assets_evaluate WHERE member_id = '$member_id' and member_assets_id = '$asset_id' and evaluator_type = 1 and branch_id = '$branch_id' ORDER BY uid desc LIMIT 1 ";
        $row = $this->reader->getRow($sql);
        return $row;
    }

}