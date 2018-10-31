<?php

class monitorControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('enum');
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Monitor");
        Tpl::setDir("monitor");
    }

    public function monitorOp()
    {
        //获取monitor items
        $info = monitorClass::getDashboardInfo();
        Tpl::output("info", $info);
        Tpl::showPage("monitor");
    }

    public function getDashboardLoanDataOp()
    {
        $data = monitorClass::getDashboardLoan();
        return $data;
    }

    public function getDashboardSavingsDataOp()
    {
        $data = monitorClass::getDashboardSavings();
        return $data;
    }

    public function getDashboardBusinessActivityOp()
    {
        $data = monitorClass::getDashboardBusinessActivity();
        return $data;
    }

    public function monitorOldOp()
    {

        //获取monitor items
        $mitems = monitorClass::getMonitorItems();
        $group = array();
        foreach ($mitems as $item) {
            if (!$group[$item['group']]) {
                $group[$item['group']] = array("items" => array(), "title" => $item['group']);
            }
            $group[$item['group']]['items'][] = $item;
        }


        Tpl::output("monitor_items", $group);

        Tpl::showPage("monitor");
    }
    public function showOverdueLoanPageOp(){
        if($this->user_position==userPositionEnum::BRANCH_MANAGER){
            $list=(new monitorOverdueLoanTaskClass())->getTaskPendingList($this->user_info['branch_id'],objGuidTypeEnum::SITE_BRANCH);
        }elseif($this->user_position==userPositionEnum::OPERATOR){
            $list=(new monitorOverdueLoanTaskClass())->getTaskPendingList($this->user_id,objGuidTypeEnum::UM_USER);
        }else{
            $list=(new monitorOverdueLoanTaskClass())->getTaskPendingList();
        }
        Tpl::output("loan_list",$list);
        Tpl::showPage("monitor.overdue.loan");

    }

    public function loanContractPageOp($p){
        $p = $p?:$_REQUEST;
        $condition = array(
            "date_start" => date('Y-m-d'),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::output("uid", $p['uid']);
        Tpl::showPage("loan.contract");
    }

    public function ajaxLoanContractListOp($p){
        if($p['uid']){
            $filter = array(
                'uid' => $p['uid'],
            );
        }else{
            $filter = array(
                'client_text' => $p['client_text'],
                'contract_sn' => $p['contract_sn'],
                'state' => $p['state'],
                'date_start' => $p['date_start'],
                'date_end' => $p['date_end'],
            );
        }
        return monitorClass::getLoanContract($filter);
    }

    public function loanContractDetailPageOp(){
        $uid = intval($_GET['uid']);
        //合同信息
        $data = loan_contractClass::getLoanContractDetailInfo($uid);
        $data = $data->DATA;
        $r = new ormReader();
        //
        $sql = "SELECT * FROM loan_contract_billpay_code  WHERE contract_id = " . $uid;
        $list = $r->getRows($sql);
        $data['billpay_history'] = $list;
        //获取合同已还历史
        $m_loan_installment_scheme = new loan_installment_schemeModel();
        $list = $m_loan_installment_scheme->select(array('contract_id' => $uid, 'state' => 100));
        $data['repayment_history'] = $list;
        Tpl::output("data", $data);
        Tpl::showPage("loan.contract.detail");
    }

}
