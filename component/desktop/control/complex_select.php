<?php

class complex_selectControl extends control {
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("empty_layout");
        Tpl::setDir("complex_select");
        Tpl::output("html_title", "弹出窗口的单选多选项");
    }

    public function indexOp() {
        Tpl::showPage("index");
    }
}