<html>
<head>
    <title><?php echo $output['html_title'] ?: "Test Page"; ?></title>
    <link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-3.3.4/css/bootstrap.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/font/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/jqeasyui1.4/themes/metro/easyui.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/main-style.css?v=1" rel="stylesheet" type="text/css"/>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery214.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jq.extend.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-3.3.4/js/bootstrap.min.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jq.json.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/jqeasyui1.4/jquery.easyui.min.js"></script>
    <?php require_once template('widget/iframe.common.js'); ?>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/yo.js?v=<?php echo time()?>"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/yo.extend.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/easyui.extend.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/common.js?v=3"></script>
    <!-- 图片放大查看-->
    <link rel="stylesheet" href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/jquery-viewer/viewer.min.css?v=1">
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/jquery-viewer/viewer-jquery.min.js?v=1"></script>
    <!-- 图片左右滑动 -->
    <link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/magnifier/magnifier.css?v=3" rel="stylesheet" type="text/css"/>
    <?php include(template(":widget/item.image.viewer.js"));?>

</head>
<body>
<?php require_once($tpl_file); ?>
<?php if(!$output['no_include_bottom']){?>
    <?php require_once template('widget/app.content.bottom'); ?>
<?php }?>
<?php //require_once template('widget/app.common.js'); ?>
<?php include(template("widget/yo.dialog"));?>
</body>
</html>
