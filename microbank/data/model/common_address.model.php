<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/16
 * Time: 18:30
 */
class common_addressModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('common_address');
    }


    public function getMemberResidencePlaceByGuid($guid)
    {
        $address = $this->orderBy('uid desc')->find(array(
            'obj_type' => objGuidTypeEnum::CLIENT_MEMBER,
            'obj_guid' => $guid,
            'address_category' => addressCategoryEnum::MEMBER_RESIDENCE_PLACE,
            'state' => addressStateEnum::ACTIVE
        ));
        return $address;
    }

    /**
     * 保存会员居住地
     * @param $p
     * @return ormResult|result
     * @throws Exception
     */
    public function insertMemberResidence($p)
    {
        $obj_guid = intval($p['obj_guid']);
        $address_category = addressCategoryEnum::MEMBER_RESIDENCE_PLACE;
        $obj_type = objGuidTypeEnum::CLIENT_MEMBER;
        $id1 = intval($p['id1']);
        $id2 = intval($p['id2']);
        $id3 = intval($p['id3']);
        $id4 = intval($p['id4']);
        $address_detail  = trim($p['address_detail']);
        $full_text = trim($p['full_text']);

        $address = $this->getRow(array(
            'obj_type' => objGuidTypeEnum::CLIENT_MEMBER,
            'obj_guid' => $obj_guid,
            'address_category' => addressCategoryEnum::MEMBER_RESIDENCE_PLACE,
            'state' => addressStateEnum::ACTIVE
        ));

        $conn = ormYo::Conn();
        $conn->startTransaction();
        if ($address) {
            $address->state = addressStateEnum::INACTIVE;
            $rt_1 = $address->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return $rt_1;
            }
        }

        $row = $this->newRow();
        $row->obj_type = $obj_type;
        $row->obj_guid = $obj_guid;
        $row->address_category = $address_category;
        $row->id1 = $id1;
        $row->id2 = $id2;
        $row->id3 = $id3;
        $row->id4 = $id4;
        $row->address_detail = $address_detail;
        $row->full_text = $full_text;
        $row->street = $p['street'];
        $row->house_number = $p['house_number'];
        $row->address_group = $p['address_group'];
        $row->create_time = Now();
        $row->state = addressStateEnum::ACTIVE;
        $rt_2 = $row->insert();
        if (!$rt_2->STS) {
            $conn->rollback();
            return $rt_2;
        }
        $conn->submitTransaction();
        return new result(true, 'Setting Successful!');
    }

}