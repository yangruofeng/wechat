<?php

// 系统统计类

class statisticsClass {

    /** 所有合同数统计
     * @param int $type
     * @return int
     */
    public static function getLoanContractNumSummary($type=0)
    {
        $r = new ormReader();
        $num = 0;
        switch( $type)
        {
            case 0:
                // all
                $sql = "select count(*) from loan_contract where state>='".loanContractStateEnum::CREATE."' ";
                $num = $r->getOne($sql);
                break;
            case 1:
                // 正常执行的(含逾期合同)
                $sql = "select count(*) from loan_contract where state>='".loanContractStateEnum::PENDING_DISBURSE."' 
                and state <'".loanContractStateEnum::COMPLETE."' ";
                $num = $r->getOne($sql);
                break;
            case 2:
                // 延期的

                $sql = "select count(DISTINCT s.contract_id) from loan_contract c left join loan_installment_scheme s on s.contract_id=c.uid where  c.state>='".loanContractStateEnum::PENDING_DISBURSE."' 
                    and c.state <'".loanContractStateEnum::COMPLETE."' 
                    and s.state !='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "' and date_format(s.receivable_date,'%Y%m%d') < '" . date('Ymd') . "' group by c.uid ";
                $num = $r->getOne($sql);
                break;
            case 3:
                // 被拒绝的
                $sql = "select count(*) from loan_contract where state='".loanContractStateEnum::REFUSED."' ";
                $num = $r->getOne($sql);
                break;
            case 4:
                // write off
                $sql = "select count(*) from loan_contract where state='".loanContractStateEnum::WRITE_OFF."' ";
                $num = $r->getOne($sql);
                break;
            case 5:
                // 正常完成的
                $sql = "select count(*) from loan_contract where state='".loanContractStateEnum::COMPLETE."' ";
                $num = $r->getOne($sql);
                break;
            case 6:
                // 待审核的
                $sql = "select count(*) from loan_contract where and state in('".loanContractStateEnum::CREATE."','".loanContractStateEnum::PENDING_APPROVAL."') ";
                $num = $r->getOne($sql);
                break;
            default:
                break;
        }
        return $num;
    }

    /** 获得贷款总额
     * @return result
     */
    public static function getLoanTotal()
    {
        $m_loan_contract = new loan_contractModel();
        //$m_contract = new loan_contractModel();
        // 贷款待放款的都算
        $sql = "select * from loan_contract where state >='" . loanContractStateEnum::PENDING_DISBURSE . "' ";
        $rows = $m_loan_contract->reader->getRows($sql);
        $total = 0;
        if (count($rows) > 0) {
            foreach ($rows as $v) {
                $source = ($v['currency']);
                $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
                if ($rate <= 0) {
                    return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
                }
                $total += round($v['receivable_principal'] * $rate, 2);
            }
        }
        return new result(true, 'success', $total);
    }

    /** 获得贷款应还总额
     * @param $member_id
     * @return result
     */
    public static function getLoanTotalRepayable()
    {
        $m_loan_contract = new loan_contractModel();
        // 贷款待放款的都算
        $sql = "select s.*,c.currency from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid  where c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "' and c.state !='".loanContractStateEnum::WRITE_OFF."' and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "' ";
        $rows = $m_loan_contract->reader->getRows($sql);
        $total = 0;
        if (count($rows) > 0) {
            foreach ($rows as $v) {
                if ($v['state'] != schemaStateTypeEnum::COMPLETE) {
                    $source = strtoupper($v['currency']);
                    $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
                    if ($rate <= 0) {
                        return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
                    }
                    $total += round($v['receivable_principal'] * $rate, 2);
                }
            }
        }
        return new result(true, 'success', $total);
    }

    /** 获得贷款总利息和手续费
     * @return result
     */
    public static function getLoanInterestTotal()
    {
        $m_loan_contract = new loan_contractModel();
        //$m_contract = new loan_contractModel();
        // 贷款待放款的都算
        $sql = "select * from loan_contract where state >='" . loanContractStateEnum::PENDING_DISBURSE . "' ";
        $rows = $m_loan_contract->reader->getRows($sql);
        $total = 0;
        if (count($rows) > 0) {
            foreach ($rows as $v) {
                $source = ($v['currency']);
                $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
                if ($rate <= 0) {
                    return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
                }  
                $total += round($v['receivable_interest'] * $rate, 2);
                $total += round($v['receivable_operation_fee'] * $rate, 2);
            }
        }
        return new result(true, 'success', $total);
    }

    /** 获得贷款应还总利息和手续费
     * @param $member_id
     * @return result
     */
    public static function getOutstandingLoanInterestTotal()
    {
        $m_loan_contract = new loan_contractModel();
        // 贷款待放款的都算
        $sql = "select s.*,c.currency from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid  where c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "'
        and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "' ";
        $rows = $m_loan_contract->reader->getRows($sql);
        $total = 0;
        if (count($rows) > 0) {
            foreach ($rows as $v) {
                if ($v['state'] != schemaStateTypeEnum::COMPLETE) {
                    $source = strtoupper($v['currency']);
                    $rate = global_settingClass::getCurrencyRateBetween($source, currencyEnum::USD);
                    if ($rate <= 0) {
                        return new result(false, 'No currency rate', null, errorCodesEnum::NO_CURRENCY_EXCHANGE_RATE);
                    }
                    $total += round($v['receivable_interest'] * $rate, 2);
                    $total += round($v['receivable_operation_fee'] * $rate, 2);
                }
            }
        }
        return new result(true, 'success', $total);
    }
    
    /** 获得贷款应还总利息和手续费
     * @param $member_id
     * @return result
     */
    public static function getMemberSavings()
    {
        $m_member = new memberModel();
        // 贷款待放款的都算
        $sql = "select a.currency,sum(a.balance - a.outstanding) balance from client_member m left join passbook p on m.obj_guid = p.obj_guid left join passbook_account a on p.uid = a.book_id group by currency";
        $rows = $m_member->reader->getRows($sql);
        return $rows;
    }


    /** 某一产品的贷款统计
     * @param $sub_product_id
     * @return array
     */
    public static function getSubProductLoanSummary($sub_product_id)
    {
        // todo 先用产品code来合计
        $m = new loan_sub_productModel();
        $product_info = $m->getRow($sub_product_id);
        if( !$product_info ){
            return array();
        }

        $product_code = $product_info->sub_product_code;

        // 合计合同数
        $sql = " select count(c.uid) from loan_contract c 
        where c.state>='".loanContractStateEnum::PENDING_DISBURSE."' and c.sub_product_code='$product_code' ";
        $contract_num = ($m->reader->getOne($sql))?:0;

        // 合计客户数
        $sql = "select count(DISTINCT c.account_id) from loan_contract c 
        where c.state>='".loanContractStateEnum::PENDING_DISBURSE."' and c.sub_product_code='$product_code'";
        $client_num = ($m->reader->getOne($sql))?:0;

        // 合计贷款金额
        $sql = "select c.currency,sum(c.apply_amount) amount from loan_contract c 
        where c.state>='".loanContractStateEnum::PENDING_DISBURSE."' and c.sub_product_code='$product_code' group by c.currency ";
        $rows = $m->reader->getRows($sql);
        $loan_amount_total = array();
        foreach( $rows as $v ){
            $loan_amount_total[$v['currency']] = $v['amount'];
        }

        // 待还本金
        $sql = "select c.currency,sum(s.receivable_principal) amount from loan_contract c 
        left join loan_installment_scheme s on s.contract_id=c.uid  where c.state>='".loanContractStateEnum::PENDING_DISBURSE."' 
        and c.sub_product_code='$product_code' and s.state!='".schemaStateTypeEnum::COMPLETE."' and s.state!='".schemaStateTypeEnum::CANCEL."'   group by c.currency";
        $rows = $m->reader->getRows($sql);
        $pending_loan_principal = array();
        foreach( $rows as $v ){
            $pending_loan_principal[$v['currency']] = $v['amount'];
        }

        return array(
            'total_contract_num' => $contract_num,
            'total_client_num' => $client_num,
            'total_loan_out_principal' => $loan_amount_total,
            'total_pending_receive_principal' => $pending_loan_principal
        );

    }

}