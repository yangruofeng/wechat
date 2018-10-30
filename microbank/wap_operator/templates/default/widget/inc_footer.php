<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/inc_footer.css?v=2">
<footer class="bottom-footer" id="footer">
  <ul class="footer-nav clearfix">
    <li class="client <?php if($output['nav_footer'] == 'client'){echo 'active';}?>" onclick="<?php if($output['nav_footer'] == 'client'){echo 'javascript:;';}else{?>javascript:location.href='<?php echo getUrl('client', 'index', array(), false, WAP_OPERATOR_SITE_URL)?>'<?php }?>">
        <p></p>
        <span><?php echo 'Client';?></span>
      </li>
    <li class="request <?php if($output['nav_footer'] == 'request'){echo 'active';}?>" onclick="<?php if($output['nav_footer'] == 'request'){echo 'javascript:;';}else{?>javascript:location.href='<?php echo getUrl('request', 'index', array(), false, WAP_OPERATOR_SITE_URL)?>'<?php }?>">
      <p></p>
      <span><?php echo 'Task';?></span>
    </li>
    <li class="account <?php if($output['nav_footer'] == 'account'){echo 'active';}?>" onclick="<?php if($output['nav_footer'] == 'account'){echo 'javascript:;';}else{?>javascript:location.href='<?php echo getUrl('member', 'index', array(), false, WAP_OPERATOR_SITE_URL)?>'<?php }?>">
      <p></p>
      <span><?php echo 'Account';?></span>
    </li>
  </ul>
</footer>
<script type="text/javascript">
  var type = '<?php echo $_GET['source']?>';
  if (type == 'app') {
    app_show(type);
  }
  function app_show(type) {
    if (type == 'app') {
      $('#footer').hide();
    } else {
      $('#footer').show();
    }
  }
</script>
