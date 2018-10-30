<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/4
 * Time: 17:27
 */
class biz_manual_voucherModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('biz_manual_voucher');
    }

    public function getManualVoucherList($branch_id, $pageNumber, $pageSize)
    {
        $sql = "select * from biz_manual_voucher WHERE"
            . " branch_id=".qstr($branch_id)." AND state = 100 ORDER BY update_time DESC";

        $page = $this->reader->getPage($sql, $pageNumber, $pageSize);
        return array(
            "sts" => true,
            "data" => $page->rows,
            "total" => $page->count,
            "pageNumber" => $pageNumber,
            "pageTotal" => $page->pageCount,
            "pageSize" => $pageSize,
        );
    }
}