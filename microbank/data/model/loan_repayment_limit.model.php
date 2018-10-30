<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class loan_repayment_limitModel extends tableModelBase
{
    public function  __construct()
    {
        parent::__construct('loan_repayment_limit');
    }

    public function getrepaymentLimitList(){
        $sql = "select * from loan_repayment_limit order by uid desc";
        $list = $this->reader->getRows($sql);
        return $list;
    }

    public function addPrepaymentLimit($params){
      $loan_days = $params['loan_days'];
      $limit_days = $params['limit_days'];
      $insert = $this->newRow();
      $insert->loan_days = $loan_days;
      $insert->limit_days = $limit_days;
      $rt = $insert->insert();
      return $rt;
    }

    public function delPrepaymentLimit($uid){
        $row = $this->getRow(array('uid' => $uid));
        if (!$row) {
            return new result(false, 'Invalid Id!');
        }
        $rt = $row->delete();
        return $rt;
    }
}
