<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/2
 * Time: 15:46
 */
class member_credit_grant_assetsModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('member_credit_grant_assets');
    }

    /**
     * 授信资产
     */
    public function getCreditGrantAssets($uid, $type = '')
    {
        $where = '';
        if ($type === '1' || $type === '0') {
            $where = ' and a.mortgage_state = ' . $type;
        }
        $uid = $uid ?: 0;
        $sql = "select c.*,a.mortgage_state,a.asset_name,a.asset_sn,a.asset_cert_type,a.relative_id,a.relative_name,a.valuation,a.asset_type,m.mortgage_file_type "
            . "from member_credit_grant_assets c left join member_assets a on c.member_asset_id = a.uid "
            . "left join member_asset_mortgage m on m.member_asset_id = a.uid  where c.grant_id = " . $uid . $where;
        $list = $this->reader->getRows($sql);
        return $list;
    }

    /**
     * 获取授信资产的图片
     */
    public function getCreditGrantAssetsImage($grant_id)
    {
        $sql = "SELECT c.`member_asset_id` asset_id,b.`image_url` FROM member_credit_grant_assets c "
            . " INNER JOIN  member_assets a ON c.`member_asset_id` = a.`uid`"
            . " INNER JOIN member_verify_cert_image b ON a.cert_id = b.cert_id"
            . " WHERE c.`grant_id`='" . $grant_id . "'";
        $list = $this->reader->getRows($sql);
        $ret = array();
        foreach ($list as $item) {
            if (!$ret[$item['asset_id']]) {
                $ret[$item['asset_id']] = array();
            }
            $ret[$item['asset_id']][] = $item['image_url'];
        }
        return $ret;
    }
}