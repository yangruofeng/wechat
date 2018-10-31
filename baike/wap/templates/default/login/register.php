<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/login.css?v=1">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/script/datepicker/datepicker.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/script/lCalendar/lCalendar.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap register-wrap">
  <div class="register-form">
    <input type="hidden" name="member_id" id="member_id" value="<?php echo $_GET['member_id'];?>">
    <div class="form-item">
      <label for=""><?php echo $lang['label_login_account'];?></label>
      <input type="text" name="login_code" id="login_code" value="" placeholder="Enter">
    </div>
    <div class="form-item">
      <label for=""><?php echo $lang['label_family_name'];?></label>
      <input type="text" name="family_name" id="family_name" value="" placeholder="Enter">
    </div>
    <div class="form-item">
      <label for=""><?php echo $lang['label_given_name'];?></label>
      <input type="text" name="given_name" id="given_name" value="" placeholder="Enter">
    </div>
    <div class="form-item input-gender">
      <label for=""><?php echo $lang['label_gender'];?></label>
      <select class="" name="gender" id="gender">
        <option value="male"><?php echo $lang['label_male'];?></option>
        <option value="female"><?php echo $lang['label_female'];?></option>
      </select>
      <i class="aui-iconfont aui-icon-down"></i>
    </div>
    <div class="form-item input-date">
      <label for=""><?php echo $lang['label_birthday'];?></label>
      <input type="text" name="birthday" id="birthday" value="" placeholder="Enter" data-lcalendar="1900-01-01,<?php echo Date('Y-m-d');?>" onfocus="this.blur()" onfocus="this.blur()">
      <i class="aui-iconfont aui-icon-calendar"></i>
    </div>
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple register-next-btn" id="confirm"><?php echo $lang['act_confirm'];?></div>
    <div class="aui-btn aui-btn-danger aui-btn-block aui-btn-outlined custom-btn mt03rem" id="skip"><?php echo $lang['act_skip'];?></div>
    <p class="agree-tip"><?php echo $lang['tip_your_hereby_agree_to'];?> <em><?php echo $lang['tip_samrithisak_register_protocol'];?></em></p>
  </div>
</div>
<div class="register-success">
  <div class="content">
    <img src="<?php echo WAP_SITE_URL;?>/resource/image/gou.png" alt="">
    <p class="title"><?php echo $lang['tip_signup_succeeded'];?></p>
    <p class="tip"><?php echo str_replace('xxx','<em id="count">3</em>',$lang['tip_jump_to_login_cutdown']);?></p>
  </div>
</div>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/datepicker/datepicker.min.js?v=2"></script>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/lCalendar/lCalendar_<?php echo Language::currentCode() == 'zh_cn' ?:'en';?>.js?v=5"></script>
<script type="text/javascript">
var calendar = new lCalendar();
calendar.init({
  'trigger': '#birthday',
  'name': '<?php echo $lang['label_start_time'];?>',
  'type': 'date'
});

var toast = new auiToast();

$('#confirm').on('click', function(e){
  var member_id = $.trim($('#member_id').val()), login_code = $.trim($('#login_code').val()), family_name = $.trim($('#family_name').val()),
      given_name = $.trim($('#given_name').val()),gender = $.trim($('#gender').val()), birthday = $.trim($('#birthday').val()), param = {};
  if(!login_code){
    verifyFail('<?php echo $lang['tip_please_input_account'];?>');
    return;
  }
  if(!family_name){
    verifyFail('<?php echo $lang['tip_please_input_family_name'];?>');
    return;
  }
  if(!given_name){
    verifyFail('<?php echo $lang['tip_please_input_given_name'];?>');
    return;
  }
  param.member_id = member_id;
  param.login_code = login_code;
  param.family_name = family_name;
  param.given_name = given_name;
  param.gender = gender;
  param.birthday = birthday;
  $.ajax({
    type: 'POST',
    url: '<?php echo WAP_SITE_URL;?>/index.php?act=login&op=registerDetail',
    data: param,
    dataType: 'json',
    success: function(data){
      if(data.STS){
        $('.register-success').show();
        var count = $('#count').text();
        var times = setInterval(function(){
          count--;
          $('#count').text(count);
          if(count <= 0){
            clearInterval(times);
            window.location.href = "<?php echo getUrl('login', 'index', array(), false, WAP_SITE_URL)?>";
          }
        },1000);
      }else{
        verifyFail(data.MSG);
      }
    },
    error: function(xhr, type){
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
