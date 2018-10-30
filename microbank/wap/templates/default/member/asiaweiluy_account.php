<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/member.css?v=6">
<?php include_once(template('widget/inc_header'));?>
<?php $detail = $output['detail'];?>
<div class="wrap bank-account-wrap">
  <div class="bank-logo">
    <img src="<?php echo WAP_SITE_URL;?>/resource/image/asiaweiluy.png" class="logo">
  </div>
  <div class="bank-form">
    <input type="hidden" name="account_handler_id" id="account_handler_id" value="<?php if($detail){echo $detail['uid'];}?>">
    <div class="form-item first">
      <label for=""><?php echo $lang['label_account'];?></label>
      <input type="text" name="account" id="account" value="<?php if($detail){echo $detail['handler_account'];}?>" placeholder="<?php echo $lang['label_enter'];?>">
    </div>
    <div class="form-item">
      <label for=""><?php echo $lang['label_member_name'];?></label>
      <input type="text" name="name" id="name" value="<?php if($detail){echo $detail['handler_name'];}?>" placeholder="<?php echo $lang['label_enter'];?>">
    </div>
    <div class="form-item form-select last">
      <label for=""><?php echo $lang['label_account_phone'];?></label>
      <input type="hidden" name="verify_id" id="verify_id" value="227">
      <div class="form-select-input" <?php if($detail){echo 'style="display: none;"';}?>>
        <select name="country_code" id="country_code">
          <option value="66">+66</option>
          <option value="84">+84</option>
          <option value="86">+86</option>
          <option value="855">+855</option>
        </select>
        <i class="aui-iconfont aui-icon-down"></i>
        <input type="number" name="phone" id="phone" value="" placeholder="<?php echo $lang['label_enter'];?>">
      </div>
      <input type="text" name="handler_phone" id="handler_phone" value="<?php if($detail){echo $detail['handler_phone'];}?>" <?php if(!$detail){echo 'style="display: none;"';}?>>
    </div>
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn register-next-btn custom-btn-purple" id="edit" <?php if(!$detail){echo 'style="display: none;"';}?>><?php echo $lang['act_edit'];?></div>
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn register-next-btn custom-btn-purple" id="confirm" <?php if($detail){echo 'style="display: none;"';}?>><?php echo $lang['act_submit'];?></div>
  </div>
  <div class="bank-notice">
    <div class="title">
      <?php echo $lang['label_notice'];?>
    </div>
    <div class="content">
      <?php echo $lang['label_only_support_bind_bank'];?>
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
<script type="text/javascript">
  $('#edit').on('click', function(){
    $('#account').val('');
    $('#name').val('');
    $('#phone').val('');
    $('.form-select-input').show();
    $('#handler_phone').hide();
    $('#edit').hide();
    $('#confirm').show();
  });
  $('#confirm').on('click', function(){
    var account = $.trim($('#account').val()), name = $.trim($('#name').val()),
        country_code = $.trim($('#country_code').val()), phone = $.trim($('#phone').val());
    if(!account){
      verifyFail('');
      return;
    }
    if(!name){
      verifyFail('<?php echo $lang['tip_please_input_ace_name'];?>');
      return;
    }
    if(!phone){
      verifyFail('<?php echo $lang['tip_please_input_ace_phone'];?>');
      return;
    }
    toast.loading({
      title: '<?php echo $lang['label_loading'];?>'
    });
    $.ajax({
      type: 'POST',
      url: '<?php echo WAP_SITE_URL;?>/index.php?act=member&op=getVetifyCode',
      data: { country_code: country_code, phone: phone },
      dataType: 'json',
      success: function(data){
        toast.hide();
        inputVerify(country_code, phone);
        /*if(data.STS){
          $('#verify_id').val(data.DATA.verify_id);
          inputVerify(country_code, phone);
        }else{
          verifyFail(data.MSG);
        }*/
      },
      error: function(xhr, type){
        toast.hide();
        verifyFail('<?php echo $lang['tip_get_data_error'];?>');
      }
    });
  });
  var sms_code = '';
  function changeInput(el){
    var val = $.trim($(el).val()), index = parseInt($.trim($(el).attr('index'))), next = index + 1;
    var verify_id = $.trim($('#verify_id').val()), param = {};
    if(val){
      sms_code += val;
      if(index < 6){
        $('.verify-inputs input').blur();
        $('.verify-inputs .input' + next).focus();
        $('.verify-inputs input').removeClass('active');
        $('.verify-inputs .input'+next).addClass('active');
      }else{
        param.verify_id = verify_id;
        param.verify_code = sms_code;
        toast.loading({
          title: 'Loading...'
        });
        handleAccount(sms_code);
        sms_code = '';
      }
    }
  }
  function handleAccount(sms_code){
    var account_handler_id = $.trim($('#account_handler_id').val()), account = $.trim($('#account').val()), name = $.trim($('#name').val()),
        country_code = $.trim($('#country_code').val()), phone = $.trim($('#phone').val()),
        sms_id = $.trim($('#verify_id').val()), param = {};
        var url ='<?php echo WAP_SITE_URL;?>/index.php?act=member&op=bankHandle';
        if(account_handler_id){
          param.account_handler_id = account_handler_id;
        }
        param.sms_id = sms_id;
        param.sms_code = sms_code;
        param.account = account;
        param.name = name;
        param.country_code = country_code;
        param.phone = phone;
        $.ajax({
          type: 'POST',
          url: url,
          data: param,
          dataType: 'json',
          success: function(data){
            toast.hide();
            if(data.STS){
              var data = data.DATA;
              $('.verify-success').hide();
              $('#account_handler_id').val(data.uid);
              $('#account').val(data.handler_account);
              $('#name').val(data.handler_name);
              $('.form-select-input').hide();
              $('#handler_phone').show();
              $('#handler_phone').val(data.handler_phone);
            }else{
              verifyFail(data.MSG);
            }
            //inputVerify(country_code, phone);
            /*if(data.STS){
              $('#verify_id').val(data.DATA.verify_id);
              inputVerify(country_code, phone);
            }else{
              verifyFail(data.MSG);
            }*/
          },
          error: function(xhr, type){
            toast.hide();
            verifyFail('<?php echo $lang['tip_get_data_error'];?>');
          }
        });
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
        el.text('<?php echo $lang['act_get_code'];?>');
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
