<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/member.css?v=8">
<?php include_once(template('widget/inc_nav_header'));?>
<div class="wrap user-wrap">
  <?php $info = $output['member_info'];?>
  <div class="member-top"></div>
  <div class="aui-content aui-margin-b-10">
    <ul class="aui-list aui-media-list member-basic">
      <li class="aui-list-item aui-list-item-middle" onclick="javascript:location.href='<?php echo getUrl('member', 'editProfile', array(), false, WAP_SITE_URL)?>'">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-media loan-period"><img src="<?php echo getImageUrl($info['member_icon'])?:WAP_SITE_URL.'/resource/image/default_avatar.png'; ?>" class="aui-img-round aui-list-img-sm"></div>
          <div class="aui-list-item-inner aui-list-item-arrow">
            <?php if($info){ ?>
              <div class="login">
                <p><?php echo $info['member_name'];?></p>
                <p class="obj">(<?php echo $info['obj_guid'];?>)</p>
              </div>

            <?php }else{ ?>
              <span class="logout"><a href="<?php echo getUrl('login', 'index', array(), false, WAP_SITE_URL)?>"><?php echo $lang['act_login'];?>/<?php echo $lang['act_register'];?></a></span>
            <?php } ?>
            <div class="aui-list-item-text loan-text">

            </div>
          </div>
        </div>
      </li>
    </ul>
  </div>
  <div class="aui-content aui-margin-b-10 user-credit">
    <div class="credit-bar">
      <div class="credit-bar-info">
        <div class="credit-bar-inner">
          <div class="prosess"></div>
          <div class="text">
            <?php echo $lang['label_credit'].$lang['label_colon'];?> <?php if($info['credit']['credit']['balance']){ echo $info['credit']['credit']['balance']; }else{ echo '0'; }?>
          </div>
        </div>
      </div>
      <p class="total"><?php echo $lang['label_total'].$lang['label_colon'];?> <?php if($info['credit']['credit']['balance']){ echo $info['credit']['credit']['balance']; }else{ echo '0'; }?></p>
    </div>
  </div>
  <div class="aui-content aui-margin-b-10">
    <ul class="aui-list user-item">
      <li class="aui-list-item" onclick="javascript:location.href='<?php echo getUrl('member', 'loanContract', array(), false, WAP_SITE_URL)?>'">
        <div class="aui-list-item-label-icon">
          <img src="<?php echo WAP_SITE_URL;?>/resource/image/icon-1.png" alt="" class="icon-item">
        </div>
        <div class="aui-list-item-inner aui-list-item-arrow">
          <?php echo $lang['label_loan'];?>
        </div>
      </li>
      <li class="aui-list-item" onclick="javascript:location.href='<?php echo getUrl('member', 'insuranceContract', array(), false, WAP_SITE_URL)?>'">
        <div class="aui-list-item-label-icon">
          <img src="<?php echo WAP_SITE_URL;?>/resource/image/icon-2.png" alt="" class="icon-item">
        </div>
        <div class="aui-list-item-inner aui-list-item-arrow">
          <?php echo $lang['label_insurance'];?>
        </div>
      </li>
      <li class="aui-list-item" onclick="javascript:location.href='<?php echo getUrl('member', 'asiaweiluyAccount', array(), false, WAP_SITE_URL)?>'">
        <div class="aui-list-item-label-icon">
          <img src="<?php echo WAP_SITE_URL;?>/resource/image/icon-3.png" alt="" class="icon-item icon-item4">
        </div>
        <div class="aui-list-item-inner aui-list-item-arrow">
          <?php echo $lang['label_asiaweiluy_account'];?>
        </div>
      </li>
    </ul>
  </div>
 <div class="aui-content aui-margin-b-10">
   <ul class="aui-list user-item">
     <li class="aui-list-item" onclick="javascript:location.href='<?php echo getUrl('member', 'aboutUs', array(), false, WAP_SITE_URL)?>'">
       <div class="aui-list-item-label-icon">
         <img src="<?php echo WAP_SITE_URL;?>/resource/image/icon-4.png" alt="" class="icon-item icon-item4">
       </div>
       <div class="aui-list-item-inner aui-list-item-arrow">
         <?php echo $lang['label_about_us'];?>
       </div>
     </li>
    </ul>
  </div>
  <div class="aui-content aui-margin-b-15">
    <ul class="aui-list user-item">
      <li class="aui-list-item" onclick="javascript:location.href='<?php echo getUrl('member', 'setting', array(), false, WAP_SITE_URL)?>'">
        <div class="aui-list-item-label-icon">
          <img src="<?php echo WAP_SITE_URL;?>/resource/image/icon-5.png" alt="" class="icon-item icon-item4">
        </div>
        <div class="aui-list-item-inner aui-list-item-arrow">
          <?php echo $lang['label_setting'];?>
        </div>
      </li>
    </ul>
  </div>
</div>
<?php include_once(template('widget/inc_footer'));?>
<script type="text/javascript">

</script>
