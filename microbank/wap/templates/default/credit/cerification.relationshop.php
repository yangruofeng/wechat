<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/credit.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap cerification-wrap">
  <div class="guarantor-tip aui-margin-b-10">
    Tip: Submit your guarantor information,and he will receive the information to be confirmed. After he confirmed,your guarantee relationship established.
  </div>
  <?php $guarantee_relationship = $output['guarantee_relationship'];?>
  <form id="" method="post">
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
                Relationship
              </div>
              <div class="aui-list-item-input input-select">
                <select class="" name="relation_type" id="relation_type">
                  <option value="0">Select</option>
                  <?php foreach ($guarantee_relationship as $key => $value) { ?>
                    <option value="<?php echo $value['item_code'];?>"><?php $item_name_json = json_decode($value['item_name_json'],true);echo $item_name_json[Language::currentCode()];?></option>
                  <?php }?>
                </select>
                <i class="aui-iconfont aui-icon-down"></i>
            </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Member Account
              </div>
              <div class="aui-list-item-input">
                <input type="text" name="guarantee_member_account" id="guarantee_member_account" placeholder="<?php echo $lang['label_enter'];?>">
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label">
                Member Phone
              </div>
              <div class="aui-list-item-input country_code">
                <select name="country_code" id="country_code">
                  <option value="66">+66</option>
                  <option value="84">+84</option>
                  <option value="86">+86</option>
                  <option value="855">+855</option>
                </select>
                <i class="aui-iconfont aui-icon-down"></i>
                <input type="number" name="phone" id="phone" value="" placeholder="<?php echo $lang['label_enter'];?>">
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div style="">
      <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple aui-margin-t-15" id="submit">Submit</div>
    </div>
  </form>
</div>
<div class="upload-success">
  <div class="content">
    <img src="<?php echo WAP_SITE_URL;?>/resource/image/gou.png" alt="">
    <p class="title"><?php echo 'Add Successfully';?></p>
    <p class="tip"><?php echo str_replace('xxx','<em id="count">3</em>','It exits automatically xxx seconds later.');?></p>
  </div>
</div>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/common.js"></script>
<script type="text/javascript">
var formData = new FormData(document.getElementById('uploadPicture'));
$('#submit').on('click', function(){
  var relation_type = $.trim($('#relation_type').val()),
      guarantee_member_account = $.trim($('#guarantee_member_account').val()),
      phone = $.trim($('#phone').val()),
      country_code = $.trim($('#country_code').val());
  if(!relation_type){
    verifyFail('<?php echo $lang['tip_please_input_the_company_name'];?>');
    return;
  }
  if(!guarantee_member_account){
    verifyFail('<?php echo $lang['tip_please_input_the_company_address'];?>');
    return;
  }
  if(!phone){
    verifyFail('<?php echo $lang['tip_please_input_the_position'];?>');
    return;
  }

  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'POST',
    url: '<?php echo WAP_SITE_URL;?>/index.php?act=credit&op=ajaxAddRelationship',
    data: {relation_type: relation_type, guarantee_member_account: guarantee_member_account, country_code: country_code, phone: phone},
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
            window.location.href = "<?php echo getUrl('credit', 'certTypeList', array('type'=>certificationTypeEnum::GUARANTEE_RELATIONSHIP,'back'=>'certList'), false, WAP_SITE_URL)?>";
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
</script>
