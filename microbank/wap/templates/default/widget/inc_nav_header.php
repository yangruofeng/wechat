<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/inc_header.css?v=6">
<?php $msgcount = $output['msgcount'];?>
<div class="home-header">
  <h2 class="title"><?php echo $output['header_title'];?></h2>
  <div class="header-btn clearfix">
    <span class="s-msg" onclick="javascript:location.href='<?php echo getUrl('message', 'index', array(), false, WAP_SITE_URL)?>'">
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/icon-msg.png" alt="" class="icon-msg">
      <?php if($msgcount>0){ ?><em><?php echo $msgcount;?></em><?php } ?>
    </span>
    <span id="plus"><i class="aui-iconfont aui-icon-plus"></i></span>
  </div>
</div>
<ul class="plus-list" style="display: none;">
  <li><a href="<?php echo getUrl('loan', 'hotLine', array(), false, WAP_SITE_URL)?>"><?php echo 'Hotline';?></a></li>
</ul>
<script type="text/javascript">
$('#plus').on('click',function(e){
  $('.plus-list').toggle();
});
$('body').on('click', function(e){
  var target = $(e.target);
  if(!target.is('#plus') && !target.is('#plus *') && !target.is('.plus-list') && !target.is('.plus-list *')){
    var f = $('.plus-list').css('display');
    if(f == 'block'){
      $('.plus-list').toggle();
    }
  }
});
</script>
