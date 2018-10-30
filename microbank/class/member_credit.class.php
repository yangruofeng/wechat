<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/30
 * Time: 13:41
 */
class member_creditClass
{
    function __construct()
    {

    }


    public static function getMemberCreditLeftDays($member_id)
    {
        //获取信用信息
        $credit_info = (new member_creditModel())->getRow(array(
            'member_id' => $member_id
        ));
        if( !$credit_info ){
            return 0;
        }

        // 计算剩余日期
        $left_days = ceil( ( strtotime($credit_info['expire_time']) -time() )/24/3600);
        if( $left_days <= 0 ){
            $left_days = 0;
        }
        return $left_days;

    }
    public static function DisburseCreditToClientByGrant($grant_id,$contract_id){
        $m_grant=new member_credit_grantModel();
        $grant_item=$m_grant->find(array("uid"=>$grant_id));
        if(!$grant_item){
            return new result(false,"No Application Found");
        }
        $member_id=$grant_item['member_id'];

        //取出资产信用
        $m_asset=new member_credit_grant_assetsModel();
        $total_asset_credit=$grant_item['default_credit'];
        $list_asset=$m_asset->select(array("grant_id"=>$grant_id));
        foreach($list_asset as $item){
            $total_asset_credit+=$item['credit'];
        }

        //取出货币信用
        $m_product=new member_credit_grant_productModel();
        $list_currency=$m_product->select(array("grant_id"=>$grant_id));
        $list_currency=resetArrayKey($list_currency,"member_credit_category_id");
        $total_currency_credit=0;

        //取出有效的category-id
        $sql="SELECT a.uid,a.alias FROM member_credit_category a INNER JOIN loan_category b ON a.`category_id`=b.`uid` WHERE a.`is_close`=0 AND b.`is_close`=0 AND a.`member_id`='".$member_id."'";
        $list_active_category=$m_grant->reader->getRows($sql);
        $list_active_category=resetArrayKey($list_active_category,"uid");
        $active_cc_ids=array_keys($list_active_category);

        foreach($list_currency as $id_key=>$item){
            if($item['exchange_rate']){
                $total_currency_credit+=$item['credit_usd']+$item['credit_khr']/$item['exchange_rate'];
            }
            //检查member_credit_category_id是否已经被关闭，如果被关闭则无效
            if(!in_array($id_key,$active_cc_ids)){
                return new result(false,"Invalid Category:".$id_key.",it was closed");
            }
        }
        if($total_asset_credit!=$total_currency_credit){
            return new result(false,"Invalid Application,total asset credit not eq total currency credit:".$total_asset_credit.'-'.$total_currency_credit);
        }





        $credit = ceil($grant_item['default_credit']);




        $m_credit = new member_creditModel();

        $member_credit = $m_credit->getRow(array(
            'member_id' => $member_id
        ));
        $is_first_time=false;
        if(!$member_credit){
            $member_credit=$m_credit->newRow();
            $member_credit->member_id=$member_id;
            $member_credit->credit=0;
            $member_credit->grant_time=Now();
            $member_credit->update_time=Now();
            $member_credit->credit_terms=0;
            $member_credit->credit_balance=0;
            $ret_insert=$member_credit->insert();
            if(!$ret_insert){
                return $ret_insert;
            }
            $is_first_time=true;
        }

        $m_credit_category=new member_credit_categoryModel();
        $member_credit_category=$m_credit_category->getRows(array("member_id"=>$member_id,"is_close"=>0));
        if(!count($member_credit_category)){
            return new result(false,"still no set category for this client");
        }
        $before_credit_balance=$member_credit->credit_balance;
        $before_credit=$member_credit->credit;
        $after_credit_balance=$before_credit_balance+$total_currency_credit;
        $after_credit=$before_credit+$total_currency_credit;

        //计算有效期
        if($grant_item['is_append']){
            $member_credit->credit+=$total_currency_credit;
            $member_credit->credit_balance+=$total_currency_credit;
            if($is_first_time){
                $credit_terms = intval($grant_item['credit_terms']);
                $expire_time = date('Y-m-d H:i:s', time() + $credit_terms * 30 * 24 * 3600);
                $member_credit->credit_terms=$credit_terms;
                $member_credit->expire_time=$expire_time;
            }
            $member_credit->update_time=Now();
            $ret_main=$member_credit->update();
            if(!$ret_main){
                $ret_main->MSG="Update Main Failed-1:".$ret_main->MSG;
                return $ret_main;
            }

            foreach($member_credit_category as $cc_item){
                $cc_item->credit+=$list_currency[$cc_item['uid']]['credit']?:0;
                $cc_item->credit_usd+=$list_currency[$cc_item['uid']]['credit_usd']?:0;
                $cc_item->credit_khr+=$list_currency[$cc_item['uid']]['credit_khr']?:0;
                $cc_item->credit_balance+=$list_currency[$cc_item['uid']]['credit']?:0;
                $cc_item->credit_usd_balance+=$list_currency[$cc_item['uid']]['credit_usd']?:0;
                $cc_item->credit_khr_balance+=$list_currency[$cc_item['uid']]['credit_khr']?:0;
                $cc_item->interest_rate_usd=$list_currency[$cc_item['uid']]['interest_rate']?:0;
                $cc_item->interest_rate_khr=$list_currency[$cc_item['uid']]['interest_rate_khr']?:0;
                $cc_item->operation_fee_usd=$list_currency[$cc_item['uid']]['operation_fee']?:0;
                $cc_item->operation_fee_khr=$list_currency[$cc_item['uid']]['operation_fee_khr']?:0;

                $cc_item->update_time=Now();
                $ret_cc=$cc_item->update();
                if(!$ret_cc->STS){
                    $ret_cc->MSG="Update-Category-Failed:".$ret_cc->MSG;
                    return $ret_cc;
                }
            }

        }else{
            $member_credit->credit=$total_currency_credit;
            $member_credit->credit_balance=$total_currency_credit;
            $credit_terms = intval($grant_item['credit_terms']);
            $expire_time = date('Y-m-d H:i:s', time() + $credit_terms * 30 * 24 * 3600);
            $member_credit->credit_terms=$credit_terms;
            $member_credit->expire_time=$expire_time;
            $member_credit->update_time=Now();
            $ret_main=$member_credit->update();
            if(!$ret_main){
                $ret_main->MSG="Update Main Failed:".$ret_main->MSG;
                return $ret_main;
            }


            foreach($member_credit_category as $cc_item){
                $cc_item->credit=$list_currency[$cc_item['uid']]['credit']?:0;
                $cc_item->credit_usd=$list_currency[$cc_item['uid']]['credit_usd']?:0;
                $cc_item->credit_khr=$list_currency[$cc_item['uid']]['credit_khr']?:0;
                $cc_item->credit_balance=$list_currency[$cc_item['uid']]['credit']?:0;
                $cc_item->credit_usd_balance=$list_currency[$cc_item['uid']]['credit_usd']?:0;
                $cc_item->credit_khr_balance=$list_currency[$cc_item['uid']]['credit_khr']?:0;
                $cc_item->interest_rate_usd=$list_currency[$cc_item['uid']]['interest_rate']?:0;
                $cc_item->interest_rate_khr=$list_currency[$cc_item['uid']]['interest_rate_khr']?:0;
                $cc_item->operation_fee_usd=$list_currency[$cc_item['uid']]['operation_fee']?:0;
                $cc_item->operation_fee_khr=$list_currency[$cc_item['uid']]['operation_fee_khr']?:0;

                $cc_item->update_time=Now();
                $ret_cc=$cc_item->update();
                if(!$ret_cc->STS){
                    $ret_cc->MSG="Update-Category-Failed:".$ret_cc->MSG;
                    return $ret_cc;
                }
            }
        }




        // 更新member还款能力
        $loan_account=loan_accountClass::getLoanAccountRowByMemberId($member_id);
        if (!$loan_account) {
            return new result(false, 'No loan account.');
        }
        $loan_account->repayment_ability = $grant_item['monthly_repayment_ability'];
        $loan_account->update_time = Now();
        $ret = $loan_account->update();
        if (!$ret->STS) {
            return $ret;
        }

        if(!$grant_item['is_append']){
            //如果是替代，要把之前的grant状态设置为过期
            $sql="update member_credit_grant set state='".commonApproveStateEnum::EXPIRY."' where member_id='".$member_id."' and state='".commonApproveStateEnum::PASS."' and uid!='".$grant_id."'";
            $ret=$m_grant->conn->execute($sql);
            if(!$ret->STS){
                return new result(false,"update-grant-state:".$ret->MSG);
            }

        }



        $m_flow=new member_credit_flowModel();
        //写flow
        $flow = $m_flow->newRow();
        $flow->member_id = $member_id;
        $flow->event_type = creditEventTypeEnum::GRANT;
        $flow->begin_balance = $before_credit_balance;
        $flow->flag = 1;
        $flow->amount = $credit;
        $flow->after_balance = $after_credit_balance;
        $flow->remark = 'Grant add credit balance.';
        $flow->create_time = Now();
        $insert = $flow->insert();
        if (!$insert->STS) {
            return new result(false, 'Grant fail', null, errorCodesEnum::DB_ERROR);
        }
        //写log
        // 添加信用变更日志
        $m_log = new member_credit_logModel();
        $log = $m_log->newRow();
        $log->member_id = $member_id;
        $log->event_type = creditEventTypeEnum::GRANT;
        $log->authorized_contract_id = $contract_id;
        $log->begin_credit = $before_credit;
        $log->flag = 1;
        $log->amount = $credit;
        $log->after_credit = $before_credit+$credit;
        $log->remark = 'Grant credit';
        $log->create_time = Now();
        $insert = $log->insert();
        if (!$insert->STS) {
            return new result(false, 'Insert credit log fail.', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $member_credit);
    }

    public static function addCreditForMemberByCategoryId($member_credit_category_id,$credit,$remark='',$event_type,$authorized_id = 0){
        $m_credit_category=new member_credit_categoryModel();
        $cc_row=$m_credit_category->getRow(array("uid"=>$member_credit_category_id));
        if($cc_row){
            $member_id=$cc_row->member_id;
        }else{
            return new result(false,"Invalid Credit Category");
        }
        $m_credit = new member_creditModel();
        $m_flow = new member_credit_flowModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member_id
        ));
        if($member_credit->expire_time<Now()){
            return new result(false,"Credit was expired,not allowed to add");
        }

        $before_credit_balance=$member_credit->credit_balance;
        $after_credit_balance=$member_credit->credit_balance+$credit;
        $before_credit=$member_credit->credit;
        $after_credit=$member_credit->credit+$credit;

        $member_credit->credit+=$credit;
        $member_credit->credit_balance+=$credit;
        $member_credit->update_time=Now();
        $ret=$member_credit->update();
        if(!$ret->STS){
            return $ret;
        }

        $cc_row->credit+=$credit;
        $cc_row->credit_balance+=$credit;
        $cc_row->update_time=Now();
        $ret=$cc_row->update();
        if(!$ret->STS){
            return $ret;
        }

        //写flow
        $flow = $m_flow->newRow();
        $flow->member_id = $member_id;
        $flow->event_type = creditEventTypeEnum::GRANT;
        $flow->begin_balance = $before_credit_balance;
        $flow->flag = 1;
        $flow->amount = $credit;
        $flow->after_balance = $after_credit_balance;
        $flow->remark = $remark;
        $flow->create_time = Now();
        $insert = $flow->insert();
        if (!$insert->STS) {
            return new result(false, 'Grant fail', null, errorCodesEnum::DB_ERROR);
        }
        //写log
        // 添加信用变更日志
        $m_log = new member_credit_logModel();
        $log = $m_log->newRow();
        $log->member_id = $member_id;
        $log->event_type = $event_type?:creditEventTypeEnum::GRANT;
        $log->authorized_contract_id = $authorized_id;
        $log->begin_credit = $before_credit;
        $log->flag = 1;
        $log->amount = $credit;
        $log->after_credit = $after_credit;
        $log->remark = $remark;
        $log->create_time = Now();
        $insert = $log->insert();
        if (!$insert->STS) {
            return new result(false, 'Insert credit log fail.', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true);
    }
    public static function cutCreditForMemberByCategoryId($member_credit_category_id,$credit,$remark='',$event_type,$authorized_id = 0){
        $m_credit_category=new member_credit_categoryModel();
        $cc_row=$m_credit_category->getRow(array("uid"=>$member_credit_category_id));
        if($cc_row){
            $member_id=$cc_row->member_id;
        }else{
            return new result(false,"Invalid Credit Category");
        }
        $m_credit = new member_creditModel();
        $m_flow = new member_credit_flowModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member_id
        ));
        if($member_credit->expire_time<Now()){
            return new result("Credit was expired,not allowed to add");
        }

        $before_credit_balance=$member_credit->credit_balance;
        $after_credit_balance=$member_credit->credit_balance-$credit;
        $before_credit=$member_credit->credit;
        $after_credit=$member_credit->credit-$credit;
        if($after_credit<0 || $after_credit_balance<0){
            return new result(false,"Credit Balance is not enough");
        }

        $member_credit->credit-=$credit;
        $member_credit->credit_balance-=$credit;
        $member_credit->update_time=Now();
        $ret=$member_credit->update();
        if(!$ret->STS){
            return $ret;
        }

        $cc_row->credit-=$credit;
        $cc_row->credit_balance-=$credit;
        $cc_row->update_time=Now();
        $ret=$cc_row->update();
        if(!$ret->STS){
            return $ret;
        }

        //写flow
        $flow = $m_flow->newRow();
        $flow->member_id = $member_id;
        $flow->event_type = creditEventTypeEnum::GRANT;
        $flow->begin_balance = $before_credit_balance;
        $flow->flag = -1;
        $flow->amount = $credit;
        $flow->after_balance = $after_credit_balance;
        $flow->remark = $remark;
        $flow->create_time = Now();
        $insert = $flow->insert();
        if (!$insert->STS) {
            return new result(false, 'Grant fail', null, errorCodesEnum::DB_ERROR);
        }
        //写log
        // 添加信用变更日志
        $m_log = new member_credit_logModel();
        $log = $m_log->newRow();
        $log->member_id = $member_id;
        $log->event_type = $event_type?:creditEventTypeEnum::GRANT;
        $log->authorized_contract_id = $authorized_id;
        $log->begin_credit = $before_credit;
        $log->flag = -1;
        $log->amount = $credit;
        $log->after_credit = $after_credit;
        $log->remark = $remark;
        $log->create_time = Now();
        $insert = $log->insert();
        if (!$insert->STS) {
            return new result(false, 'Insert credit log fail.', null, errorCodesEnum::DB_ERROR);
        }
        return new result(true);
    }



    /** 为客户授信
     * @param $member_id
     * @param $credit
     * @param $credit_terms
     * @return result
     */
    public static function creditGrant($member_id, $credit, $credit_terms, $authorized_contract_id = 0)
    {
        $credit = ceil($credit);
        $credit_terms = intval($credit_terms);
        $m_credit = new member_creditModel();
        $m_flow = new member_credit_flowModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member_id
        ));

        $now = Now();
        $expire_time = date('Y-m-d H:i:s', time() + $credit_terms * 30 * 24 * 3600);

        if ($member_credit) {

            // 信用增减
            $before_credit = $member_credit->credit;
            $before_credit_balance = $member_credit->credit_balance;
            if ($credit == $before_credit) {
                // 没改变信用值
                return new result(true, 'success', $member_credit);
            }

            if ($credit > $before_credit) {

                // 提升信用
                // 自动增加信用余额
                $expend_credit = $before_credit - $before_credit_balance;  // 消耗的信用
                $after_credit_balance = $credit - $expend_credit;
                if( $after_credit_balance < 0 ){
                    $after_credit_balance = 0;
                }

                $member_credit->credit = $credit;
                $member_credit->credit_balance = $after_credit_balance;
                $member_credit->grant_time = $now;
                $member_credit->credit_terms = $credit_terms;
                $member_credit->expire_time = $expire_time;
                $up = $member_credit->update();
                if (!$up->STS) {
                    return new result(false, 'Grant fail', null, errorCodesEnum::DB_ERROR);
                }

                $flow_amount = $after_credit_balance - $before_credit_balance;
                $flow = $m_flow->newRow();
                $flow->member_id = $member_id;
                $flow->event_type = creditEventTypeEnum::GRANT;
                $flow->begin_balance = $before_credit_balance;
                $flow->flag = 1;
                $flow->amount = $flow_amount;
                $flow->after_balance = $after_credit_balance;
                $flow->remark = 'Grant add credit balance.';
                $flow->create_time = $now;
                $insert = $flow->insert();
                if (!$insert->STS) {
                    return new result(false, 'Grant fail', null, errorCodesEnum::DB_ERROR);
                }


            } else {

                // 降低信用
                // 扣减信用余额
                $expend_credit = $before_credit - $before_credit_balance;  // 消耗的信用
                if ($expend_credit >= $credit) {
                    $after_credit_balance = 0;
                } else {
                    $after_credit_balance = $credit - $expend_credit;
                }

                $flow_amount = $before_credit_balance - $after_credit_balance;

                $member_credit->credit = $credit;
                $member_credit->credit_balance = $after_credit_balance;
                $member_credit->grant_time = $now;
                $member_credit->credit_terms = $credit_terms;
                $member_credit->expire_time = $expire_time;
                $up = $member_credit->update();
                if (!$up->STS) {
                    return new result(false, 'Grant fail', null, errorCodesEnum::DB_ERROR);
                }

                $flow = $m_flow->newRow();
                $flow->member_id = $member_id;
                $flow->event_type = creditEventTypeEnum::GRANT;
                $flow->begin_balance = $before_credit_balance;
                $flow->flag = -1;
                $flow->amount = $flow_amount;
                $flow->after_balance = $after_credit_balance;
                $flow->remark = 'Grant minus credit balance.';
                $flow->create_time = $now;
                $insert = $flow->insert();
                if (!$insert->STS) {
                    return new result(false, 'Grant fail', null, errorCodesEnum::DB_ERROR);
                }

            }


        } else {

            // 信用增减
            $before_credit = 0;
            $before_credit_balance = 0;

            // 初次授信
            $member_credit = $m_credit->newRow();
            $member_credit->member_id = $member_id;
            $member_credit->credit = $credit;
            $member_credit->credit_balance = $credit;
            $member_credit->credit_terms = $credit_terms;
            $member_credit->grant_time = $now;
            $member_credit->expire_time = $expire_time;
            $insert = $member_credit->insert();
            if (!$insert->STS) {
                return new result(false, 'Grant fail', null, errorCodesEnum::DB_ERROR);
            }

            $flow = $m_flow->newRow();
            $flow->member_id = $member_id;
            $flow->event_type = creditEventTypeEnum::GRANT;
            $flow->begin_balance = $before_credit_balance;
            $flow->flag = 1;
            $flow->amount = $credit;
            $flow->after_balance = $credit;
            $flow->remark = 'Grant add credit balance.';
            $flow->create_time = $now;
            $insert = $flow->insert();
            if (!$insert->STS) {
                return new result(false, 'Grant fail', null, errorCodesEnum::DB_ERROR);
            }


        }

        // 添加信用变更日志
        $m_log = new member_credit_logModel();
        $log = $m_log->newRow();
        $log->member_id = $member_id;
        $log->event_type = creditEventTypeEnum::GRANT;
        $log->authorized_contract_id = $authorized_contract_id;
        $log->begin_credit = $before_credit;
        $log->flag = ($credit - $before_credit) > 0 ? 1 : -1;
        $log->amount = abs($credit - $before_credit);
        $log->after_credit = $credit;
        $log->remark = 'Grant credit';
        $log->create_time = Now();
        $insert = $log->insert();
        if (!$insert->STS) {
            return new result(false, 'Insert credit log fail.', null, errorCodesEnum::DB_ERROR);
        }


        return new result(true, 'success', $member_credit);

    }


    /** 提升客户信用
     * @param $event_type
     * @param $member_id
     * @param $credit
     * @return result
     */
    public static function increaseMemberCredit($event_type, $member_id, $credit, $remark, $authorized_id = 0)
    {
        return new result(false, 'not support yet');
    }

    /** 降低客户信用
     * @param $event_type
     * @param $member_id
     * @param $credit
     * @return result
     */
    public static function decreaseMemberCredit($event_type, $member_id, $credit, $remark, $authorized_id = 0)
    {

        return new result(false, 'not support yet');
    }

    /** 公共方法增加信用余额
     * @param $type
     * @param $member_credit_category_id
     * @param $amount
     * @param $remark
     * @return result
     */
    public static function addCreditBalance($type, $member_credit_category_id, $amount,$currency, $remark=null)
    {
        switch ($type) {

            case creditEventTypeEnum::CREDIT_LOAN:
                return self::creditLoanUpdateCreditBalance($member_credit_category_id, $amount,$currency, 1, $remark);
                break;
            default:
                return self::unknownEventUpdateCreditBalance($member_credit_category_id, $amount,$currency, 1, $remark);
                break;
        }
    }

    /** 公共方法扣减信用余额
     * @param $type
     * @param $member_credit_category_id
     * @param $amount
     * @param $remark
     * @return result
     */
    public static function minusCreditBalance($type, $member_credit_category_id, $amount,$currency, $remark=null)
    {
        switch ($type) {
            case creditEventTypeEnum::CREDIT_LOAN:
                return self::creditLoanUpdateCreditBalance($member_credit_category_id, $amount,$currency, -1, $remark);
                break;
            default:
                return self::unknownEventUpdateCreditBalance($member_credit_category_id, $amount,$currency, -1, $remark);
                break;
        }
    }

    /** 未定义事件增减余额
     * @param $member_id
     * @param $amount
     * @param $flag
     * @param $remark
     * @return result
     */
    protected static function unknownEventUpdateCreditBalance($member_id, $amount,$currency, $flag, $remark=null)
    {
        $event_type = 'unknown';
        switch ($flag) {
            case 1:
                if (!$remark) $remark = 'Unknown event add';
                $re = self::baseUpdateCreditBalanceAdd($event_type, $member_id, $amount,$currency, $remark);
                return $re;
                break;
            case -1:
                if (!$remark) $remark = 'Unknown event minus';
                $re = self::baseUpdateCreditBalanceMinus($event_type, $member_id, $amount,$currency, $remark);
                return $re;
                break;
            default:
                return new result(false, 'Unsupported', null, errorCodesEnum::NOT_SUPPORTED);
                break;
        }
    }

    /** 信用贷款增减余额
     * @param $member_credit_category_id
     * @param $amount
     * @param $flag
     * @param $remark
     * @return result
     */
    protected static function creditLoanUpdateCreditBalance($member_credit_category_id, $amount,$currency, $flag, $remark=null)
    {
        $event_type = creditEventTypeEnum::CREDIT_LOAN;
        switch ($flag) {
            case 1:
                if (!$remark) $remark = 'Credit loan repayment';
                $re = self::baseUpdateCreditBalanceAdd($event_type, $member_credit_category_id, $amount,$currency, $remark);
                return $re;
                break;
            case -1:
                if (!$remark) $remark = 'Credit loan';
                $re = self::baseUpdateCreditBalanceMinus($event_type, $member_credit_category_id, $amount,$currency, $remark);
                return $re;
                break;
            default:
                return new result(false, 'Unsupported', null, errorCodesEnum::NOT_SUPPORTED);
                break;
        }

    }


    /** 基础增加信用余额方法
     * @param $event_type
     * @param $member_credit_category_id
     * @param $amount
     * @param $remark
     * @return result
     */
    protected static function baseUpdateCreditBalanceAdd($event_type, $member_credit_category_id, $amount,$currency, $remark)
    {
        $m_credit_category=new member_credit_categoryModel();
        $cc_row=$m_credit_category->getRow(array("uid"=>$member_credit_category_id));
        if($cc_row){
            $member_id=$cc_row->member_id;
        }else{
            return new result(false,"Invalid Credit Category",errorCodesEnum::NO_DATA);
        }

        $amount = ceil($amount);
        if ($amount <= 0) {
            return new result(false, 'Invalid amount', null, errorCodesEnum::INVALID_AMOUNT);
        }
        $amount_currency=$amount;
        if($currency==currencyEnum::USD){

        }elseif($currency==currencyEnum::KHR){
            $amount=$amount/4000;
        }else{
            return  new result(false,"not support the currency:".$currency);
        }


        $m_credit = new member_creditModel();
        $m_flow = new member_credit_flowModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member_id
        ));
        if (!$member_credit) {
            return new result(false, 'Un grant credit', null, errorCodesEnum::MEMBER_UN_GRANT_CREDIT);
        }

        $credit = $member_credit->credit;
        $before_credit_balance = $member_credit->credit_balance;
        $now = Now();

        // 不能超过信用值
        $after_credit_balance = $before_credit_balance + $amount;
        if ($after_credit_balance > $credit) {
            $after_credit_balance = $credit;
        }
        $member_credit->credit_balance = $after_credit_balance;
        $member_credit->update_time = $now;
        $up = $member_credit->update();
        if (!$up->STS) {
            return new result(false, 'fail', null, errorCodesEnum::DB_ERROR);
        }

        $after_category_credit_balance = $cc_row->credit_balance + $amount;
        if ($after_category_credit_balance > $cc_row->credit) {
            $after_category_credit_balance = $cc_row->credit;
        }
        $cc_row->credit_balance=$after_category_credit_balance;
        if($currency==currencyEnum::USD){
            $cc_row->credit_usd_balance+=$amount_currency;
            if($cc_row->credit_usd<$cc_row->credit_usd_balance){
                $cc_row->credit_usd_balanc=$cc_row->credit_usd;
            }
        }else{
            $cc_row->credit_khr_balance+=$amount_currency;
            if($cc_row->credit_khr<$cc_row->credit_khr_balance){
                $cc_row->credit_khr_balanc=$cc_row->credit_khr;
            }

        }
        $cc_row->update_time=Now();
        $ret=$cc_row->update();
        if(!$ret->STS){
            return $ret;
        }

        $flow = $m_flow->newRow();
        $flow->member_id = $member_id;
        $flow->event_type = $event_type;
        $flow->begin_balance = $before_credit_balance;
        $flow->flag = 1;
        $flow->amount = $amount;
        $flow->after_balance = $after_credit_balance;
        $flow->remark = $remark;
        $flow->create_time = $now;
        $insert = $flow->insert();
        if (!$insert->STS) {
            return new result(false, 'Grant fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $member_credit);

    }


    /** 基础扣减信用余额方法
     * @param $event_type
     * @param $member_credit_category_id
     * @param $amount
     * @param $remark
     * @return result
     */
    protected static function baseUpdateCreditBalanceMinus($event_type, $member_credit_category_id, $amount,$currency, $remark)
    {
        $m_credit_category=new member_credit_categoryModel();
        $cc_row=$m_credit_category->getRow(array("uid"=>$member_credit_category_id));
        if($cc_row){
            $member_id=$cc_row->member_id;
        }else{
            return new result(false,"Invalid Credit Category",null,errorCodesEnum::NO_DATA);
        }

        $amount = ceil($amount);
        if ($amount <= 0) {
            return new result(false, 'Invalid amount', null, errorCodesEnum::INVALID_AMOUNT);
        }

        $amount_currency=$amount;
        if($currency==currencyEnum::USD){

        }elseif($currency==currencyEnum::KHR){
            $amount=$amount/4000;
        }else{
            return  new result(false,"not support the currency:".$currency);
        }


        $m_credit = new member_creditModel();
        $m_flow = new member_credit_flowModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member_id
        ));
        if (!$member_credit) {
            return new result(false, 'Un grant credit', null, errorCodesEnum::MEMBER_UN_GRANT_CREDIT);
        }
        $member_credit_uid = $member_credit['uid'];

        $before_credit_balance = $member_credit->credit_balance;
        if( $before_credit_balance < $amount ){
            // 不允许扣除超出信用的操作
            return new result(false,'Credit balance not enough for:'.$amount,null,errorCodesEnum::OUT_OF_CREDIT_BALANCE);
        }
        if( $cc_row->credit_balance < $amount ){
            // 不允许扣除超出信用的操作
            return new result(false,'Category credit balance not enough for:'.$amount,null,errorCodesEnum::OUT_OF_CREDIT_BALANCE);
        }

        $now = Now();
        // 用sql执行更新，防止并发(更新的时候再检查余额是否足够,避免判断足额而因为并发等待实际已经不足额了的BUG)
        $sql = "update member_credit set credit_balance=credit_balance-$amount,update_time='$now' where 
        uid='$member_credit_uid' and credit_balance>=$amount ";  // 也可以直接更新判断更新后的值是否为负
        $up = $m_credit->conn->execute($sql);
        if( !$up->STS ){
            return new result(false,'Update credit balance fail.',null,errorCodesEnum::DB_ERROR);
        }
        // 判断是否当前行执行成功，需要检测影响行的值
        if( $up->AFFECTED_ROWS < 1 ){
            return new result(false,'Credit balance not enough for:'.$amount,null,errorCodesEnum::OUT_OF_CREDIT_BALANCE);
        }
        if($currency==currencyEnum::USD){
            $sql = "update member_credit_category set credit_balance=credit_balance-$amount,credit_usd_balance=credit_usd_balance-$amount_currency,update_time='$now' where
        uid=".qstr($member_credit_category_id)." and credit_balance>=$amount and credit_usd_balance>=$amount_currency ";  // 也可以直接更新判断更新后的值是否为负

        }else{
            $sql = "update member_credit_category set credit_balance=credit_balance-$amount,credit_khr_balance=credit_khr_balance-$amount_currency,update_time='$now' where
        uid=".qstr($member_credit_category_id)." and credit_balance>=$amount and credit_khr_balance>=$amount_currency ";  // 也可以直接更新判断更新后的值是否为负

        }

        $up = $m_credit->conn->execute($sql);
        if( !$up->STS ){
            return new result(false,'Update category credit balance fail.',null,errorCodesEnum::DB_ERROR);
        }
        // 判断是否当前行执行成功，需要检测影响行的值
        if( $up->AFFECTED_ROWS < 1 ){
            return new result(false,'Category credit balance not enough for:'.$amount,null,errorCodesEnum::OUT_OF_CREDIT_BALANCE);
        }

        $after_member_credit = $m_credit->getRow($member_credit_uid);
        if( !$after_member_credit ){
            return new result(false,'Db select error.',null,errorCodesEnum::DB_ERROR);
        }

        $after_credit_balance = $after_member_credit->credit_balance;
        $before_credit_balance = $after_credit_balance + $amount;


        $flow = $m_flow->newRow();
        $flow->member_id = $member_id;
        $flow->event_type = $event_type;
        $flow->begin_balance = $before_credit_balance;
        $flow->flag = -1;
        $flow->amount = $amount;
        $flow->after_balance = $after_credit_balance;
        $flow->remark = $remark;
        $flow->create_time = $now;
        $insert = $flow->insert();
        if (!$insert->STS) {
            return new result(false, 'Grant fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $member_credit);

    }

    /** 待算信用余额基础方法
     * @param $event_type
     * @param $member_id
     * @param $amount
     * @param $remark
     * @return result
     */
    protected static function baseUpdateCreditBalanceOutstanding($event_type, $member_id, $amount, $remark)
    {
        $amount = ceil($amount);
        if ($amount <= 0) {
            return new result(false, 'Invalid amount', null, errorCodesEnum::INVALID_AMOUNT);
        }

        $m_credit = new member_creditModel();
        $m_flow = new member_credit_flowModel();
        $member_credit = $m_credit->getRow(array(
            'member_id' => $member_id
        ));
        if (!$member_credit) {
            return new result(false, 'Un grant credit', null, errorCodesEnum::MEMBER_UN_GRANT_CREDIT);
        }

        $before_credit_balance = $member_credit->credit_balance;
        $now = Now();
        $flow = $m_flow->newRow();
        $flow->member_id = $member_id;
        $flow->event_type = $event_type;
        $flow->begin_balance = $before_credit_balance;
        $flow->flag = 0;
        $flow->amount = $amount;
        $flow->after_balance = $before_credit_balance;
        $flow->remark = $remark;
        $flow->create_time = $now;
        $insert = $flow->insert();
        if (!$insert->STS) {
            return new result(false, 'Grant fail', null, errorCodesEnum::DB_ERROR);
        }

        return new result(true, 'success', $member_credit);
    }


    /** 获取客户最后一次授信详情
     * @param $member_id
     * @return mixed
     */
    public static function getLastGrantCreditDetail($member_id)
    {
        return member_credit_grantClass::getMemberLastGrantInfo($member_id);
    }


    public static function getMemberCreditHistory($member_id)
    {
        $r = new ormReader();
        $sql = "select a.*,b.branch_name from member_authorized_contract a left join site_branch b
        on b.uid=a.branch_id where a.member_id=".qstr($member_id)." and a.state>".qstr(authorizedContractStateEnum::CREATE)
        ." order by a.uid desc ";
        $rows = $r->getRows($sql);
        return $rows;
    }


}