<?php

class savings_redeemModel extends tableModelBase
{
    private $table_name = 'savings_transaction';

    function __construct()
    {
        parent::__construct($this->table_name);
    }

    public function createRedeemApply($mainOpts, $details) {
        try {
            $this->conn->startTransaction();

            $mainOpts['trx_type'] = savingsTransactionTypeEnum::REDEEM;
            $mainOpts['create_time'] = Now();
            $mainOpts['state'] = savingsTransactionStateEnum::TEMP;
            $row = $this->newRow($mainOpts);
            $rt = $row->insert();
            if (!$rt->STS) throw new Exception($rt->MSG);

            $detail_model = new savings_redeem_detailModel();
            foreach ($details as $detail_row) {
                $detail_row['trx_id'] = $row->uid;
                $rt = $detail_model->insertArr($detail_row);
                if (!$rt->STS) throw new Exception($rt->MSG);
            }

            $this->conn->submitTransaction();
            return new result(true, null, $row);
        } catch (Exception $ex) {
            $this->conn->rollback();
            return new result(false, $ex->getMessage(), null, errorCodesEnum::DB_ERROR);
        }

    }

    public function getRedeemAmountTodayOfClient($client, $productId) {
        $client_obj_type = qstr($client['client_obj_type']);
        $client_obj_guid = qstr($client['client_obj_guid']);
        $productId = qstr($productId);
        $today_start = qstr(date("Y-m-d"));
        $today_end = qstr($today_start . " 23:59:59");
        $state_filter = "state in (" . qstr(savingsRedeemStateEnum::PROCESSING) . "," . qstr(savingsRedeemStateEnum::FINISHED) . ")";

        $sql = <<<SQL
select sum(amount) from savings_transaction
where client_obj_type = $client_obj_type and client_obj_guid = $client_obj_guid and product_id = $productId
 and create_time BETWEEN $today_start and $today_end
 and $state_filter 
SQL;
        return $this->reader->getOne($sql);
    }
}