<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/18
 * Time: 9:23
 */
class reportControl extends wap_operator_baseControl
{
    public function __construct()
    {
        parent::__construct();
        //Language::read('act,label,tip');
        Tpl::setLayout('weui_layout');
        Tpl::setDir('report');
    }

    /**
     * co提交资料界面
     */
    public function toBMIndexOp(){
        Tpl::output('html_title', "To Branch Manager");
        Tpl::output('header_title',"To Branch Manager");
        Tpl::output("back_url",getWapOperatorUrl("home","clientReport",array("id"=>$_GET['member_id'])));
        $member_id=$_GET['member_id'];

        $m_task=new task_co_bmModel();
        $task=$m_task->find(array("member_id"=>$member_id,"co_id"=>$this->user_id,"state"=>array(">",0)));
        Tpl::output("task",$task);

        $msg_list=$m_task->getAllMsgTaskOfMemberIdAndCoId($member_id,$this->user_id);
        Tpl::output("member_id",$member_id);
        Tpl::output("msg_list",$msg_list);
        Tpl::showPage("to.bm");
    }

    /**
     * co提交资料给BM
     */
    public function submitToBMOp($p){
        if(!$p['msg']){
            showMessage("Required To Input Comment","",10);
        }
        $m_task=new task_co_bmModel();
        $m_task->cancelOldTask($p['member_id'],$this->user_id);

        $user=new objectUserClass($this->user_id);
        $row=$m_task->newRow();
        $row->member_id=$p['member_id'];
        $row->co_id=$this->user_id;
        $row->co_name=$user->user_name;
        $row->submit_time=Now();
        $row->update_time=Now();
        $row->submit_comment=$p['msg'];
        $row->state=commonApproveStateEnum::APPROVING;
        $ret=$row->insert();



        if($ret->STS){
            $task_id=$row->uid;
            $ret_task=taskControllerClass::handleNewTask($task_id,userTaskTypeEnum::CO_SUBMIT_BM,$user->branch_id,objGuidTypeEnum::SITE_BRANCH,$this->user_id,objGuidTypeEnum::UM_USER,$p['msg']);
            return new result(true,"Submit Success");
        }else{
            return $ret;
        }

    }
    public function interestListOp(){
        $list = loan_categoryClass::getMemberCreditCategoryList($_GET['member_id']);
        Tpl::output('credit_category', $list);
        Tpl::output('html_title', 'Interest List');
        Tpl::output('header_title', 'Interest List');
        Tpl::showPage('client.interest');
    }
}