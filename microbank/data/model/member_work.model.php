<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/14
 * Time: 17:36
 */
class member_workModel extends tableModelBase
{
    function __construct()
    {
        parent::__construct('member_work');
    }

    public function getMemberWork($member_id)
    {
        $work = $this->orderBy('uid desc')->getRow(array(
            'member_id' => $member_id
        ));
        return $work;
    }

}