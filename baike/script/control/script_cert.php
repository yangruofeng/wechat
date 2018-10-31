<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/26
 * Time: 16:28
 */
class script_certControl
{



    public function updateExpireCertListOp()
    {
        $m_cert = new member_verify_certModel();

        $sql = "select * from member_verify_cert  where cert_expire_time is not null 
        and verify_state='".certStateEnum::PASS."' and date_format(cert_expire_time,'%Y%m%d')<'".date('Ymd')."' ";
        $rows = $m_cert->reader->getRows($sql);

        $result = array(
            'success' => array(),
            'fail' => array()
        );
        foreach( $rows as $v ){
            $rt = memberIdentityClass::updateCertFileExpired($v['uid']);
            if( $rt->STS ){
                $result['success'][] = $v['uid'];
            }else{
                $result['fail'][] = $v['uid'];
            }
        }


        $date = date('Y-m-d H:i:s');
        echo $date." \n";
        print_r($result);

    }



    /**
     *  更新过期的认证资料
     */
    public function updateExpireCertList_oldOp()
    {

        $m_cert = new member_verify_certModel();


        $sql = "update member_verify_cert set verify_state='".certStateEnum::EXPIRED."' where cert_expire_time is not null 
        and verify_state='".certStateEnum::PASS."' and date_format(cert_expire_time,'%Y%m%d')<'".date('Ymd')."' ";

        $up = $m_cert->conn->execute($sql);


        $date = date('Y-m-d H:i:s');
        if( !$up->STS ){
            echo $date." -> success \n";
        }

        echo $date." -> success \n";
    }


}