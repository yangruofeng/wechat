<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title><?php echo $output['html_title']; ?></title>

    <link rel="stylesheet" href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/css/normalize.css">
    <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery214.js"></script>

    <!--[if IE]>
    <script src="http://libs.useso.com/js/html5shiv/3.7/html5shiv.min.js"></script>
    <![endif]-->

</head>
<body>

<?php
require_once($tpl_file);
?>
</body>
</html>