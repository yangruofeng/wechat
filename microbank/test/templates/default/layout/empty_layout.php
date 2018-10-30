<html>
<head>
    <title><?php echo $output['html_title'] ?: "Test Page"; ?></title>

    <link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-3.3.4/css/bootstrap.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/font/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/jqeasyui1.4/themes/metro/easyui.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/viewer/viewer.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/main-style.css?v=24" rel="stylesheet" type="text/css"/>

    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery214.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jq.extend.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-3.3.4/js/bootstrap.min.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jq.json.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/jqeasyui1.4/jquery.easyui.min.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/yo.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/yo.extend.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/easyui.extend.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/common.js?v=1"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/viewer/viewer.js"></script>
</head>
<body>
<?php require_once($tpl_file); ?>
<script src="<?php echo BACK_OFFICE_SITE_URL; ?>/resource/script/common.js?v=2"></script>
</body>
</html>
