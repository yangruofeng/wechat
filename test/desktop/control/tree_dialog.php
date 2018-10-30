<?php

class tree_dialogControl extends control {
    public function __construct()
    {
        Tpl::setLayout("empty_layout");
        Tpl::setDir("tree_dialog");
        Tpl::output("html_title", "树桩目录的选择框");
    }

    public function indexOp() {
        Tpl::showPage("index");
    }

    public function singleSelectOp()
    {
        Tpl::output("html_title", "单选选择框");
        Tpl::showpage('select.ratio');

    }

    public function multiSelectOp()
    {
        Tpl::output("html_title", "复选选择框");
        Tpl::showpage('select.multi');

    }


}