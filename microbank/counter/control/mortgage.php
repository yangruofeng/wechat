<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/12
 * Time: 13:55
 */
class mortgageControl extends counter_baseControl
{

    public function __construct()
    {
        parent::__construct();
        Tpl::setDir("mortgage");
        Language::read('mortgage,certification,member');
        Tpl::setLayout('home_layout');
        $this->outputSubMenu('mortgage');

    }

    /***********************************AddBy Tim********************************************/
    public function myStoragePageOp()
    {
        $r = new ormReader();
        $sql = "SELECT b.`asset_type`,COUNT(a.uid) cnt FROM member_assets_storage a"
            . " LEFT JOIN member_assets b"
            . " ON a.`member_asset_id`=b.`uid`"
            . " WHERE a.`is_history`=0 and a.is_pending=0 AND a.`to_operator_id`='" . $this->user_id . "' AND a.`flow_type`<'" . assetStorageFlowType::WITHDRAW_BY_CLIENT . "' group by b.asset_type";


        $mortgage_list = $r->getRows($sql);
        $mortgage = resetArrayKey($mortgage_list, 'asset_type');

        $asset_type = (new member_assetsClass())->getAssetType();
        $asset = array();
        foreach ($asset_type as $type) {
            $asset[$type] = intval($mortgage[$type]['cnt']);
        }

        Tpl::output('asset', $asset);
        Tpl::showPage("my.storage.page");
    }

    public function getMyStorageListOp($p)
    {
        $branch_id = $this->user_info['branch_id'];
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $search_by = $p['search_by'];
        $phone_country = $p['country_code'];
        $phone = trim($p['phone_number']);
        if ($phone) {
            if ($search_by == "1") {
                $ret = memberClass::searchMember(array(
                    "type" => '2',
                    "country_code" => $phone_country,
                    "phone_number" => $phone
                ));

            } elseif ($search_by == "2") {
                $ret = memberClass::searchMember(array(
                    "type" => '1',
                    "guid" => $phone
                ));

            } elseif ($search_by == "3") {
                $ret = memberClass::searchMember(array(
                    "type" => '3',
                    "login_code" => $phone
                ));
            } elseif ($search_by == "4") {
                $ret = memberClass::searchMember(array(
                    "type" => '4',
                    "display_name" => $phone
                ));
            }
            $member_id = $ret['uid'] ?: 0;
        }

        $r = new ormReader();

        $sql = "SELECT a.*,b.`asset_type`,b.`asset_name`,b.`asset_sn`,c.`login_code` member_name,c.`display_name`,c.`phone_id` FROM member_assets_storage a "
            . " LEFT JOIN member_assets b ON a.`member_asset_id`=b.`uid`"
            . " LEFT JOIN client_member c ON b.`member_id`=c.`uid`"
            . " WHERE a.`is_history`=0 and a.is_pending=0 AND a.`to_operator_id`='" . $this->user_id . "' AND a.`flow_type`<20";

        if (isset($member_id)) {
            $sql .= " AND c.uid='" . $member_id . "'";
        }

        $sql .= " order by c.uid,b.asset_type";


        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $data = $data->toArray();
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        /*
        if ($rows) {
            foreach ($rows as $key => $row) {
                $sql_2 = "SELECT count(uid) img_count FROM member_asset_mortgage_image WHERE asset_mortgage_id = " . $row['mortgage_id'];
                $img_count = $r->getRow($sql_2);
                $row['img_amount'] = $img_count['img_count'];
                $rows[$key] = $row;
            }
        }*/
        $data['sts'] = true;
        return $data;
    }

    public function showMyStorageAssetDetailPageOp()
    {
        $user_position = $this->user_position;
        Tpl::output('user_position',$user_position);
        $asset_id = intval($_REQUEST['asset_id']);
        $ret = credit_officerClass::getMemberAssetDetailAndResearchInfo($asset_id, $this->user_id);
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

        //保险柜
        $last_request = end($storage_list);
        $safe_id = $last_request['safe_id'];
        Tpl::output('safe_id',$safe_id);
        Tpl::output('safe_branch_id',$last_request['safe_branch_id']);
        $m_site_branch_safe = M('site_branch_safe');
        $safe_box = $m_site_branch_safe->find(array('uid'=>$safe_id));
        $safe_code = $safe_box['safe_code'];
        Tpl::output('safe_code',$safe_code);


        //获取本行的teller_id
        $receiver_list = counter_baseClass::getBranchUserListOfTeller($this->branch_id);
        Tpl::output("receiver_list", $receiver_list);

        //获取未接受列表，可以删除
        $m_transfer = new member_assets_storageModel();
        $request_transfer = $m_transfer->select(array("from_operator_id" => $this->user_id, "is_pending" => 1, "flow_type" => assetStorageFlowType::TRANSFER, "member_asset_id" => $asset_id));
        Tpl::output("pending_receive", $request_transfer);

        Tpl::output("show_menu", "myStoragePage");

        Tpl::showPage("asset.item.detail");

    }

    //提交转移申请
    public function submitTransferToTellerOp()
    {
        $asset_id = $_POST['asset_id'];
        $receiver_id = $_POST['receiver_id'];
        $safe_id = $_POST['safe_id'];
        $safe_branch_id = $_POST['safe_branch_id'];
        $url = getUrl("mortgage", "showMyStorageAssetDetailPage", array("asset_id" => $asset_id), false, ENTRY_COUNTER_SITE_URL);
        if (!$asset_id || !$receiver_id) {
            showMessage("Invalid Parameter", $url);
        }
        $ret = member_assetsClass::insertStorageFlowAsTransfer($asset_id, $receiver_id, $this->user_id,$safe_id,$safe_branch_id);
        if (!$ret->STS) {
            showMessage($ret->MSG, $url);
        } else {
            showMessage("Save Success", $url);
        }

    }

    //转移待确认
    public function pendingReceiveFromTransferOp()
    {
        $user_position = $this->user_position;
        Tpl::output('user_position',$user_position);
        $branch_id = $this->branch_id;
        $sql = "SELECT a.*,b.`asset_type`,b.`asset_name`,b.`asset_sn`,c.`login_code` member_name,c.`display_name`,c.`phone_id` FROM member_assets_storage a "
            . " LEFT JOIN member_assets b ON a.`member_asset_id`=b.`uid`"
            . " LEFT JOIN client_member c ON b.`member_id`=c.`uid`"
            . " WHERE a.`is_history`=0 and a.is_pending=1 AND a.`to_operator_id`='" . $this->user_id . "' AND a.`flow_type`='" . assetStorageFlowType::TRANSFER . "'";
        $sql .= " order by a.create_time desc";
        $r = new ormReader();
        $rows = $r->getRows($sql);
        Tpl::output("list", $rows);
        $sql_2 = "SELECT * FROM site_branch_safe WHERE branch_id=".$branch_id;
        $safe_box = $r->getRows($sql_2);
        Tpl::output("safe_box", $safe_box);

        Tpl::showPage("pending.receive.transfer.list");

    }

    public function showPendingReceiveDetailPageOp()
    {
        //以后看有没有必要显示接受页面
        $uid = intval($_POST['uid']);
        $safe_id = intval($_POST['safe_id']);
        $remark = trim($_POST['remark']);
        $url = getUrl("mortgage", "pendingReceiveFromTransfer", array(), false, ENTRY_COUNTER_SITE_URL);
        $ret = member_assetsClass::receiveAssetFromTransferByCT($uid,$safe_id,$remark);
        if (!$ret->STS) {
            showMessage($ret->MSG, $url);
        } else {
            showMessage("Save Success", $url);
        }

    }

    public function showPendingReceiveDetailPageForTellerOp(){
        //以后看有没有必要显示接受页面
        $uid = intval($_GET['uid']);
        $url = getUrl("mortgage", "pendingReceiveFromTransfer", array(), false, ENTRY_COUNTER_SITE_URL);
        $ret = member_assetsClass::receiveAssetFromTransfer($uid);
        if (!$ret->STS) {
            showMessage($ret->MSG, $url);
        } else {
            showMessage("Save Success", $url);
        }

    }

    //已抵押未收抵押物
    public function pendingReceiveFromClientOp()
    {
        $sql = "SELECT a.*,b.`asset_type`,b.`asset_name`,b.`asset_sn`,c.`login_code` member_name,c.`display_name`,c.`phone_id`,d.create_time authorize_time"
            . " FROM member_asset_mortgage a "
            . " LEFT JOIN member_assets b ON a.`member_asset_id`=b.`uid`"
            . " LEFT JOIN client_member c ON b.`member_id`=c.`uid`"
            . " LEFT JOIN member_authorized_contract d ON a.`contract_no`=d.`contract_no`"
            . " WHERE a.`is_received`=0 and a.mortgage_type=1 AND d.branch_id='" . $this->branch_id . "'";
        $sql .= " order by d.uid desc";
        $r = new ormReader();
        $rows = $r->getRows($sql);
        Tpl::output("list", $rows);
        Tpl::showPage("pending.receive.client.list");
    }

    //已批准的取出请求
    public function pendingWithdrawByRequestOp()
    {
        $sql = "SELECT a.*,b.`asset_type`,b.`asset_name`,b.`asset_sn`,c.`login_code` member_name,c.`display_name`,c.`phone_id` FROM member_asset_request_withdraw a "
            . " LEFT JOIN member_assets b ON a.`member_asset_id`=b.`uid`"
            . " LEFT JOIN client_member c ON b.`member_id`=c.`uid`"
            . " WHERE a.`state`='" . assetRequestWithdrawStateEnum::PENDING_WITHDRAW . "'";
        $sql .= " order by a.create_time desc";
        $r = new ormReader();
        $rows = $r->getRows($sql);
        Tpl::output("list", $rows);
        Tpl::showPage("pending.withdraw.request.list");
    }

    //删除还没接受的
    public function submitDeletePendingReceiveOfTransferOp()
    {
        $request_id = $_REQUEST['request_id'];
        $asset_id = $_REQUEST['asset_id'];
        $url = getUrl("mortgage", "showMyStorageAssetDetailPage", array("asset_id" => $asset_id), false, ENTRY_COUNTER_SITE_URL);
        $ret = member_assetsClass::deleteStorageFlow($request_id);
        if (!$ret->STS) {
            showMessage($ret->MSG, $url);
        } else {
            showMessage("Save Success", $url);
        }
    }

    //取出抵押物
    public function showPendingWithdrawDetailPageOp()
    {
        $request_id = intval($_REQUEST['request_id']);
        $m = M("member_asset_request_withdraw");
        $request_row = $m->find(array("uid" => $request_id));
        Tpl::output("request_item", $request_row);

        $asset_id = $request_row['member_asset_id'];
        $ret = credit_officerClass::getMemberAssetDetailAndResearchInfo($asset_id, $this->user_id);
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
        //取出抵押时的照片
        $m_mortgage = new member_asset_mortgageModel();
        $row_mortgage = $m_mortgage->orderBy("uid desc")->find(array("member_asset_id" => $asset_id));
        if ($row_mortgage) {
            $m_mortgage_img = new member_asset_mortgage_imageModel();
            $image_list = $m_mortgage_img->select(array("asset_mortgage_id" => $row_mortgage['uid']));
            $row_mortgage['image_list'] = $image_list;
        }
        Tpl::output("mortgage", $row_mortgage);
        //判断该资产是否为当前用户所持有，否则不可取出
        $last_holder = end($storage_list);
        $is_last_holder = false;
        if ($last_holder) {
            if ($last_holder['to_operator_id'] == $this->user_id && !$last_holder['is_pending']) {
                $is_last_holder = true;
            }
        }
        Tpl::output("is_last_holder", $is_last_holder);


        Tpl::output("show_menu", "pendingWithdrawByRequest");

        Tpl::showPage("asset.item.withdraw");
    }

    function submitWithdrawByClientOp()
    {
        $p = $_POST;
        $is_agent = $p['is_agent'];
        $request_id = $p['request_id'];
        $url = getUrl("mortgage", "showPendingWithdrawDetailPage", array("request_id" => $request_id), false, ENTRY_COUNTER_SITE_URL);

        $m_request = M("member_asset_request_withdraw");
        $request_row = $m_request->getRow(array("uid" => $request_id));
        if (!$request_row) {
            showMessage("Invalid Parameter:No Request Found", $url);
        }
        $asset_id = $request_row->member_asset_id;
        $member_image = $p['member_image'];
        $contract_images = explode(',', $p['contract_images']);
        $args = array(
            'asset_id' => $asset_id,
            'user_id' => $this->user_id,
            'member_image' => $member_image,
            'contract_images' => $contract_images,
            'is_agent' => $is_agent?1:0,
            'agent_name' => $p['agent_name'],
            'agent_id_sn' => $p['agent_id_sn'],
            'agent_id1' => $p['agent_id1'],
            'agent_id2' => $p['agent_id2'],
            'authorization_cert' => $p['authorization_cert'],
            'mortgage_cert' => $p['mortgage_cert'],
        );

        $conn = ormYo::Conn();
        $conn->startTransaction();
        $ret = member_credit_grantClass::signWithdrawMortgagedContract($args);
        if (!$ret->STS) {
            $conn->rollback();
            showMessage($ret->MSG, $url);
        } else {
            $request_row->state = assetRequestWithdrawStateEnum::DONE;
            $request_row->update_time = Now();
            $request_row->update_operator_id = $this->user_id;
            $request_row->update_operator_name = $this->user_name;
            $ret = $request_row->update();
            if (!$ret->STS) {
                $conn->rollback();
                showMessage($ret->MSG, $url);
            }
        }
        $conn->submitTransaction();
        showMessage("Save Success!", $url);
    }

    public function branchSafeOp()
    {
        $m_site_branch_safe = M('site_branch_safe');
        $safe_list = $m_site_branch_safe->select(array('branch_id' => $this->branch_id));
        Tpl::output('safe_list', $safe_list);
        Tpl::showPage("branch.safe");
    }

    public function addBranchSafeOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $m_branch_safe = new site_branch_safeModel();
            $p['branch_id'] = $this->branch_id;
            $p['operator_id'] = $this->user_id;
            $p['operator_name'] = $this->user_name;
            $rt = $m_branch_safe->addSafe($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('mortgage', 'branchSafe', array(), false, ENTRY_COUNTER_SITE_URL));
            } else {
                showMessage($rt->MSG);
            }
        } else {
            Tpl::output("show_menu", "branchSafe");
            Tpl::showPage('branch.safe.add');
        }
    }

    public function editBranchSafeOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_branch_safe = new site_branch_safeModel();
        if ($p['form_submit'] == 'ok') {
            $p['operator_id'] = $this->user_id;
            $p['operator_name'] = $this->user_name;
            $rt = $m_branch_safe->editSafe($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('mortgage', 'branchSafe', array(), false, ENTRY_COUNTER_SITE_URL));
            } else {
                showMessage($rt->MSG);
            }
        } else {
            $uid = intval($p['uid']);
            $safe = $m_branch_safe->find(array('uid' => $uid));
            Tpl::output("safe", $safe);
            Tpl::output("show_menu", "branchSafe");
            Tpl::showPage('branch.safe.edit');
        }
    }
}