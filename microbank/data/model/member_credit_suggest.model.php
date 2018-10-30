<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/16
 * Time: 15:24
 */
class member_credit_suggestModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_credit_suggest');
    }

    public function getSuggestDetailBySuggestId($uid)
    {
        $sql = "select e.*,a.asset_type,a.asset_name,a.mortgage_state,a.valuation from member_credit_suggest_detail e left join member_assets a on a.uid=e.member_asset_id
            where e.credit_suggest_id=" . $uid;
        $list = $this->reader->getRows($sql);
        return resetArrayKey($list, 'member_asset_id');
    }

    public function getSuggestRateBySuggestId($uid)
    {
        $sql = "select * from member_credit_suggest_rate WHERE credit_suggest_id = " . $uid;
        $suggest_rate = $this->reader->getRows($sql);
        return resetArrayKey($suggest_rate, 'product_id');
    }
    public function getSuggestProductBySuggestId($uid)
    {
        $sql = "select a.*,b.alias category_name from member_credit_suggest_product a ";
        $sql.=" left join member_credit_category b on a.member_credit_category_id=b.uid WHERE a.credit_suggest_id = " . $uid;
        $suggest_rate = $this->reader->getRows($sql);
        return resetArrayKey($suggest_rate, 'member_credit_category_id');
    }


    public function getSuggestListById($member_id)
    {
        $sql = "SELECT * FROM member_credit_suggest WHERE uid IN (SELECT MAX(uid) FROM member_credit_suggest WHERE member_id = " . $member_id . " GROUP BY operator_id) ORDER BY request_type DESC";
        $suggest_list = $this->reader->getRows($sql);
        foreach ($suggest_list as $key => $val) {
            $suggest_list[$key]['suggest_detail_list'] = $this->getSuggestDetailBySuggestId($val['uid']);
            $suggest_list[$key]['suggest_rate'] = $this->getSuggestRateBySuggestId($val['uid']);
        }
        return $suggest_list;
    }

    /**
     * 获取operator最新提交记录
     * @param $member_id
     * @param $operator_id
     */
    public function getLastSuggestOfOperator($member_id, $operator_id)
    {
        $suggest_info = $this->orderBy('uid DESC')->find(array(
            'member_id' => intval($member_id),
            'operator_id' => intval($operator_id)
        ));
        if ($suggest_info) {
            $suggest_info['suggest_detail_list'] = $this->getSuggestDetailBySuggestId($suggest_info['uid']);
            //$suggest_info['suggest_rate'] = $this->getSuggestRateBySuggestId($suggest_info['uid']);
            $suggest_info['suggest_product'] = $this->getSuggestProductBySuggestId($suggest_info['uid']);

            // 授信信息
            $suggest_info['suggest_grant_info'] = $this->getCreditSuggestGrantInfo($suggest_info['uid']);
        }
        return $suggest_info;
    }


    public function getCreditSuggestGrantInfo($suggest_id)
    {
        $m = new member_credit_grantModel();
        $grant_info = $m->orderBy('uid desc')->find(array(
            'credit_suggest_id' => $suggest_id
        ));
        if( $grant_info ){
            $sql = "select ga.*,us.user_name from member_credit_grant_attender ga left join um_user us
            on ga.attender_id=us.uid where ga.grant_id=".qstr($grant_info['uid']);
            $attender_info = $m->reader->getRows($sql);
            $grant_info['grant_attender_list'] = $attender_info;
        }
        return $grant_info;
    }

    /**
     * 提交总部
     * @param $uid
     * @return result
     * @throws Exception
     */
    public function submitRequestCreditToHq($uid)
    {
        $row = $this->getRow(array('uid' => intval($uid), 'state' => memberCreditSuggestEnum::CREATE));
        if (!$row) {
            return new result(false, 'Param Error!');
        }

        $member_id = $row['member_id'];
        $row_1 = $this->getRow(array('member_id' => $member_id, 'state' => memberCreditSuggestEnum::APPROVING));
        if ($row_1) {
            return new result(false, 'Have Suggest Credit For Voting!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        //目的是让当前这一条成为唯一的让hq审批的申请
        $sql = "UPDATE member_credit_suggest SET state = 0 WHERE member_id = " . $member_id . " AND state = " . memberCreditSuggestEnum::PENDING_APPROVE;
        $rt = $this->conn->execute($sql);
        if (!$rt->STS) {
            $conn->rollback();
            return new result(false, 'Submit Failure!');
        }

        $sql = "UPDATE member_credit_suggest SET state = 3 WHERE member_id = " . $member_id . " AND state = " . memberCreditSuggestEnum::HQ_REJECT;
        $rt = $this->conn->execute($sql);
        if (!$rt->STS) {
            $conn->rollback();
            return new result(false, 'Submit Failure!');
        }

        $row->state = memberCreditSuggestEnum::PENDING_APPROVE;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            $conn->submitTransaction();
            return new result(true, 'Submit Successful!');
        } else {
            $conn->rollback();
            return new result(false, 'Submit Failure!');
        }
    }
    /**
     * 提交总部后又撤回
     * @param $uid
     * @return result
     * @throws Exception
     */
    public function cancelSubmitRequestCreditToHq($uid)
    {
        $row = $this->getRow(array('uid' => intval($uid), 'state' => memberCreditSuggestEnum::PENDING_APPROVE));
        if (!$row) {
            return new result(false, 'Param Error!');
        }
        $row->state = memberCreditSuggestEnum::CREATE;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Cancel Successful!');
        } else {

            return new result(false, 'Cancel Failure!');
        }
    }
}