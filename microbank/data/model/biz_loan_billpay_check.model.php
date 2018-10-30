<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/12
 * Time: 10:34
 */
class biz_loan_billpay_checkModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('biz_loan_billpay_check');
    }

    public function getBillPayCheckList($pageNumber, $pageSize, $filter = array())
    {
        $sql = "SELECT lbc.*,cm.display_name,cm.login_code,cm.obj_guid FROM biz_loan_billpay_check lbc left JOIN client_member cm ON lbc.member_id = cm.uid WHERE 1 = 1
        and lbc.state=".qstr(bizStateEnum::DONE);

        if (intval($filter['operator_id'])) {
            $sql .= ' AND lbc.operator_id = ' . intval($filter['operator_id']);
        }

        if( $filter['bank_id'] ){
            $sql .= " and lbc.bank_id=".qstr($filter['bank_id']);
        }
        $data = $this->reader->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;
        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }
}