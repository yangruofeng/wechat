<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/9
 * Time: 17:56
 */
class member_loan_schemaClass
{

    /** 获取客户下次应还贷款计划
     * @param $member_id
     * @param array $filter
     * @return array
     */
    public static function getMemberNextRepaymentSchema($member_id, $filter = array())
    {

        if ( $filter['member_credit_category_id'] ) {


            // 获取member贷款总欠款
            $total_repayment_amount = memberClass::getMemberLoanTotalPendingRepaymentAmountGroupByCurrency($member_id);

            $next_repayment_amount = array();
            $next_repayment_schemas = memberClass::getMemberLoanNextRepaymentDaySchemaList($member_id, array(
                'member_credit_category_id' => $filter['member_credit_category_id']
            ), true);

            $due_date = null;
            if ($next_repayment_schemas) {

                $due_date = date('Y-m-d', strtotime(current($next_repayment_schemas)['receivable_date']));
                foreach ($next_repayment_schemas as $k => $v) {

                    $interestClass = interestTypeClass::getInstance($v['repayment_type'], $v['repayment_period']);
                    $rt = loan_contractClass::getContractInterestInfo($v['contract_id']);
                    if ($rt->STS) {
                        $interest_info = $rt->DATA;
                        $v = $interestClass->calculateRepaymentInterestOfSchema($v, $interest_info);
                    }

                    $schema_amount = $v['amount'] - $v['actual_payment_amount'];
                    // 罚金
                    $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
                    $v['penalty'] = $penalty;
                    $v['total_payable_amount'] = $schema_amount + $penalty;
                    $next_repayment_schemas[$k] = $v;

                    if ($next_repayment_amount[$v['currency']]) {
                        $next_repayment_amount[$v['currency']] += $v['total_payable_amount'];
                    } else {
                        $next_repayment_amount[$v['currency']] = $v['total_payable_amount'];
                    }

                }
            }

            $next_repayment_info = array(
                'due_date' => $due_date,
                'total_repayment_amount' => $total_repayment_amount,
                'next_total_repayment_amount' => $next_repayment_amount,
                'next_repayment_schema_list' => $next_repayment_schemas
            );

            return $next_repayment_info;


        } elseif ($filter['contract_id']) {

            $contract_id = $filter['contract_id'];
            $m_contract = new loan_contractModel();
            $contract_info = $m_contract->getRow($contract_id);
            if (!$contract_info) {
                return null;
            }

            // 获取member贷款总欠款
            $total_repayment_amount = memberClass::getMemberLoanTotalPendingRepaymentAmountGroupByCurrency($member_id);


            // 合同下的
            $schema_list = $m_contract->getContractUncompletedSchemas($contract_id);

            $next_repayment_schemas = array();
            $next_repayment_amount = array();
            $due_date = date('Y-m-d', strtotime(current($schema_list)['receivable_date']));

            if (count($schema_list) > 0) {

                $due_date = date('Y-m-d', strtotime(current($schema_list)['receivable_date']));

                $interestClass = interestTypeClass::getInstance($contract_info['repayment_type'], $contract_info['repayment_period']);
                $interest_info = loan_contractClass::getContractInterestInfo($contract_id)->DATA;

                foreach ($schema_list as $k => $v) {

                    if ($interest_info) {
                        $v = $interestClass->calculateRepaymentInterestOfSchema($v, $interest_info);
                    }

                    $schema_amount = $v['amount'] - $v['actual_payment_amount'];
                    // 罚金
                    $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
                    $v['penalty'] = $penalty;
                    $v['total_payable_amount'] = $schema_amount + $penalty;
                    $next_repayment_schemas[$k] = $v;

                    if ($next_repayment_amount[$v['currency']]) {
                        $next_repayment_amount[$v['currency']] += $v['total_payable_amount'];
                    } else {
                        $next_repayment_amount[$v['currency']] = $v['total_payable_amount'];
                    }

                }
            }

            $next_repayment_info = array(
                'due_date' => $due_date,
                'total_repayment_amount' => $total_repayment_amount,
                'next_total_repayment_amount' => $next_repayment_amount,
                'next_repayment_schema_list' => $next_repayment_schemas
            );

            return $next_repayment_info;


        } else {


            // 获取member贷款总欠款
            $total_repayment_amount = memberClass::getMemberLoanTotalPendingRepaymentAmountGroupByCurrency($member_id);

            $next_repayment_amount = array();
            $next_repayment_schemas = memberClass::getMemberLoanNextRepaymentDaySchemaList($member_id);

            $due_date = null;
            if ($next_repayment_schemas) {

                $due_date = date('Y-m-d', strtotime(current($next_repayment_schemas)['receivable_date']));
                foreach ($next_repayment_schemas as $k => $v) {

                    $interestClass = interestTypeClass::getInstance($v['repayment_type'], $v['repayment_period']);
                    $rt = loan_contractClass::getContractInterestInfo($v['contract_id']);
                    if ($rt->STS) {
                        $interest_info = $rt->DATA;
                        $v = $interestClass->calculateRepaymentInterestOfSchema($v, $interest_info);
                    }

                    $schema_amount = $v['amount'] - $v['actual_payment_amount'];
                    // 罚金
                    $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
                    $v['penalty'] = $penalty;
                    $v['total_payable_amount'] = $schema_amount + $penalty;
                    $next_repayment_schemas[$k] = $v;

                    if ($next_repayment_amount[$v['currency']]) {
                        $next_repayment_amount[$v['currency']] += $v['total_payable_amount'];
                    } else {
                        $next_repayment_amount[$v['currency']] = $v['total_payable_amount'];
                    }

                }
            }

            $next_repayment_info = array(
                'due_date' => $due_date,
                'total_repayment_amount' => $total_repayment_amount,
                'next_total_repayment_amount' => $next_repayment_amount,
                'next_repayment_schema_list' => $next_repayment_schemas
            );

            return $next_repayment_info;


        }


    }

    /**
     * 供counter的还款首页用，原来不应该去取scheme，速度太慢
     * @param $member_id
     * @return array
     */
    public static function getMemberPendingRepaymentContractGroupByProduct($member_id)
    {
        $ret_contactList = memberClass::getLoanContractList(array("type" => 2, "member_id" => $member_id));
        $contactList = $ret_contactList->DATA;
        if ($contactList) {
            $contactList = $contactList['list'];
        }
        $ret = array();
        foreach ($contactList as $item) {
            if ($item['state'] > loanContractStateEnum::CREATE && $item['state'] <= loanContractStateEnum::PROCESSING) {
                if (!$ret[$item['member_credit_category_id']]) {
                    $ret[$item['member_credit_category_id']] = array(
                        'member_credit_category_id' => $item['member_credit_category_id'],
//                        'sub_product_name' => $item['sub_product_name'],
                        'product_name' => $item['product_name'],
                        'alias' => $item['alias'],
                    );
                }
                $ret[$item['member_credit_category_id']]['contract_list'][] = $item;
            }
        }
        return $ret;
    }

    /**
     * 供counter使用，获取还款计划表供选择是否还款
     * @param $member_id
     * @param $filter
     */
    public static function getMemberPendingRepaymentSchema($member_id, $filter, $is_include_penalty = true)
    {
        $schemas = memberClass::getMemberAllPendingRepaymentSchema($member_id, $filter);

        // 封装格式
        $return_list = array();
        foreach ($schemas as $k => $v) {

            $interestClass = interestTypeClass::getInstance($v['repayment_type'], $v['repayment_period']);
            $rt = loan_contractClass::getContractInterestInfo($v['contract_id']);
            if ($rt->STS) {
                $interest_info = $rt->DATA;
                $v = $interestClass->calculateRepaymentInterestOfSchema($v, $interest_info);
            }

            $schema_amount = $v['amount'] - $v['actual_payment_amount'];
            // 罚金
            $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
            $v['penalty'] = $penalty;
            if ($is_include_penalty) {
                $v['total_payable_amount'] = $schema_amount + $penalty;
            } else {
                $v['total_payable_amount'] = $schema_amount;
            }
            $return_list[] = $v;
        }

        return $return_list;
    }


    /** 获取客户全部贷款还款计划（按产品分组）
     * @param $member_id
     * @param $is_include_penalty
     * @return array
     */
    public static function getMemberAllLoanRepaymentSchemaGroupByProduct($member_id, $is_include_penalty = true, $filter = array())
    {
        $schemas = memberClass::getMemberAllPendingRepaymentSchema($member_id, $filter);

        // 封装格式
        $return_list = array();
        foreach ($schemas as $k => $v) {

            $interestClass = interestTypeClass::getInstance($v['repayment_type'], $v['repayment_period']);
            $rt = loan_contractClass::getContractInterestInfo($v['contract_id']);
            if ($rt->STS) {
                $interest_info = $rt->DATA;
                $v = $interestClass->calculateRepaymentInterestOfSchema($v, $interest_info);
            }

            $schema_amount = $v['amount'] - $v['actual_payment_amount'];
            // 罚金
            $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
            $v['penalty'] = $penalty;
            if ($is_include_penalty) {
                $v['total_payable_amount'] = $schema_amount + $penalty;
            } else {
                $v['total_payable_amount'] = $schema_amount;
            }

            if ($return_list[$v['sub_product_code']]) {
                $return_list[$v['sub_product_code']]['schema_list'][] = $v;
            } else {
                $return_list[$v['sub_product_code']]['sub_product_name'] = $v['sub_product_name'];
                $return_list[$v['sub_product_code']]['product_name'] = $v['product_name'];
                $return_list[$v['sub_product_code']]['schema_list'][] = $v;
            }
        }

        return $return_list;
    }


    public static function getMemberOverdueContractAndPenalty($member_id)
    {
        $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
        if( !$loan_account ){
            return array();
        }
        $today = date('Y-m-d 23:59:59');
        $r = new ormReader();
        $sql = "select c.contract_sn,c.currency,s.*,lp.penalty_amount from loan_installment_scheme s INNER join
        loan_contract c on c.uid=s.contract_id left join loan_penalty lp on lp.scheme_id=s.uid
        where c.account_id=".qstr($loan_account['uid']).
        " and c.state>=".qstr(loanContractStateEnum::PENDING_DISBURSE)." and c.state<".qstr(loanContractStateEnum::COMPLETE).
        " and s.penalty_start_date<='$today' and lp.state!=".qstr(loanPenaltyHandlerStateEnum::DONE)."
        and s.state!=".schemaStateTypeEnum::CANCEL."
         group by s.uid order by s.penalty_start_date asc,c.uid asc";
        $rows = $r->getRows($sql);
        $list = array();
        foreach( $rows as $v ){
            if( $v['penalty_amount'] > 0 ){

            }else{
                $penalty = loan_baseClass::calculateSchemaRepaymentPenalties($v['uid']);
                $v['penalty_amount'] = $penalty;
            }
            $list[] = $v;
        }
        return $list;

    }


    public static function getMemberLoanSchemesByBillCode($bill_code)
    {

        $r = new ormReader();
        $sql = "select ls.*,c.currency,c.contract_sn,c.repayment_type,c.repayment_period from loan_installment_scheme ls INNER join loan_contract c on c.uid=ls.contract_id
        left join loan_contract_billpay_code lcb on lcb.contract_id=c.uid where c.state>=".qstr(loanContractStateEnum::PENDING_DISBURSE).
        " and c.state<".qstr(loanContractStateEnum::COMPLETE)." and ls.state!=".qstr(schemaStateTypeEnum::CANCEL).
        " and ls.state!=".qstr(schemaStateTypeEnum::COMPLETE)."
        and lcb.bill_code=".qstr($bill_code)." group by ls.uid order by ls.receivable_date asc ";
        $list = $r->getRows($sql);

        // 重算金额
        $return_list = array();
        foreach ($list as $k => $v) {

            $interestClass = interestTypeClass::getInstance($v['repayment_type'], $v['repayment_period']);
            $rt = loan_contractClass::getContractInterestInfo($v['contract_id']);
            if ($rt->STS) {
                $interest_info = $rt->DATA;
                $v = $interestClass->calculateRepaymentInterestOfSchema($v, $interest_info);
            }

           $return_list[] = $v;
        }

        return $return_list;

    }


}