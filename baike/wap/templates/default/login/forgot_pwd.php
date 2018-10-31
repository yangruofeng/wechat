<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/login.css?v=5">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap forgot-pwd-wrap">
  <div class="forgot-pwd-form">
    <div class="form-item form-phone">
      <select class="" name="country_code" id="country_code">
        <option value="66">+66</option>
        <option value="84">+84</option>
        <option value="86">+86</option>
        <option value="855">+855</option>
      </select>
      <i class="aui-iconfont aui-icon-down"></i>
      <input type="number" name="phone" id="phone" value="" placeholder="<?php echo $lang['tip_enter_mobile_number'];?>">
    </div>
    <div class="form-item get-verify">
      <input type="number" name="verify_code" id="verify_code" value="" placeholder="<?php echo $lang['tip_enter_verification_code'];?>">
      <span class="btn-verify" id="getCode"><?php echo $lang['act_get_code'];?></span>
    </div>
    <div class="form-item">
      <input type="password" name="password" id="password" value="" placeholder="<?php echo $lang['tip_set_new_password'];?>">
    </div>
    <input type="hidden" name="verify_id" id="verify_id" value="">
    <p class="error-tip"></p>
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple register-next-btn" id="confirm"><?php echo $lang['act_confirm'];?></div>
    <p class="agree-tip"><?php echo $lang['tip_your_hereby_agree_to'];?> <em><?php echo $lang['tip_samrithisak_register_protocol'];?></em></p>
  </div>
</div>
<script type="text/javascript">
var toast = new auiToast();
$('#getCode').on('click', function(e){
  $('.error-tip').text('');
  var el = $('#getCode');
  if(el.hasClass('disabled')) return;
  var country_code = $.trim($('#country_code').val()), phone = $.trim($('#phone').val()), count = 60;
  if(!phone){
    verifyFail('<?php echo $lang['tip_please_input_phone_number'];?>');
    return;
  }
  toast.loading({
      title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'GET',
    url: '<?php echo ENTRY_API_SITE_URL;?>/phone.code.send.php',
    data: { country_code: country_code, phone: phone },
    dataType: 'json',
    success: function(data){
      toast.hide();
      if(data.STS){
        $('#verify_id').val(data.DATA.verify_id);
        toast.success({
          title: data.MSG,
          duration: 2000
        });
        el.addClass('disabled');
        setInterval(function(){
          el.text(count + 's');
          count--;
          if(count <= 0){
            el.removeClass('disabled');
            el.text('Get Code');
          }
        }, 1000);
      }else{
        $('.error-tip').text(data.MSG);
      }

    },
    error: function(xhr, type){
      toast.hide();
      verifyFail('<?php echo $lang['tip_get_data_error'];?>');
    }
  });
});
$('#confirm').on('click', function(e){
  var country_code = $.trim($('#country_code').val()), phone = $.trim($('#phone').val()),
      sms_id = $.trim($('#verify_id').val()), sms_code = $.trim($('#verify_code').val()), password = $.trim($('#password').val());
  if(!phone){
    verifyFail('<?php echo $lang['tip_please_input_phone_number'];?>');
    return;
  }
  if(!sms_code){
    verifyFail('<?php echo $lang['tip_please_input_verify_code'];?>');
    return;
  }
  if(!sms_id){
    verifyFail('<?php echo $lang['tip_please_reget_verify_code'];?>');
    return;
  }
  if(!password){
    verifyFail('<?php echo $lang['tip_please_input_new_pwd'];?>');
    return;
  }
  toast.loading({
      title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'GET',
    url: '<?php echo ENTRY_API_SITE_URL;?>/member.resetpwd.php',
    data: { country_code: country_code, phone: phone, sms_id: sms_id, sms_code: sms_code, password: password },
    dataType: 'json',
    success: function(data){
      toast.hide();
      if(data.STS){
        toast.success({
          title: 'Success',
          duration: 1000
        });
        setTimeout(function(){
          window.location.href = "<?php echo getUrl('login', 'index', array(), false, WAP_SITE_URL)?>";
        }, 1000);
      }else{
        $('.error-tip').text(data.MSG);
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
