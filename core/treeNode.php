<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 6/28/2015
 * Time: 5:46 PM
 */
class treeNode{
    public $uid;
    public $pid;
    public $node_text;
    public $node_text_local;//本地语言
    public $node_path="";
    public $node_path_text="";
    public $node_path_text_local="";//本地语言路径
    public $node_level=0;
    public $is_end=0;
    public $children=array();
    public $ext_id=0;
    function __construct($initArr){
        if(is_array($initArr)){
            $this->uid=$initArr['uid'];
            $this->pid=$initArr['pid'];
            $this->node_text=$initArr['node_text'];
            $this->node_path=$initArr['node_path'];
            $this->node_level=$initArr['node_level'];
            $this->node_path_text=$initArr['node_text'];
            $this->node_text_local=$initArr['node_text_local'];
            $this->node_path_text_local=$initArr['node_text_local'];
            $this->is_end=$initArr['is_end'];
            $this->children=array();
        }
    }
    public function findChild($chd_key){
        if(!$chd_key) return null;
        if($this->uid==$chd_key) return $this;
        if(!count($this->children)) return null;
        foreach($this->children as $nd){
            if($nd->uid==$chd_key){
                return $nd;
            }
            if(count($nd->children)>0){
                $rt_nd=$nd->findChild($chd_key);
            }
            if($rt_nd){
                return $rt_nd;
            }
        }
        return null;
    }
    public function toArray(){
        return obj2array($this);
    }

}