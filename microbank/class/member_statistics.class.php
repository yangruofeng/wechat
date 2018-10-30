<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/5/15
 * Time: 11:40
 */
// 客户统计类，只放跟客户统计有关的

class member_statisticsClass
{

    /** 获取客户的工资收入合计
     * @param $member_id
     * @return int
     */
    public static function getMemberTotalIncomeSalary($member_id,$include_all=true)
    {
        $member_id = intval($member_id);
        $where = '';
        if( !$include_all ){
            $where .= " and state<100 ";
        }
        $r = new ormReader();
        $sql = "select sum(salary) total_amount from member_income_salary where member_id='$member_id' $where ";
        return ($r->getOne($sql)) ?: 0;
    }


    /** 获取客户商业调查收入合计
     * @param $member_id
     * @return int
     */
    public static function getMemberTotalIncomeBusiness($member_id)
    {

        $member_id = intval($member_id);
        $industry_list = memberClass::getMemberIndustryInfo($member_id,false);
        $total_income = 0;
        foreach( $industry_list as $v ){
            $total_income += $v['profit'];
        }
        return $total_income;

    }


    /** 获取客户其他收入合计
     * @param $member_id
     * @return int
     */
    public static function  getMemberTotalOtherAttachmentIncome($member_id)
    {
        $member_id = intval($member_id);
        $r = new ormReader();
        $sql = "select sum(amount*flag) from (
select case ext_type when " . qstr(memberAttachmentTypeEnum::INCOME) . " then 1  when " . qstr(memberAttachmentTypeEnum::EXPENSE) . " then -1 else 0 end as flag,ext_amount amount
from member_attachment where member_id='$member_id' and state<100
) x ";
        return ($r->getOne($sql)) ?: 0;

    }


    /** 获取客户认证资产总数量
     * @param $member_id
     * @return int
     */
    public static function getMemberTotalAssetNum($member_id)
    {
        $member_id = intval($member_id);
        $r = new ormReader();
        $sql = "select count(*) from member_assets where member_id='$member_id' and asset_state>='" . assetStateEnum::CERTIFIED . "' ";
        return ($r->getOne($sql)) ?: 0;
    }


    /** 获取客户某一产品进行中的贷款
     * @param $account_id
     * @param $sub_product_code
     * @return mixed
     */
    public static function getMemberExecutingLoanContactsOfProduct($account_id, $sub_product_code)
    {
        $r = new ormReader();

        $loan_account_id = intval($account_id);
        $where = " account_id='$loan_account_id' and state>='" . loanContractStateEnum::PENDING_DISBURSE . "'
        and state <'" . loanContractStateEnum::COMPLETE . "' and sub_product_code=" . qstr($sub_product_code);

        $sql = "select count(*) from loan_contract where $where ";
        return $r->getOne($sql);
    }


    /** 统计member贷款合计应还的本金
     * @param $member_id
     * @return array|null
     */
    public static function getMemberTotalPayableLoanPrincipalGroupByCurrency($member_id)
    {
        $loan_account = memberClass::getLoanAccountInfoByMemberId($member_id);
        if (!$loan_account) {
            return null;
        }
        $loan_account_id = $loan_account['uid'];

        $r = new ormReader();

        $sql = "select sum(s.receivable_principal) amount,c.currency from loan_installment_scheme s left join loan_contract c on s.contract_id=c.uid  
        where c.account_id='" . $loan_account_id . "'  and c.state>='" . loanContractStateEnum::PENDING_DISBURSE . "'
        and c.state<'" . loanContractStateEnum::COMPLETE . "'
        and s.state!='" . schemaStateTypeEnum::CANCEL . "' and s.state!='" . schemaStateTypeEnum::COMPLETE . "' group by c.currency ";
        $rows = $r->getRows($sql);
        $return = array();
        foreach ($rows as $v) {
            $return[$v['currency']] = $v['amount'];
        }
        return $return;
    }

    /** 获得贷款总额
     * @param $member_id
     * @return result
     */
    public static function getLoanTotalGroupByCurrency($member_id)
    {
        $member_id = intval($member_id);
        if (!$member_id) {
            return new result(false, 'Invalid param', null, errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if (!$member) {
            return new result(false, 'No member', null, errorCodesEnum::MEMBER_NOT_EXIST);
        }
        $m_account = new loan_accountModel();
        $loan_account = $m_account->getRow(array(
            'obj_guid' => $member->obj_guid
        ));
        $account_id = $loan_account ? $loan_account->uid : 0;

        // 贷款待放款的都算
        $sql = "select * from loan_contract where account_id='$account_id' and state >='" . loanContractStateEnum::PENDING_DISBURSE . "' ";
        $rows = $m_member->reader->getRows($sql);
        $loan_total = array();
        if (count($rows) > 0) {
            foreach ($rows as $v) {

                $loan_total[$v['currency']] = round($loan_total[$v['currency']], 2) + $v['receivable_principal'];;
            }
        }
        return new result(true, 'success', $loan_total);
    }

}