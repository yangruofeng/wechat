<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/6/6
 * Time: 13:41
 */
class error_logClass
{

    public static function coAppUploadErrorLog($input_name)
    {
        $default_dir ='app_log/co_app';
        $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
        $upload->set('save_path', null);
        $upload->set('default_dir', $default_dir);
        $up = $upload->upload($input_name,false);
        if( !$up ){
            return new result(false,'Upload fail:'.$upload->get('error'),null,errorCodesEnum::UNEXPECTED_DATA);
        }

        return new result(true,'success',array(
            'file_path' => $upload->full_path
        ));
    }


    public static function coAppAddErrorLog($content)
    {
        $date_dir = date('Ymd');
        $tmp_name = sprintf('%010d', time() - 946656000)
            . sprintf('%03d', microtime() * 1000)
            . sprintf('%04d', mt_rand(0, 9999));
        $file_dir = _UPLOAD_.'/app_log/co_app/'.$date_dir;
        $file_path = $file_dir.'/'.$tmp_name.'.txt';

        if (!is_dir($file_dir)) {
            if (!@mkdir($file_dir, 0755,true)) {
               return new result(false,'Make folder fail.',null,errorCodesEnum::NOT_SUPPORTED);
            }
        }
        @chmod($file_dir, 0755);

        $re = file_put_contents($file_path,$content);  // 是否权限问题，此方法不能创建目录
        if( $re == false ){
            return new result(false,'Add fail.',null,errorCodesEnum::UNEXPECTED_DATA);
        }
        return new result(true,'success',array(
            'file_path' => $file_path
        ));

    }

    public static function ls($path)
    {
        if( is_dir($path) ){
            $list = array();
            $ls = scandir($path);
            foreach( $ls as $d ){
                if( substr($d,0,1) === '.'){
                    continue;
                }
                $new_path = $path.'/'.$d;
                if( is_dir($new_path) ){
                    $list[] = array(
                        'is_dir' => true,
                        'dir_path' => $new_path,
                        'name' => $d,
                    );
                }else{
                    $list[] = array(
                        'is_dir' => false,
                        'dir_path' => $new_path,
                        'file_url' => str_replace(_UPLOAD_,getConf('project_site_url').'/data/upload',$new_path),
                        'name' => $d,
                    );
                }
            }
            return $list;
        }
        return array();
    }


}