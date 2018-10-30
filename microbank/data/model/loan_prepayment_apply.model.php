<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/23
 * Time: 14:38
 */
class loan_prepayment_applyModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('loan_prepayment_apply');
    }

    public function updateApplyState($apply_id, $state, $handler_id)
    {
        $userObj = new objectUserClass($handler_id);
        $arr_update = array(
            'uid' => $apply_id,
            'state' => $state,
            'handler_id' => $handler_id,
            'handler_name' => $userObj->user_name,
            'handle_time' => Now()
        );
        $rt = $this->update($arr_update);
        if ($rt->STS) {
            return new result(true, 'Update successful.');
        } else {
            return new result(true, 'Update failed.' . $rt->MSG);
        }
    }
}