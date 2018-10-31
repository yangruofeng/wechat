<?php

class office_indexControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("null_layout");
        Tpl::setDir("layout");
    }
    public function indexOp(){
        echo 'Illegal entrance';
        Tpl::showPage("null_layout");
    }


}
