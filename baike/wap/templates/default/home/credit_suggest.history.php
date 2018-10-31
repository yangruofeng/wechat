<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap suggest-history-wrap">
  <?php $list = $output['list'];?>
  <ul class="aui-list suggest-history-ul">
    <?php if(count($list) > 0){?>
      <?php foreach ($list as $v) { ?>
        <li class="aui-margin-b-10">
          <div class="item">
            <label for="">Monthly Repayment Ability</label><span><?php echo ncPriceFormat($v['monthly_repayment_ability']);?></span>
          </div>
          <div class="item">
            <label for="">Invalid Terms</label><span><?php echo $v['credit_terms'];?><em>(Months)</em></span>
          </div>
          <div class="item">
            <label for="">Default Credit</label><span><?php echo ncPriceFormat($v['default_credit']);?><em></em></span>
          </div>
          <div class="item">
            <label for="">Max Credit</label><span><?php echo ncPriceFormat($v['max_credit']);?><em></em></span>
          </div>
          <div class="item">
            <label for="">Interest rate without mortgage</label><span><?php echo ncPriceFormat($v['interest_without_mortgage']);?><em></em></span>
          </div>
          <div class="item">
            <label for="">Interest rate with mortgage</label><span><?php echo ncPriceFormat($v['interest_with_mortgage']);?><em></em></span>
          </div>
          <div class="item">
            <label for="">Request Time</label><span><?php echo timeFormat($v['request_time']);?><em></em></span>
          </div>
          <div class="item">
            <label for="">Remark</label><span><?php echo $v['remark'];?><em></em></span>
          </div>
        </li>
      <?php }?>
    <?php }else{ ?>
      <div class="no-record"><?php echo $lang['label_no_data'];?></div>
    <?php } ?>
    
  </ul>
</div>
<script type="text/javascript">

</script>
