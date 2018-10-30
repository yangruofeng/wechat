<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/18
 * Time: 16:59
 */
class member_guaranteeModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('member_guarantee');
    }

    public function getMemberGuaranteeList($member_id)
    {

        $sql = "select g.*,m.display_name,m.kh_display_name,m.member_icon,m.member_image,m.phone_id,d.item_name_json relation_type_name_json from member_guarantee g left join client_member m on m.uid=g.relation_member_id
        left join core_definition d on d.item_code=g.relation_type and d.category='".userDefineEnum::GUARANTEE_RELATIONSHIP."' where g.member_id='$member_id' and g.relation_state='".memberGuaranteeStateEnum::ACCEPT."'  ";
        $list1 = $this->reader->getRows($sql);
        return $list1;
    }

    public function getAsGuaranteeMemberList($member_id)
    {
        // 作为担保人的（申请+通过的）
        $sql = "select g.*,m.display_name,m.kh_display_name,m.member_icon,m.member_image,m.phone_id,d.item_name_json relation_type_name_json from member_guarantee g left join client_member m on m.uid=g.member_id
        left join core_definition d on d.item_code=g.relation_type and d.category='".userDefineEnum::GUARANTEE_RELATIONSHIP."' where g.relation_member_id='$member_id' and g.relation_state in('".memberGuaranteeStateEnum::CREATE."','".memberGuaranteeStateEnum::ACCEPT."') ";
        $list2 = $this->reader->getRows($sql);
        return $list2;
    }
}