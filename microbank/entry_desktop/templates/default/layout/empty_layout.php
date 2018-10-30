<html>
<head>
    <title><?php echo $output['html_title'] ?: "Test Page"; ?></title>
    <script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery214.js"></script>
</head>
<body>
<?php require_once($tpl_file);?>
</body>
</html>
