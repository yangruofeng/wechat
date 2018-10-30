<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/1/2015
 * Time: 9:01 PM
 */
class control
{
    function __construct()
    {
        // ****** 一定不要在这里（公用control）写任何私有逻辑
        //$this->checkLogin();
    }

    protected function checkLogin()
    {
        if (!getSessionVar("is_login") || !getSessionVar("user_info")) {
            $ref_url = request_uri();
            $login_url = getUrl("login", "login", array("ref_url" => urlencode($ref_url)), false, ENTRY_DESKTOP_SITE_URL);
            @header('Location:' . $login_url);
            die();
        } else {
            return operator::getUserInfo();
        }
    }

    public function getTplOp($p)
    {

        $tpl_name = $p['tpl'];
        $tpl_dir = $p['tpl_dir'];
        $data = $p['data'] ?: array();
        if (!$tpl_name) {
            echo "<div>Not Found Template!</div>";die;
        }
        $dir = $tpl_dir ?: "default";
        $dynamic = $p['dynamic'];
        if (is_array($dynamic)) {
            $api = $dynamic['api'];
            $method = $dynamic['method'] . "Op";
            $param = $dynamic['param'];
            @include_once(CURRENT_ROOT . "/control/" . $api . ".php");
            $api .= "Control";

            try {
                if ($api && $method) {
                    $cls = new $api();
                    $rt = $cls->$method($param);
                    if ($rt instanceof ormCollection) {
                        $rt = $rt->toArray();
                    }
                    if (!is_null($rt) && is_object($rt)) {
                        $rt = obj2array($rt);
                    }
                    $data = array_merge($data, $rt);
                }
            } catch (Exception $ex) {
                showMessage($ex->getMessage());
            }
        }
        Tpl::getTpl($tpl_name, $dir, $data);
    }

    public function getDebug()
    {
        return Tpl::showTrace();
    }

    public function uploadPictureOp()
    {
        $handler = new UploadFile();
        $dir = $_GET['dir'];
        if ($dir) {
            $handler->set("save_path", $dir);
        }
        $inputName = $_GET['inputName'];
        $result = $handler->upload($inputName);
        if ($result) {
            $full_path_1 = getUserIcon($handler->file_name, $handler->relative_path);
            $rt = new result(true, '', array(
                "file_name" => $handler->file_name,
                "relative_path" => $handler->relative_path,
                "full_path" => $handler->full_path,
                "full_path_1" => $full_path_1,
                "base_name" => $handler->base_name
            ));
        } else {
            $err = $handler->error;
            $rt = new result(false, $err);
        }

        ob_get_clean();
        echo json_encode($rt, true);
        ob_end_flush();
        die();
    }

    public function uploadFile()
    {
        $handler = new UploadFile();
        $dir = $_REQUEST['dir'];
        if ($dir) {
            $handler->set("save_path", $dir);
        }
        $handler->set("allow_type", array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf', 'tbi', 'xls', 'txt', 'pdf', 'xlsx', 'doc', 'docx', 'zip', 'rar', '7z', 'arj'));
        $inputName = $_REQUEST['inputName'];
        $result = $handler->upload($inputName);
        if ($result) {
            $rt = new result(true, '', array(
                "file_name" => $handler->file_name,
                "relative_path" => $handler->relative_path,
                "full_path" => $handler->full_path,
                "base_name" => $handler->base_name
            ));
        } else {
            $err = $handler->error;
            $rt = new result(false, $err);
        }

        ob_get_clean();
        echo json_encode($rt, true);
        ob_end_flush();
        die();
    }

    public function uploadPictureForEditor()
    {
        $handler = new UploadFile();
        $dir = $_GET['dir'];
        if ($dir) {
            $handler->set("save_path", $dir);
        }
        $inputName = $_GET['inputName'];
        $result = $handler->upload($inputName);
        if ($result) {
            $rt = array("err" => "", "msg" => $handler->full_path);
        } else {
            $err = $handler->error;
            $rt = array("err" => $err, "msg" => $handler->full_path);
        }
        ob_get_clean();
        echo json_encode($rt, true);
        ob_end_flush();
        die();
    }

    public function upload2UpYunOp($p)
    {
        $p = ($_POST);
        $file_key = $p['file_key'] ?: 'file_uploader';
        $save_path = $p['file_dir'] ?: '';

        if (!empty($_FILES[$file_key])) {
            $upload = new UploadFile();  // 每次需要重置，否则上次上传影响下次
            $upload->set('save_path', null);
            $upload->set('default_dir', $save_path);
            $re = $upload->server2upun($file_key);
            if ($re == false) {
                return new result(false, 'Upload photo fail');
            }
            $img_path = $upload->img_url;
            return new result(true, "Uploaded Success!", array(
                "image_path" => $img_path,
                "full_path" => getImageUrl($img_path, imageThumbVersion::MAX_240)
            ));
        }
        return new result(false, "No Choose Files");
    }

    public function downloadAttachFile()
    {
        $file_name = $_GET['file_name'];
        $file_path = _UPLOAD_ . DS . "attachment" . DS . $file_name;
        if (is_file($file_path)) {

            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=" . basename($file_path));
            $ua = $_SERVER["HTTP_USER_AGENT"];
            $encoded_filename = rawurlencode($file_name);
            if (preg_match("/MSIE/", $ua)) {
                header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
            } else if (preg_match("/Firefox/", $ua)) {
                header("Content-Disposition: attachment; filename*=\"utf8''" . $file_name . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $file_name . '"');
            }

            readfile($file_path);
            exit;
        } else {
            echo "No Found File！";
            exit;
        }
    }

    /**
     * 获取除默认语言以外的语言
     */
    protected function getLangWithoutDefault()
    {
        $lang_list = C('lang_type_list');
        $default_lang = $this->profile['default_language'] ?: 'en';
        unset($lang_list[$default_lang]);
        return $lang_list;
    }

    /**
     * upyun参数
     */
    public function getUploadParamOp()
    {
        $default_dir = empty($_GET['default_dir']) ? '' : $_GET['default_dir'];
        $upload = new UploadFile();
        if ($default_dir) {
            $upload->set('default_dir', $default_dir . DS);
        } else {
            $upload->set('default_dir', '');
        }
        $param = $upload->upload2upyun($_GET['multi']);
        echo my_json_encode($param);
    }

    public function alertExit($msg)
    {
        Tpl::setDir("widget");
        Tpl::setLayout("msg_layout");
        Tpl::output("msg", $msg);
        Tpl::showPage("msg");
    }

}
