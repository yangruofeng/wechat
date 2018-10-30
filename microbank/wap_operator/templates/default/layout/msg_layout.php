<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php  echo 'MESSAGE';?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/weui/page.css?v=<?php echo time()?>">
    <link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/weui/weui.css?v=<?php echo time()?>">
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/zepto.min.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/zepto.cookie.min.js"></script>
    <script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/aui/aui-toast.js"></script>
    <script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/fastclick.js"></script>
    <script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/weui/jweixin.js"></script>
    <script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/weui/weui.min.js"></script>

</head>
<body>
<div class="container">
    <div class="page">
        <?php
        require_once($tpl_file);
        ?>
    </div>
</div>
</body>
</html>
