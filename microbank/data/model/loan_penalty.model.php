<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/10
 * Time: 9:38
 */
class loan_penaltyModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('loan_penalty');
    }

    public function getPenaltyByMemberId($member_id)
    {
        $r = new ormReader();
        $sql = "SELECT la.* FROM client_member cm LEFT JOIN loan_account la ON cm.obj_guid = la.obj_guid WHERE cm.uid = $member_id";
        $loan_account = $r->getRow($sql);
        $account_id = $loan_account['uid'];

        $sql = "SELECT lp.*,lc.contract_sn,lis.scheme_name FROM loan_penalty lp LEFT JOIN loan_contract lc ON lp.contract_id = lc.uid LEFT JOIN loan_installment_scheme lis ON lp.scheme_id = lis.uid WHERE lp.account_id = " . intval($account_id) . " AND lp.state = " . loanPenaltyHandlerStateEnum::CREATE;
        $penalty = $r->getRows($sql);
        return $penalty;
    }


}