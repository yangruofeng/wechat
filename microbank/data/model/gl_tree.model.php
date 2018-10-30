<?php

class gl_treeModel extends tableModelBase {
    public function __construct()
    {
        parent::__construct('gl_tree');
    }

    public function getAccountInfoById($uid){
        $info = $this->find(array('uid' => $uid));
        return $info;
    }

    public function getAccountList($params){
        $info = $this->select($params);
        return $info;
    }

    public function getChildrenByParentGlCode($id) {
        return $this->select(array('parent_gl_code' => $id));
    }

    public function getTopLevelAccounts() {
        return $this->getChildrenByParentGlCode(0);
    }

}