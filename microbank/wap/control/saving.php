<?php

class savingControl {
  public function __construct(){
    Language::read('act,label,tip');
    Tpl::setLayout('empty_layout');
    Tpl::setDir('saving');
  }

  public function indexOp(){
    Tpl::output('html_title', 'Saving');
    Tpl::output('header_title', 'Saving');
    Tpl::output('nav_footer', 'saving');
    Tpl::showPage('index');
  }
}
