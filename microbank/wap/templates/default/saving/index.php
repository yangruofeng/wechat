<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/saving.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap saving-wrap">
  <div class="aui-content aui-margin-b-10">
    <ul class="aui-list aui-media-list message-list" id="messageList"></ul>
    <div class="data-loading" style="display: none;"><img src="<?php echo WAP_SITE_URL;?>/resource/image/loading.gif" alt=""></div>
    <div class="no-record" style="display: none;"><?php echo $lang['label_no_data'];?></div>
  </div>
</div>
<?php include_once(template('widget/inc_footer'));?>
<script type="text/javascript">

</script>
