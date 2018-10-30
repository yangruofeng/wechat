<?php

class clientControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('certification,operator');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "User List");
        $verify_field = enum_langClass::getCertificationTypeEnumLang();
        Tpl::output("verify_field", $verify_field);
        Tpl::setDir("client");

    }

    public function clientOp()
    {
        Tpl::showPage("client");
    }

    public function getLoanBalance($uid = 0)
    {
        $r = new ormReader();
        $sql1 = "select contract_id,sum(receivable_principal) as count from loan_installment_scheme where state != " . schemaStateTypeEnum::CREATE . " and state != " . schemaStateTypeEnum::COMPLETE . " GROUP BY contract_id";
        $rows = $r->getRows($sql1);
        $sum_arr = array();
        foreach ($rows as $key => $value) {
            $sum_arr[$value['contract_id']] = $value['count'];
        }
        $sql2 = "SELECT uid,account_id from loan_contract";
        if ($uid) {
            $sql2 = "SELECT uid,account_id from loan_contract where account_id = " . $uid;
        }
        $rows2 = $r->getRows($sql2);
        $acc_loan_balance = array();
        foreach ($rows2 as $key => $value) {
            if ($acc_loan_balance[$value['account_id']]) {
                $acc_loan_balance[$value['account_id']]['sum'] += $sum_arr[$value['uid']];
            } else {
                $acc_loan_balance[$value['account_id']]['sum'] = $sum_arr[$value['uid']];
            }
        }
        return $acc_loan_balance;
    }

    /**
     * 获取会员列表
     * @param $p
     * @return array
     */
    public function getClientListOp($p)
    {
        $search_text = trim($p['search_text']);
        $r = new ormReader();
        $sql = "SELECT loan.*,client.uid AS member_id,client.obj_guid AS o_guid,client.login_code,client.display_name,client.alias_name,client.phone_id,client.email,client.create_time ";
        $sql.=" ,member_credit.credit,member_credit.credit_balance ";
        $sql.=" FROM client_member AS client LEFT JOIN loan_account AS loan ON loan.obj_guid = client.obj_guid ";
        $sql.=" left join member_credit on client.uid=member_credit.member_id";
        $sql.=" WHERE 1 = 1 ";

        if ($search_text) {
            $sql .= " AND (loan.obj_guid = " . qstr($search_text);
            $sql .= " OR client.login_code LIKE '%" . qstr2($search_text) . "%'";
            $sql .= " OR client.display_name LIKE '%" . qstr2($search_text) . "%'";
            $sql .= " OR client.phone_id LIKE '%" . qstr2($search_text) . "%')";
        }

        if (intval($p['ck'])) {
            $sql .= ' AND client.create_time >= "' . date("Y-m-d") . '"';
        }

        $sql .= " ORDER BY client.create_time DESC";
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
     * 会员详情
     */
    public function clientDetailOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $member_id = intval($p['uid']);
        $url=getBackOfficeUrl("web_credit","creditClient",array("uid"=>$member_id,"hide_top_menu"=>1));
        goURL2($url);

        /*
        /*
        $rt = memberInfoClass::getMemberDetail($member_id);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }
        $data = $rt->DATA;
        Tpl::output("contract_info", $data['contract_info']);
        Tpl::output("loan_summary", $data['loan_summary']);
        Tpl::output("detail", $data['detail']);
        Tpl::output('credit_info', $data['credit_info']);
        Tpl::output("contracts", $data['contracts']);
        Tpl::output("insurance_contracts", $data['insurance_contracts']);
        Tpl::output("black", $data['black']);
        Tpl::output("savings_balance", $data['savings_balance']);
        $pre = $p['pre'];
        Tpl::output("pre", $pre);
        Tpl::showPage("client.detail.v4");
        */
    }

    public function editClientBlackFieldOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_client_black = M('client_black');
        $data = $m_client_black->getBlackInfo($p['obj_guid']);
        if ($data->STS) {
            $param = json_decode($data->DATA['type'], true);
            foreach ($param as $key => $value) {
                if ($key == $p['filed']) {
                    $param[$key] = $p['state'];
                }
            }
            $param['obj_guid'] = $p['obj_guid'];
            $param['auditor_id'] = $this->user_id;
            $param['auditor_name'] = $this->user_name;
            $rt = $m_client_black->updateBlack($param);
        } else {
            $param = array('t1' => 0, 't2' => 0, 't3' => 0, 't4' => 0, 't5' => 0);
            foreach ($param as $key => $value) {
                if ($key == $p['filed']) {
                    $param[$key] = $p['state'];
                }
            }
            $param['obj_guid'] = $p['obj_guid'];
            $param['auditor_id'] = $this->user_id;
            $param['auditor_name'] = $this->user_name;
            $rt = $m_client_black->insertBlack($param);
        }
        if ($rt->STS) {
            return new result(true, 'Edit Success!');
        } else {
            return new result(false, 'Invalid Member!');
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
        $source_mark = trim($p['source_mark']);

        $r = new ormReader();
        $sql = "SELECT * FROM member_verify_cert WHERE member_id = $member_id AND cert_type = $cert_type AND verify_state = 10 ORDER BY auditor_time DESC";
        $verify_cert = $r->getRow($sql);
        if (!$verify_cert) {
            return new result(false, 'Param Error!');
        }

        $url = getUrl('client', 'showCertificationDetail', array('uid' => $verify_cert['uid'], 'source_mark' => $source_mark), false, BACK_OFFICE_SITE_URL);
        return new result(true, 'Param Error!', $url);
    }

    /**
     * 查看已审核资料
     * @throws Exception
     */
    public function showCertificationDetailOp()
    {
        $uid = intval($_GET['uid']);
        $m_member_verify_cert = M('member_verify_cert');
        $row = $m_member_verify_cert->getRow(array('uid' => $uid));
        if (!$row) {
            showMessage('Invalid Id!');
        }

        if ($row->verify_state < certStateEnum::PASS) {
            showMessage('The request has not been audited!');
        }

        $this->cerificationDetailOp();
    }

    public function cerificationOp()
    {
        Tpl::showPage("cerification");
    }

    public function getCerificationListOp($p)
    {
        $r = new ormReader();

        // 不分组了，家庭关系有多条
        $sql1 = "select verify.*,member.login_code,member.display_name,member.phone_id,member.email,ma.asset_name,ma.asset_sn from member_verify_cert as verify"
            . " left join client_member as member on verify.member_id = member.uid"
            . " left join member_assets as ma on verify.uid = ma.cert_id"
            . " where 1 = 1 ";

        if ($p['cert_type'] != 0) {
            $sql1 .= " and verify.cert_type = '" . $p['cert_type'] . "' ";
        }
        if ($p['verify_state'] == 1) {
            $sql1 .= " and (verify.verify_state = 0 or verify.verify_state = -1 ) ";
        }
        if ($p['verify_state'] == 10) {
            $sql1 .= " and verify.verify_state = " . $p['verify_state'];
        }
        if ($p['verify_state'] == 100) {
            $sql1 .= " and verify.verify_state = " . $p['verify_state'];
        }
        if ($p['member_name']) {
            $name = ' (member.login_code like "%' . $p['member_name'] . '%" )';
            $sql1 .= " and " . $name;
        }
        $sql1 .= " ORDER BY verify.uid desc";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql1, $pageNumber, $pageSize);
        $rows = $data->rows;
        $list = array();
        // 取图片
        foreach ($rows as $row) {
            $sql = "select * from member_verify_cert_image where cert_id='" . $row['uid'] . "'";
            $images = $r->getRows($sql);
            $row['cert_images'] = $images;
            $list[] = $row;
        }
        $total = $data->count;
        $pageTotal = $data->pageCount;

        $asset_type = enum_langClass::getAssetsType();

        return array(
            "sts" => true,
            "data" => $list,
            "total" => $total,
            "cur_uid" => $this->user_id,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
            "asset_type" => $asset_type,
        );

    }

    public function cerificationDetailOp()
    {
        $sample_images = global_settingClass::getCertSampleImage();
        Tpl::output('cert_sample_images', $sample_images);

        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($p['uid']);

        $r = new ormReader();
        $m_member_verify_cert = M('member_verify_cert');
        $m_client_member = M('client_member');
        $data = $m_member_verify_cert->find(array('uid' => $uid));

        $client_info = $m_client_member->find(array('uid' => $data['member_id']));
        Tpl::output('client_info', $client_info);

        $sql = "SELECT mvc.*,ma.asset_name,ma.asset_sn FROM member_verify_cert mvc LEFT JOIN member_assets ma ON mvc.uid = ma.cert_id where mvc.member_id = " . $data['member_id'] . " and mvc.cert_type = " . $data['cert_type'] . " order by mvc.uid desc";
        $history = $r->getRows($sql);

        foreach ($history as $k => $v) {
            $sql = "select * from member_verify_cert_image where cert_id='" . $v['uid'] . "'";
            $images = $r->getRows($sql);
            $v['cert_images'] = $images;
            $history[$k] = $v;
        }
        Tpl::output('history', $history);

        // image
        $sql = "select * from member_verify_cert_image where cert_id='" . $data['uid'] . "'";
        $images = $r->getRows($sql);
        $data['cert_images'] = $images;
        Tpl::output('info', $data);

        switch ($data['cert_type']) {
            case certificationTypeEnum::RESIDENT_BOOK :
            case certificationTypeEnum::ID :
            case certificationTypeEnum::PASSPORT :
            case certificationTypeEnum::BIRTH_CERTIFICATE:
            case certificationTypeEnum::FAIMILYBOOK :
                Tpl::showPage("certification.detail");
                break;
            case certificationTypeEnum::MOTORBIKE:
            case certificationTypeEnum::HOUSE :
            case certificationTypeEnum::CAR :
            case certificationTypeEnum::STORE :
            case certificationTypeEnum::LAND :
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
                    'cert_id' => $data['uid']
                ));
                Tpl::output('extend_info', $extend_info);
                Tpl::showPage('certification.work.detail');
                break;
            default:
                showMessage('Not supported type');
        }
    }

    public function setCertificationExpiredOp($p) {
        $user_id = $this->user_id;
        return memberIdentityClass::updateCertFileExpired($p["uid"], $user_id);
    }

    public function blackListOp()
    {
        $r = new ormReader();
        $sql = "select type,count(uid) total_count from client_black group by type";
        $rows = $r->getRows($sql);
        $type_count = array();
        foreach ($rows as $v) {
            $type_count[$v['type']] = $v['total_count'];
        }

        $types = (new blackTypeEnum())->Dictionary();
        foreach ($types as $k => $value) {
            $temp = array(
                'type' => $k,
                'count' => $type_count[$k] ?: 0
            );
            $types[$k] = $temp;
        }

        Tpl::output('types', $types);
        Tpl::showPage("black");
    }

    public function getBlackClientListOp($p)
    {
        $r = new ormReader();
        $sql = "select uid,display_name from client_member";
        $list = $r->getRows($sql);
        $sql1 = "select * from client_black where type = " . $p['type'];
        $black = $r->getRow($sql1);
        $members = $black['list'];
        $members = $members ? explode(',', $members) : array();
        foreach ($list as $key => $value) {
            $list[$key]['check'] = false;
            if (in_array($value['uid'], $members)) {
                $list[$key]['check'] = true;
            }
        }
        return new result(true, '', $list);
    }

    public function getBlackListOp($p)
    {
        $r = new ormReader();
        $sql = "select member.*,black.type as black from client_member as member left join client_black as black on member.obj_guid = black.obj_guid";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        $sql1 = "select * from client_black";

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );

    }

    public function addBlackClientOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        Tpl::output('type', $p['type']);
        Tpl::showPage("black_add");
    }

    public function getAddBlackClientOp($p)
    {

        $m = new client_blackModel();
        $type = $p['type'];
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 15;
        $data = $m->getClientListNotInBlackOfType($type, $pageNumber, $pageSize, $p);

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

    public function removeBlackClientOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        Tpl::output('type', $p['type']);
        Tpl::showPage("black_remove");
    }

    public function getRemoveBlackClientOp($p)
    {

        $m = new client_blackModel();
        $type = $p['type'];
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 15;
        $data = $m->getBlackClientByType($type, $pageNumber, $pageSize, $p);
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

    public function updateBlackClientListOp($p)
    {
        $m_client_black = M('client_black');
        $p['auditor_id'] = $this->user_id;
        $p['auditor_name'] = $this->user_name;
        $rt = $m_client_black->updateBlack($p);
        if ($rt->STS) {
            return new result(true);
        } else {
            return new result(false);
        }
    }

    public function updateBlackClientTypeOp($p)
    {
        $m_client_black = new client_blackModel();
        $member_id = $p['uid'];
        $type = $p['type'];
        $state = $p['state'];
        $user_info = array(
            'user_id' => $this->user_id,
            'user_name' => $this->user_name
        );

        $rt = $m_client_black->updateClientBlack($member_id, $type, $state, $user_info);
        return $rt;

    }

    public function editBlackOp()
    {
        $r = new ormReader();
        $p = array_merge(array(), $_GET, $_POST);
        $m_client_member = M('client_member');
        $m_client_black = M('client_black');
        if ($p['form_submit'] == 'ok') {
            unset($p['form_submit']);
            unset($p['op']);
            unset($p['act']);
            $member_info = $m_client_member->getRow(array(
                'obj_guid' => $p['obj_guid']
            ));
            if (!$member_info) {
                showMessage('No member.');
            }
            $data = $m_client_black->getBlackInfo($p['obj_guid']);
            $p['member_id'] = $member_info->uid;
            $p['auditor_id'] = $this->user_id;
            $p['auditor_name'] = $this->user_name;
            if ($data->STS) {
                $rt = $m_client_black->updateBlack($p);
            } else {
                $rt = $m_client_black->insertBlack($p);
            }
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('client', 'blackList', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage($rt->MSG, getUrl('client', 'editBlack', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $row = $m_client_member->getRow(array('uid' => $p['uid']));
            $data = $row->toArray();
            if (!$data) {
                showMessage('Client Not Exist', getUrl('loan', 'credit', array(), false, BACK_OFFICE_SITE_URL));
            }
            $sql = "SELECT loan.*,client.display_name,client.alias_name,client.phone_id,client.email FROM loan_account as loan left join client_member as client on loan.obj_guid = client.obj_guid where loan.obj_guid = '" . $data['obj_guid'] . "'";
            $info = $r->getRow($sql);
            Tpl::output('info', $info);
            $blacks = $m_client_black->getBlackInfo($data['uid']);
            $blacks = $blacks->DATA;
            $black = json_decode($blacks['type'], true);
            Tpl::output('black', $black);
            Tpl::showPage("black.edit");
        }


    }

    public function creditReportOp()
    {
        $r = new ormReader();
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['obj_guid']) {

            $member_info = (new memberModel())->find(array(
                'obj_guid' => $p['obj_guid']
            ));

            $sql = "SELECT client.*,loan.uid as loan_uid,c.credit FROM loan_account as loan left join client_member as client on loan.obj_guid = client.obj_guid left join member_credit c on c.member_id=client.uid where client.obj_guid = " . $p['obj_guid'];
            $data = $r->getRow($sql);

            $sql1 = "SELECT * FROM member_verify_cert where member_id = " . $data['uid'];
            $rows = $r->getRows($sql1);

            $sql2 = "SELECT * FROM member_credit_grant where member_id = " . $member_info['uid'] . " ORDER BY  uid desc";
            $credit_list = $r->getRows($sql2);

            $sql3 = "SELECT repayment.uid,repayment.state FROM loan_contract as contract right JOIN loan_repayment as repayment on contract.uid = repayment.contract_id"
                . " where contract.account_id = " . $data['loan_uid'] . " and repayment.state = 100";
            $remayment_list = $r->getRows($sql3);
            $sql4 = "SELECT scheme.uid FROM loan_contract as contract left JOIN loan_installment_scheme as scheme on contract.uid = scheme.contract_id"
                . " where contract.account_id = " . $data['loan_uid'] . " and contract.state >= 20 and scheme.state != 100 and '" . date("Y-m-d H:m:s") . "' > scheme.penalty_start_date";
            $breach_list = $r->getRows($sql4);
        }
        $verifys = array();
        foreach ($rows as $key => $value) {
            $verifys[$value['cert_type']] = $value;
        }
        Tpl::output("detail", $data);
        Tpl::output("verifys_list", $rows);
        Tpl::output("verifys", $verifys);
        Tpl::output('credit_list', $credit_list);
        Tpl::output('remayment_count', count($remayment_list));
        Tpl::output('default_count', count($breach_list));
        Tpl::showPage("client.report");
    }

    public function gradeOp()
    {
        $r = new ormReader();
        $sql = "select * from member_grade";
        $rows = $r->getRows($sql);
        Tpl::output('list', $rows);
        Tpl::showPage("grade");
    }

    public function addGradeOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $m_member_grade = M('member_grade');
            unset($p['form_submit']);
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $member_grade = $m_member_grade->find(array('uid' => $p['grade_id']));
            if ($member_grade) {
                $rt = $m_member_grade->updateGrade($p);
                if ($rt->STS) {
                    showMessage($rt->MSG, getUrl('client', 'grade', array(), false, BACK_OFFICE_SITE_URL));
                } else {
                    showMessage($rt->MSG, getUrl('client', 'grade', $p, false, BACK_OFFICE_SITE_URL));
                }
            } else {
                $rt = $m_member_grade->insertGrade($p);
                if ($rt->STS) {
                    showMessage($rt->MSG, getUrl('client', 'grade', array(), false, BACK_OFFICE_SITE_URL));
                } else {
                    showMessage($rt->MSG, getUrl('client', 'grade', $p, false, BACK_OFFICE_SITE_URL));
                }
            }
        } else {
            $m_member_grade = M('member_grade');
            $member_grade = $m_member_grade->find(array('uid' => $p['uid']));
            Tpl::output('member_grade', $member_grade);
            Tpl::showPage("grade.add");
        }

    }

    public function clientSavingsBalanceFlowOp()
    {
        $uid = intval($_GET['uid']);
        $client_info = memberClass::getMemberBaseInfo($uid);
        if (!$client_info) {
            showMessage('Invalid id.');
        }
        Tpl::output('client_info', $client_info);
        $params['member_id'] = $uid;
        $params['currency'] = $_GET['currency'];
        $data = member_savingsClass::getMemberBillList($params);
        Tpl::output('data', $data->DATA);
        Tpl::showPage('client.saving.flow');
    }

}
