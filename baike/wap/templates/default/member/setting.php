<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/member.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap setting-wrap">
  <div class="aui-content aui-margin-b-10">
    <ul class="aui-list user-item">
      <li class="aui-list-item" onclick="javascript:location.href='<?php echo getUrl('member', 'changePassword', array(), false, WAP_OPERATOR_SITE_URL)?>'">
        <div class="aui-list-item-inner aui-list-item-arrow">
          <?php echo $lang['label_password'];?>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner aui-list-item-arrow" onclick="javascript:location.href='<?php echo getUrl('member', 'changeLang', array(), false, WAP_OPERATOR_SITE_URL)?>'">
          <?php echo $lang['label_language'];?>
          <span class="color949494">
            <?php if(Language::currentCode() == 'zh_cn'){echo $lang['label_simplified_chinese'];}elseif(Language::currentCode() == 'kh'){echo $lang['label_khmer'];}else{echo $lang['label_english'];}?>
          </span>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner aui-list-item-arrow">
          <?php echo $lang['label_feedback'];?>
        </div>
      </li>
    </ul>
  </div>
  <div class="aui-content aui-margin-b-15">
    <ul class="aui-list user-item">
      <li class="aui-list-item" onclick="javascript:location.href='<?php echo getUrl('member', 'setting', array(), false, WAP_OPERATOR_SITE_URL)?>'">
        <div class="aui-list-item-inner aui-list-item-arrow">
          <?php echo $lang['label_help'];?>
        </div>
      </li>
    </ul>
  </div>
  <div class="aui-content aui-margin-t-10">
    <ul class="aui-list user-item">
      <li class="aui-list-item" onclick="logout();">
        <div class="aui-list-item-inner logout-btn">
          <?php echo $lang['act_logOut'];?>
        </div>
      </li>
    </ul>
  </div>
</div>
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
