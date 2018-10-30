<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/29
 * Time: 下午3:50
 */
class counter_baseClass
{
    static function getCODofTeller($teller_id)
    {
        return userClass::getPassbookBalanceOfUser($teller_id);
    }

    /*
     * 获取在柜台处理存款业务的限额
     */
    static function getDepositLimitOfCounter($branch_id)
    {
        //$m_branch_limit = M('site_branch_limit');
        //$branch_limit = $m_branch_limit->find(array('branch_id'=>$branch_id,'limit_key'=>'limit_deposit'));
        return array(
            "USD" => array(
                "per_time" => 5000,
                "per_day" => 100000,
                "per_member_per_day" => 20000,
            ),
            "KHR" => array(
                "per_time" => 20000000,
                "per_day" => 40000000,
                "per_member_per_day" => 20000000,
            )
        );
    }

    /*
     * 获取在柜台处理取款业务的限额
     */
    static function getWithdrawalLimitOfCounter($branch_id)
    {
        return array(
            "USD" => array(
                "per_time" => 5000,
                "per_day" => 100000,
                "per_member_per_day" => 20000,
            ),
            "KHR" => array(
                "per_time" => 20000000,
                "per_day" => 40000000,
                "per_member_per_day" => 20000000,
            )
        );
    }

    /*
     * 获取柜台当天累计存款业务额度
     */
    static function getDepositPerDayOfCounter($branch_id)
    {
        return 0;
    }

    /*
     * 获取单个用户当日累计存款额度
     */
    static function getDepositPerDayOfMember($member_id)
    {
        return 0;
    }

    /*
     * 获取在柜台处理取款业务的限额
     */

    static function getWithdrawLimitOfCounter($branch_id)
    {

    }

    /*
     * 获取在柜台处理还款业务的限额
     */
    static function getRepaymentLimitOfCounter($branch_id)
    {

    }

    /*
     * 获取在柜台处理转账业务的限额
     */

    static function getTransferLimitOfCounter($branch_id)
    {

    }

    //根据member_id获取member信息，在counter展示的部分可能要做权限方面过滤，所以独立写出来
    static function getMemberInfoByID($member_id)
    {
        $ret = memberClass::searchMember(array(
            "type" => '0',
            "member_id" => $member_id
        ));
        if ($member_id > 0) {
            $obj = new objectMemberClass($member_id);
            $save_balance = $obj->getSavingsAccountBalance();
            $ret['save_balance'] = $save_balance;
            $ret['member_scene_image']=$obj::getNewestSceneImage($member_id);

        }
        //get id-card-handle pic
        $sql = "SELECT b.`image_url` FROM member_verify_cert a INNER JOIN member_verify_cert_image b "
            . " ON a.uid=b.cert_id WHERE b.`image_key`='" . certImageKeyEnum::ID_HANDHELD . "' AND a.`member_id`='" . $member_id . "'";
        $r = new ormReader();
        $id_handled_img = $r->getOne($sql);
        if ($id_handled_img) {
            $ret['hold_id_card'] = getImageUrl($id_handled_img, imageThumbVersion::MAX_240);
        }

        return $ret;
    }

    static function getMemberInfoByPhone($country_code, $phone_number)
    {

    }

    static function getMemberInfoByCard($card_id)
    {

    }

    //得到分行的所有teller
    public static function getBranchUserListOfTeller($branch_id, $include_chief_teller = true)
    {
        $sql = "SELECT a.uid,a.`user_code`,a.`user_name`,a.`user_position`,a.`user_status`,b.`depart_name`,c.`branch_name` FROM um_user a"
            . " INNER JOIN site_depart b ON a.`depart_id`=b.`uid`"
            . " INNER JOIN site_branch c ON b.`branch_id`=c.`uid`";
        $sql .= " where c.uid='" . $branch_id . "' and user_status != -1";
        if ($include_chief_teller) {
            $sql .= " and (a.user_position='" . userPositionEnum::TELLER . "' or a.user_position='" . userPositionEnum::CHIEF_TELLER . "')";
        } else {
            $sql .= " and (a.user_position='" . userPositionEnum::TELLER . "')";
        }
        $r = new ormReader();
        return $r->getRows($sql);
    }


}