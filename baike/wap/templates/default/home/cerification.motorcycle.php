<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap cerification-wrap">
  <form id="uploadPicture" enctype="multipart/form-data" method="post">
    <input type="hidden" value="<?php echo $output['token'];?>" name="token">
    <input type="hidden" value="<?php echo $output['member_id'];?>" name="member_id">
    <input type="hidden" value="motorbike" name="type">
    <?php if($output['cert_id'] > 0) { ?>
      <input type="hidden" value="<?php echo $output['cert_id'];?>" name="cert_id">
    <?php } ?>
    <div class="cerification-picture aui-margin-b-10">
        <div class="cerification-form">
            <ul class="aui-list aui-form-list cerification-item">
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label label">
                            Asset Name
                        </div>
                        <div class="aui-list-item-input">
                            <input type="text" name="asset_name" id="asset_name" placeholder="Enter">
                        </div>
                    </div>
                </li>
            </ul>
        </div>
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="certificate_front_uncheck">
              <input type="file" id="certificate_front" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/upload-1.png?v=1" alt="" class="icon-upload">
            </div>
            <div class="check" id="certificate_front_check">
              <span class="cancel-check" onclick="cancelCheck('certificate_front');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="certificate_front_check_img">
            </div>
          </div>
          <div class="name"><?php echo 'Frontal resident book image';?></div>
        </div>
        <div class="up-example">
          <div class="up-exam">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/cert-example-7.png" alt="" class="example-pic">
          </div>
          <div class="name"><?php echo $lang['label_example'];?></div>
        </div>
      </div>
    </div>
    <div class="cerification-picture aui-margin-b-10">
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="certificate_back_uncheck">
              <input type="file" id="certificate_back" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/upload-1.png?v=1" alt="" class="icon-upload">
            </div>
            <div class="check" id="certificate_back_check">
              <span class="cancel-check" onclick="cancelCheck('certificate_back');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="certificate_back_check_img">
            </div>
          </div>
          <div class="name"><?php echo 'Back resident book image';?></div>
        </div>
        <div class="up-example">
          <div class="up-exam">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/cert-example-8.png" alt="" class="example-pic">
          </div>
          <div class="name"><?php echo $lang['label_example'];?></div>
        </div>
      </div>
    </div>
    <div class="cerification-picture aui-margin-b-10">
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="motorbike_photo_uncheck">
              <input type="file" id="motorbike_photo" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/upload-1.png?v=1" alt="" class="icon-upload">
            </div>
            <div class="check" id="motorbike_photo_check">
              <span class="cancel-check" onclick="cancelCheck('motorbike_photo');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="motorbike_photo_check_img">
            </div>
          </div>
          <div class="name"><?php echo 'Back resident book image';?></div>
        </div>
        <div class="up-example">
          <div class="up-exam">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/cert-example-21.png" alt="" class="example-pic">
          </div>
          <div class="name"><?php echo $lang['label_example'];?></div>
        </div>
      </div>
    </div>
    <div style="padding: 1rem;">
      <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple" id="submit"><?php echo $lang['act_submit'];?></div>
    </div>
  </form>
</div>
<div class="upload-success">
  <div class="content">
    <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/gou.png" alt="">
    <p class="title"><?php echo 'Upload Successfully';?></p>
    <p class="tip"><?php echo str_replace('xxx','<em id="count">3</em>','It exits automatically xxx seconds later.');?></p>
  </div>
</div>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/common.js"></script>
<script type="text/javascript">
var formData = new FormData(document.getElementById('uploadPicture'));
$('#submit').on('click', function(){
  var certificate_front = $('#certificate_front_check_img').attr('src'),
      certificate_back = $('#certificate_back_check_img').attr('src'),//
      motorbike_photo = $('#motorbike_photo_check_img').attr('src');//
  if(!certificate_front || !certificate_back || !motorbike_photo){
    verifyFail('<?php echo $lang['tip_please_upload_all_photos'];?>');
    return;
  }
    formData.append("asset_name",$("#asset_name").val());
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'POST',
    url: '<?php echo ENTRY_API_SITE_URL;?>/officer.submit.cert.asset.php',
    data: formData,
    processData : false,
    contentType : false,
    dataType: 'json',
    success: function(data){
      toast.hide();
      if(data.STS){
        $('.upload-success').show();
        var count = $('#count').text();
        var times = setInterval(function(){
          count--;
          $('#count').text(count);
          if(count <= 1){
            clearInterval(times);
            window.location.href = "<?php echo getUrl('home', 'certTypeList', array('type'=>certificationTypeEnum::MOTORBIKE,'back'=>'certList','id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>";
          }
        },1000);
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
        $("#certificate_front,#certificate_back,#motorbike_photo").change(function(){
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
