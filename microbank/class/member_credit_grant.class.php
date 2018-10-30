<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/30
 * Time: 13:41
 */
class member_credit_grantClass
{
    function __construct()
    {

    }

    /**
     * Bm提交授信申请
     * @param $param
     * @return array
     */
    public function getBmCreditSuggestList($param)
    {
        $r = new ormReader();
        $state = $param['verify_state'];

        if ($param['verify_state'] == 'new') {
            $where = " where mcs.state='" . memberCreditSuggestEnum::PENDING_APPROVE . "'";
        } elseif ($param['verify_state'] == 'rejected') {
            $where = " where mcs.state='" . memberCreditSuggestEnum::HQ_REJECT . "'";
            $state = memberCreditSuggestEnum::HQ_REJECT;
        } else {
            $where = " where 1=1";
        }

        // 不要再覆盖了，需要查询投票的时间，不是update_time
        $sql = "SELECT mcs.*,cm.display_name,cm.login_code,sb.branch_name,mcg.vote_time FROM member_credit_suggest mcs ".
            " left join member_credit_grant mcg on mcg.credit_suggest_id=mcs.uid "
            . " INNER JOIN client_member cm ON mcs.member_id = cm.uid"
            . " INNER JOIN um_user uu ON uu.uid = mcs.operator_id"
            . " LEFT JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " LEFT JOIN site_branch sb ON sd.branch_id = sb.uid"
            . $where;

        $search_text = trim($param['search_text']);
        if ($search_text) {
            $sql .= " AND cm.display_name like '%" . qstr2($search_text) . "'";
        }
        $sql .= " ORDER BY mcs.update_time DESC";

        $pageNumber = intval($param['pageNumber']) ?: 1;
        $pageSize = intval($param['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "state" => $state,
        );
    }

    /**
     * 获取已经审核的授信请求
     * @param $param
     * @return array
     */
    public function getCreditGrant($param)
    {
        $r = new ormReader();
        $sql = "SELECT mcg.*,cm.display_name,cm.login_code FROM member_credit_grant mcg
                INNER JOIN client_member cm ON mcg.member_id = cm.uid
                INNER JOIN um_user uu ON uu.uid = mcg.operator_id
                WHERE mcg.credit_suggest_id != 0";

        $search_text = trim($param['search_text']);
        if ($search_text) {
            $sql .= " AND cm.display_name like '%" . qstr2($search_text) . "'";
        }

        $verify_state = $param['verify_state'];
        $sql .= " AND mcg.state = '" . $param['verify_state'] . "'";
        $sql .= " ORDER BY mcg.uid DESC";
        /*
        if ($verify_state == 'approving') {
            $sql .= " AND mcg.state = 0";
            $sql .= " ORDER BY mcg.grant_time DESC";
        } else {
            $sql .= " AND mcg.state > 0";
            $sql .= " ORDER BY mcg.uid DESC";
        }*/

        $pageNumber = intval($param['pageNumber']) ?: 1;
        $pageSize = intval($param['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "state" => $verify_state
        );
    }

    /**
     * 获取bm提交的申请
     * @param $uid
     * @return result
     */
    public function getBmCreditSuggestDetailById($uid)
    {
        $m_suggest = new member_credit_suggestModel();
        $bm_suggest = $m_suggest->find(array(
            'uid' => $uid
        ));

        if (!$bm_suggest) {
            return new result(false, 'Invalid Id!');
        }

        if ($bm_suggest['state'] != memberCreditSuggestEnum::PENDING_APPROVE && $bm_suggest['state'] != memberCreditSuggestEnum::APPROVING) {
            return new result(false, 'Invalid Id!');
        }

        $m_member_credit_suggest = new member_credit_suggestModel();
        $bm_suggest['suggest_detail_list'] = $m_member_credit_suggest->getSuggestDetailBySuggestId($bm_suggest['uid']);
        $bm_suggest['suggest_product']=$m_member_credit_suggest->getSuggestProductBySuggestId($bm_suggest['uid']);
        //$bm_suggest['suggest_rate'] = $m_member_credit_suggest->getSuggestRateBySuggestId($bm_suggest['uid']);
        //$bm_suggest['suggest_product'] = $m_member_credit_suggest->getSuggestProductBySuggestId($bm_suggest['uid']);


        $member_id = $bm_suggest['member_id'];
        $operator_id = $bm_suggest['operator_id'];

        $m_credit_request = new member_credit_requestModel();
        $last_request = $m_credit_request->find(array("member_id" => $member_id, "state" => creditRequestStateEnum::CREATE));
        if ($last_request) {
            $bm_suggest['credit_request'] = $last_request;
        }

        $r = new ormReader();
        $sql = "select sb.branch_name from um_user uu INNER JOIN site_depart sd ON uu.depart_id = sd.uid INNER JOIN site_branch sb ON sd.branch_id = sb.uid WHERE uu.uid = " . $operator_id;
        $branch_name = $r->getOne($sql);
        $bm_suggest['branch_name'] = $branch_name;

        $data = array(
            'bm_suggest' => $bm_suggest,
            'member_id' => $member_id,
        );

        return new result(true, '', $data);
    }

    /**
     * Bm申请历史
     * todo::待改
     * @param $param
     * @return bool|mixed|null
     */
    public function getBmCreditSuggestHistory($param)
    {
        $uid = intval($param['uid']);
        $m_member_credit_suggest = M('member_credit_suggest');
        $credit_suggest = $m_member_credit_suggest->find(array('uid' => $uid));
        if (!$credit_suggest) {
            showMessage('Invalid Id!');
        }

        $member_id = $credit_suggest['member_id'];
        $branch_id = $credit_suggest['branch_id'];

        $m_member_credit_suggest = M('member_credit_suggest');
        $credit_suggest = $m_member_credit_suggest->orderBy('update_time DESC')->select(array('member_id' => $member_id, 'branch_id' => $branch_id, 'state' => array('gt', 0)));
        foreach ($credit_suggest as $key => $val) {
            $sql = "select e.*,a.asset_type,a.mortgage_state from member_credit_suggest_detail e left join member_assets a on a.uid=e.member_asset_id
            where e.credit_suggest_id='" . $val['uid'] . "' ";
            $list = $m_member_credit_suggest->reader->getRows($sql);
            $credit_suggest[$key]['suggest_detail_list'] = $list;
        }

        return $credit_suggest;
    }

    /**
     * 提交讨论结果进行投票
     * @param $param
     * @return result
     */
    public function commitCreditApplication($param)
    {
        $suggest_id = intval($param['suggest_id']);
        $member_id = intval($param['member_id']);
        $operator_id = $param['operator_id'];
        $operator_name = $param['operator_name'];
        $client_request_credit = intval($param['client_request_credit']);
        $monthly_repayment_ability = round($param['monthly_repayment_ability'], 2);
        $invalid_terms = intval($param['credit_terms']);
        $default_credit = intval($param['default_credit']);
        $remark = trim($param['remark']);
        $committee_member = $param['committee_member'];
        $is_auto_Authorize = intval($param['is_auto_Authorize']);

        $max_credit = $param['max_credit'];
        if (!$param['default_credit_category_id']) {
            return new result(false, "Required to choose credit category");
        }
        if (!$invalid_terms) {
            return new result(false, "Required for credit terms");
        }

        // 如果提交的max credit 不等于 default+increase ，不能通过
        $total_increase_credit = 0;
        foreach ($param['credit_increase'] as $item) {
            $total_increase_credit += $item['credit'] ?: 0;
            if (!$item['member_credit_category_id']) {
                return new result(false, "Required to choose credit category");
            }
        }
        if (($default_credit + $total_increase_credit) != $max_credit) {
            return new result(false, 'Invalid Parameter:Max credit is not equals default-credit plus asset-increase-credit. ');
        }

        //判断该客人是否还存在待签订合同
        if (memberClass::isSignAuthorizedContract($member_id) < 0) {
            return new result(false, 'Not allowed to grant Credit: The client has some credit-agreement that are not signed');
        }

        $is_grant_by_bm = ($param['operator_type'] == 1) ? 1 : 0;
        $is_auto_vote = 0;//是否自动完成投票
        if (!$is_grant_by_bm) {
            //如果不是bm提交的，判断voter人数
            //先计算max-credit，判断voter人数
            $limit_voter = global_settingClass::getVoterOfGrantingCreditByAmount($max_credit);
            if ($limit_voter > 1) {//必须超过1人投票
                if (count($committee_member) < $limit_voter) {
                    return new result(false, "Asking for at least " . $limit_voter . " voters");
                }
            }
            if (count($committee_member) == 1) {
                if ($committee_member[0] == $operator_id) {
                    $is_auto_vote = 1;//说明允许只有一个投票者，并且是自己
                }
            }
        } else {
            $is_auto_Authorize = 0;//bm提交的，必须授权
            $is_auto_vote = 1;
        }


        /*
        if (empty($committee_member)) {
            return new result(false, 'Please select committee member!');
        }
        */

        //$max_credit = $default_credit;
        $conn = ormYo::Conn();
        if (!$param['is_start_transaction_outside']) {
            $conn->startTransaction();
        }
        $m_member_credit_suggest = new member_credit_suggestModel();
        if ($suggest_id) {
            $member_credit_suggest = $m_member_credit_suggest->getRow($suggest_id);
            if ($member_credit_suggest['state'] != memberCreditSuggestEnum::PENDING_APPROVE) {
                $conn->rollback();
                return new result(false, 'Invalid State.');
            }
            $member_credit_suggest->state = memberCreditSuggestEnum::APPROVING;
            $member_credit_suggest->update_time = Now();
            $rt_4 = $member_credit_suggest->update();
            if (!$rt_4->STS) {
                $conn->rollback();
                return new result(false, 'Commit Failure!');
            }

        }
        //处理member_credit_request
        $m_credit_request = new member_credit_requestModel();
        $last_request = $m_credit_request->getRow(array("member_id" => $member_id, "state" => creditRequestStateEnum::CREATE));
        if ($last_request) {
            $request_id = $last_request->uid;
            $last_request->update_time = Now();
            $last_request->update_operator_id = $operator_id;
            $last_request->update_operator_name = $operator_name;
            $ret_request = $last_request->update();
            if (!$ret_request->STS) {
                $conn->rollback();
                return new result(false, 'Commit Failure:Failed to Update Credit Request State');
            }
        } else {
            $request_id = 0;
        }


        $m = new member_credit_grantModel();
        $row = $m->newRow();
        $row->grant_time = Now();
        $row->member_id = $member_id;
        $row->operator_id = $operator_id;
        $row->operator_name = $operator_name;
        $row->client_request_credit = $client_request_credit;
        $row->monthly_repayment_ability = $monthly_repayment_ability;
        $row->default_credit = $default_credit;
        $row->default_credit_category_id = $param['default_credit_category_id'];
        $row->max_credit = $max_credit;
        $row->credit = $default_credit;
        $row->credit_terms = $invalid_terms;
        $row->remark = $remark;
        $row->credit_suggest_id = $suggest_id;
        $row->credit_request_id = $request_id;
        $row->is_append = intval($param['is_append']) ?: 0;
        $row->state = commonApproveStateEnum::APPROVING;
        $row->update_time = Now();
        $expire_time = date('Y-m-d H:i:s', strtotime('+2 day'));
        $row->vote_expire_time = $expire_time;

        $row->is_auto_Authorize = $is_auto_Authorize;
        $rt_1 = $row->insert();
        if (!$rt_1->STS) {
            $conn->rollback();
            return new result(false, 'Commit Failure!');
        } else {
            $grant_id = $rt_1->AUTO_ID;
        }

        $m_member_credit_grant_assets = new member_credit_grant_assetsModel();
        $arr_credit_increase = $param['credit_increase'];
        foreach ($arr_credit_increase as $id => $item) {
            if (!$item['member_credit_category_id']) {
                $conn->rollback();
                return new result(false, "Require Credit Category For Mortgage Asset");
            }
            //判断资产有没有处于已经抵押或者待签合同状态
            $chk_asset_valid=member_assetsClass::checkAssetIdValidOfGrantCredit($id);
            if(!$chk_asset_valid->STS){
                $conn->rollback();
                return $chk_asset_valid;
            }

            $row_1 = $m_member_credit_grant_assets->newRow();
            $row_1->grant_id = $row->uid;
            $row_1->member_asset_id = $id;
            $row_1->credit = $item['credit'];
            $row_1->member_credit_category_id = $item['member_credit_category_id'];
            $rt_2 = $row_1->insert();
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Commit Failure!');
            }
        }

        //处理currency
        if($suggest_id){
            $credit_currency=$m_member_credit_suggest->getSuggestProductBySuggestId($suggest_id);
            foreach($credit_currency as $ccy_credit){
                $m_member_credit_grant_product=new member_credit_grant_productModel();
                $prod_row=$m_member_credit_grant_product->newRow();
                $prod_row->grant_id=$row->uid;
                $prod_row->member_credit_category_id=$ccy_credit['member_credit_category_id'];
                $prod_row->credit=$ccy_credit['credit'];
                $prod_row->credit_usd=$ccy_credit['credit_usd'];
                $prod_row->credit_khr=$ccy_credit['credit_khr'];
                $prod_row->exchange_rate=$ccy_credit['exchange_rate'];
                $rt_prod=$prod_row->insert();
                if(!$rt_prod->STS){
                    $conn->rollback();
                    return $rt_prod;
                }
            }
        }



        $m_member_credit_grant_attender = M('member_credit_grant_attender');
        if ($is_auto_vote) {//自动投票的处理,插入投票人，调用自动完成投票的逻辑
            if (!count($committee_member)) {
                $committee_member[] = $operator_id;
            }
        }

        $m_user = new um_userModel();
        foreach ($committee_member as $val) {
            $user_info = $m_user->find(array(
                'uid' => $val
            ));
            $row_2 = $m_member_credit_grant_attender->newRow();
            $row_2->attender_id = $val;
            $row_2->attender_name = $user_info['user_name'];
            $row_2->grant_id = $row->uid;
            $row_2->vote_result = commonApproveStateEnum::CREATE;
            if ($is_auto_vote) {
                $row_2->vote_result = commonApproveStateEnum::PASS;
                $row_2->update_time = Now();
            }
            $rt_3 = $row_2->insert();
            if (!$rt_3->STS) {
                $conn->rollback();
                return new result(false, 'Commit Failure!');
            }
        }
        if ($is_auto_vote) {
            $ret_vote = $this->completeVote($row->uid, true);
            if (!$ret_vote->STS) {
                $conn->rollback();
                return $ret_vote;
            }
        }

        if (!$param['is_start_transaction_outside']) {
            $conn->submitTransaction();
        }

        return new result(true, 'Commit Successful!', array('uid' => $rt_1->AUTO_ID));
    }

    public function rejectCreditApplication($param)
    {
        $suggest_id = intval($param['suggest_id']);
        $remark = trim($param['remark']);
        $m_member_credit_suggest = M('member_credit_suggest');
        $member_credit_suggest = $m_member_credit_suggest->getRow($suggest_id);
        if ($member_credit_suggest['state'] != memberCreditSuggestEnum::PENDING_APPROVE) {
            return new result(false, 'Invalid State.');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $member_credit_suggest->state = memberCreditSuggestEnum::HQ_REJECT;
        $member_credit_suggest->remark = $remark;
        $member_credit_suggest->update_time = Now();
        $rt = $member_credit_suggest->update();
        if (!$rt->STS) {
            $conn->rollback();
            return new result(true, 'Handle Failed.');
        }

        $m_credit_request = new member_credit_requestModel();
        $credit_request = $m_credit_request->getRow(array("member_id" => $member_credit_suggest['member_id'], "state" => creditRequestStateEnum::GRANTED));
        if ($credit_request) {
            $credit_request->state = creditRequestStateEnum::CREATE;
            $credit_request->update_time = Now();
            $credit_request->update_operator_id = $param['operator_id'];
            $credit_request->update_operator_name = $param['operator_name'];
            $ret_request = $credit_request->update();
            if (!$ret_request->STS) {
                $conn->rollback();
                return new result(true, 'Handle Failed.');
            }
        }

        $task_msg = "Reject Request For Credit【No.：" . $suggest_id . "】 For Remark：【" . $remark . "】 At " . Now();
        taskControllerClass::handleNewTask($suggest_id, userTaskTypeEnum::BM_REQUEST_FOR_CREDIT, $member_credit_suggest->branch_id, objGuidTypeEnum::SITE_BRANCH, $param['operator_id'], objGuidTypeEnum::UM_USER, $task_msg);

        $conn->submitTransaction();
        return new result(true, 'Handle Successful.');
    }

    /**
     * 获取授信投票信息
     * @param $uid
     * @return array|bool|mixed
     */
    public function getCreditGrantById($uid)
    {
        $m_grant = new member_credit_grantModel();
        $credit_grant = $m_grant->find(array('uid' => $uid));
        if (!$credit_grant) {
            return array();
        }

        if ($credit_grant['credit_request_id']) {
            $credit_request = M('member_credit_request')->find(array('uid' => $credit_grant['credit_request_id']));
            $credit_grant['credit_request'] = $credit_request;
        }

        if ($credit_grant['credit_suggest_id']) {
            $credit_request = M('member_credit_suggest')->find(array('uid' => $credit_grant['credit_suggest_id']));
            $credit_grant['suggest_remark'] = $credit_request['remark'];
            $credit_grant['credit_suggest']=$credit_request;
        }

        $sql = "select e.*,a.asset_type,a.asset_name,a.mortgage_state,a.valuation from member_credit_grant_assets e left join member_assets a on a.uid=e.member_asset_id
        where e.grant_id = " . $credit_grant['uid'];
        $list = $m_grant->reader->getRows($sql);
        $list = resetArrayKey($list, 'member_asset_id');
        $credit_grant['suggest_detail_list'] = $list;

        /*
        $sql = "select * from member_credit_grant_rate WHERE credit_grant_id = " . $credit_grant['uid'];
        $list = $m_grant->reader->getRows($sql);
        $list = resetArrayKey($list, 'product_id');
        $credit_grant['grant_rate'] = $list;
        */

        $sql = "select * from member_credit_grant_product  WHERE grant_id = " . $credit_grant['uid'];
        $list = $m_grant->reader->getRows($sql);
        $list = resetArrayKey($list, 'member_credit_category_id');
        $credit_grant['grant_product']=$list;


        if ($credit_grant['state'] == 100) {
            $sql = "select mac.*,sb.branch_name from member_authorized_contract mac LEFT JOIN site_branch sb ON mac.branch_id = sb.uid WHERE mac.grant_credit_id = " . $credit_grant['uid'] . " and mac.state = " . authorizedContractStateEnum::COMPLETE;
            $authorized_contract = $m_grant->reader->getRow($sql);
            $credit_grant['authorized_contract'] = $authorized_contract;
        }

        return $credit_grant;
    }

    /**
     * 参与投票人员
     * @param $grant_id
     * @return ormCollection
     */
    public function getVoteCommitteeMember($grant_id)
    {
        $grant_id = intval($grant_id);
        $r = new ormReader();
        $sql = "SELECT mcgv.*,uu.user_name FROM member_credit_grant_attender mcgv LEFT JOIN um_user uu ON mcgv.attender_id = uu.uid WHERE mcgv.grant_id = " . $grant_id;
        return $r->getRows($sql);
    }

    /**
     * 投票
     * @param $param
     * @return result
     */
    public function submitVoteCreditApplication($param)
    {
        $grant_id = intval($param['grant_id']);
        $vote_state = intval($param['vote_state']);
        $account = trim($param['account']);
        $password = trim($param['password']);
        $default_user_id = $param['user_id'];

        if (!$default_user_id) {
            //需要验证登录
            $m_um_user = M('um_user');
            $um_user = $m_um_user->find(array('user_code' => $account));
//        if (!$um_user || $um_user['user_position'] != userPositionEnum::COMMITTEE_MEMBER || $um_user['user_position'] == userPositionEnum::BACK_OFFICER) {
            if (!$um_user) {
                return new result(false, 'Invalid Account!');
            }

            if ($um_user['password'] != md5($password)) {
                return new result(false, 'Password Error!');
            }
            $user_id = $um_user['uid'];
        } else {
            $user_id = $default_user_id;
        }
        $m_member_credit_grant_attender = M('member_credit_grant_attender');
        $row = $m_member_credit_grant_attender->getRow(array('grant_id' => $grant_id, 'attender_id' => $user_id));
        if (!$row) {
            return new result(false, 'Invalid Account!');
        }
        if ($row->vote_result != commonApproveStateEnum::CREATE) {
            return new result(false, 'The user has voted yet!');
        }
        $m_member_credit_grant = M('member_credit_grant');
        $member_credit_grant = $m_member_credit_grant->find($row->grant_id);
        if (!$member_credit_grant) {
            return new result(false, 'Invalid credit grant!');
        }
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $row->vote_result = $vote_state;
        $row->vote_remark = $param['vote_remark'] ?: '';
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            //判断是否所有人完成投票，触发完成动作
            if ($this->checkGrantVoteFinished($grant_id)) {
                $ret = $this->completeVote($grant_id, true);
                if ($ret->STS) {
                    $conn->submitTransaction();
                    return new result(true, "Vote Success!");
                } else {
                    $conn->rollback();
                    return $ret;
                }
            } else {
                $conn->submitTransaction();
                return new result(true, "Vote Success!");
            }
        } else {
            $conn->rollback();
            return new result(true, 'Vote Failed!');
        }
    }

    public function checkGrantVoteFinished($uid)
    {
        $r = new ormReader();
        $sql = "select count(*) vote_count from member_credit_grant_attender WHERE grant_id = " . $uid;
        $vote_count = $r->getOne($sql);

        $sql = "select count(*) approval_count from member_credit_grant_attender WHERE vote_result >0 AND grant_id = " . $uid;
        $approval_count = $r->getOne($sql);
        if ($vote_count == $approval_count) {
            return true;
        }
        return false;

    }

    /**
     * 确认投票
     * @param $uid
     * @return ormResult|result
     */
    public function completeVote($uid, $is_start_transaction_outside = false)
    {
        $m_member_credit_grant = M('member_credit_grant');
        $m_member_credit_suggest = M('member_credit_suggest');
        $row = $m_member_credit_grant->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid id.');
        } else {
            $suggest_id = $row->credit_suggest_id;
        }
        $member_id=$row->member_id;

        $r = new ormReader();
        $sql = "select count(*) vote_count from member_credit_grant_attender WHERE grant_id = " . $uid;
        $vote_count = $r->getOne($sql);

        $sql = "select count(*) approval_count from member_credit_grant_attender WHERE vote_result = 100 AND grant_id = " . $uid;
        $approval_count = $r->getOne($sql);

        $conn = ormYo::Conn();
        if (!$is_start_transaction_outside) {
            $conn->startTransaction();
        }

        if ($approval_count >= $vote_count) {
            $row->state = commonApproveStateEnum::PASS;
            $row->vote_result = commonApproveStateEnum::PASS;
            $row->vote_time = Now();
            $row->update_time = Now();
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return $rt_1;
            }

            $suggest_state = memberCreditSuggestEnum::PASS;
            if ($row->credit_request_id > 0) {
                $m_credit_request = new member_credit_requestModel();
                $request_row = $m_credit_request->getRow($row->credit_request_id);
                $request_row->state = creditRequestStateEnum::GRANTED;
                $request_row->update_time = Now();
                $rt_2 = $request_row->update();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return $rt_2;
                }
            }

            /*
             暂时不支持自动签约
            if ($row->is_auto_Authorize == 1) {
                $rt_4 = member_credit_grantClass::signFastAuthorizedContract($row->uid, $row->member_id, $row->operator_id);
                if (!$rt_4->STS) {
                    $conn->rollback();
                    return $rt_4;
                }
            }
            */

            // 更新资产的信用
            $rt = $this->updateAssetsCreditAndStateByGrant($uid);
            if (!$rt->STS) {
                $conn->rollback();
                return $rt;
            }

            $data = array(
                'is_fast_grant' => $suggest_id == 0 ? true : false,
            );
            $msg = 'Pass!';
            $task_msg = "Approve Request For Credit【No.：" . $suggest_id . "】 At " . Now();

        } else {
            $row->state = commonApproveStateEnum::REJECT;
            $row->vote_result = commonApproveStateEnum::REJECT;
            $row->vote_time = Now();
            $row->update_time = Now();
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return $rt_1;
            }

            //更新member_attachment,member_income_salary,member_income_business的状态
            $sql="update member_attachment set state=0 where member_id='".$member_id."' and state=100 and grant_id='".$uid."'";
            $ret_upt=$conn->execute($sql);
            if(!$ret_upt->STS){
                $conn->rollback();
                return $ret_upt;
            }
            $sql="update member_income_business set state=0 where member_id='".$member_id."' and state=100 and grant_id='".$uid."'";

            $ret_upt=$conn->execute($sql);
            if(!$ret_upt->STS){
                $conn->rollback();
                return $ret_upt;
            }
            $sql="update member_income_salary set state=0 where member_id='".$member_id."' and state=100 and grant_id='".$uid."'";
            $ret_upt=$conn->execute($sql);
            if(!$ret_upt->STS){
                $conn->rollback();
                return $ret_upt;
            }

            $suggest_state = memberCreditSuggestEnum::NO_PASS;
            $data = array(
                'is_fast_grant' => $suggest_id == 0 ? true : false,
            );
            $msg = 'Not Pass!';
            $task_msg = "Unapprove Request For Credit【No.：" . $suggest_id . "】 At " . Now();
        }

        if ($suggest_id) {
            $suggest_row = $m_member_credit_suggest->getRow($suggest_id);
            $branch_id = $suggest_row->branch_id;
            $suggest_row->state = $suggest_state;
            $suggest_row->update_time = Now();
            $rt_2 = $suggest_row->update();
            if (!$rt_2->STS) {
                $conn->rollback();
                return $rt_2;
            }
        }

        if (!$is_start_transaction_outside) {
            $conn->submitTransaction();
        }

        taskControllerClass::handleNewTask($suggest_id, userTaskTypeEnum::BM_REQUEST_FOR_CREDIT, intval($branch_id), objGuidTypeEnum::SITE_BRANCH, 0, objGuidTypeEnum::UM_USER, $task_msg);

        return new result(true, $msg, $data);
    }

    public function updateAssetsCreditAndStateByGrant($grant_id)
    {
        $r = new ormReader();
        $sql = "select * from member_credit_grant_assets where grant_id='$grant_id' ";
        $rows = $r->getRows($sql);
        if (count($rows) < 1) {
            return new result(true);
        }
        $conn = ormYo::Conn();
        foreach ($rows as $v) {
            $sql = "update member_assets set credit='" . $v['credit'] . "',asset_state=" . qstr(assetStateEnum::GRANTED) . " where uid='" . $v['member_asset_id'] . "' ";
            $up = $conn->execute($sql);
            if (!$up->STS) {
                return new result(false, 'Update asset credit fail.');
            }
        }
        return new result(true);
    }

    /**
     * 重置投票时间
     * @param $uid
     * @return result
     */
    public function resetVoteTimer($uid)
    {
        $m_member_credit_grant = M('member_credit_grant');
        $row = $m_member_credit_grant->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row->grant_time = Now();
            $row->update_time = Now();
            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Reset Failure!');
            }

            $sql = "update member_credit_grant_attender set vote_result = " . commonApproveStateEnum::CREATE . ",update_time = null WHERE grant_id = " . $uid;
            $rt_2 = $m_member_credit_grant->conn->execute($sql);
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Reset Failure!');
            }

            $conn->submitTransaction();
            return new result(true, 'Reset Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /** 取消通过的信用授信数据
     * @param $grant_id
     * @return result
     */
    public static function creditGrantCancelExecute($grant_id)
    {
        $m_credit_grant = new member_credit_grantModel();
        $grant = $m_credit_grant->getRow(array(
            'uid' => $grant_id
        ));
        if( !$grant ){
            return new result(false,'No grant info:'.$grant_id,null,errorCodesEnum::NO_DATA);
        }
        // 更新状态为取消
        $grant->state = commonApproveStateEnum::CANCEL;
        $grant->update_time = Now();
        $up = $grant->update();
        if( !$up->STS ){
            return new result(false,'Update credit grant fail.',null,errorCodesEnum::DB_ERROR);
        }

        // 更新申请状态为cancel
        $suggest_id = $grant->credit_suggest_id;
        $m_member_credit_suggest = new member_credit_suggestModel();
        $row_suggest = $m_member_credit_suggest->getRow($suggest_id);
        if (!$row_suggest) {
            return new result(false, 'No credit suggest info:'.$suggest_id,null,errorCodesEnum::NO_DATA);
        }
        $row_suggest->state = memberCreditSuggestEnum::CANCEL;
        $row_suggest->update_time = Now();
        $rt = $row_suggest->update();
        if( !$rt->STS ){
            return new result(false,'Update credit suggest fail.',null,errorCodesEnum::DB_ERROR);
        }

        //更新member_attachment,member_income_salary,member_income_business的状态
        $conn=$m_credit_grant->conn;
        $member_id=$grant['member_id'];
        $sql="update member_attachment set state=0,request_id=0,grant_id=0 where member_id='".$member_id."' and grant_id='".$uid."'";
        $ret_upt=$conn->execute($sql);
        if(!$ret_upt->STS){
            return $ret_upt;
        }
        $sql="update member_income_business set state=0,request_id=0,grant_id=0 where member_id='".$member_id."' and grant_id='".$uid."'";

        $ret_upt=$conn->execute($sql);
        if(!$ret_upt->STS){
            return $ret_upt;
        }
        $sql="update member_income_salary set state=0,request_id=0,grant_id=0 where member_id='".$member_id."' and grant_id='".$uid."'";

        $ret_upt=$conn->execute($sql);
        if(!$ret_upt->STS){
            return $ret_upt;
        }



        $request_id = $grant['credit_request_id'];
        if( $request_id ){
            // 要更新申请为new，可返回让CO修改
            $m_credit_request = new member_credit_requestModel();
            $credit_request = $m_credit_request->getRow($request_id);
            if( !$credit_request ){
                return new result(false,'No credit request info:'.$request_id,null,errorCodesEnum::NO_DATA);
            }
            $credit_request->state = creditRequestStateEnum::CREATE;
            $credit_request->update_time = Now();
            $up = $credit_request->update();
            if( !$up->STS ){
                return new result(false,'Update credit request fail',null,errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true,'success',$grant);

    }

    /**
     * 重新编辑
     * @param $uid
     * @return result
     */
    public function cancelCreditGrant($uid)
    {

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = self::creditGrantCancelExecute($uid);
        if (!$rt->STS) {
            $conn->rollback();
            return new result(false, 'Cancel Failure!');
        }
        $conn->submitTransaction();
        $grant_info = $rt->DATA;
        $suggest_id = $grant_info['credit_suggest_id'];
        return new result(true, '', $suggest_id);
    }


    /** 最近的有效授信记录
     * @param $member_id
     * @return mixed
     */
    public static function getMemberLastGrantInfo($member_id)
    {
        // 最后一次授信
        $grant_credit = (new member_credit_grantModel())->orderBy('uid desc')->find(array(
            'member_id' => $member_id,
            'state' => commonApproveStateEnum::PASS
        ));
        return $grant_credit;
    }

    public static function deleteCreditGrant($uid)
    {
        // 最后一次授信
        $m_member_credit_grant = M('member_credit_grant');
        $row = $m_member_credit_grant->getRow($uid);
        if (!$row) {
            return new result(false, 'Invalid Id.');
        }
        $member_id=$row->member_id;

        $m_member_authorized_contract = M('member_authorized_contract');
        $authorized_contract = $m_member_authorized_contract->find(array('grant_credit_id' => $uid, 'state' => authorizedContractStateEnum::COMPLETE));
        if ($authorized_contract) {
            return new result(false, 'Existing authorized contract.');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            //先去掉credit
            /*
            $ret=member_creditClass::cutCreditForMemberByCategoryId($row->default_credit_category_id,$row->default_credit,"Cancel Grant",creditEventTypeEnum::GRANT);
            if(!$ret->STS){
                $conn->rollback();
                return $ret;
            }
            */


            $credit_suggest_id = $row['credit_suggest_id'];
            $credit_request_id = $row['credit_request_id'];

            $rt_1 = $row->delete();
            if (!$rt_1->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed.');
            }

            $rt_2 = M('member_credit_grant_assets')->delete(array('grant_id' => $uid));
            if (!$rt_2->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed.');
            }

            $rt_3 = M('member_credit_grant_attender')->delete(array('grant_id' => $uid));
            if (!$rt_3->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed.');
            }

            $rt_4 = M('member_credit_grant_rate')->delete(array('credit_grant_id' => $uid));
            if (!$rt_4->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed.');
            }

            if ($credit_suggest_id) {
                $row_1 = M('member_credit_suggest')->getRow($credit_suggest_id);
                if ($row_1) {
                    $row_1->state = memberCreditSuggestEnum::PENDING_APPROVE;
                    $row_1->update_time = Now();
                    $rt_5 = $row_1->update();
                    if (!$rt_5->STS) {
                        $conn->rollback();
                        return new result(false, 'Delete failed.');
                    }
                }
            }

            if ($credit_request_id) {
                $row_2 = M('member_credit_request')->getRow($credit_request_id);
                if ($row_2) {
                    $row_2->state = creditRequestStateEnum::CREATE;
                    $row_2->update_time = Now();
                    $rt_6 = $row_2->update();
                    if (!$rt_6->STS) {
                        $conn->rollback();
                        return new result(false, 'Delete failed.');
                    }
                }
            }

            //更新member_attachment,member_income_salary,member_income_business的状态
            $sql="update member_attachment set state=0,request_id=0,grant_id=0 where member_id='".$member_id."' and grant_id='".$uid."'";
            $ret_upt=$conn->execute($sql);
            if(!$ret_upt->STS){
                $conn->rollback();
                return $ret_upt;
            }
            $sql="update member_income_business set state=0,request_id=0,grant_id=0 where member_id='".$member_id."' and grant_id='".$uid."'";

            $ret_upt=$conn->execute($sql);
            if(!$ret_upt->STS){
                $conn->rollback();
                return $ret_upt;
            }
            $sql="update member_income_salary set state=0,request_id=0,grant_id=0 where member_id='".$member_id."' and grant_id='".$uid."'";

            $ret_upt=$conn->execute($sql);
            if(!$ret_upt->STS){
                $conn->rollback();
                return $ret_upt;
            }

            $conn->submitTransaction();
            return new result(true, 'Delete successful.');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }




    /**
     * 更新授权合同
     * @param $args
     * @return result
     * @throws Exception
     */
    public static function updateAuthorizeContract($args)
    {
        if (!$args['contract_id']) {
            return new result(false, "Invalid Parameter:Empty Contract ID");
        }
        $m_contract = new member_authorized_contractModel();
        $row = $m_contract->getRow($args['contract_id']);
        if (!$row || $row->state == authorizedContractStateEnum::COMPLETE) {
            return new result(false, "No object can be update!");
        }
        // 取消和锁定的合同也不能更新
        if( $row->state == authorizedContractStateEnum::LOCK ||
         $row->state == authorizedContractStateEnum::CANCEL ){
            return new result(false,'Can not modify now.',null,errorCodesEnum::UN_EDITABLE);
        }

        $objUser = new objectUserClass($args['operator_id']);
        //可能以后加上对user的密码验证

        $row->update_operator_id = $args['operator_id'];
        $row->update_operator_name = $args['operator_name'];
        $row->update_time = Now();
        if ($args['member_image']) {
            $row->member_img = $args['member_image'];
        }
        // 插入合同的纸质文件图片
        if (count($args['contract_images'])) {
            $image_arr = array();
            $sql = "insert into member_authorized_contract_image(authorized_contract_id,image_path) values ";
            foreach ($args['contract_images'] as $image_path) {
                if (!$image_path) continue;
                $temp = "('" . $row->uid . "','$image_path')";
                $image_arr[] = $temp;
            }
            if (count($image_arr)) {
                $sql .= implode(',', $image_arr);
                $insert = $m_contract->conn->execute($sql);
                if (!$insert->STS) {
                    return new result(false, 'Insert auth contract image fail.', null, errorCodesEnum::DB_ERROR);
                }
            }
        }
        $m_mortgage = new member_asset_mortgageModel();
        if (count($args['received_list'])) {
            $m_asset = new member_assetsModel();
            foreach ($args['received_list'] as $item) {
                $mortgage_row = $m_mortgage->getRow($item['asset_mortgage_id']);
                if (!$mortgage_row) continue;
                $mortgage_row->is_received = $item['is_received'];
                $mortgage_row->keep_time = Now();
                $mortgage_row->keeper_id = $args['operator_id'];
                $mortgage_row->keeper_name = $args['operator_name'];
                // 插入图片
                if (!empty($item['asset_images'])) {
                    $asset_images = array();
                    $sql = "insert into member_asset_mortgage_image(asset_mortgage_id,image_path) values ";
                    foreach ($item['asset_images'] as $image_path) {
                        if (!$image_path) continue;
                        $temp = "('" . $mortgage_row->uid . "','$image_path')";
                        $asset_images[] = $temp;
                    }
                    if (count($asset_images)) {
                        $sql .= implode(',', $asset_images);
                        $insert = $m_mortgage->conn->execute($sql);
                        if (!$insert->STS) {
                            return new result(false, 'Insert asset mortgage image fail.', null, errorCodesEnum::DB_ERROR);
                        }
                    }
                }
                if ($item['is_received']) {
                    //更新资产表的hold_state字段
                    $asset_row = $m_asset->getRow($mortgage_row->member_asset_id);
                    if ($asset_row) {
                        $asset_row->hold_state = 1;
                        $asset_row->update_time = Now();
                        $ret_update_asset = $asset_row->update();
                        if (!$ret_update_asset->STS) {
                            return $ret_update_asset;
                        }
                    }

                    $ret_storage = member_assetsClass::insertStorageFlow(array(
                        "member_asset_id" => $mortgage_row->member_asset_id,
                        "mortgage_id" => $mortgage_row->uid,
                        "contract_no" => $row->contract_no,
                        "to_branch_id" => $args['branch_id'],
                        "to_branch_name" => $args['branch_name'],
                        "to_operator_id" => $args['operator_id'],
                        "to_operator_name" => $objUser->user_name,
                        "remark" => "Received From Client",
                        "creator_id" => $args['operator_id'],
                        "creator_name" => $args['operator_name'],
                        "flow_type" => assetStorageFlowType::RECEIVED_FROM_CLIENT
                    ));
                    if (!$ret_storage->STS) {
                        return $ret_storage;
                    }
                }
                $ret_update = $mortgage_row->update();
                if (!$ret_update->STS) {
                    return $ret_update;
                }
            }
        }
        $chk_row = $m_mortgage->find(array("contract_no" => $row->contract_no, "is_received" => 0));
        if (!$chk_row) {
            $row->state = authorizedContractStateEnum::COMPLETE;
        }
        $ret = $row->update();
        return $ret;
    }

    /**  取回抵押物品
     * @param $user_id
     * @param $grant_id
     * @param $member_id
     * @param $assets_list
     *          array(1,3,5,8)
     * @param $member_image
     * @param array $contract_images
     *          array('a.png','b.png')
     * @return result
     */
    public static function signWithdrawMortgagedContract($args)
    {

        $asset_id = $args['asset_id'];
        $operator_id = $args['user_id'];
        $member_image = $args['member_image'];
        $contract_images = $args['contract_images'];

        if (empty($asset_id)) {
            return new result(false, 'No asset param.', null, errorCodesEnum::INVALID_PARAM);
        }

        // 先判断参数
        if ($args['is_agent']) {
            if (!$args['agent_id_sn'] || !$args['agent_id1'] || !$args['agent_id2']
                || !$args['authorization_cert'] || !$args['mortgage_cert']
            ) {
                return new result(false, 'Agent info is incomplete.');
            }
        }

        //先获取资产对应的抵押记录
        $m_member_assets = new member_assetsModel();
        $m_grant_asset = new member_credit_grant_assetsModel();
        $m_asset_mortgage = new member_asset_mortgageModel();

        $row_mortgage = $m_asset_mortgage->orderBy("uid desc")->getRow(array("member_asset_id" => $asset_id));
        if (!$row_mortgage || $row_mortgage->mortgage_type != 1 || $row_mortgage->is_received != 1) {
            return new result(false, "No Mortgaged-Record Before");
        }
        $grant_id = $row_mortgage->grant_id;
        //获取资产记录
        $row_asset = $m_member_assets->getRow(array("uid" => $asset_id));
        if (!$row_asset) {
            return new result(false, "No Asset-Record Found");
        }
        $member_id = $row_asset->member_id;
        //获取授权合同
//        $row_grant = (new member_credit_grantModel())->getRow($grant_id);
//        if (!$row_grant || $row_grant->state != authorizedContractStateEnum::COMPLETE) {
//            return new result(false, "Invalid Authorize-Contract");
//        }

        $objUser = new objectUserClass($operator_id);
        // 插入合同表
        $m_auth_contract = new member_authorized_contractModel();
        $conn = $m_auth_contract->conn;
        $contract_no = $m_auth_contract->generateAuthorizedContractSn();
        $auth_contract = $m_auth_contract->newRow();
        $auth_contract->contract_no = $contract_no;
        $auth_contract->contract_type = -1;
        $auth_contract->grant_credit_id = $grant_id;
        $auth_contract->member_id = $member_id;
        $auth_contract->member_img = $member_image;
        $auth_contract->fee = 0;
        $auth_contract->officer_id = $operator_id;
        $auth_contract->officer_name = $objUser->user_name;
        $auth_contract->create_time = Now();
        $auth_contract->is_paid = 1;
        $auth_contract->branch_id = $objUser->branch_id;
        $auth_contract->is_agent = intval($args['is_agent']);
        if ($args['is_agent']) {
            $auth_contract->agent_name = $args['agent_name'];
            $auth_contract->agent_id_sn = $args['agent_id_sn'];
            $auth_contract->agent_id1 = $args['agent_id1'];
            $auth_contract->agent_id2 = $args['agent_id2'];
            $auth_contract->authorization_cert = $args['authorization_cert'];
            $auth_contract->mortgage_cert = $args['mortgage_cert'];
        }
        $insert = $auth_contract->insert();
        if (!$insert->STS) {
            return new result(false, 'Insert auth contract fail.', null, errorCodesEnum::DB_ERROR);
        }

        // 插入合同的纸质文件图片
        if (!empty($contract_images)) {
            $image_arr = array();
            $sql = "insert into member_authorized_contract_image(authorized_contract_id,image_path) values ";
            foreach ($contract_images as $image_path) {
                $temp = "('" . $auth_contract->uid . "','$image_path')";
                $image_arr[] = $temp;
            }
            $sql .= implode(',', $image_arr);
            $insert = $m_auth_contract->conn->execute($sql);
            if (!$insert->STS) {
                return new result(false, 'Insert auth contract image fail.', null, errorCodesEnum::DB_ERROR);
            }
        }

        $member_credit = (new member_creditModel())->getRow(array(
            'member_id' => $member_id
        ));
        $credit_balance = 0;
        if ($member_credit) {
            $credit_balance = $member_credit->credit_balance;
        }
        $m_credit_grant_assets = new member_credit_grant_assetsModel();
        $row_grant_credit = $m_credit_grant_assets->getRow(array(
            "grant_id" => $grant_id,
            "member_asset_id" => $asset_id,
            "is_mortgage" => 1
        ));

        $take_credit = $row_grant_credit->credit ?: 0;
        if ($take_credit > $credit_balance) {
            $cut_credit = $credit_balance;
        } else {
            $cut_credit = $take_credit;
        }
        // 处理物品的赎回
        // 插入日志
        $new_row_mortgage = $m_asset_mortgage->newRow();
        $new_row_mortgage->grant_id = $grant_id;
        $new_row_mortgage->contract_type = assetMortgageContractTypeEnum::CREDIT_LOAN;
        $new_row_mortgage->contract_no = $auth_contract->contract_no;
        $new_row_mortgage->member_asset_id = $asset_id;
        $new_row_mortgage->mortgage_type = -1;  // 赎回
        $new_row_mortgage->credit = $cut_credit;
        $new_row_mortgage->operator_id = $objUser->user_id;
        $new_row_mortgage->operator_name = $objUser->user_name;
        $new_row_mortgage->operator_time = Now();
        $new_row_mortgage->ref_id = $row_mortgage->uid;
        $new_row_mortgage->branch_id = $objUser->branch_id;
        $insert = $new_row_mortgage->insert();
        if (!$insert->STS) {
            return new result(false, $insert->MSG, null, errorCodesEnum::DB_ERROR);
        }

        // 更新授信资产记录
        $row_grant_credit->is_mortgage = 0;
        $row_grant_credit->asset_mortgage_id = 0;
        $ret_update = $row_grant_credit->update();
        if (!$ret_update->STS) {
            return $ret_update;
        }

        // 更新资产记录
        $row_asset->mortgage_state = 0;
        $row_asset->hold_state = 0;
        $row_asset->update_time = Now();
        $ret_update = $row_asset->update();
        if (!$ret_update->STS) {
            return $ret_update;
        }


        //更新客人的信用记录
        $remark = 'Mortgage assets:' . $row_asset->asset_name;
        $ret = member_creditClass::cutCreditForMemberByCategoryId($row_grant_credit['member_credit_category_id'], $row_grant_credit['credit'], $remark, creditEventTypeEnum::GRANT, $auth_contract->uid);
        if (!$ret->STS) {
            return $ret;
        }


        //插入库存记录
        $ret = member_assetsClass::insertStorageFlow(array(
            "member_asset_id" => $asset_id,
            "mortgage_id" => $new_row_mortgage->uid,
            "contract_no" => $auth_contract->contract_no,
            "from_operator_id" => $objUser->user_id,
            "from_operator_name" => $objUser->user_name,
            "from_branch_id" => $objUser->branch_id,
            "from_branch_name" => $objUser->branch_name,
            "flow_type" => assetStorageFlowType::WITHDRAW_BY_CLIENT,
            "creator_id" => $objUser->user_id,
            "creator_name" => $objUser->user_name
        ));
        return $ret;
    }

    public function getMyVoteList($param)
    {
        $user_id = intval($param['user_id']);
        $pageNumber = intval($param['pageNumber']) ?: 1;
        $pageSize = intval($param['pageSize']) ?: 20;
        $state = intval($param['state']);

        $where = " where mcga.attender_id = $user_id";
        if ($state == 10) {
            $time = Now();
            $where .= " AND mcga.vote_result = 0 AND mcg.state = " . qstr(commonApproveStateEnum::APPROVING) . "AND mcg.vote_expire_time > " . qstr($time);
        }

        if ($state == 0) {
            $time = Now();
            $where .= " AND mcga.vote_result = 0 AND (mcg.state = 20 OR mcg.state = 110 OR mcg.vote_expire_time < " . qstr($time) . ")";
        }

        if ($state == 100) {
            $where .= " AND (mcga.vote_result = 100 OR mcga.vote_result = 10)";
        }

        $r = new ormReader();
        $sql = "SELECT mcga.vote_result my_vote_result, mcga.update_time my_update_time, mcg.*,cm.obj_guid,cm.display_name, cm.login_code FROM member_credit_grant_attender mcga"
            . " INNER JOIN member_credit_grant mcg ON mcga.grant_id = mcg.uid"
            . " INNER JOIN client_member cm ON mcg.member_id = cm.uid"
            . " $where ORDER BY mcg.grant_time DESC";

        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "state" => $state
        );
    }

    public static function isAuthorisedContractCanCancel($auth_contract)
    {
        $member_id = $auth_contract['member_id'];
        $last_grant = self::getMemberLastGrantInfo($member_id);
        $grant_id = intval($last_grant['uid']);
        // 不是最新的授信，不可删除
        if( $grant_id != $auth_contract['grant_credit_id'] ){
            return 0;
        }
        // 是否已经贷款了，贷款不能取消
        $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
        $account_id = intval($loan_account['uid']);
        $r = new ormReader();
        $sql = "select count(*) cnt from loan_contract where account_id='$account_id'
         and credit_grant_id='$grant_id' and state>=".qstr(loanContractStateEnum::PENDING_DISBURSE);
        $num = $r->getOne($sql);
        if( $num > 0 ){
            return 0;
        }

        return 1;


    }
}