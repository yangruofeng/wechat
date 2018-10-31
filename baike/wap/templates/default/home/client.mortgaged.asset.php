<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=3">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap credit-officer-wrap">
  <?php $list = $output['data'];$verify_field = enum_langClass::getCertificationTypeEnumLang();?>
  <?php if(!$list){?>
    <div class="no-record">No record</div>
  <?php }else{ ?>
    <ul class="aui-list operator-list aui-margin-b-10">
      <?php if(count($list) > 0){?>
        <?php foreach ($list as $v) { ?>
          <li class="aui-list-item operator-item">
            <div class="aui-list-item-inner item">
              <div class="aui-list-item-text">
                  <div class="aui-list-item-title type">
                    <?php echo $v['asset_name'];?>
                      <span class="code">
                    (<?php echo $verify_field[$v['asset_type']];?>)</span>
                  </div>
                  <div class="aui-list-item-right type"></div>
              </div>
              <div class="aui-list-item-text">
                  <div class="aui-list-item-title title"><?php echo $v['contract_no']?></div>
                  <div class="aui-list-item-right text"><?php echo timeFormat($v['operator_time']);?></div>
              </div>
            </div>
          </li>
        <?php }?>
      <?php }else{ ?>
        <div class="no-record"><?php echo $lang['label_no_data'];?></div>
      <?php } ?>
      
    </ul>
  <?php } ?>
</div>
<script>
  
</script>