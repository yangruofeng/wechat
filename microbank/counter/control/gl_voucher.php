<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/3/2018
 * Time: 4:38 PM
 */

class gl_voucherControl extends counter_baseControl{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout('home_layout');
        Tpl::output("html_title", "Gl Account List");
        Tpl::setDir("gl");
        $this->outputSubMenu('gl_voucher');
    }

    public function voucherIndexOp(){
        Tpl::showPage("voucher.index");
    }
    public function submitNewVoucherOp(){
        $p=$_POST;
        $biz_amount=floatval($p['biz_amount']);
        $biz_currency=$p['biz_currency'];
        $biz_remark=$p['biz_remark'];
        if(!$biz_amount){
            showMessage("Required to Input Voucher Amount");
        }
        $dr_list=array();
        $cr_list=array();
        foreach($p['is_debit'] as $i=>$v){
            $item=array(
                "is_debit"=>$v,
                "gl_code"=>trim($p['gl_code'][$i]),
                "gl_subject"=>trim($p['gl_subject'][$i]),
                "gl_amount"=>floatval($p['gl_amount'][$i])
            );
            if(!$item['gl_code']){
                showMessage("No Gl-Code at Line ".$i);
            }
            if(!$item['gl_subject']){
                showMessage("No Subject at Line ".$i);
            }
            if(!$item['gl_amount']){
                showMessage("No Amount at Line ".$i);
            }
            if($v){
                $dr_list[]=$item;
            }else{
                $cr_list[]=$item;
            }
        }
        if(!$dr_list){
            showMessage("No Debit Record!");
        }
        if(!$cr_list){
            showMessage("No Credit Record");
        }
        //判断cr和dr的总额是否和biz_amount相等

        //判断cr和dr的gl_code是否合理，是否重复
        $dr_amount=0;
        foreach($dr_list as $i=>$item){
            $chk_ret=$this->checkGlCodeValidOp(array("code"=>$item['gl_code'],"currency"=>$biz_currency));
            $gl_acct=$chk_ret->DATA;
            if(!$chk_ret->STS){
                showMessage($chk_ret->MSG);
            }
            $dr_list[$i]['gl_type']=$gl_acct['gl_type'];
            $dr_list[$i]['gl_name']=$gl_acct['gl_name'];
            $dr_amount+=$item['gl_amount'];
        }
        $cr_amount=0;
        foreach($cr_list as $i=>$item){
            $chk_ret=$this->checkGlCodeValidOp(array("code"=>$item['gl_code'],"currency"=>$biz_currency));
            $gl_acct=$chk_ret->DATA;
            if(!$chk_ret->STS){
                showMessage($chk_ret->MSG);
            }
            $cr_list[$i]['gl_type']=$gl_acct['gl_type'];
            $cr_list[$i]['gl_name']=$gl_acct['gl_name'];
            $cr_amount+=$item['gl_amount'];
        }
        if($dr_amount!=$biz_amount){
            showMessage("Total Amount Of Debit-Record is Not Equals Voucher-Amount");
        }
        if($cr_amount!=$biz_amount){
            showMessage("Total Amount Of Credit-Record is Not Equals Voucher-Amount");
        }

        //检查过关，提交给biz
        $conn=ormYo::Conn();
        $conn->startTransaction();
        $biz=new bizManualVoucherClass(bizSceneEnum::BACK_OFFICE);

        $rt = $biz->execute(array(
            "biz_amount"=>$biz_amount,
            "biz_currency"=>$biz_currency,
            "biz_remark"=>$biz_remark,
            "biz_date"=>Now(),//要前端输入
            "branch_id"=>$this->branch_id,
            "operator_id"=>$this->user_id,
            "operator_name"=>$this->user_name,
            "dr_list"=>$dr_list,
            "cr_list"=>$cr_list
        ));
        if ($rt->STS) {
            $conn->submitTransaction();
            showMessage("Create Voucher Success!",getUrl("gl_tree","showVoucherListPage",array(),false,BACK_OFFICE_SITE_URL));
        } else {
            $conn->rollback();
            showMessage($rt->MSG);
        }
    }
    public function checkGlCodeValidOp($p){
        $_code=$p['code'];
        $m=new gl_treeModel();
        $item=$m->find(array("gl_code"=>$_code));
        if($item){
            if(!$item){
                return new result(false,"No Found Gl-Code");
            }
            if($item['currency']!=$p['currency']){
                return new result(false,"Invalid:Currency Is Different");
            }
            if(!$item['is_leaf']){
                return new result(false,"Invalid:Not Leaf Account");
            }
            if($item['is_dynamic']){
                return new result(false,"Invalid:Reserved Account");
            }
            return new result(true,"",$item);
        }else{
            //再从passbook_account找
            $pb_acct=new passbook_accountModel();
            $item=$pb_acct->find(array("gl_code"=>$_code));
            if(!$item){
                return new result(false,"No Found Gl-Code");
            }
            if($item['currency']!=$p['currency']){
                return new result(false,"Invalid:Currency Is Different");
            }
            $pb=new passbookModel();
            $item_book=$pb->find(array("uid"=>$item['book_id']));
            if(!in_array($item_book['obj_type'],array('bank','branch','partner'))){
                return new result(false,"Invalid:Reserved Account");
            }
            $arr=array(
                'gl_code'=>$_code,
                'gl_name'=>$item_book['book_name']."_".strtoupper($p['currency']),
                'gl_type'=>$item_book['book_type']
            );
            return new result(true,"",$arr);
        }


    }
    public function showVoucherListPageOp(){
        Tpl::showPage("voucher.list");
    }
    public function getVoucherListOp($p){
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 10;
        $biz=new biz_manual_voucherModel();
        $ret=$biz->getManualVoucherList(0,$pageNumber,$pageSize);
        $rows=$ret['data'];
        if(count($rows)){
            $rows=resetArrayKey($rows,"uid");
            $arr_id=array_keys($rows);
            $str_id=implode("','",$arr_id);
            $md=new biz_manual_voucher_detailModel();
            $list=$md->select("biz_id in ('".$str_id."')");
            foreach($list as $item){
                $rows[$item['biz_id']]['detail'][]=$item;
            }
        }
        $ret['data']=$rows;
        return $ret;
    }

}