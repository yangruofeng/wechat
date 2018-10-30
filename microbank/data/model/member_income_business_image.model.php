<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/8
 * Time: 16:10
 */
class member_income_business_imageModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_income_business_image');
    }

    public function getImagesGroupIndustryByMemberId($member_id)
    {
        $business_images = $this->select(array('member_id' => $member_id));
        $business_images_new = array();
        foreach ($business_images as $val) {
            $business_images_new[$val['industry_id']][] = $val;
        }
        return $business_images_new;
    }
}