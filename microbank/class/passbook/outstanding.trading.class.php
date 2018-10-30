<?php

abstract class outstandingTradingClass extends tradingClass {
    public function __construct()
    {
        parent::__construct();
        $this->is_outstanding = true;
    }
}