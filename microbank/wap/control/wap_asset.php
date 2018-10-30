<?php

class wap_assetControl
{
    public function __construct()
    {
        Language::read('label');
        Tpl::output('html_title', 'Asset Detail');
        Tpl::output('header_title', 'Asset Detail');
        Tpl::setLayout('empty_layout');
        Tpl::setDir('wap_asset');
    }

    public function showAssetDetailOp()
    {
        $uid = intval($_GET['uid']);
        $m_member_assets = M('member_assets');
        $asset_info = $m_member_assets->find(array('uid' => $uid));
        Tpl::output('asset_info', $asset_info);

        $m_member_assets_owner = M('member_assets_owner');
        $asset_owner = $m_member_assets_owner->select(array('member_asset_id' => $uid));
        Tpl::output('asset_owner', $asset_owner);

        $m_member_verify_cert_image = M('member_verify_cert_image');
        $asset_image = $m_member_verify_cert_image->find(array('cert_id' => $asset_info['cert_id']));
        Tpl::output('asset_image', $asset_image);
        Tpl::showPage('asset.info');
    }

    public function evaluateInfoOp()
    {
        Tpl::output('header_title', 'Evaluate Info');
        $uid = intval($_GET['uid']);
        $m_member_assets_evaluate = M('member_assets_evaluate');
        $asset_evaluate = $m_member_assets_evaluate->orderBy('uid DESC')->find(array('member_assets_id' => $uid, 'evaluator_type' => 1));
        Tpl::output('asset_evaluate', $asset_evaluate);
        Tpl::showPage('asset.evaluate.info');
    }

    public function rentalInfoOp()
    {
        Tpl::output('header_title', 'Rental Info');
        $uid = intval($_GET['uid']);
        $m_member_assets_rental = M('member_assets_rental');
        $assets_rental = $m_member_assets_rental->orderBy('uid DESC')->find(array('asset_id' => $uid));
        Tpl::output('assets_rental', $assets_rental);
        Tpl::showPage('asset.rental.info');
    }

    public function storageFLowOp()
    {
        Tpl::output('header_title', 'Storage FLow');
        $uid = intval($_GET['uid']);
        $storage_list = member_assetsClass::getAssetStorageFlow($uid);
        Tpl::output("storage_list", $storage_list);

        $r = new ormReader();
        $sql = "SELECT ma.*,cm.display_name,cm.login_code FROM member_assets ma
                LEFT JOIN client_member cm ON ma.member_id = cm.uid
                WHERE ma.uid = " . $uid;
        $asset_info = $r->getRow($sql);
        Tpl::output("asset_info", $asset_info);

        Tpl::showPage('asset.storage.flow');
    }

    public function loanContractOp()
    {
        Tpl::output('header_title', 'Loan Contract');
        $uid = intval($_GET['uid']);
        $loan_ret = member_assetsClass::getAssetRelativeContract($uid);
        $principal_outstanding = $loan_ret['principal_outstanding'];
        $loan_list = $loan_ret['contract_list'];
        Tpl::output("loan_list", $loan_list);
        Tpl::output("principal_outstanding", $principal_outstanding);
        Tpl::showPage('asset.loan.list');
    }

    public function moreImageOp()
    {
        Tpl::output('header_title', 'More Images');
        $uid = intval($_GET['uid']);
        $m_member_assets = M('member_assets');
        $asset_info = $m_member_assets->find(array('uid' => $uid));
        $m_member_verify_cert_image = M('member_verify_cert_image');
        $image_list = $m_member_verify_cert_image->select(array('cert_id' => $asset_info['cert_id']));
        Tpl::output("image_list", resetArrayKey($image_list, 'image_key'));

        $image_structure = global_settingClass::getCertImageStructure();
        $stt = $image_structure[$asset_info['asset_type']];
        Tpl::output("image_structure", $stt);

        Tpl::showPage('asset.more.images');
    }

}
