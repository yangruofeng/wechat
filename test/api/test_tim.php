<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/10/28
 * Time: 21:48
 */
class loanCredit{
    function releaseCredit(){
        $task=new taskItem();

        $wf=new workflow();
        $wf->invokeTask();
    }
}
class taskItem{
    function invoke(){

    }
}
class workflow{

}