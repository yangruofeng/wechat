<?php

/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2018/9/26
 * Time: 下午5:20
 */
class test_owenClass
{
//    static function getCODofTeller($teller_id)
//    {
//        return userClass::getPassbookBalanceOfUser($teller_id);
//    }

    /*
     * 新增用户
     * $p 用户数据  //新增的时候直接操作database
     */
    static function AddUser($param)
    {
        $user_name = $param['user_name'];
        $password  = $param['password'];
        $gender    = $param['gender'];
        if (!$user_name || !$password || !$gender) {
            return new result(false, 'IUsername or password cannot be empty', $param);
        }
        $m_test_user = M('test_user');

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $m_test_user->newRow();
            $row->user_name = $user_name;
            $row->password = md5($password);
            $row->gender = $gender;
            $rt = $row->insert();

            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Add failed');
            }
            $conn->submitTransaction();
            return new result(true, 'Add Successful');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, 'Add Failed');
        }

    }
    /*
     * 查询用户列表   model中操作database
     */
    public function getUserList($page_num,$page_size){
        //还需要优化
        $page_num = $page_num ?: 1;
        $page_size = $page_size ?: 20;
        //调用model查询
        $m_user = new test_owenModel();
        return $m_user->getUserList($page_num,$page_size);
    }
    /*
     * 删除用户
     */
    public function deleteOneUser($uid){

        $m_test_user = M('test_user');
        return $m_test_user->delete($uid);
    }
    /*
     * 修改用户信息
     */
    public function editUser($p){
        $uid = $p['uid'];
        $m_um_user = M('test_user');
        $row = $m_um_user->getRow(array('uid' => $uid));
        if (empty($row)) {
            return new result(false, 'Invalid Id!');
        }
        $row->user_name = $p['user_name'];
        $row->password = md5($p['password']);
        $row->gender = $p['gender'];
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Edit success');
        }else{
            return new result(false, 'Edit failed');
        }

    }
    /*
     * 获取单个用户信息
     */
    public function getOneUser($uid){
        $m_um_user = M('test_user');
        $row = $m_um_user->getRow(array('uid' => $uid));
        if (empty($row)) {
            return new result(false, 'Invalid Id!');
        }else{
            return new result(true, $row);
        }
    }





}