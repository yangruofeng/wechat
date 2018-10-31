<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/member.css?v=5">
<div class="wrap user-wrap">
  <?php include_once(template('widget/inc_simple_header'));?>
  <?php $info = $output['member_info'];?>
  <div class="member-top"></div>
  <div class="aui-content aui-margin-b-10">
    <ul class="aui-list aui-media-list member-basic">
      <li class="aui-list-item aui-list-item-middle" onclick="javascript:location.href='<?php echo getUrl('member', 'editProfile', array(), false, WAP_OPERATOR_SITE_URL)?>'">
        <div class="aui-media-list-item-inner">
          <div class="aui-list-item-media"><img src="<?php echo getImageUrl($info['member_icon'])?:WAP_OPERATOR_SITE_URL.'/resource/image/default_avatar.png'; ?>" class="aui-img-round aui-list-img-sm avatar"></div>
          <div class="aui-list-item-inner aui-list-item-arrow">
            <?php if($info['member_name']){?>
              <div class="login">
                <p><?php echo $info['obj_guid'];?></p>
              </div>
            <?php }?>
            <div class="aui-list-item-text">
              <?php if(!$info['member_name']){?>
                <a href="<?php echo getUrl('login', 'index', array(), false, WAP_OPERATOR_SITE_URL)?>" class="log-link">Login/Register</a>
              <?php }?>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </div>
  <div class="aui-content aui-margin-b-10">
    <ul class="aui-list user-item">
      <li class="aui-list-item" onclick="javascript:location.href='<?php echo getUrl('member', 'cash', array(), false, WAP_OPERATOR_SITE_URL)?>'">
        <div class="aui-list-item-label-icon">
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/icon-1.png" alt="" class="icon-item">
        </div>
        <div class="aui-list-item-inner aui-list-item-arrow">
          <?php echo 'Card On Hand';?><span class="cash">$200</span>
        </div>
      </li>
      <li class="aui-list-item" onclick="javascript:location.href='<?php echo getUrl('member', 'setting', array(), false, WAP_OPERATOR_SITE_URL)?>'">
        <div class="aui-list-item-label-icon">
          <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/icon-5.png" alt="" class="icon-item">
        </div>
        <div class="aui-list-item-inner aui-list-item-arrow">
          <?php echo 'Setting';?>
        </div>
      </li>
    </ul>
  </div>
  <div class="aui-content aui-margin-b-10">
    <ul class="aui-list user-item">
      <li class="aui-list-item" onclick="logout();">
        <div class="aui-list-item-inner logout-btn">
          <?php echo 'LogOut';?>
        </div>
      </li>
    </ul>
  </div>
</div>
<?php include_once(template('widget/inc_footer'));?>
<script type="text/javascript">
function logout(){
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'GET',
    url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=member&op=logout',
    data: {},
    dataType: 'json',
    success: function(data){
      if(data.STS){
        window.location.href = "<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=member&op=index";
      }else{
        verifyFail(data.MSG);
      }
    },
    error: function(xhr, type){
      toast.hide();
      verifyFail('<?php echo $lang['tip_logout_fail'];?>');
    }
  });
}
</script>
