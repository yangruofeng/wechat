<?php

class bizMemberPurchaseSavingsProductClass extends bizBaseClass
{
    public function __construct($scene_code)
    {
        $is_open = $this->checkBizOpen();
        if (!$is_open->STS) {
            throw new Exception('Function close!');
        }
        $this->scene_code = $scene_code;
        $this->biz_code = bizCodeEnum::MEMBER_PURCHASE_SAVINGS_PRODUCT;
        $this->bizModel = new biz_member_purchase_savings_productModel();
    }

    public function checkBizOpen()
    {
        return new result(true);
    }

    function getBizDetailById($id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->find(array(
            'uid' => $id
        ));
        return $biz;
    }

    public function bizStart($memberId,$amount,$currency,$termOpts,$productOpts) {
        $memberObj = new objectMemberClass($memberId);
        $chk = $memberObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        // 是否在黑名单
        $black_list = $memberObj->getBlackList();
        if( in_array(blackTypeEnum::SAVINGS,$black_list) ){
            return new result(false,'Member is in black list for savings.',null,errorCodesEnum::MEMBER_IS_IN_BLACK_LIST);
        }

        $m_biz = $this->bizModel;
        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->member_id = $memberId;
        $biz->product_id = $productOpts['product_id'];
        $biz->category_id = $productOpts['category_id'];
        $biz->amount = $amount;
        $biz->currency = $currency;
        $biz->term = $termOpts['term'];
        $biz->end_date = $termOpts['end_date'];
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $insert = $biz->insert();
        if( !$insert->STS  ){
            return new result(false,'Fail',null,errorCodesEnum::DB_ERROR);
        }

        // 创建合同
        $ret = memberSavingsV2Class::createContract($memberId, $amount, $currency, $termOpts, $productOpts, array(
            'source' => $this->scene_code
        ));
        if (!$ret->STS) return $ret;
        $result_data = $ret->DATA;

        // 更新合同号
        $biz->contract_id = $ret->DATA['uid'];
        $up_rt = $biz->update();
        if (!$up_rt->STS) {
            return new result(false, 'Update contract id failed - ' . $up_rt->MSG, null, errorCodesEnum::DB_ERROR);
        }

        $result_data['biz_id'] = $biz->uid;
        return new result(true,'success',$result_data);
    }

    public function checkMemberTradingPasswordSign($biz_id, $member_id, $time, $sign, $remark = '')
    {
        $ret = parent::checkMemberTradingPasswordSign($biz_id, $member_id, $time, $sign, $remark);
        if (!$ret->STS) return $ret;

        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        $biz->verify_sign = $sign;
        $biz->update_time = Now();
        $up = $biz->update();
        if ($up->STS)
            return new result(true);
        else
            return new result(false, 'Update verify sign failed', null, errorCodesEnum::DB_ERROR);
    }

    public function bizSubmit($biz_id) {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        if( $biz->state == bizStateEnum::DONE ){
            return new result(true,'success',$biz);
        }
        if( $biz->state != bizStateEnum::CREATE ){
            return new result(false,'Invalid state.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }
        if (!$biz->verify_sign) {
            return new result(false, 'Unverified', null, errorCodesEnum::UNAUTHORIZED);
        }
        if (!$biz->contract_id) {
            return new result(false, 'No contract', null, errorCodesEnum::UNEXPECTED_DATA);
        }

        // 更新状态
        $biz->state = bizStateEnum::PENDING_CHECK;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Update biz fail',null,errorCodesEnum::DB_ERROR);
        }

        $re = memberSavingsV2Class::confirmContractBegin($biz->contract_id);
        if (!$re->STS) {
            $biz->state = bizStateEnum::FAIL;
            $biz->update_time = Now();
            $biz->update();
            return $re;
        }

        $contract_info = $re->DATA;
        $member_id = $biz->member_id;
        $amount = $contract_info['amount'];
        $currency = $contract_info['currency'];
        $purchase_fee = $contract_info['purchase_fee'];
        $product_id = $contract_info['product_id'];

        $re = passbookWorkerClass::memberPurchaseSavingsProduct($member_id,$product_id,$amount,$purchase_fee,$currency);
        $trade_id = intval($re->DATA['trade_id']);
        $biz->passbook_trading_id  = $trade_id;
        if( !$re->STS ){
            if( $re->CODE != errorCodesEnum::UNKNOWN_ERROR ){
                $biz->state = bizStateEnum::FAIL;
            }
            $biz->update_time = Now();
            $biz->update();
            return $re;
        }

        $re = memberSavingsV2Class::confirmContractFinish($biz->contract_id, $trade_id);
        if (!$re->STS) {
            return $re;
        } else {
            $biz->update_time = Now();
            $biz->state = bizStateEnum::DONE;
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Update biz fail',null,errorCodesEnum::DB_ERROR);
            }
        }

        $result_data = $re->DATA;
        $result_data['biz_id'] = $biz->uid;
        return new result(true,'success',$result_data);
    }
}