<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 7/3/2018
 * Time: 4:38 PM
 */

class gl_treeControl extends counter_baseControl{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout('home_layout');
        Tpl::output("html_title", "Gl Account List");
        Tpl::setDir("gl");
        $this->outputSubMenu('gl_account');
    }
    function indexOp(){
        $gl_tree=new gl_treeModel();
        $top_list=$gl_tree->getTopLevelAccounts();
        Tpl::output("node_list",$top_list);
        Tpl::showPage("tree.style.default");
    }
    function getChildrenNodeOp($p){
        $gl_code=$p['parent_gl_code'];
        $gl_tree=new gl_treeModel();
        $chd_list=$gl_tree->getChildrenByParentGlCode($gl_code);
        return $chd_list;
    }
    function showTableStyleOp(){
        $gl_tree=new gl_treeModel();
        $list=$gl_tree->select("1=1");
        Tpl::output("node_list",$list);
        Tpl::showPage("tree.style.table");
    }
    function showUserDefinedOp(){
        $gl_tree=new gl_treeModel();
        $min_level=$gl_tree->reader->getOne("SELECT IFNULL(MIN(gl_level),0) FROM gl_tree WHERE is_system=0");
        if($min_level>0){
            $list=$gl_tree->select("is_system=0 and gl_level=".$min_level);
            Tpl::output("node_list",$list);
        }
        Tpl::showPage("tree.style.define");
    }

}