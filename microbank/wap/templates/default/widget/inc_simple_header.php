<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/inc_header.css?v=1">
<header class="top-header" id="header">
  <h2 class="title"><?php echo $output['header_title'];?></h2>
</header>
<script>
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
</script>
