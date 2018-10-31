<?php
$new_url = $output['new_url'];
?>
<html>
<head>
    <title><?php echo $output['html_title'] ?: "Test Page"; ?></title>
    <meta http-equiv="refresh" content="0;url=<?php echo $new_url; ?>">
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery214.js"></script>
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jq.extend.js"></script>
    <?php require_once template('widget/app.common.js'); ?>
</head>
<body>
<?php require_once($tpl_file); ?>
</body>
</html>
