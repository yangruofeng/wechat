<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL; ?>/resource/css/member.css?v=2">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL; ?>/resource/css/help.css?v=2">
<?php include_once(template('widget/inc_header')); ?>
<div class="wrap aboutus-wrap">
    <?php $detail = $output['detail']; ?>
    <div class="about-profile aui-margin-b-10">
        <ul class="profile-model-list">
            <li class="profile-model-item profile-model-title clearfix">
                <div class="title">
                    <?php echo $detail['help_title']; ?>
                </div>
                <div class="handler" title="">
                    <img src="<?php echo ENTRY_DESKTOP_SITE_URL . DS . 'resource/img/default_avatar.jpg'?>">
                    <?php echo $detail['handler_name']; ?>
                </div>
                <div class="handle_time">
                    <?php echo dateFormat($detail['handle_time']); ?>
                </div>
            </li>
            <li class="profile-model-item profile-model-content">
                <?php echo $detail['help_content']; ?>
            </li>
        </ul>
    </div>
</div>
