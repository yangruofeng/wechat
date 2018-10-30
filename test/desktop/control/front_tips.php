<?php

class front_tipsControl extends control {
    public function __construct()
    {
        Tpl::setLayout("empty_layout");
        Tpl::setDir("front_tips");
        Tpl::output("html_title", "alert warning的模式");
    }

    public function indexOp() {
        Tpl::showPage("index");
    }
}