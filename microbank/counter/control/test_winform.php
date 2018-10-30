<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/25
 * Time: 11:27
 */
class test_winformControl extends counter_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setDir('home');
        Tpl::setLayout('home_layout');
    }

    public function indexOp()
    {
        Tpl::showPage('test');
    }
}