<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/22
 * Time: 23:53
 */
class bizCreditContractCancelClass extends bizBaseClass
{
    public function __construct()
    {

        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!',errorCodesEnum::FUNCTION_CLOSED);
        }
        $this->biz_code = bizCodeEnum::CANCEL_CREDIT_CONTRACT;
        $this->scene_code = bizSceneEnum::COUNTER;
        $this->bizModel = new biz_cancel_credit_contractModel();

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


    public function checkTellerPassword($biz_id,$card_no,$key)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $teller_id = $biz->cashier_id;
        $teller = new objectUserClass($teller_id);

        $branch_id = $teller->branch_id;
        $chk = $this->checkTellerAuth($teller_id,$branch_id,$card_no,$key);
        if( !$chk->STS ){
            return $chk;
        }
        $biz->update_time = Now();
        $biz->cashier_name = $teller->user_name;
        if( $biz->getRowState() == ormDataRow::ROWSTATE_MODIFIED ){
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Update fail.',null,errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true,'success',array(
            'biz_id' => $biz_id
        ));
    }

    public function checkChiefTellerPassword($biz_id,$card_no,$key)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $cashier_id = $biz->cashier_id;
        $cashierObj = new objectUserClass($cashier_id);

        $branch_id = $cashierObj->branch_id;
        $rt = $this->checkChiefTellerAuth($branch_id,$card_no,$key);
        if( !$rt->STS ){
            return $rt;
        }
        $ct_id = $rt->DATA;
        $ctObj = new objectUserClass($ct_id);
        $biz->bm_id = $ct_id;
        $biz->bm_name = $ctObj->user_name;
        $biz->update_time = Now();
        if( $biz->getRowState() == ormDataRow::ROWSTATE_MODIFIED ){
            $up = $biz->update();
            if( !$up->STS ){
                return new result(false,'Update chief teller info fail.',null,errorCodesEnum::DB_ERROR);
            }
        }

        return new result(true,'success',array(
            'biz_id' => $biz_id
        ));

    }

    public function execute($params)
    {
        $uid = intval($params['uid']);
        $cashier_id = intval($params['cashier_id']);
        $cashier_card_no = $params['cashier_card_no'];
        $cashier_card_key = $params['cashier_key'];
        $ct_card_no = $params['chief_teller_card_no'];
        $ct_card_key = $params['chief_teller_key'];
        $remark = $params['remark'];
        $return_fee_way = $params['return_fee_way'];

        $contract_info = (new member_authorized_contractModel())->getRow($uid);
        if( !$contract_info ){
            return new result(false,'No contract info:'.$uid,null,errorCodesEnum::NO_DATA);
        }

        if( !member_credit_grantClass::isAuthorisedContractCanCancel($contract_info) ){
            return new result(false,'Contract can not cancel.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        $member_id = $contract_info['member_id'];

        // 先检查身份
        $userObj = new objectUserClass($cashier_id);
        $chk = $userObj->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        // 检查teller
        $branch_id = $userObj->branch_id;
        $ck = $this->checkTellerAuth($cashier_id,$branch_id,$cashier_card_no,$cashier_card_key);
        if( !$ck->STS ){
            return $ck;
        }

        // 检查chief teller
        $ck = $this->checkChiefTellerAuth($branch_id,$ct_card_no,$ct_card_key);
        if( !$ck->STS ){
            return $ck;
        }

        $ct_id = $ck->DATA;
        $ctObj = new objectUserClass($ct_id);

        // 插入biz
        $m_biz = $this->bizModel;
        $biz = $m_biz->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->member_id = $member_id;
        $biz->contract_id = $contract_info['uid'];
        $biz->remark = $remark;
        $biz->state = bizStateEnum::CREATE;
        $biz->create_time = Now();
        $biz->cashier_id = $userObj->user_id;
        $biz->cashier_name = $userObj->user_name;
        $biz->bm_id = $ctObj->user_id;
        $biz->bm_name = $ctObj->user_name;
        $biz->branch_id = $branch_id;
        if( $return_fee_way == 1 ){
            $biz->return_fee_way = 1;  // cash
        }else{
            $biz->return_fee_way = 0;  // balance
        }
        $insert = $biz->insert();
        if( !$insert->STS ){
            return new result(false,'Insert info fail:'.$insert->MSG,null,errorCodesEnum::DB_ERROR);
        }

        $biz_id = $biz->uid;

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $rt = $this->bizConfirm($biz_id);
            if( !$rt->STS ){
                $conn->rollback();
            }else{
                $conn->submitTransaction();
            }
            $rt->DATA['biz_id'] = $biz_id;

            return $rt;

        }catch( Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNKNOWN_ERROR);
        }


    }


    public function bizConfirm($biz_id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'No biz info:'.$biz_id,null,errorCodesEnum::NO_DATA);
        }
        if( $biz->biz_code != $this->biz_code ){
            return new result(false,'Invalid type',null,errorCodesEnum::UN_MATCH_OPERATION);
        }
        if( $biz->state == bizStateEnum::DONE ){
            return new result(true,'success');
        }

        $contract_id = $biz->contract_id;
        $user_id = $biz->cashier_id;
        $rt = memberCreditContractClass::cancelCreditContract($contract_id,$biz->return_fee_way,array(
            'user_id' => $user_id
        ));
        return $rt;

    }

}