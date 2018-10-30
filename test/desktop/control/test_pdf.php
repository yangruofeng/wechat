<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/8/10
 * Time: 10:35
 */
class test_pdfControl
{

    public function previewOp()
    {
        $pdf = 'example_008.pdf';

        $file = GLOBAL_ROOT.'/test/example_008.pdf';
        $file = GLOBAL_ROOT.'/test/task20180707.xlsx';
        //echo $file;die;
        Header("Content-type: application/xlsx");// 文件将被称为 downloaded.pdf
        //header("Content-Disposition:inline;filename='downloaded.pdf'");
        readfile($file);
    }
}