<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/inc_header.css?v=6">
<header class="top-header" id="header">
  <span class="back" onclick="javascript:history.back(-1);"><i class="aui-iconfont aui-icon-left"></i></span>
  <h2 class="title"><?php echo $output['header_title'];?></h2>
  <span class="icon-location" id="location"><img src="<?php echo WAP_SITE_URL;?>/resource/image/icon-6.png"></span>
</header>
<ul class="location-list" style="display: none;">
  <li><a href="<?php echo getUrl('loan', 'index', array(), false, WAP_SITE_URL)?>"><?php echo $lang['label_home'];?></a></li>
  <li><a href="<?php echo getUrl('credit', 'index', array(), false, WAP_SITE_URL)?>"><?php echo $lang['label_credit'];?></a></li>
  <li><a href="<?php echo getUrl('member', 'index', array(), false, WAP_SITE_URL)?>"><?php echo $lang['label_account'];?></a></li>
</ul>
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
  $('#location').on('click',function(){
    $('.location-list').toggle();
  });
  $('body').on('click', function(e){
    var target = $(e.target);
    if(!target.is('#location') && !target.is('#location *') && !target.is('.location-list') && !target.is('.location-list *')){
      var f = $('.location-list').css('display');
      if(f == 'block'){
        $('.location-list').toggle();
      }
    }
  });
</script>
