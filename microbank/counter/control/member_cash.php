<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/29
 * Time: 下午4:08
 */
class member_cashControl extends counter_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Language::read('member');
        Tpl::setDir('member_cash');
        Tpl::setLayout('home_layout');
        Tpl::output("sub_menu",$this->getMemberBusinessMenu());
    }

    public function depositIndexOp(){
        $member_id=$_GET['member_id'];
        $client_info=counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info",$client_info);
        Tpl::output("member_id",$member_id);

        $limit_branch = userClass::getBranchLimit($this->branch_id, bizCodeEnum::MEMBER_DEPOSIT_BY_CASH);
        Tpl::output('branch_limit', $limit_branch);

        $member_limit = memberClass::getMemberLimit($client_info['member_grade'], bizCodeEnum::MEMBER_DEPOSIT_BY_CASH);
        Tpl::output("member_limit",$member_limit);

        Tpl::showPage("deposit.index");
    }


/**
 * 创建存款合同,data是biz_id
 */
    public function createClientDepositOp($p){
        //todo:要对$p做边界检查，currency & amount
        $cashier_id = $this->user_id;
        $p['cashier_id'] = $cashier_id;
        $rt = counter_member_cashClass::createMemberCashDeposit($p);
        if ($rt->STS) {
            return new result(true,$rt->MSG,$rt->DATA);
        }else{
            return new result(false,$rt->MSG);
        }
    }

    /**
     * deposit列表
    */
    public function getDepositListOp($p){
        $cashier_id = $this->user_id;
        $r = new ormReader();
        $sql = "SELECT bmd.*,cm.login_code FROM biz_member_deposit bmd INNER JOIN client_member cm ON bmd.member_id = cm.uid WHERE bmd.state = 100 AND bmd.cashier_id=".$cashier_id;
        $sql .= " ORDER BY bmd.uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
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

    /**
     * 存款验证Biz/cashier/time
     */
    public function depositCheckOp(){
        $biz_id=$_GET['biz_id'];//这里要检查biz_id的有效性
        if(!$biz_id){
            show_exception("Invalid Parameter:Biz-ID is incorrect!");
        }
        $arr_biz=counter_member_cashClass::getMemberDepositBizByID($biz_id);
        if(!$arr_biz || !count($arr_biz)){
            show_exception("invalid business information");
        }

        // 检查是否需要CT 验证
        $bizClass = new bizMemberDepositByCashClass(bizSceneEnum::COUNTER);
        $ct_check = $bizClass->isNeedCTApprove($biz_id);

        Tpl::output('is_ct_check',$ct_check);


        //cashier必须是当前用户
        if($arr_biz['cashier_id']!=$this->user_id){
            show_exception("invalid business information: are you a hacker?");
        }
        //超时5分钟不认可
        $time_diff=time()-strtotime($arr_biz['create_time']);
        if($time_diff>=5*60){
            show_exception("The operation timed out!");
        }
        Tpl::output("biz",$arr_biz);
        $member_info=counter_baseClass::getMemberInfoByID($arr_biz['member_id']);
        $member_scene_image = objectMemberClass::getNewestSceneImage($member_info['uid']);
        Tpl::output('member_scene_image',$member_scene_image);
        Tpl::output("member_id",$arr_biz['member_id']);
        Tpl::output('show_menu', 'depositIndex');
        Tpl::output("client_info",$member_info);

        Tpl::showPage('deposit.check');
    }

    /**
     * 存款验证密码及提交
    */
    public function checkClientDepositOp($p)
    {
        $rt = counter_member_cashClass::memberCashDeposit($p);
        if ($rt->STS) {
            return new result(true,$rt->MSG);
        } else {
            return new result(false, $rt->MSG);
        }
    }

    public function withdrawIndexOp(){
        $member_id=$_GET['member_id'];
        $client_info=counter_baseClass::getMemberInfoByID($member_id);
        Tpl::output("client_info",$client_info);
        Tpl::output("member_id",$member_id);

        $limit_branch = userClass::getBranchLimit($this->branch_id, bizCodeEnum::MEMBER_WITHDRAW_TO_CASH);
        Tpl::output('branch_limit', $limit_branch);

        $member_limit = memberClass::getMemberLimit($client_info['member_grade'], bizCodeEnum::MEMBER_WITHDRAW_TO_CASH);
        Tpl::output("member_limit",$member_limit);

        Tpl::showPage("withdraw.index");
    }


    /**
     * 创建取款合同
    */
    public function createClientWithdrawalOp($p){
        $cashier_id = $this->user_id;
        $p['cashier_id'] = $cashier_id;
        $rt = counter_member_cashClass::createMemberCashWithdrawal($p);
        if ($rt->STS) {
            return new result(true,$rt->MSG,$rt->DATA);
        }else{
            return new result(false,$rt->MSG);
        }
    }


    /**
     * withdrawal列表
     */
    public function getWithdrawalListOp($p){
        $cashier_id = $this->user_id;
        $r = new ormReader();
        $sql = "SELECT bmw.*,cm.login_code FROM biz_member_withdraw bmw INNER JOIN client_member cm ON bmw.member_id = cm.uid WHERE bmw.state = 100 AND bmw.cashier_id=".$cashier_id;
        $sql .= " ORDER BY bmw.uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
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

    /**
     * 取款验证Biz/cashier/time
    */
    public function withdrawalCheckOp(){
        $biz_id=$_GET['biz_id'];//这里要检查biz_id的有效性
        if(!$biz_id){
            show_exception("Invalid Parameter:Biz-ID is incorrect!");
        }
        $arr_biz=counter_member_cashClass::getMemberWithdrawBizByID($biz_id);
        if(!$arr_biz || !count($arr_biz)){
            show_exception("invalid business information");
        }

        // 检查是否需要CT 验证
        $bizClass = new bizMemberWithdrawToCashClass(bizSceneEnum::COUNTER);
        $ct_check = $bizClass->isNeedCTApprove($biz_id);
        Tpl::output('is_ct_check',$ct_check);


        //cashier必须是当前用户
        if($arr_biz['cashier_id']!=$this->user_id){
            show_exception("invalid business information: are you a hacker?");
        }
        //超时5分钟不认可
        $time_diff=time()-strtotime($arr_biz['create_time']);
        if($time_diff>=5*60){
            show_exception("The operation timed out!");
        }
        Tpl::output("biz",$arr_biz);
        $member_info=counter_baseClass::getMemberInfoByID($arr_biz['member_id']);
        $member_scene_image = objectMemberClass::getNewestSceneImage($member_info['uid']);
        Tpl::output('member_scene_image',$member_scene_image);

        Tpl::output("client_info",$member_info);
        Tpl::output("member_id",$arr_biz['member_id']);
        Tpl::output('show_menu', 'withdrawIndex');
        Tpl::showPage('withdrawal.check');

    }

    /**
     * 取款款验证密码及提交
     */
    public function checkClientWithdrawalOp($p)
    {
        $rt = counter_member_cashClass::memberCashWithdrawal($p);
        if ($rt->STS) {
            return new result(true,$rt->MSG);
        } else {
            return new result(false, $rt->MSG);
        }
    }

}



