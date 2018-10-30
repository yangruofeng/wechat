<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/13
 * Time: 17:24
 */
class common_app_versionModel extends tableModelBase
{

    function __construct()
    {
        parent::__construct('common_app_version');
    }

    public function addVersion($p)
    {
        $app_name = trim($_POST['app_name']);
        $version = trim($_POST['version']);
        $remark = trim($_POST['remark']);
        $is_required = intval($_POST['is_required']);
        $creator_id = intval($p['creator_id']);
        $creator_name = trim($p['creator_name']);
        if (empty($app_name) || empty($version)) {
            return new result(false, 'App name and version cannot be empty!');
        }

        $chk_version = $this->find(array('app_name' => $app_name, 'version' => $version));
        if ($chk_version) {
            return new result(false, 'Version exists!');
        }

        $handler = new UploadFile();
        $handler->set("save_path", $app_name);
        $handler->set('maxAttachSize', 500 * 1024 * 1024);  // 重置默认的最大附件大小
//        $handler->set("file_name", $app_name . '_' . $version);
        @ini_set('upload_max_filesize', '500M');
        @chmod(BASE_DATA_PATH . DS . 'downloads', 0755);
        $handler->set("upload", BASE_DATA_PATH . DS . 'downloads');
        $handler->set("allow_type", array('apk', 'ipa', 'pxl', 'deb', 'zip', 'rar'));
        $result = $handler->upload('app_file');

        if (!$result) {
            return new result(false, 'App file 1 upload failed!');
        }
        $download_url = $app_name . DS . $handler->file_name;

        if ($_FILES['app_file_2']['name']) {
            $handler->file_name = null;
            $result = $handler->upload('app_file_2');
            if (!$result) {
                return new result(false, 'App file 2 upload failed!');
            }
            $download_url_1 = $app_name . DS . $handler->file_name;
        } else {
            $download_url_1 = '';
        }

        $row = $this->newRow();
        $row->app_name = $app_name;
        $row->version = $version;
        $row->download_url = $download_url;
        $row->download_url_1 = $download_url_1;
        $row->is_required = $is_required;
        $row->remark = $remark;
        $row->creator_id = $creator_id;
        $row->creator_name = $creator_name;
        $row->create_time = Now();
        $rt = $row->insert();
        if ($rt->STS) {
            return new result(true, 'Add successful!');
        } else {
            return new result(false, 'Add failed--' . $rt->MSG);
        }

    }
}