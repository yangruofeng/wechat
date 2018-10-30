<?php
/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2017/10/16
 * Time: 10:43
 */

class tree_editorControl extends control {
    public function __construct()
    {
        Tpl::setLayout("empty_layout");
        Tpl::setDir("tree_editor");
        Tpl::output("html_title", "树桩目录的编辑");
    }

    public function indexOp() {
        Tpl::output("html_title", "树桩目录的编辑");
        Tpl::showPage("index");
    }

    public function defaultStyleOp()
    {
        Tpl::output("html_title", "基本样式");
        Tpl::showpage('default_style');

    }

    public function metroStyleOp()
    {
        Tpl::output("html_title", "Metro风格（异步加载）");
        Tpl::showpage('metro_async');
    }

    public function rightClickEditOp()
    {
        Tpl::output("html_title", "右键菜单（选择+编辑）");
        Tpl::showpage('right_click_edit');
    }

}