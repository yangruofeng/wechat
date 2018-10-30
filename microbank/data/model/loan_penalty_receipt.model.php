<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/10
 * Time: 17:40
 */
class loan_penalty_receiptModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('loan_penalty_receipt');
    }


    public function getPendingPenaltyByMemberId($member_id)
    {
        $r = new ormReader();
        $sql = "SELECT la.* FROM client_member cm LEFT JOIN loan_account la ON cm.obj_guid = la.obj_guid WHERE cm.uid = $member_id";
        $loan_account = $r->getRow($sql);
        $sql = "SELECT * FROM loan_penalty_receipt WHERE state !=" . loanPenaltyReceiptStateEnum::COMPLETE . " AND account_id=" . $loan_account['uid'];
        $sql .= " ORDER BY uid DESC";
        $pending_penalty = $r->getRows($sql);
        return $pending_penalty;
    }

    public function getPenaltyByUid($uid)
    {
        $r = new ormReader();
        $sql = "SELECT * FROM loan_penalty_receipt WHERE  uid=" . $uid;
        $penalty = $r->getRow($sql);
        if (empty($penalty)) {
            return new result(false, 'No info');
        }
        return new result(true, '', $penalty);
    }

}