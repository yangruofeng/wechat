<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/23
 * Time: 9:34
 */
class bizMemberDepositByCashClass extends bizMemberDepositClass
{

    public function __construct($scene_code)
    {
        parent::__construct($scene_code);
        $this->biz_code = bizCodeEnum::MEMBER_DEPOSIT_BY_CASH;
        $this->scene_code = $scene_code;
    }






    /**
     * @return result
     */
    protected function checkLimit($branch_id,$member_id,$amount,$currency)
    {
        switch( $this->scene_code )
        {
            case bizSceneEnum::APP_MEMBER:
                return new result(false,'Member app not supported cash deposit.',null,errorCodesEnum::NOT_SUPPORTED);
            case bizSceneEnum::APP_CO :
                return new result(false, 'Member deposit cash is not supported by APP_CO.', null, errorCodesEnum::NOT_SUPPORTED);
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

            case bizSceneEnum::BACK_OFFICE :
                return new result(false, 'Member deposit cash  is not supported by back office.', null, errorCodesEnum::NOT_SUPPORTED);
            default:
                return new result(false, 'Unknown scene', null, errorCodesEnum::NOT_SUPPORTED);
        }
    }


    public function checkMemberTradingPassword($biz_id, $password)
    {
        // 重写
        return new result(true,'success',array(
            'biz_id' => $biz_id
        ));
    }


    public function bizStart($member_id,$amount,$currency,$cashier_id,$remark)
    {


        $memberObj = new objectMemberClass($member_id);
        $chk = $memberObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        // 是否在黑名单
        $black_list = $memberObj->getBlackList();
        if( in_array(blackTypeEnum::DEPOSIT,$black_list) ){
            return new result(false,'Member is in black list for deposit.',null,errorCodesEnum::MEMBER_IS_IN_BLACK_LIST);
        }

        $userObj = new objectUserClass($cashier_id);
        $chk = $userObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        // 需要检查信用限制
        $credit = $userObj->getCredit();
        if( $credit > 0 ){
            $cashier_balance = $userObj->getPassbookBalance();
            // 换算信用
            $total_amount = system_toolClass::convertMultiCurrencyAmount($cashier_balance,currencyEnum::USD);
            if( $total_amount >= $credit ){
                return new result(false,'Cashier balance out of credit:'.$credit,null,errorCodesEnum::OUT_OF_ACCOUNT_CREDIT);
            }
        }

        $branch_id = $userObj->branch_id;

        // 检查场景限制
        $chk = $this->checkLimit($branch_id,$member_id,$amount,$currency);
        if( !$chk->STS ){
            return $chk;
        }


        $m_biz = $this->bizModel;
        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->member_id = $member_id;
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->fee = 0;
        $biz->cashier_id = $cashier_id;
        $biz->remark = $remark;
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $biz->branch_id = $branch_id;
        $insert = $biz->insert();
        if( !$insert->STS  ){
            return new result(false,'Fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'biz_id' => $biz->uid,
        ));

    }




    public function bizSubmit($biz_id)
    {
        $m = $this->bizModel;
        $biz = $m->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info.',null,errorCodesEnum::NO_DATA);
        }
        if( $biz->state == bizStateEnum::DONE ){
            return new result(true,'success',$biz);
        }

        $member_id = $biz->member_id;
        $cashier_id = $biz->cashier_id;
        $amount = $biz->amount;
        $currency = $biz->currency;

        $userObj = new objectUserClass($cashier_id);

        $branchObj = new objectBranchClass($userObj->branch_id);

        //$remark = $biz->remark?:"Counter:branch ".$branchObj->branch_name.',cashier '.$userObj->user_name;

        $remark = $biz->remark;

        // 账本处理
        $rt = passbookWorkerClass::memberDepositByCash($member_id,$cashier_id,$amount,$currency,$remark);
        if( !$rt->STS ){
            $biz->state = bizStateEnum::FAIL;
            $biz->update_time = Now();
            $biz->update();
            return $rt;
        }

        $trade_id = intval($rt->DATA);
        $biz->state = bizStateEnum::DONE;
        $biz->update_time = Now();
        $biz->passbook_trading_id = $trade_id;
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Deposit success,update biz fail!',null,errorCodesEnum::DB_ERROR);
        }

        $title = "Deposit by cash";
        $subject = "Deposit in $amount".$currency." by cash(Counter).";
        member_messageClass::sendSystemMessage($member_id,$title,$subject);

        return new result(true,'success',$biz);
    }


}