<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=5">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap credit-officer-wrap">
  <?php $list = $output['list'];?>
  <ul class="aui-list operator-list aui-margin-b-10">
    <?php if(count($list) > 0){?>
      <?php foreach ($list as $v) { ?>
        <li class="aui-list-item operator-item">
          <div class="aui-list-item-inner item">
            <div class="aui-list-item-text">
                <div class="aui-list-item-title type"><?php echo $v['officer_name'];?><span class="code">(<?php echo $v['user_code'];?>)</span></div>
                <div class="aui-list-item-right type"></div>
            </div>
            <div class="aui-list-item-text">
                <div class="aui-list-item-title title"><?php echo $v['user_position']?></div>
                <div class="aui-list-item-right text"><?php echo $v['mobile_phone']?></div>
            </div>
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
