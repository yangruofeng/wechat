<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/request.css?v=4">
<?php include_once(template('widget/inc_simple_header'));?>
<div class="wrap request-wrap request-index-wrap">
  <ul class="aui-list info-list aui-margin-b-10">
    <li class="aui-list-item operator-item" onclick="javascript:location.href='<?php echo getUrl('request', 'checkApplication', array(), false, WAP_OPERATOR_SITE_URL)?>'">
      <div class="aui-list-item-inner content aui-list-item-arrow">
        <?php echo 'Check Consult';?>
      </div>
    </li>
    <li class="aui-list-item operator-item" onclick="javascript:location.href='<?php echo getUrl('request', 'checkOverdue', array(), false, WAP_OPERATOR_SITE_URL)?>'">
      <div class="aui-list-item-inner content aui-list-item-arrow">
        <?php echo 'Check Overdue';?>
      </div>
    </li>
  </ul>
</div>
<?php include_once(template('widget/inc_footer'));?>
<script type="text/javascript">
  
</script>
