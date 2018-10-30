<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/10
 * Time: 17:38
 */
class member_assetsModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('member_assets');
    }

    /**
     * 获取当前用户审核的会员资产
     */
    public function getMemberAssets($operator_id, $member_id)
    {
        $sql = "select a.uid,a.asset_type,a.asset_name,e.evaluation valuation,e.remark,e.operator_name from member_assets a left join ( select * from member_assets_evaluate where evaluator_type = 1 and operator_id = '$operator_id' order by uid desc ) e on a.uid=e.member_assets_id where a.asset_state >='" . assetStateEnum::CERTIFIED . "' and a.member_id='$member_id' group by a.member_id,a.uid order by e.uid desc";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    public function getCoAssetEvaluationByUid($uid)
    {
        $sql = "SELECT * FROM member_assets_evaluate WHERE uid IN (SELECT MAX(uid) FROM member_assets_evaluate WHERE member_assets_id = $uid AND evaluator_type = 0 GROUP BY operator_id)";
        $list = $this->reader->getRows($sql);
        $list = resetArrayKey($list, 'operator_id');
        return $list;
    }

    /**
     * 获取资产关联
     */
    public function getMemberEvaluate($eva_ids)
    {
        $sql = "SELECT a.uid,a.asset_type,a.asset_name,e.evaluation valuation,e.remark,e.operator_id,e.operator_name FROM member_assets a LEFT JOIN member_assets_evaluate e on a.uid = e.member_assets_id where e.uid IN ($eva_ids) order by e.uid desc";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    /**
     * 获取CO审核资产
     */
    public function getMemberEvaluateByCO($type, $eva_ids)
    {
        $sql = "SELECT a.uid,a.asset_type,e.evaluation valuation,e.remark,e.operator_id,e.operator_name FROM member_assets a LEFT JOIN member_assets_evaluate e  on a.uid = e.member_assets_id where a.asset_type = '$type' and e.uid IN ($eva_ids) order by e.uid desc;";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    /**
     * 获取用户已抵押资产
     */
    public function getMemberMortgaged($member_id)
    {
        $sql = "select m.*,a.member_id,a.asset_name,a.asset_type from member_assets a left join member_asset_mortgage m on a.uid = m.member_asset_id where a.member_id = '$member_id' and a.mortgage_state = '1' GROUP BY member_asset_id;";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    /**
     * 获取用户已抵押资产
     */
    public function getAssets($asset_id)
    {
        $info = $this->getRow($asset_id);
        return $info;
    }

    /**
     * 获取
     * @param $member_id
     * @param $operator_id
     * @return array
     */
    public function getAssetsEvaluate($member_id, $operator_id)
    {
        $sql = "SELECT * FROM member_assets_evaluate mae INNER JOIN member_assets ma ON mae.member_assets_id = ma.uid WHERE ma.asset_state = 100 AND mae.uid IN (SELECT MAX(uid) FROM member_assets_evaluate WHERE member_id = $member_id AND operator_id = $operator_id GROUP BY member_assets_id)";
        $evaluate_list = $this->reader->getRows($sql);
        $evaluate_list = resetArrayKey($evaluate_list, 'member_assets_id');
        $evaluate_total = 0;
        foreach ($evaluate_list as $v) {
            $evaluate_total += $v['evaluation'];
        }
        return array('evaluate_total' => $evaluate_total, 'evaluate_list' => $evaluate_list);
    }

    public function updateAssetState($cert_id, $state)
    {
        $asset = $this->getRow(array('cert_id' => $cert_id));
        $asset_sn = $asset['asset_sn'];
        $asset_type = $asset['asset_type'];
        $chk_sn = $this->find(array(
                'asset_sn' => $asset_sn,
                'asset_type' => $asset_type,
                'asset_state' => array('>=', assetStateEnum::CERTIFIED)
            )
        );
        if ($chk_sn) {
            return new result(false, 'The asset sn has been approved');
        }
        $asset->asset_state = $state;
        $asset->update_time = Now();
        return $asset->update();
    }

    public function getAssetInfoBySn($asset_sn)
    {
        $sql = "SELECT a.*,m.login_code FROM member_assets a LEFT JOIN client_member m ON a.member_id = m.uid WHERE a.asset_sn = '$asset_sn'";
        $info = $this->reader->getRow($sql);

        if ($info) {
            $m_member_assets_evaluate = M('member_assets_evaluate');
            $evaluate = $m_member_assets_evaluate->orderBy('uid DESC')->find(array('member_assets_id' => $info['uid']));
            $info['valuation'] = $evaluate['evaluation'];
        }

        return $info;
    }


}