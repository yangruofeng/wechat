<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/2
 * Time: 21:45
 */
class biz_scene_imageModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('biz_scene_image');
    }

    public function insertSceneImage($member_id,$member_image,$biz_code=null,$scene_code=null)
    {
        if( !$member_id || !$member_image ){
            return new result(false,'Invalid params.',null,errorCodesEnum::INVALID_PARAM);
        }
        // 先查询是否已经存在了,不区分业务，只检查图片本身
        $image = $this->getRow(array(
            'member_id' => $member_id,
            'member_image' => $member_image
        ));
        if( $image ){
            return new result(true,'success');
        }
        $image = $this->newRow();
        $image->scene_code = $scene_code;
        $image->biz_code = $biz_code;
        $image->member_id = $member_id;
        $image->member_image = $member_image;
        $image->create_time = Now();
        $insert = $image->insert();
        return $insert;

    }

    public function getMemberNewestSceneImage($member_id)
    {
        $row = $this->orderBy('uid desc')->find(array(
            'member_id' => $member_id
        ));
        return array("image"=>$row['member_image'],"time"=>$row['create_time']);
    }
}