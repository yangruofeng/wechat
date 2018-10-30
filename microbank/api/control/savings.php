<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/13
 * Time: 10:14
 */

/** 储蓄账户操作类
 * Class savingsControl
 */
class savingsControl extends bank_apiControl
{

    public function requestWithdrawOp()
    {

        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        $member_id =  intval($params['member_id']);
        $amount = round($params['amount'],2);
        $currency = $params['currency'];
        $handler_id = $params['member_handler_id'];

        $remark = $params['remark'];
        if( !$member_id || $amount<=0 || !$currency ||  !$handler_id ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $handler_info = (new member_account_handlerModel())->getRow($handler_id);
        if( !$handler_info ){
            return new result(false,'No handler info',null,errorCodesEnum::NO_ACCOUNT_HANDLER);
        }

        switch( $handler_info->handler_type ){
            case memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY :
                $biz_class = new bizMemberWithdrawToPartnerClass(bizSceneEnum::APP_MEMBER);
                $rt = $biz_class->bizStart($member_id,$amount,$currency,$handler_id,$remark);
                return $rt;
                break;
            case memberAccountHandlerTypeEnum::BANK :
                $biz_class = new bizMemberWithdrawToBankClass(bizSceneEnum::APP_MEMBER);
                $rt = $biz_class->bizStart($member_id,$amount,$currency,$handler_id,$remark);
                return $rt;
                break;
            default:
                return new result(false,'Not support type!',null,errorCodesEnum::NOT_SUPPORTED);
        }


    }


    public function requestWithdrawSubmitOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        $biz_id = $params['biz_id'];
        $member_id = $params['member_id'];
        $request_time = $params['request_time'];
        $sign = trim($params['sign']);
        if( !$biz_id || !$member_id || !$sign ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_biz = new biz_member_withdrawModel();
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No withdraw biz info.',null,errorCodesEnum::NO_DATA);
        }


        // check trading password
        $biz_class = bizFactoryClass::getInstance(bizSceneEnum::APP_MEMBER,$biz->biz_code);
        $remark = 'Withdraw by member-app';
        $chk = $biz_class->checkMemberTradingPasswordSign($biz_id,$member_id,$request_time,$sign,$remark);

        if( !$chk->STS ){
            return $chk;
        }

        // 提交业务
        $rt = $biz_class->bizSubmit($biz_id);
        return $rt;


    }

    public function requestDepositOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        $member_id = $params['member_id'];
        $amount = round($params['amount'],2);
        $currency = $params['currency'];
        $handler_id = $params['member_handler_id'];
        $remark = $params['remark'];
        if( !$member_id || $amount<=0 || !$currency ||  !$handler_id ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $handler_info = (new member_account_handlerModel())->getRow($handler_id);
        if( !$handler_info ){
            return new result(false,'No handler info',null,errorCodesEnum::NO_ACCOUNT_HANDLER);
        }

        switch( $handler_info->handler_type ){
            case memberAccountHandlerTypeEnum::PARTNER_ASIAWEILUY :
                $biz_class = new bizMemberDepositByPartnerClass(bizSceneEnum::APP_MEMBER);
                $rt = $biz_class->bizStart($member_id,$amount,$currency,$handler_id,$remark);
                return $rt;
                break;
            case memberAccountHandlerTypeEnum::BANK :
                $system_bank_id = $params['system_bank_id'];
                $biz_class = new bizMemberDepositByBankClass(bizSceneEnum::APP_MEMBER);
                $rt = $biz_class->bizStart($member_id,$amount,$currency,$handler_id,$system_bank_id,$remark);
                return $rt;
                break;
            default:
                return new result(false,'Not support type!',null,errorCodesEnum::NOT_SUPPORTED);
        }


    }


    public function requestDepositSubmitOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        $biz_id = $params['biz_id'];
        $member_id = $params['member_id'];
        $request_time = $params['request_time'];
        $sign = trim($params['sign']);
        if( !$biz_id || !$member_id || !$sign ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $m_biz = new biz_member_depositModel();
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No withdraw biz info.',null,errorCodesEnum::NO_DATA);
        }
        // check trading password
        $biz_class = bizFactoryClass::getInstance(bizSceneEnum::APP_MEMBER,$biz->biz_code);
        $remark = 'Deposit by member-app';
        $chk = $biz_class->checkMemberTradingPasswordSign($biz_id,$member_id,$request_time,$sign,$remark);
        if( !$chk->STS ){
            return $chk;
        }

        // 提交业务
        $rt = $biz_class->bizSubmit($biz_id);
        return $rt;

    }

    public function transferToMemberOp()
    {
        $params = array_merge($_GET,$_POST);
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $from_member_id = $params['member_id'];
        $to_member_id = $params['to_member_id'];
        $amount = round($params['amount'],2);
        $currency = $params['currency'];
        $remark = $params['remark'];
        if( !$from_member_id || !$to_member_id || $amount<=0 || !$currency ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $biz_class = new bizMemberToMemberClass(bizSceneEnum::APP_MEMBER);
        $rt = $biz_class->bizStart($from_member_id,$to_member_id,$amount,$currency,$remark);
        return $rt;

    }


    public function memberTransferSubmitOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        $biz_id = $params['biz_id'];
        $member_id = $params['member_id'];
        $request_time = $params['request_time'];
        $sign = trim($params['sign']);
        if( !$biz_id || !$member_id || !$sign ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_biz = new biz_member_transferModel();
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No withdraw biz info.',null,errorCodesEnum::NO_DATA);
        }
        // check trading password
        $biz_class = bizFactoryClass::getInstance(bizSceneEnum::APP_MEMBER,$biz->biz_code);
        $remark = 'Transfer by member-app';
        $chk = $biz_class->checkMemberTradingPasswordSign($biz_id,$member_id,$request_time,$sign,$remark);
        if( !$chk->STS ){
            return $chk;
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{


            // 提交业务
            $rt = $biz_class->bizSubmit($biz_id);
            if( !$rt->STS ){
                $conn->rollback();
                return $rt;
            }
            $conn->submitTransaction();
            return $rt;

        }catch( Exception $e)
        {
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }


    }

    public function memberTransferRecentlyHistoryOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        $member_id = $params['member_id'];
        $list =  member_savingsClass::getMemberDistinctRecentlyTransfer($member_id);
        return new result(true,'success',array(
            'list' => $list
        ));
    }


    public function memberTransferRecentlySearchOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        $member_id = $params['member_id'];
        $keywords = $params['keywords'];
        $list = member_savingsClass::getMemberRecentlyTransferKeywordSearchList($member_id,$keywords);
        return new result(true,'success',array(
            'transfer_list' => $list
        ));
    }


    public function getMemberBillListOp()
    {

        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        return member_savingsClass::getMemberBillList($params);

    }

    public function getBillDetailOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        $bill_id = $params['bill_id'];
        return member_savingsClass::getMemberBillFlowDetailById($bill_id);

    }


    public function getMemberBillOfDayTypeOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        return member_savingsClass::getMemberBillListGroupByDayAndType($params);
    }


    public function getMemberTransactionListOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        return member_savingsClass::getMemberBillTransaction($params);
    }




}