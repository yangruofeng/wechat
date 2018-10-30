<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 6/3/2016
 * Time: 11:37 PM
 */
class imageHandler{
    /**
     * 等比例压缩图片,支持图片格式jpg,jpeg,png
     * @param string $srcImgPath	需要压缩的图片地址
     * @param string $dst_dir	上传的文件夹
     * @param string $dst_name	上传后的名称，不包括扩展名
     * @param int $maxWidth	如果需要等比例压缩图片，指定压缩后的最大宽度，默认为200
     * @param int $maxHeight	如果需要等比例压缩图片，指定压缩后的最大高度，默认为200
     * @return boolean	成功返回true，否则返回false
     */
    static function resizeImg($srcImgPath, $dst_dir, $dst_name, $maxWidth=200, $maxHeight=200) {
        if (!file_exists($srcImgPath)) {
            return false;
        }
        $file = pathinfo($srcImgPath);
        $ext = $file['extension'];
        if (empty($ext)){
            return false;
        }
        $ext = strtolower($ext);
        if (empty($ext) || !in_array($ext, array('jpg', 'jpeg', 'png'))){
            return false;
        }
        list($srcWidth, $srcHeight) = getimagesize($srcImgPath);
        if($maxWidth==0){ //说明是按固定高压缩
            $maxWidth=$srcWidth*$maxHeight/$srcHeight;
        }
        if($maxHeight==0){
            $maxHeight=$srcHeight*$maxWidth/$srcWidth;
        }
        if($maxWidth>$srcWidth && $maxHeight>$srcHeight){
           return false;
        }
        //设置描绘的x、y坐标，高度、宽度
        $dst_x = $dst_y = $src_x = $src_y = 0;
        $ratio = min ( $maxHeight / $srcHeight, $maxWidth / $srcWidth );
        $dst_h = ceil ( $srcHeight * $ratio );
        $dst_w = ceil ( $srcWidth * $ratio );
        $dst_x = ($maxWidth - $dst_w)/2;
        $dst_y = ($maxHeight - $dst_h)/2;
        $dst_im = imagecreatetruecolor($maxWidth, $maxHeight);
        // 载入原图
        $createFun = 'ImageCreateFrom' . ($ext == 'jpg' ? 'jpeg' : $ext);
        $srcImg = $createFun($srcImgPath);

        //使用红色作为背景
        //$red = imagecolorallocate($im, 255, 0, 0);
        //imagefill($im, 0, 0, $red);
        // 复制图片
        imagecopyresampled ( $dst_im, $srcImg, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $srcWidth, $srcHeight );
        // 生成图片
        $imageFun = 'image' . ($ext == 'jpg' ? 'jpeg' : $ext);
        $file_name = $dst_dir.$dst_name.".".$ext;
        //压缩比例为70%
        if($imageFun == 'imagejpeg'){
            $result = imagejpeg($dst_im, $file_name, 70);
        }else{
            $result = imagepng($dst_im, $file_name, 7);
        }
        imagedestroy($dst_im);
        if (!$result){
            if (file_exists($file_name)) {
                unlink($file_name);
            }
        }
        return $result;
    }
    /*
     * 剪切图片，参数
     * src: 原图路径
     * dst: 剪切图保存路径
     * x1: 原图起点位置x
     * y1:原图起点位置y
     * w: 原图剪切宽度
     * h:原图剪切高度
     * dst_w: 新图宽度
     * dst_h:新图高度
     * src_max_w:原图展示的最大宽度
     * src_max_h:原图展示的最大高度
     * */
   static function cutImage($p){
       try{
           $ifn = $p['src'];
           $ofn = $p['dst'];
           $dst_w=$p['dst_w']?:120;
           $dst_h=$p['dst_h']?:120;
           $src_max_w=$p['src_max_w'];
           $src_max_h=$p['src_max_h'];
           $ext = strtoupper(end(explode('.',$ifn)));
           if(is_file($ifn) && ($ext == "JPG" || $ext == "JPEG")){
               $source = imagecreatefromjpeg($ifn);
           }elseif(is_file($ifn) && $ext == "PNG"){
               $source = imagecreatefromPNG($ifn);
           }elseif(is_file($ifn) && $ext == "GIF"){
               $source = imagecreatefromGIF($ifn);
           }
           if(!$source) return new result(false,"No Found Source");
           $sourceWidth = imagesx($source);
           $sourceHeight = imagesy($source);
           if($src_max_w){
               if($sourceWidth>$src_max_w){
                   //先把source缩小
                   $newSRC_H=floor($src_max_w/$sourceWidth*$sourceHeight);
                   $newSrc=imagecreatetruecolor($src_max_w,$newSRC_H);
                   $is_resize=imagecopyresampled($newSrc,$source,0,0,0,0,$src_max_w,$newSRC_H,$sourceWidth,$sourceHeight);
                   if(!$is_resize){
                       return new result(false,"Resize Image Failed");
                   }
               }
           }elseif($src_max_h){
               if($sourceHeight>$src_max_h){
                   //先把source缩小
                   $newSRC_W=floor($src_max_h/$sourceWidth*$sourceHeight);
                   $newSrc=imagecreatetruecolor($newSRC_W,$src_max_h);
                   $is_resize=imagecopyresampled($newSrc,$source,0,0,0,0,$newSRC_W,$src_max_h,$sourceWidth,$sourceHeight);
                   if(!$is_resize){
                       return new result(false,"Resize Image Failed");
                   }
               }
           }
           if(!$newSrc){
               $newSrc=$source;
           }
           $thumbWidth = $p['w'];
           $thumbHeight = $p['h'];
           $thumb = imagecreatetruecolor($dst_w,$dst_h);
           $x1 = $p['x1'];
           $y1 = $p['y1'];
           $is_create=imagecopyresampled($thumb,$newSrc,0,0,$x1,$y1,$dst_w,$dst_h,$thumbWidth,$thumbHeight);
           if($is_create){
               imagejpeg($thumb, $ofn);
               imagedestroy($thumb);
               return new result(true);
           }else{
               return new result(false,"Cut Picture Failed");
           }

       }catch (Exception $ex){
           return new result(false,$ex->getMessage());
       }
    }
}