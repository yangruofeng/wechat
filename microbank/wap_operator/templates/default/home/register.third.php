<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=1">
<?php include_once(template('widget/inc_simple_header'));?>
<div class="reg-nav">
  <ul class="nav-ul clearfix">
    <li class="nav-item">
      <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up1.png"></p>
      <p class="text">Register</p>
    </li>
    <li class="nav-item">
      <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up2_press.png"></p>
      <p class="text">Information</p>
    </li>
    <li class="nav-item">
      <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up3_press.png"></p>
      <p class="text">Upload Avatar</p>
    </li>
    <li class="nav-item">
      <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up4_press.png"></p>
      <p class="text">Finished</p>
    </li>
  </ul>
</div>
<div class="wrap reg-succ-wrap">
  <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/gou.png" alt="">
  <p class="title"><?php echo 'Register Success';?></p>
  <p class="tip"><?php echo str_replace('xxx','<em id="count">3</em>',$lang['tip_jump_to_login_cutdown']);?></p>
</div>
<script>
  var count = $('#count').text();
  var times = setInterval(function(){
    count--;
    $('#count').text(count);
    if(count <= 1){
      clearInterval(times);
      window.location.href = "<?php echo getUrl('home', 'index', array(), false, WAP_OPERATOR_SITE_URL)?>";
    }
  },1000);
</script>