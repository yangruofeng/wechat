<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/13
 * Time: 17:26
 */
class bank_appControl extends bank_apiControl
{

    /**
     * 获取APP的最新版本信息
     * @return result
     */
    public function getVersionOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $app_name = $params['app_name'];
        $version = $params['version'];

        return versionClass::checkUpdate($app_name, $version);
    }
}