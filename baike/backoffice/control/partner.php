<?php

class partnerControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('common');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Partner");
        Tpl::setDir("partner");
    }

    /**
     * 银行
     */
    public function bankOp()
    {
        $class_partner = new partnerClass();
        $partner_list = $class_partner->getPartnerList();
        Tpl::output("bank_list", $partner_list);
        $currency_list = (new currencyEnum())->Dictionary();
        Tpl::output("currency_list", $currency_list);
        Tpl::showpage('partner.index');
    }

    /**
     * 经销商
     */
    public function dealerOp()
    {
        Tpl::showpage('dealer');
    }
    public function partnerDepositByHQOp($p){
        $partner_id = intval($p['partner_id']);
        $user_id = $this->user_id;
        $amount = round($p['amount'], 2);
        $password = trim($p['password']);
        $currency = trim($p['currency']);
        $remark = trim($p['remark']);
        if (!$amount || !$password || !$partner_id) {
            return new result(false, "Invalid Parameter");
        }
        $m_bank = M('partner');
        $bank = $m_bank->getRow($partner_id);
        if (!$bank) {
            return new result(false, 'Invalid Partner Id!');
        }

        $class_biz = new bizHeadquarterToPartnerClass(bizSceneEnum::BACK_OFFICE);// bizFactoryClass::getInstance(bizSceneEnum::BACK_OFFICE, bizCodeEnum::HEADQUARTER_TO_BANK);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $class_biz->execute($partner_id, $user_id, $password, $amount, $currency, $remark);
        if ($rt->STS) {
            $conn->submitTransaction();
            return new result(true, "Deposit Successful");
        } else {
            $conn->rollback();
            return new result(false, 'Deposit Failure--' . $rt->MSG);
        }
    }
    public function partnerWithdrawByHQOp($p){
        $partner_id = intval($p['partner_id']);
        $user_id = $this->user_id;
        $amount = round($p['amount'], 2);
        $password = trim($p['password']);
        $currency = trim($p['currency']);
        $remark = trim($p['remark']);
        if (!$amount || !$password || !$partner_id) {
            return new result(false, "Invalid Parameter");
        }
        $m_bank = M('partner');
        $bank = $m_bank->getRow($partner_id);
        if (!$bank) {
            return new result(false, 'Invalid Partner Id!');
        }


        $class_biz = new bizPartnerToHeadquarterClass(bizSceneEnum::BACK_OFFICE);// bizFactoryClass::getInstance(bizSceneEnum::BACK_OFFICE, bizCodeEnum::HEADQUARTER_TO_BANK);
        $conn = ormYo::Conn();
        $conn->startTransaction();
        $rt = $class_biz->execute($partner_id, $user_id, $password, $amount, $currency, $remark);
        if ($rt->STS) {
            $conn->submitTransaction();
            return new result(true, "Withdraw Successful");
        } else {
            $conn->rollback();
            return new result(false, 'Withdraw Failure--' . $rt->MSG);
        }

    }
    public function apiLogPageOp(){
        $partner_id = $_REQUEST['partner_id'];
        $m_partner = M('partner');
        $partner_info = $m_partner->getRow($partner_id);
        if (!$partner_info) {
            return new result(false, 'Invalid Partner Id!');
        }
        Tpl::output("partner_info", $partner_info);
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showpage('partner.api.log');
    }

    public function getApiLogOp($p){
        $p = $p ? : $_REQUEST;
        $partner_id = $p['partner_id'];
        $pageNumber = $p['pageNumber']?:50;
        $pageSize = $p['pageSize']?:1;
        $filters = array();
        $filters['start_date'] = $p['date_start'];
        $filters['end_date'] = $p['date_end'];
        return partnerClass::getPartnerApiLogData($partner_id, $pageNumber, $pageSize, $filters);
    }

}
