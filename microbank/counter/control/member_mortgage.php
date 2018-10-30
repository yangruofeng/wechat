<?php


class member_mortgageControl extends counter_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('member');
        Language::read('mortgage,certification,member');
        Tpl::setDir('member_mortgage');
        Tpl::setLayout('home_layout');
        Tpl::output("sub_menu",$this->getMemberBusinessMenu());
    }

    public function clientMortgageIndexOp(){
        $member_id = $_GET['member_id'];
        Tpl::output('member_id',$member_id);
        $client_info = counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info", $client_info);
        Tpl::showPage('client.mortgage');
    }

    public function getClientStorageListOp($p){
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $member_id = intval($p['member_id']);

        $r = new ormReader();
        $sql = "SELECT a.*,b.`asset_type`,b.`asset_name`,b.`asset_sn`,c.`login_code` member_name,c.`display_name`,c.`phone_id` FROM member_assets_storage a "
            . " LEFT JOIN member_assets b ON a.`member_asset_id`=b.`uid`"
            . " LEFT JOIN client_member c ON b.`member_id`=c.`uid`"
            . " WHERE a.`is_history`=0 and a.is_pending=0 AND a.`flow_type`<20 AND c.uid=".$member_id;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $data = $data->toArray();
        $data['sts'] = true;
        return $data;
    }

    public function showMyStorageAssetDetailPageOp()
    {
        $asset_id = intval($_REQUEST['asset_id']);
        $ret = credit_officerClass::getMemberAssetDetailAndResearchInfo($asset_id, $this->user_id);
        $asset = $ret->DATA;
        Tpl::output("asset", $asset);
        $member_id = $asset['member_id'];
        Tpl::output('member_id',$member_id);
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

        Tpl::output("show_menu", "clientMortgageIndex");

        Tpl::showPage("asset.item.detail");

    }

}



