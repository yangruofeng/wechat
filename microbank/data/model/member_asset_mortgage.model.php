<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/9
 * Time: 15:21
 */
class member_asset_mortgageModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_asset_mortgage');
    }

    /**
     * 授信资产
     */
    public function getAssetMortgages($m_asset_ids, $type = 0)
    {
        $sql = "select m.*,i.asset_mortgage_id,i.image_path from member_asset_mortgage m left join member_asset_mortgage_image i on m.uid = i.asset_mortgage_id where m.uid in ($m_asset_ids)";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    /**
     * 授信资产
     */
    public function getAssetMortgagesAndContract()
    {
        $sql = "select max(m.uid) max,m.*,a.asset_type from member_asset_mortgage m left join member_authorized_contract c on m.grant_id = c.grant_credit_id left join member_assets a on m.member_asset_id = a.uid GROUP BY m.member_asset_id,m.contract_no";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    /**
     * branch抵押数量
     * @param $branch_id
     * @return array
     */
    public function getAssetMortgagedNumberByBranch($branch_id)
    {
        $sql = "SELECT ma.asset_type,count(mam.uid) amount FROM member_asset_mortgage mam"
            . " INNER JOIN member_assets ma ON mam.member_asset_id = ma.uid"
            . " INNER JOIN client_member cm ON ma.member_id=cm.uid"
            . " WHERE cm.branch_id = " . $branch_id . " AND mam.mortgage_type = 1 GROUP BY ma.asset_type";
        $mortgage_list = $this->reader->getRows($sql);
        $mortgage = resetArrayKey($mortgage_list, 'asset_type');

        $asset_num = array(
            certificationTypeEnum::LAND => intval($mortgage[certificationTypeEnum::LAND]['amount']),
            certificationTypeEnum::HOUSE => intval($mortgage[certificationTypeEnum::HOUSE]['amount']),
            certificationTypeEnum::CAR => intval($mortgage[certificationTypeEnum::CAR]['amount']),
            certificationTypeEnum::MOTORBIKE => intval($mortgage[certificationTypeEnum::MOTORBIKE]['amount']),
        );
        return $asset_num;
    }

    public function getAssetMortgage($uid)
    {
        $uid = intval($uid);
        $r = new ormReader();
        $sql = "SELECT mam.*,sb.branch_name FROM member_asset_mortgage mam INNER JOIN site_branch sb ON mam.branch_id = sb.uid WHERE mam.uid = " . $uid;
        $asset_mortgage = $r->getRow($sql);
        if (!$asset_mortgage) {
            showMessage('Invalid Id.');
        }

        $member_asset_id = $asset_mortgage['member_asset_id'];
        $sql = "SELECT ma.*,cm.display_name FROM member_assets ma
                LEFT JOIN client_member cm ON ma.member_id = cm.uid
                WHERE ma.uid = " . $member_asset_id;
        $asset_info = $r->getRow($sql);

        $m_member_assets_owner = M('member_assets_owner');
        $asset_owner = $m_member_assets_owner->select(array('member_asset_id' => $member_asset_id));
        $asset_info['relative_list'] = array_column($asset_owner, 'relative_name');

        $contract_no = $asset_mortgage['contract_no'];
        $sql = "SELECT mcg.*,mcc.alias FROM member_authorized_contract mac
               LEFT JOIN member_credit_grant mcg ON mac.grant_credit_id = mcg.uid
               LEFT JOIN member_credit_category mcc ON mcc.uid = mcg.default_credit_category_id
               WHERE mac.contract_no = " . qstr($contract_no);
        $info = $r->getRow($sql);

        $sql = "SELECT evaluation FROM member_assets_evaluate WHERE member_assets_id = $member_asset_id AND evaluator_type = 1 ORDER BY uid DESC";
        $evaluation = $r->getOne($sql);

        $asset_info['keep_time'] = $asset_mortgage['keep_time'] ?: $asset_mortgage['operator_time'];
        $asset_info['contract_no'] = $asset_mortgage['contract_no'];
        $asset_info['branch_name'] = $asset_mortgage['branch_name'];
        $asset_info['keeper_name'] = $asset_mortgage['keeper_name'];
        $asset_info['product_alias'] = $info['alias'];
        $asset_info['evaluation'] = $evaluation;
        return $asset_info;
    }
}