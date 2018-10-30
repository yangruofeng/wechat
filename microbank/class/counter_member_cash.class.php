<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/30
 * Time: 15:34
 */


class counter_member_cashClass{


    /*
     * 返回存款的biz详细，只读的数组
     */
    static function getMemberDepositBizByID($biz_id){
        $class_member_deposit_cash = new bizMemberDepositByCashClass(bizSceneEnum::COUNTER);
        $ret= $class_member_deposit_cash->getBizDetailById($biz_id);
        return $ret;
    }
    static function getMemberWithdrawBizByID($biz_id){
        $class_member_deposit_cash = new bizMemberWithdrawToCashClass(bizSceneEnum::COUNTER);
        $ret= $class_member_deposit_cash->getBizDetailById($biz_id);
        return $ret;
    }

    /**
     * 创建存款biz
    */
    static function createMemberCashDeposit($p)
    {
        $cashier_id = intval($p['cashier_id']);
        $client_id = intval($p['client_id']);
        $amount = round($p['amount'], 2);
        $currency = trim($p['currency']);
        $remark = trim($p['remark']);

        if ($amount<=0) {
            return new result(false, "The amount must be greater than 0", null, errorCodesEnum::INVALID_PARAM);
        }

        $class_member_deposit_cash = new bizMemberDepositByCashClass(bizSceneEnum::COUNTER);
        $rt_1 = $class_member_deposit_cash->bizStart($client_id, $amount, $currency, $cashier_id, $remark);
        if ($rt_1->STS) {
            return new result(true, $rt_1->MSG,$rt_1->DATA['biz_id']);
        } else {
            return new result(false, $rt_1->MSG);
        }
    }


    /**
     * 存款验证及提交
     */
    public static function memberCashDeposit($p){
        $biz_id = intval($p['biz_id']);
        $cashier_card_no = trim($p['cashier_card_no']);
        $cashier_key = trim($p['cashier_key']);
        $client_trade_pwd = trim($p['client_trade_pwd']);
        $card_no = trim($p['chief_teller_card_no']);
        $key = trim($p['chief_teller_key']);
        $member_image = $p['deposit_member_image'];

        $class_member_deposit_cash = new bizMemberDepositByCashClass(bizSceneEnum::COUNTER);

        $biz_info = $class_member_deposit_cash->getBizDetailById($biz_id);
        if( !$biz_info ){
            return new result(false,'Invalid biz :'.$biz_id);
        }

        //验证密码
//        $rt_1 =  $class_member_deposit_cash->checkMemberTradingPassword($biz_id, $client_trade_pwd);
//        if (!$rt_1->STS) {
//            return new result(false, 'Client Trading Password Error');
//        }
        $rt_2 =  $class_member_deposit_cash->checkTellerPassword($biz_id, $cashier_card_no, $cashier_key);
        if (!$rt_2->STS) {
            return new result(false, 'Teller Trading Password Error:'.$rt_2->MSG);
        }

        $is_ct_check = $class_member_deposit_cash->isNeedCTApprove($biz_id);
        if( $is_ct_check ){
            $rt_3 =  $class_member_deposit_cash->checkChiefTellerPassword($biz_id,$card_no,$key);
            if (!$rt_3->STS) {
                return new result(false, 'Chief Teller Trading Password Error:'.$rt_3->MSG);
            }
        }

        //启动事务
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {

            $rt = $class_member_deposit_cash->insertMemberInfo($biz_id,$member_image);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }

            $rt = $class_member_deposit_cash->bizSubmit($biz_id);
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, $rt->MSG);
            }
            $conn->submitTransaction();
            return new result(true, 'Deposit Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }


    /**
     * 创建取款biz
     */
    static function createMemberCashWithdrawal($p)
    {
        $cashier_id = intval($p['cashier_id']);
        $client_id = intval($p['client_id']);
        $amount = round($p['amount'], 2);
        $currency = trim($p['currency']);
        $remark = trim($p['remark']);

        if ($amount<=0) {
            return new result(false, "The amount must be greater than 0", null, errorCodesEnum::INVALID_PARAM);
        }

        $class_member_deposit_cash = new bizMemberWithdrawToCashClass(bizSceneEnum::COUNTER);
        $rt_1 = $class_member_deposit_cash->bizStart($client_id, $amount, $currency, $cashier_id, $remark);
        if ($rt_1->STS) {
            return new result(true, $rt_1->MSG,$rt_1->DATA['biz_id']);
        } else {
            return new result(false, $rt_1->MSG);
        }
    }

    /**
     * 取款验证及提交
     */
    public static function memberCashWithdrawal($p){
        $biz_id = intval($p['biz_id']);
        $cashier_card_no = trim($p['cashier_card_no']);
        $cashier_key = trim($p['cashier_key']);
        $client_trade_pwd = trim($p['client_trade_pwd']);
        $card_no = trim($p['chief_teller_card_no']);
        $key = trim($p['chief_teller_key']);
        $member_image = $p['withdraw_member_image'];

        //验证密码
        $class_member_deposit_cash = new bizMemberWithdrawToCashClass(bizSceneEnum::COUNTER);
        $rt_1 =  $class_member_deposit_cash->checkMemberTradingPassword($biz_id, $client_trade_pwd);
        if (!$rt_1->STS) {
            return new result(false, 'Client Trading Password Error:'.$rt_1->MSG);
        }
        $rt_2 =  $class_member_deposit_cash->checkTellerPassword($biz_id,$cashier_card_no,$cashier_key);
        if (!$rt_2->STS) {
            return new result(false, 'Teller Trading Password Error:'.$rt_2->MSG);
        }

        if( $class_member_deposit_cash->isNeedCTApprove($biz_id) ){
            $rt_3 =  $class_member_deposit_cash->checkChiefTellerPassword($biz_id,$card_no,$key);
            if (!$rt_3->STS) {
                return new result(false, 'Chief Teller Trading Password Error:'.$rt_3->MSG);
            }
        }


        //启动事务
        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {

            $rt = $class_member_deposit_cash->insertMemberInfo($biz_id,$member_image);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }

            $rt = $class_member_deposit_cash->bizSubmit($biz_id);
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, $rt->MSG);
            }

            $conn->submitTransaction();
            return new result(true, 'Withdrawal Successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

}