<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/10/9
 * Time: 13:48
 */
class test_mysqlControl extends baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setDir('test_mysql');
    }

    /* 测试结果
     * 1. 在事务内插入的数据，未提交事务时，在事务内可以查询到数据，在事务外不能查询到数据
     * 2. 在事务内做数据库的更新，未提交事务的时候，在事务内查询，是更新后的数据，在事务外查询，数据是未跟新状态，只有事务提交后才会更新数据
     * */

    public function insertDataByTransactionOp()
    {
        $params = array_merge($_GET,$_POST);
        if( $params['form_submit'] == 'ok' ){
            $m = new test_tableModel();
            $conn = $m->conn;
            $conn->startTransaction();
            try{
                $row = $m->newRow();
                $row->name = $params['name'];
                $row->age = intval($params['age']);
                $row->update_time = date('Y-m-d H:i:s');
                $in = $row->insert();
                if( $in->STS ){
                    // 故意延迟提交，看是否可以查询到数据
                    $data = $m->orderBy('uid desc')->find(array(
                        'uid' => array('>',0)
                    ));
                    print_r($data);
                    sleep(120);
                    $conn->submitTransaction();
                    showMessage('success');
                }else{
                    $conn->rollback();
                    showMessage('fail');
                }

            }catch (Exception $e){
                showMessage($e->getMessage());
            }
        }

        Tpl::showPage('insert.data.page');
    }


    public function updateDataOp()
    {
        $uid = 1;
        $m = new test_tableModel();
        $conn = $m->conn;
        $conn->startTransaction();
        try{
            $row = $m->getRow($uid);

            // 更新前的数据
            print_r($row->toArray());

            $row->age += 1;
            $row->update_time = Now();
            $up = $row->update();
            if( $up->STS ){

                $data = $m->find(array(
                    'uid' => $uid
                ));
                // 更新后的数据，事务还未提交
                print_r($data);
                sleep(30);
                $conn->submitTransaction();
                showMessage('success');

            }else{
                $conn->rollback();
                showMessage('Update fail.');
            }


        }catch (Exception $e){
            showMessage($e->getMessage());
        }
    }

    public function getDataListOp()
    {
        $m = new test_tableModel();
        $list = $m->select('1=1');
        Tpl::output('list',$list);
        Tpl::showPage('data.list.page');
    }

}