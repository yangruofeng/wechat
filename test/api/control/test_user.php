<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/10/17
 * Time: 13:41
 */

class test_userControl extends Control
{
    public function __construct()
    {
        parent::__construct();
    }

    //新增用户  API
    public static function testAddUserOp()
    {

        //暂时先写个test数据
        $param = array_merge(array(),$_GET,$_POST);
        $param['user_name'] = "Jay";
        $param['password']  = '123456';
        $param['gender']    = 1;

        $rt = test_owenClass::AddUser($param);
        if( $rt->STS ){
            return new result(true, 'New user success', $param);
        }else{
            return new result(false, 'New user failed', $param);
        }
    }

    //获取用户列表操作  API
    public function getUserListOp(){

        $param = array_merge(array(),$_GET,$_POST);
        $page_num = $param['page_num'];
        $page_size = $param['page_size'];
        $re = test_owenClass::getUserList($page_num,$page_size);
        return new result(true,'success',$re);

    }

    //删除单个||某一个用户 API
    public function deleteUserOp(){
        //测试uid 写死  已删除成功
//        $param = array_merge(array(),$_GET,$_POST);
//        $uid = $param['uid'];
        $uid = 2;  //该ID在测试的时候，随意输入获取
        $rt  = test_owenClass::deleteOneUser($uid);
        if( $rt->STS ){
            return new result(true, 'successfully deleted', $uid);
        }else{
            return new result(false, 'failed to delete', $uid);
        }
    }

    //修改用户信息
    public function UpdateOneUserOp(){
        $p = array_merge(array(), $_GET, $_POST);
        //test data
        $p['uid'] = 3;
        $p['user_name'] = 'blank';
        $p['password'] = '456789';
        $p['gender'] = 2;
        $p['form_submit'] = 'nook';
        $test_owen = new test_owenClass();
        if ($p['form_submit'] == 'ok') {
            //修改用户信息
            $rt = $test_owen->editUser($p);
            if ($rt->STS) {
                return new result(true, 'Successfully modified');
            } else {
                return new result(false, 'fail to edit');
            }
        }else{
            //获取用户信息
            $rt = $test_owen->getOneUser($p['uid']);
            if ($rt->STS) {
                return new result(true, 'Successfully obtained user information', $rt);
            } else {
                return new result(false, 'Failed to get user information');
            }
        }

    }


}