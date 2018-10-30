<?php

class savings_redeem_detailModel extends tableModelBase
{
    private $table_name = 'savings_redeem_detail';

    function __construct()
    {
        parent::__construct($this->table_name);
    }
}