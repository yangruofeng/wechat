<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/5
 * Time: 10:48
 */
class serviceControl extends counter_baseControl
{

    public function __construct()
    {
        parent::__construct();
        Tpl::setDir("service");
        Language::read('service');
        Tpl::setLayout('home_layout');
        $this->outputSubMenu('service');

    }

//   requestLoan页面展示
    public function loanConsultOp()
    {
        Tpl::showPage("loan.consult");
    }

//   currencyExchange页面展示
    public function currencyExchangeOp()
    {
        Tpl::showPage("coming.soon");
    }

//   查询贷款申请列表，分页展示
    public function getLoanConsultListOp($p)
    {
        $branch_id = $this->user_info['branch_id'];
        $r = new ormReader();
        $p['creator_name'] = $this->user_name;
        $request_source= loanApplySourceEnum::CLIENT;
        $sql = "SELECT * FROM loan_consult WHERE branch_id=".$branch_id." AND request_source=".qstr($request_source);
        if (trim($p['search_text'])) {
            $sql .= " AND applicant_name like '%" . qstr2(trim($p['search_text'])) . "%' OR contact_phone like '%" . qstr2(trim($p['search_text'])) . "%'";
        }
        $sql .= " ORDER BY uid DESC";
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
            "current_user" => $this->user_id
        );
    }

//    添加贷款申请
    public function addLoanConsultOp()
    {
        Tpl::output('show_menu','loanConsult');

        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $m_loan_consult = M('loan_consult');
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $p['operator_id']=$this->user_id;
            $p['operator_name']=$this->user_name;
            $p['operator_remark']='';
            $p['request_source'] = loanApplySourceEnum::CLIENT;
            $p['branch_id'] = $this->user_info['branch_id'];
            $p['state'] = loanConsultStateEnum::ALLOT_BRANCH;
            $rt = $m_loan_consult->addConsult($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('service', 'loanConsult', array(), false, ENTRY_COUNTER_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('service', 'addRequestLoan', $p, false, ENTRY_COUNTER_SITE_URL));
            }
        } else {
            $m_core_definition = M('core_definition');
            $define_arr = $m_core_definition->getDefineByCategory(array('mortgage_type'));
            Tpl::output('mortgage_type', $define_arr['mortgage_type']);

            $apply_source = (new loanConsultSourceEnum())->Dictionary();
            Tpl::output('request_source', $apply_source);

            $currency_list = (new currencyEnum())->Dictionary();
            Tpl::output('currency_list', $currency_list);

            Tpl::showPage("add.loan.consult");
        }

    }

    public function consultDetailOp()
    {
        Tpl::output('show_menu','loanConsult');
        $uid = $_GET['uid'];
        $m_loan_consult = M('loan_consult');
        $list = $m_loan_consult->find(array('uid'=>$uid));
        Tpl::output('consult',$list);
        Tpl::showPage("loan.detail.consult");
    }

//    返回区域信息
    public function getAreaListOp($p)
    {
        $pid = intval($p['uid']);
        $m_core_tree = M('core_tree');
        $list = $m_core_tree->getChildByPid($pid, 'region');
        return array('list' => $list);
    }

//  删除新增申请
    public function deleteLoanConsultOp(){
        $uid = $_GET["uid"];
        $m_loan_apply = M("loan_consult");
        $r = $m_loan_apply->delete(array("uid"=>$uid));
        if($r->STS){
            showMessage("Delete Success");
        }else{
            showMessage("Delete failure");
        }

    }

}