<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/inc_header.css?v=6">
<header class="top-header" id="header" style="display: <?php echo $_GET['source'] == 'app' ? 'none' : 'block';?>">
    <div class="page__hd">
        <div class="weui-cell" style="padding: 0">
            <div class="weui-cell__hd">
                <a class="back" href="<?php if($output['back_url']){
                    echo $output['back_url'];
                }else{
                   echo 'javascript:history.back(-1);';
                }?>
                ">
                    <i class="weui-icon-back"></i>
                </a>
            </div>
            <div class="weui-cell__bd">
                <h2 class="title"><?php echo $output['header_title'];?></h2>
            </div>
        </div>
    </div>
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