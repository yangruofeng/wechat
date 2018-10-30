<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/23
 * Time: 12:29
 */
class memberCreditContractClass
{

    /** 取消授权合同
     * @param $contract_id
     * @param array $params
     * @return result
     */
    public static function cancelCreditContract($contract_id,$return_fee_way,$params=array())
    {
        // todo 没有考虑那种一次授信多个合同的情况
        $contract_id = intval($contract_id);
        $m = new member_authorized_contractModel();
        $auth_contract = $m->getRow($contract_id);
        if( !$auth_contract ){
            return new result(false,'No credit contract:'.$contract_id);
        }

        if( $auth_contract->state == authorizedContractStateEnum::CANCEL ){
            return new result(true,'success');
        }

        $member_id = $auth_contract['member_id'];
        $grant_id = $auth_contract['grant_credit_id'];


        // 先将合同变为锁住
        $auth_contract->state = authorizedContractStateEnum::LOCK;
        $auth_contract->update_time = Now();
        $up = $auth_contract->update();
        if( !$up->STS ){
            return new result(false,'Update contract fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        // 清空信用
        // 查找合同的各项信用
        $m_grant_product = new member_credit_grant_productModel();
        $list = $m_grant_product->select(array(
            'grant_id' => $grant_id
        ));

        $m_member_category = new member_credit_categoryModel();

        $all_total_credit = 0;
        foreach( $list as $v ){
            $member_category_id = $v['member_credit_category_id'];
            $category = $m_member_category->getRow(array(
                'uid' => $member_category_id
            ));
            if( !$category ){
                continue;
            }
            $credit_total = $v['credit'];
            $credit_usd = $v['credit_usd'];
            $credit_khr = $v['credit_khr'];

            $all_total_credit += $credit_total;
            // 减扣信用

            if( $category['credit'] < $credit_total || $category['credit_balance'] < $credit_total ){
                return new result(false,$category['alias'].' credit not enough:'.$category['credit_balance'].
            '/'.$category['credit'].' for' .$credit_total,null,errorCodesEnum::OUT_OF_CREDIT_BALANCE);
            }

            if( $category['credit_usd'] < $credit_usd || $category['credit_usd_balance'] < $credit_usd ){
                return new result(false,$category['alias'].' credit-USD not enough:'.$category['credit_usd_balance'].
                    '/'.$category['credit_usd'].' for' .$credit_usd,null,errorCodesEnum::OUT_OF_CREDIT_BALANCE);
            }

            if( $category['credit_khr'] < $credit_khr || $category['credit_khr_balance'] < $credit_khr ){
                return new result(false,$category['alias'].' credit-KHR not enough:'.$category['credit_khr_balance'].
                    '/'.$category['credit_khr'].' for' .$credit_usd,null,errorCodesEnum::OUT_OF_CREDIT_BALANCE);
            }

            // 更新category信用
            $category->credit -= $credit_total;
            $category->credit_balance -= $credit_total;
            $category->credit_usd -= $credit_usd;
            $category->credit_usd_balance -= $credit_usd;
            $category->credit_khr -= $credit_khr;
            $category->credit_khr_balance -= $credit_khr;
            $category->update_time = Now();
            $category->update_operator_id = $params['user_id'];
            $up = $category->update();
            if( !$up->STS ){
                return new result(false,'Update category credit fail:'.$up->MSG,
                    null,errorCodesEnum::DB_ERROR);
            }


        }

        // 扣减总信用
        $member_credit = (new member_creditModel())->getRow(array(
            'member_id' => $member_id
        ));
        if( !$member_credit ){
            return new result(false,'No credit info:'.$member_id,null,errorCodesEnum::NO_DATA);
        }

        if( $member_credit['credit'] < $all_total_credit
        || $member_credit['credit_balance'] < $all_total_credit ){
            return new result(false,'Member credit not enough: '.$member_credit['credit_balance'].
        '/'.$member_credit['credit'].' for '.$all_total_credit);
        }

        $member_credit->credit -= $all_total_credit;
        $member_credit->credit_balance -= $all_total_credit;
        $member_credit->update_time = Now();
        $up = $member_credit->update();
        if( !$up->STS ){
            return new result(false,'Update member credit fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        // 抵押的资产还原
        $mortgage_asset = (new member_asset_mortgageModel())->select(array(
            'contract_type' => 0,
            'contract_no' => $auth_contract['contract_no']
        ));
        if( count($mortgage_asset) > 0 ){
            // todo 是否删掉抵押记录
            $asset_ids = array();
            foreach( $mortgage_asset as $v ){
                $asset_ids[] = $v['member_asset_id'];
            }
            $sql = "update member_assets set asset_state=".qstr(assetStateEnum::CERTIFIED).",mortgage_state='0' where 
            uid in (".join(',',$asset_ids).") ";
            $up = $m->conn->execute($sql);
            if( !$up->STS ){
                return new result(false,'Update asset state fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
            }
        }

        // cancel 授信的数据
        $rt = member_credit_grantClass::creditGrantCancelExecute($grant_id);
        if( !$rt->STS ){
            return $rt;
        }

        // 如果客户已经支付loan fee，需要返还
        if( $auth_contract['is_paid'] == 1 ){

            if( $auth_contract['fee'] > 0 ){

                $remark = "Credit contract cancel and return loan fee and admin fee: contract No. ".$auth_contract['contract_no'];
                $sys_memo = "Member credit contract cancel,contract No. ".$auth_contract['contract_no'].
                ". Loan fee: ".$auth_contract['loan_fee_amount'].',admin fee: '.$auth_contract['admin_fee_amount'];

                if( $return_fee_way == 1 ){
                    // 如果支付是余额，不能现金返还
                    if( $auth_contract['payment_way'] != repaymentWayEnum::CASH ){
                        return new result(false,'Paid way is not cash,can not return cash.',
                            null,errorCodesEnum::NOT_SUPPORTED);
                    }
                    $user_id = $auth_contract['officer_id'];
                    // 现金返回是减掉签订时cashier的余额
                    // 收入多少，扣掉多少
                    $m_payment_detail = new member_authorized_contract_payment_detailModel();
                    $list = $m_payment_detail->select(array(
                        'contract_id' => $auth_contract['uid']
                    ));
                    foreach( $list as $v ){
                        $amount = -1*$v['amount'];
                        $currency = $v['currency'];
                        $tradingClass = new userAdjustTradingClass($user_id,$amount,$currency);
                        $tradingClass->subject = 'Return Loan Fee';
                        $tradingClass->remark = $remark;
                        $tradingClass->sys_memo = $sys_memo;
                        $rt = $tradingClass->execute();
                        if( !$rt->STS ){
                            return $rt;
                        }
                    }

                }else{
                    // 返给客户是余额增加
                    $tradingClass = new memberAdjustTradingClass($member_id,$auth_contract['fee'],currencyEnum::USD);
                    $tradingClass->subject = 'Return Loan Fee';
                    $tradingClass->remark = $remark;
                    $tradingClass->sys_memo = $sys_memo;
                    $rt = $tradingClass->execute();
                    if( !$rt->STS ){
                        return $rt;
                    }
                }


            }
        }

        // 其余完成后，更新合同为取消
        $auth_contract->state = authorizedContractStateEnum::CANCEL;
        $auth_contract->update_time = Now();
        $up = $auth_contract->update();
        if( !$up->STS ){
            return new result(false,'Update credit contract fail:'.$up->MSG,null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',$auth_contract);

    }


}