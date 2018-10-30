<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/10/9
 * Time: 11:48
 */
class chief_credit_officerClass extends credit_officerClass{
    public static function getFollowedMemberList($branch_id){
        $sql = "select m.*,c.credit,c.credit_balance,c.expire_time,c.grant_time,c.credit_terms from member_follow_officer f inner join client_member m on m.uid=f.member_id
        left join member_credit c on c.member_id=m.uid where m.branch_id='$branch_id'
        and f.is_active='1' group by f.officer_id,f.member_id order by f.update_time desc  ";
        return (new ormReader())->getRows($sql);
    }

}