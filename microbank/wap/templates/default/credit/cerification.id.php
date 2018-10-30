<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=6">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap cerification-wrap">
  <form id="uploadPicture" enctype="multipart/form-data" method="post">
    <input type="hidden" value="<?php echo $output['token'];?>" name="token">
    <input type="hidden" value="<?php echo $output['member_id'];?>" name="member_id">
    <?php if($output['cert_id'] > 0) { ?>
      <input type="hidden" value="<?php echo $output['cert_id'];?>" name="cert_id">
    <?php } ?>
    <div class="aui-margin-b-10">
      <div class="id-cert-form">
        <ul class="aui-list aui-form-list">
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                <?php echo 'English Name';?>
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="name_en" id="name_en" placeholder="Enter">
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                <?php echo 'Khmer Name';?>
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="name_kh" id="name_kh" placeholder="Enter">
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                <?php echo 'ID Number';?>
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="cert_sn" id="cert_sn" placeholder="Enter">
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div class="verify-cert-id">
      Upload Certificate Image
    </div>
    <div class="cerification-picture">
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="front_photo_uncheck">
              <input type="file" id="front_photo" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_SITE_URL;?>/resource/image/upload-1.png?v=1" alt="" class="icon-upload">
            </div>
            <div class="check" id="front_photo_check">
              <span class="cancel-check" onclick="cancelCheck('front_photo');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="front_photo_check_img">
            </div>
          </div>
          <div class="name"><?php echo $lang['label_frontal_id_card_image'];?></div>
        </div>
        <div class="up-example">
          <div class="up-exam">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/cert-example-1.png" alt="" class="example-pic">
          </div>
          <div class="name"><?php echo $lang['label_example'];?></div>
        </div>
      </div>
    </div>
    <div class="cerification-picture">
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="back_photo_uncheck">
              <input type="file" id="back_photo" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_SITE_URL;?>/resource/image/upload-1.png?v=1" alt="" class="icon-upload">
            </div>
            <div class="check" id="back_photo_check">
              <span class="cancel-check" onclick="cancelCheck('back_photo');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="back_photo_check_img">
            </div>
          </div>
          <div class="name"><?php echo $lang['label_back_id_card_image'];?></div>
        </div>
        <div class="up-example">
          <div class="up-exam">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/cert-example-2.png" alt="" class="example-pic">
          </div>
          <div class="name"><?php echo $lang['label_example'];?></div>
        </div>
      </div>
    </div>
    <div class="cerification-picture">
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="hand_photo_uncheck">
              <input type="file" id="hand_photo" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_SITE_URL;?>/resource/image/upload-1.png?v=1" alt="" class="icon-upload">
            </div>
            <div class="check" id="hand_photo_check">
              <span class="cancel-check" onclick="cancelCheck('hand_photo');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="hand_photo_check_img">
            </div>
          </div>
          <div class="name"><?php echo $lang['label_handheld_id_card_image'];?></div>
        </div>
        <div class="up-example">
          <div class="up-exam">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/cert-example-3.png" alt="" class="example-pic">
          </div>
          <div class="name"><?php echo $lang['label_example'];?></div>
        </div>
      </div>
    </div>
    <div style="padding: .5rem 1rem;">
      <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" id="submit"><?php echo $lang['act_submit'];?></div>
    </div>
  </form>
</div>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/common.js"></script>
<script type="text/javascript">
var formData = new FormData(document.getElementById('uploadPicture'));
$('#submit').on('click', function(){
  var name_en = $.trim($('#name_en').val()), name_kh = $.trim($('#name_kh').val()), cert_sn = $.trim($('#cert_sn').val()),
      front_photo = $('#front_photo_check_img').attr('src'), back_photo = $('#back_photo_check_img').attr('src'), hand_photo = $('#hand_photo_check_img').attr('src');
  if(!name_en){
    verifyFail('Pelese input the english name.');
  }
  if(!name_kh){
    verifyFail('Pelese input the khmer name.');
  }
  if(!cert_sn){
    verifyFail('Pelese input the ID number.');
  }
  if(!front_photo || !back_photo || !hand_photo){
    verifyFail('<?php echo $lang['tip_please_upload_all_photos'];?>');
    return;
  }
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'POST',
    url: '<?php echo ENTRY_API_SITE_URL;?>/member.cert.id.php',
    data: formData,
    processData : false,
    contentType : false,
    dataType: 'json',
    success: function(data){
      toast.hide();
      if(data.STS){
        window.location.href = "<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=showCertCheckInfo&type="+data.DATA.cert_result.cert_type+"&back=credit";
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
var demo_h5_upload_ops = {
    init:function(){
      this.eventBind();
    },
    eventBind:function(){
        var that = this;
        $("#front_photo,#back_photo,#hand_photo").change(function(){
          alert()
            var reader = new FileReader(), self = this;
            reader.onload = function (e) {
                that.compress(this.result, $(self).attr('id'));
            };
            reader.readAsDataURL(this.files[0]);
        });
    },
    compress : function (res, el) {
      var that = this;
      var img = new Image(),
          maxH = 1000;
      img.onload = function () {
          var cvs = document.createElement('canvas'),
              ctx = cvs.getContext('2d');

          if(img.height > maxH) {
              img.width *= maxH / img.height;
              img.height = maxH;
          }
          cvs.width = img.width;
          cvs.height = img.height;

          ctx.clearRect(0, 0, cvs.width, cvs.height);
          ctx.drawImage(img, 0, 0, img.width, img.height);
          var dataUrl = cvs.toDataURL('image/jpeg', 1);
          var blob = convertImgDataToBlob(dataUrl);
          formData.append(el, blob);
          $('#'+el+'_check_img').attr('src', dataUrl);
          $('#'+el+'_uncheck').hide();
          $('#'+el+'_check').show();
      };
      img.src = res;
    }
};
$(document).ready( function(){
  demo_h5_upload_ops.init();
});

function cancelCheck(el){
  $('#'+el+'_check_img').attr('src', '');
  $('#'+el+'_uncheck').show();
  $('#'+el+'_check').hide();
  formData.delete(el);
}
</script>
