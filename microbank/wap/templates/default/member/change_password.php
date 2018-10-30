<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/member.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap change-pwd-wrap">
  <div class="change-pwd-form">
    <ul class="aui-list aui-form-list credit-loan-item">
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo $lang['label_old_password'];?>
          </div>
          <div class="aui-list-item-input">
            <input type="password" name="old_pwd" id="old_pwd" placeholder="<?php echo $lang['label_enter'];?>">
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo $lang['label_new_password'];?>
          </div>
          <div class="aui-list-item-input">
            <input type="password" name="new_pwd" id="new_pwd" placeholder="<?php echo $lang['label_enter'];?>">
          </div>
        </div>
      </li>
      <li class="aui-list-item">
        <div class="aui-list-item-inner">
          <div class="aui-list-item-label label">
            <?php echo $lang['label_confirm_password'];?>
          </div>
          <div class="aui-list-item-input">
            <input type="password" name="confirm_pwd" id="confirm_pwd" placeholder="<?php echo $lang['label_enter'];?>">
          </div>
        </div>
      </li>
    </ul>
    <div style="padding: .6rem 0;">
      <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" id="confirm"><?php echo $lang['act_confirm'];?></div>
    </div>
  </div>
</div>
<script type="text/javascript">
$('#confirm').on('click', function(){
  var old_pwd = $.trim($('#old_pwd').val()), new_pwd = $.trim($('#new_pwd').val()), confirm_pwd = $.trim($('#confirm_pwd').val());
  if(!old_pwd){
    verifyFail('<?php echo $lang['tip_please_input_old_password'];?>');
    return;
  }
  if(!new_pwd){
    verifyFail('<?php echo $lang['tip_please_input_new_password'];?>');
    return;
  }
  if(!confirm_pwd){
    verifyFail('<?php echo $lang['tip_please_input_confirm_pwd'];?>');
    return;
  }
  if(new_pwd != confirm_pwd){
    verifyFail('<?php echo $lang['tip_please_input_same_pwd'];?>');
    return;
  }
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });

  $.ajax({
    type: 'POST',
    url: '<?php echo WAP_SITE_URL;?>/index.php?act=member&op=ajaxChangePassword',
    data: {old_pwd: old_pwd, new_pwd: new_pwd},
    dataType: 'json',
    success: function(data){
      toast.hide();
      if(data.STS){
        verifyFail(data.MSG);
        setTimeout(function(){
          $('.back').click();
        },2000);
        //window.location.href = "<?php echo WAP_SITE_URL;?>/index.php?act=member&op=index";
      }else{
        verifyFail(data.MSG);
      }
    },
    error: function(xhr, type){
      toast.hide();
      verifyFail('<?php echo $lang['tip_modify_fail'];?>');
    }
  });
});
</script>
