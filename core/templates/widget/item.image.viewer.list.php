
<?php
    $image_list=$image_list?:$output['image_list'];
    $image_version=$image_version?:imageThumbVersion::SMALL_IMG;
    $viewer_width=$viewer_width?:360;
    $viewer_index=$viewer_index?:uniqid('img_index_');

?>
<style>
    .image-icon-album {
        position: absolute;
        top: 0;
        right: 0;
        width: 35px;
        height: 35px;
        background-image: url('<?php echo GLOBAL_RESOURCE_SITE_URL . '/images/icon/album.png' ?>');
        background-size: 35px;
    }
    .image-icon-camera {
        position: absolute;
        top: 0;
        right: 0;
        width: 35px;
        height: 35px;
        background-image: url('<?php echo GLOBAL_RESOURCE_SITE_URL . '/images/icon/camera.png' ?>');
        background-size: 35px;
    }
</style>
<?php if($image_version==imageThumbVersion::MAX_240){?>
    <style>
        .magnifier-assembly{
            height: 260px;
        }
        .magnifier-line{
            height: 260px;
        }
        .small-img{
            height: 260px;
        }
        .magnifier-line ul.docs-pictures li{
            width: 260px;
        }

    </style>
<?php }else{?>
    <style>
        .magnifier-assembly{
            height: 92px;
        }
        .magnifier-line{
            height: 92px;
        }
        .small-img{
            height: 78px;
        }
        .magnifier-line ul.docs-pictures li{
            width: 100px;
        }
    </style>
<?php }?>
<div class="magnifier<?php echo $viewer_index; ?>" style="width: <?php echo $viewer_width;?>px;">
    <div class="magnifier" style="width:<?php echo $viewer_width;?>px; " index="<?php echo $viewer_index; ?>" data-inx="1">
        <div class="magnifier-container" style="display:none;">
            <div class="images-cover"></div>
            <div class="move-view"></div>
        </div>
        <div class="magnifier-assembly">
            <div class="magnifier-btn">
                <span class="magnifier-btn-left">&lt;</span>
                <span class="magnifier-btn-right">&gt;</span>
            </div>
            <!--按钮组-->
            <div class="magnifier-line">
                <ul class="clearfix animation03 docs-pictures">
                    <?php foreach ($image_list as $img_url) {?>
                        <?php if (is_array($img_url)) { ?>
                            <li>
                                <div class="small-img">
                                    <img class="img-asset-item" data-original="<?php echo getImageUrl($img_url['url']); ?>"
                                         src="<?php echo getImageUrl($img_url['url'], $image_version); ?>"/>
                                    <?php if ($img_url['image_source'] == 1) { ?>
                                        <icon class="image-icon-album"></icon>
                                    <?php } else { ?>
                                        <icon class="image-icon-camera"></icon>
                                    <?php } ?>
                                </div>
                            </li>
                        <?php } elseif($img_url) { ?>
                            <li>
                                <div class="small-img">
                                    <img class="img-asset-item" data-original="<?php echo getImageUrl($img_url); ?>"
                                         src="<?php echo getImageUrl($img_url, $image_version); ?>"/>
                                </div>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>
