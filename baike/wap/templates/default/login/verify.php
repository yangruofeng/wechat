<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/login.css?v=4">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap verify-wrap">
  <div class="verify-form">
    <div class="form-item form-phone">
      <select name="country_code" id="country_code">
        <option value="66">+66</option>
        <option value="84">+84</option>
        <option value="86">+86</option>
        <option value="855">+855</option>
      </select>
      <i class="aui-iconfont aui-icon-down"></i>
      <input type="number" name="phone" id="phone" value="" placeholder="<?php echo $lang['tip_enter_mobile_number'];?>">
    </div>
    <div class="form-item">
      <input type="password" name="password" id="password" value="" placeholder="<?php echo $lang['label_password'];?>">
    </div>
    <div class="form-item">
      <input type="password" name="confirm_pwd" id="confirm_pwd" value="" placeholder="<?php echo $lang['label_confirm_password'];?>">
    </div>
    <input type="hidden" name="verify_id" id="verify_id" value="">
    <p class="error-tip"></p>
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple register-next-btn" id="next"><?php echo $lang['act_confirm'];?></div>
    <p class="agree-tip"><?php echo $lang['tip_your_hereby_agree_to'];?> <em><?php echo $lang['tip_samrithisak_register_protocol'];?></em></p>
  </div>
</div>
<div class="verify-success" style="display: none;">
  <div class="wrap">
    <div class="title">
      <?php echo $lang['label_enter_verify_code'];?>
    </div>
    <div class="content">
      <p class="tip1"><?php echo $lang['tip_verification_codebeen_send'];?><em></em>&nbsp;<em></em></p>
      <p class="tip1"><em id="vCode"></em>&nbsp;<em id="vPhone"></em>&nbsp;<em class="count-down" id="countDown"></em></p>
      <div class="verify-inputs clearfix">
        <input type="number" name="" value="" class="input1 active" index="1" onkeyup="changeInput(this);">
        <input type="number" name="" value="" class="input2" index="2" onkeyup="changeInput(this);">
        <input type="number" name="" value="" class="input3" index="3" onkeyup="changeInput(this);">
        <input type="number" name="" value="" class="input4" index="4" onkeyup="changeInput(this);">
        <input type="number" name="" value="" class="input5" index="5" onkeyup="changeInput(this);">
        <input type="number" name="" value="" class="input6" index="6" onkeyup="changeInput(this);">
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
var toast = new auiToast();
$('#next').on('click', function(e){
  $('.error-tip').text('');
  var country_code = $.trim($('#country_code').val()), phone = $.trim($('#phone').val()),
      verify_id = $.trim($('#verify_id').val()), verify_code = $.trim($('#verify_code').val()),
      password = $.trim($('#password').val()), confirm_pwd = $.trim($('#confirm_pwd').val());
  if(!phone){
    verifyFail('<?php echo $lang['tip_please_input_phone_number'];?>');
    return;
  }
  if(!password){
    verifyFail('<?php echo $lang['tip_please_input_pwd'];?>');
    return;
  }
  if(!confirm_pwd){
    verifyFail('<?php echo $lang['tip_please_input_confirm_pwd'];?>');
    return;
  }
  if(confirm_pwd != password){
    verifyFail('<?php echo $lang['tip_please_input_same_pwd'];?>');
    return;
  }
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'POST',
    url: '<?php echo WAP_SITE_URL;?>/index.php?act=login&op=getVetifyCode',
    data: { country_code: country_code, phone: phone },
    dataType: 'json',
    success: function(data){
      toast.hide();
      if(data.STS){
        $('#verify_id').val(data.DATA.verify_id);
        inputVerify(country_code, phone);
      }else{
        $('.error-tip').text(data.MSG);
      }
    },
    error: function(xhr, type){
      toast.hide();
      verifyFail('<?php echo $lang['tip_get_data_error'];?>');
    }
  });

  $('.verify-inputs input').on('click', function(){
    $('.verify-inputs input').blur();
    $('.verify-inputs input.active').focus();
  });

});
var sms_code = '';
function changeInput(el){
  var val = $.trim($(el).val()), index = parseInt($.trim($(el).attr('index'))), next = index + 1;
  var country_code = $.trim($('#country_code').val()), phone = $.trim($('#phone').val()),
      sms_id = $.trim($('#verify_id').val()),
      password = $.trim($('#password').val()), param = {};
  if(val){
    sms_code += val;
    if(index < 6){
      $('.verify-inputs input').blur();
      $('.verify-inputs .input' + next).focus();
      $('.verify-inputs input').removeClass('active');
      $('.verify-inputs .input'+next).addClass('active');
    }else{
      param.country_code = country_code;
      param.phone = phone;
      param.sms_id = sms_id;
      param.sms_code = sms_code;
      param.password = password;
      toast.loading({
        title: 'Loading...'
      });
      sms_code = '';
      $.ajax({
        type: 'POST',
        url: '<?php echo WAP_SITE_URL;?>/index.php?act=login&op=phoneRegister',
        data: param,
        dataType: 'json',
        success: function(data){
          toast.hide();
          if(data.STS){
            window.location.href = "<?php echo WAP_SITE_URL;?>/index.php?act=login&op=register&member_id="+data.DATA.member_id;
          }else{
            $('.error-tip').text(data.MSG);
            $('.verify-success').hide();
          }
        },
        error: function(xhr, type){
          toast.hide();
          verifyFail('Get API Error!');
        }
      });
    }
  }
}
function inputVerify(country_code, phone){
  $('#vCode').text('+' + country_code);
  $('#vPhone').text(phone);
  $('.verify-success').show();
  $('.verify-inputs .input1').focus();
  var count = 60, el = $('#countDown');
  el.text(count + 's');
  setInterval(function(){
    count--;
    el.text(count + 's');
    if(count <= 0){
      el.removeClass('disabled');
      el.text('Get Code');
    }
  }, 1000);
}
function verifyFail(msg){
  toast.fail({
    title: msg,
    duration: 2000
  });
}

</script>
