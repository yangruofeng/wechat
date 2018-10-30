<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/8/1
 * Time: 14:13
 */
class bizBranchExchangeClass extends bizBaseClass
{
    public function __construct($scene_code)
    {
        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!');
        }

        $this->scene_code = $scene_code;
        $this->biz_code = bizCodeEnum::BRANCH_EXCHANGE;
        $this->bizModel = new biz_obj_exchangeModel();
    }

    public function checkBizOpen()
    {
        return new result(true);
    }

    public function getBizDetailById($id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->find(array(
            'uid' => $id
        ));
        return $biz;
    }


    public function getExchangeHistory($branch_id,$page_number,$page_size)
    {
        $m = new site_branchModel();
        $branch_info = $m->getBranchInfoById($branch_id);
        $sql = " select * from biz_obj_exchange where obj_guid=".qstr($branch_info['obj_guid'])." and 
        state=".qstr(bizStateEnum::DONE)." order by uid desc ";
        return $m->reader->getPage($sql,$page_number,$page_size);
    }

    public function bizStart($branch_id,$user_id,$amount,$from_currency,$to_currency,$exchange_rate,$remark=null)
    {
        $m_biz = $this->bizModel;

        $userObj = new objectUserClass($user_id);
        $chk = $userObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        $amount = round($amount,2);
        if( $amount <= 0 ){
            return new result(false,'Invalid amount:'.$amount,null,errorCodesEnum::INVALID_PARAM);
        }

        $exchange_rate = round($exchange_rate,10);
        if( bccomp($exchange_rate,0,10) <= 0 ){
            return new result(false,'Invalid exchange rate:'.$exchange_rate,null,errorCodesEnum::INVALID_PARAM);
        }

        if( $from_currency == $to_currency ){
            return new result(false,'Exchange to same currency.',null,errorCodesEnum::INVALID_PARAM);
        }

        $branchObj = new objectBranchClass($branch_id);
        $branch_passbook = $branchObj->getPassbook();
        $branch_balance = $branch_passbook->getAccountBalance();

        // 需要检查余额,浮点数的比较
        if( bccomp($branch_balance[$from_currency],$amount,2) < 0 ){
            return new result(false,'Balance not enough.',null,errorCodesEnum::BALANCE_NOT_ENOUGH);
        }

        $exchange_amount = round($amount*$exchange_rate,2);

        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->obj_type = $branchObj->object_type;
        $biz->obj_guid = $branchObj->object_id;
        $biz->handler_obj_type = $userObj->object_type;
        $biz->handler_obj_guid = $userObj->object_id;
        $biz->handler_name = $userObj->user_name;
        $biz->amount = $amount;
        $biz->from_currency = $from_currency;
        $biz->to_currency = $to_currency;
        $biz->exchange_rate = $exchange_rate;
        $biz->exchange_amount = $exchange_amount;
        $biz->remark = $remark;
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $insert = $biz->insert();
        if( !$insert->STS ){
            return new result(false,'Insert biz fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'biz_id' => $biz->uid
        ));


    }


    public function checkUserTradingPassword($biz_id,$password)
    {
        $biz = $this->bizModel->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid biz id:'.$biz_id,null,errorCodesEnum::INVALID_PARAM);
        }

        $info = (new um_userModel())->getUserInfoByGuid($biz->handler_obj_guid);
        if( $info['trading_password'] != md5($password) ){
            return new result(false,'Trading password error.',null,errorCodesEnum::PASSWORD_ERROR);
        }
        return new result(true,'success',array(
            'biz_id' => $biz_id
        ));
    }


    public function bizConfirm($biz_id)
    {
        $biz = $this->bizModel->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid biz id:'.$biz_id,null,errorCodesEnum::INVALID_PARAM);
        }

        if( $biz->state == bizStateEnum::DONE ){
            return new result(true,'success');
        }

        if( $biz->biz_code != $this->biz_code ){
            return new result(false,'Invalid biz code.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        if( $biz->obj_type != objGuidTypeEnum::SITE_BRANCH ){
            return new result(false,'Invalid obj type.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }
        $branch_info = (new site_branchModel())->getBranchInfoByGUID($biz->obj_guid);
        $branchObj = new objectBranchClass($branch_info['uid']);
        $chk = $branchObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }


        $branch_passbook = $branchObj->getPassbook();
            $remark = $biz->remark;
            $trading = new exchangeTradingClass($branch_passbook,$biz->amount,$biz->from_currency,$biz->to_currency,$biz->exchange_rate);
            $trading->remark = $remark;
            $rt = $trading->execute();
            if( !$rt->STS ){
                $biz->state = bizStateEnum::FAIL;
            $biz->update_time = Now();
            $biz->update();
            return $rt;
        }

        $trade_id = intval($rt->DATA);
        $biz->state = bizStateEnum::DONE;
        $biz->exchange_amount = $trading->exchange_to_amount;
        $biz->update_time = Now();
        $biz->passbook_trading_id = $trade_id;
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Update biz fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success');

    }



}