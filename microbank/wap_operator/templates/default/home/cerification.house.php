<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap cerification-wrap">
  <form id="uploadPicture" enctype="multipart/form-data" method="post">
    <input type="hidden" value="house" name="type">
    <input type="hidden" value="23.125487" name="x_coordinate">
    <input type="hidden" value="48.125487" name="y_coordinate">
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
              <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/upload-1.png?v=1" alt="" class="icon-upload">
            </div>
            <div class="check" id="property_card_check">
              <span class="cancel-check" onclick="cancelCheck('property_card');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="property_card_check_img">
            </div>
          </div>
          <div class="name"><?php echo 'Housing Ownership Certificates';?></div>
        </div>
        <div class="up-example">
          <div class="up-exam">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/cert-example-13.png" alt="" class="example-pic">
          </div>
          <div class="name"><?php echo $lang['label_example'];?></div>
        </div>
      </div>
    </div>
    <div class="cerification-picture aui-margin-b-10">
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="house_front_uncheck">
              <input type="file" id="house_front" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/upload-1.png?v=1" alt="" class="icon-upload">
            </div>
            <div class="check" id="house_front_check">
              <span class="cancel-check" onclick="cancelCheck('house_front');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="house_front_check_img">
            </div>
          </div>
          <div class="name"><?php echo 'front view of house';?></div>
        </div>
        <div class="up-example">
          <div class="up-exam">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/cert-example-14.png" alt="" class="example-pic">
          </div>
          <div class="name"><?php echo $lang['label_example'];?></div>
        </div>
      </div>
    </div>
    <div class="cerification-picture aui-margin-b-10">
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="house_side_face_uncheck">
              <input type="file" id="house_side_face" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/upload-1.png?v=1" alt="" class="icon-upload">
            </div>
            <div class="check" id="house_side_face_check">
              <span class="cancel-check" onclick="cancelCheck('house_side_face');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="house_side_face_check_img">
            </div>
          </div>
          <div class="name"><?php echo $lang['label_handheld_housing_property_image'];?></div>
        </div>
        <div class="up-example">
          <div class="up-exam">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/cert-example-15.png" alt="" class="example-pic">
          </div>
          <div class="name"><?php echo $lang['label_example'];?></div>
        </div>
      </div>
    </div>
    <div class="cerification-picture aui-margin-b-10">
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="house_front_road_uncheck">
              <input type="file" id="house_front_road" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/upload-1.png?v=1" alt="" class="icon-upload">
            </div>
            <div class="check" id="house_front_road_check">
              <span class="cancel-check" onclick="cancelCheck('house_front_road');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="house_front_road_check_img">
            </div>
          </div>
          <div class="name"><?php echo $lang['label_handheld_housing_property_image'];?></div>
        </div>
        <div class="up-example">
          <div class="up-exam">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/cert-example-16.png" alt="" class="example-pic">
          </div>
          <div class="name"><?php echo $lang['label_example'];?></div>
        </div>
      </div>
    </div>
    <div class="cerification-picture aui-margin-b-10">
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="house_inside_uncheck">
              <input type="file" id="house_inside" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/upload-1.png?v=1" alt="" class="icon-upload">
            </div>
            <div class="check" id="house_inside_check">
              <span class="cancel-check" onclick="cancelCheck('house_inside');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="house_inside_check_img">
            </div>
          </div>
          <div class="name"><?php echo $lang['label_handheld_housing_property_image'];?></div>
        </div>
        <div class="up-example">
          <div class="up-exam">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/cert-example-17.png" alt="" class="example-pic">
          </div>
          <div class="name"><?php echo $lang['label_example'];?></div>
        </div>
      </div>
    </div>
    <div class="cerification-picture aui-margin-b-10">
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="house_relationships_certify_uncheck">
              <input type="file" id="house_relationships_certify" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/upload-1.png?v=1" alt="" class="icon-upload">
            </div>
            <div class="check" id="house_relationships_certify_check">
              <span class="cancel-check" onclick="cancelCheck('house_relationships_certify');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="house_relationships_certify_check_img">
            </div>
          </div>
          <div class="name"><?php echo $lang['label_handheld_housing_property_image'];?></div>
        </div>
        <div class="up-example">
          <div class="up-exam">
            <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/cert-example-18.png" alt="" class="example-pic">
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
  var property_card = $('#property_card_check_img').attr('src'), house_front = $('#house_front_check_img').attr('src'), house_side_face = $('#house_side_face_check_img').attr('src'),
      house_front_road = $('#house_front_road_check_img').attr('src'), house_inside = $('#house_inside_check_img').attr('src'), house_relationships_certify = $('#house_relationships_certify_check_img').attr('src');
  if(!property_card || !house_front || !house_side_face || !house_front_road || !house_inside || !house_relationships_certify){
    verifyFail('<?php echo $lang['tip_please_upload_all_photos'];?>');
    return;
  }
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
            window.location.href = "<?php echo getUrl('home', 'certTypeList', array('type'=>certificationTypeEnum::HOUSE,'back'=>'certList','id'=>$_GET['id']), false, WAP_OPERATOR_SITE_URL)?>";
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
        $("#property_card,#house_front,#house_side_face,#house_front_road,#house_inside,#house_relationships_certify").change(function(){
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
