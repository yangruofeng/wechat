<?php

class form_validateControl extends control {
    public function __construct()
    {
        Tpl::setLayout("empty_layout");
        Tpl::setDir("form_validate");
        Tpl::output("html_title", "表单验证模式");
    }

    public function indexOp() {
        Tpl::showPage("index");
    }

    public function valid_unameOp(){
      echo json_encode(array('info' => '验证成功', 'status' => 'y'));
    }

    public function validOp(){
      $params = array_merge(array(), $_GET, $_POST);
      $obj_validate = new Validate();
      $obj_validate->deliverparam = $params;
      $error = $obj_validate->validate();
      if ($error != ''){
        showMessage($error,'','html','error');
      }
    }

    function check_memberOp(){
      echo 'true';
      //echo 'false';
    }
}
