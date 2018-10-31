<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/request.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap overdue-handle-wrap">
<form class="custom-form" id="" method="post">
    <div class="cerification-input aui-margin-b-10">
      <div class="loan-form">
        <ul class="aui-list aui-form-list loan-item">
          <li class="aui-list-item last-item">
            <div class="aui-list-item-inner">
              <div class="aui-list-item-label label label-all">
                Remark
              </div>
            </div>
          </li>
          <li class="aui-list-item">
            <div class="aui-list-item-inner paddingright075" style="margin-top: -0.5rem;">
              <textarea class="mui_textarea" name="remark" id="remark"><?php echo $data['remark']?:'';?></textarea>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div style="padding: 0 .8rem;">
      <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple aui-margin-t-15" id="submit">Submit</div>
    </div>
  </form>
</div>

<script type="text/javascript">
  $('#submit').on('click', function(){
  var uid = '<?php echo $_GET['uid'];?>',
  type = '<?php echo $_GET['type'];?>',
      remark = $.trim($('#remark').val());
  if(!remark){
    verifyFail('<?php echo 'Please input remark.';?>');
    return;
  }
  toast.loading({
    title: '<?php echo $lang['label_loading'];?>'
  });
  $.ajax({
    type: 'post',
    url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=request&op=ajaxEditOverdue',
    data: {uid: uid, type: type, remark: remark},
    dataType: 'json',
    success: function(data){
      toast.hide();
      if(data.STS){
        window.location.href = "<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=request&op=checkOverdue";
      }else{
        if(data.CODE == '<?php echo errorCodesEnum::INVALID_TOKEN;?>' || data.CODE == '<?php echo errorCodesEnum::NO_LOGIN;?>'){
          if(window.operator){
            window.operator.reLogin();
            return;
          }
          window.location.href = "<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=login&op=index";
        }
        $('.error-tip').text(data.MSG);
      }

    },
    error: function(xhr, type){
      toast.hide();
      verifyFail('<?php echo $lang['tip_get_data_error'];?>');
    }
  });
});
</script>
