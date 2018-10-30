<?php

class versionClass {
    public static function checkUpdate($app_name, $version) {
        $version = $version?:'';
        if (!preg_match('/^\d+(?:\.\d+)+$/', $version)) $version='';
        $download_url = getConf('app_download_url');

        if( !$download_url ){
            return new result(false,'Download url config error',null,errorCodesEnum::CONFIG_ERROR);
        }

        $download_url = rtrim($download_url,'/');

        if( !$app_name ){
            return new result(false,'Lack of param',null,errorCodesEnum::DATA_LACK);
        }

        $m = new common_app_versionModel();
        $newest_version = $m->orderBy('uid desc')->getRow(array(
            'app_name' => $app_name,
            'version' => array('gt',$version)
        ));

        if( $newest_version  ){
            $newest_version->download_url = $download_url.'/'.$newest_version->download_url;
        }else{
            $newest_version = null;
        }

        $update_version = $m->orderBy('uid desc')->getRow(array(
            'app_name' => $app_name,
            'is_required' => 1,
            'version' => array('gt',$version)
        ));

        if( $update_version ){
            $update_version->download_url = $download_url.'/'.$update_version->download_url;
        }

        /********* !!!!!!一定要拼接地址   ***********/
        // 没有需要更新的版本，返回最新版本
        if( !$update_version ){
            return new result(true,'success',$newest_version);
        } else if ($newest_version->download_url_1) {
            // 如果最新版本有安装包，返回最新版本信息，并设置必须更新为1
            $newest_version->is_required = 1;
            return new result(true,'success',$newest_version);
        } else {
            // 如果最新版本没有安装包，返回必须更新版本信息
            return new result(true,'success',$update_version);
        }
    }
}