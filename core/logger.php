<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/26/2015
 * Time: 10:30 PM
 */
class logger{
    private static $log =   array();
    static  function record($log_name=false,$log_content,$save_path=null){
        if(!is_string($log_name)) return false;
        if(is_object($log_content) || is_array($log_content)){
            $log_content=json_encode($log_content);
        }
        $prefix="--".date('ymd His').": ";
        $suffix="\n";//for all

        $path = _LOG_;
        if( $save_path ){
            $path .= '/'.$save_path;
        }
        if( !is_dir($path) ){
            @mkdir($path,0755,true);
        }
        $file_path=$path .'/'.$log_name."-".date('Ymd').".log";
        $log_content=mb_convert_encoding( $log_content, 'UTF-8', 'UNICODE,UTF-8,GBK,GB2312,BIG5');
        self::$log[]=$log_content;
        file_put_contents($file_path,$prefix.$log_content.$suffix,FILE_APPEND);
        return true;
    }
    public static function History(){
        return self::$log;
    }

}