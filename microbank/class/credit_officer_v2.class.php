<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/10
 * Time: 17:19
 */
class credit_officer_v2Class extends credit_officerClass
{

    public static function addExtendImageForAsset($params)
    {
        $asset_id = intval($params['asset_id']);
        $image_list = $params['image_list'];

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
            $user_name = 'System';
        }

        $photos = array();
        foreach( $image_list as $item ){
            if( !$item['image_url'] ){
                continue;
            }
            $photos[] = array(
                'image_key' => $item['image_key']?:'image_item_'.substr(time(),-6),
                'image_url' => $item['image_url'],
                'image_source' => intval($item['image_source'])
            );
        }


        if (!empty($photos)) {
            $cert_id = $asset_info->cert_id;
            $sql = "insert into member_verify_cert_image(cert_id,image_key,image_url,image_sha,image_source,creator_id,creator_name) values ";
            $sql_arr = array();
            foreach ($photos as $key => $v) {
                $fields = array(
                    qstr($cert_id),
                    qstr($v['image_key']),
                    qstr($v['image_url']),
                    "NULL",   // sha
                    qstr($v['image_source']),
                    qstr($user_id),
                    qstr($user_name)
                );
                $sql_arr[] = "(" . join(",", $fields) . ")";
            }
            $sql .= implode(',', $sql_arr);
            $insert = $m_asset->conn->execute($sql);
            if (!$insert->STS) {
                return new result(false, 'Add image fail.', null, errorCodesEnum::DB_ERROR);
            }
        }
        return new result(true);
    }


}