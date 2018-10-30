<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/inc_header.css?v=6">
<header class="top-header" id="header" style="display: <?php echo $_GET['source'] == 'app' ? 'none' : 'block';?>">
  <h2 class="title">Error</h2>
</header>
<style>
.tip {
  font-size: .7rem;
  text-align: center;
  padding: 1rem .4rem;
}
.redirect {
  margin-top: .5rem;
  font-size: .6rem;
}
.redirect span.s {
  color: #f60;
}
.redirect span.l {
  display: inline-block;
  padding: .05rem .5rem;
  color: #fff;
  background: #28c568;
  border-radius: .1rem;
  margin-left: .4rem;
}
</style>
<div class="tip">
  <?php echo $output['msg'];?>
  <p class="redirect">Jump to the login interface after <span class="s" id="count">1</span> seconds <span class="l" id="login">Direct login</span></p>
</div>
<script type="text/javascript">
  var count = parseInt($('#count').text());
  var times = setInterval(function(){
    count--;
    $('#count').text(count);
    if(count <= 1){
      clearInterval(times);
      if(window.operator){
        window.operator.reLogin();
      }else{
        window.location.href = "<?php echo getUrl('login', 'index', array(), false, WAP_OPERATOR_SITE_URL)?>";
      }
    }
  },1000);
  $('#login').click(function(){
    if(window.operator){
        window.operator.reLogin();
      }else{
        window.location.href = "<?php echo getUrl('login', 'index', array(), false, WAP_OPERATOR_SITE_URL)?>";
      }
  });
</script>
