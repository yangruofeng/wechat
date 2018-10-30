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
    <input type="hidden" name="login_type" id="login_type" value="1">
    <div class="form-item login-type-item login-type-item1 input-account">
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/login-1.png" alt="" class="icon">
      <input type="text" name="login_code" id="login_code" value="" placeholder="Account">
    </div>
    <div class="form-item login-type-item login-type-item2 input-phone"  style="display: none;">
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/login-3.png" alt="" class="icon">
      <select class="" name="country_code" id="country_code">
        <option value="66">+66</option>
        <option value="84">+84</option>
        <option value="86">+86</option>
        <option value="855">+855</option>
      </select>
      <i class="aui-iconfont aui-icon-down"></i>
      <input type="text" name="phone" id="phone" value="" placeholder="<?php echo $lang['label_phone'];?>">
    </div>
    <div class="form-item login-type-item login-type-item3 input-email"  style="display: none;">
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/login-2.png" alt="" class="icon">
      <input type="text" name="email" id="email" value="" placeholder="<?php echo $lang['label_email'];?>">
    </div>
    <div class="form-item input-pwd">
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/icon-pwd.png" alt="" class="icon">
      <input type="password" name="login_password" id="login_password" value="" placeholder="<?php echo $lang['label_password'];?>">
    </div>
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn login-btn" id="login"><?php echo $lang['act_login'];?></div>
    <div class="other-oprt">
      <a class="l-reg" href="<?php echo getUrl('login', 'verify', array(), false, WAP_SITE_URL)?>"><?php echo $lang['act_register'];?></a>
      <a class="pull-right" href="<?php echo getUrl('login', 'forgotPassword', array(), false, WAP_SITE_URL)?>"><?php echo $lang['act_forgot_password'];?></a>
    </div>
    <div class="other-login-method">
      <div class="title">
        <div class="line"></div>
        <span><?php echo $lang['tip_other_login_methods'];?></span>
      </div>
      <div class="content clearfix">
        <div>
          <a class="item item-1" onclick="toggleLoginType(1);" href="javasctipt:;" style="display: none;">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/login-1.png" alt="">
            <p><?php echo $lang['label_account'];?></p>
          </a>
          <a class="item item-2" onclick="toggleLoginType(2);" href="javasctipt:;">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/login-3.png" alt="">
            <p><?php echo $lang['label_phone'];?></p>
          </a>
          <a class="item item-3" onclick="toggleLoginType(3);" href="javasctipt:;">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/login-2.png" alt="">
            <p><?php echo $lang['label_email'];?></p>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  var toast = new auiToast();
  $('#login').on('click',function(){
    var login_type = parseInt($('#login_type').val()), login_code = $.trim($('#login_code').val()), login_password = $.trim($('#login_password').val());
    var param = {};
    param.login_type = login_type;
    param.client_id = 0;
    param.client_type = 'wap';
    switch (login_type) {
      case 1:
        if(!login_code){
          verifyFail('<?php echo $lang['tip_please_input_account'];?>');
          break;
        }
        param.login_code = login_code;
        break;
      case 2:
        var country_code = $.trim($('#country_code').val()), phone = $.trim($('#phone').val());
        if(!phone){
          verifyFail('<?php echo $lang['tip_please_input_phone_number'];?>');
          break;
        }
        param.country_code = country_code;
        param.phone = phone;
        break;
      case 3:
        var email = $.trim($('#email').val());
        if(!email){
          verifyFail('<?php echo $lang['tip_please_input_email'];?>');
          break;
        }
        param.email = email;
        break;
      default:
        if(!login_code){
          verifyFail('<?php echo $lang['tip_please_input_account'];?>');
          break;
        }
        param.login_code = login_code;
    }
    if(!login_password){
      verifyFail('<?php echo $lang['tip_please_input_login_pwd'];?>');
      return;
    }
    param.login_password = login_password;
    toast.loading({
      title: '<?php echo $lang['label_loading'];?>'
    });
    $.ajax({
      type: 'POST',
      url: '<?php echo WAP_SITE_URL;?>/index.php?act=login&op=login',
      data: param,
      dataType: 'json',
      success: function(data){
        toast.hide();
        if(data.STS){
          window.location.href = "<?php echo getUrl('loan', 'index', array(), false, WAP_SITE_URL)?>";
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
  function toggleLoginType(type){
    $('a.item').show();
    $('a.item-'+type).hide();
    $('.login-type-item').hide();
    $('.login-type-item'+type).show();
    $('#login_type').val(type);
  }
  function verifyFail(msg){
    toast.fail({
      title: msg,
      duration: 2000
    });
  }
</script>
