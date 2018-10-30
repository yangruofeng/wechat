<?php

class savings_transactionModel extends tableModelBase
{
    private $table_name = 'savings_transaction';

    function __construct()
    {
        parent::__construct($this->table_name);
    }

    public function getListWithProduct($filters, $page, $pageSize) {
        $filter_segments = array();
        if ($filters['client_obj_type']) {
            $filter_segments[]="a.client_obj_type = " .qstr($filters['client_obj_type']);
        }
        if ($filters['client_obj_guid']) {
            $filter_segments[]="a.client_obj_guid = " .qstr($filters['client_obj_guid']);
        }
        if ($filters['product_id']) {
            $filter_segments[]="a.product_id = " .qstr($filters['product_id']);
        }
        if ($filters['category_id']) {
            $filter_segments[]="b.category_id = " . qstr($filters['category_id']);
        }
        if ($filters['date_start']) {
            $filter_segments[]="a.create_time >= " . qstr($filters['date_start']);
        }
        if ($filters['date_end']) {
            $filter_segments[]="a.create_time <= " . qstr($filters['date_end']);
        }
        if ($filters['state']) {
            if (is_array($filters['state'])) {
                $filter_segments[]= "a.state in (" .
                    join(",", array_map(function($v){return qstr($v);}, $filters['state'])) .
                    ")";
            } else {
                $filter_segments[]= "a.state = " . qstr($filters['state']);
            }
        } else {
            $filter_segments[]= "a.state in (" .
                qstr(savingsTransactionStateEnum::PROCESSING) . "," .
                qstr(savingsTransactionStateEnum::FINISHED) . ")";
        }

        $filter_sql = join(" AND ", $filter_segments);

        $sql = <<<SQL
select a.*, b.product_code, b.product_name, b.category_id, 
 c.category_code, c.category_name, c.category_type, c.category_icon, c.category_term_style
from savings_transaction a 
inner join savings_product b on b.uid = a.product_id
inner join savings_category c on c.uid = b.category_id
where $filter_sql
SQL;

        return $this->reader->getPage($sql, $page, $pageSize);
    }
}