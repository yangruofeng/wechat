<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/24
 * Time: 17:02
 */
class member_authorized_contract_payment_detailModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('member_authorized_contract_payment_detail');
    }

    public function insertPaymentDetail($contract_id,$currency_amount)
    {
        if( !$contract_id || empty($currency_amount) ){
            return new result(false,'Invalid param.',null,errorCodesEnum::INVALID_PARAM);
        }

        // 先执行删除操作
        $sql = "delete from member_authorized_contract_payment_detail where contract_id=".qstr($contract_id);
        $del = $this->conn->execute($sql);
        if( !$del->STS ){
            return $del;
        }

        $data = array();
        foreach( $currency_amount as $c=>$a ){
            $str = "('$contract_id','$a','$c','".Now()."')";
            $data[] = $str;
        }
        $sql = "insert into member_authorized_contract_payment_detail(contract_id,amount,currency,pay_time)
        values  ".implode(',',$data);
        $insert = $this->conn->execute($sql);
        return $insert;

    }
}