<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/9/18
 * Time: 17:44
 */
class testUploadFileApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Test upload file";
        $this->description = "测试文件上传";
        $this->url = C("bank_api_url") . "/test.upload.file.php";

        $this->parameters = array();

        $this->parameters[]= new apiParameter("upload_file", "上传文件，文件流", null, true);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => ''
        );
    }
}