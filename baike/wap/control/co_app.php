<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/27
 * Time: 19:57
 */
class co_appControl
{
    public function __construct()
    {
        Tpl::setLayout('empty_layout');
        Tpl::setDir('account');
    }


    public function tellerReceiveConfirmOp()
    {
        // 二维码扫描界面
        $uid = $_GET['uid'];
        $m = new biz_obj_transferModel();
        $data = $m->find(array(
            'uid' => $uid
        ));

        Tpl::output('data',$data);
        Tpl::showPage('receive.confirm');
    }

    public function submitReceiveConfirmOp()
    {
        $params = array_merge($_GET,$_POST);
        $biz_id = $params['uid'];
        $trading_password = $params['trading_password'];
        $type = $params['type'];

        $biz_info = (new bizCoTransferToTellerClass())->getBizDetailById($biz_id);
        if( !$biz_info ){
            return new result(false,'No biz info!');
        }

        $user = (new um_userModel())->getRow(array(
            'obj_guid' => $biz_info['receiver_obj_guid']
        ));

        $userObj = new objectUserClass($user['uid']);
        $chk = $userObj->checkTradingPassword($trading_password);
        if( !$chk->STS ){
            return $chk;
        }

        try{
            $conn = ormYo::Conn();
            $conn->startTransaction();
            if( $type == 1 ){
                $rt = (new bizCoTransferToTellerClass())->confirm($biz_id);
            }else{
                $rt = (new bizCoTransferToTellerClass())->cancel($biz_id);
            }
            if( !$rt->STS ){
                $conn->rollback();
            }
            $conn->submitTransaction();
            return $rt;

        }catch( Exception $e )
        {
            return new result(false,$e->getMessage());
        }


    }

}