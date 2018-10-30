<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=1">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/inc_header.css?v=3">
<style>
  html,body {
    background-color: #fff;
  }
</style>
<header class="top-header" id="header">
  <h2 class="title"><?php echo $output['header_title'];?></h2>
  <span class="right-btn" id="complete"><?php echo $lang['label_complete'];?></span>
</header>
<div class="wrap withdraw-success-wrap">
  <div class="tip-wrap">
    <img src="<?php echo WAP_SITE_URL;?>/resource/image/gou.png">
    <p><?php echo $lang['tip_withdrawal_successed'];?></p>
    <p><?php echo $lang['tip_Lengding'];?><?php echo $lang['tip_please_wait_patiently'];?></p>
  </div>
</div>
<script type="text/javascript">
  if(window.operator){
    window.operator.showTitle('<?php echo $output['header_title'];?>');
  }
  var type = '<?php echo $_GET['source']?>', l = '<?php echo $_GET['lang']?>';
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
  $('#complete').on('click', function(){
    window.location.href = '<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=index';
  });
</script>
