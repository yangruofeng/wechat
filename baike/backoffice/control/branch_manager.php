<?php

class branch_managerControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('operator,certification');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Branch Manager");
        Tpl::setDir("branch_manager");
    }

    /**
     * 新创建client
     */
    public function clientOp()
    {
        $msg_task_list = taskControllerClass::getPendingTaskMsgList($this->user_info['branch_id'], userTaskTypeEnum::BM_NEW_CLIENT);
        Tpl::output("msg_task_list", $msg_task_list);
        Tpl::showPage('client');
    }

    /**
     * 获取client列表
     * @param $p
     * @return array
     */
    public function getClientListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $filters = array(
            'branch_id' => $this->user_info['branch_id'],
            'search_text' => trim($p['search_text']),
            'is_credit' => intval($p['is_credit']),
            'pending_committee_approve' => intval($p['pending_committee_approve']),
            'member_state_cancel' => intval($p['member_state_cancel']),
            'member_state_new' => intval($p['member_state_new']),
        );
        $data = memberInfoClass::getClientPage($pageNumber, $pageSize, $filters);
        return $data;
    }

    /**
     * 修改member co
     * @param $p
     * @return result
     */
    public function editMemberCoOp($p)
    {
        $member_id = intval($p['uid']);
        if (is_array($p['co_id'])) {
            $co_arr = $p['co_id'];
        } else if ($p['co_id']) {
            $co_arr = array($p['co_id']);
        } else {
            $co_arr = array();
        }
        $class_member = new memberClass();
        $rt = $class_member->setMemberCo($member_id, $co_arr);
        return $rt;
    }

    /**
     * member 身份信息
     */
    public function showPersonalInformationOp()
    {
        $uid = intval($_GET['uid']);

        $r = new ormReader();
        $m_client_member = M('client_member');
        $m_member_verify_cert_image = M('member_verify_cert_image');

        $client_info = $m_client_member->find(array('uid' => $uid));
        if (!$client_info) {
            showMessage('Invalid Id!');
        }
        Tpl::output('client_info', $client_info);

        //id
        $sql = "SELECT * FROM member_verify_cert WHERE member_id = $uid AND cert_type = " . certificationTypeEnum::ID . " AND verify_state = " . certStateEnum::PASS . " ORDER BY auditor_time DESC";
        $id_cert = $r->getRow($sql);
        if ($id_cert) {
            $id_cert_images = $m_member_verify_cert_image->select(array('cert_id' => $id_cert['uid']));
            $id_cert['images'] = $id_cert_images;
            Tpl::output('id_cert', $id_cert);
        }

        //passport
        $sql = "SELECT * FROM member_verify_cert WHERE member_id = $uid AND cert_type = " . certificationTypeEnum::PASSPORT . " AND verify_state = " . certStateEnum::PASS . " ORDER BY auditor_time DESC";
        $passport_cert = $r->getRow($sql);
        if ($passport_cert) {
            $id_cert_images = $m_member_verify_cert_image->select(array('cert_id' => $passport_cert['uid']));
            $passport_cert['images'] = $id_cert_images;
            Tpl::output('passport_cert', $passport_cert);
        }

        //family book
        $sql = "SELECT * FROM member_verify_cert WHERE member_id = $uid AND cert_type = " . certificationTypeEnum::FAIMILYBOOK . " AND verify_state = " . certStateEnum::PASS . " ORDER BY auditor_time DESC";
        $family_book_cert = $r->getRow($sql);
        if ($family_book_cert) {
            $id_cert_images = $m_member_verify_cert_image->select(array('cert_id' => $family_book_cert['uid']));
            $family_book_cert['images'] = $id_cert_images;
            Tpl::output('family_book_cert', $family_book_cert);
        }

        //work
        $sql = "SELECT * FROM member_verify_cert WHERE member_id = $uid AND cert_type = " . certificationTypeEnum::WORK_CERTIFICATION . " AND verify_state = " . certStateEnum::PASS . " ORDER BY auditor_time DESC";
        $work_cert = $r->getRow($sql);
        if ($work_cert) {
            $id_cert_images = $m_member_verify_cert_image->select(array('cert_id' => $work_cert['uid']));
            $work_cert['images'] = $id_cert_images;
            $m_work = new member_workModel();
            $extend_info = $m_work->find(array('cert_id' => $work_cert['uid']));
            $work_cert['extend_info'] = $extend_info;
            Tpl::output('work_cert', $work_cert);
        }

        Tpl::showPage('member.personal.information');
    }

    /**
     * 展示client 详情
     */
    public function showClientDetailOp()
    {
        $uid = intval($_GET['uid']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $uid));
        if (!$client_info) {
            showMessage('Invalid Id!');
        }
        Tpl::output('client_info', $client_info);

        //co start
        $r = new ormReader();
        $sql = 'SELECT mc.*,uu.user_name,uu.mobile_phone FROM member_follow_officer mc INNER JOIN um_user uu ON mc.officer_id = uu.uid WHERE uu.user_status = 1 AND mc.is_active = 1 AND mc.member_id = ' . $uid;
        $co_list = $r->getRows($sql);

        $sql = "SELECT uu.* FROM um_user uu INNER JOIN site_depart sd ON uu.depart_id = sd.uid WHERE uu.user_status = 1 AND sd.branch_id = " . $this->user_info['branch_id'] . " AND uu.user_position = '" . userPositionEnum::CREDIT_OFFICER . "'";
        $branch_co_list = $r->getRows($sql);

        Tpl::output('client_info', $client_info);
        Tpl::output('co_list', $co_list);
        Tpl::output('branch_co_list', $branch_co_list);
        //co end

        //credit process start
        $r = new ormReader();
        $credit_process = array();

        //register
        $credit_process['register']['time'] = $client_info['create_time'];

        $cert_type = array(
            certificationTypeEnum::ID,
            certificationTypeEnum::FAIMILYBOOK,
            certificationTypeEnum::PASSPORT,
            certificationTypeEnum::WORK_CERTIFICATION,
        );
        $cert_type = "(" . implode(',', $cert_type) . ")";
        $sql = "SELECT * FROM member_verify_cert WHERE member_id = $uid AND verify_state = " . certStateEnum::PASS . " AND cert_type IN $cert_type ORDER BY auditor_time DESC";
        $personal_info = $r->getRow($sql);
        $credit_process['personal_info']['is_check'] = $personal_info ? true : false;
        $credit_process['personal_info']['time'] = $personal_info['auditor_time'];

        $sql = "SELECT * FROM member_income_research WHERE member_id = $uid AND branch_id = " . $this->user_info['branch_id'] . " AND researcher_type = 1 ORDER BY research_time DESC";
        $income_research = $r->getRow($sql);
        $credit_process['income_research']['is_check'] = $income_research ? true : false;
        $credit_process['income_research']['time'] = $income_research['research_time'];

        //Request Credit
        $m_member_credit_suggest = M('member_credit_suggest');
        $chk_credit_suggest = $m_member_credit_suggest->orderBy('request_time DESC')->find(array('branch_id' => $this->user_info['branch_id'], 'member_id' => $uid, 'request_type' => 1));
        $credit_process['chk_credit_suggest']['is_check'] = $chk_credit_suggest ? true : false;
        $credit_process['chk_credit_suggest']['time'] = $chk_credit_suggest['request_time'];

        //Assets Information
        $m_member_assets = M('member_assets');
        $chk_member_assets = $m_member_assets->orderBy('valuate_time DESC')->find(array('member_id' => $uid, 'asset_state' => 100));
        $credit_process['chk_member_assets']['is_check'] = $chk_member_assets ? true : false;
        $credit_process['chk_member_assets']['time'] = $chk_member_assets['valuate_time'];

        //Assets Evaluate
        $m_member_assets_evaluate = M('member_assets_evaluate');
        $chk_member_assets_evaluate = $m_member_assets_evaluate->orderBy('evaluate_time DESC')->find(array('member_id' => $uid, 'evaluator_type' => 1, 'branch_id' => $this->user_info['branch_id']));
        $credit_process['chk_member_assets_evaluate']['is_check'] = $chk_member_assets_evaluate ? true : false;
        $credit_process['chk_member_assets_evaluate']['time'] = $chk_member_assets_evaluate['evaluate_time'];

        //member_credit_grant
        $m_member_credit_grant = M('member_credit_grant');
        $member_credit_grant = $m_member_credit_grant->orderBy('update_time DESC')->find(array('member_id' => $uid));
        $credit_process['member_credit_grant'] = $member_credit_grant;

        Tpl::output('credit_process', $credit_process);
        //credit process end

        //CBC
        $m_client_cbc = M('client_cbc');
        $client_cbc = $m_client_cbc->find(array('client_id' => $uid, 'client_type' => 0, 'state' => 1));
        Tpl::output('client_cbc', $client_cbc);

        Tpl::showPage('member.detail');
    }

    /**
     * 会员详细信息
     */
    public function showClientInfoDetailOp()
    {
        $r = new ormReader();
        $p = array_merge(array(), $_GET, $_POST);
        $contract_info = array();
        $member_id = intval($p['uid']);
        if (!$member_id) {
            showMessage('Client Error');
        }

        $sql = "SELECT client.*,loan.uid as loan_uid FROM client_member as client left join loan_account as loan on loan.obj_guid = client.obj_guid where client.uid = " . $p['uid'];
        $data = $r->getRow($sql);

        if (!$data) {
            showMessage('Client Error', '', 'html', 'error');
        }

        $loan_uid = $data['loan_uid'] ?: 0;
        $sql2 = "SELECT contract.*,product.product_code,product.product_name,product.product_description FROM loan_contract as contract left join loan_product as product on contract.product_id = product.uid where contract.account_id = " . $loan_uid . " order by contract.uid desc";
        $contracts = $r->getRows($sql2);

        $sql2 = "SELECT contract.* FROM insurance_contract as contract left join insurance_account as account on contract.account_id = account.uid where account.obj_guid = " . $data['obj_guid'] . " order by contract.uid desc";
        $insurance_contracts = $r->getRows($sql2);

        //*这是request loan的次数
        $sql = "SELECT count(uid) as count from loan_apply where member_id = " . $p['uid'];
        $loan_count = $r->getOne($sql);
        $contract_info['all_enquiries'] = $loan_count;

        //*这是第一次发放贷款的日期
        $sql = "select d.create_time from loan_contract c LEFT JOIN loan_disbursement d on c.uid = d.contract_id where c.account_id = " . $loan_uid . " ORDER BY d.create_time desc limit 1";
        $create_time = $r->getOne($sql);
        $contract_info['earliest_loan_issue_date'] = $create_time;
        $loan_summary = memberClass::getMemberLoanSummary($p['uid'], 1);
        $guarantee_loan_summary = memberClass::getMemberLoanSummary($p['uid'], 2);

        Tpl::output("contract_info", $contract_info);
        Tpl::output("loan_summary", $loan_summary->DATA);
        Tpl::output("guarantee_loan_summary", $guarantee_loan_summary->DATA);

        $cert_type_lang = enum_langClass::getCertificationTypeEnumLang();
        Tpl::output('cert_type_lang', $cert_type_lang);

        $re = memberClass::getMemberSimpleCertResult($member_id);
        if (!$re->STS) {
            showMessage('Error: ' . $re->MSG);
        }
        $verifys = $re->DATA;

        $credit_info = memberClass::getCreditBalance(intval($p['uid']));
        Tpl::output("detail", $data);
        Tpl::output('credit_info', $credit_info);
        Tpl::output("verifys", $verifys);
        Tpl::output("contracts", $contracts);
        Tpl::output("insurance_contracts", $insurance_contracts);

        $sql = "select uid, type from client_black";
        $types = $r->getRows($sql);
        foreach ($types as $key => $value) {
            $members = $value['list'];
            $members = $members ? explode(',', $members) : array();
            $types[$key]['check'] = false;
            if (in_array($p['uid'], $members)) {
                $types[$key]['check'] = true;
            }
            unset($types[$key]['list']);
        }
        Tpl::output("black", $types);

        if ($data['id_address1']) {
            $arr = array($data['id_address1'], $data['id_address2'], $data['id_address3'], $data['id_address4']);
            $adds = implode(',', $arr) ?: 0;
            $sql = "select uid,node_text,node_text_alias from core_tree where uid in(" . $adds . ")";
            $address = $r->getRows($sql);
            $addr = array();
            foreach ($address as $key => $value) {
                $addr[$value['uid']] = $value;
            }
            Tpl::output("addr", $addr);
        }

        $sql = "SELECT mw.*,mvc.verify_remark FROM member_work mw INNER JOIN member_verify_cert mvc ON mw.cert_id = mvc.uid WHERE mw.member_id = $member_id AND mw.state = 20";
        $member_work = $r->getRow($sql);
        Tpl::output("member_work", $member_work);

        //获取co advice
        $m_member_credit_suggest = M('member_credit_suggest');
        $credit_suggest = $m_member_credit_suggest->orderBy('uid DESC')->find(array('member_id' => $member_id));
        Tpl::output("credit_suggest", $credit_suggest);

        $sql = "SELECT SUM(valuation) assets_valuation FROM member_assets WHERE member_id = " . $member_id . " AND asset_state = 100";
        $assets_valuation = $r->getOne($sql);
        Tpl::output("assets_valuation", $assets_valuation);

        $sql = "SELECT update_time FROM member_assets WHERE member_id = " . $member_id . " AND asset_state = 100 ORDER BY uid DESC";
        $evaluate_time = $r->getOne($sql);
        Tpl::output("evaluate_time", $evaluate_time);

        $sql = "SELECT asset_type,SUM(valuation) assets_valuation FROM member_assets WHERE member_id = " . $member_id . " AND asset_state = 100 GROUP BY asset_type ORDER BY asset_type ASC";
        $assets_valuation_type = $r->getRows($sql);
        Tpl::output("assets_valuation_type", $assets_valuation_type);

        Tpl::output("member_id", $member_id);
        Tpl::output("source", $_GET['source']);
        Tpl::output("suggest_id", $_GET['suggest_id']);
        Tpl::output("off_id", $_GET['off_id']);

        Tpl::showPage("member.info.detail");
    }

    /**
     * 编辑cbc
     */
    public function editMemberCbcOp($p)
    {
        $p['creator_id'] = $this->user_id;
        $p['creator_name'] = $this->user_name;
        $m_client_cbc = new client_cbcModel();
        $rt = $m_client_cbc->editClientCbc($p);
        return $rt;
    }

    /**
     * 贷款咨询
     */
    public function loanConsultOp()
    {
        $msg_task_list = taskControllerClass::getPendingTaskMsgList($this->user_info['branch_id'], userTaskTypeEnum::BM_NEW_CONSULT);
        Tpl::output("msg_task_list", $msg_task_list);
        Tpl::showPage('loan.consult');
    }

    /**
     * 获取申请列表
     * @param $p
     * @return array
     */
    public function getLoanConsultListOp($p)
    {
        $verify_state = intval($p['verify_state']);
        $r = new ormReader();
        $sql = "SELECT lc.* FROM loan_consult lc WHERE branch_id = " . $this->user_info['branch_id'];
        if ($verify_state == loanConsultStateEnum::CO_HANDING) {
            $sql .= " AND (lc.state = '" . loanConsultStateEnum::ALLOT_CO . "' OR lc.state = '" . loanConsultStateEnum::CO_HANDING . "')";
        } elseif ($verify_state == loanConsultStateEnum::CREATE) {
            $sql .= " AND (lc.state = '" . loanConsultStateEnum::CREATE . "' OR lc.state = '" . loanConsultStateEnum::ALLOT_BRANCH . "' OR lc.state = '" . loanConsultStateEnum::OPERATOR_APPROVED . "')";
        } else {
            $sql .= " AND lc.state = " . $verify_state;
        }
        if (trim($p['search_text'])) {
            $sql .= " AND lc.applicant_name like '%" . trim($p['search_text']) . "%' OR lc.contact_phone like '%" . trim($p['search_text']) . "%'";
        }
        $sql .= " ORDER BY lc.uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
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
            "verify_state" => $verify_state,
        );
    }

    /**
     * 贷款申请审核
     */
    public function handleLoanConsultOp()
    {
        $uid = intval($_GET['uid']);
        $m_loan_consult = M('loan_consult');

        $row = $m_loan_consult->getRow($uid);
        if (!$row) {
            showMessage('Invalid Id!');
        }

        Tpl::output('apply_info', $row);

        $apply_source = (new loanConsultSourceEnum())->Dictionary();
        Tpl::output('apply_source', $apply_source);

        Tpl::showpage('loan.consult.handle');
    }

    /**
     * @param $p
     * @return array
     */
    public function getCoListOp($p)
    {
        $search_text = trim($p['search_text']);

        $r = new ormReader();
        $sql = "SELECT uu.* FROM um_user uu INNER JOIN site_depart sd ON uu.depart_id = sd.uid WHERE uu.user_position = '" . userPositionEnum::CREDIT_OFFICER . "' AND sd.branch_id = " . $this->user_info['branch_id'];
        if ($search_text) {
            $sql .= " AND uu.user_name like '%" . $search_text . "'";
        }

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
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
        );
    }

    /**
     * 分配咨询给co
     * @param $p
     * @return result
     */
    public function allotConsultToCoOp($p)
    {
        $uid = intval($p['uid']);
        $co_id = intval($p['co_id']);
        $remark = trim($p['remark']);

        $m_um_user = M('um_user');
        $m_loan_consult = M('loan_consult');

        $user = $m_um_user->find(array('uid' => $co_id, 'user_position' => userPositionEnum::CREDIT_OFFICER));
        if (!$user) {
            return new result(false, 'Invalid Co id!');
        }

        $row = $m_loan_consult->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid id!');
        }

        if ($row->state >= loanConsultStateEnum::CO_CANCEL) {
            return new result(false, 'Co had done it!');
        }

        $row->bm_id = $this->user_id;
        $row->bm_name = $this->user_name;
        $row->bm_remark = $remark;
        $row->co_id = $co_id;
        $row->co_name = $user['user_name'];
        $row->state = loanConsultStateEnum::ALLOT_CO;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Allot Successful!');
        } else {
            return new result(false, 'Allot Failed!');
        }

    }

    public function rejectConsultOp($p)
    {
        $uid = intval($p['uid']);
        $m_loan_consult = M('loan_consult');
        $remark = trim($p['remark']);
        $operator_id = $this->user_id;
        $operator_name = $this->user_name;
        $rt = $m_loan_consult->rejectById($uid, $remark, $operator_id, $operator_name);
        return $rt;
    }

    /**
     * 收入情况
     */
    public function showIncomeResearchOp()
    {
        $member_id = intval($_GET['uid']);
        $officer_id = $this->user_id;

        //输出co的调查历史记录
        $co_list = memberClass::getMemberCreditOfficerList($member_id);
        $co_list = resetArrayKey($co_list, "officer_id");
        $co_research = array();
        $cur_co_research = array();
        $cnt_valid_co = 0;
        foreach ($co_list as $co_k => $co) {
            $co_last_research = credit_officerClass::getLastSubmitMemberIncomeResearch($co['officer_id'], $member_id) ?: array();
            if (!$co_last_research) {
                $co_research[$co['officer_id']] = array(
                    "operator_name" => $co['officer_name']
                );
            } else {
                $co_last_research['total_income'] = $co_last_research['income_rental_land'] + $co_last_research['income_rental_housing'] + $co_last_research['income_business'] + $co_last_research['income_salary'] + $co_last_research['income_others'];
                $co_research[$co['officer_id']] = $co_last_research;
                if ($co_last_research['total_income'] > 0) {
                    $cnt_valid_co += 1;//认为这个co做了评估，参与计算平均值
                    $cur_co_research = $co_last_research;//avg-item的非数字项取最后一个co的评估值
                }
            }
        }

        $member_industry_info = memberClass::getMemberIndustryInfo($member_id);
        //计算co评估的average,构造一个item和co调查内容完全一样，如果bm没有自己的调查记录，就默认填充avg的内容
        if ($cnt_valid_co > 1) {
            $avg_item['operator_id'] = 0;
            $avg_item['operator_name'] = "--Average--";
            $avg_item['total_income'] = intval(sumArrayByKey($co_research, "total_income") / $cnt_valid_co);
            $avg_item['income_rental_land'] = intval(sumArrayByKey($co_research, "income_rental_land") / $cnt_valid_co);
            $avg_item['income_rental_housing'] = intval(sumArrayByKey($co_research, "income_rental_housing") / $cnt_valid_co);
            $avg_item['income_business'] = intval(sumArrayByKey($co_research, "income_business") / $cnt_valid_co);
            $avg_item['business_employees'] = intval(sumArrayByKey($co_research, "business_employees") / $cnt_valid_co);
            $avg_item['income_salary'] = intval(sumArrayByKey($co_research, "income_salary") / $cnt_valid_co);
            $avg_item['income_others'] = intval(sumArrayByKey($co_research, "income_others") / $cnt_valid_co);
            $avg_item['company_name'] = $cur_co_research['company_name'];
            $avg_item['work_position'] = $cur_co_research['work_position'];

            //构造industry的平均值,去co_research的member_industry_research
            $co_industry_ret = array();
            foreach ($co_research as $cr_item) {
                $cr_1 = resetArrayKey($cr_item['member_industry_research'], "industry_id");
                foreach ($member_industry_info as $mik => $miv) {
                    $co_industry_ret[$mik][] = $cr_1[$mik];
                }
            }
            $avg_item_industry = array();
            foreach ($member_industry_info as $ids_k => $ids_v) {
                $new_avg_item = array();
                $new_avg_item['employees'] = sumArrayByKey($co_industry_ret[$ids_k], "employees") / $cnt_valid_co;
                $new_avg_item['profit'] = sumArrayByKey($co_industry_ret[$ids_k], "profit") / $cnt_valid_co;
                $cur_research_text = my_json_decode(current($co_industry_ret[$ids_k])['research_text']);
                $new_avg_item['research_text'] = my_json_encode(array(
                    "place" => $cur_research_text['place'],
                    "employees" => $new_avg_item['employees'],
                    "profit" => $new_avg_item['profit']
                ));
                $new_avg_item['industry_id'] = $ids_k;
                $avg_item_industry[] = $new_avg_item;
            }
            $avg_item['member_industry_research'] = $avg_item_industry ?: array();
            $co_research[] = $avg_item;
        } elseif ($cnt_valid_co == 1) {
            $avg_item = $cur_co_research;
        } else {
            $avg_item = array('member_industry_research' => array());
        }


        $last_research_info = credit_officerClass::getLastSubmitMemberIncomeResearch($officer_id, $member_id);
        if (!$last_research_info) {
            $last_research_info = $avg_item;
        }
        $research_lst = $last_research_info['member_industry_research'] ?: array();
        $research_lst = resetArrayKey($research_lst, "industry_id");
        foreach ($member_industry_info as $ids_k => $ids_item) {
            if ($research_lst[$ids_k]) {
                $member_industry_info[$ids_k]['research_json'] = $research_lst[$ids_k]['research_text'];
            }
        }

        $m_industry_place = M("common_industry_place");
        $place_lst = $m_industry_place->getAll();
        Tpl::output("business_place", $place_lst ? $place_lst->toArray() : array());

        $total = intval($last_research_info['income_rental_land'] + $last_research_info['income_rental_housing'] + $last_research_info['income_business'] + $last_research_info['income_salary'] + $last_research_info['income_others']);
        Tpl::output('last_research_info', $last_research_info);
        Tpl::output('member_industry_info', $member_industry_info);
        Tpl::output('total', $total);

        Tpl::output("co_research", $co_research);

        Tpl::output('member_id', $member_id);
        Tpl::output('operator_id', $officer_id);
        Tpl::showPage('client.income_research');
    }

    /**
     * 评估历史
     */
    public function showIncomeResearchHistoryOp()
    {
        $member_id = intval($_GET['member_id']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $member_id));
        Tpl::output('client_info', $client_info);

        $operator_id = intval($_GET['operator_id']);
        $m_um_user = M('um_user');
        $operator_info = $m_um_user->find(array('uid' => $operator_id));
        Tpl::output('operator_info', $operator_info);

        $m_member_income_research = M('member_income_research');
        $income_research = $m_member_income_research->orderBy('uid DESC')->select(array('member_id' => $member_id, 'branch_id' => $this->user_info['branch_id'], 'operator_id' => $operator_id));
        if ($income_research) {
            Tpl::output('income_research', $income_research);
        }

        Tpl::output('member_id', $member_id);
        Tpl::showPage('client.income_research.history');
    }

    /**
     * BM评估历史
     */
    public function showBmIncomeResearchHistoryOp()
    {
        $member_id = intval($_GET['uid']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $member_id));
        Tpl::output('client_info', $client_info);

        $m_member_income_research = M('member_income_research');
        $income_research = $m_member_income_research->orderBy('uid DESC')->select(array('member_id' => $member_id, 'branch_id' => $this->user_info['branch_id'], 'researcher_type' => 1));
        if ($income_research) {
            Tpl::output('income_research', $income_research);
        }
        Tpl::output('member_id', $member_id);
        Tpl::output('source', $_GET['source']);
        Tpl::showPage('client.income_research.history');
    }

    /**
     * co所有评估列表（member_id）
     */
    public function showCoIncomeResearchOp()
    {
        $member_id = intval($_GET['uid']);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $member_id));
        Tpl::output('client_info', $client_info);

        $m_member_income_research = M('member_income_research');
        $income_research = $m_member_income_research->orderBy('uid DESC')->select(array('member_id' => $member_id, 'branch_id' => $this->user_info['branch_id'], 'researcher_type' => 0));
        Tpl::output('income_research', $income_research);
        Tpl::output('member_id', $member_id);
        Tpl::output('source', $_GET['source']);
        Tpl::showPage('client.income_research.history');
    }

    /**
     * 评估详情
     */
    public function showIncomeResearchDetailOp()
    {
        $uid = intval($_GET['uid']);
        $m_member_income_research = M('member_income_research');
        $income_research = $m_member_income_research->orderBy('uid DESC')->find(array('uid' => $uid));
        Tpl::output('income_research', $income_research);

        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $income_research['member_id']));
        Tpl::output('client_info', $client_info);

        $m_common_industry_research = M('common_industry_research');
        $industry_research = $m_common_industry_research->select(array('research_id' => $uid));
        $industry_research = resetArrayKey($industry_research, 'industry_id');
        Tpl::output('industry_research', $industry_research);

        $m_common_industry_place = M('common_industry_place');
        $industry_place = $m_common_industry_place->getAll();
        $industry_place = $industry_place->toArray();
        $industry_place = resetArrayKey($industry_place, 'uid');
        Tpl::output('industry_place', $industry_place);

        $member_industry = memberClass::getMemberIndustryInfo($income_research['member_id']);
        Tpl::output('member_industry', $member_industry);

        Tpl::output('member_id', $income_research['member_id']);
        Tpl::showPage('client.income_research.old');
    }

    /**
     * BM评估编辑
     */
    public function editBmIncomeResearchOp()
    {
        $param = array_merge(array(), $_GET, $_POST);
        $param['officer_id'] = $this->user_id;
        $param['researcher_type'] = 1;

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = credit_officerClass::addMemberIncomeResearch($param);
        if (!$rt->STS) {
            $conn->rollback();
            return $rt;
        } else {
            $conn->submitTransaction();
            return $rt;
        }
    }

    /**
     * 信用额度申请
     */
    public function showRequestCreditOp()
    {
        $m_client_member = M('client_member');

        if (isset($_GET['uid'])) {
            $member_id = intval($_GET['uid']);
            // 最后一次的提交记录
            $m_suggest = new member_credit_suggestModel();
            $last_suggest = $m_suggest->orderBy('uid desc')->find(array(
                'member_id' => $member_id,
                'operator_id' => $this->user_id
            ));
        } else {
            $request_id = intval($_GET['request_id']);
            // 最后一次的提交记录
            $m_suggest = new member_credit_suggestModel();
            $last_suggest = $m_suggest->find(array(
                'uid' => $request_id,
                'branch_id' => $this->user_info['branch_id'],
                'request_type' => 1
            ));

            Tpl::output('source', 'request_credit');
        }

        $m_member_credit_suggest = new member_credit_suggestModel();
        if ($last_suggest) {
            $last_suggest['suggest_detail_list'] = $m_member_credit_suggest->getSuggestDetailBySuggestId($last_suggest['uid']);
            $last_suggest['suggest_rate'] = $m_member_credit_suggest->getSuggestRateBySuggestId($last_suggest['uid']);
            $member_id = $last_suggest['member_id'];
        }

        $ret = userClass::getMemberCreditReferenceInfo($member_id, $this->user_id);
        Tpl::output('data', $ret->DATA);

        //会员信息
        $client_info = $m_client_member->find(array('uid' => $member_id));
        if (!$client_info) {
            showMessage('Invalid Id!');
        }
        Tpl::output('client_info', $client_info);

        //收入调查
        $m_member_income_research = M('member_income_research');
        $income_research = $m_member_income_research->orderBy('uid DESC')->find(array('member_id' => $member_id, 'operator_id' => $this->user_id, 'researcher_type' => 1));
        Tpl::output('income_research', $income_research);

        $m_member_assets = M('member_assets');
        $member_asset = $m_member_assets->select(array('member_id' => $member_id, 'asset_state' => 100));
        Tpl::output('member_asset', $member_asset);

        $co_list = memberClass::getMemberCreditOfficerList($member_id);
        $co_list = resetArrayKey($co_list, "officer_id");

        $credit_loan = credit_loanClass::getProductInfo();
        $prod_list = loan_productClass::getActiveSubProductListById($credit_loan['uid']);
        foreach ($prod_list as $k => $v) {
            $rate = loan_productCLass::getMinMonthlyRate($v['uid'], 'max');
            $prod_list[$k]['max_rate_mortgage'] = $rate;
        }
        $prod_list = resetArrayKey($prod_list, "uid");
        Tpl::output("product_list", $prod_list);

        //co提交记录
        $r = new ormReader();
        $sql = "SELECT operator_id FROM member_credit_suggest WHERE member_id = $member_id AND branch_id = " . $this->user_info['branch_id'] . " AND request_type = 0 GROUP BY operator_id ORDER BY operator_id ASC";
        $co_ids = $r->getRows($sql);
        $co_suggest_list = array();
        foreach ($co_ids as $co_id) {
            $co_last_suggest = $m_suggest->orderBy('uid desc')->find(array('member_id' => $member_id, 'operator_id' => $co_id['operator_id']));
            $co_last_suggest['suggest_detail_list'] = $m_member_credit_suggest->getSuggestDetailBySuggestId($co_last_suggest['uid']);
            $co_last_suggest['suggest_rate'] = $m_member_credit_suggest->getSuggestRateBySuggestId($co_last_suggest['uid']);
            $co_suggest_list[$co_id['operator_id']] = $co_last_suggest;
        }

        if (count($co_ids) > 1) {
            $co_list[0] = array('officer_name' => '--AVG--');
            $avg_co_suggest = array();
            $avg_co_suggest['uid'] = 0;
            $avg_co_suggest['operator_name'] = '--AVG--';
            $avg_co_suggest['client_request_credit'] = intval(sumArrayByKey($co_suggest_list, "client_request_credit") / count($co_ids));
            $avg_co_suggest['monthly_repayment_ability'] = intval(sumArrayByKey($co_suggest_list, "monthly_repayment_ability") / count($co_ids));
            $avg_co_suggest['default_credit'] = intval(sumArrayByKey($co_suggest_list, "default_credit") / count($co_ids));
            $avg_co_suggest['max_credit'] = intval(sumArrayByKey($co_suggest_list, "max_credit") / count($co_ids));
            $avg_co_suggest['credit_terms'] = intval(sumArrayByKey($co_suggest_list, "credit_terms") / count($co_ids));

            $suggest_detail_list = array();
            foreach ($member_asset as $asset) {
                $suggest_detail_list[$asset['uid']] = array_merge($asset, array('credit' => 0, 'valuation' => 0, 'count' => 0));
            }

            $suggest_rate = array();
            foreach ($prod_list as $product) {
                $suggest_rate[$product['product_id']] = array_merge($product, array('rate_no_mortgage' => 0, 'rate_mortgage1' => 0, 'rate_mortgage2' => 0, 'count' => 0));
            }

            foreach ($co_suggest_list as $co_suggest) {
                foreach ($suggest_detail_list as $k1 => $v1) {
                    $co_suggest_detail_list = $co_suggest['suggest_detail_list'][$k1];
                    if ($co_suggest_detail_list) {
                        $suggest_detail_list[$k1]['credit'] += floatval($co_suggest_detail_list['credit']);
                        $suggest_detail_list[$k1]['valuation'] += floatval($co_suggest_detail_list['valuation']);
                        $suggest_detail_list[$k1]['count'] += 1;
                    }
                }

                foreach ($suggest_rate as $k2 => $v2) {
                    $co_suggest_rate = $co_suggest['suggest_rate'][$k2];
                    if ($co_suggest_rate) {
                        $suggest_rate[$k2]['rate_no_mortgage'] += $co_suggest_rate['rate_no_mortgage'];
                        $suggest_rate[$k2]['rate_mortgage1'] += $co_suggest_rate['rate_mortgage1'];
                        $suggest_rate[$k2]['rate_mortgage2'] += $co_suggest_rate['rate_mortgage2'];
                        $suggest_rate[$k2]['count'] += 1;
                    }
                }
            }

            foreach ($suggest_detail_list as $key => $val) {
                $suggest_detail_list[$key]['credit'] = $val['count'] > 0 ? $val['credit'] / $val['count'] : 0;
                $suggest_detail_list[$key]['valuation'] = $val['count'] > 0 ? $val['valuation'] / $val['count'] : 0;
            }

            foreach ($suggest_rate as $key => $val) {
                $suggest_rate[$key]['rate_no_mortgage'] = $val['count'] > 0 ? $val['rate_no_mortgage'] / $val['count'] : 0;
                $suggest_rate[$key]['rate_mortgage1'] = $val['count'] > 0 ? $val['rate_mortgage1'] / $val['count'] : 0;
                $suggest_rate[$key]['rate_mortgage2'] = $val['count'] > 0 ? $val['rate_mortgage2'] / $val['count'] : 0;
            }

            $avg_co_suggest['suggest_detail_list'] = $suggest_detail_list;
            $avg_co_suggest['suggest_rate'] = $suggest_rate;
            $co_suggest_list[0] = $avg_co_suggest;
        } else {
            $avg_co_suggest = $co_last_suggest;
        }

        Tpl::output('last_suggest', $last_suggest ?: $avg_co_suggest);
        Tpl::output('co_suggest_list', $co_suggest_list);

        $m_member_assets = M('member_assets');
        $member_assets = $m_member_assets->select(array('member_id' => $member_id, 'asset_state' => 100));
        Tpl::output('member_assets', $member_assets);
        Tpl::output('co_list', $co_list);

        $rate_set = global_settingClass::getCreditGrantRateAndDefaultInterest();
        Tpl::output('rate_set', $rate_set);

        $m_site_branch_limit = M('site_branch_limit');
        $approve_credit_limit = $m_site_branch_limit->field('limit_value')->find(array('branch_id' => $this->user_info['branch_id'], 'limit_key' => 'approve_credit_limit'));
        Tpl::output('approve_credit_limit', intval($approve_credit_limit));

        Tpl::output('member_id', $member_id);
        $this->outputMemberReferenceInfo($member_id, $this->user_id, $this->user_name);

        Tpl::showPage('client.request.credit');
    }

    private function outputMemberReferenceInfo($member_id, $bm_id, $bm_name)
    {
        //cbc
        $m_client_cbc = M('client_cbc');
        $client_cbc = $m_client_cbc->find(array('client_id' => $member_id, "client_type" => 0, 'state' => 1));
        Tpl::output('client_cbc', $client_cbc);

        // 收入的调查
        $m_income = new member_income_researchModel();
        $row = $m_income->orderBy('research_time desc')->find(array(
            'member_id' => $member_id,
            'operator_id' => $this->user_id
        ));
        $total_income = null;
        if ($row) {
            $total_income = ($row['income_rental_land'] + $row['income_rental_housing'] + $row['income_business']
                + $row['income_salary'] + $row['income_others']);
        }
        Tpl::output('total_income', $total_income);

        //check list
        $r = new ormReader();
        $sql = "SELECT cert_type,COUNT(uid) cert_count FROM member_verify_cert WHERE member_id = $member_id AND verify_state = " . certStateEnum::PASS . " GROUP BY cert_type";
        $check_list = $r->getRows($sql);
        $check_list = resetArrayKey($check_list, 'cert_type');

        $sql = "SELECT COUNT(uid) guarantee_num FROM member_guarantee WHERE member_id = " . $member_id . " AND relation_state = 100";
        $guarantee_num = $r->getOne($sql);
        $check_list[certificationTypeEnum::GUARANTEE_RELATIONSHIP]['cert_count'] = intval($guarantee_num);
        Tpl::output('check_list', $check_list);

        //assets
        $sql = "SELECT a.uid,a.asset_type,a.asset_name,e.evaluation valuation,e.remark FROM member_assets a LEFT JOIN ( SELECT * FROM member_assets_evaluate WHERE evaluator_type = 1 AND branch_id = '" . $this->user_info['branch_id'] . "' ORDER BY uid DESC ) e ON a.uid=e.member_assets_id WHERE a.asset_state >='" . assetStateEnum::CERTIFIED . "' and a.member_id= $member_id GROUP BY a.member_id,a.uid ORDER BY e.uid DESC;";
        $bm_assets_list = $r->getRows($sql);
        $assets_total = 0;
        foreach ($bm_assets_list as $v) {
            $assets_total += $v['valuation'];
        }
        Tpl::output('bm_assets_list', $bm_assets_list);
        Tpl::output('total_assets_evaluation', $assets_total);

        //business scene
        $m_member_business_scene = M('member_business_photo');
        $business_scene = $m_member_business_scene->select(array(
            'member_id' => $member_id,
            'type' => businessPhotoTypeEnum::PLACE_SCENE
        ));
        Tpl::output('business_scene', $business_scene);

        //business photo
        $business_photo = $m_member_business_scene->select(array(
            'member_id' => $member_id,
            'type' => businessPhotoTypeEnum::CONTRACT
        ));
        Tpl::output('business_photo', $business_photo);

        $co_list = memberClass::getMemberCreditOfficerList($member_id);
        $co_list = resetArrayKey($co_list, "officer_id");
        $co_research = array();
        $co_last_research = credit_officerClass::getLastSubmitMemberIncomeResearch($bm_id, $member_id);
        if (!count($co_last_research)) {
            $co_research[$bm_id] = array(
                "operator_name" => $bm_name
            );
        } else {
            $co_last_research['total_income'] = $co_last_research['income_rental_land'] + $co_last_research['income_rental_housing'] + $co_last_research['income_business'] + $co_last_research['income_salary'] + $co_last_research['income_others'];
            $co_research[$bm_id] = $co_last_research;
        }
        foreach ($co_list as $co_k => $co) {
            $co_last_research = credit_officerClass::getLastSubmitMemberIncomeResearch($co['officer_id'], $member_id) ?: array();
            if (!count($co_last_research)) {
                $co_research[$co['officer_id']] = array(
                    "operator_name" => $co['officer_name']
                );
            } else {
                $co_last_research['total_income'] = $co_last_research['income_rental_land'] + $co_last_research['income_rental_housing'] + $co_last_research['income_business'] + $co_last_research['income_salary'] + $co_last_research['income_others'];
                $co_research[$co['officer_id']] = $co_last_research;

            }
        }
        $member_industry_info = memberClass::getMemberIndustryInfo($member_id);
        Tpl::output('member_industry_info', $member_industry_info);
        Tpl::output("co_research", $co_research);

        $m_industry_place = M("common_industry_place");
        $place_lst = $m_industry_place->getAll();
        Tpl::output("business_place", $place_lst ? $place_lst->toArray() : array());
    }

    /**
     * Bm评估
     */
    public function saveBmRequestCreditOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $params['officer_id'] = $this->user_id;
        $params['request_type'] = 1;

        $increase_credit = $params['increase_credit'];
        $asset_id = $params['asset_id'];
        $asset_credit = array();
        foreach ($increase_credit as $key => $val) {
            $asset_credit[] = array(
                'asset_id' => $asset_id[$key],
                'credit' => $val,
            );
        }
        $params['asset_credit'] = $asset_credit;

        $product_id_arr = $params['product_id'];
        $product_name_arr = $params['product_name'];
        $rate_no_mortgage_arr = $params['rate_no_mortgage'];
        $rate_mortgage1_arr = $params['rate_mortgage1'];
        $rate_mortgage2_arr = $params['rate_mortgage2'];
        $suggest_rate = array();
        foreach ($product_id_arr as $key => $product_id) {
            $suggest_rate[] = array(
                'product_id' => $product_id,
                'product_name' => $product_name_arr[$key],
                'rate_no_mortgage' => $rate_no_mortgage_arr[$key],
                'rate_mortgage1' => $rate_mortgage1_arr[$key],
                'rate_mortgage2' => $rate_mortgage2_arr[$key],
            );
        }
        $params['rate_credit'] = $suggest_rate;

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = credit_officerClass::submitMemberSuggestCredit($params);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage($rt->MSG);
        } else {
            $conn->rollback();
            showMessage($rt->MSG);
        }
    }

    /**
     * Bm提交
     * @param $p
     * @return result
     * @throws Exception
     */
    public function submitRequestCreditToHqOp($p)
    {
        $uid = intval($p['uid']);
        $m = new member_credit_suggestModel();
        $row = $m->getRow(array('uid' => $uid, 'state' => memberCreditSuggestEnum::CREATE));
        if (!$row) {
            return new result(false, 'Param Error!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        //目的是让当前这一条成为唯一的让hq审批的申请
        $sql = "update member_credit_suggest set state = 0 WHERE branch_id = " . $this->user_info['branch_id'] . " AND member_id = " . $row['member_id'] . " AND state = " . memberCreditSuggestEnum::PENDING_APPROVE;
        $rt = $m->conn->execute($sql);
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

    /*
     * bm自己权限范围内的授信
     */
    public function submitRequestCreditToFastGrantOp($p)
    {
        //判断权限
        $uid = intval($p['uid']);
        $m = new member_credit_suggestModel();
        $row = $m->getRow(array('uid' => $uid, 'state' => memberCreditSuggestEnum::CREATE));
        if (!$row) {
            return new result(false, 'Param Error!');
        }

        $m_site_branch_limit = M('site_branch_limit');
        $approve_credit_limit = $m_site_branch_limit->field('limit_value')->find(array('branch_id' => $this->user_info['branch_id'], 'limit_key' => 'approve_credit_limit'));
        $approve_credit_limit = $approve_credit_limit ?: 0;
        if (intval(($row->max_credit)) > intval($approve_credit_limit)) {
            return new result(false, "No Permission to grant credit more than " . $approve_credit_limit);
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();

        //目的是让当前这一条成为唯一的让hq审批的申请
        $sql = "update member_credit_suggest set state = 0 WHERE branch_id = " . $this->user_info['branch_id'] . " AND member_id = " . $row['member_id'] . " AND state = " . memberCreditSuggestEnum::PENDING_APPROVE;
        $rt = $conn->execute($sql);
        if (!$rt->STS) {
            $conn->rollback();
            return new result(false, 'Submit Failure!');
        }

        $row->state = memberCreditSuggestEnum::PENDING_APPROVE;
        $row->update_time = Now();
        $rt = $row->update();
        if (!$rt->STS) {
            $conn->rollback();
            return new result(false, 'Submit Failure!');
        }

        //模拟hq插入grant记录，一定不需要投票
        $param['suggest_id'] = $uid;
        $param['operator_id'] = $this->user_id;
        $param['operator_name'] = $this->user_name;
        $param['operator_type'] = 1;//bm
        $param['is_start_transaction_outside'] = 1;//外部启动事务

        $param['member_id'] = $row->member_id;
        $param['client_request_credit'] = $row->client_request_credit;
        $param['monthly_repayment_ability'] = $row->monthly_repayment_ability;
        $param['invalid_terms'] = $row->credit_terms;
        $param['default_credit'] = $row->default_credit;
        $param['remark'] = $row->remark;
        $param['committee_member'] = array($this->user_id);//默认自己做投票人

        $class_credit_grant = new member_credit_grantClass();
        $ret_bm_suggest = $class_credit_grant->getBmCreditSuggestDetailById($uid);
        $bm_suggest = $ret_bm_suggest->DATA['bm_suggest'];
        //构造$asset_id/increase_credit;
        $increase_credit = array();
        $asset_id = array();
        $bm_suggest_detail = $bm_suggest['suggest_detail_list'];
        if (count($bm_suggest_detail)) {
            foreach ($bm_suggest_detail as $bsd_item) {
                $increase_credit[] = $bsd_item['credit'];
                $asset_id[] = $bsd_item['member_asset_id'];
            }
        }
        $param['asset_id'] = $asset_id;
        $param['increase_credit'] = $increase_credit;

        //构造rate
        $bm_suggest_rate = $bm_suggest['suggest_rate'];
        $arr_product_id = array();
        $arr_product_name = array();
        $arr_rate_no_mortgage = array();
        $arr_rate_mortgage1 = array();
        $arr_rate_mortgage2 = array();
        foreach ($bm_suggest_rate as $rate_item) {
            $arr_product_id[] = $rate_item['product_id'];
            $arr_product_name[] = $rate_item['product_name'];
            $arr_rate_no_mortgage[] = $rate_item['rate_no_mortgage'];
            $arr_rate_mortgage1[] = $rate_item['rate_mortgage1'];
            $arr_rate_mortgage2[] = $rate_item['rate_mortgage2'];
        }
        $param['product_id'] = $arr_product_id;
        $param['product_name'] = $arr_product_name;
        $param['rate_no_mortgage'] = $arr_rate_no_mortgage;
        $param['rate_mortgage1'] = $arr_rate_mortgage1;
        $param['rate_mortgage2'] = $arr_rate_mortgage2;

        $rt = $class_credit_grant->commitCreditApplication($param);//里面有判断是bm的话自动完成投票
        if (!$rt->STS) {
            $conn->rollback();
            return new result(false, $rt->MSG);
        }

        $conn->submitTransaction();
        return new result(true, 'Submit Successful!');
    }

    /**
     * 信用申请列表
     */
    public function getRequestCreditHistoryOp()
    {
        $operator_id = intval($_GET['operator_id']);
        $user_info = M('um_user')->find(array('uid' => $operator_id));
        Tpl::output("user_info", $user_info);

        $member_id = intval($_GET['member_id']);
        $client_info = M('client_member')->find(array('uid' => $member_id));
        Tpl::output("client_info", $client_info);

        $m_member_credit_suggest = new member_credit_suggestModel();
        $credit_suggest = $m_member_credit_suggest->orderBy('uid DESC')->select(array('member_id' => $member_id, 'operator_id' => $operator_id));
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

        Tpl::output('member_id', $member_id);
        Tpl::showPage('client.request.credit.history');
    }

    /**
     * 信用审核投票列表
     * @param $p
     * @return array
     */
    public function getCreditGrantListOp($p)
    {
        $member_id = intval($p['member_id']);
        $filter = array(
            'member_id' => $member_id,
            'state' => 100,
        );
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $m_member_credit_grant = new member_credit_grantModel();
        $rt = $m_member_credit_grant->getCreditGrantList($pageNumber, $pageSize, $filter);
        $data = $rt->DATA;
        $rows = $data['rows'];
        $total = $data['total'];
        $pageTotal = $data['page_total'];

        $credit_loan = credit_loanClass::getProductInfo();
        $prod_list = loan_productClass::getActiveSubProductListById($credit_loan['uid']);
        foreach ($prod_list as $k => $v) {
            $rate = loan_productCLass::getMinMonthlyRate($v['uid'], 'max');
            $prod_list[$k]['max_rate_mortgage'] = $rate;
        }
        $prod_list = resetArrayKey($prod_list, "uid");

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "product_list" => $prod_list,
        );
    }

    /**
     *  信用申请
     */
    public function requestCreditOp()
    {
        $msg_task_list = taskControllerClass::getPendingTaskMsgList($this->user_info['branch_id'], userTaskTypeEnum::BM_REQUEST_FOR_CREDIT);
        Tpl::output("msg_task_list", $msg_task_list);
        Tpl::showPage('request.credit');
    }

    /**
     * 获取授信提交列表
     * @param $p
     * @return array
     */
    public function getRequestCreditListOp($p)
    {
        $search_text = trim($p['search_text']);
        $verify_state = intval($p['verify_state']);
        $r = new ormReader();
        $sql = "SELECT mcs.*,cm.display_name,cm.login_code,mcg.vote_time FROM member_credit_suggest mcs"
            . " LEFT JOIN client_member cm ON mcs.member_id = cm.uid 
            left join member_credit_grant mcg on mcs.uid=mcg.credit_suggest_id"
            . " WHERE 1 = 1";

        if ($verify_state == memberCreditSuggestEnum::CREATE) {
            $sql = "SELECT mcs.*,cm.display_name,cm.login_code,mcg.vote_time FROM member_credit_suggest mcs"
                . " LEFT JOIN client_member cm ON mcs.member_id = cm.uid 
                left join member_credit_grant mcg on mcs.uid=mcg.credit_suggest_id"
                . " INNER JOIN member_credit_request mcr ON mcr.member_id = mcs.member_id"
                . " WHERE mcr.state = " . creditRequestStateEnum::CREATE;
            $sql .= " AND mcs.state = 0 AND mcs.uid in (select max(uid) from member_credit_suggest where mcs.branch_id=" . intval($this->user_info['branch_id']) . " AND mcs.request_type = 1 group by member_id)";
        } else if ($verify_state == memberCreditSuggestEnum::PENDING_APPROVE) {
            $str = "(" . memberCreditSuggestEnum::PENDING_APPROVE . ',' . memberCreditSuggestEnum::APPROVING . ")";
            $sql .= " AND mcs.state in $str and mcs.branch_id=" . intval($this->user_info['branch_id']) . " AND mcs.request_type = 1";
        } else {
            $sql .= " AND mcs.state = $verify_state and mcs.branch_id=" . intval($this->user_info['branch_id']) . " AND mcs.request_type = 1";
        }

        if ($search_text) {
            $sql .= " AND cm.display_name like '%" . $search_text . "'";
        }

        $sql .= " ORDER BY mcs.update_time DESC";

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        foreach ($rows as $key => $val) {
            $sql = "select e.*,a.asset_type,a.mortgage_state from member_credit_suggest_detail e left join member_assets a on a.uid=e.member_asset_id
            where e.credit_suggest_id='" . $val['uid'] . "' ";
            $list = $r->getRows($sql);
            $rows[$key]['suggest_detail_list'] = $list;
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "state" => $verify_state,
        );
    }

    /**
     * 核销申请
     */
    public function requestWrittenOffOp()
    {
        Tpl::showPage('request.written.off');
    }


    public function getContractListByKeySearchOp($p)
    {
        $key_word = trim($p['search_text']);
        $key_type = $p['key_type'];
        $m_contract = new loan_contractModel();
        $list = $m_contract->searchContractListForWrittenOff($key_type, $key_word);
        return array(
            "sts" => true,
            "data" => $list,
        );

    }

    public function contractWrittenOffDetailOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $contract_id = $params['uid'];

        $m_loan_contract = new loan_contractModel();
        $loan_contract = $m_loan_contract->find(array('uid' => $contract_id));
        if (!$loan_contract) {
            showMessage('Invalid contract id:' . $contract_id);
        }

        if ($params['form_submit'] == 'ok') {

            $remark = trim($params['remark']);
            $rt = loan_written_offClass::addWrittenOffRequest($contract_id, $remark, $this->user_id);
            if ($rt->STS) {
                showMessage('Apply success');
            } else {
                showMessage('Apply fail:' . $rt->MSG);
            }
        }

        $data = $loan_contract;
        $rt = loan_written_offClass::calculateContractWriteOffLoss($contract_id);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }
        $data['loss_info'] = $rt->DATA;

        $client_info = loan_contractClass::getLoanContractMemberInfo($contract_id);
        $data['client_info'] = $client_info;

        $request_record = loan_written_offClass::getLastWriteOffRequest($contract_id);
        $data['request_record'] = $request_record;

        Tpl::output('data', $data);
        Tpl::showpage('contract.written.off.detail');

    }

    /**
     * 获取合同信息BY SN
     * @param $p
     * @return array
     */
    public function getContractInfoBySnOp($p)
    {
        $contract_sn = trim($p['search_text']);
        $m_loan_contract = M('loan_contract');
        $loan_contract = $m_loan_contract->find(array('contract_sn' => $contract_sn));
        if (!$loan_contract) {
            return array(
                "sts" => true,
                "data" => array(),
            );
        }
        $data = $loan_contract;
        $uid = $loan_contract['uid'];
        $rt = loan_written_offClass::calculateContractWriteOffLoss($uid);
        if (!$rt->STS) {
            return array(
                "sts" => true,
                "data" => array(),
            );
        }
        $data['loss_info'] = $rt->DATA;

        $client_info = loan_contractClass::getLoanContractMemberInfo($uid);
        $data['client_info'] = $client_info;

        $request_record = loan_written_offClass::getLastWriteOffRequest($uid);
        $data['request_record'] = $request_record;

        $is_submit = true;
        if ($loan_contract['state'] < loanContractStateEnum::PENDING_DISBURSE || $loan_contract['state'] >= loanContractStateEnum::COMPLETE) {
            $is_submit = false;
            $hint = 'This state contract can\'t be written off.';
        }
        if ($request_record && $request_record['state'] == writeOffStateEnum::APPROVING) {
            $is_submit = false;
            $hint = 'The last application was under review.';
        }

        return array(
            "sts" => true,
            "data" => $data,
            "is_submit" => $is_submit,
            "hint" => $hint,
            "request_record" => $request_record,
        );
    }

    /**
     * @param $p
     * @return result
     */
    public function addWrittenOffRequestOp($p)
    {
        $uid = intval($p['uid']);
        $remark = trim($p['remark']);
        $rt = loan_written_offClass::addWrittenOffRequest($uid, $remark, $this->user_id);
        if ($rt->STS) {
            return new result(true, 'Add Successful.');
        } else {
            return new result(false, $rt->MSG);
        }

    }

    /**
     *获取申请核销记录
     */
    public function getWrittenOffListOp($p)
    {
        $creator_id = $this->user_id;
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = loan_written_offClass::getWriteOffRequest($pageNumber, $pageSize, $creator_id);
        return $data;
    }


    /**
     * 贷款专员
     */
    public function creditOfficerOp()
    {
        $r = new ormReader();
        $sql = "select u.uid,u.user_code,u.user_name,u.mobile_phone,u.user_position,u.user_status,u.remark from um_user u LEFT JOIN site_depart d ON u.depart_id = d.uid where d.branch_id = '" . $this->user_info['branch_id'] . "' and u.user_position = '" . userPositionEnum::CREDIT_OFFICER . "'";
        $list = $r->getRows($sql);
        Tpl::output('list', $list);
        Tpl::showPage('credit.officer');
    }

    /**
     * 贷款专员详细信息
     */
    public function showCreditOfficerDetailOp()
    {
        $uid = intval($_GET['uid']);
        $cash = userClass::getPassbookBalanceOfUser($uid);
        Tpl::output('cash', $cash);
        $r = new ormReader();
        // 用内链，剔除不存在的member
        $sql = "select m.*,c.credit,c.credit_balance from member_follow_officer f inner join client_member m on  m.uid=f.member_id left join member_credit c on c.member_id=m.uid where f.officer_id='$uid' and f.is_active='1' group by f.member_id order by f.update_time desc";
        $client_list = $r->getRows($sql);
        Tpl::output('client_list', $client_list);
        $sql1 = "select * from um_user_track where user_id = '$uid' ORDER BY sign_time desc";
        $track_list = $r->getRows($sql1);
        $track_list_new = array();
        foreach ($track_list as $k => $v) {
            $track_list_new[$v['sign_day']][] = $v;
        }
        Tpl::output('track_list', $track_list_new);

        $date_start = $_GET['start_date'] ?: date("Y-m-d", strtotime(dateAdd(Now(), -7)));
        $date_end = $_GET['end_date'] ?: date('Y-m-d');
        $condition = array(
            "date_start" => $date_start,
            "date_end" => $date_end,
        );
        Tpl::output("condition", $condition);

        $filters = array(
            "start_date" => $date_start,
            "end_date" => $date_end,
        );
        $m_um_user_track = new um_user_trackModel();
        $track_arr = $m_um_user_track->getTrackList($uid, $filters);
        Tpl::output('coord_json', my_json_encode($track_arr));

        Tpl::output('uid', $uid);
        Tpl::output('show_tab', $_GET['show_tab'] ?: 'co_co_cash_on_hand');

        Tpl::showPage('credit.officer.detail');
    }

    public function showUserDayTraceOp()
    {
        $params = array_merge($_GET,$_POST);
        $user_id = $params['user_id'];
        $day = $params['date'];
        $m = new um_user_trackModel();
        $list = $m->getDayTraceList($user_id,$day);

        // 格式化数据
        $coordinate = array();
        foreach( $list as $v ){
            $coordinate[] = array(
                'x' => $v['coord_x'],
                'y' => $v['coord_y'],
                'location' => $v['location'],
            );
        }

        Tpl::output('coordinates',$coordinate);

        Tpl::showpage('credit.officer.day.trace');
    }

    /**
     * 逾期合同
     */
    public function overdueContractOp()
    {
        Tpl::showPage('overdue.contract');
    }

    /**
     * 获取client列表
     * @param $p
     * @return array
     */
    public function getOverdueContractOp($p)
    {
        if ($p['search_text']) {
            $where = " and obj_guid = '" . $p['search_text'] . "' ";
        }
        $r = new ormReader();
        $sql = "select uid,obj_guid,login_code,phone_id from client_member where branch_id = '" . $this->user_info['branch_id'] . "' $where ";
        $m_rows = $r->getRows($sql);
        $m_ids = implode(',', array_column($m_rows, 'obj_guid')) ?: 0;
        $m_rows_new = array();
        foreach ($m_rows as $k => $v) {
            $m_rows_new[$v['obj_guid']]['member_id'] = $v['uid'];
            $m_rows_new[$v['obj_guid']]['login_code'] = $v['login_code'];
            $m_rows_new[$v['obj_guid']]['phone_id'] = $v['phone_id'];
        }

        $sql = "select a.obj_guid,c.uid,c.contract_sn,c.currency,c.apply_amount,s.receivable_date,sum(s.amount) amount,sum(s.actual_payment_amount) actual_payment_amount from loan_account a left join loan_contract c ON a.uid = c.account_id left join loan_product p on p.uid=c.product_id  left join loan_installment_scheme s on s.contract_id=c.uid where a.obj_guid IN($m_ids) and c.state in('" . loanContractStateEnum::PENDING_DISBURSE . "','" . loanContractStateEnum::PROCESSING . "') and s.state !='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "' and date_format(s.receivable_date,'%Y%m%d') < '" . date('Ymd') . "' group by c.uid ";
        $sql .= " order by c.uid desc ";

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $contract_co_sql = "select *,max(uid) from co_overdue_contract_task WHERE state != 0 GROUP BY contract_id ORDER BY uid desc";
        $contract_co_rows = $r->getRows($contract_co_sql);
        $contract_co_rows_new = array();
        foreach ($contract_co_rows as $k => $v) {
            $contract_co_rows_new[$v['contract_id']]['co_id'] = $v['co_id'];
            $contract_co_rows_new[$v['contract_id']]['co_name'] = $v['co_name'];
        }
        foreach ($rows as $k => $v) {
            $rows[$k]['member_id'] = $m_rows_new[$v['obj_guid']]['member_id'];
            $rows[$k]['login_code'] = $m_rows_new[$v['obj_guid']]['login_code'];
            $rows[$k]['phone_id'] = $m_rows_new[$v['obj_guid']]['phone_id'];
            $rows[$k]['co_id'] = $contract_co_rows_new[$v['uid']]['co_id'];
            $rows[$k]['co_name'] = $contract_co_rows_new[$v['uid']]['co_name'];
        }
        $total = $data->count;
        $pageTotal = $data->pageCount;

        $sql = "select u.uid,u.user_code,u.user_name,u.mobile_phone,u.user_position,u.user_status,u.remark from um_user u LEFT JOIN site_depart d ON u.depart_id = d.uid where d.branch_id = '" . $this->user_info['branch_id'] . "' and u.user_position = '" . userPositionEnum::CREDIT_OFFICER . "'";
        $co_list = $r->getRows($sql);
        Tpl::output('list', $co_list);
        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "co_list" => $co_list,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 修改Overdue Contract co
     * @param $p
     * @return result
     */
    public function editOverdueContractCoOp($p)
    {
        $p['operator_id'] = $this->user_info['uid'];
        $p['operator_name'] = $this->user_info['user_name'];
        $rt = credit_officerClass::setOverdueContractCo($p);
        return $rt;
    }

    /**
     * 逾期合同
     */
    public function overdueOp()
    {
        Tpl::showPage('overdue.co');
    }

    public function getOverdueForCoOp($p)
    {
        $r = new ormReader();
        $sql = "SELECT mfo.officer_id,mfo.officer_name,COUNT(DISTINCT(cm.uid)) member_count,COUNT(DISTINCT(lc.uid)) loan_count FROM member_follow_officer mfo"
            . " INNER JOIN um_user uu ON uu.uid = mfo.officer_id"
            . " INNER JOIN site_depart sd ON sd.uid = uu.depart_id"
            . " INNER JOIN client_member cm ON cm.uid = mfo.member_id"
            . " INNER JOIN loan_account la ON la.obj_guid = cm.obj_guid"
            . " INNER JOIN loan_contract lc ON lc.account_id = la.uid"
            . " INNER JOIN loan_installment_scheme lis ON lis.contract_id = lc.uid"
            . " WHERE mfo.officer_type = 0 AND sd.branch_id = " . intval($this->user_info['branch_id'])
            . " AND lc.state >= " . loanContractStateEnum::PENDING_DISBURSE . " AND lc.state <= " . loanContractStateEnum::PROCESSING
            . " AND lis.state != " . schemaStateTypeEnum::CANCEL . " AND lis.state!= " . schemaStateTypeEnum::COMPLETE . " AND date_format(lis.receivable_date,'%Y%m%d') < " . qstr(date('Ymd'))
            . " GROUP BY mfo.officer_id";

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
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
        );
    }

    public function overdueContractForCoOp()
    {
        $co_id = intval($_GET['co_id']);
        $m_um_user = M('um_user');
        $co_info = $m_um_user->find(array('uid' => $co_id));
        Tpl::output('co_info', $co_info);

        $r = new ormReader();
        $sql = "select u.uid,u.user_code,u.user_name,u.mobile_phone,u.user_position,u.user_status,u.remark from um_user u LEFT JOIN site_depart d ON u.depart_id = d.uid where d.branch_id = '" . $this->user_info['branch_id'] . "' and u.user_position = '" . userPositionEnum::CREDIT_OFFICER . "'";
        $co_list = $r->getRows($sql);
        Tpl::output('co_list', $co_list);
        Tpl::showPage('overdue.contract.co');
    }

    public function getOverdueContractForCoOp($p)
    {
        $co_id = intval($p['co_id']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $r = new ormReader();
        $sql = "SELECT cm.uid member_id,cm.display_name,lc.virtual_contract_sn,lc.currency,lis.* FROM member_follow_officer mfo"
            . " INNER JOIN client_member cm ON cm.uid = mfo.member_id"
            . " INNER JOIN loan_account la ON la.obj_guid = cm.obj_guid"
            . " INNER JOIN loan_contract lc ON lc.account_id = la.uid"
            . " INNER JOIN loan_installment_scheme lis ON lis.contract_id = lc.uid"
            . " WHERE mfo.officer_id = $co_id"
            . " AND lc.state >= " . loanContractStateEnum::PENDING_DISBURSE . " AND lc.state <= " . loanContractStateEnum::PROCESSING
            . " AND lis.state != " . schemaStateTypeEnum::CANCEL . " AND lis.state!= " . schemaStateTypeEnum::COMPLETE . " AND date_format(lis.receivable_date,'%Y%m%d') < " . qstr(date('Ymd'))
            . " ORDER BY lis.receivable_date DESC";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $scheme_ids = array_column($rows, 'uid');
            $scheme_id_str = "(" . implode(',', $scheme_ids) . ")";
            $sql = "SELECT * FROM co_overdue_contract_task WHERE scheme_id IN $scheme_id_str AND state > 0";
            $overdue_contract_task = $r->getRows($sql);
            $overdue_contract_task = resetArrayKey($overdue_contract_task, 'scheme_id');
            foreach ($rows as $key => $row) {
                $row['overdue_days'] = intval((time() - strtotime($row['receivable_date'])) / (24 * 3600));
                $row['overdue_amount'] = $row['amount'] - $row['paid_operation_fee'] - $row['paid_interest'] - $row['paid_principal'];
                $row['overdue_contract_task'] = $overdue_contract_task[$row['uid']];
                $rows[$key] = $row;
            }
        }
        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    public function getCoCheckOverdueListOp()
    {
        $scheme_id = intval($_GET['scheme_id']);
        $co_id = intval($_GET['co_id']);
        $r = new ormReader();
        $sql = "SELECT cm.uid member_id,cm.display_name,lc.virtual_contract_sn,lc.currency,lis.* FROM client_member cm"
            . " INNER JOIN loan_account la ON la.obj_guid = cm.obj_guid"
            . " INNER JOIN loan_contract lc ON lc.account_id = la.uid"
            . " INNER JOIN loan_installment_scheme lis ON lis.contract_id = lc.uid"
            . " WHERE lis.uid = $scheme_id";
        $scheme_info = $r->getRow($sql);

        if ($scheme_info['state'] != schemaStateTypeEnum::CANCEL || $scheme_info['state'] != schemaStateTypeEnum::COMPLETE) {
            $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($scheme_info['uid']);
            $payable_amount = $scheme_info['amount'] - $scheme_info['actual_payment_amount'];
            $scheme_info['payable_amount'] = $payable_amount + $penalty;
        }

        Tpl::output('scheme_info', $scheme_info);
        Tpl::output('co_id', $co_id);

        $m_loan_contract_dun = M('loan_contract_dun');
        $check_list = $m_loan_contract_dun->orderBy('uid DESC')->select(array('scheme_id' => $scheme_id));
        Tpl::output('check_list', $check_list);
        $m_co_overdue_contract_task = M('co_overdue_contract_task');
        $overdue_contract_task = $m_co_overdue_contract_task->orderBy('uid DESC')->find(array('scheme_id' => $scheme_id, 'co_id' => $co_id));
        Tpl::output('overdue_contract_task', $overdue_contract_task);
        Tpl::showPage('overdue.contract.co.check');
    }

    /**
     * 积分
     */
    public function pointOp()
    {
        Tpl::showPage('point');
    }

    /**
     * 资产评估
     */
    public function showAssetsEvaluateOp()
    {
        $member_id = intval($_GET['uid']);
        $m_member_assets = new member_assetsModel();
        $bm_assets_list = $m_member_assets->getMemberAssets($this->user_id, $member_id);
        Tpl::output('bm_assets_list', $bm_assets_list);

        $asset_ids = array_column($bm_assets_list, 'uid');
        $co_assets_list = array();
        foreach ($asset_ids as $uid) {
            $co_assets_list[$uid] = $m_member_assets->getCoAssetEvaluationByUid($uid);
        }
        Tpl::output('co_assets_list', $co_assets_list);

        $m_member_follow_officer = M('member_follow_officer');
        $co_list = $m_member_follow_officer->select(array('member_id' => $member_id));
        Tpl::output('co_list', $co_list);

        Tpl::output('member_id', $member_id);
        Tpl::showPage('client.assets_evaluate');
    }

    /**
     * 资产评估历史
     */
    public function showAssetEvaluateHistoryOp()
    {
        $asset_id = intval($_GET['uid']);
        $member_id = intval($_GET['member_id']);
        $operator_id = intval($_GET['operator_id']) ?: $this->user_id;
        $m_member_assets = M('member_assets');
        $asset_info = $m_member_assets->find(array('uid' => $asset_id));
        Tpl::output('asset_info', $asset_info);

        $m_um_user = M('um_user');
        $operator_info = $m_um_user->find(array('uid' => $operator_id));
        Tpl::output('operator_info', $operator_info);

        $m_member_assets_evaluate = M('member_assets_evaluate');
        $list = $m_member_assets_evaluate->orderBy('evaluate_time DESC')->select(array('member_assets_id' => $asset_id, 'operator_id' => $operator_id));
        Tpl::output('list', $list);

        Tpl::output('member_id', $member_id);
        Tpl::showPage('client.assets_evaluate.history');
    }

    /**
     * Bm评估资产
     */
    public function editBmAssetEvaluateOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        if ($params['form_submit'] == 'ok') {
            $params['id'] = $params['asset_id'];
            $params['valuation'] = $params['evaluation'];
            $params['officer_id'] = $this->user_info['uid'];
            $params['evaluator_type'] = 1;
            $ret = credit_officerClass::submitMemberAssetsEvaluate($params);
            if ($ret->STS) {
                showMessage('Add evaluate success.', getUrl('branch_manager', 'showAssetsEvaluate', array('uid' => $params['member_id']), true, BACK_OFFICE_SITE_URL));
            } else {
                showMessage('Add evaluate fail.', getUrl('branch_manager', 'editBmAssetEvaluate', array('uid' => $params['asset_id'], 'member_id' => $params['member_id']), false, BACK_OFFICE_SITE_URL));
            }

        } else {
            $asset_id = intval($_GET['uid']);
            $member_id = intval($_GET['member_id']);

            $m_member_assets = M('member_assets');
            $m_member_assets_evaluate = M('member_assets_evaluate');
            $asset_info = $m_member_assets->find(array('uid' => $asset_id));
            Tpl::output('asset_info', $asset_info);

            $cert_id = $asset_info['cert_id'];
            $m_member_verify_cert_image = M('member_verify_cert_image');
            $asset_images = $m_member_verify_cert_image->select(array('cert_id' => $cert_id));
            Tpl::output('asset_images', $asset_images);

            $row = $m_member_assets_evaluate->orderBy('uid DESC')->find(array('member_assets_id' => $asset_id, 'operator_id' => $this->user_id));
            Tpl::output('data', $row);

            //co
            $co_assets_list = $m_member_assets->getCoAssetEvaluationByUid($asset_id);
            Tpl::output('co_assets_list', $co_assets_list);

            Tpl::output('asset_id', $asset_id);
            Tpl::output('member_id', $member_id);
            Tpl::showPage('client.assets_evaluate.branch.edit');
        }
    }

    /**
     * 会员资产信息
     */
    public function showAssetsInformationOp()
    {
        $member_id = intval($_GET['uid']);
        $cert_type = certificationTypeEnum::HOUSE . ',' . certificationTypeEnum::CAR . ',' . certificationTypeEnum::LAND . ',' . certificationTypeEnum::MOTORBIKE;
        $m_member_verify_cert = M('member_verify_cert');
        $m_member_verify_cert_image = M('member_verify_cert_image');
        $pass_rows = $m_member_verify_cert->getVerifyCertList($member_id, $cert_type);
        $no_audit_rows = $m_member_verify_cert->getVerifyCertListLast($member_id, $cert_type);
        $all_rows = array_merge($pass_rows, $no_audit_rows);
        arsort($all_rows);
        $ids = implode(',', array_column($all_rows, 'uid')) ?: 0;
        $cert_rows = $m_member_verify_cert->getVerifyCertByIds($ids);
        $img_rows = $m_member_verify_cert_image->getVerifyCertImagesByIds($ids);
        $img_rows_new = array();
        foreach ($img_rows as $k => $v) {
            $img_rows_new[$v['cert_id']][] = $v;
        }
        $list = array();
        foreach ($cert_rows as $k => $v) {
            $cert_rows[$k]['cert_img'] = $img_rows_new[$v['uid']];
            $list[$v['cert_type']][] = $cert_rows[$k];
        }
        Tpl::output('list', $list);
        Tpl::output('member_id', $member_id);
        Tpl::showPage('client.assets_information');
    }

    /**
     * 分行现金情况
     */
    public function cashInVaultOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);

        $branch_id = $this->user_info['branch_id'];
        $branch = new objectBranchClass($branch_id);
        $rt1 = $branch->getPassbookCurrencyBalance();
        $rt2 = $branch->getPassbookCurrencyAccountDetail();
        $arr = array_merge(array(), $rt1, $rt2);

        $currency_list = (new currencyEnum())->Dictionary();
        $branch_balance = array();
        foreach ($currency_list as $key => $currency) {
            $branch_balance['cash_' . $key] = ncPriceFormat(passbookAccountClass::getBalance($arr[$key]['balance'], $arr[$key]['outstanding']));
            $branch_balance['out_' . $key] = ncPriceFormat(passbookAccountClass::getOutstanding($arr[$key]['balance'], $arr[$key]['outstanding']));
        }

        Tpl::output('branch_balance', $branch_balance);
        Tpl::showPage('branch.cash_in_vault');
    }

    /**
     * 分行现金交易记录
     * @param $p
     * @return array
     */
    public function getCashInVaultListOp($p)
    {
        $branch_id = $this->user_info['branch_id'];
        $m_branch = M('site_branch');
        $branch = $m_branch->find(array('uid' => $branch_id));
        $branch_guid = $branch['obj_guid'];
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);

        $r = new ormReader();
        $sql = "SELECT * FROM biz_obj_transfer"
            . " WHERE (create_time BETWEEN " . qstr($d1) . " AND " . qstr($d2) . ")"
            . " AND (receiver_obj_guid = " . qstr($branch_guid) . " OR sender_obj_guid = " . qstr($branch_guid) . ")"
            . " AND state != 10";

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
            "branch_guid" => $branch_guid
        );
    }

    /**
     * 分行cashier co现金情况
     */
    public function cashOnHandOp()
    {
        $branch_id = $this->user_info['branch_id'];
        $r = new ormReader();
        $sql = "SELECT uu.uid,uu.user_name,uu.user_status,uu.user_position FROM um_user uu"
            . " INNER JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " INNER JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE uu.user_status = 1 AND (uu.user_position = " . qstr(userPositionEnum::TELLER) . " OR uu.user_position = " . qstr(userPositionEnum::CREDIT_OFFICER) . ") AND sb.uid = " . $branch_id
            . " ORDER BY uu.user_position DESC,uu.uid ASC";

        $cashier_list = $r->getRows($sql);

        $cash_on_hand = array();
        $currency_list = (new currencyEnum())->Dictionary();
        foreach ($cashier_list as $key => $cashier) {
            $rt1 = userClass::getPassbookBalanceOfUser($cashier['uid']);
            $rt2 = userClass::getPassbookAccountAllCurrencyDetailOfUser($cashier['uid']);
            $arr = array_merge(array(), $rt1, $rt2);
            foreach ($currency_list as $k => $currency) {
                $cashier['balance'][$k] = round(passbookAccountClass::getBalance($arr[$k]['balance'], $arr[$k]['outstanding']), 2);
                $cashier['outstanding'][$k] = passbookAccountClass::getOutstanding($arr[$k]['balance'], $arr[$k]['outstanding']);
            }
            $cash_on_hand[] = $cashier;
        }

        Tpl::output('cash_on_hand', $cash_on_hand);
        Tpl::showPage('branch.cash_on_hand');
    }

    /**
     * 单个cashier详情
     */
    public function branchCashierTransactionOp()
    {
        $uid = intval($_GET['uid']);
        Tpl::output('uid', $uid);
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);

        $user_info = M('um_user')->find(array('uid' => $uid));
        Tpl::output("user_info", $user_info);

        $rt1 = userClass::getPassbookBalanceOfUser($uid);
        $rt2 = userClass::getPassbookAccountAllCurrencyDetailOfUser($uid);
        $arr = array_merge(array(), $rt1, $rt2);
        $currency_list = (new currencyEnum())->Dictionary();
        $cash_on_hand = array();
        foreach ($currency_list as $k => $currency) {
            $cash_on_hand['balance'][$k] = round(passbookAccountClass::getBalance($arr[$k]['balance'], $arr[$k]['outstanding']), 2);
            $cash_on_hand['outstanding'][$k] = passbookAccountClass::getOutstanding($arr[$k]['balance'], $arr[$k]['outstanding']);
        }
        Tpl::output("cash_on_hand", $cash_on_hand);
        Tpl::showPage("branch.cashier.transaction");
    }

    /**
     * 单个cashier flow 列表
     * @param $p
     * @return array
     */
    public function getCashierTransactionsListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $currency = trim($p['currency']);
        $user_id = intval($p['uid']);
        $userObj = new objectUserClass($user_id);
        $passbook = $userObj->getUserPassbook();

        $filters = array();
        $filters['start_date'] = $p['date_start'];
        $filters['end_date'] = $p['date_end'];

        $m_passbook_account_flow = new passbook_account_flowModel();
        $data = $m_passbook_account_flow->searchFlowListByBookAndCurrency($passbook, $currency, $pageNumber, $pageSize, $filters);
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
        );
    }

    /**
     * 分行抵押情况
     */
    public function mortgagePageOp()
    {
        $branch_id = $this->user_info['branch_id'];
        $r = new ormReader();
        $sql = "SELECT b.`asset_type`,COUNT(a.uid) cnt FROM member_assets_storage a"
            . " LEFT JOIN member_assets b"
            . " ON a.`member_asset_id`=b.`uid`"
            . " WHERE a.`is_history` = 0 and a.is_pending = 0 AND a.`to_branch_id` = $branch_id group by b.asset_type";

        $mortgage_list = $r->getRows($sql);
        $mortgage = resetArrayKey($mortgage_list, 'asset_type');

        $asset_type = (new member_assetsClass())->getAssetType();
        $asset = array();
        foreach ($asset_type as $type) {
            $asset[$type] = intval($mortgage[$type]['cnt']);
        }

        Tpl::output('asset', $asset);
        Tpl::showPage("branch.storage.page");
    }

    /**
     * 抵押物列表
     * @param $p
     * @return array|ormPageResult
     */
    public function getMyStorageListOp($p)
    {
        $branch_id = $this->user_info['branch_id'];
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $r = new ormReader();

        $sql = "SELECT a.*,b.member_id,b.`asset_type`,b.`asset_name`,b.`asset_sn`,c.`login_code` member_name,c.`display_name`,c.`phone_id` FROM member_assets_storage a "
            . " LEFT JOIN member_assets b ON a.`member_asset_id`=b.`uid`"
            . " LEFT JOIN client_member c ON b.`member_id`=c.`uid`"
            . " WHERE a.`is_history` = 0 AND a.is_pending = 0 AND a.`to_branch_id` = $branch_id";
        if (trim($p['search_text'])) {
            $sql .= " AND (c.login_code like '%" . trim($p['search_text']) . "%'";
            $sql .= " OR c.display_name like '%" . trim($p['search_text']) . "%'";
            $sql .= " OR c.phone_id like '%" . trim($p['search_text']) . "%')";
        }
        $sql .= " order by c.uid,b.asset_type";

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
        );
    }

    public function journalVoucherOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        $trade_type = global_settingClass::getAllTradingType();
        Tpl::output("trade_type", $trade_type);
        Tpl::showpage('bm.journal.voucher');
    }

    public function getJournalVoucherDataOp($p)
    {
        $trade_id = $p['trade_id'];
        $obj = new objectBranchClass($this->user_info['branch_id']);
        $passbook = $obj->getPassbook();
        $trade_type = $p['trade_type'];
        $remark = $p['remark'];
        $date_start = $p['date_start'];
        $date_end = $p['date_end'];
        $pageNumber = $p['pageNumber'];
        $pageSize = $p['pageSize'];
        $filters = array(
            'trade_id' => $trade_id,
            'trade_type' => $trade_type,
            'remark' => $remark,
            'start_date' => $date_start,
            'end_date' => $date_end
        );
        return counter_codClass::getCounterVoucherData($passbook, $pageNumber, $pageSize, $filters);
    }
    public function coSubmitTaskListOp(){
        $m_task=new task_co_bmModel();
        $task_list=$m_task->getCoSubmitTaskByBranchId($this->user_info['branch_id']);
        Tpl::output("task_list",$task_list);
        Tpl::showPage("co.submit.task.list");
    }
}
