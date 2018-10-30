<?php

class front_exceptionControl extends control {
    public function __construct()
    {
        Tpl::setLayout("empty_layout");
        Tpl::setDir("front_exception");
        Tpl::output("html_title", "throw error的前端处理");
    }

    public function indexOp()
    {
        Tpl::showPage("index");
    }

    public function testOp()
    {
        throw new Exception('abc');
    }
}