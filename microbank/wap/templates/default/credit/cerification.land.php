<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=6">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap cerification-wrap">
  <form id="uploadPicture" enctype="multipart/form-data" method="post">
    <input type="hidden" value="land" name="type">
    <input type="hidden" value="<?php echo $output['token'];?>" name="token">
    <input type="hidden" value="<?php echo $output['member_id'];?>" name="member_id">
    <?php if($output['cert_id'] > 0) { ?>
      <input type="hidden" value="<?php echo $output['cert_id'];?>" name="cert_id">
    <?php } ?>
    <div class="cerification-picture aui-margin-b-10">
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="property_card_uncheck">
              <input type="file" id="property_card" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_SITE_URL;?>/resource/image/upload-1.png?v=1" alt="" class="icon-upload">
            </div>
            <div class="check" id="property_card_check">
              <span class="cancel-check" onclick="cancelCheck('property_card');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="property_card_check_img">
            </div>
          </div>
          <div class="name"><?php echo 'Land Property';?></div>
        </div>
        <div class="up-example">
          <div class="up-exam">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/cert-example-11.png" alt="" class="example-pic">
          </div>
          <div class="name"><?php echo $lang['label_example'];?></div>
        </div>
      </div>
    </div>
    <div class="cerification-picture aui-margin-b-10">
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="trading_record_uncheck">
              <input type="file" id="trading_record" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_SITE_URL;?>/resource/image/upload-1.png?v=1" alt="" class="icon-upload">
            </div>
            <div class="check" id="trading_record_check">
              <span class="cancel-check" onclick="cancelCheck('trading_record');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="trading_record_check_img">
            </div>
          </div>
          <div class="name"><?php echo 'Land Transaction Table';?></div>
        </div>
        <div class="up-example">
          <div class="up-exam">
            <img src="<?php echo WAP_SITE_URL;?>/resource/image/cert-example-12.png" alt="" class="example-pic">
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
    <img src="<?php echo WAP_SITE_URL;?>/resource/image/gou.png" alt="">
    <p class="title"><?php echo 'Upload Successfully';?></p>
    <p class="tip"><?php echo str_replace('xxx','<em id="count">3</em>','It exits automatically xxx seconds later.');?></p>
  </div>
</div>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/common.js"></script>
<script type="text/javascript">
var formData = new FormData(document.getElementById('uploadPicture'));
$('#submit').on('click', function(){
  $('.upload-success').show();
  var count = $('#count').text();
  var times = setInterval(function(){
    count--;
    $('#count').text(count);
    if(count <= 0){
      clearInterval(times);
      window.location.href = "<?php echo getUrl('credit', 'certTypeList', array('type'=>certificationTypeEnum::LAND,'back'=>'certList'), false, WAP_SITE_URL)?>";
    }
  },1000);
  return
  var property_card = $('#property_card_check_img').attr('src'), trading_record = $('#trading_record_check_img').attr('src');
  if(!property_card || !trading_record){
    verifyFail('<?php echo $lang['tip_please_upload_all_photos'];?>');
    return;
  }
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'POST',
    url: '<?php echo ENTRY_API_SITE_URL;?>/member.cert.asset.php',
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
          if(count <= 0){
            clearInterval(times);
            window.location.href = "<?php echo getUrl('credit', 'certTypeList', array('type'=>certificationTypeEnum::LAND,'back'=>'certList'), false, WAP_SITE_URL)?>";
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
        $("#property_card,#trading_record").change(function(){
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
