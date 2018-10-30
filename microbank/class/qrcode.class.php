<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/4/9
 * Time: 10:54
 */
class qrcodeClass
{
    /** 生成二维码图片
     * @param $value -》二维码的值
     * @param bool $file_path -》生成的文件地址，false不生成文件
     * @param string $error_level  容错率 L、M、Q、H
     * @param string $size   图片大小 1-10
     */
    public static function generateQrCodeImage($value,$file_path=false,$error_level='H',$size='8')
    {
        ob_clean();
        @header("Content-Type: image/png;");
        include BASE_CORE_PATH.'/phpqrcode.php';
        $errorCorrectionLevel = $error_level;  //L、M、Q、H  容错率
        $matrixPointSize = $size;  //1-10  图片大小
        QRcode::png($value, $file_path, $errorCorrectionLevel, $matrixPointSize, 2);
    }
}