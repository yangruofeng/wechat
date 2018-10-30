<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 6/7/2015
 * Time: 11:02 PM
 * 模拟其他开发语言中的enum，没有enum真他妈不爽
 */

abstract class Enum {
    public function toArray(){
        $cn=get_class($this);
        $f=new ReflectionClass($cn);
        $cst=$f->getConstants();
        return $cst;
    }
    public function toString(){
        $arr=$this->Values();
        return implode(",",$arr);

    }
    public function Keys(){
        $arr=$this->toArray();
        return array_keys($arr);
    }
    public function Values(){
        $arr=$this->toArray();
        return array_values($arr);
    }
    public function Dictionary($prefix=""){
        $keys=$this->Keys();
        $values=$this->Values();
        $rt=array();
        foreach($keys as $i=>$k){
            if($prefix){
                $k=substr($k,strlen($prefix));
            }
            $k=str_replace("_"," ",$k);
            $rt[$values[$i]]=$k;
        }
        return $rt;
    }

}



