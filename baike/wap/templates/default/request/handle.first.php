<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/request.css?v=2">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap handle-wrap">
  <!--<div class="handle-nav">
    <ul class="nav-ul clearfix">
      <li class="nav-item">
        <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up1.png"></p>
        <p class="text">Comment</p>
      </li>
      <li class="nav-item">
        <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up2.png"></p>
        <p class="text">Product</p>
      </li>
      <li class="nav-item">
        <p><img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/sign_up3_1.png"></p>
        <p class="text">Rate</p>
      </li>
    </ul>
  </div>  -->
  <div class="handle-form">
    <p class="title">Remark</p>
    <textarea name="remark" id="remark"></textarea>
  </div>
  <div style="padding: .2rem .8rem;">
    <!--<div class="aui-btn aui-btn-block custom-btn custom-btn-purple" id="continue">Continue</div>-->
    <div class="aui-btn aui-btn-block custom-btn custom-btn-purple" id="accept">Accepted</div>
    <div class="aui-btn aui-btn-block custom-btn aui-margin-t-10 reject-btn" id="reject">Rejected</div>
  </div>
</div>
<script type="text/javascript">
  $('#accept').on('click', function(){
    var remark = $('#remark').val();
    if(!remark){
      verifyFail('<?php echo 'Please input remark.';?>');
      return;
    }
    handle(1);
  });
  $('#continue').on('click', function(){
    var remark = $('#remark').val();
    if(!remark){
      //verifyFail('<?php echo 'Please input comment.';?>');
      //return;
    }
    handle(1);
  });
  $('#reject').on('click', function(){
    var remark = $('#remark').val();
    if(!remark){
      verifyFail('<?php echo 'Please input comment.';?>');
      return;
    }
    handle(0);
  });
  function handle(type){
    var remark = $('#remark').val();
    $.ajax({
      url: '<?php echo getUrl('request', 'ajaxHandleFirst', array(), false, WAP_OPERATOR_SITE_URL)?>',
      type: 'post',
      data: {consult_id: '<?php echo $_GET['uid'];?>',result: type, remark: remark},
      dataType: 'json',
      success: function(ret){
        if(ret.STS){
          toast.success({
            title: ret.MSG,
            duration: 2000
          });
          setTimeout(function(){
            window.location.href = '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=request&op=checkApplication';
          }, 2000);
        }else{
          verifyFail(ret.MSG);
        }
      }
    });
  }
</script>
