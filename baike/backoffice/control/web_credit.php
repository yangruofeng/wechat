<?php

class web_creditControl extends back_office_baseControl
{
    public $is_bm;

    public function __construct()
    {
        parent::__construct();
        Language::read('operator,certification');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Operator");
        Tpl::setDir("web_credit");

        //is bm
        $is_bm = in_array($this->user_info['user_position'], array(userPositionEnum::BRANCH_MANAGER, userPositionEnum::ROOT)) ? true : false;
        $this->is_bm = $is_bm;
        Tpl::output('is_bm', $is_bm);
        $this->getProcessingTask();
    }

    //todo::判断是否为当前member的co或bm
    /**
     * 新创建client
     */
    public function clientOp()
    {
        $r = new ormReader();
        $sql = "SELECT count(cm.uid) cnt FROM client_member cm"
            . " INNER JOIN member_follow_officer mfo ON cm.uid = mfo.member_id"
            . " WHERE mfo.officer_id = " . $this->user_id . " and mfo.officer_type = 1 and mfo.is_active = 1 ";
        $all_cnt = $r->getOne($sql);
        $sql = "SELECT count(cm.uid) cnt FROM client_member cm"
            . " INNER JOIN member_follow_officer mfo ON cm.uid = mfo.member_id"
            . " WHERE mfo.officer_id = " . $this->user_id . " and mfo.officer_type = 1 and mfo.is_active = 1 " . " and (cm.member_state='" . memberStateEnum::CREATE . "' or cm.operate_state='" . newMemberCheckStateEnum::CLOSE . "')";
        $pending_check_cnt = $r->getOne($sql);
        $sql = "SELECT count(cm.uid) cnt FROM client_member cm"
            . " INNER JOIN member_follow_officer mfo ON cm.uid = mfo.member_id"
            . " WHERE mfo.officer_id = " . $this->user_id . " and mfo.officer_type = 1 and mfo.is_active = 1 " . " and ifnull(cm.branch_id,0)<=0";
        $no_branch_cnt = $r->getOne($sql);
        $sql = "SELECT count(cm.uid) cnt FROM client_member cm"
            . " INNER JOIN member_follow_officer mfo ON cm.uid = mfo.member_id"
            . " LEFT JOIN member_credit mc ON cm.uid = mc.member_id"
            . " WHERE mfo.officer_id = " . $this->user_id . " and mfo.officer_type = 1 and mfo.is_active = 1 " . " and ifnull(mc.credit,0)<=0";
        $no_credit_cnt = $r->getOne($sql);
        $sql = "SELECT count(cm.uid) cnt FROM client_member cm"
            . " INNER JOIN member_follow_officer mfo ON cm.uid = mfo.member_id"
            . " WHERE mfo.officer_id = " . $this->user_id . " and mfo.officer_type = 1 and mfo.is_active = 1 " . " and cm.member_state = " . memberStateEnum::TEMP_LOCKING;
        $suspended_cnt = $r->getOne($sql);
        Tpl::output("all_count", $all_cnt);
        Tpl::output("pending_check_count", $pending_check_cnt);
        Tpl::output("no_branch_count", $no_branch_cnt);
        Tpl::output("no_credit_count", $no_credit_cnt);
        Tpl::output("suspended_cnt", $suspended_cnt);

        Tpl::output('param_pending_check', intval($_GET['param_pending_check']));
        Tpl::output('param_no_branch', intval($_GET['param_no_branch']));
        Tpl::output('param_no_credit', intval($_GET['param_no_credit']));
        Tpl::output('param_suspended', intval($_GET['param_suspended']));


        Tpl::showPage('client');
    }

    /**
     * 获取新创建client列表
     * @param $p
     * @return array
     */
    public function getMyClientListOp($p)
    {
        $r = new ormReader();
        $sql = "SELECT cm.*,site_branch.branch_name,member_credit.credit FROM client_member cm"
            . " INNER JOIN member_follow_officer mfo ON cm.uid = mfo.member_id"
            . " LEFT JOIN site_branch  ON cm.branch_id = site_branch.uid"
            . " LEFT JOIN member_credit  ON cm.uid = member_credit.member_id"
            . " WHERE mfo.officer_id = " . $this->user_id . " and mfo.officer_type = 1 and mfo.is_active = 1 ";
        if (trim($p['search_text'])) {
            $sql .= " AND (cm.display_name LIKE '%" . trim($p['search_text']) . "%' OR cm.login_code LIKE '%" . trim($p['search_text']) . "%' OR cm.phone_id LIKE '%" . trim($p['search_text']) . "%')";
        }
        if (intval($p['param_pending_check'])) {
            $sql .= " and (cm.member_state='" . memberStateEnum::CREATE . "' or cm.operate_state='" . newMemberCheckStateEnum::CLOSE . "')";
        }
        if (intval($p['param_no_branch'])) {
            $sql .= " and cm.branch_id<=0";
        }
        if (intval($p['param_no_credit'])) {
            $sql .= " and ifnull(member_credit.credit,0)<=0";

        }
//        if (!intval($p['param_suspended'])) {
//            $sql .= " and cm.member_state != " . intval(memberStateEnum::TEMP_LOCKING);
//        }
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
        );
    }

    /**
     * client信息
     */
    public function creditClientOp($input_param)
    {
        $uid = intval($_GET['uid']) ?: $input_param['member_id'];
        $class_member = new memberClass();
        $rt = $class_member->getMemberDetailAndResearch($uid, $this->user_id, $this->user_position);
        if ($rt->STS) {
            //打开了这个界面，就要注销任务（可能有的user没有这个任务，不报错）
            if ($this->user_position == userPositionEnum::BRANCH_MANAGER) {
                $ret_task = taskControllerClass::finishTask($uid, userTaskTypeEnum::BM_NEW_CLIENT, $this->user_info['branch_id'], objGuidTypeEnum::SITE_BRANCH);
            }


            $data = $rt->DATA;

            //client info
            Tpl::output('client_info', $data['client_info']);

            //Residence
            Tpl::output('residence', $data['residence']);

            //Map
            Tpl::output('map_detail', $data['map_detail']);

            //member co list
            Tpl::output('member_co_list', $data['member_co_list']);
            Tpl::output('member_cc_list', $data['member_cc_list']);
            Tpl::output('member_rc_list', $data['member_rc_list']);


            //member operator
            Tpl::output('member_operator', $data['operator']);

            //product list
            Tpl::output('credit_info', $data['credit_info']);
            Tpl::output("credit_category", $data['credit_category']);

            //identity
            Tpl::output('identity_list', $data['identity_list']);

            //cbc
            Tpl::output('member_cbc', $data['member_cbc']);

            //client request
            Tpl::output('client_request', $data['client_request']);
            Tpl::output("client_relative", $data['client_relative']);

            //assets
            $assets_type = enum_langClass::getAssetsType();
            Tpl::output('assets_type', $assets_type);
            Tpl::output('assets', $data['assets']);

            //Business research
            Tpl::output('business_income', $data['business_income']);


            //income salary
            Tpl::output('salary_income', $data['salary_income']);

            //attachment
            Tpl::output('attachment_income', $data['attachment_income']);

            //credit suggest
            Tpl::output('suggest_list', $data['suggest_list']);


            //loan-account
            Tpl::output("loan_account", $data['loan_account']);

            //suggest_profile
            $m_dict = new core_dictionaryModel();
            $credit_grant_profile = $m_dict->getDictValue(dictionaryKeyEnum::CREDIT_GRANT_RATE);
            Tpl::output('credit_grant_profile', $credit_grant_profile);

            /*
             * 现在禁止BM直接授信
            if ($this->user_position == userPositionEnum::BRANCH_MANAGER) {
                //bm拥有授信额度
                $m_site_branch_limit = M('site_branch_limit');
                $approve_credit_limit = $m_site_branch_limit->field('limit_value')->find(array('branch_id' => $this->user_info['branch_id'], 'limit_key' => 'approve_credit_limit'));
                Tpl::output('approve_credit_limit', intval($approve_credit_limit));
            }
            */
            $m_co_task = new task_co_bmModel();
            $co_submi_task = $m_co_task->getCoSubmitTaskByMemberId($uid);
            Tpl::output("co_submit_task", $co_submi_task);


            Tpl::output("hide_top_menu", $_GET['hide_top_menu'] ?: $input_param['hide_top_menu']);

            Tpl::output('is_voting_suggest', $data['is_voting_suggest']);

            //输出用户权限
            $author_list = memberSettingClass::getMemberAuthority($uid);
            Tpl::output("author_list", $author_list);

            Tpl::showPage('client.credit');
        } else {
            showMessage($rt->MSG);
        }
    }

    /**
     * 修改工作和生意页面
     */
    public function editMemberWorkTypeAndIndustryPageOp()
    {
        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        //WorkType
        $work_type = (new workTypeEnum())->Dictionary();
        Tpl::output('work_type', $work_type);

        //industry
        $industry_list = M('common_industry')->orderBy('industry_name asc')->select(array('state' => 1));
        $industry_list_new = array();
        foreach ($industry_list as $k => $v) {
            $first = strtoupper(substr($v['industry_name'], 0, 1));
            if (in_array($first, $industry_list_new)) {
                array_push($industry_list_new[$first], $v);
            } else {
                $industry_list_new[$first][] = $v;
            }
        }
        Tpl::output('industry_list', $industry_list_new);

        Tpl::showPage('client.work.type.edit');
    }

    /**
     * 修改work type and industry
     */
    public function editMemberWorkTypeAndIndustryOp()
    {
        $member_id = intval($_POST['member_id']);
        $work_type = trim($_POST['work_type']);
        $is_with_business = intval($_POST['is_with_business']);
        $member_industry = $_POST['member_industry'];
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $class_member = new memberClass();
            $rt_1 = $class_member->editMemberWorkType($member_id, $work_type);
            if (!$rt_1->STS) {
                $conn->rollback();
                showMessage('Edit failed1!' . $rt_1->MSG);
            }

            $rt_2 = $class_member->editMemberBusiness($member_id, $is_with_business, $member_industry);
            if (!$rt_2->STS) {
                $conn->rollback();
                showMessage('Edit failed2!' . $rt_2->MSG);
            }

            $conn->submitTransaction();
            showMessage('Edit successful.', getUrl('web_credit', 'creditClient', array('uid' => $member_id), false, BACK_OFFICE_SITE_URL));
        } catch (Exception $ex) {
            $conn->rollback();
            showMessage($ex->getMessage());
        }
    }

    /**
     * 修改居住地页面
     */
    public function editMemberResidencePageOp()
    {
        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        $m_common_address = new common_addressModel();
        $residence = $m_common_address->getMemberResidencePlaceByGuid($client_info['obj_guid']);
        if ($residence) {
            $address_id = $residence['id4'];
            $m_core_tree = M('core_tree');
            $region_list = $m_core_tree->getParentAndBrotherById($address_id, 'region');
            Tpl::output('region_list', $region_list);
            Tpl::output('residence', $residence);
        }

        Tpl::showPage('client.residence.edit');
    }

    /**
     * 修改居住地
     */
    public function editMemberResidenceOp()
    {

        $full_text_arr = array();
        // house number
        $full_text_arr[] = $_POST['house_number'];
        // street
        $full_text_arr[] = $_POST['street'];
        // group
        $full_text_arr[] = $_POST['address_group'];

        if (trim($_POST['address_detail'])) {
            $full_text_arr[] = trim($_POST['address_detail']);
        }
        $m_core_tree = M('core_tree');
        if (intval($_POST['id4'])) {
            $birth_village_info = $m_core_tree->find(array('uid' => intval($_POST['id4'])));
            $full_text_arr[] = $birth_village_info['node_text'];
        }
        if (intval($_POST['id3'])) {
            $birth_commune_info = $m_core_tree->find(array('uid' => intval($_POST['id3'])));
            $full_text_arr[] = $birth_commune_info['node_text'];
        }
        if (intval($_POST['id2'])) {
            $birth_district_info = $m_core_tree->find(array('uid' => intval($_POST['id2'])));
            $full_text_arr[] = $birth_district_info['node_text'];
        }
        if (intval($_POST['id1'])) {
            $birth_province_info = $m_core_tree->find(array('uid' => intval($_POST['id1'])));
            $full_text_arr[] = $birth_province_info['node_text'];
        }
        $address_detail = implode(', ', $full_text_arr);
        $_POST['full_text'] = $address_detail;

        $params = $_POST;
        $params['officer_id'] = $this->user_id;
        $rt = userClass::editMemberResidencePlace($params);
        /*$m_common_address = new common_addressModel();
        $rt = $m_common_address->insertMemberResidence($_POST);*/
        if (!$rt->STS) {
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            showMessage('Edit successful.', getUrl('web_credit', 'creditClient', array('uid' => $_POST['member_id']), false, BACK_OFFICE_SITE_URL));
        }
    }

    /**
     * 修改会员branch页面
     */
    public function editMemberBranchPageOp()
    {
        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        //branch list
        $branch_list = M('site_branch')->select(array('status' => 1));
        $branch_list = resetArrayKey($branch_list, 'uid');
        Tpl::output('branch_list', $branch_list);

        Tpl::showPage('client.branch.edit');
    }

    /**
     * 修改会员branch
     */
    public function editMemberBranchOp()
    {
        $member_id = intval($_POST['uid']);
        $branch_id = intval($_POST['branch_id']);
        $rt = memberClass::resetMemberBranch($member_id, $branch_id, $this->user_id, $this->user_name);
        if (!$rt->STS) {
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            showMessage('Edit successful.', getUrl('web_credit', 'creditClient', array('uid' => $member_id), false, BACK_OFFICE_SITE_URL));
        }
    }

    /**
     * 修改co页面
     */
    public function editMemberCoPageOp()
    {
        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        //member co list
        $member_co_list = M('member_follow_officer')->select(array('member_id' => $uid, 'is_active' => 1));
        Tpl::output('member_co_list', $member_co_list);
        //co list
        $class_user = new userClass();
        $co_list = $class_user->getCoList($client_info['branch_id']);
        Tpl::output('co_list', $co_list);

        Tpl::showPage('client.co.edit');
    }

    /**
     * 修改co
     */
    public function editMemberCoOp()
    {
        $member_id = intval($_POST['member_id']);
        if (is_array($_POST['co_id'])) {
            $co_arr = $_POST['co_id'];
        } else if ($_POST['co_id']) {
            $co_arr = array($_POST['co_id']);
        } else {
            $co_arr = array();
        }

        $class_member = new memberClass();
        $rt = $class_member->setMemberCo($member_id, $co_arr);
        if (!$rt->STS) {
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            showMessage('Edit successful.', getUrl('web_credit', 'creditClient', array('uid' => $member_id), false, BACK_OFFICE_SITE_URL));
        }
    }


    /**
     * 修改member限制产品页面
     */
    public function editMemberLimitProductPageOp()
    {
        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        //product list
        $class_loan_product = new loan_productClass();
        $product_list = $class_loan_product->getValidSubProductList();
        Tpl::output('product_list', $product_list);

        //limit product
        $limit_product = M('member_limit_loan_product')->select(array('member_id' => $uid));
        $limit_product = array_column($limit_product, 'product_code');
        Tpl::output('limit_product', $limit_product);

        Tpl::showPage('client.limit.product.edit');
    }

    /**
     * 修改member限制产品
     */
    public function editMemberLimitProductOp()
    {
        $member_id = intval($_POST['member_id']);
        if (is_array($_POST['sub_product_code'])) {
            $product_arr = $_POST['sub_product_code'];
        } else if ($_POST['sub_product_code']) {
            $product_arr = array($_POST['sub_product_code']);
        } else {
            $product_arr = array();
        }

        $class_member = new memberClass();
        $rt = $class_member->setMemberLimitProduct($member_id, $product_arr, $this->user_id);
        if (!$rt->STS) {
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            showMessage('Edit successful.', getUrl('web_credit', 'creditClient', array('uid' => $member_id), false, BACK_OFFICE_SITE_URL));
        }
    }

    /**
     * cbc详情
     */
    public function showMemberCbcDetailOp()
    {
        $client_id=$_GET['client_id'];
        $client_type=$_GET['client_type'];
        //有可能查看relative的cbc
        $m_client_cbc = new client_cbcModel();
        if($client_id && $client_type){
            $cbc_detail = $m_client_cbc->getClientLatestCbcDetail($client_id,$client_type);
        }else{
            $uid = intval($_GET['uid']);
            $cbc_detail = $m_client_cbc->getCbcDetailById($uid);
        }
        Tpl::output("is_readonly",$_GET['is_readonly']?:0);
        Tpl::output('cbc_detail', $cbc_detail);
        Tpl::output('member_id', $_GET['member_id']);
        Tpl::showPage('client.cbc.detail');
    }

    /**
     * 添加cbc页面
     */
    public function addMemberCbcPageOp()
    {
        $client_id = intval($_GET['client_id']);
        $client_type = intval($_GET['client_type']);
        if ($client_type == 0) {
            $client_info = memberClass::getMemberBaseInfo($client_id);
            if (!$client_info) {
                showMessage('Invalid id.');
            }
        } elseif ($client_type == 1) {
            $m_relative = new member_credit_request_relativeModel();
            $item = $m_relative->find(array("uid" => $client_id));
            if ($item) {
                $client_info = array(
                    "phone_country" => $item['country_code'],
                    "phone_number" => $item['phone_number'],
                    "id_sn" => $item['id_sn'],
                    "member_state" => "None",
                    "kh_display_name" => $item['name'],
                    "display_name" => $item['name'],
                    "grade_code" => $item['relation_type'] . '-' . $item['relation_name'],
                    "login_code" => "None",
                    "member_icon" => $item['headshot']
                );
            }

        } else {
            showMessage("Invalid Client Type");
        }
        //获取最后一条作为default
        $m_cbc = new client_cbcModel();
        $last_item = $m_cbc->orderBy("uid desc")->find(array("client_id" => $client_id, "client_type" => $client_type));
        Tpl::output("last_item", $last_item);
        Tpl::output('client_info', $client_info);
        Tpl::output("client_id", $client_id);
        Tpl::output("member_id", intval($_GET['member_id']));
        Tpl::output("client_type", $client_type);
        Tpl::showPage('client.cbc.add');
    }

    /**
     * 添加cbc
     */
    public function addMemberCbcOp()
    {
        $param = $_POST;

        $client_id = intval($param['client_id']);
        // 如果有上传文件
        if (!empty($_FILES['cbc_file']) && $_FILES['cbc_file']['size'] > 0) {
            // 上传文件
            $upload_path = _UPLOAD_ . DS . 'cbc';
            $file = $_FILES['cbc_file'];
            $file_name = $file['name'];
            $handler = new UploadFile();

            @ini_set('upload_max_filesize', '500M');
            @chmod($upload_path, 0755);
            $handler->set('maxAttachSize', 500 * 1024 * 1024);  // 重置默认的最大附件大小
            $handler->set('dir_type', 1);
            $handler->set("upload", $upload_path);
            $handler->set('default_dir', $client_id);
            $handler->set("allow_type", array('pdf'));
            $result = $handler->upload('cbc_file');

            if (!$result) {
                return new result(false, 'Upload file fail:' . $handler->getError());
            }
            $file_path = $handler->file_path;
            $param['cbc_file'] = $file_path;


        }

        $param['creator_id'] = $this->user_id;
        $param['creator_name'] = $this->user_name;
        $m_client_cbc = new client_cbcModel();
        $rt = $m_client_cbc->editClientCbc($param);
        if (!$rt->STS) {
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            showMessage('Edit successful.', getUrl('web_credit', 'creditClient', array('uid' => intval($param['member_id'])), false, BACK_OFFICE_SITE_URL));
        }
    }

    public function deleteMemberCBCOp($p)
    {
        $uid = $p['uid'];
        $officer_id = $this->user_id;
        $rt = credit_officerClass::deleteMemberCBC($uid, $officer_id);
        return $rt;
    }

    /**
     * 身份详情
     */
    public function showIdentityDetailOp()
    {
        $uid = intval($_GET['uid']);
        $m_member_verify_cert = new member_verify_certModel();
        $cert_info = $m_member_verify_cert->getVerifyCertDetailById($uid);
        if (!$cert_info) {
            showMessage('Invalid Id.');
        }
        Tpl::output('cert_info', $cert_info);
        Tpl::showPage('client.identity.detail');
    }

    public function addMemberRequestPageOp()
    {
        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);
        Tpl::showPage('client.request.edit');
    }

    /**
     * 修改会员额度申请页面
     */
    public function editMemberRequestPageOp()
    {
        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        $client_request = credit_researchClass::getClientRequestCredit($uid);
        if (count($client_request)) {
            if ($client_request['state'] == creditRequestStateEnum::CANCEL || $client_request['state'] == creditRequestStateEnum::DONE) {
                //添加
                Tpl::output('client_request', array());
            } elseif ($client_request['state'] == creditRequestStateEnum::CREATE) {
                //修改
                Tpl::output('client_request', $client_request);

            } else {
                //granted 不能添加修改
                showMessage("Can't add/edit request,Please waiting for client go to authorize the contract.");
            }
        } else {
            Tpl::output('client_request', array());
        }

        $type_list = (new loanRelativeTypeEnum())->Dictionary();
        Tpl::output('type_list', $type_list);

        $define_arr = M('core_definition')->getDefineByCategory(array(userDefineEnum::GUARANTEE_RELATIONSHIP));
        Tpl::output('guarantee_list', $define_arr[userDefineEnum::GUARANTEE_RELATIONSHIP]);


        Tpl::showPage('client.request.edit');
    }

    /**
     * 添加会员额度申请
     */
    public function editMemberRequestOp()
    {
        $p = $_POST;
        $member_id = $p['member_id'];
        $p['officer_id'] = $this->user_id;
        $rt = credit_officerClass::editMemberCreditRequest($p);
        if (!$rt->STS) {
            showMessage('Failed!' . $rt->MSG);
        } else {
            // 调到客户详情页
            showMessage('Successful.', getUrl('web_credit', 'creditClient', array('uid' => $member_id), false, BACK_OFFICE_SITE_URL));
        }
    }

    public function editRequestRelationOp($p)
    {
        $p['officer_id'] = $this->user_id;
        if (intval($p['relation_id'])) {
            $rt = credit_officerClass::editCreditRequestRelative($p, false);
            return $rt;
        } else {
            $rt = credit_officerClass::addCreditRequestRelative($p, false);
            return $rt;
        }
    }

    public function deleteRequestRelationOp($p)
    {
        $rt = credit_officerClass::deleteCreditRequestRelative(intval($p['relation_id']));
        return $rt;
    }

    /**
     * 添加商业调查页面
     */
    public function addMemberBusinessIncomePageOp()
    {
        $member_id = intval($_GET['member_id']);
        $industry_id = intval($_GET['industry_id']);
        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        $client_relative = memberClass::getMemberCurrentRelative($member_id);
        Tpl::output("client_relative", $client_relative);

        $m_common_industry = new common_industryModel();
        $industry_info = $m_common_industry->getIndustryInfo($industry_id);
        if (!$industry_info) {
            showMessage('Invalid Industry.');
        }
        Tpl::output('industry_info', $industry_info);

        $industry_place = M('common_industry_place')->select(array('uid' => array('neq', 0)));
        Tpl::output('industry_place', $industry_place);
        if (trim($_GET['branch_code'])) {
            $branch_code = trim($_GET['branch_code']);
            Tpl::output('branch_code', $branch_code);
            //获取co的list，bm包括operator,operator的记录不包括operator
            if ($this->user_position == userPositionEnum::BRANCH_MANAGER) {
                $request_by_bm = true;
            } else {
                $request_by_bm = false;
            }
            $income_research_co = credit_researchClass::getCoMemberBusinessIncomeResearchList($member_id, $industry_id, $branch_code, $request_by_bm);
            //获取所有图片
            $all_image_list = array();
            foreach ($income_research_co as $income_item) {
                if (count($income_item['business_image'])) {
                    $all_image_list = array_merge($all_image_list, $income_item['business_image']);
                }
            }
            Tpl::output("image_list", $all_image_list);

            if (count($income_research_co) > 1) {
                $income_research_co[0] = credit_researchClass::avgCoBusinessResearch($industry_id, $income_research_co);
                //array_unshift($income_research_co,credit_researchClass::avgCoBusinessResearch($industry_id, $income_research_co));
            }

            if (count($income_research_co)) {
                $default_item = end($income_research_co);
                if (bccomp($default_item['coord_x'], 0, 2) == 0 && bccomp($default_item['coord_y'], 0, 2) == 0) { //如果default没有地图，取其它的
                    foreach ($income_research_co as $v) {
                        if (bccomp($v['coord_x'], 0, 2) != 0 || bccomp($v['coord_y'], 0, 2) != 0) {
                            $default_item['coord_x'] = $v['coord_x'];
                            $default_item['coord_y'] = $v['coord_y'];
                            break;
                        }
                    }
                }
                if (bccomp($default_item['coord_x'], 0, 2) == 0 && bccomp($default_item['coord_y'], 0, 2) == 0) { //如果default没有地图，处理历史数据
                    $sql = "select coord_x,coord_y from member_income_business where member_id='" . $member_id . "' and industry_id=" . qstr($industry_id) . " and branch_code=" . qstr($branch_code) . " and coord_x!=0 and coord_y!=0";
                    $tmp_row = $m_common_industry->reader->getRow($sql);
                    if ($tmp_row) {
                        $default_item['coord_x'] = $tmp_row['coord_x'];
                        $default_item['coord_y'] = $tmp_row['coord_y'];
                    }
                }
                Tpl::output("business_income", $default_item);
            }


            $income_research_text_co = array();
            foreach ($income_research_co as $k => $v) {
                $income_research_text_co[$k] = my_json_decode($v['research_text']);
            }
            Tpl::output('income_research_text_co', $income_research_text_co);
            Tpl::output('income_research_co', $income_research_co);

            $co_list = memberClass::getMemberCreditOfficerList($member_id, $request_by_bm);
            $co_list = resetArrayKey($co_list, "officer_id");
            if (count($income_research_co) > 1) {
                $co_list[0] = array(
                    'officer_name' => '--AVG--'
                );
            }
            Tpl::output('co_list', $co_list);
        }

        Tpl::showPage('client.business.income.add');
    }

    /**
     * 添加商业调查
     */
    public function addMemberBusinessIncomeOp()
    {
        $p = $_POST;
        $member_id = intval($p['member_id']);

        $business_info = array(
            'industry_id' => intval($p['industry_id']),
            'branch_code' => trim($p['branch_code']),
//            'industry_place_id' => $p['industry_place'],
            'industry_place_text' => $p['industry_place'],
            'business_employees' => $p['employees'],
            'business_income' => $p['income'],
            'business_expense' => $p['expense'],
            'business_profit' => $p['income'] - $p['expense'],
            'address_detail' => $p['address_detail'],
            'coord_x' => $p['coord_x'],
            'coord_y' => $p['coord_y'],
        );
        $research_text = $p['research_text'];

        if (!$p['relative_id']) {
            $p['relative_id'] = 0;
        }

        if (!is_array($p['relative_id'])) {
            $p['relative_id'] = array(
                $p['relative_id']
            );
        }
        $relative_id = implode(',', $p['relative_id']);
        $business_info['relative_id'] = $relative_id;

        $m_common_industry = new common_industryModel();
        $industry_info = $m_common_industry->getIndustryInfo(intval($p['industry_id']));
        if (!$industry_info) {
            showMessage('Invalid Industry.');
        }

        $industry_text = $industry_info['industry_text_all'];
        $industry_research_json = array();
        foreach ($industry_text as $key => $val) {
            $industry_research_json[$key] = $val['type'] == 'description' ? trim($research_text[$key]) : round($research_text[$key], 2);
        }
        $business_info['industry_research_json'] = my_json_encode($industry_research_json);

        $operator_type = $this->user_info['user_position'] == userPositionEnum::BRANCH_MANAGER ? researchPositionTypeEnum::BRANCH_MANAGER : researchPositionTypeEnum::OPERATOR;
        $operator_id = $this->user_id;
        $image_files = my_json_decode(str_replace("'",'"',$p['image_files']));

        $add_images = array();
        foreach ($image_files as $key => $v) {
            $add_images[$key] = array(
                'image_url' => $v,
                'image_source' => imageSourceEnum::ALBUM,
            );
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = credit_researchClass::addMemberBusinessResearch($member_id, $business_info, $operator_type, $operator_id, $add_images);
        if (!$rt->STS) {
            $conn->rollback();
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            $conn->submitTransaction();
            showMessage('Edit successful.', getUrl('web_credit', 'creditClient', array('uid' => $member_id), false, BACK_OFFICE_SITE_URL));
        }
    }

    /**
     * 修改商业收入页面
     */
    public function editMemberBusinessIncomePageOp()
    {
        $income_id = intval($_GET['income_id']);
        $m_member_income_business = new member_income_businessModel();
        $income_item = $m_member_income_business->find(array("uid" => $income_id));
        if (!$income_item) {
            showMessage('Invalid id.');
        }
        Tpl::output("income_id", $income_id);

        $member_id = $income_item['member_id'];
        $industry_id = $income_item['industry_id'];
        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        $client_relative = memberClass::getMemberCurrentRelative($member_id);
        Tpl::output("client_relative", $client_relative);

        $m_common_industry = new common_industryModel();
        $industry_info = $m_common_industry->getIndustryInfo($industry_id);
        if (!$industry_info) {
            showMessage('Invalid Industry.');
        }
        Tpl::output('industry_info', $industry_info);

        $industry_place = M('common_industry_place')->select(array('uid' => array('neq', 0)));
        Tpl::output('industry_place', $industry_place);

        //取联系人列表
        $m_member_income_business_owner = M('member_income_business_owner');
        $business_owner = $m_member_income_business_owner->select(array('income_business_id' => $income_id));
        $income_item['relative_list'] = $business_owner;
        $m_member_income_business_image = M('member_income_business_image');
        $business_image = $m_member_income_business_image->select(array('income_business_id' => $income_id, 'is_delete' => 0));
        $income_item['business_image'] = $business_image;
        Tpl::output("image_list", $business_image);


        $default_item = $income_item;
        Tpl::output("business_income", $default_item);

        if ($default_item['branch_code']) {
            $branch_code = $default_item['branch_code'];
            Tpl::output('branch_code', $branch_code);
            //获取co的list，bm包括operator,operator的记录不包括operator
            if ($this->user_position == userPositionEnum::BRANCH_MANAGER) {
                $request_by_bm = true;
            } else {
                $request_by_bm = false;
            }
            $income_research_co = credit_researchClass::getCoMemberBusinessIncomeResearchList($member_id, $industry_id, $branch_code, $request_by_bm);
            if (count($income_research_co) > 1) {
                $income_research_co[0] = credit_researchClass::avgCoBusinessResearch($industry_id, $income_research_co);
                //array_unshift($income_research_co,credit_researchClass::avgCoBusinessResearch($industry_id, $income_research_co));
            }

            $income_research_text_co = array();
            foreach ($income_research_co as $k => $v) {
                $income_research_text_co[$k] = my_json_decode($v['research_text']);
            }
            Tpl::output('income_research_text_co', $income_research_text_co);
            Tpl::output('income_research_co', $income_research_co);

            $co_list = memberClass::getMemberCreditOfficerList($member_id, $request_by_bm);
            $co_list = resetArrayKey($co_list, "officer_id");
            if (count($income_research_co) > 1) {
                $co_list[0] = array(
                    'officer_name' => '--AVG--'
                );
            }
            Tpl::output('co_list', $co_list);
        }


        Tpl::showPage('client.business.income.add');
    }

    /**
     * 修改商业收入
     */
    public function editMemberBusinessIncomeOp()
    {
        $p = $_POST;
        $income_id = intval($p['income_id']);
        $business_info = array(
            'research_id' => $income_id,
//            'industry_place_id' => $p['industry_place'],
            'industry_place_text' => $p['industry_place'],
            'business_employees' => $p['employees'],
            'business_income' => $p['income'],
            'business_expense' => $p['expense'],
            'business_profit' => $p['income'] - $p['expense'],
            'address_detail' => $p['address_detail'],
            'coord_x' => $p['coord_x'],
            'coord_y' => $p['coord_y'],
        );
        $research_text = $p['research_text'];

        if (!$p['relative_id']) {
            $p['relative_id'] = 0;
        }

        if (!is_array($p['relative_id'])) {
            $p['relative_id'] = array(
                $p['relative_id']
            );
        }
        $relative_id = implode(',', $p['relative_id']);
        $business_info['relative_id'] = $relative_id;

        $business_income = credit_researchClass::getBusinessIncomeResearchDetailById($income_id);
        if (!$business_income) {
            showMessage('Invalid id.');
        }

        $member_id = $business_income['member_id'];
        $industry_id = $business_income['industry_id'];

        $m_common_industry = new common_industryModel();
        $industry_info = $m_common_industry->getIndustryInfo($industry_id);
        if (!$industry_info) {
            showMessage('Invalid Industry.');
        }

        $industry_text = $industry_info['industry_text_all'];
        $industry_research_json = array();
        foreach ($industry_text as $key => $val) {
            $industry_research_json[$key] = $val['type'] == 'description' ? trim($research_text[$key]) : round($research_text[$key], 2);
        }
        $business_info['industry_research_json'] = my_json_encode($industry_research_json);

        $operator_type = $this->user_info['user_position'] == userPositionEnum::BRANCH_MANAGER ? researchPositionTypeEnum::BRANCH_MANAGER : researchPositionTypeEnum::OPERATOR;
        $operator_id = $this->user_id;
        $image_files = my_json_decode(str_replace("'",'"',$p['image_files']));
        $old_image_list = $images = (new member_income_business_imageModel())->select(array(
            'income_business_id' => $income_id,
            'is_delete' => 0
        ));

        $del_images = array();
        foreach ($old_image_list as $img) {
            if (!in_array($img['image_url'], $image_files)) {
                $del_images[] = $img['uid'];
            }
        }
        if ($del_images) {
            //$business_info['delete_image_ids'] = implode(',', $del_images);
            $business_info['change_state_images'] = $del_images;
        }

        $old_image_list = array_column($old_image_list, 'image_url');

        // todo 保留没有修改的图片的原始来源
        $add_images = array();
        foreach ($image_files as $key => $v) {
            if (!in_array($v, $old_image_list)) {
                $add_images[$key] = array(
                    'image_url' => $v,
                    'image_source' => imageSourceEnum::ALBUM,
                );
            }
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = credit_researchClass::editIncomeBusinessInfo($business_info, $operator_type, $operator_id, $add_images);
        if (!$rt->STS) {
            $conn->rollback();
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            $conn->submitTransaction();
            showMessage('Edit successful.', getUrl('web_credit', 'creditClient', array('uid' => $member_id), false, BACK_OFFICE_SITE_URL));
        }
    }

    /**
     * 删除Business
     * @param $p
     * @return result
     */
    public function deleteMemberBusinessIncomeOp($p)
    {
        $uid = intval($p['uid']);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = credit_researchClass::deleteMemberIncomeBusiness($uid);
        if (!$rt->STS) {
            $conn->rollback();
            return $rt;
        } else {
            $conn->submitTransaction();
            return $rt;
        }
    }

    public function deleteAllMemberBusinessIncomeOp($p)
    {
        $member_id = intval($p['member_id']);
        $industry_id = intval($p['industry_id']);
        $branch_code = trim($p['branch_code']);
        if (!$this->is_bm) {
            return new result(false, 'No permission to delete.');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = credit_researchClass::deleteAllMemberIncomeBusiness($member_id, $industry_id, $branch_code);
        if (!$rt->STS) {
            $conn->rollback();
            return $rt;
        } else {
            $conn->submitTransaction();
            return $rt;
        }
    }

    /**
     * 添加工资收入页面
     */
    public function addMemberSalaryIncomePageOp()
    {
        $member_id = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($member_id);

        if (!$client_info) {
            showMessage('Invalid id.');
        }
        $client_relative = memberClass::getMemberCurrentRelative($member_id);
        Tpl::output("client_relative", $client_relative);
        Tpl::output('client_info', $client_info);
        Tpl::showPage('client.salary.income.add');
    }

    /**
     * 添加工资收入
     */
    public function addMemberSalaryIncomeOp()
    {
        $p = $_POST;
        $income_info = array(
            'member_id' => intval($p['member_id']),
            'company_name' => $p['company_name'],
            'company_phone' => $p['company_phone'],
            'position' => $p['position'],
            'salary' => $p['salary'],
            'address_detail' => $p['address_detail'],
        );
        if ($p['relative_id'] > 0) {
            $client_relative = memberClass::getMemberCurrentRelative($p['member_id']);
            $relative_item = $client_relative[$p['relative_id']];
            if (is_array($relative_item)) {
                $p['relative_name'] = $relative_item['name'];
                $income_info['relative_id'] = $p['relative_id'];
                $income_info['relative_name'] = $p['relative_name'];
            }
        } else {
            $income_info['relative_id'] = 0;
            $income_info['relative_name'] = 'Own';
        }
        $operator_type = $this->user_info['user_position'] == userPositionEnum::BRANCH_MANAGER ? 1 : 0;
        $operator_id = $this->user_id;
        $image_files =my_json_decode(str_replace("'",'"',$p['image_files']));

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = credit_researchClass::addMemberSalaryIncomeResearch($income_info, $image_files, $operator_type, $operator_id);
        if (!$rt->STS) {
            $conn->rollback();
            showMessage('Add failed!' . $rt->MSG);
        } else {
            $conn->submitTransaction();
            showMessage('Add successful.', getUrl('web_credit', 'creditClient', array('uid' => intval($p['member_id'])), false, BACK_OFFICE_SITE_URL));
        }
    }

    /**
     * 修改工资收入页面
     */
    public function editMemberSalaryIncomePageOp()
    {
        $research_id = intval($_GET['uid']);
        $income_salary = credit_researchClass::getMemberSalaryIncomeResearch($research_id);
        if (!$income_salary) {
            showMessage('Invalid id.');
        }
        Tpl::output('income_salary', $income_salary);

        $client_info = memberClass::getMemberBaseInfo($income_salary['member_id']);
        if (!$client_info) {
            showMessage('Invalid Client.');
        }
        $client_relative = memberClass::getMemberCurrentRelative($income_salary['member_id']);
        Tpl::output("client_relative", $client_relative);

        Tpl::output('client_info', $client_info);

        Tpl::showPage('client.salary.income.edit');
    }

    /**
     * 修改工资收入
     */
    public function editMemberSalaryIncomeOp()
    {
        $p = $_POST;
        $research_id = intval($p['research_id']);
        $income_info = array(
            'company_name' => $p['company_name'],
            'company_phone' => $p['company_phone'],
            'position' => $p['position'],
            'salary' => $p['salary'],
            'address_detail' => $p['address_detail'],
            'coord_x' => $p['coord_x'],
            'coord_y' => $p['coord_y'],
        );
        $m_salary = new member_income_salaryModel();
        $salary_item = $m_salary->find(array("uid" => $research_id));
        if (!is_array($salary_item)) {
            showMessage('Invalid Parameter:No Salary-item Found');
        }

        if ($p['relative_id'] > 0) {
            $client_relative = memberClass::getMemberCurrentRelative($salary_item['member_id']);
            $relative_item = $client_relative[$p['relative_id']];
            if (is_array($relative_item)) {
                $p['relative_name'] = $relative_item['name'];
                $income_info['relative_id'] = $p['relative_id'];
                $income_info['relative_name'] = $p['relative_name'];
            }
        } else {
            $income_info['relative_id'] = 0;
            $income_info['relative_name'] = 'Own';
        }

        $operator_type = $this->user_info['user_position'] == userPositionEnum::BRANCH_MANAGER ? 1 : 0;
        $operator_id = $this->user_id;
        $image_files = my_json_decode(str_replace("'",'"',$p['image_files']));

        $old_image_list = $images = (new member_income_salary_imageModel())->select(array(
            'salary_id' => $research_id
        ));

        $del_images = array();
        foreach ($old_image_list as $img) {
            if (!in_array($img['image_url'], $image_files)) {
                $del_images[] = $img['uid'];
            }
        }

        if ($del_images) {
            $income_info['delete_image_ids'] = implode(',', $del_images) . ',';
        }

        $old_image_list = array_column($old_image_list, 'image_url');

        // todo 保留没有修改的图片的原始来源
        $add_images = array();
        foreach ($image_files as $key => $v) {
            if (!in_array($v, $old_image_list)) {
                $add_images[$key] = $v;
            }
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = credit_researchClass::editMemberSalaryIncomeResearch($research_id, $income_info, $add_images, $operator_type, $operator_id);
        if (!$rt->STS) {
            $conn->rollback();
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            $conn->submitTransaction();
            $member_id = $rt->DATA['member_id'];
            showMessage('Edit successful.', getUrl('web_credit', 'creditClient', array('uid' => $member_id), false, BACK_OFFICE_SITE_URL));
        }
    }

    /**
     * 添加其他收入页面
     */
    public function addMemberAttachmentPageOp()
    {
        $member_id = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        Tpl::showPage('client.attachment.add');
    }

    /**
     * 添加工资收入
     */
    public function addMemberAttachmentOp()
    {
        $p = $_POST;
        $member_id = intval($p['member_id']);
        $attachment_info = array(
            'title' => trim($p['title']),
            'ext_type' => intval($p['ext_type']),
            'ext_amount' => round($p['ext_amount'], 2),
            'remark' => $p['remark'],
        );

        $operator_id = $this->user_id;
        $image_files = my_json_decode(str_replace("'",'"',$p['image_files']));

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = credit_researchClass::addMemberAttachmentResearch($member_id, $attachment_info, $image_files, $operator_id);
        if (!$rt->STS) {
            $conn->rollback();
            showMessage('Add failed!' . $rt->MSG);
        } else {
            $conn->submitTransaction();
            showMessage('Add successful.', getUrl('web_credit', 'creditClient', array('uid' => $member_id), false, BACK_OFFICE_SITE_URL));
        }
    }

    /**
     * 修改其他收入页面
     */
    public function editMemberAttachmentPageOp()
    {
        $research_id = intval($_GET['uid']);
        $attachment = credit_researchClass::getMemberAttachmentResearch($research_id);
        if (!$attachment) {
            showMessage('Invalid id.');
        }
        Tpl::output('attachment', $attachment);

        $client_info = memberClass::getMemberBaseInfo($attachment['member_id']);
        if (!$client_info) {
            showMessage('Invalid Client.');
        }
        Tpl::output('client_info', $client_info);

        Tpl::showPage('client.attachment.edit');
    }

    /**
     * 修改其他收入
     */
    public function editMemberAttachmentOp()
    {
        $p = $_POST;
        $research_id = intval($p['research_id']);
        $attachment_info = array(
            'title' => trim($p['title']),
            'ext_type' => intval($p['ext_type']),
            'ext_amount' => round($p['ext_amount'], 2),
            'remark' => $p['remark'],
        );

        $operator_id = $this->user_id;
        $image_files=my_json_decode(str_replace("'",'"',$p['image_files']));


        $old_image_list = $images = (new member_attachment_imageModel())->select(array(
            'attachment_id' => $research_id
        ));

        $del_images = array();
        foreach ($old_image_list as $img) {
            if(!in_array($img['image_url'],$image_files)){
                $del_images[] = $img['uid'];
            }
        }

        if ($del_images) {
            $attachment_info['delete_image_ids'] = implode(',', $del_images) . ',';
        }
        $old_image_list = array_column($old_image_list, 'image_url');
        // todo 保留没有修改的图片的原始来源
        $add_images = array();
        foreach ($image_files as $key => $v) {
            if (!in_array($v, $old_image_list)) {
                $add_images[$key] = $v;
            }
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = credit_researchClass::editMemberAttachmentResearch($research_id, $attachment_info, $add_images, $operator_id);
        if (!$rt->STS) {
            $conn->rollback();
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            $conn->submitTransaction();
            $member_id = $rt->DATA['member_id'];
            showMessage('Edit successful.', getUrl('web_credit', 'creditClient', array('uid' => $member_id), false, BACK_OFFICE_SITE_URL));
        }
    }

    /**
     * 资产详情  估值  租金
     */
    public function showMemberAssetDetailOp()
    {
        $asset_id = intval($_GET['asset_id']);
        $class_member_asset = new member_assetsClass();
        $asset_info = $class_member_asset->getAssetDetailById($asset_id);
        if (!$asset_info) {
            showMessage('Invalid Id.');
        }
        Tpl::output('asset_info', $asset_info);

        $asset_evaluate = $class_member_asset->getAssetEvaluateByOperatorId($asset_id, $this->user_id);
        Tpl::output('asset_evaluate', $asset_evaluate);

        $asset_rental = credit_researchClass::getLastMemberAssetRentalResearch($asset_id);
        Tpl::output('asset_rental', $asset_rental);

        Tpl::showPage('client.asset.detail');
    }

    /**
     * 修改资产信息
     */
    public function editAssetInfoOp()
    {
        $p = $_POST;
        $asset_id = intval($p['asset_id']);
        $asset_info = array(
            'asset_name' => $p['asset_name'],
            'asset_type' => intval($p['asset_type']),
        );
        $asset_images = $p['asset_images'];

        $class_member_asset = new member_assetsClass();
        $rt = $class_member_asset->editAssetInfo($asset_id, $asset_info, $asset_images, 1);
        if (!$rt->STS) {
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            showMessage('Edit successful.');
        }
    }

    /**
     * 修改估值
     */
    public function editAssetEvaluateOp()
    {
        $p = $_POST;
        $asset_id = intval($p['asset_id']);
        $evaluation = round($p['evaluation'], 2);
        $remark = trim($p['remark']);
        $operator_id = $this->user_id;
        $rt = credit_researchClass::editAssetEvaluate($asset_id, $evaluation, $remark, $operator_id);
        if (!$rt->STS) {
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            showMessage('Edit successful.');
        }
    }

    /**
     * 修改租金
     */
    public function editAssetRentalOp()
    {
        $p = $_POST;
        $asset_id = intval($p['asset_id']);
        $rental_info = array(
            'renter' => trim($p['renter']),
            'monthly_rent' => round($p['monthly_rent'], 2),
            'remark' => trim($p['remark']),
        );
        $image_files = $p['image_files'];
        $operator_id = $this->user_id;
        $operator_type = $this->user_position == userPositionEnum::BRANCH_MANAGER ? 1 : 0;
        $conn = ormYo::Conn();
        $conn->startTransaction();

        $m_member_assets_rental = M('member_assets_rental');
        $row = $m_member_assets_rental->find(array('asset_id' => $asset_id));
        if ($row) {
            $rt = credit_researchClass::editMemberAssetRentalResearch($row['uid'], $rental_info, $image_files, $operator_type, $operator_id);
        } else {
            $rt = credit_researchClass::addMemberAssetRentalResearch($asset_id, $rental_info, $image_files, $operator_type, $operator_id);
        }
        if (!$rt->STS) {
            $conn->rollback();
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            $conn->submitTransaction();
            showMessage('Edit successful.');
        }
    }

    /*
     *添加资产项的页面
     */
    public function addAssetItemOp()
    {
        $member_id = intval($_GET['member_id']);
        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        $asset_type = $_GET['asset_type'];
        $image_structure = global_settingClass::getCertImageStructure();
        $stt = $image_structure[$asset_type];
        Tpl::output("image_structure", $stt);

        $property_list = enum_langClass::getAssetsType();
        $relative = memberClass::getMemberCurrentRelative($member_id);

        //处理特殊的certType
        if($asset_type==certificationTypeEnum::DEGREE){
            $cert_type_list=(new degreeTypeEnum())->Dictionary();
        }else{
            $cert_type_list=(new assetsCertTypeEnum())->Dictionary();
        }
        Tpl::output("cert_type_list",$cert_type_list);

        Tpl::output("client_relative", $relative);
        Tpl::output("title", $property_list[$asset_type]);
        Tpl::output("member_id", $member_id);
        Tpl::output("asset_type", $asset_type);
        Tpl::showPage("asset.item.add");

    }

    public function submitNewAssetItemOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $property_list = enum_langClass::getAssetsType(1);
        $params['type'] = $property_list[$params['asset_type']];

        if (!$params['relative_id']) {
            $params['relative_id'] = 0;
        }

        if (!is_array($params['relative_id'])) {
            $params['relative_id'] = array(
                $params['relative_id']
            );
        }
        $params['relative_id'] = implode(',', $params['relative_id']);

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $params['operator_id'] = $this->user_id;
            $params['operator_name'] = $this->user_name;
            $params['create_time'] = Now();
            $re = member_assetsClass::addAsset($params, certSourceTypeEnum::OPERATOR);
            if ($re->STS) {
                $conn->submitTransaction();
                //add by tim, 当后台设置co提交的资料是否需要operator审批，不需要的话就自动审批
                $cert_row = $re->DATA['cert_result'];
                if ($cert_row && $cert_row instanceof ormDataRow) {
                    if ($cert_row->verify_state == certStateEnum::CREATE) {
                        $is_auto = !global_settingClass::isAllowOperatorApproveAssetsByCO();
                        if ($is_auto) {
                            $cert_row->verify_state = certStateEnum::PASS;
                            $cert_row->verify_remark = "Auto Approve By System";
                            $cert_row->auditor_id = $this->user_id;
                            $cert_row->auditor_name = $this->user_name;
                            $cert_row->auditor_time = Now();
                            $ret_update = $cert_row->update();
                            if ($ret_update->STS) {
                                $m_asset = new member_assetsModel();
                                $asset_row = $m_asset->getRow(array("cert_id" => $cert_row->uid));
                                if ($asset_row) {
                                    $asset_row->asset_state = assetStateEnum::CERTIFIED;
                                    $asset_row->update_time = Now();
                                    $ret_update = $asset_row->update();
                                }
                            }
                        }

                    }
                }

                showMessage($re->MSG, getUrl('web_credit', 'creditClient', array('uid' => $params['member_id']), false, BACK_OFFICE_SITE_URL));
            } else {
                $conn->rollback();
                showMessage($re->MSG, getUrl('web_credit', 'creditClient', array('uid' => $params['member_id']), false, BACK_OFFICE_SITE_URL));
            }

        } catch (Exception $e) {
            $conn->rollback();
            showMessage($e->getMessage(), getUrl('web_credit', 'creditClient', array('uid' => $params['member_id']), false, BACK_OFFICE_SITE_URL));
        }
    }

    /*
     * 资产项详细
     */
    public function assetItemDetailOp()
    {
        $asset_id = $_GET['asset_id'];

        $ret = credit_officerClass::getMemberAssetDetailAndResearchInfo($asset_id, $this->user_id);
        $asset = $ret->DATA;
        Tpl::output("assets_info", $asset);


        $member_id = $asset['member_id'];

        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        $relative_id = $asset['relative_id'];
        if ($relative_id > 0) {
            $m_relative = M("member_credit_request_relative");
            $client_relative = $m_relative->find(array("uid" => $relative_id));
            Tpl::output("client_relative", $client_relative);
        }

        //最后一次的取出申请
        $sql = "select * from member_asset_request_withdraw where member_asset_id='" . $asset_id . "' order by uid desc";
        $last_request_withdraw = (new ormReader())->getRow($sql);
        Tpl::output("request_withdraw", $last_request_withdraw);


        $asset_type = $asset['asset_type'];
        $image_structure = global_settingClass::getCertImageStructure();
        $stt = $image_structure[$asset_type];
        Tpl::output("image_structure", $stt);

        $property_list = enum_langClass::getAssetsType();

        // 得到资产抵押时的照片
        $image_list = member_assetsClass::getAssetMortgageImages($asset_id);
        $images = array();
        foreach ($image_list as $v) {
            $images[] = $v['image_path'];
        }
        Tpl::output('asset_mortgage_images', $images);

        Tpl::output("title", $property_list[$asset_type]);
        Tpl::output("member_id", $member_id);
        Tpl::output('asset_id', $asset_id);
        Tpl::output("asset_type", $asset_type);

        //处理特殊的certType
        if($asset_type==certificationTypeEnum::DEGREE){
            $cert_type_list=(new degreeTypeEnum())->Dictionary();
        }else{
            $cert_type_list=(new assetsCertTypeEnum())->Dictionary();
        }
        Tpl::output("cert_type_list",$cert_type_list);

        $relative = memberClass::getMemberCurrentRelative($member_id);
        Tpl::output("client_relative", $relative);

        Tpl::showPage("asset.item.info");
    }

    /*
     * 资产项详细
     */
    public function showAssetItemDetailOp()
    {
        $asset_id = intval($_GET['asset_id']);

        $ret = credit_officerClass::getMemberAssetDetailAndResearchInfo($asset_id, $this->user_id, true);
        $asset = $ret->DATA;
        Tpl::output("asset", $asset);

        $member_id = $asset['member_id'];
        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        $r = new ormReader();
        $sql = "SELECT * FROM member_assets_evaluate WHERE member_assets_id = $asset_id AND evaluator_type = 1 ";
        $asset_evaluate = $r->getRow($sql);
        Tpl::output("asset_evaluate", $asset_evaluate);

        $m = new member_assets_rentalModel();
        $asset_rental = $m->orderBy('uid desc')->find(array(
            'asset_id' => $asset_id,
        ));

        if ($asset_rental) {
            $m_member_assets_rental_image = new member_assets_rental_imageModel();
            $images = $m_member_assets_rental_image->select(array(
                'rental_id' => $asset_rental['uid']
            ));
            $asset_rental['images'] = $images;
        }
        Tpl::output("asset_rental", $asset_rental);

        //对应的grant的还贷情况
        $loan_ret = member_assetsClass::getAssetRelativeContract($asset_id);
        $principal_outstanding = $loan_ret['principal_outstanding'];
        $loan_list = $loan_ret['contract_list'];
        Tpl::output("loan_list", $loan_list);
        Tpl::output("principal_outstanding", $principal_outstanding);
        //保存流水
        $storage_list = member_assetsClass::getAssetStorageFlow($asset_id);
        Tpl::output("storage_list", $storage_list);
        //获取本行的teller_id
        $receiver_list = counter_baseClass::getBranchUserListOfTeller($this->branch_id);
        Tpl::output("receiver_list", $receiver_list);

        //获取未接受列表，可以删除
        $m_transfer = new member_assets_storageModel();
        $request_transfer = $m_transfer->select(array("from_operator_id" => $this->user_id, "is_pending" => 1, "flow_type" => assetStorageFlowType::TRANSFER, "member_asset_id" => $asset_id));
        Tpl::output("pending_receive", $request_transfer);

        Tpl::output("member_id", $member_id);

        if ($_GET['source_mark'] == 'bm_suggest') {
            Tpl::showPage("show.asset.item.bm");
        } elseif ($_GET['source_mark'] == 'op_suggest') {
            Tpl::showPage("show.asset.item.operator");
        } elseif ($_GET['source_mark'] == 'grant_committee') {
            Tpl::showPage("show.asset.item.committee");
        } elseif ($_GET['source_mark'] == 'fast_grant') {
            Tpl::showPage("show.asset.item.fast_grant");
        } elseif ($_GET['source_mark'] == 'client_detail') {
            Tpl::showPage("show.asset.item.client_detail");
        } else if ($_GET['source_mark'] == 'tools_client_detail') {
            Tpl::showPage("show.asset.item.tools_client_detail");
        } else {
            showMessage('Invalid Mark.');
        }
    }

    public function showAssetsEvaluatePageOp()
    {
        $asset_id = $_REQUEST['asset_id'];
        $ret = credit_officerClass::getMemberAssetDetailAndResearchInfo($asset_id, $this->user_id);
        $asset = $ret->DATA;
        $member_id = $asset['member_id'];
        $history = userClass::getOneAssetEvaluateHistoryForMember($this->user_id, $asset_id);

        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        $property_list = enum_langClass::getAssetsType();
        Tpl::output("asset_type", $property_list[$asset['asset_type']]);

        //获取co估值以及平均值
        $co_avg = credit_officerClass::getMemberAssetValuationOfCO($asset_id);
        Tpl::output('co_avg', $co_avg);
        if (count($history)) {
            $default_item = end($history);
        } else {
            if ($co_avg) {
                $avg_val = end($co_avg);
                $avg_val = $avg_val['evaluation'];
            }
            $default_item = array(
                "evaluation" => $avg_val
            );
        }
        Tpl::output('default_item', $default_item);

        Tpl::output("asset", $asset);
        Tpl::output("history", $history);
        Tpl::showPage("asset.item.valuation");
    }

    public function showAssetsRentalPageOp()
    {
        $asset_id = $_REQUEST['asset_id'];
        $ret = credit_officerClass::getMemberAssetDetailAndResearchInfo($asset_id, $this->user_id);
        $asset = $ret->DATA;
        $member_id = $asset['member_id'];
        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        $rental = credit_researchClass::getLastMemberAssetRentalResearch($asset_id);
        Tpl::output("rental", $rental);

        $property_list = enum_langClass::getAssetsType();
        Tpl::output("asset_type", $property_list[$asset['asset_type']]);
        Tpl::output("asset", $asset);
        Tpl::showPage("asset.item.rent");
    }

    function submitAssetEvaluateOp()
    {
        $params = $_POST;
        $params['officer_id'] = $this->user_id;
        $asset_id = $params['id'];
        $position = $this->user_position;
        if ($position == userPositionEnum::BRANCH_MANAGER) {
            $params['evaluator_type'] = researchPositionTypeEnum::BRANCH_MANAGER;
        } else {
            $params['evaluator_type'] = researchPositionTypeEnum::OPERATOR;
        }
        $ret = credit_officerClass::submitMemberAssetsEvaluate($params);
        if ($ret->STS) {
            showMessage("Save Successful!", getUrl('web_credit', 'assetItemDetail', array('asset_id' => $asset_id), false, BACK_OFFICE_SITE_URL));
        } else {
            showMessage($ret->MSG, getUrl('web_credit', 'showAssetsEvaluatePage', array('asset_id' => $asset_id), false, BACK_OFFICE_SITE_URL));
        }

    }

    function submitAssetRentalOp()
    {
        $params = $_POST;
        $params['officer_id'] = $this->user_id;
        $asset_id = $params['asset_id'];
        unset($_FILES['tmp_image']);

        if (!$params['uid']) {
            //更新
            $ret = credit_officerClass::addMemberAssetRentalResearch($params, $_FILES);
        } else {
            //修改
            //delete_image_ids
            $old_ids = $params['old_image'];
            //showMessage(my_json_encode($old_ids), getUrl('web_credit', 'assetItemDetail', array('asset_id' => $asset_id), false, BACK_OFFICE_SITE_URL));
            $rental = credit_researchClass::getLastMemberAssetRentalResearch($asset_id);
            $old_list = $rental['image_list'];
            $del_ids = array();
            if (count($old_list)) {
                foreach ($old_list as $img) {
                    if (!in_array($img['uid'], $old_ids)) {
                        $del_ids[] = $img['uid'];
                    }
                }
            }
            if (count($del_ids)) {
                $str_del = implode(",", $del_ids);
                $params['delete_image_ids'] = $str_del;
            }
            $ret = credit_officerClass::editMemberAssetRentalResearch($params, $_FILES);
        }

        if ($ret->STS) {
            showMessage("Save Successful!", getUrl('web_credit', 'assetItemDetail', array('asset_id' => $asset_id), false, BACK_OFFICE_SITE_URL));
        } else {
            showMessage($ret->MSG, getUrl('web_credit', 'showAssetsEvaluatePage', array('asset_id' => $asset_id), false, BACK_OFFICE_SITE_URL));
        }
    }

    public function showAssetItemEditPageOp()
    {
        $asset_id = $_REQUEST['asset_id'];
        $ret = credit_officerClass::getMemberAssetDetailAndResearchInfo($asset_id, $this->user_id);
        $asset = $ret->DATA;
        Tpl::output("asset", $asset);
        $member_id = $asset['member_id'];
        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        $asset_type = $asset['asset_type'];
        $image_structure = global_settingClass::getCertImageStructure();
        $stt = $image_structure[$asset_type];
        Tpl::output("image_structure", $stt);

        $property_list = enum_langClass::getAssetsType();
        //处理特殊的certType
        if($asset_type==certificationTypeEnum::DEGREE){
            $cert_type_list=(new degreeTypeEnum())->Dictionary();
        }else{
            $cert_type_list=(new assetsCertTypeEnum())->Dictionary();
        }
        Tpl::output("cert_type_list",$cert_type_list);

        Tpl::output("title", $property_list[$asset_type]);
        Tpl::output("member_id", $member_id);
        Tpl::output("asset_type", $asset_type);
        Tpl::showPage("asset.item.edit");
    }

    public function submitEditAssetItemOp()
    {
        //没找到api


    }

    /**
     * 信用建议
     */
    public function editSuggestCreditPageOp()
    {
        (new web_credit_v2Control())->editSuggestCreditPageOp();
    }


    /**
     * 保存申请
     */
    public function saveRequestCreditOp()
    {
        $params = array_merge(array(), $_GET, $_POST);

        $params['officer_id'] = $this->user_id;
        $params['request_type'] = ($this->user_position == userPositionEnum::BRANCH_MANAGER) ? researchPositionTypeEnum::BRANCH_MANAGER : researchPositionTypeEnum::OPERATOR;
        $all_category_id = array();
        if (!$params['default_credit_category_id']) {
            showMessage("required to choose credit category");
        }
        $all_category_id[] = $params['default_credit_category_id'];

        $increase_credit = $params['increase_credit'];
        $asset_id = $params['asset_id'];
        $member_credit_category_id = $params['member_credit_category_id'];
        $chk_increase = $params['chk_increase'];//是否选择抵押
        $asset_credit = array();
        foreach ($increase_credit as $key => $val) {
            if ($chk_increase[$key]) {
                if (!$member_credit_category_id[$key]) {
                    showMessage("required to choose credit category");
                }
                if (!in_array($member_credit_category_id[$key], $all_category_id)) {
                    $all_category_id[] = $member_credit_category_id[$key];
                }
                $asset_credit[] = array(
                    'asset_id' => $asset_id[$key],
                    'credit' => $val,
                    'member_credit_category_id' => $member_credit_category_id[$key]
                );
            }
        }
        $params['asset_credit'] = my_json_encode($asset_credit);

        $currency_credit = array();
        $credit_ccy_id = $params['credit_ccy_id'];
        $credit_ccy_chk = $params['credit_ccy_chk'];
        $credit_ccy_usd = $params['credit_ccy_usd'];
        $credit_ccy_khr = $params['credit_ccy_khr'];
        $credit_ccy_total = $params['credit_ccy_total'];

        foreach ($credit_ccy_id as $i => $ccy_id) {
            if (in_array($ccy_id, $all_category_id)) {
                $currency_credit[$ccy_id] = array(
                    "member_credit_category_id" => $ccy_id,
                    "credit" => $credit_ccy_total[$i],
                    "credit_usd" => ($credit_ccy_chk[$i] ? $credit_ccy_usd[$i] : $credit_ccy_total[$i]),
                    "credit_khr" => ($credit_ccy_chk[$i] ? $credit_ccy_khr[$i] : 0)
                );
            }
        }
        $params['credit_currency'] = $currency_credit;


        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = credit_officerClass::submitMemberSuggestCredit($params);
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage($rt->MSG, getBackOfficeUrl("web_credit", "creditClient", array("uid" => $params['member_id'])));
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
        return $m->submitRequestCreditToHq($uid);
    }

    public function cancelSubmitRequestCreditToHqOp($p)
    {
        $uid = intval($p['uid']);

        $m = new member_credit_suggestModel();
        return $m->cancelSubmitRequestCreditToHq($uid);
    }

    /*
     * bm自己权限范围内的授信
     */
    public function submitRequestCreditToFastGrantOp($p)
    {
        //判断权限
        $uid = intval($p['uid']);

        if (!$this->checkUserIsBm()) {
            return new result(false, 'Not BM.');
        }

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
     * 判断user是否为bm
     * @return bool
     */
    public function checkUserIsBm()
    {
        return $this->user_position == userPositionEnum::BRANCH_MANAGER ? true : false;
    }

    /**
     * 计算co均值
     * @param $co_suggest_list
     * @param $member_asset
     * @param $prod_list
     * @return array
     */
    public function avgCoSuggest($co_suggest_list, $member_asset, $prod_list)
    {
        $count = count($co_suggest_list);
        $co_list[0] = array('officer_name' => '--AVG--');
        $avg_co_suggest = array();
        $avg_co_suggest['uid'] = 0;
        $avg_co_suggest['operator_name'] = '--AVG--';
        $avg_co_suggest['monthly_repayment_ability'] = intval(sumArrayByKey($co_suggest_list, "monthly_repayment_ability") / $count);
        $avg_co_suggest['default_credit'] = intval(sumArrayByKey($co_suggest_list, "default_credit") / $count);
        $avg_co_suggest['max_credit'] = intval(sumArrayByKey($co_suggest_list, "max_credit") / $count);
        $avg_co_suggest['credit_terms'] = intval(sumArrayByKey($co_suggest_list, "credit_terms") / $count);

        $suggest_detail_list = array();
        foreach ($member_asset as $asset) {
            $suggest_detail_list[$asset['uid']] = array_merge($asset, array('credit' => 0, 'valuation' => 0, 'count' => 0));
        }
        //依赖自己对资产做valuation，所以不对co的increase做平均
        foreach ($suggest_detail_list as $key => $val) {
            $suggest_detail_list[$key]['credit'] = $val['count'] > 0 ? $val['credit'] / $val['count'] : 0;
            $suggest_detail_list[$key]['valuation'] = $val['count'] > 0 ? $val['valuation'] / $val['count'] : 0;
        }
        $suggest_product = $co_suggest_list[0]['suggest_product'];//默认取第一个

        //


        $avg_co_suggest['suggest_detail_list'] = $suggest_detail_list;
        $avg_co_suggest['suggest_product'] = $suggest_product;

        return $avg_co_suggest;
    }

    /**
     * 获取信用贷子产品
     */
    private function getSubCreditLoan()
    {
        //产品列表
        $credit_loan = credit_loanClass::getProductInfo();
        $prod_list = loan_productClass::getActiveSubProductListById($credit_loan['uid']);
        foreach ($prod_list as $k => $v) {
            $rate = loan_productCLass::getMinMonthlyRate($v['uid'], 'max');
            $prod_list[$k]['max_rate_mortgage'] = $rate;
        }
        $prod_list = resetArrayKey($prod_list, "uid");
        return $prod_list;
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
     * 修改身份证件
     */
    public function uploadClientIdentityOp()
    {
        $member_id = $_GET['member_id'];
        $identity_type = $_GET['identity_type'];
        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        $image_structure = global_settingClass::getCertImageStructure();
        $stt = $image_structure[$identity_type];
        Tpl::output("image_structure", $stt);

        $property_list = memberIdentityClass::getIdentityType();

        Tpl::output("member_id", $member_id);
        Tpl::output("identity_type", $identity_type);
        Tpl::output("title", $property_list[$identity_type]);

        if ($identity_type == certificationTypeEnum::ID) {
            $country_code = (new nationalityEnum)->Dictionary();
            Tpl::output('country_code', $country_code);
            Tpl::showPage("identity.item.add");
        } else {
            Tpl::showPage("member.document.add");
        }
    }

    /**
     * 修改身份证件
     */
    public function editUploadClientIdentityOp()
    {
        $r = new ormReader();
        $member_id = intval($_GET['member_id']);
        $cert_id = intval($_GET['cert_id']);
        $identity_type = $_GET['identity_type'];
        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid client id.');
        }
        Tpl::output('client_info', $client_info);

        $m_member_verify_cert = M('member_verify_cert');
        $data = $m_member_verify_cert->getRow(array('uid' => $cert_id));
        if (!$data) {
            showMessage('Invalid identity  id!');
        }
        // image
        $sql = "select * from member_verify_cert_image where cert_id='" . $data['uid'] . "'";
        $images = $r->getRows($sql);
        $data['cert_images'] = resetArrayKey($images, 'image_key');
        Tpl::output('cert_info', $data);


        $image_structure = global_settingClass::getCertImageStructure();
        $stt = $image_structure[$identity_type];
        Tpl::output("image_structure", $stt);

        $property_list = memberIdentityClass::getIdentityType();

        Tpl::output("member_id", $member_id);
        Tpl::output("identity_type", $identity_type);
        Tpl::output("title", $property_list[$identity_type]);

        if ($identity_type == certificationTypeEnum::ID) {
            $country_code = (new nationalityEnum)->Dictionary();
            Tpl::output('country_code', $country_code);
            Tpl::showPage("identity.item.edit");
        } else {
            Tpl::showPage("member.document.edit");
        }
    }

    /**
     * 保存修改新证件
     */
    public function submitClientNewIdentityOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $params['auditor_id'] = $this->user_id;
        $params['auditor_name'] = $this->user_name;
        $class_member_identity = new memberIdentityClass();
        $rt = $class_member_identity->saveClientNewIdentity($params);
        if ($rt->STS) {
            showMessage($rt->MSG, getUrl('web_credit', 'creditClient', array('uid' => $params['client_id']), false, BACK_OFFICE_SITE_URL));
        } else {
            showMessage($rt->MSG);
        }
    }

    function deleteMemberAssetOp($p)
    {
        $asset_id = $p['asset_id'];
        $officer_id = $this->user_id;
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = credit_officerClass::deleteMemberAsset($asset_id, $officer_id);
        if ($rt->STS) {
            $conn->submitTransaction();
        } else {
            $conn->rollback();
        }
        return $rt;
    }

    function changeMemberAssetInvalidOp($p)
    {
        $officer_id = $this->user_id;
        $asset_id = $p['asset_id'];
        $is_invalid = 1;
        $rt = credit_officerClass::updateMemberAssetState($asset_id, $officer_id, $is_invalid);
        return $rt;
    }

    /**
     * 修改资产信息
     */
    public function editAssetBaseInfoOp($p)
    {

        $user_position = $this->user_position;
        if ($user_position == userPositionEnum::BRANCH_MANAGER) {
            $is_bm = true;
        } else {
            $is_bm = false;
        }

        $asset_id = intval($p['asset_id']);
        $params = array(
            'asset_id' => $asset_id,
            'asset_name' => $p['asset_name'],
            'asset_sn' => $p['asset_sn'],
            'asset_type' => intval($p['asset_type']),
            'cert_issue_time' => $p['cert_issue_time'],
        );

        if (!$p['relative_id']) {
            $p['relative_id'] = 0;
        }

        if (!is_array($p['relative_id'])) {
            $p['relative_id'] = array(
                $p['relative_id']
            );
        }
        $relative_id = implode(',', $p['relative_id']);
        $params['relative_id'] = $relative_id;

        $rt = member_assetsClass::editMemberAssetBaseInfo($params, $is_bm);
        return $rt;

    }

    public function editAssetCertTypeOp($p)
    {
        $asset_id = intval($p['asset_id']);
        $asset_cert_type = $p['asset_cert_type'];
        return member_assetsClass::editAssetCertType($asset_id, $asset_cert_type);
    }

    public function assetAddMoreImageOp($p)
    {

        $asset_id = $p['asset_id'];
        $images = @json_decode($p['image_files'],true);
        $user_id = $this->user_id;
        // 封装数据格式
        $data = array();
        $data['asset_id'] = $asset_id;
        $data['officer_id'] = $user_id;
        $image_list = array();
        foreach( $images as $path ){
            $image_list[] = array(
                'image_key' => '',
                'image_url' => $path,
                'image_source' => imageSourceEnum::ALBUM
            );
        }
        $data['image_list'] = $image_list;
        return credit_officer_v2Class::addExtendImageForAsset($data);

    }

    public function showPackageInterestSettingOp()
    {
        $package_id = $_GET['package_id'];
        $uid = $_GET['package_id'];
        $package_name = $_GET['package_name'];
        $arr = loan_productClass::getSizeRateByPackageIdGroupByProduct($uid);
        Tpl::output("list", $arr);
        Tpl::output("package_name", $package_name);
        Tpl::output("is_readonly", true);
        Tpl::showPage("../loan/product.package.item.interest");
    }

    public function showRequestWithdrawMortgagePageOp()
    {
        $asset_id = $_REQUEST['asset_id'];
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
        if (count($request_list)) {
            $last_request = end($request_list);
            if ($last_request['state'] == assetRequestWithdrawStateEnum::PENDING_APPROVE) {
                Tpl::output("default_item", $last_request);
            }
        }
        Tpl::output("request_list", $request_list);

        Tpl::showPage("asset.item.request.withdraw");
    }

    public function submitAssetRequestWithdrawOp()
    {
        $args = $_POST;
        $args['operator_id'] = $this->user_id;
        $args['operator_name'] = $this->user_name;
        $ret = member_assetsClass::saveRequestWithdraw($args);
        if (!$ret->STS) {
            showMessage($ret->MSG, getUrl("web_credit", "showRequestWithdrawMortgagePage", array("asset_id" => $args['member_asset_id']), false, BACK_OFFICE_SITE_URL));
        } else {
            showMessage("Save Success!", getUrl("web_credit", "assetItemDetail", array("asset_id" => $args['member_asset_id']), false, BACK_OFFICE_SITE_URL));
        }
    }

    public function showAssetsSurveyPageOp()
    {
        $asset_id = $_REQUEST['asset_id'];
        $ret = credit_officerClass::getMemberAssetDetailAndResearchInfo($asset_id, $this->user_id);
        $asset = $ret->DATA;
        $member_id = $asset['member_id'];
        $client_info = memberClass::getMemberBaseInfo($member_id);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);


        $property_list = enum_langClass::getAssetsType();
        Tpl::output("asset_type", $property_list[$asset['asset_type']]);
        Tpl::output("asset", $asset);
        Tpl::showPage("asset.item.survey");
    }

    public function submitAssetSurveyOp()
    {
        $p = $_POST;
        $asset_id = $p['asset_id'];
        $items = array();
        foreach ($p as $k => $val) {
            if (startWith($k, "item_")) {
                $items[substr($k, 5)] = $val;
            }
        }
        $survey_json = my_json_encode($items);
        $ret = credit_researchClass::assetAddSurveyInfo($asset_id, $survey_json, $this->user_id);
        if (!$ret->STS) {
            showMessage($ret->MSG, getUrl("web_credit", "showAssetsSurveyPage", array("asset_id" => $asset_id), false, BACK_OFFICE_SITE_URL));
        } else {
            showMessage("Save Success!", getUrl("web_credit", "assetItemDetail", array("asset_id" => $asset_id), false, BACK_OFFICE_SITE_URL));
        }

    }

    /**
     * 修改会员Operator页面
     */
    public function editMemberOperatorPageOp()
    {
        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        $m_um_user = M('um_user');
        $operator_list = $m_um_user->getOperatorList();
        Tpl::output('operator_list', $operator_list);

        $m_member_follow_officer = M('member_follow_officer');
        $member_operator = $m_member_follow_officer->getOperatorInfoByMemberId($uid);
        Tpl::output('member_operator', $member_operator);
        Tpl::showPage('client.operator.edit');
    }

    /**
     * 修改会员operator
     */
    public function editMemberOperatorOp()
    {
        $member_id = intval($_POST['uid']);
        $officer_id = intval($_POST['officer_id']);
        $m_member = new memberClass();
        $rt = $m_member->resetMemberOperator($member_id, $officer_id);
        if (!$rt->STS) {
            showMessage('Edit failed!' . $rt->MSG);
        } else {
            showMessage('Edit successful.', getUrl('web_credit', 'creditClient', array('uid' => $member_id), false, BACK_OFFICE_SITE_URL));
        }
    }

    public function pushMessageToUserOp()
    {
        $member_id = intval($_GET['uid']);
        $member_info = memberClass::getMemberBaseInfo($member_id);
        Tpl::output('member_info', $member_info);

        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showPage('member.push_notification');
    }

    public function getPushNotificationListOp($p)
    {
        $member_id = intval($p['member_id']);
        $search_text = trim($p['search_text']);
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $r = new ormReader();
        $sql = "SELECT mm.*,mmr.read_time FROM member_message mm INNER JOIN member_message_receiver mmr ON mm.message_id = mmr.message_id WHERE (mm.message_time BETWEEN '" . $d1 . "' AND '" . $d2 . "') AND mm.message_type = " . messageTypeEnum::NORMAL . " AND mmr.receiver_id = " . $member_id;
        if ($search_text) {
            $sql .= " AND mm.message_title like '%" . qstr2($search_text) . "%'";
        }

        $sql .= ' ORDER BY mm.message_id DESC';
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

    public function addPushNotificationOp($p)
    {
        $member_id = intval($p['member_id']);
        $title = trim($p['message_title']);
        $body = trim($p['message_body']);
        if (!$title || !$body) {
            return new result(false, 'Param Error.');
        }

        $rt = member_messageClass::sendSystemMessage($member_id, $title, $body);
        return $rt;
    }

    public function lockClientForCreditOfficerOp($p)
    {
        $member_id = intval($p['member_id']);
        $rt = memberInfoClass::lockClientForCo($member_id, 1);
        return $rt;
    }

    public function unlockClientForCreditOfficerOp($p)
    {
        $member_id = intval($p['member_id']);
        //需要reject所有co-to-bm的任务
        $m_task = new task_co_bmModel();
        $ret_cancel = $m_task->cancelOldTaskOfMemberId($member_id);
        $rt = memberInfoClass::lockClientForCo($member_id, 0);
        return $rt;
    }

    public function setPrimaryCoOp($p)
    {
        $uid = intval($p['primary_id']);
        $member_id = intval($p['member_id']);
        $m_member_follow_officer = M('member_follow_officer');
        $pre_officer = $m_member_follow_officer->getRow(array('member_id' => $member_id, 'is_primary' => 1));
        if ($pre_officer) {
            $pre_officer->is_primary = 0;
            $rt = $pre_officer->update();
            if (!$rt) {
                return new result(false, 'Clear Pre Primary Failure');
            }
        }
        $row = $m_member_follow_officer->getRow($uid);
        if ($row) {
            $row->is_primary = 1;
            $rt = $row->update();
            if ($rt->STS) {
                return new result(true, 'Set Primary CO Successful!');
            } else {
                return new result(false, 'Set Failure!');
            }
        } else {
            return new result(false, 'Invalid CO');
        }
    }

    public function changeMemberStateOp($p)
    {
        $member_id = intval($p['member_id']);
        $member_state = intval($p['member_state']);
        $remark = trim($p['remark']);
        if ($this->user_position == userPositionEnum::BRANCH_MANAGER || $this->user_position == userPositionEnum::ROOT) {
            $rt = memberClass::changeMemberState($member_id, $member_state, $remark, $this->user_id);
            return $rt;
        } else {
            return new result(false, 'Not Bm.');
        }
    }

    /**
     * 设置member的产品
     */
    public function editMemberCreditCategoryOp()
    {
        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);
        /*

        $list = M('loan_category')->getCategoryList();//所有loan category
        $member_category = loan_categoryClass::getMemberCreditCategoryList($uid, 1); //member category
        $member_category_new = array();
        foreach($member_category as $k => $v){
            $member_category_new[$v['category_id']] = $v;
        }
        foreach($list as $k => $v){
            if($member_category_new[$v['uid']]){
                $list[$k]['category_id'] = $v['uid'];
                $list[$k]['is_close'] = $member_category_new[$v['uid']]['is_close'];
                $list[$k]['member_id'] = $member_category_new[$v['uid']]['member_id'];
                $list[$k]['member_category_id'] = $member_category_new[$v['uid']]['uid'];
                $list[$k]['sub_product_id'] = $member_category_new[$v['uid']]['sub_product_id'];
                $list[$k]['sub_product_name'] = $member_category_new[$v['uid']]['sub_product_name'];
                $list[$k]['is_one_time'] = $member_category_new[$v['uid']]['is_one_time'];
            }else{
                $list[$k]['category_id'] = $v['uid'];
                $list[$k]['is_close'] = 1;
                $list[$k]['member_id'] = 0;
                $list[$k]['member_category_id'] = 0;
                $list[$k]['sub_product_id'] = $v['sub_product_id'];
                $list[$k]['sub_product_name'] = $v['default_product_name'];
                $list[$k]['is_one_time'] = $v['is_one_time'];
            }
        }*/
        $list = loan_categoryClass::getMemberCreditCategorySetting($uid);
        Tpl::output('member_category_list', $list);
        Tpl::showPage('client.credit.category.index');
    }

    public function editMemberCreditCategoryOldOp()
    {
        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);

        //product list
        $cls = new loan_categoryClass();
        $credit_category = $cls->getMemberCreditCategoryList($uid);
        Tpl::output("member_category_list", $credit_category);
        Tpl::showPage('client.credit.category.index');
    }

    public function editMemberCreditCategoryPageOp()
    {
        $member_id = $_GET['member_id'];
        //输出可选的sub_product
        $sub_list = loan_productClass::getAllActiveSubProductList();
        $arr_sub = array();
        foreach ($sub_list as $item) {
            $arr_sub[] = array("sub_product_id" => $item['uid'], "sub_product_name" => $item['sub_product_name']);
        }
        Tpl::output("sub_list", $arr_sub);

        $code_list = loan_categoryClass::getAllCategoryList();
        $arr_category = array();
        foreach ($code_list as $item) {
            $arr_category[$item['uid']] = array("category_id" => $item['uid'], "category_name" => $item['category_name']);
        }
        Tpl::output("code_list", $arr_category);

        Tpl::output("member_id", $member_id);
        $uid = $_GET['uid'];
        if ($uid) {
            $cur_item = loan_categoryClass::getMemberCreditCategoryItemById($uid);
            Tpl::output("category_info", $cur_item);
        }
        Tpl::showPage("client.credit.category.editor");


    }

    function submitCreditCategoryEditorOp()
    {
        $p = $_POST;
        if (!$p['member_id']) {
            showMessage("Required Member ID");
        }
        if (!$p['sub_product_id']) {
            showMessage("Required to input Repayment ID");
        }
        if (!$p['interest_package_id']) {
            showMessage("Required to input interest-package");
        }

        $m_member_category = new member_credit_categoryModel();
        $row = $m_member_category->getRow(array("member_id" => $p['member_id'], "category_id" => $p['category_id']));
        if (!$row) {
            showMessage("This Credit Category has not been set up yet");
        }
        $row->sub_product_id = $p['sub_product_id'];
//        if(isset($p['is_one_time'])){
        $row->is_one_time = intval($p['is_one_time']);
//        }
        $row->interest_package_id = intval($p['interest_package_id']);
        $row->update_time = Now();
        $row->update_operator_id = $this->user_id;
        $ret = $row->update();
        if ($ret->STS) {
            showMessage("Saved Success!", getBackOfficeUrl("web_credit", "editMemberCreditCategory", array("uid" => $p['member_id'])));
        } else {
            showMessage($ret->MSG);
        }

    }


    public function submitLoanCategoryOp($p)
    {
        $officer_id = $this->user_id;
        $member_id = intval($p['member_id']);
        $category_id = intval($p['category_id']);
        $is_close = intval($p['state']);
        $params['officer_id'] = $officer_id;
        $params['member_id'] = $member_id;
        $params['category_id'] = $category_id;
        $params['is_close'] = $is_close;

        $m_member_category = new member_credit_categoryModel();
        $info = $m_member_category->find(array('member_id' => $member_id, 'category_id' => $category_id));
        if ($info) { //存在则更改is_close
            $ret = $m_member_category->updateMemberCategoryState($params);
            $ret->DATA = getBackOfficeUrl("web_credit", "editMemberCreditCategoryProductPage", array("member_id" => $member_id, "uid" => $info['uid']));
        } else {//否则添加
            $ret = $m_member_category->addMemberCategory($params);
            $ret->DATA = getBackOfficeUrl("web_credit", "editMemberCreditCategoryProductPage", array("member_id" => $member_id, "uid" => $ret->DATA));
        }
        return $ret;
    }

    public function editMemberCreditCategoryProductPageOp()
    {
        /*
        $member_id = $_GET['member_id'];
        $uid = $_GET['uid'];
        $m = new member_credit_categoryModel();
        $item = $m->find(array("uid" => $uid));
        if (!$item) {
            showMessage("Invalid Parameter:No Row Found");
        }
        Tpl::output("category_info", $item);

        //输出可选的sub_product
        $sub_list = loan_productClass::getAllActiveSubProductList();
        $arr_sub = array();
        foreach ($sub_list as $item) {
            $arr_sub[] = array("sub_product_id" => $item['uid'], "sub_product_name" => $item['sub_product_name']);
        }
        Tpl::output("sub_list", $arr_sub);

        //package_list
        $package_list = loan_productClass::getProductPackageList();
        Tpl::output('package_list', $package_list);
        Tpl::output("member_id", $member_id);
        Tpl::output("uid", $uid);
        Tpl::showPage("client.credit.category.editor");
        */
        (new web_credit_v2Control())->editMemberCreditCategoryProductPageV2Op();


    }

    /**
     * BM 处理 CO 对客户的提交动作
     */
    public function handleCOSubmitOp($p)
    {
        $task_id = $p['task_id'];
        $sts = $p['sts'];//1 接受，-1 拒绝
        $msg = $p['msg'];
        if (!$task_id) return new result(false, "Invalid Parameter");
        $m_task = new task_co_bmModel();
        $task = $m_task->getRow(array("uid" => $task_id, "state" => commonApproveStateEnum::APPROVING));
        if (!$task) return new result(false, "Invalid Parameter:Task State Is Expiry");
        if ($sts) {
            $task->state = commonApproveStateEnum::PASS;
        } else {
            $task->state = commonApproveStateEnum::REJECT;
            $task->handle_comment = $msg;
        }
        $task->handle_time = Now();
        $task->update_time = Now();
        $task->handler_id = $this->user_id;
        $task->handler_name = $this->user_name;
        $ret_task = $task->update();
        if ($sts && $ret_task->STS) {
            memberInfoClass::lockClientForCo($task->member_id, 1);
        }
        return $ret_task;

    }

    public function getCoSubmitTaskOp()
    {
        $task_id = $_GET['task_id'];
        $member_id = $_GET['member_id'];
        taskControllerClass::finishTask($task_id, userTaskTypeEnum::CO_SUBMIT_BM, $this->user_info['branch_id'], objGuidTypeEnum::SITE_BRANCH);
        $this->creditClientOp(array("member_id" => $member_id, "hide_top_menu" => 1));
    }

    public function creditCategoryInterestPageOp()
    {
        $member_credit_category_id = $_GET['mcc_id'];
        $member_id = $_GET['member_id'];
        $credit_list = loan_categoryClass::getMemberCreditCategoryList($member_id);
        $category = $credit_list[$member_credit_category_id];
        Tpl::output("credit_category", $category);
        $loan_acct = loan_accountClass::getLoanAccountInfoByMemberId($member_id);
        Tpl::output("loan_account", $loan_acct);
        Tpl::showPage("member.credit.category.interest.list");
    }
}
