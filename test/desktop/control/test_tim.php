<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/18/2018
 * Time: 11:25 AM
 */
class test_timControl extends control
{
    public function __construct()
    {
        Tpl::setLayout("empty_layout");
        Tpl::setDir("test_tim");
    }
    public function testMapOp(){
        Tpl::showPage("google_map");
    }
    public function testRoundOp(){
        $uid=1433;
        $ret=$this->updateRoundOfContractForKHR($uid);
        var_dump($ret);

    }
    /*
         * 格式化KHR贷款,因为面值最小是100，四舍五入（50为界）
     * ，每一条scheme的子项 round 100
     * total repayment round 1000
         */
    public static function updateRoundOfContractForKHR($contract_id){
        //先格式化installment-scheme
        $r=new ormReader();
        $sql="select * from loan_installment_scheme where contract_id=".qstr($contract_id)." order by uid";
        $arr_scheme=$r->getRows($sql);
        $list_sql=array();
        $next_init_principal=0;
        $total_fee=0;
        $total_interest=0;
        $last_i=count($arr_scheme)-1;
        foreach($arr_scheme as $i=>$item){
            $new_ref_amt=floor($item['ref_amount']/100)*100+($item['ref_amount']%100>=50?100:0);
            $new_rp_interest=floor($item['receivable_interest']/100)*100+($item['receivable_interest']%100>=50?100:0);
            $new_rp_operation_fee=floor($item['receivable_operation_fee']/100)*100+($item['receivable_operation_fee']%100>=50?100:0);

            if($i==$last_i){
                //最后一行要使剩余本金等于应收本金
                if($next_init_principal>0){
                    $new_rp_principal=$next_init_principal;
                }else{
                    $new_rp_principal=$item['initial_principal'];//说明只有一行数据
                }
                $new_ref_amt=$new_rp_principal+$new_rp_operation_fee+$new_rp_interest;
            }else{
                $new_rp_principal=$new_ref_amt-$new_rp_interest-$new_rp_operation_fee;
            }
            //重新把new_ref_amt格式化到1000

            $remainder=$new_ref_amt%1000;
            if($remainder>0){
                $ext_amt=1000-$remainder;
                $new_ref_amt+=$ext_amt;
                //最后一期要把多收的加在利息上,才能保证本金的应收和期初相等
                if($i==$last_i){
                    $new_rp_interest+=$ext_amt;
                }else{
                    $new_rp_principal+=$ext_amt;
                }
            }
            $new_amt=$new_ref_amt;



            $sql="update loan_installment_scheme";
            $sql.=" set ";
            $sql.=" receivable_principal=".$new_rp_principal;
            $sql.=",receivable_interest=".$new_rp_interest;
            $sql.=",receivable_operation_fee=".$new_rp_operation_fee;
            $sql.=",ref_amount=".$new_ref_amt;
            $sql.=",amount=".$new_amt;
            if($next_init_principal>0){
                $sql.=",initial_principal=".$next_init_principal;
            }
            $sql.=" where uid=".qstr($item['uid']);
            $list_sql[]=$sql;
            $next_init_principal=($next_init_principal>0?$next_init_principal:$item['initial_principal'])-$new_rp_principal;
            if($next_init_principal<0){
                $next_init_principal=0;
            }
            $total_fee+=$new_rp_operation_fee;
            $total_interest+=$new_rp_interest;
        }
        //格式化主表
        $sql="update loan_contract set";
        $sql.=" ref_operation_fee=".$total_fee;
        $sql.=",receivable_operation_fee=".$total_fee;
        $sql.=",receivable_interest=".$total_interest;
        $sql.=" where uid=".qstr($contract_id);
        $list_sql[]=$sql;
        if(count($list_sql)){
            //$sql=join($list_sql,";"); 一次请求不支持多个sql语句执行，这里存在一个效率问题了
            foreach($list_sql as $sql){
                $ret=$r->conn->execute($sql);
                if(!$ret->STS){
                    return $ret;
                }
            }
            return new result(true,"SUCCESS ROUND");
        }else{
            return new result(true,"SUCCESS ROUND");
        }
    }
}