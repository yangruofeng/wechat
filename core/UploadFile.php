<?php
/**
 * 文件上传类
 *
 *
 *
 * @package    library
 * @copyright  Copyright (c) 2007-2013 KHBuy Inc. (http://www.KHBuy.com)
 * @license    http://www.KHBuy.com
 * @link       http://www.KHBuy.com
 * @author       KHBuy Team
 * @since      File available since Release v1.1
 */

/**
 * 初始化
 *
 *    $upload = new UploadFile();
 *    $upload->set('default_dir','upload');
 *    $upload->set('max_size',1024);
 *    //生成4张缩略图，宽高依次如下
 *    $thumb_width    = '300,600,800,100';
 *    $thumb_height    = '300,600,800,100';
 *    $upload->set('thumb_width',    $thumb_width);
 *    $upload->set('thumb_height',$thumb_height);
 *    //4张缩略图名称扩展依次如下
 *    $upload->set('thumb_ext',    '_small,_mid,_max,_tiny');
 *    //生成新图的扩展名为.jpg
 *    $upload->set('new_ext','jpg');
 *    //开始上传
 *    $result = $upload->upfile('file');
 *    if (!$result){
 *        echo '上传成功';
 *    }
 *
 */

defined('InKHBuy') or exit('Access Invalid!');

class UploadFile
{
    /**
     * 文件存储位置
     */
    public $img_url;
    /**
     * 文件存储路径
     */
    public $save_path;
    /**
     * 允许上传的文件类型
     */
    private $allow_type = array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf', 'tbi');

    protected $image_mime = array(
        'bmp' => 'image/x-ms-bmp',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'tga' => 'image/x-targa',
        'psd' => 'image/vnd.adobe.photoshop',
    );

    /**
     * 上传文件名
     */
    public $file_name;
    public $full_path;
    public $relative_path;
    public $file_path;  // 上传的相对路径
    public $base_name;
    public $upload;
    /**
     * 上传文件后缀名
     */
    private $ext;

    /**
     * 默认文件存放文件夹
     */
    private $default_dir = "default";
    /**
     * 错误信息
     */
    public $error = '';
    /**
     * 生成的缩略图，返回缩略图时用到
     */
    public $thumb_image;

    private $maxAttachSize = 10737418240;  // 改成10M，免得经常API因为大小出错
    private $dir_type = '3';//存储路径类型

    private $upload_file;


    function __construct()
    {
        $upyun_param = C('upyun_param');
        $this->upyun = new UpYun($upyun_param['bucket'], $upyun_param['user_name'], $upyun_param['pwd']);
        $this->opts = array(
            UpYun::X_GMKERL_THUMBNAIL => 'thumbtype'
        );
    }

    /**
     * 设置
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * 读取
     */
    public function get($key)
    {
        return $this->$key;
    }


    public function upload($inputName, $check_type = true)
    {
        $tempFile = $this->getTempFileName();
        $err = '';
        $localName = '';
        //设置temp路径
        if (isset($_SERVER['HTTP_CONTENT_DISPOSITION']) && preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i', $_SERVER['HTTP_CONTENT_DISPOSITION'], $info)) {//HTML5上传
            file_put_contents($tempFile, file_get_contents("php://input"));
            $localName = urldecode($info[2]);
        } else {
            $upfile = @$_FILES[$inputName];
            if (!isset($upfile)) $err = 'Invalid File Field';
            elseif (!empty($upfile['error'])) {
                switch ($upfile['error']) {
                    case '1':
                        $err = 'The Size of File Exceeds Limit Of PHP :'.$upfile['size'];
                        break;
                    case '2':
                        $err = 'The Size of File Exceeds Limit Of Html:'.$upfile['size'];
                        break;
                    case '3':
                        $err = 'File Upload is not complete';
                        break;
                    case '4':
                        $err = 'Empty File';
                        break;
                    case '6':
                        $err = 'Lack of Temporary Folder';
                        break;
                    case '7':
                        $err = 'Write Failed';
                        break;
                    case '8':
                        $err = 'Upload Interrupt';
                        break;
                    case '999':
                    default:
                        $err = 'Unknown Exception';
                }
            } else {
                if (@move_uploaded_file($upfile['tmp_name'], $tempFile)) {
                    $localName = $upfile['name'];
                } else {
                    $this->setError("Upload Failed");
                    return false;
                }
            }
        }

        if ($err) {
            $this->setError($err);
            @unlink($tempFile);
            return false;
        }
        $fileInfo = pathinfo($localName);
        $this->set("base_name", $fileInfo['basename']);
        $extension = $fileInfo['extension'];
        $extension = strtolower($extension);

        $bytes = filesize($tempFile);
        if ($bytes > $this->maxAttachSize) {
            $err = 'The File Size is '.$bytes.', More Than limit of ' . $this->formatBytes($this->maxAttachSize) . '';
        }

        if ($check_type) {

            if (!in_array($extension, $this->allow_type)) {
                $err = "File Type Not Allowed";
            }
        }


        if ($err) {
            $this->setError($err);
            @unlink($tempFile);
            return false;
        }
        //开始移动文件
        $relativePath = $this->getRelativePath();
        if ($this->file_name) {
            $realFileName = $this->file_name;
        } else {
            $realFileName = $this->getRealFileName($extension);
        }

        $defaultDir = ($this->upload ?: _UPLOAD_) . DS . $this->default_dir;
        if (!is_dir($defaultDir)) {
            if (!@mkdir($defaultDir, 0755, true)) {
                $this->setError("Make Folder Failed:" . $defaultDir);
                @unlink($tempFile);
                return false;
            }
        }
        $attachDir = ($this->upload ?: _UPLOAD_) . DS . $relativePath;
        if (!is_dir($attachDir)) {
            if (!@mkdir($attachDir, 0755, true)) {
                $this->setError("Make Folder Failed:" . $attachDir);
                @unlink($tempFile);
                return false;
            } else {
                @fclose(fopen($attachDir . '/index.htm', 'w'));
            }
        }
        $targetFile = $attachDir . DS . $realFileName;
        rename($tempFile, $targetFile);
        @chmod($targetFile, 0755);

        $full_path = ($this->upload ?: _UPLOAD_) . DS . $relativePath . DS . $realFileName;


        $this->set("ext", $extension);
        $this->set("file_size", filesize($targetFile));
        $this->set("file_name", $realFileName);
        $this->set("full_path", $full_path);
        $this->file_path = $relativePath . DS . $realFileName;
        $this->set("relative_path", $relativePath);

        @unlink($tempFile);

        return true;


    }

    private function getRealFileName($ext = "", $prefix = "")
    {
        $tmp_name = sprintf('%010d', time() - 946656000)
            . sprintf('%03d', microtime() * 1000)
            . sprintf('%04d', mt_rand(0, 9999));
        $tmp_name = ($prefix ? $prefix . "_" : "") . $tmp_name . '.' . $ext;
        return $tmp_name;
    }

    private function getRelativePath()
    {
        if ($this->save_path) {
            $tmp = $this->save_path;
        } else {

            $tmp = ($this->default_dir !== null) ? $this->default_dir : "default";
            $sub_dir = $this->getSysSetPath();
            if ($sub_dir) {
                $tmp .= DS . $sub_dir;
            }
        }

        return $tmp;
    }

    private function getTempFileName()
    {
        $tmp = _UPLOAD_ . DS . "temp";
        if (!is_dir($tmp)) {
            if (!@mkdir($tmp, 0755)) {
                $this->setError("Make Temporary Folder Failed");
                return false;
            }
        }
        $tmp_name = sprintf('%010d', time() - 946656000)
            . sprintf('%03d', microtime() * 1000)
            . sprintf('%04d', mt_rand(0, 9999));
        $tmp_name .= ".tmp";
        return $tmp . DS . $tmp_name;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * 设置错误信息
     *
     * @param string $error 错误信息
     * @return bool 布尔类型的返回结果
     */
    private function setError($error)
    {
        $this->error = $error;
    }

    /**
     * 根据系统设置返回商品图片保存路径
     */
    public function getSysSetPath()
    {
        switch ($this->dir_type) {
            case "1":
                //按文件类型存放,例如/a.jpg
                $subpath = "";
                break;
            case "2":
                //按上传年份存放,例如2011/a.jpg
                $subpath = date("Y", time()) . "/";
                break;
            case "3":
                //按上传年月存放,例如2011/04/a.jpg
                $subpath = date("Ymd");
                break;
            case "4":
                //按上传年月日存放,例如2011/04/19/a.jpg
                $subpath = date("Y", time()) . "/" . date("m", time()) . "/" . date("d", time()) . "/";
                break;
            default:
                $subpath = '';
        }
        return $subpath;
    }

    function jsonString($str)
    {
        return preg_replace("/([\\\\\/'])/", '\\\$1', $str);
    }

    function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
        } elseif ($bytes >= 1048576) {
            $bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
        } elseif ($bytes >= 1024) {
            $bytes = round($bytes / 1024 * 100) / 100 . 'KB';
        } else {
            $bytes = $bytes . 'Bytes';
        }
        return $bytes;
    }

    /**
     *生成上传参数
     */
    public function upload2upyun($multi = '')
    {
        if (empty($this->file_name)) {
            $this->file_name = $this->getRealFileName();
        }
        if ($multi) {
            $file_name = substr($this->file_name, 0, strlen($this->file_name) - 1);
            $file = $this->default_dir . $file_name . '_{random}{.suffix}';
        } else {
            $file = $this->default_dir . $this->file_name . '{suffix}';
        }
        $config = C('upyun_param');

        $operator = $config['user_name'];
        $method = 'POST';
        $uri = '/' . $config['bucket'];
        $password = md5($config['pwd']);

        $policy_arr = array(
            'bucket' => $config['bucket'],
            'save-key' => $file,
            'expiration' => time() + 86400,
        );

        $policy = base64_encode(json_encode($policy_arr));
        $signature = base64_encode(hash_hmac('sha1', $method . '&' . $uri . '&' . $policy , $password, true));
//        $signature = base64_encode(hash_hmac('sha1', 'POST&/upyun-temp&Wed, 09 Nov 2016 14:26:58 GMT&eyJidWNrZXQiOiAidXB5dW4tdGVtcCIsICJzYXZlLWtleSI6ICIvZGVtby5qcGciLCAiZXhwaXJhdGlvbiI6ICIxNDc4Njc0NjE4IiwgImRhdGUiOiAiV2VkLCA5IE5vdiAyMDE2IDE0OjI2OjU4IEdNVCIsICJjb250ZW50LW1kNSI6ICI3YWM2NmMwZjE0OGRlOTUxOWI4YmQyNjQzMTJjNGQ2NCJ9&7ac66c0f148de9519b8bd264312c4d64', '482c811da5d5b4bc6d497ffa98491e38', true));
        $param = array(
            'policy' => $policy,
            'authorization' => "UPYUN $operator:$signature",
        );
        return $param;
    }

    /* 后台上传到upun */
    public function server2upun($field, $s_type = false)
    {
        //上传文件
        $this->upload_file = $_FILES[$field];
        if ($this->upload_file['tmp_name'] == "") {
            $this->setError('cant_find_temporary_files');
            return false;
        }
        //对上传文件错误码进行验证
        $error = $this->fileInputError();
        if (!$error) {
            return false;
        }
        //验证是否是合法的上传文件
        if (!is_uploaded_file($this->upload_file['tmp_name'])) {
            $this->setError('upload_file_attack');
            return false;
        }

        //验证文件大小
        if ($this->upload_file['size'] == 0) {
            $error = 'upload_file_size_none';
            $this->setError($error);
            return false;
        }

        // 文件最大大小
        $file_size = @filesize($this->upload_file['tmp_name']);
        if ( $file_size > $this->maxAttachSize) {
            $error = 'upload_file_size_cant_over:' . $this->maxAttachSize . 'B'.'('.$file_size.')';
            $this->setError($error);
            return false;
        }


        //检查是否为有效图片
        if (!$image_info = @getimagesize($this->upload_file['tmp_name'])) {
            $error = 'upload_image_is_not_image';
            $this->setError($error);
            return false;
        }

        // 支持类型
        $image_mime = $image_info['mime'];
        if (!in_array($image_mime, $this->image_mime)) {
            $error = 'upload_image_type_not_allowed';
            $this->setError($error);
            return false;
        }

        //文件后缀名
        $mime_type = array_flip($this->image_mime);
        $tmp_ext = $mime_type[$image_mime] ?: 'jpg';
        $this->ext = $tmp_ext;

        /*$tmp_ext = explode(".", $this->upload_file['name']);
        $tmp_ext = $tmp_ext[count($tmp_ext) - 1];
        $this->ext = strtolower($tmp_ext);*/

        //设置图片路径
        $this->dir_type = 1;

        //设置文件名称
        if (empty($this->file_name)) {
            $this->file_name = $this->getRealFileName($this->ext);
        }

        if ($this->error != '') return false;
        $this->relative_path = $this->getRelativePath();
        $this->img_url = $this->relative_path . '/' . $this->file_name;
        $fh = fopen($this->upload_file['tmp_name'], 'rb');
        $upyun_param = C('upyun_param');
        $result = $this->upyun->writeFile(DS . $upyun_param['oss_url_prefix'] . $this->img_url, $fh, True, $this->opts);   // 上传图片，自动创建目录
        fclose($fh);
        if ($upyun_param['oss_url_prefix']) {
            $this->full_path = $upyun_param['upyun_url'] . DS . $upyun_param['oss_url_prefix'] . '/' . $this->img_url;
        } else {
            $this->full_path = $upyun_param['upyun_url'] . DS . $this->img_url;
        }
        return $result;

    }

    /**
     * 获取上传文件的错误信息
     *
     * @param string $field 上传文件数组键值
     * @return string 返回字符串错误信息
     */
    private function fileInputError()
    {
        switch ($this->upload_file['error']) {
            case 0:
                //文件上传成功
                return true;
                break;

            case 1:
                //上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值
                $this->setError('upload_file_size_over');
                return false;
                break;

            case 2:
                //上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值
                $this->setError('upload_file_size_over');
                return false;
                break;

            case 3:
                //文件只有部分被上传
                $this->setError('upload_file_is_not_complete');
                return false;
                break;

            case 4:
                //没有文件被上传
                $this->setError('upload_file_is_not_uploaded');
                return false;
                break;

            case 6:
                //找不到临时文件夹
                $this->setError('upload_dir_chmod');
                return false;
                break;

            case 7:
                //文件写入失败
                $this->setError('upload_file_write_fail');
                return false;
                break;

            default:
                return true;
        }
    }


    /**
     * 删除upyun文件
     */
    public function deleteFile($file = '')
    {

        if (!empty($file)) {
            try {
                $this->upyun->delete($file);
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }
}
