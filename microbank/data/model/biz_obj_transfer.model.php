<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/4
 * Time: 17:27
 */
class biz_obj_transferModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('biz_obj_transfer');
    }

    public function getBankTransactionByGuid($guid, $pageNumber, $pageSize)
    {
        $sql = "select * from biz_obj_transfer WHERE"
            . " (receiver_obj_guid = " . qstr($guid)
            . " OR  sender_obj_guid = " . qstr($guid)
            . " ) AND state = 100 ORDER BY update_time DESC";

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

    public function getHqVaultFlow($guid, $object_id_arr, $pageNumber, $pageSize)
    {
        if (!$object_id_arr) {
            return array(
                "sts" => true,
                "data" => array(),
                "total" => 0,
                "pageNumber" => $pageNumber,
                "pageTotal" => 0,
                "pageSize" => $pageSize,
            );
        } else {
            $object_id_str = "(" . implode(',', $object_id_arr) . ")";
            $sql = "select * from biz_obj_transfer WHERE"
                . " ((sender_obj_guid IN $object_id_str AND receiver_obj_guid = " . qstr($guid)
                . ") OR (receiver_obj_guid IN $object_id_str AND sender_obj_guid = " . qstr($guid)
                . " )) AND state = 100 ORDER BY update_time DESC";
        }

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