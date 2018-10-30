<?php

class loan_committeeControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('credit_committee,certification,operator,console');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Home");
        Tpl::setDir("loan_committee");
    }

    /**
     * bm提交审核授信申请
     */
    public function approveCreditApplicationOp()
    {
        Tpl::showPage('credit.application');
    }

    /**
     * 获取bm申请
     * @param $p
     * @return array
     */
    public function getCreditGrantListOp($p)
    {

        $class_credit_grant = new member_credit_grantClass();
        if ($p['verify_state'] == 'new') {
            return $class_credit_grant->getBmCreditSuggestList($p);
        } elseif ($p['verify_state'] == 'rejected') {
            return $class_credit_grant->getBmCreditSuggestList($p);
        } else {
            return $class_credit_grant->getCreditGrant($p);
        }
    }

    /**
     * approve页面
     */
    public function showRequestCreditOp($uid)
    {
        //输出可选投票人
        $m_group = new um_user_groupModel();
        $committee_member = $m_group->getListByGroupKey(userGroupKeyEnum::GRANT_CREDIT_COMMITTEE);//$m_um_user->select(array('user_position' => userPositionEnum::COMMITTEE_MEMBER, 'user_status' => 1));
        $committee_member = resetArrayKey($committee_member, "user_id");
        $arr_voter = array_keys($committee_member);
        if (!in_array($this->user_id, $arr_voter)) {
            showMessage("You are not committee member, not allowed to operate this page");
        }
        Tpl::output('committee_member', $committee_member);


        if (!intval($uid)) {
            $uid = intval($_GET['uid']);
        }

        //****************输出请求信息

        $class_credit_grant = new member_credit_grantClass();
        $rt = $class_credit_grant->getBmCreditSuggestDetailById($uid);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }
        $data = $rt->DATA;
        $member_id = $data['member_id'];
        Tpl::output('bm_suggest', $data['bm_suggest']);
        //****************输出 co极其建议的列表
        $co_list = memberClass::getMemberCreditOfficerList($member_id, true);
        $co_list = resetArrayKey($co_list, "officer_id");
        $co_suggest_list = array();
        foreach ($co_list as $co_id => $co) {
            $co_suggest_list[$co_id] = credit_researchClass::getLastSuggestCreditByOfficerId($member_id, $co_id);
        }
        Tpl::output('co_list', $co_list);
        Tpl::output('co_suggest_list', $co_suggest_list);

        //****************输出客人信息
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $member_id));
        if (!$client_info) {
            showMessage('Invalid Id!');
        }
        $loan_account_info = (new loan_accountModel())->find(array('obj_guid'=>$client_info['obj_guid']));
        Tpl::output('client_loan_account_info',$loan_account_info);
        Tpl::output('client_info', $client_info);
        Tpl::output('member_id', $member_id);
        Tpl::output('source', 'credit_committee');

        $m_um_user = M('um_user');
        //$committee_member = $m_um_user->select(array('user_position' => userPositionEnum::COMMITTEE_MEMBER, 'user_status' => 1));

        //操作信息
        $operator_id = $data['bm_suggest']['operator_id'];
        $operator = new objectUserClass($data['bm_suggest']['operator_id']);
        $operator_position = $operator->position;
        Tpl::output("operator_name", $operator->user_name);
        Tpl::output("operator_code", $operator->user_code);
        Tpl::output("branch_name", $operator->branch_name);


        $analysis = credit_researchClass::getSystemAnalysisCreditOfMember($member_id, $operator_id, $operator_position);
        Tpl::output("analysis", $analysis);
        $member_asset = $analysis['suggest']['increase'];
        if (is_array($member_asset)) {
            $member_asset = resetArrayKey($member_asset, "uid");
        }
        Tpl::output('member_assets', $member_asset);

        //*****输出现在的credit-category
        $prod_list = loan_categoryClass::getMemberCreditCategoryList($member_id);
        Tpl::output("credit_category", $prod_list);


        //获取最新申请，没有申请的情况下不能创建suggest
        $last_request = credit_researchClass::getClientRequestCredit($member_id);
        if (!$last_request || $last_request['state'] != creditRequestStateEnum::CREATE) {
            //Tpl::showPage("suggest.credit.invalid");

        } else {
            $client_relative = $last_request['relative_list'];
            Tpl::output("client_relative", $client_relative);
        }

        $analysis2=creditFlowClass::getSystemAnalysisCreditOfMember($member_id, $operator_id, $operator_position);
        Tpl::output("new_analysis",$analysis2);


        Tpl::showPage('credit.application.detail');
    }


    public function showAssetMapOp()
    {
        $param = array_merge($_GET, $_POST);
        $asset_id = $param['asset_id'];
        $asset_detail = (new member_assetsModel())->find(array(
            'uid' => $asset_id
        ));
        $map_detail = array(
            'coord_x' => $asset_detail['coord_x'],
            'coord_y' => $asset_detail['coord_y'],
        );

        Tpl::output('map_title', 'Asset Location');
        Tpl::output('map_detail', $map_detail);
        Tpl::showPage('asset.map.page');
    }

    /**
     * BM提交历史
     */
    public function getRequestCreditHistoryOp()
    {
        $uid = intval($_GET['uid']);
        $m_member_credit_suggest = M('member_credit_suggest');
        $credit_suggest = $m_member_credit_suggest->find(array('uid' => $uid));
        if (!$credit_suggest) {
            showMessage('Invalid Id!');
        }

        $member_id = $credit_suggest['member_id'];
        $branch_id = $credit_suggest['branch_id'];
        $client_info = M('client_member')->find(array('uid' => $member_id));
        Tpl::output("client_info", $client_info);

        $branch_info = M('site_branch')->find(array('uid' => $branch_id));
        Tpl::output("branch_info", $branch_info);

        $m_member_credit_suggest = M('member_credit_suggest');
        $credit_suggest = $m_member_credit_suggest->orderBy('update_time DESC')->select(array('member_id' => $member_id, 'branch_id' => $branch_id, 'request_type' => 1, 'state' => array('gt', 0)));
        foreach ($credit_suggest as $key => $val) {
            $credit_suggest[$key]['suggest_detail_list'] = $m_member_credit_suggest->getSuggestDetailBySuggestId($val['uid']);
            $credit_suggest[$key]['suggest_rate'] = $m_member_credit_suggest->getSuggestRateBySuggestId($val['uid']);
        }
        Tpl::output('credit_suggest', $credit_suggest);

        $credit_loan = credit_loanClass::getProductInfo();
        $prod_list = loan_productClass::getActiveSubProductListById($credit_loan['uid']);
        foreach ($prod_list as $k => $v) {
            $rate = loan_productCLass::getMinMonthlyRate($v['uid'], 'max');
            $prod_list[$k]['max_rate_mortgage'] = $rate;
        }
        $prod_list = resetArrayKey($prod_list, "uid");
        Tpl::output("product_list", $prod_list);

        Tpl::output("uid", $uid);
        Tpl::showPage('credit.application.history');
    }

    /**
     * 提交投票授权信用
     */
    public function commitCreditApplicationOp()
    {
        $param = array_merge(array(), $_GET, $_POST);

        $type = intval($param['type']);
        $param['operator_id'] = $this->user_id;
        $param['operator_name'] = $this->user_name;
        if ($type == 1) {

            //在没选择投票人的时候把自己加上
            $committee_member = $param['committee_member'];
            if (!count($committee_member)) {
                $param['committee_member'] = array($this->user_id);
            } else {
                if (!in_array($this->user_id, $committee_member)) {
                    $param['committee_member'][] = $this->user_id;
                }
            }
            $committee_member = $param['committee_member'];

            $suggest_id = $param['suggest_id'];
            $default_item = (new member_credit_suggestModel())->find(array("uid" => $suggest_id));
            if (!$default_item) {
                showMessage("Invalid Parameter:No Request Found");
            }
            $default_item['remark'] = $param['remark'];
            $param = array_merge($param, $default_item);


            $rt = creditFlowClass::ApproveBMSuggestCreditToVote($param);
            if ($rt->STS) {
                $data = $rt->DATA;
                if (count($committee_member) == 1) {
                    showMessage("Approved Success!", getUrl('loan_committee', 'approveCreditApplication', array(), false, BACK_OFFICE_SITE_URL));
                    //说明不需要投票
                    /*
                    if ($param['is_fast_grant']) {
                        showMessage("Approved Success!", getUrl('loan_committee', 'fastGrantCredit', array(), false, BACK_OFFICE_SITE_URL));
                    } else {
                        showMessage("Approved Success!", getUrl('loan_committee', 'approveCreditApplication', array(), false, BACK_OFFICE_SITE_URL));
                    }
                    */

                } else {
                    $this->voteCreditApplicationOp($data['uid']);
                }

            } else {
                if (!$param['is_fast_grant']) {
                    showMessage($rt->MSG, getUrl('loan_committee', 'approveCreditApplication', array(), false, BACK_OFFICE_SITE_URL));
                } else {
                    showMessage($rt->MSG, getUrl('loan_committee', 'fastGrantCredit', array(), false, BACK_OFFICE_SITE_URL));
                }
            }
        } else {
            showMessage('Not allowed.');
            /* $rt = creditFlowClass::rejectCreditApplication($param);
             if ($rt->STS) {
                 showMessage($rt->MSG, getUrl('loan_committee', 'approveCreditApplication', array(), false, BACK_OFFICE_SITE_URL));
             } else {
                 showMessage($rt->MSG);
             }*/
        }
    }

    /**
     * 投票页面
     */
    public function voteCreditApplicationOp($uid)
    {
        if (!intval($uid)) {
            $uid = intval($_GET['uid']);
        }

        //****************输出请求信息

        $class_credit_grant = new member_credit_grantClass();
        $credit_grant = $class_credit_grant->getCreditGrantById($uid);
        if (!$credit_grant) {
            showMessage("Invalid Parameter:No Grant Record Found");
        }

        $member_id = $credit_grant['member_id'];
        $m_suggest = new member_credit_suggestModel();
        $bm_suggest = $m_suggest->find(array("uid" => $credit_grant['credit_suggest_id']));
        $credit_grant['request_time'] = $bm_suggest['request_time'];
        Tpl::output('credit_grant', $credit_grant);

        //操作信息
        $operator_id = $bm_suggest['operator_id'];
        $operator = new objectUserClass($operator_id);
        $operator_position = $operator->position;
        Tpl::output("operator_name", $operator->user_name);
        Tpl::output("operator_code", $operator->user_code);
        Tpl::output("branch_name", $operator->branch_name);


        //****************输出 co极其建议的列表
        $co_list = memberClass::getMemberCreditOfficerList($member_id, true);
        $co_list = resetArrayKey($co_list, "officer_id");
        $co_suggest_list = array();
        foreach ($co_list as $co_id => $co) {
            $co_suggest_list[$co_id] = credit_researchClass::getLastSuggestCreditByOfficerId($member_id, $co_id);
        }
        Tpl::output('co_list', $co_list);
        Tpl::output('co_suggest_list', $co_suggest_list);

        //****************输出客人信息
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $member_id));
        if (!$client_info) {
            showMessage('Invalid Id!');
        }
        $loan_account_info = (new loan_accountModel())->find(array('obj_guid'=>$client_info['obj_guid']));
        Tpl::output('client_loan_account_info',$loan_account_info);
        Tpl::output('client_info', $client_info);
        Tpl::output('member_id', $member_id);
        Tpl::output('source', 'credit_committee');

        $analysis = credit_researchClass::getSystemAnalysisCreditOfMember($member_id, $operator_id, $operator_position,$uid);
        Tpl::output("analysis", $analysis);
        $member_asset = $analysis['suggest']['increase'];
        if (is_array($member_asset)) {
            $member_asset = resetArrayKey($member_asset, "uid");
        }
        Tpl::output('member_assets', $member_asset);

        //*****输出现在的credit-category
        $prod_list = loan_categoryClass::getMemberCreditCategoryList($member_id);
        Tpl::output("credit_category", $prod_list);


        //获取最新申请，没有申请的情况下不能创建suggest
        $last_request = credit_researchClass::getClientRequestCredit($member_id);
        if (!$last_request || $last_request['state'] != creditRequestStateEnum::CREATE) {
            //Tpl::showPage("suggest.credit.invalid");

        } else {
            $client_relative = $last_request['relative_list'];
            Tpl::output("client_relative", $client_relative);
        }

        //$vote_url = getUrl('committee_vote', 'committeeVoteCreditApplication', array('uid' => $uid), false, BACK_OFFICE_SITE_URL);
        $vote_url = getUrl('credit_vote', 'votePage', array('uid' => $uid), false, WAP_OPERATOR_SITE_URL);
        Tpl::output('vote_url', $vote_url);

        $member_credit_grant_vote = $class_credit_grant->getVoteCommitteeMember($uid);
        Tpl::output('member_credit_grant_vote', $member_credit_grant_vote);

        $analysis2=creditFlowClass::getSystemAnalysisCreditOfMember($member_id, $operator_id, $operator_position,$uid);
        Tpl::output("new_analysis",$analysis2);


        Tpl::showPage('credit.application.vote');
    }


    /**
     * 授权信息
     */
    public function showCreditGrantOp()
    {
        $uid = intval($_GET['uid']);
        $class_credit_grant = new member_credit_grantClass();
        $credit_grant = $class_credit_grant->getCreditGrantById($uid);
        Tpl::output('credit_grant', $credit_grant);



        $credit_request = credit_researchClass::getClientCreditRequestDetailById($credit_grant['credit_request_id']);
        Tpl::output('client_credit_request',$credit_request);
        $client_relative = $credit_request['relative_list'];
        Tpl::output("client_relative", $client_relative);

        $member_id = $credit_grant['member_id'];
        $client_info = M('client_member')->find(array('uid' => $member_id));
        Tpl::output('client_info', $client_info);

        Tpl::output("member_assets", member_assetsClass::getAllAssetListOfMember($member_id));

        $credit_category = loan_categoryClass::getMemberCreditCategoryList($member_id);
        Tpl::output("credit_category", $credit_category);


        $member_credit_grant_vote = $class_credit_grant->getVoteCommitteeMember($uid);
        Tpl::output('member_credit_grant_vote', $member_credit_grant_vote);

//        $prod_list = memberClass::getMemberCreditLoanProduct($member_id);
//        Tpl::output("product_list", $prod_list);

        //*****输出现在的credit-category
        $prod_list = loan_categoryClass::getMemberCreditCategoryList($member_id);
        Tpl::output("credit_category", $prod_list);

        Tpl::showPage('show.credit.grant');
    }

    /**
     * 投票二维码url
     */
    public function getQrCodeOp()
    {
        $url = $_GET['url'];
        qrcodeClass::generateQrCodeImage($url);
    }

    /**
     * 获取投票结果
     * @param $p
     * @return array
     */
    public function getVoteResultOp($p)
    {
        $grant_id = intval($p['suggest_id']);
        $class_credit_grant = new member_credit_grantClass();
        $member_credit_grant_vote = $class_credit_grant->getVoteCommitteeMember($grant_id);
        return array(
            "sts" => true,
            "data" => $member_credit_grant_vote,
        );
    }

    /**
     * 完成投票统计
     * @param $p
     * @return ormResult|result
     */
    public function completeVoteCreditApplicationOp($p)
    {
        $uid = intval($p['uid']);
        $class_credit_grant = new member_credit_grantClass();
        $rt = $class_credit_grant->completeVote($uid);
        return $rt;
    }

    /**
     * 重置本次投票
     * @param $p
     * @return mixed
     */
    public function resetVoteTimerOp($p)
    {
        $uid = intval($p['uid']);
        $class_credit_grant = new member_credit_grantClass();
        $rt = $class_credit_grant->resetVoteTimer($uid);
        return $rt;
    }

    /**
     * 重新编辑
     */
    public function reeditCreditApplicationOp()
    {
        $uid = intval($_GET['uid']);
        $class_credit_grant = new member_credit_grantClass();
        $rt_1 = $class_credit_grant->cancelCreditGrant($uid);
        if (!$rt_1->STS) {
            showMessage($rt_1->MSG);
        }
        $suggest_id = $rt_1->DATA;
        $this->showRequestCreditOp($suggest_id);
    }

    /**
     * 快速授信
     */
    public function fastGrantCreditOp()
    {
        $work_type = (new workTypeEnum())->Dictionary();
        Tpl::output('work_type', $work_type);
        $m_member_grade = M('member_grade');
        $grade_list = $m_member_grade->select(array('uid' => array('>=', 0)));
        Tpl::output('grade_list', $grade_list);
        Tpl::showPage('fast.client');
    }

    /**
     * 获取会员列表
     * @param $p
     * @return array
     */
    public function getClientListOp($p)
    {
        $search_text = trim($p['search_text']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $filter = array(
            'work_type' => $p['work_type'],
            'grade_id' => $p['grade_id'],
        );

        $m_client_member = new memberModel();
        $rt = $m_client_member->searchMemberListByFreeText($search_text, $pageNumber, $pageSize, $filter);
        $data = $rt->DATA;
        $rows = $data['rows'];
        $total = $data['total'];
        $pageTotal = $data['page_total'];

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 快速授信
     */
    public function fastGrantCreditDetailOp()
    {
        $m_group = new um_user_groupModel();
        $committee_member = $m_group->getListByGroupKey(userGroupKeyEnum::FAST_CREDIT_COMMITTEE);//$m_um_user->select(array('user_position' => userPositionEnum::COMMITTEE_MEMBER, 'user_status' => 1));
        $committee_member = resetArrayKey($committee_member, "user_id");
        $arr_voter = array_keys($committee_member);
        if (!in_array($this->user_id, $arr_voter)) {
            showMessage("You are not committee member, not allowed to operate this page");
        }


        $member_id = intval($_GET['member_id']);
        //会员信息
        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid Id!');
        }
        Tpl::output('client_info', $client_info);
        //获取有没有签订合同的授信
        /*
        $active_grant=memberClass::getMemberNewGrantCredit($member_id);
        if($active_grant){
            showMessage("Not allow to grant new credit for this client:the last grant-credit is not authorized.");
        }
        */

        $rate_set = global_settingClass::getCreditGrantRateAndDefaultInterest();
        Tpl::output('rate_set', $rate_set);

        Tpl::output('committee_member', $committee_member);

        Tpl::output('member_id', $member_id);
        Tpl::output('source', 'credit_committee');

        $analysis = credit_researchClass::getSystemAnalysisCreditOfMember($member_id, 0, null);
        Tpl::output("analysis", $analysis);
        $member_asset = $analysis['suggest']['increase'];
        if (is_array($member_asset)) {
            $member_asset = resetArrayKey($member_asset, "uid");
        }
        Tpl::output('member_assets', $member_asset);

        $prod_list = memberClass::getMemberCreditLoanProduct($member_id);
        Tpl::output("product_list", $prod_list);

        //获取最新申请，没有申请的情况下不能创建suggest
        $last_request = credit_researchClass::getClientRequestCredit($member_id);
        if ($last_request && $last_request['state'] == creditRequestStateEnum::CREATE) {
            $client_relative = $last_request['relative_list'];
            Tpl::output("client_relative", $client_relative);
        }

        $product_package = loan_productClass::getProductPackageList();
        Tpl::output("package_list", $product_package);

        Tpl::showPage('fast.grant.credit.detail');
    }

    /**
     * 减少额度
     */
    public function cutCreditOp()
    {
        $work_type = (new workTypeEnum())->Dictionary();
        Tpl::output('work_type', $work_type);
        $m_member_grade = M('member_grade');
        $grade_list = $m_member_grade->select(array('uid' => array('>=', 0)));
        Tpl::output('grade_list', $grade_list);
        Tpl::showPage('cut.credit');
    }

    /**
     * 修改会员额度
     */
    public function editMemberCreditOp()
    {
        $member_id = intval($_GET['member_id']);
        //会员信息
        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid Id!');
        }
        Tpl::output('client_info', $client_info);
        Tpl::output('member_id', $member_id);
        Tpl::output('member_id', $member_id);
        Tpl::showPage('cut.member.credit');
    }

    /**
     * 提交会员额度
     */
    public function submitCutCreditOp()
    {
        $member_id = intval($_POST['member_id']);
        $amount = intval($_POST['amount']);
        $remark = trim($_POST['remark']);

        $rt = member_creditClass::decreaseMemberCredit(creditEventTypeEnum::CUT_CREDIT, $member_id, $amount, $remark);
        if ($rt->STS) {
            showMessage('Cut Successful!', getUrl('loan_committee', 'cutCredit', array(), false, BACK_OFFICE_SITE_URL));
        } else {
            showMessage($rt->MSG);
        }
    }

    /**
     * 提前还款申请审核
     */
    public   function approvePrepaymentRequestOp()
    {
        Tpl::showPage('prepayment.request');
    }

    /**
     * 获取提前还款申请列表
     * @param $p
     * @return array
     */
    public function getLoanPrepaymentApplyListOp($p)
    {
        $pageNumber = intval($p['pageNumber']);
        $pageSize = intval($p['pageSize']);
        $filter = array(
            'search_text' => trim($p['search_text']),
            'state' => intval($p['state']),
        );

        $class_loan_contract = new loan_contractClass();
        $data = $class_loan_contract->getLoanPrepaymentApplyList($pageNumber, $pageSize, $filter);
        $data['sts'] = true;
        $data['state'] = intval($p['state']);
        $data['user_id'] = $this->user_id;
        return $data;
    }

    /**
     * 提前还款详情
     */
    public function showRepaymentRequestOp()
    {
        $uid = intval($_GET['uid']);
        $class_loan_contract = new loan_contractClass();
        $request_detail = $class_loan_contract->getRepaymentRequestDetail($uid);
        Tpl::output('request_detail', $request_detail);
        Tpl::showPage('prepayment.request.detail');
    }

    /**
     * 提前还款详情
     */
    public function handlerRepaymentRequestOp()
    {
        $uid = intval($_GET['uid']);
        $class_loan_contract = new loan_contractClass();
        $rt = $class_loan_contract->getRepaymentRequestTask($uid, $this->user_id);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }

        $request_detail = $class_loan_contract->getRepaymentRequestDetail($uid);
        Tpl::output('request_detail', $request_detail);
        Tpl::output('is_handle', true);
        Tpl::showPage('prepayment.request.detail');
    }

    /**
     * 审核提前还款申请
     * @param $p
     * @return result
     */
    public function auditPrepaymentRequestOp($p)
    {
        $uid = intval($p['uid']);
        $audit_remark = trim($p['audit_remark']);
        $state = intval($p['state']);
        $auditor_id = $this->user_id;

        $class_loan_contract = new loan_contractClass();
        $rt = $class_loan_contract->auditPrepaymentRequest($uid, $state, $audit_remark, $auditor_id);
        return $rt;
    }

    /**
     * 取消任务
     * @param $p
     * @return result
     */
    public function abandonPrepaymentRequestTaskOp($p)
    {
        $uid = intval($p['uid']);
        $auditor_id = $this->user_id;
        $class_loan_contract = new loan_contractClass();
        $rt = $class_loan_contract->abandonPrepaymentRequestTask($uid, $auditor_id);
        return $rt;
    }

    /**
     * 提前罚金申请审核
     */
    public function approvePenaltyRequestOp()
    {
        Tpl::showPage('penalty.request');
    }

    /**
     * 获取罚金申请列表
     * @param $p
     * @return array
     */
    public function getPenaltyApplyListOp($p)
    {
        $pageNumber = intval($p['pageNumber']);
        $pageSize = intval($p['pageSize']);
        $filter = array(
            'search_text' => trim($p['search_text']),
            'state' => intval($p['state']),
        );

        $class_member_penalty = new member_penaltyClass();
        $data = $class_member_penalty->getPenaltyApplyList($pageNumber, $pageSize, $filter);
        $data['sts'] = true;
        $data['state'] = intval($p['state']);
        $data['user_id'] = $this->user_id;
        return $data;
    }


    /**
     * 罚金详情
     */
    public function showPenaltyRequestOp()
    {
        $uid = intval($_GET['uid']);
        $class_member_penalty = new member_penaltyClass();
        $request_detail = $class_member_penalty->getPenaltyRequestDetail($uid);
        Tpl::output('request_detail', $request_detail);
        Tpl::showPage('penalty.request.detail');
    }

    /**
     * 获取罚金任务
     */
    public function handlePenaltyRequestOp()
    {
        $uid = intval($_GET['uid']);
        $class_member_penalty = new member_penaltyClass();
        $rt = $class_member_penalty->getPenaltyRequestTask($uid, $this->user_id);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }

        $request_detail = $class_member_penalty->getPenaltyRequestDetail($uid);
        Tpl::output('request_detail', $request_detail);
        Tpl::output('is_handle', true);
        Tpl::showPage('penalty.request.detail');
    }

    /**
     * 审核罚金申请
     * @param $p
     * @return result
     */
    public function auditPenaltyRequestOp($p)
    {
        $uid = intval($p['uid']);
        $audit_remark = trim($p['audit_remark']);
        $state = intval($p['state']);
        $auditor_id = $this->user_id;

        $class_member_penalty = new member_penaltyClass();
        $rt = $class_member_penalty->auditPenaltyRequest($uid, $state, $audit_remark, $auditor_id);
        return $rt;
    }

    /**
     * 取消罚金任务
     * @param $p
     * @return result
     */
    public function abandonPenaltyRequestTaskOp($p)
    {
        $uid = intval($p['uid']);
        $auditor_id = $this->user_id;
        $class_member_penalty = new member_penaltyClass();
        $rt = $class_member_penalty->abandonPenaltyRequestTask($uid, $auditor_id);
        return $rt;
    }

    /**
     * 核销任务申请
     */
    public function approveWrittenOffRequestOp()
    {
        Tpl::showPage('written.off.request');
    }

    /**
     * 获取核销任务申请列表
     * @param $p
     * @return array
     */
    public function getWrittenOffRequestListOp($p)
    {
        $pageNumber = intval($p['pageNumber']);
        $pageSize = intval($p['pageSize']);
        $filter = array(
            'search_text' => trim($p['search_text']),
            'state' => intval($p['state']),
        );

        $m_loan_written_of = new loan_writtenoffModel();
        $data = $m_loan_written_of->getWrittenOffList($pageNumber, $pageSize, $filter);
        $data['sts'] = true;
        $data['state'] = intval($p['state']);
        $data['user_id'] = $this->user_id;
        return $data;
    }

    /**
     * 核销审核
     * @param $uid
     */
    public function handleWrittenOffOp($uid)
    {
        $m_group = new um_user_groupModel();
        $committee_member = $m_group->getListByGroupKey(userGroupKeyEnum::WRITTEN_OFF_LOAN_COMMITTEE);//$m_um_user->select(array('user_position' => userPositionEnum::COMMITTEE_MEMBER, 'user_status' => 1));
        $committee_member = resetArrayKey($committee_member, "user_id");
        $arr_voter = array_keys($committee_member);
        if (!in_array($this->user_id, $arr_voter)) {
            showMessage("You are not committee member, not allowed to operate this page");
        }

        if (!intval($uid)) {
            $uid = intval($_GET['uid']);
        }
        $m_loan_written_of = new loan_writtenoffModel();
        $data = $m_loan_written_of->getWrittenOffDetail($uid);
        if (!$data) {
            showMessage('Invalid Id.');
        }
        Tpl::output('written_off', $data);

        $member_id = $data['member_id'];
        //会员信息
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $member_id));
        Tpl::output('client_info', $client_info);

        // $m_um_user = M('um_user');
        //$committee_member = $m_um_user->select(array('user_position' => userPositionEnum::COMMITTEE_MEMBER, 'user_status' => 1));
        Tpl::output('committee_member', $committee_member);

        Tpl::showPage('written.off.detail');
    }

    /**
     * 提交核销进行投票
     */
    public function commitWrittenOffOp()
    {
        $param = array_merge(array(), $_GET, $_POST);
        $param['operator_id'] = $this->user_id;
        $param['operator_name'] = $this->user_name;
        $committee_member = $param['committee_member'];
        //在没选择投票人的时候把自己加上
        if (!count($committee_member)) {
            $param['committee_member'] = array($this->user_id);
        } else {
            if (!in_array($this->user_id, $committee_member)) {
                $param['committee_member'][] = $this->user_id;
            }
        }
        $committee_member = $param['committee_member'];
        $class_loan_written_off = new loan_written_offClass();
        $rt = $class_loan_written_off->commitWrittenOff($param);
        if ($rt->STS) {
            if (count($committee_member) == 1) {
                //说明不需要投票
                showMessage("Approved Success!", getUrl('loan_committee', 'approveWrittenOffRequest', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                $this->voteWrittenOffOp($param['off_id']);
            }

        } else {
            showMessage($rt->MSG, getUrl('loan_committee', 'approveWrittenOffRequest', array(), false, BACK_OFFICE_SITE_URL));
        }
    }

    /**
     * 投票页面
     */
    public function voteWrittenOffOp($uid)
    {
        if (!intval($uid)) {
            $uid = intval($_GET['uid']);
        } else {
            $uid = intval($uid);
        }

        $m_loan_written_off = new loan_writtenoffModel();
        $data = $m_loan_written_off->getWrittenOffDetail($uid);
        if (!$data) {
            showMessage('Invalid Id.');
        }
        Tpl::output('written_off', $data);

        $countdown = strtotime($data['update_time']) + 300 - time();
        Tpl::output('countdown', $countdown);

        $member_id = $data['member_id'];
        $client_info = M('client_member')->find(array('uid' => $member_id));
        Tpl::output('client_info', $client_info);

        $vote_url = getUrl('committee_vote', 'committeeVoteWrittenOff', array('uid' => $uid), false, BACK_OFFICE_SITE_URL);
        Tpl::output('vote_url', $vote_url);

        $class_loan_written_off = new loan_written_offClass();
        $vote_committee_member = $class_loan_written_off->getVoteCommitteeMember($uid);
        Tpl::output('vote_committee_member', $vote_committee_member);

        Tpl::showPage('written.off.vote');
    }

    /**
     * 重新编辑
     */
    public function reeditWrittenOffOp()
    {
        $uid = intval($_GET['uid']);
        $class_loan_written_off = new loan_written_offClass();
        $rt_1 = $class_loan_written_off->cancelWrittenOff($uid);
        if (!$rt_1->STS) {
            showMessage($rt_1->MSG);
        }

        $this->handleWrittenOffOp($uid);
    }

    /**
     * 投票结果
     */
    public function getWrittenOffVoteResultOp($p)
    {
        $off_id = intval($p['off_id']);
        $class_loan_written_off = new loan_written_offClass();
        $vote_committee_member = $class_loan_written_off->getVoteCommitteeMember($off_id);
        return array(
            "sts" => true,
            "data" => $vote_committee_member,
        );
    }

    /**
     * 重置本次投票
     * @param $p
     * @return mixed
     */
    public function resetWrittenOffVoteTimerOp($p)
    {
        $uid = intval($p['uid']);
        $class_loan_written_off = new loan_written_offClass();
        $rt = $class_loan_written_off->resetVoteTimer($uid);
        return $rt;
    }

    /**
     * 完成投票统计
     * @param $p
     * @return ormResult|result
     */
    public function completeVoteWrittenOffOp($p)
    {
        $uid = intval($p['uid']);
        $class_loan_written_off = new loan_written_offClass();
        $rt = $class_loan_written_off->completeVote($uid);
        return $rt;
    }

    public function showWrittenOffOp()
    {
        $uid = intval($_GET['uid']);
        if (!intval($uid)) {
            $uid = intval($_GET['uid']);
        } else {
            $uid = intval($uid);
        }

        $m_loan_written_off = new loan_writtenoffModel();
        $data = $m_loan_written_off->getWrittenOffDetail($uid);
        if (!$data) {
            showMessage('Invalid Id.');
        }
        Tpl::output('written_off', $data);

        $member_id = $data['member_id'];
        $client_info = M('client_member')->find(array('uid' => $member_id));
        Tpl::output('client_info', $client_info);

        $class_loan_written_off = new loan_written_offClass();
        $vote_committee_member = $class_loan_written_off->getVoteCommitteeMember($uid);
        Tpl::output('vote_committee_member', $vote_committee_member);

        Tpl::showPage('written.off.result');
    }

    /**
     * 授信记录
     */
    public function grantCreditHistoryOp()
    {
        //这个暂时改成 pending sign credit-agreement, by tim
        Tpl::showPage('grant.credit.history');
    }

    /**
     * 获取授信记录列表
     */
    public function grantCreditHistoryListOp($p)
    {
        //这个暂时改成 pending sign credit-agreement
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $filter = array(
            'state' => commonApproveStateEnum::PASS,
            'search_text' => $p['search_text']
        );

        $m_member_credit_grant = new member_credit_grantModel();
        $page = $m_member_credit_grant->getCreditGrantNotSignList($pageNumber, $pageSize, $filter);
        $data = $page->DATA;
        return array(
            'sts' => true,
            "data" => $data['rows'],
            "total" => $data['total'],
            "pageTotal" => $data['page_total'],
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 授信详情
     */
    public function showGrantCreditDetailOp()
    {
        $uid = intval($_GET['uid']);
        $class_credit_grant = new member_credit_grantClass();
        $credit_grant = $class_credit_grant->getCreditGrantById($uid);
        Tpl::output('credit_grant', $credit_grant);

        $member_id = $credit_grant['member_id'];
        $client_info = M('client_member')->find(array('uid' => $member_id));
        Tpl::output('client_info', $client_info);

        $member_credit_grant_vote = $class_credit_grant->getVoteCommitteeMember($uid);
        Tpl::output('member_credit_grant_vote', $member_credit_grant_vote);

        $prod_list = memberClass::getMemberCreditLoanProduct($member_id);
        Tpl::output("product_list", $prod_list);

        Tpl::output("member_assets", member_assetsClass::getAllAssetListOfMember($member_id));


        $credit_category = loan_categoryClass::getMemberCreditCategoryList($member_id);
        Tpl::output("credit_category", $credit_category);


        $m_member_authorized_contract = new member_authorized_contractModel();
        $authorized_contract_list = $m_member_authorized_contract->getConstructListByGrantId($uid);
        Tpl::output("authorized_contract", $authorized_contract_list);

        $contract_list = M('loan_contract')->getContractByGrantId($uid);
        Tpl::output("contract_list", $contract_list);

        Tpl::showPage('show.credit.grant.detail');

    }

    /**
     * 删除授信
     * @param $p
     * @return result
     */
    public function deleteCreditGrantOp($p)
    {
        $uid = intval($p['uid']);
        $rt = member_credit_grantClass::deleteCreditGrant($uid);
        return $rt;
    }

    /**
     * 审核提取抵押物的申请
     */
    public function approveWithdrawMortgageRequestOp()
    {
        Tpl::showPage("mortgage.withdraw.request");

    }

    /**
     * 获取核销任务申请列表
     * @param $p
     * @return array
     */
    public function getWithdrawMortgageRequestListOp($p)
    {
        $pageNumber = intval($p['pageNumber']);
        $pageSize = intval($p['pageSize']);
        $filter = array(
            'search_text' => trim($p['search_text']),
            'state' => intval($p['state']),
        );
        $state = intval($p['state']);
        if ($state == assetRequestWithdrawStateEnum::PENDING_WITHDRAW) {
            $where = " where state>='" . assetRequestWithdrawStateEnum::PENDING_WITHDRAW . "'";
        } else {
            $where = " where state='" . $state . "'";
        }
        $reader = new ormReader();
        $sql = "SELECT a.*,b.`asset_name`,b.`asset_sn`,c.`display_name` member_display_name FROM member_asset_request_withdraw a "
            . " LEFT JOIN member_assets b ON a.member_asset_id=b.`uid`"
            . " LEFT JOIN client_member c ON b.member_id=c.`uid` " . $where;
        $ret = $reader->getPage($sql, $pageNumber, $pageSize);
        $data = $ret->toArray();
        $data['sts'] = true;
        $data['state'] = intval($p['state']);
        $data['user_id'] = $this->user_id;
        return $data;
    }

    public function approveWithdrawMortgageRequestDetailOp()
    {
        $request_id = $_REQUEST['request_id'];
        $m_request = M("member_asset_request_withdraw");
        $current_request = $m_request->find(array("uid" => $request_id));
        if (!$current_request) {
            showMessage("Invalid Parameter:No Object Found");
        }
        $asset_id = $current_request['member_asset_id'];
        $ret = credit_officerClass::getMemberAssetDetailAndResearchInfo($asset_id, $this->user_id);
        $asset = $ret->DATA;
        Tpl::output("asset", $asset);
        $member_id = $asset['member_id'];
        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        //对应的grant的还贷情况
        $loan_ret = member_assetsClass::getAssetRelativeContract($asset_id);
        $principal_outstanding = $loan_ret['principal_outstanding'];
        $loan_list = $loan_ret['contract_list'];
        Tpl::output("loan_list", $loan_list);
        Tpl::output("principal_outstanding", $principal_outstanding);
        //保存流水
        $storage_list = member_assetsClass::getAssetStorageFlow($asset_id);
        Tpl::output("storage_list", $storage_list);
        //获取最后一次请求
        $request_list = member_assetsClass::getAssetWithdrawRequestHistory($asset_id);
        Tpl::output("current_request", $current_request);
        Tpl::output("request_list", $request_list);

        Tpl::showPage("mortgage.withdraw.request.detail");
    }

    public function submitApproveRequestWithdrawMortgageOp()
    {
        $args = $_GET;
        $args['auditor_id'] = $this->user_id;
        $args['auditor_name'] = $this->user_name;
        $ret = member_assetsClass::saveRequestWithdraw($args);
        if (!$ret->STS) {
            showMessage($ret->MSG, getUrl("loan_committee", "approveWithdrawMortgageRequestDetail", array("request_id" => $args['uid']), false, BACK_OFFICE_SITE_URL));
        } else {
            showMessage("Save Success", getUrl("loan_committee", "approveWithdrawMortgageRequestDetail", array("request_id" => $args['uid']), false, BACK_OFFICE_SITE_URL));
        }

    }

    public function userVoteOp()
    {
        Tpl::showPage('my.vote');
    }

    public function getMyCreditVoteListOp($p)
    {
        $p['user_id'] = $this->user_id;
        $class_credit_grant = new member_credit_grantClass();
        return $class_credit_grant->getMyVoteList($p);
    }

    public function submitVoteCreditResultOp()
    {
        $param = array_merge(array(), $_GET, $_POST);
        $class_credit_grant = new member_credit_grantClass();
        $param['user_id'] = $this->user_id;
        $rt = $class_credit_grant->submitVoteCreditApplication($param);
        if ($rt->STS) {
            showMessage("Vote Success!", getBackOfficeUrl("loan_committee", "userVote"));
        } else {
            showMessage($rt->MSG);
        }
    }

    public function signCreditAgreementOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage("sign.credit.agreement");
    }

    public function signCreditAgreementListOp($p)
    {
//这个暂时改成 pending sign credit-agreement
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $filter = array(
            'state' => commonApproveStateEnum::PASS,
            'search_text' => $p['search_text']
        );

        $m_member_credit_grant = new member_credit_grantModel();
        $page = $m_member_credit_grant->getCreditGrantSignList($pageNumber, $pageSize, $filter);
        $data = $page->DATA;
        return array(
            'sts' => true,
            "data" => $data['rows'],
            "total" => $data['total'],
            "pageTotal" => $data['page_total'],
            "pageNumber" => $pageNumber,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 授权合同详情
     */
    public function showCreditContractDetailOp()
    {
        $uid = intval($_GET['uid']);
        $m_member_authorized_contract = new member_authorized_contractModel();
        $authorized_contract = $m_member_authorized_contract->getConstructBaseInfo($uid);
        Tpl::output("authorized_contract", $authorized_contract);
        $contract_images = $m_member_authorized_contract->getConstructImages($uid);
        Tpl::output("contract_images", $contract_images);
        $member_id = $authorized_contract['member_id'];
        $client_info = M('client_member')->find(array('uid' => $member_id));
        Tpl::output('client_info', $client_info);


        //是否允许删除，如果该合同的授权id还没存在于贷款合同中
        $grant_id=$authorized_contract['grant_credit_id'];
        if($grant_id){
            $sql="select * from loan_contract where credit_grant_id=".qstr($grant_id)." and state>=0";
            $chk_item=$m_member_authorized_contract->reader->getRow($sql);
            if(!$chk_item && $authorized_contract['state']>authorizedContractStateEnum::CANCEL){
                Tpl::output("allowed_delete",1);
            }

        }

        Tpl::showPage('show.credit.contract.detail');

    }
    /*
     * 取消授权合同，只改状态，不处理其它的
     */
    function cancelCreditAgreementOp($p){
        $contract_id=$p['contract_id'];
        if(!$contract_id){
            return new result(false,"Invalid Parameter:No Contract ID");
        }
        $m=new member_authorized_contractModel();
        $row=$m->getRow(array("uid"=>$contract_id));
        if($row){
            $row->state=authorizedContractStateEnum::CANCEL;
            $row->udpate_time=Now();
            return $row->update();
        }else{
            return new result(false,"Invalid Parameter:No Found Contract BY--".$contract_id);
        }
    }
}
