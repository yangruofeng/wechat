<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/3/2018
 * Time: 4:38 PM
 */

class gl_treeControl extends back_office_baseControl{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Gl Account List");
        Tpl::setDir("gl");
    }
    function indexOp(){
        $gl_tree=new gl_treeModel();
        $top_list=$gl_tree->getTopLevelAccounts();
        Tpl::output("node_list",$top_list);
        Tpl::showPage("tree.style.default");
    }
    function getChildrenNodeOp($p){
        $gl_code=$p['parent_gl_code'];
        $gl_tree=new gl_treeModel();
        $chd_list=$gl_tree->getChildrenByParentGlCode($gl_code);
        return $chd_list;
    }
    function showTableStyleOp(){
        $gl_tree=new gl_treeModel();
        $list=$gl_tree->select("1=1");
        Tpl::output("node_list",$list);
        Tpl::showPage("tree.style.table");
    }
    function showUserDefinedOp(){
        $gl_tree=new gl_treeModel();
        $min_level=$gl_tree->reader->getOne("SELECT IFNULL(MIN(gl_level),0) FROM gl_tree WHERE is_system=0");
        if($min_level>0){
            $list=$gl_tree->select("is_system=0 and gl_level=".$min_level);
            Tpl::output("node_list",$list);
        }
        Tpl::showPage("tree.style.define");
    }
    function addAccountPageOp(){
        Tpl::showPage("node.add");
    }
    function submitNewNodeOp(){
        $args=$_POST;
        $parent_gl_code=trim($args['parent_gl_code']);
        if(!$parent_gl_code){
            showMessage("Please Input Parent-Gl-Code");
        }
        $new_gl_code=intval($args['gl_code']);
        if(!$new_gl_code){
            showMessage("Invalid Gl-Code");
        }
        $new_gl_name=$args['gl_name'];
        if(!$new_gl_name){
            showMessage("Invalid Gl-Name");
        }

        $is_leaf=$args['is_leaf'];
        //先判断parent_gl_code
        $gl_tree=new gl_treeModel();
        $r1=$gl_tree->find(array("gl_code"=>$parent_gl_code));
        if(!$r1){
            showMessage("No Parent-GL-Code Found In Account List");
        }
        if($r1['is_leaf'] || $r1['is_dynamic'] || $r1['currency']){
            showMessage("Invalid Parent-GL-Code,Not Allowed to Use This Code");
        }
        //如果下级直接有货币，也不能添加
        $chk_cnt=$gl_tree->reader->getOne("SELECT count(uid) cnt FROM gl_tree WHERE LENGTH(currency)>0 AND parent_gl_code=".qstr($parent_gl_code));
        if($chk_cnt>0){
            showMessage("Invalid Parent-GL-Code,Not Allowed to Use This Code");
        }
        $gl_code=$r1['gl_code']."-".$new_gl_code;
        //先判断是否已经存在了
        $chk_row=$gl_tree->find(array("gl_code"=>$gl_code));
        if($chk_row){
            showMessage("Invalid GL-Code,already exist");
        }
        $ccy_list=(new accountingCurrencyEnum())->Dictionary();
        $ccy_gl_default=array(
            accountingCurrencyEnum::KHR=>1,
            accountingCurrencyEnum::USD=>2,
            accountingCurrencyEnum::THB=>5
        );
        $conn=$gl_tree->conn;
        $conn->startTransaction();

        //先构造gl_tree的gl_code(1+3的模式)

        $gl_name=$r1['gl_name'].'-'.$new_gl_name;
        $row_gl=$gl_tree->newRow(array(
            "gl_code"=>$gl_code,
            "gl_name"=>$gl_name,
            "gl_type"=>$r1['gl_type'],
            "parent_gl_code"=>$r1['gl_code'],
            'remark'=>'x',
            "is_leaf"=>0,
            "is_dynamic"=>0,
            "gl_level"=>$r1['gl_level']+1,
            "is_system"=>0
        ));
        $ret=$row_gl->insert();
        if(!$ret->STS){
            $conn->rollback();
            showMessage($ret->MSG);
        }
        if($is_leaf){
            //插入各种货币的gl_code
            $ccy_gl_list=array();
            foreach($ccy_list as $k=>$v){
                $ccy_gl_code=$gl_code."-".$ccy_gl_default[$k];
                $ccy_gl_name=$gl_name."_".$k;
                $row=$gl_tree->newRow(array(
                    "gl_code"=>$ccy_gl_code,
                    "gl_name"=>$ccy_gl_name,
                    "gl_type"=>$r1['gl_type'],
                    "parent_gl_code"=>$gl_code,
                    'currency'=>$k,
                    "is_leaf"=>1,
                    "is_dynamic"=>0,
                    "gl_level"=>$r1['gl_level']+2,
                    "is_system"=>0
                ));
                $ccy_list['gl_code_'.strtolower($k)]=$ccy_gl_code;
                $ret=$row->insert();
                if(!$ret->STS){
                    $conn->rollback();
                    showMessage($ret->MSG);
                }
            }
            //插入gl_account
            $ret_gl_acct=gl_accountClass::getAccountOfUserDefineByPassbookType($r1['gl_type']);
            if(!$ret_gl_acct->STS){
                $conn->rollback();
                showMessage($ret_gl_acct->MSG);
            }
            $gl_account=$ret_gl_acct->DATA;
            $new_book_id=core_gen_idModel::getGUID("gl_account_".$gl_account['book_code']);
            $new_book_id=str_pad($new_book_id,3,"0",STR_PAD_LEFT);
            $new_book_code=$gl_account['book_code']."-".$new_book_id;
            $book_arr=array(
                "book_code"=>$new_book_code,
                "book_name"=>$new_gl_name,
                "parent_book_code"=>$gl_account['book_code'],
                "category"=>$r1['gl_type'],
                "is_system"=>0,
                "is_leaf"=>1,
                'gl_code_khr'=>$ccy_list['gl_code_khr']?:'',
                'gl_code_usd'=>$ccy_list['gl_code_usd']?:'',
                'gl_code_thb'=>$ccy_list['gl_code_thb']?:""
            );
            $gl_account_model=new gl_accountModel();
            $row_book=$gl_account_model->newRow($book_arr);
            $ret=$row_book->insert();
            if(!$ret->STS){
                $conn->rollback();
                showMessage($ret->MSG);
            }
            //建立passbook

        }
        $conn->submitTransaction();
        showMessage("Add Successful!",getUrl("gl_tree","showUserDefined",array(),false,BACK_OFFICE_SITE_URL));
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