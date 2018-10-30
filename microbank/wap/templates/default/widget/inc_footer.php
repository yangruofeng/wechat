<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/inc_footer.css?v=6">
<footer class="bottom-footer" id="footer">
  <ul class="footer-nav clearfix">
    <li class="loan <?php if($output['nav_footer'] == 'loan'){echo 'active';}?>"
      onclick="<?php if($output['nav_footer'] == 'loan'){echo 'javascript:;';}else{?>javascript:location.href='<?php echo getUrl('loan', 'index', array(), false, WAP_SITE_URL)?>'<?php }?>">
      <p></p>
      <span><?php echo $lang['label_home'];?></span>
    </li>
    <li class="credit <?php if($output['nav_footer'] == 'credit'){echo 'active';}?>" onclick="<?php if($output['nav_footer'] == 'credit'){echo 'javascript:;';}else{?>javascript:location.href='<?php echo getUrl('credit', 'index', array(), false, WAP_SITE_URL)?>'<?php }?>">
      <p></p>
      <span><?php echo $lang['label_loan'];?></span>
    </li>
    <li class="service <?php if($output['nav_footer'] == 'saving'){echo 'active';}?>" onclick="<?php if($output['nav_footer'] == 'saving'){echo 'javascript:;';}else{?>javascript:location.href='<?php echo getUrl('saving', 'index', array(), false, WAP_SITE_URL)?>'<?php }?>">
      <p></p>
      <span><?php echo 'Service';?></span>
    </li>
    <li class="account <?php if($output['nav_footer'] == 'account'){echo 'active';}?>" onclick="<?php if($output['nav_footer'] == 'account'){echo 'javascript:;';}else{?>javascript:location.href='<?php echo getUrl('member', 'index', array(), false, WAP_SITE_URL)?>'<?php }?>">
      <p></p>
      <span><?php echo $lang['label_account'];?></span>
    </li>
  </ul>
</footer>
