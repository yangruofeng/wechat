<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/7
 * Time: 16:17
 */
class member_income_businessModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_income_business');
    }


    public function getMemberIndustryResearchByOfficer($member_id,$industry_id,$user_id,$include_all=true)
    {
        $where = '';
        if( !$include_all ){
            $where .= ' and bs.state<100 ';
        }
        // 一个分支只取一条
        $sql = "select bs.branch_code,us.uid research_id,us.profit,us.member_id,us.industry_id from member_income_business bs left join (select * from member_income_business where member_id='$member_id' 
        and industry_id='$industry_id' and operator_id='$user_id' order by uid desc ) us on  bs.branch_code=us.branch_code 
        where bs.member_id='$member_id' and bs.industry_id='$industry_id'  $where  group by bs.branch_code ";
        $list = $this->reader->getRows($sql);
        return $list;
    }




    public function getMemberIndustryResearchList($member_id,$industry_id)
    {
        $sql = "select * from member_income_business where member_id=".qstr($member_id)." and industry_id=".qstr($industry_id).
        " order by uid desc  ";
        $list = $this->reader->getRows($sql);
        // 获取关系人列表
        if( !empty($list) ){
            $ids = array(0);
            foreach( $list as $v ){
                $ids[] = $v['uid'];
            }
            $sql = "select * from member_income_business_owner where uid in (".implode(',',$ids).")  ";
            $owners = $this->reader->getRows($sql);
            $each_owners = array();
            $each_owners_name = array();
            foreach( $owners as $v ){
                $each_owners[$v['income_business_id']][] = $v;
                $each_owners_name[$v['income_business_id']][] = $v['relative_name'];
            }

            foreach( $list as $key=>$value ){
                $value['owner_list'] = $each_owners[$value['uid']];
                $value['owner_name_list'] = $each_owners_name[$value['uid']];
                $list[$key] = $value;
            }
        }
        return $list;
    }
}