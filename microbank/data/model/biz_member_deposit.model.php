<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/17
 * Time: 18:01
 */
class biz_member_depositModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('biz_member_deposit');
    }

    public function searchPendingRecordForPartner($searchText, $page, $pageSize) {
        $pending_state = bizStateEnum::PENDING_CHECK;
        if ($searchText) {
            $search_filter = "AND (" . join(" OR ", array(
                    "a.uid = " . qstr($searchText),
                    "b.api_trx_id = " . qstr($searchText),
                    "c.login_code like '%" . qstr2($searchText) . "%'",
                    "c.display_name like '%" . qstr2($searchText) . "%'"
            )) . ")";
        } else {
            $search_filter = "";
        }

        $sql = <<<SQL
SELECT a.*, c.`login_code`, c.`display_name`, b.`api_trx_id` partner_api_trx_id, b.`api_state`, b.`api_error`, d.`state` trading_state, d.`is_outstanding`
FROM biz_member_deposit a
INNER JOIN client_member c ON c.`uid` = a.`member_id`
LEFT JOIN partner_trx_api b ON b.`ref_biz_type` = 'savings' AND b.`ref_biz_sub_type` = a.`biz_code` AND b.`ref_biz_id` = a.`uid`
LEFT JOIN passbook_trading d ON d.`uid` = a.`passbook_trading_id`
WHERE a.`biz_code` = 'member_deposit_by_partner' AND a.`state` = $pending_state $search_filter
ORDER BY a.uid
SQL;

        return $this->reader->getPage($sql, $page, $pageSize);
    }
}