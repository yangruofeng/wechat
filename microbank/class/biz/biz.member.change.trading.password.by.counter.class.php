<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/24
 * Time: 18:24
 */
class bizMemberChangeTradingPasswordByCounterClass extends bizBaseClass
{

    public function __construct()
    {

        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!');
        }
        $this->scene_code = bizSceneEnum::COUNTER;
        $this->biz_code = bizCodeEnum::MEMBER_CHANGE_TRADING_PASSWORD_BY_COUNTER;
        $this->bizModel = new biz_member_change_passwordModel();
    }

    public function getBizDetailById($id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->find(array(
            'uid' => $id
        ));
        return $biz;
    }

    public function checkBizOpen()
    {
        return new result(true);
    }

    public function execute($user_id,$member_id,$member_image,$new_password,$fee,$currency,$payment_way)
    {
        $new_password = trim($new_password);
        $payment_way = intval($payment_way);
        $fee = round($fee,2);
        $userObj = new objectUserClass($user_id);
        $chk = $userObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        $member = (new memberModel())->getRow($member_id);
        if( !$member ){
            return new result(false,'Member not exists.',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $memberObj = new objectMemberClass($member_id);

        // 检查验证码
//        $m_verify_code = new phone_verify_codeModel();
//        $chk = $m_verify_code->verifyCode($sms_id,$sms_code);
//        if( !$chk->STS ){
//            return $chk;
//        }

        $biz = $this->bizModel->newRow();
        $biz->scene_code = $this->scene_code;
        $biz->biz_code = $this->biz_code;
        $biz->member_id = $member_id;
        $biz->member_image = $member_image;
        $biz->pwd_type = passwordTypeEnum::TRADING_PASSWORD;
        $biz->fee = $fee;
        $biz->currency = $currency;
        $biz->payment_way = intval($payment_way);
        $biz->state = bizStateEnum::CREATE;
        $biz->operator_id = $userObj->user_id;
        $biz->operator_name = $userObj->user_name;
        $biz->create_time = Now();
        $biz->update_time = Now();
        $biz->branch_id = $userObj->branch_id;
        $insert = $biz->insert();
        if( !$insert->STS ){
            return new result(false,'Insert biz fail.'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        // 扣款
        $remark = 'Client change trading password ';
        $trade_id = null;
        if( $fee > 0 ){
            switch ($payment_way){
                case repaymentWayEnum::CASH:
                    $rt = passbookWorkerClass::otherIncomeByCash($userObj->user_id,$fee,$currency,$remark);
                    if( !$rt->STS ){
                        return $rt;
                    }
                    $trade_id = intval($rt->DATA);
                    break;
                case repaymentWayEnum::PASSBOOK:


                    // 自动从有钱账户扣除
                    $member_balance = $memberObj->getSavingsAccountBalance();
                    // 计算扣款明细
                    $paid_currency_amount = system_toolClass::calMultiCurrencyDeductForSingleCurrencyAmount($fee,$currency,$member_balance);
                    if( !$paid_currency_amount->STS  ){
                        return new result(false,'Balance not enough.',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
                    }

                    $paid_currency_amount = $paid_currency_amount->DATA['multi_currency'];
                    foreach( $paid_currency_amount as $c=>$a ){
                        $rt = passbookWorkerClass::otherIncomeByClientBalance($member_id,$a,$c,$remark);
                        if( !$rt->STS ){
                            return $rt;
                        }
                    }

                    break;
                default:
                    return new result(false,'Not support repayment way.',null,errorCodesEnum::NOT_SUPPORTED);
                    break;
            }
        }


        // 更新新密码
        $member->trading_password = $new_password;
        $member->update_time = Now();
        $up = $member->update();
        if( !$up->STS ){
            return new result(false,'Update member password fail.'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }


        if( $trade_id ){
            $biz->passbook_trading_id = $trade_id;
        }
        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Update biz fail.'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        $biz->biz_id = $biz->uid;

        return new result(true,'success',$biz);

    }

}