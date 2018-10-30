<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=3">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap register-wrap">
  <div class="reg-nav">
    <ul class="nav-ul clearfix">
      <li class="nav-item">
        <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up1.png"></p>
        <p class="text">Register</p>
      </li>
      <li class="nav-item">
        <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up2.png"></p>
        <p class="text">Information</p>
      </li>
      <li class="nav-item">
        <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up3.png"></p>
        <p class="text">Upload Avatar</p>
      </li>
      <li class="nav-item">
        <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up4.png"></p>
        <p class="text">Finished</p>
      </li>
    </ul>
  </div>
  <div class="aui-content custom-content aui-margin-b-10">
    <div class="base-form register-form">
      <div class="form-item form-phone aui-margin-b-10">
        <div class="phone-select-div">
          <select name="country_code" id="country_code">
            <option value="66">+66</option>
            <option value="84">+84</option>
            <option value="86">+86</option>
            <option value="855" selected>+855</option>
          </select>
          <i class="aui-iconfont aui-icon-down"></i>
        </div>
        <input type="number" name="phone" id="phone" value="" placeholder="<?php echo $lang['tip_enter_mobile_number'];?>">
      </div>
      <input type="hidden" name="verify_id" id="verify_id" value="">
      <p class="error-tip"></p>
      <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple register-next-btn" id="next"><?php echo $lang['act_confirm'];?></div>
    </div>
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
<script>
  $('#next').on('click', function(){
    var country_code = $.trim($('#country_code').val()), phone = $.trim($('#phone').val());
    if(!phone){
      verifyFail('Please input phone.');
      return;
    }
    $.ajax({
      url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=ajaxRegFirst',
      type: 'post',
      data: {country_code: country_code, phone: phone},
      success: function(data){
        toast.hide();
        if(data.STS){
          $('#verify_id').val(data.DATA.verify_id);
          inputVerify(country_code, phone);
        }else{
          $('.error-tip').text(data.MSG);
        }
      }
    });
  });
  function reGetCode(){
    $('#next').click();
  }
  function inputVerify(country_code, phone){
    $('#vCode').text('+' + country_code);
    $('#vPhone').text(phone);
    $('.verify-success').show();
    $('.verify-inputs .input1').focus();
    var count = 60, el = $('#countDown');
    el.text(count + 's');
    var time = setInterval(function(){
      count--;
      el.text(count + 's');
      if(count <= 0){
        el.removeClass('disabled');
        el.html('<span onclick="reGetCode();">Get Code</span>');
        clearInterval(time);
      }
    }, 1000);
  }
  var sms_code = '';
  function changeInput(el){
    var val = $.trim($(el).val()), index = parseInt($.trim($(el).attr('index'))), next = index + 1;
    var country_code = $.trim($('#country_code').val()), phone = $.trim($('#phone').val()),
        sms_id = $.trim($('#verify_id').val()), param = {};
    if(val){
      sms_code += val;
      if(index < 6){
        $('.verify-inputs input').blur();
        $('.verify-inputs .input' + next).focus();
        $('.verify-inputs input').removeClass('active');
        $('.verify-inputs .input'+next).addClass('active');
      }else{
        param.verify_id = sms_id;
        param.verify_code = sms_code;
        toast.loading({
          title: 'Loading...'
        });
        sms_code = '';
        $.ajax({
          type: 'POST',
          url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=phoneRegister',
          data: param,
          dataType: 'json',
          success: function(data){
            toast.hide();
            if(data.STS){
              window.location.href = "<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=regSecond&sms_id="+param.verify_id+"&sms_code="+param.verify_code+"&country_code="+country_code+"&phone="+phone;
            }else{
              verifyFail(data.MSG);
              //$('.error-tip').text(data.MSG);
              //$('.verify-success').hide();
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
  function verifyFail(msg){
    toast.fail({
      title: msg,
      duration: 2000
    });
  }
</script>