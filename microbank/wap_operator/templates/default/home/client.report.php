<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=3">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap report-wrap">
  <ul class="aui-list info-list aui-margin-b-10">
    <li class="aui-list-item operator-item" onclick="javascript:location.href='<?php echo getUrl('home', 'clientCbc', array(), false, WAP_OPERATOR_SITE_URL)?>'">
      <div class="aui-list-item-inner content aui-list-item-arrow">
        <?php echo 'CBC';?>
      </div>
    </li>
    <li class="aui-list-item operator-item" onclick="javascript:location.href='<?php echo getUrl('home', 'creditOfficer', array('id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>'">
      <div class="aui-list-item-inner content aui-list-item-arrow">
        <?php echo 'Officer';?>
      </div>
    </li>
    <li class="aui-list-item operator-item" onclick="javascript:location.href='<?php echo getUrl('home', 'mortgagedAsset', array('member_id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>'">
      <div class="aui-list-item-inner content aui-list-item-arrow">
        <?php echo 'Mortgaged Asset';?>
      </div>
    </li>
    <li class="aui-list-item operator-item" onclick="javascript:location.href='<?php echo getUrl('home', 'interestList', array('member_id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>'">
      <div class="aui-list-item-inner content aui-list-item-arrow">
        <?php echo 'Interest List';?>
      </div>
    </li>
  </ul>
</div>
<script>
  
</script>