<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2018/1/4
 * Time: 16:06
 */
class editorControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Editor");
        Tpl::setDir("editor");
    }
}