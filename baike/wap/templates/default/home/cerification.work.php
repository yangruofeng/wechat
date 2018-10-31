<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap cerification-wrap input-cerification-wrap">
  <form id="uploadPicture" enctype="multipart/form-data" method="post">
    <input type="hidden" value="<?php echo $output['token'];?>" name="token">
    <input type="hidden" value="<?php echo $output['member_id'];?>" name="member_id">
    <?php if($output['cert_id'] > 0) { ?>
      <input type="hidden" value="<?php echo $output['cert_id'];?>" name="cert_id">
    <?php } ?>
    <div class="cerification-input aui-margin-b-10">
      <div class="cerification-form">
        <ul class="aui-list aui-form-list cerification-item">
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                <?php echo $lang['label_company_name'];?>
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="company_name" id="company_name" placeholder="Enter">
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                <?php echo $lang['label_company_address'];?>
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="company_address" id="company_address" placeholder="Enter">
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                <?php echo $lang['label_position'];?>
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="position" id="position" placeholder="Enter">
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
                <div class="aui-list-item-label label">
                    <?php echo $lang['label_company_employee'];?>
                </div>
                <div class="aui-list-item-input radio-label">
                    <label><input class="aui-radio" type="radio" value="1" name="is_government"> Yes</label>&nbsp;&nbsp;
                    <label><input class="aui-radio" type="radio" value="0" name="is_government" checked> No</label>
                </div>
            </div>
        </li>
        </ul>
      </div>
    </div>
    <div class="cerification-picture input-cerification-picture aui-margin-b-10">
      <div class="title"><?php echo $lang['label_working_certificate'];?></div>
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="work_card_uncheck">
              <input type="file" id="work_card" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/upload-1.png" alt="" class="icon-upload">
            </div>
            <div class="check" id="work_card_check">
              <span class="cancel-check" onclick="cancelCheck('work_card');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="work_card_check_img">
            </div>
          </div>
        </div>
      </div>
      <div class="name"><?php echo $lang['label_description'].$lang['label_colon'];?> <?php echo $lang['card_reference_letter'];?></div>
    </div>
    <div class="cerification-picture input-cerification-picture aui-margin-b-10">
      <div class="title"><?php echo $lang['label_hold_certificate_of_employment'];?></div>
      <div class="upload-wrap clearfix">
        <div class="up-btn">
          <div class="upload-input">
            <div class="uncheck" id="employment_certification_uncheck">
              <input type="file" id="employment_certification" value="" class="input" accept="image/*;" capture="camera">
              <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/upload-1.png" alt="" class="icon-upload">
            </div>
            <div class="check" id="employment_certification_check">
              <span class="cancel-check" onclick="cancelCheck('employment_certification');"><i class="aui-iconfont aui-icon-close"></i></span>
              <img src="" alt="" class="check1" id="employment_certification_check_img">
            </div>
          </div>
        </div>
      </div>
      <div class="name"><?php echo $lang['label_tip'].$lang['label_colon'];?><?php echo $lang['tip_please_upload_the_handheld_employment'];?></div>
    </div>
    <div class="aui-btn aui-btn-danger aui-btn-block custom-btn aui-margin-t-15 custom-btn-purple" id="submit"><?php echo $lang['act_submit'];?></div>
  </form>
</div>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/common.js"></script>
<script type="text/javascript">
var formData = new FormData(document.getElementById('uploadPicture'));
$('#submit').on('click', function(){
  var company_name = $.trim($('#company_name').val()), company_address = $.trim($('#company_address').val()),
      position = $.trim($('#position').val()), is_government = $('input[name=is_government]').prop('checked') ? 1 : 0,
      work_card = $('#work_card_check_img').attr('src'), employment_certification = $('#employment_certification_check_img').attr('src');
  if(!company_name){
    verifyFail('<?php echo $lang['tip_please_input_the_company_name'];?>');
    return;
  }
  if(!company_address){
    verifyFail('<?php echo $lang['tip_please_input_the_company_address'];?>');
    return;
  }
  if(!position){
    verifyFail('<?php echo $lang['tip_please_input_the_position'];?>');
    return;
  }
  if(!work_card || !employment_certification){
    verifyFail('<?php echo $lang['tip_please_upload_all_photos'];?>');
    return;
  }
  formData.append('company_name', company_name);
  formData.append('company_address', company_address);
  formData.append('position', position);
  formData.append('is_government', is_government);
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'POST',
    url: '<?php echo ENTRY_API_SITE_URL;?>/officer.submit.cert.work.php',
    data: formData,
    processData : false,
    contentType : false,
    dataType: 'json',
    success: function(data){
      toast.hide();
      if(data.STS){
        window.location.href = "<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=home&op=showCertCheckInfo&cert_id="+data.DATA.cert_result.uid+"&type="+data.DATA.cert_result.cert_type+"&back=home&id=<?php echo $_GET['id']?>";
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
        $("#work_card,#employment_certification").change(function(){
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
