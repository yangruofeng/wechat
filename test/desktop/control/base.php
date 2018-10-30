<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2017/10/16
 * Time: 10:44
 */

class baseControl extends control {
    public function __construct()
    {
        Tpl::setLayout("empty_layout");
        Tpl::setDir("");
        Tpl::output("html_title", "UI Test");
    }

    public function indexOp() {
        Tpl::showPage("index");
    }
}