<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/member.css?v=7">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap setting-wrap">
  <div class="aui-content aui-margin-b-10">
    <ul class="aui-list user-item">
      <li class="aui-list-item">
        <div class="aui-list-item-inner avatar-item aui-list-item-arrow">
          <?php echo 'Avatar';?>
          <form class="form-avatar" id="form-avatar-upload" enctype="multipart/form-data"  method="post">
            <input type="hidden" name="token" value="<?php echo $output['token']; ?>">
            <input type="hidden" name="member_id" value="<?php echo $output['member_id']; ?>">
    				<span><?php echo $lang['member_info_avatar']; ?></span>
                    <img src="<?php echo getImageUrl($output['member_icon'])?:WAP_OPERATOR_SITE_URL.'/resource/image/default_avatar.png'; ?>" id="user_avatar_pic" />
	    			<span class="i-go-right"></span>
	    			<input type="file" name="m_avatar" id="m_avatar" class="m_avatar"  />
    			</form>
        </div>
      </li>
    </ul>
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
<script type="text/javascript">

var pc = new PhotoClip('.clipArea', {
		size: 260,
		outputSize: 640,
		file: '#file',
		ok: '#clipBtn',
		loadStart: function() {
			console.log('开始读取照片');
		},
		loadComplete: function() {
			console.log('照片读取完成');
		},
		done: function(dataURL) {
			//console.log(dataURL);
      $('.avatar-pic-wrap').hide();
      toast.loading({
        title: '<?php echo 'Uploading...';?>'
      });
			var data = dataURL.split(',')[1];
			data = window.atob(data);
			var ia = new Uint8Array(data.length);
			for (var i = 0; i < data.length; i++) {
			    ia[i] = data.charCodeAt(i);
			};
			// canvas.toDataURL 返回的默认格式就是 image/png
			var blob = new Blob([ia], {type:"image/png"});
			var formData = new FormData($('#form-avatar-upload')[0]);
			formData.append('avator',blob);

      $.ajax({
        type: 'POST',
        url: '<?php echo ENTRY_API_SITE_URL;?>/member.edit.profile.avator.php',
        data: formData,
        processData : false,
        contentType : false,
        dataType: 'json',
        success: function(data){
          console.log(data)
          toast.hide();
          if(data.STS){
            $('#user_avatar_pic').attr('src', data.MSG.member_icon);
          }else{
            verifyFail(data.MSG);
          }
        },
        error: function(xhr, type){
          toast.hide();
          verifyFail('<?php echo $lang['tip_get_data_error'];?>');
        }
      });
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
