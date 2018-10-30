<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $output['html_title'];?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
  <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="format-detection" content="telephone=no">
  <link rel="stylesheet" type="text/css" href="<?php echo DOWNLOAD_SITE_URL;?>/resource/script/aui/aui.2.0.css?v=2">
  <link rel="stylesheet" type="text/css" href="<?php echo DOWNLOAD_SITE_URL;?>/resource/css/init.css?v=23">
  <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/zepto.min.js"></script>
  <script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/zepto.cookie.min.js"></script>
  <script src="<?php echo DOWNLOAD_SITE_URL;?>/resource/script/aui/aui-toast.js"></script>
  <script src="<?php echo DOWNLOAD_SITE_URL;?>/resource/script/fastclick.js"></script>
  <script type="text/javascript">
  var toast = new auiToast();
  function verifyFail(msg){
    toast.fail({
      title: msg,
      duration: 2000
    });
  }
  var COOKIE_PRE = '<?php echo COOKIE_PRE;?>';var COOKIE_DOMAIN="<?php echo SUBDOMAIN_SUFFIX;?>";
  var CURRENT_LANGUAGE_CODE = "<?php echo Language::currentCode(); ?>";
  </script>
</head>
<body>
  <?php
  require_once($tpl_file);
  ?>

</body>

</html>
