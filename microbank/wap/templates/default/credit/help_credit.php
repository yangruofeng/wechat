<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=3">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap credit-help-wrap">
  <div class="credit-help-item">
    • <?php echo $lang['label_what_credit_tip_1'];?>
  </div>
  <div class="credit-help-item">
    • <?php echo $lang['label_what_credit_tip_2'];?>
  </div>
  <div class="credit-help-item">
    • <?php echo $lang['label_what_credit_tip_3'].$lang['label_colon'];?>
  </div>
  <div class="credit-help-item item">
    <span><?php echo $lang['label_what_credit_tip_4_span'];?>---</span>
    <?php echo $lang['label_what_credit_tip_4'];?>
  </div>
  <div class="credit-help-item item">
    <span><?php echo $lang['label_what_credit_tip_5_span'];?>---</span>
    <?php echo $lang['label_what_credit_tip_5'];?>
  </div>
  <div class="credit-help-item item">
    <span><?php echo $lang['label_what_credit_tip_6_span'];?>---</span>
    <?php echo $lang['label_what_credit_tip_6'];?>
  </div>
  <div class="credit-help-item item">
    <span><?php echo $lang['label_what_credit_tip_7_span'];?>---</span>
    <?php echo $lang['label_what_credit_tip_7'];?>
  </div>
</div>
<script type="text/javascript">
  var type = '<?php echo $_GET['source'];?>';
  if (type == 'app') {
    app_show(type);
  }
  function app_show(type) {
    if (type == 'app') {
        $('#header').hide();
    } else {
        $('#header').show();
    }
  }
</script>
