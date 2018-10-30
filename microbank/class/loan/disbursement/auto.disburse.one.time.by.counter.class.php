<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/9/28
 * Time: 11:49
 */
class autoDisburseOneTimeByCounterClass
{

    public function loanPreview($auth_contract_id,$user_id)
    {
        $auth_contract = (new member_authorized_contractModel())->find(array(
            'uid' => $auth_contract_id
        ));

        if( !$auth_contract ){
            return new result(false,'No auth contract info:'.$auth_contract_id,null,errorCodesEnum::NO_DATA);
        }

        $member_id = $auth_contract['member_id'];

        $bizClass = new bizOneTimeCreditLoanClass();

        // 任务列表
        $list = $bizClass->getMemberOneTimeLoanList($member_id);
        if( empty($list) ){
            return new result(false,'No one time product can loan.',null,errorCodesEnum::NO_DATA);
        }

        // 过滤此次授信合同的产品
        $credit_grant_id = $auth_contract['grant_credit_id'];
        $grant_product = (new member_credit_grant_productModel())->select(array(
            'grant_id' => $credit_grant_id
        ));

        $grant_member_category = array();
        foreach( $grant_product as $v ){
            $grant_member_category[] = $v['member_credit_category_id'];
        }


        $biz_ids = array();

        $category_contract = array();

        foreach( $list as $item ){


            $member_category_id = $item['member_credit_category_id'];
            if( !in_array($member_category_id,$grant_member_category) ){
                continue;
            }

            $contract_result = array();

            foreach( $item['contract_param'] as $k=>$v ){
                $currency = $v['currency'];

                $result_data = array();
                // 插入放款任务不放在事务内，可以柜台人工再去执行
                $rt = $bizClass->insertLoanTask($user_id,$member_id,$member_category_id,$currency);
                if( !$rt->STS ){
                    $result_data = array(
                        'is_error' => 1,
                        'error_msg' => $rt->MSG,
                        'contract_param' => $v,
                    );

                }else{

                    $biz_id = $rt->DATA['biz_id'];
                    // 开始放款，内部有事务
                    $rt = $bizClass->disburseStart($biz_id);
                    if( !$rt->STS ){
                        $result_data = array(
                            'is_error' => 1,
                            'error_msg' => $rt->MSG,
                            'contract_param' => $v,
                        );

                    }else{

                        $biz_ids[] = $biz_id;
                        $result_data = array(
                            'is_error' => 0,
                            'error_msg' => $rt->MSG,
                            'contract_param' => $v,
                            'contract_data' => array(
                                'contract_id' => $rt->DATA['contract_id'],
                                'contract_sn' => $rt->DATA['contract_sn']
                            ),
                            'biz_id' => $biz_id
                        );
                    }
                    $contract_result[$currency] = $result_data;

                }
                $contract_result[$currency] = $result_data;

            }

            $item['contract_result'] = $contract_result;
            $category_contract[] = $item;
        }

        return new result(true,'success',array(
            'biz_ids' => $biz_ids,
            'category_contract' => $category_contract
        ));

    }


    public function loanCancel($biz_ids)
    {
        if( empty($biz_ids) ){
            return new result(true,'success');
        }
        $bizClass = new bizOneTimeCreditLoanClass();
        foreach( $biz_ids as $biz_id ){
            $rt = $bizClass->disburseCancel($biz_id);
        }
        return new result(true,'success');

    }


    public function loanConfirm($biz_ids)
    {
        if( empty($biz_ids) ){
            return new result(false,'Empty loan biz params.',null,errorCodesEnum::INVALID_PARAM);
        }

        $bizClass = new bizOneTimeCreditLoanClass();

        $data = array(
            'total' => count($biz_ids),
            'loan_success' => 0,
            'loan_fail' => 0,
            'disburse_success' => 0,
            'disburse_fail' => 0,
            'detail_info' => ''
        );

        $have_success = false;

        foreach( $biz_ids as $biz_id){

            $rt = $bizClass->disburseConfirm($biz_id,true);

            if( !$rt->STS ){
                $data['loan_fail'] += 1;
            }else{
                $have_success = true;
                $data['loan_success'] += 1;
                if( $rt->DATA['is_disburse_success']){
                    $data['disburse_success'] += 1;
                }else{
                    $data['disburse_fail'] += 1;
                }
            }
        }

        if( $have_success ){
            $msg = "Total contracts: <kbd>".$data['total']."</kbd><br/>";
            $msg .= "Success contracts: <kbd>".$data['loan_success']."</kbd>,Fail contracts: <kbd>".$data['loan_fail']."</kbd><br>";
            $msg .= "Disburse success: <kbd>".$data['disburse_success']."</kbd>,Disburse fail: <kbd>".$data['disburse_fail']."</kbd>";
            return new result(true,$msg,$data);
        }else{
            return new result(false,'No any contract disburse success!',$data);
        }



    }


    public function withdraw($member_id,$user_id,$currency_amount)
    {

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try{

            $biz_ids = array();
            $remark = 'Client loan withdraw.';
            $bizClass = new bizMemberWithdrawToCashClass(bizSceneEnum::COUNTER);
            foreach( $currency_amount as $c=>$a ){

                if( $a > 0 ){

                    $rt = $bizClass->bizStart($member_id,$a,$c,$user_id,$remark);
                    if( !$rt->STS ){
                        $conn->rollback();
                        return $rt;
                    }

                    $biz_id = $rt->DATA['biz_id'];
                    $rt = $bizClass->bizSubmit($biz_id);
                    if( !$rt->STS ){
                        $conn->rollback();
                        return $rt;
                    }
                    $biz_ids[] = $biz_id;

                }
            }
            $conn->submitTransaction();
            return new result(true,'success',array(
                'biz_ids' => $biz_ids
            ));

        }catch (Exception $e ){
            $conn->rollback();
            return new result(false,$e->getMessage(),null,errorCodesEnum::UNEXPECTED_DATA);
        }
    }





}