<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/inc_header.css?v=6">
<header class="top-header" id="header" style="display: <?php echo $_GET['source'] == 'app' ? 'none' : 'block';?>">
  <span class="back" onclick="javascript:history.back(-1);"><i class="aui-iconfont aui-icon-left"></i></span>
  <h2 class="title"><?php echo $output['header_title'];?></h2>
</header>
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
  if(l && CURRENT_LANGUAGE_CODE != l){
    changeLang(l, 3);
  }
</script>