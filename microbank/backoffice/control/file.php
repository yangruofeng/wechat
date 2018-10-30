<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/8/10
 * Time: 17:46
 */
class fileControl extends control
{
    public function pdfViewOp()
    {
        $params = array_merge($_GET,$_POST);

        $file = urldecode($params['file_path']);
        Header("Content-type: application/pdf");
        readfile($file);

    }
}