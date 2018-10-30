<?php

class operatorControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('operator,certification');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Operator");
        Tpl::setDir("operator");
        $this->getProcessingTask();
    }

    /**
     * 新创建client
     */
    public function newClientOp()
    {
        Tpl::showPage('new_client');
    }

    /**
     * 获取新创建client列表
     * @param $p
     * @return array
     */
    public function getClientListOp($p)
    {
        $verify_state = intval($p['verify_state']);
        $r = new ormReader();
        if ($verify_state <= newMemberCheckStateEnum::LOCKED) {
            $sql = "SELECT cm.*,sb.branch_name FROM client_member cm"
                . " LEFT JOIN um_user uu ON cm.operator_id = uu.uid"
                . " LEFT JOIN site_branch sb ON cm.branch_id = sb.uid"
                . " left join member_follow_officer mf on cm.uid=mf.member_id and mf.officer_type=1"
                . " Where mf.uid is null and cm.operate_state = " . $verify_state;
        } else {
            $sql = "SELECT cm.*,sb.branch_name FROM client_member cm"
                . " LEFT JOIN um_user uu ON cm.operator_id = uu.uid"
                . " LEFT JOIN site_branch sb ON cm.branch_id = sb.uid"
                . " Where cm.operate_state = " . $verify_state;
        }

        if (trim($p['search_text'])) {
            $sql .= " AND (cm.display_name LIKE '%" . trim($p['search_text']) . "%' OR cm.login_code LIKE '%" . trim($p['search_text']) . "%' OR cm.phone_id LIKE '%" . trim($p['search_text']) . "%')";
        }
        if ($verify_state > newMemberCheckStateEnum::LOCKED) {
            $sql .= " ORDER BY cm.operate_time DESC";
        } else {
            $sql .= " ORDER BY cm.uid DESC";
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
            "current_user" => $this->user_id,
            "verify_state" => $verify_state
        );
    }

    public function getTaskOfNewClientOp()
    {
        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid Id!');
        }
        if ($client_info['member_state'] != memberStateEnum::CREATE) {
            $conn = ormYo::Conn();
            $conn->startTransaction();
            $m_member = new client_memberModel();
            $row = $m_member->getRow($uid);
            $row->operator_id = $this->user_id;
            $row->operator_name = $this->user_name;
            $row->update_time = Now();
            $rt = $row->update();
            if (!$rt->STS) {
                $conn->rollback();
                showMessage("Failed got the client", getBackOfficeUrl("operator", "newClient"));
            }

            //只是绑定给operator-id
            $ret = memberClass::memberBindOfficer($uid, $this->user_id, 0, 1);
            if ($ret->STS) {
                $conn->submitTransaction();
                showMessage("Successful got the client", getBackOfficeUrl("operator", "newClient"));
            } else {
                $conn->rollback();
                showMessage($ret->MSG, getBackOfficeUrl("operator", "newClient"));
            }
        }

        $ret = taskControllerClass::handleNewTask($uid, userTaskTypeEnum::OPERATOR_NEW_CLIENT, $this->user_id);
        if (!$ret->STS) {
            showMessage("Failed To Get Task : " . $ret->MSG);
        }
        taskControllerClass::startBizTask($this->user_id);
    }

    /**
     * 检查新注册Client
     */
    public function checkNewClientOp()
    {

        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid Id!');
        }
        if ($client_info['operate_state'] != newMemberCheckStateEnum::LOCKED || $client_info['operator_id'] != $this->user_id) {
            showMessage('Invalid Task!');
        }

        $work_type = (new workTypeEnum)->Dictionary();
        Tpl::output('work_type', $work_type);

        Tpl::output('client_info', $client_info);
        Tpl::showPage('new_client.check');

    }

    /**
     * 处理结果
     * @param $p
     * @return result
     */
    public function submitCheckClientOp($p)
    {
        $uid = intval($p['uid']);
        $verify_state = trim($p['verify_state']);
        $remark = trim($p['remark']);
        $work_type = trim($p['work_type']);
        $task_arr = array(
            'member_id' => $uid,
            'operate_state' => $verify_state,
            'operate_remark' => $remark,
            'work_type' => $work_type,
        );

        /*
        $class_user_task = new userTaskClass($this->user_id);

        $rt = $class_user_task->finishedTask($uid, operateTypeEnum::NEW_CLIENT, $task_arr);
        */
        $rt = taskControllerClass::finishTask($uid, userTaskTypeEnum::OPERATOR_NEW_CLIENT, $this->user_id, objGuidTypeEnum::UM_USER, $task_arr);
        if (!$rt->STS) {
            return new result(false, $rt->MSG);
        } else {
            return new result(true, $rt->MSG);
        }

    }

    /**
     * 获取Branch列表
     * @param $p
     * @return array
     */
    public function getBranchListOp($p)
    {
        $search_text = trim($p['search_text']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $m_site_branch = new site_branchModel();
        $rt = $m_site_branch->searchBranchListByFreeText($search_text, $pageNumber, $pageSize);
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
     * 取消任务
     */
    public function abandonTaskOp()
    {
        /*
        $class_user_task = new userTaskClass($this->user_id);
        $rt = $class_user_task->cancelTask();
        if (!$rt->STS) {
            showMessage($rt->MSG);
        } else {
            Tpl::showPage('task.cancel.page');
        }*/
        $rt = taskControllerClass::cancelBizTask($this->user_id);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        } else {
            Tpl::showPage('task.cancel.page');
        }
    }

    /**
     * 贷款申请
     */
    public function loanConsultOp()
    {
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
        $sql = "SELECT lc.*,sb.branch_name FROM loan_consult lc LEFT JOIN site_branch sb ON lc.branch_id = sb.uid ";
        $sql .= " LEFT JOIN client_member cm ON lc.`member_id`=cm.uid";
        $sql .= " WHERE 1=1";
        if ($verify_state == loanConsultStateEnum::CREATE) {
            $sql .= " AND lc.state = " . $verify_state . "  AND (lc.member_id=0 OR (lc.`member_id`>0 AND cm.`operator_id` IS NOT NULL))";
        } elseif ($verify_state == loanConsultStateEnum::OPERATOR_APPROVED) {
            $sql .= " AND lc.state >= " . $verify_state;
        } else {
            $sql .= " AND lc.state = " . $verify_state;
        }
        if (trim($p['search_text'])) {
            $sql .= " AND lc.applicant_name like '%" . trim($p['search_text']) . "%' OR lc.contact_phone = '" . trim($p['search_text']) . "'";
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
     * 获取任务
     * @throws Exception
     */
    public function getTaskOfConsultOp()
    {
        $uid = intval($_GET['uid']);
        $m_loan_consult = new loan_consultModel();
        $row = $m_loan_consult->getRow($uid);
        if (!$row) {
            showMessage('Invalid Id!');
        }

        $ret = taskControllerClass::handleNewTask($uid, userTaskTypeEnum::OPERATOR_NEW_CONSULT, $this->user_id);
        if (!$ret->STS) {
            showMessage("Failed To Get Task : " . $ret->MSG);
        }
        taskControllerClass::startBizTask($this->user_id);
    }

    /**
     * 贷款申请审核
     */
    public function operateLoanConsultOp()
    {
        $uid = intval($_GET['uid']);
        $m_loan_consult = M('loan_consult');

        $row = $m_loan_consult->getRow($uid);
        if (!$row) {
            showMessage('Invalid Id!');
        }

        /*
        $class_user_task = new userTaskClass($this->user_id);
        $rt = $class_user_task->handleTask($uid, operateTypeEnum::LOAN_CONSULT);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }
        $this->getProcessingTask();
        */

        Tpl::output('apply_info', $row);
        Tpl::showpage('loan.consult.audit');
    }

    /**
     * 贷款申请处理
     * @param $p
     * @return result
     */
    public function submitLoanConsultOp($p)
    {
        $uid = intval($p['uid']);
        $verify_state = trim($p['verify_state']);
        $remark = trim($p['remark']);
        $task_arr = array(
            'uid' => $uid,
            'operate_state' => $verify_state,
            'operate_remark' => $remark,
        );

//        $class_user_task = new userTaskClass($this->user_id);
//        $rt = $class_user_task->finishedTask($uid, operateTypeEnum::LOAN_CONSULT, $task_arr);

        $rt = taskControllerClass::finishTask($uid, userTaskTypeEnum::OPERATOR_NEW_CONSULT, $this->user_id, objGuidTypeEnum::UM_USER, $task_arr);
        if (!$rt->STS) {
            return new result(false, $rt->MSG);
        } else {
            return new result(true, $rt->MSG);
        }
    }

    /**
     * 添加贷款申请
     */
    public function addLoanConsultOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $m_loan_consult = M('loan_consult');
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $p['operator_id'] = $this->user_id;
            $p['operator_name'] = $this->user_name;
            $p['operator_remark'] = '';
            $p['state'] = loanConsultStateEnum::OPERATOR_APPROVED;

            $rt = $m_loan_consult->addConsult($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('operator', 'loanConsult', array('type' => 'unprocessed'), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('operator', 'addLoanConsult', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $m_core_definition = M('core_definition');
            $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type'));
            Tpl::output('mortgage_type', $define_arr['mortgage_type']);

            $currency_list = (new currencyEnum())->Dictionary();
            Tpl::output('currency_list', $currency_list);

            $apply_source = (new loanConsultSourceEnum())->Dictionary();
            Tpl::output('request_source', $apply_source);
            Tpl::showpage('loan.consult.add');
        }
    }

    /**
     * Certification File
     */
    public function certificationFileOp()
    {
        $type = trim($_GET['type']) ?: certificationTypeEnum::ID;
        Tpl::output('type', $type);
        $certification_type = enum_langClass::getCertificationTypeEnumLang();
        Tpl::output('title', $certification_type[$type]);
        Tpl::showPage("certification");
    }

    /**
     * 获取资料列表
     * @param $p
     * @return array
     */
    public function getCertificationListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $filter = array(
            'cert_type' => $p['cert_type'],
            'member_name' => $p['member_name'],
            'operator_id' => $this->user_id,
        );
        if (intval($p['verify_state']) == certStateEnum::PASS) {
            $filter['verify_state'] = array(
                certStateEnum::PASS,
                certStateEnum::EXPIRED
            );
        } else {
            $filter['verify_state'] = intval($p['verify_state']);
        }

        $m = new member_verify_certModel();
        $page = $m->getPageList($pageNumber, $pageSize, $filter);
        $page['sts'] = true;
        $page['cur_uid'] = $this->user_id;
        return $page;
    }

    /**
     * 获取任务
     * @throws Exception
     */
    public function getTaskOfCertificationOp()
    {
        $uid = intval($_GET['uid']);
        $m_member_verify_cert = new member_verify_certModel();
        $info = $m_member_verify_cert->getVerifyCertDetailById($uid);
        if (!$info) {
            showMessage('Invalid Id!');
        }

        $member_id = $info['member_id'];
        $m_member_follow_officer = M('member_follow_officer');
        $operator = $m_member_follow_officer->orderBy('uid desc')->find(array(
            'member_id' => $member_id,
            'officer_type' => 1,
            'is_active' => 1
        ));

        if ($operator['officer_id'] != $this->user_id) {
            showMessage('The member does not belong to you.');
        }

        $ret = taskControllerClass::handleNewTask($uid, userTaskTypeEnum::OPERATOR_NEW_CERT, $this->user_id);
        if (!$ret->STS) {
            showMessage("Failed To Get Task : " . $ret->MSG);
        }
        taskControllerClass::startBizTask($this->user_id);
    }

    /**
     * 查看已审核资料
     * @throws Exception
     */
    public function showCertificationDetailOp()
    {
        $uid = intval($_GET['uid']);
        $m_member_verify_cert = new member_verify_certModel();
        $info = $m_member_verify_cert->getVerifyCertDetailById($uid);
        if (!$info) {
            showMessage('Invalid Id!');
        }
        $cert_type = $info['cert_type'];

        $sample_images = global_settingClass::getCertSampleImage();
        Tpl::output('cert_sample_images', $sample_images);
        $certification_type = enum_langClass::getCertificationTypeEnumLang();
        Tpl::output('certification_type', $certification_type);
        Tpl::output('title', $certification_type[$cert_type]);

//        if ($info['verify_state'] < certStateEnum::PASS) {
//            showMessage('The request has not been audited!');
//        }
        if ($info['verify_state'] == certStateEnum::LOCK && $info['auditor_id'] == $this->user_id) {
            $is_handle = 1;
        } else {
            $is_handle = 0;
        }
        Tpl::output('is_handle', $is_handle);

        $history = $m_member_verify_cert->getVerifyCertHistoryByType($info['member_id'], $info['cert_type']);
        Tpl::output('info', $info);
        Tpl::output('history', $history);

        if ($cert_type == certificationTypeEnum::FAIMILYBOOK) {
            $ID = $m_member_verify_cert->find(array('member_id' => $info['member_id'], 'cert_type' => certificationTypeEnum::ID, 'verify_state' => 10));
            Tpl::output('IDInfo', $ID);
        }

        switch ($cert_type) {
            case certificationTypeEnum::RESIDENT_BOOK :
            case certificationTypeEnum::ID :
            case certificationTypeEnum::PASSPORT :
            case certificationTypeEnum::BIRTH_CERTIFICATE:
            case certificationTypeEnum::FAIMILYBOOK :
                $country_code = (new nationalityEnum)->Dictionary();
                Tpl::output('country_code', $country_code);
                Tpl::showPage("certification.detail");
                break;
            case certificationTypeEnum::MOTORBIKE :
            case certificationTypeEnum::HOUSE :
            case certificationTypeEnum::STORE :
            case certificationTypeEnum::CAR :
            case certificationTypeEnum::LAND :
            case certificationTypeEnum::DEGREE:
                $m_member_assets = M('member_assets');
                $asset_info = $m_member_assets->find(array('cert_id' => $uid));
                Tpl::output('asset_info', $asset_info);
                $m_member_assets_owner = M('member_assets_owner');
                $asset_owner = $m_member_assets_owner->select(array('member_asset_id' => $asset_info['uid']));
                Tpl::output('asset_owner', $asset_owner);
                Tpl::showPage("certification.detail");
                break;
            case certificationTypeEnum::WORK_CERTIFICATION :
                $m_work = new member_workModel();
                $extend_info = $m_work->getRow(array(
                    'cert_id' => $uid
                ));
                Tpl::output('extend_info', $extend_info);
                Tpl::showPage('certification.work.detail');
                break;
            default:
                showMessage('Not supported type');
        }
    }

    /**
     * 资料认证
     * @return result
     * @throws Exception
     */
    public function certificationConfirmOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);
        $p['auditor_id'] = $this->user_id;
        $rt = taskControllerClass::finishTask($uid, userTaskTypeEnum::OPERATOR_NEW_CERT, $this->user_id, objGuidTypeEnum::UM_USER, $p);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        } else {
            $data = $rt->DATA;
            showMessage($rt->MSG, getUrl('operator', 'certificationFile', array('type' => $data['cert_type']), false, BACK_OFFICE_SITE_URL));
        }
    }

    /**
     * 会员列表
     * @param $p
     * @return array
     */
    public function getGrandCreditListOp($p)
    {
        $r = new ormReader();
        if (trim($p['type'] == 'all')) {
            $sql = "SELECT cm.uid uid FROM client_member cm WHERE cm.member_state != 0";
            if (trim($p['search_text'])) {
                $sql .= " AND cm.display_name LIKE '%" . trim($p['search_text']) . "%' OR cm.login_code LIKE '%" . trim($p['search_text']) . "%' OR cm.phone_id LIKE '%" . trim($p['search_text']) . "%'";
            }
            $sql .= " ORDER BY cm.uid DESC";
        } else {
            $sql = "SELECT cm.uid uid,max(mcl.create_time) last_cert_time FROM client_member cm INNER JOIN member_verify_cert mvc ON cm.uid = mvc.member_id INNER JOIN member_cert_log mcl ON mvc.uid = mcl.cert_id WHERE cm.member_state != 0 AND cm.member_state != 20 AND mcl.state = 0";
            if (trim($p['search_text'])) {
                $sql .= " AND cm.display_name LIKE '%" . trim($p['search_text']) . "%' OR cm.login_code LIKE '%" . trim($p['search_text']) . "%' OR cm.phone_id LIKE '%" . trim($p['search_text']) . "%'";
            }
            $sql .= " GROUP BY cm.uid ORDER BY last_cert_time DESC";
        }

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $member_ids = array_column($rows, 'uid');
            $member_id_str = "(" . implode(',', $member_ids) . ")";
            $sql = "SELECT client.*,loan.uid load_uid,loan.allow_multi_contract,c.credit,c.credit_balance FROM client_member client LEFT JOIN loan_account loan ON loan.obj_guid = client.obj_guid LEFT JOIN member_credit c ON c.member_id=client.uid WHERE client.uid IN $member_id_str ORDER BY client.uid DESC";
            $member_list = $r->getRows($sql);
            $member_list = resetArrayKey($member_list, 'uid');
            foreach ($rows as $key => $row) {
                $row = array_merge(array(), $row, $member_list[$row['uid']]);
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

    /**
     * 修改信用额度
     * @throws Exception
     */
    public function editCreditOp()
    {
        $r = new ormReader();
        $p = array_merge(array(), $_GET, $_POST);
        $m_member = new memberModel();
        $m_core_dictionary = M('core_dictionary');
        $member = $m_member->getRow(array(
            'obj_guid' => intval($p['obj_guid'])
        ));
        if (!$member) {
            showMessage('No member');
        }
        $setting = $m_core_dictionary->getDictionary('global_settings');
        $setting = my_json_decode($setting['dict_value']);
        if ($p['credit'] > $setting['operator_credit_maximum']) {
            showMessage('Credit limit.');
        }
        $m_loan_account = new loan_accountModel();
        $rt = $m_loan_account->getCreditInfo(intval($p['obj_guid']));
        $data = $rt->DATA;

        $m_credit = new member_creditModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member['uid']
        ));

        $credit_reference = credit_loanClass::getCreditLevelList();
        $cert_lang = enum_langClass::getCertificationTypeEnumLang();
        foreach ($credit_reference as $k => $v) {
            $item = $v;
            $cert_list = $item['cert_list'];
            foreach ($cert_list as $key => $value) {
                $cert_list[$key] = $cert_lang[$value];
            }
            $item['cert_list'] = $cert_list;
            $credit_reference[$k] = $item;
        }
        Tpl::output('credit_reference_value', $credit_reference);

        $sql = "SELECT loan.*,client.uid as member_id,client.display_name,client.alias_name,client.phone_id,client.email,client.co_name FROM loan_account as loan left join client_member as client on loan.obj_guid = client.obj_guid where loan.obj_guid = '" . intval($p['obj_guid']) . "'";
        $info = $r->getRow($sql);

        if ($p['form_submit'] == 'ok') {
            $conn = ormYo::Conn();
            $conn->startTransaction();
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $p['before_credit'] = $member_credit['credit'];
            $rt = $m_loan_account->editCredit($p);
            if (!$rt->STS) {
                unset($p['form_submit']);
                $conn->rollback();
                showMessage($rt->MSG, getUrl('operator', 'editCredit', $p, false, BACK_OFFICE_SITE_URL));

            }

            $sql = "SELECT mcl.uid FROM member_cert_log mcl INNER JOIN member_verify_cert mvc ON mvc.uid = mcl.cert_id WHERE mcl.state = 0 AND mvc.member_id = " . $info['member_id'];
            $log_id_list = $r->getRows($sql);
            if ($log_id_list) {
                $log_ids = array_column($log_id_list, 'uid');
                $log_id_str = '(' . implode(',', $log_ids) . ')';
                $sql = "UPDATE member_cert_log SET state = 100,update_time = '" . Now() . "' WHERE uid IN $log_id_str";
                $rt_1 = $r->conn->execute($sql);
                if (!$rt_1->STS) {
                    unset($p['form_submit']);
                    $conn->rollback();
                    showMessage($rt_1->MSG, getUrl('operator', 'editCredit', $p, false, BACK_OFFICE_SITE_URL));
                }
            }

            $conn->submitTransaction();
            showMessage($rt->MSG, getUrl('operator', 'grantCredit', array(), false, BACK_OFFICE_SITE_URL));
        } else {
            $m_loan_approval = M('loan_approval');
            $approvaling = $m_loan_approval->getRow(array('obj_guid' => intval($p['obj_guid']), 'state' => 0));//申请中
            if ($approvaling) {
                Tpl::output('approval_info', $approvaling);
            }

            $member_id = $member['uid'];
            $re = memberClass::getMemberSimpleCertResult($member_id);
            if (!$re->STS) {
                showMessage('Error: ' . $re->MSG);
            }
            $verifys = $re->DATA;

            $verify_field = enum_langClass::getCertificationTypeEnumLang();
            Tpl::output("verify_field", $verify_field);

            //获取co advice
            $m_member_credit_suggest = M('member_credit_suggest');
            $credit_suggest = $m_member_credit_suggest->orderBy('uid DESC')->find(array('member_id' => $info['member_id']));
            Tpl::output("credit_suggest", $credit_suggest);

            $sql = "SELECT SUM(valuation) assets_valuation FROM member_assets WHERE member_id = " . $info['member_id'] . " AND asset_state = 100";
            $assets_valuation = $r->getOne($sql);
            Tpl::output("assets_valuation", $assets_valuation);

            $sql = "SELECT update_time FROM member_assets WHERE member_id = " . $info['member_id'] . " AND asset_state = 100 ORDER BY uid DESC";
            $evaluate_time = $r->getOne($sql);
            Tpl::output("evaluate_time", $evaluate_time);

            $sql = "SELECT asset_type,SUM(valuation) assets_valuation FROM member_assets WHERE member_id = " . $info['member_id'] . " AND asset_state = 100 GROUP BY asset_type ORDER BY asset_type ASC";
            $assets_valuation_type = $r->getRows($sql);
            Tpl::output("assets_valuation_type", $assets_valuation_type);

            Tpl::output('info', $info);
            Tpl::output("verifys", $verifys);
            Tpl::output('credit_info', $member_credit);
            Tpl::output('loan_info', $data);
            Tpl::showPage("grant.credit.edit");
        }
    }

    /**
     * @param $p
     * @return result
     */
    public function getCheckDetailUrlOp($p)
    {
        $member_id = intval($p['member_id']);
        $cert_type = intval($p['cert_type']);

        $r = new ormReader();
        $sql = "SELECT * FROM member_verify_cert WHERE member_id = $member_id AND cert_type = $cert_type AND verify_state = 10 ORDER BY auditor_time DESC";
        $verify_cert = $r->getRow($sql);
        if (!$verify_cert) {
            return new result(false, 'Param Error!');
        }

        $url = getUrl('operator', 'showCertificationDetail', array('uid' => $verify_cert['uid'], 'source' => 'credit'), false, BACK_OFFICE_SITE_URL);
        return new result(true, 'Param Error!', $url);
    }

    /**
     * 获取client
     * @param $p
     * @return result
     */
    public function getClientInfoOp($p)
    {
        $country_code = trim($p['country_code']);
        $phone = trim($p['phone']);

        $format_phone = tools::getFormatPhone($country_code, $phone);
        $contact_phone = $format_phone['contact_phone'];

        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('phone_id' => $contact_phone, 'is_verify_phone' => 1));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

//        if ($client_info->member_state == memberStateEnum::LOCKING || $client_info->member_state == memberStateEnum::CANCEL) {
//            return new result(false, 'This account is not available！');
//        }

        $m_member_grade = M('member_grade');
        $member_grade = $m_member_grade->find(array('uid' => $client_info['member_grade']));
        $client_info['grade_code'] = $member_grade['grade_code'];

        $client_info['member_state'] = L('client_member_state_' . $client_info['member_state']);//使用这个状态值
        $client_info['member_icon'] = getImageUrl($client_info['member_icon'], imageThumbVersion::AVATAR);

        return new result(true, '', $client_info);
    }

    /**
     * 挂失页面
     */
    public function requestLockOp()
    {
        Tpl::showPage("request.lock");
    }

    /**
     * 挂失操作
     * @param $p
     * @return result
     */
    public function lockMemberOp($p)
    {
        $uid = intval($p['uid']);
        $m_client_member = M('client_member');
        $row = $m_client_member->getRow(array('uid' => $uid));
        $old_member_state = $row['member_state'];
        $member_property = $row['member_property'];
        $member_property_array = json_decode($member_property, true);
        $member_property_array['old_member_state'] = $old_member_state;
        if (!$row) {
            return new result(false, 'No eligible clients!');
        }

        if ($row->member_state == memberStateEnum::TEMP_LOCKING || $row->member_state == memberStateEnum::CANCEL
            || $row->member_state == memberStateEnum::SYSTEM_LOCKING
        ) {
            return new result(false, 'This account is not available！');
        }

        $row->member_state = memberStateEnum::TEMP_LOCKING;
        $row->member_property = json_encode($member_property_array);
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Lock successful');
        } else {
            return new result(false, 'Lock failure');
        }
    }

    /**
     * 挂失列表
     */
    public function lockListOp()
    {
        Tpl::showPage('member.lock');
    }

    /**
     * 获取挂失列表
     * @param $p
     * @return array
     */
    public function getLockListOp($p)
    {
        $search_text = trim($p['search_text']);
        $r = new ormReader();
        $sql = "SELECT * FROM client_member WHERE member_state=" . memberStateEnum::TEMP_LOCKING;

        if ($search_text) {
            $sql .= " AND (obj_guid= '$search_text' OR login_code like '%$search_text%' OR display_name like '%$search_text%' OR phone_id like '%$search_text%')";
        }
        $sql .= " ORDER BY update_time DESC";

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
     * 投诉建议
     */
    public function complaintAdviceOp()
    {
        Tpl::showPage('complaint.advice');
    }

    /**
     * 增加投诉或建议
     */
    public function addComplaintAdviceOp()
    {
        Tpl::showPage('complaint.advice.add');
    }

    /**
     * 获取投诉建议列表
     */
    public function getComplaintAdviceListOp($p)
    {
        $search_text = trim($p['search_text']);
        $type = trim($p['type']);
        $r = new ormReader();
        $sql = "SELECT * FROM complaint_advice WHERE uid>0";
        if ($type) {
            $sql .= " AND type = '" . $type . "'";
        }
        if ($search_text) {
            $sql .= " AND title like '%" . $search_text . "%'";
        }

        $sql .= " ORDER BY create_time DESC";
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
     * 新增投诉或建议
     */
    public function saveComplaintAdviceOp()
    {
        $type = trim($_POST['type']);
        $title = trim($_POST['title']);
        $content = $_POST['content'];
        $contact_name = trim($_POST['contact_name']);
        $country_code = trim($_POST['country_code']);
        $phone_number = trim($_POST['phone_number']);

        $format_phone = tools::getFormatPhone($country_code, $phone_number);
        $contact_phone = $format_phone['contact_phone'];
        $create_time = Now();
        $state = complaintAdviceEnum::CREATE;

        $arr = array(
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'contact_name' => $contact_name,
            'contact_phone' => $contact_phone,
            'create_time' => $create_time,
            'state' => $state
        );
        $m_complaint_advice = M("complaint_advice");
        $r = $m_complaint_advice->insert($arr);
        if ($r->STS) {
            showMessage('Add successfully', getUrl('operator', 'addComplaintAdvice', array(), false, BACK_OFFICE_SITE_URL));
        } else {
            showMessage('Add failure');
        }
    }

    /**
     *投诉建议详情
     */
    public function detailsOp()
    {
        $uid = $_GET['uid'];
        $r = new ormReader();
        $sql = 'SELECT * FROM complaint_advice WHERE uid=' . $uid;
        $data = $r->getRow($sql);
        Tpl::output('data', $data);
        Tpl::showPage('complaint.advice.details');
    }

    public function clientProfileIndexOp()
    {
        Tpl::showPage('member.profile');
    }

    public function clientProfileWorkTypeOp()
    {
        $uid = $_GET['uid'];
        $member_info = memberClass::getMemberBaseInfo($uid);
        $member_industry = memberClass::getMemberIndustryInfo($uid);
        Tpl::output("member_info", $member_info);
        Tpl::output("member_industry", $member_industry);
        $m_common_industry = M('common_industry');
        $industry_list = $m_common_industry->select(array('state' => 1));
        Tpl::output('industry_list', $industry_list);
        $work_type = (new workTypeEnum)->Dictionary();
        Tpl::output('work_type', $work_type);

        Tpl::showPage("member.profile.work_type");
    }

    public function submitClientProfileWorkTypeOp($p)
    {
        $member_id = intval($p['uid']);
        $is_with_business = intval($p['is_with_business']);
        $work_type = trim($p['work_type']);

        //处理industry_list
        $m_common_industry = M('common_industry');
        $industry_list = $m_common_industry->select(array('state' => 1));
        $arr_member_industry = array();
        if ($is_with_business) {
            foreach ($industry_list as $ik => $iv) {
                if (intval($p['industry_item_' . $iv['uid']]) > 0) {
                    $arr_member_industry[] = $iv['uid'];
                }
            }
        }

        $m_client_member = M('client_member');
        $row = $m_client_member->getRow(array('uid' => $member_id));
        $row->work_type = $work_type;
        $row->is_with_business = $is_with_business;
        $row->operate_time = Now();
        $ret = $row->update();
        if ($ret->STS) {
            memberClass::setMemberIndustry($member_id, $arr_member_industry);
        }
        return new result($ret->STS, $ret->STS ? 'Handle Successful!' : $ret->MSG);
    }

    public function clientProfileResidenceOp()
    {
        $uid = $_GET['uid'];
        $member_info = memberClass::getMemberBaseInfo($uid);
        Tpl::output("member_info", $member_info);

        $m_common_address = M('common_address');
        $residence = $m_common_address->getMemberResidencePlaceByGuid($member_info['obj_guid']);
        if ($residence) {
            $address_id = $residence['id4'];
            $m_core_tree = M('core_tree');
            $region_list = $m_core_tree->getParentAndBrotherById($address_id, 'region');
            Tpl::output('region_list', $region_list);
            Tpl::output('residence', $residence);
        }

        Tpl::showPage("member.profile.residence");
    }

    public function submitClientProfileResidenceOp($p)
    {
        $m_common_address = new common_addressModel();
        $rt = $m_common_address->insertMemberResidence($p);
        return $rt;
    }

    public function clientProfileBranchOp()
    {
        $uid = $_GET['uid'];
        $member_info = memberClass::getMemberBaseInfo($uid);
        Tpl::output("member_info", $member_info);

        $m_site_branch = M('site_branch');
        $branch_list = $m_site_branch->select(array('status' => 1));
        Tpl::output("branch_list", $branch_list);

        Tpl::showPage("member.profile.branch");
    }

    public function submitClientProfileBranchOp($p)
    {
        $member_id = intval($p['uid']);
        $branch_id = intval($p['branch_id']);
        $rt = memberClass::resetMemberBranch($member_id, $branch_id, $this->user_id, $this->user_name);
        return $rt;
    }

    public function submitConsultApplicantBranchOp($p)
    {
        $uid = intval($p['uid']);
        $branch_id = intval($p['branch_id']);
        $m_loan_consult = new loan_consultModel();
        $m_member = new memberModel();
        $consult = $m_loan_consult->getRow(array('uid' => $uid));
        $member = $m_member->getRow(array('phone_id' => $consult->contact_phone, 'is_verify_phone' => 1));
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            if ($member && !$member->branch_id) {
                $member->branch_id = $branch_id;
                $member->update_time = Now();
                $update = $member->update();
                if (!$update->STS) {
                    $conn->rollback();
                    return new result(false, 'Setting fail');
                }
            }

            $m_credit_officer = new credit_officerClass();
            $rt = $m_credit_officer->resetConsultApplicantBranch($uid, $branch_id, $this->user_id, $this->user_name);
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Setting fail');
            }
            $conn->submitTransaction();
            return new result(true, 'Setting successful');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    public function submitConsultApplicantStateOp($p)
    {
        $uid = intval($p['uid']);
        $state = intval($p['state']);
        $m_credit_officer = new credit_officerClass();
        $rt = $m_credit_officer->resetConsultApplicantState($uid, $state, $this->user_id);
        return $rt;
    }

    /**
     * 帮助文档
     */
    public function helpOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showpage('help');
    }

    /**
     * 获取帮助文档列表
     * @param $p
     * @return array
     */
    public function getHelpListOp($p)
    {
        $search_text = trim($p['search_text']);
        $type = intval($p['type']);
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $r = new ormReader();
        $sql = "SELECT * FROM common_cms WHERE (create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        if ($search_text) {
            $sql .= ' AND (help_title like "%' . $search_text . '%")';
        }
        if ($type == 1) {
            $sql .= " AND is_system = 0";
        } elseif ($type == 2) {
            $sql .= " AND is_system = 1";
        }
        $sql .= ' ORDER BY sort DESC,uid DESC';
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
            "pageSize" => $pageSize
        );
    }

    public function editHelpOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($_GET['uid']);
        $m_common_cms = M('common_cms');
        $help = $m_common_cms->find(array('uid' => $uid));
        if (!$help) {
            showMessage('Invalid Id!');
        }

        if ($p['form_submit'] == 'ok') {
            $update = array(
                'uid' => $uid,
                'category' => $p['category'],
                'help_title' => trim($p['help_title']),
                'help_content' => $p['help_content'],
                'state' => intval($p['is_show']) ? 100 : 10,
                'sort' => intval($p['sort']),
                'handler_id' => $this->user_id,
                'handler_name' => $this->user_name,
                'handle_time' => Now(),
            );
            $rt = $m_common_cms->update($update);
            if ($rt->STS) {
                showMessage('Edit Successful!', getUrl('operator', 'help', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage($rt->MSG);
            }
        } else {
            $help_category = (new helpCategoryEnum())->Dictionary();
            Tpl::output('help_category', $help_category);
            Tpl::output('help', $help);
            Tpl::showpage('help.edit');
        }
    }

    public function addSystemHelpOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $m_common_cms = M('common_cms');
            $category = $p['category'];
            $help_title = trim($p['help_title']);
            $help_content = $p['help_content'];
            $state = intval($p['is_show']) ? 100 : 10;
            $sort = intval($p['sort']);
            if (!$category || !$help_title || !$help_content) {
                showMessage('Invalid Param!');
            }

            $row = $m_common_cms->newRow();
            $row->category = $category;
            $row->help_title = $help_title;
            $row->help_content = $help_content;
            $row->handler_id = $this->user_id;
            $row->handler_name = $this->user_name;
            $row->create_time = Now();
            $row->handle_time = Now();
            $row->state = $state;
            $row->sort = $sort;
            $row->is_system = 1;
            $rt = $row->insert();

            if ($rt->STS) {
                showMessage('Add Successful!', getUrl('operator', 'help', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('operator', 'addSystemHelp', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $help_category = (new helpCategoryEnum())->Dictionary();
            Tpl::output('help_category', $help_category);
            Tpl::showpage('help.add');
        }
    }

    public function checkCbcOp()
    {
        Tpl::showPage("cbc");
    }

    public function getCbcListOp($p)
    {
        $search_text = trim($p['search_text']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $state = intval($p['state']) ?: 1;
        $m_client_cbc = M('client_cbc');
        $m_client_member = M('member');
        if ($state == 1) {
            $rows = $m_client_cbc->getClientCbcIds();
            $member_id_str = '';
            if ($rows) {
                $member_ids = array_column($rows, 'member_id');
                $member_id_str = implode(',', $member_ids);
            }
            $list = $m_client_member->getClientCbcNewList($member_id_str, $search_text, $pageNumber, $pageSize);
        } else {
            $list = $m_client_cbc->getClientCbcChecked($search_text, $pageNumber, $pageSize);
        }

        return $list->DATA;
    }

    public function addClientCbcOp()
    {
        $mid = intval($_GET['uid']);
        if (!$mid || $mid <= 0) {
            showMessage('Invalid Param!');
        }
        $m_client_cbc = M('client_cbc');
        $m_member = M('member');
        $history = $m_client_cbc->getCbcByMemberId($mid);
        $info = $m_member->getMemberInfoById($mid);
        Tpl::output('mid', $mid);
        Tpl::output('history', $history);
        Tpl::output('info', $info);
        Tpl::showpage('cbc.add');
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
     * member cbc历史列表
     */
    public function checkCbcClientHistoryOp()
    {
        $mid = intval($_GET['uid']);
        if (!$mid || $mid <= 0) {
            showMessage('Invalid Param!');
        }
        Tpl::output('mid', $mid);
        Tpl::showpage('cbc.history');
    }

    /**
     * member cbc历史列表
     */
    public function getCbcHistoryOp($p)
    {
        $mid = intval($p['uid']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $m_client_cbc = M('client_cbc');
        $history = $m_client_cbc->getCbcListByMemberId($mid, $pageNumber, $pageSize);
        return $history->DATA;
    }

    /**
     * member cbc历史详情
     */
    public function getClientCbcHistoryDetailOp()
    {
        $uid = intval($_GET['uid']);
        $m_client_cbc = M('client_cbc');
        $m_member = M('member');
        $detail = $m_client_cbc->getCbcDetailById($uid);
        $member_info = $m_member->getMemberInfoById($detail['member_id']);
        Tpl::output('mid', $detail['member_id']);
        Tpl::output('detail', $detail);
        Tpl::output('member_info', $member_info);
        Tpl::showpage('cbc.history.detail');
    }

    /**
     * sms
     */
    public function smsOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showpage('sms');
    }

    /**
     * @param $p
     * @return array
     */
    public function getSmsListOp($p)
    {
        $search_text = trim($p['search_text']);
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $r = new ormReader();
        $sql = "SELECT * FROM common_sms WHERE (create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "') AND task_state != " . smsTaskState::CANCEL;
        if ($search_text) {
            $sql .= " AND phone_id like '%" . $search_text . "%'";
        }
        if (1) {
            $sql .= " AND task_state = " . smsTaskState::SEND_FAILED;
        }
        $sql .= ' ORDER BY uid DESC';
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
            "pageSize" => $pageSize
        );
    }

    public function resendSmsOp($p)
    {
        $uid = intval($p['uid']);
        $m_common_sms = M('common_sms');
        $sms_row = $m_common_sms->getRow($uid);
        if (!$sms_row) {
            $data = array('state' => 'Resend Failed');
            return new result(false, 'Invalid Id!', $data);
        }
        if ($sms_row->task_state != smsTaskState::SEND_FAILED) {
            $data = array('state' => 'Resend Failed');
            return new result(false, 'Sms state error!', $data);
        }

        $smsHandler = new smsHandler();
        if ($sms_row->task_type == smsTaskType::VERIFICATION_CODE) {
            // 发送短信验证码
            $verify_code = mt_rand(100001, 999999);
            $contact_phone = $sms_row->phone_id;

            $rt = $smsHandler->sendVerifyCode($contact_phone, $verify_code);
            if (!$rt->STS) {
                $data = array('state' => 'Resend Failed');
                return new result(false, 'Send code fail: ' . $rt->MSG, $data);
            }
            $data = $rt->DATA;
            $content = $data->content;
            $conn = ormYo::Conn();
            $conn->startTransaction();
            try {
                $m_phone_verify_code = M('common_verify_code');
                $new_row = $m_phone_verify_code->newRow();
                $new_row->phone_country = $sms_row->phone_country;
                $new_row->phone_id = $contact_phone;
                $new_row->verify_code = $verify_code;
                $new_row->create_time = Now();
                $new_row->sms_id = $sms_row->uid;
                $insert = $new_row->insert();
                if (!$insert->STS) {
                    $conn->rollback();
                    return new result(false, 'Insert verify code fail');
                }

                $sms_row->task_state = smsTaskState::CANCEL;
                $sms_row->update_time = Now();
                $update = $sms_row->update();
                if (!$update->STS) {
                    $conn->rollback();
                    $data = array('state' => 'Resend Failed');
                    return new result(false, 'Update sms fail', $data);
                }
                $conn->submitTransaction();
                $data = array('content' => $content, 'state' => L('task_state_' . smsTaskState::SEND_SUCCESS));
                return new result(true, 'Resend successful!', $data);
            } catch (Exception $ex) {
                $conn->rollback();
                return new result(false, $ex->getMessage());
            }

        } else {
            $rt = $smsHandler->resend($uid);
            if ($rt->STS) {
                $data = $rt->DATA;
                $data = array('content' => $data->content, 'state' => L('task_state_' . smsTaskState::SEND_SUCCESS));
                return new result(true, 'Resend successful!', $data);
            } else {
                $data = array('state' => 'Resend Failed');
                return new result(true, 'Resend failed!', $data);
            }
        }
    }

    /**
     * 新创建client
     */
    public function opClientOp()
    {
        Tpl::showPage('operator_client');
    }

    public function consultationOp()
    {
        $r = new ormReader();
        $sql = "select count(*) from loan_consult where operator_id='" . $this->user_id . "' and contact_phone not in (select phone_id from client_member)";
        $no_register_cnt = $r->getOne($sql);
        Tpl::output("no_register_count", $no_register_cnt);
        $sql = "select count(*) from loan_consult where operator_id='" . $this->user_id . "'";
        $all_cnt = $r->getOne($sql);
        Tpl::output('all_count', $all_cnt);
        $msg_list = taskControllerClass::getPendingTaskMsgList($this->user_id, userTaskTypeEnum::OPERATOR_MY_CONSULT);
        Tpl::output("msg_task_list", $msg_list);


        if ($_GET['no_register']) {
            Tpl::output('param_no_register', 1);
        } else {
            Tpl::output('param_no_register', 0);
        }
        Tpl::showPage("consultation");
    }

    public function getMyConsultationOp($p)
    {
        $r = new ormReader();
        $sql = "SELECT lc.*,site_branch.branch_name FROM loan_consult lc"
            . " LEFT JOIN site_branch ON lc.branch_id=site_branch.uid"
            . " WHERE lc.operator_id = " . $this->user_id;
        if (trim($p['search_text'])) {
            $sql .= " AND (lc.contact_phone LIKE '%" . qstr2(trim($p['search_text'])) . "%' OR lc.applicant_name LIKE '%" . qstr2(trim($p['search_text'])) . "%')";
        }
        if ($p['no_register']) {
            $sql .= " AND lc.contact_phone not in (select phone_id from client_member)";
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
            "pageSize" => $pageSize
        );
    }

    public function showConsultPageOp()
    {
        $uid = $_GET['uid'];
        $sql = "SELECT lc.*,site_branch.branch_name from loan_consult lc left join site_branch on lc.branch_id=site_branch.uid"
            . " WHERE lc.uid = " . $uid;
        $r = new ormReader();
        $row = $r->getRow($sql);
        if (!$row) {
            showMessage("Invalid Params:No record found", getUrl("operator", "getMyConsultation", array(), false, BACK_OFFICE_SITE_URL));
        } else {
            $phone_id = $row['contact_phone'];
            $tmp_m = M("client_member");
            $tmp_row = $tmp_m->find(array("phone_id" => $phone_id));
            if ($tmp_row['uid'] > 0) {
                $client_info = memberClass::getMemberBaseInfo($tmp_row['uid']);
                Tpl::output('client_info', $client_info);
            }

            Tpl::output("consultation", $row);
            Tpl::showPage("consultation.item");
        }
    }


    public function clientChangePhotoOp()
    {
        Tpl::showPage('client.change.photo');
    }


    public function getClientChangePhotoRequestListOp($p)
    {

        $user_id = $this->user_id;
        $userObj = new objectUserClass($user_id);

        $pageNumber = $p['pageNumber'] ?: 1;
        $pageSize = $p['pageSize'] ?: 20;
        $state = $p['verify_state'] ?: bizStateEnum::CREATE;
        $search_text = $p['search_text'];

        $m = new member_change_photo_requestModel();
        $page_data = $m->getPageListByState($userObj->branch_id, $pageNumber, $pageSize, $state, array(
            'keyword' => $search_text
        ));

        return array(
            "sts" => true,
            "data" => $page_data->rows,
            "total" => $page_data->count,
            "pageNumber" => $pageNumber,
            "pageTotal" => $page_data->pageCount,
            "pageSize" => $pageSize
        );
    }

    public function getTaskOfChangeClientPhotoOp()
    {
        $task_id = $_GET['uid'];
        $ret = taskControllerClass::handleNewTask($task_id, userTaskTypeEnum::CHANGE_CLIENT_ICON, $this->user_id);
        if (!$ret->STS) {
            showMessage("Failed To Get Task : " . $ret->MSG);
        }
        taskControllerClass::startBizTask($this->user_id);

    }

    public function clientChangePhotoDetailOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $uid = intval($params['uid']);
        $m = new member_change_photo_requestModel();
        $request = $m->getRow($uid);
        if (!$request) {
            showMessage('Invalid request:' . $uid);
        }

        if ($params['form_submit'] == 'ok') {
            $params['user_id'] = $this->user_id;
            $params['user_name'] = $this->user_name;
            $rt = taskControllerClass::finishTask($params['uid'], userTaskTypeEnum::CHANGE_CLIENT_ICON, $this->user_id, objGuidTypeEnum::UM_USER, $params);
            if ($rt->STS) {
                showMessage('Check Success.');
            } else {
                showMessage($rt->MSG);
            }

        } else {

            $memberObj = new objectMemberClass($request->member_id);
            Tpl::output('client_info', $memberObj->object_info);
            Tpl::output('request_info', $request);
            Tpl::showPage('client.change.photo.check');
        }

    }


    /**
     * 获取地址选项
     * @param $p
     * @return array
     */
    public function getAreaListOp($p)
    {
        $pid = intval($p['uid']);
        $m_core_tree = M('core_tree');
        $list = $m_core_tree->getChildByPid($pid, 'region');
        return array('list' => $list);
    }

    public function openImageWindowOp()
    {
        Tpl::output("imgurl", $_GET['img']);
        Tpl::showPage("operator.image");
    }

    public function deviceApplyOp()
    {
        Tpl::showPage("device.apply");
    }

    /**
     * 获取新创建client列表
     * @param $p
     * @return array
     */
    public function getMemberDeviceListOp($p)
    {
        $verify_state = intval($p['verify_state']);
        $r = new ormReader();
        $sql = "select a.*,cm.login_code,cm.display_name from member_new_device_apply a left join client_member cm on a.member_id = cm.uid where a.state = " . $verify_state;
        if (trim($p['search_text'])) {
            $sql .= " AND (cm.display_name LIKE '%" . trim($p['search_text']) . "%' OR cm.login_code LIKE '%" . trim($p['search_text']) . "%' OR cm.phone_id LIKE '%" . trim($p['search_text']) . "%')";
        }

        $sql .= " ORDER BY cm.uid DESC";

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
            "current_user" => $this->user_id,
            "verify_state" => $verify_state
        );
    }

    public function getTaskOfDeviceApplyOp()
    {
        $uid = $_GET['uid'];
        $ret = taskControllerClass::handleNewTask($uid, userTaskTypeEnum::CHANGE_CLIENT_DEVICE, $this->user_id);
        if (!$ret->STS) {
            showMessage("Failed To Get Task : " . $ret->MSG);
        }
        taskControllerClass::startBizTask($this->user_id);
    }

    public function checkDeviceApplyOp()
    {
        $uid = $_GET['uid'];
        $info = member_deviceClass::getDeviceRowInfoByUid($uid);
        Tpl::output('info', $info);
        Tpl::showPage('device.apply.check');
    }

    /**
     * 处理结果
     * @param $p
     * @return result
     */
    public function submitCheckDeviceApplyOp($p)
    {
        $params['uid'] = intval($p['uid']);
        $params['state'] = trim($p['verify_state']);
        $params['remark'] = trim($p['remark']);
        $params['operator_id'] = $this->user_id;
        $params['operator_name'] = $this->user_name;
        $rt = taskControllerClass::finishTask($params['uid'], userTaskTypeEnum::CHANGE_CLIENT_DEVICE, $this->user_id, objGuidTypeEnum::UM_USER, $params);
        if (!$rt->STS) {
            return new result(false, $rt->MSG);
        } else {
            return new result(true, $rt->MSG);
        }

    }

    public function warningOfExpireDateOp()
    {
        Tpl::showPage('warning.expire.date');
    }

    public function getWarningOfExpireDateOp($p)
    {
        $r = new ormReader();
        $sql = "SELECT cm.*,site_branch.branch_name FROM client_member cm"
            . " INNER JOIN member_follow_officer mfo ON cm.uid = mfo.member_id"
            . " LEFT JOIN site_branch  ON cm.branch_id = site_branch.uid"
            . " WHERE mfo.officer_id = " . $this->user_id . " and mfo.officer_type = 1 and mfo.is_active = 1 AND cm.member_state = " . memberStateEnum::VERIFIED;

        if (intval($p['is_expired'])) {
            $time = Now();
        } else {
            $time = date("Y-m-d", strtotime(dateAdd(Now(), 90)));
        }
        $sql .= " AND cm.id_expire_time < " . qstr($time);
        if (trim($p['search_text'])) {
            $sql .= " AND (cm.display_name LIKE '%" . trim($p['search_text']) . "%' OR cm.login_code LIKE '%" . trim($p['search_text']) . "%' OR cm.phone_id LIKE '%" . trim($p['search_text']) . "%')";
        }

        $sql .= " ORDER BY cm.id_expire_time ASC";
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

    public function pendingVerifyOp()
    {
        Tpl::showPage('pending.verify');
    }

    public function getPendingVerifyListOp($p)
    {
        $r = new ormReader();
        $verify_type = memberIdentityClass::getIdentityType();
        $verify_type_keys = array_keys($verify_type);
        $verify_type_str = "(" . implode(',', $verify_type_keys) . ")";
        $sql = "SELECT cm.* FROM client_member cm"
            . " INNER JOIN member_follow_officer mfo ON cm.uid = mfo.member_id"
            . " INNER JOIN member_verify_cert mvc ON cm.uid = mvc.member_id"
            . " WHERE mfo.officer_id = " . intval($this->user_id)
            . " AND mfo.officer_type = 1 AND mfo.is_active = 1 AND cm.member_state = " . memberStateEnum::CHECKED
            . " AND mvc.cert_type IN $verify_type_str AND mvc.verify_state = " . intval(certStateEnum::PASS);

        if (trim($p['search_text'])) {
            $sql .= " AND (cm.display_name LIKE '%" . trim($p['search_text']) . "%' OR cm.login_code LIKE '%" . trim($p['search_text']) . "%' OR cm.phone_id LIKE '%" . trim($p['search_text']) . "%')";
        }

        $sql .= " GROUP BY mvc.member_id";

        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        foreach ($rows as $key => $row) {
            $sql = "SELECT * FROM member_verify_cert WHERE member_id = " . intval($row['uid']) . " AND cert_type IN $verify_type_str AND verify_state = " . intval(certStateEnum::PASS) . " GROUP BY cert_type";
            $cert_list = $r->getRows($sql);
            $cert_list = resetArrayKey($cert_list, 'cert_type');
            $row['cert_list'] = $cert_list;
            $rows[$key] = $row;
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "verify_type" => $verify_type,
        );
    }

    public function pendingVerifyDetailOp()
    {
        $member_id = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($member_id);

        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        $cert_list = memberIdentityClass::getMemberVerifiedCert($member_id);
        Tpl::output('cert_list', $cert_list);

        $verify_type = memberIdentityClass::getIdentityType();
        Tpl::output('verify_type', $verify_type);

        $member_verify_type = (new memberVerifyTypeEnum())->Dictionary();
        Tpl::output('member_verify_type', $member_verify_type);

        Tpl::showPage('pending.verify.detail');
    }

    public function changeMemberStateToVerifiedOp($p)
    {
        $member_id = intval($p['uid']);
        $verify_type = intval($p['verify_type']);
        $verify_remark = trim($p['verify_remark']);

        $member_state = memberStateEnum::VERIFIED;
        $rt = memberClass::changeMemberState($member_id, $member_state, $verify_remark, $this->user_id, $verify_type);
        return $rt;
    }



    public function warningOfOverdueLoanOp(){
        $list = loanReportClass::getOperatorClientOverdueLoan($this->user_info['uid']);
        Tpl::output('list', $list);
        Tpl::showPage("warning.overdue.loan");
    }


    public function clientChangeTradingPasswordIndexOp()
    {
        Tpl::showPage('client.change.trading.password');

    }

    public function getChangeTradingPasswordRequestListOp($p)
    {

        $search_text = $p['search_text'];
        $state = $p['verify_state'];
        $page_number = $p['pageNumber']?:1;
        $page_size = $p['pageSize']?:20;
        $filter = array(
            'search_text' => $search_text,
            'state' => $state
        );

        $m = new member_change_trading_password_requestModel();
        $page_data = $m->getListOfPage($page_number,$page_size,$filter);

        return array(
            "sts" => true,
            'state' => $state,
            "data" => $page_data->rows,
            "total" => $page_data->count,
            "pageNumber" => $page_number,
            "pageTotal" => $page_data->pageCount,
            "pageSize" => $page_size
        );
    }

    public function getTaskOfClientChangeTradingPasswordOp()
    {
        $task_id = $_GET['uid'];
        $ret = taskControllerClass::handleNewTask($task_id, userTaskTypeEnum::CLIENT_CHANGE_TRADING_PASSWORD, $this->user_id);
        if (!$ret->STS) {
            showMessage("Failed To Get Task : " . $ret->MSG);
        }
        taskControllerClass::startBizTask($this->user_id);
    }

    public function clientChangeTradingPasswordDetailOp()
    {
        $params = array_merge($_GET,$_POST);
        $uid = $params['uid'];
        $m = new member_change_trading_password_requestModel();
        $request = $m->find(array(
            'uid' => $uid
        ));
        if( !$request ){
            showMessage('No request info:'.$uid);
        }
        $member_id = $request['member_id'];
        $member_info = (new client_memberModel())->find(array(
            'uid' => $member_id
        ));
        if( !$member_info ){
            showMessage('Invalid member:'.$member_id);
        }


        if ($params['form_submit'] == 'ok') {
            $params['user_id'] = $this->user_id;
            $params['user_name'] = $this->user_name;

            $rt = taskControllerClass::finishTask($params['uid'], userTaskTypeEnum::CLIENT_CHANGE_TRADING_PASSWORD, $this->user_id, objGuidTypeEnum::UM_USER, $params);
            if ($rt->STS) {
                showMessage('Success.',getBackOfficeUrl('operator','clientChangeTradingPasswordIndex'));
            } else {
                showMessage($rt->MSG);
            }

        } else {

            Tpl::output('request',$request);
            Tpl::output('member_info',$member_info);
            Tpl::showPage('client.change.trading.password.check');
        }


    }

    public function relativeCertificateFileOp()
    {
        $type = trim($_GET['type']) ?: certificationTypeEnum::ID;
        Tpl::output('type', $type);
        $certification_type = enum_langClass::getCertificationTypeEnumLang();
        Tpl::output('title', $certification_type[$type]);
        Tpl::showPage("relative.certification");
    }

    public function getRelativeCertificationListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $filter = array(
            'cert_type' => $p['cert_type'],
            'search_text' => trim($p['search_text']),
            'operator_id' => $this->user_id,
        );
        if (intval($p['verify_state']) == certStateEnum::PASS) {
            $filter['verify_state'] = array(
                certStateEnum::PASS,
                certStateEnum::EXPIRED
            );
        } else {
            $filter['verify_state'] = intval($p['verify_state']);
        }

        $m = new member_relative_verify_certModel();
        $page = $m->getPageList($pageNumber, $pageSize, $filter);
        $page['sts'] = true;
        $page['cur_uid'] = $this->user_id;
        return $page;
    }

    public function getTaskOfRelativeCertificationOp()
    {
        $uid = intval($_GET['uid']);
        $m_member_verify_cert = new member_relative_verify_certModel();
        $info = $m_member_verify_cert->getRow($uid);
        if (!$info) {
            showMessage('Invalid Id!');
        }
        $member_id = $info['member_id'];
        $m_member_follow_officer = M('member_follow_officer');
        $operator = $m_member_follow_officer->orderBy('uid desc')->find(array(
            'member_id' => $member_id,
            'officer_type' => 1,
            'is_active' => 1
        ));

        if ($operator['officer_id'] != $this->user_id) {
            showMessage('The member does not belong to you.');
        }

        $ret = taskControllerClass::handleNewTask($uid, userTaskTypeEnum::OPERATOR_RELATIVE_NEW_CERT, $this->user_id);
        if (!$ret->STS) {
            showMessage("Failed To Get Task : " . $ret->MSG);
        }
        taskControllerClass::startBizTask($this->user_id);
    }

    public function showRelativeCertificationDetailOp()
    {
        $uid = intval($_GET['uid']);
        $m_member_verify_cert = new member_relative_verify_certModel();
        $info = $m_member_verify_cert->getVerifyCertDetailById($uid);
        if (!$info) {
            showMessage('Invalid Id!');
        }
        $cert_type = $info['cert_type'];

        $sample_images = global_settingClass::getCertSampleImage();
        Tpl::output('cert_sample_images', $sample_images);
        $certification_type = enum_langClass::getCertificationTypeEnumLang();
        Tpl::output('certification_type', $certification_type);
        Tpl::output('title', $certification_type[$cert_type]);

        if ($info['verify_state'] == certStateEnum::LOCK && $info['auditor_id'] == $this->user_id) {
            $is_handle = 1;
        } else {
            $is_handle = 0;
        }
        Tpl::output('is_handle', $is_handle);

        $history = $m_member_verify_cert->getVerifyCertHistoryByType($info['relative_id'], $info['cert_type']);

        Tpl::output('info', $info);
        Tpl::output('history', $history);

        if ($cert_type == certificationTypeEnum::FAIMILYBOOK) {
            $ID = $m_member_verify_cert->find(array('relative_id' => $info['relative_id'], 'cert_type' => certificationTypeEnum::ID, 'verify_state' => certStateEnum::PASS));
            Tpl::output('IDInfo', $ID);
        }

        switch ($cert_type) {
            case certificationTypeEnum::RESIDENT_BOOK :
            case certificationTypeEnum::ID :
            case certificationTypeEnum::PASSPORT :
            case certificationTypeEnum::BIRTH_CERTIFICATE:
            case certificationTypeEnum::FAIMILYBOOK :
                $country_code = (new nationalityEnum)->Dictionary();
                Tpl::output('country_code', $country_code);
                Tpl::showPage("relative.certification.detail");
                break;
            default:
                showMessage('Not supported type');
        }
    }


    public function relativeCertificationConfirmOp()
    {
        $p = array_merge(array(), $_GET, $_POST);

        $uid = intval($p['uid']);
        $p['auditor_id'] = $this->user_id;
        $rt = taskControllerClass::finishTask($uid, userTaskTypeEnum::OPERATOR_RELATIVE_NEW_CERT, $this->user_id, objGuidTypeEnum::UM_USER, $p);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        } else {
            $data = $rt->DATA;
            showMessage($rt->MSG, getUrl('operator', 'relativeCertificateFile', array('type' => $data['cert_type']), false, BACK_OFFICE_SITE_URL));
        }
    }

}
