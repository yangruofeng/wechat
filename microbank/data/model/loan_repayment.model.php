<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/2
 * Time: 15:48
 */
class loan_repaymentModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('loan_repayment');
    }

    public function getDetailInfoById($id)
    {
        $sql = "select r.*,c.contract_sn from loan_repayment r left join loan_contract c on c.uid=r.contract_id
        where r.uid=".qstr($id);
        return $this->reader->getRow($sql);
    }
}