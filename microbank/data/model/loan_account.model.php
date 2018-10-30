<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/6
 * Time: 17:53
 */
class loan_accountModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('loan_account');
    }

    /**
     * 获取role信息
     * @param $uid
     * @return result
     */
    public function getCreditInfo($obj_guid)
    {
        $info = $this->find(array('obj_guid' => $obj_guid));
        if (empty($info)) {
            return new result(false, 'Invalid Account');
        }
        return new result(true, '', $info);
    }

    public function addAccount($obj_guid){
      //创建account
      $insert = $this->newRow();
      $insert->obj_guid = $obj_guid;
      $insert->update_time = Now();
      $rt = $insert->insert();
      if ($rt->STS) {
        return new result(true, '', $rt->SOURCE_ROW);
      } else {
        return new result(false, 'Invalid Account');
      }
    }

    /**
     * 编辑role
     * @param $param
     * @return result
     */
    public function editCredit($param)
    {
        $obj_guid = intval($param['obj_guid']);
        $before_credit = $param['before_credit'];
        $credit = $param['credit'];
        $valid_time = intval($param['valid_time']);
        $repayment_ability = $param['repayment_ability'];
        $remark = $param['remark'];
        if (!$credit) {
            return new result(false, 'Credit cannot be empty!');
        }
        if( $valid_time < 1 ){
            return new result(false, 'Invalid time!');
        }
        $m_loan_account = M('loan_account');
        $m_loan_approval = M('loan_approval');

        $row = $m_loan_account->getRow(array('obj_guid' => $obj_guid));
        if (empty($row)) {
            return new result(false, 'Invalid Account!');
        }
        $approvaling = $m_loan_approval->getRow(array('obj_guid' => $obj_guid,'state'=>0));//申请中
        if($approvaling){
          return new result(false, 'Approving');
        }

        $rt_5 = $m_loan_approval->getRow(array('obj_guid' => $obj_guid));
        $insert = $m_loan_approval->newRow();
        if($rt_5){
          $insert->type = ($before_credit <= $credit) ? 1 : 2;
        }else{
          $insert->type = 0;
        }
        $insert->obj_guid = $obj_guid;
        $insert->before_credit = $before_credit;
        $insert->current_credit = $credit;
        $insert->valid_time = $valid_time;
        $insert->valid_time_unit = loanPeriodUnitEnum::YEAR;
        $insert->creator_id = intval($param['creator_id']);
        $insert->creator_name = $param['creator_name'];
        $insert->repayment_ability = $repayment_ability;
        $insert->remark = $remark;
        $insert->create_time = Now();
        $rt = $insert->insert();
        if ($rt->STS) {
            return new result(true, 'Approval successful!');
        } else {
            return new result(false, 'Add failed--' . $rt->MSG);
        }
    }




}
