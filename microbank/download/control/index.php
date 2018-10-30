<?php

/**
 * Created by PhpStorm.
 * User: 43070
 * Date: 2017/10/16
 * Time: 10:44
 */
class indexControl
{
    public function __construct()
    {
        Tpl::output('html_title', 'APP DownLoad');
        Tpl::setLayout('empty_layout');
        Tpl::setDir('index');
    }

    public function indexOp()
    {

        $m = new common_app_versionModel();

        $download_url = getConf('app_download_url');
        $member_app = $m->orderBy('uid desc')->find(array(
            'app_name' => appTypeEnum::MEMBER_APP
        ));
        if ($member_app) {
            $member_app['download_url_android'] = $download_url . '/' . $member_app['download_url'];
        }

        $operator_app = $m->orderBy('uid desc')->find(array(
            'app_name' => appTypeEnum::OPERATOR_APP
        ));
        if ($operator_app) {
            $operator_app['download_url'] = $download_url . '/' . $operator_app['download_url'];

        }

        Tpl::output('member_app', $member_app);
        Tpl::output('operator_app', $operator_app);

        Tpl::output('header_title', 'APP DownLoad');
        Tpl::showPage('app.download');
    }
}