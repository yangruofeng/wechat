<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/6
 * Time: 0:25
 */
class officer_v2_testControl extends officerControl
{

    public function testOp()
    {
        return new result(true,'Test');
    }
}