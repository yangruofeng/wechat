<?php
    $image_item=$image_item?:$output['image_item'];
    $image_version=$image_version?:imageThumbVersion::SMALL_IMG;
    $viewer_width=$viewer_width?:150;
    $viewer_index=$viewer_index?:uniqid('img_index_');
?>
<div class="magnifier<?php echo $viewer_index; ?>" style="width: <?php echo $viewer_width;?>px;">
    <div class="magnifier" style="width:<?php echo $viewer_width;?>px; " index="<?php echo $viewer_index; ?>">
        <div class="magnifier-container" style="display:none;">
            <div class="images-cover"></div>
            <div class="move-view"></div>
        </div>
        <div class="magnifier-assembly">
            <!--按钮组-->
            <div class="magnifier-line">
                <ul class="clearfix animation03 docs-pictures">
                    <li>
                        <div class="small-img">
                            <img class="img-asset-item" data-original="<?php echo getImageUrl($image_item); ?>" src="<?php echo getImageUrl($image_item, $image_version); ?>" />
                        </div>
                    </li>
                </ul>
            </div>
            <!--缩略图-->
        </div>
        <div class="magnifier-view"></div>
        <!--经过放大的图片显示容器-->
    </div>
</div>