<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/8/6
 * Time: 13:24
 */
class memberBusinessClass
{

    public static function getMemberBusinessTotalIncomeOfOfficer($member_id,$officer_id,$include_all=true)
    {
        $data = self::getMemberIndustryAndResearchByOfficer($member_id,$officer_id,$include_all);
        return round($data['total_profit'],2);
    }

    public static function getMemberIndustryAndResearchByOfficer($member_id,$officer_id,$include_all=true)
    {
        $m_industry = new member_industryModel();
        $m_business_income = new member_income_businessModel();
        $industry_list = $m_industry->getMemberActiveIndustryList($member_id);
        $total_profit = 0;
        foreach( $industry_list as $k=>$v ){
            $industry_id = $v['industry_id'];
            // 统计此项下面的各个分支数据
            $research_list = $m_business_income->getMemberIndustryResearchByOfficer($member_id,$industry_id,$officer_id,$include_all);
            $industry_profit = 0;
            foreach( $research_list as $key=>$value ){
                $value['research_id'] = intval($value['research_id']);
                $value['profit'] = round($value['profit'],2);
                $industry_profit += $value['profit'];
                $research_list[$key] = $value;
            }
            $total_profit += $industry_profit;
            $v['total_profit'] = $industry_profit;
            $v['research_list'] = $research_list;
            $industry_list[$k] = $v;

        }
        return array(
            'total_profit' => $total_profit,
            'industry_list' => $industry_list
        );
    }


    /** 弃用了
     * @param $member_id
     * @return array
     */
    public static function getMemberIndustryAndResearchList($member_id)
    {
        $m_industry = new member_industryModel();
        $m_business_income = new member_income_businessModel();
        $industry_list = $m_industry->getMemberActiveIndustryList($member_id);
        $total_profit = 0;
        foreach( $industry_list as $k=>$v ){
            $industry_id = $v['industry_id'];
            // 此项下面的调查内容
            $research_list = $m_business_income->getMemberIndustryResearchList($member_id,$industry_id);
            // 统计总额和格式化owner
            $industry_profit = 0;
            foreach( $research_list as $key=>$value ){
                $industry_profit += $value['profit'];
                $value['owners_name'] = $value['owner_name_list']?implode('/',$value['owner_name_list']):'Own';
                $research_list[$key] = $value;
            }
            $v['research_list'] = $research_list;
            $v['total_profit'] = $industry_profit;
            $total_profit += $industry_profit;
            $industry_list[$k] = $v;
        }
        return array(
            'total_profit' => $total_profit,
            'industry_list' => $industry_list
        );
    }


    public static function getMemberIncomeBusinessPageData($member_id,$industry_id,$research_info=array())
    {
        $member_info = (new memberModel())->getMemberInfoById($member_id);
        //$business_place = global_settingClass::getBusinessPlaceList();
        $business_place = null;  // 现在用的是输入
        $industry_info = (new common_industryModel())->getRow($industry_id);
        if( !$industry_info ){
            throw new Exception('No industry info:'.$industry_id,errorCodesEnum::INVALID_PARAM);
        }

        $research = $research_info;

        // 上次调查的关系人列表
        $last_relative = array();
        foreach( $research['relative_list'] as $v ){
            $last_relative[] = $v['relative_id'];
        }
        // 关系人列表
        $relative_list = member_relativeClass::getMemberRelativeList($member_id);

        $all_relative_list = array();

        $own = array(
            'uid' => 0,
            //'name' => $member_info['display_name']?:$member_info['login_code'].'(own)',
            'name' => 'Own',
            'is_business_owner' => 0
        );
        if( in_array(0,$last_relative) ){
            $own['is_business_owner'] = 1;
        }

        if( !$research_info ){
            // 没有调查过，就默认勾选上自己
            $own['is_business_owner'] = 1;
        }

        $all_relative_list[] = $own;

        foreach( $relative_list as $k=>$v ){
            if( in_array($v['uid'],$last_relative) ){
                $v['is_business_owner'] = 1;
            }else{
                $v['is_business_owner'] = 0;
            }
            $all_relative_list[] = $v;
        }
        return array(
            'member_industry_info' => $industry_info,
            'last_research_info' => $research,
            'relative_list' => $all_relative_list,
            'business_place' => $business_place
        );
    }


    public static function getMemberIncomeBusinessEditInfo($research_id)
    {
        $research = credit_researchClass::getBusinessIncomeResearchDetailById($research_id);
        if( !$research ){
            return new result(false,'No data:'.$research_id,null,errorCodesEnum::INVALID_PARAM);
        }
        $member_id = $research['member_id'];
        $industry_id = $research['industry_id'];
        $data = self::getMemberIncomeBusinessPageData($member_id,$industry_id,$research);
        return new result(true,'success',$data);

    }
    public static function getLastMemberIncomeBusinessInfoByIndustryBranchCode($member_id,$industry_id,$branch_code,$user_id=0){
        $sql="select mib.*,uu.user_name from member_income_business mib inner join um_user uu on mib.operator_id=uu.uid ";
        $sql.=" where mib.industry_id=".qstr($industry_id)." and mib.branch_code=".qstr($branch_code)." and mib.member_id=".qstr($member_id);
        if($user_id>0){
            $sql.=" and mib.operator_id!='".$user_id."'";
        }else{
            $sql.=" and uu.user_position='".userPositionEnum::CREDIT_OFFICER."'";
        }
        $sql.=" order by mib.uid desc";
        $r=new ormReader();
        $item=$r->getRow($sql);
        $research_id=$item['uid'];
        $ret=credit_researchClass::getBusinessIncomeResearchDetailById($research_id);
        $ret['reference_name']=$item['user_name'];
        return $ret;
    }





}