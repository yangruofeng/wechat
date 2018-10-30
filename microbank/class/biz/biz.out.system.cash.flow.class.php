<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/25
 * Time: 15:33
 */
class bizOutSystemCashFlowClass extends bizBaseClass
{
    public function __construct($scene_code)
    {

        $is_open = $this->checkBizOpen();
        if( !$is_open->STS ){
            throw new Exception('Function close!',errorCodesEnum::FUNCTION_CLOSED);
        }
        $this->scene_code = bizSceneEnum::COUNTER;
        $this->biz_code = bizCodeEnum::OUT_SYSTEM_CASH_FLOW;
        $this->bizModel = new biz_out_system_flowModel();
    }

    public function checkBizOpen()
    {
        return new result(true);
    }

    /** 获取对应系统账户的余额
     * @return array
     */
    public static function getSystemAccountBalance()
    {
        $accountObj = new objectGlAccountClass(systemAccountCodeEnum::OUT_SYSTEM_INCOME_AND_EXPENSES);
        return $accountObj->getPassbookCurrencyBalance();
    }

    public function getBizDetailById($id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->find(array(
            'uid' => $id
        ));
        return $biz;
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
        $biz->cashier_trading_password = $teller->trading_password;
        $biz->cashier_name = $teller->user_name;
        $biz->update_time = Now();
        $up = $biz->update();

        return new result(true);
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
        $bm_id = $rt->DATA;
        $bmObj = new objectUserClass($bm_id);
        $biz->bm_id = $bm_id;
        $biz->bm_name = $bmObj->user_name;
        $biz->bm_trading_password = $bmObj->trading_password;
        $biz->update_time = Now();
        $biz->update();

        return $rt;
    }


    /** 获取分页流水列表
     * @param $cashier_id
     * @param $page_num
     * @param $page_size
     * @param $flag
     * @return mixed|ormPageResult
     */
    public function getPageFlowList($cashier_id,$page_num,$page_size,$flag,$extend=array())
    {
        $r = new ormReader();
        $sql = "select * from biz_out_system_flow where cashier_id=".qstr($cashier_id)." and flow_flag=".qstr($flag)."
         and state=".qstr(bizStateEnum::DONE);
        if($extend['search_by'] && $extend['search_value']){
            switch($extend['search_by']){
                case 1:
                    $sql.=" and extend_cid=".qstr($extend['search_value']);
                    break;
                case 2:
                    $sql.=" and extend_client_name=".qstr($extend['search_value']);
                    break;
                case 3:
                    $sql.=" and extend_contract_sn=".qstr($extend['search_value']);
                    break;
                case 4:
                    $sql.=" and remark like '%".qstr2($extend['search_value'])."%'";
                    break;
                default:
                    break;
            }
        }
        $sql .= " ORDER BY uid DESC";
        return $r->getPage($sql,$page_num,$page_size);
    }


    /** 业务开始
     * @param $cashier_id
     * @param $flag
     *  1 收入
     *  -1 支出
     * 0 待收
     * @param $amount
     * @param $currency
     * @param $remark
     * @return result
     */
    public function bizStart($cashier_id,$flag,$amount,$currency,$remark,$extend=array())
    {
        $cashier = new objectUserClass($cashier_id);

        $chk = $cashier->checkValid();
        if( !$chk->STS ){
            return $chk;
        }

        $cashier_position = $cashier->position;
        $amount = round($amount,2);
        if( $amount <= 0 ){
            return new result(false,'Invalid amount:'.$amount,null,errorCodesEnum::INVALID_AMOUNT);
        }

        switch ($flag){
            case flagTypeEnum::INCOME:
                $flag_type = flagTypeEnum::INCOME;

                // 收入要检查信用
                if( $cashier_position == userPositionEnum::CHIEF_TELLER ){

                    $branch_id = $cashier->branch_id;
                    $branchObj = new objectBranchClass($branch_id);

                    // 检查分行的信用限制
                    $credit = $branchObj->getCredit();
                    if( $credit > 0 ){
                        $branch_balance = $branchObj->getPassbookCurrencyBalance();
                        $total_amount = system_toolClass::convertMultiCurrencyAmount($branch_balance,
                            currencyEnum::USD);
                        if( $total_amount >= $credit ){
                            return new result(false,'Branch cash in vault out of credit limit:'.$credit,null,
                                errorCodesEnum::OUT_OF_ACCOUNT_CREDIT);
                        }
                    }

                }else{

                    // 需要检查信用限制
                    $credit = $cashier->getCredit();
                    if( $credit > 0 ){
                        $cashier_balance = $cashier->getPassbookBalance();
                        // 换算信用
                        $total_amount = system_toolClass::convertMultiCurrencyAmount($cashier_balance,currencyEnum::USD);
                        if( $total_amount >= $credit ){
                            return new result(false,'Cashier balance out of credit:'.$credit,null,errorCodesEnum::OUT_OF_ACCOUNT_CREDIT);
                        }
                    }
                }

                break;
            case flagTypeEnum::PAYOUT:
                $flag_type = flagTypeEnum::PAYOUT;
                break;
            case flagTypeEnum::OUTSTANDING:
                $flag_type = flagTypeEnum::OUTSTANDING;
                break;
            default:
                return new result(false,'Unknown type:'.$flag,null,errorCodesEnum::INVALID_PARAM);
        }
        $biz = $this->bizModel->newRow();
        $biz->biz_code = $this->biz_code;
        $biz->scene_code = $this->scene_code;
        $biz->amount = $amount;
        $biz->flow_flag = $flag_type;
        $biz->currency = $currency;
        $biz->remark = $remark;
        $biz->cashier_id = $cashier_id;
        $biz->cashier_name = $cashier->user_name;

        $biz->extend_cid=$extend['extend_cid'];
        $biz->extend_client_name=$extend['extend_client_name'];
        $biz->extend_contract_sn=$extend['extend_contract_sn'];

        $biz->create_time = Now();
        $biz->state = bizStateEnum::CREATE;
        $biz->branch_id = $cashier->branch_id;
        $biz->account_type = ($cashier_position == userPositionEnum::CHIEF_TELLER)?1:0;
        $insert = $biz->insert();
        if( !$insert->STS ){
            return new result(false,'Create biz fail.',null,errorCodesEnum::DB_ERROR);
        }

        return new result(true,'success',array(
            'biz_id' => $biz->uid
        ));
    }


    /** 完成业务
     * @param $biz_id
     * @return result
     */
    public function bizSubmit($biz_id)
    {
        $m_biz = $this->bizModel;
        $biz = $m_biz->getRow($biz_id);
        if( !$biz ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }

        if( $biz->biz_code != $this->biz_code ){
            return new result(false,'Un-match handle.',null,errorCodesEnum::UN_MATCH_OPERATION);
        }

        if( $biz->state == bizStateEnum::DONE ){
            return new result(true,'success',$biz);
        }

        $cashier_id = $biz->cashier_id;
        $branch_id = $biz->branch_id;
        $amount = $biz->amount;
        $currency = $biz->currency;
        $remark = $biz->remark;

        switch( $biz->flow_flag ){
            case flagTypeEnum::INCOME:
                if( $biz->account_type == 0 ){
                    $rt = (new cashierOutSystemIncomeTradingClass($cashier_id,$amount,$currency,$remark))->execute();

                }else{
                    $rt = (new branchOutSystemIncomeTradingClass($branch_id,$amount,$currency,$remark))->execute();
                }
                if( !$rt->STS ){
                    $biz->state = bizStateEnum::FAIL;
                    $biz->update_time = Now();
                    $biz->update();
                    return $rt;
                }
                $trade_id = intval($rt->DATA);
                break;
            case flagTypeEnum::PAYOUT:
                if( $biz->account_type == 0 ){
                    $rt = (new cashierOutSystemPaymentTradingClass($cashier_id,$amount,$currency,$remark))->execute();

                }else{
                    $rt = (new branchOutSystemPaymentTradingClass($branch_id,$amount,$currency,$remark))->execute();
                }
                if( !$rt->STS ){
                    $biz->state = bizStateEnum::FAIL;
                    $biz->update_time = Now();
                    $biz->update();
                    return $rt;
                }
                $trade_id = intval($rt->DATA);
                break;
            case flagTypeEnum::OUTSTANDING:
                $trade_id = 0;
                break;
            default:
                return new result(false,'Unknown type:'.$biz->flow_flag,null,errorCodesEnum::INVALID_PARAM);
        }

        $biz->state = bizStateEnum::DONE;
        $biz->passbook_trading_id = $trade_id;
        $biz->update_time = Now();
        $up = $biz->update();
        if( !$up->STS ){
            return new result(false,'Update biz fail.',null,errorCodesEnum::DB_ERROR);
        }

        $biz->biz_id = $biz->uid;
        return new result(true,'success',$biz);
    }


}