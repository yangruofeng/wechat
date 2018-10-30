<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=4">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap credit-help-wrap">
  <div class="credit-help-item">
    • <?php echo $lang['label_how_to_get_credit_tip_1'];?>
  </div>
  <div class="credit-help-item label">
    • <?php echo $lang['label_how_to_get_credit_tip_2'].$lang['label_colon'];?>
  </div>
  <div class="credit-help-item item">
    <?php echo $lang['label_how_to_get_credit_tip_3'];?>
  </div>
  <div class="credit-help-item item">
    <?php echo $lang['label_how_to_get_credit_tip_4'];?>
  </div>
  <div class="credit-help-item item">
    <?php echo $lang['label_how_to_get_credit_tip_5'];?>
  </div>
  <div class="credit-help-item label">
    • <?php echo $lang['label_how_to_get_credit_tip_6'].$lang['label_colon'];?>
  </div>
  <div class="credit-help-item item">
    <?php echo $lang['label_how_to_get_credit_tip_7'];?>
  </div>
  <div class="credit-help-item item">
    <?php echo $lang['label_how_to_get_credit_tip_8'];?>
  </div>
  <div class="credit-help-item label">
    • <?php echo $lang['label_how_to_get_credit_tip_9'].$lang['label_colon'];?>
  </div>
  <div class="credit-help-item item">
    <?php echo $lang['label_how_to_get_credit_tip_10'];?>
  </div>
  <div class="credit-help-item item">
    <?php echo $lang['label_how_to_get_credit_tip_11'];?>
  </div>
  <div class="credit-help-item item">
    <?php echo $lang['label_how_to_get_credit_tip_12'];?>
  </div>
  <div class="credit-help-item">
    • <?php echo $lang['label_how_to_get_credit_tip_13'];?>
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
