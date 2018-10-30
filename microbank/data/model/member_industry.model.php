<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/8/6
 * Time: 14:03
 */
class member_industryModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_industry');
    }


    public function getMemberActiveIndustryList($member_id)
    {
        $member_id = intval($member_id);
        $sql = "select i.*,a.member_id,a.industry_id from member_industry a inner join common_industry i on i.uid=a.industry_id 
        where a.member_id='$member_id' and a.state=".qstr(memberIndustryStateEnum::ACTIVE)." order by i.industry_name asc ";
        return $this->reader->getRows($sql);
    }
}