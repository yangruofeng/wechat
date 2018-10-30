<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/6
 * Time: 11:41
 */
class error_logControl extends bank_apiControl
{

    public function coAppUploadErrorLogOp()
    {
        $params = array_merge(array(),$_GET,$_POST);

        if( !empty($_FILES['log_file'])){
            $rt = error_logClass::coAppUploadErrorLog('log_file');
            if( !$rt->STS ){
                return $rt;
            }
        }

        if( $params['log_text'] ){
            $log_text = urldecode($params['log_text']);
            $rt = error_logClass::coAppAddErrorLog($log_text);
            if( !$rt->STS ){
                return $rt;
            }

        }

        return new result(true,'success');

    }

    public function recordOp() {
        $params = array_merge(array(), $_GET, $_POST);
        $log_name = $params['log_name'] ?: "unclassified";
        $log_content = $params['log_content'];

        logger::record($log_name, $log_content);
        return new result(true);
    }
}