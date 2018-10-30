<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/2
 * Time: 2:05
 */
class announceControl
{
    public function __construct()
    {
        Tpl::setLayout('empty_layout');
        Tpl::setDir('announce');
    }

    public function indexOp()
    {
        Tpl::showpage('announcement');
    }
}