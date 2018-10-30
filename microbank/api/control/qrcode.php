<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/9
 * Time: 16:50
 */
class qrcodeControl extends bank_apiControl
{


    public function generateCommonImageOp()
    {
        ob_clean();
        @header("Content-Type: image/png;");
        include BASE_CORE_PATH.'/phpqrcode.php';
        $params = array_merge(array(),$_GET,$_POST);
        $value = $params['content']?urldecode($params['content']):'0';
        $errorCorrectionLevel = "H";  //L、M、Q、H  容错率
        $matrixPointSize = "8";  //1-10  图片大小
        QRcode::png($value, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }

    public function getMemberQrcodeImageOp()
    {
        ob_clean();
        @header("Content-Type: image/png;");
        include BASE_CORE_PATH.'/phpqrcode.php';
        $params = array_merge(array(),$_GET,$_POST);
        $value = $params['member_id']?:'0';
        // todo 详细信息
        $errorCorrectionLevel = "H";  //L、M、Q、H  容错率
        $matrixPointSize = "8";  //1-10  图片大小
        QRcode::png($value, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }


    public function logo()
    {
        // 带logo
        include BASE_CORE_PATH.'/phpqrcode.php';
        $value = $_GET['url'];//二维码内容
        $errorCorrectionLevel = 'H';//容错级别
        $matrixPointSize = 8;//生成图片大小
        //生成二维码图片
        QRcode::png($value, 'qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2);
        $logo = 'test.png';//准备好的logo图片
        $QR = 'qrcode.png';//已经生成的原始二维码图

        if ( $logo ) {
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $QR_height = imagesy($QR);//二维码图片高度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                $logo_qr_height, $logo_width, $logo_height);
        }

        ob_clean();
        @header("Content-Type: image/png;");
        //输出图片
        imagepng($QR);
    }


}