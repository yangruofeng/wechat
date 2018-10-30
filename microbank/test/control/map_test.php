<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/4
 * Time: 10:31
 */
class map_testControl extends control{

    public function __construct()
    {
        Tpl::setDir('home');
        Tpl::setLayout('msg_layout');
    }

    public function mapTraceOp()
    {
        Tpl::showpage('map.trace.page2');
    }
}