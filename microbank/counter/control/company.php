<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/12
 * Time: 13:52
 */
class companyControl extends counter_baseControl
{

    public function __construct()
    {
        parent::__construct();
        Tpl::setDir("company");
        Language::read('company');
        Tpl::setLayout('home_layout');
        $this->outputSubMenu('company');

    }

    public function indexOp(){
        Tpl::showPage("coming.soon");
    }

}