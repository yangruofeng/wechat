<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/22
 * Time: 16:34
 */

class bizMemberDepositByBankClass extends bizMemberDepositClass
{

    public function __construct($scene_code)
    {
        throw new Exception('Not support yet.',errorCodesEnum::NOT_SUPPORTED);
        parent::__construct($scene_code);
        $this->biz_code = bizCodeEnum::MEMBER_DEPOSIT_BY_BANK;
        $this->scene_code = $scene_code;
    }




    /**
     * @return result
     */
    protected function checkLimit($member_id,$amount,$currency)
    {
        switch( $this->scene_code )
        {
            case bizSceneEnum::APP_MEMBER:

                // 检查member限制
                $chk = $this->checkMemberLimit($member_id,$amount,$currency);
                if( !$chk->STS ){
                    return $chk;
                }
                return new result(true);

            case bizSceneEnum::APP_CO :
                return new result(false, 'Deposit by bank is not supported by APP_CO', null, errorCodesEnum::NOT_SUPPORTED);
            case bizSceneEnum::COUNTER :

                return new result(false, 'Deposit by bank is not supported by counter.', null, errorCodesEnum::NOT_SUPPORTED);

            case bizSceneEnum::BACK_OFFICE :
                return new result(false, 'Deposit by bank is not supported by BACKOFFICE', null, errorCodesEnum::NOT_SUPPORTED);
            default:
                return new result(false, 'Unknown scene', null, errorCodesEnum::NOT_SUPPORTED);
        }
    }


    public function bizStart($member_id,$amount,$currency,$memberAccountId,$system_bank_id,$remark)
    {
        $limit = $this->getLimit();
        if( $limit && $amount > $limit ){
            return new result(false,'Invalid amount',null,errorCodesEnum::INVALID_AMOUNT);
        }

        $chk = $this->checkLimit($amount,$currency);
        if( !$chk->STS ){
            return $chk;
        }

        $memberObj = new objectMemberClass($member_id);
        $rt = $memberObj->checkValid();
        if( !$rt->STS ){
            return new $rt;
        }

        // 是否在黑名单
        $black_list = $memberObj->getBlackList();
        if( in_array(blackTypeEnum::DEPOSIT,$black_list) ){
            return new result(false,'Member is in black list for deposit.',null,errorCodesEnum::MEMBER_IS_IN_BLACK_LIST);
        }

        $m_site_bank = new site_bankModel();
        $system_bank = $m_site_bank->getRow($system_bank_id);
        if( !$system_bank ){
            return new result(false,'Not found system bank',null,errorCodesEnum::NO_DATA);
        }

        $m_biz = $this->bizModel;
        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->member_id = $member_id;
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->fee = 0;
        $biz->member_handler_id = $memberAccountId;
        $biz->bank_id = $system_bank_id;
        $biz->bank_code = $system_bank->bank_code;
        $biz->bank_name = $system_bank->bank_name;
        $biz->remark = $remark;
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $insert = $biz->insert();
        if( !$insert->STS  ){
            return new result(false,'Fail',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'biz_id' =>  $biz->uid
        ));
    }


    public function bizSubmit($biz_id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid biz id',null,errorCodesEnum::INVALID_PARAM);
        }
        $member_id = $biz->member_id;
        $amount = $biz->amount;
        $currency = $biz->currency;
        $bankAccountId = $biz->bank_id;
        $bank_info = member_handlerClass::getHandlerInfoById($bankAccountId);

        $rt = passbookWorkerClass::memberDepositByBank($member_id,$bankAccountId,$amount,$currency);
        if( !$rt->STS ){
            $biz->state = bizStateEnum::FAIL;
            $biz->update_time = Now();
            $biz->update();
            return $rt;
        }else{

            $biz->state = bizStateEnum::DONE;
            $biz->update_time = Now();
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Handle fail',null,errorCodesEnum::DB_ERROR);
            }

            $title = "Deposit by bank";
            $subject = "Deposit in $amount".$currency." by bank(".substr($bank_info['handler_account'],-4).").";
            member_messageClass::sendSystemMessage($member_id,$title,$subject);
            return new result(true,'success');
        }

    }


}