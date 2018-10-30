<?php
/**
 * Created by PhpStorm.
 * User: Owen
 * Date: 9/26/2018
 * Time: 15:31 AM
 */
class test_owenControl extends control
{
    public function __construct()
    {
        Tpl::setLayout("empty_layout");
        Tpl::setDir("test_owen");
    }
    //用户列表 加载模版
    public function UserOp(){
        Tpl::showPage("user");
    }
    //ajax 数据请求   用户列表
    public function getUserListOp(){
        $param = array_merge(array(), $_GET, $_POST);
        $page_num = $param['pageNumber'];
        $page_size = $param['pageSize'];
        //请求方法 class
        $page = test_owenClass::getUserList($page_num,$page_size);

        return $page;
    }

    //新增用户 output
    public function testAddUserOp(){

        //暂时先写个test数据
        $param = array();
        $param['user_name'] = "Jay";
        $param['password']  = '123456';
        $param['gender']    = 1;
        //$class_user = new userClass();
        //$rt = $class_user->addUser($p);
        $rt = test_owenClass::AddUser($param);
        if( $rt->STS ){
            return new result(true, 'New user success', $param);
        }else{
            return new result(false, 'New user failed', $param);
        }
    }

}