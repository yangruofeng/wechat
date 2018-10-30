<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/23
 * Time: 13:55
 */

class bizMemberWithdrawToCashClass extends bizMemberWithdrawClass
{
    public function __construct($scene_code)
    {
        parent::__construct($scene_code);

        $this->biz_code = bizCodeEnum::MEMBER_WITHDRAW_TO_CASH;
        $this->scene_code = $scene_code;

    }



    public function getFee($amount)
    {
        return $amount*0;
    }

    // 检查交易场景限制
    public  function checkLimit($branch_id,$member_id,$amount,$currency)
    {
        switch($this->scene_code )
        {
            case bizSceneEnum::APP_MEMBER:
                return new result(false,'Member app not support withdraw cash.',null,errorCodesEnum::NOT_SUPPORTED);
                break;
            case bizSceneEnum::APP_CO :
                return new result(false,'CO app not support withdraw cash.',null,errorCodesEnum::NOT_SUPPORTED);
                break;
            case bizSceneEnum::COUNTER :

                // 检查分行限制
                $chk = $this->checkBranchDayLimit($branch_id,$amount,$currency);
                if( !$chk->STS ){
                    return $chk;
                }

                // 检查member限制
                $chk = $this->checkMemberLimit($member_id,$amount,$currency);
                if( !$chk->STS ){
                    return $chk;
                }

                return new result(true);
                break;
            case bizSceneEnum::BACK_OFFICE :
                return new result(false,'Back office not support withdraw cash.',null,errorCodesEnum::NOT_SUPPORTED);
                break;
            default:
                return new result(false,'Not support scene.',null,errorCodesEnum::NOT_SUPPORTED);
        }

    }

    public function checkMemberTradingPassword($biz_id, $password)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $member_id = $biz->member_id;
        $objectMember = new objectMemberClass($member_id);
        //$chk = $objectMember->checkTradingPassword($password,'Withdraw to cash',true);
        // 柜台是两次md5加密
        if( $password != md5($objectMember->trading_password) ){
            return new result(false,'Password error',null,errorCodesEnum::PASSWORD_ERROR);
        }

        $biz->member_trading_password = $objectMember->trading_password;
        $biz->update_time = Now();
        $up = $biz->update();

        return new result(true,'success');

    }



    // 业务开始
    public  function bizStart($memberId,$amount,$currency,$cashierId,$remark)
    {

        // 检查余额
        $objectMember = new objectMemberClass($memberId);

        $chk = $objectMember->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        // 是否在黑名单
        $black_list = $objectMember->getBlackList();
        if( in_array(blackTypeEnum::WITHDRAW,$black_list) ){
            return new result(false,'Member is in black list for withdraw.',null,errorCodesEnum::MEMBER_IS_IN_BLACK_LIST);
        }

        $cny_balance = $objectMember->getSavingsAccountBalance();
        if( $cny_balance[$currency] < $amount ){
            return new result(false,'Member balance not enough.',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
        }

        $cashierObj = new objectUserClass($cashierId);
        $chk = $cashierObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        $branch_id = $cashierObj->branch_id;

        // 检查场景限制
        $chk = $this->checkLimit($branch_id,$memberId,$amount,$currency);
        if( !$chk->STS ){
            return $chk;
        }

        // 检查cashier余额
        $cashierBalance = $cashierObj->getPassbookBalance();
        if( $cashierBalance[$currency] < $amount ){
            return new result(false,'Cashier balance not enough.',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
        }

        $m_biz = $this->bizModel;
        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->member_id = $memberId;
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->cashier_id = $cashierId;
        $biz->fee = $this->getFee($amount);
        $biz->remark = $remark;
        $biz->scene_code = $this->scene_code;
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $biz->branch_id = $branch_id;
        $insert = $biz->insert();
        if( !$insert->STS  ){
            return new result(false,'Fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'biz_id' => $biz->uid
        ));

    }



    // 业务提交
    public  function bizSubmit($biz_id)
    {

        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        if( $biz->state == bizStateEnum::DONE ){
            return new result(true,'success',$biz);
        }

        $member_id = $biz->member_id;
        $memberObj = new objectMemberClass($member_id);
        $cashier_id = $biz->cashier_id;
        $amount = $biz->amount;
        $currency = $biz->currency;
        $trading_password = $memberObj->trading_password;

        $userObj = new objectUserClass($cashier_id);

        $branchObj = new objectBranchClass($userObj->branch_id);

        //$remark = "Counter:branch ".$branchObj->branch_name.',cashier '.$userObj->user_name;

        $remark = $biz->remark;

        $re = passbookWorkerClass::memberWithdrawToCash($member_id,$trading_password,$cashier_id,$amount,$currency,$remark);
        if( !$re->STS ){
            $biz->update_time = Now();
            $biz->state = bizStateEnum::FAIL;
            $biz->update();
            return $re;
        }else{

            $trade_id = $re->DATA;
            $biz->state = bizStateEnum::DONE;
            $biz->update_time = Now();
            $biz->passbook_trading_id = $trade_id;
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Update biz fail',null,errorCodesEnum::DB_ERROR);
            }
        }

        $title = "Withdraw cash";
        $subject = "Withdraw cash(Counter): $amount".$currency;
        member_messageClass::sendSystemMessage($member_id,$title,$subject);

        return new result(true,'success',$biz);


    }

}
