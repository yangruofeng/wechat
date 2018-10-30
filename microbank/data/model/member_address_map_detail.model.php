<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/19
 * Time: 16:58
 */
class member_address_map_detailModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_address_map_detail');
    }


    public function getResidenceAddressMapInfo($member_id)
    {
        return $this->orderBy('uid desc')->find(array(
            'member_id' => $member_id,
            'address_type' => addressCategoryEnum::MEMBER_RESIDENCE_PLACE
        ));
    }
}