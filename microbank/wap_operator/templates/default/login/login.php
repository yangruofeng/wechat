<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/login.css?v=3">
<style>
  html, body {
    background-color: #000;
  }
</style>
<div class="wrap login-wrap">
  <div class="login-logo">
    <img src="<?php echo WAP_SITE_URL;?>/resource/image/logo.png" alt="LOGO">
  </div>
  <div class="login-form">
    <div class="form-item login-type-item login-type-item1 input-account">
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/login-1.png" alt="" class="icon">
      <input type="text" name="user_code" id="user_code" value="" placeholder="Account">
    </div>
    <div class="form-item input-pwd">
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/icon-pwd.png" alt="" class="icon">
      <input type="password" name="login_password" id="login_password" value="" placeholder="<?php echo $lang['label_password'];?>">
    </div>
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn login-btn" id="login"><?php echo $lang['act_login'];?></div>
    <div class="other-oprt">
      <a class="pull-right" href="<?php echo getUrl('login', 'forgotPassword', array(), false, WAP_SITE_URL)?>"><?php echo $lang['act_forgot_password'];?></a>
    </div>
  </div>
</div>
<script type="text/javascript">
  var toast = new auiToast();
  $('#login').on('click',function(){
    var user_code = $.trim($('#user_code').val()), login_password = $.trim($('#login_password').val());
    var param = {};
    param.user_code = user_code;
    param.client_type = 'wap';
    if(!user_code){
      verifyFail('<?php echo $lang['tip_please_input_user_account'];?>');
      return;
    }
    if(!login_password){
      verifyFail('<?php echo $lang['tip_please_input_login_pwd'];?>');
      return;
    }
    param.password = login_password;
    toast.loading({
      title: '<?php echo $lang['label_loading'];?>'
    });
    $.ajax({
      type: 'POST',
      url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=login&op=login',
      data: param,
      dataType: 'json',
      success: function(data){
        toast.hide();
        if(data.STS){
          window.location.href = "<?php echo getUrl('client', 'index', array(), false, WAP_OPERATOR_SITE_URL)?>";
        }else{
          verifyFail(data.MSG);
        }
      },
      error: function(xhr, type){
        toast.hide();
        verifyFail('<?php echo $lang['tip_get_data_error'];?>');
      }
    });
  });
  function verifyFail(msg){
    toast.fail({
      title: msg,
      duration: 2000
    });
  }
</script>
