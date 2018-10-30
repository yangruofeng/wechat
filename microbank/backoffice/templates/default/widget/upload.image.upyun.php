
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/uploadify/jquery.uploadify.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/uploadify/uploadify.css" />
<script type="text/javascript">
    var site_url="<?php echo getUrl('upload2upyun','upload2upyun')?>";
    var upload_url="<?php echo C('target_url')?>";
    var upyun_url="<?php echo UPYUN_URL.DS?>";
    var swf_url = "<?php echo _CORE_WEB_.DS?>uploadify/uploadify.swf";
    var button_text = "<?php echo $lang['point_upload']; ?>";
</script>
