<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/17/2018
 * Time: 11:26 AM
 */
class data_center_certificationControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('operator,certification');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Dev");
        Tpl::setDir("data_center_certification");
    }

    /**
     * certification页面
     */
    public function indexOp()
    {
        $certification_type = enum_langClass::getCertificationTypeEnumLang();
        unset($certification_type[certificationTypeEnum::GUARANTEE_RELATIONSHIP]);
        Tpl::output("certification_type", $certification_type);
        Tpl::showPage("index");
    }

    /**
     * 获取certification数据
     * @param $p
     * @return array
     */
    public function getCertificationListOp($p)
    {
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = trim($p['search_text']);
        $cert_type = intval($p['cert_type']);
        $verify_state = intval($p['verify_state']);

        $filters = array(
            'search_text' => $search_text,
            'cert_type' => $cert_type,
            'verify_state' => $verify_state,
        );
        $data = certificationDataClass::getCertificationList($pageNumber, $pageSize, $filters);
        return $data;
    }

    public function showCertificationDetailOp()
    {
        $uid = intval($_GET['uid']);
        $data = certificationDataClass::getCertificationAssetDetail($uid);
        $client_info = M('client_member')->find(array('uid' => $data['member_id']));
        Tpl::output('client_info', $client_info);
        Tpl::output("detail", $data);

        $asset_id = $data['asset_id'];
        if($asset_id){
            //asset info
            $ret = credit_officerClass::getMemberAssetDetailAndResearchInfo($asset_id, $this->user_id, true);
            $asset = $ret->DATA;
            Tpl::output("asset", $asset);
            //asset_evaluate
            $r = new ormReader();
            $sql = "SELECT * FROM member_assets_evaluate WHERE member_assets_id = $asset_id AND evaluator_type = 1 ";
            $asset_evaluate = $r->getRow($sql);
            Tpl::output("asset_evaluate", $asset_evaluate);

            //asset_rental
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

            //loan_list
            $loan_ret = member_assetsClass::getAssetRelativeContract($asset_id);
            $loan_list = $loan_ret['contract_list'];
            Tpl::output("loan_list", $loan_list);
            //storage_list
            $storage_list = member_assetsClass::getAssetStorageFlow($asset_id);
            Tpl::output("storage_list", $storage_list);
        }





        Tpl::showPage("certification.detail");
    }
}