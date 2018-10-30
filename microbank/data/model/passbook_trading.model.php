<?php

class passbook_tradingModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('passbook_trading');
    }

    public function getTradingFlows($tradingId) {
        $sql = <<<SQL
select a.uid passbook_id, a.obj_type, a.book_type, a.book_name, a.obj_guid, b.uid account_id, b.currency, c.begin_balance,c.credit, c.debit,c.end_balance, c.update_time, c.uid, c.subject,c.remark
from passbook a 
inner join passbook_account b on b.book_id = a.uid
inner join passbook_account_flow c on c.account_id = b.uid
where c.trade_id = $tradingId
SQL;
        return $this->reader->getRows($sql, true);
    }
}