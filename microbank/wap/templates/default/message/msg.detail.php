<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/message.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap message-detail-wrap">
  <?php $detail = $output['detail'];?>
  <div class="send-info">
    <div class="sender">
      <?php echo $detail['sender_name'];?>
      <span class="time"><?php echo $detail['message_time'];?></span>
    </div>
  </div>
  <div class="msg-body">
    <div class="msg-content">
      <div class="title">
        <?php echo $detail['message_title'];?>
      </div>
      <div class="body">
        <?php echo $detail['message_body'];?>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">

</script>
