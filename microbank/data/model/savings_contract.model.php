<?php

class savings_contractModel extends tableModelBase
{
    private $table_name = 'savings_contract';

    function __construct()
    {
        parent::__construct($this->table_name);
    }

    public function create($mainOpts) {
        try {
            $this->conn->startTransaction();

            $transaction_model = new savings_transactionModel();
            $trx_data = array_intersect_key($mainOpts, array_flip(array(
                'client_obj_type', 'client_obj_guid', 'product_id', 'amount',
                'currency', 'create_source', 'creator_id', 'creator_name')));
            $trx_data['fee'] = $mainOpts['purchase_fee'];
            $trx_data['trx_type'] = savingsTransactionTypeEnum::PURCHASE;
            $trx_data['create_time'] = Now();
            $trx_data['state'] = savingsTransactionStateEnum::TEMP;
            $trx_data['update_time'] = Now();

            $trx_row = $transaction_model->newRow($trx_data);
            $rt = $trx_row->insert();
            if (!$rt->STS) throw new Exception($rt->MSG);

            $mainOpts['trx_id'] = $trx_row->uid;
            $mainOpts['create_time'] = Now();
            $mainOpts['state'] = savingsContractStateEnum::TEMP;
            $mainOpts['update_time'] = Now();
            $contract_row = $this->newRow($mainOpts);
            $rt = $contract_row->insert();
            if (!$rt->STS) throw new Exception($rt->MSG);

            $this->conn->submitTransaction();
            return new result(true, null, array_merge(array(), $trx_row->toArray(), $contract_row->toArray()));
        } catch (Exception $ex) {
            $this->conn->rollback();
            return new result(false, $ex->getMessage(), null, errorCodesEnum::DB_ERROR);
        }
    }

    public function getListWithProduct($filters = array()) {
        $filter_segments = array();
        if ($filters['state']) {
            if (is_array($filters['state'])) {
                $filter_segments[]= "a.state in (" .
                    join(",", array_map(function($v){return qstr($v);}, $filters['state'])) .
                    ")";
            } else {
                $filter_segments[]= "a.state = " . qstr($filters['state']);
            }
        } else {
            $filter_segments[]= "a.state not in (" .
                qstr(savingsContractStateEnum::CANCELLED) . "," .
                qstr(savingsContractStateEnum::TEMP) . ")";
        }

        if ($filters['client_obj_guid']) {
            $filter_segments[]= "a.client_obj_guid = " . qstr($filters['client_obj_guid']);
            $filter_segments[]= "a.client_obj_type = " . qstr($filters['client_obj_type']);
        }
        if ($filters['end_date']) {
            if (is_array($filters['end_date'])) {
                $filter_segments[]="a.end_date " . $filters['end_date'][0] . qstr($filters['end_date'][1]);
            } else {
                $filter_segments[]="a.end_date = " . qstr($filters['end_date']);
            }
        }
        if ($filters['category_type']) {
            $filter_segments[]="c.category_type = " . qstr($filters['category_type']);
        }
        if ($filters['category_id']) {
            $filter_segments[]="c.uid = " . qstr($filters['category_id']);
        }
        if ($filters['product_id']) {
            $filter_segments[]="a.product_id = " . qstr($filters['product_id']);
        }

        $filter_sql = join(" AND ", $filter_segments);

        $processing_redeem_state = qstr(savingsTransactionStateEnum::PROCESSING);
        $finished_redeem_state = qstr(savingsTransactionStateEnum::FINISHED);
        $redeem_state_filter="e.state in ($processing_redeem_state,$finished_redeem_state)";

        $sql = <<<SQL
select a.*, b.product_code, b.category_id, c.category_code, c.category_type,
 sum(case when e.state = $processing_redeem_state then d.amount else 0 end) redeeming_amount,
 sum(case when e.state = $finished_redeem_state then d.amount else 0 end) redeemed_amount
from savings_contract a 
inner join savings_product b on b.uid = a.product_id
inner join savings_category c on c.uid = b.category_id
left join savings_redeem_detail d on d.contract_id = a.uid
left join savings_transaction e on e.uid = d.trx_id and $redeem_state_filter
where $filter_sql
group by a.uid
SQL;
        return $this->reader->getRows($sql);
    }
}