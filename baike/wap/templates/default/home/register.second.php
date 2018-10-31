<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap register-wrap">
  <div class="base-wrapper" style="display: block;">
    <div class="reg-nav">
      <ul class="nav-ul clearfix">
        <li class="nav-item">
          <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up1.png"></p>
          <p class="text">Register</p>
        </li>
        <li class="nav-item">
          <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up2_press.png"></p>
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
        <div class="form-item aui-margin-b-10">
          <label for=""><?php echo $lang['label_login_account'];?></label>
          <input type="text" name="login_code" id="login_code" value="" placeholder="Enter">
        </div>
        <div class="form-item aui-margin-b-10">
          <label for=""><?php echo $lang['label_password'];?></label>
          <input type="password" name="password" id="password" value="" placeholder="Enter">
        </div>
        <div class="form-item aui-margin-b-10">
          <label for=""><?php echo $lang['label_confirm_password'];?></label>
          <input type="password" name="confirm_pwd" id="confirm_pwd" value="" placeholder="Enter">
        </div>
        <div class="form-item form-select aui-margin-b-10">
          <label for=""><?php echo 'Civil Status';?></label>
          <select name="civil_status" id="civil_status">
            <option value="0">Select</option>
            <option value="1">Spinsterhood</option>
            <option value="2">Married</option>
          </select>
          <i class="aui-iconfont aui-icon-down"></i>
        </div>
        <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" id="baseInfo"><?php echo 'Next';?></div>
      </div>
    </div>
  </div>
  <div class="avatar-wrapper" style="display: none;">
    <div class="reg-nav">
      <ul class="nav-ul clearfix">
        <li class="nav-item">
          <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up1.png"></p>
          <p class="text">Register</p>
        </li>
        <li class="nav-item">
          <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up2_press.png"></p>
          <p class="text">Information</p>
        </li>
        <li class="nav-item">
          <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up3_press.png"></p>
          <p class="text">Upload Avatar</p>
        </li>
        <li class="nav-item">
          <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up4.png"></p>
          <p class="text">Finished</p>
        </li>
      </ul>
    </div>
    <div class="base-form register-form">
      <div class="avatar-main">
        <form class="form-avatar" id="form-avatar-upload" enctype="multipart/form-data"  method="post">
          <input type="hidden" name="is_bind_officer" value="1">
          <input type="hidden" name="officer_id" value="<?php echo $output['officer_id'];?>">
          <input type="hidden" name="officer_name" value="<?php echo $output['officer_name'];?>">
          <input type="hidden" name="country_code" value="<?php echo $_GET['country_code'];?>">
          <input type="hidden" name="phone" value="<?php echo $_GET['phone'];?>">
          <input type="hidden" name="sms_id" value="<?php echo $_GET['sms_id'];?>">
          <input type="hidden" name="sms_code" value="<?php echo $_GET['sms_code'];?>">
          <span><?php echo $lang['member_info_avatar']; ?></span>
          <img src="<?php echo getImageUrl($output['member_icon'])?:WAP_OPERATOR_SITE_URL.'/resource/image/default_avatar1.png'; ?>" id="user_avatar_pic" />
          <span class="i-go-right"></span>
          <input type="file" name="photo" id="m_avatar" class="m_avatar"  />
        </form>
      </div>
      <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" id="confirm"><?php echo $lang['act_confirm'];?></div>
    </div>
  </div>
</div>
<!--------------------图片裁剪-------------------->
<div class="avatar-pic-wrap" style="display:none;">
	<div class="opr">
		<div class="cancel"><?php echo $lang['act_cancel']; ?></div>
		<div class="define" id="clipBtn"><?php echo $lang['act_confirm']; ?></div>
	</div>
	<div class="clip-area">
    <div class="clipArea"></div>
  </div>
</div>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/PhotoClip/iscroll-zoom-min.js"></script>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/PhotoClip/hammer.min.js"></script>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/PhotoClip/lrz.all.bundle.js"></script>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/PhotoClip/PhotoClip.js"></script>
<script>

var pc = new PhotoClip('.clipArea', {
  size: 260,
  outputSize: 640,
  file: '#file',
  ok: '#clipBtn',
  loadStart: function() {
    //console.log('开始读取照片');
  },
  loadComplete: function() {
    //console.log('照片读取完成');
  },
  done: function(dataURL) {
    //console.log(dataURL);
    $('.avatar-pic-wrap').hide();
    var data = dataURL.split(',')[1];
    data = window.atob(data);
    var ia = new Uint8Array(data.length);
    for (var i = 0; i < data.length; i++) {
        ia[i] = data.charCodeAt(i);
    };
    // canvas.toDataURL 返回的默认格式就是 image/png
    var blob = new Blob([ia], {type:"image/png"});
    var formData = new FormData($('#form-avatar-upload')[0]);
    formData.append('photo',blob);
    $('#user_avatar_pic').attr('src', dataURL);
  },
  fail: function(msg) {
    alert(msg);
  }
});

$('#m_avatar').on('change',function(){
  $('.avatar-pic-wrap').show();
  pc.load(this.files[0]);
});
$('.avatar-pic-wrap .cancel').click(function(){
  $('.avatar-pic-wrap').hide()
});
$('#baseInfo').on('click', function(){
  var login_code = $.trim($('#login_code').val()), password = $.trim($('#password').val()),
      confirm_pwd = $.trim($('#confirm_pwd').val()), civil_status = $.trim($('#civil_status').val());
  if(!login_code){
    verifyFail('Please input login account.');
    return;
  }
  if(!password){
    verifyFail('Please input password.');
    return;
  }
  if(!confirm_pwd){
    verifyFail('Please input confirm password.');
    return;
  }
  if(password !== confirm_pwd){
    verifyFail('Please input the same password.');
    return;
  }
  if(civil_status == 0){
    verifyFail('Please select civil status.');
    return;
  }
  $('.base-wrapper').hide();
  $('.avatar-wrapper').show();
  });

  $('#confirm').on('click', function(){
    var formData = new FormData($('#form-avatar-upload')[0]);
    var login_code = $.trim($('#login_code').val()), password = $.trim($('#password').val()),
        confirm_pwd = $.trim($('#confirm_pwd').val()), civil_status = $.trim($('#civil_status').val());
    formData.append('login_code',login_code);
    formData.append('password',password);
    formData.append('civil_status',civil_status);
    $.ajax({
      type: 'POST',
      url: '<?php echo ENTRY_API_SITE_URL;?>/member.phone.register.new.php',
      data: formData,
      processData : false,
      contentType : false,
      dataType: 'json',
      success: function(data){
        if(data.STS){
          window.location.href = '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=regThird';
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
  
</script>