<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=1">
<style>
    .cerification-wrap .cerification-picture .icon-upload {
        width: 100%;
        height: 100%;
        margin-top: 0;
    }
</style>
<?php include_once(template('widget/inc_header'));?>
<?php $image_list = $output['image_list'];?>
<div class="wrap cerification-wrap">
    <form id="uploadPicture" enctype="multipart/form-data" method="post">
        <?php foreach($output['image_structure'] as $key => $structure) { ?>
        <div class="cerification-picture aui-margin-b-10">
            <div class="upload-wrap clearfix">
                <div class="up-btn">
                    <div class="upload-input">
                        <div class="uncheck" id="property_card_uncheck">
                            <img src="<?php echo getImageUrl($image_list[$structure['key']]['image_url']);?>" alt="" class="icon-upload">
                        </div>
                    </div>
                    <div class="name"><?php echo $structure['des'];?></div>
                </div>
                <div class="up-example">
                    <div class="up-exam">
                        <img src="<?php echo $structure['image'];?>" alt="" class="example-pic">
                    </div>
                    <div class="name"><?php echo $lang['label_example'];?></div>
                </div>
            </div>
        </div>
        <?php }?>
    </form>
</div>
